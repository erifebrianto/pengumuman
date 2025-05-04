-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 09:44 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengumuman`
--

-- --------------------------------------------------------

--
-- Table structure for table `countdown`
--

CREATE TABLE `countdown` (
  `id` int(11) NOT NULL,
  `waktu_target` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countdown`
--

INSERT INTO `countdown` (`id`, `waktu_target`, `created_at`) VALUES
(7, '2025-05-04 10:07:00', '2025-05-03 22:06:17');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `jurusan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id`, `jurusan`) VALUES
(1, 'Teknik Komputer Dan Jaringan'),
(2, 'Akuntansi dan Keuangan Lembaga');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `nama_mata_pelajaran` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `jurusan_id`, `nama_mata_pelajaran`) VALUES
(16, 1, 'Bahasa Indonesia'),
(17, 1, 'Pendidikan Agama Islam'),
(18, 1, 'Bahasa Indonesia'),
(19, 1, 'Bahasa Inggris'),
(20, 1, 'Matematika'),
(21, 1, 'Simulasi Digital'),
(22, 1, 'Administrasi Infrastruktur Jaringan'),
(23, 1, 'Teknologi Jaringan Berbasis Luas'),
(24, 1, 'Pemrograman Dasar'),
(35, 2, 'Pendidikan Agama Islam'),
(36, 2, 'Bahasa Indonesia'),
(37, 2, 'Bahasa Inggris'),
(38, 2, 'Matematika'),
(39, 2, 'Simulasi Digital'),
(40, 2, 'Ekonomi Bisnis'),
(41, 2, 'Akuntansi Dasar'),
(42, 2, 'Prakarya dan Kewirausahaan'),
(43, 2, 'Komputer Akuntansi'),
(44, 2, 'Aplikasi Pengolah Angka');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_siswa`
--

CREATE TABLE `nilai_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `nilai` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_siswa`
--

INSERT INTO `nilai_siswa` (`id`, `siswa_id`, `mapel_id`, `nilai`) VALUES
(120, 58, 20, '81.00'),
(121, 58, 16, '83.00'),
(122, 58, 17, '79.00'),
(123, 59, 20, '82.00'),
(124, 59, 16, '84.00'),
(125, 59, 17, '80.00'),
(126, 60, 20, '80.00'),
(127, 60, 16, '85.00'),
(128, 60, 17, '81.00'),
(129, 61, 20, '81.00'),
(130, 61, 16, '82.00'),
(131, 61, 17, '82.00'),
(132, 62, 20, '82.00'),
(133, 62, 16, '83.00'),
(134, 62, 17, '78.00'),
(135, 63, 20, '80.00'),
(136, 63, 16, '84.00'),
(137, 63, 17, '79.00'),
(138, 64, 20, '81.00'),
(139, 64, 16, '85.00'),
(140, 64, 17, '80.00'),
(141, 65, 20, '82.00'),
(142, 65, 16, '82.00'),
(143, 65, 17, '81.00'),
(144, 66, 20, '80.00'),
(145, 66, 16, '83.00'),
(146, 66, 17, '82.00'),
(147, 67, 20, '81.00'),
(148, 67, 16, '84.00'),
(149, 67, 17, '78.00');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(255) DEFAULT NULL,
  `alamat_sekolah` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(255) DEFAULT NULL,
  `no_tlp` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `ttd_kepala_sekolah` varchar(255) DEFAULT NULL,
  `nama_kepala_sekolah` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `nisn` varchar(255) DEFAULT NULL,
  `no_ujian` varchar(50) DEFAULT NULL,
  `kelas` varchar(255) DEFAULT NULL,
  `nama_ortu` varchar(255) DEFAULT NULL,
  `rata_rata` decimal(10,0) DEFAULT NULL,
  `status` enum('Lulus','Tidak Lulus') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `nis`, `nisn`, `no_ujian`, `kelas`, `nama_ortu`, `rata_rata`, `status`, `created_at`, `updated_at`) VALUES
(58, 4, 'Siswa 1', 'Kota A', '2001-01-01', '1001', '2001', '0010202502', 'XII TKJ 1', 'Ortu 1', '86', 'Tidak Lulus', '2025-05-04 05:23:21', NULL),
(59, 4, 'Siswa 2', 'Kota A', '2002-01-01', '1002', '2002', '0010202503', 'XII TKJ 1', 'Ortu 2', '87', 'Lulus', '2025-05-04 05:23:21', NULL),
(60, 4, 'Siswa 3', 'Kota A', '2003-01-01', '1003', '2003', '0010202504', 'XII TKJ 1', 'Ortu 3', '88', 'Tidak Lulus', '2025-05-04 05:23:21', NULL),
(61, 4, 'Siswa 4', 'Kota A', '2004-01-01', '1004', '2004', '0010202505', 'XII TKJ 1', 'Ortu 4', '89', 'Lulus', '2025-05-04 05:23:21', NULL),
(62, 4, 'Siswa 5', 'Kota A', '2005-01-01', '1005', '2005', '0010202506', 'XII AKL 1', 'Ortu 5', '85', 'Tidak Lulus', '2025-05-04 05:23:21', NULL),
(63, 4, 'Siswa 6', 'Kota A', '2006-01-01', '1006', '2006', '0010202507', 'XII AKL 1', 'Ortu 6', '86', 'Lulus', '2025-05-04 05:23:21', NULL),
(64, 4, 'Siswa 7', 'Kota A', '2007-01-01', '1007', '2007', '0010202508', 'XII AKL 1', 'Ortu 7', '87', 'Tidak Lulus', '2025-05-04 05:23:21', NULL),
(65, 4, 'Siswa 8', 'Kota A', '2008-01-01', '1008', '2008', '0010202509', 'XII OTKP 1', 'Ortu 8', '88', 'Lulus', '2025-05-04 05:23:21', NULL),
(66, 4, 'Siswa 9', 'Kota A', '2009-01-01', '1009', '2009', '0010202510', 'XII OTKP 1', 'Ortu 9', '89', 'Tidak Lulus', '2025-05-04 05:23:21', NULL),
(67, 4, 'Siswa 10', 'Kota A', '0000-00-00', '10010', '20010', '0010202511', 'XII OTKP 1', 'Ortu 10', '85', 'Lulus', '2025-05-04 05:23:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(4, 'admin', '', '$2y$10$sYOAcjFUA1CFvcK.o6sPF.sLqCS18REKt6TPAR18pKlMYRnJfipBu', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countdown`
--
ALTER TABLE `countdown`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jurusan_id` (`jurusan_id`);

--
-- Indexes for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `mapel_id` (`mapel_id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countdown`
--
ALTER TABLE `countdown`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD CONSTRAINT `fk_jurusan_id` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD CONSTRAINT `nilai_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_siswa_ibfk_2` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
