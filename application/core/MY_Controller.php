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

		// Redirect paksa hanya jika SATU-SATUNYA role yang dimiliki adalah Guru, 
		// kecualikan segment menu guru, profile, ekstrakurikuler, kedisiplinan, dan aksi penyimpanan profil PTK mandiri.
		$is_only_guru = (count($user_roles) === 1 && in_array('guru', $user_roles, true));
		if ($is_only_guru) {
			$segment1 = $this->uri->segment(1);
			$segment2 = $this->uri->segment(2);
			$allowed_segments = ['guru', 'profile', 'ekstrakurikuler', 'kedisiplinan'];
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
			$is_allowed = in_array($segment1, $allowed_segments, true) || ($segment1 === 'ptk' && in_array($segment2, $allowed_ptk_methods, true));
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

	



}



/* End of file My_Controller.php */

/* Location: ./application/core/My_Controller.php */
