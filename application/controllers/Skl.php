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
        $this->load->view('templates/header');
        $this->load->view('skl/search');
        $this->load->view('templates/footer');
    }

    public function result()
{
    $nis = $this->input->post('nis');
    $siswa = $this->Siswa_model->get_by_nis($nis);

    if ($siswa) {
        $templatePath = FCPATH . 'template/skl_template.docx';
        $wordPath = FCPATH . 'temp/skl_' . $siswa->nis . '.docx';
        $pdfPath  = FCPATH . 'temp/skl_' . $siswa->nis . '.pdf';

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
        $templateProcessor->setValue('nis', $siswa->nis);
        $templateProcessor->setValue('kelas', $siswa->kelas);
        $templateProcessor->setValue('status', ucfirst($siswa->status));
        $templateProcessor->saveAs($wordPath);

        // Konversi ke PDF menggunakan LibreOffice
        $cmd = 'soffice --headless --convert-to pdf ' . escapeshellarg($wordPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
        exec($cmd, $output, $returnCode);

        if ($returnCode === 0 && file_exists($pdfPath)) {
            redirect(base_url('temp/skl_' . $siswa->nis . '.pdf'));
        } else {
            $this->session->set_flashdata('error', 'Gagal mengonversi Word ke PDF.');
            redirect('skl/search');
        }
    } else {
        $this->session->set_flashdata('error', 'Data tidak ditemukan!');
        redirect('skl/search');
    }
}


    public function download_skl($nis)
    {
        // Mengambil data siswa dari database berdasarkan NIS
        $siswa = $this->Siswa_model->get_by_nis($nis);
        if ($siswa) {
            // Generate Word
            $templatePath = FCPATH . 'template/skl_template.docx';
            $savePath = FCPATH . 'temp/skl_' . $siswa->nis . '.docx';

            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
            $templateProcessor->setValue('nis', $siswa->nis);
            $templateProcessor->setValue('kelas', $siswa->kelas);
            $templateProcessor->setValue('status', ucfirst($siswa->status));
            $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir);  // Pastikan ada field alamat di database
            $templateProcessor->saveAs($savePath);

            // Check if file exists before converting
            if (file_exists($savePath)) {
                // Convert Word to HTML first
                $phpWord = IOFactory::load($savePath);
                $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
                $htmlPath = FCPATH . 'temp/skl_' . $siswa->nis . '.html';
                $htmlWriter->save($htmlPath);

                // Now convert HTML to PDF using dompdf
                $this->convertHtmlToPdf($htmlPath, $siswa->nis);
            } else {
                $this->session->set_flashdata('error', 'Template SKL gagal disimpan.');
                redirect('skl/search');
            }
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
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
