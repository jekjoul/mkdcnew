<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->ensureUsersPtkColumn();
		$this->ensureGuruRole();
		$this->page_data['page']->title = 'Users Management';
		$this->page_data['page']->menu = 'users';
	}

	public function index()
	{
		ifPermissions('users_list');
		$this->page_data['page']->title = 'Akun';
		$this->page_data['page']->titleUrl = 'users';
		$this->page_data['page']->subtitle = 'Daftar Akun';
		$this->page_data['page']->subtitleUrl = 'users';
		$this->page_data['page']->icon = 'fa7-solid:users';

		$this->page_data['users'] = $this->users_model->get();
		$this->load->view('users/list', $this->page_data);
	}

	public function add()
	{
		ifPermissions('users_add');
		$this->page_data['page']->title = 'Akun';
		$this->page_data['page']->titleUrl = 'users';
		$this->page_data['page']->subtitle = 'Tambah Akun';
		$this->page_data['page']->subtitleUrl = 'users/add';
		$this->page_data['page']->icon = 'fa7-solid:users';
		$this->load->view('users/add', $this->page_data);
	}

	public function save()
	{
		ifPermissions('users_add');
		postAllowed();

		$roles = $this->input->post('role');
		$primary_role = 0;
		if (is_array($roles) && !empty($roles)) {
			$primary_role = (int) $roles[0];
		}

		$id = $this->users_model->create([
			'role' => $primary_role,
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
			'status' => (int) post('status'),
			'password' => hash("sha256", post('password')),
			'id_ptk' => post('id_ptk') ?: null,
		]);

		// Simpan ke user_roles
		if (is_array($roles)) {
			foreach ($roles as $r) {
				$this->db->insert('user_roles', [
					'user_id' => $id,
					'role_id' => (int) $r,
					'created_at' => date('Y-m-d H:i:s')
				]);
			}
		}

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id . '.' . $ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/users');

			if ($image['status']) {
				$this->users_model->update($id, ['img_type' => $ext]);
			} else {
				copy(FCPATH . 'uploads/users/default.png', 'uploads/users/' . $id . '.png');
			}
		} else {

			copy(FCPATH . 'uploads/users/default.png', 'uploads/users/' . $id . '.png');
		}

		$this->activity_model->add('New User #' . $id . ' Created by User:' . logged('name'), logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'New User Created Successfully');

		redirect('users');
	}

	public function view($id)
	{
		$this->page_data['page']->title = 'Akun';
		$this->page_data['page']->titleUrl = 'users';
		$this->page_data['page']->subtitle = 'Detail Akun';
		$this->page_data['page']->subtitleUrl = 'users';
		$this->page_data['page']->icon = 'fa7-solid:users';
		ifPermissions('users_view');

		$this->page_data['User'] = $this->users_model->getById($id);
		$this->page_data['User']->role = $this->roles_model->getByWhere([
			'id' => $this->page_data['User']->role
		])[0];
		$this->page_data['User']->activity = $this->activity_model->getByWhere([
			'user' => $id
		], ['order' => ['id', 'desc']]);
		$this->load->view('users/view', $this->page_data);
	}

	public function edit($id)
	{
		$this->page_data['page']->title = 'Akun';
		$this->page_data['page']->titleUrl = 'users';
		$this->page_data['page']->subtitle = 'Sunting Akun';
		$this->page_data['page']->subtitleUrl = 'users';
		$this->page_data['page']->icon = 'fa7-solid:users';
		ifPermissions('users_edit');

		$this->page_data['User'] = $this->users_model->getById($id);
		$this->load->view('users/edit', $this->page_data);
	}


	public function update($id)
	{

		ifPermissions('users_edit');

		postAllowed();

		$roles = $this->input->post('role');
		$primary_role = 0;
		if (is_array($roles) && !empty($roles)) {
			$primary_role = (int) $roles[0];
		}

		$data = [
			'role' => $primary_role,
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
			'id_ptk' => post('id_ptk') ?: null,
		];

		$password = post('password');

		if (logged('id') != $id)
			$data['status'] = post('status') == 1;

		if (!empty($password))
			$data['password'] = hash("sha256", $password);

		$id = $this->users_model->update($id, $data);

		// Synchronize with PTK if linked
		if (!empty($data['id_ptk'])) {
			$ptk_data = [
				'nama_ptk' => $data['name'],
				'email'    => $data['email']
			];
			$this->db->where('id_ptk', $data['id_ptk']);
			$this->db->update('ptk', $ptk_data);
		}


		// Hapus dan masukkan ulang ke user_roles
		$this->db->delete('user_roles', ['user_id' => $id]);
		if (is_array($roles)) {
			foreach ($roles as $r) {
				$this->db->insert('user_roles', [
					'user_id' => $id,
					'role_id' => (int) $r,
					'created_at' => date('Y-m-d H:i:s')
				]);
			}
		}

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id . '.' . $ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/users');

			if ($image['status']) {
				$this->users_model->update($id, ['img_type' => $ext]);

				// Synchronize image to PTK if linked
				if (!empty($data['id_ptk'])) {
					$source = FCPATH . 'uploads/users/' . $id . '.' . $ext;
					$ptk_foto_name = 'ptk_' . $data['id_ptk'] . '_' . time() . '.' . $ext;
					$dest_dir = FCPATH . 'uploads/ptk_foto/';
					if (!is_dir($dest_dir)) {
						mkdir($dest_dir, 0777, true);
					}
					
					if (copy($source, $dest_dir . $ptk_foto_name)) {
						// Delete old PTK photo if it exists
						$old_ptk = $this->db->get_where('ptk', ['id_ptk' => $data['id_ptk']])->row();
						if ($old_ptk && $old_ptk->foto && $old_ptk->foto != 'default.png') {
							$old_path = $dest_dir . $old_ptk->foto;
							if (is_file($old_path)) {
								unlink($old_path);
							}
						}
						
						$this->db->where('id_ptk', $data['id_ptk']);
						$this->db->update('ptk', ['foto' => $ptk_foto_name]);
					}
				}
			}
		}

		$this->activity_model->add("User #$id Disunting oleh:" . logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Profil berhasil disunting!');

		redirect('users');
	}

	public function check()
	{
		$email = !empty(get('email')) ? get('email') : false;
		$username = !empty(get('username')) ? get('username') : false;
		$notId = !empty($this->input->get('notId')) ? $this->input->get('notId') : 0;

		if ($email)
			$exists = count($this->users_model->getByWhere([
				'email' => $email,
				'id !=' => $notId,
			])) > 0 ? true : false;

		if ($username)
			$exists = count($this->users_model->getByWhere([
				'username' => $username,
				'id !=' => $notId,
			])) > 0 ? true : false;

		echo $exists ? 'false' : 'true';
	}

	public function delete($id)
	{

		ifPermissions('users_delete');

		if ($id !== 1 && $id != logged($id)) {
		} else {
			redirect('/', 'refresh');
			return;
		}

		$id = $this->users_model->delete($id);

		$this->activity_model->add("User #$id Deleted by User:" . logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'User has been Deleted Successfully');

		redirect('users');
	}

	public function change_status($id)
	{
		$this->users_model->update($id, ['status' => get('status') == 'true' ? 1 : 0]);
		echo 'done';
	}

	/**
	 * Halaman pengaturan hak akses individual per user
	 */
	public function hak_akses($user_id)
	{
		ifPermissions('users_edit');

		$user = $this->users_model->getById($user_id);
		if (!$user) {
			show_404();
		}

		$this->page_data['page']->title = 'Akun';
		$this->page_data['page']->titleUrl = 'users';
		$this->page_data['page']->subtitle = 'Hak Akses Individual';
		$this->page_data['page']->subtitleUrl = 'users/hak_akses/' . $user_id;
		$this->page_data['page']->icon = 'fa7-solid:users';

		// Ambil nama role user
		$user_roles = [];
		$role_names = [];
		if ($this->db->table_exists('user_roles')) {
			$ur_list = $this->db->get_where('user_roles', ['user_id' => $user->id])->result();
			foreach ($ur_list as $ur) {
				$user_roles[] = (int) $ur->role_id;
				$role_row = $this->db->get_where('roles', ['id' => $ur->role_id])->row();
				if ($role_row) {
					$role_names[] = $role_row->title;
				}
			}
		}
		if (empty($user_roles)) {
			$user_roles[] = (int) $user->role;
			$role_row = $this->db->get_where('roles', ['id' => $user->role])->row();
			if ($role_row) {
				$role_names[] = $role_row->title;
			}
		}

		// Ambil permission bawaan role
		$this->db->where_in('role', $user_roles);
		$role_perms_result = $this->db->get('role_permissions')->result();
		$role_permissions = array_map(function ($rp) {
			return $rp->permission;
		}, $role_perms_result);

		// Ambil permission individual user
		$user_perms_result = $this->db->get_where('user_permissions', ['user_id' => $user->id])->result();
		$user_permissions = array_map(function ($up) {
			return $up->permission;
		}, $user_perms_result);

		// Ambil data PTK jika terhubung
		$ptk_data = null;
		if (!empty($user->id_ptk)) {
			$ptk_data = $this->db->get_where('ptk', ['id_ptk' => $user->id_ptk])->row();
		}

		$this->page_data['ptk_data'] = $ptk_data;
		$this->page_data['user_data'] = $user;
		$this->page_data['role_names'] = $role_names;
		$this->page_data['role_permissions'] = $role_permissions;
		$this->page_data['user_permissions'] = $user_permissions;

		$this->load->view('users/hak_akses', $this->page_data);
	}

	/**
	 * Simpan hak akses individual per user
	 */
	public function save_hak_akses($user_id)
	{
		ifPermissions('users_edit');
		postAllowed();

		$user = $this->users_model->getById($user_id);
		if (!$user) {
			show_404();
		}

		// Hapus permission individual lama
		$this->db->delete('user_permissions', ['user_id' => $user->id]);

		// Simpan permission individual baru
		$permissions = $this->input->post('permission');
		if (is_array($permissions) && !empty($permissions)) {
			$batch = [];
			foreach ($permissions as $perm) {
				$batch[] = [
					'user_id' => $user->id,
					'permission' => $perm,
					'created_at' => date('Y-m-d H:i:s'),
				];
			}
			$this->db->insert_batch('user_permissions', $batch);
		}

		$this->activity_model->add(
			logged('name') . ' mengatur hak akses individual User: ' . $user->name,
			logged('id')
		);

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Hak akses individual untuk ' . $user->name . ' berhasil disimpan.');

		redirect('users/hak_akses/' . $user_id);
	}

	private function ensureUsersPtkColumn()
	{
		$this->load->dbforge();
		if (!$this->db->field_exists('id_ptk', 'users')) {
			$this->dbforge->add_column('users', [
				'id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'role'],
			]);
		}

		if (!$this->db->table_exists('user_roles')) {
			$this->dbforge->add_field([
				'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
				'user_id' => ['type' => 'INT', 'constraint' => 11],
				'role_id' => ['type' => 'INT', 'constraint' => 11],
				'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
			]);
			$this->dbforge->add_key('id', true);
			$this->dbforge->create_table('user_roles', true);

			// Migrasi awal: salin role users lama ke user_roles
			$users = $this->db->get('users')->result();
			foreach ($users as $u) {
				if ($u->role > 0) {
					$this->db->insert('user_roles', [
						'user_id' => $u->id,
						'role_id' => $u->role,
						'created_at' => date('Y-m-d H:i:s'),
					]);
				}
			}
		}
	}

	private function ensureGuruRole()
	{
		$this->db->where('LOWER(title)', 'guru');
		if (!$this->db->get('roles')->row()) {
			$this->db->insert('roles', ['title' => 'Guru']);
		}
	}
}

/* End of file Users.php */
/* Location: ./application/controllers/Users.php */
