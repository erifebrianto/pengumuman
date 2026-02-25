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
            // --- LOGIKA WHATSAPP QUEUE WABLAS ---
            
            // 1. Pastikan Siswa punya Token Download yang unik untuk keamanan link WA
            if (empty($siswa->token_download)) {
                // Generate token acak 32 karakter rahasia
                $token = bin2hex(random_bytes(16));
                $this->Siswa_model->update_token($siswa->nis, $token);
                $siswa->token_download = $token; // Update objek saat ini untuk dipakai di pesan
            }

            // 2. Cek apakah NIS ini sudah masuk antrian / pernah dikirim WA sebelumnya
            $queue_check = $this->db->get_where('whatsapp_queue', ['nis' => $siswa->nis])->row();
            
            // Ambil pengaturan Wablas dari Database Utama
            $pengaturan = $this->db->get('pengaturan')->row();

            // Eksekusi trigger WA Queue hanya jika Fitur AKTIF
            if ($pengaturan && $pengaturan->wablas_status == 1 && !$queue_check && !empty($siswa->no_hp)) {
                // Link khusus WA
                $link_download = base_url('skl/download_skl_wa/' . $siswa->token_download);
                $is_lulus = (strtolower($siswa->status) == 'lulus');
                
                $pesan_raw = "";
                if ($is_lulus) {
                    $pesan_raw = !empty($pengaturan->wablas_template_lulus) ? $pengaturan->wablas_template_lulus : 
                    "🚨 *PENGUMUMAN RESMI SEKOLAH* 🚨\n\nHalo Bapak/Ibu Wali Murid & Ananda *{NAMA_SISWA}*.\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *LULUS* ✅.\nSilakan unduh SKL resmi pada tautan berikut: {LINK_DOWNLOAD}";
                } else {
                    $pesan_raw = !empty($pengaturan->wablas_template_gagal) ? $pengaturan->wablas_template_gagal : 
                    "🚨 *PENGUMUMAN RESMI SEKOLAH* 🚨\n\nHalo Bapak/Ibu Wali Murid & Ananda *{NAMA_SISWA}*.\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *TIDAK LULUS* ❌.\nTetap Semangat! Unduh Keterangan hasil ujian pada tautan berikut: {LINK_DOWNLOAD}";
                }

                // Parse Variabel Dinamis
                $pesan = str_replace(
                    ['{NAMA_SISWA}', '{NIS}', '{KELAS}', '{LINK_DOWNLOAD}'],
                    [$siswa->nama_lengkap, $siswa->nis, $siswa->kelas, $link_download],
                    $pesan_raw
                );

                // Insert ke antrian
                $this->db->insert('whatsapp_queue', [
                    'nis' => $siswa->nis,
                    'no_hp' => $siswa->no_hp, // Pastikan field no_hp ada di DB siswa
                    'pesan' => $pesan,
                    'status' => 'pending'
                ]);
            }
            // --- END LOGIKA WHATSAPP ---

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
            
            if ($isWindows) {
                $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
                exec($cmd, $output, $returnCode);
            } else {
                $sofficeOptPath = '/opt/libreoffice6.4/program/soffice';
                $loProfile = FCPATH . "temp/lo_profile_single_" . $siswa->nis . "_" . rand(100, 999);
                
                $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg(FCPATH . 'temp/') . " 2>&1";
                
                $outputStr = shell_exec($cmd);
                $returnCode = ($outputStr === null || strpos($outputStr, 'Error') !== false) ? 1 : 0;

                // Cleanup temporary background LibreOffice profile
                if (is_dir($loProfile)) {
                    shell_exec("rm -rf " . escapeshellarg($loProfile));
                }
            }

            // Cek apakah PDF berhasil dihasilkan
            if ($returnCode === 0 && file_exists($pdfPath)) {
                $this->load->helper('download');
                $data = file_get_contents($pdfPath);
                $name = 'SKL_' . $siswa->nis . '.pdf';
                force_download($name, $data);
            } else {
                $this->session->set_flashdata('error', 'Gagal mengonversi SKL ke PDF.');
                redirect('skl/search');
            }
        } else {
            $this->session->set_flashdata('error', 'Data siswa tidak ditemukan.');
            redirect('skl/search');
        }
    }


    // Endpoint Khusus Wablas/WhatsApp dengan URL Rahasia (Token)
    public function download_skl_wa($token)
    {
        $siswa = $this->Siswa_model->get_by_token($token);
        if ($siswa) {
            // Path
            $templatePath = FCPATH . 'template/skl_template.docx';
            $docxPath = FCPATH . 'temp/skl_wa_' . $siswa->nis . '.docx';
            $pdfPath  = FCPATH . 'temp/skl_wa_' . $siswa->nis . '.pdf';

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
            
            if ($isWindows) {
                $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
                exec($cmd, $output, $returnCode);
            } else {
                $sofficeOptPath = '/opt/libreoffice6.4/program/soffice';
                $loProfile = FCPATH . "temp/lo_profile_wa_" . $siswa->nis . "_" . rand(100, 999);
                
                $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg(FCPATH . 'temp/') . " 2>&1";
                
                $outputStr = shell_exec($cmd);
                $returnCode = ($outputStr === null || strpos($outputStr, 'Error') !== false) ? 1 : 0;

                // Cleanup temporary background LibreOffice profile
                if (is_dir($loProfile)) {
                    shell_exec("rm -rf " . escapeshellarg($loProfile));
                }
            }

            // Cek apakah PDF berhasil dihasilkan
            if ($returnCode === 0 && file_exists($pdfPath)) {
                $this->load->helper('download');
                $data = file_get_contents($pdfPath);
                $name = 'SKL_WA_' . $siswa->nis . '.pdf';
                force_download($name, $data);
            } else {
                // Return string murni karena akses via WA tanpa sesi
                echo "Maaf, dokumen PDF SKL gagal dibentuk oleh server. Harap lapor ke Panitia Sekolah.";
            }
        } else {
            echo "Maaf, Tautan Dokumen Anda tidak valid atau telah kedaluwarsa.";
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

    // Menampilkan log dari background process
    public function logs()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
            return;
        }

        // Ambil file log hari ini
        $log_file = FCPATH . "application/logs/batch/generate_" . date('Y_m_d') . ".log";
        $log_content = "Belum ada log/aktivitas generate pada hari ini.";

        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
        }

        $data['log_content'] = $log_content;
        $data['log_date'] = date('d-m-Y');

        $this->load->view('templates/header');
        $this->load->view('skl/logs', $data);
        $this->load->view('templates/footer');
    }
}
