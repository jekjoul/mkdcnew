<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jurnal_guru_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil data Jurnal Guru dari Agenda Pembelajaran yang SUDAH Terlaksana
     */
    public function getJurnalGuru($filters = [])
    {
        $this->db->select('
            ap.id_agenda,
            ap.id_pembelajaran_mapel,
            ap.tanggal,
            ap.hari,
            ap.pertemuan_ke,
            ap.materi,
            ap.kegiatan,
            ap.hambatan,
            ap.pemecahan,
            ap.catatan,
            ap.status,
            pm.id_ptk,
            ptk.nama_ptk,
            m.id_mapel,
            m.nama_mapel,
            r.id_rombel,
            r.nama_rombel,
            t.nama_tingkat,
            t.tingkat_angka,
            p.id_lembaga,
            l.nama_lembaga,
            l.id_ptk_kepsek,
            tp.id_tahun_pelajaran,
            tp.tahun_pelajaran,
            tp.semester
        ');
        $this->db->from('agenda_pembelajaran ap');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = ap.id_pembelajaran_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');

        // Hanya agenda yang sudah terlaksana
        $this->db->where('ap.status', 'Terlaksana');

        if (!empty($filters['id_ptk'])) {
            $this->db->where('pm.id_ptk', (int)$filters['id_ptk']);
        }
        if (!empty($filters['id_mapel'])) {
            $this->db->where('pm.id_mapel', (int)$filters['id_mapel']);
        }
        if (!empty($filters['id_rombel'])) {
            $this->db->where('p.id_rombel', (int)$filters['id_rombel']);
        }
        if (!empty($filters['bulan'])) {
            $this->db->where('MONTH(ap.tanggal)', (int)$filters['bulan']);
        }
        if (!empty($filters['tahun'])) {
            $this->db->where('YEAR(ap.tanggal)', (int)$filters['tahun']);
        }

        $this->db->order_by('ap.tanggal', 'ASC');
        $this->db->order_by('ap.pertemuan_ke', 'ASC');

        $result = $this->db->get()->result();
        if (empty($result)) return [];

        $agenda_ids = array_column($result, 'id_agenda');
        $attendance_stats = $this->getAttendanceBatchStats($agenda_ids);

        foreach ($result as $row) {
            $row->hambatan_fix  = !empty($row->hambatan)  ? $row->hambatan  : '';
            $row->pemecahan_fix = !empty($row->pemecahan) ? $row->pemecahan : '';

            $stats = isset($attendance_stats[$row->id_agenda])
                ? $attendance_stats[$row->id_agenda]
                : ['h' => 0, 'i' => 0, 's' => 0, 'a' => 0];
            $row->absensi_h = $stats['h'];
            $row->absensi_i = $stats['i'];
            $row->absensi_s = $stats['s'];
            $row->absensi_a = $stats['a'];
        }

        return $result;
    }

    /**
     * Hitung akumulasi statistik presensi siswa per id_agenda
     */
    private function getAttendanceBatchStats($agenda_ids = [])
    {
        if (empty($agenda_ids) || !$this->db->table_exists('presensi_agenda_siswa')) {
            return [];
        }

        $this->db->select("
            id_agenda,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) AS total_h,
            SUM(CASE WHEN status = 'Izin'  THEN 1 ELSE 0 END) AS total_i,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) AS total_s,
            SUM(CASE WHEN status = 'Alpa'  THEN 1 ELSE 0 END) AS total_a
        ");
        $this->db->from('presensi_agenda_siswa');
        $this->db->where_in('id_agenda', $agenda_ids);
        $this->db->group_by('id_agenda');

        $query = $this->db->get();
        if (!$query) return [];

        $stats = [];
        foreach ($query->result() as $r) {
            $stats[$r->id_agenda] = [
                'h' => (int)$r->total_h,
                'i' => (int)$r->total_i,
                's' => (int)$r->total_s,
                'a' => (int)$r->total_a,
            ];
        }

        return $stats;
    }

    /**
     * STEP 0 — Daftar Tahun Pelajaran
     */
    public function getTahunPelajaranList()
    {
        $sql = "
            SELECT id_tahun_pelajaran,
                   CONCAT(tahun_pelajaran, ' — Sem. ', semester) AS label_tahun
            FROM pembelajaran_tahun_pelajaran
            ORDER BY tahun_pelajaran DESC, semester ASC
        ";
        $query = $this->db->query($sql);
        return $query ? $query->result() : [];
    }

    /**
     * STEP 1 — Daftar Guru yang memiliki pembelajaran_mapel (Admin)
     * Difilter berdasarkan tahun pelajaran jika dipilih
     */
    public function getGuruList($id_tahun_pelajaran = null)
    {
        $where = $id_tahun_pelajaran
            ? "AND p.id_tahun_pelajaran = " . (int)$id_tahun_pelajaran
            : "";
        $sql = "
            SELECT DISTINCT ptk.id_ptk, ptk.nama_ptk
            FROM ptk
            INNER JOIN pembelajaran_mapel pm ON pm.id_ptk = ptk.id_ptk
            INNER JOIN pembelajaran p ON p.id_pembelajaran = pm.id_pembelajaran
            WHERE 1=1 {$where}
            ORDER BY ptk.nama_ptk ASC
        ";
        $query = $this->db->query($sql);
        return $query ? $query->result() : [];
    }

    /**
     * STEP 2 — Daftar Mapel berdasarkan id_ptk (dan opsional tahun pelajaran)
     */
    public function getMapelByPtk($id_ptk = null, $id_tahun_pelajaran = null)
    {
        $conditions = [];
        if ($id_ptk)              $conditions[] = "pm.id_ptk = " . (int)$id_ptk;
        if ($id_tahun_pelajaran)  $conditions[] = "p.id_tahun_pelajaran = " . (int)$id_tahun_pelajaran;
        $where = !empty($conditions) ? "AND " . implode(' AND ', $conditions) : "";

        $sql = "
            SELECT DISTINCT m.id_mapel, m.nama_mapel
            FROM pembelajaran_mapel pm
            INNER JOIN mapel m ON m.id_mapel = pm.id_mapel
            INNER JOIN pembelajaran p ON p.id_pembelajaran = pm.id_pembelajaran
            WHERE 1=1 {$where}
            ORDER BY m.nama_mapel ASC
        ";
        $query = $this->db->query($sql);
        return $query ? $query->result() : [];
    }

    /**
     * STEP 3 — Daftar Rombel berdasarkan id_ptk + id_mapel (dan opsional tahun pelajaran)
     * Label: "VII - Aiman" (nama_tingkat - nama_rombel)
     */
    public function getRombelByPtkMapel($id_ptk = null, $id_mapel = null, $id_tahun_pelajaran = null)
    {
        $conditions = [];
        if ($id_ptk)             $conditions[] = "pm.id_ptk = "             . (int)$id_ptk;
        if ($id_mapel)           $conditions[] = "pm.id_mapel = "           . (int)$id_mapel;
        if ($id_tahun_pelajaran) $conditions[] = "p.id_tahun_pelajaran = "  . (int)$id_tahun_pelajaran;
        $where = !empty($conditions) ? "AND " . implode(' AND ', $conditions) : "";

        $sql = "
            SELECT DISTINCT
                r.id_rombel,
                r.nama_rombel,
                t.nama_tingkat,
                CONCAT(IFNULL(t.nama_tingkat,''), ' - ', r.nama_rombel) AS label_rombel
            FROM pembelajaran_mapel pm
            INNER JOIN pembelajaran p   ON p.id_pembelajaran = pm.id_pembelajaran
            INNER JOIN rombel r         ON r.id_rombel = p.id_rombel
            LEFT  JOIN master_tingkat_sekolah t ON t.id_tingkat_sekolah = p.id_tingkat_sekolah
            WHERE 1=1 {$where}
            ORDER BY t.nama_tingkat ASC, r.nama_rombel ASC
        ";
        $query = $this->db->query($sql);
        return $query ? $query->result() : [];
    }

    /**
     * Alias untuk getMapelByPtk
     */
    public function getMapelList($id_ptk = null)
    {
        return $this->getMapelByPtk($id_ptk);
    }

    /**
     * Alias untuk getRombelByPtkMapel
     */
    public function getRombelList($id_ptk = null)
    {
        return $this->getRombelByPtkMapel($id_ptk, null);
    }

}
