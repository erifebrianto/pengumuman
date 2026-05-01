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
            // Simpan siswa
            $siswa_data = [
                'user_id'        => $this->session->userdata('user_id'),
                'nama_lengkap'   => $this->input->post('nama_lengkap'),
                'tempat_lahir'   => $this->input->post('tempat_lahir'),
                'tanggal_lahir'  => $this->input->post('tanggal_lahir'),
                'nis'            => $this->input->post('nis'),
                'nisn'           => $this->input->post('nisn'),
                'no_hp'          => $this->input->post('no_hp'),
                'no_ujian'       => $this->input->post('no_ujian'),
                'kelas'          => $this->input->post('kelas'),
                'nama_ortu'      => $this->input->post('nama_ortu'),
                'rata_rata'      => $this->input->post('rata_rata'),
                'status'         => $this->input->post('status'),
                'created_at'     => date('Y-m-d H:i:s')
            ];
            $siswa_id = $this->Siswa_model->insert($siswa_data);

            // Simpan nilai siswa
            $nilai = $this->input->post('nilai');
            $nilai_data = [];
            foreach ($nilai as $mapel_id => $val) {
                $nilai_data[] = [
                    'siswa_id'  => $siswa_id,
                    'mapel_id'  => $mapel_id,
                    'nilai'     => $val
                ];
            }
            $this->Nilai_model->insert_batch($nilai_data);

            redirect('siswa');
        }

        $data['jurusan'] = $this->Jurusan_model->get_all();

        $this->load->view('templates/header');
        $this->load->view('siswa/create', $data);
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
        $config['allowed_types'] = 'xls|xlsx';
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
                        $mapped_row[trim($header_name)] = isset($row[$col_letter]) ? $row[$col_letter] : null;
                    }
                }

                // Identify basic info (flexible names)
                $siswa = [
                    'nama_lengkap'  => $mapped_row['Nama'] ?? $mapped_row['Nama Lengkap'] ?? null,
                    'tempat_lahir'  => $mapped_row['Tempat Lahir'] ?? null,
                    'tanggal_lahir' => $mapped_row['Tanggal Lahir'] ?? null,
                    'nis'           => $mapped_row['NIS'] ?? null,
                    'nisn'          => $mapped_row['NISN'] ?? null,
                    'no_hp'         => $mapped_row['No HP'] ?? $mapped_row['Nomor HP'] ?? null,
                    'no_ujian'      => $mapped_row['No Ujian'] ?? $mapped_row['Nomor Ujian'] ?? null,
                    'kelas'         => $mapped_row['Kelas'] ?? null,
                    'jurusan'       => $mapped_row['Jurusan'] ?? null,
                    'nama_ortu'     => $mapped_row['Nama Ortu'] ?? $mapped_row['Wali'] ?? null,
                    'rata_rata'     => $mapped_row['Rata-rata'] ?? $mapped_row['Rata Rata'] ?? null,
                    'status'        => $mapped_row['Status'] ?? $mapped_row['Keterangan'] ?? null,
                    'nilai_mapel'   => []
                ];

                // Anything else is considered Mapel
                $not_mapel = ['Nama', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'NIS', 'NISN', 'No HP', 'Nomor HP', 'No Ujian', 'Nomor Ujian', 'Kelas', 'Jurusan', 'Nama Ortu', 'Wali', 'Rata-rata', 'Rata Rata', 'Status', 'Keterangan'];
                
                foreach ($mapped_row as $key => $value) {
                    if (!in_array($key, $not_mapel) && !empty($key)) {
                        $siswa['nilai_mapel'][] = [
                            'mapel' => $key,
                            'nilai' => $value
                        ];
                    }
                }

                if (!empty($siswa['nisn']) || !empty($siswa['nama_lengkap'])) {
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
            foreach ($preview as $row) {
                // Resolve Jurusan
                $jurusan = null;
                if (!empty($row['jurusan'])) {
                    $jurusan = $this->Jurusan_model->get_by_name($row['jurusan']);
                }

                $siswa_data = [
                    'user_id'       => $this->session->userdata('user_id'),
                    'jurusan_id'    => $jurusan ? $jurusan->id : null,
                    'nama_lengkap'  => $row['nama_lengkap'],
                    'tempat_lahir'  => $row['tempat_lahir'],
                    'tanggal_lahir' => $row['tanggal_lahir'],
                    'nis'           => $row['nis'],
                    'nisn'          => $row['nisn'],
                    'no_hp'         => $row['no_hp'],
                    'no_ujian'      => $row['no_ujian'],
                    'kelas'         => $row['kelas'],
                    'nama_ortu'     => $row['nama_ortu'],
                    'rata_rata'     => $row['rata_rata'],
                    'status'        => $row['status'],
                    'created_at'    => date('Y-m-d H:i:s')
                ];

                // Cek if exists by NISN or NIS
                $existing = $this->Siswa_model->get_by_nis($row['nis']);
                if (!$existing && !empty($row['nisn'])) {
                    $existing = $this->db->get_where('siswa', ['nisn' => $row['nisn']])->row();
                }

                if ($existing) {
                    $this->Siswa_model->update($existing->id, $siswa_data);
                    $siswa_id = $existing->id;
                    // Delete old scores
                    $this->db->delete('nilai_siswa', ['siswa_id' => $siswa_id]);
                } else {
                    $siswa_id = $this->Siswa_model->insert($siswa_data);
                }

                // Import Nilai
                if ($jurusan && isset($row['nilai_mapel']) && is_array($row['nilai_mapel'])) {
                    foreach ($row['nilai_mapel'] as $pair) {
                        if ($pair['nilai'] === null || $pair['nilai'] === '') continue;

                        $mapel = $this->Mata_pelajaran_model->get_by_name_and_jurusan($pair['mapel'], $jurusan->id);
                        if ($mapel) {
                            $this->Nilai_model->create([
                                'siswa_id' => $siswa_id,
                                'mapel_id' => $mapel->id,
                                'nilai'    => $pair['nilai']
                            ]);
                        }
                    }
                }
            }
            $this->session->unset_userdata('preview_data');
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
            if (file_exists('/usr/bin/php')) {
                $php_bin = '/usr/bin/php';
            } elseif (file_exists('/usr/local/bin/php')) {
                $php_bin = '/usr/local/bin/php';
            } elseif (file_exists('/opt/lampp/bin/php')) {
                $php_bin = '/opt/lampp/bin/php';
            }
            
            $cmd = "{$php_bin} " . escapeshellarg(FCPATH . "index.php") . " skl_generator generate_pengumuman_batch " . escapeshellarg($mode) . " > /dev/null 2>&1 &";
            
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

