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

		$id = $this->users_model->create([
			'role' => post('role'),
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
			'status' => (int) post('status'),
			'password' => hash("sha256", post('password')),
			'id_ptk' => post('id_ptk') ?: null,
		]);

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

		$data = [
			'role' => post('role'),
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

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id . '.' . $ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/users');

			if ($image['status']) {
				$this->users_model->update($id, ['img_type' => $ext]);
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

	private function ensureUsersPtkColumn()
	{
		$this->load->dbforge();
		if (!$this->db->field_exists('id_ptk', 'users')) {
			$this->dbforge->add_column('users', [
				'id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'role'],
			]);
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
