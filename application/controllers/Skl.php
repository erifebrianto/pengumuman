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
        $this->db->query("ALTER TABLE pengaturan MODIFY verification_method varchar(255) DEFAULT 'exam_number_nis'");
        $this->db->query("ALTER TABLE siswa MODIFY rata_rata decimal(5,2) DEFAULT NULL");

        $short_map = [
            'Pendidikan Agama Islam dan Budi Pekerti' => 'n_agama',
            'Pendidikan Pancasila' => 'n_pancasila',
            'Bahasa Indonesia' => 'n_indonesia',
            'Pendidikan Jasmani, Olahraga dan Kesehatan' => 'n_pjok',
            'Sejarah' => 'n_sejarah',
            'Seni Budaya' => 'n_seni',
            'Matematika' => 'n_matematika',
            'Bahasa Inggris' => 'n_inggris',
            'Informatika' => 'n_informatika',
            'Projek Ilmu Pengetahuan Alam dan Sosial' => 'n_ipas',
            'Dasar-dasar Program Keahlian' => 'n_dpk',
            'Konsentrasi Keahlian' => 'n_kk',
            'Kreativitas, Inovasi, dan Kewirausahaan' => 'n_pkk',
            'Praktik Kerja Lapangan' => 'n_pkl',
            'Mata Pelajaran Pilihan' => 'n_pilihan',
            'Bahasa Jawa' => 'n_jawa',
        ];
        foreach ($short_map as $mapel_name => $kode) {
            $existing = $this->db->get_where('mata_pelajaran', ['nama_mata_pelajaran' => $mapel_name])->row();
            if ($existing) {
                if ($existing->kode_mapel !== $kode) {
                    $this->db->query("UPDATE mata_pelajaran SET kode_mapel = ? WHERE id = ?", [$kode, $existing->id]);
                }
            } else {
                $this->db->insert('mata_pelajaran', [
                    'nama_mata_pelajaran' => $mapel_name,
                    'kode_mapel' => $kode
                ]);
            }
        }
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
        if (!in_array($method, ['nisn', 'nis_nisn', 'nis_nama', 'exam_number_nis', 'nisn_exam_number', 'nis', 'exam_number'])) {
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
        if (!in_array($method, ['nisn', 'nis_nisn', 'nis_nama', 'exam_number_nis', 'nisn_exam_number', 'nis', 'exam_number'])) {
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
            case 'exam_number':
                $no_ujian = $this->input->post('no_ujian');
                if (empty($no_ujian)) redirect('skl/search');
                $fields = ['no_ujian' => trim($no_ujian)];
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
            // Path
            $templatePath = FCPATH . 'template/skl_template.docx';
            $docxPath = FCPATH . 'temp/skl_' . $siswa->nis . '.docx';
            $pdfPath  = FCPATH . 'temp/skl_' . $siswa->nis . '.pdf';

            // Generate Word
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

            // Deteksi OS dan jalur LibreOffice
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
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
                $loProfile = FCPATH . "temp/lo_profile_single_" . $siswa->nis . "_" . rand(100, 999);
                
                $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg(FCPATH . 'temp/') . " 2>&1";
                
                $outputStr = $this->safe_shell_exec($cmd);
                $returnCode = ($outputStr === false || $outputStr === null || strpos($outputStr, 'Error') !== false) ? 1 : 0;

                // Cleanup temporary background LibreOffice profile
                if (is_dir($loProfile)) {
                    $this->safe_shell_exec("rm -rf " . escapeshellarg($loProfile));
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

            // Deteksi OS dan jalur LibreOffice
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                $sofficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $cmd = $sofficePath . ' --headless --convert-to pdf ' . escapeshellarg($docxPath) . ' --outdir ' . escapeshellarg(FCPATH . 'temp/');
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
                $loProfile = FCPATH . "temp/lo_profile_wa_" . $siswa->nis . "_" . rand(100, 999);
                
                $cmd = "env LD_LIBRARY_PATH=\"\" " . escapeshellcmd($sofficeOptPath) . " -env:UserInstallation=file://" . escapeshellarg($loProfile) . " --headless --invisible --nologo --nodefault --convert-to pdf " . escapeshellarg($docxPath) . " --outdir " . escapeshellarg(FCPATH . 'temp/') . " 2>&1";
                
                $outputStr = $this->safe_shell_exec($cmd);
                $returnCode = ($outputStr === false || $outputStr === null || strpos($outputStr, 'Error') !== false) ? 1 : 0;

                // Cleanup temporary background LibreOffice profile
                if (is_dir($loProfile)) {
                    $this->safe_shell_exec("rm -rf " . escapeshellarg($loProfile));
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
