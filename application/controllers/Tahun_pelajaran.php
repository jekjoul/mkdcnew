<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tahun_pelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tahun_pelajaran_model');
    }

    public function index()
    {
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tahun_pelajaran';
        $this->page_data['page']->subtitle = 'Tahun Pelajaran';
        $this->page_data['page']->subtitleUrl = 'tahun_pelajaran';
        $this->page_data['page']->icon = 'solar:calendar-date-linear';
        $this->page_data['tahun_pelajaran'] = $this->tahun_pelajaran_model->get();
        $this->load->view('tahun_pelajaran/list', $this->page_data);
    }

    public function add()
    {
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tahun_pelajaran';
        $this->page_data['page']->subtitle = 'Tambah Tahun Pelajaran';
        $this->page_data['page']->subtitleUrl = 'tahun_pelajaran';
        $this->page_data['page']->icon = 'solar:calendar-date-linear';
        $this->page_data['row'] = null;
        $this->load->view('tahun_pelajaran/form', $this->page_data);
    }

    public function edit($id)
    {
        $this->page_data['row'] = $this->tahun_pelajaran_model->getById($id);
        if (!$this->page_data['row']) show_404();

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tahun_pelajaran';
        $this->page_data['page']->subtitle = 'Edit Tahun Pelajaran';
        $this->page_data['page']->subtitleUrl = 'tahun_pelajaran';
        $this->page_data['page']->icon = 'solar:calendar-date-linear';
        $this->load->view('tahun_pelajaran/form', $this->page_data);
    }

    public function save()
    {
        postAllowed();
        $tahun = post('tahun_pelajaran');
        $semester = post('semester');
        $status = post('status');

        // Validasi Duplikat: Mencegah kombinasi tahun pelajaran dan semester yang sama
        $exists = $this->db->get_where('pembelajaran_tahun_pelajaran', ['tahun_pelajaran' => $tahun, 'semester' => $semester])->num_rows();
        if ($exists > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Data Gagal Disimpan! Tahun Pelajaran $tahun Semester $semester sudah tersedia.");
            redirect('tahun_pelajaran');
            return;
        }

        // Jika status Aktif, otomatis nonaktifkan semua tahun pelajaran lainnya
        if ($status == 'Aktif') {
            $this->db->update('pembelajaran_tahun_pelajaran', ['status' => 'Nonaktif']);
        }

        $data = ['tahun_pelajaran' => $tahun, 'semester' => $semester, 'status' => $status];

        if ($this->db->insert('pembelajaran_tahun_pelajaran', $data)) {
            $this->activity_model->add(logged('name') . ' Menambah Tahun Pelajaran: ' . $data['tahun_pelajaran']);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Tahun Pelajaran Berhasil Ditambahkan');
        }
        redirect('tahun_pelajaran');
    }

    public function update($id)
    {
        postAllowed();
        $tahun = post('tahun_pelajaran');
        $semester = post('semester');
        $status = post('status');

        // Validasi Duplikat selain data yang sedang diedit
        $exists = $this->db->get_where('pembelajaran_tahun_pelajaran', ['tahun_pelajaran' => $tahun, 'semester' => $semester, 'id_tahun_pelajaran !=' => $id])->num_rows();
        if ($exists > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Update Gagal! Tahun Pelajaran $tahun Semester $semester sudah tersedia.");
            redirect('tahun_pelajaran');
            return;
        }

        // Jika status Aktif, otomatis nonaktifkan semua tahun pelajaran lainnya
        if ($status == 'Aktif') {
            $this->db->update('pembelajaran_tahun_pelajaran', ['status' => 'Nonaktif']);
        }

        $data = ['tahun_pelajaran' => $tahun, 'semester' => $semester, 'status' => $status];

        $this->db->where('id_tahun_pelajaran', $id);
        if ($this->db->update('pembelajaran_tahun_pelajaran', $data)) {
            $this->activity_model->add(logged('name') . ' Mengubah Tahun Pelajaran: ' . $data['tahun_pelajaran']);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Tahun Pelajaran Berhasil Diperbarui');
        }
        redirect('tahun_pelajaran');
    }

    public function delete($id)
    {
        $row = $this->tahun_pelajaran_model->getById($id);
        if ($row) {
            $this->db->delete('pembelajaran_tahun_pelajaran', ['id_tahun_pelajaran' => $id]);
            $this->activity_model->add(logged('name') . ' Menghapus Tahun Pelajaran: ' . $row->tahun_pelajaran);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Tahun Pelajaran Berhasil Dihapus');
        }
        redirect('tahun_pelajaran');
    }

    public function checkIfUnique()
    {
        $tahun = get('tahun_pelajaran');
        $semester = get('semester');
        $id = get('id_tahun_pelajaran');

        if (!$tahun || !$semester) {
            echo 'true';
            return;
        }

        $arg = [
            'tahun_pelajaran' => $tahun,
            'semester' => $semester
        ];

        if ($id) {
            $arg['id_tahun_pelajaran !='] = $id;
        }

        $exists = $this->db->get_where('pembelajaran_tahun_pelajaran', $arg)->num_rows();
        echo $exists > 0 ? 'false' : 'true';
    }
}
