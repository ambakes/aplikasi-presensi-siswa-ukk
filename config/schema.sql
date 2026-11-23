-- schema.sql - Aplikasi Presensi Siswa SMK TI Airlangga Samarinda
-- Struktur sesuai lembar soal: 3 tabel inti (kelas, siswa, presensi) + 1 tabel pendukung (users)
-- Urutan: tabel yang dirujuk dibuat lebih dulu (kelas -> siswa -> presensi). users bebas.

CREATE DATABASE IF NOT EXISTS `uuk-website`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `uuk-website`;

-- Tabel kelas
CREATE TABLE kelas (
    id_kelas   INT         NOT NULL AUTO_INCREMENT,
    nama_kelas VARCHAR(20) NOT NULL,
    jurusan    VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_kelas),
    UNIQUE KEY uq_kelas_nama (nama_kelas)
) ENGINE=InnoDB;

-- Tabel siswa
-- NISN adalah PRIMARY KEY dan bertipe CHAR(10) agar angka 0 di depan tidak hilang
CREATE TABLE siswa (
    nisn     CHAR(10)     NOT NULL,
    nama     VARCHAR(100) NOT NULL,
    alamat   VARCHAR(255) NULL,
    id_kelas INT          NOT NULL,
    PRIMARY KEY (nisn),
    CONSTRAINT fk_siswa_kelas
        FOREIGN KEY (id_kelas) REFERENCES kelas (id_kelas)
        ON DELETE RESTRICT   -- kelas yang masih punya siswa tidak bisa dihapus
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabel presensi: satu baris untuk setiap tap kartu
-- Tidak ada kolom status: siswa yang tidak tap dianggap tidak hadir
CREATE TABLE presensi (
    id            INT      NOT NULL AUTO_INCREMENT,
    nisn          CHAR(10) NOT NULL,
    tanggal_waktu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_presensi_siswa
        FOREIGN KEY (nisn) REFERENCES siswa (nisn)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabel pendukung: akun administrator (tidak berhubungan dengan tabel lain)
CREATE TABLE users (
    id_user  INT          NOT NULL AUTO_INCREMENT,
    username VARCHAR(50)  NOT NULL,
    password VARCHAR(255) NOT NULL,   -- diisi hasil password_hash()
    PRIMARY KEY (id_user),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB;