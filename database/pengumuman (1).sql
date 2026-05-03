-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 03 Bulan Mei 2026 pada 12.39
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.17

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
(7, '2026-05-01 16:35:00', '2026-05-02 02:44:15');

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
(58, NULL, 'Pendidikan Agama dan Budi Pekerti', NULL),
(59, NULL, 'Pendidikan Pancasila', NULL),
(60, NULL, 'Bahasa Indonesia', NULL),
(61, NULL, 'Pendidikan Jasmani, Olahraga dan Kesehatan', NULL),
(62, NULL, 'Sejarah', NULL),
(63, NULL, 'Seni Budaya', NULL),
(64, NULL, 'Matematika', NULL),
(65, NULL, 'Bahasa Inggris', NULL),
(66, NULL, 'Informatika', NULL),
(67, NULL, 'Projek Ilmu Pengetahuan Alam dan Sosial', NULL),
(68, NULL, 'Dasar-dasar Program Keahlian', NULL),
(69, NULL, 'Konsentrasi Keahlian', NULL),
(70, NULL, 'Kreativitas, Inovasi, dan Kewirausahaan', NULL),
(71, NULL, 'Praktik Kerja Lapangan', NULL),
(72, NULL, 'Mata Pelajaran Pilihan', NULL),
(73, NULL, 'Bahasa Jawa', NULL);

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

--
-- Dumping data untuk tabel `nilai_siswa`
--

INSERT INTO `nilai_siswa` (`id`, `siswa_id`, `mapel_id`, `nilai`) VALUES
(372, 1034, 58, 86.00),
(373, 1034, 59, 85.00),
(374, 1034, 60, 84.00),
(375, 1034, 61, 83.00),
(376, 1034, 62, 82.00),
(377, 1034, 63, 81.00),
(378, 1034, 64, 80.00),
(379, 1034, 65, 84.00),
(380, 1034, 66, 85.00),
(381, 1034, 67, 83.00),
(382, 1034, 68, 84.00),
(383, 1034, 69, 85.00),
(384, 1034, 70, 86.00),
(385, 1034, 71, 87.00),
(386, 1034, 72, 85.00),
(387, 1034, 73, 83.00),
(388, 1035, 58, 90.00),
(389, 1035, 59, 89.00),
(390, 1035, 60, 88.00),
(391, 1035, 61, 87.00),
(392, 1035, 62, 86.00),
(393, 1035, 63, 85.00),
(394, 1035, 64, 84.00),
(395, 1035, 65, 88.00),
(396, 1035, 66, 89.00),
(397, 1035, 67, 87.00),
(398, 1035, 68, 88.00),
(399, 1035, 69, 89.00),
(400, 1035, 70, 90.00),
(401, 1035, 71, 91.00),
(402, 1035, 72, 89.00),
(403, 1035, 73, 87.00),
(404, 1036, 58, 84.00),
(405, 1036, 59, 83.00),
(406, 1036, 60, 82.00),
(407, 1036, 61, 85.00),
(408, 1036, 62, 81.00),
(409, 1036, 63, 80.00),
(410, 1036, 64, 79.00),
(411, 1036, 65, 83.00),
(412, 1036, 66, 84.00),
(413, 1036, 67, 82.00),
(414, 1036, 68, 83.00),
(415, 1036, 69, 84.00),
(416, 1036, 70, 85.00),
(417, 1036, 71, 86.00),
(418, 1036, 72, 84.00),
(419, 1036, 73, 82.00),
(420, 1037, 58, 88.00),
(421, 1037, 59, 87.00),
(422, 1037, 60, 86.00),
(423, 1037, 61, 85.00),
(424, 1037, 62, 84.00),
(425, 1037, 63, 83.00),
(426, 1037, 64, 82.00),
(427, 1037, 65, 86.00),
(428, 1037, 66, 87.00),
(429, 1037, 67, 85.00),
(430, 1037, 68, 86.00),
(431, 1037, 69, 87.00),
(432, 1037, 70, 88.00),
(433, 1037, 71, 89.00),
(434, 1037, 72, 87.00),
(435, 1037, 73, 85.00),
(436, 1038, 58, 87.00),
(437, 1038, 59, 86.00),
(438, 1038, 60, 85.00),
(439, 1038, 61, 84.00),
(440, 1038, 62, 83.00),
(441, 1038, 63, 82.00),
(442, 1038, 64, 81.00),
(443, 1038, 65, 85.00),
(444, 1038, 66, 86.00),
(445, 1038, 67, 84.00),
(446, 1038, 68, 85.00),
(447, 1038, 69, 86.00),
(448, 1038, 70, 87.00),
(449, 1038, 71, 88.00),
(450, 1038, 72, 86.00),
(451, 1038, 73, 84.00),
(452, 1039, 58, 85.00),
(453, 1039, 59, 84.00),
(454, 1039, 60, 83.00),
(455, 1039, 61, 86.00),
(456, 1039, 62, 82.00),
(457, 1039, 63, 81.00),
(458, 1039, 64, 80.00),
(459, 1039, 65, 84.00),
(460, 1039, 66, 85.00),
(461, 1039, 67, 83.00),
(462, 1039, 68, 84.00),
(463, 1039, 69, 85.00),
(464, 1039, 70, 86.00),
(465, 1039, 71, 87.00),
(466, 1039, 72, 85.00),
(467, 1039, 73, 83.00),
(468, 1040, 58, 86.00),
(469, 1040, 59, 85.00),
(470, 1040, 60, 84.00),
(471, 1040, 61, 83.00),
(472, 1040, 62, 82.00),
(473, 1040, 63, 81.00),
(474, 1040, 64, 80.00),
(475, 1040, 65, 84.00),
(476, 1040, 66, 85.00),
(477, 1040, 67, 83.00),
(478, 1040, 68, 84.00),
(479, 1040, 69, 85.00),
(480, 1040, 70, 86.00),
(481, 1040, 71, 87.00),
(482, 1040, 72, 85.00),
(483, 1040, 73, 83.00),
(484, 1041, 58, 89.00),
(485, 1041, 59, 88.00),
(486, 1041, 60, 87.00),
(487, 1041, 61, 86.00),
(488, 1041, 62, 85.00),
(489, 1041, 63, 84.00),
(490, 1041, 64, 83.00),
(491, 1041, 65, 87.00),
(492, 1041, 66, 88.00),
(493, 1041, 67, 86.00),
(494, 1041, 68, 87.00),
(495, 1041, 69, 88.00),
(496, 1041, 70, 89.00),
(497, 1041, 71, 90.00),
(498, 1041, 72, 88.00),
(499, 1041, 73, 86.00),
(500, 1042, 58, 83.00),
(501, 1042, 59, 82.00),
(502, 1042, 60, 81.00),
(503, 1042, 61, 84.00),
(504, 1042, 62, 80.00),
(505, 1042, 63, 79.00),
(506, 1042, 64, 78.00),
(507, 1042, 65, 82.00),
(508, 1042, 66, 83.00),
(509, 1042, 67, 81.00),
(510, 1042, 68, 82.00),
(511, 1042, 69, 83.00),
(512, 1042, 70, 84.00),
(513, 1042, 71, 85.00),
(514, 1042, 72, 83.00),
(515, 1042, 73, 81.00),
(516, 1043, 58, 88.00),
(517, 1043, 59, 87.00),
(518, 1043, 60, 86.00),
(519, 1043, 61, 85.00),
(520, 1043, 62, 84.00),
(521, 1043, 63, 83.00),
(522, 1043, 64, 82.00),
(523, 1043, 65, 86.00),
(524, 1043, 66, 87.00),
(525, 1043, 67, 85.00),
(526, 1043, 68, 86.00),
(527, 1043, 69, 87.00),
(528, 1043, 70, 88.00),
(529, 1043, 71, 89.00),
(530, 1043, 72, 87.00),
(531, 1043, 73, 85.00);

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
  `verification_method` varchar(255) DEFAULT 'exam_number_nis'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_sekolah`, `mode_pengumuman`, `alamat_sekolah`, `email`, `kode_pos`, `no_tlp`, `website`, `logo_sekolah`, `background`, `ttd_kepala_sekolah`, `nama_kepala_sekolah`, `wablas_domain`, `wablas_token`, `wablas_template_lulus`, `wablas_template_gagal`, `wablas_status`, `wa_batch_limit`, `wa_delay_min`, `wa_delay_max`, `verification_method`) VALUES
(1, 'SMK IT MA\'ARIF NU KARANGLEWAS', 'status', 'Jl. Desa Babakan RT 001 RW 001, Kecamatan Karanglewas, Kabupaten Banyumas Babakan, Kec. Karanglewas Kab. Banyumas, Prov. Jawa Tengah Kode Pos: 53161', 'smkitmaarifnukaranglewas@gmail.com', '53161', '082225587585', 'smkitmanukaranglewas.sch.id', 'uploads/logo_sekolah_1777799802.png', 'uploads/background_skl_1777656103.jpg', '', 'Nanag Yanuar, S.Pd.', 'https://solo.wablas.com', '6o5DMzJbEfVaXcEeBigL1A91WoxjRfrnXxPEFSwvKHUYzQHT8wFIWS9', NULL, NULL, 1, 10, 5, 10, 'nisn');

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
  `token_download` varchar(64) DEFAULT NULL,
  `kurikulum` varchar(255) DEFAULT NULL,
  `program_keahlian` varchar(255) DEFAULT NULL,
  `konsentrasi_keahlian` varchar(255) DEFAULT NULL,
  `tanggal_kelulusan` varchar(255) DEFAULT NULL,
  `no_ijazah` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `jurusan_id`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `nis`, `nisn`, `no_hp`, `no_ujian`, `kelas`, `nama_ortu`, `rata_rata`, `status`, `created_at`, `updated_at`, `token_download`, `kurikulum`, `program_keahlian`, `konsentrasi_keahlian`, `tanggal_kelulusan`, `no_ijazah`) VALUES
(1034, 4, NULL, 'AGUS SETIAWAN', 'CIREBON', '2007-01-10', '12352', '11223351', NULL, '2025-0309-108', 'XII BDP-1', NULL, 84, 'Lulus', '2026-05-03 17:19:28', NULL, '918f16cefc8ba7845cadd087afa28bb0', 'Kurikulum Merdeka', 'Bisnis Daring dan Pemasaran', 'Bisnis Ritel', '2026-05-04', 'DN-03/M-SMK/24/0000008'),
(1035, 4, NULL, 'DEWI LESTARI', 'BEKASI', '2006-10-21', '12353', '11223352', NULL, '2025-0309-109', 'XII TKJ-1', NULL, 88, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Komputer dan Jaringan', 'Teknik Komputer Jaringan', '2026-05-04', 'DN-03/M-SMK/24/0000009'),
(1036, 4, NULL, 'RIZKY MAULANA', 'TASIKMALAYA', '2007-03-05', '12354', '11223353', NULL, '2025-0309-110', 'XII TBSM-1', NULL, 83, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Otomotif', 'Teknik Sepeda Motor', '2026-05-04', 'DN-03/M-SMK/24/0000010'),
(1037, 4, NULL, 'FITRIANI PUTRI', 'BOGOR', '2006-12-12', '12355', '11223354', NULL, '2025-0309-111', 'XII BDP-2', NULL, 86, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Bisnis Daring dan Pemasaran', 'Bisnis Ritel', '2026-05-04', 'DN-03/M-SMK/24/0000011'),
(1038, 4, NULL, 'AHMAD FAUZI', 'DEPOK', '2007-02-18', '12356', '11223355', NULL, '2025-0309-112', 'XII TKJ-2', NULL, 85, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Komputer dan Jaringan', 'Teknik Komputer Jaringan', '2026-05-04', 'DN-03/M-SMK/24/0000012'),
(1039, 4, NULL, 'YULIANA SARI', 'KARAWANG', '2006-11-30', '12357', '11223356', NULL, '2025-0309-113', 'XII TBSM-2', NULL, 84, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Otomotif', 'Teknik Sepeda Motor', '2026-05-04', 'DN-03/M-SMK/24/0000013'),
(1040, 4, NULL, 'DANI SAPUTRA', 'SUBANG', '2007-04-02', '12358', '11223357', NULL, '2025-0309-114', 'XII BDP-3', NULL, 84, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Bisnis Daring dan Pemasaran', 'Bisnis Ritel', '2026-05-04', 'DN-03/M-SMK/24/0000014'),
(1041, 4, NULL, 'SRI WAHYUNI', 'INDRAMAYU', '2006-09-14', '12359', '11223358', NULL, '2025-0309-115', 'XII TKJ-3', NULL, 87, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Komputer dan Jaringan', 'Teknik Komputer Jaringan', '2026-05-04', 'DN-03/M-SMK/24/0000015'),
(1042, 4, NULL, 'BAMBANG HARIYANTO', 'PURWAKARTA', '2007-01-25', '12360', '11223359', NULL, '2025-0309-116', 'XII TBSM-3', NULL, 82, 'Lulus', '2026-05-03 17:19:28', NULL, NULL, 'Kurikulum Merdeka', 'Teknik Otomotif', 'Teknik Sepeda Motor', '2026-05-04', 'DN-03/M-SMK/24/0000016'),
(1043, 4, NULL, 'LINA APRILIA', 'SUKABUMI', '2006-08-19', '12361', '11223360', NULL, '2025-0309-117', 'XII BDP-4', NULL, 86, 'Lulus', '2026-05-03 17:19:29', NULL, NULL, 'Kurikulum Merdeka', 'Bisnis Daring dan Pemasaran', 'Bisnis Ritel', '2026-05-04', 'DN-03/M-SMK/24/0000017');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT untuk tabel `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=532;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1044;

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
