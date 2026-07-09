-- Database Migration Script
-- Generated at: 2026-07-02 16:30:51

-- ==========================================
-- 1. CREATE NEW TABLES
-- ==========================================

CREATE TABLE `agenda_pembelajaran` (
  `id_agenda` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelajaran_mapel` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `hari` varchar(20) NOT NULL,
  `pertemuan_ke` int(11) NOT NULL,
  `materi` text,
  `kegiatan` text,
  `status` varchar(20) NOT NULL DEFAULT 'Belum',
  `catatan` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `jumlah_jam` int(11) DEFAULT NULL,
  `jam_mulai` varchar(10) DEFAULT NULL,
  `jam_selesai` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_agenda`),
  KEY `id_pembelajaran_mapel` (`id_pembelajaran_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- 2. ALTER EXISTING TABLES (ADD/MODIFY COLUMNS)
-- ==========================================

ALTER TABLE `alumni` 
  ADD COLUMN `jenis_pendidikan` varchar(50) NULL DEFAULT NULL   AFTER `tanggal_kembali`,
  ADD COLUMN `id_alumni_asal` int(11) NULL DEFAULT NULL   AFTER `jenis_pendidikan`;

ALTER TABLE `lembaga` 
  ADD COLUMN `nama_lembaga_singkat` varchar(50) NOT NULL    AFTER `nama_lembaga`;

ALTER TABLE `pembelajaran` 
  ADD COLUMN `status` varchar(20) NULL DEFAULT 'Aktif'   AFTER `created_at`;

ALTER TABLE `perangkat_pembelajaran` 
  ADD COLUMN `id_tahun_pelajaran` int(11) NOT NULL    AFTER `id_perangkat`,
  ADD COLUMN `id_tingkat_sekolah` int(11) NOT NULL    AFTER `id_tahun_pelajaran`,
  ADD COLUMN `id_mapel` int(11) NOT NULL    AFTER `id_tingkat_sekolah`,
  ADD COLUMN `file_cp` varchar(255) NULL DEFAULT NULL   AFTER `id_mapel`,
  ADD COLUMN `file_tp` varchar(255) NULL DEFAULT NULL   AFTER `file_cp`,
  ADD COLUMN `file_atp` varchar(255) NULL DEFAULT NULL   AFTER `file_tp`,
  ADD COLUMN `file_modul_ajar` varchar(255) NULL DEFAULT NULL   AFTER `file_atp`,
  ADD COLUMN `file_kisi_sts` varchar(255) NULL DEFAULT NULL   AFTER `file_modul_ajar`,
  ADD COLUMN `file_soal_sts` varchar(255) NULL DEFAULT NULL   AFTER `file_kisi_sts`,
  ADD COLUMN `file_kisi_sas` varchar(255) NULL DEFAULT NULL   AFTER `file_soal_sts`,
  ADD COLUMN `file_soal_sas` varchar(255) NULL DEFAULT NULL   AFTER `file_kisi_sas`;

-- ==========================================
-- 3. ALTER EXISTING TABLES (ADD INDEXES)
-- ==========================================


-- ==========================================
-- 4. INSERT DEFAULT SETTINGS (IF NOT EXISTS)
-- ==========================================

INSERT INTO `settings` (`key`, `value`) 
SELECT 'google_client_id', '' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'google_client_id');

INSERT INTO `settings` (`key`, `value`) 
SELECT 'google_client_secret', '' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'google_client_secret');

INSERT INTO `settings` (`key`, `value`) 
SELECT 'daftar_ulang_status', 'Tidak Aktif' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'daftar_ulang_status');

INSERT INTO `settings` (`key`, `value`) 
SELECT 'daftar_ulang_start_date', '' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'daftar_ulang_start_date');

INSERT INTO `settings` (`key`, `value`) 
SELECT 'daftar_ulang_end_date', '' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'daftar_ulang_end_date');

-- ==========================================
-- 5. NEW GOOGLE DRIVE & GOOGLE AI SYSTEM UPDATE
-- ==========================================

-- Tambahkan kolom Google Drive File ID jika belum ada di tabel perangkat_pembelajaran
ALTER TABLE `perangkat_pembelajaran`
  ADD COLUMN `cp_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `file_soal_sas`,
  ADD COLUMN `tp_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `cp_drive_file_id`,
  ADD COLUMN `atp_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `tp_drive_file_id`,
  ADD COLUMN `modul_ajar_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `atp_drive_file_id`,
  ADD COLUMN `kisi_sts_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `modul_ajar_drive_file_id`,
  ADD COLUMN `soal_sts_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `kisi_sts_drive_file_id`,
  ADD COLUMN `kisi_sas_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `soal_sts_drive_file_id`,
  ADD COLUMN `soal_sas_drive_file_id` varchar(255) NULL DEFAULT NULL AFTER `kisi_sas_drive_file_id`;

-- Tambahkan konfigurasi Google AI API Key jika belum ada di tabel settings
INSERT INTO `settings` (`key`, `value`) 
SELECT 'google_ai_api_key', '0' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'google_ai_api_key');

-- Tambahkan konfigurasi Google AI Model jika belum ada di tabel settings
INSERT INTO `settings` (`key`, `value`) 
SELECT 'google_ai_model', 'gemini-3.1-flash-lite' FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `key` = 'google_ai_model');

-- ==========================================
-- 6. MULTI FILE MODUL AJAR / RPP TABLE
-- ==========================================

CREATE TABLE IF NOT EXISTS `perangkat_pembelajaran_modul_ajar` (
  `id_modul` int(11) NOT NULL AUTO_INCREMENT,
  `id_tahun_pelajaran` int(11) NOT NULL,
  `id_tingkat_sekolah` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `drive_file_id` varchar(255) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_modul`),
  KEY `id_tahun_pelajaran` (`id_tahun_pelajaran`),
  KEY `id_tingkat_sekolah` (`id_tingkat_sekolah`),
  KEY `id_mapel` (`id_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



