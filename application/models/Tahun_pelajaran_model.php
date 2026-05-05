<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tahun_pelajaran_model extends MY_Model
{
    public $table = 'pembelajaran_tahun_pelajaran';

    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->db->order_by('id_tahun_pelajaran', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id_tahun_pelajaran' => $id])->row();
    }
}
