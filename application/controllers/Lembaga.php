<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lembaga extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function index()
	{
		$this->page_data['page']->title = 'Lembaga';
		$this->page_data['page']->titleUrl = 'lembaga';
		$this->page_data['page']->subtitle = 'Daftar Lembaga';
		$this->page_data['page']->subtitleUrl = 'lembaga';
		$this->page_data['page']->icon = 'solar:home-linear';
		$this->load->view('lembaga/v_lembaga_list', $this->page_data);
	}

	public function detail()
	{
		$this->page_data['page']->title = 'Lembaga';
		$this->page_data['page']->titleUrl = 'lembaga';
		$this->page_data['page']->subtitle = 'Detail Lembaga';
		$this->page_data['page']->subtitleUrl = 'lembaga/detail';
		$this->page_data['page']->icon = 'solar:home-linear';
		$this->load->view('lembaga/v_lembaga_detail', $this->page_data);
	}
}
	

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */