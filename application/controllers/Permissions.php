<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permissions extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title = 'Permissions Management';
		$this->page_data['page']->menu = 'permissions';
	}

	public function index()
	{
		$this->page_data['page']->title = 'Permission';
		$this->page_data['page']->titleUrl = 'permissions';
		$this->page_data['page']->subtitle = 'Permisions';
		$this->page_data['page']->subtitleUrl = 'permissions';
		$this->page_data['page']->icon = 'simple-icons:openaccess';
		ifPermissions('permissions_list');
		
		// Mengambil seluruh data permissions beserta parent title untuk list view
		$this->db->select('p.*, parent.title as parent_title');
		$this->db->from('permissions p');
		$this->db->join('permissions parent', 'p.parent_id = parent.id', 'left');
		$this->db->order_by('p.id', 'DESC');
		$this->page_data['permissions'] = $this->db->get()->result();
		
		$this->load->view('permissions/list', $this->page_data);
	}

	public function add()
	{
		$this->page_data['page']->title = 'Permission';
		$this->page_data['page']->titleUrl = 'permissions';
		$this->page_data['page']->subtitle = 'Permisions';
		$this->page_data['page']->subtitleUrl = 'permissions';
		$this->page_data['page']->icon = 'simple-icons:openaccess';
		ifPermissions('permissions_add');

		// Ambil list permission yang bertindak sebagai parent (level 1 dan level 2)
		$this->db->select('*');
		$this->db->from('permissions');
		$this->db->where('level <', 3);
		$this->db->order_by('level', 'ASC');
		$this->db->order_by('title', 'ASC');
		$this->page_data['parents'] = $this->db->get()->result();

		$this->load->view('permissions/add', $this->page_data);
	}

	public function edit($id)
	{
		$this->page_data['page']->title = 'Permission';
		$this->page_data['page']->titleUrl = 'permissions';
		$this->page_data['page']->subtitle = 'Permisions';
		$this->page_data['page']->subtitleUrl = 'permissions';
		$this->page_data['page']->icon = 'simple-icons:openaccess';
		ifPermissions('permissions_edit');

		$this->page_data['permission'] = $this->permissions_model->getById($id);

		// Ambil list parent permission kecuali dirinya sendiri agar tidak sirkular reference
		$this->db->select('*');
		$this->db->from('permissions');
		$this->db->where('level <', 3);
		$this->db->where('id !=', $id);
		$this->db->order_by('level', 'ASC');
		$this->db->order_by('title', 'ASC');
		$this->page_data['parents'] = $this->db->get()->result();

		$this->load->view('permissions/edit', $this->page_data);
	}

	public function save()
	{

		postAllowed();

		ifPermissions('permissions_add');

		$parent_id = $this->input->post('parent_id');
		$level = 1;
		if (!empty($parent_id)) {
			$parent = $this->permissions_model->getById($parent_id);
			if ($parent) {
				$level = $parent->level + 1;
			}
		} else {
			$parent_id = null;
		}

		$permission = $this->permissions_model->create([
			'title' => $this->input->post('name'),
			'code' => $this->input->post('code'),
			'parent_id' => $parent_id,
			'level' => $level,
		]);

		$this->activity_model->add("New Permission #$permission Created by User: #" . logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'New Permission Created Successfully');

		redirect('permissions');
	}

	public function update($id)
	{

		postAllowed();

		ifPermissions('permissions_edit');

		$parent_id = $this->input->post('parent_id');
		$level = 1;
		if (!empty($parent_id)) {
			$parent = $this->permissions_model->getById($parent_id);
			if ($parent) {
				$level = $parent->level + 1;
			}
		} else {
			$parent_id = null;
		}

		$data = [
			'title' => $this->input->post('name'),
			'code' => $this->input->post('code'),
			'parent_id' => $parent_id,
			'level' => $level,
		];

		$permission = $this->permissions_model->update($id, $data);

		$this->activity_model->add("Permission #$id Updated by User: #" . logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Permission has been Updated Successfully');

		redirect('permissions');
	}

	public function delete($id)
	{

		ifPermissions('permissions_delete');

		$this->permissions_model->delete($id);

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Permission has been Deleted Successfully');

		$this->activity_model->add("Permission #$permission Deleted by User: #" . logged('id'));

		redirect('permissions');
	}

	public function checkIfUnique()
	{

		$code = get('code');

		if (!$code)
			die('Invalid Request');

		$arg = ['code' => $code];

		if (!empty(get('notId')))
			$arg['id !='] = get('notId');

		$query = $this->permissions_model->getByWhere($arg);

		if (!empty($query))
			die('false');
		else
			die('true');
	}
}

/* End of file Permissions.php */
/* Location: ./application/controllers/Permissions.php */