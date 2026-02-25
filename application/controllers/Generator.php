<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

class Generator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Hanya diizinkan dijalankan lewat CLI
        if (!is_cli()) {
            show_error('Akses ditolak. Controller ini hanya bisa diakses via CLI.', 403);
            exit;
        }

        // Optimasi: Bypass limit waktu dan perbesar memory limit
        ini_set('max_execution_time', 0); // 0 = unlimited
        ini_set('memory_limit', '1024M'); // 1 GB untuk amankan 1000+ siswa

        $this->load->model('Siswa_model');
        $this->load->model('Batch_model');
    }

    public function generate_pengumuman_batch()
    {
        // 1. Cek Status Locking: Cegah Double Process
        $status_data = $this->Batch_model->get_status();
        if ($status_data && $status_data->status == 'processing') {
            echo "Generation is already running. Progress: {$status_data->progress} / {$status_data->total}\n";
            return;
        }

        // 2. Ambil data siswa
        $all_siswa = $this->Siswa_model->get_all();
        $total = count($all_siswa);

        if ($total == 0) {
            echo "No data to process.\n";
            return;
        }

        // Set status 'processing' untuk mengunci proses lain
        $this->Batch_model->reset_status($total);

        // 3. Pastikan template tersedia
        $templatePath = FCPATH . 'template/skl_template.docx';
        if (!file_exists($templatePath)) {
            echo "Template docx not found at {$templatePath}!\n";
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

        $processed = 0;
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        echo "Started batch processing for {$total} students...\n";

        // 4. Looping Semua Siswa
        foreach ($all_siswa as $siswa) {
            $pdfFileName = "skl_{$siswa->nis}.pdf";
            $pdfOutput   = $upload_dir . $pdfFileName;

            // Jika PDF sudah ada, skip (hemat waktu & CPU)
            if (file_exists($pdfOutput)) {
                $processed++;
                $this->update_progress($processed, $total);
                continue; 
            }

            // Generate Word (.docx) sementara
            $docxPath = $cli_temp_dir . "skl_{$siswa->nis}.docx";
            try {
                $templateProcessor = new TemplateProcessor($templatePath);
                $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
                $templateProcessor->setValue('nis', $siswa->nis);
                $templateProcessor->setValue('kelas', $siswa->kelas);
                $templateProcessor->setValue('no_ujian', $siswa->no_ujian);
                $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir ?? '-');

                // Gunakan rich text untuk status
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
                echo "Error generating Word for NIS: {$siswa->nis} - " . $e->getMessage() . "\n";
                continue;
            }

            // 5. Convert ke PDF via Shell Command
            if ($isWindows) {
                // Windows biasanya tidak mendukung unoconv natively kecuali WSL.
                // Fallback menggunakan libreoffice CLI biasa.
                $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $cmd = $sofficePath . ' --headless --invisible --nologo --nodefault --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg($upload_dir);
            } else {
                // GNU/Linux: Gunakan `unoconv` untuk background listener yang gigih
                // unoconv secara otomatis terhubung ke background LibreOffice listener
                $cmd = "unoconv -f pdf -o " . escapeshellarg($upload_dir . $pdfFileName) . " " . escapeshellarg($docxPath);
                
                // Jika unoconv tidak ada, command alternatif (uncomment line di bawah):
                // $cmd = "libreoffice --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg($upload_dir);
            }

            // Eksekusi Command
            exec($cmd, $output, $returnCode);

            // Cek apabila berhasil
            if ($returnCode !== 0 || !file_exists($pdfOutput)) {
                echo "Failed converting PDF: $pdfFileName\n";
            }

            // Cleanup: Hapus docx untuk menghemat kapasitas storage
            if (file_exists($docxPath)) {
                unlink($docxPath);
            }
            
            // Beri jeda 50 milidetik agar CPU tidak dipaksa ke 100% Usage (Aman dari Spike/Crash)
            usleep(50000); 

            // Update counter
            $processed++;

            // Update status ke Database kelipatan 10 (Agar I/O DB ringan) atau saat record terakhir
            if ($processed % 10 == 0 || $processed == $total) {
                $this->update_progress($processed, $total);
            }
        }

        // Tandai Selesai
        $this->Batch_model->update_status([
            'status' => 'completed',
            'progress' => $total
        ]);
        
        echo "\n[ OK ] Batch Pengumuman Processing terlah selesai!\n";
    }

    private function update_progress($processed, $total)
    {
        $this->Batch_model->update_status([
            'progress' => $processed,
            'total'    => $total
        ]);
        // Logging progress ke stdout (jika dipanggil via cron)
        echo "Processed {$processed} / {$total} \n";
    }
}
