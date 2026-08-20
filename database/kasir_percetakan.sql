-- ============================================================
-- DATABASE: kasir_percetakan
-- Aplikasi Kasir Percetakan - Laravel 10
-- ============================================================

CREATE DATABASE IF NOT EXISTS `kasir_percetakan` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kasir_percetakan`;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: customers
-- ============================================================
CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: products_services
-- ============================================================
CREATE TABLE `products_services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT 'pcs',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: transactions
-- ============================================================
CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(100) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('nominal','percent') NOT NULL DEFAULT 'nominal',
  `tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('tunai','transfer','qris') NOT NULL DEFAULT 'tunai',
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','lunas','batal') NOT NULL DEFAULT 'lunas',
  `notes` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
  KEY `transactions_customer_id_foreign` (`customer_id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: transaction_items
-- ============================================================
CREATE TABLE `transaction_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `product_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `paper_type` varchar(100) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(50) DEFAULT 'pcs',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `custom_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_items_transaction_id_foreign` (`transaction_id`),
  KEY `transaction_items_product_service_id_foreign` (`product_service_id`),
  CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaction_items_product_service_id_foreign` FOREIGN KEY (`product_service_id`) REFERENCES `products_services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: invoice_settings
-- ============================================================
CREATE TABLE `invoice_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) NOT NULL DEFAULT 'Percetakan Saya',
  `business_tagline` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `invoice_prefix` varchar(20) NOT NULL DEFAULT 'INV',
  `invoice_format` varchar(50) NOT NULL DEFAULT 'INV-{YYYY}{MM}-{NUM}',
  `footer_text` text DEFAULT NULL,
  `custom_notes` text DEFAULT NULL,
  `tax_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `primary_color` varchar(20) NOT NULL DEFAULT '#0d6efd',
  `font_size` varchar(10) NOT NULL DEFAULT '12',
  `paper_size` enum('thermal58','thermal80','a4','a5') NOT NULL DEFAULT 'a4',
  `show_qr` tinyint(1) NOT NULL DEFAULT 1,
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA: users
-- ============================================================
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@percetakan.com', NOW(), '$2y$12$T2sp3a2PNhsZAW4G2jl4xOxJfHWEpQkHFqyMfHf4CqFf0/vHqXNaC', 'admin', 1, NULL, NOW(), NOW()),
(2, 'Kasir 1', 'kasir@percetakan.com', NOW(), '$2y$12$T2sp3a2PNhsZAW4G2jl4xOxJfHWEpQkHFqyMfHf4CqFf0/vHqXNaC', 'kasir', 1, NULL, NOW(), NOW());
-- Password untuk kedua user: password123

-- ============================================================
-- SEED DATA: invoice_settings
-- ============================================================
INSERT INTO `invoice_settings` (`id`, `business_name`, `business_tagline`, `address`, `phone`, `whatsapp`, `email`, `logo`, `invoice_prefix`, `invoice_format`, `footer_text`, `custom_notes`, `tax_enabled`, `tax_percent`, `primary_color`, `font_size`, `paper_size`, `show_qr`, `dark_mode`, `created_at`, `updated_at`) VALUES
(1, 'Percetakan Modern', 'Solusi Cetak Profesional Anda', 'Jl. Raya No. 123, Kota Anda', '(0721) 123456', '081234567890', 'info@percetakanmodern.com', NULL, 'INV', 'INV-{YYYY}{MM}-{NUM}', 'Terima kasih telah mempercayakan kebutuhan cetak Anda kepada kami!', 'Barang yang sudah dibeli tidak dapat dikembalikan.', 0, 0.00, '#0d6efd', '12', 'a4', 1, 0, NOW(), NOW());

-- ============================================================
-- SEED DATA: products_services
-- ============================================================
INSERT INTO `products_services` (`id`, `name`, `category`, `description`, `price`, `unit`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Print Hitam Putih', 'Printing', 'Cetak dokumen hitam putih A4', 500.00, 'lembar', 1, 1, NOW(), NOW()),
(2, 'Print Warna', 'Printing', 'Cetak dokumen warna A4', 1500.00, 'lembar', 1, 2, NOW(), NOW()),
(3, 'Fotokopi Hitam Putih', 'Fotokopi', 'Fotokopi dokumen hitam putih A4', 300.00, 'lembar', 1, 3, NOW(), NOW()),
(4, 'Fotokopi Warna', 'Fotokopi', 'Fotokopi dokumen warna A4', 1200.00, 'lembar', 1, 4, NOW(), NOW()),
(5, 'Laminating A4', 'Laminating', 'Laminating dokumen ukuran A4', 3000.00, 'lembar', 1, 5, NOW(), NOW()),
(6, 'Laminating F4', 'Laminating', 'Laminating dokumen ukuran F4', 3500.00, 'lembar', 1, 6, NOW(), NOW()),
(7, 'Jilid Biasa', 'Penjilidan', 'Penjilidan dokumen dengan spiral', 5000.00, 'buah', 1, 7, NOW(), NOW()),
(8, 'Jilid Hardcover', 'Penjilidan', 'Penjilidan hardcover skripsi/tesis', 50000.00, 'buah', 1, 8, NOW(), NOW()),
(9, 'Cetak Banner 1x1m', 'Banner', 'Cetak banner flexi ukuran 1x1 meter', 35000.00, 'buah', 1, 9, NOW(), NOW()),
(10, 'Cetak Banner 2x1m', 'Banner', 'Cetak banner flexi ukuran 2x1 meter', 65000.00, 'buah', 1, 10, NOW(), NOW()),
(11, 'Desain Logo', 'Desain Grafis', 'Jasa desain logo profesional', 150000.00, 'buah', 1, 11, NOW(), NOW()),
(12, 'Desain Banner', 'Desain Grafis', 'Jasa desain banner/spanduk', 75000.00, 'buah', 1, 12, NOW(), NOW()),
(13, 'Cetak Brosur A5', 'Brosur', 'Cetak brosur ukuran A5 art paper', 1500.00, 'lembar', 1, 13, NOW(), NOW()),
(14, 'Cetak Stiker Vinyl', 'Stiker', 'Cetak stiker vinyl custom', 25000.00, 'lembar', 1, 14, NOW(), NOW()),
(15, 'Stempel Kayu', 'Stempel', 'Buat stempel kayu custom', 85000.00, 'buah', 1, 15, NOW(), NOW()),
(16, 'Stempel Flash', 'Stempel', 'Buat stempel flash self-inking', 120000.00, 'buah', 1, 16, NOW(), NOW()),
(17, 'Scan Dokumen A4', 'Scan', 'Scan dokumen hitam putih A4', 2000.00, 'lembar', 1, 17, NOW(), NOW()),
(18, 'Print Foto 4R', 'Foto', 'Cetak foto ukuran 4R glossy', 5000.00, 'lembar', 1, 18, NOW(), NOW());

-- ============================================================
-- SEED DATA: customers
-- ============================================================
INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Fauzi', '081234567001', 'ahmad@email.com', 'Jl. Merdeka No. 1', 'Pelanggan tetap', NOW(), NOW()),
(2, 'Siti Rahayu', '081234567002', NULL, 'Jl. Sudirman No. 5', NULL, NOW(), NOW()),
(3, 'Budi Santoso', '081234567003', 'budi@email.com', NULL, 'Mahasiswa UIN', NOW(), NOW()),
(4, 'Dewi Lestari', '081234567004', NULL, 'Jl. Ahmad Yani No. 10', NULL, NOW(), NOW()),
(5, 'Rizky Pratama', '081234567005', NULL, NULL, NULL, NOW(), NOW());

SET FOREIGN_KEY_CHECKS=0;

-- Reset auto increment
ALTER TABLE `users` AUTO_INCREMENT = 3;
ALTER TABLE `customers` AUTO_INCREMENT = 6;
ALTER TABLE `products_services` AUTO_INCREMENT = 19;
ALTER TABLE `invoice_settings` AUTO_INCREMENT = 2;

SET FOREIGN_KEY_CHECKS=1;
