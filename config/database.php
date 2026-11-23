<?php
class Database {
    // Konfigurasi koneksi database MariaDB di Laragon
    private $host = "localhost";
    private $db_name = "db_presensi_siswa";
    private $username = "root";
    private $password = "";
    private $conn;

    // Constructor otomatis berjalan saat objek Database dibuat
    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Koneksi database gagal. Silakan coba lagi nanti.");
        }
    }

    // Mengembalikan objek koneksi PDO ke model
    public function getConnection() {
        return $this->conn;
    }
}