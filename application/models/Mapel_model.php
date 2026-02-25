<?php
class Mapel_model extends CI_Model {
    public function get_all() {
        return $this->db->get('mapel_kelas')->result_array();
    }
    public function get_by_name($nama)
{
    return $this->db->get_where('mata_pelajaran', ['nama_mata_pelajaran' => $nama])->row_array();
}

}
