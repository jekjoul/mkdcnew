<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function permission_denied()
	{
		$this->page_data['page'] = (object) [
			'title' => 'Akses Ditolak',
			'titleUrl' => 'dashboard',
			'subtitle' => 'Peringatan Hak Akses',
			'subtitleUrl' => 'errors/permission_denied',
			'icon' => 'solar:shield-warning-bold'
		];
		$this->load->view('errors/html/error_403_permission', $this->page_data);
	}

	/**
	 * Halaman ditampilkan ketika tidak ada koneksi internet
	 */
	public function offline()
	{
		$this->load->view('errors/offline');
	}

}

/* End of file Errors.php */
/* Location: ./application/controllers/Errors.php */