<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Edit_inline_ptk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_edit_inline_ptk');
    }

    private function ensurePermissions()
    {
        $code = 'menu_edit_inline_ptk';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kepegawaian'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Edit Inline PTK',
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
        $this->page_data['page']->titleUrl = 'edit_inline_ptk';
        $this->page_data['page']->subtitle = 'Edit Inline PTK';
        $this->page_data['page']->subtitleUrl = 'edit_inline_ptk';
        $this->page_data['page']->icon = 'solar:pen-new-square-linear';

        // Define editable fields
        $this->page_data['fields'] = [
            'nama_ptk' => ['label' => 'Nama Lengkap', 'type' => 'text'],
            'gelar_depan' => ['label' => 'Gelar Depan', 'type' => 'text'],
            'gelar_belakang' => ['label' => 'Gelar Belakang', 'type' => 'text'],
            'jenis_kelamin' => ['label' => 'Jenis Kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            'tempat_lahir' => ['label' => 'Tempat Lahir', 'type' => 'text'],
            'tanggal_lahir' => ['label' => 'Tanggal Lahir', 'type' => 'date'],
            'agama' => ['label' => 'Agama', 'type' => 'select', 'options' => ['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan']],
            'status_perkawinan' => ['label' => 'Status Perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            'nama_ibu_kandung' => ['label' => 'Nama Ibu Kandung', 'type' => 'text'],
            'nik' => ['label' => 'NIK', 'type' => 'text'],
            'niy' => ['label' => 'NIY', 'type' => 'text'],
            'nuptk' => ['label' => 'NUPTK', 'type' => 'text'],
            'no_sk_pengangkatan' => ['label' => 'No SK Pengangkatan', 'type' => 'text'],
            'tgl_sk_pengangkatan' => ['label' => 'Tgl SK Pengangkatan', 'type' => 'date'],
            'email' => ['label' => 'Email', 'type' => 'text'],
            'telepon' => ['label' => 'No Ponsel', 'type' => 'text'],
            'status_pegawai' => ['label' => 'Status Pegawai', 'type' => 'select', 'options' => ['GTY/PTY', 'ASN']],
            'penugasan' => ['label' => 'Penugasan', 'type' => 'select', 'options' => ['Guru', 'Guru & TAS', 'TAS', 'Kepala Sekolah']],
            'alamat' => ['label' => 'Alamat', 'type' => 'text'],
            'rt' => ['label' => 'RT', 'type' => 'text'],
            'rw' => ['label' => 'RW', 'type' => 'text']
        ];

        $this->load->view('edit_inline_ptk/index', $this->page_data);
    }

    public function get_ptk()
    {
        postAllowed();
        
        $this->db->select('*');
        $this->db->from('ptk');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $ptk_list = $this->db->get()->result_array();

        echo json_encode([
            'status' => true,
            'ptk' => $ptk_list
        ]);
    }

    public function update_batch()
    {
        postAllowed();
        $ptk_data = $this->input->post('ptk');

        if (empty($ptk_data) || !is_array($ptk_data)) {
            echo json_encode(['status' => false, 'message' => 'Data input tidak lengkap atau tidak valid.']);
            return;
        }

        $this->db->trans_start();

        $count = 0;
        foreach ($ptk_data as $id_ptk => $fields) {
            // Verify PTK exists and is active
            $exists = $this->db->get_where('ptk', [
                'id_ptk' => $id_ptk,
                'status_keaktifan' => 'Aktif'
            ])->num_rows();

            if ($exists > 0 && !empty($fields)) {
                $this->db->where('id_ptk', $id_ptk)->update('ptk', $fields);
                $count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui data PTK. Terjadi kesalahan database.']);
        } else {
            $this->activity_model->add(logged('name') . " melakukan update inline untuk $count data PTK.");
            echo json_encode(['status' => true, 'message' => "Berhasil memperbarui $count data PTK."]);
        }
    }
}
