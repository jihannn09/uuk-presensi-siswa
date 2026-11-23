<?php
require_once __DIR__ . '/baseModel.php';

class Kelas extends BaseModel {
    private $id_kelas;
    private $nama_kelas;
    private $jurusan;

    // Getter dan Setter
    public function getIdKelas() { return $this->id_kelas; }
    public function setIdKelas($id) { $this->id_kelas = $id; }
    public function getNamaKelas() { return $this->nama_kelas; }
    public function setNamaKelas($nama) { $this->nama_kelas = trim($nama); }
    public function getJurusan() { return $this->jurusan; }
    public function setJurusan($jurusan) { $this->jurusan = trim($jurusan); }

    // Ambil semua data kelas
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
        return $stmt->fetchAll();
    }

    // Ambil 1 data kelas berdasarkan ID (untuk form edit)
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM kelas WHERE id_kelas = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Cek apakah nama kelas sudah terpakai agar tidak kembar
    public function isDuplicateName($nama_kelas, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM kelas WHERE nama_kelas = :nama AND id_kelas != :id");
            $stmt->execute([':nama' => $nama_kelas, ':id' => $exclude_id]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM kelas WHERE nama_kelas = :nama");
            $stmt->execute([':nama' => $nama_kelas]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // Tambah kelas baru
    public function create() {
        $stmt = $this->db->prepare("INSERT INTO kelas (nama_kelas, jurusan) VALUES (:nama, :jurusan)");
        return $stmt->execute([
            ':nama' => $this->nama_kelas,
            ':jurusan' => $this->jurusan
        ]);
    }

    // Ubah data kelas
    public function update() {
        $stmt = $this->db->prepare("UPDATE kelas SET nama_kelas = :nama, jurusan = :jurusan WHERE id_kelas = :id");
        return $stmt->execute([
            ':nama' => $this->nama_kelas,
            ':jurusan' => $this->jurusan,
            ':id' => $this->id_kelas
        ]);
    }

    // Hapus kelas (ditolak jika masih memiliki siswa terdaftar)
    public function delete($id) {
        $cek = $this->db->prepare("SELECT COUNT(*) FROM siswa WHERE id_kelas = :id");
        $cek->execute([':id' => $id]);
        if ($cek->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM kelas WHERE id_kelas = :id");
        return $stmt->execute([':id' => $id]);
    }
}