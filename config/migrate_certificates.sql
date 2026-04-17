-- ============================================================
-- Migration: Tabel certificates untuk fitur sertifikat donasi
-- Sodakoh Pohon | 2026-04-01
-- ============================================================

-- Buat tabel certificates
CREATE TABLE IF NOT EXISTS `certificates` (
  `id`                 int(11)       NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(100)  NOT NULL COMMENT 'Sama dengan donations.certificate_number',
  `donation_id`        int(11)       NOT NULL,
  `donor_name`         varchar(255)  NOT NULL COMMENT 'Nama yang dicetak di sertifikat',
  `campaign_name`      varchar(255)  NOT NULL,
  `trees_count`        int(11)       NOT NULL DEFAULT 1,
  `issued_at`          date          NOT NULL COMMENT 'Tanggal yang dicetak di sertifikat',
  `issued_by`          varchar(100)  NOT NULL DEFAULT 'Sodakoh Pohon',
  `created_at`         datetime      DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_certificate_number` (`certificate_number`),
  KEY `idx_donation_id` (`donation_id`),
  CONSTRAINT `fk_cert_donation` FOREIGN KEY (`donation_id`)
    REFERENCES `donations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Data sertifikat donasi pohon';

-- ============================================================
-- Auto-populate: Buat sertifikat dari donasi paid yang sudah ada
-- (hanya yang belum ada di tabel certificates)
-- ============================================================
INSERT IGNORE INTO `certificates`
  (`certificate_number`, `donation_id`, `donor_name`, `campaign_name`, `trees_count`, `issued_at`)
SELECT
  d.`certificate_number`,
  d.`id`,
  CASE WHEN d.`anonymous` = 1 THEN 'Hamba Allah' ELSE d.`donor_name` END,
  c.`title`,
  d.`trees_count`,
  DATE(d.`created_at`)
FROM `donations` d
JOIN `campaigns`  c ON c.`id` = d.`campaign_id`
WHERE d.`status` = 'paid'
  AND d.`certificate_number` IS NOT NULL
  AND d.`certificate_number` != '';
