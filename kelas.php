<?php
// Memuat layout (sekaligus cek login), koneksi, dan model Kelas
require_once 'layout/header.php';
require_once 'layout/navbar.php';
require_once 'config/database.php';
require_once 'model/kelas.php';

$db = (new Database())->getConnection();
$kelasModel = new Kelas($db);

$errors = [];
$alertSuccess = '';
$alertError = '';

$editMode = false;
$id_kelas = '';
$nama_kelas = '';
$jurusan = '';

// Mengambil data kelas ke form saat tombol Ubah diklik
if (isset($_GET['edit'])) {
    $dataEdit = $kelasModel->getById((int)$_GET['edit']);
    if ($dataEdit) {
        $editMode = true;
        $id_kelas = $dataEdit['id_kelas'];
        $nama_kelas = $dataEdit['nama_kelas'];
        $jurusan = $dataEdit['jurusan'];
    }
}

// Proses hapus kelas dengan pengecekan relasi siswa
if (isset($_GET['hapus'])) {
    $idHapus = (int)$_GET['hapus'];
    if ($kelasModel->delete($idHapus)) {
        $alertSuccess = "Data kelas berhasil dihapus.";
    } else {
        $alertError = "Kelas tidak dapat dihapus karena masih memiliki siswa terdaftar.";
    }
}

// Proses simpan tambah / ubah data kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = $_POST['id_kelas'] ?? '';
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $editMode = !empty($id_kelas);

    if ($nama_kelas === '') {
        $errors['nama_kelas'] = "Nama kelas wajib diisi.";
    } elseif ($kelasModel->isDuplicateName($nama_kelas, $editMode ? $id_kelas : null)) {
        $errors['nama_kelas'] = "Nama kelas sudah terdaftar (tidak boleh kembar).";
    }

    if ($jurusan === '') {
        $errors['jurusan'] = "Jurusan wajib diisi.";
    }

    if (empty($errors)) {
        $kelasModel->setNamaKelas($nama_kelas);
        $kelasModel->setJurusan($jurusan);

        if ($editMode) {
            $kelasModel->setIdKelas($id_kelas);
            $kelasModel->update();
            $alertSuccess = "Data kelas berhasil diperbarui.";
        } else {
            $kelasModel->create();
            $alertSuccess = "Kelas baru berhasil ditambahkan.";
        }
        $editMode = false;
        $id_kelas = $nama_kelas = $jurusan = '';
    }
}

$daftarKelas = $kelasModel->getAll();
?>

<h4 class="mb-3">Kelola Data Kelas</h4>

<?php if ($alertSuccess): ?>
    <div class="alert alert-success"><?= htmlspecialchars($alertSuccess) ?></div>
<?php endif; ?>
<?php if ($alertError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($alertError) ?></div>
<?php endif; ?>

<div class="row">
    <!-- Kolom Kiri: Form Tambah / Ubah Kelas -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold"><?= $editMode ? 'Ubah Kelas' : 'Tambah Kelas' ?></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id_kelas" value="<?= htmlspecialchars($id_kelas) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control <?= isset($errors['nama_kelas']) ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: XI PPLG 1" value="<?= htmlspecialchars($nama_kelas) ?>">
                        <?php if (isset($errors['nama_kelas'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_kelas']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control <?= isset($errors['jurusan']) ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim" value="<?= htmlspecialchars($jurusan) ?>">
                        <?php if (isset($errors['jurusan'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['jurusan']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><?= $editMode ? 'Simpan Perubahan' : 'Tambah' ?></button>
                        <?php if ($editMode): ?>
                            <a href="kelas.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Kelas -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftarKelas as $i => $k): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($k['nama_kelas']) ?></td>
                                <td><?= htmlspecialchars($k['jurusan']) ?></td>
                                <td class="text-center">
                                    <a href="kelas.php?edit=<?= $k['id_kelas'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                                    <a href="kelas.php?hapus=<?= $k['id_kelas'] ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Hapus kelas ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?>