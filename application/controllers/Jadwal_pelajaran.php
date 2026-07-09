<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal_pelajaran extends MY_Controller
{
    private $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    public function index()
    {
        ifPermissions('jadwal_pelajaran_list');
        $this->loadJadwalIndex('Aktif');
    }

    public function nonaktif()
    {
        $this->loadJadwalIndex('Nonaktif');
    }

    private function loadJadwalIndex($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'jadwal_pelajaran/nonaktif' : 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Jadwal Tahun Tidak Aktif' : 'Jadwal Mingguan';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'jadwal_pelajaran/nonaktif' : 'jadwal_pelajaran';
        $this->page_data['page']->icon = 'akar-icons:schedule';

        $this->page_data['pembelajaran'] = $this->getAllPembelajaran($status_tahun);
        $this->page_data['settings'] = $this->getSettings(0);
        $this->page_data['menit_jp'] = $this->getMenitJp($this->page_data['settings']);
        $this->page_data['is_nonaktif'] = $is_nonaktif;

        $this->load->view('jadwal_pelajaran/list', $this->page_data);
    }

    public function waktu()
    {
        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = 'Atur Waktu Mingguan';
        $this->page_data['page']->subtitleUrl = 'jadwal_pelajaran/waktu';
        $this->page_data['page']->icon = 'akar-icons:schedule';

        $settings = $this->getSettings(0);
        $this->page_data['hari'] = $this->hari;
        $this->page_data['settings'] = $settings;
        $this->page_data['menit_jp'] = $this->getMenitJp($settings);

        $this->load->view('jadwal_pelajaran/waktu', $this->page_data);
    }

    public function simpan_waktu()
    {
        postAllowed();
        $this->saveSettings(0);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kerangka waktu mingguan berhasil disimpan');
        redirect('jadwal_pelajaran/semua');
    }

    public function semua()
    {
        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = 'Susun Jadwal Semua Kelas';
        $this->page_data['page']->subtitleUrl = 'jadwal_pelajaran/semua';
        $this->page_data['page']->icon = 'akar-icons:schedule';

        $settings = $this->getSettings(0);
        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $mapel_by_pembelajaran = [];
        $items = [];

        foreach ($pembelajaran as $row) {
            $mapel_by_pembelajaran[$row->id_pembelajaran] = $this->getMapelPembelajaran($row->id_pembelajaran);
            $items[$row->id_pembelajaran] = $this->getItems($row->id_pembelajaran, $settings);
        }

        $this->page_data['hari'] = $this->hari;
        $this->page_data['settings'] = $settings;
        $this->page_data['menit_jp'] = $this->getMenitJp($settings);
        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['mapel_by_pembelajaran'] = $mapel_by_pembelajaran;
        $this->page_data['items'] = $items;

        $this->load->view('jadwal_pelajaran/semua', $this->page_data);
    }

    public function simpan_semua()
    {
        postAllowed();

        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $valid_ids = [];
        $mapel_limits = [];
        foreach ($pembelajaran as $row) {
            $valid_ids[] = (int) $row->id_pembelajaran;
            foreach ($this->getMapelPembelajaran($row->id_pembelajaran) as $mapel) {
                $mapel_limits[$row->id_pembelajaran][$mapel->id_mapel] = (int) $mapel->jumlah_jam;
            }
        }

        $jadwal = $this->input->post('jadwal');
        $pakai = [];
        $insert = [];
        if (is_array($jadwal)) {
            foreach ($jadwal as $id_pembelajaran => $days) {
                $id_pembelajaran = (int) $id_pembelajaran;
                if (!in_array($id_pembelajaran, $valid_ids, true) || !is_array($days)) {
                    continue;
                }

                foreach ($days as $hari => $slots) {
                    if (!in_array($hari, $this->hari, true) || !is_array($slots)) {
                        continue;
                    }

                    foreach ($slots as $slot_ke => $id_mapel) {
                        $slot_ke = (int) $slot_ke;
                        $id_mapel = (int) $id_mapel;
                        if ($slot_ke <= 0 || $id_mapel <= 0 || empty($mapel_limits[$id_pembelajaran][$id_mapel])) {
                            continue;
                        }

                        $key = $id_pembelajaran . '-' . $id_mapel;
                        $pakai[$key] = isset($pakai[$key]) ? $pakai[$key] + 1 : 1;
                        if ($pakai[$key] > $mapel_limits[$id_pembelajaran][$id_mapel]) {
                            continue;
                        }

                        $insert[] = [
                            'id_pembelajaran' => $id_pembelajaran,
                            'hari' => $hari,
                            'slot_ke' => $slot_ke,
                            'id_mapel' => $id_mapel,
                        ];
                    }
                }
            }
        }

        if (!empty($valid_ids)) {
            $this->db->where_in('id_pembelajaran', $valid_ids);
            $this->db->delete('jadwal_pelajaran_item');
        }

        foreach ($insert as $row) {
            $this->db->insert('jadwal_pelajaran_item', $row);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Jadwal mingguan semua kelas berhasil disimpan');
        redirect('jadwal_pelajaran/semua');
    }

    public function atur($id_pembelajaran)
    {
        redirect('jadwal_pelajaran/semua');
    }

    public function simpan_pengaturan($id_pembelajaran)
    {
        redirect('jadwal_pelajaran/waktu');
    }

    private function saveSettings($id_pembelajaran)
    {
        $aktif = $this->input->post('hari_aktif');
        $mulai = $this->input->post('jam_mulai');
        $menit_jp = (int) post('menit_jp');
        if ($menit_jp <= 0) {
            $menit_jp = 40;
        }
        $jumlah_jp = $this->input->post('jumlah_jp');
        $break_name = $this->input->post('break_name');
        $break_after = $this->input->post('break_after');
        $break_duration = $this->input->post('break_duration');

        $this->db->where('id_pembelajaran', $id_pembelajaran);
        $this->db->delete('jadwal_pelajaran_pengaturan');

        foreach ($this->hari as $hari) {
            if (empty($aktif[$hari])) {
                continue;
            }

            $breaks = $this->susunIstirahat(
                isset($break_name[$hari]) ? $break_name[$hari] : [],
                isset($break_after[$hari]) ? $break_after[$hari] : [],
                isset($break_duration[$hari]) ? $break_duration[$hari] : []
            );

            $this->db->insert('jadwal_pelajaran_pengaturan', [
                'id_pembelajaran' => $id_pembelajaran,
                'hari' => $hari,
                'jam_mulai' => !empty($mulai[$hari]) ? $mulai[$hari] : '07:00',
                'menit_jp' => $menit_jp,
                'jumlah_jp' => !empty($jumlah_jp[$hari]) ? (int) $jumlah_jp[$hari] : 8,
                'istirahat_json' => json_encode($breaks),
            ]);
        }
    }

    public function simpan_jadwal($id_pembelajaran)
    {
        redirect('jadwal_pelajaran/semua');
    }

    private function getPembelajaran($id)
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

    private function getAllPembelajaran($status_tahun = 'Aktif')
    {
        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, COUNT(pm.id_mapel) AS jumlah_mapel');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran = p.id_pembelajaran', 'left');
        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
        } else {
            $this->db->where('tp.status !=', 'Aktif');
        }
        $this->db->group_by('p.id_pembelajaran');
        $this->db->having('jumlah_mapel >', 0); // Only active pembelajaran with mapped subjects
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('tp.status', 'ASC');
        return $this->db->get()->result();
    }

    private function getMapelPembelajaran($id_pembelajaran)
    {
        $this->db->select('pm.id_mapel, pm.jumlah_jam, pm.id_ptk, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('pm.id_pembelajaran', $id_pembelajaran);
        $this->db->where('pm.jumlah_jam >', 0);
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    private function getSettings($id_pembelajaran)
    {
        $rows = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => $id_pembelajaran])->result();
        $settings = [];
        foreach ($this->hari as $hari) {
            $settings[$hari] = [
                'aktif' => false,
                'jam_mulai' => $hari === 'Jumat' ? '07:00' : '07:00',
                'menit_jp' => 40,
                'jumlah_jp' => $hari === 'Jumat' ? 6 : 8,
                'istirahat' => [],
            ];
        }

        foreach ($rows as $row) {
            $settings[$row->hari] = [
                'aktif' => true,
                'jam_mulai' => substr($row->jam_mulai, 0, 5),
                'menit_jp' => (int) $row->menit_jp,
                'jumlah_jp' => (int) $row->jumlah_jp,
                'istirahat' => json_decode($row->istirahat_json ?: '[]', true) ?: [],
            ];
        }

        if (empty($rows)) {
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
                $settings[$hari]['aktif'] = true;
                $settings[$hari]['istirahat'] = [['name' => 'Istirahat', 'after' => 4, 'duration' => 20]];
            }
        }

        return $settings;
    }

    private function getMenitJp($settings)
    {
        foreach ($this->hari as $hari) {
            if (!empty($settings[$hari]['aktif'])) {
                return (int) $settings[$hari]['menit_jp'];
            }
        }

        return 40;
    }

    private function getItems($id_pembelajaran, $settings = null)
    {
        $rows = $this->db->get_where('jadwal_pelajaran_item', ['id_pembelajaran' => $id_pembelajaran])->result();
        $items = [];
        foreach ($rows as $row) {
            if ($settings !== null) {
                if (empty($settings[$row->hari]['aktif']) || (int) $row->slot_ke > (int) $settings[$row->hari]['jumlah_jp']) {
                    continue;
                }
            }
            $items[$row->hari][(int) $row->slot_ke] = (int) $row->id_mapel;
        }

        return $items;
    }

    private function hitungPemakaian($mapel, $items)
    {
        $pemakaian = [];
        foreach ($mapel as $row) {
            $pemakaian[$row->id_mapel] = 0;
        }

        foreach ($items as $slots) {
            foreach ($slots as $id_mapel) {
                if (isset($pemakaian[$id_mapel])) {
                    $pemakaian[$id_mapel]++;
                }
            }
        }

        return $pemakaian;
    }

    private function getConflictSources($exclude_id_pembelajaran)
    {
        $this->db->select('j.id_pembelajaran, j.hari, j.slot_ke, j.id_mapel, pm.id_ptk, ptk.nama_ptk, m.nama_mapel, l.nama_lembaga, t.nama_tingkat, r.nama_rombel');
        $this->db->from('jadwal_pelajaran_item j');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran = j.id_pembelajaran AND pm.id_mapel = j.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('mapel m', 'm.id_mapel = j.id_mapel', 'left');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = j.id_pembelajaran');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->where('j.id_pembelajaran !=', $exclude_id_pembelajaran);
        $this->db->where('pm.id_ptk IS NOT NULL', null, false);
        $rows = $this->db->get()->result();

        $settings_cache = [];
        $sources = [];
        foreach ($rows as $row) {
            if (!isset($settings_cache[$row->id_pembelajaran])) {
                $settings_cache[$row->id_pembelajaran] = $this->getSettings($row->id_pembelajaran);
            }

            $setting = isset($settings_cache[$row->id_pembelajaran][$row->hari]) ? $settings_cache[$row->id_pembelajaran][$row->hari] : null;
            if (!$setting || empty($setting['aktif'])) {
                continue;
            }

            $range = $this->slotRange($setting, (int) $row->slot_ke);
            if (!$range) {
                continue;
            }

            $sources[] = [
                'id_ptk' => (int) $row->id_ptk,
                'nama_ptk' => $row->nama_ptk,
                'hari' => $row->hari,
                'start' => $range['start'],
                'end' => $range['end'],
                'mapel' => $row->nama_mapel,
                'rombel' => trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel),
            ];
        }

        return $sources;
    }

    private function findConflicts($id_pembelajaran, $items)
    {
        $mapel = $this->getMapelPembelajaran($id_pembelajaran);
        $mapel_by_id = [];
        foreach ($mapel as $row) {
            $mapel_by_id[$row->id_mapel] = $row;
        }

        $settings = $this->getSettings($id_pembelajaran);
        $sources = $this->getConflictSources($id_pembelajaran);
        $conflicts = [];

        foreach ($items as $item) {
            if (empty($mapel_by_id[$item['id_mapel']]->id_ptk)) {
                continue;
            }

            $setting = isset($settings[$item['hari']]) ? $settings[$item['hari']] : null;
            $range = $setting ? $this->slotRange($setting, (int) $item['slot_ke']) : null;
            if (!$range) {
                continue;
            }

            foreach ($sources as $source) {
                if ((int) $source['id_ptk'] !== (int) $mapel_by_id[$item['id_mapel']]->id_ptk || $source['hari'] !== $item['hari']) {
                    continue;
                }

                if ($range['start'] < $source['end'] && $range['end'] > $source['start']) {
                    $conflicts[] = $source;
                }
            }
        }

        return $conflicts;
    }

    private function slotRange($setting, $slot_ke)
    {
        if ($slot_ke <= 0 || $slot_ke > (int) $setting['jumlah_jp']) {
            return null;
        }

        $breaks = [];
        foreach ($setting['istirahat'] as $break) {
            if (isset($break['after'], $break['duration'])) {
                $breaks[(int) $break['after']] = (int) $break['duration'];
            }
        }

        $minutes = $this->timeToMinutes($setting['jam_mulai']);
        if (isset($breaks[0])) {
            $minutes += (int) $breaks[0];
        }

        for ($i = 1; $i < $slot_ke; $i++) {
            $minutes += (int) $setting['menit_jp'];
            if (isset($breaks[$i])) {
                $minutes += (int) $breaks[$i];
            }
        }

        return [
            'start' => $minutes,
            'end' => $minutes + (int) $setting['menit_jp'],
        ];
    }

    private function timeToMinutes($time)
    {
        $parts = explode(':', (string) $time);
        return ((int) $parts[0] * 60) + (isset($parts[1]) ? (int) $parts[1] : 0);
    }

    private function susunIstirahat($name, $after, $duration)
    {
        $breaks = [];
        if (!is_array($after)) {
            return $breaks;
        }

        foreach ($after as $index => $slot) {
            $slot = (int) $slot;
            $durasi = isset($duration[$index]) ? (int) $duration[$index] : 0;
            $nama = isset($name[$index]) ? trim((string) $name[$index]) : '';
            if ($slot >= 0 && $durasi > 0) {
                $breaks[] = ['name' => $nama !== '' ? $nama : 'Waktu Khusus', 'after' => $slot, 'duration' => $durasi];
            }
        }

        usort($breaks, function ($a, $b) {
            return $a['after'] - $b['after'];
        });

        return $breaks;
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('jadwal_pelajaran_pengaturan')) {
            $this->dbforge->add_field([
                'id_pengaturan' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran' => ['type' => 'INT', 'constraint' => 11],
                'hari' => ['type' => 'VARCHAR', 'constraint' => 20],
                'jam_mulai' => ['type' => 'TIME'],
                'menit_jp' => ['type' => 'INT', 'constraint' => 11, 'default' => 40],
                'jumlah_jp' => ['type' => 'INT', 'constraint' => 11, 'default' => 8],
                'istirahat_json' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_pengaturan', true);
            $this->dbforge->add_key('id_pembelajaran');
            $this->dbforge->create_table('jadwal_pelajaran_pengaturan', true);
        }

        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            $this->dbforge->add_field([
                'id_jadwal' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran' => ['type' => 'INT', 'constraint' => 11],
                'hari' => ['type' => 'VARCHAR', 'constraint' => 20],
                'slot_ke' => ['type' => 'INT', 'constraint' => 11],
                'id_mapel' => ['type' => 'INT', 'constraint' => 11],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_jadwal', true);
            $this->dbforge->add_key('id_pembelajaran');
            $this->dbforge->create_table('jadwal_pelajaran_item', true);
        }
    }
}
