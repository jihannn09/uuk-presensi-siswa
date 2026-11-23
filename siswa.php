<?php
// Memuat layout, koneksi, dan model Siswa & Kelas
require_once 'layout/header.php';
require_once 'layout/navbar.php';
require_once 'config/database.php';
require_once 'model/siswa.php';
require_once 'model/kelas.php';

$db = (new Database())->getConnection();
$siswaModel = new Siswa($db);
$kelasModel = new Kelas($db);

$errors = [];
$alertSuccess = '';

$editMode = false;
$old_nisn = '';
$nisn = '';
$nama = '';
$alamat = '';
$id_kelas = '';

// Mengambil data siswa ke form saat tombol Ubah diklik
if (isset($_GET['edit'])) {
    $dataEdit = $siswaModel->getByNisn($_GET['edit']);
    if ($dataEdit) {
        $editMode = true;
        $old_nisn = $dataEdit['nisn'];
        $nisn = $dataEdit['nisn'];
        $nama = $dataEdit['nama'];
        $alamat = $dataEdit['alamat'] ?? '';
        $id_kelas = $dataEdit['id_kelas'];
    }
}

// Proses hapus data siswa
if (isset($_GET['hapus'])) {
    if ($siswaModel->delete($_GET['hapus'])) {
        $alertSuccess = "Data siswa berhasil dihapus.";
    }
}

// Proses simpan tambah / ubah data siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_nisn = trim($_POST['old_nisn'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_kelas = $_POST['id_kelas'] ?? '';
    $editMode = !empty($old_nisn);

    // Validasi NISN: wajib 10 digit angka dan tidak kembar
    if ($nisn === '') {
        $errors['nisn'] = "NISN wajib diisi.";
    } elseif (!preg_match('/^\d{10}$/', $nisn)) {
        $errors['nisn'] = "NISN wajib tepat 10 digit angka.";
    } else {
        $cekNisn = $siswaModel->getByNisn($nisn);
        if ($cekNisn && (!$editMode || $nisn !== $old_nisn)) {
            $errors['nisn'] = "NISN sudah terdaftar (tidak boleh kembar).";
        }
    }

    if ($nama === '') {
        $errors['nama'] = "Nama siswa wajib diisi.";
    }

    if ($id_kelas === '' || !$kelasModel->getById((int)$id_kelas)) {
        $errors['id_kelas'] = "Silakan pilih kelas yang valid.";
    }

    if (empty($errors)) {
        $siswaModel->setNisn($nisn);
        $siswaModel->setNama($nama);
        $siswaModel->setAlamat($alamat);
        $siswaModel->setIdKelas((int)$id_kelas);

        if ($editMode) {
            $siswaModel->update($old_nisn);
            $alertSuccess = "Data siswa berhasil diperbarui.";
        } else {
            $siswaModel->create();
            $alertSuccess = "Siswa baru berhasil ditambahkan.";
        }

        $editMode = false;
        $old_nisn = $nisn = $nama = $alamat = $id_kelas = '';
    }
}

$keyword = trim($_GET['cari'] ?? '');
$daftarSiswa = $siswaModel->getAll($keyword);
$daftarKelas = $kelasModel->getAll();
?>

<h4 class="mb-3">Kelola Data Siswa</h4>

<?php if ($alertSuccess): ?>
    <div class="alert alert-success"><?= htmlspecialchars($alertSuccess) ?></div>
<?php endif; ?>

<div class="row">
    <!-- Kolom Kiri: Form Tambah / Ubah Siswa -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold"><?= $editMode ? 'Ubah Siswa' : 'Tambah Siswa' ?></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="old_nisn" value="<?= htmlspecialchars($old_nisn) ?>">

                    <div class="mb-3">
                        <label class="form-label">NISN (10 Digit Angka)</label>
                        <input type="text" name="nisn" maxlength="10" class="form-control <?= isset($errors['nisn']) ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: 0081234567" value="<?= htmlspecialchars($nisn) ?>">
                        <?php if (isset($errors['nisn'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nisn']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($nama) ?>">
                        <?php if (isset($errors['nama'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nama']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="id_kelas" class="form-select <?= isset($errors['id_kelas']) ? 'is-invalid' : '' ?>">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($daftarKelas as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas == $k['id_kelas'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['id_kelas'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['id_kelas']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat <span class="text-muted small">(Opsional)</span></label>
                        <textarea name="alamat" rows="2" class="form-control"><?= htmlspecialchars($alamat) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><?= $editMode ? 'Simpan Perubahan' : 'Tambah Siswa' ?></button>
                        <?php if ($editMode): ?>
                            <a href="siswa.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Pencarian & Tabel Data Siswa -->
    <div class="col-md-8">
        <!-- Form Pencarian Nama / NISN -->
        <div class="card shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama atau NISN siswa..." value="<?= htmlspecialchars($keyword) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    <?php if ($keyword !== ''): ?>
                        <a href="siswa.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Siswa -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Alamat</th>
                            <th width="135" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarSiswa)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Data siswa tidak ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($daftarSiswa as $s): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($s['nisn']) ?></code></td>
                                    <td><?= htmlspecialchars($s['nama']) ?></td>
                                    <td><?= htmlspecialchars($s['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($s['alamat'] ?: '-') ?></td>
                                    <td class="text-center">
                                        <a href="siswa.php?edit=<?= urlencode($s['nisn']) ?>" class="btn btn-warning btn-sm">Ubah</a>
                                        <a href="siswa.php?hapus=<?= urlencode($s['nisn']) ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Hapus siswa ini? Semua log presensinya juga akan terhapus.')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?>