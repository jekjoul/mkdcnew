-- ======================================================================
-- SCRIPT UPDATE DATABASE PRODUCTION - FITUR MASTER TUGAS TAMBAHAN & PTK
-- ======================================================================

-- 1. PEMBUATAN TABEL BARU
-- 1.1 Tabel Master Tugas Tambahan
CREATE TABLE IF NOT EXISTS `master_tugas_tambahan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jenis` VARCHAR(100) NOT NULL,
  `nama` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 1.2 Tabel Tugas Tambahan PTK
CREATE TABLE IF NOT EXISTS `tugas_tambahan_ptk` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_ptk` INT NOT NULL,
  `id_tugas_tambahan` INT NOT NULL,
  `no_sk` VARCHAR(255) NULL,
  `tgl_sk` DATE NULL,
  `tmt` DATE NULL,
  `tst` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2. PENDAFTARAN PERMISSIONS BARU (DENGAN SUBQUERY DINAMIS)
-- Bersihkan jika sebelumnya sudah ada untuk mencegah duplikasi
DELETE FROM permissions WHERE code IN (
  'menu_master_tugas_tambahan', 'master_tugas_tambahan_list', 'master_tugas_tambahan_add', 'master_tugas_tambahan_edit', 'master_tugas_tambahan_delete',
  'menu_tugas_tambahan_ptk', 'tugas_tambahan_ptk_list', 'tugas_tambahan_ptk_add', 'tugas_tambahan_ptk_edit', 'tugas_tambahan_ptk_delete'
);

-- 2.1 Master Tugas Tambahan
INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'group_master') as t), 2, 'Master Tugas Tambahan', 'menu_master_tugas_tambahan');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_master_tugas_tambahan') as t), 3, 'Melihat Master Tugas Tambahan', 'master_tugas_tambahan_list');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_master_tugas_tambahan') as t), 3, 'Menambah Master Tugas Tambahan', 'master_tugas_tambahan_add');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_master_tugas_tambahan') as t), 3, 'Mengubah Master Tugas Tambahan', 'master_tugas_tambahan_edit');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_master_tugas_tambahan') as t), 3, 'Menghapus Master Tugas Tambahan', 'master_tugas_tambahan_delete');

-- 2.2 Tugas Tambahan PTK
INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'group_pembelajaran') as t), 2, 'Tugas Tambahan PTK', 'menu_tugas_tambahan_ptk');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_tugas_tambahan_ptk') as t), 3, 'Melihat Tugas Tambahan PTK', 'tugas_tambahan_ptk_list');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_tugas_tambahan_ptk') as t), 3, 'Menambah Tugas Tambahan PTK', 'tugas_tambahan_ptk_add');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_tugas_tambahan_ptk') as t), 3, 'Mengubah Tugas Tambahan PTK', 'tugas_tambahan_ptk_edit');

INSERT INTO permissions (parent_id, level, title, code) 
VALUES ((SELECT id FROM (SELECT id FROM permissions WHERE code = 'menu_tugas_tambahan_ptk') as t), 3, 'Menghapus Tugas Tambahan PTK', 'tugas_tambahan_ptk_delete');


-- 3. PENDAFTARAN HAK AKSES ROLE (ROLE PERMISSIONS)
-- Bersihkan hak akses lama untuk menghindari duplicate key
DELETE FROM role_permissions WHERE (role = 1 AND permission IN (
  'menu_master_tugas_tambahan', 'master_tugas_tambahan_list', 'master_tugas_tambahan_add', 'master_tugas_tambahan_edit', 'master_tugas_tambahan_delete',
  'menu_tugas_tambahan_ptk', 'tugas_tambahan_ptk_list', 'tugas_tambahan_ptk_add', 'tugas_tambahan_ptk_edit', 'tugas_tambahan_ptk_delete',
  'perangkat_pembelajaran_list', 'perangkat_pembelajaran_add', 'perangkat_pembelajaran_edit', 'perangkat_pembelajaran_delete'
)) OR (role = 7 AND permission IN (
  'menu_tugas_tambahan_ptk', 'tugas_tambahan_ptk_list', 'tugas_tambahan_ptk_add', 'tugas_tambahan_ptk_edit', 'tugas_tambahan_ptk_delete'
));

-- 3.1 Pendaftaran Role Admin (Role ID: 1)
INSERT INTO role_permissions (role, permission) VALUES 
(1, 'menu_master_tugas_tambahan'),
(1, 'master_tugas_tambahan_list'),
(1, 'master_tugas_tambahan_add'),
(1, 'master_tugas_tambahan_edit'),
(1, 'master_tugas_tambahan_delete'),
(1, 'menu_tugas_tambahan_ptk'),
(1, 'tugas_tambahan_ptk_list'),
(1, 'tugas_tambahan_ptk_add'),
(1, 'tugas_tambahan_ptk_edit'),
(1, 'tugas_tambahan_ptk_delete'),
(1, 'perangkat_pembelajaran_list'),
(1, 'perangkat_pembelajaran_add'),
(1, 'perangkat_pembelajaran_edit'),
(1, 'perangkat_pembelajaran_delete');

-- 3.2 Pendaftaran Role Tenaga Administrasi Sekolah (Role ID: 7)
INSERT INTO role_permissions (role, permission) VALUES 
(7, 'menu_tugas_tambahan_ptk'),
(7, 'tugas_tambahan_ptk_list'),
(7, 'tugas_tambahan_ptk_add'),
(7, 'tugas_tambahan_ptk_edit'),
(7, 'tugas_tambahan_ptk_delete');
