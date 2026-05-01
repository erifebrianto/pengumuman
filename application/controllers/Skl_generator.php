<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

class Skl_generator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Removed is_cli() from constructor as process_single needs to be accessible via HTTP cURL

        // Optimasi: Bypass limit waktu dan perbesar memory limit
        ini_set('max_execution_time', 0); // 0 = unlimited
        ini_set('memory_limit', '1024M'); // 1 GB untuk amankan 1000+ siswa

        $this->load->model('Siswa_model');
        $this->load->model('Batch_model');
    }

    private function get_log_file()
    {
        $log_dir = FCPATH . "application/logs/batch/";
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        return $log_dir . "generate_" . date('Y_m_d') . ".log";
    }

    private function write_log($message, $type = "INFO")
    {
        $file = $this->get_log_file();
        $date = date('Y-m-d H:i:s');
        $formatted_message = "[{$date}] [{$type}] {$message}" . PHP_EOL;
        
        // Write to file
        file_put_contents($file, $formatted_message, FILE_APPEND);
        
        // Output to CLI as well
        echo $formatted_message;
    }

    public function generate_pengumuman_batch($mode = 'skip')
    {
        if (!is_cli()) {
            show_error('Akses ditolak. Controller ini hanya bisa diakses via CLI.', 403);
            exit;
        }
        $this->write_log("--- Memulai Proses Batch Generate Pengumuman (Mode: {$mode}) ---", "START");

        // 1. Cek Status Locking: Cegah Double Process
        $status_data = $this->Batch_model->get_status();
        if ($status_data && $status_data->status == 'processing') {
            $this->write_log("Generation is already running. Progress: {$status_data->progress} / {$status_data->total}", "WARNING");
            return;
        }

        // 2. Ambil data siswa
        $all_siswa = $this->Siswa_model->get_all();
        $total = count($all_siswa);

        if ($total == 0) {
            $this->write_log("No data to process.", "WARNING");
            return;
        }

        // Set status 'processing' untuk mengunci proses lain
        $this->Batch_model->reset_status($total);

        // 3. Pastikan template tersedia
        $templatePath = FCPATH . 'template/skl_template.docx';
        if (!file_exists($templatePath)) {
            $this->write_log("Template docx not found at {$templatePath}!", "ERROR");
            $this->Batch_model->update_status(['status' => 'error']);
            return;
        }

        // Buat folder output
        $tahun = date('Y');
        $upload_dir = FCPATH . "uploads/pengumuman/{$tahun}/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Buat folder temp spesifik CLI agar tidak bertabrakan
        $cli_temp_dir = FCPATH . "temp/cli_batch/";
        if (!is_dir($cli_temp_dir)) {
            mkdir($cli_temp_dir, 0755, true);
        }

        // Jika mode overwrite, hapus semua file PDF yang ada di dalam folder tersebut sebelum jalan
        if ($mode === 'overwrite') {
            $this->write_log("Mode Overwrite: Menghapus semua file PDF lama di folder {$tahun}...", "INFO");
            // Menghapus PDF beserta file hidden corrupt berawalan .~lock dari LibreOffice crash sebelumnya
            $files_to_delete = glob($upload_dir . '{*.pdf,.*.pdf#,*.tmp}', GLOB_BRACE);
            foreach($files_to_delete as $file) {
                if(is_file($file)) {
                    unlink($file);
                }
            }
        }

        $processed = 0;
        $sukses = 0;
        $gagal = 0;
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $this->write_log("Ditemukan {$total} siswa. Menyiapkan konversi PDF tahun {$tahun}...");

        // 4. Looping Semua Siswa
        foreach ($all_siswa as $siswa) {

            // Cek apakah perintah stop dijalankan dari admin secara dinamis
            $current_status = $this->Batch_model->get_status();
            if ($current_status && $current_status->status == 'stopped') {
                $this->write_log("Proses dihentikan paksa oleh Admin pada siswa NIS: {$siswa->nis}", "WARNING");
                break;
            }

            $pdfFileName = "skl_{$siswa->nis}.pdf";
            $pdfOutput   = $upload_dir . $pdfFileName;

            // Jika PDF sudah ada (mode skip, atau terlewat saat hapus), skip saja
            if (file_exists($pdfOutput)) {
                $processed++;
                $sukses++;
                $this->update_progress($processed, $total);
                continue; 
            }

            // Trigger proses tunggal via HTTP cURL internal untuk mengisolasi proses LibreOffice agar tidak bocor/zombie
            $url = base_url("index.php/skl_generator/process_single/{$siswa->nis}/{$mode}");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Max 30 detik per siswa
            $result = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            // Bersihkan output dari whitespace berlebih
            $result = trim($result);

            if ($result === "Success" || $result === "Skipped") {
                $sukses++;
            } else {
                $errorMsg = $result ? $result : "cURL Error: " . $curl_error;
                $this->write_log("Gagal konversi PDF: NIS {$siswa->nis}. Reason: {$errorMsg}", "ERROR");
                $gagal++;
            }

            // Beri jeda 50 milidetik agar CPU tidak dipaksa ke 100% Usage (Aman dari Spike/Crash)
            usleep(50000); 

            // Update counter
            $processed++;

            // Update status ke Database kelipatan 5 (Agar I/O DB ringan) atau saat record terakhir
            if ($processed % 5 == 0 || $processed == $total) {
                $this->update_progress($processed, $total);
            }
        }

        // Tandai Selesai
        $this->Batch_model->update_status([
            'status' => 'completed',
            'progress' => $total
        ]);
        
        $this->write_log("Batch Processing selesai! Total: {$total}, Sukses: {$sukses}, Gagal: {$gagal}.", "COMPLETED");
    }

    private function update_progress($processed, $total)
    {
        $this->Batch_model->update_status([
            'progress' => $processed,
            'total'    => $total
        ]);
        // Bikin log berkala biar tidak spam
        if ($processed % 50 == 0 || $processed == $total) {
            $this->write_log("Progress: {$processed} / {$total}", "INFO");
        }
    }
    public function process_single($nis, $mode = 'skip')
    {
        // This endpoint will be called autonomously by cURL for each student
        $siswa = $this->Siswa_model->get_by_nis($nis);
        if (!$siswa) {
            echo "Siswa not found";
            return;
        }

        $tahun = date('Y');
        $upload_dir = FCPATH . "uploads/pengumuman/{$tahun}/";
        $cli_temp_dir = FCPATH . "temp/cli_batch/";
        
        $pdfFileName = "skl_{$siswa->nis}.pdf";
        $pdfOutput   = $upload_dir . $pdfFileName;

        if (file_exists($pdfOutput) && $mode === 'skip') {
            echo "Skipped";
            return;
        }

        $templatePath = FCPATH . 'template/skl_template.docx';
        $docxPath = $cli_temp_dir . "skl_{$siswa->nis}.docx";

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
            $templateProcessor->setValue('nis', $siswa->nis);
            $templateProcessor->setValue('kelas', $siswa->kelas);
            $templateProcessor->setValue('no_ujian', $siswa->no_ujian);
            $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir ?? '-');

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
            $templateProcessor->setComplexValue('status_lulus_rich', $statusRichText);
            $templateProcessor->saveAs($docxPath);

        } catch (Exception $e) {
            echo "Error Word: " . $e->getMessage();
            return;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
            $cmd = $sofficePath . ' --headless --invisible --nologo --nodefault --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg($upload_dir);
            exec($cmd, $output, $returnCode);
        } else {
            $cmd = "unoconv -f pdf -o " . escapeshellarg($upload_dir . $pdfFileName) . " " . escapeshellarg($docxPath) . " 2>&1";
            exec($cmd, $output, $returnCode);

            $outputStr = implode(" ", $output);
            if ($returnCode === 127 || strpos($outputStr, 'not found') !== false) {
                if (file_exists('/opt/libreoffice6.4/program/soffice')) {
                    $sofficeOptPath = '/opt/libreoffice6.4/program/soffice';
                } elseif (file_exists('/usr/bin/libreoffice')) {
                    $sofficeOptPath = '/usr/bin/libreoffice';
                } elseif (file_exists('/usr/bin/soffice')) {
                    $sofficeOptPath = '/usr/bin/soffice';
                } else {
                    $sofficeOptPath = 'libreoffice';
                }
                $loProfile = $cli_temp_dir . "lo_profile_" . $siswa->nis . "_" . rand(100, 999);
                $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg($upload_dir) . " 2>&1";
                
                $outputStr = shell_exec($cmd);

                if (is_dir($loProfile)) {
                    shell_exec("rm -rf " . escapeshellarg($loProfile));
                }
            }
        }

        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        if (file_exists($pdfOutput)) {
            echo "Success";
        } else {
            echo "Failed. Output: " . $outputStr;
        }
    }
}
