<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lembaga extends MY_Controller
{

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

		$lembaga = $this->lembaga_model->getAllLembaga();

		// Mengambil jumlah siswa aktif per lembaga dalam satu query untuk efisiensi
		$counts = $this->db->select('p.id_lembaga, COUNT(DISTINCT ps.peserta_didik_id) as total')
			->from('pembelajaran_siswa ps')
			->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran')
			->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran')
			->where('tp.status', 'Aktif')
			->group_by('p.id_lembaga')
			->get()->result_array();
		$student_counts = array_column($counts, 'total', 'id_lembaga');

		foreach ($lembaga as $row) {
			$row->total_siswa = isset($student_counts[$row->id_lembaga]) ? $student_counts[$row->id_lembaga] : 0;
		}

		$this->page_data['lembaga'] = $lembaga;
		$this->load->view('lembaga/v_lembaga_list', $this->page_data);
	}

	public function detail()
	{
		$id = $this->uri->segment(3);
		$this->page_data['page']->title = 'Lembaga';
		$this->page_data['page']->titleUrl = 'lembaga';
		$this->page_data['page']->subtitle = 'Detail Lembaga';
		$this->page_data['page']->subtitleUrl = 'lembaga/detail';
		$this->page_data['page']->icon = 'solar:home-linear';

		$lembaga = $this->lembaga_model->getDetailLembaga($id);
		if ($lembaga) {
			// Mengambil nama Kepala Sekolah dari tabel PTK
			$kepsek = $this->db->select('nama_ptk')->get_where('ptk', ['id_ptk' => $lembaga->id_ptk_kepsek])->row();
			$lembaga->nama_kepsek = $kepsek ? $kepsek->nama_ptk : '-';
		}

		$this->page_data['lembaga'] = $lembaga;
		$this->load->view('lembaga/v_lembaga_detail', $this->page_data);
	}
}
	

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */