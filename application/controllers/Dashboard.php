<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek apakah user sudah login
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('Siswa_model');
    }

    public function index() {
        $data['title'] = 'Dashboard';

        // Ambil data dari model Siswa_model
        $data['total_siswa'] = $this->Siswa_model->count_all();
        $data['jumlah_lulus'] = $this->Siswa_model->count_by_status('lulus');
        $data['jumlah_tidak_lulus'] = $this->Siswa_model->count_by_status('tidak lulus');
        $data['kelulusan_per_kelas'] = $this->Siswa_model->get_kelulusan_per_kelas(); 

        // Load view dashboard
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data); // ganti dari 'templates/content'
        $this->load->view('templates/footer');
    }

    public function index2() {
        $data['title'] = 'Dashboard';

        // Ambil data dari model Siswa_model
        $data['total_siswa'] = $this->Siswa_model->count_all();
        $data['jumlah_lulus'] = $this->Siswa_model->count_by_status('lulus');
        $data['jumlah_tidak_lulus'] = $this->Siswa_model->count_by_status('tidak lulus');
        $data['kelulusan_per_kelas'] = $this->Siswa_model->get_kelulusan_per_kelas(); 

        // Load view dashboard
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index2', $data); // ganti dari 'templates/content'
        $this->load->view('templates/footer');
    }

}
