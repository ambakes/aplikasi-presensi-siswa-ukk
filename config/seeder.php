<?php
// seeder.php - data awal (akun admin, kelas, siswa contoh)
// Jalankan SETELAH tabel dibuat:
//   - terminal dari folder proyek : php config/seeder.php
// Aman dijalankan berulang (data yang sudah ada dilewati).

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Seeder hanya dapat dijalankan melalui terminal.');
}
require __DIR__ . '/database.php';

$pdo = (new Database())->getConnection();

// 1. Akun admin demo (password disimpan dengan password_hash)
$stmt = $pdo->prepare(
    'INSERT IGNORE INTO users (username, password) VALUES (?, ?)'
);
$stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);

// 2. Kelas (nama_kelas maksimal 20 karakter)
$daftarKelas = [
    ['X TKJ 1',  'Teknik Komputer dan Jaringan'],
    ['X RPL 1',  'Rekayasa Perangkat Lunak'],
    ['XI TKJ 1', 'Teknik Komputer dan Jaringan'],
    ['XI RPL 1', 'Rekayasa Perangkat Lunak'],
];
$stmt = $pdo->prepare(
    'INSERT IGNORE INTO kelas (nama_kelas, jurusan) VALUES (?, ?)'
);
foreach ($daftarKelas as $k) {
    $stmt->execute($k);
}

// 3. Siswa contoh (NISN = PRIMARY KEY, teks 10 digit, ada yang diawali angka 0)
$daftarSiswa = [
    ['0012345678', 'Andi Pratama',   'Jl. Pemuda No. 10, Samarinda', 'X TKJ 1'],
    ['0023456789', 'Budi Santoso',   'Jl. Juanda No. 5, Samarinda',  'X RPL 1'],
    ['0034567890', 'Citra Lestari',  null,                           'XI TKJ 1'],
    ['0045678901', 'Dewi Anggraini', 'Jl. Gatot Subroto No. 22',     'XI RPL 1'],
    ['0056789012', 'Eko Wijaya',     'Jl. Ahmad Yani No. 8',         'X TKJ 1'],
];

$cariKelas = $pdo->prepare('SELECT id_kelas FROM kelas WHERE nama_kelas = ?');
$tambahSiswa = $pdo->prepare(
    'INSERT IGNORE INTO siswa (nisn, nama, alamat, id_kelas) VALUES (?, ?, ?, ?)'
);

foreach ($daftarSiswa as [$nisn, $nama, $alamat, $namaKelas]) {
    $cariKelas->execute([$namaKelas]);
    $idKelas = $cariKelas->fetchColumn();
    $tambahSiswa->execute([$nisn, $nama, $alamat, $idKelas]);
}

header('Content-Type: text/plain; charset=utf-8');
echo "Seeder selesai.\n";
echo "Akun demo -> username: admin | password: admin123\n";
