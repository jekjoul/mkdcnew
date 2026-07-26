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
                if (!empty($s->niy))              $pin_list[] = "'" . $this->db->escape_str($s->niy) . "'";
                if (!empty($s->pin_fingerprint)) $pin_list[] = "'" . $this->db->escape_str($s->pin_fingerprint) . "'";
                if (!empty($s->nik))              $pin_list[] = "'" . $this->db->escape_str($s->nik) . "'";
            } else {
                if (isset($s->id_siswa)) $user_id = intval($s->id_siswa);
                if (!empty($s->nipd))            $pin_list[] = "'" . $this->db->escape_str($s->nipd) . "'";
                if (!empty($s->pin_fingerprint)) $pin_list[] = "'" . $this->db->escape_str($s->pin_fingerprint) . "'";
            }

            if ($user_id > 0) {
                $id_user_list[] = $user_id;
            }
        }

        $id_user_list = array_unique($id_user_list);
        $pin_list     = array_unique($pin_list);

        $where_cond = [];
        if (!empty($id_user_list)) {
            $where_cond[] = "id_user IN (" . implode(',', $id_user_list) . ")";
        }
        if (!empty($pin_list)) {
            $where_cond[] = "pin IN (" . implode(',', $pin_list) . ")";
        }

        if (empty($where_cond)) return ['by_id' => [], 'by_pin' => []];

        $where_sql = "(" . implode(" OR ", $where_cond) . ")";

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
                WHERE tipe_user = ?
                  AND {$where_sql}
                  AND YEAR(tanggal)  = ?
                  AND MONTH(tanggal) = ?
                GROUP BY id_user, pin, tanggal
            ";
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
                WHERE tipe_user = ?
                  AND {$where_sql}
                  AND YEAR(tanggal)  = ?
                  AND MONTH(tanggal) = ?
                GROUP BY id_user, pin, tanggal
            ";
        }

        $result = $this->db->query($sql, [$tipe_user, $year, $month])->result();

        $matrix_by_id  = [];
        $matrix_by_pin = [];

        foreach ($result as $row) {
            if ($row->id_user > 0) {
                $matrix_by_id[$row->id_user][$row->tanggal] = $row;
            }
            if (!empty($row->pin)) {
                $matrix_by_pin[(string)$row->pin][$row->tanggal] = $row;
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
            JOIN {$join_table} u ON u.{$join_key} = p.id_user
            WHERE p.tipe_user = ?
              AND p.tanggal   = ?
            GROUP BY p.id_user, p.tanggal
            ORDER BY jam_dhuha ASC
        ";


        return $this->db->query($sql, [$tipe_user, $tanggal])->result();
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

        // Cari pin user
        $pin = 0;
        if ($tipe_user === 'siswa') {
            $user_obj = $this->db->get_where('siswa', ['id_siswa' => $id_user])->row();
            $pin = $user_obj ? $user_obj->pin_fingerprint : 0;
        } else {
            $user_obj = $this->db->get_where('ptk', ['id_ptk' => $id_user])->row();
            $pin = $user_obj ? $user_obj->pin_fingerprint : 0;
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

        // Simpan override ke tabel presensi_override (akan dibuat jika belum ada)
        $this->_simpan_override($tipe_user, $id_user, $tanggal, $status, $keterangan, $pin);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Presensi berhasil diperbarui secara manual.');

        $rombel     = $this->input->post('rombel');
        $bulan_tahun = $this->input->post('bulan_tahun');

        if ($tipe_user === 'siswa') {
            redirect('presensi/siswa?rombel=' . urlencode($rombel) . '&bulan_tahun=' . urlencode($bulan_tahun));
        } else {
            redirect('presensi/guru?bulan_tahun=' . urlencode($bulan_tahun));
        }
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
                    `pin`        INT(11) NOT NULL DEFAULT 0,
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
            $machine_users_by_pin[(string)$m->pin] = $m;
        }

        $templates = $this->db->get('presensi_machine_templates')->result();
        $templates_by_pin = [];
        foreach ($templates as $t) {
            $templates_by_pin[(string)$t->pin][] = $t;
        }

        // 2. Data Siswa Aktif (PIN = NIPD atau pin_fingerprint)
        $all_siswa = $this->db->select('id_siswa, nipd, pin_fingerprint, nama_siswa')
                              ->from('siswa')
                              ->where('status_keaktifan', 'Aktif')
                              ->order_by('nama_siswa', 'ASC')
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
            $pin = !empty($s->pin_fingerprint) ? trim((string)$s->pin_fingerprint) : trim((string)$s->nipd);
            if (empty($pin)) continue;

            $m = isset($machine_users_by_pin[$pin]) ? $machine_users_by_pin[$pin] : null;
            $processed_pins[$pin] = true;

            $merged_users[] = (object)[
                'pin'               => $pin,
                'nama'              => $s->nama_siswa,
                'nama_mesin'        => $m ? $m->nama : null,
                'tipe_user'         => 'Siswa',
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
            $pin = !empty($p->pin_fingerprint) ? trim((string)$p->pin_fingerprint) : trim((string)$p->niy);
            if (empty($pin)) continue;

            $m = isset($machine_users_by_pin[$pin]) ? $machine_users_by_pin[$pin] : null;
            $processed_pins[$pin] = true;

            $merged_users[] = (object)[
                'pin'               => $pin,
                'nama'              => $p->nama_ptk,
                'nama_mesin'        => $m ? $m->nama : null,
                'tipe_user'         => 'PTK / Guru',
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
            $pin = trim((string)$m->pin);
            if (empty($pin) || isset($processed_pins[$pin])) continue;

            $merged_users[] = (object)[
                'pin'               => $pin,
                'nama'              => $m->nama,
                'nama_mesin'        => $m->nama,
                'tipe_user'         => 'User Mesin Saja',
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
}
