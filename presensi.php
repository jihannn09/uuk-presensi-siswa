<?php
// Memuat layout (sekaligus cek login), koneksi, dan model Presensi & Kelas
require_once 'layout/header.php';
require_once 'layout/navbar.php';
require_once 'config/database.php';
require_once 'model/presensi.php';
require_once 'model/kelas.php';

$db = (new Database())->getConnection();
$presensiModel = new Presensi($db);
$kelasModel = new Kelas($db);

$pesan = '';

// Proses menghapus log presensi yang salah catat
if (isset($_GET['hapus'])) {
    $idHapus = (int) $_GET['hapus'];
    if ($presensiModel->delete($idHapus)) {
        $pesan = "Log presensi berhasil dihapus.";
    }
}

// Secara bawaan menampilkan data presensi hari ini
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$id_kelas = $_GET['id_kelas'] ?? '';
$keyword = trim($_GET['keyword'] ?? '');

$dataPresensi = $presensiModel->getFiltered($tanggal, $id_kelas, $keyword);
$daftarKelas = $kelasModel->getAll();
?>

<!-- Judul halaman dan tombol pintas simulasi tap -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Rekap Presensi Siswa</h4>
    <a href="tap.php" class="btn btn-success btn-sm">+ Simulasi Tap Kartu</a>
</div>

<!-- Notifikasi berhasil hapus log -->
<?php if ($pesan): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($pesan) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form Filter Tanggal, Kelas, dan Pencarian Nama/NISN -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Filter Kelas</label>
                <select name="id_kelas" class="form-select">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($daftarKelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Cari Nama / NISN</label>
                <input type="text" name="keyword" class="form-control" placeholder="Ketik nama atau NISN..." value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="presensi.php" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Rekap Kehadiran -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Waktu Tap</th>
                    <th width="90" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dataPresensi)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data presensi ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($dataPresensi as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($row['nisn']) ?></code></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['nama_kelas']) ?></span></td>
                            <td><?= htmlspecialchars($row['tanggal_waktu']) ?></td>
                            <td class="text-center">
                                <a href="presensi.php?hapus=<?= $row['id'] ?>&tanggal=<?= urlencode($tanggal) ?>&id_kelas=<?= urlencode($id_kelas) ?>&keyword=<?= urlencode($keyword) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Hapus log presensi ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?>