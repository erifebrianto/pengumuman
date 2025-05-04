<?php
class Setting_model extends CI_Model{
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

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('countdown', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('countdown', ['id' => $id]);
    }
}