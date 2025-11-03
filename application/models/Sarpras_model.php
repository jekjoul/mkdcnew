<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sarpras_model extends MY_Model
{

    public $tanah = 'sarpras_tanah';
    public $bangunan = 'sarpras_bangunan';
    public $ruangan = 'sarpras_ruangan';
    public $alat = 'sarpras_alat';
    public $jenis_ruangan = 'master_jenis_ruangan';

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

    public function getAllBangunan()
    {
        $this->db->select('*');
        $this->db->from($this->bangunan);
        $this->db->join($this->tanah, 'sarpras_bangunan.id_tanah = sarpras_tanah.id_tanah', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    public function getBangunan()
    {
        $this->db->select('*');
        $this->db->from($this->bangunan);
        $query = $this->db->get();
        return $query->result();
    }

    public function getAllRuangan()
    {
        $this->db->select('*');
        $this->db->from($this->ruangan);
        $this->db->join($this->bangunan, 'sarpras_ruangan.id_bangunan = sarpras_bangunan.id_bangunan', 'left');
        $this->db->join($this->jenis_ruangan, 'sarpras_ruangan.id_jenis_ruangan = master_jenis_ruangan.id_jenis_ruangan', 'left');
        $query = $this->db->get();
        return $query->result();
    }
}
