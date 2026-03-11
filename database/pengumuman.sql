-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 11 Mar 2026 pada 09.02
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
-- Database: `pengumuman`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `batch_generation`
--

CREATE TABLE `batch_generation` (
  `id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'idle',
  `progress` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `countdown`
--

CREATE TABLE `countdown` (
  `id` int(11) NOT NULL,
  `waktu_target` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `countdown`
--

INSERT INTO `countdown` (`id`, `waktu_target`, `created_at`) VALUES
(7, '2025-12-10 18:35:00', '2026-02-25 06:40:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `jurusan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id`, `jurusan`) VALUES
(1, 'Teknik Komputer Dan Jaringan'),
(2, 'Akuntansi dan Keuangan Lembaga');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `nama_mata_pelajaran` varchar(100) DEFAULT NULL,
  `kode_mapel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `jurusan_id`, `nama_mata_pelajaran`, `kode_mapel`) VALUES
(16, 1, 'Bahasa Indonesia', NULL),
(17, 1, 'Pendidikan Agama Islam', NULL),
(18, 1, 'Bahasa Indonesia', NULL),
(19, 1, 'Bahasa Inggris', NULL),
(20, 1, 'Matematika', NULL),
(21, 1, 'Simulasi Digital', NULL),
(22, 1, 'Administrasi Infrastruktur Jaringan', NULL),
(23, 1, 'Teknologi Jaringan Berbasis Luas', NULL),
(24, 1, 'Pemrograman Dasar', NULL),
(35, 2, 'Pendidikan Agama Islam', NULL),
(36, 2, 'Bahasa Indonesia', NULL),
(37, 2, 'Bahasa Inggris', NULL),
(38, 2, 'Matematika', NULL),
(39, 2, 'Simulasi Digital', NULL),
(40, 2, 'Ekonomi Bisnis', NULL),
(41, 2, 'Akuntansi Dasar', NULL),
(42, 2, 'Prakarya dan Kewirausahaan', NULL),
(43, 2, 'Komputer Akuntansi', NULL),
(44, 2, 'Aplikasi Pengolah Angka', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_siswa`
--

CREATE TABLE `nilai_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `nilai` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(255) DEFAULT NULL,
  `mode_pengumuman` enum('nilai','status') NOT NULL DEFAULT 'nilai',
  `alamat_sekolah` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(255) DEFAULT NULL,
  `no_tlp` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `ttd_kepala_sekolah` varchar(255) DEFAULT NULL,
  `nama_kepala_sekolah` varchar(255) DEFAULT NULL,
  `wablas_domain` varchar(255) DEFAULT 'https://tegal.wablas.com',
  `wablas_token` varchar(255) DEFAULT '',
  `wablas_template_lulus` text DEFAULT NULL,
  `wablas_template_gagal` text DEFAULT NULL,
  `wablas_status` tinyint(1) DEFAULT 0,
  `wa_batch_limit` int(11) DEFAULT 10,
  `wa_delay_min` int(11) DEFAULT 3,
  `wa_delay_max` int(11) DEFAULT 6,
  `verification_method` enum('nisn','nisn_dob','exam_number','exam_number_dob','exam_number_nis') DEFAULT 'exam_number_nis'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_sekolah`, `mode_pengumuman`, `alamat_sekolah`, `email`, `kode_pos`, `no_tlp`, `website`, `logo_sekolah`, `background`, `ttd_kepala_sekolah`, `nama_kepala_sekolah`, `wablas_domain`, `wablas_token`, `wablas_template_lulus`, `wablas_template_gagal`, `wablas_status`, `wa_batch_limit`, `wa_delay_min`, `wa_delay_max`, `verification_method`) VALUES
(1, 'SMK NEGERI 1', 'status', 'Alamat Sekolah', 'email@sekolah.com', '12345', '082221518789', 'https://sekolah.com', 'uploads/logo_sekolah.png', 'uploads/background_skl.jpg', '', 'Kepala Sekolah', 'https://solo.wablas.com', '6o5DMzJbEfVaXcEeBigL1A91WoxjRfrnXxPEFSwvKHUYzQHT8wFIWS9', NULL, NULL, 1, 10, 5, 10, 'exam_number');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `nisn` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `no_ujian` varchar(50) DEFAULT NULL,
  `kelas` varchar(255) DEFAULT NULL,
  `nama_ortu` varchar(255) DEFAULT NULL,
  `rata_rata` decimal(10,0) DEFAULT NULL,
  `status` enum('Lulus','Tidak Lulus') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `token_download` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `jurusan_id`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `nis`, `nisn`, `no_hp`, `no_ujian`, `kelas`, `nama_ortu`, `rata_rata`, `status`, `created_at`, `updated_at`, `token_download`) VALUES
(897, 4, NULL, 'AKMAL TRIO SAPUTRO', NULL, NULL, '14118', '0064019145', '081335614467', '2025-0309-401', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, '1e4d1b5db4f55d10e7779246a4f6ff58'),
(898, 4, NULL, 'AL \'DHINO KURNIA RAMADHAN', NULL, NULL, '14119', '0066091303', '085700004299', '2025-0309-402', 'XII TKR-4', NULL, NULL, 'Tidak Lulus', '2026-02-25 04:43:44', NULL, '24fb55b702fb7e78b766d1373b6ee20b'),
(899, 4, NULL, 'ANDI PRATAMA', NULL, NULL, '14120', '0071004998', NULL, '2025-0309-403', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(900, 4, NULL, 'ANDRIAN FIRMANSYAH', NULL, NULL, '14121', '0076791125', NULL, '2025-0309-404', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(901, 4, NULL, 'ANGGIT IKA KURNIAWAN', NULL, NULL, '14122', '0067963807', NULL, '2025-0309-405', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(902, 4, NULL, 'ARI SAWON SURYANA', NULL, NULL, '14123', '0075421383', NULL, '2025-0309-406', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(903, 4, NULL, 'ARMAN YOGA RAKSA', NULL, NULL, '14124', '0087734507', NULL, '2025-0309-407', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(904, 4, NULL, 'BRAM KASIMANDIKA', NULL, NULL, '14125', '0082293301', NULL, '2025-0309-408', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(905, 4, NULL, 'CATUR PRAYITNO', NULL, NULL, '14126', '0089617443', NULL, '2025-0309-409', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(906, 4, NULL, 'DEMA ALAN SAPUTRA', NULL, NULL, '14127', '0068967254', NULL, '2025-0309-410', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(907, 4, NULL, 'ERJI BAGAS ARYA PRATAMA', NULL, NULL, '14128', '0074733704', NULL, '2025-0309-411', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(908, 4, NULL, 'EVAN DANELLA', NULL, NULL, '14129', '0079009433', NULL, '2025-0309-412', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(909, 4, NULL, 'EXCEL ARYANDA VALLENTINO', NULL, NULL, '14130', '0064423853', NULL, '2025-0309-413', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(910, 4, NULL, 'FAHRUS ALI MUNTAZA', NULL, NULL, '14131', '0061909641', NULL, '2025-0309-414', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(911, 4, NULL, 'FAJAR FATHUR RAHMAN', NULL, NULL, '14132', '0078240705', NULL, '2025-0309-415', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(912, 4, NULL, 'FARHAN AJI PANGESTU', NULL, NULL, '14133', '0066046480', NULL, '2025-0309-416', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(913, 4, NULL, 'GIBRAN NOVAMRULLOH', NULL, NULL, '14134', '0074391957', NULL, '2025-0309-417', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(914, 4, NULL, 'GILANG FEBRIANSYAH', NULL, NULL, '14135', '0068023286', NULL, '2025-0309-418', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(915, 4, NULL, 'GILANG RESTU ADI', NULL, NULL, '14136', '0079389946', NULL, '2025-0309-419', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(916, 4, NULL, 'IYAN SIDIK ANGGA MAULANA', NULL, NULL, '14137', '0083391518', NULL, '2025-0309-420', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(917, 4, NULL, 'MUHAMMAD RIKY RIVALDI', NULL, NULL, '14139', '0046801082', NULL, '2025-0309-421', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(918, 4, NULL, 'MUJIATUL SUGENG', NULL, NULL, '14140', '0047217523', NULL, '2025-0309-422', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(919, 4, NULL, 'NAKAYA OWEN ANGER DAIRLIARDI', NULL, NULL, '14141', '0062177592', NULL, '2025-0309-423', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(920, 4, NULL, 'NOVAN YUDI SETIAWAN', NULL, NULL, '14142', '0078127217', NULL, '2025-0309-424', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(921, 4, NULL, 'PANGGIH PANGESTU', NULL, NULL, '14143', '0071986415', NULL, '2025-0309-425', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(922, 4, NULL, 'RAGIL PANCA RAHMAT', NULL, NULL, '14144', '0067684867', NULL, '2025-0309-426', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(923, 4, NULL, 'RIZKO LINTANG MUKTI', NULL, NULL, '14145', '0052954909', NULL, '2025-0309-427', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(924, 4, NULL, 'VIAN ANDHIKA PRATAMA', NULL, NULL, '14146', '0077319563', NULL, '2025-0309-428', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL),
(925, 4, NULL, 'WANGUN SETIANING AJI', NULL, NULL, '14147', '0077298852', NULL, '2025-0309-429', 'XII TKR-4', NULL, NULL, 'Lulus', '2026-02-25 04:43:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(4, 'admin', '', '$2y$10$sYOAcjFUA1CFvcK.o6sPF.sLqCS18REKt6TPAR18pKlMYRnJfipBu', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `whatsapp_queue`
--

CREATE TABLE `whatsapp_queue` (
  `id` int(11) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `api_response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL,
  `retry_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `whatsapp_queue`
--

INSERT INTO `whatsapp_queue` (`id`, `nis`, `no_hp`, `pesan`, `status`, `api_response`, `created_at`, `sent_at`, `retry_count`) VALUES
(1, '14118', '081335614467', '???? *PENGUMUMAN RESMI SEKOLAH* ????\n\nHalo Bapak/Ibu Wali Murid & Ananda *AKMAL TRIO SAPUTRO*.\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *LULUS* ✅.\nSilakan unduh SKL resmi pada tautan berikut: http://localhost/pengumuman/skl/download_skl_wa/1e4d1b5db4f55d10e7779246a4f6ff58', 'sent', '{\"status\":true,\"message\":\"Message is pending and waiting to be processed\",\"data\":{\"device_id\":\"OMM1F9\",\"quota\":999,\"messages\":[{\"id\":\"f5b3cb53-b926-472d-9cb3-594b7f535f7b\",\"phone\":6281335614467,\"message\":\"???? *PENGUMUMAN RESMI SEKOLAH* ????\\n\\nHalo Bapak\\/Ibu Wali Murid & Ananda *AKMAL TRIO SAPUTRO*.\\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *LULUS* \\u2705.\\nSilakan unduh SKL resmi pada tautan berikut: http:\\/\\/localhost\\/pengumuman\\/skl\\/download_skl_wa\\/1e4d1b5db4f55d10e7779246a4f6ff58\",\"status\":\"pending\",\"ref_id\":\"\"}]}}', '2026-03-11 14:39:04', '2026-03-11 14:41:13', 0),
(2, '14119', '085700004299', '???? *PENGUMUMAN RESMI SEKOLAH* ????\n\nHalo Bapak/Ibu Wali Murid & Ananda *AL \'DHINO KURNIA RAMADHAN*.\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *TIDAK LULUS* ❌.\nTetap Semangat! Unduh Keterangan hasil ujian pada tautan berikut: http://localhost/pengumuman/skl/download_skl_wa/24fb55b702fb7e78b766d1373b6ee20b', 'sent', '{\"status\":true,\"message\":\"Message is pending and waiting to be processed\",\"data\":{\"device_id\":\"OMM1F9\",\"quota\":998,\"messages\":[{\"id\":\"2c88ddcf-5199-4808-bfa4-4ddbc1176f0b\",\"phone\":6285700004299,\"message\":\"\\ud83d\\udea8 *PENGUMUMAN RESMI SEKOLAH* \\ud83d\\udea8\\n\\nHalo Bapak\\/Ibu Wali Murid & Ananda *AL \'DHINO KURNIA RAMADHAN*.\\nBerdasarkan Rapat Pleno Dewan Guru, siswa dinyatakan: *TIDAK LULUS* \\u274c.\\nTetap Semangat! Unduh Keterangan hasil ujian pada tautan berikut: http:\\/\\/localhost\\/pengumuman\\/skl\\/download_skl_wa\\/24fb55b702fb7e78b766d1373b6ee20b\",\"status\":\"pending\",\"ref_id\":\"\"}]}}', '2026-03-11 14:43:58', '2026-03-11 14:43:58', 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `batch_generation`
--
ALTER TABLE `batch_generation`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `countdown`
--
ALTER TABLE `countdown`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jurusan_id` (`jurusan_id`);

--
-- Indeks untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `mapel_id` (`mapel_id`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `jurusan_id` (`jurusan_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `whatsapp_queue`
--
ALTER TABLE `whatsapp_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `nis` (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `batch_generation`
--
ALTER TABLE `batch_generation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `countdown`
--
ALTER TABLE `countdown`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=926;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `whatsapp_queue`
--
ALTER TABLE `whatsapp_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD CONSTRAINT `fk_jurusan_id` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD CONSTRAINT `nilai_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_siswa_ibfk_2` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
