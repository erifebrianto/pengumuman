<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Countdown_model');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function countdowns()
    {
        $data['countdowns'] = $this->Countdown_model->get_all();
        $this->load->view('templates/header');
        $this->load->view('setting/index', $data);
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
