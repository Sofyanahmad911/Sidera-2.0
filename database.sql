-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Okt 2026 pada 13.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sidera`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_desa`
--

CREATE TABLE `arsip_desa` (
  `id` bigint(20) NOT NULL,
  `judul_arsip` varchar(200) NOT NULL,
  `kategori_arsip` enum('Surat Masuk','Surat Keluar','Foto Kegiatan','Dokumen Penting','Catatan Inventaris','Lainnya') NOT NULL,
  `nomor_dokumen` varchar(100) DEFAULT NULL,
  `tgl_dokumen` date NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_bagan`
--

CREATE TABLE `kategori_bagan` (
  `id` int(11) NOT NULL,
  `nama_bagan` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_bagan`
--

INSERT INTO `kategori_bagan` (`id`, `nama_bagan`) VALUES
(1, 'SOTK Pemdes Serage'),
(2, 'BPD');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `aksi` varchar(100) NOT NULL,
  `detail_aksi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mutasi_penduduk`
--

CREATE TABLE `mutasi_penduduk` (
  `id` bigint(20) NOT NULL,
  `penduduk_id` bigint(20) NOT NULL,
  `jenis_mutasi` enum('Lahir','Mati','Datang','Pindah') NOT NULL,
  `tanggal_mutasi` date NOT NULL,
  `keterangan` text NOT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mutasi_penduduk`
--

INSERT INTO `mutasi_penduduk` (`id`, `penduduk_id`, `jenis_mutasi`, `tanggal_mutasi`, `keterangan`, `dokumen_pendukung`, `admin_id`, `created_at`) VALUES
(1, 422, 'Mati', '2026-09-29', 'qwert', NULL, 1, '2026-09-29 13:48:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penduduk`
--

CREATE TABLE `penduduk` (
  `id` bigint(20) NOT NULL,
  `foto_warga` varchar(255) DEFAULT NULL,
  `nik` varchar(16) NOT NULL,
  `no_kk` varchar(16) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `umur` int(11) NOT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `rt` varchar(3) NOT NULL,
  `rw` varchar(3) NOT NULL,
  `nama_dusun` varchar(100) NOT NULL,
  `kel_desa` varchar(100) NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `kabupaten` varchar(100) NOT NULL,
  `provinsi` varchar(100) NOT NULL,
  `agama` varchar(30) NOT NULL,
  `status_perkawinan` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NOT NULL,
  `pekerjaan` varchar(100) NOT NULL,
  `kewarganegaraan` varchar(50) DEFAULT 'wni',
  `status_kependudukan` enum('Aktif','Meninggal','Pindah') DEFAULT 'Aktif',
  `arsip_foto_ktp_kk` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_dasar` enum('Hidup','Meninggal','Pindah','Hilang') DEFAULT 'Hidup'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penduduk`
--

INSERT INTO `penduduk` (`id`, `foto_warga`, `nik`, `no_kk`, `nama_lengkap`, `tempat_lahir`, `tgl_lahir`, `umur`, `jenis_kelamin`, `rt`, `rw`, `nama_dusun`, `kel_desa`, `kecamatan`, `kabupaten`, `provinsi`, `agama`, `status_perkawinan`, `pekerjaan`, `kewarganegaraan`, `status_kependudukan`, `arsip_foto_ktp_kk`, `created_at`, `updated_at`, `status_dasar`) VALUES
(313, NULL, '5202042201948676', '', 'Sari Wijaya', 'Lombok Timur', '2011-04-22', 15, 'Perempuan', '003', '002', 'Batu Bangka', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(314, NULL, '5202043009058540', '', 'Lestari Saputra', 'Suralaga', '1950-12-25', 75, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Cerai Hidup', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(315, NULL, '5202048651033916', '', 'Zainal Hakim', 'Lombok Tengah', '1954-08-15', 72, 'Laki-laki', '003', '002', 'Batu Bangka', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Hindu', 'Cerai Hidup', 'Pegawai Swasta', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(316, NULL, '5202045598430566', '', 'Dimas Mahendra', 'Lombok Tengah', '2014-08-11', 12, 'Laki-laki', '004', '001', 'Batu Bangka', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(317, NULL, '5202048144341372', '', 'Ramdani Putra', 'Lombok Tengah', '1971-07-25', 55, 'Laki-laki', '004', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Hindu', 'Kawin', 'PNS', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(318, NULL, '5202044891010141', '', 'Kamarudin Wahyuningsih', 'Lombok Barat', '1995-11-11', 30, 'Laki-laki', '004', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Hindu', 'Belum Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(319, NULL, '5202045637569130', '', 'Rina Riyadi', 'Suralaga', '1996-08-09', 30, 'Perempuan', '001', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(320, NULL, '5202043718547815', '', 'Irfan Putra', 'Suralaga', '2002-05-12', 24, 'Laki-laki', '003', '001', 'Serage Pusat', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Hindu', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(321, NULL, '5202046252714474', '', 'Ahmad Ningsih', 'Lombok Timur', '2002-04-18', 24, 'Laki-laki', '005', '001', 'Serage Pusat', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Cerai Mati', 'Perangkat Desa', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(322, NULL, '5202041148522464', '', 'Eka Wahyuningsih', 'Lombok Barat', '1956-05-11', 70, 'Perempuan', '002', '002', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Cerai Mati', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-02 15:06:11', '2026-09-02 15:06:11', 'Hidup'),
(323, NULL, '5202042000000001', '5202041000000001', 'Ahmad', 'Lombok Tengah', '1975-01-10', 51, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(324, NULL, '5202042000000002', '5202041000000001', 'Siti', 'Lombok Tengah', '1978-05-15', 48, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(325, NULL, '5202042000000003', '5202041000000001', 'Budi', 'Lombok Tengah', '2000-08-20', 26, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(326, NULL, '5202042000000004', '5202041000000001', 'Ayu', 'Lombok Tengah', '2005-12-05', 20, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(327, NULL, '5202042000000005', '5202041000000002', 'Joko', 'Lombok Tengah', '1976-02-11', 50, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(328, NULL, '5202042000000006', '5202041000000002', 'Rini', 'Lombok Tengah', '1979-06-16', 47, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(329, NULL, '5202042000000007', '5202041000000002', 'Bayu', 'Lombok Tengah', '2001-09-21', 24, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(330, NULL, '5202042000000008', '5202041000000002', 'Putri', 'Lombok Tengah', '2006-01-06', 20, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(331, NULL, '5202042000000009', '5202041000000003', 'Andi', 'Lombok Tengah', '1977-03-12', 49, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Buruh Harian Lepas', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(332, NULL, '5202042000000010', '5202041000000003', 'Dewi', 'Lombok Tengah', '1980-07-17', 46, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(333, NULL, '5202042000000011', '5202041000000003', 'Reza', 'Lombok Tengah', '2002-10-22', 23, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(334, NULL, '5202042000000012', '5202041000000003', 'Nisa', 'Lombok Tengah', '2007-02-07', 19, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(335, NULL, '5202042000000013', '5202041000000004', 'Yudi', 'Lombok Tengah', '1978-04-13', 48, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(336, NULL, '5202042000000014', '5202041000000004', 'Sari', 'Lombok Tengah', '1981-08-18', 45, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(337, NULL, '5202042000000015', '5202041000000004', 'Rizki', 'Lombok Tengah', '2003-11-23', 22, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(338, NULL, '5202042000000016', '5202041000000004', 'Sinta', 'Lombok Tengah', '2008-03-08', 18, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(339, NULL, '5202042000000017', '5202041000000005', 'Eko', 'Lombok Tengah', '1979-05-14', 47, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(340, NULL, '5202042000000018', '5202041000000005', 'Nita', 'Lombok Tengah', '1982-09-19', 43, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(341, NULL, '5202042000000019', '5202041000000005', 'Gilang', 'Lombok Tengah', '2004-12-24', 21, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(342, NULL, '5202042000000020', '5202041000000005', 'Rara', 'Lombok Tengah', '2009-04-09', 17, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(343, NULL, '5202042000000021', '5202041000000006', 'Iwan', 'Lombok Tengah', '1980-06-15', 46, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Tukang Kayu', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(344, NULL, '5202042000000022', '5202041000000006', 'Rita', 'Lombok Tengah', '1983-10-20', 42, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(345, NULL, '5202042000000023', '5202041000000006', 'Dimas', 'Lombok Tengah', '2005-01-25', 21, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(346, NULL, '5202042000000024', '5202041000000006', 'Dinda', 'Lombok Tengah', '2010-05-10', 16, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:50', '2026-09-09 08:33:50', 'Hidup'),
(347, NULL, '5202042000000025', '5202041000000007', 'Hendra', 'Lombok Tengah', '1981-07-16', 45, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(348, NULL, '5202042000000026', '5202041000000007', 'Maya', 'Lombok Tengah', '1984-11-21', 41, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(349, NULL, '5202042000000027', '5202041000000007', 'Aditya', 'Lombok Tengah', '2006-02-26', 20, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(350, NULL, '5202042000000028', '5202041000000007', 'Tiara', 'Lombok Tengah', '2011-06-11', 15, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(351, NULL, '5202042000000029', '5202041000000008', 'Irwan', 'Lombok Tengah', '1982-08-17', 44, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(352, NULL, '5202042000000030', '5202041000000008', 'Nia', 'Lombok Tengah', '1985-12-22', 40, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(353, NULL, '5202042000000031', '5202041000000008', 'Fajar', 'Lombok Tengah', '2007-03-27', 19, 'Laki-laki', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(354, NULL, '5202042000000032', '5202041000000008', 'Amel', 'Lombok Tengah', '2012-07-12', 14, 'Perempuan', '001', '001', 'Lekong Jae', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(355, NULL, '5202042000000033', '5202041000000009', 'Surya', 'Lombok Tengah', '1983-09-18', 42, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Sopir', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(356, NULL, '5202042000000034', '5202041000000009', 'Wati', 'Lombok Tengah', '1986-01-23', 40, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(357, NULL, '5202042000000035', '5202041000000009', 'Ilham', 'Lombok Tengah', '2008-04-28', 18, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(358, NULL, '5202042000000036', '5202041000000009', 'Bela', 'Lombok Tengah', '2013-08-13', 13, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(359, NULL, '5202042000000037', '5202041000000010', 'Agus', 'Lombok Tengah', '1984-10-19', 41, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Karyawan Swasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(360, NULL, '5202042000000038', '5202041000000010', 'Ani', 'Lombok Tengah', '1987-02-24', 39, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(361, NULL, '5202042000000039', '5202041000000010', 'Kevin', 'Lombok Tengah', '2009-05-29', 17, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(362, NULL, '5202042000000040', '5202041000000010', 'Siska', 'Lombok Tengah', '2014-09-14', 11, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(363, NULL, '5202042000000041', '5202041000000011', 'Dwi', 'Lombok Tengah', '1985-11-20', 40, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Buruh Tani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(364, NULL, '5202042000000042', '5202041000000011', 'Sri', 'Lombok Tengah', '1988-03-25', 38, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(365, NULL, '5202042000000043', '5202041000000011', 'Dika', 'Lombok Tengah', '2010-06-30', 16, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(366, NULL, '5202042000000044', '5202041000000011', 'Rina', 'Lombok Tengah', '2015-10-15', 10, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(367, NULL, '5202042000000045', '5202041000000012', 'Hasan', 'Lombok Tengah', '1986-12-21', 39, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'PNS', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(368, NULL, '5202042000000046', '5202041000000012', 'Nur', 'Lombok Tengah', '1989-04-26', 37, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Guru', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(369, NULL, '5202042000000047', '5202041000000012', 'Rian', 'Lombok Tengah', '2011-07-01', 15, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(370, NULL, '5202042000000048', '5202041000000012', 'Tasya', 'Lombok Tengah', '2016-11-16', 9, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(371, NULL, '5202042000000049', '5202041000000013', 'Zainal', 'Lombok Tengah', '1987-01-22', 39, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(372, NULL, '5202042000000050', '5202041000000013', 'Halimah', 'Lombok Tengah', '1990-05-27', 36, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(373, NULL, '5202042000000051', '5202041000000013', 'Rendy', 'Lombok Tengah', '2012-08-02', 14, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(374, NULL, '5202042000000052', '5202041000000013', 'Vina', 'Lombok Tengah', '2017-12-17', 8, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(375, NULL, '5202042000000053', '5202041000000014', 'Rahman', 'Lombok Tengah', '1988-02-23', 38, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(376, NULL, '5202042000000054', '5202041000000014', 'Aisyah', 'Lombok Tengah', '1991-06-28', 35, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(377, NULL, '5202042000000055', '5202041000000014', 'Aldo', 'Lombok Tengah', '2013-09-03', 13, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(378, NULL, '5202042000000056', '5202041000000014', 'Zara', 'Lombok Tengah', '2018-01-18', 8, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(379, NULL, '5202042000000057', '5202041000000015', 'Arif', 'Lombok Tengah', '1989-03-24', 37, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Karyawan Honorer', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(380, NULL, '5202042000000058', '5202041000000015', 'Fitri', 'Lombok Tengah', '1992-07-29', 34, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Bidan', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(381, NULL, '5202042000000059', '5202041000000015', 'Dafa', 'Lombok Tengah', '2014-10-04', 11, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(382, NULL, '5202042000000060', '5202041000000015', 'Kiki', 'Lombok Tengah', '2019-02-19', 7, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(383, NULL, '5202042000000061', '5202041000000016', 'Ilham', 'Lombok Tengah', '1990-04-25', 36, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Peternak', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(384, NULL, '5202042000000062', '5202041000000016', 'Yuni', 'Lombok Tengah', '1993-08-30', 33, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(385, NULL, '5202042000000063', '5202041000000016', 'Yoga', 'Lombok Tengah', '2015-11-05', 10, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(386, NULL, '5202042000000064', '5202041000000016', 'Mila', 'Lombok Tengah', '2020-03-20', 6, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(387, NULL, '5202042000000065', '5202041000000017', 'Fauzi', 'Lombok Tengah', '1991-05-26', 35, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(388, NULL, '5202042000000066', '5202041000000017', 'Ratna', 'Lombok Tengah', '1994-09-01', 32, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(389, NULL, '5202042000000067', '5202041000000017', 'Rio', 'Lombok Tengah', '2016-12-06', 9, 'Laki-laki', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(390, NULL, '5202042000000068', '5202041000000017', 'Nadia', 'Lombok Tengah', '2021-04-21', 5, 'Perempuan', '002', '001', 'Kesempuh', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(391, NULL, '5202042000000069', '5202041000000018', 'Anwar', 'Lombok Tengah', '1970-06-27', 56, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(392, NULL, '5202042000000070', '5202041000000018', 'Dian', 'Lombok Tengah', '1973-10-02', 52, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(393, NULL, '5202042000000071', '5202041000000018', 'Candra', 'Lombok Tengah', '1995-01-07', 31, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(394, NULL, '5202042000000072', '5202041000000018', 'Sasa', 'Lombok Tengah', '2000-05-22', 26, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(395, NULL, '5202042000000073', '5202041000000019', 'Ramdan', 'Lombok Tengah', '1972-07-28', 54, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Buruh Tani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(396, NULL, '5202042000000074', '5202041000000019', 'Eka', 'Lombok Tengah', '1975-11-03', 50, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(397, NULL, '5202042000000075', '5202041000000019', 'Ivan', 'Lombok Tengah', '1997-02-08', 29, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Karyawan Swasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(398, NULL, '5202042000000076', '5202041000000019', 'Tari', 'Lombok Tengah', '2002-06-23', 24, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(399, NULL, '5202042000000077', '5202041000000020', 'Lukman', 'Lombok Tengah', '1974-08-29', 52, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Petani', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(400, NULL, '5202042000000078', '5202041000000020', 'Indah', 'Lombok Tengah', '1977-12-04', 48, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(401, NULL, '5202042000000079', '5202041000000020', 'Farel', 'Lombok Tengah', '1999-03-09', 27, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Karyawan Swasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(402, NULL, '5202042000000080', '5202041000000020', 'Uci', 'Lombok Tengah', '2004-07-24', 22, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(403, NULL, '5202042000000081', '5202041000000021', 'Herman', 'Lombok Tengah', '1976-09-30', 49, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'PNS', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(404, NULL, '5202042000000082', '5202041000000021', 'Kusuma', 'Lombok Tengah', '1979-01-05', 47, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Guru', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(405, NULL, '5202042000000083', '5202041000000021', 'Naufal', 'Lombok Tengah', '2001-04-10', 25, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(406, NULL, '5202042000000084', '5202041000000021', 'Rani', 'Lombok Tengah', '2006-08-25', 20, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(407, NULL, '5202042000000085', '5202041000000022', 'Supriadi', 'Lombok Tengah', '1978-10-31', 47, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Peternak', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(408, NULL, '5202042000000086', '5202041000000022', 'Titin', 'Lombok Tengah', '1981-02-06', 45, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(409, NULL, '5202042000000087', '5202041000000022', 'Galih', 'Lombok Tengah', '2003-05-11', 23, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(410, NULL, '5202042000000088', '5202041000000022', 'Laras', 'Lombok Tengah', '2008-09-26', 17, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(411, NULL, '5202042000000089', '5202041000000023', 'Mulyadi', 'Lombok Tengah', '1980-11-01', 45, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(412, NULL, '5202042000000090', '5202041000000023', 'Rika', 'Lombok Tengah', '1983-03-07', 43, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Pedagang', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(413, NULL, '5202042000000091', '5202041000000023', 'Satria', 'Lombok Tengah', '2005-06-12', 21, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(414, NULL, '5202042000000092', '5202041000000023', 'Kania', 'Lombok Tengah', '2010-10-27', 15, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(415, NULL, '5202042000000093', '5202041000000024', 'Rudi', 'Lombok Tengah', '1982-12-02', 43, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Wiraswasta', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(416, NULL, '5202042000000094', '5202041000000024', 'Lina', 'Lombok Tengah', '1985-04-08', 41, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(417, NULL, '5202042000000095', '5202041000000024', 'Iqbal', 'Lombok Tengah', '2007-07-13', 19, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(418, NULL, '5202042000000096', '5202041000000024', 'Elsa', 'Lombok Tengah', '2012-11-28', 13, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(419, NULL, '5202042000000097', '5202041000000025', 'Wahyu', 'Lombok Tengah', '1984-01-03', 42, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Guru Honorer', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(420, NULL, '5202042000000098', '5202041000000025', 'Susanti', 'Lombok Tengah', '1987-05-09', 39, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Kawin', 'Mengurus Rumah Tangga', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(421, NULL, '5202042000000099', '5202041000000025', 'Alif', 'Lombok Tengah', '2009-08-14', 17, 'Laki-laki', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Pelajar/Mahasiswa', 'WNI', 'Aktif', NULL, '2026-09-09 08:33:51', '2026-09-09 08:33:51', 'Hidup'),
(422, NULL, '5202042000000100', '5202041000000025', 'Fira', 'Lombok Tengah', '2014-12-29', 11, 'Perempuan', '003', '002', 'Belenje', 'Serage', 'Praya Barat Daya', 'Lombok Tengah', 'Nusa Tenggara Barat', 'Islam', 'Belum Kawin', 'Belum/Tidak Bekerja', 'WNI', 'Meninggal', NULL, '2026-09-09 08:33:51', '2026-09-29 13:48:30', 'Hidup');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_surat`
--

CREATE TABLE `pengajuan_surat` (
  `id` int(11) NOT NULL,
  `nik_pemohon` varchar(20) NOT NULL,
  `jenis_surat` varchar(100) NOT NULL,
  `keperluan` text NOT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Ditolak') DEFAULT 'Menunggu',
  `tanggal_request` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `potensi_desa`
--

CREATE TABLE `potensi_desa` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `potensi_desa`
--

INSERT INTO `potensi_desa` (`id`, `judul`, `deskripsi`, `gambar`, `created_at`) VALUES
(3, 'nyesek ', 'Kain tenun songket lombok merupakan kain tenun dengan motif asli khas pulau lombok.\r\nBerbeda dengan kain tenun rang rang, motif kain tenun songket mengisi penuh seluruh lembaran kain.\r\nSongket berasal dari kata sungkit yang berarti mengangkat.\r\nHal tersebut sesuai dengan proses pembuatan motif kain songet, dimana motif tenun tersebut dibuat dengan cara mengangkat sejumlah kain lungsi dengan lidi untuk membentuk rongga rongga.\r\nRongga rongga tersebut selanjutnya dimasuki oleh benang pakan secara berulang kali, dengan warna benang sesuai motif yang hendak dibuat.\r\nKain tenun songket memiliki beberapa keunikan, diantaranya adalah:\r\nMemiliki motif tenun yang timbul\r\nMotif memadati seluruh permukaan songket\r\nDitenun menggunakan benang emas, benang perak, ataupun benang katun berwarna\r\nMotif nya terkesan lebih mewah dan elegan\r\nProses pembuatannya memerlukan waktu lebih lama, karena detail motifnya yang lebih rumit', '1787195425_c5b9ebfae6.mp4', '2026-08-20 03:10:25'),
(4, 'Roah bubur beaq', 'Tradisi Roah Bubur Beaq merupakan cerminan kuat dari perpaduan nilai spiritual Islam dan adat istiadat lokal suku Sasak yang dikenal dengan konsep wetu telu atau akulturasi budaya.\r\nBerikut adalah beberapa poin penting yang memperdalam makna dari tradisi tersebut:\r\nMakna dan Filosofi Bubur Merah Simbol Kehidupan: Dalam filosofi masyarakat Sasak dan Nusantara pada umumnya, bubur merah (beaq) dan bubur putih (puteq) sering kali melambangkan asal-usul penciptaan manusia (pria dan wanita atau unsur darah dan kesucian).Wujud Syukur: Sajian ini menjadi media simbolis untuk menyatakan rasa syukur atas kelahiran, keselamatan, atau berkah kehidupan yang diterima oleh keluarga atau komunitas.Fungsi Sosial dan Relevansi Tradisi\r\nPenguat Tali Silaturahmi: Proses pembuatan bubur secara gotong royong mempererat hubungan antarwarga desa (batur sasak).Harmoni Agama dan Adat: Kehadiran tokoh agama (tuan guru/ustaz) bersama tokoh adat menunjukkan bahwa masyarakat Sasak berhasil menyelaraskan syariat Islam (melalui zikir dan doa) dengan ritual warisan leluhur.\r\nTolak Bala: Ritual ini sering digelar pada momen penting, seperti menyambut bulan Safar (penanggalan Hijriah/Sasak), kelahiran anak, atau setelah sembuh dari penyakit, dengan harapan dijauhkan dari marabahaya.', '1787200110_7f11416560.mp4', '2026-08-20 04:28:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_desa`
--

CREATE TABLE `profil_desa` (
  `id` int(11) NOT NULL DEFAULT 1,
  `logo_desa` varchar(255) DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `sejarah` text DEFAULT NULL,
  `tentang_desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_desa`
--

INSERT INTO `profil_desa` (`id`, `logo_desa`, `visi`, `misi`, `sejarah`, `tentang_desa`) VALUES
(1, '1788686724_52f3c56daa.jpg', '<p>Tulis Visi di sini…</p>', '<p>Tulis Misi di sini...</p>', '<p>Tulis Sejarah di sini...</p>', '<p>Tulis Tentang Desa di sini...</p>');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_konten`
--

CREATE TABLE `profil_konten` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `konten` longtext NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-file-lines',
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `is_deletable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_konten`
--

INSERT INTO `profil_konten` (`id`, `judul`, `konten`, `icon`, `gambar`, `urutan`, `is_deletable`) VALUES
(1, 'Visi Pemerintahan', 'Mewujudkan Desa Serage yang mandiri, inovatif, dan sejahtera.', 'fa-eye', NULL, 1, 0),
(2, 'Misi Pemerintahan', '<ul><li>Meningkatkan kualitas pelayanan.</li><li>Mendorong ekonomi warga.</li></ul>', 'fa-bullseye', NULL, 2, 0),
(3, 'Sejarah Desa', '<div>Sejarah Desa SERAGE ( Satu Raga)</div><div>Serage Terbentuk Berawal dari musyawarah para tokoh - tokoh yang ada di desa serage.</div><div>Para tokoh membutuhkan perjuangan baik itu, beban pikiran, tenaga yang sangat dramatis bahkan sampai membuat pertumpahan darah. Sehingga terbentuknya Desa Segare ini.</div><div>Setelah terbentuknya desa serage masyarakat sangatlah antusias dan saling bahu membahu untuk menyumbangkan tenaga dan material berupa bahan bangunan.</div>', 'fa-clock-rotate-left', NULL, 3, 0),
(6, 'potensi', 'ppp', 'fa-file-lines', '', 4, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `struktur_organisasi`
--

CREATE TABLE `struktur_organisasi` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `jabatan` varchar(100) NOT NULL,
  `nama_pejabat` varchar(100) NOT NULL,
  `foto_pejabat` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `struktur_organisasi`
--

INSERT INTO `struktur_organisasi` (`id`, `parent_id`, `jabatan`, `nama_pejabat`, `foto_pejabat`, `kategori_id`) VALUES
(8, NULL, 'ketua bpd', 'pak haris', NULL, 2),
(9, NULL, 'kepala desa', 'Herman yadi S,adm.', '1788405761_c68c054f09.jpg', 1),
(11, 9, 'kasi pemerintahan ', 'muhammad junaedi', '1791018162_edaa202a81.jpg', 1),
(14, 8, 'wakil ketua', 'maswan', NULL, 2),
(15, 9, 'sekdes', 'ramli ahmad', NULL, 1),
(16, 15, 'Kaur kesejahteraan Rakyat', 'agus munadi', NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_surat`
--

CREATE TABLE `template_surat` (
  `id` int(11) NOT NULL,
  `kode_surat` varchar(50) NOT NULL,
  `nama_surat` varchar(150) NOT NULL,
  `header_surat` text NOT NULL,
  `isi_template` longtext NOT NULL,
  `variabel_input` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variabel_input`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `template_surat`
--

INSERT INTO `template_surat` (`id`, `kode_surat`, `nama_surat`, `header_surat`, `isi_template`, `variabel_input`) VALUES
(1, 'Kesra 2. 8', 'Surat Keterangan Tidak Mampu (SKTM)', 'SKTM', '', NULL),
(2, 'Pem 1. 2', 'Surat Keterangan Domisili', 'Domisili', '', NULL),
(3, 'Pem 3. 1', 'Surat Pengantar SKCK', 'SKCK', '', NULL),
(4, 'Ket 4. 1', 'Surat Keterangan Kelahiran', 'Kelahiran', '', NULL),
(5, 'Ket 4. 2', 'Surat Keterangan Kematian', 'Kematian', '', NULL),
(6, 'Pem 1. 4', 'Surat Pengantar Pindah Penduduk', 'Pindah', '', NULL),
(7, 'Ekon 5. 1', 'Surat Keterangan Usaha (SKU)', 'SKU', '', NULL),
(8, 'Kesra 2. 9', 'Surat Keterangan Penghasilan', 'Penghasilan', '', NULL),
(9, 'Huk 1. 1', 'Surat Keterangan Ahli Waris', 'Ahli_Waris', '', NULL),
(10, 'Ptn 590', 'Surat Pengantar / Pernyataan Tanah', 'Tanah', '', NULL),
(11, 'Ket 4. 4', 'Surat Pengantar Kehilangan', 'Kehilangan', '', NULL),
(12, 'Bina 4. 1', 'Surat Keterangan Belum Menikah', 'Belum_Menikah', '', NULL),
(13, 'Umum 400.1', 'Surat Keterangan Beda Identitas', 'Beda_Nama', '', NULL),
(14, 'Kesra 2. 1', 'Surat Pengantar Nikah', 'Pengantar_Nikah', '', NULL),
(15, '145/HBH', 'Surat Pernyataan Hibah', 'hibah', 'Template dirender melalui file fisik hibah.php', '{}'),
(16, '145/JB', 'Surat Pernyataan Jual Beli', 'jual_beli', 'Template dirender melalui file fisik jual_beli.php', '{}'),
(17, '145/WRS', 'Surat pernyataan Waris', 'warisan', 'Template dirender melalui file fisik warisan.php', '{}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_surat`
--

CREATE TABLE `transaksi_surat` (
  `id` bigint(20) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `penduduk_id` bigint(20) DEFAULT NULL,
  `data_dinamis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_dinamis`)),
  `tgl_terbit` date NOT NULL,
  `file_pdf_path` varchar(255) NOT NULL,
  `petugas_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `role` enum('Super Admin','Operator') DEFAULT 'Operator',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_admin`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$YmPJKLq.MgTdzY7iN3CWxOi1lPgZDz0ReAWkI9dhlG826USB4QPby', 'Administrator Desa', 'Super Admin', '2026-08-06 20:09:03'),
(2, 'asep', '$2y$10$dI8U99RPqH8jmPfO9oEy.OkzHHtExQt7jjm/c.FaR4eu0uudvxEEq', 'asep', 'Super Admin', '2026-09-01 03:31:25');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `arsip_desa`
--
ALTER TABLE `arsip_desa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori_bagan`
--
ALTER TABLE `kategori_bagan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indeks untuk tabel `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penduduk_id` (`penduduk_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indeks untuk tabel `penduduk`
--
ALTER TABLE `penduduk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `idx_pencarian` (`nik`,`nama_lengkap`),
  ADD KEY `idx_filter` (`rt`,`rw`,`pekerjaan`);

--
-- Indeks untuk tabel `pengajuan_surat`
--
ALTER TABLE `pengajuan_surat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `potensi_desa`
--
ALTER TABLE `potensi_desa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `profil_desa`
--
ALTER TABLE `profil_desa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `profil_konten`
--
ALTER TABLE `profil_konten`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `template_surat`
--
ALTER TABLE `template_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_surat` (`kode_surat`);

--
-- Indeks untuk tabel `transaksi_surat`
--
ALTER TABLE `transaksi_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_surat` (`nomor_surat`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `penduduk_id` (`penduduk_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `arsip_desa`
--
ALTER TABLE `arsip_desa`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kategori_bagan`
--
ALTER TABLE `kategori_bagan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT untuk tabel `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `penduduk`
--
ALTER TABLE `penduduk`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=423;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_surat`
--
ALTER TABLE `pengajuan_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `potensi_desa`
--
ALTER TABLE `potensi_desa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `profil_konten`
--
ALTER TABLE `profil_konten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `template_surat`
--
ALTER TABLE `template_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `transaksi_surat`
--
ALTER TABLE `transaksi_surat`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  ADD CONSTRAINT `mutasi_penduduk_ibfk_1` FOREIGN KEY (`penduduk_id`) REFERENCES `penduduk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mutasi_penduduk_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD CONSTRAINT `struktur_organisasi_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `struktur_organisasi` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi_surat`
--
ALTER TABLE `transaksi_surat`
  ADD CONSTRAINT `transaksi_surat_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `template_surat` (`id`),
  ADD CONSTRAINT `transaksi_surat_ibfk_2` FOREIGN KEY (`penduduk_id`) REFERENCES `penduduk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
