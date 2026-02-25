<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Countdown_model');
        $this->load->model('Setting_model');
        $this->load->library('form_validation');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }
    public function index()
    {
        $data['pengaturan'] = $this->Setting_model->get_first(); // Ambil 1 record (asumsi hanya 1 baris setting)

        $this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'required');
        $this->form_validation->set_rules('alamat_sekolah', 'Alamat Sekolah', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        // tambahkan rule lain sesuai kebutuhan

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('setting/index', $data);
            $this->load->view('templates/footer');
        } else {
            $data_update = [
                'nama_sekolah'        => $this->input->post('nama_sekolah'),
                'alamat_sekolah'      => $this->input->post('alamat_sekolah'),
                'email'               => $this->input->post('email'),
                'kode_pos'            => $this->input->post('kode_pos'),
                'no_tlp'              => $this->input->post('no_tlp'),
                'website'             => $this->input->post('website'),
                'nama_kepala_sekolah' => $this->input->post('nama_kepala_sekolah'),
            ];

        // Upload logo sekolah
        if (!empty($_FILES['logo_sekolah']['name'])) {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['file_name']     = 'logo_sekolah';
            $config['overwrite']     = TRUE;

            $this->load->library('upload', $config); // Load ulang
            if ($this->upload->do_upload('logo_sekolah')) {
                $upload_data = $this->upload->data();
                $data_update['logo_sekolah'] = 'uploads/' . $upload_data['file_name'];
            }
        }

        // Upload tanda tangan kepala sekolah
        if (!empty($_FILES['ttd_kepala_sekolah']['name'])) {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['file_name']     = 'ttd_kepala_sekolah';
            $config['overwrite']     = TRUE;

            $this->load->library('upload', $config); // Load ulang
            if ($this->upload->do_upload('ttd_kepala_sekolah')) {
                $upload_data = $this->upload->data();
                $data_update['ttd_kepala_sekolah'] = 'uploads/' . $upload_data['file_name'];
            }
        }

        // Upload background halaman SKL
        if (!empty($_FILES['background']['name'])) {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['file_name']     = 'background_skl';
            $config['overwrite']     = TRUE;

            $this->load->library('upload', $config); // Load ulang
            if ($this->upload->do_upload('background')) {
                $upload_data = $this->upload->data();
                $data_update['background'] = 'uploads/' . $upload_data['file_name'];
            }
        }


            // Update setting
            $this->Setting_model->update(1, $data_update);
            $this->session->set_flashdata('success', 'Pengaturan berhasil diperbarui.');
            redirect('setting');
        }
    }

    public function countdowns()
    {
        $data['countdowns'] = $this->Countdown_model->get_all();
        $this->load->view('templates/header');
        $this->load->view('setting/countdowns', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        if ($this->input->method() === 'post') {
            $waktu_target = $this->input->post('waktu_target');
            if ($waktu_target) {
                $this->Countdown_model->set_target_time($waktu_target);
                redirect('setting/countdowns');
            }
        }
        $this->load->view('templates/header');
        $this->load->view('setting/create');
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $countdown = $this->Countdown_model->get_by_id($id);
        
        if (!$countdown) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $waktu_target = $this->input->post('waktu_target');
            if ($waktu_target) {
                $this->Countdown_model->update_target_time($id, $waktu_target);
                redirect('setting/countdowns');
            }
        }

        $data['countdown'] = $countdown;
        $this->load->view('templates/header');
        $this->load->view('setting/edit', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id)
    {
        $this->Countdown_model->delete_target_time($id);
        redirect('setting');
    }
}
