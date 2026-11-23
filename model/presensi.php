<?php
require_once __DIR__ . '/basemodel.php';

class Presensi extends BaseModel {
    private $id;
    private $nisn;
    private $tanggal_waktu;

    // Getter dan Setter
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNisn() { return $this->nisn; }
    public function setNisn($nisn) { $this->nisn = trim($nisn); }
    public function getTanggalWaktu() { return $this->tanggal_waktu; }

    // Ambil semua rekap presensi dengan INNER JOIN 3 tabel (presensi, siswa, kelas)
    public function getAll() {
        $sql = "SELECT presensi.id, presensi.nisn, presensi.tanggal_waktu, siswa.nama, kelas.nama_kelas 
                FROM presensi
                INNER JOIN siswa ON presensi.nisn = siswa.nisn
                INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
                ORDER BY presensi.tanggal_waktu DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Filter rekap berdasarkan tanggal, kelas, dan pencarian nama/NISN
    public function getFiltered($tanggal = '', $id_kelas = '', $keyword = '') {
        $sql = "SELECT presensi.id, presensi.nisn, presensi.tanggal_waktu, siswa.nama, kelas.nama_kelas 
                FROM presensi
                INNER JOIN siswa ON presensi.nisn = siswa.nisn
                INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
                WHERE 1=1";
        $params = [];

        if (!empty($tanggal)) {
            $sql .= " AND DATE(presensi.tanggal_waktu) = :tanggal";
            $params[':tanggal'] = $tanggal;
        }
        if (!empty($id_kelas)) {
            $sql .= " AND siswa.id_kelas = :id_kelas";
            $params[':id_kelas'] = $id_kelas;
        }
        if (!empty($keyword)) {
            $sql .= " AND (siswa.nama LIKE :kw1 OR siswa.nisn LIKE :kw2)";
            $params[':kw1'] = "%$keyword%";
            $params[':kw2'] = "%$keyword%";
        }

        $sql .= " ORDER BY presensi.tanggal_waktu DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Simpan tap kartu (hanya mengirim NISN; id & tanggal_waktu otomatis dari MariaDB)
    public function tapKartu() {
        $stmt = $this->db->prepare("INSERT INTO presensi (nisn) VALUES (:nisn)");
        return $stmt->execute([':nisn' => $this->nisn]);
    }

    // Hapus log presensi yang salah catat
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM presensi WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}