<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai_model extends CI_Model {

    public function create_batch($data)
    {
        return $this->db->insert_batch('nilai_siswa', $data);
    }

    public function get_by_siswa($siswa_id)
    {
        return $this->db->get_where('nilai_siswa', ['siswa_id' => $siswa_id])->result();
    }

    // Tambahan jika ingin update batch nanti
    public function update_batch($data)
    {
        return $this->db->update_batch('nilai_siswa', $data, 'id');
    }
}
