<?php
require __DIR__ . '/config/auth.php';
require_once __DIR__ . '/classes/SiswaModel.php';
require_once __DIR__ . '/classes/KelasModel.php';

$judul = 'Kelola Siswa';
$pesan = $_SESSION['pesan_siswa'] ?? '';
unset($_SESSION['pesan_siswa']);

$error = [];
$nisn = '';
$lama = '';
$nama = '';
$alamat = '';
$idKelas = 0;
$daftar = [];
$kelas = [];

try {
    $pdo = (new Database())->getConnection();
    $model = new Siswa($pdo);
    $kelas = (new Kelas($pdo))->getAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        cekToken();
        $aksi = $_POST['aksi'] ?? '';

        if ($aksi === 'hapus') {
            $model->hapus(is_string($_POST['nisn'] ?? null) ? $_POST['nisn'] : '');
            $_SESSION['pesan_siswa'] = 'Siswa berhasil dihapus.';
            header('Location: siswa.php');
            exit;
        }

        if ($aksi === 'simpan') {
            $nisn = trim(is_string($_POST['nisn'] ?? null) ? $_POST['nisn'] : '');
            $lama = is_string($_POST['lama'] ?? null) ? $_POST['lama'] : '';
            $nama = trim(is_string($_POST['nama'] ?? null) ? $_POST['nama'] : '');
            $alamat = trim(is_string($_POST['alamat'] ?? null) ? $_POST['alamat'] : '');
            $idKelas = (int) ($_POST['id_kelas'] ?? 0);

            if (!preg_match('/^[0-9]{10}$/D', $nisn)) {
                $error['nisn'] = 'NISN harus tepat 10 digit angka.';
            }
            if ($nama === '' || mb_strlen($nama) > 100) {
                $error['nama'] = 'Nama wajib diisi, maksimal 100 karakter.';
            }
            if (mb_strlen($alamat) > 255) {
                $error['alamat'] = 'Alamat maksimal 255 karakter.';
            }
            if (!(new Kelas($pdo))->cari($idKelas)) {
                $error['id_kelas'] = 'Pilih kelas yang tersedia.';
            }
            if ($lama !== '' && !$model->cari($lama)) {
                $error['nisn'] = 'Siswa tidak ditemukan.';
            }

            if (!$error) {
                try {
                    $model->simpan($nisn, $nama, $alamat, $idKelas, $lama);
                    $_SESSION['pesan_siswa'] = $lama !== ''
                        ? 'Siswa berhasil diubah.'
                        : 'Siswa berhasil ditambahkan.';
                    header('Location: siswa.php');
                    exit;
                } catch (PDOException $e) {
                    if (($e->errorInfo[1] ?? null) === 1062) {
                        $error['nisn'] = 'NISN sudah terdaftar.';
                    } else {
                        throw $e;
                    }
                }
            }
        }
    } elseif (isset($_GET['ubah'])) {
        $data = $model->cari(is_string($_GET['ubah']) ? $_GET['ubah'] : '');
        if ($data) {
            $nisn = $lama = $data['nisn'];
            $nama = $data['nama'];
            $alamat = $data['alamat'] ?? '';
            $idKelas = (int) $data['id_kelas'];
        } else {
            $pesan = 'Siswa tidak ditemukan.';
        }
    }

    $daftar = $model->getAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $pesan = 'Data siswa belum dapat diproses. Periksa koneksi database.';
}

require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>
<link rel="stylesheet" href="assets/kelas.css">

<section class="kelas-page">
    <div class="kelas-heading">
        <div>
            <h2>Kelola Siswa</h2>
            <p>Tambah dan kelola data siswa beserta kelasnya.</p>
        </div>
        <span class="kelas-count"><?= count($daftar) ?> siswa terdaftar</span>
    </div>

    <?php if ($pesan !== ''): ?>
        <p class="kelas-notice" role="status"><?= e($pesan) ?></p>
    <?php endif; ?>

    <div class="kelas-layout">
        <section class="kelas-panel">
            <h3><?= $lama !== '' ? 'Ubah Siswa' : 'Tambah Siswa' ?></h3>
            <p class="kelas-help">Masukkan NISN 10 digit dan pilih kelas.</p>

            <form class="kelas-form" method="post" action="siswa.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <input type="hidden" name="aksi" value="simpan">
                <input type="hidden" name="lama" value="<?= e($lama) ?>">

                <p>
                    <label for="nisn">NISN</label><br>
                    <input id="nisn" name="nisn" inputmode="numeric" maxlength="10" value="<?= e($nisn) ?>" required>
                    <?php if (isset($error['nisn'])): ?>
                        <br><span class="kelas-error" role="alert"><?= e($error['nisn']) ?></span>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="nama">Nama siswa</label><br>
                    <input id="nama" name="nama" maxlength="100" value="<?= e($nama) ?>" required>
                    <?php if (isset($error['nama'])): ?>
                        <br><span class="kelas-error" role="alert"><?= e($error['nama']) ?></span>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="alamat">Alamat (boleh kosong)</label><br>
                    <input id="alamat" name="alamat" maxlength="255" value="<?= e($alamat) ?>">
                    <?php if (isset($error['alamat'])): ?>
                        <br><span class="kelas-error" role="alert"><?= e($error['alamat']) ?></span>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="id_kelas">Kelas</label><br>
                    <select id="id_kelas" name="id_kelas" class="presensi-select" required>
                        <option value="">Pilih kelas</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= (int) $k['id_kelas'] ?>" <?= $idKelas === (int) $k['id_kelas'] ? 'selected' : '' ?>>
                                <?= e($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($error['id_kelas'])): ?>
                        <br><span class="kelas-error" role="alert"><?= e($error['id_kelas']) ?></span>
                    <?php endif; ?>
                </p>

                <?php if (!$kelas): ?>
                    <p class="kelas-help">Belum ada kelas. Tambahkan kelas terlebih dahulu.</p>
                <?php endif; ?>
                <button class="kelas-save" type="submit">Simpan Siswa</button>
                <?php if ($lama !== ''): ?>
                    <a class="kelas-cancel" href="siswa.php">Batal ubah</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="kelas-panel">
            <h3>Daftar Siswa</h3>
            <p class="kelas-help">Daftar siswa beserta kelasnya.</p>
            <?php if (!$daftar): ?>
                <p class="kelas-empty">Belum ada siswa yang ditampilkan.</p>
            <?php else: ?>
                <div class="kelas-table-wrap">
                    <table>
                        <thead><tr><th>NISN</th><th>Nama</th><th>Kelas</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($daftar as $s): ?>
                                <tr>
                                    <td><?= e($s['nisn']) ?></td>
                                    <td><?= e($s['nama']) ?></td>
                                    <td><?= e($s['nama_kelas']) ?></td>
                                    <td>
                                        <a href="siswa.php?ubah=<?= urlencode($s['nisn']) ?>">Ubah</a>
                                        <form method="post" action="siswa.php" style="display:inline"
                                              onsubmit="return confirm('Hapus siswa dan riwayat presensinya?');">
                                            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                                            <input type="hidden" name="aksi" value="hapus">
                                            <input type="hidden" name="nisn" value="<?= e($s['nisn']) ?>">
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
    </div>
</section>
<?php require __DIR__ . '/layouts/footer.php'; ?>
