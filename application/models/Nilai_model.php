<?php
class Nilai_model extends CI_Model {
    public function insert_batch($data) {
        return $this->db->insert_batch('nilai_siswa', $data);
    }
    public function get_nilai_with_mapel($siswa_id) {
        $this->db->select('nilai_siswa.nilai, mata_pelajaran.nama_mata_pelajaran');
        $this->db->from('nilai_siswa');
        $this->db->join('mata_pelajaran', 'nilai_siswa.mapel_id = mata_pelajaran.id');
        $this->db->where('nilai_siswa.siswa_id', $siswa_id);
        return $this->db->get()->result();
    }
        public function create($data)
    {
        return $this->db->insert('nilai_siswa', $data);
    }

    public function create_batch($data)
    {
        return $this->db->insert_batch('nilai_siswa', $data);
    }
}
