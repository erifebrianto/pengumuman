<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek apakah user sudah login
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['title'] = 'Dashboard';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/content');
        $this->load->view('templates/footer');
    }
}
