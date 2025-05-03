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
    }

    public function search()
    {
        $this->load->view('skl/search');
    }
public function result()
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
}

public function download_skl($nis)
{
    $siswa = $this->Siswa_model->get_by_nis($nis);
    if ($siswa) {
        // Path
        $templatePath = FCPATH . 'template/skl_template.docx';
        $docxPath = FCPATH . 'temp/skl_' . $siswa->nis . '.docx';
        $pdfPath  = FCPATH . 'temp/skl_' . $siswa->nis . '.pdf';

        // Generate Word
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
        $templateProcessor->setValue('nis', $siswa->nis);
        $templateProcessor->setValue('kelas', $siswa->kelas);
        $templateProcessor->setValue('status', ucfirst($siswa->status));
        $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir ?? '-');
        $templateProcessor->saveAs($docxPath);

        // Deteksi OS dan jalur LibreOffice
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $sofficePath = $isWindows
            ? '"C:\Program Files\LibreOffice\program\soffice.exe"'
            : '/usr/bin/soffice'; // Ubah jika beda lokasi di Linux hosting

        // Konversi Word ke PDF
        $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
        exec($cmd, $output, $returnCode);

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
        $this->load->view('templates/header');
        $this->load->view('skl/upload');
        $this->load->view('templates/footer');
    }
}
