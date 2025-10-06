<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sarpras_model extends MY_Model
{

    public $tanah = 'tanah';
    public $bangunan = 'bangunan';
    public $ruangan = 'banruangangunan';
    public $alat = 'alat';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllTanah()
    {
        $this->db->select('*');
        $this->db->from($this->tanah);
        $query = $this->db->get();
        return $query->result();
    }
}
