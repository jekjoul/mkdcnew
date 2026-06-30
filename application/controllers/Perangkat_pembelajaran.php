<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat_pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Perangkat_pembelajaran_model', 'perangkat_model');
        $this->perangkat_model->ensureTables();
    }

    public function index()
    {
        ifPermissions('perangkat_pembelajaran_list');
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->subtitle = 'Perangkat Pembelajaran';
        $this->page_data['page']->subtitleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->icon = 'solar:document-add-linear';
        $this->page_data['items'] = $this->perangkat_model->getAdminItems();
        $this->load->view('perangkat_pembelajaran/list', $this->page_data);
    }

    public function detail($id_pembelajaran_mapel)
    {
        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->subtitle = 'Detail Perangkat';
        $this->page_data['page']->subtitleUrl = 'perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:document-add-linear';
        $this->page_data['item'] = $item;
        $this->page_data['perangkat'] = $perangkat;
        $this->page_data['materi'] = $perangkat ? $this->perangkat_model->getMateri($perangkat->id_perangkat) : [];
        $this->page_data['back_url'] = url('perangkat_pembelajaran');
        $this->page_data['generate_url'] = url('perangkat_pembelajaran/generate/' . $id_pembelajaran_mapel);
        $this->page_data['save_url'] = $perangkat ? url('perangkat_pembelajaran/simpan/' . $perangkat->id_perangkat) : '#';
        $this->load->view('perangkat_pembelajaran/detail', $this->page_data);
    }

    public function generate($id_pembelajaran_mapel)
    {
        postAllowed();
        $cadangan_hari = post('cadangan_hari') !== false ? (int) post('cadangan_hari') : 28;
        $perangkat = $this->perangkat_model->generate($id_pembelajaran_mapel, $cadangan_hari);
        if (!$perangkat) {
            show_404();
        }

        $this->activity_model->add(logged('name') . ' Generate Perangkat Pembelajaran #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Perangkat pembelajaran berhasil digenerate.');
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function simpan($id_perangkat)
    {
        postAllowed();
        $this->perangkat_model->savePerangkat($id_perangkat, [
            'cp' => post('cp'),
            'atp' => post('atp'),
            'modul_ajar' => post('modul_ajar'),
        ]);
        $this->perangkat_model->saveMateri($this->input->post('materi', true));

        $row = $this->db->get_where('perangkat_pembelajaran', ['id_perangkat' => (int) $id_perangkat])->row();
        $this->activity_model->add(logged('name') . ' Menyimpan Perangkat Pembelajaran #' . $id_perangkat);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Perangkat pembelajaran berhasil disimpan.');
        redirect('perangkat_pembelajaran/detail/' . $row->id_pembelajaran_mapel);
    }
}
