<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jurnal_guru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jurnal_guru_model', 'jurnal_model');
    }

    private function currentPtk()
    {
        $user_id = logged('id');
        if (!$user_id) return null;

        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        if (!$user) return null;

        if (!empty($user->id_ptk)) {
            $ptk = $this->db->get_where('ptk', ['id_ptk' => (int) $user->id_ptk])->row();
            if ($ptk) return $ptk;
        }

        $ptk = null;
        if (!empty($user->email)) {
            $this->db->where('LOWER(email)', strtolower($user->email));
            $ptk = $this->db->get('ptk')->row();
        }

        if (!$ptk && !empty($user->name)) {
            $this->db->where('LOWER(nama_ptk)', strtolower($user->name));
            $ptk = $this->db->get('ptk')->row();
        }

        if ($ptk) {
            $this->db->where('id', $user_id)->update('users', ['id_ptk' => $ptk->id_ptk]);
        }

        return $ptk;
    }

    private function setPage($title, $subtitle, $subtitleUrl, $icon)
    {
        if (!isset($this->page_data['page'])) {
            $this->page_data['page'] = new stdClass();
        }
        $this->page_data['page']->title = $title;
        $this->page_data['page']->titleUrl = 'jurnal_guru';
        $this->page_data['page']->subtitle = $subtitle;
        $this->page_data['page']->subtitleUrl = $subtitleUrl;
        $this->page_data['page']->icon = $icon;
    }

    public function index()
    {
        $user_role  = logged('role');
        $ptk_logged = $this->currentPtk();
        $is_admin   = ($user_role == '1' || $user_role == 'admin');

        // Guru yang tidak terhubung PTK → redirect dashboard
        if (!$is_admin) {
            if (!$ptk_logged) {
                $this->session->set_flashdata('alert-type', 'warning');
                $this->session->set_flashdata('alert', 'Akun Anda belum terhubung dengan data PTK/Guru.');
                redirect('dashboard');
                return;
            }
        }

        $this->page_data['is_admin']   = $is_admin;
        $this->page_data['ptk_logged'] = $ptk_logged;

        // Tahun pelajaran — dimuat server-side untuk semua role
        $this->page_data['tahun_pelajaran_list'] = $this->jurnal_model->getTahunPelajaranList();

        // Admin: guru list dimuat setelah tahun pelajaran dipilih (via AJAX)
        // Guru: mapel list dari PTK sendiri dimuat server-side
        $this->page_data['guru_list']  = $is_admin ? [] : [];
        $this->page_data['mapel_list'] = !$is_admin
            ? $this->jurnal_model->getMapelByPtk($ptk_logged->id_ptk)
            : [];

        // Tabel data dimulai kosong — diisi via AJAX setelah filter dipilih
        $this->page_data['jurnal_list'] = [];

        $this->setPage('Jurnal KBM Guru', 'Jurnal Guru', 'jurnal_guru', 'solar:book-bookmark-bold');
        $this->load->view('jurnal_guru/index', $this->page_data);
    }

    /**
     * AJAX: Data jurnal sebagai JSON (update tabel real-time)
     */
    public function get_data()
    {
        $user_role  = logged('role');
        $is_admin   = ($user_role == '1' || $user_role == 'admin');
        $ptk_logged = $this->currentPtk();

        $id_tahun_pelajaran = $this->input->get('id_tahun_pelajaran');
        $id_ptk             = $this->input->get('id_ptk');
        $id_mapel           = $this->input->get('id_mapel');
        $id_rombel          = $this->input->get('id_rombel');

        if (!$is_admin) {
            if (!$ptk_logged) {
                header('Content-Type: application/json');
                echo json_encode(['data' => [], 'total' => 0, 'is_admin' => false]);
                return;
            }
            $id_ptk = $ptk_logged->id_ptk;
        }

        $filters = [
            'id_tahun_pelajaran' => $id_tahun_pelajaran,
            'id_ptk'             => $id_ptk,
            'id_mapel'           => $id_mapel,
            'id_rombel'          => $id_rombel,
        ];

        $list = $this->jurnal_model->getJurnalGuru($filters);

        $rows = [];
        foreach ($list as $item) {
            $tingkat     = !empty($item->nama_tingkat) ? $item->nama_tingkat . ' - ' : '';
            $label_kelas = $tingkat . htmlspecialchars((string)$item->nama_rombel, ENT_QUOTES);
            $rows[] = [
                'hari'         => htmlspecialchars((string)$item->hari,       ENT_QUOTES),
                'tanggal'      => date('d M Y', strtotime($item->tanggal)),
                'pertemuan_ke' => $item->pertemuan_ke,
                'label_kelas'  => $label_kelas,
                'nama_mapel'   => htmlspecialchars((string)$item->nama_mapel,   ENT_QUOTES),
                'nama_ptk'     => htmlspecialchars((string)($item->nama_ptk  ?? ''), ENT_QUOTES),
                'materi'       => htmlspecialchars(strip_tags((string)($item->materi ?? '')), ENT_QUOTES),
                'hambatan'     => htmlspecialchars((string)($item->hambatan_fix  ?? ''), ENT_QUOTES),
                'pemecahan'    => htmlspecialchars((string)($item->pemecahan_fix ?? ''), ENT_QUOTES),
                'absensi_h'    => (int)$item->absensi_h,
                'absensi_i'    => (int)$item->absensi_i,
                'absensi_s'    => (int)$item->absensi_s,
                'absensi_a'    => (int)$item->absensi_a,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $rows, 'total' => count($rows), 'is_admin' => $is_admin]);
    }

    /**
     * AJAX: Daftar Guru setelah Tahun Pelajaran dipilih (Admin only)
     */
    public function get_guru_options()
    {
        header('Content-Type: application/json');
        $user_role = logged('role');
        $is_admin  = ($user_role == '1' || $user_role == 'admin');
        if (!$is_admin) { echo json_encode([]); return; }

        $id_tahun_pelajaran = $this->input->get('id_tahun_pelajaran');
        $list = $this->jurnal_model->getGuruList($id_tahun_pelajaran ?: null);
        echo json_encode($list);
    }

    /**
     * AJAX: Daftar Mapel setelah Guru dipilih
     */
    public function get_mapel_options()
    {
        header('Content-Type: application/json');
        $user_role  = logged('role');
        $is_admin   = ($user_role == '1' || $user_role == 'admin');
        $ptk_logged = $this->currentPtk();

        $id_tahun_pelajaran = $this->input->get('id_tahun_pelajaran');
        $id_ptk             = $this->input->get('id_ptk');

        if (!$is_admin) {
            if (!$ptk_logged) { echo json_encode([]); return; }
            $id_ptk = $ptk_logged->id_ptk;
        }

        $list = $this->jurnal_model->getMapelByPtk($id_ptk ?: null, $id_tahun_pelajaran ?: null);
        echo json_encode($list);
    }

    /**
     * AJAX: Daftar Rombel setelah Guru + Mapel dipilih
     */
    public function get_rombel_options()
    {
        header('Content-Type: application/json');
        $user_role  = logged('role');
        $is_admin   = ($user_role == '1' || $user_role == 'admin');
        $ptk_logged = $this->currentPtk();

        $id_tahun_pelajaran = $this->input->get('id_tahun_pelajaran');
        $id_ptk             = $this->input->get('id_ptk');
        $id_mapel           = $this->input->get('id_mapel');

        if (!$is_admin) {
            if (!$ptk_logged) { echo json_encode([]); return; }
            $id_ptk = $ptk_logged->id_ptk;
        }

        $list = $this->jurnal_model->getRombelByPtkMapel(
            $id_ptk  ?: null,
            $id_mapel ?: null,
            $id_tahun_pelajaran ?: null
        );
        echo json_encode($list);
    }

    public function cetak()
    {
        $user_role  = logged('role');
        $ptk_logged = $this->currentPtk();
        $is_admin   = ($user_role == '1' || $user_role == 'admin');

        $id_tahun_pelajaran = $this->input->get('id_tahun_pelajaran');
        $id_ptk             = $this->input->get('id_ptk');
        $id_mapel           = $this->input->get('id_mapel');
        $id_rombel          = $this->input->get('id_rombel');

        if (!$is_admin) {
            if (!$ptk_logged) show_404();
            $id_ptk = $ptk_logged->id_ptk;
        }

        $filters = [
            'id_tahun_pelajaran' => $id_tahun_pelajaran,
            'id_ptk'             => $id_ptk,
            'id_mapel'           => $id_mapel,
            'id_rombel'          => $id_rombel,
        ];

        $this->page_data['is_admin']    = $is_admin;
        $this->page_data['ptk_logged']  = $ptk_logged;
        $this->page_data['jurnal_list'] = $this->jurnal_model->getJurnalGuru($filters);

        // Metadata header cetak
        $this->page_data['guru_info']   = $id_ptk
            ? $this->db->get_where('ptk',   ['id_ptk'   => (int)$id_ptk])->row()   : null;
        $this->page_data['mapel_info']  = $id_mapel
            ? $this->db->get_where('mapel',  ['id_mapel'  => (int)$id_mapel])->row()  : null;
        $this->page_data['rombel_info'] = $id_rombel
            ? $this->db->get_where('rombel', ['id_rombel' => (int)$id_rombel])->row() : null;
        $this->page_data['tahun_info']  = $id_tahun_pelajaran
            ? $this->db->get_where('pembelajaran_tahun_pelajaran', ['id_tahun_pelajaran' => (int)$id_tahun_pelajaran])->row() : null;

        $this->load->view('jurnal_guru/cetak', $this->page_data);
    }
}

