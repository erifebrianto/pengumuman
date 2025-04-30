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
}
