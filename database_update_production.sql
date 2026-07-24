-- ====================================================================
-- SKRIP MIGRASI DATABASE SERVER PRODUCTION
-- Fitur: Presensi Agenda Pembelajaran Realtime, Rekapitulasi Presensi,
--        Kerangka Waktu Mingguan & Penyesuaian Jadwal KBM.
-- Tanggal Pembuatan: 2026-07-22
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. PEMBUATAN TABEL PRESENSI SISWA PER AGENDA PEMBELAJARAN (REALTIME AUTOSAVE)
CREATE TABLE IF NOT EXISTS `presensi_agenda_siswa` (
  `id_presensi_agenda` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_agenda` INT UNSIGNED NOT NULL,
  `id_siswa` INT UNSIGNED NOT NULL,
  `status` ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NULL DEFAULT NULL,
  `catatan` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presensi_agenda`),
  UNIQUE KEY `uk_agenda_siswa` (`id_agenda`, `id_siswa`),
  KEY `idx_agenda` (`id_agenda`),
  KEY `idx_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. MEMASTIKAN TABEL PENGATURAN KERANGKA WAKTU MINGGUAN (JADWAL PELAJARAN)
CREATE TABLE IF NOT EXISTS `jadwal_pelajaran_pengaturan` (
  `id_pengaturan` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pembelajaran` INT(11) NOT NULL,
  `hari` VARCHAR(20) NOT NULL,
  `jam_mulai` TIME NOT NULL DEFAULT '07:00:00',
  `menit_jp` INT(11) NOT NULL DEFAULT 40,
  `jumlah_jp` INT(11) NOT NULL DEFAULT 8,
  `istirahat_json` TEXT NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengaturan`),
  KEY `idx_pembelajaran` (`id_pembelajaran`),
  KEY `idx_hari` (`hari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. MEMASTIKAN TABEL SLOT MASTER JADWAL PELAJARAN
CREATE TABLE IF NOT EXISTS `jadwal_pelajaran_item` (
  `id_jadwal` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pembelajaran` INT(11) NOT NULL,
  `hari` VARCHAR(20) NOT NULL,
  `slot_ke` INT(11) NOT NULL,
  `id_mapel` INT(11) NOT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `idx_pembelajaran` (`id_pembelajaran`),
  KEY `idx_hari_slot` (`hari`, `slot_ke`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- SELESAI. Skrip ini siap dieksekusi di phpMyAdmin / MySQL Production Client.
-- ====================================================================
