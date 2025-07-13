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
		$this->page_data['page']->menu = 'lembaga';
		$this->page_data['page']->submenu = 'lembaga_list';
		$this->load->view('lembaga/v_lembaga_list', $this->page_data);
	}

	public function detailLembaga()
	{
		$this->page_data['page']->title = 'Lembaga';
		$this->page_data['page']->menu = 'lembaga';
		$this->page_data['page']->submenu = 'lembaga_list';
		$this->load->view('lembaga/v_lembaga_view', $this->page_data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */