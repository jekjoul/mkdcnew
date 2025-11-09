<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends MY_Model
{
    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';

    public function getJenisRuangan()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $query = $this->db->get();
        return $query->result();
    }

    public function getDetailJenisRuangan($id)
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $this->db->where('id_jenis_ruangan', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getJenisRuanganAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query->result();
    }

    public function jenisRuanganExist($id)
    {
        $this->db->where('id_jenis_ruangan', $id);
        $count = $this->db->count_all_results('sarpras_ruangan');
        return $count;
    }

    public function jenisRuanganNamaExist($nama)
    {
        $this->db->where('nama_jenis_ruangan', $nama);
        $count = $this->db->count_all_results($this->jenis_ruangan);
        return $count;
    }

    public function getJenisSarana()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_sarana);
        $query = $this->db->get();
        return $query->result();
    }

    public function getDetailJenisSarana($id)
    {
        $this->db->select('*');
        $this->db->from($this->jenis_sarana);
        $this->db->where('id_jenis_sarana', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getJenisSaranaAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_sarana);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query->result();
    }

    public function jenisSaranaExist($id)
    {
        $this->db->where('id_jenis_sarana', $id);
        $count = $this->db->count_all_results('sarpras_sarana');
        return $count;
    }

    public function jenisSaranaNamaExist($nama)
    {
        $this->db->where('nama_jenis_sarana', $nama);
        $count = $this->db->count_all_results($this->jenis_sarana);
        return $count;
    }
}
