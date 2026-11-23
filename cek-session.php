<?php
// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi halaman: jika belum login (is_login tidak true), kembalikan ke index.php
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}