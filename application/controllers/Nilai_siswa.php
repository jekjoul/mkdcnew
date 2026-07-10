<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nilai_siswa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
        $this->ensureDefaultSetting();
    }

    public function index()
    {
        ifPermissions('nilai_siswa_list');
        $this->loadNilaiList('Aktif');
    }

    public function nonaktif()
    {
        $this->loadNilaiList('Nonaktif');
    }

    private function loadNilaiList($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Nilai Siswa';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'nilai_siswa/nonaktif' : 'nilai_siswa';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Data Nilai Tidak Aktif' : 'Daftar Nilai';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'nilai_siswa/nonaktif' : 'nilai_siswa';
        $this->page_data['page']->icon = 'solar:clipboard-list-linear';

        $this->page_data['default_setting'] = $this->getSetting(0);
        $this->page_data['items'] = $this->getPembelajaranMapel($status_tahun);
        $this->page_data['is_nonaktif'] = $is_nonaktif;

        $this->load->view('nilai_siswa/list', $this->page_data);
    }

    public function input($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapelDetail($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $this->page_data['page']->title = 'Nilai Siswa';
        $this->page_data['page']->titleUrl = 'nilai_siswa';
        $this->page_data['page']->subtitle = 'Input Nilai';
        $this->page_data['page']->subtitleUrl = 'nilai_siswa/input/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:clipboard-list-linear';

        $this->page_data['item'] = $item;
        $this->page_data['setting'] = $this->getSetting((int) $id_pembelajaran_mapel);
        $this->page_data['siswa'] = $this->getSiswaPembelajaran($item->id_pembelajaran);
        $this->page_data['nilai'] = $this->getNilaiMapel((int) $id_pembelajaran_mapel);

        $this->load->view('nilai_siswa/input', $this->page_data);
    }

    public function simpan_nilai($id_pembelajaran_mapel)
    {
        postAllowed();

        $item = $this->getPembelajaranMapelDetail($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        // 1. Simpan Label Kolom jika ada post labels_tugas & labels_uh
        $labels_tugas = $this->input->post('labels_tugas');
        $labels_uh = $this->input->post('labels_uh');
        $labels_data = [
            'labels_tugas' => is_array($labels_tugas) ? json_encode(array_values($labels_tugas)) : null,
            'labels_uh' => is_array($labels_uh) ? json_encode(array_values($labels_uh)) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $existing_setting = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if ($existing_setting) {
            $this->db->where('id_pengaturan_nilai', $existing_setting->id_pengaturan_nilai);
            $this->db->update('nilai_siswa_pengaturan', $labels_data);
        } else {
            // Salin bobot persen dari default jika setting khusus mapel belum ada
            $default_setting = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row();
            $labels_data['id_pembelajaran_mapel'] = (int) $id_pembelajaran_mapel;
            $labels_data['persen_harian'] = $default_setting ? $default_setting->persen_harian : 40;
            $labels_data['persen_psts'] = $default_setting ? $default_setting->persen_psts : 30;
            $labels_data['persen_psas'] = $default_setting ? $default_setting->persen_psas : 30;
            $labels_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('nilai_siswa_pengaturan', $labels_data);
        }

        // Tarik ulang setting terbaru untuk menghitung rapor
        $setting = $this->getSetting((int) $id_pembelajaran_mapel);

        // 2. Simpan Nilai Harian Siswa
        $rows = $this->input->post('nilai');
        if (is_array($rows)) {
            foreach ($rows as $id_siswa => $row) {
                $id_siswa = (int) $id_siswa;
                if ($id_siswa <= 0 || !$this->isSiswaInPembelajaran($item->id_pembelajaran, $id_siswa)) {
                    continue;
                }

                $nilai_harian = $this->normalizeNilai(isset($row['harian']) ? $row['harian'] : null);
                $nilai_psts = $this->normalizeNilai(isset($row['psts']) ? $row['psts'] : null);
                $nilai_psas = $this->normalizeNilai(isset($row['psas']) ? $row['psas'] : null);
                
                // Normalisasi array dinamis extra
                $extra_tugas = isset($row['extra_tugas']) && is_array($row['extra_tugas']) ? array_map([$this, 'normalizeNilai'], $row['extra_tugas']) : null;
                $extra_uh = isset($row['extra_uh']) && is_array($row['extra_uh']) ? array_map([$this, 'normalizeNilai'], $row['extra_uh']) : null;

                $nilai_rapor = $this->hitungRapor($nilai_harian, $nilai_psts, $nilai_psas, $setting);

                $data = [
                    'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
                    'id_siswa' => $id_siswa,
                    'nilai_harian' => $nilai_harian,
                    'nilai_psts' => $nilai_psts,
                    'nilai_psas' => $nilai_psas,
                    'nilai_rapor' => $nilai_rapor,
                    'extra_tugas' => $extra_tugas ? json_encode(array_values($extra_tugas)) : null,
                    'extra_uh' => $extra_uh ? json_encode(array_values($extra_uh)) : null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $existing = $this->db->get_where('nilai_siswa', [
                    'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
                    'id_siswa' => $id_siswa,
                ])->row();

                if ($existing) {
                    $this->db->where('id_nilai_siswa', $existing->id_nilai_siswa);
                    $this->db->update('nilai_siswa', $data);
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('nilai_siswa', $data);
                }
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Nilai siswa berhasil disimpan');
        redirect('nilai_siswa/input/' . $id_pembelajaran_mapel);
    }

    public function setting($id_pembelajaran_mapel = 0)
    {
        $id_pembelajaran_mapel = (int) $id_pembelajaran_mapel;
        $item = null;
        if ($id_pembelajaran_mapel > 0) {
            $item = $this->getPembelajaranMapelDetail($id_pembelajaran_mapel);
            if (!$item) {
                show_404();
            }
        }

        $this->page_data['page']->title = 'Nilai Siswa';
        $this->page_data['page']->titleUrl = 'nilai_siswa';
        $this->page_data['page']->subtitle = $id_pembelajaran_mapel > 0 ? 'Setting Persentase Mapel' : 'Setting Persentase Default';
        $this->page_data['page']->subtitleUrl = 'nilai_siswa/setting/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:settings-linear';

        $this->page_data['item'] = $item;
        $this->page_data['setting'] = $this->getSetting($id_pembelajaran_mapel, false);
        $this->page_data['default_setting'] = $this->getSetting(0);
        $this->page_data['id_pembelajaran_mapel'] = $id_pembelajaran_mapel;

        $this->load->view('nilai_siswa/setting', $this->page_data);
    }

    public function simpan_setting($id_pembelajaran_mapel = 0)
    {
        postAllowed();

        $id_pembelajaran_mapel = (int) $id_pembelajaran_mapel;
        if ($id_pembelajaran_mapel > 0 && !$this->getPembelajaranMapelDetail($id_pembelajaran_mapel)) {
            show_404();
        }

        $harian = $this->normalizePersen(post('persen_harian'));
        $psts = $this->normalizePersen(post('persen_psts'));
        $psas = $this->normalizePersen(post('persen_psas'));
        $total = round($harian + $psts + $psas, 2);

        if ($total != 100.0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Total persentase harus 100%. Total saat ini: ' . $total . '%.');
            redirect('nilai_siswa/setting/' . $id_pembelajaran_mapel);
            return;
        }

        $data = [
            'id_pembelajaran_mapel' => $id_pembelajaran_mapel,
            'persen_harian' => $harian,
            'persen_psts' => $psts,
            'persen_psas' => $psas,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => $id_pembelajaran_mapel])->row();
        if ($existing) {
            $this->db->where('id_pengaturan_nilai', $existing->id_pengaturan_nilai);
            $this->db->update('nilai_siswa_pengaturan', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('nilai_siswa_pengaturan', $data);
        }

        $this->refreshRaporBySetting($id_pembelajaran_mapel);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Setting persentase nilai berhasil disimpan');
        redirect($id_pembelajaran_mapel > 0 ? 'nilai_siswa/input/' . $id_pembelajaran_mapel : 'nilai_siswa');
    }

    private function getPembelajaranMapel($status_tahun = 'Aktif')
    {
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_pembelajaran, pm.id_mapel, pm.id_ptk, p.id_tahun_pelajaran, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk, COUNT(DISTINCT ps.peserta_didik_id) AS jumlah_siswa, COUNT(DISTINCT ns.id_nilai_siswa) AS jumlah_dinilai, np.id_pengaturan_nilai');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran', 'left');
        $this->db->join('nilai_siswa ns', 'ns.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->join('nilai_siswa_pengaturan np', 'np.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
        } else {
            $this->db->where('tp.status !=', 'Aktif');
        }
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    private function getPembelajaranMapelDetail($id_pembelajaran_mapel)
    {
        $this->db->select('pm.*, p.id_tahun_pelajaran, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('pm.id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        return $this->db->get()->row();
    }

    private function getSiswaPembelajaran($id_pembelajaran)
    {
        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->where('ps.id_pembelajaran', (int) $id_pembelajaran);
        $this->db->order_by('s.nama_siswa', 'ASC');
        return $this->db->get()->result();
    }

    private function getNilaiMapel($id_pembelajaran_mapel)
    {
        $rows = $this->db->get_where('nilai_siswa', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->result();
        $nilai = [];
        foreach ($rows as $row) {
            $nilai[(int) $row->id_siswa] = $row;
        }

        return $nilai;
    }

    private function getSetting($id_pembelajaran_mapel, $fallback = true)
    {
        $row = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if ($row || !$fallback || (int) $id_pembelajaran_mapel === 0) {
            return $row;
        }

        return $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row();
    }

    private function ensureDefaultSetting()
    {
        if (!$this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row()) {
            $this->db->insert('nilai_siswa_pengaturan', [
                'id_pembelajaran_mapel' => 0,
                'persen_harian' => 40,
                'persen_psts' => 30,
                'persen_psas' => 30,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function normalizeNilai($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (float) str_replace(',', '.', (string) $value);
        if ($value < 0) {
            return 0;
        }
        if ($value > 100) {
            return 100;
        }

        return $value;
    }

    private function normalizePersen($value)
    {
        $value = (float) str_replace(',', '.', (string) $value);
        return $value < 0 ? 0 : $value;
    }

    private function hitungRapor($nilai_harian, $nilai_psts, $nilai_psas, $setting)
    {
        if (!$setting) {
            return null;
        }

        $components = [
            ['nilai' => $nilai_harian, 'persen' => (float) $setting->persen_harian],
            ['nilai' => $nilai_psts, 'persen' => (float) $setting->persen_psts],
            ['nilai' => $nilai_psas, 'persen' => (float) $setting->persen_psas],
        ];

        $total = 0;
        $has_value = false;
        foreach ($components as $component) {
            if ($component['nilai'] !== null) {
                $has_value = true;
                $total += ((float) $component['nilai'] * $component['persen']) / 100;
            }
        }

        return $has_value ? round($total, 2) : null;
    }

    private function isSiswaInPembelajaran($id_pembelajaran, $id_siswa)
    {
        return $this->db->get_where('pembelajaran_siswa', [
            'id_pembelajaran' => (int) $id_pembelajaran,
            'peserta_didik_id' => (string) $id_siswa,
        ])->row() ? true : false;
    }

    private function refreshRaporBySetting($id_pembelajaran_mapel)
    {
        if ($id_pembelajaran_mapel > 0) {
            $ids = [$id_pembelajaran_mapel];
        } else {
            $rows = $this->db->get('pembelajaran_mapel')->result();
            $ids = array_map(function ($row) {
                return (int) $row->id_pembelajaran_mapel;
            }, $rows);
        }

        foreach ($ids as $id) {
            $setting = $this->getSetting($id);
            $rows = $this->db->get_where('nilai_siswa', ['id_pembelajaran_mapel' => $id])->result();
            foreach ($rows as $row) {
                $this->db->where('id_nilai_siswa', $row->id_nilai_siswa);
                $this->db->update('nilai_siswa', [
                    'nilai_rapor' => $this->hitungRapor($row->nilai_harian, $row->nilai_psts, $row->nilai_psas, $setting),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('nilai_siswa_pengaturan')) {
            $this->dbforge->add_field([
                'id_pengaturan_nilai' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'persen_harian' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 40],
                'persen_psts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 30],
                'persen_psas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 30],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_pengaturan_nilai', true);
            $this->dbforge->add_key('id_pembelajaran_mapel');
            $this->dbforge->create_table('nilai_siswa_pengaturan', true);
        }

        // Add dynamically defined columns for assignments/exams structure to nilai_siswa_pengaturan
        if (!$this->db->field_exists('labels_tugas', 'nilai_siswa_pengaturan')) {
            $this->dbforge->add_column('nilai_siswa_pengaturan', [
                'labels_tugas' => ['type' => 'TEXT', 'null' => true],
                'labels_uh' => ['type' => 'TEXT', 'null' => true],
            ]);
        }

        if (!$this->db->table_exists('nilai_siswa')) {
            $this->dbforge->add_field([
                'id_nilai_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'nilai_harian' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_psts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_psas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_rapor' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_nilai_siswa', true);
            $this->dbforge->add_key('id_pembelajaran_mapel');
            $this->dbforge->add_key('id_siswa');
            $this->dbforge->create_table('nilai_siswa', true);
        }

        // Add dynamic value storage columns for assignment & exam scores to nilai_siswa
        if (!$this->db->field_exists('extra_tugas', 'nilai_siswa')) {
            $this->dbforge->add_column('nilai_siswa', [
                'extra_tugas' => ['type' => 'TEXT', 'null' => true],
                'extra_uh' => ['type' => 'TEXT', 'null' => true],
            ]);
        }
    }
}
