<?php
class User_model extends CI_Model {

    public function register($data) {
        return $this->db->insert('users', $data);
    }

    public function login($username, $password) {
        $user = $this->db->get_where('users', ['username' => $username])->row();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function get_all() {
        return $this->db->get('users')->result();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }
}
