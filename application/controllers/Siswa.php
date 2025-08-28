<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function all()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Daftar PTK';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_list', $this->page_data);
	}

	public function detail()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Detail PTK';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_detail', $this->page_data);
	}

    public function siswaAdd()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Tambah';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_add', $this->page_data);
	}




}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */