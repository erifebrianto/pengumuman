<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Siswa_model');
        $this->load->model('Nilai_model');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['title'] = 'Data Siswa';
        $data['siswa'] = $this->Siswa_model->get_all();
        $this->load->view('templates/header', $data);
        $this->load->view('siswa/index', $data);
        $this->load->view('templates/footer');
    }
    public function create()
    {
        $this->load->model('Siswa_model');
        $this->load->model('Nilai_model');
        $this->load->model('Mapel_model');

        if ($this->input->post()) {
            // Simpan data siswa
            $siswa_data = [
                'user_id'       => $this->session->userdata('user_id'),
                'nama_lengkap'  => $this->input->post('nama_lengkap'),
                'tempat_lahir'  => $this->input->post('tempat_lahir'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'nis'           => $this->input->post('nis'),
                'nisn'          => $this->input->post('nisn'),
                'kelas'         => $this->input->post('kelas'),
                'nama_ortu'     => $this->input->post('nama_ortu'),
                'rata_rata'     => $this->input->post('rata_rata'),
                'status'        => $this->input->post('status'),
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $this->Siswa_model->create($siswa_data);
            $siswa_id = $this->db->insert_id();

            // Simpan nilai siswa berdasarkan input form dinamis
            $mata_pelajaran = $this->input->post('mata_pelajaran');
            $nilai = $this->input->post('nilai');

            $nilai_data = [];
            if (!empty($mata_pelajaran)) {
                foreach ($mata_pelajaran as $index => $mapel) {
                    $nilai_data[] = [
                        'siswa_id'        => $siswa_id,
                        'mata_pelajaran'  => $mapel,
                        'nilai'           => $nilai[$index],
                    ];
                }
                $this->Nilai_model->create_batch($nilai_data);
            }

            redirect('siswa');
        }

        // Ambil mata pelajaran dari DB, lalu kelompokkan berdasarkan kelas
        $mapel_all = $this->Mapel_model->get_all();
        $kelas_mapel = [];
        foreach ($mapel_all as $row) {
            $kelas_mapel[$row['kelas']][] = $row['mata_pelajaran'];
        }

        $data['kelas_mapel'] = $kelas_mapel;

        $this->load->view('templates/header');
        $this->load->view('siswa/create', $data);
        $this->load->view('templates/footer');
    }


}
