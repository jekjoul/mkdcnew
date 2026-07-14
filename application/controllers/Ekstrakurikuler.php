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
        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Daftar Kegiatan Ekstrakurikuler';
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler';
        $this->page_data['page']->icon = 'solar:dialog-linear';

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
        
        $is_admin = in_array('admin', $user_roles, true) || logged('role') == 1 || hasPermissions('pembelajaran_list');

        $this->db->select('e.*, tp.tahun_pelajaran, tp.semester');
        $this->db->from('ekstrakurikuler e');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = e.id_tahun_pelajaran', 'left');
        $this->db->order_by('tp.status', 'ASC');
        $this->db->order_by('e.nama_ekskul', 'ASC');
        $raw_ekskul = $this->db->get()->result();

        // Filter dinamis untuk guru pembina non-admin
        $filtered_ekskul = [];
        foreach ($raw_ekskul as $row) {
            if ($is_admin) {
                $filtered_ekskul[] = $row;
            } else {
                if ($ptk_id > 0 && !empty($row->id_ptk_pembina)) {
                    $decoded = json_decode($row->id_ptk_pembina, true);
                    if (is_array($decoded) && in_array($ptk_id, $decoded, true)) {
                        $filtered_ekskul[] = $row;
                    } elseif ((int)$row->id_ptk_pembina === $ptk_id) {
                        $filtered_ekskul[] = $row;
                    }
                }
            }
        }

        $this->page_data['ekskul'] = $filtered_ekskul;
        $this->page_data['is_admin'] = $is_admin;

        // Ambil daftar guru aktif & tahun pelajaran
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk_list'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
        $this->page_data['ta_list'] = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->result();

        $this->load->view('ekstrakurikuler/list', $this->page_data);
    }

    public function tambah()
    {
        $is_admin = (logged('role') == 1 || ($this->db->table_exists('user_roles') && $this->db->get_where('user_roles', ['user_id' => $userId, 'role_id' => 1])->num_rows() > 0));
        if (!$is_admin) {
            show_error('Hanya Admin yang diizinkan untuk menambah kegiatan ekstrakurikuler.', 403, 'Akses Ditolak');
        }

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Tambah Ekstrakurikuler';
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler/tambah';
        $this->page_data['page']->icon = 'solar:dialog-linear';

        $this->page_data['row'] = null;
        $this->page_data['form_action'] = url('ekstrakurikuler/simpan');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk_list'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
        $this->page_data['ta_list'] = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->result();

        $this->load->view('ekstrakurikuler/form', $this->page_data);
    }

    public function edit($id)
    {
        $userId = logged('id');
        $is_admin = (logged('role') == 1 || $this->db->get_where('user_roles', ['user_id' => $userId, 'role_id' => 1])->num_rows() > 0);
        if (!$is_admin) {
            show_error('Hanya Admin yang diizinkan untuk mengubah konfigurasi ekstrakurikuler.', 403, 'Akses Ditolak');
        }

        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if (!$ekskul) {
            show_404();
        }

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Edit Ekstrakurikuler';
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler/edit/' . $id;
        $this->page_data['page']->icon = 'solar:dialog-linear';

        $this->page_data['row'] = $ekskul;
        $this->page_data['form_action'] = url('ekstrakurikuler/simpan');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk_list'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
        $this->page_data['ta_list'] = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->result();

        $this->load->view('ekstrakurikuler/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        $userId = logged('id');
        $is_admin = (logged('role') == 1 || $this->db->get_where('user_roles', ['user_id' => $userId, 'role_id' => 1])->num_rows() > 0);
        if (!$is_admin) {
            show_error('Hanya Admin yang diizinkan untuk menyimpan data ekstrakurikuler.', 403, 'Akses Ditolak');
        }

        $id = (int) post('id_ekskul');
        $pembinas = $this->input->post('id_ptk_pembina');
        $pembinas_json = null;
        if (is_array($pembinas) && !empty($pembinas)) {
            $pembinas_json = json_encode(array_map('intval', $pembinas));
        }

        $data = [
            'nama_ekskul' => post('nama_ekskul'),
            'id_ptk_pembina' => $pembinas_json,
            'id_tahun_pelajaran' => post('id_tahun_pelajaran'),
            'keterangan' => post('keterangan'),
        ];

        // Upload logo jika ada
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/ekskul/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();
                $data['logo'] = $upload_data['file_name'];

                // Hapus logo lama jika update
                if ($id > 0) {
                    $old = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
                    if ($old && !empty($old->logo) && file_exists('./uploads/ekskul/' . $old->logo)) {
                        unlink('./uploads/ekskul/' . $old->logo);
                    }
                }
            }
        }

        if ($id > 0) {
            $this->db->where('id_ekskul', $id);
            $this->db->update('ekstrakurikuler', $data);
            $this->session->set_flashdata('alert', 'Ekstrakurikuler berhasil diperbarui.');
        } else {
            $this->db->insert('ekstrakurikuler', $data);
            $this->session->set_flashdata('alert', 'Ekstrakurikuler berhasil ditambahkan.');
        }

        $this->session->set_flashdata('alert-type', 'success');
        redirect('ekstrakurikuler');
    }

    public function hapus($id)
    {
        $userId = logged('id');
        $is_admin = (logged('role') == 1 || $this->db->get_where('user_roles', ['user_id' => $userId, 'role_id' => 1])->num_rows() > 0);
        if (!$is_admin) {
            show_error('Hanya Admin yang diizinkan untuk menghapus data ekstrakurikuler.', 403, 'Akses Ditolak');
        }

        $old = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if ($old && !empty($old->logo) && file_exists('./uploads/ekskul/' . $old->logo)) {
            unlink('./uploads/ekskul/' . $old->logo);
        }
        $this->db->delete('ekstrakurikuler', ['id_ekskul' => $id]);
        $this->db->delete('ekstrakurikuler_siswa', ['id_ekskul' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Ekstrakurikuler berhasil dihapus.');
        redirect('ekstrakurikuler');
    }

    private function checkPembinaAccess($ekskul)
    {
        $userId = logged('id');
        $user = $this->db->get_where('users', ['id' => $userId])->row();
        $ptk_id = $user ? (int) $user->id_ptk : 0;
        
        $is_admin = (logged('role') == 1 || ($this->db->table_exists('user_roles') && $this->db->get_where('user_roles', ['user_id' => $userId, 'role_id' => 1])->num_rows() > 0));
        if ($is_admin) {
            return true;
        }

        if ($ptk_id > 0 && !empty($ekskul->id_ptk_pembina)) {
            $decoded = json_decode($ekskul->id_ptk_pembina, true);
            if (is_array($decoded) && in_array($ptk_id, $decoded, true)) {
                return true;
            } elseif ((int)$ekskul->id_ptk_pembina === $ptk_id) {
                return true;
            }
        }
        return false;
    }

    public function daftar_siswa($id)
    {
        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if (!$ekskul) {
            show_404();
        }

        if (!$this->checkPembinaAccess($ekskul)) {
            show_error('Anda tidak memiliki akses ke data anggota ekstrakurikuler ini.', 403, 'Akses Ditolak');
        }

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Anggota Ekskul: ' . $ekskul->nama_ekskul;
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler/daftar_siswa/' . $id;
        $this->page_data['page']->icon = 'solar:dialog-linear';

        $this->page_data['ekskul'] = $ekskul;

        // Ambil semua siswa aktif
        $this->db->order_by('nama_siswa', 'ASC');
        $this->page_data['siswa'] = $this->db->get_where('siswa', ['status_keaktifan' => 'Aktif'])->result();

        // Ambil ID siswa yang sudah terpilih di ekskul ini
        $siswa_terpilih = [];
        foreach ($this->db->get_where('ekstrakurikuler_siswa', ['id_ekskul' => $id])->result() as $es) {
            $siswa_terpilih[] = (int) $es->id_siswa;
        }
        $this->page_data['siswa_terpilih'] = $siswa_terpilih;

        $this->load->view('ekstrakurikuler/siswa', $this->page_data);
    }

    public function simpan_siswa($id)
    {
        postAllowed();
        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if (!$ekskul) {
            show_404();
        }

        if (!$this->checkPembinaAccess($ekskul)) {
            show_error('Anda tidak memiliki akses untuk mengubah anggota ekstrakurikuler ini.', 403, 'Akses Ditolak');
        }

        $this->db->delete('ekstrakurikuler_siswa', ['id_ekskul' => $id]);
        $siswa_ids = $this->input->post('siswa');

        if (is_array($siswa_ids)) {
            foreach ($siswa_ids as $sid) {
                // Ambil nilai lama jika sudah ada
                $old_val = $this->db->get_where('ekstrakurikuler_siswa', ['id_ekskul' => $id, 'id_siswa' => $sid])->row();
                $this->db->insert('ekstrakurikuler_siswa', [
                    'id_ekskul' => $id,
                    'id_siswa' => (int) $sid,
                    'nilai' => $old_val ? $old_val->nilai : 'B',
                    'catatan' => $old_val ? $old_val->catatan : 'Aktif mengikuti kegiatan',
                ]);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Anggota ekstrakurikuler berhasil diperbarui.');
        redirect('ekstrakurikuler');
    }

    public function detail($id)
    {
        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id])->row();
        if (!$ekskul) {
            show_404();
        }

        if (!$this->checkPembinaAccess($ekskul)) {
            show_error('Anda tidak memiliki akses ke halaman nilai ekstrakurikuler ini.', 403, 'Akses Ditolak');
        }

        $this->page_data['page']->title = 'Ekstrakurikuler';
        $this->page_data['page']->titleUrl = 'ekstrakurikuler';
        $this->page_data['page']->subtitle = 'Input Nilas Ekskul: ' . $ekskul->nama_ekskul;
        $this->page_data['page']->subtitleUrl = 'ekstrakurikuler/detail/' . $id;
        $this->page_data['page']->icon = 'solar:dialog-linear';

        $this->page_data['ekskul'] = $ekskul;

        // Ambil daftar siswa yang ikut ekskul ini (dukung siswa aktif maupun alumni yang memiliki riwayat arsip di ekskul ini)
        $this->db->select('es.*, s.nama_siswa, s.nisn, s.rombel, s.status_keaktifan');
        $this->db->from('ekstrakurikuler_siswa es');
        $this->db->join('siswa s', 's.id_siswa = es.id_siswa');
        $this->db->where('es.id_ekskul', $id);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $this->page_data['peserta'] = $this->db->get()->result();

        $this->load->view('ekstrakurikuler/detail', $this->page_data);
    }

    public function update_nilai($id_ekskul)
    {
        postAllowed();
        $ekskul = $this->db->get_where('ekstrakurikuler', ['id_ekskul' => $id_ekskul])->row();
        if (!$ekskul) {
            show_404();
        }

        if (!$this->checkPembinaAccess($ekskul)) {
            show_error('Anda tidak memiliki akses untuk mengubah nilai ekstrakurikuler ini.', 403, 'Akses Ditolak');
        }

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
                'id_tahun_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nama_ekskul' => ['type' => 'VARCHAR', 'constraint' => 100],
                'id_ptk_pembina' => ['type' => 'TEXT', 'null' => true],
                'logo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keterangan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_ekskul', true);
            $this->dbforge->create_table('ekstrakurikuler', true);
        } else {
            // Cek & tambahkan kolom id_tahun_pelajaran jika belum ada
            if (!$this->db->field_exists('id_tahun_pelajaran', 'ekstrakurikuler')) {
                $this->dbforge->add_column('ekstrakurikuler', [
                    'id_tahun_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_ekskul']
                ]);
            }
            // Cek & tambahkan logo jika belum ada
            if (!$this->db->field_exists('logo', 'ekstrakurikuler')) {
                $this->dbforge->add_column('ekstrakurikuler', [
                    'logo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'id_ptk_pembina']
                ]);
            }
            
            // Ubah tipe kolom id_ptk_pembina menjadi TEXT jika sebelumnya bertipe INT
            $field_data = $this->db->field_data('ekstrakurikuler');
            foreach ($field_data as $field) {
                if ($field->name === 'id_ptk_pembina' && strpos(strtolower($field->type), 'int') !== false) {
                    $this->db->query("ALTER TABLE ekstrakurikuler MODIFY id_ptk_pembina TEXT NULL");
                }
            }
        }

        if (!$this->db->table_exists('ekstrakurikuler_siswa')) {
            $this->dbforge->add_field([
                'id_ekskul_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_ekskul' => ['type' => 'INT', 'constraint' => 11],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'nilai' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // Sangat Baik, Baik, Cukup, Kurang
                'catatan' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->dbforge->add_key('id_ekskul_siswa', true);
            $this->dbforge->create_table('ekstrakurikuler_siswa', true);
        }
    }
}
