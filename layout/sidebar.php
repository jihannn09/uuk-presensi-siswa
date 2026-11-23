<?php
// Mendeteksi nama file halaman yang sedang dibuka untuk menandai menu aktif
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigasi utama aplikasi -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="presensi.php">RFID Presensi</a>

        <!-- Tombol garis tiga (hamburger) saat layar diperkecil / mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Daftar menu navigasi -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'presensi.php' ? 'active fw-bold' : '' ?>" href="presensi.php">Rekap Presensi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'tap.php' ? 'active fw-bold' : '' ?>" href="tap.php">Simulasi Tap Kartu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'siswa.php' ? 'active fw-bold' : '' ?>" href="siswa.php">Kelola Siswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'kelas.php' ? 'active fw-bold' : '' ?>" href="kelas.php">Kelola Kelas</a>
                </li>
            </ul>

            <!-- Info admin yang sedang login (diamankan htmlspecialchars) & tombol logout -->
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small">Halo, <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
                <a href="logout.php" class="btn btn-sm btn-outline-light" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- Wadah utama untuk isi konten tiap halaman -->
<main class="container flex-grow-1">