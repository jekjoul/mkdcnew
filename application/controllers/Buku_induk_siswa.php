<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Buku_induk_siswa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureSiswaBukuIndukColumns();
    }

    public function index()
    {
        $this->page_data['page']->title = 'Buku Induk Siswa';
        $this->page_data['page']->titleUrl = 'buku_induk_siswa';
        $this->page_data['page']->subtitle = 'Daftar Buku Induk';
        $this->page_data['page']->subtitleUrl = 'buku_induk_siswa';
        $this->page_data['page']->icon = 'solar:book-bookmark-linear';

        $this->db->order_by('nama_siswa', 'ASC');
        $this->page_data['siswa'] = $this->db->get('siswa')->result();

        $this->load->view('buku_induk_siswa/list', $this->page_data);
    }

    public function view($id_siswa = null)
    {
        $data = $this->buildBukuIndukData($id_siswa);

        $this->page_data['page']->title = 'Buku Induk Siswa';
        $this->page_data['page']->titleUrl = 'buku_induk_siswa';
        $this->page_data['page']->subtitle = $data['siswa']->nama_siswa;
        $this->page_data['page']->subtitleUrl = 'buku_induk_siswa/view/' . $id_siswa;
        $this->page_data['page']->icon = 'solar:book-bookmark-linear';
        $this->page_data = array_merge($this->page_data, $data);

        $this->load->view('buku_induk_siswa/view', $this->page_data);
    }

    public function cetak($id_siswa = null)
    {
        $data = $this->buildBukuIndukData($id_siswa);
        $data['auto_print'] = $this->input->get('auto') === '1';
        $this->load->view('buku_induk_siswa/print', $data);
    }

    public function export_pdf($id_siswa = null)
    {
        redirect('buku_induk_siswa/cetak/' . $id_siswa . '?auto=1');
    }

    private function buildBukuIndukData($id_siswa)
    {
        if (!$id_siswa) {
            redirect('buku_induk_siswa');
        }

        $siswa = $this->db->get_where('siswa', ['id_siswa' => $id_siswa])->row();
        if (!$siswa) {
            show_404();
        }

        return [
            'siswa' => $siswa,
            'foto' => $this->getFotoSiswa($id_siswa),
            'pembelajaran_terakhir' => $this->getPembelajaranTerakhir($id_siswa),
            'semester_columns' => $this->getSemesterColumns($id_siswa),
            'nilai_rows' => $this->getNilaiRows($id_siswa),
        ];
    }

    private function getFotoSiswa($id_siswa)
    {
        $this->db->order_by('id_foto', 'DESC');
        return $this->db->get_where('siswa_foto', ['id_siswa' => $id_siswa])->row();
    }

    private function getPembelajaranTerakhir($id_siswa)
    {
        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel', 'left');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran', 'left');
        $this->db->where('ps.peserta_didik_id', (string) $id_siswa);
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        return $this->db->get()->row();
    }

    private function getSemesterColumns($id_siswa)
    {
        $this->db->select('DISTINCT tp.id_tahun_pelajaran, tp.tahun_pelajaran, tp.semester', false);
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->where('ps.peserta_didik_id', (string) $id_siswa);
        $this->db->order_by('tp.id_tahun_pelajaran', 'ASC');
        $rows = $this->db->get()->result();

        $columns = [];
        foreach ($rows as $row) {
            $columns[] = [
                'id_tahun_pelajaran' => (int) $row->id_tahun_pelajaran,
                'tahun_pelajaran' => $row->tahun_pelajaran,
                'semester' => $row->semester,
            ];
        }

        while (count($columns) < 6) {
            $columns[] = [
                'id_tahun_pelajaran' => 0,
                'tahun_pelajaran' => '',
                'semester' => '',
            ];
        }

        return array_slice($columns, 0, 6);
    }

    private function getNilaiRows($id_siswa)
    {
        $this->db->select('m.id_mapel, m.nama_mapel, tp.id_tahun_pelajaran, ns.nilai_rapor');
        $this->db->from('nilai_siswa ns');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = ns.id_pembelajaran_mapel');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->where('ns.id_siswa', (int) $id_siswa);
        $this->db->order_by('m.nama_mapel', 'ASC');
        $this->db->order_by('tp.id_tahun_pelajaran', 'ASC');
        $rows = $this->db->get()->result();

        $nilai = [];
        foreach ($rows as $row) {
            if (!isset($nilai[$row->id_mapel])) {
                $nilai[$row->id_mapel] = [
                    'nama_mapel' => $row->nama_mapel,
                    'semester' => [],
                ];
            }

            $nilai[$row->id_mapel]['semester'][(int) $row->id_tahun_pelajaran] = $row->nilai_rapor;
        }

        return array_values($nilai);
    }

    private function ensureSiswaBukuIndukColumns()
    {
        $this->load->dbforge();
        $fields = [
            'no_ijazah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kewarganegaraan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Indonesia'],
            'anak_ke' => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'jenis_tempat_tinggal' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alat_transportasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jarak_ke_sekolah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'koordinat' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'sekolah_asal' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'riwayat_penyakit' => ['type' => 'TEXT', 'null' => true],
            'prestasi_siswa' => ['type' => 'TEXT', 'null' => true],
        ];

        foreach ($fields as $field => $definition) {
            if (!$this->db->field_exists($field, 'siswa')) {
                $this->dbforge->add_column('siswa', [$field => $definition]);
            }
        }
    }
}
