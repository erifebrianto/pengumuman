<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;

class Skl extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Siswa_model');
        $this->load->model('Countdown_model');
    }

    public function search()
    {
        // Ambil waktu countdown
        $data['countdown'] = $this->Countdown_model->get_target_time();

        // Ambil data pengaturan dari database
        $this->load->model('Setting_model');
        $pengaturan = $this->Setting_model->get_first();

        // Tambahkan ke data yang dikirim ke view
        $data['nama_sekolah'] = $pengaturan->nama_sekolah ?? 'Nama Sekolah';
        $data['logo_sekolah'] = $pengaturan->logo_sekolah ?? 'default_logo.png'; // fallback jika tidak ada
        $data['background'] = $pengaturan->background ?? '';

        $this->load->view('skl/search', $data);
    }

    public function result()
    {
        $nis = $this->input->post('nis');
        $no_ujian = $this->input->post('no_ujian');

        // Validasi format no_ujian
        if (!preg_match('/^\d{4}-\d{4}-\d{3}$/', $no_ujian)) {
            $this->session->set_flashdata('error', 'Format No. Ujian tidak valid. Contoh: 2025-0309-002');
            redirect('skl/search');
        }

        $siswa = $this->Siswa_model->get_by_nis_and_no_ujian($nis, $no_ujian);

        if ($siswa) {
            $data['siswa'] = $siswa;
            $this->load->view('skl/result', $data);  // tampilkan hasil & tombol download
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
            redirect('skl/search');
        }
    }

/*    public function result()
    {
        $nis = $this->input->post('nis');
        $siswa = $this->Siswa_model->get_by_nis($nis);

        if ($siswa) {
            $data['siswa'] = $siswa;
            $this->load->view('skl/result', $data);  // tampilkan hasil & tombol download
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
            redirect('skl/search');
        }
    }*/

    public function download_skl($nis)
    {
        $siswa = $this->Siswa_model->get_by_nis($nis);
        if ($siswa) {
            // Path
            $templatePath = FCPATH . 'template/skl_template.docx';
            $docxPath = FCPATH . 'temp/skl_' . $siswa->nis . '.docx';
            $pdfPath  = FCPATH . 'temp/skl_' . $siswa->nis . '.pdf';

            // Generate Word
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
            $templateProcessor->setValue('nis', $siswa->nis);
            $templateProcessor->setValue('kelas', $siswa->kelas);
            $templateProcessor->setValue('no_ujian', $siswa->no_ujian);
            $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir ?? '-');

            // Gunakan rich text untuk status lulus / tidak lulus
            $statusRichText = new \PhpOffice\PhpWord\Element\TextRun();

            if (strtolower($siswa->status) === 'lulus') {
                $statusRichText->addText('LULUS', ['bold' => true]);
                $statusRichText->addText(' / ', []);
                $statusRichText->addText('TIDAK LULUS', ['strikethrough' => true, 'color' => '888888']);
            } else {
                $statusRichText->addText('LULUS', ['strikethrough' => true, 'color' => '888888']);
                $statusRichText->addText(' / ', []);
                $statusRichText->addText('TIDAK LULUS', ['bold' => true]);
            }

            // Set ke template
            $templateProcessor->setComplexValue('status_lulus_rich', $statusRichText);

            $templateProcessor->saveAs($docxPath);

            // Deteksi OS dan jalur LibreOffice
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $sofficePath = $isWindows
                ? '"C:\Program Files\LibreOffice\program\soffice.exe"'
                : '/opt/libreoffice6.4/program/soffice'; // Path LibreOffice di Hosting

            // Konversi Word ke PDF
            $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
            exec($cmd, $output, $returnCode);

            // Cek apakah PDF berhasil dihasilkan
            if ($returnCode === 0 && file_exists($pdfPath)) {
                redirect(base_url('temp/skl_' . $siswa->nis . '.pdf'));
            } else {
                $this->session->set_flashdata('error', 'Gagal mengonversi SKL ke PDF.');
                redirect('skl/search');
            }
        } else {
            $this->session->set_flashdata('error', 'Data siswa tidak ditemukan.');
            redirect('skl/search');
        }
    }


    private function convertHtmlToPdf($htmlPath, $nis)
    {
        // Load HTML content
        $htmlContent = file_get_contents($htmlPath);

        // Initialize Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);

        // (Optional) Set paper size
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first pass)
        $dompdf->render();

        // Save PDF to file
        $pdfPath = FCPATH . 'temp/skl_' . $nis . '.pdf';
        file_put_contents($pdfPath, $dompdf->output());

        // Redirect to the generated PDF
        redirect(base_url('temp/skl_' . $nis . '.pdf'));
    }
    // Form untuk mengupload template SKL
    public function upload()
    {
            if (!$this->session->userdata('user_id')) {
            redirect('auth/login'); // Sesuaikan dengan URL login Anda
            return;
        }
        $this->load->helper('file');

        // Konfigurasi upload template SKL
        $config['upload_path']   = './template/';
        $config['allowed_types'] = 'docx';
        $config['max_size']      = 2048;
        $config['file_name']     = 'skl_template.docx';
        $config['overwrite']     = TRUE;

        $this->load->library('upload', $config);

        // Proses upload template
        if (!$this->upload->do_upload('template')) {
            // Jika gagal upload, tampilkan error
            $this->session->set_flashdata('error', $this->upload->display_errors());
        } else {
            // Jika berhasil upload, beri pesan sukses
            $this->session->set_flashdata('success', 'Template berhasil diupload.');
        }

        // Redirect kembali ke halaman upload form
        redirect('skl/upload_form');
    }

    // Form untuk upload template
    public function upload_form()
    {
        if (!$this->session->userdata('user_id')) {
        redirect('auth/login'); // Sesuaikan dengan URL login Anda
        return;
    }
        $this->load->view('templates/header');
        $this->load->view('skl/upload');
        $this->load->view('templates/footer');
    }
}
