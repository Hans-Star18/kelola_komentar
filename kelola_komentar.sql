-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Apr 2022 pada 17.15
-- Versi server: 10.4.20-MariaDB
-- Versi PHP: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kelola_komentar`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_balasan_ke_balasan`
--

CREATE TABLE `tabel_balasan_ke_balasan` (
  `id` int(11) NOT NULL,
  `isi_balasan` text NOT NULL,
  `id_balasan_ke_komentar` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `pengguna_dibalas` varchar(255) DEFAULT NULL,
  `balasan_ke` int(11) NOT NULL,
  `balasan_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `balasan_diperbarui` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_balasan_ke_komentar`
--

CREATE TABLE `tabel_balasan_ke_komentar` (
  `id_komentar` int(11) NOT NULL,
  `isi_balasan` text NOT NULL,
  `balasan_ke` int(11) NOT NULL,
  `balasan_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `balasan_diperbarui` datetime NOT NULL DEFAULT current_timestamp(),
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_komentar_baru`
--

CREATE TABLE `tabel_komentar_baru` (
  `id` int(11) NOT NULL,
  `isi_komentar` text NOT NULL,
  `id_halaman` int(11) NOT NULL,
  `komentar_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `komentar_diperbarui` datetime NOT NULL DEFAULT current_timestamp(),
  `id_pengguna` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_pengguna`
--

CREATE TABLE `tabel_pengguna` (
  `id` int(11) NOT NULL,
  `nama_pengguna` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pengguna_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `pengguna_diperbarui` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tabel_balasan_ke_balasan`
--
ALTER TABLE `tabel_balasan_ke_balasan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tabel_balasan_ke_komentar`
--
ALTER TABLE `tabel_balasan_ke_komentar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tabel_komentar_baru`
--
ALTER TABLE `tabel_komentar_baru`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tabel_pengguna`
--
ALTER TABLE `tabel_pengguna`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tabel_balasan_ke_balasan`
--
ALTER TABLE `tabel_balasan_ke_balasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tabel_balasan_ke_komentar`
--
ALTER TABLE `tabel_balasan_ke_komentar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tabel_komentar_baru`
--
ALTER TABLE `tabel_komentar_baru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tabel_pengguna`
--
ALTER TABLE `tabel_pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
