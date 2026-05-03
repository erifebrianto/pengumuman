<?php
class Siswa_model extends CI_Model {

    // Create
    public function create($data) {
        return $this->db->insert('siswa', $data);
    }

    // Read
    public function get_all() {
        return $this->db->get('siswa')->result();
    }

    // Update
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('siswa', $data);
    }

    // Delete
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('siswa');
    }

    public function empty_data() {
        // Hapus semua data dari nilai_siswa dan siswa
        $this->db->empty_table('nilai_siswa');
        return $this->db->empty_table('siswa');
    }

    // Get by ID
    public function get_by_id($id) {
        return $this->db->get_where('siswa', ['id' => $id])->row();
    }
    public function insert($data) {
        $this->db->insert('siswa', $data);
        return $this->db->insert_id();
    }
    public function get_by_nis($nis){
        return $this->db->get_where('siswa', ['nis' => $nis])->row();
    }
        public function count_all() {
        return $this->db->count_all('siswa');
    }
    public function get_by_no_ujian_and_nis($no_ujian, $nis)
    {
        return $this->db->get_where('siswa', [
            'no_ujian' => $no_ujian,
            'nis'      => $nis
        ])->row();
    }
    // Siswa_model.php
    public function get_by_nis_and_no_ujian($nis, $no_ujian)
    {
        return $this->db->get_where('siswa', [
            'nis' => $nis,
            'no_ujian' => $no_ujian
        ])->row();
    }

    public function get_by_fields($fields)
    {
        foreach ($fields as $key => $value) {
            $this->db->where($key, trim($value));
        }
        return $this->db->get('siswa')->row();
    }

    public function get_by_token($token)
    {
        return $this->db->get_where('siswa', ['token_download' => $token])->row();
    }

    public function update_token($nis, $token)
    {
        $this->db->where('nis', $nis);
        return $this->db->update('siswa', ['token_download' => $token]);
    }



    public function count_by_status($status) {
        return $this->db->where('status', $status)->count_all_results('siswa');
    }

    public function get_recent($limit = 10) {
        return $this->db->order_by('id', 'DESC')->limit($limit)->get('siswa')->result_array();
    }
    public function get_kelulusan_per_kelas() {
        $this->db->select('kelas, 
                           SUM(CASE WHEN status = "lulus" THEN 1 ELSE 0 END) as lulus,
                           SUM(CASE WHEN status = "tidak lulus" THEN 1 ELSE 0 END) as tidak_lulus');
        $this->db->group_by('kelas');
        $query = $this->db->get('siswa');
        return $query->result_array();
    }


}
