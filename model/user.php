<?php
require_once __DIR__ . '/baseModel.php';

class User extends BaseModel {
    // Enkapsulasi: properti dibuat private
    private $id_user;
    private $username;
    private $password;

    // Getter dan Setter
    public function getIdUser() { return $this->id_user; }
    public function setIdUser($id) { $this->id_user = $id; }
    public function getUsername() { return $this->username; }
    public function setUsername($username) { $this->username = $username; }
    public function setPassword($password) { $this->password = $password; }

    public function getAll() {
        $stmt = $this->db->query("SELECT id_user, username FROM users");
        return $stmt->fetchAll();
    }

    // Proses verifikasi login admin
    public function login($username, $password) {
        // Prepared statement untuk mencegah SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Mencocokkan password input dengan hash di database
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}