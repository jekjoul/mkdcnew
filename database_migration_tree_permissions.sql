-- ====================================================================
-- MIGRASI DATABASE: SISTEM HIERARKI TREE PERMISSIONS (DATABASE-DRIVEN)
-- ====================================================================
-- Menambahkan kolom parent_id dan level, serta menyinkronkan data
-- permissions dan role_permissions hasil penyisiran menu & fitur lengkap.
-- ====================================================================

-- 1. Modifikasi Skema Tabel `permissions` (DDL)
-- Tambahkan kolom parent_id dan level jika belum ada
ALTER TABLE `permissions` ADD COLUMN `parent_id` INT NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `permissions` ADD COLUMN `level` TINYINT NOT NULL DEFAULT 1 AFTER `parent_id`;

-- 2. Kosongkan Data Permissions Lama (Untuk Membangun Tree Baru)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `permissions`;
SET FOREIGN_KEY_CHECKS = 1;

-- 3. Seeding Data Permissions Berjenjang 3 Level (DML)
INSERT INTO `permissions` (`id`, `parent_id`, `level`, `code`, `title`) VALUES
-- L1: Dashboard Utama
(1, NULL, 1, 'group_dashboard', 'Dashboard Utama'),
(2, 1, 2, 'menu_dashboard', 'Dashboard Admin'),
(3, 1, 2, 'menu_dashboard_guru', 'Dashboard Guru'),

-- L1: Calon Siswa & Daftar Ulang
(4, NULL, 1, 'group_calon_siswa', 'Calon Siswa & Daftar Ulang'),
(5, 4, 2, 'menu_calon_siswa', 'Calon Siswa'),
(6, 5, 3, 'calon_siswa_list', 'Melihat Daftar Calon Siswa'),
(7, 5, 3, 'calon_siswa_view', 'Melihat Detail Calon Siswa'),
(8, 5, 3, 'calon_siswa_add', 'Menambah Calon Siswa Baru'),
(9, 5, 3, 'calon_siswa_edit', 'Mengubah Data Calon Siswa'),
(10, 5, 3, 'calon_siswa_delete', 'Menghapus Calon Siswa'),
(11, 5, 3, 'calon_siswa_export', 'Ekspor Data Calon Siswa'),
(12, 5, 3, 'calon_siswa_import', 'Impor Data Calon Siswa'),
(13, 4, 2, 'menu_validasi_daftar_ulang', 'Validasi Daftar Ulang'),
(14, 13, 3, 'calon_siswa_validasi', 'Memproses Validasi Berkas Calon Siswa'),
(15, 4, 2, 'menu_aktivasi_calon_siswa', 'Aktivasi Calon Siswa'),
(16, 15, 3, 'calon_siswa_aktivasi', 'Mengaktivasi Calon Siswa (Menjadi Siswa Aktif)'),

-- L1: Kelembagaan & Sarpras
(17, NULL, 1, 'group_kelembagaan', 'Kelembagaan & Sarpras'),
(18, 17, 2, 'menu_lembaga', 'Data Lembaga'),
(19, 18, 3, 'lembaga_list', 'Melihat Lembaga'),
(20, 18, 3, 'lembaga_edit', 'Mengubah Data Lembaga'),
(21, 17, 2, 'menu_sarpras', 'Data Sarana Prasarana (Sarpras)'),

-- L1: Kepegawaian (PTK)
(22, NULL, 1, 'group_kepegawaian', 'Kepegawaian (PTK)'),
(23, 22, 2, 'menu_data_ptk', 'Daftar Kepegawaian GTK/PTK'),
(24, 23, 3, 'ptk_list', 'Melihat Daftar Kepegawaian PTK'),
(25, 23, 3, 'ptk_buat_akun', 'Membuat Akun Login PTK/Guru'),
(26, 22, 2, 'menu_ptk_nonaktif', 'PTK Nonaktif'),
(27, 22, 2, 'menu_sinkron_dapodik_gtk', 'Sinkron Dapodik GTK'),

-- L1: Kesiswaan & Kedisiplinan
(28, NULL, 1, 'group_kesiswaan', 'Kesiswaan & Kedisiplinan'),
(29, 28, 2, 'menu_kesiswaan_data_siswa', 'Data Siswa Utama'),
(30, 29, 3, 'siswa_list', 'Melihat Daftar Siswa'),
(31, 29, 3, 'siswa_view', 'Melihat Detail Siswa'),
(32, 29, 3, 'siswa_add', 'Menambah Siswa Baru'),
(33, 29, 3, 'siswa_edit', 'Mengubah Data Siswa'),
(34, 29, 3, 'siswa_delete', 'Menghapus Siswa'),
(35, 28, 2, 'menu_data_siswa_guru', 'Data Siswa Rombel (Portal Guru)'),
(36, 28, 2, 'menu_sinkron_dapodik', 'Sinkron Dapodik Siswa'),
(37, 36, 3, 'sync_dapodik_view', 'Mengakses Fitur Sinkronisasi Dapodik'),
(38, 28, 2, 'menu_kedisiplinan', 'Kedisiplinan & BK'),
(39, 38, 3, 'kedisiplinan_add', 'Laporkan Pelanggaran Murid'),
(40, 38, 3, 'kedisiplinan_bk', 'Tindak Lanjut Konseling BK & Poin'),
(41, 38, 3, 'kedisiplinan_delete', 'Hapus Laporan Pelanggaran'),

-- L1: Kurikulum & Pembelajaran
(42, NULL, 1, 'group_pembelajaran', 'Kurikulum & Pembelajaran'),
(43, 42, 2, 'menu_pembelajaran_guru', 'Pembelajaran Saya (Portal Guru)'),
(44, 42, 2, 'menu_perangkat_guru', 'Perangkat Mengajar (Portal Guru)'),
(45, 42, 2, 'menu_jadwal_guru', 'Jadwal Mengajar (Portal Guru)'),
(46, 42, 2, 'menu_input_nilai_guru', 'Input Nilai Siswa (Portal Guru)'),
(47, 42, 2, 'menu_profil_ptk_guru', 'Profil PTK (Portal Guru)'),
(48, 42, 2, 'menu_pembelajaran', 'Manajemen Pembelajaran Rombel'),
(49, 48, 3, 'pembelajaran_list', 'Melihat Daftar Rombel'),
(50, 48, 3, 'pembelajaran_add', 'Atur Rombel Baru'),
(51, 48, 3, 'pembelajaran_edit', 'Ubah Pembelajaran Rombel'),
(52, 48, 3, 'pembelajaran_delete', 'Hapus Pembelajaran Rombel'),
(53, 42, 2, 'menu_jadwal_pelajaran', 'Jadwal Pelajaran Rombel'),
(54, 53, 3, 'jadwal_pelajaran_list', 'Mengelola Jadwal Pelajaran'),
(55, 42, 2, 'menu_jadwal_tidak_aktif', 'Jadwal Tidak Aktif'),
(56, 42, 2, 'menu_perangkat_pembelajaran', 'Perangkat Pembelajaran Rombel'),
(57, 42, 2, 'menu_nilai_siswa', 'Penilaian Siswa Rombel'),
(58, 57, 3, 'nilai_siswa_list', 'Mengelola Nilai Siswa'),
(59, 42, 2, 'menu_tahun_pelajaran', 'Tahun Pelajaran & Kalender'),
(60, 59, 3, 'tahun_pelajaran_list', 'Melihat Tahun Pelajaran'),
(61, 59, 3, 'tahun_pelajaran_add', 'Menambah Tahun Pelajaran'),
(62, 59, 3, 'tahun_pelajaran_edit', 'Mengubah Tahun Pelajaran'),
(63, 59, 3, 'tahun_pelajaran_delete', 'Menghapus Tahun Pelajaran'),
(64, 42, 2, 'menu_ekstrakurikuler', 'Ekstrakurikuler & Roster'),
(65, 64, 3, 'ekstrakurikuler_add', 'Menambah Ekskul Baru'),
(66, 64, 3, 'ekstrakurikuler_edit', 'Mengubah Ekskul'),
(67, 64, 3, 'ekstrakurikuler_delete', 'Menghapus Ekskul'),
(68, 64, 3, 'ekstrakurikuler_anggota', 'Mengelola Anggota Ekskul'),
(69, 64, 3, 'ekstrakurikuler_nilai', 'Input Nilai Ekskul'),

-- L1: Pencetakan & Administrasi Surat
(70, NULL, 1, 'group_surat', 'Pencetakan & Administrasi Surat'),
(71, 70, 2, 'menu_surat_menyurat', 'Surat Menyurat & Cetak'),
(72, 71, 3, 'surat_list', 'Mengelola Surat Masuk/Keluar/Template/Kop & Cetak Absensi'),

-- L1: Alumni & Dokumen Sekolah
(73, NULL, 1, 'group_alumni', 'Alumni & Dokumen Sekolah'),
(74, 73, 2, 'menu_alumni', 'Data Alumni Siswa'),
(75, 74, 3, 'alumni_list', 'Mengelola Data Alumni'),
(76, 73, 2, 'menu_buku_induk_siswa', 'Buku Induk Siswa'),
(77, 76, 3, 'buku_induk_siswa_list', 'Mengelola Buku Induk Siswa'),

-- L1: Master Data Referensi
(78, NULL, 1, 'group_master', 'Master Data Referensi'),
(79, 78, 2, 'menu_master_lembaga', 'Master Lembaga'),
(80, 78, 2, 'menu_master_tingkat', 'Master Tingkat Sekolah'),
(81, 78, 2, 'menu_master_rombel', 'Master Rombel'),
(82, 78, 2, 'menu_master_rombel_nonaktif', 'Master Rombel Nonaktif'),
(83, 78, 2, 'menu_master_mapel', 'Master Mata Pelajaran'),
(84, 78, 2, 'menu_master_sarana', 'Master Sarana & Prasarana'),
(85, 78, 2, 'sub_master_aksi', 'Aksi Master Data Referensi'),
(86, 85, 3, 'master_list', 'Melihat Master'),
(87, 85, 3, 'master_add', 'Menambah Master'),
(88, 85, 3, 'master_edit', 'Mengubah Master'),
(89, 85, 3, 'master_delete', 'Menghapus Master'),

-- L1: Manajemen Pengguna & Log
(90, NULL, 1, 'group_users', 'Manajemen Pengguna & Log'),
(91, 90, 2, 'menu_users', 'Akun Pengguna'),
(92, 91, 3, 'users_list', 'Melihat Akun'),
(93, 91, 3, 'users_add', 'Tambah Akun'),
(94, 91, 3, 'users_edit', 'Ubah Akun'),
(95, 91, 3, 'users_delete', 'Hapus Akun'),
(96, 91, 3, 'users_view', 'Melihat Detail Akun'),
(97, 90, 2, 'menu_roles', 'Hak Akses Role & Permissions'),
(98, 97, 3, 'roles_list', 'Melihat Role'),
(99, 97, 3, 'roles_add', 'Tambah Role'),
(100, 97, 3, 'roles_edit', 'Ubah Role & Permissions'),
(101, 97, 3, 'permissions_list', 'Melihat Daftar Permissions'),
(102, 97, 3, 'permissions_add', 'Menambah Permission Baru'),
(103, 97, 3, 'permissions_edit', 'Mengubah Permission'),
(104, 97, 3, 'permissions_delete', 'Menghapus Permission'),
(105, 90, 2, 'sub_log_backup', 'Log Aktivitas & Backup'),
(106, 105, 3, 'activity_log_list', 'Melihat Log Aktivitas'),
(107, 105, 3, 'activity_log_view', 'Melihat Detail Log Aktivitas'),
(108, 105, 3, 'backup_db', 'Melakukan Backup Database'),

-- L1: Pengaturan System
(109, NULL, 1, 'group_settings', 'Pengaturan System'),
(110, 109, 2, 'sub_pengaturan_sistem', 'Pengaturan Aplikasi'),
(111, 110, 3, 'general_settings', 'Pengaturan Umum Aplikasi'),
(112, 110, 3, 'company_settings', 'Pengaturan Informasi Lembaga'),
(113, 110, 3, 'login_theme', 'Pengaturan Tema Halaman Login'),
(114, 110, 3, 'email_templates', 'Pengaturan Template Email');

-- 4. Seed List Roles Jabatan Fungsional
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

-- 5. Sinkronisasi Data Mapping Role-Permissions
DELETE FROM `role_permissions` WHERE `role` IN (1, 3, 4, 6, 7, 8, 9, 10);

INSERT INTO `role_permissions` (`role`, `permission`) VALUES
-- Admin (ID 1)
(1, 'menu_dashboard'), (1, 'menu_dashboard_guru'),
(1, 'menu_calon_siswa'), (1, 'calon_siswa_list'), (1, 'calon_siswa_view'), (1, 'calon_siswa_add'), (1, 'calon_siswa_edit'), (1, 'calon_siswa_delete'), (1, 'calon_siswa_export'), (1, 'calon_siswa_import'),
(1, 'menu_validasi_daftar_ulang'), (1, 'calon_siswa_validasi'),
(1, 'menu_aktivasi_calon_siswa'), (1, 'calon_siswa_aktivasi'),
(1, 'menu_lembaga'), (1, 'lembaga_list'), (1, 'lembaga_edit'),
(1, 'menu_sarpras'),
(1, 'menu_data_ptk'), (1, 'ptk_list'), (1, 'ptk_buat_akun'),
(1, 'menu_ptk_nonaktif'), (1, 'menu_sinkron_dapodik_gtk'),
(1, 'menu_kesiswaan_data_siswa'), (1, 'siswa_list'), (1, 'siswa_view'), (1, 'siswa_add'), (1, 'siswa_edit'), (1, 'siswa_delete'),
(1, 'menu_data_siswa_guru'),
(1, 'menu_sinkron_dapodik'), (1, 'sync_dapodik_view'),
(1, 'menu_kedisiplinan'), (1, 'kedisiplinan_add'), (1, 'kedisiplinan_bk'), (1, 'kedisiplinan_delete'),
(1, 'menu_pembelajaran_guru'), (1, 'menu_perangkat_guru'), (1, 'menu_jadwal_guru'), (1, 'menu_input_nilai_guru'), (1, 'menu_profil_ptk_guru'),
(1, 'menu_pembelajaran'), (1, 'pembelajaran_list'), (1, 'pembelajaran_add'), (1, 'pembelajaran_edit'), (1, 'pembelajaran_delete'),
(1, 'menu_jadwal_pelajaran'), (1, 'jadwal_pelajaran_list'),
(1, 'menu_jadwal_tidak_aktif'), (1, 'menu_perangkat_pembelajaran'),
(1, 'menu_nilai_siswa'), (1, 'nilai_siswa_list'),
(1, 'menu_tahun_pelajaran'), (1, 'tahun_pelajaran_list'), (1, 'tahun_pelajaran_add'), (1, 'tahun_pelajaran_edit'), (1, 'tahun_pelajaran_delete'),
(1, 'menu_ekstrakurikuler'), (1, 'ekstrakurikuler_add'), (1, 'ekstrakurikuler_edit'), (1, 'ekstrakurikuler_delete'), (1, 'ekstrakurikuler_anggota'), (1, 'ekstrakurikuler_nilai'),
(1, 'menu_surat_menyurat'), (1, 'surat_list'),
(1, 'menu_alumni'), (1, 'alumni_list'),
(1, 'menu_buku_induk_siswa'), (1, 'buku_induk_siswa_list'),
(1, 'menu_master_lembaga'), (1, 'menu_master_tingkat'), (1, 'menu_master_rombel'), (1, 'menu_master_rombel_nonaktif'), (1, 'menu_master_mapel'), (1, 'general_settings'), (1, 'company_settings'), (1, 'login_theme'), (1, 'email_templates'),
(1, 'master_list'), (1, 'master_add'), (1, 'master_edit'), (1, 'master_delete'),
(1, 'menu_users'), (1, 'users_list'), (1, 'users_add'), (1, 'users_edit'), (1, 'users_delete'), (1, 'users_view'),
(1, 'menu_roles'), (1, 'roles_list'), (1, 'roles_add'), (1, 'roles_edit'), (1, 'permissions_list'), (1, 'permissions_add'), (1, 'permissions_edit'), (1, 'permissions_delete'),
(1, 'activity_log_list'), (1, 'activity_log_view'), (1, 'backup_db'),

-- Tenaga Administrasi Sekolah (ID 3)
(3, 'menu_dashboard'),
(3, 'menu_calon_siswa'), (3, 'calon_siswa_list'), (3, 'calon_siswa_view'), (3, 'calon_siswa_add'), (3, 'calon_siswa_edit'),
(3, 'menu_validasi_daftar_ulang'), (3, 'calon_siswa_validasi'),
(3, 'menu_lembaga'), (3, 'lembaga_list'),
(3, 'menu_data_ptk'), (3, 'ptk_list'),
(3, 'menu_kesiswaan_data_siswa'), (3, 'siswa_list'), (3, 'siswa_view'),
(3, 'menu_sinkron_dapodik'), (3, 'sync_dapodik_view'),
(3, 'menu_kedisiplinan'), (3, 'kedisiplinan_add'),
(3, 'menu_pembelajaran'), (3, 'pembelajaran_list'),
(3, 'menu_jadwal_pelajaran'),
(3, 'menu_tahun_pelajaran'), (3, 'tahun_pelajaran_list'),
(3, 'menu_ekstrakurikuler'), (3, 'ekstrakurikuler_anggota'),
(3, 'menu_surat_menyurat'), (3, 'surat_list'),
(3, 'menu_alumni'), (3, 'alumni_list'),
(3, 'menu_buku_induk_siswa'), (3, 'buku_induk_siswa_list'),
(3, 'menu_master_lembaga'), (3, 'menu_master_tingkat'), (3, 'menu_master_rombel'), (3, 'menu_master_rombel_nonaktif'), (3, 'menu_master_mapel'),
(3, 'master_list'), (3, 'master_add'), (3, 'master_edit'),

-- Guru (ID 4)
(4, 'menu_dashboard_guru'), (4, 'menu_data_siswa_guru'), (4, 'menu_pembelajaran_guru'), (4, 'menu_perangkat_guru'), 
(4, 'menu_jadwal_guru'), (4, 'menu_input_nilai_guru'), (4, 'menu_profil_ptk_guru'), 
(4, 'menu_ekstrakurikuler'), (4, 'ekstrakurikuler_anggota'), (4, 'ekstrakurikuler_nilai'),
(4, 'menu_kedisiplinan'), (4, 'kedisiplinan_add'),

-- Wakasek Kesiswaan (ID 6)
(6, 'menu_dashboard'),
(6, 'menu_calon_siswa'), (6, 'calon_siswa_list'), (6, 'calon_siswa_view'), (6, 'calon_siswa_add'), (6, 'calon_siswa_edit'), (6, 'calon_siswa_delete'), (6, 'calon_siswa_export'),
(6, 'menu_validasi_daftar_ulang'), (6, 'calon_siswa_validasi'),
(6, 'menu_aktivasi_calon_siswa'), (6, 'calon_siswa_aktivasi'),
(6, 'menu_kesiswaan_data_siswa'), (6, 'siswa_list'), (6, 'siswa_view'),
(6, 'menu_kedisiplinan'), (6, 'kedisiplinan_add'), (6, 'kedisiplinan_bk'), (6, 'kedisiplinan_delete'),
(6, 'menu_ekstrakurikuler'), (6, 'ekstrakurikuler_add'), (6, 'ekstrakurikuler_edit'), (6, 'ekstrakurikuler_delete'), (6, 'ekstrakurikuler_anggota'), (6, 'ekstrakurikuler_nilai'),
(6, 'menu_alumni'), (6, 'alumni_list'),
(6, 'menu_buku_induk_siswa'), (6, 'buku_induk_siswa_list'),
(6, 'master_list'), (6, 'users_list'),

-- Wakasek Kurikulum (ID 7)
(7, 'menu_dashboard'), 
(7, 'menu_pembelajaran'), (7, 'pembelajaran_list'), (7, 'pembelajaran_add'), (7, 'pembelajaran_edit'), (7, 'pembelajaran_delete'), 
(7, 'menu_jadwal_pelajaran'), (7, 'jadwal_pelajaran_list'),
(7, 'menu_jadwal_tidak_aktif'), (7, 'menu_perangkat_pembelajaran'), 
(7, 'menu_nilai_siswa'), (7, 'nilai_siswa_list'),
(7, 'menu_tahun_pelajaran'), (7, 'tahun_pelajaran_list'), (7, 'tahun_pelajaran_add'), (7, 'tahun_pelajaran_edit'),
(7, 'master_list'), (7, 'master_add'), (7, 'master_edit'),

-- Wakasek Sarpras (ID 8)
(8, 'menu_dashboard'), (8, 'menu_sarpras'), (8, 'menu_master_sarana'), 
(8, 'master_list'), (8, 'master_add'), (8, 'master_edit'), (8, 'master_delete'),

-- Guru BK (ID 9)
(9, 'menu_dashboard'), (9, 'menu_kesiswaan_data_siswa'), (9, 'siswa_list'), (9, 'siswa_view'), (9, 'menu_dashboard_guru'), 
(9, 'menu_kedisiplinan'), (9, 'kedisiplinan_add'), (9, 'kedisiplinan_bk'), (9, 'kedisiplinan_delete'),
(9, 'master_list'),

-- Kepala Sekolah (ID 10)
(10, 'menu_dashboard'), (10, 'menu_calon_siswa'), (10, 'calon_siswa_list'), (10, 'calon_siswa_view'),
(10, 'menu_validasi_daftar_ulang'), (10, 'menu_aktivasi_calon_siswa'),
(10, 'menu_lembaga'), (10, 'lembaga_list'),
(10, 'menu_sarpras'),
(10, 'menu_data_ptk'), (10, 'ptk_list'),
(10, 'menu_kesiswaan_data_siswa'), (10, 'siswa_list'), (10, 'siswa_view'),
(10, 'menu_kedisiplinan'),
(10, 'menu_pembelajaran'), (10, 'pembelajaran_list'),
(10, 'menu_jadwal_pelajaran'), (10, 'jadwal_pelajaran_list'),
(10, 'menu_perangkat_pembelajaran'), (10, 'menu_nilai_siswa'), (10, 'menu_tahun_pelajaran'), (10, 'menu_alumni'), 
(10, 'menu_buku_induk_siswa'), (10, 'general_settings'), (10, 'company_settings'), (10, 'login_theme'), (10, 'email_templates'),
(10, 'master_list'), (10, 'users_list'),
(10, 'menu_ekstrakurikuler');
