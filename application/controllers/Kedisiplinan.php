<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kedisiplinan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureKedisiplinanTables();
    }

    public function index()
    {
        ifPermissions('menu_kedisiplinan');
        
        $this->page_data['page']->title = 'Kedisiplinan';
        $this->page_data['page']->titleUrl = 'kedisiplinan';
        $this->page_data['page']->subtitle = 'Daftar Pelanggaran Siswa';
        $this->page_data['page']->subtitleUrl = 'kedisiplinan';
        $this->page_data['page']->icon = 'solar:shield-warning-linear';

        $this->db->select('kp.*, s.nama_siswa, s.nisn, s.rombel, kk.nama_pelanggaran, kk.bobot_poin');
        $this->db->from('kedisiplinan_pelanggaran_siswa kp');
        $this->db->join('siswa s', 's.id_siswa = kp.id_siswa');
        $this->db->join('kedisiplinan_pelanggaran_kategori kk', 'kk.id_kategori = kp.id_kategori', 'left');
        $this->db->order_by('kp.tanggal_pelanggaran', 'DESC');
        $this->page_data['pelanggaran'] = $this->db->get()->result();

        // Rekapitulasi poin per siswa
        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.rombel, COUNT(kp.id_pelanggaran_siswa) as total_pelanggaran, SUM(COALESCE(kk.bobot_poin, 0)) as total_poin');
        $this->db->from('siswa s');
        $this->db->join('kedisiplinan_pelanggaran_siswa kp', 's.id_siswa = kp.id_siswa');
        $this->db->join('kedisiplinan_pelanggaran_kategori kk', 'kk.id_kategori = kp.id_kategori', 'left');
        $this->db->group_by('s.id_siswa');
        $this->db->order_by('total_poin', 'DESC');
        $this->page_data['rekap_siswa'] = $this->db->get()->result();

        // Data Aturan Sanksi Dinamis
        $this->db->order_by('min_poin', 'ASC');
        $this->page_data['aturan_sanksi'] = $this->db->get('kedisiplinan_sanksi_aturan')->result();

        $this->load->view('kedisiplinan/list', $this->page_data);
    }

    public function kategori()
    {
        ifPermissions('kedisiplinan_kategori');
        $this->page_data['page']->title = 'Kedisiplinan';
        $this->page_data['page']->titleUrl = 'kedisiplinan';
        $this->page_data['page']->subtitle = 'Kategori Pelanggaran';
        $this->page_data['page']->subtitleUrl = 'kedisiplinan/kategori';
        $this->page_data['page']->icon = 'solar:settings-linear';

        $this->db->order_by('bobot_poin', 'ASC');
        $this->page_data['kategori'] = $this->db->get('kedisiplinan_pelanggaran_kategori')->result();
        $this->load->view('kedisiplinan/kategori', $this->page_data);
    }

    public function sanksi()
    {
        ifPermissions('kedisiplinan_kategori');
        $this->page_data['page']->title = 'Kedisiplinan';
        $this->page_data['page']->titleUrl = 'kedisiplinan';
        $this->page_data['page']->subtitle = 'Aturan Status Sanksi & Poin';
        $this->page_data['page']->subtitleUrl = 'kedisiplinan/sanksi';
        $this->page_data['page']->icon = 'solar:settings-linear';

        $this->db->order_by('min_poin', 'ASC');
        $this->page_data['sanksi'] = $this->db->get('kedisiplinan_sanksi_aturan')->result();
        $this->load->view('kedisiplinan/sanksi', $this->page_data);
    }

    public function sanksi_simpan()
    {
        ifPermissions('kedisiplinan_kategori');
        postAllowed();

        $id_sanksi = (int) post('id_sanksi');
        $data = [
            'min_poin' => (int) post('min_poin'),
            'max_poin' => (int) post('max_poin'),
            'nama_sanksi' => post('nama_sanksi'),
            'warna_badge' => post('warna_badge') ?: 'warning',
        ];

        if ($id_sanksi > 0) {
            $this->db->where('id_sanksi', $id_sanksi);
            $this->db->update('kedisiplinan_sanksi_aturan', $data);
            $msg = 'Aturan sanksi berhasil diperbarui.';
        } else {
            $this->db->insert('kedisiplinan_sanksi_aturan', $data);
            $msg = 'Aturan sanksi baru berhasil ditambahkan.';
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', $msg);
        redirect('kedisiplinan/sanksi');
    }

    public function sanksi_hapus($id)
    {
        ifPermissions('kedisiplinan_kategori');
        $this->db->delete('kedisiplinan_sanksi_aturan', ['id_sanksi' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Aturan sanksi berhasil dihapus.');
        redirect('kedisiplinan/sanksi');
    }

    public function kategori_simpan()
    {
        ifPermissions('kedisiplinan_kategori');
        postAllowed();

        $data = [
            'nama_pelanggaran' => post('nama_pelanggaran'),
            'bobot_poin' => (int) post('bobot_poin'),
        ];

        $this->db->insert('kedisiplinan_pelanggaran_kategori', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kategori pelanggaran berhasil ditambahkan.');
        redirect('kedisiplinan/kategori');
    }

    public function kategori_hapus($id)
    {
        ifPermissions('kedisiplinan_kategori');
        $this->db->delete('kedisiplinan_pelanggaran_kategori', ['id_kategori' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kategori pelanggaran berhasil dihapus.');
        redirect('kedisiplinan/kategori');
    }

    public function tambah()
    {
        ifPermissions('kedisiplinan_add');
        $this->page_data['page']->title = 'Kedisiplinan';
        $this->page_data['page']->titleUrl = 'kedisiplinan';
        $this->page_data['page']->subtitle = 'Input Pelanggaran Baru';
        $this->page_data['page']->subtitleUrl = 'kedisiplinan/tambah';
        $this->page_data['page']->icon = 'solar:shield-warning-linear';

        $this->db->order_by('nama_siswa', 'ASC');
        $this->page_data['siswa'] = $this->db->get_where('siswa', ['status_keaktifan' => 'Aktif'])->result();

        $this->db->order_by('nama_pelanggaran', 'ASC');
        $this->page_data['kategori'] = $this->db->get('kedisiplinan_pelanggaran_kategori')->result();

        $this->load->view('kedisiplinan/form', $this->page_data);
    }

    public function simpan()
    {
        ifPermissions('kedisiplinan_add');
        postAllowed();

        $data = [
            'id_siswa' => (int) post('id_siswa'),
            'id_kategori' => (int) post('id_kategori'),
            'tanggal_pelanggaran' => post('tanggal_pelanggaran') ?: date('Y-m-d'),
            'catatan' => post('catatan'),
            'tindak_lanjut' => post('tindak_lanjut') ?: 'Belum ditentukan',
            'pelapor' => post('pelapor') ?: logged('name'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('kedisiplinan_pelanggaran_siswa', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Laporan pelanggaran siswa berhasil ditambahkan.');
        redirect('kedisiplinan');
    }

    public function edit_tindak_lanjut($id)
    {
        postAllowed();
        ifPermissions('kedisiplinan_bk');

        $update_data = [
            'tindak_lanjut' => post('tindak_lanjut'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $id_kategori = (int) post('id_kategori');
        if ($id_kategori > 0) {
            $update_data['id_kategori'] = $id_kategori;
        }

        $this->db->where('id_pelanggaran_siswa', $id);
        $this->db->update('kedisiplinan_pelanggaran_siswa', $update_data);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Tindak lanjut BK berhasil diperbarui.');
        redirect('kedisiplinan');
    }

    public function hapus($id)
    {
        ifPermissions('kedisiplinan_delete');

        $this->db->delete('kedisiplinan_pelanggaran_siswa', ['id_pelanggaran_siswa' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Laporan pelanggaran berhasil dihapus.');
        redirect('kedisiplinan');
    }

    private function ensureKedisiplinanTables()
    {
        $this->load->dbforge();
        if (!$this->db->table_exists('kedisiplinan_pelanggaran_kategori')) {
            $this->dbforge->add_field([
                'id_kategori' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'nama_pelanggaran' => ['type' => 'VARCHAR', 'constraint' => 150],
                'bobot_poin' => ['type' => 'INT', 'constraint' => 5],
            ]);
            $this->dbforge->add_key('id_kategori', true);
            $this->dbforge->create_table('kedisiplinan_pelanggaran_kategori', true);

            // Seed default values
            $this->db->insert_batch('kedisiplinan_pelanggaran_kategori', [
                ['nama_pelanggaran' => 'Terlambat Masuk Sekolah', 'bobot_poin' => 5],
                ['nama_pelanggaran' => 'Membolos di Jam Pelajaran', 'bobot_poin' => 10],
                ['nama_pelanggaran' => 'Tidak Mengerjakan Tugas', 'bobot_poin' => 5],
                ['nama_pelanggaran' => 'Merusak Sarana Kelas', 'bobot_poin' => 20],
                ['nama_pelanggaran' => 'Membawa HP / Gadget Tanpa Izin', 'bobot_poin' => 10],
                ['nama_pelanggaran' => 'Tawuran / Berkelahi', 'bobot_poin' => 75],
                ['nama_pelanggaran' => 'Mencuri / Mengambil Hak Orang Lain', 'bobot_poin' => 50],
            ]);
        }

        if (!$this->db->table_exists('kedisiplinan_pelanggaran_siswa')) {
            $this->dbforge->add_field([
                'id_pelanggaran_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'id_kategori' => ['type' => 'INT', 'constraint' => 11],
                'tanggal_pelanggaran' => ['type' => 'DATE'],
                'catatan' => ['type' => 'TEXT', 'null' => true],
                'tindak_lanjut' => ['type' => 'TEXT', 'null' => true],
                'pelapor' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_pelanggaran_siswa', true);
            $this->dbforge->create_table('kedisiplinan_pelanggaran_siswa', true);
        } else {
            // Check if column 'pelapor' exists, if not add it
            if (!$this->db->field_exists('pelapor', 'kedisiplinan_pelanggaran_siswa')) {
                $this->dbforge->add_column('kedisiplinan_pelanggaran_siswa', [
                    'pelapor' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true]
                ]);
            }
        }

        if (!$this->db->table_exists('kedisiplinan_sanksi_aturan')) {
            $this->dbforge->add_field([
                'id_sanksi' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'min_poin' => ['type' => 'INT', 'constraint' => 5],
                'max_poin' => ['type' => 'INT', 'constraint' => 5],
                'nama_sanksi' => ['type' => 'VARCHAR', 'constraint' => 200],
                'warna_badge' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'warning'],
            ]);
            $this->dbforge->add_key('id_sanksi', true);
            $this->dbforge->create_table('kedisiplinan_sanksi_aturan', true);

            // Seed default sanksi rules
            $this->db->insert_batch('kedisiplinan_sanksi_aturan', [
                ['min_poin' => 1, 'max_poin' => 15, 'nama_sanksi' => 'Pembinaan Ringan (Wali Kelas / Guru)', 'warna_badge' => 'success'],
                ['min_poin' => 16, 'max_poin' => 30, 'nama_sanksi' => 'Peringatan I & Konseling BK', 'warna_badge' => 'warning'],
                ['min_poin' => 31, 'max_poin' => 50, 'nama_sanksi' => 'Peringatan II & Pemanggilan Orang Tua', 'warna_badge' => 'warning'],
                ['min_poin' => 51, 'max_poin' => 75, 'nama_sanksi' => 'Peringatan Keras / Skorsing', 'warna_badge' => 'danger'],
                ['min_poin' => 76, 'max_poin' => 999, 'nama_sanksi' => 'Pengembalian Kepada Orang Tua (DO)', 'warna_badge' => 'danger'],
            ]);
        }

        // Ensure permissions registered in 'permissions' table
        if ($this->db->table_exists('permissions')) {
            $parent = $this->db->get_where('permissions', ['code' => 'menu_kedisiplinan'])->row();
            $parentId = $parent ? $parent->id : null;
            $level = $parent ? ($parent->level + 1) : 2;

            $newPerms = [
                [
                    'code' => 'kedisiplinan_kategori',
                    'title' => 'Atur Kategori Poin Kedisiplinan'
                ],
                [
                    'code' => 'kedisiplinan_bk',
                    'title' => 'Tindak Lanjut BK Kedisiplinan'
                ],
                [
                    'code' => 'kedisiplinan_add',
                    'title' => 'Tambah Laporan Kedisiplinan'
                ],
                [
                    'code' => 'kedisiplinan_delete',
                    'title' => 'Hapus Laporan Kedisiplinan'
                ]
            ];

            foreach ($newPerms as $np) {
                $exist = $this->db->get_where('permissions', ['code' => $np['code']])->row();
                if (!$exist) {
                    $this->db->insert('permissions', [
                        'code' => $np['code'],
                        'title' => $np['title'],
                        'parent_id' => $parentId,
                        'level' => $level
                    ]);
                }

                if ($this->db->table_exists('role_permissions')) {
                    $rolesToGrant = array_filter(array_unique([1, $this->session->userdata('role')]));
                    foreach ($rolesToGrant as $rId) {
                        if (!empty($rId)) {
                            $hasRolePerm = $this->db->get_where('role_permissions', ['role' => $rId, 'permission' => $np['code']])->row();
                            if (!$hasRolePerm) {
                                $this->db->insert('role_permissions', [
                                    'role' => $rId,
                                    'permission' => $np['code']
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
