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

            foreach ($sheet as $i => $row) {
                if ($i == 1) continue; // Skip header

                $siswa = [
                    'nama_lengkap' => $row['A'],
                    'tempat_lahir' => $row['B'],
                    'tanggal_lahir' => $row['C'],
                    'nis' => $row['D'],
                    'nisn' => $row['E'],
                    'no_ujian' => $row['F'], // Tambahan: Nomor Ujian
                    'kelas' => $row['G'],
                    'nama_ortu' => $row['H'],
                    'rata_rata' => $row['I'],
                    'status' => $row['J'],
                    'nilai_mapel' => []
                ];

                // Mulai dari kolom K untuk mapel/nilai
                $col = 'K';
                while (isset($row[$col]) && $row[$col] !== null) {
                    $mapel = $row[$col];
                    $col_nilai = ++$col;
                    $nilai = isset($row[$col_nilai]) ? $row[$col_nilai] : null;

                    if ($mapel && $nilai !== null) {
                        $siswa['nilai_mapel'][] = [
                            'mapel' => $mapel,
                            'nilai' => $nilai
                        ];
                    }
                    $col = ++$col; // Lanjut ke pasangan mapel/nilai berikutnya
                }

                $data['preview'][] = $siswa;
            }

            $this->session->set_userdata('preview_data', $data['preview']);
            $this->load->view('templates/header');
            $this->load->view('siswa/import_preview', $data);
            $this->load->view('templates/footer');
        }
    }

    public function do_import()
    {
        $this->load->model('Mapel_model');

        $preview = $this->session->userdata('preview_data');
        if ($preview) {
            foreach ($preview as $row) {
                $siswa_data = [
                    'user_id'       => $this->session->userdata('user_id'),
                    'nama_lengkap'  => $row['nama_lengkap'],
                    'tempat_lahir'  => $row['tempat_lahir'],
                    'tanggal_lahir' => $row['tanggal_lahir'],
                    'nis'           => $row['nis'],
                    'nisn'          => $row['nisn'],
                    'no_ujian'      => $row['no_ujian'], // Tambahan
                    'kelas'         => $row['kelas'],
                    'nama_ortu'     => $row['nama_ortu'],
                    'rata_rata'     => $row['rata_rata'],
                    'status'        => $row['status'],
                    'created_at'    => date('Y-m-d H:i:s')
                ];

                $this->Siswa_model->create($siswa_data);
                $siswa_id = $this->db->insert_id();

                if (isset($row['nilai_mapel']) && is_array($row['nilai_mapel'])) {
                    foreach ($row['nilai_mapel'] as $pair) {
                        $mapel = $this->Mapel_model->get_by_name($pair['mapel']);
                        if ($mapel) {
                            $this->Nilai_model->create([
                                'siswa_id' => $siswa_id,
                                'mapel_id' => $mapel['id'],
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

    
}
