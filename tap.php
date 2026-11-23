<?php
// Memuat layout, koneksi, dan model Siswa & Presensi
require_once 'layout/header.php';
require_once 'layout/navbar.php';
require_once 'config/database.php';
require_once 'model/siswa.php';
require_once 'model/presensi.php';

$db = (new Database())->getConnection();
$siswaModel = new Siswa($db);
$presensiModel = new Presensi($db);

$errorNisn = '';
$sukses = '';
$nisnInput = '';

// Proses validasi dan penyimpanan saat simulasi kartu di-tap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisnInput = trim($_POST['nisn'] ?? '');

    if ($nisnInput === '') {
        $errorNisn = "NISN wajib diisi.";
    } elseif (!preg_match('/^\d{10}$/', $nisnInput)) {
        $errorNisn = "NISN harus terdiri dari 10 digit angka.";
    } else {
        $siswa = $siswaModel->getByNisn($nisnInput);
        if (!$siswa) {
            $errorNisn = "Kartu ditolak: NISN ({$nisnInput}) tidak terdaftar di sistem.";
        } else {
            $presensiModel->setNisn($nisnInput);
            if ($presensiModel->tapKartu()) {
                $sukses = "Presensi berhasil dicatat untuk <b>" . htmlspecialchars($siswa['nama']) . "</b> (" . htmlspecialchars($nisnInput) . ").";
                $nisnInput = '';
            }
        }
    }
}
?>

<!-- Form Simulasi Pembaca Kartu RFID -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Simulasi Pembaca Kartu RFID Gerbang</div>
            <div class="card-body p-4">
                <?php if ($sukses): ?>
                    <div class="alert alert-success"><?= $sukses ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tempelkan Kartu / Masukkan NISN (10 Digit)</label>
                        <input type="text" name="nisn" maxlength="10" class="form-control form-control-lg <?= $errorNisn ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: 0081234561" value="<?= htmlspecialchars($nisnInput) ?>" autofocus>
                        <?php if ($errorNisn): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errorNisn) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Tap Kartu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?>