<?php
require_once __DIR__ . '/BaseModel.php';

class Kelas extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query('SELECT id_kelas, nama_kelas, jurusan FROM kelas ORDER BY nama_kelas')->fetchAll();
    }

    public function cari(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kelas WHERE id_kelas = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function simpan(string $nama, string $jurusan, int $id): void
    {
        if ($id > 0) {
            $stmt = $this->pdo->prepare('UPDATE kelas SET nama_kelas = ?, jurusan = ? WHERE id_kelas = ?');
            $stmt->execute([$nama, $jurusan, $id]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO kelas (nama_kelas, jurusan) VALUES (?, ?)');
            $stmt->execute([$nama, $jurusan]);
        }
    }

    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM kelas WHERE id_kelas = ?');
        $stmt->execute([$id]);
    }
}
