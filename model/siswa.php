<?php
require_once __DIR__ . '/basemodel.php';

class Siswa extends BaseModel {
    private $nisn;
    private $nama;
    private $alamat;
    private $id_kelas;

    // Getter dan Setter
    public function getNisn() { return $this->nisn; }
    public function setNisn($nisn) { $this->nisn = trim($nisn); }
    public function getNama() { return $this->nama; }
    public function setNama($nama) { $this->nama = trim($nama); }
    public function getAlamat() { return $this->alamat; }
    public function setAlamat($alamat) { $this->alamat = trim($alamat); }
    public function getIdKelas() { return $this->id_kelas; }
    public function setIdKelas($id_kelas) { $this->id_kelas = $id_kelas; }

    // Ambil daftar siswa digabung nama kelas (INNER JOIN) + fitur cari nama/NISN
    public function getAll($keyword = '') {
        $sql = "SELECT siswa.*, kelas.nama_kelas, kelas.jurusan 
                FROM siswa 
                INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas";
        if (!empty($keyword)) {
            $sql .= " WHERE siswa.nama LIKE :kw1 OR siswa.nisn LIKE :kw2";
        }
        $sql .= " ORDER BY siswa.nama ASC";

        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) {
            $stmt->execute([
                ':kw1' => "%$keyword%",
                ':kw2' => "%$keyword%"
            ]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    // Ambil 1 siswa berdasarkan NISN (untuk edit, cek duplikat, dan validasi tap kartu)
    public function getByNisn($nisn) {
        $stmt = $this->db->prepare("SELECT * FROM siswa WHERE nisn = :nisn");
        $stmt->execute([':nisn' => $nisn]);
        return $stmt->fetch();
    }

    // Tambah data siswa baru
    public function create() {
        $stmt = $this->db->prepare("INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES (:nisn, :nama, :alamat, :id_kelas)");
        return $stmt->execute([
            ':nisn' => $this->nisn,
            ':nama' => $this->nama,
            ':alamat' => $this->alamat ?: null,
            ':id_kelas' => $this->id_kelas
        ]);
    }

    // Update data siswa
    public function update($old_nisn) {
        $stmt = $this->db->prepare("UPDATE siswa SET nisn = :nisn, nama = :nama, alamat = :alamat, id_kelas = :id_kelas WHERE nisn = :old_nisn");
        return $stmt->execute([
            ':nisn' => $this->nisn,
            ':nama' => $this->nama,
            ':alamat' => $this->alamat ?: null,
            ':id_kelas' => $this->id_kelas,
            ':old_nisn' => $old_nisn
        ]);
    }

    // Hapus data siswa
    public function delete($nisn) {
        $stmt = $this->db->prepare("DELETE FROM siswa WHERE nisn = :nisn");
        return $stmt->execute([':nisn' => $nisn]);
    }
}