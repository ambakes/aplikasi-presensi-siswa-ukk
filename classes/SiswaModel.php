<?php
require_once __DIR__ . '/BaseModel.php';

class Siswa extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query('SELECT s.*, k.nama_kelas FROM siswa s INNER JOIN kelas k ON s.id_kelas = k.id_kelas ORDER BY s.nama')->fetchAll();
    }

    public function cari(string $nisn): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM siswa WHERE nisn = ?');
        $stmt->execute([$nisn]);
        return $stmt->fetch() ?: null;
    }

    public function simpan(string $nisn, string $nama, string $alamat, int $kelas, string $lama): void
    {
        if ($lama !== '') {
            $stmt = $this->pdo->prepare('UPDATE siswa SET nisn = ?, nama = ?, alamat = ?, id_kelas = ? WHERE nisn = ?');
            $stmt->execute([$nisn, $nama, $alamat === '' ? null : $alamat, $kelas, $lama]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nisn, $nama, $alamat === '' ? null : $alamat, $kelas]);
        }
    }

    public function hapus(string $nisn): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM siswa WHERE nisn = ?');
        $stmt->execute([$nisn]);
    }

    public function cariSiswa(string $kata): array
    {
        $stmt = $this->pdo->prepare('SELECT s.*, k.nama_kelas FROM siswa s INNER JOIN kelas k ON s.id_kelas = k.id_kelas WHERE s.nama LIKE ? OR s.nisn LIKE ? ORDER BY s.nama');
        $stmt->execute(['%' . $kata . '%', '%' . $kata . '%']);
        return $stmt->fetchAll();
    }
}
