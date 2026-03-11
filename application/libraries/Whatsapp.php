<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function send($to, $message)
    {
        $pengaturan = $this->CI->db->get('pengaturan')->row();
        
        if (!$pengaturan || empty($pengaturan->wablas_domain) || empty($pengaturan->wablas_token)) {
            return [
                'status' => 'error',
                'message' => 'API Wablas belum dikonfigurasi.'
            ];
        }

        $domain = rtrim($pengaturan->wablas_domain, '/');
        $token  = $pengaturan->wablas_token;
        
        $phone = preg_replace('/[^0-9]/', '', $to);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        $curl = curl_init();
        $data = [
            'phone' => $phone,
            'message' => $message,
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
            return [
                'status' => 'sent',
                'response' => $result
            ];
        } else {
            return [
                'status' => 'failed',
                'error' => $result ? $result : "cURL Error: " . $curl_error,
                'http_code' => $http_code
            ];
        }
    }
}
