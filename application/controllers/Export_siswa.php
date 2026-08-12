<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Export_siswa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_export_siswa');
    }

    private function ensurePermissions()
    {
        $code = 'menu_export_siswa';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kesiswaan'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Export Siswa massal',
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
        $this->page_data['page']->titleUrl = 'export_siswa';
        $this->page_data['page']->subtitle = 'Export Siswa massal';
        $this->page_data['page']->subtitleUrl = 'export_siswa';
        $this->page_data['page']->icon = 'solar:download-linear';

        // 1. Get active Rombel / Pembelajaran grouped by Lembaga
        $this->db->select('p.id_pembelajaran, l.id_lembaga, l.nama_lembaga, l.nama_lembaga_singkat, l.bentuk_pendidikan, t.nama_tingkat, r.nama_rombel');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('p.status', 'Aktif');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('CAST(t.tingkat_angka AS UNSIGNED)', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $rombel_raw = $this->db->get()->result();

        // Group by Lembaga
        $rombel_grouped = [];
        foreach ($rombel_raw as $row) {
            $key = $row->id_lembaga;
            if (!isset($rombel_grouped[$key])) {
                $rombel_grouped[$key] = [
                    'nama_lembaga' => $row->nama_lembaga,
                    'nama_lembaga_singkat' => !empty($row->nama_lembaga_singkat) ? $row->nama_lembaga_singkat : (!empty($row->bentuk_pendidikan) ? $row->bentuk_pendidikan : 'Lainnya'),
                    'list' => []
                ];
            }
            $rombel_grouped[$key]['list'][] = $row;
        }
        $this->page_data['rombel_grouped'] = $rombel_grouped;

        // 2. Define exportable fields grouped by category
        $this->page_data['fields_grouped'] = [
            'Data Pribadi' => [
                'nama_siswa' => 'Nama Lengkap',
                'nisn' => 'NISN',
                'nipd' => 'NIPD',
                'nik' => 'NIK',
                'no_kk' => 'No Kartu Keluarga',
                'jenis_kelamin' => 'Jenis Kelamin',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'agama' => 'Agama',
                'kewarganegaraan' => 'Kewarganegaraan',
                'anak_ke' => 'Anak Ke'
            ],
            'Kontak & Domisili' => [
                'telepon' => 'Telepon',
                'email' => 'Email',
                'alamat' => 'Alamat',
                'rt' => 'RT',
                'rw' => 'RW',
                'jenis_tempat_tinggal' => 'Jenis Tempat Tinggal',
                'alat_transportasi' => 'Alat Transportasi',
                'jarak_ke_sekolah' => 'Jarak ke Sekolah',
                'koordinat' => 'Koordinat'
            ],
            'Riwayat Pendidikan & Medis' => [
                'sekolah_asal' => 'Sekolah Asal',
                'no_ijazah' => 'No Ijazah',
                'riwayat_penyakit' => 'Riwayat Penyakit',
                'prestasi_siswa' => 'Prestasi Siswa',
                'tanggal_pendaftaran' => 'Tanggal Pendaftaran',
                'status_pendaftaran' => 'Status Pendaftaran',
                'status_keaktifan' => 'Status Keaktifan'
            ],
            'Data Ayah Kandung' => [
                'nama_ayah' => 'Nama Ayah',
                'nik_ayah' => 'NIK Ayah',
                'pekerjaan_ayah' => 'Pekerjaan Ayah',
                'penghasilan_ayah' => 'Penghasilan Ayah',
                'tahun_lahir_ayah' => 'Tahun Lahir Ayah',
                'pendidikan_ayah' => 'Pendidikan Ayah',
                'alamat_ayah' => 'Alamat Ayah'
            ],
            'Data Ibu Kandung' => [
                'nama_ibu' => 'Nama Ibu',
                'nik_ibu' => 'NIK Ibu',
                'pekerjaan_ibu' => 'Pekerjaan Ibu',
                'penghasilan_ibu' => 'Penghasilan Ibu',
                'tahun_lahir_ibu' => 'Tahun Lahir Ibu',
                'pendidikan_ibu' => 'Pendidikan Ibu',
                'alamat_ibu' => 'Alamat Ibu'
            ]
        ];

        // Flat fields array for mapping in view excel
        $flat_fields = [];
        foreach ($this->page_data['fields_grouped'] as $cat => $fields) {
            foreach ($fields as $key => $label) {
                $flat_fields[$key] = $label;
            }
        }
        $this->page_data['flat_fields'] = $flat_fields;

        $this->load->view('export_siswa/index', $this->page_data);
    }

    public function get_students()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');

        if (empty($id_pembelajaran) || !is_array($id_pembelajaran)) {
            echo json_encode(['status' => false, 'message' => 'Harap pilih minimal satu rombel.']);
            return;
        }

        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin, r.nama_rombel, l.nama_lembaga_singkat, l.bentuk_pendidikan');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('pembelajaran p', 'ps.id_pembelajaran = p.id_pembelajaran');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->where_in('ps.id_pembelajaran', $id_pembelajaran);
        $this->db->where('s.status_keaktifan', 'Aktif');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('s.nama_siswa', 'ASC');
        $students = $this->db->get()->result_array();

        echo json_encode([
            'status' => true,
            'students' => $students
        ]);
    }

    public function export_excel()
    {
        $id_pembelajaran = $this->input->post('id_pembelajaran');
        $selected_fields = $this->input->post('fields');
        $selected_students = $this->input->post('students');

        if (empty($id_pembelajaran) || empty($selected_fields) || empty($selected_students)) {
            show_error('Data input tidak lengkap. Harap pilih rombel, minimal satu field, dan minimal satu siswa.', 400, 'Input Error');
            return;
        }

        // Get selected pembelajaran details
        $this->db->select('p.*, l.nama_lembaga, l.nama_lembaga_singkat, l.bentuk_pendidikan, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where_in('p.id_pembelajaran', $id_pembelajaran);
        $rombel_data = $this->db->get()->result();

        // Get single representative year/semester
        $tahun_pelajaran = isset($rombel_data[0]) ? $rombel_data[0]->tahun_pelajaran : '';
        $semester = isset($rombel_data[0]) ? $rombel_data[0]->semester : '';

        // Fetch students data with rombel & lembaga info
        $this->db->select('s.*, r.nama_rombel, l.nama_lembaga_singkat, l.bentuk_pendidikan, l.nama_lembaga');
        $this->db->from('siswa s');
        $this->db->join('pembelajaran_siswa ps', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('pembelajaran p', 'ps.id_pembelajaran = p.id_pembelajaran');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->where_in('s.id_siswa', $selected_students);
        $this->db->where_in('ps.id_pembelajaran', $id_pembelajaran);
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('s.nama_siswa', 'ASC');
        $students = $this->db->get()->result_array();

        // Flat fields map for excel headers
        $fields_map = [
            'nama_siswa' => 'Nama Lengkap',
            'nisn' => 'NISN',
            'nipd' => 'NIPD',
            'nik' => 'NIK',
            'no_kk' => 'No Kartu Keluarga',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'kewarganegaraan' => 'Kewarganegaraan',
            'anak_ke' => 'Anak Ke',
            'telepon' => 'Telepon',
            'email' => 'Email',
            'alamat' => 'Alamat',
            'rt' => 'RT',
            'rw' => 'RW',
            'jenis_tempat_tinggal' => 'Jenis Tempat Tinggal',
            'alat_transportasi' => 'Alat Transportasi',
            'jarak_ke_sekolah' => 'Jarak ke Sekolah',
            'koordinat' => 'Koordinat',
            'sekolah_asal' => 'Sekolah Asal',
            'no_ijazah' => 'No Ijazah',
            'riwayat_penyakit' => 'Riwayat Penyakit',
            'prestasi_siswa' => 'Prestasi Siswa',
            'tanggal_pendaftaran' => 'Tanggal Pendaftaran',
            'status_pendaftaran' => 'Status Pendaftaran',
            'status_keaktifan' => 'Status Keaktifan',
            'nama_ayah' => 'Nama Ayah',
            'nik_ayah' => 'NIK Ayah',
            'pekerjaan_ayah' => 'Pekerjaan Ayah',
            'penghasilan_ayah' => 'Penghasilan Ayah',
            'tahun_lahir_ayah' => 'Tahun Lahir Ayah',
            'pendidikan_ayah' => 'Pendidikan Ayah',
            'alamat_ayah' => 'Alamat Ayah',
            'nama_ibu' => 'Nama Ibu',
            'nik_ibu' => 'NIK Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'penghasilan_ibu' => 'Penghasilan Ibu',
            'tahun_lahir_ibu' => 'Tahun Lahir Ibu',
            'pendidikan_ibu' => 'Pendidikan Ibu',
            'alamat_ibu' => 'Alamat Ibu'
        ];

        // Format Date Helper
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Set output header
        $filename = 'Export_Siswa_Gabungan_' . date('YmdHis') . '.xls';
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $data = [
            'rombel_data' => $rombel_data,
            'selected_fields' => $selected_fields,
            'selected_students' => $selected_students,
            'students' => $students,
            'fields_map' => $fields_map,
            'bulan' => $bulan,
            'tahun_pelajaran' => $tahun_pelajaran,
            'semester' => $semester
        ];

        $this->load->view('export_siswa/excel', $data);
    }
}
