<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends MY_Model
{
    public $jenis_ruangan = 'master_jenis_ruangan';

    public function getJenisRuanganAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query->result();
    }
}
