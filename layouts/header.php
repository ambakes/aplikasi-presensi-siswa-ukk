<?php
// header.php - dipakai di semua halaman
// Cara pakai di tiap halaman:
//   $judul = 'Data Siswa';
//   require 'header.php';
// Khusus halaman login, set $halaman_publik = true; sebelum require.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi: semua halaman selain login wajib sudah login
if (empty($halaman_publik) && !isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$judul = $judul ?? 'Presensi Siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul) ?> - Presensi SMK TI Airlangga</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header">
    <div class="site-header-inner">
        <a class="site-brand" href="dashboard.php" aria-label="Kembali ke beranda">
            <img src="assets/images/SMK%20PUSAT%20KEUNGGULAN%20(2).png" width="300" height="61"
                 alt="Logo SMK TI Airlangga Samarinda dan SMK Pusat Keunggulan">
        </a>

        <div class="site-header-copy">
            <p class="site-kicker">Sistem Informasi Sekolah</p>
            <h1>Aplikasi Presensi Siswa</h1>
            <p class="site-tagline">Presensi siswa berbasis kartu</p>
        </div>
    </div>
</header>
