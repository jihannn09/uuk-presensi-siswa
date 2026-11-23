<?php
// Abstract class induk untuk semua model
abstract class BaseModel {
    // Protected agar properti $db bisa dipakai langsung oleh class turunannya
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Method abstrak yang wajib dibuat ulang di setiap class anak
    abstract public function getAll();
}