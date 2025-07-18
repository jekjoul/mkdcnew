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
		$this->page_data['page']->menu = 'sarpras';
		$this->page_data['page']->submenu = 'sarpras_tanah';
		$this->load->view('sarpras/v_tanah', $this->page_data);
	}

    public function tanahList()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->menu = 'sarpras';
		$this->page_data['page']->submenu = 'sarpras_tanah';
		$this->load->view('sarpras/v_tanah_list', $this->page_data);
	}





}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */