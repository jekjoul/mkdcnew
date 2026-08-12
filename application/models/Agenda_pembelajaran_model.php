<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agenda_pembelajaran_model extends MY_Model
{
    private $agenda_table = 'agenda_pembelajaran';

    public function ensureTables()
    {
        $this->load->dbforge();

        // Ensure columns in agenda_pembelajaran
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
            if (!$this->db->field_exists('media_files', 'agenda_pembelajaran')) {
                $this->dbforge->add_column('agenda_pembelajaran', [
                    'media_files' => ['type' => 'TEXT', 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('hambatan', 'agenda_pembelajaran')) {
                $this->dbforge->add_column('agenda_pembelajaran', [
                    'hambatan' => ['type' => 'TEXT', 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('pemecahan', 'agenda_pembelajaran')) {
                $this->dbforge->add_column('agenda_pembelajaran', [
                    'pemecahan' => ['type' => 'TEXT', 'null' => true]
                ]);
            }
        }

        // Ensure custom agenda fields in pembelajaran_mapel
        if ($this->db->table_exists('pembelajaran_mapel')) {
            if (!$this->db->field_exists('judul_agenda', 'pembelajaran_mapel')) {
                $this->dbforge->add_column('pembelajaran_mapel', [
                    'judul_agenda' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('id_ptk_pemilik', 'pembelajaran_mapel')) {
                $this->dbforge->add_column('pembelajaran_mapel', [
                    'id_ptk_pemilik' => ['type' => 'INT', 'constraint' => 11, 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('status_takeover', 'pembelajaran_mapel')) {
                $this->dbforge->add_column('pembelajaran_mapel', [
                    'status_takeover' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Tidak']
                ]);
            }
            if (!$this->db->field_exists('id_ptk_takeover', 'pembelajaran_mapel')) {
                $this->dbforge->add_column('pembelajaran_mapel', [
                    'id_ptk_takeover' => ['type' => 'INT', 'constraint' => 11, 'null' => true]
                ]);
            }
        }

        // Ensure draft columns in jadwal_pelajaran_item
        if ($this->db->table_exists('jadwal_pelajaran_item')) {
            if (!$this->db->field_exists('status_draft', 'jadwal_pelajaran_item')) {
                $this->dbforge->add_column('jadwal_pelajaran_item', [
                    'status_draft' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Aktif']
                ]);
            }
            if (!$this->db->field_exists('tanggal_efektif', 'jadwal_pelajaran_item')) {
                $this->dbforge->add_column('jadwal_pelajaran_item', [
                    'tanggal_efektif' => ['type' => 'DATE', 'null' => true]
                ]);
            }
        }
    }

    public function getPembelajaranMapel($id_pembelajaran_mapel)
    {
        $this->db->select('pm.*, p.id_tahun_pelajaran, p.id_tingkat_sekolah, p.id_rombel, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, tp.kurikulum, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk as nama_guru_pengampu, ptk_pemilik.nama_ptk as nama_guru_pemilik, ptk_takeover.nama_ptk as nama_guru_takeover');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('ptk ptk_pemilik', 'ptk_pemilik.id_ptk = pm.id_ptk_pemilik', 'left');
        $this->db->join('ptk ptk_takeover', 'ptk_takeover.id_ptk = pm.id_ptk_takeover', 'left');
        $this->db->where('pm.id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        $query = $this->db->get();
        if ($query) {
            $row = $query->row();
            if ($row && empty($row->judul_agenda)) {
                $row->judul_agenda = $this->getDefaultAgendaTitle($row);
            }
            return $row;
        }
        return null;
    }

    public function getDefaultAgendaTitle($item)
    {
        if (!$item) return 'Agenda Pembelajaran Harian';
        return "Agenda Harian Mapel " . $item->nama_mapel . " Kelas " . $item->nama_rombel . " Semester " . $item->semester . " Tahun Pelajaran " . $item->tahun_pelajaran;
    }

    public function getAdminItems($status_tahun = 'Aktif', $id_ptk = null)
    {
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_ptk, pm.judul_agenda, pm.status_takeover, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, COALESCE(ptk.nama_ptk, ptk_pemilik.nama_ptk, ptk_takeover.nama_ptk) as nama_ptk, COUNT(ap.id_agenda) AS total_agenda, SUM(CASE WHEN ap.status = "Terlaksana" THEN 1 ELSE 0 END) AS terlaksana');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('ptk ptk_pemilik', 'ptk_pemilik.id_ptk = pm.id_ptk_pemilik', 'left');
        $this->db->join('ptk ptk_takeover', 'ptk_takeover.id_ptk = pm.id_ptk_takeover', 'left');
        $this->db->join('agenda_pembelajaran ap', 'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');

        // Only include Agendas that have been explicitly created/named by teachers
        $this->db->where('pm.judul_agenda IS NOT NULL', NULL, FALSE);
        $this->db->where("TRIM(pm.judul_agenda) != ''", NULL, FALSE);

        if (!empty($id_ptk)) {
            $this->db->group_start();
            $this->db->where('pm.id_ptk', (int) $id_ptk);
            $this->db->or_where('pm.id_ptk_pemilik', (int) $id_ptk);
            $this->db->or_where('pm.id_ptk_takeover', (int) $id_ptk);
            $this->db->group_end();
        }

        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
        } elseif ($status_tahun === 'Tidak Aktif') {
            $this->db->where('tp.status !=', 'Aktif');
        }

        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        
        return $this->db->get()->result();
    }

    public function getAvailableMapelForGuru($id_ptk = null, $status_tahun = null)
    {
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_ptk, pm.judul_agenda, p.id_tahun_pelajaran, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, COALESCE(ptk.nama_ptk, ptk_pemilik.nama_ptk, ptk_takeover.nama_ptk) as nama_ptk');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('ptk ptk_pemilik', 'ptk_pemilik.id_ptk = pm.id_ptk_pemilik', 'left');
        $this->db->join('ptk ptk_takeover', 'ptk_takeover.id_ptk = pm.id_ptk_takeover', 'left');

        if (!empty($id_ptk)) {
            $this->db->group_start();
            $this->db->where('pm.id_ptk', (int) $id_ptk);
            $this->db->or_where('pm.id_ptk_pemilik', (int) $id_ptk);
            $this->db->or_where('pm.id_ptk_takeover', (int) $id_ptk);
            $this->db->group_end();
        }

        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
        }

        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');

        return $this->db->get()->result();
    }

    public function getGuruItems($id_ptk, $status_tahun = 'Aktif')
    {
        return $this->getAdminItems($status_tahun, $id_ptk);
    }

    public function getAgendaByMapel($id_pembelajaran_mapel)
    {
        return $this->db->order_by('pertemuan_ke', 'ASC')
            ->get_where($this->agenda_table, ['id_pembelajaran_mapel' => $id_pembelajaran_mapel])
            ->result();
    }

    public function getItemAgenda($id_agenda)
    {
        return $this->db->get_where($this->agenda_table, ['id_agenda' => (int) $id_agenda])->row();
    }

    public function updateItemAgenda($id_agenda, $data)
    {
        $this->db->where('id_agenda', (int) $id_agenda);
        return $this->db->update($this->agenda_table, $data);
    }

    public function updateJudulAgenda($id_pembelajaran_mapel, $judul)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        $this->db->where('id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        return $this->db->update('pembelajaran_mapel', ['judul_agenda' => trim($judul)]);
    }

    public function takeoverAgenda($id_pembelajaran_mapel, $new_id_ptk)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return false;

        $old_ptk = $item->id_ptk;
        $pemilik = $item->id_ptk_pemilik ?: $old_ptk;

        $this->db->where('id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        return $this->db->update('pembelajaran_mapel', [
            'id_ptk' => (int) $new_id_ptk,
            'id_ptk_pemilik' => (int) $pemilik,
            'id_ptk_takeover' => (int) $new_id_ptk,
            'status_takeover' => 'Ya'
        ]);
    }

    public function getAvailableTemplates($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return [];

        $id_mapel = (int)$item->id_mapel;
        $id_tingkat = (int)$item->id_tingkat_sekolah;
        $target_sem = strtolower(trim((string)$item->semester));
        $target_id_tp = (int)$item->id_tahun_pelajaran;

        $is_ganjil = (strpos($target_sem, '1') !== false || strpos($target_sem, 'ganjil') !== false);
        $is_genap  = (strpos($target_sem, '2') !== false || strpos($target_sem, 'genap') !== false);

        // Strict match: same mapel, same tingkat_sekolah, and same semester type
        $this->db->select('pm.id_pembelajaran_mapel, pm.judul_agenda, ptk.nama_ptk, tp.tahun_pelajaran, tp.semester, p.id_tahun_pelajaran, r.nama_rombel, COUNT(ap.id_agenda) as total_agenda, (CASE WHEN p.id_tahun_pelajaran = ' . $target_id_tp . ' THEN 1 ELSE 0 END) as is_same_year');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join('agenda_pembelajaran ap', 'ap.id_pembelajaran_mapel = pm.id_pembelajaran_mapel');
        
        $this->db->where('pm.judul_agenda IS NOT NULL', NULL, FALSE);
        $this->db->where("TRIM(pm.judul_agenda) != ''", NULL, FALSE);
        $this->db->where('pm.id_mapel', $id_mapel);
        
        if ($id_tingkat > 0) {
            $this->db->where('p.id_tingkat_sekolah', $id_tingkat);
        }

        $this->db->where('pm.id_pembelajaran_mapel !=', (int)$id_pembelajaran_mapel);

        if ($is_ganjil) {
            $this->db->where_in('tp.semester', ['1', 'Ganjil', 'ganjil']);
        } elseif ($is_genap) {
            $this->db->where_in('tp.semester', ['2', 'Genap', 'genap']);
        } else {
            $this->db->where('tp.semester', $item->semester);
        }

        $this->db->group_by(['pm.id_pembelajaran_mapel', 'pm.judul_agenda', 'ptk.nama_ptk', 'tp.tahun_pelajaran', 'tp.semester', 'p.id_tahun_pelajaran', 'r.nama_rombel']);
        $this->db->having('total_agenda > 0');
        $this->db->order_by('is_same_year', 'DESC');
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('r.nama_rombel', 'ASC');

        $query = $this->db->get();
        return ($query && is_object($query)) ? $query->result() : [];
    }

    public function copyAndAdaptAgenda($target_id_pembelajaran_mapel, $source_id_pembelajaran_mapel)
    {
        $target_item = $this->getPembelajaranMapel($target_id_pembelajaran_mapel);
        $source_agendas = $this->getAgendaByMapel($source_id_pembelajaran_mapel);

        if (!$target_item || empty($source_agendas)) return false;

        $upload_dir = './uploads/agenda_media/';
        $now = date('Y-m-d H:i:s');

        // Helper function for copying media files
        $duplicateMediaFiles = function($media_files_json) use ($upload_dir) {
            if (empty($media_files_json)) return null;
            $media_items = json_decode($media_files_json, true);
            if (!is_array($media_items)) return null;

            $new_media_items = [];
            foreach ($media_items as $media) {
                if (isset($media['type']) && $media['type'] === 'file' && !empty($media['file_name'])) {
                    $old_file = $upload_dir . $media['file_name'];
                    if (is_file($old_file)) {
                        $ext = pathinfo($media['file_name'], PATHINFO_EXTENSION);
                        $new_filename = 'agenda_copy_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                        $new_file = $upload_dir . $new_filename;
                        @copy($old_file, $new_file);

                        $new_media = $media;
                        $new_media['file_name'] = $new_filename;
                        $new_media_items[] = $new_media;
                    } else {
                        $new_media_items[] = $media;
                    }
                } else {
                    $new_media_items[] = $media;
                }
            }
            return json_encode($new_media_items);
        };

        // Try schedule-based adaptation first
        $schedules = [];
        if ($this->db->table_exists('jadwal_pelajaran_item')) {
            $where_sched = [
                'id_pembelajaran' => $target_item->id_pembelajaran,
                'id_mapel' => $target_item->id_mapel
            ];
            if ($this->db->field_exists('status_draft', 'jadwal_pelajaran_item')) {
                $where_sched['status_draft'] = 'Aktif';
            }
            $sched_query = $this->db->get_where('jadwal_pelajaran_item', $where_sched);
            $schedules = ($sched_query && is_object($sched_query)) ? $sched_query->result() : [];
        }

        $active_days = $this->db->where('id_tahun_pelajaran', $target_item->id_tahun_pelajaran)
            ->where('status', 'Efektif')
            ->order_by('tanggal', 'ASC')
            ->get('pembelajaran_hari_efektif')->result();

        // If no schedule or no active days, perform direct copy fallback
        if (empty($schedules) || empty($active_days)) {
            $this->db->delete($this->agenda_table, ['id_pembelajaran_mapel' => (int)$target_id_pembelajaran_mapel]);

            foreach ($source_agendas as $i => $src) {
                $copied_media_json = $duplicateMediaFiles($src->media_files);

                $this->db->insert($this->agenda_table, [
                    'id_pembelajaran_mapel' => $target_id_pembelajaran_mapel,
                    'tanggal' => $src->tanggal,
                    'hari' => $src->hari,
                    'pertemuan_ke' => $src->pertemuan_ke ?: ($i + 1),
                    'materi' => $src->materi,
                    'kegiatan' => $src->kegiatan,
                    'status' => 'Belum',
                    'catatan' => null,
                    'jumlah_jam' => $src->jumlah_jam ?: 2,
                    'jam_mulai' => $src->jam_mulai,
                    'jam_selesai' => $src->jam_selesai,
                    'link_video' => $src->link_video,
                    'slide_drive_id' => $src->slide_drive_id,
                    'media_files' => $copied_media_json,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
            return true;
        }

        $pengaturan = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => $target_item->id_pembelajaran])->result();
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
                    $after_slot = isset($ist->after) ? (int)$ist->after : (isset($ist->setelah_jp_ke) ? (int)$ist->setelah_jp_ke : 0);
                    $durasi = isset($ist->duration) ? (int)$ist->duration : (isset($ist->durasi_menit) ? (int)$ist->durasi_menit : 0);
                    if ($after_slot > 0) $istirahat_map[$after_slot] = $durasi;
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

                $start_info = $getSlotTime($slots[0]);
                $end_info = $getSlotTime($slots[count($slots) - 1]);
                $jam_mulai = $start_info[0];
                $jam_selesai = $end_info[1];
            }

            $scheduled_days[$day_key] = [
                'jumlah_jam' => $jumlah_jam,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai
            ];
        }

        $day_names = [0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'];

        $this->db->delete($this->agenda_table, ['id_pembelajaran_mapel' => (int)$target_id_pembelajaran_mapel]);

        $idx = 0;

        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];

            if (isset($scheduled_days[$day_ind])) {
                if (!isset($source_agendas[$idx])) break;
                $src = $source_agendas[$idx];
                $sched_info = $scheduled_days[$day_ind];

                $copied_media_json = $duplicateMediaFiles($src->media_files);

                $this->db->insert($this->agenda_table, [
                    'id_pembelajaran_mapel' => $target_id_pembelajaran_mapel,
                    'tanggal' => $ad->tanggal,
                    'hari' => ucfirst($day_ind),
                    'pertemuan_ke' => $idx + 1,
                    'materi' => $src->materi,
                    'kegiatan' => $src->kegiatan,
                    'status' => 'Belum',
                    'catatan' => null,
                    'jumlah_jam' => $sched_info['jumlah_jam'],
                    'jam_mulai' => $sched_info['jam_mulai'],
                    'jam_selesai' => $sched_info['jam_selesai'],
                    'link_video' => $src->link_video,
                    'slide_drive_id' => $src->slide_drive_id,
                    'media_files' => $copied_media_json,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $idx++;
            }
        }

        // If no agenda was inserted due to empty schedule day match, fallback to direct copy
        if ($idx === 0) {
            $this->db->delete($this->agenda_table, ['id_pembelajaran_mapel' => (int)$target_id_pembelajaran_mapel]);
            foreach ($source_agendas as $i => $src) {
                $copied_media_json = $duplicateMediaFiles($src->media_files);

                $this->db->insert($this->agenda_table, [
                    'id_pembelajaran_mapel' => $target_id_pembelajaran_mapel,
                    'tanggal' => $src->tanggal,
                    'hari' => $src->hari,
                    'pertemuan_ke' => $src->pertemuan_ke ?: ($i + 1),
                    'materi' => $src->materi,
                    'kegiatan' => $src->kegiatan,
                    'status' => 'Belum',
                    'catatan' => null,
                    'jumlah_jam' => $src->jumlah_jam ?: 2,
                    'jam_mulai' => $src->jam_mulai,
                    'jam_selesai' => $src->jam_selesai,
                    'link_video' => $src->link_video,
                    'slide_drive_id' => $src->slide_drive_id,
                    'media_files' => $copied_media_json,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
            return true;
        }

        return $idx > 0;
    }

    public function syncSchedule($id_pembelajaran_mapel, $start_date = null)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        $agendas = $this->getAgendaByMapel($id_pembelajaran_mapel);

        if (!$item || empty($agendas)) return false;

        $schedules = [];
        if ($this->db->table_exists('jadwal_pelajaran_item')) {
            $where_sched = [
                'id_pembelajaran' => $item->id_pembelajaran,
                'id_mapel' => $item->id_mapel
            ];
            if ($this->db->field_exists('status_draft', 'jadwal_pelajaran_item')) {
                $where_sched['status_draft'] = 'Aktif';
            }
            $sched_query = $this->db->get_where('jadwal_pelajaran_item', $where_sched);
            $schedules = ($sched_query && is_object($sched_query)) ? $sched_query->result() : [];
        }

        if (empty($schedules)) return false;

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
                    $after_slot = isset($ist->after) ? (int)$ist->after : (isset($ist->setelah_jp_ke) ? (int)$ist->setelah_jp_ke : 0);
                    $durasi = isset($ist->duration) ? (int)$ist->duration : (isset($ist->durasi_menit) ? (int)$ist->durasi_menit : 0);
                    if ($after_slot > 0) $istirahat_map[$after_slot] = $durasi;
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

                $start_info = $getSlotTime($slots[0]);
                $end_info = $getSlotTime($slots[count($slots) - 1]);
                $jam_mulai = $start_info[0];
                $jam_selesai = $end_info[1];
            }

            $scheduled_days[$day_key] = [
                'jumlah_jam' => $jumlah_jam,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai
            ];
        }

        $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran);
        $this->db->where('status', 'Efektif');
        if (!empty($start_date)) {
            $this->db->where('tanggal >=', $start_date);
        }
        $active_days = $this->db->order_by('tanggal', 'ASC')->get('pembelajaran_hari_efektif')->result();

        if (empty($active_days)) return false;

        $day_names = [0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'];
        $calendar_slots = [];

        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            $day_ind = $day_names[$w];
            if (isset($scheduled_days[$day_ind])) {
                $calendar_slots[] = [
                    'tanggal' => $ad->tanggal,
                    'hari' => ucfirst($day_ind),
                    'jumlah_jam' => $scheduled_days[$day_ind]['jumlah_jam'],
                    'jam_mulai' => $scheduled_days[$day_ind]['jam_mulai'],
                    'jam_selesai' => $scheduled_days[$day_ind]['jam_selesai']
                ];
            }
        }

        $now = date('Y-m-d H:i:s');
        $cal_index = 0;

        foreach ($agendas as $agenda) {
            if ($agenda->status === 'Terlaksana') {
                continue;
            }

            if (!empty($start_date)) {
                if ($agenda->tanggal < $start_date) {
                    continue;
                }
                if (isset($calendar_slots[$cal_index])) {
                    $cal = $calendar_slots[$cal_index];
                    $this->db->where('id_agenda', $agenda->id_agenda);
                    $this->db->update($this->agenda_table, [
                        'tanggal' => $cal['tanggal'],
                        'hari' => $cal['hari'],
                        'jumlah_jam' => $cal['jumlah_jam'],
                        'jam_mulai' => $cal['jam_mulai'],
                        'jam_selesai' => $cal['jam_selesai'],
                        'updated_at' => $now
                    ]);
                    $cal_index++;
                }
            } else {
                if (isset($calendar_slots[$cal_index])) {
                    $cal = $calendar_slots[$cal_index];
                    $this->db->where('id_agenda', $agenda->id_agenda);
                    $this->db->update($this->agenda_table, [
                        'tanggal' => $cal['tanggal'],
                        'hari' => $cal['hari'],
                        'jumlah_jam' => $cal['jumlah_jam'],
                        'jam_mulai' => $cal['jam_mulai'],
                        'jam_selesai' => $cal['jam_selesai'],
                        'updated_at' => $now
                    ]);
                    $cal_index++;
                }
            }
        }

        return true;
    }

    public function getActiveSchedules($id_pembelajaran, $id_mapel)
    {
        if ($this->db->table_exists('jadwal_pelajaran_item')) {
            return $this->db->get_where('jadwal_pelajaran_item', [
                'id_pembelajaran' => (int) $id_pembelajaran,
                'id_mapel' => (int) $id_mapel
            ])->result();
        } elseif ($this->db->table_exists('jadwal_pelajaran')) {
            return $this->db->get_where('jadwal_pelajaran', [
                'id_pembelajaran' => (int) $id_pembelajaran,
                'id_mapel' => (int) $id_mapel
            ])->result();
        }
        return [];
    }

    public function detectScheduleStatus($id_pembelajaran_mapel)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) return ['status' => 'error', 'message' => 'Data pembelajaran mapel tidak ditemukan'];

        // Check for pending draft schedule header
        $draft_header = null;
        if ($this->db->table_exists('jadwal_pelajaran_header')) {
            $draft_query = $this->db->get_where('jadwal_pelajaran_header', [
                'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
                'status' => 'Draft'
            ]);
            $draft_header = ($draft_query && is_object($draft_query)) ? $draft_query->row() : null;
        }

        if ($draft_header && !empty($draft_header->tanggal_mulai_efektif)) {
            return [
                'status' => 'draft_pending',
                'tanggal_efektif' => $draft_header->tanggal_mulai_efektif,
                'message' => 'Terdeteksi Rencana Draft Jadwal Baru "' . html_escape($draft_header->nama_jadwal) . '" yang akan berlaku efektif mulai tanggal ' . date('d M Y', strtotime($draft_header->tanggal_mulai_efektif)) . '.'
            ];
        }

        $agendas = $this->getAgendaByMapel($id_pembelajaran_mapel);
        if (empty($agendas)) {
            return ['status' => 'empty', 'message' => 'Agenda pembelajaran harian belum digenerate atau disalin'];
        }

        $schedules = $this->getActiveSchedules($item->id_pembelajaran, $item->id_mapel);

        if (empty($schedules)) {
            return ['status' => 'warning_no_schedule', 'message' => 'Jadwal pelajaran mingguan untuk mata pelajaran ini belum diatur di menu Jadwal Pelajaran'];
        }

        $slots_by_day = [];
        foreach ($schedules as $sched) {
            $slots_by_day[ucfirst(strtolower($sched->hari))] = true;
        }

        $has_mismatch = false;
        foreach ($agendas as $ag) {
            if ($ag->status !== 'Terlaksana' && !isset($slots_by_day[$ag->hari])) {
                $has_mismatch = true;
                break;
            }
        }

        if ($has_mismatch) {
            return ['status' => 'warning_schedule_changed', 'message' => 'Terdeteksi perubahan jadwal pelajaran. Silakan klik tombol "Sesuaikan Jadwal (Sync)" untuk memperbarui waktu agenda.'];
        }

        return ['status' => 'ok', 'message' => 'Jadwal agenda sudah selaras dengan jadwal pelajaran'];
    }

    public function getPtkAgendaSummary($status_tahun = 'Aktif')
    {
        $items = $this->getAdminItems($status_tahun);
        if (empty($items)) {
            $items = $this->getAdminItems(null);
        }

        $ptk_map = [];
        foreach ($items as $item) {
            $ptk_id = (int) ($item->id_ptk ?: (!empty($item->id_ptk_pemilik) ? $item->id_ptk_pemilik : (!empty($item->id_ptk_takeover) ? $item->id_ptk_takeover : 0)));
            if (!$ptk_id) {
                continue;
            }
            if (!isset($ptk_map[$ptk_id])) {
                $ptk_map[$ptk_id] = (object) [
                    'id_ptk' => $ptk_id,
                    'nama_ptk' => $item->nama_ptk ?: 'Guru (ID: ' . $ptk_id . ')',
                    'nip' => '',
                    'nuptk' => '',
                    'total_mapel' => 0,
                    'total_agenda' => 0,
                    'terlaksana' => 0
                ];
            }
            $ptk_map[$ptk_id]->total_mapel += 1;
            $ptk_map[$ptk_id]->total_agenda += (int) $item->total_agenda;
            $ptk_map[$ptk_id]->terlaksana += (int) $item->terlaksana;
        }

        if (!empty($ptk_map)) {
            $ptk_ids = array_filter(array_keys($ptk_map), function ($v) {
                return (int) $v > 0;
            });
            if (!empty($ptk_ids)) {
                $query = $this->db->select('id_ptk, nama_ptk, nip, nuptk')->where_in('id_ptk', array_values($ptk_ids))->get('ptk');
                if ($query && is_object($query)) {
                    $ptk_details = $query->result();
                    foreach ($ptk_details as $detail) {
                        if (isset($ptk_map[$detail->id_ptk])) {
                            if (!empty($detail->nama_ptk)) {
                                $ptk_map[$detail->id_ptk]->nama_ptk = $detail->nama_ptk;
                            }
                            $ptk_map[$detail->id_ptk]->nip = $detail->nip;
                            $ptk_map[$detail->id_ptk]->nuptk = $detail->nuptk;
                        }
                    }
                }
            }
        }

        usort($ptk_map, function ($a, $b) {
            return strcmp($a->nama_ptk, $b->nama_ptk);
        });

        return array_values($ptk_map);
    }

    public function getAgendaHarianPerPtk($id_ptk, $id_tahun_pelajaran = null, $status = null, $bulan = null)
    {
        $this->db->select('ap.*, pm.id_pembelajaran_mapel, pm.judul_agenda, m.nama_mapel, m.mapel_singkat, r.nama_rombel, t.nama_tingkat, l.nama_lembaga_singkat, tp.tahun_pelajaran, tp.semester, ptk.nama_ptk');
        $this->db->from('agenda_pembelajaran ap');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = ap.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');

        $this->db->group_start();
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->or_where('pm.id_ptk_pemilik', (int) $id_ptk);
        $this->db->or_where('pm.id_ptk_takeover', (int) $id_ptk);
        $this->db->group_end();

        if ($id_tahun_pelajaran) {
            $this->db->where('p.id_tahun_pelajaran', (int) $id_tahun_pelajaran);
        } else {
            $this->db->where('tp.status', 'Aktif');
        }

        if (!empty($status)) {
            $this->db->where('ap.status', $status);
        }

        if (!empty($bulan)) {
            $this->db->where('MONTH(ap.tanggal)', (int) $bulan);
        }

        $this->db->order_by('ap.tanggal', 'ASC');
        $this->db->order_by('ap.jam_mulai', 'ASC');
        $this->db->order_by('ap.pertemuan_ke', 'ASC');

        $query = $this->db->get();
        if ($query && is_object($query)) {
            return $query->result();
        }
        return [];
    }

    public function reorderAgendaItems($id_pembelajaran_mapel, $ordered_ids)
    {
        if (empty($ordered_ids) || !is_array($ordered_ids)) return false;

        $existing = $this->getAgendaByMapel($id_pembelajaran_mapel);
        if (empty($existing)) return false;

        // Extract existing fixed schedule slots in sequence
        $schedule_slots = [];
        foreach ($existing as $ex) {
            $schedule_slots[] = [
                'pertemuan_ke' => $ex->pertemuan_ke,
                'tanggal' => $ex->tanggal,
                'hari' => $ex->hari,
                'jam_mulai' => $ex->jam_mulai,
                'jam_selesai' => $ex->jam_selesai,
                'jumlah_jam' => $ex->jumlah_jam
            ];
        }

        $this->db->trans_start();
        $now = date('Y-m-d H:i:s');
        foreach ($ordered_ids as $index => $id_agenda) {
            if (isset($schedule_slots[$index])) {
                $slot = $schedule_slots[$index];
                $this->db->where('id_agenda', (int)$id_agenda);
                $this->db->where('id_pembelajaran_mapel', (int)$id_pembelajaran_mapel);
                $this->db->update($this->agenda_table, [
                    'pertemuan_ke' => $slot['pertemuan_ke'],
                    'tanggal' => $slot['tanggal'],
                    'hari' => $slot['hari'],
                    'jam_mulai' => $slot['jam_mulai'],
                    'jam_selesai' => $slot['jam_selesai'],
                    'jumlah_jam' => $slot['jumlah_jam'],
                    'updated_at' => $now
                ]);
            }
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function moveAgendaItemUp($id_agenda)
    {
        $current = $this->getItemAgenda($id_agenda);
        if (!$current) return false;

        $id_pm = $current->id_pembelajaran_mapel;
        $all = $this->getAgendaByMapel($id_pm);

        $currentIndex = -1;
        foreach ($all as $idx => $row) {
            if ($row->id_agenda == $id_agenda) {
                $currentIndex = $idx;
                break;
            }
        }

        if ($currentIndex <= 0) return false;

        $prev = $all[$currentIndex - 1];

        $content_fields = ['materi', 'kegiatan', 'catatan', 'hambatan', 'pemecahan', 'status', 'link_video', 'slide_drive_id', 'media_files'];
        $current_data = [];
        $prev_data = [];

        foreach ($content_fields as $field) {
            $current_data[$field] = $prev->$field;
            $prev_data[$field] = $current->$field;
        }

        $now = date('Y-m-d H:i:s');
        $current_data['updated_at'] = $now;
        $prev_data['updated_at'] = $now;

        $this->db->trans_start();
        $this->db->where('id_agenda', (int)$current->id_agenda)->update($this->agenda_table, $current_data);
        $this->db->where('id_agenda', (int)$prev->id_agenda)->update($this->agenda_table, $prev_data);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function moveAgendaItemDown($id_agenda)
    {
        $current = $this->getItemAgenda($id_agenda);
        if (!$current) return false;

        $id_pm = $current->id_pembelajaran_mapel;
        $all = $this->getAgendaByMapel($id_pm);

        $currentIndex = -1;
        foreach ($all as $idx => $row) {
            if ($row->id_agenda == $id_agenda) {
                $currentIndex = $idx;
                break;
            }
        }

        if ($currentIndex < 0 || $currentIndex >= count($all) - 1) return false;

        $next = $all[$currentIndex + 1];

        $content_fields = ['materi', 'kegiatan', 'catatan', 'hambatan', 'pemecahan', 'status', 'link_video', 'slide_drive_id', 'media_files'];
        $current_data = [];
        $next_data = [];

        foreach ($content_fields as $field) {
            $current_data[$field] = $next->$field;
            $next_data[$field] = $current->$field;
        }

        $now = date('Y-m-d H:i:s');
        $current_data['updated_at'] = $now;
        $next_data['updated_at'] = $now;

        $this->db->trans_start();
        $this->db->where('id_agenda', (int)$current->id_agenda)->update($this->agenda_table, $current_data);
        $this->db->where('id_agenda', (int)$next->id_agenda)->update($this->agenda_table, $next_data);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
