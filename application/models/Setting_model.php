<?php
class Setting_model extends CI_Model {
    
    // Untuk tabel countdown
    public function get_all()
    {
        return $this->db->get('countdown')->result();
    }

    public function get($id)
    {
        return $this->db->get_where('countdown', ['id' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('countdown', $data);
    }

    public function update_countdown($id, $data)
    {
        return $this->db->where('id', $id)->update('countdown', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('countdown', ['id' => $id]);
    }

    // Untuk tabel pengaturan
    public function get_first()
    {
        return $this->db->get('pengaturan')->row();
    }

    public function update_pengaturan($data)
    {
        $this->db->where('id', 1); // hanya 1 baris pengaturan
        return $this->db->update('pengaturan', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('pengaturan', $data);
    }


}
