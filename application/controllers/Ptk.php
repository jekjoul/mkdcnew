<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ptk extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function ptk()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Daftar PTK';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('ptk/v_ptk_list', $this->page_data);
	}

	public function ptkDetail()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Detail PTK';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('ptk/v_ptk_detail', $this->page_data);
	}

    public function ptkTambah()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Tambah';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('ptk/v_ptk_add', $this->page_data);
	}

	public function ptkNonaktif()
	{
		$this->page_data['page']->title = 'PTK Nonaktif';
		$this->page_data['page']->subtitle = 'Daftar PTK Nonaktif';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('ptk/v_ptk_nonaktif_list', $this->page_data);
	}




}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */