<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mata_pelajaran_model extends CI_Model {

    public function get_all() {
        $this->db->select('mata_pelajaran.*, jurusan.jurusan');
        $this->db->from('mata_pelajaran');
        $this->db->join('jurusan', 'jurusan.id = mata_pelajaran.jurusan_id');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('mata_pelajaran', ['id' => $id])->row();
    }

    public function create($data) {
        return $this->db->insert('mata_pelajaran', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('mata_pelajaran', $data);
    }

    public function delete($id) {
        return $this->db->delete('mata_pelajaran', ['id' => $id]);
    }

    public function get_all_jurusan() {
        return $this->db->get('jurusan')->result();
    }
    public function get_by_jurusan($jurusan_id) {
        return $this->db->get_where('mata_pelajaran', ['jurusan_id' => $jurusan_id])->result_array();
    }    
    public function get_mapel_by_jurusan($jurusan_id) {
        $this->db->select('id, nama_mata_pelajaran');
        $this->db->from('mata_pelajaran');
        $this->db->where('jurusan_id', $jurusan_id);
        $query = $this->db->get();
        return $query->result();
    }
    public function create_batch($nilai_data) {
        $this->db->insert_batch('nilai_siswa', $nilai_data);
    }
}
