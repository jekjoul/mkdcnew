<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';
    public $standar_sarana = 'master_standar_sarana';


    public function jenisRuangan()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_ruangan'] = $this->master_model->getJenisRuangan();
        $this->load->view('master/v_jenis_ruangan_list', $this->page_data);
    }

    public function jenisRuanganTambah()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->subsubtitle = 'Tambah';
        $this->page_data['page']->subsubtitleUrl = 'sarpras/tanahTabah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->load->view('sarpras/v_tanah_add', $this->page_data);
    }

    public function jenisRuanganSimpan()
    {
        $nama = $this->input->post('nama_jenis_ruangan');
        $data = array(
            'nama_jenis_ruangan' => $this->input->post('nama_jenis_ruangan'),
            'status' => $this->input->post('status'),
        );
        $this->db->insert($this->jenis_ruangan, $data);

        $dbaseerror = $this->db->error();
        $numbererror = $dbaseerror['code'];
        $messagerror = $dbaseerror['message'];

        if (!$numbererror) {
            $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data jenis ruangan baru - ' . $nama, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Tambah Jenis Ruangan Berhasil');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Tambah Jenis Ruangan Gagal!');
        }

        redirect('master/jenisRuangan');
    }

    public function jenisRuanganEdit($id)
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_ruangan'] = $this->master_model->getDetailJenisRuangan($id);
        $this->load->view('master/v_jenis_ruangan_form', $this->page_data);
    }

    public function jenisRuanganUpdate($id)
    {
        $nama = $this->input->post('nama_jenis_ruangan');

        $caridata = $this->master_model->jenisRuanganNamaExist($nama);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Update Gagal! Jenis ' . $nama . ' sudah tersedia.');
            redirect('master/jenisRuangan');
        } else {

            $data = array(
                'nama_jenis_ruangan' => $this->input->post('nama_jenis_ruangan'),
                'status' => $this->input->post('status'),
            );
            $this->db->where('id_jenis_ruangan', $id);
            $this->db->update($this->jenis_ruangan, $data);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data jenis ruangan baru - ' . $nama, logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Update Jenis Ruangan Berhasil');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Update Jenis Ruangan Gagal!');
            }

            redirect('master/jenisRuangan');
        }
    }

    public function jenisRuanganDelete($id)
    {

        $caridata = $this->master_model->jenisRuanganExist($id);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Tidak bisa dihapus! Terdapat " . $caridata . " ruangan yang menggunakan jenis ini.");
            redirect('master/jenisRuangan');
        } else {
            $this->db->where('id_jenis_ruangan', $id);
            $this->db->delete($this->jenis_ruangan);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan hapus data jenis ruangan', logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Jenis Ruangan Berhasil Dihapus');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Jenis Ruangan Gagal Dihapus!');
            }

            redirect('master/jenisRuangan');
        }
    }
}
