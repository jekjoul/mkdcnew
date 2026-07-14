<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ekstrakurikuler extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureEkskulTables();
    }

    public function index()
    {
        ifPermissions('menu_dashboard_guru'); // Guru pembina, Admin, dll

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Daftar Kegiatan Ekstrakurikuler';
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler';
        $this->page_data['page']->icon = 'solar:dialog-linear';

        // Filter: Jika Guru biasa, hanya tampilkan ekskul yang dibinanya. Jika Admin/Kepsek, tampilkan semua.
        $userId = logged('id');
        $user = $this->db->get_where('users', ['id' => $userId])->row();
        $ptk_id = $user ? (int) $user->id_ptk : 0;

        $user_roles = [];
        if ($this->db->table_exists('user_roles')) {
            $ur_res = $this->db->get_where('user_roles', ['user_id' => $userId])->result();
            if ($ur_res) {
                foreach ($ur_res as $ur) {
                    $r_row = $this->db->get_where('roles', ['id' => $ur->role_id])->row();
                    if ($r_row) {
                        $user_roles[] = strtolower((string) $r_row->title);
                    }
                }
            }
        }
        
        $is_admin = in_array('admin', $user_roles, true) || logged('role') == 1;

        $this->db->select('e.*, p.nama_ptk AS nama_pembina');
        $this->db->from('ekstrakurikuler e');
        $this->db->join('ptk p', 'p.id_ptk = e.id_ptk_pembina', 'left');
        if (!$is_admin && $ptk_id > 0) {
            $this->db->where('e.id_ptk_pembina', $ptk_id);
        }
        $this->db->order_by('e.nama_ekskul', 'ASC');
        $this->page_data['ekskul'] = $this->db->get()->result();
        $this->page_data['is_admin'] = $is_admin;

        // Ambil daftar guru aktif untuk form tambah ekskul
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk_list'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();

        $this->load->view('ekstrakurikuler/list', $this->page_data);
    }

    public function simpan()
    {
        ifPermissions('master_add');
        postAllowed();

        $data = [
            'nama_ekskul' => post('nama_ekskul'),
            'id_ptk_pembina' => post('id_ptk_pembina') ?: null,
            'keterangan' => post('keterangan'),
        ];

        $this->db->insert('ekstrakurikuler', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kegiatan ekstrakurikuler berhasil ditambahkan.');
        redirect('ekstrakurikuler');
    }

    public function hapus($id)
    {
        ifPermissions('master_delete');
        $this->db->delete('ekstrakurikuler', ['id_ekskul' => $id]);
        $this->db->delete('ekstrakurikuler_siswa', ['id_ekskul' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Ekstrakurikuler berhasil dihapus.');
        redirect('ekstrakurikuler');
    }

    public function detail($id)
    {
        ifPermissions('menu_dashboard_guru');
        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if (!$ekskul) {
            show_404();
        }

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Peserta & Nilai: ' . $ekskul->nama_ekskul;
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler/detail/' . $id;
        $this->page_data['page']->icon = 'solar:users-group-two-rounded-linear';

        $this->page_data['ekskul'] = $ekskul;

        // Ambil daftar siswa yang ikut ekskul ini
        $this->db->select('es.*, s.nama_siswa, s.nisn, s.rombel');
        $this->db->from('ekstrakurikuler_siswa es');
        $this->db->join('siswa s', 's.id_siswa = es.id_siswa');
        $this->db->where('es.id_ekskul', $id);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $this->page_data['peserta'] = $this->db->get()->result();

        // Ambil calon peserta ekskul (siswa aktif yang belum terdaftar di ekskul ini)
        $this->db->select('s.id_siswa, s.nama_siswa, s.rombel, s.nisn');
        $this->db->from('siswa s');
        $this->db->where('s.status_keaktifan', 'Aktif');
        $this->db->where("s.id_siswa NOT IN (SELECT id_siswa FROM ekstrakurikuler_siswa WHERE id_ekskul = $id)", null, false);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $this->page_data['calon_peserta'] = $this->db->get()->result();

        $this->load->view('ekstrakurikuler/detail', $this->page_data);
    }

    public function tambah_peserta($id_ekskul)
    {
        ifPermissions('menu_dashboard_guru');
        postAllowed();

        $data = [
            'id_ekskul' => $id_ekskul,
            'id_siswa' => (int) post('id_siswa'),
            'nilai' => 'A', // Default Nilai Sangat Baik
            'catatan' => 'Aktif mengikuti kegiatan',
        ];

        $this->db->insert('ekstrakurikuler_siswa', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Peserta ekskul berhasil ditambahkan.');
        redirect('ekstrakurikuler/detail/' . $id_ekskul);
    }

    public function hapus_peserta($id_ekskul_siswa, $id_ekskul)
    {
        ifPermissions('menu_dashboard_guru');
        $this->db->delete('ekstrakurikuler_siswa', ['id_ekskul_siswa' => $id_ekskul_siswa]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Peserta ekskul berhasil dikeluarkan.');
        redirect('ekstrakurikuler/detail/' . $id_ekskul);
    }

    public function update_nilai($id_ekskul)
    {
        ifPermissions('menu_dashboard_guru');
        postAllowed();

        $id_ekskul_siswa_arr = $this->input->post('id_ekskul_siswa');
        $nilai_arr = $this->input->post('nilai');
        $catatan_arr = $this->input->post('catatan');

        if (is_array($id_ekskul_siswa_arr)) {
            foreach ($id_ekskul_siswa_arr as $index => $id_es) {
                $this->db->where('id_ekskul_siswa', $id_es);
                $this->db->update('ekstrakurikuler_siswa', [
                    'nilai' => $nilai_arr[$index],
                    'catatan' => $catatan_arr[$index],
                ]);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Nilai & evaluasi ekskul berhasil disimpan.');
        redirect('ekstrakurikuler/detail/' . $id_ekskul);
    }

    private function ensureEkskulTables()
    {
        $this->load->dbforge();
        if (!$this->db->table_exists('ekstrakurikuler')) {
            $this->dbforge->add_field([
                'id_ekskul' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'nama_ekskul' => ['type' => 'VARCHAR', 'constraint' => 100],
                'id_ptk_pembina' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'keterangan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_ekskul', true);
            $this->dbforge->create_table('ekstrakurikuler', true);

            // Default seed
            $this->db->insert_batch('ekstrakurikuler', [
                ['nama_ekskul' => 'Pramuka Wajib', 'keterangan' => 'Gerakan Pramuka pangkalan sekolah'],
                ['nama_ekskul' => 'Paskibra', 'keterangan' => 'Pasukan Pengibar Bendera'],
                ['nama_ekskul' => 'Palang Merah Remaja (PMR)', 'keterangan' => 'Penyelamatan dan pertolongan pertama'],
                ['nama_ekskul' => 'Karya Ilmiah Remaja (KIR)', 'keterangan' => 'Penelitian sains remaja'],
                ['nama_ekskul' => 'Ekskul Olahraga / Futsal', 'keterangan' => 'Cabang futsal putra/putri'],
            ]);
        }

        if (!$this->db->table_exists('ekstrakurikuler_siswa')) {
            $this->dbforge->add_field([
                'id_ekskul_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_ekskul' => ['type' => 'INT', 'constraint' => 11],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'nilai' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true], // A, B, C, D
                'catatan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_ekskul_siswa', true);
            $this->dbforge->create_table('ekstrakurikuler_siswa', true);
        }
    }
}
