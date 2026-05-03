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
        @file_put_contents($file, $formatted_message, FILE_APPEND);
        
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

            // Call method internal directly to ensure it works on both local and hosting
            ob_start();
            $this->process_single($siswa->nis, $mode);
            $result = trim(ob_get_clean());

            if (strpos($result, "Success") !== false || strpos($result, "Skipped") !== false) {
                $sukses++;
            } else {
                $this->write_log("Gagal konversi PDF: NIS {$siswa->nis}. Reason: {$result}", "ERROR");
                $gagal++;
            } 

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
            $templateProcessor->setValue('no_surat', $siswa->no_surat ?? '-');
            $templateProcessor->setValue('nama_lengkap', $siswa->nama_lengkap);
            $templateProcessor->setValue('nis', $siswa->nis);
            $templateProcessor->setValue('kelas', $siswa->kelas);
            $templateProcessor->setValue('no_ujian', $siswa->no_ujian);
            $templateProcessor->setValue('tempat_lahir', $siswa->tempat_lahir ?? '-');
            
            $tanggal_lahir_formatted = '-';
            if (!empty($siswa->tanggal_lahir) && $siswa->tanggal_lahir !== '-') {
                $raw_date = str_replace('/', '-', $siswa->tanggal_lahir);
                $time = strtotime($raw_date);
                if ($time) {
                    $months = [
                        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    $d = date('d', $time);
                    $m = (int)date('m', $time);
                    $y = date('Y', $time);
                    $tanggal_lahir_formatted = $d . ' ' . $months[$m] . ' ' . $y;
                } else {
                    $tanggal_lahir_formatted = $siswa->tanggal_lahir;
                }
            }
            $templateProcessor->setValue('tanggal_lahir', $tanggal_lahir_formatted);
            $templateProcessor->setValue('nisn', $siswa->nisn ?? '-');
            $templateProcessor->setValue('kurikulum', $siswa->kurikulum ?? '-');
            $templateProcessor->setValue('program_keahlian', $siswa->program_keahlian ?? '-');
            $templateProcessor->setValue('konsentrasi_keahlian', $siswa->konsentrasi_keahlian ?? '-');
            $templateProcessor->setValue('tanggal_kelulusan', $siswa->tanggal_kelulusan ?? '-');
            $templateProcessor->setValue('no_ijazah', $siswa->no_ijazah ?? '-');
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
            $siswa_scores = [];
            foreach ($nilai_siswa as $n) {
                if (is_numeric($n->nilai)) {
                    $siswa_scores[$n->nama_mata_pelajaran] = $n->nilai;
                }
            }
            $total_nilai = 0;
            $count_mapel = 0;
            foreach ($siswa_scores as $nama_mapel => $nilai_angka) {
                $total_nilai += (float)$nilai_angka;
                $count_mapel++;
            }
            $rata_rata_siswa = ($count_mapel > 0) ? ($total_nilai / $count_mapel) : 0;
            $templateProcessor->setValue('rata_rata', number_format($rata_rata_siswa, 2));

            $all_mapel = $this->db->get('mata_pelajaran')->result();
            foreach ($all_mapel as $mp) {
                $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $mp->nama_mata_pelajaran));
                $clean_name_collapsed = preg_replace('/_+/', '_', $clean_name);

                if (isset($siswa_scores[$mp->nama_mata_pelajaran]) && is_numeric($siswa_scores[$mp->nama_mata_pelajaran])) {
                    $nilai_val = $siswa_scores[$mp->nama_mata_pelajaran];
                    $templateProcessor->setValue('n_' . $clean_name, $nilai_val);
                    if ($clean_name !== $clean_name_collapsed) {
                        $templateProcessor->setValue('n_' . $clean_name_collapsed, $nilai_val);
                    }
                    if (!empty($mp->kode_mapel)) {
                        $templateProcessor->setValue($mp->kode_mapel, $nilai_val);
                    }
                } else {
                    try {
                        $templateProcessor->deleteRow('n_' . $clean_name);
                    } catch (Exception $e) {
                        $templateProcessor->setValue('n_' . $clean_name, '');
                    }
                    try {
                        if ($clean_name !== $clean_name_collapsed) {
                            $templateProcessor->deleteRow('n_' . $clean_name_collapsed);
                        }
                    } catch (Exception $e) {
                        if ($clean_name !== $clean_name_collapsed) {
                            $templateProcessor->setValue('n_' . $clean_name_collapsed, '');
                        }
                    }
                    try {
                        if (!empty($mp->kode_mapel)) {
                            $templateProcessor->deleteRow($mp->kode_mapel);
                        }
                    } catch (Exception $e) {
                        if (!empty($mp->kode_mapel)) {
                            $templateProcessor->setValue($mp->kode_mapel, '');
                        }
                    }
                }
            }

            // Opsi: Tabel dinamis via ${tabel_nilai}
            $table = new \PhpOffice\PhpWord\Element\Table([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80
            ]);
            $table->addRow();
            $table->addCell(800)->addText("No", ['bold' => true]);
            $table->addCell(6000)->addText("Mata Pelajaran", ['bold' => true]);
            $table->addCell(1200)->addText("Nilai", ['bold' => true]);

            $no_table = 1;
            foreach ($nilai_siswa as $n) {
                if (is_numeric($n->nilai)) {
                    $table->addRow();
                    $table->addCell(800)->addText($no_table++);
                    $table->addCell(6000)->addText($n->nama_mata_pelajaran);
                    $table->addCell(1200)->addText(number_format((float)$n->nilai, 2));
                }
            }
            $templateProcessor->setComplexValue('tabel_nilai', $table);

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

            // Perbaiki nomor urut tabel (re-numbering)
            $reflection = new ReflectionClass($templateProcessor);
            $property = $reflection->getProperty('tempDocumentMainPart');
            $property->setAccessible(true);
            $xml = $property->getValue($templateProcessor);

            if (preg_match_all('/<w:tr[^>]*>.*?<\/w:tr>/is', $xml, $matches)) {
                $current_expected = 1;
                $has_numbering = false;
                foreach ($matches[0] as $index => $row_xml) {
                    if (preg_match('/<w:tc[^>]*>.*?<\/w:tc>/is', $row_xml, $cell_match)) {
                        $first_cell = $cell_match[0];
                        if (preg_match('/<w:t[^>]*>([1-9]|[12][0-9]|30)\s*\.?\s*<\/w:t>/is', $first_cell, $num_match)) {
                            $num = intval($num_match[1]);
                            if ($has_numbering && $num <= $current_expected) {
                                $current_expected = $num;
                            }
                            $has_numbering = true;
                            if ($num != $current_expected) {
                                $new_cell = preg_replace('/(<w:t[^>]*>)[1-9]([0-9])?(\s*\.?\s*<\/w:t>)/is', '${1}' . $current_expected . '${3}', $first_cell, 1);
                                $new_row = str_replace($first_cell, $new_cell, $row_xml);
                                $xml = str_replace($row_xml, $new_row, $xml);
                                $matches[0][$index] = $new_row;
                            }
                            $current_expected++;
                        }
                    }
                }
                $property->setValue($templateProcessor, $xml);
            }

            $templateProcessor->saveAs($docxPath);

        } catch (Exception $e) {
            echo "Error Word: " . $e->getMessage();
            return;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
            $cmd = $sofficePath . ' --headless --invisible --nologo --nodefault --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg($upload_dir);
            if (function_exists('exec')) {
                @exec($cmd, $output, $returnCode);
            } else {
                $returnCode = 1;
            }
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
            $loProfile = $cli_temp_dir . "lo_profile_" . $siswa->nis . "_" . rand(100, 999);
            $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg($upload_dir) . " 2>&1";
            
            $outputStr = $this->safe_shell_exec($cmd);

            if (is_dir($loProfile)) {
                $this->safe_shell_exec("rm -rf " . escapeshellarg($loProfile));
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

    private function safe_shell_exec($cmd)
    {
        if (function_exists('shell_exec')) {
            return @shell_exec($cmd);
        } elseif (function_exists('exec')) {
            @exec($cmd, $outputArray);
            return implode("\n", $outputArray);
        } elseif (function_exists('system')) {
            ob_start();
            @system($cmd);
            return ob_get_clean();
        } elseif (function_exists('passthru')) {
            ob_start();
            @passthru($cmd);
            return ob_get_clean();
        }
        return false;
    }
}
