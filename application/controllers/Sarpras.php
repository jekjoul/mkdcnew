<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sarpras extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public $tanah = 'sarpras_tanah';
	public $bangunan = 'sarpras_bangunan';
	public $ruangan = 'sarpras_ruangan';
	public $alat = 'sarpras_sarana';


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
		$this->db->insert($this->tanah, $data);
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
		$this->db->update($this->tanah, $data);

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
			$this->db->update($this->tanah, $data);

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
		$this->page_data['bangunan'] = $this->sarpras_model->getAllbangunan();
		$this->page_data['tanah'] = $this->sarpras_model->getAllTanah();
		$this->load->view('sarpras/v_bangunan_list', $this->page_data);
	}

	public function bangunanUpdate()
	{
		$id = $this->uri->segment(3);
		$data = array(
			'nama_bangunan' => $this->input->post('nama_bangunan'),
			'id_tanah' => $this->input->post('tanah'),
			'panjang' => $this->input->post('panjang'),
			'lebar' => $this->input->post('lebar'),
			'luas_tapak' => $this->input->post('luas_tapak'),
			'tgl_pendirian' => $this->input->post('tgl_pendirian'),
			'no_pbg' => $this->input->post('no_pbg'),
			'status_bangunan' => $this->input->post('status_bangunan'),
		);
		$this->db->where('id_bangunan', $id);
		$this->db->update($this->bangunan, $data);

		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data tanah ' . $no_sertifikat, logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Update Bangunan Berhasil');
		redirect('sarpras/bangunan');
	}

	public function bangunanBerkasUpdate($id)
	{
		$berkas = $this->input->post('berkas');
		$config['upload_path']      = './uploads/bangunan_berkas/'; // Folder penyimpanan (harus bisa ditulisi/writable)
		$config['allowed_types']    = 'pdf'; // HANYA mengizinkan file PDF
		// $config['max_size']         = 2048; // Ukuran maksimum file dalam KB (misalnya 2MB)
		$config['encrypt_name']     = FALSE; // Ganti nama file untuk keamanan
		$config['overwrite']        = TRUE; //Mengaktifkan mode menimpa (overwrite)
		$nama = "bangunan-" . $id;
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
			redirect('sarpras/bangunan');
		} else {
			// Unggahan BERHASIL
			$data = $this->upload->data();

			// Lakukan sesuatu dengan data file, misalnya simpan ke database:
			$file_name = $data['file_name'];
			// $file_path = $data['full_path'];

			$data = array(
				'berkas_bangunan' => $file_name
			);
			$this->db->where('id_bangunan', $id);
			$this->db->update($this->bangunan, $data);

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Update Berkas Berhasil');

			// Redirect kembali ke halaman form
			redirect('sarpras/bangunan');
		}
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
		$this->page_data['tanah'] = $this->sarpras_model->getAllTanah();
		$this->load->view('sarpras/v_bangunan_add', $this->page_data);
	}

	public function bangunanSimpan()
	{
		$data = array(
			'nama_bangunan' => $this->input->post('nama_bangunan'),
			'id_tanah' => $this->input->post('tanah'),
			'panjang' => $this->input->post('panjang'),
			'lebar' => $this->input->post('lebar'),
			'luas_tapak' => $this->input->post('luas_tapak'),
			'tgl_pendirian' => $this->input->post('tgl_pendirian'),
			'no_pbg' => $this->input->post('no_pbg'),
			'status_bangunan' => $this->input->post('status_bangunan'),
		);
		$this->db->insert($this->bangunan, $data);
		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data bangunan baru', logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Input Bangunan Berhasil');
		redirect('sarpras/bangunan');
	}

	public function ruangan()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/ruangan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->page_data['bangunan'] = $this->sarpras_model->getBangunan();
		$this->page_data['jenis_ruangan'] = $this->master_model->getJenisRuanganAktif();
		$this->page_data['ruangan'] = $this->sarpras_model->getAllRuangan();
		$this->load->view('sarpras/v_ruangan_list', $this->page_data);
	}

	public function ruanganDetail($id)
	{
		$this->page_data['row'] = $this->sarpras_model->getDetailRuangan($id);
		$this->page_data['jenis_sarana'] = $this->sarpras_model->getJenisSaranaHave();
		$this->page_data['sarana'] = $this->sarpras_model->getRuanganSarana($id);
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subtitle = 'Ruangan';
		$this->page_data['page']->subtitleUrl = 'sarpras/ruangan';
		$this->page_data['page']->subsubtitle = $this->page_data['row']->nama_ruangan;
		$this->page_data['page']->subsubtitleUrl = 'sarpras/ruangan';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';

		$this->load->view('sarpras/v_ruangan_detail', $this->page_data);
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
		$this->page_data['bangunan'] = $this->sarpras_model->getBangunan();
		$this->page_data['jenis_ruangan'] = $this->master_model->getJenisRuanganAktif();
		$this->load->view('sarpras/v_ruangan_add', $this->page_data);
	}

	public function ruanganSimpan()
	{
		$data = array(
			'nama_ruangan' => $this->input->post('nama_ruangan'),
			'panjang_ruangan' => $this->input->post('panjang_ruangan'),
			'lebar_ruangan' => $this->input->post('lebar_ruangan'),
			'luas_tapak_ruangan' => $this->input->post('luas_tapak_ruangan'),
			'kapasitas' => $this->input->post('kapasitas'),
			'kondisi' => $this->input->post('kondisi'),
			'status' => $this->input->post('status'),
			'id_bangunan' => $this->input->post('id_bangunan'),
			'id_jenis_ruangan' => $this->input->post('id_jenis_ruangan'),
		);
		$this->db->insert($this->ruangan, $data);
		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data ruangan baru', logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Input Ruangan Berhasil');
		redirect('sarpras/ruangan');
	}

	public function ruanganUpdate()
	{
		$id = $this->uri->segment(3);
		$nama_ruangan = $this->input->post('nama_ruangan');
		$data = array(
			'nama_ruangan' => $this->input->post('nama_ruangan'),
			'panjang_ruangan' => $this->input->post('panjang_ruangan'),
			'lebar_ruangan' => $this->input->post('lebar_ruangan'),
			'luas_tapak_ruangan' => $this->input->post('luas_tapak_ruangan'),
			'kapasitas' => $this->input->post('kapasitas'),
			'kondisi' => $this->input->post('kondisi'),
			'status' => $this->input->post('status'),
			'id_bangunan' => $this->input->post('id_bangunan'),
			'id_jenis_ruangan' => $this->input->post('id_jenis_ruangan'),
		);
		$this->db->where('id_ruangan', $id);
		$this->db->update($this->ruangan, $data);
		$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data ruangan ' . $nama_ruangan, logged('id'));
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Update Ruangan Berhasil');
		redirect('sarpras/ruangan');
	}

	public function ruanganSaranaSimpan($id)
	{
		$data = array(
			'id_ruangan' => $id,
			'id_jenis_sarana' => $this->input->post('id_jenis_sarana'),
			'id_sarana' => $this->input->post('id_sarana'),
			'jumlah_sarana_ruangan' => $this->input->post('jumlah'),
		);
		$this->db->insert('sarpras_ruangan_sarana', $data);
		$dbaseerror = $this->db->error();
		$numbererror = $dbaseerror['code'];
		$messagerror = $dbaseerror['message'];

		if (!$numbererror) {
			$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data sarana pada ruanganruangan ' . $id, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Input Sarana Berhasil');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Update Sarana Gagal!' . $messagerror);
		}

		redirect('sarpras/ruanganDetail/' . $id);
	}

	public function ruanganSaranaUpdate($id, $id_ruangan)
	{
		$data = array(
			'jumlah_sarana_ruangan' => $this->input->post('jumlah'),
		);
		$this->db->where('id_ruangan_sarana', $id);
		$this->db->update('sarpras_ruangan_sarana', $data);
		$dbaseerror = $this->db->error();
		$numbererror = $dbaseerror['code'];
		$messagerror = $dbaseerror['message'];

		if (!$numbererror) {
			$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data sarana pada ruanganruangan ' . $id, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Input Sarana Berhasil');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Update Sarana Gagal!' . $messagerror);
		}

		redirect('sarpras/ruanganDetail/' . $id_ruangan);
	}


	public function alatGetSome()
	{
		$id = $this->input->post('id');
		$sarana = $this->sarpras_model->getSomeSarana($id);
		echo json_encode($sarana);
	}


	public function alat()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/alat';
		$this->page_data['page']->subtitle = 'Alat';
		$this->page_data['page']->subtitleUrl = 'sarpras/alat';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->page_data['sarana'] = $this->sarpras_model->getAllSarana();
		$this->load->view('sarpras/v_alat_list', $this->page_data);
	}

	public function alatTambah()
	{
		$this->page_data['page']->title = 'Sarpras';
		$this->page_data['page']->titleUrl = 'sarpras/alat';
		$this->page_data['page']->subtitle = 'Alat';
		$this->page_data['page']->subtitleUrl = 'sarpras/alat';
		$this->page_data['page']->subsubtitle = 'Tambah';
		$this->page_data['page']->subsubtitleUrl = 'sarpras/alatTambah';
		$this->page_data['page']->icon = 'hugeicons:maps-square-01';
		$this->page_data['jenis_sarana'] = $this->master_model->getJenisSaranaAktif();
		$this->load->view('sarpras/v_alat_add', $this->page_data);
	}

	public function alatSimpan()
	{
		$nama_sarana = $this->input->post('nama_ruangan');
		$data = array(
			'nama_sarana' => $this->input->post('nama_sarana'),
			'kode_sarana' => $this->input->post('kode_sarana'),
			'spesifikasi_sarana' => $this->input->post('spesifikasi_sarana'),
			'jumlah_sarana' => $this->input->post('jumlah_sarana'),
			'jumlah_laik' => $this->input->post('jumlah_laik'),
			'tgl_pengadaan' => $this->input->post('tgl_pengadaan'),
			'sumber_pengadaan' => $this->input->post('sumber_pengadaan'),
			'id_jenis_sarana' => $this->input->post('id_jenis_sarana'),
		);
		$this->db->insert($this->alat, $data);

		$dbaseerror = $this->db->error();
		$numbererror = $dbaseerror['code'];
		$messagerror = $dbaseerror['message'];

		if (!$numbererror) {
			$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data sarana baru - ' . $nama_sarana, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Input Sarana Berhasil');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Input Sarana Gagal!');
		}

		redirect('sarpras/alat');
	}

	public function alatEdit($id)
	{
		$this->page_data['jenis_sarana'] = $this->master_model->getJenisSaranaAktif();
		$this->page_data['sarana'] = $this->sarpras_model->getDetailSarana($id);
		$html_content = $this->load->view('sarpras/v_alat_update_form', $this->page_data, TRUE);
		echo $html_content;
	}

	public function alatUpdate($id)
	{
		$nama_sarana = $this->input->post('nama_ruangan');
		$data = array(
			'nama_sarana' => $this->input->post('nama_sarana'),
			'kode_sarana' => $this->input->post('kode_sarana'),
			'spesifikasi_sarana' => $this->input->post('spesifikasi_sarana'),
			'jumlah_sarana' => $this->input->post('jumlah_sarana'),
			'jumlah_laik' => $this->input->post('jumlah_laik'),
			'tgl_pengadaan' => $this->input->post('tgl_pengadaan'),
			'sumber_pengadaan' => $this->input->post('sumber_pengadaan'),
			'id_jenis_sarana' => $this->input->post('id_jenis_sarana'),
		);
		$this->db->where('id_sarana', $id);
		$this->db->update($this->alat, $data);

		$dbaseerror = $this->db->error();
		$numbererror = $dbaseerror['code'];
		$messagerror = $dbaseerror['message'];

		if (!$numbererror) {
			$this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data sarana baru - ' . $nama_sarana, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Update Sarana Berhasil');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Update Sarana Gagal!');
		}

		redirect('sarpras/alat');
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */