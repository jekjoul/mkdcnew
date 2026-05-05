<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model', 'master_model');
        $this->ensurePembelajaranMapelColumns();
    }

    public function index()
    {
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'pembelajaran';
        $this->page_data['page']->subtitle = 'Daftar Pembelajaran';
        $this->page_data['page']->subtitleUrl = 'pembelajaran';
        $this->page_data['page']->icon = 'solar:notebook-bookmark-linear';

        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->page_data['pembelajaran'] = $this->db->get()->result();

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

        $this->page_data['lembaga'] = $this->master_model->getAllLembaga();
        $this->page_data['tingkat'] = $this->master_model->getTingkatSekolah();
        $this->page_data['rombel']  = ($q = $this->db->get('rombel')) ? $q->result() : [];
        $this->page_data['ta_aktif'] = $ta_aktif;

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
        ];

        $this->db->insert('pembelajaran', $pembelajaran_data);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Pembelajaran berhasil disimpan. Silakan tambah mapel dan daftar siswa dari list.');
        redirect('pembelajaran');
    }

    public function tambah_mapel($id)
    {
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

        $this->load->view('pembelajaran/siswa', $this->page_data);
    }

    public function simpan_siswa($id)
    {
        postAllowed();

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
        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('p.id_pembelajaran', $id);
        return $this->db->get()->row();
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
}
