-- ============================================================
-- SQL PERUBAHAN DATABASE UNTUK SERVER PRODUCTION
-- Aplikasi MKDC - Update Fitur SK Pengangkatan & Master Lembaga
-- ============================================================

-- 1. Tambah Kolom jenis_lembaga di Tabel lembaga
ALTER TABLE `lembaga` ADD COLUMN `jenis_lembaga` VARCHAR(100) NULL DEFAULT 'Sekolah Formal';

-- 2. Buat Tabel Master Dasar Hukum SK
CREATE TABLE IF NOT EXISTS `surat_dasar_hukum` (
  `id_dasar_hukum` int(11) NOT NULL AUTO_INCREMENT,
  `kategori` varchar(100) NOT NULL DEFAULT 'Umum',
  `isi_dasar_hukum` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dasar_hukum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Buat Tabel Master Preset Redaksi SK
CREATE TABLE IF NOT EXISTS `surat_preset_sk` (
  `id_preset` int(11) NOT NULL AUTO_INCREMENT,
  `nama_preset` varchar(150) NOT NULL,
  `jenis_sk` varchar(50) NOT NULL DEFAULT 'sk_pengangkatan',
  `tentang` text NULL,
  `menimbang` text NULL,
  `mengingat_json` longtext NULL,
  `memperhatikan` text NULL,
  `redaksi_pertama` text NULL,
  `poin_kedua` text NULL,
  `poin_ketiga` text NULL,
  `poin_keempat` text NULL,
  `poin_kelima` text NULL,
  `kabupaten_penutup` varchar(100) NULL,
  `id_ptk_penandatangan` int(11) NULL DEFAULT 0,
  `jabatan_penandatangan` varchar(150) NULL,
  `payload_json` longtext NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_preset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4. Pendaftaran Template SK Pengangkatan Yayasan di tabel surat_template (Jika belum ada)
INSERT INTO `surat_template` (`nama_template`, `target_url`, `kategori`, `status`)
SELECT 'Surat Keputusan Pengangkatan Pegawai/Guru Yayasan', 'surat/sk_pengangkatan', 'Yayasan & Kelembagaan', 'Aktif'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `surat_template` WHERE `target_url` = 'surat/sk_pengangkatan');
