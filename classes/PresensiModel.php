<?php
require_once __DIR__ . '/BaseModel.php';

class Presensi extends BaseModel
{
    public function getAll(): array
    {
        return $this->cari('', '', 0);
    }

    public function tap(string $nisn): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO presensi (nisn) VALUES (?)');
        $stmt->execute([$nisn]);
    }

    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM presensi WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function cari(string $kata, string $tanggal, int $kelas): array
    {
        $sql = 'SELECT p.id, p.nisn, p.tanggal_waktu, s.nama, k.nama_kelas FROM presensi p INNER JOIN siswa s ON p.nisn = s.nisn INNER JOIN kelas k ON s.id_kelas = k.id_kelas WHERE (s.nama LIKE ? OR p.nisn LIKE ?)';
        $values = ['%' . $kata . '%', '%' . $kata . '%'];
        if ($tanggal !== '') {
            $sql .= ' AND DATE(p.tanggal_waktu) = ?';
            $values[] = $tanggal;
        }
        if ($kelas > 0) {
            $sql .= ' AND k.id_kelas = ?';
            $values[] = $kelas;
        }
        $stmt = $this->pdo->prepare($sql . ' ORDER BY p.tanggal_waktu DESC, p.id DESC');
        $stmt->execute($values);
        return $stmt->fetchAll();
    }
}
