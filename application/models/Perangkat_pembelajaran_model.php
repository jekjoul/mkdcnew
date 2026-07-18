<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat_pembelajaran_model extends MY_Model
{
    private $perangkat_table = 'perangkat_pembelajaran';
    private $agenda_table = 'agenda_pembelajaran';

    public function ensureTables()
    {
        $this->load->dbforge();

        // Drop old table schemas if necessary to recreate clean new structure
        if ($this->db->table_exists('perangkat_materi_harian')) {
            $this->dbforge->drop_table('perangkat_materi_harian', true);
        }

        if ($this->db->table_exists('perangkat_pembelajaran')) {
            if (!$this->db->field_exists('id_tahun_pelajaran', 'perangkat_pembelajaran')) {
                $this->dbforge->drop_table('perangkat_pembelajaran', true);
            }
        }

        if (!$this->db->table_exists('perangkat_pembelajaran')) {
            $this->dbforge->add_field([
                'id_perangkat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_tahun_pelajaran' => ['type' => 'INT', 'constraint' => 11],
                'id_tingkat_sekolah' => ['type' => 'INT', 'constraint' => 11],
                'id_mapel' => ['type' => 'INT', 'constraint' => 11],
                'file_cp' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_tp' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_atp' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_modul_ajar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_kisi_sts' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_soal_sts' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_kisi_sas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'file_soal_sas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_perangkat', true);
            $this->dbforge->create_table('perangkat_pembelajaran', true);
        }

        if (!$this->db->table_exists('agenda_pembelajaran')) {
            $this->dbforge->add_field([
                'id_agenda' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11],
                'tanggal' => ['type' => 'DATE'],
                'hari' => ['type' => 'VARCHAR', 'constraint' => 20],
                'pertemuan_ke' => ['type' => 'INT', 'constraint' => 11],
                'materi' => ['type' => 'TEXT', 'null' => true],
                'kegiatan' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Belum'],
                'catatan' => ['type' => 'TEXT', 'null' => true],
                'jumlah_jam' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'jam_mulai' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'jam_selesai' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_agenda', true);
            $this->dbforge->add_key('id_pembelajaran_mapel');
            $this->dbforge->create_table('agenda_pembelajaran', true);
        }
    }

    public function getPembelajaranMapel($id_pembelajaran_mapel)
    {
        $this->db->select('pm.*, p.id_tahun_pelajaran, p.id_tingkat_sekolah, p.id_rombel, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, tp.kurikulum, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('pm.id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        $query = $this->db->get();
        if ($query) {
            return $query->row();
        }
        return null;
    }

    public function getPerangkatByMapel($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return null;

        return $this->db->get_where($this->perangkat_table, [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel
        ])->row();
    }

    public function getAgendaByMapel($id_pembelajaran_mapel)
    {
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get_where($this->agenda_table, ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->result();
    }

    public function getAdminItems($status_tahun = 'Aktif')
    {
        $this->db->select('MIN(pm.id_pembelajaran_mapel) AS id_pembelajaran_mapel, pp.id_perangkat, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, GROUP_CONCAT(DISTINCT r.nama_rombel ORDER BY r.nama_rombel SEPARATOR ", ") AS nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, GROUP_CONCAT(DISTINCT ptk.nama_ptk ORDER BY ptk.nama_ptk SEPARATOR ", ") AS nama_ptk, COUNT(ap.id_agenda) AS total_materi, SUM(CASE WHEN ap.status = "Terlaksana" THEN 1 ELSE 0 END) AS diajarkan, pp.file_cp, pp.file_tp, pp.file_atp, pp.file_kktp, pp.file_kisi_sts, pp.file_soal_sts, pp.file_kisi_sas, pp.file_soal_sas, (SELECT COUNT(*) FROM perangkat_pembelajaran_modul_ajar ma WHERE ma.id_tahun_pelajaran = p.id_tahun_pelajaran AND ma.id_tingkat_sekolah = p.id_tingkat_sekolah AND ma.id_mapel = pm.id_mapel) AS total_modul_ajar');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join($this->perangkat_table . ' pp', 'pp.id_tahun_pelajaran = p.id_tahun_pelajaran AND pp.id_tingkat_sekolah = p.id_tingkat_sekolah AND pp.id_mapel = pm.id_mapel', 'left');
        $this->db->join($this->agenda_table . ' ap', 'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        
        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
            $this->db->where('p.status', 'Aktif');
        } else {
            $this->db->group_start();
            $this->db->where('tp.status !=', 'Aktif');
            $this->db->or_where('p.status !=', 'Aktif');
            $this->db->group_end();
        }

        $this->db->group_by('p.id_tahun_pelajaran');
        $this->db->group_by('p.id_lembaga');
        $this->db->group_by('p.id_tingkat_sekolah');
        $this->db->group_by('pm.id_mapel');

        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    public function getGuruItems($id_ptk, $status_tahun = 'Aktif')
    {
        $this->db->select('MIN(pm.id_pembelajaran_mapel) AS id_pembelajaran_mapel, pp.id_perangkat, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, GROUP_CONCAT(DISTINCT r.nama_rombel ORDER BY r.nama_rombel SEPARATOR ", ") AS nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, COUNT(ap.id_agenda) AS total_materi, SUM(CASE WHEN ap.status = "Terlaksana" THEN 1 ELSE 0 END) AS diajarkan, pp.file_cp, pp.file_tp, pp.file_atp, pp.file_kktp, pp.file_kisi_sts, pp.file_soal_sts, pp.file_kisi_sas, pp.file_soal_sas, (SELECT COUNT(*) FROM perangkat_pembelajaran_modul_ajar ma WHERE ma.id_tahun_pelajaran = p.id_tahun_pelajaran AND ma.id_tingkat_sekolah = p.id_tingkat_sekolah AND ma.id_mapel = pm.id_mapel) AS total_modul_ajar');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join($this->perangkat_table . ' pp', 'pp.id_tahun_pelajaran = p.id_tahun_pelajaran AND pp.id_tingkat_sekolah = p.id_tingkat_sekolah AND pp.id_mapel = pm.id_mapel', 'left');
        $this->db->join($this->agenda_table . ' ap', 'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->where('pm.id_ptk', (int) $id_ptk);

        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
            $this->db->where('p.status', 'Aktif');
        } else {
            $this->db->group_start();
            $this->db->where('tp.status !=', 'Aktif');
            $this->db->or_where('p.status !=', 'Aktif');
            $this->db->group_end();
        }

        $this->db->group_by('p.id_tahun_pelajaran');
        $this->db->group_by('p.id_lembaga');
        $this->db->group_by('p.id_tingkat_sekolah');
        $this->db->group_by('pm.id_mapel');

        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    public function hasScheduleAndEffectiveDays($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        $schedule_count = $this->db->get_where('jadwal_pelajaran_item', [
            'id_pembelajaran' => $item->id_pembelajaran,
            'id_mapel' => $item->id_mapel
        ])->num_rows();

        $effective_count = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where_in('status', ['Efektif', 'Daring', 'Luar Kelas'])
            ->get('pembelajaran_hari_efektif')->num_rows();

        return ($schedule_count > 0 && $effective_count > 0);
    }

    public function generateAgenda($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        // Clean existing agenda for this mapel if any
        $this->db->delete($this->agenda_table, ['id_pembelajaran_mapel' => $id_pembelajaran_mapel]);

        // Get class schedules (jadwal pelajaran)
        $schedules = $this->db->get_where('jadwal_pelajaran_item', [
            'id_pembelajaran' => $item->id_pembelajaran,
            'id_mapel' => $item->id_mapel
        ])->result();

        if (empty($schedules)) return false;

        // Get pengaturan jadwal for calculating exact time
        $pengaturan_rows = $this->db->get_where('jadwal_pelajaran_pengaturan', [
            'id_pembelajaran' => $item->id_pembelajaran
        ])->result();
        
        if (empty($pengaturan_rows)) {
            $pengaturan_rows = $this->db->get_where('jadwal_pelajaran_pengaturan', [
                'id_pembelajaran' => 0
            ])->result();
        }

        $pengaturan_by_day = [];
        foreach ($pengaturan_rows as $p_row) {
            $pengaturan_by_day[strtolower($p_row->hari)] = $p_row;
        }

        // Group slots by day
        $slots_by_day = [];
        foreach ($schedules as $sched) {
            $day_key = strtolower($sched->hari);
            if (!isset($slots_by_day[$day_key])) {
                $slots_by_day[$day_key] = [];
            }
            $slots_by_day[$day_key][] = $sched->slot_ke;
        }

        $scheduled_days = [];
        foreach ($slots_by_day as $day_key => $slots) {
            sort($slots); // [1, 2, 3] etc
            $jumlah_jam = count($slots);
            $jam_mulai = '';
            $jam_selesai = '';

            $p_day = isset($pengaturan_by_day[$day_key]) ? $pengaturan_by_day[$day_key] : null;

            if ($p_day && !empty($p_day->jam_mulai)) {
                $istirahat = json_decode($p_day->istirahat_json) ?: [];
                $istirahat_map = [];
                foreach ($istirahat as $ist) {
                    $after_slot = isset($ist->after) ? (int)$ist->after : (isset($ist->setelah_jp_ke) ? (int)$ist->setelah_jp_ke : 0);
                    $durasi = isset($ist->duration) ? (int)$ist->duration : (isset($ist->durasi_menit) ? (int)$ist->durasi_menit : 0);
                    if ($after_slot > 0) {
                        $istirahat_map[$after_slot] = $durasi;
                    }
                }

                // Helper to find slot times
                $getSlotTime = function($target_slot) use ($p_day, $istirahat_map) {
                    $time_sec = strtotime($p_day->jam_mulai);
                    for ($i = 1; $i < $target_slot; $i++) {
                        $time_sec += ($p_day->menit_jp * 60);
                        if (isset($istirahat_map[$i])) {
                            $time_sec += ($istirahat_map[$i] * 60);
                        }
                    }
                    $end_sec = $time_sec + ($p_day->menit_jp * 60);
                    return [date('H:i', $time_sec), date('H:i', $end_sec)];
                };

                $first_slot = $slots[0];
                $last_slot = $slots[count($slots) - 1];

                $start_info = $getSlotTime($first_slot);
                $end_info = $getSlotTime($last_slot);

                $jam_mulai = $start_info[0];
                $jam_selesai = $end_info[1];
            }

            $scheduled_days[$day_key] = [
                'jumlah_jam' => $jumlah_jam,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai
            ];
        }

        // Get active teaching days (hari aktif pembelajaran) - Hanya yang berstatus 'Efektif' saja
        $active_days = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where('status', 'Efektif')
            ->order_by('tanggal', 'ASC')
            ->get('pembelajaran_hari_efektif')->result();

        if (empty($active_days)) return false;

        $pageNum = 1;
        $now = date('Y-m-d H:i:s');
        
        $day_names = [
            0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'
        ];

        // Hitung total pertemuan valid N terlebih dahulu
        $valid_count = 0;
        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];
            if (isset($scheduled_days[$day_ind])) {
                $valid_count++;
            }
        }

        // Tentukan indeks Tugas (minimal 3) & Ujian Harian (minimal 2) secara berkala
        $used = [1 => true]; // Indeks 1 sudah dipakai untuk perkenalan
        $target_tugas = [
            max(2, (int)floor($valid_count * 0.25)),
            max(3, (int)floor($valid_count * 0.55)),
            max(4, (int)floor($valid_count * 0.85))
        ];
        $target_uh = [
            max(3, (int)floor($valid_count * 0.40)),
            max(5, (int)floor($valid_count * 0.75))
        ];

        $final_tugas = [];
        foreach ($target_tugas as $t) {
            while (isset($used[$t]) && $t < $valid_count) {
                $t++;
            }
            $used[$t] = true;
            $final_tugas[] = $t;
        }

        $final_uh = [];
        foreach ($target_uh as $u) {
            while (isset($used[$u]) && $u < $valid_count) {
                $u++;
            }
            $used[$u] = true;
            $final_uh[] = $u;
        }

        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];
            
            if (isset($scheduled_days[$day_ind])) {
                $sched_info = $scheduled_days[$day_ind];
                $current_pert = $pageNum++;
                
                $materi = '';
                $kegiatan = '';
                
                if ($current_pert === 1) {
                    $materi = 'Perkenalan Guru dan Mata Pelajaran';
                    $kegiatan = 'Perkenalan guru pengampu, kontrak belajar, serta pembahasan materi yang akan dipelajari selama satu semester ini.';
                } else {
                    $tugas_num = array_search($current_pert, $final_tugas);
                    $uh_num = array_search($current_pert, $final_uh);
                    
                    if ($tugas_num !== false) {
                        $materi = 'Pemberian Tugas Mandiri/Kelompok ' . ($tugas_num + 1);
                        $kegiatan = 'Pemberian tugas terstruktur untuk mengukur pemahaman konsep siswa terhadap kompetensi dasar.';
                    } elseif ($uh_num !== false) {
                        $materi = 'Penilaian Harian (PH) / Ujian Harian ' . ($uh_num + 1);
                        $kegiatan = 'Melaksanakan kegiatan Ujian Harian tertulis/praktik secara objektif untuk menilai kompetensi siswa.';
                    }
                }

                $this->db->insert($this->agenda_table, [
                    'id_pembelajaran_mapel' => $id_pembelajaran_mapel,
                    'tanggal' => $ad->tanggal,
                    'hari' => ucfirst($day_ind),
                    'pertemuan_ke' => $current_pert,
                    'materi' => $materi,
                    'kegiatan' => $kegiatan,
                    'status' => 'Belum',
                    'catatan' => '',
                    'jumlah_jam' => $sched_info['jumlah_jam'],
                    'jam_mulai' => $sched_info['jam_mulai'],
                    'jam_selesai' => $sched_info['jam_selesai'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        return $pageNum > 1;
    }

    public function generateAgendaAI($id_pembelajaran_mapel, $ai_data)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        // Clear existing agenda first
        $this->db->delete($this->agenda_table, ['id_pembelajaran_mapel' => $id_pembelajaran_mapel]);

        // Get class schedules (jadwal pelajaran) specifically for this mapel
        $schedules = $this->db->get_where('jadwal_pelajaran_item', [
            'id_pembelajaran' => $item->id_pembelajaran,
            'id_mapel' => $item->id_mapel
        ])->result();

        if (empty($schedules)) return false;

        // Get pengaturan jadwal for calculating exact time
        $pengaturan = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => $item->id_pembelajaran])->result();
        if (empty($pengaturan)) {
            $pengaturan = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => 0])->result();
        }
        
        $pengaturan_by_day = [];
        foreach ($pengaturan as $p) {
            $pengaturan_by_day[strtolower($p->hari)] = $p;
        }

        $slots_by_day = [];
        foreach ($schedules as $sched) {
            $day_key = strtolower($sched->hari);
            if (!isset($slots_by_day[$day_key])) {
                $slots_by_day[$day_key] = [];
            }
            $slots_by_day[$day_key][] = $sched->slot_ke;
        }

        $scheduled_days = [];
        foreach ($slots_by_day as $day_key => $slots) {
            sort($slots);
            $jumlah_jam = count($slots);
            $jam_mulai = '';
            $jam_selesai = '';

            $p_day = isset($pengaturan_by_day[$day_key]) ? $pengaturan_by_day[$day_key] : null;

            if ($p_day && !empty($p_day->jam_mulai)) {
                $istirahat = json_decode($p_day->istirahat_json) ?: [];
                $istirahat_map = [];
                foreach ($istirahat as $ist) {
                    // Check either 'after' (used by weekly scheduler settings) or 'setelah_jp_ke'
                    $after_slot = isset($ist->after) ? (int)$ist->after : (isset($ist->setelah_jp_ke) ? (int)$ist->setelah_jp_ke : 0);
                    $durasi = isset($ist->duration) ? (int)$ist->duration : (isset($ist->durasi_menit) ? (int)$ist->durasi_menit : 0);
                    if ($after_slot > 0) {
                        $istirahat_map[$after_slot] = $durasi;
                    }
                }

                $getSlotTime = function($target_slot) use ($p_day, $istirahat_map) {
                    $time_sec = strtotime($p_day->jam_mulai);
                    for ($i = 1; $i < $target_slot; $i++) {
                        $time_sec += ($p_day->menit_jp * 60);
                        if (isset($istirahat_map[$i])) {
                            $time_sec += ($istirahat_map[$i] * 60);
                        }
                    }
                    $end_sec = $time_sec + ($p_day->menit_jp * 60);
                    return [date('H:i', $time_sec), date('H:i', $end_sec)];
                };

                $first_slot = $slots[0];
                $last_slot = $slots[count($slots) - 1];

                $start_info = $getSlotTime($first_slot);
                $end_info = $getSlotTime($last_slot);

                $jam_mulai = $start_info[0];
                $jam_selesai = $end_info[1];
            }

            $scheduled_days[$day_key] = [
                'jumlah_jam' => $jumlah_jam,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai
            ];
        }

        // Hanya yang berstatus 'Efektif' saja
        $active_days = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where('status', 'Efektif')
            ->order_by('tanggal', 'ASC')
            ->get('pembelajaran_hari_efektif')->result();

        if (empty($active_days)) return false;

        $pageNum = 1;
        $now = date('Y-m-d H:i:s');
        $day_names = [
            0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'
        ];

        // Hitung total pertemuan valid N terlebih dahulu
        $valid_count = 0;
        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];
            if (isset($scheduled_days[$day_ind])) {
                $valid_count++;
            }
        }

        // Tentukan indeks Tugas (minimal 3) & Ujian Harian (minimal 2) secara berkala
        $used = [1 => true]; // Indeks 1 sudah dipakai untuk perkenalan
        $target_tugas = [
            max(2, (int)floor($valid_count * 0.25)),
            max(3, (int)floor($valid_count * 0.55)),
            max(4, (int)floor($valid_count * 0.85))
        ];
        $target_uh = [
            max(3, (int)floor($valid_count * 0.40)),
            max(5, (int)floor($valid_count * 0.75))
        ];

        $final_tugas = [];
        foreach ($target_tugas as $t) {
            while (isset($used[$t]) && $t < $valid_count) {
                $t++;
            }
            $used[$t] = true;
            $final_tugas[] = $t;
        }

        $final_uh = [];
        foreach ($target_uh as $u) {
            while (isset($used[$u]) && $u < $valid_count) {
                $u++;
            }
            $used[$u] = true;
            $final_uh[] = $u;
        }

        $ai_by_pertemuan = [];
        foreach ($ai_data as $ai_row) {
            if (isset($ai_row['pertemuan'])) {
                $ai_by_pertemuan[(int)$ai_row['pertemuan']] = $ai_row;
            }
        }

        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];
            
            if (isset($scheduled_days[$day_ind])) {
                $sched_info = $scheduled_days[$day_ind];
                $current_pert = $pageNum++;
                
                if ($current_pert === 1) {
                    $materi_ai = 'Perkenalan Guru dan Mata Pelajaran';
                    $kegiatan_ai = 'Perkenalan guru pengampu, kontrak belajar, serta pembahasan materi yang akan dipelajari selama satu semester ini.';
                } else {
                    $ai_index = $current_pert - 1;
                    $materi_ai = isset($ai_by_pertemuan[$ai_index]['materi']) ? $ai_by_pertemuan[$ai_index]['materi'] : '';
                    $kegiatan_ai = isset($ai_by_pertemuan[$ai_index]['kegiatan']) ? $ai_by_pertemuan[$ai_index]['kegiatan'] : '';
                    
                    $tugas_num = array_search($current_pert, $final_tugas);
                    $uh_num = array_search($current_pert, $final_uh);
                    
                    if ($tugas_num !== false) {
                        $materi_ai = '[Tugas Mandiri/Kelompok ' . ($tugas_num + 1) . '] ' . $materi_ai;
                        $kegiatan_ai = 'Pemberian tugas terstruktur. ' . $kegiatan_ai;
                    } elseif ($uh_num !== false) {
                        $materi_ai = '[Penilaian Harian ' . ($uh_num + 1) . '] ' . $materi_ai;
                        $kegiatan_ai = 'Melaksanakan Ujian Harian / Penilaian Harian (PH) tertulis. ' . $kegiatan_ai;
                    }
                }

                $this->db->insert($this->agenda_table, [
                    'id_pembelajaran_mapel' => $id_pembelajaran_mapel,
                    'tanggal' => $ad->tanggal,
                    'hari' => ucfirst($day_ind),
                    'pertemuan_ke' => $current_pert,
                    'materi' => $materi_ai,
                    'kegiatan' => $kegiatan_ai,
                    'status' => 'Belum',
                    'catatan' => '',
                    'jumlah_jam' => $sched_info['jumlah_jam'],
                    'jam_mulai' => $sched_info['jam_mulai'],
                    'jam_selesai' => $sched_info['jam_selesai'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        return $pageNum > 1;
    }

    public function saveBerkas($id_pembelajaran_mapel, $files)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        $existing = $this->db->get_where($this->perangkat_table, [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel
        ])->row();

        $data = [];
        foreach ($files as $key => $filename) {
            if ($filename !== null) {
                $data[$key] = $filename;
            }
        }

        if (empty($data)) return false;

        $now = date('Y-m-d H:i:s');
        if ($existing) {
            $data['updated_at'] = $now;
            $this->db->where('id_perangkat', $existing->id_perangkat);
            return $this->db->update($this->perangkat_table, $data);
        } else {
            $data['id_tahun_pelajaran'] = $item->id_tahun_pelajaran;
            $data['id_tingkat_sekolah'] = $item->id_tingkat_sekolah;
            $data['id_mapel'] = $item->id_mapel;
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
            return $this->db->insert($this->perangkat_table, $data);
        }
    }

    public function saveDriveIds($id_pembelajaran_mapel, $drive_ids)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return;

        $existing = $this->db->get_where($this->perangkat_table, [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel
        ])->row();

        if ($existing) {
            $this->db->where('id_perangkat', $existing->id_perangkat);
            $this->db->update($this->perangkat_table, $drive_ids);
        }
    }

    public function deleteBerkasFile($id_pembelajaran_mapel, $field)
    {
        $existing = $this->getPerangkatByMapel($id_pembelajaran_mapel);
        if ($existing && !empty($existing->$field)) {
            $filepath = './uploads/perangkat_pembelajaran/' . $existing->$field;
            if (is_file($filepath)) {
                unlink($filepath);
            }
            $key_drive = str_replace('file_', '', $field) . '_drive_file_id';
            $this->db->where('id_perangkat', $existing->id_perangkat);
            $this->db->update($this->perangkat_table, [
                $field => null,
                $key_drive => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function saveAgenda($id_pembelajaran_mapel, $agenda_post)
    {
        foreach ((array) $agenda_post as $id_agenda => $row) {
            $id_agenda = (int) $id_agenda;
            if (!$id_agenda) continue;

            $this->db->where('id_agenda', $id_agenda);
            $this->db->where('id_pembelajaran_mapel', $id_pembelajaran_mapel);
            $this->db->update($this->agenda_table, [
                'materi' => isset($row['materi']) ? $row['materi'] : '',
                'kegiatan' => isset($row['kegiatan']) ? $row['kegiatan'] : '',
                'status' => isset($row['status']) ? $row['status'] : 'Belum',
                'catatan' => isset($row['catatan']) ? $row['catatan'] : '',
                'jumlah_jam' => isset($row['jumlah_jam']) ? (int)$row['jumlah_jam'] : null,
                'jam_mulai' => isset($row['jam_mulai']) ? $row['jam_mulai'] : null,
                'jam_selesai' => isset($row['jam_selesai']) ? $row['jam_selesai'] : null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function copyPerangkatFromLastYear($id_tahun_pelajaran, $id_tingkat_sekolah, $id_mapel)
    {
        // Find previous school year
        $prev_year = $this->db->where('id_tahun_pelajaran <', $id_tahun_pelajaran)
            ->order_by('id_tahun_pelajaran', 'DESC')
            ->limit(1)
            ->get('pembelajaran_tahun_pelajaran')->row();
            
        if (!$prev_year) return false;

        $source = $this->db->get_where($this->perangkat_table, [
            'id_tahun_pelajaran' => $prev_year->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $id_tingkat_sekolah,
            'id_mapel' => $id_mapel
        ])->row();

        if (!$source) return false;

        // Copy files
        $fields = ['file_cp', 'file_tp', 'file_atp', 'file_modul_ajar', 'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'];
        $data = [
            'id_tahun_pelajaran' => $id_tahun_pelajaran,
            'id_tingkat_sekolah' => $id_tingkat_sekolah,
            'id_mapel' => $id_mapel,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        foreach ($fields as $field) {
            $data[$field] = $source->$field;
        }

        $existing = $this->db->get_where($this->perangkat_table, [
            'id_tahun_pelajaran' => $id_tahun_pelajaran,
            'id_tingkat_sekolah' => $id_tingkat_sekolah,
            'id_mapel' => $id_mapel
        ])->row();

        if ($existing) {
            $this->db->where('id_perangkat', $existing->id_perangkat)->update($this->perangkat_table, $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->perangkat_table, $data);
        }
        return true;
    }

    public function copyAgendaFromSource($target_id_pembelajaran_mapel, $source_id_pembelajaran_mapel)
    {
        // Get target agenda items (must already be generated)
        $target_items = $this->db->get_where($this->agenda_table, ['id_pembelajaran_mapel' => $target_id_pembelajaran_mapel])->result();
        if (empty($target_items)) {
            // Generate it first
            $gen = $this->generateAgenda($target_id_pembelajaran_mapel);
            if (!$gen) return false;
            $target_items = $this->db->get_where($this->agenda_table, ['id_pembelajaran_mapel' => $target_id_pembelajaran_mapel])->result();
        }

        // Get source agenda items
        $source_items = $this->db->get_where($this->agenda_table, ['id_pembelajaran_mapel' => $source_id_pembelajaran_mapel])->result();
        if (empty($source_items)) return false;

        // Map source items by pertemuan_ke
        $source_map = [];
        foreach ($source_items as $s) {
            $source_map[$s->pertemuan_ke] = $s;
        }

        // Update target items
        foreach ($target_items as $t) {
            if (isset($source_map[$t->pertemuan_ke])) {
                $src = $source_map[$t->pertemuan_ke];
                $this->db->where('id_agenda', $t->id_agenda);
                $this->db->update($this->agenda_table, [
                    'materi' => $src->materi,
                    'kegiatan' => $src->kegiatan,
                    'status' => $src->status,
                    'catatan' => $src->catatan,
                    'jumlah_jam' => $src->jumlah_jam,
                    'jam_mulai' => $src->jam_mulai,
                    'jam_selesai' => $src->jam_selesai,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        return true;
    }

    public function getSourceLastYearAgenda($target_id_pembelajaran_mapel)
    {
        $target = $this->getPembelajaranMapel($target_id_pembelajaran_mapel);
        if (!$target) return null;

        // Find previous school year
        $prev_year = $this->db->where('id_tahun_pelajaran <', $target->id_tahun_pelajaran)
            ->order_by('id_tahun_pelajaran', 'DESC')
            ->limit(1)
            ->get('pembelajaran_tahun_pelajaran')->row();
        if (!$prev_year) return null;

        // Find pembelajaran with same rombel in prev year
        $prev_pemb = $this->db->get_where('pembelajaran', [
            'id_tahun_pelajaran' => $prev_year->id_tahun_pelajaran,
            'id_rombel' => $target->id_rombel
        ])->row();
        if (!$prev_pemb) return null;

        // Find pembelajaran_mapel
        $prev_pm = $this->db->get_where('pembelajaran_mapel', [
            'id_pembelajaran' => $prev_pemb->id_pembelajaran,
            'id_mapel' => $target->id_mapel
        ])->row();

        return $prev_pm ? $prev_pm->id_pembelajaran_mapel : null;
    }

    public function getOtherActiveRombelAgendas($target_id_pembelajaran_mapel)
    {
        $target = $this->getPembelajaranMapel($target_id_pembelajaran_mapel);
        if (!$target) return [];

        $this->db->select('pm.id_pembelajaran_mapel, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->where('p.id_tahun_pelajaran', $target->id_tahun_pelajaran);
        $this->db->where('p.id_tingkat_sekolah', $target->id_tingkat_sekolah);
        $this->db->where('pm.id_mapel', $target->id_mapel);
        $this->db->where('pm.id_pembelajaran_mapel !=', $target_id_pembelajaran_mapel);
        $this->db->order_by('r.nama_rombel', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Ambil semua rombel (termasuk rombel aktif saat ini) yang mengampu
     * mapel & tingkat yang sama di tahun ajaran yang sama, lengkap dengan
     * statistik agenda masing-masing rombel.
     */
    public function getAllRombelSameMapelTingkat($id_pembelajaran_mapel, $id_ptk = null)
    {
        $target = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$target) return [];

        $this->db->select([
            'pm.id_pembelajaran_mapel',
            'r.nama_rombel',
            'ptk.nama_ptk',
            'COUNT(ap.id_agenda) AS total_agenda',
            'SUM(CASE WHEN ap.status = "Terlaksana" THEN 1 ELSE 0 END) AS terlaksana',
        ]);
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p',                'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r',                     'r.id_rombel = p.id_rombel');
        $this->db->join('ptk',                          'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join($this->agenda_table . ' ap',    'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->where('p.id_tahun_pelajaran',  $target->id_tahun_pelajaran);
        $this->db->where('p.id_tingkat_sekolah',  $target->id_tingkat_sekolah);
        $this->db->where('pm.id_mapel',            $target->id_mapel);
        if ($id_ptk !== null) {
            $this->db->where('pm.id_ptk', (int) $id_ptk);
        }
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('r.nama_rombel', 'ASC');

        return $this->db->get()->result();
    }

    public function getModulAjarByMapel($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return [];

        return $this->db->get_where('perangkat_pembelajaran_modul_ajar', [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel
        ])->result();
    }

    public function saveModulAjar($id_pembelajaran_mapel, $nama_file, $drive_file_id, $label)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        $now = date('Y-m-d H:i:s');
        return $this->db->insert('perangkat_pembelajaran_modul_ajar', [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel,
            'nama_file' => $nama_file,
            'drive_file_id' => $drive_file_id,
            'label' => $label,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    public function deleteModulAjar($id_modul)
    {
        $row = $this->db->get_where('perangkat_pembelajaran_modul_ajar', ['id_modul' => (int)$id_modul])->row();
        if ($row) {
            $filepath = './uploads/perangkat_pembelajaran/' . $row->nama_file;
            if (is_file($filepath)) {
                unlink($filepath);
            }
            $this->db->delete('perangkat_pembelajaran_modul_ajar', ['id_modul' => (int)$id_modul]);
            return $row;
        }
        return false;
    }
}
