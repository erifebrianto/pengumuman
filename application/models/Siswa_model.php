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

    // Get by ID
    public function get_by_id($id) {
        return $this->db->get_where('siswa', ['id' => $id])->row();
    }
    public function insert($data) {
        $this->db->insert('siswa', $data);
        return $this->db->insert_id();
    }
}
