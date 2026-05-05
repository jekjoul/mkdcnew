<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model', 'master_model');
    }

    public function index()
    {
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Daftar Pembelajaran';
        $this->page_data['page']->subtitleUrl = 'pembelajaran';
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->page_data['pembelajaran'] = $this->db->get()->result();

        $this->load->view('pembelajaran/list', $this->page_data);
    }

    public function tambah()
    {
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Atur Pembelajaran Baru';
        $this->page_data['page']->subtitleUrl = 'pembelajaran/tambah';
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $ta_aktif = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif'])->row();
        if (!$ta_aktif) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal! Tidak ada Tahun Pelajaran yang Aktif. Silakan aktifkan terlebih dahulu.');
            redirect('tahun_pelajaran');
            return;
        }

        $this->page_data['lembaga'] = $this->master_model->getAllLembaga();
        $this->page_data['tingkat'] = $this->master_model->getTingkatSekolah();
        $this->page_data['rombel']  = ($q = $this->db->get('rombel')) ? $q->result() : [];
        $this->page_data['mapel']   = $this->master_model->getMapel();
        $this->page_data['siswa']   = ($q = $this->db->get('data_siswa')) ? $q->result() : [];
        $this->page_data['ta_aktif'] = $ta_aktif;

        $this->load->view('pembelajaran/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();

        $pembelajaran_data = [
            'id_tahun_pelajaran' => post('id_tahun_pelajaran'),
            'id_lembaga'         => post('id_lembaga'),
            'id_tingkat_sekolah' => post('id_tingkat_sekolah'),
            'id_rombel'          => post('id_rombel'),
        ];

        $this->db->insert('pembelajaran', $pembelajaran_data);
        $id_pembelajaran = $this->db->insert_id();

        $mapels = $this->input->post('mapel');
        if (!empty($mapels)) {
            foreach ($mapels as $id_mapel) {
                $this->db->insert('pembelajaran_mapel', ['id_pembelajaran' => $id_pembelajaran, 'id_mapel' => $id_mapel]);
            }
        }

        $siswas = $this->input->post('siswa');
        if (!empty($siswas)) {
            foreach ($siswas as $pd_id) {
                $this->db->insert('pembelajaran_siswa', ['id_pembelajaran' => $id_pembelajaran, 'peserta_didik_id' => $pd_id]);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Pembelajaran berhasil dikonfigurasi');
        redirect('pembelajaran');
    }
}
