<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ptk extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public $table = 'ptk';

	public function ptk()
	{
		ifPermissions('ptk_list');
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->titleUrl = 'ptk/ptk';
		$this->page_data['page']->subtitle = 'Daftar PTK';
		$this->page_data['page']->subtitleUrl = 'ptk/ptk';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		
		$ptk_list = $this->db->get_where($this->table, ['status_keaktifan' => 'Aktif'])->result();
		
		$users = $this->db->select('id_ptk, username')->get_where('users', 'id_ptk IS NOT NULL')->result();
		$user_map = [];
		foreach ($users as $u) {
			$user_map[$u->id_ptk] = $u->username;
		}
		
		$this->page_data['ptk'] = $ptk_list;
		$this->page_data['user_map'] = $user_map;
		$this->load->view('ptk/v_ptk_list', $this->page_data);
	}

	public function buat_akun($id_ptk)
	{
		ifPermissions('ptk_buat_akun');
		
		$ptk = $this->db->get_where($this->table, ['id_ptk' => $id_ptk])->row();
		if (!$ptk) {
			show_404();
		}
		
		$existing = $this->db->get_where('users', ['id_ptk' => $id_ptk])->row();
		if ($existing) {
			$this->session->set_flashdata('alert-type', 'warning');
			$this->session->set_flashdata('alert', 'Akun MKDC untuk PTK ' . $ptk->nama_ptk . ' sudah pernah dibuat.');
			redirect('ptk/ptk');
		}
		
		$username = strtolower(str_replace(' ', '', preg_replace('/[^a-zA-Z0-9]/', '', $ptk->nama_ptk)));
		$i = 1;
		$orig_username = $username;
		while ($this->db->get_where('users', ['username' => $username])->row()) {
			$username = $orig_username . $i;
			$i++;
		}
		
		$email = !empty($ptk->email) ? $ptk->email : $username . '@mkdc.sch.id';
		$i = 1;
		$orig_email = $email;
		while ($this->db->get_where('users', ['email' => $email])->row()) {
			$email = 'ptk' . $i . '_' . $orig_email;
			$i++;
		}
		
		$default_password = !empty($ptk->nuptk) ? $ptk->nuptk : '123456';
		$hashed_password = hash("sha256", $default_password);
		
		$user_data = [
			'name' => $ptk->nama_ptk,
			'username' => $username,
			'email' => $email,
			'password' => $hashed_password,
			'role' => 4, // Role 4 is Guru
			'id_ptk' => $ptk->id_ptk,
			'status' => 1
		];
		
		if ($this->db->insert('users', $user_data)) {
			$this->activity_model->add(logged('name') . ' Membuat akun MKDC secara otomatis untuk PTK: ' . $ptk->nama_ptk, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Akun berhasil dibuat. Username: ' . $username . ', Password: ' . $default_password);
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Gagal membuat akun MKDC.');
		}
		
		redirect('ptk/ptk');
	}

	public function ptkDetail($id)
	{
		$row = $this->db->get_where($this->table, ['id_ptk' => $id])->row();
		if (!$row) {
			show_404();
		}
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->titleUrl = 'ptk/ptk';
		$this->page_data['page']->subtitle = $row->nama_ptk;
		$this->page_data['page']->subtitleUrl = 'ptk/ptkDetail';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->page_data['row'] = $row;
		$this->db->order_by('tahun_lulus', 'DESC');
		$this->db->order_by('tanggal_lulus', 'DESC');
		$this->page_data['riwayat_pendidikan'] = $this->db->get_where('ptk_riwayat_pendidikan', ['id_ptk' => $id])->result();
		$this->db->select('ptk_dokumen_pribadi.*, master_jenis_dokumen_ptk.nama_jenis_dokumen');
		$this->db->from('ptk_dokumen_pribadi');
		$this->db->join('master_jenis_dokumen_ptk', 'master_jenis_dokumen_ptk.id_jenis_dokumen = ptk_dokumen_pribadi.id_jenis_dokumen', 'left');
		$this->db->where('ptk_dokumen_pribadi.id_ptk', $id);
		$this->db->order_by('master_jenis_dokumen_ptk.nama_jenis_dokumen', 'ASC');
		$this->page_data['dokumen_pribadi'] = $this->db->get()->result();
		$this->db->order_by('nama_jenis_dokumen', 'ASC');
		$this->page_data['jenis_dokumen'] = $this->db->get_where('master_jenis_dokumen_ptk', ['status' => 'Aktif'])->result();
		$this->load->view('ptk/v_ptk_detail', $this->page_data);
	}

	public function ptkPendidikanSimpan($id_ptk)
	{
		postAllowed();

		$ptk = $this->db->get_where($this->table, ['id_ptk' => $id_ptk])->row();
		if (!$ptk) {
			show_404();
		}

		$data = $this->pendidikanData($id_ptk);

		if ($this->db->insert('ptk_riwayat_pendidikan', $data)) {
			$this->activity_model->add(logged('name') . ' Menambah riwayat pendidikan PTK: ' . $ptk->nama_ptk, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Berhasil Ditambahkan');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Gagal Ditambahkan');
		}

		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile') !== false) {
			redirect('profile');
		} else {
			redirect('ptk/ptkDetail/' . $id_ptk);
		}
	}

	public function ptkPendidikanUpdate($id_pendidikan)
	{
		postAllowed();

		$pendidikan = $this->db->get_where('ptk_riwayat_pendidikan', ['id_pendidikan' => $id_pendidikan])->row();
		if (!$pendidikan) {
			show_404();
		}

		$data = $this->pendidikanData($pendidikan->id_ptk);
		$this->db->where('id_pendidikan', $id_pendidikan);

		if ($this->db->update('ptk_riwayat_pendidikan', $data)) {
			$ptk = $this->db->get_where($this->table, ['id_ptk' => $pendidikan->id_ptk])->row();
			$this->activity_model->add(logged('name') . ' Mengubah riwayat pendidikan PTK: ' . ($ptk ? $ptk->nama_ptk : $pendidikan->id_ptk), logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Berhasil Diperbarui');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Gagal Diperbarui');
		}

		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile') !== false) {
			redirect('profile');
		} else {
			redirect('ptk/ptkDetail/' . $pendidikan->id_ptk);
		}
	}

	public function ptkPendidikanHapus($id_pendidikan)
	{
		$pendidikan = $this->db->get_where('ptk_riwayat_pendidikan', ['id_pendidikan' => $id_pendidikan])->row();
		if (!$pendidikan) {
			show_404();
		}

		$this->db->where('id_pendidikan', $id_pendidikan);
		if ($this->db->delete('ptk_riwayat_pendidikan')) {
			if ($pendidikan->berkas) {
				$this->hapusFileDokumen($pendidikan->berkas);
			}
			$ptk = $this->db->get_where($this->table, ['id_ptk' => $pendidikan->id_ptk])->row();
			$this->activity_model->add(logged('name') . ' Menghapus riwayat pendidikan PTK: ' . ($ptk ? $ptk->nama_ptk : $pendidikan->id_ptk), logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Berhasil Dihapus');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Riwayat Pendidikan Gagal Dihapus');
		}

		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile') !== false) {
			redirect('profile');
		} else {
			redirect('ptk/ptkDetail/' . $pendidikan->id_ptk);
		}
	}

	public function ptkPendidikanUpload($id_pendidikan)
	{
		postAllowed();
		$pendidikan = $this->db->get_where('ptk_riwayat_pendidikan', ['id_pendidikan' => $id_pendidikan])->row();
		if (!$pendidikan) {
			show_404();
		}

		$upload = $this->uploadDokumenPribadi($pendidikan->id_ptk);
		if ($upload['status']) {
			// Hapus file lama jika ada
			if ($pendidikan->berkas) {
				$this->hapusFileDokumen($pendidikan->berkas);
			}

			$this->db->where('id_pendidikan', $id_pendidikan);
			$this->db->update('ptk_riwayat_pendidikan', ['berkas' => $upload['file_name']]);

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Berkas Ijazah Berhasil Diunggah');
		} else {
			$this->session->set_flashdata('alert-type', 'warning');
			$this->session->set_flashdata('alert', $upload['message']);
		}

		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile') !== false) {
			redirect('profile');
		} else {
			redirect('ptk/ptkDetail/' . $pendidikan->id_ptk);
		}
	}

	private function pendidikanData($id_ptk)
	{
		return [
			'id_ptk' => $id_ptk,
			'jenjang' => post('jenjang'),
			'satuan_pendidikan' => post('satuan_pendidikan'),
			'jurusan' => post('jurusan') ?: null,
			'tahun_masuk' => post('tahun_masuk') ?: null,
			'tahun_lulus' => post('tahun_lulus') ?: null,
			'tanggal_lulus' => post('tanggal_lulus') ?: null,
			'no_ijazah' => post('no_ijazah') ?: null,
			'keterangan' => post('keterangan') ?: null,
		];
	}

	public function ptkDokumenSimpan($id_ptk)
	{
		postAllowed();

		$ptk = $this->db->get_where($this->table, ['id_ptk' => $id_ptk])->row();
		if (!$ptk) {
			show_404();
		}

		$upload = $this->uploadDokumenPribadi($id_ptk);
		if (!$upload['status']) {
			$this->session->set_flashdata('alert-type', 'warning');
			$this->session->set_flashdata('alert', $upload['message']);
			redirect('ptk/ptkDetail/' . $id_ptk);
		}

		$data = $this->dokumenPribadiData($id_ptk);
		$data['berkas'] = $upload['file_name'];

		if ($this->db->insert('ptk_dokumen_pribadi', $data)) {
			$this->activity_model->add(logged('name') . ' Menambah dokumen pribadi PTK: ' . $ptk->nama_ptk, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Berhasil Ditambahkan');
		} else {
			$this->hapusFileDokumen($upload['file_name']);
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Gagal Ditambahkan');
		}

		redirect('ptk/ptkDetail/' . $id_ptk);
	}

	public function ptkDokumenUpdate($id_dokumen)
	{
		postAllowed();

		$dokumen = $this->db->get_where('ptk_dokumen_pribadi', ['id_dokumen' => $id_dokumen])->row();
		if (!$dokumen) {
			show_404();
		}

		$data = $this->dokumenPribadiData($dokumen->id_ptk);
		if (!empty($_FILES['berkas']['name'])) {
			$upload = $this->uploadDokumenPribadi($dokumen->id_ptk);
			if (!$upload['status']) {
				$this->session->set_flashdata('alert-type', 'warning');
				$this->session->set_flashdata('alert', $upload['message']);
				redirect('ptk/ptkDetail/' . $dokumen->id_ptk);
			}
			$data['berkas'] = $upload['file_name'];
		}

		$this->db->where('id_dokumen', $id_dokumen);
		if ($this->db->update('ptk_dokumen_pribadi', $data)) {
			if (!empty($data['berkas'])) {
				$this->hapusFileDokumen($dokumen->berkas);
			}
			$ptk = $this->db->get_where($this->table, ['id_ptk' => $dokumen->id_ptk])->row();
			$this->activity_model->add(logged('name') . ' Mengubah dokumen pribadi PTK: ' . ($ptk ? $ptk->nama_ptk : $dokumen->id_ptk), logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Berhasil Diperbarui');
		} else {
			if (!empty($data['berkas'])) {
				$this->hapusFileDokumen($data['berkas']);
			}
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Gagal Diperbarui');
		}

		redirect('ptk/ptkDetail/' . $dokumen->id_ptk);
	}

	public function ptkDokumenHapus($id_dokumen)
	{
		$dokumen = $this->db->get_where('ptk_dokumen_pribadi', ['id_dokumen' => $id_dokumen])->row();
		if (!$dokumen) {
			show_404();
		}

		$this->db->where('id_dokumen', $id_dokumen);
		if ($this->db->delete('ptk_dokumen_pribadi')) {
			$this->hapusFileDokumen($dokumen->berkas);
			$ptk = $this->db->get_where($this->table, ['id_ptk' => $dokumen->id_ptk])->row();
			$this->activity_model->add(logged('name') . ' Menghapus dokumen pribadi PTK: ' . ($ptk ? $ptk->nama_ptk : $dokumen->id_ptk), logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Berhasil Dihapus');
		} else {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Dokumen Pribadi Gagal Dihapus');
		}

		redirect('ptk/ptkDetail/' . $dokumen->id_ptk);
	}

	public function ptkJenisDokumenSimpan()
	{
		postAllowed();

		$nama = trim((string) post('nama_jenis_dokumen'));
		if ($nama === '') {
			$this->output->set_content_type('application/json')->set_output(json_encode([
				'status' => false,
				'message' => 'Nama jenis dokumen wajib diisi',
			]));
			return;
		}

		$existing = $this->db->get_where('master_jenis_dokumen_ptk', ['nama_jenis_dokumen' => $nama])->row();
		if ($existing) {
			$this->output->set_content_type('application/json')->set_output(json_encode([
				'status' => true,
				'id' => $existing->id_jenis_dokumen,
				'nama' => $existing->nama_jenis_dokumen,
				'message' => 'Jenis dokumen sudah tersedia',
			]));
			return;
		}

		$data = [
			'nama_jenis_dokumen' => $nama,
			'status' => 'Aktif',
		];

		if ($this->db->insert('master_jenis_dokumen_ptk', $data)) {
			$id = $this->db->insert_id();
			$this->activity_model->add(logged('name') . ' Menambah jenis dokumen PTK: ' . $nama, logged('id'));
			$this->output->set_content_type('application/json')->set_output(json_encode([
				'status' => true,
				'id' => $id,
				'nama' => $nama,
				'message' => 'Jenis dokumen berhasil ditambahkan',
			]));
			return;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode([
			'status' => false,
			'message' => 'Jenis dokumen gagal ditambahkan',
		]));
	}

	private function dokumenPribadiData($id_ptk)
	{
		return [
			'id_ptk' => $id_ptk,
			'id_jenis_dokumen' => post('id_jenis_dokumen'),
			'nomor_dokumen' => post('nomor_dokumen') ?: null,
			'tanggal_dokumen' => post('tanggal_dokumen') ?: null,
			'keterangan' => post('keterangan') ?: null,
		];
	}

	private function uploadDokumenPribadi($id_ptk)
	{
		if (empty($_FILES['berkas']['name'])) {
			return [
				'status' => false,
				'message' => 'Berkas dokumen wajib diunggah',
			];
		}

		$upload_path = './uploads/ptk_dokumen_pribadi/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'pdf|jpg|jpeg|png';
		$config['max_size'] = 5120;
		$config['encrypt_name'] = false;
		$config['overwrite'] = false;
		$config['file_name'] = 'ptk-' . $id_ptk . '-' . time();

		$this->load->library('upload');
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('berkas')) {
			return [
				'status' => false,
				'message' => strip_tags($this->upload->display_errors()),
			];
		}

		$data = $this->upload->data();
		return [
			'status' => true,
			'file_name' => $data['file_name'],
		];
	}

	private function hapusFileDokumen($file_name)
	{
		if (!$file_name) {
			return;
		}

		$path = FCPATH . 'uploads/ptk_dokumen_pribadi/' . $file_name;
		if (is_file($path)) {
			unlink($path);
		}
	}

	public function ptkTambah()
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->titleUrl = 'ptk/ptk';
		$this->page_data['page']->subtitle = 'Tambah';
		$this->page_data['page']->subtitleUrl = 'ptk/ptkTambah';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
		$this->load->view('ptk/v_ptk_add', $this->page_data);
	}

	public function getKabupaten()
	{
		$id_prov = $this->input->post('id');
		$data = $this->db->get_where('reg_kabupaten', ['id_prov' => $id_prov])->result();
		echo json_encode($data);
	}

	public function getKecamatan()
	{
		$id_kab = $this->input->post('id');
		$data = $this->db->get_where('reg_kecamatan', ['id_kab' => $id_kab])->result();
		echo json_encode($data);
	}

	public function getKelurahan()
	{
		$id_kec = $this->input->post('id');
		$data = $this->db->get_where('reg_kelurahan', ['id_kec' => $id_kec])->result();
		echo json_encode($data);
	}

	public function ptkCekDuplikat()
	{
		$nik = trim((string) post('nik'));
		$email = trim((string) post('email'));
		$result = [
			'nik' => false,
			'email' => false,
		];

		if ($nik !== '') {
			$result['nik'] = (bool) $this->db->get_where($this->table, ['nik' => $nik])->row();
		}

		if ($email !== '') {
			$this->db->where('LOWER(email)', strtolower($email));
			$result['email'] = (bool) $this->db->get($this->table)->row();
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'duplicates' => $result,
			]));
	}

	public function ptkSimpan()
	{
		postAllowed();

		$nik = trim((string) post('nik'));
		$email = trim((string) post('email'));

		if ($nik !== '' && $this->db->get_where($this->table, ['nik' => $nik])->row()) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'NIK sudah terdaftar. Silakan gunakan NIK lain.');
			redirect('ptk/ptkTambah');
			return;
		}

		if ($email !== '') {
			$this->db->where('LOWER(email)', strtolower($email));
			if ($this->db->get($this->table)->row()) {
				$this->session->set_flashdata('alert-type', 'danger');
				$this->session->set_flashdata('alert', 'Email sudah terdaftar. Silakan gunakan email lain.');
				redirect('ptk/ptkTambah');
				return;
			}
		}

		$data = [
			'nama_ptk' => post('nama_ptk'),
			'gelar_depan' => post('gelar_depan'),
			'gelar_belakang' => post('gelar_belakang'),
			'jenis_kelamin' => post('jenis_kelamin'),
			'tempat_lahir' => post('tempat_lahir'),
			'tanggal_lahir' => post('tanggal_lahir'),
			'agama' => post('agama'),
			'status_perkawinan' => post('status_perkawinan'),
			'nama_ibu_kandung' => post('nama_ibu_kandung'),
			'nik' => $nik,
			'niy' => post('niy'),
			'nuptk' => post('nuptk'),
			'no_sk_pengangkatan' => post('no_sk_pengangkatan'),
			'tgl_sk_pengangkatan' => post('tgl_sk_pengangkatan'),
			'email' => $email,
			'telepon' => post('telepon'),
			'status_pegawai' => post('status_pegawai'),
			'penugasan' => post('penugasan'),
			'alamat' => post('alamat'),
			'rt' => post('rt'),
			'rw' => post('rw'),
			// Lookup nama wilayah dengan pengecekan baris (null check)
			'provinsi' => ($p = $this->db->get_where('reg_provinsi', ['id_prov' => post('provinsi')])->row()) ? $p->nama : '',
			'kabupaten' => ($k = $this->db->get_where('reg_kabupaten', ['id_kab' => post('kabupaten')])->row()) ? $k->nama : '',
			'kecamatan' => ($kc = $this->db->get_where('reg_kecamatan', ['id_kec' => post('kecamatan')])->row()) ? $kc->nama : '',
			'kelurahan_desa' => ($kl = $this->db->get_where('reg_kelurahan', ['id_kel' => post('kelurahan_desa')])->row()) ? $kl->nama : '',
			'password' => hash("sha256", post('password')),
			'status_keaktifan' => 'Aktif',
		];

		if ($this->db->insert($this->table, $data)) {
			$this->activity_model->add(logged('name') . ' Menambah data PTK: ' . $data['nama_ptk'], logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Data PTK Berhasil Ditambahkan');
		} else {
			// Tambahkan alert jika gagal di level database (misal: duplikat NIK/Email)
			$error = $this->db->error();
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Gagal simpan data: ' . $error['message']);
		}

		redirect('ptk/ptk');
	}

	public function ptkEdit($id)
	{
		$this->page_data['page']->title = 'PTK';
		$this->page_data['page']->subtitle = 'Edit PTK';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->page_data['row'] = $this->db->get_where($this->table, ['id_ptk' => $id])->row();
		$this->load->view('ptk/v_ptk_edit', $this->page_data);
	}

	public function ptkUpdate($id)
	{
		postAllowed();
		$data = [
			'nama_ptk' => post('nama_ptk'),
			'gelar_depan' => post('gelar_depan'),
			'gelar_belakang' => post('gelar_belakang'),
			'jenis_kelamin' => post('jenis_kelamin'),
			'tempat_lahir' => post('tempat_lahir'),
			'tanggal_lahir' => post('tanggal_lahir'),
			'agama' => post('agama'),
			'status_perkawinan' => post('status_perkawinan'),
			'nama_ibu_kandung' => post('nama_ibu_kandung'),
			'nik' => post('nik'),
			'niy' => post('niy'),
			'nuptk' => post('nuptk'),
			'no_sk_pengangkatan' => post('no_sk_pengangkatan'),
			'tgl_sk_pengangkatan' => post('tgl_sk_pengangkatan'),
			'email' => post('email'),
			'telepon' => post('telepon'),
			'status_pegawai' => post('status_pegawai'),
			'penugasan' => post('penugasan'),
			'alamat' => post('alamat'),
			'rt' => post('rt'),
			'rw' => post('rw'),
			// Unifikasi logika update agar tetap menyimpan nama wilayah, bukan ID
			'provinsi' => ($p = $this->db->get_where('reg_provinsi', ['id_prov' => post('provinsi')])->row()) ? $p->nama : post('provinsi'),
			'kabupaten' => ($k = $this->db->get_where('reg_kabupaten', ['id_kab' => post('kabupaten')])->row()) ? $k->nama : post('kabupaten'),
			'kecamatan' => ($kc = $this->db->get_where('reg_kecamatan', ['id_kec' => post('kecamatan')])->row()) ? $kc->nama : post('kecamatan'),
			'kelurahan_desa' => ($kl = $this->db->get_where('reg_kelurahan', ['id_kel' => post('kelurahan_desa')])->row()) ? $kl->nama : post('kelurahan_desa'),
			'status_keaktifan' => post('status_keaktifan'),
		];

		if (!empty($_FILES['foto']['name'])) {
			$upload_path = FCPATH . 'uploads/ptk_foto/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true);
			}

			$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'jpg|jpeg|png';
			$config['max_size'] = 2048;
			$config['file_name'] = 'ptk_' . $id . '_' . time() . '.' . $ext;

			$this->load->library('upload');
			$this->upload->initialize($config);

			if ($this->upload->do_upload('foto')) {
				$upload_data = $this->upload->data();
				$data['foto'] = $upload_data['file_name'];
				
				// Delete old file
				$old_ptk = $this->db->get_where($this->table, ['id_ptk' => $id])->row();
				if ($old_ptk && $old_ptk->foto && $old_ptk->foto != 'default.png') {
					if (is_file($upload_path . $old_ptk->foto)) {
						unlink($upload_path . $old_ptk->foto);
					}
				}
			}
		}

		$this->db->where('id_ptk', $id);
		$this->db->update($this->table, $data);

		// Synchronize with Users table if linked
		$user = $this->db->get_where('users', ['id_ptk' => $id])->row();
		if ($user) {
			$user_data = [
				'name' => $data['nama_ptk'],
				'email' => $data['email']
			];

			if (isset($data['foto'])) {
				// Copy from uploads/ptk_foto/ to uploads/users/
				$source = FCPATH . 'uploads/ptk_foto/' . $data['foto'];
				$ext = pathinfo($data['foto'], PATHINFO_EXTENSION);
				$dest_dir = FCPATH . 'uploads/users/';
				if (!is_dir($dest_dir)) {
					mkdir($dest_dir, 0777, true);
				}
				
				if (copy($source, $dest_dir . $user->id . '.' . $ext)) {
					$user_data['img_type'] = $ext;
				}
			}

			$this->db->where('id_ptk', $id);
			$this->db->update('users', $user_data);
		}

		$this->activity_model->add(logged('name') . ' Mengubah data PTK: ' . $data['nama_ptk'], logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Data PTK Berhasil Diperbarui');
		
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile') !== false) {
			redirect('profile');
		} else {
			redirect('ptk/ptk');
		}
	}

	public function ptkHapus($id)
	{
		$ptk = $this->db->get_where($this->table, ['id_ptk' => $id])->row();
		if ($ptk) {
			$this->db->where('id_ptk', $id);
			$this->db->delete($this->table);
			$this->activity_model->add(logged('name') . ' Menghapus data PTK: ' . $ptk->nama_ptk, logged('id'));
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Data PTK Berhasil Dihapus');
		}
		redirect('ptk/ptk');
	}

	public function ptkNonaktif()
	{
		$this->page_data['page']->title = 'PTK Nonaktif';
		$this->page_data['page']->titleUrl = 'ptk/ptk';
		$this->page_data['page']->subtitle = 'Daftar PTK Nonaktif';
		$this->page_data['page']->subtitleUrl = 'ptk/ptkNonaktif';
		$this->page_data['page']->icon = 'icon-park-outline:user-business';
		$this->db->order_by('nama_ptk', 'ASC');
		$this->page_data['ptk'] = $this->db->get_where($this->table, ['status_keaktifan' => 'Nonaktif'])->result();
		$this->load->view('ptk/v_ptk_nonaktif_list', $this->page_data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */
