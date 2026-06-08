-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2026 at 10:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_lat_hrd`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chat`
--

CREATE TABLE `tbl_chat` (
  `id_chat` int(11) NOT NULL,
  `id_pengirim` varchar(50) DEFAULT NULL,
  `id_penerima` varchar(50) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chat`
--

INSERT INTO `tbl_chat` (`id_chat`, `id_pengirim`, `id_penerima`, `pesan`, `waktu`, `is_read`) VALUES
(5, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(6, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(7, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(8, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(9, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(10, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:49', 0),
(11, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(12, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(13, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(14, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(15, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(16, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:50', 0),
(17, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:51', 0),
(18, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:51', 0),
(19, 'HRD001', 'ADM001', 'p', '2026-05-21 14:37:51', 0),
(20, 'adm091', 'tes001', 'woi anak baru\'', '2026-05-21 14:51:12', 0),
(96, 'adm091', 'BOT', 'Halo, saya ingin bertanya.', '2026-05-28 14:19:26', 0),
(97, 'BOT', 'adm091', 'Halo! Saya asisten virtual Anda. Saya siap membantu dengan pertanyaan seputar sistem kepegawaian. Silakan pilih pertanyaan di bawah.', '2026-05-28 14:19:26', 1),
(98, 'adm091', 'BOT', 'Bagaimana cara mengajukan cuti?', '2026-05-28 14:19:30', 0),
(99, 'BOT', 'adm091', 'Untuk mengajukan cuti, silakan navigasi ke menu \"Transaksi\" > \"Cuti\", lalu klik tombol \"Tambah Pengajuan\". Isi formulir yang tersedia dan kirim.', '2026-05-28 14:19:30', 1),
(152, 'adm091', 'GEMINI', 'test', '2026-05-28 14:34:05', 0),
(153, 'GEMINI', 'adm091', 'Google API Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ModelService.ListModels to see the list of available models and their supported methods.', '2026-05-28 14:34:05', 1),
(154, 'adm091', 'ADM001', 'p valo rek', '2026-05-28 15:11:04', 0),
(155, 'HRD001', 'PGW202605004', '1234', '2026-05-28 15:14:28', 1),
(156, 'HRD001', 'PGW202605004', 'p valo', '2026-05-28 15:14:30', 1),
(163, 'HRD001', 'BOT', 'Halo, saya ingin bertanya.', '2026-05-28 15:52:50', 0),
(164, 'BOT', 'HRD001', 'Halo! Saya asisten virtual Anda. Saya siap membantu dengan pertanyaan seputar sistem kepegawaian. Silakan pilih pertanyaan di bawah.', '2026-05-28 15:52:50', 1),
(165, 'HRD001', 'BOT', 'Bagaimana cara mengajukan cuti?', '2026-05-28 15:52:52', 0),
(166, 'BOT', 'HRD001', 'Untuk mengajukan cuti, silakan navigasi ke menu \"Transaksi\" > \"Cuti\", lalu klik tombol \"Tambah Pengajuan\". Isi formulir yang tersedia dan kirim.', '2026-05-28 15:52:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cuti`
--

CREATE TABLE `tbl_cuti` (
  `id_cuti` varchar(15) NOT NULL,
  `id_pegawai` varchar(15) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cuti`
--

INSERT INTO `tbl_cuti` (`id_cuti`, `id_pegawai`, `tgl_mulai`, `tgl_selesai`, `alasan`, `status`, `created_at`) VALUES
('CT202605001', 'PGW202605001', '2026-05-08', '2026-05-16', '123', 'disetujui', '2026-05-08 10:39:34'),
('CT202605002', 'PGW202605001', '2026-05-08', '2026-05-08', 'izin\r\n', 'disetujui', '2026-05-21 07:17:26'),
('CT202605003', 'PGW202605004', '2026-05-28', '2026-05-29', 'sakit', 'disetujui', '2026-05-28 08:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_departemen`
--

CREATE TABLE `tbl_departemen` (
  `id_departemen` varchar(10) NOT NULL,
  `departemen` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_departemen`
--

INSERT INTO `tbl_departemen` (`id_departemen`, `departemen`, `created_at`) VALUES
('DEP001', 'Keuangan', '2026-05-08 09:52:47'),
('DEP002', 'Pemasaran', '2026-05-08 10:10:12'),
('DEP003', 'Teknologi Informasi', '2026-05-08 10:10:21'),
('DEP004', 'Produksi', '2026-05-08 10:10:37'),
('DEP005', 'Sumber Daya Manusia', '2026-05-08 10:10:54'),
('DEP006', 'Pengangguran', '2026-05-28 08:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_izin`
--

CREATE TABLE `tbl_izin` (
  `id_izin` varchar(15) NOT NULL,
  `id_pegawai` varchar(15) DEFAULT NULL,
  `tgl_izin` date DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_izin`
--

INSERT INTO `tbl_izin` (`id_izin`, `id_pegawai`, `tgl_izin`, `alasan`, `status`, `created_at`) VALUES
('CT202605001', 'PGW202605001', '2026-05-15', 'a', 'disetujui', '2026-05-08 10:38:02'),
('CT202605002', 'staf01', '2026-04-30', 'asdasd', 'pending', '2026-05-28 07:54:43'),
('CT202605003', 'staf01', '2026-05-29', 'asdasd', 'pending', '2026-05-28 07:54:58'),
('CT202605004', 'staf01', '2026-05-29', 'asdad', 'pending', '2026-05-28 07:55:12'),
('CT202605005', 'PGW202605004', '2026-05-28', 'sakit bangg', 'disetujui', '2026-05-28 08:00:04'),
('CT202605006', 'PGW202605004', '2026-05-29', 'skait\r\n', 'ditolak', '2026-05-28 08:20:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jabatan`
--

CREATE TABLE `tbl_jabatan` (
  `id_jabatan` varchar(10) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_jabatan`
--

INSERT INTO `tbl_jabatan` (`id_jabatan`, `jabatan`, `created_at`) VALUES
('JBT001', 'Manager', '2026-05-08 10:09:19'),
('JBT002', 'HRD', '2026-05-08 10:09:19'),
('JBT003', 'Staff', '2026-05-08 10:09:19'),
('JBT004', 'Admin', '2026-05-08 10:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pegawai`
--

CREATE TABLE `tbl_pegawai` (
  `id_pegawai` varchar(15) NOT NULL,
  `id_departemen` varchar(10) DEFAULT NULL,
  `id_jabatan` varchar(10) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gaji` decimal(12,2) DEFAULT NULL,
  `status_pernikahan` enum('Menikah','Belum','Berpisah') DEFAULT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') DEFAULT NULL,
  `status_kerja` enum('Tetap','Kontrak','Pensiun','Keluar') DEFAULT NULL,
  `jumlah_cuti` int(11) DEFAULT 0,
  `jenjang_pendidikan` enum('SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3') DEFAULT NULL,
  `tgl_mulai_kerja` date DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pegawai`
--

INSERT INTO `tbl_pegawai` (`id_pegawai`, `id_departemen`, `id_jabatan`, `nama`, `alamat`, `telepon`, `email`, `gaji`, `status_pernikahan`, `jenis_kelamin`, `status_kerja`, `jumlah_cuti`, `jenjang_pendidikan`, `tgl_mulai_kerja`, `foto`, `created_at`) VALUES
('PGW202605002', 'DEP002', 'JBT003', 'jokowoooow', 'asfas', 'afdaf', 'asdfasf@gmail.com', 123123123.00, 'Menikah', 'Laki-Laki', 'Tetap', 11, 'S3', '0000-00-00', NULL, '2026-05-28 07:39:37'),
('PGW202605003', 'DEP001', 'JBT003', 'wowowow', 'keren', '0978978', 'wowo@gmail.com', 12413213.00, 'Menikah', 'Laki-Laki', 'Tetap', 11, 'S2', '2026-05-28', NULL, '2026-05-28 07:45:53'),
('PGW202605004', 'DEP001', 'JBT003', 'ini staff nama lengkap', 'sadadf', '123123', 'staff@gmail.com', 1231123.00, 'Menikah', 'Laki-Laki', 'Tetap', 98, 'S1', '2026-05-28', NULL, '2026-05-28 07:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_penghargaan`
--

CREATE TABLE `tbl_penghargaan` (
  `id_penghargaan` varchar(15) NOT NULL,
  `id_pegawai` varchar(15) DEFAULT NULL,
  `tgl_penghargaan` date DEFAULT NULL,
  `nama_penghargaan` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pengumuman`
--

CREATE TABLE `tbl_pengumuman` (
  `id_pengumuman` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tipe` enum('info','sukses','peringatan','bahaya') DEFAULT 'info',
  `id_user` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_peringatan`
--

CREATE TABLE `tbl_peringatan` (
  `id_peringatan` varchar(15) NOT NULL,
  `id_pegawai` varchar(15) DEFAULT NULL,
  `tgl_peringatan` date DEFAULT NULL,
  `jenis` enum('SP1','SP2','SP3') DEFAULT 'SP1',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_peringatan`
--

INSERT INTO `tbl_peringatan` (`id_peringatan`, `id_pegawai`, `tgl_peringatan`, `jenis`, `keterangan`, `created_at`) VALUES
('SP202605001', 'PGW202605001', '2026-05-08', 'SP1', 'ada', '2026-05-08 10:15:11'),
('SP202605002', 'PGW202605004', '2026-05-28', 'SP2', 'izin mulu', '2026-05-28 08:00:54'),
('SP202605003', 'PGW202605004', '2026-05-28', 'SP1', 'banyak mau', '2026-05-28 08:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_usaha`
--

CREATE TABLE `tbl_usaha` (
  `id_usaha` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `npwp` varchar(30) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `noaccount` varchar(50) DEFAULT NULL,
  `atasnama` varchar(100) DEFAULT NULL,
  `pimpinan` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_usaha`
--

INSERT INTO `tbl_usaha` (`id_usaha`, `nama`, `alamat`, `nomor_telepon`, `fax`, `email`, `npwp`, `bank`, `noaccount`, `atasnama`, `pimpinan`, `created_at`) VALUES
(0, 'PT Maju Sendirian', 'jalanin aja dulu no 10', '0808080808', '123123123', 'ptmajusendiri@gmail.com', '123123123', 'Sendiri', '123456789', 'PT MAJU SENDIRIAN', 'Alucard Feeder, Phd', '2026-05-08 14:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` varchar(15) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','hrd','manager','staff') DEFAULT 'staff',
  `nama` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `password`, `role`, `nama`, `foto`, `created_at`, `last_seen`) VALUES
('ADM001', 'admin1', '$2y$10$NIkGO.LF.nEaefS1yIWxze7yqo7kGhLnh983VRnBmTMY1xO5iGV6m', 'admin', 'admin', '1778219913_2 (1).png', '2026-05-08 05:53:51', '2026-05-28 15:11:36'),
('adm002', 'keren', '$2y$10$PFZco5evMfRdFKxixHBZfOxdosWtqwgvBmkfN0mcRSwWYHp4suu4q', 'admin', 'admin2', '1778232157_2.png', '2026-05-08 09:22:24', '2026-05-21 15:04:08'),
('adm091', 'admin', '$2y$10$gxBj1C4vJcDbnWo1c3VM2.50O4GQu4csN8ZBYIMQ6OBDMkQL9r9QS', 'admin', 'saya admin bang', '1779958519_premium_photo-1769810502887-7703d0a582e1.avif', '2026-05-08 09:41:47', '2026-05-28 15:55:33'),
('BOT', 'bot', 'system_generated', 'admin', 'Virtual Assistant', NULL, '2026-05-21 09:06:10', '2026-05-21 16:06:10'),
('HRD001', 'hrd', '$2y$10$kumznJDprzSp28TZCDdTIelWYpri7NKzFjhQoRl/d1sYH5.n0nynq', 'hrd', 'herade', NULL, '2026-05-08 09:25:44', '2026-05-28 15:53:27'),
('man001', 'manager', '$2y$10$cAGK3Ci4wnZ6fx0iE4jgmOO3HA75m1/n.SLe3YfgrrjspNCV0LHfG', 'manager', 'bang manager', NULL, '2026-05-08 10:38:43', '2026-05-28 14:52:45'),
('PGW202605003', 'wowo', '$2y$10$WxRoHb/Wyl/XDdHsRGxYN.ROBdgBhc5nWpsTMTtF5j7hNQ9/318g2', 'staff', 'wowowow', NULL, '2026-05-28 07:45:54', '2026-05-28 14:45:54'),
('PGW202605004', 'staff', '$2y$10$rvouH19f7IX2Kkb5lv93guCRJ409S10IL0iLKUgCy9WTNldTa37Iu', 'staff', 'ini staff nama lengkap', NULL, '2026-05-28 07:57:10', '2026-05-28 15:59:49'),
('staf01', 'saya staf ini nama saya', '$2y$10$7lihkJAhIxT2NQr.JzeZy.pDEVnCCf47/mNeul5/svyDxLwGEB2ze', 'staff', 'setaf wuoy ini nama display', NULL, '2026-05-08 09:46:31', '2026-05-21 15:04:08'),
('tes001', 'userbaru', '$2y$10$EIPwSpuBfwsx7ge04g3/Re9gPwN6Pm/VNpLDjiILVIntiNHbYVAVu', 'staff', 'userbaru', NULL, '2026-05-21 07:50:46', '2026-05-21 15:04:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_chat`
--
ALTER TABLE `tbl_chat`
  ADD PRIMARY KEY (`id_chat`);

--
-- Indexes for table `tbl_cuti`
--
ALTER TABLE `tbl_cuti`
  ADD PRIMARY KEY (`id_cuti`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `tbl_departemen`
--
ALTER TABLE `tbl_departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indexes for table `tbl_izin`
--
ALTER TABLE `tbl_izin`
  ADD PRIMARY KEY (`id_izin`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `tbl_jabatan`
--
ALTER TABLE `tbl_jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `tbl_pegawai`
--
ALTER TABLE `tbl_pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD KEY `id_departemen` (`id_departemen`,`id_jabatan`);

--
-- Indexes for table `tbl_pengumuman`
--
ALTER TABLE `tbl_pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tbl_peringatan`
--
ALTER TABLE `tbl_peringatan`
  ADD PRIMARY KEY (`id_peringatan`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `tbl_usaha`
--
ALTER TABLE `tbl_usaha`
  ADD PRIMARY KEY (`id_usaha`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `nama_user` (`nama_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_chat`
--
ALTER TABLE `tbl_chat`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `tbl_pengumuman`
--
ALTER TABLE `tbl_pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_pengumuman`
--
ALTER TABLE `tbl_pengumuman`
  ADD CONSTRAINT `tbl_pengumuman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
