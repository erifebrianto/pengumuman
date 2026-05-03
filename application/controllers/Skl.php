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
        // Convert enum to varchar
        $this->db->query("ALTER TABLE pengaturan MODIFY verification_method varchar(255) DEFAULT 'exam_number_nis'");
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
        
        $method = $pengaturan->verification_method ?? 'exam_number_nis';
        if (!in_array($method, ['nisn', 'nis_nisn', 'nis_nama', 'exam_number_nis', 'nisn_exam_number', 'nis'])) {
            $method = 'exam_number_nis';
        }
        $data['verification_method'] = $method;

        $this->load->view('skl/search', $data);
    }

    public function result()
    {
        $this->load->model('Setting_model');
        $pengaturan = $this->Setting_model->get_first();
        $method = $pengaturan->verification_method ?? 'exam_number_nis';
        if (!in_array($method, ['nisn', 'nis_nisn', 'nis_nama', 'exam_number_nis', 'nisn_exam_number', 'nis'])) {
            $method = 'exam_number_nis';
        }

        $fields = [];
        $error_msg = 'Data tidak ditemukan! Pastikan data yang Anda masukkan benar.';

        switch ($method) {
            case 'nis':
                $nis = $this->input->post('nis');
                if (empty($nis)) redirect('skl/search');
                $fields = ['nis' => trim($nis)];
                break;
            case 'nisn':
                $nisn = $this->input->post('nisn');
                if (empty($nisn)) redirect('skl/search');
                $fields = ['nisn' => trim($nisn)];
                break;
            case 'nis_nisn':
                $nis  = $this->input->post('nis');
                $nisn = $this->input->post('nisn');
                if (empty($nis) || empty($nisn)) redirect('skl/search');
                $fields = ['nis' => trim($nis), 'nisn' => trim($nisn)];
                break;
            case 'nis_nama':
                $nis  = $this->input->post('nis');
                $nama = $this->input->post('nama_lengkap');
                if (empty($nis) || empty($nama)) redirect('skl/search');
                $fields = ['nis' => trim($nis), 'nama_lengkap' => trim($nama)];
                break;
            case 'exam_number_nis':
                $no_ujian = $this->input->post('no_ujian');
                $nis      = $this->input->post('nis');
                if (empty($no_ujian) || empty($nis)) redirect('skl/search');
                $fields = ['no_ujian' => trim($no_ujian), 'nis' => trim($nis)];
                break;
            case 'nisn_exam_number':
                $nisn     = $this->input->post('nisn');
                $no_ujian = $this->input->post('no_ujian');
                if (empty($nisn) || empty($no_ujian)) redirect('skl/search');
                $fields = ['nisn' => trim($nisn), 'no_ujian' => trim($no_ujian)];
                break;
            default:
                // fallback to original enum options just in case
                $no_ujian = $this->input->post('no_ujian');
                $nis      = $this->input->post('nis');
                if (empty($no_ujian) || empty($nis)) redirect('skl/search');
                $fields = ['no_ujian' => trim($no_ujian), 'nis' => trim($nis)];
                break;
        }

        $siswa = $this->Siswa_model->get_by_fields($fields);

        if ($siswa) {
            // Ensure student has a secure download token
            if (empty($siswa->token_download)) {
                $token = bin2hex(random_bytes(16));
                $this->Siswa_model->update_token($siswa->nis, $token);
                $siswa->token_download = $token;
            }

            // --- LOGIKA WHATSAPP QUEUE WABLAS ---
            // Cek apakah NIS ini sudah masuk antrian / pernah dikirim WA sebelumnya
            $queue_check = $this->db->get_where('whatsapp_queue', ['nis' => $siswa->nis])->row();

            // Eksekusi trigger WA Queue hanya jika Fitur AKTIF
            if ($pengaturan && $pengaturan->wablas_status == 1 && !$queue_check && !empty($siswa->no_hp)) {
                $link_download = base_url('skl/download_skl_wa/' . $siswa->token_download);
                $is_lulus = (strtolower($siswa->status) == 'lulus');
                
                $pesan_raw = $is_lulus ? 
                    (!empty($pengaturan->wablas_template_lulus) ? $pengaturan->wablas_template_lulus : "LULUS") : 
                    (!empty($pengaturan->wablas_template_gagal) ? $pengaturan->wablas_template_gagal : "TIDAK LULUS");

                $pesan = str_replace(
                    ['{NAMA_SISWA}', '{NIS}', '{KELAS}', '{LINK_DOWNLOAD}'],
                    [$siswa->nama_lengkap, $siswa->nis, $siswa->kelas, $link_download],
                    $pesan_raw
                );

                $this->db->insert('whatsapp_queue', [
                    'nis' => $siswa->nis,
                    'no_hp' => $siswa->no_hp,
                    'pesan' => $pesan,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $data['pengaturan'] = $pengaturan;
            $data['siswa'] = $siswa;
            $this->load->view('skl/result', $data);
        } else {
            $this->session->set_flashdata('error', $error_msg);
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

    public function download_skl($token)
    {
        $siswa = $this->Siswa_model->get_by_token($token);
        if ($siswa) {
            // Optimasi: Cek apakah PDF hasil batch generate sudah ada
            $tahun = date('Y');
            $preGeneratedPdf = FCPATH . "uploads/pengumuman/{$tahun}/skl_{$siswa->nis}.pdf";
            
            if (file_exists($preGeneratedPdf)) {
                $this->load->helper('download');
                $data = file_get_contents($preGeneratedPdf);
                $name = 'SKL_' . $siswa->nis . '.pdf';
                force_download($name, $data);
                return;
            }

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
            $templateProcessor->setValue('tanggal_lahir', $siswa->tanggal_lahir ?? '-');
            $templateProcessor->setValue('nisn', $siswa->nisn ?? '-');
            $templateProcessor->setValue('kurikulum', $siswa->kurikulum ?? '-');
            $templateProcessor->setValue('program_keahlian', $siswa->program_keahlian ?? '-');
            $templateProcessor->setValue('konsentrasi_keahlian', $siswa->konsentrasi_keahlian ?? '-');
            $templateProcessor->setValue('tanggal_kelulusan', $siswa->tanggal_kelulusan ?? '-');
            $templateProcessor->setValue('no_ijazah', $siswa->no_ijazah ?? '-');
            $templateProcessor->setValue('rata_rata', $siswa->rata_rata ?? '-');

            // School Setting Variables
            $this->load->model('Setting_model');
            $pengaturan = $this->Setting_model->get_first();
            if ($pengaturan) {
                $templateProcessor->setValue('nama_sekolah', $pengaturan->nama_sekolah ?? '-');
                $templateProcessor->setValue('alamat_sekolah', $pengaturan->alamat_sekolah ?? '-');
                $templateProcessor->setValue('nama_kepala_sekolah', $pengaturan->nama_kepala_sekolah ?? '-');
            }

            // Populate all available subject scores
            $this->load->model('Nilai_model');
            $nilai_siswa = $this->Nilai_model->get_nilai_with_mapel($siswa->id);
            foreach ($nilai_siswa as $n) {
                $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $n->nama_mata_pelajaran));
                $templateProcessor->setValue('n_' . $clean_name, $n->nilai);
            }

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
                if (file_exists('/Applications/LibreOffice.app/Contents/MacOS/soffice')) {
                    $sofficeOptPath = '/Applications/LibreOffice.app/Contents/MacOS/soffice';
                } elseif (file_exists('/opt/libreoffice6.4/program/soffice')) {
                    $sofficeOptPath = '/opt/libreoffice6.4/program/soffice';
                } elseif (file_exists('/usr/bin/libreoffice')) {
                    $sofficeOptPath = '/usr/bin/libreoffice';
                } elseif (file_exists('/usr/bin/soffice')) {
                    $sofficeOptPath = '/usr/bin/soffice';
                } else {
                    $sofficeOptPath = 'libreoffice';
                }
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
            // Optimasi: Cek apakah PDF hasil batch generate sudah ada
            $tahun = date('Y');
            $preGeneratedPdf = FCPATH . "uploads/pengumuman/{$tahun}/skl_{$siswa->nis}.pdf";
            
            if (file_exists($preGeneratedPdf)) {
                $this->load->helper('download');
                $data = file_get_contents($preGeneratedPdf);
                $name = 'SKL_WA_' . $siswa->nis . '.pdf';
                force_download($name, $data);
                return;
            }

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
            $templateProcessor->setValue('tanggal_lahir', $siswa->tanggal_lahir ?? '-');
            $templateProcessor->setValue('nisn', $siswa->nisn ?? '-');
            $templateProcessor->setValue('kurikulum', $siswa->kurikulum ?? '-');
            $templateProcessor->setValue('program_keahlian', $siswa->program_keahlian ?? '-');
            $templateProcessor->setValue('konsentrasi_keahlian', $siswa->konsentrasi_keahlian ?? '-');
            $templateProcessor->setValue('tanggal_kelulusan', $siswa->tanggal_kelulusan ?? '-');
            $templateProcessor->setValue('no_ijazah', $siswa->no_ijazah ?? '-');
            $templateProcessor->setValue('rata_rata', $siswa->rata_rata ?? '-');

            // School Setting Variables
            $this->load->model('Setting_model');
            $pengaturan = $this->Setting_model->get_first();
            if ($pengaturan) {
                $templateProcessor->setValue('nama_sekolah', $pengaturan->nama_sekolah ?? '-');
                $templateProcessor->setValue('alamat_sekolah', $pengaturan->alamat_sekolah ?? '-');
                $templateProcessor->setValue('nama_kepala_sekolah', $pengaturan->nama_kepala_sekolah ?? '-');
            }

            // Populate all available subject scores
            $this->load->model('Nilai_model');
            $nilai_siswa = $this->Nilai_model->get_nilai_with_mapel($siswa->id);
            foreach ($nilai_siswa as $n) {
                $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $n->nama_mata_pelajaran));
                $templateProcessor->setValue('n_' . $clean_name, $n->nilai);
            }

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
                if (file_exists('/Applications/LibreOffice.app/Contents/MacOS/soffice')) {
                    $sofficeOptPath = '/Applications/LibreOffice.app/Contents/MacOS/soffice';
                } elseif (file_exists('/opt/libreoffice6.4/program/soffice')) {
                    $sofficeOptPath = '/opt/libreoffice6.4/program/soffice';
                } elseif (file_exists('/usr/bin/libreoffice')) {
                    $sofficeOptPath = '/usr/bin/libreoffice';
                } elseif (file_exists('/usr/bin/soffice')) {
                    $sofficeOptPath = '/usr/bin/soffice';
                } else {
                    $sofficeOptPath = 'libreoffice';
                }
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
        $config['upload_path']   = FCPATH . 'template/';
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
