<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelas_jauh extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureKelasJauhTables();
        $this->ensurePermissions();
    }

    public function index()
    {
        ifPermissions('kelas_jauh_list');

        $this->page_data['page']->title = 'Kelas Jauh';
        $this->page_data['page']->titleUrl = 'kelas_jauh';
        $this->page_data['page']->subtitle = 'Daftar Kelas Jauh (Siswa Menginduk)';
        $this->page_data['page']->subtitleUrl = 'kelas_jauh';
        $this->page_data['page']->icon = 'solar:globus-linear';

        // Mengambil semua data Kelas Jauh
        $this->db->select('kj.*, tp.tahun_pelajaran, tp.semester');
        $this->db->from('kelas_jauh kj');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = kj.id_tahun_pelajaran', 'left');
        $this->db->order_by('tp.status', 'ASC');
        $this->db->order_by('kj.nama_kelas_jauh', 'ASC');
        $rows = $this->db->get()->result();

        // Hitung jumlah siswa di tiap Kelas Jauh
        foreach ($rows as $row) {
            $row->jumlah_siswa = $this->db->where('id_kelas_jauh', $row->id_kelas_jauh)->count_all_results('kelas_jauh_siswa');
        }

        $this->page_data['kelas_jauh'] = $rows;
        $this->load->view('kelas_jauh/list', $this->page_data);
    }

    public function tambah()
    {
        ifPermissions('kelas_jauh_add');

        $this->page_data['page']->title = 'Kelas Jauh';
        $this->page_data['page']->titleUrl = 'kelas_jauh';
        $this->page_data['page']->subtitle = 'Tambah Kelas Jauh';
        $this->page_data['page']->subtitleUrl = 'kelas_jauh/tambah';
        $this->page_data['page']->icon = 'solar:globus-linear';

        $this->page_data['row'] = null;
        $this->page_data['form_action'] = url('kelas_jauh/simpan');
        $this->page_data['ta_list'] = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->result();

        $this->load->view('kelas_jauh/form', $this->page_data);
    }

    public function edit($id)
    {
        ifPermissions('kelas_jauh_edit');

        $row = $this->db->get_where('kelas_jauh', ['id_kelas_jauh' => $id])->row();
        if (!$row) {
            show_404();
        }

        $this->page_data['page']->title = 'Kelas Jauh';
        $this->page_data['page']->titleUrl = 'kelas_jauh';
        $this->page_data['page']->subtitle = 'Edit Kelas Jauh';
        $this->page_data['page']->subtitleUrl = 'kelas_jauh/edit/' . $id;
        $this->page_data['page']->icon = 'solar:globus-linear';

        $this->page_data['row'] = $row;
        $this->page_data['form_action'] = url('kelas_jauh/simpan');
        $this->page_data['ta_list'] = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->result();

        $this->load->view('kelas_jauh/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        $id = (int) post('id_kelas_jauh');
        if ($id > 0) {
            ifPermissions('kelas_jauh_edit');
        } else {
            ifPermissions('kelas_jauh_add');
        }

        $data = [
            'nama_kelas_jauh' => post('nama_kelas_jauh'),
            'id_tahun_pelajaran' => post('id_tahun_pelajaran'),
            'keterangan' => post('keterangan'),
        ];

        if ($id > 0) {
            $this->db->where('id_kelas_jauh', $id);
            $this->db->update('kelas_jauh', $data);
            $this->session->set_flashdata('alert', 'Kelas Jauh berhasil diperbarui.');
        } else {
            $this->db->insert('kelas_jauh', $data);
            $this->session->set_flashdata('alert', 'Kelas Jauh berhasil ditambahkan.');
        }

        $this->session->set_flashdata('alert-type', 'success');
        redirect('kelas_jauh');
    }

    public function hapus($id)
    {
        ifPermissions('kelas_jauh_delete');

        $this->db->delete('kelas_jauh', ['id_kelas_jauh' => $id]);
        $this->db->delete('kelas_jauh_siswa', ['id_kelas_jauh' => $id]);
        
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kelas Jauh berhasil dihapus.');
        redirect('kelas_jauh');
    }

    public function daftar_siswa($id)
    {
        ifPermissions('kelas_jauh_anggota');

        $kelas_jauh = $this->db->get_where('kelas_jauh', ['id_kelas_jauh' => $id])->row();
        if (!$kelas_jauh) {
            show_404();
        }

        $this->page_data['page']->title = 'Kelas Jauh';
        $this->page_data['page']->titleUrl = 'kelas_jauh';
        $this->page_data['page']->subtitle = 'Anggota Kelas Jauh: ' . $kelas_jauh->nama_kelas_jauh;
        $this->page_data['page']->subtitleUrl = 'kelas_jauh/daftar_siswa/' . $id;
        $this->page_data['page']->icon = 'solar:globus-linear';

        $this->page_data['kelas_jauh'] = $kelas_jauh;

        // Ambil semua siswa aktif
        $this->db->order_by('nama_siswa', 'ASC');
        $this->page_data['siswa'] = $this->db->get_where('siswa', ['status_keaktifan' => 'Aktif'])->result();

        // Ambil ID siswa yang sudah terpilih di kelas jauh ini
        $siswa_terpilih = [];
        foreach ($this->db->get_where('kelas_jauh_siswa', ['id_kelas_jauh' => $id])->result() as $ks) {
            $siswa_terpilih[] = (int) $ks->id_siswa;
        }
        $this->page_data['siswa_terpilih'] = $siswa_terpilih;

        $this->load->view('kelas_jauh/siswa', $this->page_data);
    }

    public function simpan_siswa($id)
    {
        postAllowed();
        ifPermissions('kelas_jauh_anggota');

        $kelas_jauh = $this->db->get_where('kelas_jauh', ['id_kelas_jauh' => $id])->row();
        if (!$kelas_jauh) {
            show_404();
        }

        $this->db->delete('kelas_jauh_siswa', ['id_kelas_jauh' => $id]);
        $siswa_ids = $this->input->post('siswa');

        if (is_array($siswa_ids)) {
            foreach ($siswa_ids as $sid) {
                $old_val = $this->db->get_where('kelas_jauh_siswa', ['id_kelas_jauh' => $id, 'id_siswa' => $sid])->row();
                $this->db->insert('kelas_jauh_siswa', [
                    'id_kelas_jauh' => $id,
                    'id_siswa' => (int) $sid,
                    'catatan' => $old_val ? $old_val->catatan : 'Siswa menginduk',
                ]);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Anggota Kelas Jauh berhasil diperbarui.');
        redirect('kelas_jauh');
    }

    public function detail($id)
    {
        ifPermissions('kelas_jauh_list');

        $kelas_jauh = $this->db->get_where('kelas_jauh', ['id_kelas_jauh' => $id])->row();
        if (!$kelas_jauh) {
            show_404();
        }

        $this->page_data['page']->title = 'Kelas Jauh';
        $this->page_data['page']->titleUrl = 'kelas_jauh';
        $this->page_data['page']->subtitle = 'Detail Anggota Kelas Jauh: ' . $kelas_jauh->nama_kelas_jauh;
        $this->page_data['page']->subtitleUrl = 'kelas_jauh/detail/' . $id;
        $this->page_data['page']->icon = 'solar:globus-linear';

        $this->page_data['kelas_jauh'] = $kelas_jauh;

        // Ambil daftar siswa yang menginduk di kelas jauh ini
        $this->db->select('kjs.*, s.nama_siswa, s.nisn, s.rombel, s.status_keaktifan');
        $this->db->from('kelas_jauh_siswa kjs');
        $this->db->join('siswa s', 's.id_siswa = kjs.id_siswa');
        $this->db->where('kjs.id_kelas_jauh', $id);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $this->page_data['peserta'] = $this->db->get()->result();

        $this->load->view('kelas_jauh/detail', $this->page_data);
    }

    private function ensureKelasJauhTables()
    {
        $this->load->dbforge();
        if (!$this->db->table_exists('kelas_jauh')) {
            $this->dbforge->add_field([
                'id_kelas_jauh' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_tahun_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nama_kelas_jauh' => ['type' => 'VARCHAR', 'constraint' => 100],
                'keterangan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_kelas_jauh', true);
            $this->dbforge->create_table('kelas_jauh', true);
        }

        if (!$this->db->table_exists('kelas_jauh_siswa')) {
            $this->dbforge->add_field([
                'id_kelas_jauh_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_kelas_jauh' => ['type' => 'INT', 'constraint' => 11],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'catatan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_kelas_jauh_siswa', true);
            $this->dbforge->create_table('kelas_jauh_siswa', true);
        }
    }

    private function ensurePermissions()
    {
        $perms = [
            'menu_kelas_jauh' => 'Menampilkan Menu Kelas Jauh',
            'kelas_jauh_list' => 'Melihat Daftar Kelas Jauh',
            'kelas_jauh_add' => 'Menambah Kelas Jauh Baru',
            'kelas_jauh_edit' => 'Mengubah Kelas Jauh',
            'kelas_jauh_delete' => 'Menghapus Kelas Jauh',
            'kelas_jauh_anggota' => 'Mengelola Anggota Kelas Jauh',
        ];

        foreach ($perms as $code => $title) {
            // Cek apakah permission sudah ada
            $exist = $this->db->get_where('permissions', ['code' => $code])->row();
            if (!$exist) {
                $this->db->insert('permissions', [
                    'code' => $code,
                    'title' => $title
                ]);
            }

            // Berikan akses default ke Admin (role = 1)
            $role_perm_exist = $this->db->get_where('role_permissions', [
                'role' => 1,
                'permission' => $code
            ])->row();
            if (!$role_perm_exist) {
                $this->db->insert('role_permissions', [
                    'role' => 1,
                    'permission' => $code
                ]);
            }
        }
    }
}
