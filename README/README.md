# Aplikasi Presensi Siswa

Aplikasi PHP native dan MariaDB untuk administrator mengelola kelas, siswa, dan presensi melalui simulasi tap NISN.

## Fitur

Login dan logout, kelola kelas dan siswa, simulasi tap, rekap dan penghapusan presensi, serta pencarian siswa dan presensi.

## Instalasi

1. Salin folder proyek ke `C:\laragon\www\uuk-website` dan jalankan Apache dan MySQL di Laragon.
2. Untuk instalasi baru, jalankan `config/schema.sql` di phpMyAdmin. Jangan jalankan ulang pada database yang sudah memiliki data tanpa memeriksa isinya.
3. Sesuaikan `config/config.php` dengan nama database serta akun MySQL di komputer.
4. Buat akun administrator dengan password yang dihasilkan `password_hash()` atau gunakan akun yang sudah tersedia. Untuk data contoh instalasi baru, `config/seeder.php` menyediakan akun dan contoh data; periksa isi file sebelum menjalankannya.
5. Buka `http://localhost/uuk-website/` dan masuk dengan akun administrator.

## Hubungan tabel

Satu kelas memiliki banyak siswa; satu siswa dapat memiliki banyak catatan presensi. Tabel users menyimpan akun administrator.

## Struktur folder

- `classes/`: class untuk mengakses data pengguna, kelas, siswa, dan presensi.
- `config/`: koneksi database, pemeriksaan login, skema SQL, dan seeder.
- `layouts/`: header, navigasi, dan footer bersama.
- `assets/`: stylesheet dan gambar.
- File PHP di folder utama: halaman aplikasi yang dibuka dari browser.

## Pengujian dan dokumentasi

Catat hasil pengujian login salah, akses tanpa login, validasi NISN, NISN duplikat, tap dengan NISN tidak terdaftar dan terdaftar, larangan menghapus kelas yang masih memiliki siswa, serta pencarian. Tambahkan ERD, tabel hasil pengujian, akun demo yang aman, dan tangkapan layar halaman utama sesuai permintaan soal.
