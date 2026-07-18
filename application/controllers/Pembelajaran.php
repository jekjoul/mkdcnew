<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model', 'master_model');
        $this->ensurePembelajaranColumns();
        $this->ensurePembelajaranMapelColumns();
    }

    public function index()
    {
        ifPermissions('pembelajaran_list');
        $this->loadPembelajaranList('Aktif');
    }

    public function nonaktif()
    {
        $this->loadPembelajaranList('Nonaktif');
    }

    private function loadPembelajaranList($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'pembelajaran/nonaktif' : 'pembelajaran';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Data Pembelajaran Tidak Aktif' : 'Daftar Pembelajaran';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'pembelajaran/nonaktif' : 'pembelajaran';
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->db->select('p.*, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, wali.nama_ptk AS nama_wali_kelas, (SELECT COUNT(*) FROM pembelajaran_siswa ps JOIN siswa s ON s.id_siswa = ps.peserta_didik_id WHERE ps.id_pembelajaran = p.id_pembelajaran) AS jumlah_siswa');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->join('ptk wali', 'wali.id_ptk = p.id_ptk_wali', 'left');
        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
            $this->db->where('p.status', 'Aktif');
        } else {
            $this->db->group_start();
            $this->db->where('tp.status !=', 'Aktif');
            $this->db->or_where('p.status !=', 'Aktif');
            $this->db->group_end();
        }
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->page_data['pembelajaran'] = $this->db->get()->result();
        $this->page_data['is_nonaktif'] = $is_nonaktif;

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

        $this->setFormOptions();
        $this->page_data['ta_aktif'] = $ta_aktif;
        $this->page_data['row'] = null;
        $this->page_data['form_action'] = url('pembelajaran/simpan');
        $this->page_data['submit_label'] = 'Simpan Pembelajaran';

        $this->load->view('pembelajaran/form', $this->page_data);
    }

    public function edit($id)
    {
        ifPermissions('pembelajaran_edit');
        if (!$this->checkPembelajaranAktif($id)) return;
        $pembelajaran = $this->getPembelajaranDetail($id);
        if (!$pembelajaran) {
            show_404();
        }

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Edit Pembelajaran';
        $this->page_data['page']->subtitleUrl = 'pembelajaran/edit/' . $id;
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->setFormOptions();
        $this->page_data['ta_aktif'] = (object) [
            'id_tahun_pelajaran' => $pembelajaran->id_tahun_pelajaran,
            'tahun_pelajaran' => $pembelajaran->tahun_pelajaran,
            'semester' => $pembelajaran->semester,
        ];
        $this->page_data['row'] = $pembelajaran;
        $this->page_data['form_action'] = url('pembelajaran/simpan');
        $this->page_data['submit_label'] = 'Update Pembelajaran';

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
            'id_ptk_wali'        => post('id_ptk_wali') !== '' ? post('id_ptk_wali') : null,
        ];

        $id_pembelajaran = (int) post('id_pembelajaran');
        if ($id_pembelajaran > 0) {
            if (!$this->checkPembelajaranAktif($id_pembelajaran)) return;
        }
        $this->db->where('id_tahun_pelajaran', $pembelajaran_data['id_tahun_pelajaran']);
        $this->db->where('id_lembaga', $pembelajaran_data['id_lembaga']);
        $this->db->where('id_tingkat_sekolah', $pembelajaran_data['id_tingkat_sekolah']);
        $this->db->where('id_rombel', $pembelajaran_data['id_rombel']);
        if ($id_pembelajaran > 0) {
            $this->db->where('id_pembelajaran !=', $id_pembelajaran);
        }
        $duplikat = $this->db->get('pembelajaran')->row();
        if ($duplikat) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal! Pembelajaran untuk tahun, lembaga, tingkat, dan rombel tersebut sudah ada.');
            redirect($id_pembelajaran > 0 ? 'pembelajaran/edit/' . $id_pembelajaran : 'pembelajaran/tambah');
            return;
        }

        if ($id_pembelajaran > 0) {
            $this->db->where('id_pembelajaran', $id_pembelajaran);
            $this->db->update('pembelajaran', $pembelajaran_data);
            $this->updateSiswaRombel($id_pembelajaran);
            $message = 'Pembelajaran berhasil diperbarui.';
        } else {
            $this->db->insert('pembelajaran', $pembelajaran_data);
            $message = 'Pembelajaran berhasil disimpan. Silakan tambah mapel dan daftar siswa dari list.';
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', $message);
        redirect('pembelajaran');
    }

    public function tambah_mapel($id)
    {
        if (!$this->checkPembelajaranAktif($id)) return;
        $pembelajaran = $this->getPembelajaranDetail($id);
        if (!$pembelajaran) {
            show_404();
        }

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Tambah Mapel';
        $this->page_data['page']->subtitleUrl = 'pembelajaran/tambah_mapel/' . $id;
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['mapel'] = $this->master_model->getMapel();
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk'] = $this->db->get('ptk')->result();
        $this->page_data['mapel_terpilih'] = $this->getSelectedMapel($id);

        $this->load->view('pembelajaran/mapel', $this->page_data);
    }

    public function simpan_mapel($id)
    {
        postAllowed();

        if (!$this->checkPembelajaranAktif($id)) return;
        if (!$this->db->get_where('pembelajaran', ['id_pembelajaran' => $id])->row()) {
            show_404();
        }

        $this->db->where('id_pembelajaran', $id);
        $this->db->delete('pembelajaran_mapel');

        $mapels = $this->input->post('mapel');
        $jumlah_jam = $this->input->post('jumlah_jam');
        $ptk = $this->input->post('id_ptk');
        if (!empty($mapels)) {
            foreach ($mapels as $id_mapel) {
                $this->db->insert('pembelajaran_mapel', [
                    'id_pembelajaran' => $id,
                    'id_mapel' => $id_mapel,
                    'jumlah_jam' => isset($jumlah_jam[$id_mapel]) && $jumlah_jam[$id_mapel] !== '' ? $jumlah_jam[$id_mapel] : null,
                    'id_ptk' => isset($ptk[$id_mapel]) && $ptk[$id_mapel] !== '' ? $ptk[$id_mapel] : null,
                ]);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Mapel pembelajaran berhasil disimpan');
        redirect('pembelajaran');
    }

    public function daftar_siswa($id)
    {
        if (!$this->checkPembelajaranAktif($id)) return;
        $pembelajaran = $this->getPembelajaranDetail($id);
        if (!$pembelajaran) {
            show_404();
        }

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Daftar Siswa';
        $this->page_data['page']->subtitleUrl = 'pembelajaran/daftar_siswa/' . $id;
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->db->order_by('nama_siswa', 'ASC');
        $siswa = $this->db->get('siswa')->result();

        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['siswa'] = $siswa;
        $this->page_data['siswa_terpilih'] = $this->getSelectedValues('pembelajaran_siswa', 'peserta_didik_id', $id);

        $id_tahun_pelajaran = (int) $pembelajaran->id_tahun_pelajaran;
        $this->db->select('ps.peserta_didik_id');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran');
        $this->db->where('p.id_tahun_pelajaran', $id_tahun_pelajaran);
        $this->db->where('p.status', 'Aktif');
        $this->db->where('p.id_pembelajaran !=', $id);
        $query = $this->db->get();
        $this->page_data['enrolled_siswa_ids'] = array_map(function($row) {
            return $row->peserta_didik_id;
        }, $query->result());

        $this->load->view('pembelajaran/siswa', $this->page_data);
    }

    public function simpan_siswa($id)
    {
        postAllowed();

        if (!$this->checkPembelajaranAktif($id)) return;
        $pembelajaran = $this->getPembelajaranDetail($id);
        if (!$pembelajaran) {
            show_404();
        }

        $siswa_terpilih_lama = $this->getSelectedValues('pembelajaran_siswa', 'peserta_didik_id', $id);

        $this->db->where('id_pembelajaran', $id);
        $this->db->delete('pembelajaran_siswa');

        $siswas = $this->input->post('siswa');
        $rombel_label = $this->formatRombelPembelajaran($pembelajaran);
        if (!empty($siswa_terpilih_lama)) {
            $this->db->where_in('id_siswa', $siswa_terpilih_lama);
            $this->db->where_in('rombel', [$rombel_label, $pembelajaran->nama_rombel]);
            $this->db->update('siswa', ['rombel' => null]);
        }

        if (!empty($siswas)) {
            foreach ($siswas as $id_siswa) {
                $this->db->insert('pembelajaran_siswa', [
                    'id_pembelajaran' => $id,
                    'peserta_didik_id' => $id_siswa,
                ]);
            }

            $this->db->where_in('id_siswa', $siswas);
            $this->db->update('siswa', ['rombel' => $rombel_label]);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Daftar siswa pembelajaran berhasil disimpan');
        redirect('pembelajaran');
    }

    private function getPembelajaranDetail($id)
    {
        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, wali.nama_ptk AS nama_wali_kelas');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->join('ptk wali', 'wali.id_ptk = p.id_ptk_wali', 'left');
        $this->db->where('p.id_pembelajaran', $id);
        return $this->db->get()->row();
    }

    private function setFormOptions()
    {
        $this->page_data['lembaga'] = $this->master_model->getAllLembaga();
        $this->page_data['tingkat'] = $this->master_model->getTingkatSekolah();
        $this->db->select('r.*');
        $this->db->from('rombel r');
        $this->db->join('master_tingkat_sekolah t', 'r.id_tingkat_sekolah = t.id_tingkat_sekolah', 'left');
        $this->db->where('r.status', 'Aktif');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->page_data['rombel']  = ($q = $this->db->get()) ? $q->result() : [];
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk'] = $this->db->get('ptk')->result();
    }

    private function updateSiswaRombel($id_pembelajaran)
    {
        $pembelajaran = $this->getPembelajaranDetail($id_pembelajaran);
        if (!$pembelajaran) {
            return;
        }

        $siswa = $this->getSelectedValues('pembelajaran_siswa', 'peserta_didik_id', $id_pembelajaran);
        if (empty($siswa)) {
            return;
        }

        $this->db->where_in('id_siswa', $siswa);
        $this->db->update('siswa', ['rombel' => $this->formatRombelPembelajaran($pembelajaran)]);
    }

    private function getSelectedValues($table, $column, $id_pembelajaran)
    {
        $rows = $this->db->get_where($table, ['id_pembelajaran' => $id_pembelajaran])->result();
        return array_map(function ($row) use ($column) {
            return $row->$column;
        }, $rows);
    }

    private function getSelectedMapel($id_pembelajaran)
    {
        $rows = $this->db->get_where('pembelajaran_mapel', ['id_pembelajaran' => $id_pembelajaran])->result();
        $selected = [];
        foreach ($rows as $row) {
            $selected[$row->id_mapel] = $row;
        }

        return $selected;
    }

    private function formatRombelPembelajaran($pembelajaran)
    {
        $tingkat = trim((string) $pembelajaran->nama_tingkat);
        $rombel = trim((string) $pembelajaran->nama_rombel);

        return $tingkat !== '' ? $tingkat . ' - ' . $rombel : $rombel;
    }

    private function ensurePembelajaranMapelColumns()
    {
        $this->load->dbforge();

        if (!$this->db->field_exists('jumlah_jam', 'pembelajaran_mapel')) {
            $this->dbforge->add_column('pembelajaran_mapel', [
                'jumlah_jam' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_mapel'],
            ]);
        }

        if (!$this->db->field_exists('id_ptk', 'pembelajaran_mapel')) {
            $this->dbforge->add_column('pembelajaran_mapel', [
                'id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'jumlah_jam'],
            ]);
        }
    }

    private function ensurePembelajaranColumns()
    {
        $this->load->dbforge();

        if (!$this->db->field_exists('id_ptk_wali', 'pembelajaran')) {
            $this->dbforge->add_column('pembelajaran', [
                'id_ptk_wali' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_rombel'],
            ]);
        }
    }

    public function luluskan($id_pembelajaran)
    {
        if (!hasPermissions('menu_pembelajaran')) {
            show_404();
        }

        $pembelajaran = $this->db->get_where('pembelajaran', ['id_pembelajaran' => $id_pembelajaran])->row();
        if (!$pembelajaran) {
            $this->session->set_flashdata('error', 'Data pembelajaran tidak ditemukan.');
            redirect('pembelajaran');
        }

        $this->load->model('Alumni_model');
        $siswa_list = $this->db->get_where('pembelajaran_siswa', ['id_pembelajaran' => $id_pembelajaran])->result();
        
        $count = 0;
        $tanggal_alumni = date('Y-m-d');
        foreach ($siswa_list as $s) {
            $id_siswa = (int) $s->peserta_didik_id;
            if ($this->Alumni_model->moveSiswaToAlumni($id_siswa, 'Lulus', $tanggal_alumni)) {
                $count++;
            }
        }

        // Deactivate Rombel
        $this->db->update('rombel', ['status' => 'Tidak Aktif'], ['id_rombel' => $pembelajaran->id_rombel]);
        
        // Deactivate Pembelajaran
        $this->db->update('pembelajaran', ['status' => 'Tidak Aktif'], ['id_pembelajaran' => $id_pembelajaran]);

        $this->session->set_flashdata('message', "$count siswa berhasil diluluskan ke Data Alumni. Rombel dan Pembelajaran telah dinonaktifkan.");
        redirect('pembelajaran');
    }

    private function checkPembelajaranAktif($id)
    {
        $p = $this->db->get_where('pembelajaran', ['id_pembelajaran' => $id])->row();
        if ($p && $p->status !== 'Aktif') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Aksi dibatalkan. Pembelajaran ini sudah lulus/tidak aktif.');
            redirect('pembelajaran');
            return false;
        }
        return true;
    }
}
