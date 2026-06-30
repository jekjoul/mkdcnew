<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sarpras_model extends MY_Model
{

    public $tanah = 'sarpras_tanah';
    public $bangunan = 'sarpras_bangunan';
    public $ruangan = 'sarpras_ruangan';
    public $alat = 'sarpras_sarana';
    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';

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

    public function getDetailRuangan($id)
    {
        $this->db->select('*');
        $this->db->from($this->ruangan);
        $this->db->join($this->bangunan, 'sarpras_ruangan.id_bangunan = sarpras_bangunan.id_bangunan', 'left');
        $this->db->join($this->jenis_ruangan, 'sarpras_ruangan.id_jenis_ruangan = master_jenis_ruangan.id_jenis_ruangan', 'left');
        $this->db->where('sarpras_ruangan.id_ruangan', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getAllSarana()
    {
        $this->db->select('*');
        $this->db->from($this->alat);
        $this->db->join($this->jenis_sarana, 'sarpras_sarana.id_jenis_sarana= master_jenis_sarana.id_jenis_sarana', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    public function getDetailSarana($id)
    {
        $this->db->select('*');
        $this->db->from($this->alat);
        $this->db->join($this->jenis_sarana, 'sarpras_sarana.id_jenis_sarana= master_jenis_sarana.id_jenis_sarana', 'left');
        $this->db->where('sarpras_sarana.id_sarana', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getSomeSarana($id)
    {
        $this->db->select('*');
        $this->db->from($this->alat);
        $this->db->where('sarpras_sarana.id_jenis_sarana', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function getRuanganSarana($id)
    {
        $this->db->select('*');
        $this->db->from('sarpras_ruangan_sarana');
        $this->db->join($this->jenis_sarana, 'sarpras_ruangan_sarana.id_jenis_sarana= master_jenis_sarana.id_jenis_sarana', 'left');
        $this->db->join($this->alat, 'sarpras_ruangan_sarana.id_sarana= sarpras_sarana.id_sarana', 'left');
        $this->db->where('id_ruangan', $id);
        $query = $this->db->get();
        return $query->result();
    }
}
