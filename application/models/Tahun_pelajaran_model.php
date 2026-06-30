<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tahun_pelajaran_model extends MY_Model
{
    public $table = 'pembelajaran_tahun_pelajaran';
    public $hari_efektif_table = 'pembelajaran_hari_efektif';

    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->ensureHariEfektifTable();
        $this->db->order_by('id_tahun_pelajaran', 'DESC');
        $rows = $this->db->get($this->table)->result();

        foreach ($rows as $row) {
            $row->hari_efektif = $this->getHariEfektifSummary($row->id_tahun_pelajaran);
            $row->periode_hari_efektif = $this->getSemesterRange($row);
        }

        return $rows;
    }

    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id_tahun_pelajaran' => $id])->row();
    }

    public function ensureHariEfektifTable()
    {
        if ($this->db->table_exists($this->hari_efektif_table)) {
            return;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->hari_efektif_table}` (
            `id_hari_efektif` int(11) NOT NULL AUTO_INCREMENT,
            `id_tahun_pelajaran` int(11) NOT NULL,
            `tanggal` date NOT NULL,
            `nama_hari` varchar(20) NOT NULL,
            `status` varchar(20) NOT NULL DEFAULT 'Efektif',
            `keterangan` text NULL,
            `created_at` datetime NULL,
            `updated_at` datetime NULL,
            PRIMARY KEY (`id_hari_efektif`),
            UNIQUE KEY `uniq_tahun_tanggal` (`id_tahun_pelajaran`, `tanggal`),
            KEY `idx_tahun_status` (`id_tahun_pelajaran`, `status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    }

    public function getSemesterRange($tahun_pelajaran)
    {
        preg_match_all('/\d{4}/', (string) $tahun_pelajaran->tahun_pelajaran, $matches);
        $tahun_awal = isset($matches[0][0]) ? (int) $matches[0][0] : (int) date('Y');
        $tahun_akhir = isset($matches[0][1]) ? (int) $matches[0][1] : $tahun_awal + 1;
        $semester = strtolower((string) $tahun_pelajaran->semester);

        if ($semester === 'genap') {
            return [
                'awal' => sprintf('%04d-01-01', $tahun_akhir),
                'akhir' => sprintf('%04d-06-30', $tahun_akhir),
            ];
        }

        return [
            'awal' => sprintf('%04d-07-01', $tahun_awal),
            'akhir' => sprintf('%04d-12-31', $tahun_awal),
        ];
    }

    public function getHariEfektifSummary($id_tahun_pelajaran)
    {
        $this->ensureHariEfektifTable();
        $this->db->select('COUNT(*) AS total');
        $this->db->select("SUM(CASE WHEN status = 'Efektif' THEN 1 ELSE 0 END) AS efektif", false);
        $this->db->select("SUM(CASE WHEN status = 'Libur' THEN 1 ELSE 0 END) AS libur", false);
        $this->db->select("SUM(CASE WHEN status = 'Daring' THEN 1 ELSE 0 END) AS daring", false);
        $this->db->select("SUM(CASE WHEN status = 'Luar Kelas' THEN 1 ELSE 0 END) AS luar_kelas", false);
        $row = $this->db->get_where($this->hari_efektif_table, ['id_tahun_pelajaran' => $id_tahun_pelajaran])->row();

        return (object) [
            'total' => (int) ($row->total ?? 0),
            'efektif' => (int) ($row->efektif ?? 0),
            'libur' => (int) ($row->libur ?? 0),
            'daring' => (int) ($row->daring ?? 0),
            'luar_kelas' => (int) ($row->luar_kelas ?? 0),
        ];
    }

    public function getHariEfektif($id_tahun_pelajaran)
    {
        $this->ensureHariEfektifTable();
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get_where($this->hari_efektif_table, ['id_tahun_pelajaran' => $id_tahun_pelajaran])->result();
    }

    public function generateHariEfektif($tahun_pelajaran, $hari_libur = [])
    {
        $this->ensureHariEfektifTable();

        if ($this->getHariEfektifSummary($tahun_pelajaran->id_tahun_pelajaran)->total > 0) {
            return 0;
        }

        $range = $this->getSemesterRange($tahun_pelajaran);
        $start = new DateTime($range['awal']);
        $end = new DateTime($range['akhir']);
        $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $hari_libur = array_map('intval', (array) $hari_libur);
        $now = date('Y-m-d H:i:s');
        $batch = [];

        foreach ($period as $date) {
            $day_number = (int) $date->format('w');
            $is_libur = in_array($day_number, $hari_libur, true);
            $batch[] = [
                'id_tahun_pelajaran' => $tahun_pelajaran->id_tahun_pelajaran,
                'tanggal' => $date->format('Y-m-d'),
                'nama_hari' => $this->namaHari($day_number),
                'status' => $is_libur ? 'Libur' : 'Efektif',
                'keterangan' => $is_libur ? 'Libur mingguan' : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($batch)) {
            $this->db->insert_batch($this->hari_efektif_table, $batch);
        }

        return count($batch);
    }

    public function updateHariEfektif($status_data = [], $keterangan_data = [])
    {
        $this->ensureHariEfektifTable();
        $allowed_status = ['Efektif', 'Libur', 'Daring', 'Luar Kelas'];
        $updated = 0;

        foreach ((array) $status_data as $id => $status) {
            $id = (int) $id;
            if (!$id || !in_array($status, $allowed_status, true)) {
                continue;
            }

            $this->db->where('id_hari_efektif', $id);
            $this->db->update($this->hari_efektif_table, [
                'status' => $status,
                'keterangan' => isset($keterangan_data[$id]) ? $keterangan_data[$id] : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $updated++;
        }

        return $updated;
    }

    private function namaHari($day_number)
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $hari[(int) $day_number] ?? '';
    }
}
