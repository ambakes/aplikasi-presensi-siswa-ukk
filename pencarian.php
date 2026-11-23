<?php
require __DIR__ . '/config/auth.php';
require_once __DIR__ . '/classes/SiswaModel.php';
require_once __DIR__ . '/classes/PresensiModel.php';

$judul = 'Pencarian';
$kata = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$tanggal = is_string($_GET['tanggal'] ?? null) ? $_GET['tanggal'] : '';
$pesan = '';
$siswa = [];
$presensi = [];

if ($tanggal !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $tanggal) || !checkdate((int) substr($tanggal, 5, 2), (int) substr($tanggal, 8, 2), (int) substr($tanggal, 0, 4)))) {
    $pesan = 'Tanggal tidak valid.';
    $tanggal = '';
}

try {
    $pdo = (new Database())->getConnection();
    $siswa = (new Siswa($pdo))->cariSiswa($kata);
    $presensi = (new Presensi($pdo))->cari($kata, $tanggal, 0);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $pesan = 'Pencarian belum dapat dimuat. Periksa koneksi database.';
}

require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>
<link rel="stylesheet" href="assets/kelas.css">

<section class="kelas-page">
    <div class="kelas-heading">
        <div>
            <h2>Pencarian</h2>
            <p>Cari siswa dan catatan presensi berdasarkan nama atau NISN.</p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <p class="kelas-notice" role="status"><?= e($pesan) ?></p>
    <?php endif; ?>

    <section class="kelas-panel">
        <form class="presensi-filter" method="get">
            <p>
                <label for="q">Nama atau NISN</label>
                <input id="q" name="q" value="<?= e($kata) ?>">
            </p>
            <p>
                <label for="tanggal">Tanggal presensi</label>
                <input id="tanggal" type="date" name="tanggal" value="<?= e($tanggal) ?>">
            </p>
            <button class="kelas-save" type="submit">Cari</button>
        </form>
        <p class="kelas-help">Tanggal hanya menyaring hasil presensi. Kosongkan untuk semua tanggal.</p>

        <h3>Hasil siswa</h3>
        <?php if (!$siswa): ?>
            <p class="kelas-empty">Tidak ada siswa yang cocok.</p>
        <?php else: ?>
            <div class="kelas-table-wrap">
                <table>
                    <thead><tr><th>NISN</th><th>Nama</th><th>Kelas</th></tr></thead>
                    <tbody>
                        <?php foreach ($siswa as $s): ?>
                            <tr>
                                <td><?= e($s['nisn']) ?></td>
                                <td><?= e($s['nama']) ?></td>
                                <td><?= e($s['nama_kelas']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h3 class="presensi-section-title">Hasil presensi</h3>
        <?php if (!$presensi): ?>
            <p class="kelas-empty">Tidak ada presensi yang cocok.</p>
        <?php else: ?>
            <div class="kelas-table-wrap">
                <table>
                    <thead><tr><th>NISN</th><th>Nama</th><th>Kelas</th><th>Waktu</th></tr></thead>
                    <tbody>
                        <?php foreach ($presensi as $p): ?>
                            <tr>
                                <td><?= e($p['nisn']) ?></td>
                                <td><?= e($p['nama']) ?></td>
                                <td><?= e($p['nama_kelas']) ?></td>
                                <td><?= e($p['tanggal_waktu']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>
<?php require __DIR__ . '/layouts/footer.php'; ?>
