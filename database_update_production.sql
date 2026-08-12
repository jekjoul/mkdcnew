-- =========================================================================
-- DATABASE UPDATE PRODUCTION - MULTI-VERSION JADWAL & AGENDA PEMBELAJARAN
-- =========================================================================

-- 1. Penambahan Kolom Kustom & Kepemilikan pada Tabel pembelajaran_mapel
ALTER TABLE `pembelajaran_mapel` 
  ADD COLUMN `judul_agenda` VARCHAR(255) NULL DEFAULT NULL AFTER `jumlah_jam`,
  ADD COLUMN `id_ptk_pemilik` INT(11) NULL DEFAULT NULL AFTER `judul_agenda`,
  ADD COLUMN `status_takeover` VARCHAR(20) NOT NULL DEFAULT 'Tidak' AFTER `id_ptk_pemilik`,
  ADD COLUMN `id_ptk_takeover` INT(11) NULL DEFAULT NULL AFTER `status_takeover`;

-- 2. Memastikan Kolom Tambahan & Media Files pada Tabel agenda_pembelajaran
ALTER TABLE `agenda_pembelajaran`
  ADD COLUMN `slide_drive_id` VARCHAR(255) NULL DEFAULT NULL AFTER `catatan`,
  ADD COLUMN `link_video` VARCHAR(255) NULL DEFAULT NULL AFTER `slide_drive_id`,
  ADD COLUMN `media_files` TEXT NULL DEFAULT NULL AFTER `link_video`;

-- 3. Pembuatan Tabel Baru untuk Header Versi Jadwal Pelajaran
CREATE TABLE IF NOT EXISTS `jadwal_pelajaran_header` (
  `id_jadwal_header` INT(11) NOT NULL AUTO_INCREMENT,
  `id_tahun_pelajaran` INT(11) NOT NULL,
  `nama_jadwal` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Draft',
  `tanggal_mulai_efektif` DATE NULL DEFAULT NULL,
  `tanggal_akhir_efektif` DATE NULL DEFAULT NULL,
  `keterangan` TEXT NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal_header`),
  KEY `idx_id_tahun_pelajaran` (`id_tahun_pelajaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Penambahan Kolom id_jadwal_header pada Tabel jadwal_pelajaran_item
ALTER TABLE `jadwal_pelajaran_item`
  ADD COLUMN `id_jadwal_header` INT(11) NOT NULL DEFAULT 0 AFTER `id_jadwal`;

-- 5. Pendaftaran Permission Baru untuk Menu Agenda Pembelajaran
-- Cari ID group_pembelajaran terlebih dahulu (biasanya 42, tetapi kita cari secara dinamis di SQL)
SET @parent_id = (SELECT `id` FROM `permissions` WHERE `code` = 'group_pembelajaran' LIMIT 1);

-- Sisipkan permission jika belum terdaftar
INSERT INTO `permissions` (`parent_id`, `level`, `title`, `code`)
SELECT IFNULL(@parent_id, 42), 2, 'Agenda Pembelajaran Rombel', 'menu_agenda_pembelajaran'
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `code` = 'menu_agenda_pembelajaran'
);

-- Sisipkan default role_permissions untuk Admin (Role ID: 1) dan Tenaga Administrasi (Role ID: 3)
INSERT INTO `role_permissions` (`role`, `permission`)
SELECT 1, 'menu_agenda_pembelajaran'
WHERE NOT EXISTS (
    SELECT 1 FROM `role_permissions` WHERE `role` = 1 AND `permission` = 'menu_agenda_pembelajaran'
);

INSERT INTO `role_permissions` (`role`, `permission`)
SELECT 3, 'menu_agenda_pembelajaran'
WHERE NOT EXISTS (
    SELECT 1 FROM `role_permissions` WHERE `role` = 3 AND `permission` = 'menu_agenda_pembelajaran'
);

-- =========================================================================
-- CATATAN MIGRASI DATA AWAL (AUTOMATIC MIGRATION HELPER)
-- Saat aplikasi dijalankan pertama kali, sistem secara otomatis akan memigrasi
-- data item jadwal yang belum terhubung ke dalam versi "Jadwal Mingguan Utama".
-- =========================================================================
