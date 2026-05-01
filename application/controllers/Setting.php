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
                'mode_pengumuman'     => $this->input->post('mode_pengumuman'),
                'verification_method' => $this->input->post('verification_method'),
            ];

        // Load library upload sekali
        $this->load->library('upload');

        // Upload logo sekolah
        if (!empty($_FILES['logo_sekolah']['name'])) {
            $config_logo['upload_path']   = './uploads/';
            $config_logo['allowed_types'] = 'jpg|jpeg|png';
            $config_logo['max_size']      = 2048;
            $config_logo['file_name']     = 'logo_sekolah_' . time();
            $config_logo['overwrite']     = TRUE;

            $this->upload->initialize($config_logo);
            if ($this->upload->do_upload('logo_sekolah')) {
                // Hapus logo lama jika ada
                if (!empty($pengaturan->logo_sekolah) && file_exists(FCPATH . $pengaturan->logo_sekolah)) {
                    @unlink(FCPATH . $pengaturan->logo_sekolah);
                }
                $upload_data = $this->upload->data();
                $data_update['logo_sekolah'] = 'uploads/' . $upload_data['file_name'];
            }
        }

        // Upload tanda tangan kepala sekolah
        if (!empty($_FILES['ttd_kepala_sekolah']['name'])) {
            $config_ttd['upload_path']   = './uploads/';
            $config_ttd['allowed_types'] = 'jpg|jpeg|png';
            $config_ttd['max_size']      = 2048;
            $config_ttd['file_name']     = 'ttd_kepala_sekolah_' . time();
            $config_ttd['overwrite']     = TRUE;

            $this->upload->initialize($config_ttd);
            if ($this->upload->do_upload('ttd_kepala_sekolah')) {
                // Hapus ttd lama jika ada
                if (!empty($pengaturan->ttd_kepala_sekolah) && file_exists(FCPATH . $pengaturan->ttd_kepala_sekolah)) {
                    @unlink(FCPATH . $pengaturan->ttd_kepala_sekolah);
                }
                $upload_data = $this->upload->data();
                $data_update['ttd_kepala_sekolah'] = 'uploads/' . $upload_data['file_name'];
            }
        }

        // Upload background halaman SKL
        if (!empty($_FILES['background']['name'])) {
            $config_bg['upload_path']   = './uploads/';
            $config_bg['allowed_types'] = 'jpg|jpeg|png';
            $config_bg['max_size']      = 2048;
            $config_bg['file_name']     = 'background_skl_' . time();
            $config_bg['overwrite']     = TRUE;

            $this->upload->initialize($config_bg);
            if ($this->upload->do_upload('background')) {
                // Hapus bg lama jika ada
                if (!empty($pengaturan->background) && file_exists(FCPATH . $pengaturan->background)) {
                    @unlink(FCPATH . $pengaturan->background);
                }
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

        $data['queue_count'] = [
            'pending' => $this->db->where('status', 'pending')->count_all_results('whatsapp_queue'),
            'sent'    => $this->db->where('status', 'sent')->count_all_results('whatsapp_queue'),
            'failed'  => $this->db->where('status', 'failed')->count_all_results('whatsapp_queue'),
        ];

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('setting/wablas', $data);
            $this->load->view('templates/footer');
        } else {
            $data_update = [
                'wablas_domain'  => rtrim($this->input->post('wablas_domain'), '/'),
                'wablas_token'   => $this->input->post('wablas_token'),
                'wablas_status'  => $this->input->post('wablas_status'),
                'wa_batch_limit' => $this->input->post('wa_batch_limit'),
                'wa_delay_min'   => $this->input->post('wa_delay_min'),
                'wa_delay_max'   => $this->input->post('wa_delay_max'),
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

        $this->load->library('whatsapp');
        $send_result = $this->whatsapp->send($no_hp, $pesan);

        if ($send_result['status'] == 'sent') {
            $this->session->set_flashdata('success', 'Pesan Test berhasil dikirim ke ' . $no_hp . '. Response: ' . $send_result['response']);
        } else {
            $this->session->set_flashdata('error', 'Gagal mengirim pesan Test. Pesan: ' . ($send_result['error'] ?? 'Unknown Error'));
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
