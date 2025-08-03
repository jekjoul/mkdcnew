<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sarpras extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function tanah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_tanah_list', $this->page_data);
	}

	public function tanahDetail()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_tanah_list', $this->page_data);
	}

	public function tanahTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_tanah_add', $this->page_data);
	}

	public function bangunan()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Bangunan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_bangunan_list', $this->page_data);
	}

	public function bangunanTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Bangunan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_bangunan_add', $this->page_data);
	}

	public function ruangan()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_ruangan_list', $this->page_data);
	}
 




}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */