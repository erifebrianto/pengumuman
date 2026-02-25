<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mata_pelajaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mata_pelajaran_model');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['mata_pelajaran'] = $this->Mata_pelajaran_model->get_all();
        $this->load->view('templates/header');
        $this->load->view('mata_pelajaran/index', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        if ($this->input->post()) {
            $data = [
                'jurusan_id' => $this->input->post('jurusan_id'),
                'nama_mata_pelajaran' => $this->input->post('nama_mata_pelajaran')
            ];
            $this->Mata_pelajaran_model->create($data);
            redirect('mata_pelajaran');
        }

        $data['jurusan'] = $this->Mata_pelajaran_model->get_all_jurusan();
        $this->load->view('templates/header');
        $this->load->view('mata_pelajaran/create', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        if ($this->input->post()) {
            $data = [
                'jurusan_id' => $this->input->post('jurusan_id'),
                'nama_mata_pelajaran' => $this->input->post('nama_mata_pelajaran')
            ];
            $this->Mata_pelajaran_model->update($id, $data);
            redirect('mata_pelajaran');
        }

        $data['mata_pelajaran'] = $this->Mata_pelajaran_model->get_by_id($id);
        $data['jurusan'] = $this->Mata_pelajaran_model->get_all_jurusan();
        $this->load->view('templates/header');
        $this->load->view('mata_pelajaran/edit', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id) {
        $this->Mata_pelajaran_model->delete($id);
        redirect('mata_pelajaran');
    }
}
