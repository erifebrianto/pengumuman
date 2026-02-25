<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurusan_model extends CI_Model {

    // Mendapatkan semua data jurusan
    public function get_all() {
        $query = $this->db->get('jurusan');
        return $query->result();
    }

    // Mendapatkan mata pelajaran berdasarkan jurusan
    public function get_mapel_by_jurusan($jurusan_id) {
        $this->db->select('*');
        $this->db->from('mata_pelajaran');
        $this->db->where('jurusan_id', $jurusan_id);
        $query = $this->db->get();
        return $query->result();
    }
}
