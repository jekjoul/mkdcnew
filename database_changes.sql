-- ======================================================================
-- FILE PERUBAHAN DATABASE (PRODUCTION UPDATE)
-- IMPLEMENTASI SISTEM MULTI-ROLE, EKSTRAKURIKULER, & KEDISIPLINAN/BK
-- ======================================================================

-- 1. Membuat tabel relasi untuk multi-role (user_roles)
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_roles_user` (`user_id`),
  KEY `idx_user_roles_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Membuat tabel Ekstrakurikuler Utama (jika belum ada)
CREATE TABLE IF NOT EXISTS `ekstrakurikuler` (
  `id_ekskul` int(11) NOT NULL AUTO_INCREMENT,
  `id_tahun_pelajaran` int(11) DEFAULT NULL,
  `nama_ekskul` varchar(100) NOT NULL,
  `id_ptk_pembina` text,
  `logo` varchar(255) DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id_ekskul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Migrasi tipe data pembina ekstrakurikuler agar mendukung ganda (JSON Array)
ALTER TABLE `ekstrakurikuler` MODIFY COLUMN `id_ptk_pembina` TEXT NULL DEFAULT NULL;

-- 3. Membuat tabel Siswa Anggota & Nilai Ekstrakurikuler
CREATE TABLE IF NOT EXISTS `ekstrakurikuler_siswa` (
  `id_ekskul_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `id_ekskul` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nilai` varchar(5) DEFAULT NULL,
  `catatan` text,
  PRIMARY KEY (`id_ekskul_siswa`),
  KEY `idx_ekskul_siswa_ekskul` (`id_ekskul`),
  KEY `idx_ekskul_siswa_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4. Membuat tabel Kategori Pelanggaran Kedisiplinan
CREATE TABLE IF NOT EXISTS `kedisiplinan_pelanggaran_kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `bobot_poin` int(5) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Seed default data Kategori Pelanggaran Kedisiplinan
INSERT INTO `kedisiplinan_pelanggaran_kategori` (`id_kategori`, `nama_pelanggaran`, `bobot_poin`) VALUES
(1, 'Terlambat Masuk Sekolah', 5),
(2, 'Membolos di Jam Pelajaran', 10),
(3, 'Tidak Mengerjakan Tugas', 5),
(4, 'Merusak Sarana Kelas', 20),
(5, 'Membawa HP / Gadget Tanpa Izin', 10),
(6, 'Tawuran / Berkelahi', 75),
(7, 'Mencuri / Mengambil Hak Orang Lain', 50)
ON DUPLICATE KEY UPDATE `nama_pelanggaran`=VALUES(`nama_pelanggaran`), `bobot_poin`=VALUES(`bobot_poin`);

-- 5. Membuat tabel Laporan Pelanggaran Siswa
CREATE TABLE IF NOT EXISTS `kedisiplinan_pelanggaran_siswa` (
  `id_pelanggaran_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `tanggal_pelanggaran` date NOT NULL,
  `catatan` text,
  `tindak_lanjut` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pelanggaran_siswa`),
  KEY `idx_pelanggaran_siswa` (`id_siswa`),
  KEY `idx_pelanggaran_kategori` (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 6. Insert / Update all Permissions fitur dan menu sidebar baru
INSERT INTO `permissions` (`code`, `title`) VALUES
('menu_dashboard', 'Menu Dashboard'),
('menu_kesiswaan_data_siswa', 'Menu Data Siswa'),
('menu_sinkron_dapodik', 'Menu Sinkron Dapodik'),
('menu_pembelajaran', 'Menu Pembelajaran'),
('menu_jadwal_pelajaran', 'Menu Jadwal Pelajaran'),
('menu_jadwal_tidak_aktif', 'Menu Jadwal Tidak Aktif'),
('menu_perangkat_pembelajaran', 'Menu Perangkat Pembelajaran'),
('menu_nilai_siswa', 'Menu Nilai Siswa'),
('menu_tahun_pelajaran', 'Menu Tahun Pelajaran'),
('menu_surat_menyurat', 'Menu Surat & Pencetakan'),
('menu_alumni', 'Menu Alumni'),
('menu_buku_induk_siswa', 'Menu Buku Induk Siswa'),
('menu_master_lembaga', 'Menu Master Lembaga'),
('menu_master_tingkat', 'Menu Master Tingkat'),
('menu_master_rombel', 'Menu Master Rombel'),
('menu_master_rombel_nonaktif', 'Menu Master Rombel Nonaktif'),
('menu_master_mapel', 'Menu Master Mapel'),
('menu_master_sarana', 'Menu Master Sarana'),
('menu_users', 'Menu Akun Pengguna'),
('menu_roles', 'Menu Hak Akses (Roles)'),
('menu_dashboard_guru', 'Menu Portal Utama Guru'),
('users_list', 'Melihat Daftar Akun'),
('users_add', 'Menambah Akun Baru'),
('users_edit', 'Mengubah Data Akun'),
('users_delete', 'Menghapus Akun'),
('roles_list', 'Melihat Hak Akses (Roles)'),
('roles_add', 'Menambah Role Baru'),
('roles_edit', 'Mengubah Role & Permissions'),
('master_list', 'Melihat Master Data'),
('master_add', 'Menambah Master Data'),
('master_edit', 'Mengubah Master Data'),
('master_delete', 'Menghapus Master Data'),
('pembelajaran_list', 'Mengelola Data Pembelajaran'),
('pembelajaran_add', 'Menambah Pembelajaran Rombel'),
('pembelajaran_edit', 'Mengubah Pembelajaran Rombel'),
('pembelajaran_delete', 'Menghapus Pembelajaran Rombel'),
('menu_ekstrakurikuler', 'Menu Ekstrakurikuler'),
('ekstrakurikuler_add', 'Menambah Ekstrakurikuler Baru'),
('ekstrakurikuler_edit', 'Mengubah Konfigurasi Ekstrakurikuler'),
('ekstrakurikuler_delete', 'Menghapus Kegiatan Ekstrakurikuler'),
('ekstrakurikuler_anggota', 'Mengelola Siswa Anggota Roster Ekskul'),
('ekstrakurikuler_nilai', 'Menginput Predikat Nilai & Evaluasi Ekskul'),
('menu_kedisiplinan', 'Menu Kedisiplinan & BK'),
('kedisiplinan_add', 'Melaporkan Pelanggaran Murid Baru'),
('kedisiplinan_bk', 'Memproses Tindak Lanjut Konseling BK & Poin Pelanggaran'),
('kedisiplinan_delete', 'Menghapus Laporan Pelanggaran Murid')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 7. Seed list Roles jabatan fungsional baru
INSERT INTO `roles` (`id`, `title`) VALUES
(1, 'Admin'),
(2, 'User'),
(3, 'Tenaga Administrasi Sekolah'),
(4, 'Guru'),
(6, 'Wakasek Kesiswaan'),
(7, 'Wakasek Kurikulum'),
(8, 'Wakasek Sarpras'),
(9, 'Guru BK'),
(10, 'Kepala Sekolah')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 8. Menghapus & Sync ulang mapping Permissions dari ke-8 role tersebut
DELETE FROM `role_permissions` WHERE `role` IN (1, 3, 4, 6, 7, 8, 9, 10);

INSERT INTO `role_permissions` (`role`, `permission`) VALUES
-- Admin (ID 1)
(1, 'menu_dashboard'), (1, 'menu_kesiswaan_data_siswa'), (1, 'menu_sinkron_dapodik'), (1, 'menu_pembelajaran'), 
(1, 'menu_jadwal_pelajaran'), (1, 'menu_jadwal_tidak_aktif'), (1, 'menu_perangkat_pembelajaran'), (1, 'menu_nilai_siswa'), 
(1, 'menu_tahun_pelajaran'), (1, 'menu_surat_menyurat'), (1, 'menu_alumni'), (1, 'menu_buku_induk_siswa'), 
(1, 'menu_master_lembaga'), (1, 'menu_master_tingkat'), (1, 'menu_master_rombel'), (1, 'menu_master_rombel_nonaktif'), 
(1, 'menu_master_mapel'), (1, 'menu_master_sarana'), (1, 'menu_users'), (1, 'menu_roles'), (1, 'menu_dashboard_guru'), 
(1, 'users_list'), (1, 'users_add'), (1, 'users_edit'), (1, 'users_delete'), (1, 'roles_list'), (1, 'roles_add'), (1, 'roles_edit'), 
(1, 'master_list'), (1, 'master_add'), (1, 'master_edit'), (1, 'master_delete'), (1, 'pembelajaran_list'), (1, 'pembelajaran_add'), 
(1, 'pembelajaran_edit'), (1, 'pembelajaran_delete'), (1, 'menu_ekstrakurikuler'), (1, 'ekstrakurikuler_add'), 
(1, 'ekstrakurikuler_edit'), (1, 'ekstrakurikuler_delete'), (1, 'ekstrakurikuler_anggota'), (1, 'ekstrakurikuler_nilai'), 
(1, 'menu_kedisiplinan'), (1, 'kedisiplinan_add'), (1, 'kedisiplinan_bk'), (1, 'kedisiplinan_delete'),

-- Tenaga Administrasi Sekolah (ID 3)
(3, 'menu_dashboard'), (3, 'menu_kesiswaan_data_siswa'), (3, 'menu_alumni'), (3, 'menu_buku_induk_siswa'), 
(3, 'menu_surat_menyurat'), (3, 'menu_master_lembaga'), (3, 'menu_master_tingkat'), (3, 'menu_master_rombel'), 
(3, 'menu_master_rombel_nonaktif'), (3, 'menu_master_mapel'), (3, 'master_list'), (3, 'master_add'), (3, 'master_edit'),
(3, 'menu_ekstrakurikuler'), (3, 'ekstrakurikuler_anggota'), (3, 'menu_kedisiplinan'), (3, 'kedisiplinan_add'),

-- Guru (ID 4)
(4, 'menu_dashboard_guru'), (4, 'menu_data_siswa_guru'), (4, 'menu_pembelajaran_guru'), (4, 'menu_perangkat_guru'), 
(4, 'menu_jadwal_guru'), (4, 'menu_input_nilai_guru'), (4, 'menu_profil_ptk_guru'), 
(4, 'menu_ekstrakurikuler'), (4, 'ekstrakurikuler_anggota'), (4, 'ekstrakurikuler_nilai'),
(4, 'menu_kedisiplinan'), (4, 'kedisiplinan_add'),

-- Wakasek Kesiswaan (ID 6)
(6, 'menu_dashboard'), (6, 'menu_kesiswaan_data_siswa'), (6, 'menu_alumni'), (6, 'menu_buku_induk_siswa'), 
(6, 'master_list'), (6, 'users_list'),
(6, 'menu_ekstrakurikuler'), (6, 'ekstrakurikuler_add'), (6, 'ekstrakurikuler_edit'), (6, 'ekstrakurikuler_delete'), 
(6, 'ekstrakurikuler_anggota'), (6, 'ekstrakurikuler_nilai'), 
(6, 'menu_kedisiplinan'), (6, 'kedisiplinan_add'), (6, 'kedisiplinan_bk'), (6, 'kedisiplinan_delete'),

-- Wakasek Kurikulum (ID 7)
(7, 'menu_dashboard'), (7, 'menu_pembelajaran'), (7, 'menu_jadwal_pelajaran'), (7, 'menu_jadwal_tidak_aktif'), 
(7, 'menu_perangkat_pembelajaran'), (7, 'menu_nilai_siswa'), (7, 'menu_tahun_pelajaran'), 
(7, 'pembelajaran_list'), (7, 'pembelajaran_add'), (7, 'pembelajaran_edit'), (7, 'pembelajaran_delete'), 
(7, 'master_list'), (7, 'master_add'), (7, 'master_edit'),

-- Wakasek Sarpras (ID 8)
(8, 'menu_dashboard'), (8, 'menu_master_sarana'), (8, 'master_list'), (8, 'master_add'), (8, 'master_edit'), (8, 'master_delete'),

-- Guru BK (ID 9)
(9, 'menu_dashboard'), (9, 'menu_kesiswaan_data_siswa'), (9, 'menu_dashboard_guru'), (9, 'master_list'),
(9, 'menu_kedisiplinan'), (9, 'kedisiplinan_add'), (9, 'kedisiplinan_bk'), (9, 'kedisiplinan_delete'),

-- Kepala Sekolah (ID 10)
(10, 'menu_dashboard'), (10, 'menu_kesiswaan_data_siswa'), (10, 'menu_pembelajaran'), (10, 'menu_jadwal_pelajaran'), 
(10, 'menu_perangkat_pembelajaran'), (10, 'menu_nilai_siswa'), (10, 'menu_tahun_pelajaran'), (10, 'menu_alumni'), 
(10, 'menu_buku_induk_siswa'), (10, 'menu_master_sarana'), (10, 'master_list'), (10, 'pembelajaran_list'), (10, 'users_list'),
(10, 'menu_ekstrakurikuler'), (10, 'menu_kedisiplinan');
