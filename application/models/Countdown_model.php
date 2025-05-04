<?php
class Countdown_model extends CI_Model
{
    public function get_all()
    {
        return $this->db->order_by('id', 'desc')->get('countdown')->result();
    }

    public function get_target_time()
    {
        return $this->db->order_by('id', 'desc')->get('countdown')->row();
    }

    public function set_target_time($datetime)
    {
        $data = [
            'waktu_target' => $datetime,
            'created_at' => date('Y-m-d H:i:s')  // Automatically insert current time
        ];
        return $this->db->insert('countdown', $data);
    }

    public function update_target_time($id, $datetime)
    {
        $data = [
            'waktu_target' => $datetime,
            'created_at' => date('Y-m-d H:i:s')  // Optional: Update created_at if needed
        ];
        return $this->db->where('id', $id)->update('countdown', $data);
    }

    public function delete_target_time($id)
    {
        return $this->db->where('id', $id)->delete('countdown');
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get('countdown')->row();
    }
}
