<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class Siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Siswa_model');
        $this->load->model('Jurusan_model');
        $this->load->model('Mata_pelajaran_model');
        $this->load->model('Nilai_model');
        $this->load->library('form_validation');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }
    public function index() {
        $this->load->model('Siswa_model');
        $this->load->model('Jurusan_model');
        $this->load->model('Mata_pelajaran_model');
        $this->load->model('Nilai_model');
    
        $data['siswa'] = $this->db->get('siswa')->result();
    
        $this->load->view('templates/header');
        $this->load->view('siswa/index', $data);
        $this->load->view('templates/footer');
    }
    public function detail($id) {
        $this->load->model('Nilai_model');
        $this->load->model('Mata_pelajaran_model');
        $this->load->model('Jurusan_model');
    
        $siswa = $this->db->get_where('siswa', ['id' => $id])->row();
        $nilai = $this->Nilai_model->get_nilai_with_mapel($id);
    
        echo json_encode([
            'siswa' => $siswa,
            'nilai' => $nilai
        ]);
    }    
    public function create() {
        if ($this->input->post()) {
            $siswa_data = [
                'user_id'        => $this->session->userdata('user_id'),
                'no_surat'       => $this->input->post('no_surat'),
                'nama_lengkap'   => $this->input->post('nama_lengkap'),
                'tempat_lahir'   => $this->input->post('tempat_lahir'),
                'tanggal_lahir'  => $this->input->post('tanggal_lahir'),
                'nis'            => $this->input->post('nis'),
                'nisn'           => $this->input->post('nisn'),
                'no_ujian'       => $this->input->post('no_ujian'),
                'kelas'          => $this->input->post('kelas'),
                'status'         => $this->input->post('status'),
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $existing = $this->Siswa_model->get_by_nis($siswa_data['nis']);
            
            if ($existing) {
                $this->Siswa_model->update($existing->id, $siswa_data);
            } else {
                $this->Siswa_model->insert($siswa_data);
            }

            $this->session->set_flashdata('import_success', "Berhasil menyimpan data siswa secara manual.");
            redirect('siswa');
        }

        $this->load->view('templates/header');
        $this->load->view('siswa/create');
        $this->load->view('templates/footer');
    }

    public function get_mapel_by_jurusan() {
        $jurusan_id = $this->input->post('jurusan_id');
        $mapel = $this->Mata_pelajaran_model->get_by_jurusan($jurusan_id);
        echo json_encode($mapel);
    }
    public function import()
    {
        $this->load->library('upload');

        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size']      = 2048;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file_excel')) {
            $data['error'] = $this->upload->display_errors();
            $this->load->view('templates/header');
            $this->load->view('siswa/import', $data);
            $this->load->view('templates/footer');
        } else {
            $file = $this->upload->data('full_path');

            // Pakai PhpSpreadsheet
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $data['preview'] = [];
            $headers = [];

            foreach ($sheet as $i => $row) {
                if ($i == 1) {
                    $headers = $row;
                    continue; // Skip header row for data processing
                }

                // Map row data using headers
                $mapped_row = [];
                foreach ($headers as $col_letter => $header_name) {
                    if ($header_name) {
                        $trimmed_header = trim($header_name);
                        if (isset($mapped_row[$trimmed_header])) {
                            $mapped_row[$trimmed_header . '_subject'] = isset($row[$col_letter]) ? $row[$col_letter] : null;
                        } else {
                            $mapped_row[$trimmed_header] = isset($row[$col_letter]) ? $row[$col_letter] : null;
                        }
                    }
                }

                $raw_tgl = $mapped_row['Tanggal Lahir'] ?? null;
                $tanggal_lahir = null;
                if (!empty($raw_tgl)) {
                    if (is_numeric($raw_tgl)) {
                        try {
                            $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($raw_tgl)->format('Y-m-d');
                        } catch (Exception $e) {
                            $tanggal_lahir = null;
                        }
                    } else {
                        // Ganti '/' dengan '-' agar format d/m/Y diproses dengan benar sebagai d-m-Y oleh strtotime
                        $time = strtotime(str_replace('/', '-', $raw_tgl));
                        if ($time) {
                            $tanggal_lahir = date('Y-m-d', $time);
                        } else {
                            $tanggal_lahir = null;
                        }
                    }
                }

                // Identify basic info based on SKL Format
                $siswa = [
                    'no_surat'             => $mapped_row['Nomer Surat'] ?? $mapped_row['Nomor Surat'] ?? $mapped_row['No Surat'] ?? null,
                    'nama_lengkap'         => $mapped_row['Nama Lengkap Siswa'] ?? $mapped_row['Nama Lengkap'] ?? $mapped_row['Nama'] ?? null,
                    'nis'                  => $mapped_row['Nomor Induk Siswa'] ?? $mapped_row['NIS'] ?? null,
                    'nisn'                 => $mapped_row['NISN'] ?? null,
                    'kelas'                => $mapped_row['Kelas Siswa'] ?? $mapped_row['Kelas'] ?? null,
                    'no_ujian'             => $mapped_row['Nomor Ujian'] ?? $mapped_row['No Ujian'] ?? null,
                    'tempat_lahir'         => $mapped_row['Tempat Lahir'] ?? null,
                    'tanggal_lahir'        => $tanggal_lahir,
                    'status'               => $mapped_row['Status Lulus'] ?? $mapped_row['Status'] ?? $mapped_row['Keterangan'] ?? null,
                    'kurikulum'            => $mapped_row['Kurikulum'] ?? null,
                    'program_keahlian'     => $mapped_row['Program Keahlian'] ?? null,
                    'konsentrasi_keahlian' => $mapped_row['Konsentrasi Keahlian'] ?? null,
                    'tanggal_kelulusan'    => $mapped_row['Tanggal Kelulusan'] ?? null,
                    'no_ijazah'            => $mapped_row['Nomor Ijazah'] ?? $mapped_row['No Ijazah'] ?? null,
                ];

                // Fields used for basic student details
                $siswa_fields = [
                    'Nomer Surat', 'Nomor Surat', 'No Surat', 'No', 'Nama Lengkap Siswa', 'Nama Lengkap', 'Nama', 'Nomor Induk Siswa', 'NIS', 'NISN',
                    'Kelas Siswa', 'Kelas', 'Nomor Ujian', 'No Ujian', 'Tempat Lahir', 'Tanggal Lahir',
                    'Status Lulus', 'Status', 'Keterangan', 'Kurikulum', 'Program Keahlian',
                    'Konsentrasi Keahlian', 'Tanggal Kelulusan', 'Nomor Ijazah', 'No Ijazah'
                ];

                // Normalize mapping for subjects
                $subject_mappings = [
                    'Konsentrasi Keahlian_subject' => 'Konsentrasi Keahlian',
                    'Pendidikan Agama' => 'Pendidikan Agama dan Budi Pekerti',
                    'Pendidikan Agama Islam' => 'Pendidikan Agama dan Budi Pekerti',
                    'PJOK' => 'Pendidikan Jasmani, Olahraga dan Kesehatan',
                    'Projek IPAS' => 'Projek Ilmu Pengetahuan Alam dan Sosial',
                    'Dasar Program Keahlian' => 'Dasar-dasar Program Keahlian',
                    'Kreativitas Inovasi dan Kewirausahaan' => 'Kreativitas, Inovasi, dan Kewirausahaan',
                    'PKL' => 'Praktik Kerja Lapangan',
                    'Mapel Pilihan' => 'Mata Pelajaran Pilihan'
                ];

                // Parsing dynamic subjects
                $nilai = [];
                foreach ($mapped_row as $header_name => $col_val) {
                    if (!in_array($header_name, $siswa_fields) && !empty($header_name)) {
                        $normalized_name = $subject_mappings[$header_name] ?? $header_name;
                        $nilai[$normalized_name] = $col_val;
                    }
                }

                $siswa['nilai'] = $nilai;

                if (!empty($siswa['nis']) || !empty($siswa['nama_lengkap'])) {
                    $data['preview'][] = $siswa;
                }
            }

            $this->session->set_userdata('preview_data', $data['preview']);
            $this->load->view('templates/header');
            $this->load->view('siswa/import_preview', $data);
            $this->load->view('templates/footer');
        }
    }

    public function do_import()
    {
        $preview = $this->session->userdata('preview_data');
        if ($preview) {
            $berhasil = 0;
            $gagal = 0;
            foreach ($preview as $row) {
                // Parse scores and calculate average
                $scores = $row['nilai'] ?? [];
                $total_nilai = 0;
                $count_mapel = 0;

                foreach ($scores as $nama_mapel => $nilai_angka) {
                    if (is_numeric($nilai_angka)) {
                        $total_nilai += floatval($nilai_angka);
                        $count_mapel++;
                    }
                }
                $rata_rata = ($count_mapel > 0) ? ($total_nilai / $count_mapel) : 0;

                $siswa_data = [
                    'user_id'              => $this->session->userdata('user_id'),
                    'no_surat'             => $row['no_surat'] ?? null,
                    'nama_lengkap'         => $row['nama_lengkap'],
                    'tempat_lahir'         => $row['tempat_lahir'],
                    'tanggal_lahir'        => $row['tanggal_lahir'],
                    'nis'                  => $row['nis'],
                    'nisn'                 => $row['nisn'],
                    'no_ujian'             => $row['no_ujian'],
                    'kelas'                => $row['kelas'],
                    'status'               => $row['status'],
                    'kurikulum'            => $row['kurikulum'] ?? null,
                    'program_keahlian'     => $row['program_keahlian'] ?? null,
                    'konsentrasi_keahlian' => $row['konsentrasi_keahlian'] ?? null,
                    'tanggal_kelulusan'    => $row['tanggal_kelulusan'] ?? null,
                    'no_ijazah'            => $row['no_ijazah'] ?? null,
                    'rata_rata'            => $rata_rata,
                    'created_at'           => date('Y-m-d H:i:s')
                ];

                // Cek if exists by NIS
                $existing = $this->Siswa_model->get_by_nis($row['nis']);

                if ($existing) {
                    $siswa_id = $existing->id;
                    if ($this->Siswa_model->update($siswa_id, $siswa_data)) {
                        $berhasil++;
                    } else {
                        $gagal++;
                    }
                } else {
                    $siswa_id = $this->Siswa_model->insert($siswa_data);
                    if ($siswa_id) {
                        $berhasil++;
                    } else {
                        $gagal++;
                    }
                }

                // Insert or update scores
                if ($siswa_id) {
                    $this->db->delete('nilai_siswa', ['siswa_id' => $siswa_id]);
                    foreach ($scores as $nama_mapel => $nilai_angka) {
                        if (is_numeric($nilai_angka)) {
                            // Match existing subject or insert
                            $mapel = $this->db->get_where('mata_pelajaran', ['nama_mata_pelajaran' => $nama_mapel])->row();
                            if (!$mapel) {
                                $this->db->insert('mata_pelajaran', ['nama_mata_pelajaran' => $nama_mapel]);
                                $mapel_id = $this->db->insert_id();
                            } else {
                                $mapel_id = $mapel->id;
                            }

                            $this->db->insert('nilai_siswa', [
                                'siswa_id' => $siswa_id,
                                'mapel_id' => $mapel_id,
                                'nilai'    => floatval($nilai_angka)
                            ]);
                        }
                    }
                }
            }
            $this->session->unset_userdata('preview_data');
            $this->session->set_flashdata('import_success', "Berhasil import $berhasil data siswa. Gagal: $gagal data.");
        }
        redirect('siswa');
    }

    
    public function test_word()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText("Hello World from PHPWord!");

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('HelloWorld.docx');
        echo "File HelloWorld.docx berhasil dibuat!";
    }

    public function trigger_pdf_batch()
    {
        $this->load->model('Batch_model');
        $status = $this->Batch_model->get_status();

        if ($status && $status->status == 'processing') {
            echo json_encode(['status' => 'error', 'message' => 'Proses generate sedang berjalan!']);
            return;
        }

        // Ambil parameter mode (skip/overwrite) dari POST
        $mode = $this->input->post('mode') ? $this->input->post('mode') : 'skip';

        // Buat folder log jika belum ada
        $log_dir = FCPATH . "application/logs/batch/";
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        $log_file = $log_dir . "generate_" . date('Y_m_d') . ".log";
        $date = date('Y-m-d H:i:s');
        
        $msg = "[{$date}] [INFO] Request batch generate dimulai dengan mode: {$mode}" . PHP_EOL;
        file_put_contents($log_file, $msg, FILE_APPEND);

        // Segera kunci database menjadi "processing" dengan progress 0 agar UI tidak melihat status 'completed' yang lama
        $this->Batch_model->update_status(['status' => 'processing', 'progress' => 0]);

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            $cmd = "start /B php " . escapeshellarg(FCPATH . "index.php") . " skl_generator generate_pengumuman_batch " . escapeshellarg($mode);
            $msg_cmd = "[{$date}] [INFO] Menjalankan command background (Windows): {$cmd}" . PHP_EOL;
            file_put_contents($log_file, $msg_cmd, FILE_APPEND);
            pclose(popen($cmd, "r"));
        } else {
            // Gunakan path PHP dinamis untuk Linux / hosting environment
            $php_bin = 'php';
            if (file_exists('/Applications/XAMPP/xamppfiles/bin/php')) {
                $php_bin = '/Applications/XAMPP/xamppfiles/bin/php';
            } elseif (file_exists('/usr/bin/php')) {
                $php_bin = '/usr/bin/php';
            } elseif (file_exists('/usr/local/bin/php')) {
                $php_bin = '/usr/local/bin/php';
            } elseif (file_exists('/opt/lampp/bin/php')) {
                $php_bin = '/opt/lampp/bin/php';
            }
            
            $cmd = "{$php_bin} " . escapeshellarg(FCPATH . "index.php") . " skl_generator generate_pengumuman_batch " . escapeshellarg($mode) . " >> " . escapeshellarg($log_file) . " 2>&1 &";
            
            $msg_cmd = "[{$date}] [INFO] Menjalankan command background: {$cmd}" . PHP_EOL;
            file_put_contents($log_file, $msg_cmd, FILE_APPEND);
            exec($cmd);
        }

        echo json_encode(['status' => 'success', 'message' => 'Proses Batch Generate PDF telah dimulai di background.']);
    }

    public function stop_pdf_batch()
    {
        $this->load->model('Batch_model');
        $status = $this->Batch_model->get_status();
        
        if ($status && $status->status == 'processing') {
            $this->Batch_model->update_status(['status' => 'stopped']);
            echo json_encode(['status' => 'success', 'message' => 'Perintah membatalkan proses berhasil dikirim ke background.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada proses yang sedang berjalan.']);
        }
    }

    public function check_pdf_progress()
    {
        $this->load->model('Batch_model');
        $status = $this->Batch_model->get_status();
        
        if ($status) {
            echo json_encode([
                'status' => $status->status,
                'progress' => $status->progress,
                'total' => $status->total,
                'updated_at' => $status->updated_at
            ]);
        } else {
            echo json_encode(['status' => 'idle', 'progress' => 0, 'total' => 0]);
        }
    }
}

