<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends MY_Model
{
    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';
    public $lembaga = 'lembaga';
    public $mapel = 'mapel';
    public $tingkat_sekolah = 'master_tingkat_sekolah';

    public function getAllLembaga()
    {
        $query = $this->db->get($this->lembaga);
        return $query ? $query->result() : [];
    }

    public function getDetailLembaga($id)
    {
        $query = $this->db->get_where($this->lembaga, ['id_lembaga' => $id]);
        return $query ? $query->row() : null;
    }

    public function getMapel()
    {
        $this->db->order_by('urutan', 'ASC');
        $query = $this->db->get($this->mapel);
        return $query ? $query->result() : [];
    }

    public function getDetailMapel($id)
    {
        $query = $this->db->get_where($this->mapel, ['id_mapel' => $id]);
        return $query ? $query->row() : null;
    }

    public function getTingkatSekolah()
    {
        $query = $this->db->get($this->tingkat_sekolah);
        return $query ? $query->result() : [];
    }

    public function getDetailTingkatSekolah($id)
    {
        $query = $this->db->get_where($this->tingkat_sekolah, ['id_tingkat_sekolah' => $id]);
        return $query ? $query->row() : null;
    }

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
        return $query ? $query->row() : null;
    }

    public function getJenisRuanganAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_ruangan);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query ? $query->result() : [];
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
        return $query ? $query->row() : null;
    }

    public function getJenisSaranaAktif()
    {
        $this->db->select('*');
        $this->db->from($this->jenis_sarana);
        $this->db->where('status', 'Aktif');
        $query = $this->db->get();
        return $query ? $query->result() : [];
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
