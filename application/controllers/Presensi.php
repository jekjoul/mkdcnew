<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Presensi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        ifPermissions('menu_presensi');
        $this->load->database();
    }

    // =========================================================================
    // HELPER: Agregasi raw data presensi menjadi status ringkasan per hari
    // Return: array keyed by id_user => [ 'YYYY-MM-DD' => { status, keterangan, jam_dhuha, jam_dzuhur } ]
    // =========================================================================
    private function _aggregasi_presensi($tipe_user, $user_list, $year, $month)
    {
        if (empty($user_list)) return ['by_id' => [], 'by_pin' => []];

        $id_user_list = [];
        $pin_list     = [];

        foreach ($user_list as $s) {
            $user_id = 0;
            if ($tipe_user === 'ptk') {
                if (isset($s->id_ptk)) $user_id = intval($s->id_ptk);
                if (!empty($s->niy))   $pin_list[] = "'" . $this->db->escape_str($s->niy) . "'";
            } else {
                if (isset($s->id_siswa)) $user_id = intval($s->id_siswa);
                if (!empty($s->nipd))    $pin_list[] = "'" . $this->db->escape_str($s->nipd) . "'";
            }

            if ($user_id > 0) {
                $id_user_list[] = $user_id;
            }
        }

        $id_user_list = array_unique($id_user_list);
        $pin_list     = array_unique($pin_list);

        $where_cond = [];
        $where_cond[] = "tipe_user = " . $this->db->escape($tipe_user);
        if (!empty($pin_list)) {
            $where_cond[] = "pin IN (" . implode(',', $pin_list) . ")";
        } else {
            $where_cond[] = "1=0";
        }

        $where_sql = "(" . implode(" AND ", $where_cond) . ")";

        if ($tipe_user === 'ptk') {
            // Ketentuan Presensi Guru: Cukup 1 kali atau lebih tap dalam 1 hari = 'Hadir' (keterangan NULL)
            $sql = "
                SELECT
                    id_user,
                    pin,
                    tanggal,
                    MIN(jam_scan) AS jam_dhuha,
                    MAX(jam_scan) AS jam_dzuhur,
                    'Hadir' AS status,
                    NULL AS keterangan,
                    COUNT(*) AS total_tap
                FROM presensi_harian
                WHERE {$where_sql}
                  AND YEAR(tanggal)  = ?
                  AND MONTH(tanggal) = ?
                GROUP BY id_user, pin, tanggal
            ";
            $result = $this->db->query($sql, [$year, $month])->result();
        } else {
            // Ketentuan Presensi Siswa: Dhuha vs Dzuhur
            $sql = "
                SELECT
                    id_user,
                    pin,
                    tanggal,
                    MIN(CASE WHEN sesi = 'dhuha'  THEN jam_scan END) AS jam_dhuha,
                    MIN(CASE WHEN sesi = 'dzuhur' THEN jam_scan END) AS jam_dzuhur,
                    CASE
                        WHEN MAX(CASE WHEN sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                         AND MAX(CASE WHEN sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1
                            THEN 'Hadir'
                        WHEN MAX(CASE WHEN sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                            THEN 'Hadir'
                        WHEN MAX(CASE WHEN sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1
                            THEN 'Hadir'
                        ELSE 'Hadir'
                    END AS status,
                    CASE
                        WHEN MAX(CASE WHEN sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                         AND MAX(CASE WHEN sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1
                            THEN NULL
                        WHEN MAX(CASE WHEN sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                            THEN 'Hanya Dhuha'
                        WHEN MAX(CASE WHEN sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1
                            THEN 'Hanya Dzuhur'
                        ELSE NULL
                    END AS keterangan,
                    COUNT(*) AS total_tap
                FROM presensi_harian
                WHERE {$where_sql}
                  AND YEAR(tanggal)  = ?
                  AND MONTH(tanggal) = ?
                GROUP BY id_user, pin, tanggal
            ";
            $result = $this->db->query($sql, [$year, $month])->result();
        }

        $matrix_by_id  = [];
        $matrix_by_pin = [];

        foreach ($result as $row) {
            if ($row->id_user > 0) {
                $matrix_by_id[$row->id_user][$row->tanggal] = $row;
            }
            if (!empty($row->pin)) {
                $pin_val = (string)$row->pin;
                $matrix_by_pin[$pin_val][$row->tanggal] = $row;
                $matrix_by_pin[ltrim($pin_val, '0')][$row->tanggal] = $row;
                $matrix_by_pin[(string)intval($pin_val)][$row->tanggal] = $row;
            }
        }

        // Ambil dan gabungkan data dari presensi_override agar status manual (Sakit, Izin, Alfa) ter-render dengan benar
        if ($this->db->table_exists('presensi_override')) {
            $this->db->where('tipe_user', $tipe_user);
            if (!empty($id_user_list)) {
                $this->db->where_in('id_user', $id_user_list);
            }
            $this->db->where('YEAR(tanggal)', $year);
            $this->db->where('MONTH(tanggal)', $month);
            $overrides = $this->db->get('presensi_override')->result();
            
            foreach ($overrides as $ov) {
                if (isset($matrix_by_id[$ov->id_user][$ov->tanggal])) {
                    $matrix_by_id[$ov->id_user][$ov->tanggal]->status = $ov->status;
                    $matrix_by_id[$ov->id_user][$ov->tanggal]->keterangan = $ov->keterangan;
                } else {
                    $row = new stdClass();
                    $row->id_user = $ov->id_user;
                    $row->pin = $ov->pin;
                    $row->tanggal = $ov->tanggal;
                    $row->status = $ov->status;
                    $row->keterangan = $ov->keterangan;
                    $row->jam_dhuha = null;
                    $row->jam_dzuhur = null;
                    $matrix_by_id[$ov->id_user][$ov->tanggal] = $row;
                }
                
                if (!empty($ov->pin)) {
                    $pin_val = (string)$ov->pin;
                    
                    $pins_to_update = [
                        $pin_val,
                        ltrim($pin_val, '0') === '' ? '0' : ltrim($pin_val, '0'),
                        (string)intval($pin_val)
                    ];
                    
                    foreach ($pins_to_update as $p_val) {
                        if (isset($matrix_by_pin[$p_val][$ov->tanggal])) {
                            $matrix_by_pin[$p_val][$ov->tanggal]->status = $ov->status;
                            $matrix_by_pin[$p_val][$ov->tanggal]->keterangan = $ov->keterangan;
                        } else {
                            $row = new stdClass();
                            $row->id_user = $ov->id_user;
                            $row->pin = $ov->pin;
                            $row->tanggal = $ov->tanggal;
                            $row->status = $ov->status;
                            $row->keterangan = $ov->keterangan;
                            $row->jam_dhuha = null;
                            $row->jam_dzuhur = null;
                            $matrix_by_pin[$p_val][$ov->tanggal] = $row;
                        }
                    }
                }
            }
        }

        return [
            'by_id'  => $matrix_by_id,
            'by_pin' => $matrix_by_pin
        ];
    }

    // =========================================================================
    // HELPER: Ambil presensi harian hari ini (join aggregasi)
    // =========================================================================
    private function _presensi_hari_ini($tipe_user, $tanggal, $join_table, $join_key, $join_name_col, $join_extra_col = null)
    {
        $extra_select = $join_extra_col ? ", u.{$join_extra_col}" : '';

        if ($tipe_user === 'ptk') {
            $join_cond = "p.pin IS NOT NULL AND p.pin != '' AND (u.niy = p.pin OR ltrim(u.niy, '0') = ltrim(p.pin, '0'))";
        } else {
            $join_cond = "p.pin IS NOT NULL AND p.pin != '' AND (u.nipd = p.pin OR ltrim(u.nipd, '0') = ltrim(p.pin, '0'))";
        }

        $sql = "
            SELECT
                p.id_user,
                p.pin,
                p.tanggal,
                u.{$join_name_col}{$extra_select},
                MIN(CASE WHEN p.sesi = 'dhuha'  THEN p.jam_scan END) AS jam_dhuha,
                MIN(CASE WHEN p.sesi = 'dzuhur' THEN p.jam_scan END) AS jam_dzuhur,
                COUNT(p.id_presensi) AS total_tap,
                CASE
                    WHEN MAX(CASE WHEN p.sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                     AND MAX(CASE WHEN p.sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1 THEN 'Hadir'
                    WHEN MAX(CASE WHEN p.sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1   THEN 'Hadir'
                    WHEN MAX(CASE WHEN p.sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1   THEN 'Hadir'
                    ELSE 'Hadir'
                END AS status,
                CASE
                    WHEN MAX(CASE WHEN p.sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1
                     AND MAX(CASE WHEN p.sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1 THEN NULL
                    WHEN MAX(CASE WHEN p.sesi = 'dhuha'  THEN 1 ELSE 0 END) = 1   THEN 'Hanya Dhuha'
                    WHEN MAX(CASE WHEN p.sesi = 'dzuhur' THEN 1 ELSE 0 END) = 1   THEN 'Hanya Dzuhur'
                    ELSE NULL
                END AS keterangan
            FROM presensi_harian p
            JOIN {$join_table} u ON ({$join_cond})
            WHERE p.tanggal = ?
              AND p.tipe_user = ?
            GROUP BY u.{$join_key}, p.tanggal
            ORDER BY jam_dhuha ASC
        ";

        return $this->db->query($sql, [$tanggal, $tipe_user])->result();
    }

    // =========================================================================
    // HELPER: Ambil daftar bulan dari absensi_tanggal + pastikan bulan berjalan
    // selalu tampil meskipun belum ada data di absensi_tanggal
    // =========================================================================
    private function _get_bulan_list()
    {
        $rows = [];

        // Hanya query jika tabel absensi_tanggal sudah ada
        if ($this->db->table_exists('absensi_tanggal')) {
            $query = $this->db
                ->select("DISTINCT(DATE_FORMAT(tanggal_absensi, '%Y-%m')) as bulan_tahun")
                ->from('absensi_tanggal')
                ->order_by('bulan_tahun', 'DESC')
                ->get();
            if ($query !== false) {
                $rows = $query->result();
            }
        }

        $bulan_berjalan = date('Y-m'); // contoh: '2026-07'

        // Cek apakah bulan berjalan sudah ada di list
        $sudah_ada = false;
        foreach ($rows as $b) {
            if ($b->bulan_tahun === $bulan_berjalan) {
                $sudah_ada = true;
                break;
            }
        }

        // Jika belum ada, sisipkan di urutan pertama (paling atas)
        if (!$sudah_ada) {
            $obj = new stdClass();
            $obj->bulan_tahun = $bulan_berjalan;
            array_unshift($rows, $obj);
        }

        return $rows;
    }

    // =========================================================================
    // HELPER: Ambil daftar bulan berdasarkan Tahun Pelajaran & Semester Aktif
    // Semester 1: Juli [tahun_awal] s.d. Desember [tahun_awal]
    // Semester 2: Januari [tahun_akhir] s.d. Juni [tahun_akhir]
    // =========================================================================
    private function _get_bulan_list_aktif()
    {
        $ta = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif'])->row();
        if (!$ta) {
            $ta = $this->db->order_by('id_tahun_pelajaran', 'DESC')->get('pembelajaran_tahun_pelajaran')->row();
        }

        if (!$ta) {
            $curr_y = date('Y');
            $ta = (object)[
                'tahun_pelajaran' => $curr_y . '/' . ($curr_y + 1),
                'semester'        => 'Semester 1'
            ];
        }

        preg_match_all('/\d{4}/', (string)$ta->tahun_pelajaran, $matches);
        $tahun_awal  = isset($matches[0][0]) ? (int)$matches[0][0] : (int)date('Y');
        $tahun_akhir = isset($matches[0][1]) ? (int)$matches[0][1] : $tahun_awal + 1;

        $sem_str = strtolower((string)$ta->semester);
        $is_semester_2 = (strpos($sem_str, '2') !== false || strpos($sem_str, 'genap') !== false);

        $b_names = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $bulan_list = [];

        if ($is_semester_2) {
            // Semester 2: Januari - Juni (Tahun Kedua)
            $months = ['01', '02', '03', '04', '05', '06'];
            foreach ($months as $m) {
                $ym = sprintf('%04d-%s', $tahun_akhir, $m);
                $bulan_list[] = (object)[
                    'bulan_tahun' => $ym,
                    'nama_bulan'  => $b_names[$m] . ' ' . $tahun_akhir
                ];
            }
        } else {
            // Semester 1: Juli - Desember (Tahun Awal)
            $months = ['07', '08', '09', '10', '11', '12'];
            foreach ($months as $m) {
                $ym = sprintf('%04d-%s', $tahun_awal, $m);
                $bulan_list[] = (object)[
                    'bulan_tahun' => $ym,
                    'nama_bulan'  => $b_names[$m] . ' ' . $tahun_awal
                ];
            }
        }

        return [
            'bulan_list' => $bulan_list,
            'ta_active'  => $ta
        ];
    }

    // =========================================================================
    // Halaman Rekap Presensi Siswa (Grid Rombel Bulanan)
    // =========================================================================
    public function siswa()
    {
        $this->page_data['page']->title       = 'Presensi Siswa';
        $this->page_data['page']->titleUrl    = 'presensi/siswa';
        $this->page_data['page']->subtitle    = 'Rekap Presensi Siswa';
        $this->page_data['page']->subtitleUrl = 'presensi/siswa';
        $this->page_data['page']->icon        = 'solar:users-group-two-rounded-linear';

        // --- Rekap Bulanan Grid ---
        $rombel_list = $this->db
            ->select('DISTINCT(rombel) as rombel')
            ->from('siswa')
            ->where('rombel !=', '')
            ->order_by('rombel', 'ASC')
            ->get()->result();
        $this->page_data['rombel_list'] = $rombel_list;

        // Ambil daftar bulan berdasarkan Tahun Pelajaran & Semester Aktif
        $ta_data = $this->_get_bulan_list_aktif();
        $bulan_list = $ta_data['bulan_list'];
        $this->page_data['bulan_list'] = $bulan_list;
        $this->page_data['ta_active']  = $ta_data['ta_active'];

        $selected_rombel = $this->input->get('rombel');
        $selected_month  = $this->input->get('bulan_tahun'); // format: Y-m

        // Default ke rombel pertama jika belum ada yang dipilih
        if (empty($selected_rombel) && !empty($rombel_list)) {
            $selected_rombel = $rombel_list[0]->rombel;
        }

        // Default ke bulan berjalan jika ada di list, atau bulan pertama dari list
        if (empty($selected_month) && !empty($bulan_list)) {
            $current_ym = date('Y-m');
            $found_curr = false;
            foreach ($bulan_list as $bl) {
                if ($bl->bulan_tahun === $current_ym) {
                    $selected_month = $current_ym;
                    $found_curr = true;
                    break;
                }
            }
            if (!$found_curr) {
                $selected_month = $bulan_list[0]->bulan_tahun;
            }
        }

        if ($selected_rombel && $selected_month) {
            $year  = substr($selected_month, 0, 4);
            $month = substr($selected_month, 5, 2);

            // Daftar tanggal efektif bulan ini
            $tanggal_list = [];

            // 1. Cek dari pembelajaran_hari_efektif
            if ($this->db->table_exists('pembelajaran_hari_efektif')) {
                $this->db->where('YEAR(tanggal)', $year);
                $this->db->where('MONTH(tanggal)', $month);
                $this->db->order_by('tanggal', 'ASC');
                $q = $this->db->get('pembelajaran_hari_efektif');
                if ($q && $q->num_rows() > 0) {
                    foreach ($q->result() as $r) {
                        $obj = new stdClass();
                        $obj->tanggal_absensi = $r->tanggal;
                        $obj->status          = $r->status;
                        $obj->keterangan      = $r->keterangan;
                        $tanggal_list[] = $obj;
                    }
                }
            }

            // 2. Cek dari absensi_tanggal jika masih kosong
            if (empty($tanggal_list) && $this->db->table_exists('absensi_tanggal')) {
                $this->db->where('YEAR(tanggal_absensi)', $year);
                $this->db->where('MONTH(tanggal_absensi)', $month);
                $this->db->order_by('tanggal_absensi', 'ASC');
                $q = $this->db->get('absensi_tanggal');
                if ($q && $q->num_rows() > 0) {
                    $tanggal_list = $q->result();
                }
            }

            // 3. Fallback jika kedua tabel di atas belum di-generate untuk bulan terpilih:
            if (empty($tanggal_list)) {
                $total_days = date('t', strtotime("{$year}-{$month}-01"));
                for ($d = 1; $d <= $total_days; $d++) {
                    $date_str = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $day_of_week = date('N', strtotime($date_str)); // 7 = Minggu
                    
                    $obj = new stdClass();
                    $obj->tanggal_absensi = $date_str;
                    $obj->status          = ($day_of_week == 7) ? 'Libur' : 'Efektif';
                    $obj->keterangan      = ($day_of_week == 7) ? 'Hari Minggu' : '';
                    $tanggal_list[] = $obj;
                }
            }
            $this->page_data['tanggal_list'] = $tanggal_list;

            // Daftar siswa di rombel terpilih
            $show_menginduk = $this->input->get('show_menginduk') == '1';
            $menginduk_ids  = [];
            if (!$show_menginduk && $this->db->table_exists('kelas_jauh_siswa')) {
                $q_kj = $this->db->select('id_siswa')->get('kelas_jauh_siswa');
                if ($q_kj && $q_kj->num_rows() > 0) {
                    $menginduk_ids = array_column($q_kj->result_array(), 'id_siswa');
                }
            }

            $this->db->where('rombel', $selected_rombel);
            if (!empty($menginduk_ids)) {
                $this->db->where_not_in('id_siswa', $menginduk_ids);
            }

            $this->db->order_by('nama_siswa', 'ASC');
            $siswa_list = $this->db->get('siswa')->result();
            $this->page_data['siswa_list']     = $siswa_list;
            $this->page_data['show_menginduk'] = $show_menginduk;

            // Agregasi presensi raw → matrix
            $agg_res = $this->_aggregasi_presensi('siswa', $siswa_list, $year, $month);
            $this->page_data['presensi_matrix']        = $agg_res['by_id'];
            $this->page_data['presensi_matrix_by_pin'] = $agg_res['by_pin'];

            $this->page_data['selected_rombel'] = $selected_rombel;
            $this->page_data['selected_month']  = $selected_month;
        }

        $this->load->view('presensi/v_siswa', $this->page_data);
    }

    // =========================================================================
    // Halaman Rekap Presensi PTK (Grid Bulanan)
    // =========================================================================
    public function guru()
    {
        $this->page_data['page']->title       = 'Presensi PTK';
        $this->page_data['page']->titleUrl    = 'presensi/guru';
        $this->page_data['page']->subtitle    = 'Rekap Presensi PTK (Guru & Staf)';
        $this->page_data['page']->subtitleUrl = 'presensi/guru';
        $this->page_data['page']->icon        = 'icon-park-outline:user-business';

        // Ambil daftar bulan berdasarkan Tahun Pelajaran & Semester Aktif
        $ta_data = $this->_get_bulan_list_aktif();
        $bulan_list = $ta_data['bulan_list'];
        $this->page_data['bulan_list'] = $bulan_list;
        $this->page_data['ta_active']  = $ta_data['ta_active'];

        $selected_month = $this->input->get('bulan_tahun');

        // Default ke bulan berjalan jika ada di list, atau bulan pertama dari list
        if (empty($selected_month) && !empty($bulan_list)) {
            $current_ym = date('Y-m');
            $found_curr = false;
            foreach ($bulan_list as $bl) {
                if ($bl->bulan_tahun === $current_ym) {
                    $selected_month = $current_ym;
                    $found_curr = true;
                    break;
                }
            }
            if (!$found_curr) {
                $selected_month = $bulan_list[0]->bulan_tahun;
            }
        }

        if ($selected_month) {
            $year  = substr($selected_month, 0, 4);
            $month = substr($selected_month, 5, 2);

            // Daftar tanggal efektif bulan ini
            $tanggal_list = [];

            if ($this->db->table_exists('pembelajaran_hari_efektif')) {
                $this->db->where('YEAR(tanggal)', $year);
                $this->db->where('MONTH(tanggal)', $month);
                $this->db->order_by('tanggal', 'ASC');
                $q = $this->db->get('pembelajaran_hari_efektif');
                if ($q && $q->num_rows() > 0) {
                    foreach ($q->result() as $r) {
                        $obj = new stdClass();
                        $obj->tanggal_absensi = $r->tanggal;
                        $obj->status          = $r->status;
                        $obj->keterangan      = $r->keterangan;
                        $tanggal_list[] = $obj;
                    }
                }
            }

            if (empty($tanggal_list) && $this->db->table_exists('absensi_tanggal')) {
                $this->db->where('YEAR(tanggal_absensi)', $year);
                $this->db->where('MONTH(tanggal_absensi)', $month);
                $this->db->order_by('tanggal_absensi', 'ASC');
                $q = $this->db->get('absensi_tanggal');
                if ($q && $q->num_rows() > 0) {
                    $tanggal_list = $q->result();
                }
            }

            if (empty($tanggal_list)) {
                $total_days = date('t', strtotime("{$year}-{$month}-01"));
                for ($d = 1; $d <= $total_days; $d++) {
                    $date_str = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $day_of_week = date('N', strtotime($date_str));
                    
                    $obj = new stdClass();
                    $obj->tanggal_absensi = $date_str;
                    $obj->status          = ($day_of_week == 7) ? 'Libur' : 'Efektif';
                    $obj->keterangan      = ($day_of_week == 7) ? 'Hari Minggu' : '';
                    $tanggal_list[] = $obj;
                }
            }
            $this->page_data['tanggal_list'] = $tanggal_list;

            $this->db->where('status_keaktifan', 'Aktif');
            $this->db->order_by('nama_ptk', 'ASC');
            $guru_list = $this->db->get('ptk')->result();
            $this->page_data['guru_list'] = $guru_list;

            $agg_res = $this->_aggregasi_presensi('ptk', $guru_list, $year, $month);
            $this->page_data['presensi_matrix']        = $agg_res['by_id'];
            $this->page_data['presensi_matrix_by_pin'] = $agg_res['by_pin'];

            $this->page_data['selected_month'] = $selected_month;
        }

        $this->load->view('presensi/v_guru', $this->page_data);
    }

    // =========================================================================
    // Simpan / Sunting Presensi Manual (Override oleh admin)
    // Menyimpan sesi 'manual' dengan status Sakit / Izin / Alfa
    // =========================================================================
    public function simpan_manual()
    {
        ifPermissions('menu_presensi');

        $tipe_user  = $this->input->post('tipe_user');
        $id_user    = $this->input->post('id_user');
        $tanggal    = $this->input->post('tanggal');
        $status     = $this->input->post('status');
        $keterangan = $this->input->post('keterangan');

        // Cari pin user (Gunakan NIPD untuk siswa dan NIY untuk PTK)
        $pin = '';
        if ($tipe_user === 'siswa') {
            $user_obj = $this->db->get_where('siswa', ['id_siswa' => $id_user])->row();
            $pin = $user_obj ? $user_obj->nipd : '';
        } else {
            $user_obj = $this->db->get_where('ptk', ['id_ptk' => $id_user])->row();
            $pin = $user_obj ? $user_obj->niy : '';
        }

        if ($status === 'Hadir') {
            // Jika admin menandai Hadir manual: hapus semua raw tap di tanggal itu,
            // lalu insert 1 baris sesi 'dhuha' dan 1 baris 'dzuhur' jam default
            $this->db->delete('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'tanggal'   => $tanggal
            ]);
            $this->db->insert('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'pin'       => $pin,
                'tanggal'   => $tanggal,
                'jam_scan'  => '07:00:00',
                'sesi'      => 'dhuha'
            ]);
            $this->db->insert('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'pin'       => $pin,
                'tanggal'   => $tanggal,
                'jam_scan'  => '12:00:00',
                'sesi'      => 'dzuhur'
            ]);
        } else {
            // Status Sakit / Izin / Alfa: hapus semua tap asli,
            // simpan 1 baris sesi 'other' sebagai penanda override manual
            $this->db->delete('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'tanggal'   => $tanggal
            ]);
            $this->db->insert('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'pin'       => $pin,
                'tanggal'   => $tanggal,
                'jam_scan'  => '00:00:00',
                'sesi'      => 'other',
                // Catatan: kolom keterangan & status tidak ada di tabel raw,
                // Status manual disimpan di tabel terpisah (lihat presensi_override)
            ]);
        }

        // Simpan override ke tabel presensi_override
        $this->_simpan_override($tipe_user, $id_user, $tanggal, $status, $keterangan, $pin);

        $this->activity_model->add(logged('name') . ' Mengubah presensi manual (' . strtoupper($tipe_user) . ' ID #' . $id_user . ') Tanggal: ' . $tanggal . ' Status: ' . $status, logged('id'));

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Presensi manual berhasil diperbarui.');

        $rombel      = $this->input->post('rombel');
        $bulan_tahun = $this->input->post('bulan_tahun');

        $redirect_url = 'presensi/' . ($tipe_user === 'ptk' ? 'guru' : 'siswa');
        $params = [];
        if (!empty($rombel))      $params['rombel'] = $rombel;
        if (!empty($bulan_tahun)) $params['bulan_tahun'] = $bulan_tahun;
        if (!empty($params))      $redirect_url .= '?' . http_build_query($params);

        redirect($redirect_url);
    }

    // =========================================================================
    // Hapus Presensi Manual (Hapus Log Kehadiran per Hari/Tanggal)
    // =========================================================================
    public function hapus_manual()
    {
        ifPermissions('menu_presensi');

        $tipe_user   = $this->input->post('tipe_user');
        $id_user     = intval($this->input->post('id_user'));
        $tanggal     = $this->input->post('tanggal');
        $rombel      = $this->input->post('rombel');
        $bulan_tahun = $this->input->post('bulan_tahun');

        if (empty($tanggal)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Tanggal presensi tidak valid.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'presensi/' . ($tipe_user === 'ptk' ? 'guru' : 'siswa'));
            return;
        }

        // Ambil data PIN/NIY/NIPD user untuk pembersihan presensi_harian secara menyeluruh
        $pin_list = [];
        if ($tipe_user === 'ptk') {
            $ptk = $this->db->get_where('ptk', ['id_ptk' => $id_user])->row();
            if ($ptk) {
                if (!empty($ptk->niy))              $pin_list[] = $ptk->niy;
                if (!empty($ptk->pin_fingerprint)) $pin_list[] = $ptk->pin_fingerprint;
                if (!empty($ptk->nik))              $pin_list[] = $ptk->nik;
            }
        } else {
            $siswa = $this->db->get_where('siswa', ['id_siswa' => $id_user])->row();
            if ($siswa) {
                if (!empty($siswa->nipd))            $pin_list[] = $siswa->nipd;
                if (!empty($siswa->pin_fingerprint)) $pin_list[] = $siswa->pin_fingerprint;
                if (!empty($siswa->nisn))            $pin_list[] = $siswa->nisn;
            }
        }

        // Hapus berdasarkan id_user ATAU pin
        $this->db->group_start();
        $this->db->where('id_user', $id_user);
        if (!empty($pin_list)) {
            $this->db->or_where_in('pin', $pin_list);
        }
        $this->db->group_end();
        $this->db->where('tanggal', $tanggal);
        $this->db->delete('presensi_harian');

        // Hapus juga override manualnya
        $this->db->where(['tipe_user' => $tipe_user, 'id_user' => $id_user, 'tanggal' => $tanggal]);
        $this->db->delete('presensi_override');

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berhasil menghapus data presensi tanggal ' . date('d-m-Y', strtotime($tanggal)) . '.');

        $redirect_url = 'presensi/' . ($tipe_user === 'ptk' ? 'guru' : 'siswa');
        $params = [];
        if (!empty($rombel))      $params['rombel'] = $rombel;
        if (!empty($bulan_tahun)) $params['bulan_tahun'] = $bulan_tahun;
        if (!empty($params))      $redirect_url .= '?' . http_build_query($params);

        redirect($redirect_url);
    }

    /**
     * Simpan/Update override presensi manual ke tabel presensi_override.
     * Tabel ini dibuat otomatis jika belum ada.
     */
    private function _simpan_override($tipe_user, $id_user, $tanggal, $status, $keterangan, $pin)
    {
        // Buat tabel jika belum ada
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

        $existing = $this->db->get_where('presensi_override', [
            'tipe_user' => $tipe_user,
            'id_user'   => $id_user,
            'tanggal'   => $tanggal
        ])->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            $this->db->update('presensi_override', [
                'status'     => $status,
                'keterangan' => $keterangan
            ]);
        } else {
            $this->db->insert('presensi_override', [
                'tipe_user'  => $tipe_user,
                'id_user'    => $id_user,
                'pin'        => $pin,
                'tanggal'    => $tanggal,
                'status'     => $status,
                'keterangan' => $keterangan
            ]);
        }
    }

    /**
     * Menu Tampilan Data User & Sidik Jari Mesin Fingerprint
     */
    public function user_fingerprint()
    {
        $this->page_data['page']->title       = 'Data User Mesin & Sidik Jari';
        $this->page_data['page']->titleUrl    = 'presensi/user_fingerprint';
        $this->page_data['page']->subtitle    = 'Daftar Pengguna Mesin & Template Sidik Jari Tersimpan';
        $this->page_data['page']->subtitleUrl = 'presensi/user_fingerprint';
        $this->page_data['page']->icon        = 'solar:fingerprint-linear';

        $this->load->model('Fingerprint_bridge_model', 'bridge_model');
        $this->bridge_model->ensureTables();

        // 1. Data User & Template dari Mesin
        $machine_users = $this->db->order_by('id', 'DESC')->get('presensi_machine_users')->result();
        $machine_users_by_pin = [];
        foreach ($machine_users as $m) {
            $raw_m_pin   = trim((string)$m->pin);
            $clean_m_pin = ltrim($raw_m_pin, '0');
            if ($clean_m_pin === '') $clean_m_pin = '0';

            $machine_users_by_pin[$raw_m_pin]   = $m;
            $machine_users_by_pin[$clean_m_pin] = $m;
        }

        $templates = $this->db->get('presensi_machine_templates')->result();
        $templates_by_pin = [];
        foreach ($templates as $t) {
            $raw_t_pin   = trim((string)$t->pin);
            $clean_t_pin = ltrim($raw_t_pin, '0');
            if ($clean_t_pin === '') $clean_t_pin = '0';

            $templates_by_pin[$raw_t_pin][]   = $t;
            if ($raw_t_pin !== $clean_t_pin) {
                $templates_by_pin[$clean_t_pin][] = $t;
            }
        }

        // 2. Data Siswa Aktif (PIN = NIPD atau pin_fingerprint)
        $all_siswa = $this->db->select('s.id_siswa, s.nipd, s.pin_fingerprint, s.nama_siswa, s.rombel as raw_rombel, r.nama_rombel, t.nama_tingkat')
                              ->from('siswa s')
                              ->join('pembelajaran_siswa ps', 's.id_siswa = ps.peserta_didik_id AND ps.id_pembelajaran IN (
                                  SELECT p_active.id_pembelajaran 
                                  FROM pembelajaran p_active 
                                  JOIN pembelajaran_tahun_pelajaran tp_active ON tp_active.id_tahun_pelajaran = p_active.id_tahun_pelajaran
                                  WHERE tp_active.status = "Aktif"
                              )', 'left', FALSE)
                              ->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran', 'left')
                              ->join('rombel r', 'r.id_rombel = p.id_rombel', 'left')
                              ->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left')
                              ->where('s.status_keaktifan', 'Aktif')
                              ->group_by('s.id_siswa')
                              ->order_by('s.nama_siswa', 'ASC')
                              ->get()->result();

        // 3. Data PTK / Guru Aktif (PIN = NIY atau pin_fingerprint)
        $all_ptk = $this->db->select('id_ptk, niy, pin_fingerprint, nama_ptk')
                            ->from('ptk')
                            ->where('status_keaktifan', 'Aktif')
                            ->order_by('nama_ptk', 'ASC')
                            ->get()->result();

        $merged_users   = [];
        $processed_pins = [];

        // Process Active Siswa
        foreach ($all_siswa as $s) {
            $pin_raw   = !empty($s->pin_fingerprint) ? trim((string)$s->pin_fingerprint) : trim((string)$s->nipd);
            if (empty($pin_raw)) continue;

            $pin_clean = ltrim($pin_raw, '0');
            if ($pin_clean === '') $pin_clean = '0';

            $m = $machine_users_by_pin[$pin_raw] ?? $machine_users_by_pin[$pin_clean] ?? null;
            $processed_pins[$pin_raw]   = true;
            $processed_pins[$pin_clean] = true;

            // Formulasi Nama Rombel & Tingkat
            $rombel_tingkat = '-';
            if (!empty($s->nama_rombel) && !empty($s->nama_tingkat)) {
                if (stripos($s->nama_rombel, $s->nama_tingkat) !== false) {
                    $rombel_tingkat = $s->nama_rombel;
                } else {
                    $rombel_tingkat = $s->nama_tingkat . ' - ' . $s->nama_rombel;
                }
            } elseif (!empty($s->nama_rombel)) {
                $rombel_tingkat = $s->nama_rombel;
            } elseif (!empty($s->raw_rombel)) {
                $rombel_tingkat = $s->raw_rombel;
            }

            $merged_users[] = (object)[
                'pin'               => $pin_raw,
                'nama'              => $s->nama_siswa,
                'nama_mesin'        => $m ? $m->nama : null,
                'tipe_user'         => 'Siswa',
                'rombel_tingkat'    => $rombel_tingkat,
                'ref_id'            => $s->id_siswa,
                'in_server'         => true,
                'in_machine'        => ($m !== null),
                'status_registrasi' => ($m !== null) ? 'Terdaftar di Server & Mesin' : 'Belum Teregistrasi ke Mesin',
                'password'          => $m ? $m->password : '',
                'rfid'              => $m ? $m->rfid : '',
                'privilege'         => $m ? $m->privilege : 0,
                'updated_at'        => $m ? $m->updated_at : null
            ];
        }

        // Process Active PTK
        foreach ($all_ptk as $p) {
            $pin_raw   = !empty($p->pin_fingerprint) ? trim((string)$p->pin_fingerprint) : trim((string)$p->niy);
            if (empty($pin_raw)) continue;

            $pin_clean = ltrim($pin_raw, '0');
            if ($pin_clean === '') $pin_clean = '0';

            $m = $machine_users_by_pin[$pin_raw] ?? $machine_users_by_pin[$pin_clean] ?? null;
            $processed_pins[$pin_raw]   = true;
            $processed_pins[$pin_clean] = true;

            $merged_users[] = (object)[
                'pin'               => $pin_raw,
                'nama'              => $p->nama_ptk,
                'nama_mesin'        => $m ? $m->nama : null,
                'tipe_user'         => 'PTK / Guru',
                'rombel_tingkat'    => 'PTK',
                'ref_id'            => $p->id_ptk,
                'in_server'         => true,
                'in_machine'        => ($m !== null),
                'status_registrasi' => ($m !== null) ? 'Terdaftar di Server & Mesin' : 'Belum Teregistrasi ke Mesin',
                'password'          => $m ? $m->password : '',
                'rfid'              => $m ? $m->rfid : '',
                'privilege'         => $m ? $m->privilege : 0,
                'updated_at'        => $m ? $m->updated_at : null
            ];
        }

        // Process Machine Users Saja (Tidak Terdaftar di Siswa/PTK Aktif)
        foreach ($machine_users as $m) {
            $pin_raw   = trim((string)$m->pin);
            $pin_clean = ltrim($pin_raw, '0');
            if ($pin_clean === '') $pin_clean = '0';

            if (empty($pin_raw) || isset($processed_pins[$pin_raw]) || isset($processed_pins[$pin_clean])) continue;

            $merged_users[] = (object)[
                'pin'               => $m->pin,
                'nama'              => $m->nama,
                'nama_mesin'        => $m->nama,
                'tipe_user'         => 'User Mesin Saja',
                'rombel_tingkat'    => 'Mesin Saja',
                'ref_id'            => null,
                'in_server'         => false,
                'in_machine'        => true,
                'status_registrasi' => 'Hanya Ada di Mesin',
                'password'          => $m->password,
                'rfid'              => $m->rfid,
                'privilege'         => $m->privilege,
                'updated_at'        => $m->updated_at
            ];
        }

        $this->page_data['users']            = $merged_users;
        $this->page_data['machine_users']    = $machine_users;
        $this->page_data['templates_by_pin'] = $templates_by_pin;

        $this->load->view('presensi/v_user_fingerprint', $this->page_data);
    }

    /**
     * Aksi Masal: Mengisi otomatis kolom presensi kosong siswa dalam rentang tanggal tertentu
     */
    public function override_masal()
    {
        ifPermissions('menu_presensi');
        postAllowed();

        $rombel       = $this->input->post('rombel');
        $start_date   = $this->input->post('start_date');
        $end_date     = $this->input->post('end_date');
        $status       = $this->input->post('status'); // Hadir, Tanpa Keterangan, Sakit, Izin
        $bulan_tahun  = $this->input->post('bulan_tahun');

        if (empty($rombel) || empty($start_date) || empty($end_date) || empty($status)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Semua form input aksi masal wajib diisi.');
            redirect('presensi/siswa?rombel=' . urlencode($rombel) . '&bulan_tahun=' . urlencode($bulan_tahun));
            return;
        }

        // Ambil daftar siswa di rombel tersebut
        $siswa_list = $this->db->get_where('siswa', ['rombel' => $rombel, 'status_keaktifan' => 'Aktif'])->result();
        if (empty($siswa_list)) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Tidak ditemukan siswa aktif di rombel terpilih.');
            redirect('presensi/siswa?rombel=' . urlencode($rombel) . '&bulan_tahun=' . urlencode($bulan_tahun));
            return;
        }

        // Ambil hari libur dalam rentang tanggal
        $holidays = [];
        if ($this->db->table_exists('pembelajaran_hari_efektif')) {
            $q_libur = $this->db->select('tanggal')
                ->where('tanggal >=', $start_date)
                ->where('tanggal <=', $end_date)
                ->where('status', 'Libur')
                ->get('pembelajaran_hari_efektif')
                ->result();
            foreach ($q_libur as $l) {
                $holidays[$l->tanggal] = true;
            }
        }
        if ($this->db->table_exists('absensi_tanggal')) {
            $q_libur2 = $this->db->select('tanggal_absensi')
                ->where('tanggal_absensi >=', $start_date)
                ->where('tanggal_absensi <=', $end_date)
                ->where('status', 'Libur')
                ->get('absensi_tanggal')
                ->result();
            foreach ($q_libur2 as $l) {
                $holidays[$l->tanggal_absensi] = true;
            }
        }

        // Terjemahkan opsi status "Tanpa Keterangan" menjadi "Alfa" di database
        $db_status = ($status === 'Tanpa Keterangan') ? 'Alfa' : $status;

        // Ambil data presensi yang sudah ada (baik raw maupun override) untuk menghindari menimpa data yang sudah terisi
        // Query presensi_harian
        $existing_harian = [];
        $q_harian = $this->db->select('id_user, pin, tanggal')
            ->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->where('tipe_user', 'siswa')
            ->get('presensi_harian')
            ->result();
        foreach ($q_harian as $eh) {
            $existing_harian[$eh->id_user][$eh->tanggal] = true;
            if (!empty($eh->pin)) {
                $existing_harian[(string)$eh->pin][$eh->tanggal] = true;
            }
        }

        // Query presensi_override
        $existing_override = [];
        if ($this->db->table_exists('presensi_override')) {
            $q_override = $this->db->select('id_user, pin, tanggal')
                ->where('tanggal >=', $start_date)
                ->where('tanggal <=', $end_date)
                ->where('tipe_user', 'siswa')
                ->get('presensi_override')
                ->result();
            foreach ($q_override as $eo) {
                $existing_override[$eo->id_user][$eo->tanggal] = true;
                if (!empty($eo->pin)) {
                    $existing_override[(string)$eo->pin][$eo->tanggal] = true;
                }
            }
        }

        $inserted_count = 0;

        // Mulai perulangan proses per siswa dan per tanggal
        foreach ($siswa_list as $s) {
            $id_user = intval($s->id_siswa);
            $pin = !empty($s->pin_fingerprint) ? intval($s->pin_fingerprint) : (!empty($s->nipd) ? intval($s->nipd) : 0);

            $curr = strtotime($start_date);
            $last = strtotime($end_date);

            while ($curr <= $last) {
                $tgl = date('Y-m-d', $curr);
                $day_of_week = date('N', $curr);

                // Lewati hari Minggu atau hari Libur nasional/sekolah
                if ($day_of_week == 7 || isset($holidays[$tgl])) {
                    $curr = strtotime("+1 day", $curr);
                    continue;
                }

                // Cek apakah sudah terisi di presensi_harian atau presensi_override
                $is_filled = false;
                if (isset($existing_harian[$id_user][$tgl]) || isset($existing_override[$id_user][$tgl])) {
                    $is_filled = true;
                } elseif ($pin > 0 && (isset($existing_harian[(string)$pin][$tgl]) || isset($existing_override[(string)$pin][$tgl]))) {
                    $is_filled = true;
                }

                // Jika kolom/sel masih kosong, lakukan override masal
                if (!$is_filled) {
                    if ($db_status === 'Hadir') {
                        // Insert raw dhuha & dzuhur
                        $this->db->insert('presensi_harian', [
                            'tipe_user' => 'siswa',
                            'id_user'   => $id_user,
                            'pin'       => $pin,
                            'tanggal'   => $tgl,
                            'jam_scan'  => '07:00:00',
                            'sesi'      => 'dhuha'
                        ]);
                        $this->db->insert('presensi_harian', [
                            'tipe_user' => 'siswa',
                            'id_user'   => $id_user,
                            'pin'       => $pin,
                            'tanggal'   => $tgl,
                            'jam_scan'  => '12:00:00',
                            'sesi'      => 'dzuhur'
                        ]);
                    } else {
                        // Insert raw other
                        $this->db->insert('presensi_harian', [
                            'tipe_user' => 'siswa',
                            'id_user'   => $id_user,
                            'pin'       => $pin,
                            'tanggal'   => $tgl,
                            'jam_scan'  => '00:00:00',
                            'sesi'      => 'other'
                        ]);
                    }

                    // Simpan data override ke tabel presensi_override
                    $this->_simpan_override('siswa', $id_user, $tgl, $db_status, 'Aksi masal auto-override', $pin);
                    $inserted_count++;
                }

                $curr = strtotime("+1 day", $curr);
            }
        }

        $this->activity_model->add(logged('name') . ' Melakukan override presensi masal Rombel ' . $rombel . ' dari ' . $start_date . ' s/d ' . $end_date . ' dengan status ' . $status, logged('id'));

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Aksi masal selesai. Berhasil mengisi ' . $inserted_count . ' data presensi yang kosong.');

        redirect('presensi/siswa?rombel=' . urlencode($rombel) . '&bulan_tahun=' . urlencode($bulan_tahun));
    }
}

