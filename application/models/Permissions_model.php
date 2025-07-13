<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions_model extends MY_Model {

	public $table = 'permissions';

	public function __construct()
	{
		parent::__construct();
	}

	public function getSortByName() 
	{
		$this->db->select('*');
		$this->db->from('permissions');
		$this->db->order_by('title', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

}

/* End of file Permissions_model.php */
/* Location: ./application/models/Permissions_model.php */