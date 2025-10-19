<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sarpras extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function tanah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/tanah';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->subtitleUrl = 'sarpras/tanah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->page_data['tanah'] = $this->sarpras_model->getAllTanah();
		$this->load->view('sarpras/v_tanah_list', $this->page_data);
	}

	public function tanahDetail()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/tanah';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->subtitleUrl = 'sarpras/tanah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_tanah_list', $this->page_data);
	}

	public function tanahTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/tanah';
		$this->page_data['page']->subtitle = 'Tanah';
		$this->page_data['page']->subtitleUrl = 'sarpras/tanah';
		$this->page_data['page']->subsubtitle = 'Tambah';
		$this->page_data['page']->subsubtitleUrl = 'sarpras/tanahTabah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_tanah_add', $this->page_data);
	}

	public function tanahSimpan()
	{
		$data = array(
			'nomor_sertifikat' => $this->input->post('nomor_sertifikat'),
			'atas_nama' => $this->input->post('atas_nama'),
			'luas' => $this->input->post('luas'),
			'tgl_pembukuan' => $this->input->post('tgl_pembukuan'),
			'no_surat_ukur' => $this->input->post('no_surat_ukur'),
			'status' => $this->input->post('status'),
			'batas_utara' => $this->input->post('batas_utara'),
			'batas_barat' => $this->input->post('batas_barat'),
			'batas_selatan' => $this->input->post('batas_selatan'),
			'batas_timur' => $this->input->post('batas_timur'),
		);
		$this->db->insert('tanah', $data);
		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data tanah baru', logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Input Tanah Berhasil');
		redirect('sarpras/tanah');
	}

	public function tanahUpdate()
	{
		$id = $this->uri->segment(3);
		$no_sertifikat = $this->input->post('nomor_sertifikat');
		$data = array(
			'nomor_sertifikat' => $this->input->post('nomor_sertifikat'),
			'atas_nama' => $this->input->post('atas_nama'),
			'luas' => $this->input->post('luas'),
			'tgl_pembukuan' => $this->input->post('tgl_pembukuan'),
			'no_surat_ukur' => $this->input->post('no_surat_ukur'),
			'status' => $this->input->post('status'),
			'batas_utara' => $this->input->post('batas_utara'),
			'batas_barat' => $this->input->post('batas_barat'),
			'batas_selatan' => $this->input->post('batas_selatan'),
			'batas_timur' => $this->input->post('batas_timur'),
		);
		$this->db->where('id_tanah', $id);
		$this->db->update('tanah', $data);

		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data tanah ' . $no_sertifikat, logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Update Tanah Berhasils');
		redirect('sarpras/tanah');
	}

	public function tanahBerkasUpdate($id)
	{
		$berkas = $this->input->post('berkas');
		$config['upload_path']      = './uploads/tanah_berkas/'; // Folder penyimpanan (harus bisa ditulisi/writable)
		$config['allowed_types']    = 'pdf'; // HANYA mengizinkan file PDF
		// $config['max_size']         = 2048; // Ukuran maksimum file dalam KB (misalnya 2MB)
		$config['encrypt_name']     = FALSE; // Ganti nama file untuk keamanan
		$config['overwrite']        = TRUE; //Mengaktifkan mode menimpa (overwrite)
		$nama = "tanah-" . $id;
		$config['file_name']        = $nama; //Menggunakan nama file kustom yang sudah ditentukan

		// Inisialisasi library upload dengan konfigurasi
		$this->upload->initialize($config);

		// Pastikan direktori ada. Jika tidak, coba buat.
		if (! is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}


		// 2. Lakukan proses unggahan
		// 'userfile' harus sesuai dengan atribut 'name' dari input file di form Anda
		if (! $this->upload->do_upload('berkas')) {
			// Unggahan GAGAL
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('alert-type', 'warning');
			$this->session->set_flashdata('alert', $error);

			// Redirect kembali ke halaman form
			redirect('sarpras/tanah');
		} else {
			// Unggahan BERHASIL
			$data = $this->upload->data();

			// Lakukan sesuatu dengan data file, misalnya simpan ke database:
			$file_name = $data['file_name'];
			// $file_path = $data['full_path'];

			$data = array(
				'berkas' => $file_name
			);
			$this->db->where('id_tanah', $id);
			$this->db->update('tanah', $data);

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Update Berkas Berhasils');

			// Redirect kembali ke halaman form
			redirect('sarpras/tanah');
		}
	}

	public function bangunan()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/bangunan';
		$this->page_data['page']->subtitle = 'Bangunan';
		$this->page_data['page']->subtitleUrl = 'sarpras/bangunan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->page_data['tanah'] = $this->sarpras_model->getAllbangunan();
		$this->load->view('sarpras/v_bangunan_list', $this->page_data);
	}

	public function bangunanTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/bangunan';
		$this->page_data['page']->subtitle = 'Bangunan';
		$this->page_data['page']->subtitleUrl = 'sarpras/bangunan';
		$this->page_data['page']->subsubtitle = 'Tambah';
		$this->page_data['page']->subsubtitleUrl = 'sarpras/bangunanTambah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_bangunan_add', $this->page_data);
	}

	public function ruangan()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/ruangan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_ruangan_list', $this->page_data);
	}

	public function ruanganTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subsubtitle = 'Tambah';
		$this->page_data['page']->subsubtitleUrl = 'sarpras/ruanganTambah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_ruangan_add', $this->page_data);
	}

	public function alat()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/alat';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/alat';

		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_alat_list', $this->page_data);
	}

	public function alatTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/alat';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/alat';
		$this->page_data['page']->subsubtitle = 'Tambah';
		$this->page_data['page']->subsubtitleUrl = 'sarpras/alatTambah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->load->view('sarpras/v_alat_add', $this->page_data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */