<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class MY_Controller extends CI_Controller {



	public $page_data;



	/**

	  * Extends by most of controllers not all controllers

	  */



	public function __construct()

	{



		parent::__construct();



		if( !empty($this->db->username) && !empty($this->db->hostname) && !empty($this->db->database) ){ }else{

			$this->users_model->logout();

			die('Database is not configured');

		}

		$this->initializeFingerprintTables();

		

		date_default_timezone_set( setting('timezone') );

		

		$this->config->set_item('language', getUserlang()); 



		$this->lang->load([

			'basic',

			

		], getUserlang() );



		if(!is_logged()){

			redirect('login','refresh');

		}

		$userId = logged('id');
		$user_roles = [];
		if ($userId && $this->db->table_exists('user_roles')) {
			foreach ($this->db->get_where('user_roles', ['user_id' => $userId])->result() as $ur) {
				$r_row = $this->db->get_where('roles', ['id' => $ur->role_id])->row();
				if ($r_row) {
					$user_roles[] = strtolower((string) $r_row->title);
				}
			}
		}
		
		// Fallback ke role tunggal jika data user_roles kosong
		if (empty($user_roles)) {
			$role = $this->db->get_where('roles', ['id' => logged('role')])->row();
			if ($role) {
				$user_roles[] = strtolower((string) $role->title);
			}
		}

		// Cek apakah pengguna memiliki peran Admin
		$is_admin_user = false;
		foreach ($user_roles as $r) {
			$r_clean = trim(strtolower((string) $r));
			if ($r_clean === 'admin' || $r_clean === 'administrator' || $r_clean === 'superadmin' || strpos($r_clean, 'admin') !== false) {
				$is_admin_user = true;
				break;
			}
		}

		// Redirect paksa untuk seluruh pengguna non-Admin (Guru, Guru BK, Wakasek, dll) ke Dashboard Guru, 
		// kecualikan segment portal guru, profile, modul berizin (seperti jadwal_pelajaran, kedisiplinan, dll), dan aksi PTK mandiri.
		if (!$is_admin_user) {
			$segment1 = $this->uri->segment(1);
			$segment2 = $this->uri->segment(2);

			$allowed_segments = [
				'guru', 
				'profile', 
				'ekstrakurikuler', 
				'kedisiplinan', 
				'pencetakan', 
				'jadwal_pelajaran', 
				'alumni', 
				'buku_induk_siswa', 
				'surat',
				'siswa',
				'ptk',
				'pembelajaran',
				'perangkat_pembelajaran',
				'nilai_siswa',
				'tugas_tambahan_ptk',
				'tahun_pelajaran',
				'master',
				'master_tugas_tambahan',
				'sarpras'
			];

			$allowed_ptk_methods = [
				'ptkUpdate',
				'ptkPendidikanSimpan',
				'ptkPendidikanUpdate',
				'ptkPendidikanHapus',
				'ptkPendidikanUpload',
				'ptkDokumenSimpan',
				'ptkDokumenUpdate',
				'ptkDokumenHapus',
				'getKabupaten',
				'getKecamatan',
				'getKelurahan',
				'ptkJenisDokumenSimpan'
			];

			$has_dynamic_permission = false;
			if (!empty($segment1)) {
				$has_dynamic_permission = hasPermissions($segment1) 
					|| hasPermissions('menu_' . $segment1) 
					|| hasPermissions($segment1 . '_list') 
					|| hasPermissions($segment1 . '_view') 
					|| hasPermissions($segment1 . '_edit');
			}

			$is_allowed = in_array($segment1, $allowed_segments, true) 
				|| $has_dynamic_permission 
				|| ($segment1 === 'ptk' && in_array($segment2, $allowed_ptk_methods, true));

			if (!$is_allowed) {
				redirect('guru', 'refresh');
			}
		}



		$this->page_data['url'] = (object) [

			'assets' => assets_url().'/'

		];



		$this->page_data['app'] = (object) [

			'site_title' => setting('company_name')

		];



		$this->page_data['page'] = (object) [

			'title' => 'Dashboard',

			'menu' => 'dashboard',

			'submenu' => '',

		];



	}



	public function change_language()

	{

		// die(var_dump('test_func'));

	}
	private function initializeFingerprintTables()
	{
		$this->load->database();
		
		// 1. Inisialisasi kolom tabel siswa
		if (!$this->db->field_exists('pin_fingerprint', 'siswa')) {
			$this->db->query("ALTER TABLE siswa ADD COLUMN pin_fingerprint INT DEFAULT NULL UNIQUE");
		}
		
		// 2. Inisialisasi kolom tabel ptk
		if (!$this->db->field_exists('pin_fingerprint', 'ptk')) {
			$this->db->query("ALTER TABLE ptk ADD COLUMN pin_fingerprint INT DEFAULT NULL UNIQUE");
		}

		// 3. Inisialisasi tabel presensi_harian (versi baru untuk sync sesi)
		$this->db->query("CREATE TABLE IF NOT EXISTS presensi_harian (
		  id_presensi INT AUTO_INCREMENT PRIMARY KEY,
		  tipe_user ENUM('siswa', 'ptk') NOT NULL,
		  id_user INT NOT NULL,
		  pin INT NOT NULL,
		  tanggal DATE NOT NULL,
		  jam_scan TIME DEFAULT NULL,
		  sesi ENUM('dhuha', 'dzuhur', 'other') DEFAULT 'other',
		  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  UNIQUE KEY user_tanggal_sesi (tipe_user, id_user, tanggal, sesi),
		  INDEX idx_pin_tanggal (pin, tanggal)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

		// 4. Inisialisasi tabel fingerprint_tasks
		$this->db->query("CREATE TABLE IF NOT EXISTS fingerprint_tasks (
		  id INT AUTO_INCREMENT PRIMARY KEY,
		  action ENUM('SET_USER', 'DEL_USER') NOT NULL,
		  pin INT NOT NULL,
		  nama VARCHAR(150) DEFAULT NULL,
		  status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
		  attempts INT DEFAULT 0,
		  error_message TEXT DEFAULT NULL,
		  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  INDEX idx_status (status)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

		// Pendaftaran category parent (Level 1)
		$parent_id = 0;
		$check_parent = $this->db->get_where('permissions', ['code' => 'menu_presensi_group'])->row();
		if (!$check_parent) {
			$this->db->insert('permissions', [
				'title' => 'Presensi & Kehadiran',
				'code' => 'menu_presensi_group',
				'level' => 1,
				'parent_id' => 0
			]);
			$parent_id = $this->db->insert_id();
		} else {
			$parent_id = $check_parent->id;
			// Pastikan level dan parent_id ter-update jika salah
			if ($check_parent->level != 1 || $check_parent->parent_id != 0) {
				$this->db->where('id', $parent_id)->update('permissions', ['level' => 1, 'parent_id' => 0]);
			}
		}

		// Pendaftaran sub-menu (Level 2): menu_presensi
		$check_perm = $this->db->get_where('permissions', ['code' => 'menu_presensi'])->row();
		if (!$check_perm) {
			$this->db->insert('permissions', [
				'title' => 'Akses Menu Presensi',
				'code' => 'menu_presensi',
				'level' => 2,
				'parent_id' => $parent_id
			]);
		} else {
			// Perbarui level dan parent_id agar tampil di hierarchy tree
			if ($check_perm->level != 2 || $check_perm->parent_id != $parent_id) {
				$this->db->where('id', $check_perm->id)->update('permissions', ['level' => 2, 'parent_id' => $parent_id, 'title' => 'Akses Menu Presensi']);
			}
		}

		// Pendaftaran sub-menu (Level 2): presensi_view
		$check_perm2 = $this->db->get_where('permissions', ['code' => 'presensi_view'])->row();
		if (!$check_perm2) {
			$this->db->insert('permissions', [
				'title' => 'Akses Lihat Detail Presensi',
				'code' => 'presensi_view',
				'level' => 2,
				'parent_id' => $parent_id
			]);
		} else {
			// Perbarui level dan parent_id agar tampil di hierarchy tree
			if ($check_perm2->level != 2 || $check_perm2->parent_id != $parent_id) {
				$this->db->where('id', $check_perm2->id)->update('permissions', ['level' => 2, 'parent_id' => $parent_id, 'title' => 'Akses Lihat Detail Presensi']);
			}
		}

		// Daftarkan permissions presensi HANYA untuk Admin dan Tenaga Administrasi.
		// Role lain (Guru, Wakasek, dll) harus diaktifkan manual via manajemen role.
		$default_presensi_roles = [1, 3]; // 1=Admin, 3=Tenaga Administrasi Sekolah
		foreach ($default_presensi_roles as $r_id) {
			$check_role_perm = $this->db->get_where('role_permissions', ['role' => $r_id, 'permission' => 'menu_presensi'])->row();
			if (!$check_role_perm) {
				$this->db->insert('role_permissions', ['role' => $r_id, 'permission' => 'menu_presensi']);
			}
			$check_role_perm2 = $this->db->get_where('role_permissions', ['role' => $r_id, 'permission' => 'presensi_view'])->row();
			if (!$check_role_perm2) {
				$this->db->insert('role_permissions', ['role' => $r_id, 'permission' => 'presensi_view']);
			}
		}
	}
}



/* End of file My_Controller.php */

/* Location: ./application/core/My_Controller.php */
