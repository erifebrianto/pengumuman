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

    public function wablas()
    {
        $data['pengaturan'] = $this->Setting_model->get_first();

        $this->form_validation->set_rules('wablas_domain', 'Domain Wablas', 'required');
        $this->form_validation->set_rules('wablas_token', 'Token Wablas', 'required');
        $this->form_validation->set_rules('wablas_status', 'Status Wablas', 'required|in_list[0,1]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('setting/wablas', $data);
            $this->load->view('templates/footer');
        } else {
            $data_update = [
                'wablas_domain' => rtrim($this->input->post('wablas_domain'), '/'),
                'wablas_token'  => $this->input->post('wablas_token'),
                'wablas_status' => $this->input->post('wablas_status')
            ];

            // Update setting menggunakan ID 1
            $this->Setting_model->update(1, $data_update);
            $this->session->set_flashdata('success', 'Pengaturan API Wablas berhasil diperbarui.');
            redirect('setting/wablas');
        }
    }

    public function test_wablas()
    {
        $no_hp = $this->input->post('no_hp_test');
        if (empty($no_hp)) {
            $this->session->set_flashdata('error', 'Nomor HP tujuan tidak boleh kosong.');
            redirect('setting/wablas');
        }

        $pengaturan = $this->Setting_model->get_first();
        if (!$pengaturan || empty($pengaturan->wablas_domain) || empty($pengaturan->wablas_token)) {
            $this->session->set_flashdata('error', 'Konfigurasi API Wablas belum lengkap. Harap simpan Domain dan Token terlebih dahulu.');
            redirect('setting/wablas');
        }

        $domain = rtrim($pengaturan->wablas_domain, '/');
        $token  = $pengaturan->wablas_token;
        
        // Bersihkan format nomor
        $phone = preg_replace('/[^0-9]/', '', $no_hp);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        $pesan = "🤖 *TEST KONEKSI WABLAS API*\n\nHalo! Jika Anda menerima pesan ini, artinya integrasi API WhatsApp dari Dashboard Sistem Pengumuman Kelulusan Anda telah berfungsi dengan baik. ✅";

        $curl = curl_init();
        $data = [
            'phone' => $phone,
            'message' => $pesan,
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: " . $token,
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_URL, $domain . "/api/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        if ($http_code == 200 || $http_code == 201) {
            $this->session->set_flashdata('success', 'Pesan Test berhasil dikirim ke ' . $phone . '. Response: ' . $result);
        } else {
            $error_msg = $result ? $result : "cURL Error: " . $curl_error;
            $this->session->set_flashdata('error', 'Gagal mengirim pesan Test. (HTTP Code: ' . $http_code . '). Pesan: ' . $error_msg);
        }

        redirect('setting/wablas');
    }

    public function wablas_template()
    {
        $this->form_validation->set_rules('wablas_template_lulus', 'Template Lulus', 'required');
        $this->form_validation->set_rules('wablas_template_gagal', 'Template Tidak Lulus', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Semua kolom template pesan wajib diisi.');
        } else {
            $data_update = [
                'wablas_template_lulus' => $this->input->post('wablas_template_lulus'),
                'wablas_template_gagal' => $this->input->post('wablas_template_gagal')
            ];

            $this->Setting_model->update(1, $data_update);
            $this->session->set_flashdata('success_template', 'Template Pesan otomatis WhatsApp berhasil diperbarui.');
        }
        redirect('setting/wablas');
    }
}
