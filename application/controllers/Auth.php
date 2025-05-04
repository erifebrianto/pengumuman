<?php
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login() {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('login');
        } else {
            $user = $this->User_model->login(
                $this->input->post('username'),
                $this->input->post('password')
            );

            if ($user) {
                $this->session->set_userdata([
                    'user_id' => $user->id,
                    'username' => $user->username
                ]);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah.');
                redirect('auth/login');
            }
        }
    }

    public function register() {
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('register');
        } else {
            $data = [
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            ];

            $this->User_model->register($data);
            $this->session->set_flashdata('success', 'Registrasi berhasil, silakan login.');
            redirect('auth/login');
        }
    }


    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
