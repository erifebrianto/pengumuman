<?php
class Batch_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Check and create table if not exists (Zero-config migration)
        if (!$this->db->table_exists('batch_generation')) {
            $this->load->dbforge();
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
                'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'idle'],
                'progress' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'total' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME']
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('batch_generation');
            $this->db->insert('batch_generation', [
                'status' => 'idle',
                'progress' => 0,
                'total' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function get_status() {
        return $this->db->get_where('batch_generation', ['id' => 1])->row();
    }

    public function update_status($data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', 1);
        $this->db->update('batch_generation', $data);
    }
    
    public function reset_status($total) {
        $this->db->where('id', 1);
        $this->db->update('batch_generation', [
            'status' => 'processing',
            'progress' => 0,
            'total' => $total,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
