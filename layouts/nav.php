<?php
$halamanAktif = basename($_SERVER['PHP_SELF'] ?? '');
$menuAktif = static function (string $berkas) use ($halamanAktif): string {
    return $halamanAktif === $berkas ? ' class="aktif" aria-current="page"' : '';
};
?>
<nav class="navbar" aria-label="Menu utama">
    <div class="nav-inner">
        <button type="button" class="nav-toggle" id="navToggle"
                aria-controls="navMenu" aria-expanded="false" aria-label="Buka menu">
            <span class="nav-toggle-icon" aria-hidden="true">
                <span></span><span></span><span></span>
            </span>
            <span class="nav-toggle-text">Menu</span>
        </button>

        <ul class="nav-menu" id="navMenu">
            <li><a href="dashboard.php"<?= $menuAktif('dashboard.php') ?>>Beranda</a></li>
            <li><a href="kelas.php"<?= $menuAktif('kelas.php') ?>>Kelas</a></li>
            <?php if (isset($_SESSION['id_user'])): ?>
                <li><a href="siswa.php"<?= $menuAktif('siswa.php') ?>>Siswa</a></li>
                <li><a href="tap.php"<?= $menuAktif('tap.php') ?>>Tap Kartu</a></li>
                <li><a href="rekap.php"<?= $menuAktif('rekap.php') ?>>Rekap Presensi</a></li>
                <li><a href="pencarian.php"<?= $menuAktif('pencarian.php') ?>>Pencarian</a></li>
                <li class="logout"><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"<?= $menuAktif('login.php') ?>>Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="konten">
