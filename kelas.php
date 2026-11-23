<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/KelasModel.php';
$judul = 'Kelola Kelas';
$pesan = $_SESSION['pesan_kelas'] ?? '';
unset($_SESSION['pesan_kelas']);
$errorNama = '';
$errorJurusan = '';
$nama = '';
$jurusan = '';
$id = 0;
$daftarKelas = [];
try {
    $kelas = new Kelas((new Database())->getConnection());
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $aksi = $_POST['aksi'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);
        if ($aksi === 'hapus' && $id > 0) {
            try {
                $kelas->hapus($id);
                $_SESSION['pesan_kelas'] = 'Kelas berhasil dihapus.';
            } catch (PDOException $e) {
                if (($e->errorInfo[1] ?? null) === 1451) {
                    $_SESSION['pesan_kelas'] = 'Kelas tidak bisa dihapus karena masih memiliki siswa.';
                } else {
                    throw $e;
                }
            }
            header('Location: kelas.php');
            exit;
        }
        if ($aksi === 'simpan') {
            $nama = trim(is_string($_POST['nama_kelas'] ?? null) ? $_POST['nama_kelas'] : '');
            $jurusan = trim(is_string($_POST['jurusan'] ?? null) ? $_POST['jurusan'] : '');
            if ($nama === '' || mb_strlen($nama) > 20) {
                $errorNama = 'Nama kelas wajib diisi, maksimal 20 karakter.';
            }
            if ($jurusan === '' || mb_strlen($jurusan) > 50) {
                $errorJurusan = 'Jurusan wajib diisi, maksimal 50 karakter.';
            }
            if ($errorNama === '' && $errorJurusan === '') {
                try {
                    $kelas->simpan($nama, $jurusan, $id);
                    $_SESSION['pesan_kelas'] = $id > 0 ? 'Kelas berhasil diubah.' : 'Kelas berhasil ditambahkan.';
                    header('Location: kelas.php');
                    exit;
                } catch (PDOException $e) {
                    if (($e->errorInfo[1] ?? null) === 1062) {
                        $errorNama = 'Nama kelas sudah digunakan.';
                    } else {
                        throw $e;
                    }
                }
            }
        }
    } elseif (isset($_GET['ubah'])) {
        $data = $kelas->cari((int) $_GET['ubah']);
        if ($data) {
            $id = (int) $data['id_kelas'];
            $nama = $data['nama_kelas'];
            $jurusan = $data['jurusan'];
        } else {
            $pesan = 'Kelas tidak ditemukan.';
        }
    }
    $daftarKelas = $kelas->getAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $pesan = 'Data kelas belum dapat diproses. Periksa koneksi database.';
}
require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/nav.php';
?>
<link rel="stylesheet" href="assets/kelas.css">

<section class="kelas-page">
    <div class="kelas-heading">
        <div>
            <h2>Kelola Kelas</h2>
            <p>Tambah dan kelola kelas untuk data siswa.</p>
        </div>
        <span class="kelas-count"><?= count($daftarKelas) ?> kelas terdaftar</span>
    </div>

    <?php if ($pesan !== ''): ?>
        <p class="kelas-notice" role="status"><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="kelas-layout">
        <section class="kelas-panel kelas-form-panel">
            <h3><?= $id > 0 ? 'Ubah Kelas' : 'Tambah Kelas' ?></h3>
            <p class="kelas-help">Isi nama kelas dan jurusan, lalu simpan.</p>

            <form class="kelas-form" method="post" action="kelas.php">
                <input type="hidden" name="aksi" value="simpan">
                <input type="hidden" name="id" value="<?= $id ?>">
                <p>
                    <label for="nama_kelas">Nama kelas</label><br>
                    <input id="nama_kelas" name="nama_kelas" maxlength="20" required
                           value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($errorNama !== ''): ?>
                        <br><span class="kelas-error" role="alert"><?= htmlspecialchars($errorNama, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="jurusan">Jurusan</label><br>
                    <input id="jurusan" name="jurusan" maxlength="50" required
                           value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($errorJurusan !== ''): ?>
                        <br><span class="kelas-error" role="alert"><?= htmlspecialchars($errorJurusan, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </p>
                <button class="kelas-save" type="submit">Simpan Kelas</button>
                <?php if ($id > 0): ?>
                    <a class="kelas-cancel" href="kelas.php">Batal ubah</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="kelas-panel kelas-list-panel">
            <h3>Daftar Kelas</h3>
            <p class="kelas-help">Kelas yang sudah tersimpan ditampilkan di sini.</p>
            <?php if ($daftarKelas === []): ?>
                <p class="kelas-empty">Belum ada kelas. Tambahkan kelas melalui form di samping.</p>
            <?php else: ?>
                <div class="kelas-table-wrap">
                    <table>
                        <thead><tr><th>Nama Kelas</th><th>Jurusan</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($daftarKelas as $data): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['nama_kelas'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($data['jurusan'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <a href="kelas.php?ubah=<?= (int) $data['id_kelas'] ?>">Ubah</a>
                                        <form method="post" action="kelas.php" style="display:inline"
                                              onsubmit="return confirm('Hapus kelas ini?');">
                                            <input type="hidden" name="aksi" value="hapus">
                                            <input type="hidden" name="id" value="<?= (int) $data['id_kelas'] ?>">
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
