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
    private function _aggregasi_presensi($tipe_user, $id_user_list, $year, $month)
    {
        if (empty($id_user_list)) return [];

        $ids = implode(',', array_map('intval', $id_user_list));

        $sql = "
            SELECT
                id_user,
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
              AND id_user IN ($ids)
              AND YEAR(tanggal)  = ?
              AND MONTH(tanggal) = ?
            GROUP BY id_user, tanggal
        ";


        $result = $this->db->query($sql, [$tipe_user, $year, $month])->result();

        $matrix = [];
        foreach ($result as $row) {
            $matrix[$row->id_user][$row->tanggal] = $row;
        }
        return $matrix;
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
    // Halaman Monitoring Presensi Siswa
    // =========================================================================
    public function siswa()
    {
        $this->page_data['page']->title       = 'Presensi Siswa';
        $this->page_data['page']->titleUrl    = 'presensi/siswa';
        $this->page_data['page']->subtitle    = 'Monitoring Kehadiran Siswa';
        $this->page_data['page']->subtitleUrl = 'presensi/siswa';
        $this->page_data['page']->icon        = 'solar:users-group-two-rounded-linear';

        // --- TAB 1: Kehadiran Hari Ini ---
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        $this->page_data['tanggal'] = $tanggal;

        $this->page_data['presensi_harian'] = $this->_presensi_hari_ini(
            'siswa', $tanggal, 'siswa', 'id_siswa', 'nama_siswa', 'rombel'
        );

        // Sisipkan kolom nisn secara terpisah jika dibutuhkan
        // (sudah cukup dari join)

        // --- TAB 2: Rekap Bulanan Grid ---
        $rombel_list = $this->db
            ->select('DISTINCT(rombel) as rombel')
            ->from('siswa')
            ->where('rombel !=', '')
            ->order_by('rombel', 'ASC')
            ->get()->result();
        $this->page_data['rombel_list'] = $rombel_list;

        $selected_rombel = $this->input->get('rombel');
        $selected_month  = $this->input->get('bulan_tahun'); // format: Y-m

        if ($selected_rombel && $selected_month) {
            $year  = substr($selected_month, 0, 4);
            $month = substr($selected_month, 5, 2);

            // Daftar tanggal efektif bulan ini (aman jika tabel belum ada)
            $tanggal_list = [];
            if ($this->db->table_exists('absensi_tanggal')) {
                $this->db->where('YEAR(tanggal_absensi)', $year);
                $this->db->where('MONTH(tanggal_absensi)', $month);
                $this->db->order_by('tanggal_absensi', 'ASC');
                $q = $this->db->get('absensi_tanggal');
                $tanggal_list = ($q !== false) ? $q->result() : [];
            }
            $this->page_data['tanggal_list'] = $tanggal_list;

            // Daftar siswa di rombel terpilih
            $this->db->where('rombel', $selected_rombel);
            $this->db->order_by('nama_siswa', 'ASC');
            $siswa_list = $this->db->get('siswa')->result();
            $this->page_data['siswa_list'] = $siswa_list;

            // Agregasi presensi raw → matrix [id_siswa][tanggal] = {status, keterangan, jam_dhuha, jam_dzuhur}
            $id_list = array_column($siswa_list, 'id_siswa');
            $this->page_data['presensi_matrix'] = $this->_aggregasi_presensi('siswa', $id_list, $year, $month);

            $this->page_data['selected_rombel'] = $selected_rombel;
            $this->page_data['selected_month']  = $selected_month;
        }

        // List bulan (termasuk bulan berjalan meski belum ada di absensi_tanggal)
        $this->page_data['bulan_list'] = $this->_get_bulan_list();

        $this->load->view('presensi/v_siswa', $this->page_data);
    }

    // =========================================================================
    // Halaman Monitoring Presensi Guru (PTK)
    // =========================================================================
    public function guru()
    {
        $this->page_data['page']->title       = 'Presensi Guru';
        $this->page_data['page']->titleUrl    = 'presensi/guru';
        $this->page_data['page']->subtitle    = 'Monitoring Kehadiran Guru & Staf';
        $this->page_data['page']->subtitleUrl = 'presensi/guru';
        $this->page_data['page']->icon        = 'icon-park-outline:user-business';

        // --- TAB 1: Kehadiran Hari Ini ---
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        $this->page_data['tanggal'] = $tanggal;

        $this->page_data['presensi_harian'] = $this->_presensi_hari_ini(
            'ptk', $tanggal, 'ptk', 'id_ptk', 'nama_ptk', 'penugasan'
        );

        // --- TAB 2: Rekap Bulanan Grid ---
        $selected_month = $this->input->get('bulan_tahun');

        if ($selected_month) {
            $year  = substr($selected_month, 0, 4);
            $month = substr($selected_month, 5, 2);

            // Daftar tanggal efektif bulan ini (aman jika tabel belum ada)
            $tanggal_list = [];
            if ($this->db->table_exists('absensi_tanggal')) {
                $this->db->where('YEAR(tanggal_absensi)', $year);
                $this->db->where('MONTH(tanggal_absensi)', $month);
                $this->db->order_by('tanggal_absensi', 'ASC');
                $q = $this->db->get('absensi_tanggal');
                $tanggal_list = ($q !== false) ? $q->result() : [];
            }
            $this->page_data['tanggal_list'] = $tanggal_list;

            $this->db->order_by('nama_ptk', 'ASC');
            $guru_list = $this->db->get('ptk')->result();
            $this->page_data['guru_list'] = $guru_list;

            $id_list = array_column($guru_list, 'id_ptk');
            $this->page_data['presensi_matrix'] = $this->_aggregasi_presensi('ptk', $id_list, $year, $month);

            $this->page_data['selected_month'] = $selected_month;
        }

        // List bulan (termasuk bulan berjalan meski belum ada di absensi_tanggal)
        $this->page_data['bulan_list'] = $this->_get_bulan_list();

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

    // =========================================================================
    // Halaman Status Mesin & Antrean Sinkronisasi
    // =========================================================================
    public function mesin()
    {
        $this->page_data['page']->title       = 'Konfigurasi Mesin & Antrean';
        $this->page_data['page']->titleUrl    = 'presensi/mesin';
        $this->page_data['page']->subtitle    = 'Antrean Sinkronisasi Sidik Jari';
        $this->page_data['page']->subtitleUrl = 'presensi/mesin';
        $this->page_data['page']->icon        = 'solar:settings-linear';

        $this->page_data['api_token'] = 'MKDC_FINGERPRINT_SECRET_KEY_2026';

        $tasks = [];
        if ($this->db->table_exists('fingerprint_tasks')) {
            $this->db->order_by('id', 'DESC');
            $this->db->limit(100);
            $q = $this->db->get('fingerprint_tasks');
            $tasks = ($q !== false) ? $q->result() : [];
        }
        $this->page_data['tasks'] = $tasks;

        $this->load->view('presensi/v_mesin', $this->page_data);
    }

    // =========================================================================
    // Reset tugas gagal ke antrean pending
    // =========================================================================
    public function reset_task($id)
    {
        $this->db->where('id', $id);
        $this->db->update('fingerprint_tasks', [
            'status'        => 'pending',
            'attempts'      => 0,
            'error_message' => null
        ]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Tugas berhasil di-reset ke antrean pending.');
        redirect('presensi/mesin');
    }

    // =========================================================================
    // Hapus tugas dari antrean
    // =========================================================================
    public function hapus_task($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('fingerprint_tasks');
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Tugas berhasil dihapus dari antrean.');
        redirect('presensi/mesin');
    }
}
