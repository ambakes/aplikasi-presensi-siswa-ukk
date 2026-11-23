<?php
require_once __DIR__ . '/classes/user.php';
session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: rekap.php');
    exit;
}

$judul = 'Login';
$halaman_publik = true;
$username = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';
    $username = is_string($inputUsername) ? trim($inputUsername) : '';
    $password = is_string($inputPassword) ? $inputPassword : '';

    if ($username === '' || $password === '') {
        $pesan = 'Isi username dan password terlebih dahulu.';
    } else {
        try {
            $database = new Database();
            $users = new User($database->getConnection());
            $akun = $users->cariUsername($username);

            if ($akun && password_verify($password, $akun['password'])) {
                session_regenerate_id(true);
                $_SESSION['id_user'] = $akun['id_user'];
                $_SESSION['username'] = $akun['username'];

                header('Location: rekap.php');
                exit;
            }

            $pesan = 'Username atau password salah.';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $pesan = 'Login belum bisa diproses. Cek koneksi database.';
        }
    }
}

require __DIR__ . '/layouts/header.php';
?>
<link rel="stylesheet" href="assets/login.css">
<main class="konten login-main">
<section class="login-layout" aria-labelledby="login-title">
<div class="login-intro">
    <p class="login-school">SMK TI Airlangga Samarinda</p>
    <h2>Presensi siswa,<br>lebih tertata.</h2>
    <p>Kelola data siswa dan pantau kehadiran dalam satu tempat.</p>
    <div class="login-intro-note">Aplikasi Presensi Siswa <span>•</span> Administrator</div>
</div>
<div class="login-panel">
<div class="login-panel-heading">
    <h2 id="login-title">Masuk</h2>
    <p>Gunakan akun administrator untuk melanjutkan.</p>
</div>

<?php if ($pesan !== ''): ?>
    <p class="login-error" role="alert"><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form class="login-form" method="post" action="login.php">
    <p>
        <label for="username">Username</label>
        <input id="username" name="username" type="text" maxlength="50"
               value="<?= htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
               autocomplete="username" placeholder="Masukkan username" required>
    </p>
    <p>
        <label for="password">Password</label>
        <input id="password" name="password" type="password"
               autocomplete="current-password" placeholder="Masukkan password" required>
    </p>
    <button type="submit">Masuk ke aplikasi <span aria-hidden="true">→</span></button>
</form>
<p class="login-caption">Halaman ini khusus untuk administrator sekolah.</p>
</div>
</section>

<?php require __DIR__ . '/layouts/footer.php'; ?>
