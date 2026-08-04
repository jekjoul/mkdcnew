<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lembaga_model extends MY_Model
{

    public $table = 'lembaga';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllLembaga($include_yayasan = false)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        if (!$include_yayasan) {
            $this->db->where("TRIM(UPPER(nama_lembaga_singkat)) !=", "YAYASAN");
        }
        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    public function getDetailLembaga($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id_lembaga', $id);
        $query = $this->db->get();
        return $query->row();
    }
}
