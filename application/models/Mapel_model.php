<?php
class Mapel_model extends CI_Model {
    public function get_all() {
        return $this->db->get('mapel_kelas')->result_array();
    }
}
