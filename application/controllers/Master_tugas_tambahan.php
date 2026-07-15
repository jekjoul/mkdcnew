<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tugas_tambahan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_master_tugas_tambahan');
    }

    private function ensurePermissions()
    {
        // 1. Daftarkan Menu Utama (Level 2)
        $menu_code = 'menu_master_tugas_tambahan';
        $menu_exists = $this->db->get_where('permissions', ['code' => $menu_code])->row();
        if (!$menu_exists) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_master'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $menu_code,
                'title' => 'Master Tugas Tambahan',
                'parent_id' => $parent_id,
                'level' => 2
            ]);
            $menu_id = $this->db->insert_id();

            $this->db->insert('role_permissions', ['role' => 1, 'permission' => $menu_code]);
        } else {
            $menu_id = $menu_exists->id;
        }

        // 2. Daftarkan Sub Permissions / Features (Level 3)
        $sub_permissions = [
            'master_tugas_tambahan_list' => 'Melihat Master Tugas Tambahan',
            'master_tugas_tambahan_add' => 'Menambah Master Tugas Tambahan',
            'master_tugas_tambahan_edit' => 'Mengubah Master Tugas Tambahan',
            'master_tugas_tambahan_delete' => 'Menghapus Master Tugas Tambahan'
        ];

        foreach ($sub_permissions as $code => $title) {
            $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
            if ($exists == 0) {
                $this->db->insert('permissions', [
                    'code' => $code,
                    'title' => $title,
                    'parent_id' => $menu_id,
                    'level' => 3
                ]);
                $this->db->insert('role_permissions', ['role' => 1, 'permission' => $code]);
            }
        }
    }

    public function index()
    {
        ifPermissions('master_tugas_tambahan_list');

        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master_tugas_tambahan';
        $this->page_data['page']->subtitle = 'Master Tugas Tambahan';
        $this->page_data['page']->subtitleUrl = 'master_tugas_tambahan';
        $this->page_data['page']->icon = 'solar:layers-linear';

        $this->page_data['items'] = $this->db->order_by('nama', 'ASC')->get('master_tugas_tambahan')->result();

        $this->load->view('master_tugas_tambahan/index', $this->page_data);
    }

    public function tambah()
    {
        ifPermissions('master_tugas_tambahan_add');

        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master_tugas_tambahan';
        $this->page_data['page']->subtitle = 'Tambah Tugas Tambahan';
        $this->page_data['page']->subtitleUrl = 'master_tugas_tambahan/tambah';
        $this->page_data['page']->icon = 'solar:layers-linear';
        $this->page_data['row'] = null;

        $this->load->view('master_tugas_tambahan/form', $this->page_data);
    }

    public function edit($id)
    {
        ifPermissions('master_tugas_tambahan_edit');

        $row = $this->db->get_where('master_tugas_tambahan', ['id' => $id])->row();
        if (!$row) {
            show_404();
        }

        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master_tugas_tambahan';
        $this->page_data['page']->subtitle = 'Edit Tugas Tambahan';
        $this->page_data['page']->subtitleUrl = 'master_tugas_tambahan/edit/' . $id;
        $this->page_data['page']->icon = 'solar:layers-linear';
        $this->page_data['row'] = $row;

        $this->load->view('master_tugas_tambahan/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        $id = post('id');
        $jenis = post('jenis');
        $nama = post('nama');
        $deskripsi = post('deskripsi') ?: null;

        if ($id) {
            ifPermissions('master_tugas_tambahan_edit');
        } else {
            ifPermissions('master_tugas_tambahan_add');
        }

        if (empty($jenis) || empty($nama)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Harap isi semua kolom yang wajib diisi!');
            redirect('master_tugas_tambahan');
            return;
        }

        $data = [
            'jenis' => $jenis,
            'nama' => $nama,
            'deskripsi' => $deskripsi
        ];

        if ($id) {
            $this->db->where('id', $id)->update('master_tugas_tambahan', $data);
            $this->activity_model->add(logged('name') . ' mengubah tugas tambahan: ' . $nama);
            $this->session->set_flashdata('alert', 'Data berhasil diperbarui');
        } else {
            $this->db->insert('master_tugas_tambahan', $data);
            $this->activity_model->add(logged('name') . ' menambahkan tugas tambahan: ' . $nama);
            $this->session->set_flashdata('alert', 'Data berhasil ditambahkan');
        }

        redirect('master_tugas_tambahan');
    }

    public function hapus($id)
    {
        ifPermissions('master_tugas_tambahan_delete');

        $row = $this->db->get_where('master_tugas_tambahan', ['id' => $id])->row();
        if ($row) {
            $this->db->delete('master_tugas_tambahan', ['id' => $id]);
            $this->activity_model->add(logged('name') . ' menghapus tugas tambahan: ' . $row->nama);
            $this->session->set_flashdata('alert', 'Data berhasil dihapus');
        }
        redirect('master_tugas_tambahan');
    }
}
