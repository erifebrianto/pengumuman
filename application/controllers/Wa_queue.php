<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_queue extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function process_batch()
    {
        // Pastikan hanya bisa diakses via CLI (Cronjob) untuk keamanan
        if (!is_cli()) {
            show_error('Hanya dapat diakses melalui CLI / Cronjob.', 403);
            exit;
        }

        // Ambil pengaturan Wablas dari database
        $pengaturan = $this->db->get('pengaturan')->row();
        
        if (!$pengaturan || empty($pengaturan->wablas_domain) || empty($pengaturan->wablas_token)) {
            echo "[" . date('Y-m-d H:i:s') . "] API Wablas belum dikonfigurasi di Pengaturan Dashboard.\n";
            return;
        }

        $domain_wablas = rtrim($pengaturan->wablas_domain, '/'); // Cegah double slash
        $token_wablas  = $pengaturan->wablas_token; 

        // 1. Ambil 20 antrian pesan yang berstatus 'pending'
        // Limit 20 mencegah hit API massal yang berpotensi dianggap spam / memory limit
        $this->db->where('status', 'pending');
        $this->db->limit(20);
        $this->db->order_by('id', 'ASC');
        $queue = $this->db->get('whatsapp_queue')->result();

        if (empty($queue)) {
            echo "[" . date('Y-m-d H:i:s') . "] Tidak ada antrian pesan WhatsApp.\n";
            return;
        }

        echo "[" . date('Y-m-d H:i:s') . "] Memproses " . count($queue) . " pesan WhatsApp...\n";

        foreach ($queue as $q) {
            // Nomor tujuan Wablas tidak boleh berawalan 0 atau menggunakan karakter +, hapus jika ada.
            $phone = preg_replace('/[^0-9]/', '', $q->no_hp);
            if (substr($phone, 0, 1) == '0') {
                $phone = '62' . substr($phone, 1);
            }

            // Siapkan payload ke Wablas
            $curl = curl_init();
            $data = [
                'phone' => $phone,
                'message' => $q->pesan,
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: " . $token_wablas,
                "Content-Type: application/json"
            ]);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_URL, $domain_wablas."/api/send-message");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);
            curl_close($curl);

            // Analisa hasil
            $status_update = 'failed';
            if ($http_code == 200 || $http_code == 201) {
                $status_update = 'sent';
            }

            // 2. Update status queue di database beserta balasan API Wablas
            $this->db->where('id', $q->id);
            $this->db->update('whatsapp_queue', [
                'status' => $status_update,
                'api_response' => $result ? $result : "cURL Error: " . $curl_error,
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            echo "-> Mengirim ke {$phone} (ID: {$q->id}) - Status: {$status_update}\n";

            // 3. Strategi Jeda Anti Spesialis Spam (Sleep dinamis antar pesan 3-6 detik)
            $jeda = rand(3, 6);
            sleep($jeda);
        }

        echo "[" . date('Y-m-d H:i:s') . "] Eksekusi Batch Selesai.\n";
    }
}
