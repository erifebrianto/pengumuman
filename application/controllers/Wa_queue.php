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

        $batch_limit = (!empty($pengaturan->wa_batch_limit)) ? $pengaturan->wa_batch_limit : 10;
        $delay_min   = (isset($pengaturan->wa_delay_min)) ? $pengaturan->wa_delay_min : 3;
        $delay_max   = (isset($pengaturan->wa_delay_max)) ? $pengaturan->wa_delay_max : 6;

        // 1. Ambil antrian pesan yang berstatus 'pending'
        $this->db->where('status', 'pending');
        $this->db->limit($batch_limit);
        $this->db->order_by('id', 'ASC');
        $queue = $this->db->get('whatsapp_queue')->result();

        if (empty($queue)) {
            echo "[" . date('Y-m-d H:i:s') . "] Tidak ada antrian pesan WhatsApp.\n";
            return;
        }

        echo "[" . date('Y-m-d H:i:s') . "] Memproses " . count($queue) . " pesan WhatsApp (Limit: {$batch_limit})...\n";

        $this->load->library('whatsapp');

        foreach ($queue as $q) {
            $send_result = $this->whatsapp->send($q->no_hp, $q->pesan);

            // Analisa hasil
            if ($send_result['status'] == 'sent') {
                $this->db->where('id', $q->id);
                $this->db->update('whatsapp_queue', [
                    'status' => 'sent',
                    'api_response' => $send_result['response'],
                    'sent_at' => date('Y-m-d H:i:s')
                ]);
                $status_log = 'sent';
            } else {
                $new_retry_count = $q->retry_count + 1;
                $status_update = ($new_retry_count >= 3) ? 'failed' : 'pending';
                
                $this->db->where('id', $q->id);
                $this->db->update('whatsapp_queue', [
                    'status' => $status_update,
                    'retry_count' => $new_retry_count,
                    'api_response' => $send_result['error'] ?? 'Unknown Error',
                    'sent_at' => ($status_update == 'failed') ? date('Y-m-d H:i:s') : null
                ]);
                $status_log = $status_update . " (Retry: {$new_retry_count})";
            }

            echo "-> Mengirim ke {$q->no_hp} (ID: {$q->id}) - Status: {$status_log}\n";

            // 3. Strategi Jeda Anti Spam (Sleep dinamis sesuai setting)
            $jeda = rand($delay_min, $delay_max);
            if (count($queue) > 1) {
                sleep($jeda);
            }
        }

        echo "[" . date('Y-m-d H:i:s') . "] Eksekusi Batch Selesai.\n";
    }
}
