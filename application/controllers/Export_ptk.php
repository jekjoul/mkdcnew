<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Export_ptk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_export_ptk');
    }

    private function ensurePermissions()
    {
        $code = 'menu_export_ptk';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'menu_alat_khusus'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Export PTK massal',
                'parent_id' => $parent_id,
                'level' => 2
            ]);

            $this->db->insert('role_permissions', ['role' => 1, 'permission' => $code]);
            $this->db->insert('role_permissions', ['role' => 3, 'permission' => $code]);
        }
    }

    public function index()
    {
        $this->page_data['page']->title = 'Kepegawaian';
        $this->page_data['page']->titleUrl = 'export_ptk';
        $this->page_data['page']->subtitle = 'Export PTK massal';
        $this->page_data['page']->subtitleUrl = 'export_ptk';
        $this->page_data['page']->icon = 'solar:download-linear';

        // Define exportable fields grouped by category
        $this->page_data['fields_grouped'] = [
            'Data Pribadi' => [
                'nama_ptk' => 'Nama Lengkap',
                'gelar_depan' => 'Gelar Depan',
                'gelar_belakang' => 'Gelar Belakang',
                'jenis_kelamin' => 'Jenis Kelamin',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'agama' => 'Agama',
                'status_perkawinan' => 'Status Perkawinan',
                'nama_ibu_kandung' => 'Nama Ibu Kandung',
                'nik' => 'NIK'
            ],
            'Kepegawaian' => [
                'niy' => 'NIY',
                'nuptk' => 'NUPTK',
                'no_sk_pengangkatan' => 'No SK Pengangkatan',
                'tgl_sk_pengangkatan' => 'Tgl SK Pengangkatan',
                'status_pegawai' => 'Status Pegawai',
                'penugasan' => 'Penugasan'
            ],
            'Kontak & Alamat' => [
                'email' => 'Email',
                'telepon' => 'No Ponsel',
                'alamat' => 'Alamat',
                'rt' => 'RT',
                'rw' => 'RW'
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

        $this->load->view('export_ptk/index', $this->page_data);
    }

    public function get_ptk()
    {
        postAllowed();
        
        $this->db->select('id_ptk, nama_ptk, nik, niy, penugasan, jenis_kelamin');
        $this->db->from('ptk');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $ptk_list = $this->db->get()->result_array();

        echo json_encode([
            'status' => true,
            'ptk' => $ptk_list
        ]);
    }

    public function export_excel()
    {
        $selected_fields = $this->input->post('fields');
        $selected_ptk = $this->input->post('ptk_ids');

        if (empty($selected_fields) || empty($selected_ptk)) {
            show_error('Data input tidak lengkap. Harap pilih minimal satu field dan minimal satu PTK.', 400, 'Input Error');
            return;
        }

        // Fetch PTK data
        $this->db->select('*');
        $this->db->from('ptk');
        $this->db->where_in('id_ptk', $selected_ptk);
        $this->db->order_by('nama_ptk', 'ASC');
        $ptk_list = $this->db->get()->result_array();

        // Flat fields map for excel headers
        $fields_map = [
            'nama_ptk' => 'Nama Lengkap',
            'gelar_depan' => 'Gelar Depan',
            'gelar_belakang' => 'Gelar Belakang',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'status_perkawinan' => 'Status Perkawinan',
            'nama_ibu_kandung' => 'Nama Ibu Kandung',
            'nik' => 'NIK',
            'niy' => 'NIY',
            'nuptk' => 'NUPTK',
            'no_sk_pengangkatan' => 'No SK Pengangkatan',
            'tgl_sk_pengangkatan' => 'Tgl SK Pengangkatan',
            'status_pegawai' => 'Status Pegawai',
            'penugasan' => 'Penugasan',
            'email' => 'Email',
            'telepon' => 'No Ponsel',
            'alamat' => 'Alamat',
            'rt' => 'RT',
            'rw' => 'RW'
        ];

        // Format Date Helper
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Set output header
        $filename = 'Export_PTK_Massal_' . date('YmdHis') . '.xls';
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $data = [
            'selected_fields' => $selected_fields,
            'selected_ptk' => $selected_ptk,
            'ptk_list' => $ptk_list,
            'fields_map' => $fields_map,
            'bulan' => $bulan
        ];

        $this->load->view('export_ptk/excel', $data);
    }
}
