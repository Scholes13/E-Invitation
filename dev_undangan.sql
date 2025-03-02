-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 27, 2024 at 09:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_undangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_event` bigint UNSIGNED NOT NULL,
  `code_event` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name_event` text COLLATE utf8mb4_general_ci NOT NULL,
  `type_event` text COLLATE utf8mb4_general_ci NOT NULL,
  `place_event` text COLLATE utf8mb4_general_ci NOT NULL,
  `location_event` text COLLATE utf8mb4_general_ci NOT NULL,
  `start_event` timestamp NOT NULL,
  `end_event` timestamp NOT NULL,
  `information_event` text COLLATE utf8mb4_general_ci,
  `image_event` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_top_status` tinyint(1) NOT NULL DEFAULT '1',
  `color_text_event` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color_bg_event` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_bg_event` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_bg_status` tinyint(1) NOT NULL DEFAULT '1',
  `image_left_event` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_left_status` tinyint(1) NOT NULL DEFAULT '0',
  `image_right_event` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_right_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_event`, `code_event`, `name_event`, `type_event`, `place_event`, `location_event`, `start_event`, `end_event`, `information_event`, `image_event`, `created_at`, `updated_at`, `image_top_status`, `color_text_event`, `color_bg_event`, `image_bg_event`, `image_bg_status`, `image_left_event`, `image_left_status`, `image_right_event`, `image_right_status`) VALUES
(1, 'AItX14', 'Ruby Json & Vue Perl', 'Undangan Pernikahan', 'Gedung Serba Guna', 'Jl. Pemuda No. 1, Pati, Jateng', '2026-08-10 02:00:00', '2026-08-10 14:00:00', 'Terimakasih banyak atas kehadirannya :)', NULL, '2024-12-27 06:44:21', '2024-12-27 09:25:36', 1, '#3f4040', '#66f5c5', NULL, 0, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `id_guest` bigint UNSIGNED NOT NULL,
  `name_guest` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `address_guest` text COLLATE utf8mb4_general_ci,
  `information_guest` text COLLATE utf8mb4_general_ci,
  `email_guest` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_guest` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nik_guest` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by_guest` enum('admin','register') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`id_guest`, `name_guest`, `address_guest`, `information_guest`, `email_guest`, `phone_guest`, `created_at`, `updated_at`, `nik_guest`, `created_by_guest`) VALUES
(1, 'Fitria Kuswandari', 'Kpg. Rumah Sakit No. 985, Salatiga 59402, Jatim', 'Karyawan BUMN', 'gunarto.rahmat@gmail.com', '(+62) 983 6718 2008', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(2, 'Vicky Rahayu', 'Ds. Flora No. 585, Jambi 90872, Kepri', 'Perawat', 'salimah.astuti@gmail.com', '(+62) 207 8998 022', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(3, 'Caraka Prasetyo', 'Ds. Tangkuban Perahu No. 272, Tebing Tinggi 22663, Lampung', 'Tukang Batu', 'juli81@yahoo.co.id', '(+62) 841 987 779', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(4, 'Wasis Reksa Napitupulu S.H.', 'Jln. Jayawijaya No. 383, Palu 68418, Malut', 'Penulis', 'muni.rajasa@gmail.co.id', '0849 8312 638', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(5, 'Harimurti Marpaung S.Sos', 'Ki. Zamrud No. 671, Administrasi Jakarta Pusat 90558, Jambi', 'Karyawan Honorer', 'jgunawan@gmail.co.id', '(+62) 735 8443 3469', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(6, 'Michelle Laksita M.TI.', 'Jln. Ekonomi No. 646, Kendari 98098, Sultra', 'Konsultan', 'ilyas46@mandasari.org', '(+62) 239 4233 249', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(7, 'Ghaliyati Nabila Wahyuni', 'Jln. Kiaracondong No. 395, Palangka Raya 53305, Sumut', 'Perangkat Desa', 'laksita.diah@halim.id', '(+62) 775 4691 970', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(8, 'Yosef Cakrabuana Prasetya S.Gz', 'Kpg. Jakarta No. 528, Magelang 98000, Sulut', 'Karyawan Honorer', 'emong.zulaika@yahoo.com', '(+62) 551 8917 6396', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(9, 'Putri Lidya Anggraini S.Psi', 'Dk. Bagis Utama No. 756, Tangerang 55593, Banten', 'Sopir', 'candra.mardhiyah@yahoo.com', '0433 5621 151', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(10, 'Zalindra Gabriella Usamah S.H.', 'Jr. Jamika No. 144, Binjai 40437, Sumsel', 'Tentara Nasional Indonesia (TNI)', 'wrajasa@handayani.go.id', '(+62) 27 8997 863', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(11, 'Almira Lestari', 'Ki. Monginsidi No. 868, Pasuruan 20341, Malut', 'Kondektur', 'kani.mangunsong@andriani.net', '(+62) 367 8737 4231', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(12, 'Eman Dabukke', 'Kpg. Bah Jaya No. 166, Jambi 82183, Kalsel', 'Pembantu Rumah Tangga', 'mitra.oktaviani@gmail.com', '0848 2227 6936', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(13, 'Indah Nurdiyanti', 'Jln. Gardujati No. 386, Blitar 85378, NTB', 'Pemandu Wisata', 'fhutapea@yahoo.com', '0863 224 619', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(14, 'Ajeng Almira Lailasari S.Farm', 'Ki. Gambang No. 412, Administrasi Jakarta Pusat 85200, Jabar', 'Kepala Desa', 'infoku96@gmail.com', '+62812-2576-4094', '2024-12-27 06:44:27', '2024-12-27 07:03:16', NULL, 'admin'),
(15, 'Mahdi Salahudin S.I.Kom', 'Kpg. Pattimura No. 514, Tual 35813, Babel', 'Karyawan BUMN', 'nurdiyanti.devi@fujiati.web.id', '0658 2435 102', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(16, 'Raden Sitorus S.T.', 'Psr. Abdullah No. 975, Padang 93357, Sulbar', 'Desainer', 'laksmiwati.nabila@adriansyah.biz.id', '(+62) 857 5281 3397', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(17, 'Lidya Agustina', 'Kpg. Gading No. 292, Tangerang 27704, Jateng', 'Karyawan Honorer', 'emas.aryani@yahoo.com', '(+62) 878 030 000', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(18, 'Tantri Nasyidah', 'Dk. Gremet No. 42, Ambon 32028, Kaltim', 'Pendeta', 'ikusumo@pertiwi.tv', '0365 0757 8140', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(19, 'Lamar Samosir S.Psi', 'Gg. Ciumbuleuit No. 696, Tual 85455, Sulsel', 'Presiden', 'simon.habibi@wulandari.web.id', '(+62) 699 3119 6447', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin'),
(20, 'Dimas Tarihoran', 'Dk. Moch. Toha No. 679, Tangerang 60674, Kaltara', 'Apoteker', 'xuyainah@siregar.mil.id', '0468 8054 1933', '2024-12-27 06:44:27', '2024-12-27 06:44:27', NULL, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `invitation`
--

CREATE TABLE `invitation` (
  `id_invitation` bigint UNSIGNED NOT NULL,
  `id_guest` bigint UNSIGNED NOT NULL,
  `qrcode_invitation` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `table_number_invitation` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_invitation` enum('reguler','vip') COLLATE utf8mb4_general_ci NOT NULL,
  `information_invitation` text COLLATE utf8mb4_general_ci,
  `link_invitation` text COLLATE utf8mb4_general_ci,
  `image_qrcode_invitation` text COLLATE utf8mb4_general_ci,
  `send_email_invitation` tinyint(1) NOT NULL DEFAULT '0',
  `checkin_img_invitation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `checkout_img_invitation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `checkin_invitation` timestamp NULL DEFAULT NULL,
  `checkout_invitation` timestamp NULL DEFAULT NULL,
  `id_user` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_event` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation`
--

INSERT INTO `invitation` (`id_invitation`, `id_guest`, `qrcode_invitation`, `table_number_invitation`, `type_invitation`, `information_invitation`, `link_invitation`, `image_qrcode_invitation`, `send_email_invitation`, `checkin_img_invitation`, `checkout_img_invitation`, `checkin_invitation`, `checkout_invitation`, `id_user`, `created_at`, `updated_at`, `id_event`) VALUES
(1, 14, 'Rr2JbO', '1A', 'reguler', 'Cluster 1', '/invitation/Rr2JbO', '/img/qrCode/Rr2JbO.png', 1, NULL, NULL, '2024-12-27 09:10:37', '2024-12-27 09:16:31', NULL, '2024-12-27 06:44:39', '2024-12-27 09:16:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2023_11_02_133938_create_guest_table', 1),
(4, '2023_11_02_134436_create_invitation_table', 1),
(5, '2023_11_09_190758_create_event_table', 1),
(6, '2023_12_08_212138_update_guest_table', 1),
(7, '2024_06_01_124847_add_foreignkey_invitation_table', 1),
(8, '2024_11_22_141543_update_event_table', 1),
(9, '2024_11_22_184755_create_setting_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_general_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` bigint UNSIGNED NOT NULL,
  `name_app` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `logo_app` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color_bg_app` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_bg_app` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_bg_status` tinyint(1) NOT NULL DEFAULT '1',
  `send_email` tinyint(1) DEFAULT '0',
  `send_whatsapp` tinyint(1) DEFAULT '0',
  `greeting_page` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `name_app`, `logo_app`, `color_bg_app`, `image_bg_app`, `image_bg_status`, `send_email`, `send_whatsapp`, `greeting_page`, `created_at`, `updated_at`) VALUES
(1, 'UndanganQ', NULL, '#0e7ba0', 'bg-1735284123.png', 1, 0, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` int NOT NULL DEFAULT '2' COMMENT '1:admin: 2:resepsionis',
  `information` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `role`, `information`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@yukcoding.id', 'admin', '$2y$12$6hz0eA2JzaNhx3.pC5/s0eSncsgwCpz1mtS0Cc1BsFl.TGv6XK4Ly', 1, 'Admin Utama', NULL, NULL, NULL, NULL),
(2, 'Agus', 'agus@yukcoding.id', 'agus', '$2y$12$GQuBNYpH3/fKS07kgptJieYm9iA4w5x6yVqMcNiu0GHGhUoGJE0bm', 2, 'Resepsionis 1', NULL, NULL, NULL, NULL),
(3, 'Ujang', 'ujang@yukcoding.id', 'ujang', '$2y$12$c/OPL7Jkw8XpVfh3whbegOX2gSuor.mCKoc1V46lmx49/SAFX5CcO', 2, 'Resepsionis 2', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`id_guest`),
  ADD KEY `guest_nik_guest_index` (`nik_guest`);

--
-- Indexes for table `invitation`
--
ALTER TABLE `invitation`
  ADD PRIMARY KEY (`id_invitation`),
  ADD UNIQUE KEY `invitation_qrcode_invitation_unique` (`qrcode_invitation`),
  ADD KEY `invitation_id_guest_index` (`id_guest`),
  ADD KEY `invitation_id_user_index` (`id_user`),
  ADD KEY `invitation_id_event_index` (`id_event`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_event` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guest`
--
ALTER TABLE `guest`
  MODIFY `id_guest` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `invitation`
--
ALTER TABLE `invitation`
  MODIFY `id_invitation` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invitation`
--
ALTER TABLE `invitation`
  ADD CONSTRAINT `invitation_id_guest_foreign` FOREIGN KEY (`id_guest`) REFERENCES `guest` (`id_guest`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
