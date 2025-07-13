<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_permissions_model extends MY_Model {

	public $table = 'role_permissions';

	public function __construct()
	{
		parent::__construct();
	}

	public function getByWhereSort($id) 
	{
		$this->db->select('*');
		$this->db->from('role_permissions');
		$this->db->where('role',$id);
		$this->db->order_by('permission', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

}

/* End of file Role_permissions_model.php */
/* Location: ./application/models/Role_permissions_model.php */