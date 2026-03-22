-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 14, 2026 at 03:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sodakoh_pohon`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `tree_type` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'Umum',
  `price_per_tree` decimal(12,2) NOT NULL,
  `target_trees` int(11) NOT NULL,
  `current_trees` int(11) DEFAULT 0,
  `planted_trees` int(11) DEFAULT 0,
  `donors_count` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL,
  `status` enum('active','pending','completed','cancelled') DEFAULT 'active',
  `partner` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `title`, `slug`, `description`, `long_description`, `location`, `tree_type`, `category`, `price_per_tree`, `target_trees`, `current_trees`, `planted_trees`, `donors_count`, `image`, `map_url`, `status`, `partner`, `created_at`, `deadline`, `updated_at`) VALUES
(1, 'Restorasi Mangrove Demak', NULL, 'Kawasan pesisir Demak mengalami abrasi yang cukup parah. Program ini bertujuan membangun sabuk hijau mangrove.', '', 'Demak, Jawa Tengah', 'Mangrove Rhizophora', 'Mangrove', 10000.00, 5000, 1500, 890, 245, 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09', 'https://maps.google.com/?q=-6.8945,110.6364', 'active', 'Kelompok Tani Hutan Mangrove', '2026-02-19 05:39:52', '2026-03-30', NULL),
(2, 'Reboisasi Lereng Merapi', NULL, 'Menyelamatkan hutan di lereng Merapi dari ancaman longsor dengan penanaman pohon keras.', NULL, 'Magelang, Jawa Tengah', 'Sengon & Mahoni', 'Reboisasi', 12000.00, 4000, 2300, 1650, 312, 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e', 'https://maps.google.com/?q=-7.5408,110.4424', 'active', 'Komunitas Pecinta Alam', '2026-02-19 05:39:52', '2026-03-15', NULL),
(4, 'Hutan Pangan Kalimantan', NULL, 'Membangun hutan pangan untuk ketahanan pangan masyarakat sekitar hutan.', NULL, 'Kutai, Kaltim', 'Durian & Petai', 'Hutan Pangan', 25000.00, 2000, 450, 224, 89, 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc', 'https://maps.google.com/?q=0.5000,117.1500', 'active', 'Komunitas Adat Dayak', '2026-02-19 05:39:52', '2026-05-10', NULL),
(8, 'testing-1', NULL, 'untuk perdamaian dunia', 'aiwdjaidhwiahhnujhbhhvbhy', 'Magelang', 'Pohon Biju', 'Umum', 10000000.00, 10, 9, 8, 0, 'uploads/campaigns/6996d1e0206c4.jpeg', 'https://maps.app.goo.gl/pP99ZxYjreRtoZ819', 'active', 'Kelompok Ucup Surucup', '2026-02-19 10:03:28', '2026-02-27', NULL),
(11, 'testing-4-edit', NULL, '99999', '', 'Magelang', 'Pohon Biju', 'Umum', 1000000.00, 999, 999, 999, 0, 'uploads/campaigns/69b259a0a7318.png', '', 'completed', 'Kelompok Ucup Surucup', '2026-02-19 10:23:34', '2026-02-27', NULL),
(12, 'testing-5-edit', NULL, 'kita akan menguasai dunia test', 'awokawokawokawokawokawokawokawok', 'Magelang', 'Pohon Biju', 'Umum', 100000000.00, 99, 55, 50, 0, 'uploads/campaigns/6996da0c37631.jpeg', 'https://www.google.com/maps/place/Kontrasun/@-7.4589508,110.2227495,17.14z/data=!4m6!3m5!1s0x2e7a850053438bed:0xc0a761e5f440d914!8m2!3d-7.4564443!4d110.2274099!16s%2Fg%2F11vwwxcmn6?entry=ttu&g_ep=EgoyMDI2MDMwOS4wIKXMDSoASAFQAw%3D%3D', 'active', 'Kelompok Ucup Surucup', '2026-02-19 10:38:20', '2026-02-27', '2026-03-14 21:43:12'),
(13, 'testing-11-edit', NULL, 'jsdhajhasfhjshfhshfsjfsjkahafksl', '', 'Untidar Magelang', 'Pohon Tanaman', 'Umum', 10000.00, 100, 0, 0, 0, 'uploads/campaigns/69b2643fcc4c8.png', '', 'active', 'Untidar', '2026-03-12 07:15:10', '2026-03-20', '2026-03-12 13:59:11'),
(14, 'testing-12-edit-foto', NULL, 'deskripsi singkat', 'deskripsi panjang', 'Kalimantan', 'Pohon Biju', 'Umum', 100000.00, 100, 0, 0, 0, 'uploads/campaigns/69b2617a6c18e.jpeg', 'https://www.google.com/maps/place/Kontrasun/@-7.4594595,110.2300279,14z/data=!4m6!3m5!1s0x2e7a850053438bed:0xc0a761e5f440d914!8m2!3d-7.4564443!4d110.2274099!16s%2Fg%2F11vwwxcmn6?entry=ttu&g_ep=EgoyMDI2MDMxMS4wIKXMDSoASAFQAw%3D%3D', 'active', 'Kelompok Ucup Surucup', '2026-03-12 07:28:25', '2026-03-25', '2026-03-14 21:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_benefits`
--

CREATE TABLE `campaign_benefits` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `benefit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaign_benefits`
--

INSERT INTO `campaign_benefits` (`id`, `campaign_id`, `benefit`) VALUES
(1, 1, 'Melindungi garis pantai dari abrasi'),
(2, 1, 'Menciptakan habitat baru bagi biota laut'),
(3, 1, 'Menyerap karbon hingga 4x lebih banyak'),
(4, 1, 'Memberdayakan masyarakat lokal'),
(5, 2, 'Mencegah tanah longsor'),
(6, 2, 'Menjaga sumber mata air'),
(7, 2, 'Habitat satwa liar'),
(8, 2, 'Ekowisata'),
(12, 4, 'Ketahanan pangan masyarakat'),
(13, 4, 'Sumber penghasilan tambahan'),
(14, 4, 'Konservasi plasma nutfah');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_gallery`
--

CREATE TABLE `campaign_gallery` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaign_gallery`
--

INSERT INTO `campaign_gallery` (`id`, `campaign_id`, `image_url`, `caption`, `created_at`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee', 'Penanaman mangrove tahap 1', '2026-02-19 05:39:52'),
(2, 1, 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09', 'Bibit mangrove siap tanam', '2026-02-19 05:39:52'),
(3, 1, 'https://images.unsplash.com/photo-1624535168245-0f9d5d773c2e', 'Relawan menanam mangrove', '2026-02-19 05:39:52'),
(4, 2, 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e', 'Penanaman di lereng Merapi', '2026-02-19 05:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart_data`)),
  `subtotal` decimal(12,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donation_number` varchar(50) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `donor_email` varchar(255) DEFAULT NULL,
  `donor_phone` varchar(20) DEFAULT NULL,
  `anonymous` tinyint(1) DEFAULT 0,
  `campaign_id` int(11) NOT NULL,
  `trees_count` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','failed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `certificate_number` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donation_number`, `donor_name`, `donor_email`, `donor_phone`, `anonymous`, `campaign_id`, `trees_count`, `amount`, `status`, `payment_method`, `payment_proof`, `message`, `certificate_number`, `created_at`, `updated_at`) VALUES
(1, 'DON-202602-0001', 'Ahmad Pratama', 'ahmad.pratama@gmail.com', '081234567890', 0, 1, 10, 100000.00, 'paid', 'bank_transfer', 'bukti_0001.jpg', 'Semoga bermanfaat untuk pesisir Demak 🌱', 'CERT-202602-0001', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(2, 'DON-202602-0002', 'Siti Nurhaliza', 'siti.nur@gmail.com', '081298765432', 0, 1, 5, 50000.00, 'paid', 'qris', 'bukti_0002.jpg', NULL, 'CERT-202602-0002', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(3, 'DON-202602-0003', 'Hamba Allah', NULL, NULL, 1, 1, 3, 30000.00, 'paid', 'bank_transfer', 'bukti_0003.jpg', 'Semoga jadi amal jariyah', 'CERT-202602-0003', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(4, 'DON-202602-0004', 'Budi Santoso', 'budi@mail.com', '082112223333', 0, 1, 2, 20000.00, 'pending', 'bank_transfer', NULL, NULL, NULL, '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(5, 'DON-202602-0005', 'Rina Amelia', 'rina.amelia@mail.com', '081355566677', 0, 2, 8, 96000.00, 'paid', 'qris', 'bukti_0005.jpg', 'Untuk kelestarian Merapi', 'CERT-202602-0005', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(6, 'DON-202602-0006', 'Dimas Saputra', 'dimas@mail.com', '081377788899', 0, 2, 15, 180000.00, 'paid', 'bank_transfer', 'bukti_0006.jpg', NULL, 'CERT-202602-0006', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(7, 'DON-202602-0007', 'Anonymous', NULL, NULL, 1, 2, 4, 48000.00, 'failed', 'bank_transfer', NULL, NULL, NULL, '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(10, 'DON-202602-0010', 'Agus Setiawan', 'agus@mail.com', '081366677788', 0, 4, 4, 100000.00, 'paid', 'bank_transfer', 'bukti_0010.jpg', 'Untuk masyarakat Kutai', 'CERT-202602-0010', '2026-02-19 06:33:09', '2026-02-19 06:33:09'),
(11, 'DON-202602-0011', 'Lina Marlina', 'lina@mail.com', '081377766655', 0, 4, 2, 50000.00, 'cancelled', 'qris', NULL, NULL, NULL, '2026-02-19 06:33:09', '2026-02-19 06:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `plantings`
--

CREATE TABLE `plantings` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `trees_planted` int(11) NOT NULL,
  `planting_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `volunteers` int(11) DEFAULT 0,
  `coordinator` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plantings`
--

INSERT INTO `plantings` (`id`, `campaign_id`, `trees_planted`, `planting_date`, `location`, `volunteers`, `coordinator`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 300, '2026-01-15', 'Desa Timbulsloko, Demak', 25, 'Andi Wibowo', 'Penanaman mangrove tahap awal bersama warga dan relawan.', 'planting_demak_01.jpg', 'completed', '2026-02-19 06:25:36', '2026-02-19 06:25:36'),
(2, 1, 200, '2026-02-10', 'Pantai Morosari, Demak', 18, 'Slamet Riyadi', 'Penanaman lanjutan area pesisir.', 'planting_demak_02.jpg', 'completed', '2026-02-19 06:25:36', '2026-02-19 06:25:36'),
(3, 2, 500, '2026-01-20', 'Lereng Gunung Merapi', 40, 'Rizky Hidayat', 'Penanaman pohon keras untuk mitigasi longsor.', 'planting_merapi_01.jpg', 'completed', '2026-02-19 06:25:36', '2026-02-19 06:25:36'),
(4, 2, 350, '2026-03-01', 'Desa Selo, Magelang', 30, 'Dian Kartika', 'Penanaman tahap kedua sebelum musim hujan.', 'planting_merapi_02.jpg', 'scheduled', '2026-02-19 06:25:36', '2026-02-19 10:47:57'),
(6, 4, 80, '2026-02-12', 'Kutai Timur', 15, 'Yohana Dayak', 'Penanaman pohon buah untuk ketahanan pangan.', 'planting_kutai_01.jpg', 'completed', '2026-02-19 06:25:36', '2026-02-19 06:25:36'),
(9, 8, 5, '2026-02-19', 'Magelang', 88, 'Naruto', 'asik sekali', 'uploads/plantings/6996db5674d7a.jpeg', 'completed', '2026-02-19 10:43:50', '2026-03-12 07:47:56'),
(10, 8, 3, '2026-02-19', 'Magelang', 7, 'Sasuke', 'test fungsi untuk crut', 'uploads/plantings/6996e20c5acee.jpeg', 'completed', '2026-02-19 10:45:14', '2026-02-19 11:12:28'),
(11, 11, 666, '2026-02-19', 'Magelang', 88, 'Hiruzen', 'asik', '', 'completed', '2026-02-19 10:47:20', '2026-03-12 07:48:06'),
(12, 4, 99, '2026-02-19', 'Kalimantan', 22, 'Ridwan', 'test', '', 'completed', '2026-02-19 11:01:48', '2026-02-19 11:02:26'),
(13, 4, 5, '2026-02-19', 'Test Upload Fix', 10, '', '', '', 'completed', '2026-02-19 11:10:53', NULL),
(14, 11, 333, '2026-02-19', 'Kalimantan', 12, 'Sasuke', 'testing bagian penghitung jumlah pohon', '', 'completed', '2026-02-19 23:27:50', NULL),
(15, 12, 40, '2026-02-19', 'Magelang', 14, 'Sasuke', 'testing penghitungan jumlah sisa pohon', 'uploads/plantings/69978f384c490.jpeg', 'completed', '2026-02-19 23:31:20', NULL),
(16, 12, 10, '2026-03-12', 'Desa Selo, Magelang', 0, 'adadads', 'adsaasada', 'uploads/plantings/69b2638bd6a4b.jpeg', 'completed', '2026-03-12 07:48:33', '2026-03-14 15:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `planting_gallery`
--

CREATE TABLE `planting_gallery` (
  `id` int(11) NOT NULL,
  `planting_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `planting_gallery`
--

INSERT INTO `planting_gallery` (`id`, `planting_id`, `image_url`, `caption`, `created_at`) VALUES
(1, 16, 'uploads/planting_gallery/pg_69b570533c079.png', NULL, '2026-03-14 15:27:31'),
(2, 16, 'uploads/planting_gallery/pg_69b570638d8a9.png', NULL, '2026-03-14 15:27:47'),
(3, 16, 'uploads/planting_gallery/pg_69b5708477ef0.jpg', NULL, '2026-03-14 15:28:20'),
(4, 16, 'uploads/planting_gallery/pg_69b5708478328.png', NULL, '2026-03-14 15:28:20'),
(5, 16, 'uploads/planting_gallery/pg_69b57084786c2.jpeg', NULL, '2026-03-14 15:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `avatar`, `role`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'Admin Sodakoh', 'admin@sodakohpohon.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'admin', '2026-02-19 05:39:52', NULL, '2026-03-14 20:35:10'),
(2, 'Restu Wibisono', 'restu@gmail.com', '$2y$10$ayCM/7ZbosyTjP8ANxpBN.O.NlLnyE2Xl1/MT61gMfP06kRF.NoqK', '081234567890', NULL, 'user', '2026-03-12 13:57:51', NULL, '2026-03-12 13:58:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_location` (`location`);

--
-- Indexes for table `campaign_benefits`
--
ALTER TABLE `campaign_benefits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `campaign_gallery`
--
ALTER TABLE `campaign_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donation_number` (`donation_number`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_donation_number` (`donation_number`),
  ADD KEY `idx_email` (`donor_email`);

--
-- Indexes for table `plantings`
--
ALTER TABLE `plantings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_campaign` (`campaign_id`),
  ADD KEY `idx_date` (`planting_date`);

--
-- Indexes for table `planting_gallery`
--
ALTER TABLE `planting_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_planting` (`planting_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `campaign_benefits`
--
ALTER TABLE `campaign_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `campaign_gallery`
--
ALTER TABLE `campaign_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `plantings`
--
ALTER TABLE `plantings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `planting_gallery`
--
ALTER TABLE `planting_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `campaign_benefits`
--
ALTER TABLE `campaign_benefits`
  ADD CONSTRAINT `campaign_benefits_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaign_gallery`
--
ALTER TABLE `campaign_gallery`
  ADD CONSTRAINT `campaign_gallery_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`);

--
-- Constraints for table `plantings`
--
ALTER TABLE `plantings`
  ADD CONSTRAINT `plantings_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `planting_gallery`
--
ALTER TABLE `planting_gallery`
  ADD CONSTRAINT `planting_gallery_ibfk_1` FOREIGN KEY (`planting_id`) REFERENCES `plantings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
