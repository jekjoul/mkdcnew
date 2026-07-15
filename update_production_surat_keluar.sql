-- ======================================================================
-- SCRIPT UPDATE DATABASE PRODUCTION - PEROMBAKAN SURAT KELUAR MANUAL & OTOMATIS
-- ======================================================================

-- 1. MODIFIKASI TABEL SURAT KELUAR
-- 1.1 Tambah kolom metode_pembuatan jika belum ada
ALTER TABLE `surat_keluar` 
ADD COLUMN `metode_pembuatan` VARCHAR(20) NOT NULL DEFAULT 'Manual' AFTER `id_template_surat`;

-- 1.2 Tambah kolom keterangan jika belum ada
ALTER TABLE `surat_keluar` 
ADD COLUMN `keterangan` TEXT NULL AFTER `isi_surat`;

-- 1.3 Modifikasi kolom isi_surat agar nullable
ALTER TABLE `surat_keluar` 
MODIFY COLUMN `isi_surat` TEXT NULL;


-- 2. PEMBUATAN TABEL RELASI PENANDATANGAN MULTI-PEJABAT
CREATE TABLE IF NOT EXISTS `surat_keluar_penandatangan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_surat_keluar` INT NOT NULL,
  `id_ptk` INT NOT NULL,
  `jabatan` VARCHAR(150) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
