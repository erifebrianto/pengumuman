<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
    
}
