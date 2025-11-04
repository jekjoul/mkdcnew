<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends MY_Model
{
    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';

    public function getJenisRuanganAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query->result();
    }

    public function getJenisSaranaAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_sarana);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query->result();
    }
}
