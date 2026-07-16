-- Database Changes - 15 Juli 2026
-- Pembaruan Permissions dan Role Mappings untuk Fitur Generate NIPD, Generate NIY, serta Edit Inline.

-- 1. Pendaftaran Permission Baru dengan Resolusi Parent ID Dinamis (Aman jika di-run berulang kali)
INSERT INTO `permissions` (`code`, `title`, `parent_id`, `level`)
SELECT 'menu_generate_nipd', 'Generate NIPD', id, 2 FROM `permissions` WHERE `code` = 'group_kesiswaan'
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `permissions` (`code`, `title`, `parent_id`, `level`)
SELECT 'menu_generate_niy', 'Generate NIY', id, 2 FROM `permissions` WHERE `code` = 'group_kepegawaian'
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `permissions` (`code`, `title`, `parent_id`, `level`)
SELECT 'menu_edit_inline_siswa', 'Edit Inline Siswa', id, 2 FROM `permissions` WHERE `code` = 'group_kesiswaan'
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `permissions` (`code`, `title`, `parent_id`, `level`)
SELECT 'menu_edit_inline_ptk', 'Edit Inline PTK', id, 2 FROM `permissions` WHERE `code` = 'group_kepegawaian'
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);


-- 2. Pemetaan Hak Akses Baru ke Role Terkait (Menggunakan INSERT IGNORE agar tidak duplikat)
-- Role 1 (Admin): Mendapatkan akses ke semua fitur baru
INSERT IGNORE INTO `role_permissions` (`role`, `permission`) VALUES
(1, 'menu_generate_nipd'),
(1, 'menu_generate_niy'),
(1, 'menu_edit_inline_siswa'),
(1, 'menu_edit_inline_ptk');

-- Role 6 (Wakasek Kesiswaan): Mendapatkan akses ke fitur Kesiswaan (Generate NIPD & Edit Inline Siswa)
INSERT IGNORE INTO `role_permissions` (`role`, `permission`) VALUES
(6, 'menu_generate_nipd'),
(6, 'menu_edit_inline_siswa');

-- Role 3 (TAS): Mendapatkan akses ke fitur Kepegawaian (Generate NIY & Edit Inline PTK)
INSERT IGNORE INTO `role_permissions` (`role`, `permission`) VALUES
(3, 'menu_generate_niy'),
(3, 'menu_edit_inline_ptk');
