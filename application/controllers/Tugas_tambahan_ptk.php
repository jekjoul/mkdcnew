<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tugas_tambahan_ptk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_tugas_tambahan_ptk');
    }

    private function ensurePermissions()
    {
        // 1. Daftarkan Menu Utama (Level 2)
        $menu_code = 'menu_tugas_tambahan_ptk';
        $menu_exists = $this->db->get_where('permissions', ['code' => $menu_code])->row();
        if (!$menu_exists) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_pembelajaran'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $menu_code,
                'title' => 'Tugas Tambahan PTK',
                'parent_id' => $parent_id,
                'level' => 2
            ]);
            $menu_id = $this->db->insert_id();

            $this->db->insert('role_permissions', ['role' => 1, 'permission' => $menu_code]);
            $this->db->insert('role_permissions', ['role' => 3, 'permission' => $menu_code]);
        } else {
            $menu_id = $menu_exists->id;
        }

        // 2. Daftarkan Sub Permissions / Features (Level 3)
        $sub_permissions = [
            'tugas_tambahan_ptk_list' => 'Melihat Tugas Tambahan PTK',
            'tugas_tambahan_ptk_add' => 'Menambah Tugas Tambahan PTK',
            'tugas_tambahan_ptk_edit' => 'Mengubah Tugas Tambahan PTK',
            'tugas_tambahan_ptk_delete' => 'Menghapus Tugas Tambahan PTK'
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
                $this->db->insert('role_permissions', ['role' => 3, 'permission' => $code]);
            }
        }
    }

    public function index()
    {
        ifPermissions('tugas_tambahan_ptk_list');

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tugas_tambahan_ptk';
        $this->page_data['page']->subtitle = 'Tugas Tambahan PTK';
        $this->page_data['page']->subtitleUrl = 'tugas_tambahan_ptk';
        $this->page_data['page']->icon = 'solar:user-id-linear';

        // Join query to get data
        $this->db->select('t.*, p.nama_ptk, m.nama as nama_tugas, m.jenis');
        $this->db->from('tugas_tambahan_ptk t');
        $this->db->join('ptk p', 't.id_ptk = p.id_ptk');
        $this->db->join('master_tugas_tambahan m', 't.id_tugas_tambahan = m.id');
        $this->db->order_by('p.nama_ptk', 'ASC');
        $this->page_data['items'] = $this->db->get()->result();

        $this->load->view('tugas_tambahan_ptk/index', $this->page_data);
    }

    public function tambah()
    {
        ifPermissions('tugas_tambahan_ptk_add');

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tugas_tambahan_ptk';
        $this->page_data['page']->subtitle = 'Tambah Tugas Tambahan PTK';
        $this->page_data['page']->subtitleUrl = 'tugas_tambahan_ptk/tambah';
        $this->page_data['page']->icon = 'solar:user-id-linear';
        $this->page_data['row'] = null;

        // Get active PTK
        $this->page_data['ptk_list'] = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk')->result();
        // Get Master Tugas Tambahan
        $this->page_data['tugas_list'] = $this->db->order_by('nama', 'ASC')->get('master_tugas_tambahan')->result();

        $this->load->view('tugas_tambahan_ptk/form', $this->page_data);
    }

    public function edit($id)
    {
        ifPermissions('tugas_tambahan_ptk_edit');

        $row = $this->db->get_where('tugas_tambahan_ptk', ['id' => $id])->row();
        if (!$row) {
            show_404();
        }

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'tugas_tambahan_ptk';
        $this->page_data['page']->subtitle = 'Edit Tugas Tambahan PTK';
        $this->page_data['page']->subtitleUrl = 'tugas_tambahan_ptk/edit/' . $id;
        $this->page_data['page']->icon = 'solar:user-id-linear';
        $this->page_data['row'] = $row;

        // Get active PTK
        $this->page_data['ptk_list'] = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk')->result();
        // Get Master Tugas Tambahan
        $this->page_data['tugas_list'] = $this->db->order_by('nama', 'ASC')->get('master_tugas_tambahan')->result();

        $this->load->view('tugas_tambahan_ptk/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        $id = post('id');
        $id_ptk = post('id_ptk');
        $id_tugas_tambahan = post('id_tugas_tambahan');
        $no_sk = post('no_sk');
        $tgl_sk = post('tgl_sk') ?: null;
        $tmt = post('tmt') ?: null;
        $tst = post('tst') ?: null;

        if ($id) {
            ifPermissions('tugas_tambahan_ptk_edit');
        } else {
            ifPermissions('tugas_tambahan_ptk_add');
        }

        if (empty($id_ptk) || empty($id_tugas_tambahan)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Harap isi nama PTK dan Tugas Tambahan!');
            redirect('tugas_tambahan_ptk');
            return;
        }

        $data = [
            'id_ptk' => $id_ptk,
            'id_tugas_tambahan' => $id_tugas_tambahan,
            'no_sk' => $no_sk,
            'tgl_sk' => $tgl_sk,
            'tmt' => $tmt,
            'tst' => $tst
        ];

        $ptk = $this->db->get_where('ptk', ['id_ptk' => $id_ptk])->row();
        $tugas = $this->db->get_where('master_tugas_tambahan', ['id' => $id_tugas_tambahan])->row();
        $ptk_name = $ptk ? $ptk->nama_ptk : 'Unknown';
        $tugas_name = $tugas ? $tugas->nama : 'Unknown';

        if ($id) {
            $this->db->where('id', $id)->update('tugas_tambahan_ptk', $data);
            $this->activity_model->add(logged('name') . " mengubah tugas tambahan PTK: $ptk_name sebagai $tugas_name");
            $this->session->set_flashdata('alert', 'Data berhasil diperbarui');
        } else {
            $this->db->insert('tugas_tambahan_ptk', $data);
            $this->activity_model->add(logged('name') . " menambahkan tugas tambahan PTK: $ptk_name sebagai $tugas_name");
            $this->session->set_flashdata('alert', 'Data berhasil ditambahkan');
        }

        redirect('tugas_tambahan_ptk');
    }

    public function hapus($id)
    {
        ifPermissions('tugas_tambahan_ptk_delete');

        $this->db->select('t.*, p.nama_ptk, m.nama as nama_tugas');
        $this->db->from('tugas_tambahan_ptk t');
        $this->db->join('ptk p', 't.id_ptk = p.id_ptk');
        $this->db->join('master_tugas_tambahan m', 't.id_tugas_tambahan = m.id');
        $this->db->where('t.id', $id);
        $row = $this->db->get()->row();

        if ($row) {
            $this->db->delete('tugas_tambahan_ptk', ['id' => $id]);
            $this->activity_model->add(logged('name') . " menghapus tugas tambahan PTK: {$row->nama_ptk} sebagai {$row->nama_tugas}");
            $this->session->set_flashdata('alert', 'Data berhasil dihapus');
        }
        redirect('tugas_tambahan_ptk');
    }
}
