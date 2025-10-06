<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends MY_Controller {

	public function __construct()
	  {
	    parent::__construct();
	  }

	public function all()
	{
		$this->page_data['page']->title = 'Siswa';
		$this->page_data['page']->titleUrl = 'siswa/all';
		$this->page_data['page']->subtitle = 'Daftar Siswa';
		$this->page_data['page']->subtitleUrl = 'siswa/all';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_list', $this->page_data);
	}

	public function detail()
	{
		$this->page_data['page']->title = 'Siswa';
		$this->page_data['page']->titleUrl = 'siswa/all';
		$this->page_data['page']->subtitle = 'Mirna Rahmania';
		$this->page_data['page']->subtitleUrl = 'siswa/detail';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_detail', $this->page_data);
	}

    public function siswaAdd()
	{
		$this->page_data['page']->title = 'Siswa';
		$this->page_data['page']->titleUrl = 'siswa/all';
		$this->page_data['page']->subtitle = 'Tambah';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_add', $this->page_data);
	}

	public function rekamDidik()
	{
		$this->page_data['page']->title = 'Siswa';
		$this->page_data['page']->titleUrl = 'siswa/all';
		$this->page_data['page']->subtitle = 'Mirna Rahmania';
		$this->page_data['page']->subtitleUrl = 'siswa/detail';
		$this->page_data['page']->subsubtitle = 'Rekam Didik';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->load->view('siswa/v_siswa_rekam_didik', $this->page_data);
	}

	public function inputCatatan()
	{
		
		$this->load->view('siswa/v_siswa_input_catatan');
	}




}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */