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
                'link_video' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'slide_drive_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_agenda', true);
            $this->dbforge->add_key('id_pembelajaran_mapel');
            $this->dbforge->create_table('agenda_pembelajaran', true);
        } else {
            if (!$this->db->field_exists('slide_drive_id', 'agenda_pembelajaran')) {
                $this->dbforge->add_column('agenda_pembelajaran', [
                    'slide_drive_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('link_video', 'agenda_pembelajaran')) {
                $this->dbforge->add_column('agenda_pembelajaran', [
                    'link_video' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }
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
        $this->db->reconnect();
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
        // Aktifkan db_debug agar error database SQL (seperti kolom hilang) langsung memicu error yang terlihat
        $db_debug_original = $this->db->db_debug;
        $this->db->db_debug = true;

        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            $this->db->db_debug = $db_debug_original;
            return false;
        }

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

        $this->db->db_debug = $db_debug_original;
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

        if ($prev_pm) {
            // Pastikan ada agenda di tahun sebelumnya
            $count = $this->db->get_where($this->agenda_table, ['id_pembelajaran_mapel' => $prev_pm->id_pembelajaran_mapel])->num_rows();
            if ($count > 0) {
                return $prev_pm->id_pembelajaran_mapel;
            }
        }

        return null;
    }

    public function getOtherActiveRombelAgendas($target_id_pembelajaran_mapel)
    {
        $target = $this->getPembelajaranMapel($target_id_pembelajaran_mapel);
        if (!$target) return [];

        $this->db->select('pm.id_pembelajaran_mapel, r.nama_rombel, tp.tahun_pelajaran, tp.semester, COUNT(ap.id_agenda) as total_agenda');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join($this->agenda_table . ' ap', 'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->where('p.id_tahun_pelajaran', $target->id_tahun_pelajaran);
        $this->db->where('p.id_tingkat_sekolah', $target->id_tingkat_sekolah);
        $this->db->where('pm.id_mapel', $target->id_mapel);
        $this->db->where('pm.id_pembelajaran_mapel !=', $target_id_pembelajaran_mapel);
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->having('total_agenda >', 0);
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

    /**
     * Get Mapel and Rombel filter options for active year
     */
    public function getGuruMapelRombelFilter($ptk_id = null)
    {
        // Mapel List
        $this->db->select('DISTINCT(m.id_mapel) as id_mapel, m.nama_mapel');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        $this->db->order_by('m.nama_mapel', 'ASC');
        $mapel_list = $this->db->get()->result();

        // Rombel List
        $this->db->select('DISTINCT(r.id_rombel) as id_rombel, r.nama_rombel, pm.id_mapel');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        $this->db->order_by('r.nama_rombel', 'ASC');
        $rombel_list = $this->db->get()->result();

        return [
            'mapel_list'  => $mapel_list,
            'rombel_list' => $rombel_list
        ];
    }

    /**
     * Get filtered Agenda Pembelajaran
     */
    public function getAgendaGuruFiltered($ptk_id = null, $id_mapel = null, $id_rombel = null, $status = null)
    {
        $this->db->select('a.*, pm.id_pembelajaran_mapel, r.id_rombel, r.nama_rombel, t.nama_tingkat, t.tingkat_angka, m.id_mapel, m.nama_mapel, tp.tahun_pelajaran, tp.semester, ptk.nama_ptk');
        $this->db->from('agenda_pembelajaran a');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = a.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');

        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');

        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        if ($id_mapel) {
            $this->db->where('m.id_mapel', (int) $id_mapel);
        }
        if ($id_rombel) {
            $this->db->where('r.id_rombel', (int) $id_rombel);
        }
        if ($status) {
            if ($status === 'Terlambat') {
                $today = date('Y-m-d');
                $this->db->where('a.status !=', 'Terlaksana');
                $this->db->where('a.tanggal <', $today);
            } else {
                $this->db->where('a.status', $status);
            }
        }

        $this->db->order_by('a.tanggal', 'ASC');
        $this->db->order_by('a.jam_mulai', 'ASC');
        $this->db->order_by('a.pertemuan_ke', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Quick toggle status agenda (Belum <-> Terlaksana)
     */
    public function toggleAgendaStatus($id_agenda)
    {
        $row = $this->db->get_where('agenda_pembelajaran', ['id_agenda' => (int) $id_agenda])->row();
        if (!$row) return false;

        $new_status = ($row->status === 'Terlaksana') ? 'Belum' : 'Terlaksana';

        $this->db->where('id_agenda', (int) $id_agenda);
        $this->db->update('agenda_pembelajaran', [
            'status'     => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $new_status;
    }

    /**
     * Get upcoming / today's Agenda Pembelajaran for specific PTK Guru
     */
    public function getAgendaTerdekatGuru($ptk_id, $limit = 5)
    {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $this->db->select('a.*, pm.id_pembelajaran_mapel, r.id_rombel, r.nama_rombel, t.nama_tingkat, t.tingkat_angka, m.id_mapel, m.nama_mapel, tp.tahun_pelajaran, tp.semester, ptk.nama_ptk');
        $this->db->from('agenda_pembelajaran a');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = a.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');

        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        $this->db->where('a.status !=', 'Terlaksana');

        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }

        // Prioritaskan Hari Ini (0), Besok (1), Tanggal Mendatang (2), Tanggal Lalu (3)
        $this->db->order_by("CASE 
            WHEN a.tanggal = '$today' THEN 0 
            WHEN a.tanggal = '$tomorrow' THEN 1 
            WHEN a.tanggal > '$tomorrow' THEN 2 
            ELSE 3 
        END", "ASC", false);
        $this->db->order_by('a.tanggal', 'ASC');
        $this->db->order_by('a.jam_mulai', 'ASC');

        $this->db->limit((int) $limit);

        return $this->db->get()->result();
    }

    /**
     * Get single Agenda Pembelajaran Detail by ID
     */
    public function getAgendaDetail($id_agenda)
    {
        $this->db->select('a.*, pm.id_pembelajaran_mapel, pm.id_ptk, r.id_rombel, r.nama_rombel, t.nama_tingkat, t.tingkat_angka, m.id_mapel, m.nama_mapel, tp.tahun_pelajaran, tp.semester, ptk.nama_ptk');
        $this->db->from('agenda_pembelajaran a');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = a.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('a.id_agenda', (int) $id_agenda);

        $query = $this->db->get();
        if ($query) {
            return $query->row();
        }
        return null;
    }

    /**
     * Update status and catatan for Agenda Pembelajaran
     */
    public function updateAgendaStatusCatatan($id_agenda, $status, $catatan = null, $hambatan = null, $pemecahan = null)
    {
        $data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($catatan !== null) {
            $data['catatan'] = $catatan;
        }
        if ($hambatan !== null) {
            $data['hambatan'] = $hambatan;
        }
        if ($pemecahan !== null) {
            $data['pemecahan'] = $pemecahan;
        }

        $this->db->where('id_agenda', (int) $id_agenda);
        return $this->db->update('agenda_pembelajaran', $data);
    }

    public function ensurePresensiAgendaTable()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `presensi_agenda_siswa` (
              `id_presensi_agenda` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `id_agenda` INT UNSIGNED NOT NULL,
              `id_siswa` INT UNSIGNED NOT NULL,
              `status` ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NULL DEFAULT NULL,
              `catatan` VARCHAR(255) NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
              UNIQUE KEY `uk_agenda_siswa` (`id_agenda`, `id_siswa`),
              INDEX `idx_agenda` (`id_agenda`),
              INDEX `idx_siswa` (`id_siswa`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    /**
     * Get daftar siswa rombel agenda beserta status presensi
     */
    public function getSiswaAgenda($id_agenda)
    {
        $this->ensurePresensiAgendaTable();

        $agenda = $this->getAgendaDetail($id_agenda);
        if (!$agenda) return [];

        $show_menginduk = (isset($_GET['show_menginduk']) && $_GET['show_menginduk'] == '1');
        $menginduk_ids  = [];
        if (!$show_menginduk && $this->db->table_exists('kelas_jauh_siswa')) {
            $q_kj = $this->db->select('id_siswa')->get('kelas_jauh_siswa');
            if ($q_kj && $q_kj->num_rows() > 0) {
                $menginduk_ids = array_column($q_kj->result_array(), 'id_siswa');
            }
        }

        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin, pas.status AS status_presensi, pas.catatan AS catatan_presensi');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('presensi_agenda_siswa pas', 'pas.id_agenda = ' . (int)$id_agenda . ' AND pas.id_siswa = s.id_siswa', 'left');
        $this->db->where('pm.id_pembelajaran_mapel', (int)$agenda->id_pembelajaran_mapel);

        if (!empty($menginduk_ids)) {
            $this->db->where_not_in('s.id_siswa', $menginduk_ids);
        }

        $this->db->order_by('s.nama_siswa', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Simpan status presensi 1 siswa secara realtime (AJAX Auto-save)
     */
    public function saveSinglePresensiSiswa($id_agenda, $id_siswa, $status, $catatan = null)
    {
        $this->ensurePresensiAgendaTable();

        $allowed_status = ['Hadir', 'Izin', 'Sakit', 'Alpa'];
        if (!in_array($status, $allowed_status)) {
            $status = null;
        }

        $data = [
            'id_agenda'  => (int)$id_agenda,
            'id_siswa'   => (int)$id_siswa,
            'status'     => $status,
            'catatan'    => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->replace('presensi_agenda_siswa', $data);

        // --- SINKRONISASI / OVERRIDE OTOMATIS KE PRESENSI SISWA (presensi_override) ---
        $agenda = $this->getAgendaDetail($id_agenda);
        if ($agenda && !empty($agenda->tanggal)) {
            $tanggal = $agenda->tanggal;

            // Pastikan tabel presensi_override sudah dibuat
            if (!$this->db->table_exists('presensi_override')) {
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `presensi_override` (
                        `id`         INT(11) NOT NULL AUTO_INCREMENT,
                        `tipe_user`  ENUM('siswa','ptk') NOT NULL,
                        `id_user`    INT(11) NOT NULL,
                        `pin`        VARCHAR(100) NOT NULL DEFAULT '',
                        `tanggal`    DATE NOT NULL,
                        `status`     VARCHAR(20) NOT NULL DEFAULT 'Hadir',
                        `keterangan` TEXT NULL,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `idx_override_user_tgl` (`tipe_user`, `id_user`, `tanggal`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            }

            if ($status === 'Sakit' || $status === 'Izin') {
                $status_override = $status;

                $siswa = $this->db->get_where('siswa', ['id_siswa' => (int)$id_siswa])->row();
                $pin = $siswa ? (string)$siswa->nipd : '';

                $mapel_info = !empty($agenda->nama_mapel) ? ' (' . $agenda->nama_mapel . ')' : '';
                $ket_override = !empty($catatan) 
                    ? $catatan 
                    : ($status_override . $mapel_info);

                $existing = $this->db->get_where('presensi_override', [
                    'tipe_user' => 'siswa',
                    'id_user'   => (int)$id_siswa,
                    'tanggal'   => $tanggal
                ])->row();

                if ($existing) {
                    $this->db->where('id', $existing->id);
                    $this->db->update('presensi_override', [
                        'status'     => $status_override,
                        'keterangan' => $ket_override
                    ]);
                } else {
                    $this->db->insert('presensi_override', [
                        'tipe_user'  => 'siswa',
                        'id_user'    => (int)$id_siswa,
                        'pin'        => $pin,
                        'tanggal'    => $tanggal,
                        'status'     => $status_override,
                        'keterangan' => $ket_override
                    ]);
                }
            } else {
                // Jika status Hadir, Alpa, atau kosong, hapus override (kecuali ada agenda lain pada hari itu yang Sakit/Izin)
                $other_agenda = $this->db->select('pas.status, pas.catatan, m.nama_mapel')
                    ->from('presensi_agenda_siswa pas')
                    ->join('agenda_pembelajaran ap', 'ap.id_agenda = pas.id_agenda')
                    ->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = ap.id_pembelajaran_mapel')
                    ->join('mapel m', 'm.id_mapel = pm.id_mapel', 'left')
                    ->where('ap.tanggal', $tanggal)
                    ->where('pas.id_siswa', (int)$id_siswa)
                    ->where('pas.id_agenda !=', (int)$id_agenda)
                    ->where_in('pas.status', ['Sakit', 'Izin'])
                    ->get()->row();

                if ($other_agenda) {
                    $other_status = $other_agenda->status;
                    $mapel_info   = !empty($other_agenda->nama_mapel) ? ' (' . $other_agenda->nama_mapel . ')' : '';
                    $other_ket    = !empty($other_agenda->catatan) ? $other_agenda->catatan : ($other_status . $mapel_info);

                    $this->db->where([
                        'tipe_user' => 'siswa',
                        'id_user'   => (int)$id_siswa,
                        'tanggal'   => $tanggal
                    ]);
                    $this->db->update('presensi_override', [
                        'status'     => $other_status,
                        'keterangan' => $other_ket
                    ]);
                } else {
                    // Hapus override jika tidak ada ketidakhadiran (Sakit/Izin) di agenda lain pada hari itu
                    $this->db->delete('presensi_override', [
                        'tipe_user' => 'siswa',
                        'id_user'   => (int)$id_siswa,
                        'tanggal'   => $tanggal
                    ]);
                }
            }
        }
    }

    /**
     * Get Rekap Absensi Agenda Per Mapel / Rombel Per Bulan untuk Guru
     */
    public function getRekapAbsensiAgendaGuru($ptk_id, $id_mapel, $id_rombel, $bulan, $tahun)
    {
        $this->ensurePresensiAgendaTable();

        // 1. Ambil daftar pembelajaran_mapel yang diampu oleh PTK/Guru
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_pembelajaran, pm.id_mapel');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        if ($id_mapel) {
            $this->db->where('pm.id_mapel', (int) $id_mapel);
        }
        if ($id_rombel) {
            $this->db->where('p.id_rombel', (int) $id_rombel);
        }
        $pm_rows = $this->db->get()->result();

        $pembelajaran_ids = array_unique(array_column($pm_rows, 'id_pembelajaran'));

        // 2. Ambil seluruh daftar siswa yang diampu di Rombel/Pembelajaran tersebut
        $siswa_list = [];
        if (!empty($pembelajaran_ids)) {
            $show_menginduk = (isset($_GET['show_menginduk']) && $_GET['show_menginduk'] == '1');
            $menginduk_ids  = [];
            if (!$show_menginduk && $this->db->table_exists('kelas_jauh_siswa')) {
                $q_kj = $this->db->select('id_siswa')->get('kelas_jauh_siswa');
                if ($q_kj && $q_kj->num_rows() > 0) {
                    $menginduk_ids = array_column($q_kj->result_array(), 'id_siswa');
                }
            }

            $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin');
            $this->db->from('pembelajaran_siswa ps');
            $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
            $this->db->where_in('ps.id_pembelajaran', $pembelajaran_ids);

            if (!empty($menginduk_ids)) {
                $this->db->where_not_in('s.id_siswa', $menginduk_ids);
            }

            $this->db->group_by('s.id_siswa');
            $this->db->order_by('s.nama_siswa', 'ASC');
            $siswa_list = $this->db->get()->result();
        }

        // 3. Ambil daftar agenda KBM pada mapel, rombel & bulan/tahun terpilih
        $this->db->select('a.id_agenda, a.tanggal, p.id_pembelajaran, pm.id_mapel');
        $this->db->from('agenda_pembelajaran a');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = a.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        if ($id_mapel) {
            $this->db->where('pm.id_mapel', (int) $id_mapel);
        }
        if ($id_rombel) {
            $this->db->where('p.id_rombel', (int) $id_rombel);
        }
        $this->db->where('MONTH(a.tanggal)', (int) $bulan);
        $this->db->where('YEAR(a.tanggal)', (int) $tahun);

        $agendas = $this->db->get()->result();
        $total_pertemuan = count($agendas);
        $agenda_ids = array_column($agendas, 'id_agenda');

        // 4. Hitung akumulasi presensi per siswa (Meskipun belum ada agenda, daftar siswa TETAP TAMPIL!)
        $rekap_siswa = [];
        foreach ($siswa_list as $siswa) {
            $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

            if (!empty($agenda_ids)) {
                $this->db->select("
                    SUM(CASE WHEN pas.status = 'Hadir' THEN 1 ELSE 0 END) AS total_hadir,
                    SUM(CASE WHEN pas.status = 'Izin' THEN 1 ELSE 0 END) AS total_izin,
                    SUM(CASE WHEN pas.status = 'Sakit' THEN 1 ELSE 0 END) AS total_sakit,
                    SUM(CASE WHEN pas.status = 'Alpa' THEN 1 ELSE 0 END) AS total_alpa
                ");
                $this->db->from('presensi_agenda_siswa pas');
                $this->db->where_in('pas.id_agenda', $agenda_ids);
                $this->db->where('pas.id_siswa', (int) $siswa->id_siswa);

                $stat = $this->db->get()->row();
                if ($stat) {
                    $hadir = (int) $stat->total_hadir;
                    $izin  = (int) $stat->total_izin;
                    $sakit = (int) $stat->total_sakit;
                    $alpa  = (int) $stat->total_alpa;
                }
            }

            $persentase = $total_pertemuan > 0 ? round(($hadir / $total_pertemuan) * 100, 1) : 0;

            $rekap_siswa[] = (object) [
                'id_siswa'      => $siswa->id_siswa,
                'nama_siswa'    => $siswa->nama_siswa,
                'nisn'          => $siswa->nisn,
                'nipd'          => $siswa->nipd,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'total_hadir'   => $hadir,
                'total_izin'    => $izin,
                'total_sakit'   => $sakit,
                'total_alpa'    => $alpa,
                'persentase'    => $persentase
            ];
        }

        return [
            'agendas'         => $agendas,
            'total_pertemuan' => $total_pertemuan,
            'rekap_siswa'     => $rekap_siswa
        ];
    }

    /**
     * Konversi tanggal Y-m-d ke Nama Hari Bahasa Indonesia
     */
    private function getNamaHariIndo($tanggal)
    {
        $day = date('N', strtotime($tanggal));
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        return isset($days[$day]) ? $days[$day] : 'Senin';
    }

    /**
     * Update Waktu & Jadwal Agenda (Hari, Tanggal, Jam Mulai, Jam Selesai)
     * Tanpa merubah materi, kegiatan, catatan, atau berkas media.
     */
    public function updateAgendaJadwalWaktu($id_agenda, $tanggal, $jam_mulai = null, $jam_selesai = null)
    {
        $hari = $this->getNamaHariIndo($tanggal);

        $data = [
            'tanggal'    => $tanggal,
            'hari'       => $hari,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($jam_mulai !== null) {
            $data['jam_mulai'] = $jam_mulai;
        }
        if ($jam_selesai !== null) {
            $data['jam_selesai'] = $jam_selesai;
        }

        $this->db->where('id_agenda', (int) $id_agenda);
        return $this->db->update('agenda_pembelajaran', $data);
    }

    /**
     * Get Pengaturan Kerangka Waktu Mingguan untuk suatu pembelajaran dan hari
     */
    public function getJadwalPengaturanPembelajaran($id_pembelajaran, $hari)
    {
        $setting = null;
        if ($this->db->table_exists('jadwal_pelajaran_pengaturan')) {
            $row = $this->db->get_where('jadwal_pelajaran_pengaturan', [
                'id_pembelajaran' => (int) $id_pembelajaran,
                'hari'            => $hari
            ])->row();

            if (!$row) {
                $row = $this->db->get_where('jadwal_pelajaran_pengaturan', [
                    'id_pembelajaran' => 0,
                    'hari'            => $hari
                ])->row();
            }

            if ($row) {
                $setting = [
                    'jam_mulai' => !empty($row->jam_mulai) ? substr($row->jam_mulai, 0, 5) : '07:00',
                    'menit_jp'  => (int) ($row->menit_jp ?: 40),
                    'jumlah_jp' => (int) ($row->jumlah_jp ?: 8),
                    'istirahat' => json_decode($row->istirahat_json ?: '[]', true) ?: []
                ];
            }
        }

        if (!$setting) {
            $setting = [
                'jam_mulai' => '07:00',
                'menit_jp'  => 40,
                'jumlah_jp' => 8,
                'istirahat' => [['name' => 'Istirahat', 'after' => 4, 'duration' => 20]]
            ];
        }

        return $setting;
    }

    /**
     * Hitung Waktu Jam Mulai & Jam Selesai berbasis Slot (Min Slot & Max Slot)
     */
    public function calculateSlotTimeRange($setting, $min_slot, $max_slot)
    {
        $jam_mulai_str = $setting['jam_mulai'];
        $menit_jp      = (int) $setting['menit_jp'];
        
        $breaks = [];
        if (!empty($setting['istirahat']) && is_array($setting['istirahat'])) {
            foreach ($setting['istirahat'] as $b) {
                if (isset($b['after'], $b['duration'])) {
                    $breaks[(int) $b['after']] = (int) $b['duration'];
                }
            }
        }

        $parts = explode(':', $jam_mulai_str);
        $start_minutes = ((int) $parts[0] * 60) + (isset($parts[1]) ? (int) $parts[1] : 0);

        // Calculate start time of min_slot
        $min_start_min = $start_minutes;
        if (isset($breaks[0])) {
            $min_start_min += (int) $breaks[0];
        }
        for ($i = 1; $i < $min_slot; $i++) {
            $min_start_min += $menit_jp;
            if (isset($breaks[$i])) {
                $min_start_min += (int) $breaks[$i];
            }
        }

        // Calculate end time of max_slot
        $max_end_min = $start_minutes;
        if (isset($breaks[0])) {
            $max_end_min += (int) $breaks[0];
        }
        for ($i = 1; $i <= $max_slot; $i++) {
            $max_end_min += $menit_jp;
            if (isset($breaks[$i]) && $i < $max_slot) {
                $max_end_min += (int) $breaks[$i];
            }
        }

        $h_start = sprintf('%02d', floor($min_start_min / 60));
        $m_start = sprintf('%02d', $min_start_min % 60);

        $h_end   = sprintf('%02d', floor($max_end_min / 60));
        $m_end   = sprintf('%02d', $max_end_min % 60);

        return [
            'jam_mulai'   => $h_start . ':' . $m_start,
            'jam_selesai' => $h_end . ':' . $m_end
        ];
    }

    /**
     * Get referensi Jadwal Master KBM secara realtime dari kerangka waktu mingguan
     */
    public function getJadwalMasterPembelajaran($id_pembelajaran_mapel, $hari_spesifik = null)
    {
        $pm = $this->db->get_where('pembelajaran_mapel', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if (!$pm) {
            return null;
        }

        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            return null;
        }

        $this->db->select('hari, MIN(slot_ke) AS min_slot, MAX(slot_ke) AS max_slot');
        $this->db->from('jadwal_pelajaran_item');
        $this->db->where('id_pembelajaran', (int) $pm->id_pembelajaran);
        $this->db->where('id_mapel', (int) $pm->id_mapel);
        if (!empty($hari_spesifik)) {
            $this->db->where('hari', $hari_spesifik);
        }
        $this->db->group_by('hari');

        $row = $this->db->get()->row();
        if (!$row) {
            return null;
        }

        $setting = $this->getJadwalPengaturanPembelajaran($pm->id_pembelajaran, $row->hari);
        $time_range = $this->calculateSlotTimeRange($setting, (int) $row->min_slot, (int) $row->max_slot);

        return (object) [
            'hari'        => $row->hari,
            'jam_mulai'   => $time_range['jam_mulai'],
            'jam_selesai' => $time_range['jam_selesai'],
            'min_slot'    => (int) $row->min_slot,
            'max_slot'    => (int) $row->max_slot
        ];
    }

    /**
     * Sinkronisasi Massal Jam Masuk & Jam Keluar Seluruh Agenda dengan Jadwal Master KBM
     */
    public function syncAllAgendaWithMasterJadwal($ptk_id = null, $id_mapel = null, $id_rombel = null)
    {
        $this->db->select('a.id_agenda, a.id_pembelajaran_mapel, a.tanggal, a.hari');
        $this->db->from('agenda_pembelajaran a');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = a.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        $this->db->where('a.status !=', 'Terlaksana'); // Jangan sesuaikan agenda yang sudah terlaksana

        if ($ptk_id) {
            $this->db->where('pm.id_ptk', (int) $ptk_id);
        }
        if ($id_mapel) {
            $this->db->where('pm.id_mapel', (int) $id_mapel);
        }
        if ($id_rombel) {
            $this->db->where('p.id_rombel', (int) $id_rombel);
        }

        $agendas = $this->db->get()->result();
        if (empty($agendas)) {
            return 0;
        }

        $count_updated = 0;
        foreach ($agendas as $ag) {
            $master = $this->getJadwalMasterPembelajaran($ag->id_pembelajaran_mapel, $ag->hari);
            if ($master && (!empty($master->jam_mulai) || !empty($master->jam_selesai))) {
                $jam_mulai   = !empty($master->jam_mulai) ? $master->jam_mulai : null;
                $jam_selesai = !empty($master->jam_selesai) ? $master->jam_selesai : null;

                $this->updateAgendaJadwalWaktu($ag->id_agenda, $ag->tanggal, $jam_mulai, $jam_selesai);
                $count_updated++;
            }
        }

        return $count_updated;
    }

    /**
     * Dapatkan daftar hari KBM dari jadwal pelajaran master
     */
    public function getJadwalMasterHariList($id_pembelajaran_mapel)
    {
        $pm = $this->db->get_where('pembelajaran_mapel', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if (!$pm || !$this->db->table_exists('jadwal_pelajaran_item')) {
            return [];
        }

        $this->db->distinct();
        $this->db->select('hari');
        $this->db->from('jadwal_pelajaran_item');
        $this->db->where('id_pembelajaran', (int) $pm->id_pembelajaran);
        $this->db->where('id_mapel', (int) $pm->id_mapel);
        $rows = $this->db->get()->result();

        $hari_list = [];
        foreach ($rows as $r) {
            if (!empty($r->hari)) {
                $hari_list[] = trim($r->hari);
            }
        }
        return $hari_list;
    }

    /**
     * Periksa apakah hari, tanggal, atau jam mulai/keluar agenda mengalami ketidaksesuaian dengan jadwal pelajaran master
     */
    public function checkAgendaJadwalMismatch($agenda)
    {
        if (!$agenda || empty($agenda->id_pembelajaran_mapel) || empty($agenda->tanggal)) {
            return ['is_mismatch' => false, 'master_days' => []];
        }

        // Jangan deteksi untuk agenda yang sudah terlaksana
        if (!empty($agenda->status) && $agenda->status === 'Terlaksana') {
            return ['is_mismatch' => false, 'master_days' => []];
        }

        $master_days = $this->getJadwalMasterHariList($agenda->id_pembelajaran_mapel);
        if (empty($master_days)) {
            return ['is_mismatch' => false, 'master_days' => []];
        }

        $days_map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        $day_num = date('N', strtotime($agenda->tanggal));
        $actual_day = isset($days_map[$day_num]) ? $days_map[$day_num] : '';
        $saved_day  = !empty($agenda->hari) ? trim($agenda->hari) : '';

        $master_days_lower = array_map('strtolower', array_map('trim', $master_days));
        $actual_day_lower  = strtolower(trim($actual_day));
        $saved_day_lower   = strtolower(trim($saved_day));

        // 1. Cek Kesesuaian Hari
        $day_not_in_master = !in_array($actual_day_lower, $master_days_lower, true);
        $day_name_mismatch = (!empty($saved_day) && $saved_day_lower !== $actual_day_lower);

        if ($day_not_in_master || $day_name_mismatch) {
            $reason = '';
            if ($day_not_in_master && $day_name_mismatch) {
                $reason = "Hari agenda ($saved_day) & tanggal ($actual_day) tidak sesuai dengan Jadwal Pelajaran Master (" . implode(', ', $master_days) . ")";
            } elseif ($day_not_in_master) {
                $reason = "Hari $actual_day (tanggal " . date('d/m/Y', strtotime($agenda->tanggal)) . ") tidak dijadwalkan pada Jadwal Pelajaran Master (" . implode(', ', $master_days) . ")";
            } else {
                $reason = "Hari tersimpan ($saved_day) tidak cocok dengan hari sebenarnya ($actual_day) pada tanggal " . date('d/m/Y', strtotime($agenda->tanggal));
            }

            return [
                'is_mismatch'   => true,
                'mismatch_type' => 'day',
                'actual_day'    => $actual_day,
                'saved_day'     => $saved_day,
                'master_days'   => $master_days,
                'reason'        => $reason
            ];
        }

        // 2. Cek Kesesuaian Jam Mulai & Jam Selesai dengan Master Jadwal untuk Hari ini
        $master_info = $this->getJadwalMasterPembelajaran($agenda->id_pembelajaran_mapel, $actual_day);
        if ($master_info && (!empty($master_info->jam_mulai) || !empty($master_info->jam_selesai))) {
            $m_jam_mulai   = !empty($master_info->jam_mulai) ? date('H:i', strtotime($master_info->jam_mulai)) : '';
            $m_jam_selesai = !empty($master_info->jam_selesai) ? date('H:i', strtotime($master_info->jam_selesai)) : '';

            $a_jam_mulai   = !empty($agenda->jam_mulai) ? date('H:i', strtotime($agenda->jam_mulai)) : '';
            $a_jam_selesai = !empty($agenda->jam_selesai) ? date('H:i', strtotime($agenda->jam_selesai)) : '';

            $jam_mulai_mismatch   = (!empty($m_jam_mulai) && (empty($a_jam_mulai) || $a_jam_mulai !== $m_jam_mulai));
            $jam_selesai_mismatch = (!empty($m_jam_selesai) && (empty($a_jam_selesai) || $a_jam_selesai !== $m_jam_selesai));

            if ($jam_mulai_mismatch || $jam_selesai_mismatch) {
                $reason_time = "";
                if (empty($a_jam_mulai)) {
                    $reason_time = "Jam masuk KBM belum diatur (Jadwal Master: $m_jam_mulai - $m_jam_selesai WIB)";
                } else {
                    $reason_time = "Jam KBM agenda ($a_jam_mulai - $a_jam_selesai WIB) tidak sama dengan Jadwal Pelajaran Master ($m_jam_mulai - $m_jam_selesai WIB)";
                }

                return [
                    'is_mismatch'        => true,
                    'mismatch_type'      => 'time',
                    'actual_day'         => $actual_day,
                    'saved_day'          => $saved_day,
                    'master_days'        => $master_days,
                    'master_jam_mulai'   => $m_jam_mulai,
                    'master_jam_selesai' => $m_jam_selesai,
                    'agenda_jam_mulai'   => $a_jam_mulai,
                    'agenda_jam_selesai' => $a_jam_selesai,
                    'reason'             => $reason_time
                ];
            }
        }

        return [
            'is_mismatch' => false,
            'master_days' => $master_days,
            'actual_day'  => $actual_day
        ];
    }
}
