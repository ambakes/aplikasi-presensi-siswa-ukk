<?php
require __DIR__ . '/config/auth.php';
require_once __DIR__ . '/classes/SiswaModel.php';
require_once __DIR__ . '/classes/PresensiModel.php';

$judul = 'Simulasi Tap Kartu';
$pesan = $_SESSION['pesan_tap'] ?? '';
unset($_SESSION['pesan_tap']);

$error = '';
$nisn = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekToken();
    $nisn = trim(is_string($_POST['nisn'] ?? null) ? $_POST['nisn'] : '');

    if (!preg_match('/^[0-9]{10}$/D', $nisn)) {
        $error = 'NISN harus tepat 10 digit angka.';
    } else {
        try {
            $pdo = (new Database())->getConnection();
            $siswa = (new Siswa($pdo))->cari($nisn);

            if (!$siswa) {
                $error = 'NISN tidak terdaftar. Periksa kembali data siswa.';
            } else {
                (new Presensi($pdo))->tap($nisn);
                $_SESSION['pesan_tap'] = 'Presensi berhasil dicatat untuk ' . $siswa['nama'] . '.';
                header('Location: tap.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Presensi belum dapat disimpan. Coba lagi.';
        }
    }
}

require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>
<link rel="stylesheet" href="assets/kelas.css">

<section class="kelas-page">
    <div class="kelas-heading">
        <div>
            <h2>Simulasi Tap Kartu</h2>
            <p>Masukkan NISN untuk mencatat kehadiran siswa.</p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <p class="kelas-notice" role="status"><?= e($pesan) ?></p>
    <?php endif; ?>

    <section class="kelas-panel presensi-narrow">
        <h3>Catat presensi</h3>
        <p class="kelas-help">Waktu presensi dicatat otomatis.</p>

        <form class="kelas-form" method="post" action="tap.php" novalidate>
            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
            <p>
                <label for="nisn">NISN siswa</label><br>
                <input id="nisn" name="nisn" maxlength="10" inputmode="numeric"
                       value="<?= e($nisn) ?>" required>
                <?php if ($error !== ''): ?>
                    <br><span class="kelas-error" role="alert"><?= e($error) ?></span>
                <?php endif; ?>
            </p>
            <button class="kelas-save" type="submit">Catat Presensi</button>
            <a class="kelas-cancel" href="rekap.php">Lihat rekap</a>
        </form>
    </section>
</section>
<?php require __DIR__ . '/layouts/footer.php'; ?>
