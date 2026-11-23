<?php
require_once __DIR__ . '/config/database.php';

$db = (new Database())->getConnection();
if ($db) {
    echo "Koneksi ke database berhasil!";
}