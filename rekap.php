<?php
require __DIR__ . '/config/auth.php';
require_once __DIR__ . '/classes/PresensiModel.php';
require_once __DIR__ . '/classes/KelasModel.php';

$judul = 'Rekap Presensi';
$pesan = $_SESSION['pesan_rekap'] ?? '';
unset($_SESSION['pesan_rekap']);

$tanggal = is_string($_GET['tanggal'] ?? null) ? $_GET['tanggal'] : date('Y-m-d');
$idKelas = (int) ($_GET['kelas'] ?? 0);
$kelas = [];
$daftar = [];

if ($tanggal !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $tanggal) || !checkdate((int) substr($tanggal, 5, 2), (int) substr($tanggal, 8, 2), (int) substr($tanggal, 0, 4)))) {
    $pesan = 'Tanggal tidak valid.';
    $tanggal = date('Y-m-d');
}

try {
    $pdo = (new Database())->getConnection();
    $model = new Presensi($pdo);
    $kelas = (new Kelas($pdo))->getAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        cekToken();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $model->hapus($id);
            $_SESSION['pesan_rekap'] = 'Catatan presensi dihapus.';
        }
        header('Location: rekap.php');
        exit;
    }

    $daftar = $model->cari('', $tanggal, $idKelas);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $pesan = 'Rekap belum dapat dimuat. Periksa koneksi database.';
}

require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>
<link rel="stylesheet" href="assets/kelas.css">

<section class="kelas-page">
    <div class="kelas-heading">
        <div>
            <h2>Rekap Presensi</h2>
            <p>Lihat catatan kehadiran berdasarkan tanggal dan kelas.</p>
        </div>
        <span class="kelas-count"><?= count($daftar) ?> catatan</span>
    </div>

    <?php if ($pesan !== ''): ?>
        <p class="kelas-notice" role="status"><?= e($pesan) ?></p>
    <?php endif; ?>

    <section class="kelas-panel">
        <form class="presensi-filter" method="get">
            <p>
                <label for="tanggal">Tanggal</label>
                <input id="tanggal" type="date" name="tanggal" value="<?= e($tanggal) ?>">
            </p>
            <p>
                <label for="kelas">Kelas</label>
                <select id="kelas" name="kelas">
                    <option value="0">Semua kelas</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= (int) $k['id_kelas'] ?>" <?= $idKelas === (int) $k['id_kelas'] ? 'selected' : '' ?>>
                            <?= e($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <button class="kelas-save" type="submit">Tampilkan</button>
        </form>
        <p class="kelas-help">Kosongkan tanggal untuk melihat semua tanggal.</p>

        <?php if (!$daftar): ?>
            <p class="kelas-empty">Belum ada presensi untuk pilihan ini.</p>
        <?php else: ?>
            <div class="kelas-table-wrap">
                <table>
                    <thead><tr><th>NISN</th><th>Nama</th><th>Kelas</th><th>Waktu</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($daftar as $p): ?>
                            <tr>
                                <td><?= e($p['nisn']) ?></td>
                                <td><?= e($p['nama']) ?></td>
                                <td><?= e($p['nama_kelas']) ?></td>
                                <td><?= e($p['tanggal_waktu']) ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Hapus catatan presensi ini?');">
                                        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <button type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>
<?php require __DIR__ . '/layouts/footer.php'; ?>
