<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lembaga extends MY_Controller
{
	public $jenis_lembaga_options = [
		'Sekolah Formal',
		'Sekolah Kesetaraan',
		'Pondok Pesantren',
		'Madrasah Diniyah',
		'Yayasan',
		'Majelis Ta\'lim'
	];

	public function __construct()
	{
		parent::__construct();
		$this->ensureJenisLembagaColumn();
	}

	private function ensureJenisLembagaColumn()
	{
		if ($this->db->table_exists('lembaga') && !$this->db->field_exists('jenis_lembaga', 'lembaga')) {
			$this->db->query("ALTER TABLE `lembaga` ADD `jenis_lembaga` VARCHAR(100) NULL AFTER `nama_lembaga_singkat`");
		}
	}

	public function index()
	{
        ifPermissions('lembaga_list');
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
		$this->page_data['jenis_lembaga_options'] = $this->jenis_lembaga_options;
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

		$this->page_data['ptk'] = $this->db->order_by('nama_ptk', 'ASC')->get('ptk')->result();
		$this->page_data['lembaga'] = $lembaga;
		$this->page_data['jenis_lembaga_options'] = $this->jenis_lembaga_options;
		$this->load->view('lembaga/v_lembaga_detail', $this->page_data);
	}

	public function update($id)
	{
		postAllowed();
		ifPermissions('lembaga_edit');

		$data = [
			'nama_lembaga' => $this->input->post('nama_lembaga'),
			'nama_lembaga_singkat' => $this->input->post('nama_lembaga_singkat'),
			'jenis_lembaga' => $this->input->post('jenis_lembaga'),
			'npsn' => $this->input->post('npsn'),
			'bentuk_pendidikan' => $this->input->post('bentuk_pendidikan'),
			'status' => $this->input->post('status'),
			'akreditasi' => $this->input->post('akreditasi'),
			'no_sk_akreditasi' => $this->input->post('no_sk_akreditasi'),
			'alamat' => $this->input->post('alamat'),
			'rt' => $this->input->post('rt'),
			'rw' => $this->input->post('rw'),
			'kelurahan' => $this->input->post('kelurahan'),
			'kecamatan' => $this->input->post('kecamatan'),
			'kabupaten' => $this->input->post('kabupaten'),
			'provinsi' => $this->input->post('provinsi'),
			'koordinat' => $this->input->post('koordinat'),
			'telepon' => $this->input->post('telepon'),
			'email' => $this->input->post('email'),
			'website' => $this->input->post('website'),
			'instagram' => $this->input->post('instagram'),
			'tiktok' => $this->input->post('tiktok'),
			'youtube' => $this->input->post('youtube'),
			'id_ptk_kepsek' => $this->input->post('id_ptk_kepsek') ?: null
		];

		// Handle logo upload
		if (!empty($_FILES['logo']['name'])) {
			$config['upload_path'] = './uploads/logo_lembaga/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['file_name'] = 'logo_' . $id . '_' . time();
			
			// Create directory if not exists
			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->load->library('upload');
			$this->upload->initialize($config);
			if ($this->upload->do_upload('logo')) {
				$upload_data = $this->upload->data();
				$data['logo'] = $upload_data['file_name'];
			}
		}

		$this->db->where('id_lembaga', $id);
		if ($this->db->update('lembaga', $data)) {
			$this->activity_model->add(logged('name') . ' mengubah data lembaga: ' . $data['nama_lembaga'], logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Data Lembaga berhasil diperbarui');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Gagal memperbarui data Lembaga');
		}

		redirect('lembaga/detail/' . $id);
	}
}