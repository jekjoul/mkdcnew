<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function index()
	{
		$this->page_data['page']->title = 'Dashboard';
		$this->page_data['page']->titleUrl = 'dashboard';
		$this->page_data['page']->subtitle = 'Dashboard';
		$this->page_data['page']->subtitleUrl = 'dashboard';
		$this->page_data['page']->icon = 'solar:home-angle-2-linear';
		
		$this->load->view('dashboard', $this->page_data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */