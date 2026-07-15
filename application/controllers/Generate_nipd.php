<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Generate_nipd extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_generate_nipd');
    }

    private function ensurePermissions()
    {
        $code = 'menu_generate_nipd';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kesiswaan'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Generate NIPD',
                'parent_id' => $parent_id,
                'level' => 2
            ]);

            $this->db->insert('role_permissions', ['role' => 1, 'permission' => $code]);
            $this->db->insert('role_permissions', ['role' => 6, 'permission' => $code]);
        }
    }

    public function index()
    {
        $this->page_data['page']->title = 'Kesiswaan';
        $this->page_data['page']->titleUrl = 'generate_nipd';
        $this->page_data['page']->subtitle = 'Generate NIPD';
        $this->page_data['page']->subtitleUrl = 'generate_nipd';
        $this->page_data['page']->icon = 'solar:user-id-linear';

        // 1. Get active Rombel / Pembelajaran
        $this->db->select('p.id_pembelajaran, l.nama_lembaga, l.nama_lembaga_singkat, l.bentuk_pendidikan, t.nama_tingkat, r.nama_rombel');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->page_data['rombel_list'] = $this->db->get()->result();

        // 2. Generate list of Angkatan (4-digit format, e.g. 2526)
        $tahun_pelajaran_list = $this->db->get('pembelajaran_tahun_pelajaran')->result();
        $angkatan_options = [];
        
        foreach ($tahun_pelajaran_list as $tp) {
            $pref = $this->extractYearPrefix($tp->tahun_pelajaran);
            if ($pref) {
                $angkatan_options[$pref] = $tp->tahun_pelajaran;
            }
        }

        // Add default padding years around the current year to ensure complete list
        $current_year = (int)date('Y');
        for ($i = $current_year - 4; $i <= $current_year + 1; $i++) {
            $next_y = $i + 1;
            $pref = substr($i, -2) . substr($next_y, -2);
            if (!isset($angkatan_options[$pref])) {
                $angkatan_options[$pref] = "$i/$next_y";
            }
        }
        ksort($angkatan_options);
        $this->page_data['angkatan_options'] = $angkatan_options;

        // Determine default selected angkatan prefix from the active school year
        $ta_aktif = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif'])->row();
        $this->page_data['default_angkatan'] = $ta_aktif ? $this->extractYearPrefix($ta_aktif->tahun_pelajaran) : '';

        $this->load->view('generate_nipd/index', $this->page_data);
    }

    private function extractYearPrefix($tahun_pelajaran)
    {
        // e.g. "2025/2026" or "2025-2026"
        if (preg_match('/^(\d{2})(\d{2})[\/\-](\d{2})(\d{2})$/', $tahun_pelajaran, $matches)) {
            return $matches[2] . $matches[4]; // "25" . "26" = "2526"
        }
        return '';
    }

    public function get_students()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');
        $angkatan = $this->input->post('angkatan');

        if (empty($id_pembelajaran) || empty($angkatan)) {
            echo json_encode(['status' => false, 'message' => 'Rombel dan Angkatan wajib dipilih.']);
            return;
        }

        // Get rombel/lembaga details
        $pembelajaran = $this->db->select('p.*, l.bentuk_pendidikan, l.nama_lembaga, r.nama_rombel')
                                 ->from('pembelajaran p')
                                 ->join('lembaga l', 'p.id_lembaga = l.id_lembaga')
                                 ->join('rombel r', 'p.id_rombel = r.id_rombel')
                                 ->where('p.id_pembelajaran', $id_pembelajaran)
                                 ->get()->row();

        if (!$pembelajaran) {
            echo json_encode(['status' => false, 'message' => 'Data Rombel tidak ditemukan.']);
            return;
        }

        // Determine Kode Lembaga
        $kode_lembaga = '01'; // Default SMP
        if (stripos($pembelajaran->bentuk_pendidikan, 'SMP') !== false) {
            $kode_lembaga = '01';
        } elseif (stripos($pembelajaran->bentuk_pendidikan, 'SMA') !== false) {
            $kode_lembaga = '02';
        } elseif (stripos($pembelajaran->bentuk_pendidikan, 'PONPES') !== false || stripos($pembelajaran->bentuk_pendidikan, 'PESANTREN') !== false || stripos($pembelajaran->bentuk_pendidikan, 'PONDOK') !== false) {
            $kode_lembaga = '03';
        } else {
            if (stripos($pembelajaran->nama_lembaga, 'SMP') !== false) {
                $kode_lembaga = '01';
            } elseif (stripos($pembelajaran->nama_lembaga, 'SMA') !== false) {
                $kode_lembaga = '02';
            } elseif (stripos($pembelajaran->nama_lembaga, 'PONPES') !== false || stripos($pembelajaran->nama_lembaga, 'PESANTREN') !== false || stripos($pembelajaran->nama_lembaga, 'PONDOK') !== false) {
                $kode_lembaga = '03';
            }
        }

        $prefix = $angkatan . $kode_lembaga; // 6 digits prefix

        // Get students in this Rombel
        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->where('ps.id_pembelajaran', $id_pembelajaran);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $students = $this->db->get()->result_array();

        // Calculate next sequence number for the prefix in the database
        $query = $this->db->select('nipd')
                          ->like('nipd', $prefix, 'after')
                          ->get('siswa');
        $existing_nipds = $query->result_array();
        $max_seq = 0;
        foreach ($existing_nipds as $row) {
            $nipd = $row['nipd'];
            if (strlen($nipd) === 10) {
                $seq = (int) substr($nipd, 6, 4);
                if ($seq > $max_seq) {
                    $max_seq = $seq;
                }
            }
        }
        $next_seq = $max_seq + 1;

        // Build list of students with proposed NIPD
        $formatted_students = [];
        $temp_seq = $next_seq;
        foreach ($students as $s) {
            $proposed = $prefix . sprintf('%04d', $temp_seq);
            $formatted_students[] = [
                'id_siswa' => $s['id_siswa'],
                'nama_siswa' => $s['nama_siswa'],
                'nisn' => $s['nisn'] ?: '-',
                'nipd' => $s['nipd'] ?: '',
                'proposed_nipd' => $proposed,
                'has_nipd' => !empty($s['nipd'])
            ];
            $temp_seq++;
        }

        echo json_encode([
            'status' => true,
            'students' => $formatted_students,
            'prefix' => $prefix,
            'next_seq' => $next_seq,
            'lembaga_info' => $pembelajaran->nama_lembaga . ' (' . ($pembelajaran->bentuk_pendidikan ?: 'Lainnya') . ')'
        ]);
    }

    public function generate()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');
        $angkatan = $this->input->post('angkatan');
        $siswa_ids = $this->input->post('siswa_ids');

        if (empty($id_pembelajaran) || empty($angkatan) || empty($siswa_ids) || !is_array($siswa_ids)) {
            echo json_encode(['status' => false, 'message' => 'Data input tidak lengkap atau tidak ada siswa yang dipilih.']);
            return;
        }

        // Get rombel/lembaga details
        $pembelajaran = $this->db->select('p.*, l.bentuk_pendidikan, l.nama_lembaga, r.nama_rombel')
                                 ->from('pembelajaran p')
                                 ->join('lembaga l', 'p.id_lembaga = l.id_lembaga')
                                 ->join('rombel r', 'p.id_rombel = r.id_rombel')
                                 ->where('p.id_pembelajaran', $id_pembelajaran)
                                 ->get()->row();

        if (!$pembelajaran) {
            echo json_encode(['status' => false, 'message' => 'Data Rombel tidak ditemukan.']);
            return;
        }

        // Determine Kode Lembaga
        $kode_lembaga = '01'; // Default SMP
        if (stripos($pembelajaran->bentuk_pendidikan, 'SMP') !== false) {
            $kode_lembaga = '01';
        } elseif (stripos($pembelajaran->bentuk_pendidikan, 'SMA') !== false) {
            $kode_lembaga = '02';
        } elseif (stripos($pembelajaran->bentuk_pendidikan, 'PONPES') !== false || stripos($pembelajaran->bentuk_pendidikan, 'PESANTREN') !== false || stripos($pembelajaran->bentuk_pendidikan, 'PONDOK') !== false) {
            $kode_lembaga = '03';
        } else {
            if (stripos($pembelajaran->nama_lembaga, 'SMP') !== false) {
                $kode_lembaga = '01';
            } elseif (stripos($pembelajaran->nama_lembaga, 'SMA') !== false) {
                $kode_lembaga = '02';
            } elseif (stripos($pembelajaran->nama_lembaga, 'PONPES') !== false || stripos($pembelajaran->nama_lembaga, 'PESANTREN') !== false || stripos($pembelajaran->nama_lembaga, 'PONDOK') !== false) {
                $kode_lembaga = '03';
            }
        }

        $prefix = $angkatan . $kode_lembaga;

        $this->db->trans_start();

        // Re-calculate sequence number in transaction to prevent race conditions
        $query = $this->db->select('nipd')
                          ->like('nipd', $prefix, 'after')
                          ->get('siswa');
        $existing_nipds = $query->result_array();
        $max_seq = 0;
        foreach ($existing_nipds as $row) {
            $nipd = $row['nipd'];
            if (strlen($nipd) === 10) {
                $seq = (int) substr($nipd, 6, 4);
                if ($seq > $max_seq) {
                    $max_seq = $seq;
                }
            }
        }
        $next_seq = $max_seq + 1;

        $count = 0;
        foreach ($siswa_ids as $id_siswa) {
            // Validate that the student belongs to this rombel
            $exists = $this->db->get_where('pembelajaran_siswa', [
                'id_pembelajaran' => $id_pembelajaran,
                'peserta_didik_id' => $id_siswa
            ])->num_rows();

            if ($exists > 0) {
                $new_nipd = $prefix . sprintf('%04d', $next_seq);
                $this->db->where('id_siswa', $id_siswa)->update('siswa', ['nipd' => $new_nipd]);
                $next_seq++;
                $count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal memproses pembuatan NIPD. Terjadi kesalahan database.']);
        } else {
            $this->activity_model->add(logged('name') . " men-generate NIPD untuk $count siswa di kelas " . $pembelajaran->nama_rombel, $id_pembelajaran);
            echo json_encode(['status' => true, 'message' => "$count NIPD siswa berhasil digenerate."]);
        }
    }
}
