<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Generate_niy extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_generate_niy');
    }

    private function ensurePermissions()
    {
        $code = 'menu_generate_niy';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kepegawaian'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Generate NIY',
                'parent_id' => $parent_id,
                'level' => 2
            ]);

            // Assign to role 1 (Admin) and role 3 (Tenaga Administrasi Sekolah / TAS)
            $this->db->insert('role_permissions', ['role' => 1, 'permission' => $code]);
            $this->db->insert('role_permissions', ['role' => 3, 'permission' => $code]);
        }
    }

    public function index()
    {
        $this->page_data['page']->title = 'Kepegawaian';
        $this->page_data['page']->titleUrl = 'generate_niy';
        $this->page_data['page']->subtitle = 'Generate NIY';
        $this->page_data['page']->subtitleUrl = 'generate_niy';
        $this->page_data['page']->icon = 'solar:user-id-linear';

        // Fetch active PTK list
        $this->db->select('id_ptk, nama_ptk, jenis_kelamin, tgl_sk_pengangkatan, niy, status_pegawai');
        $this->db->from('ptk');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk_list'] = $this->db->get()->result();

        $this->load->view('generate_niy/index', $this->page_data);
    }

    public function get_proposed_niys()
    {
        postAllowed();
        $ptk_data = $this->input->post('ptk_data');

        if (empty($ptk_data) || !is_array($ptk_data)) {
            echo json_encode(['status' => false, 'message' => 'Data input kosong.']);
            return;
        }

        $global_next_seq = $this->getGlobalNextSequence();
        $proposed_niys = [];

        foreach ($ptk_data as $item) {
            $id_ptk = $item['id_ptk'];
            $date = $item['date'];
            $gender = $item['gender'];

            if (empty($date) || empty($gender)) {
                $proposed_niys[$id_ptk] = '-';
                continue;
            }

            $date_clean = str_replace('-', '', $date);
            $gender_code = ($gender === 'Laki-laki') ? '001' : '002';
            $prefix = $date_clean . $gender_code;

            $proposed = $prefix . sprintf('%03d', $global_next_seq);
            $proposed_niys[$id_ptk] = $proposed;

            $global_next_seq++;
        }

        echo json_encode([
            'status' => true,
            'proposed_niys' => $proposed_niys
        ]);
    }

    public function generate()
    {
        postAllowed();
        $ptk_updates = $this->input->post('ptk_updates');

        if (empty($ptk_updates) || !is_array($ptk_updates)) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada PTK yang dipilih.']);
            return;
        }

        $this->db->trans_start();

        $global_next_seq = $this->getGlobalNextSequence();
        $count = 0;

        foreach ($ptk_updates as $item) {
            $id_ptk = $item['id_ptk'];
            $date = $item['date'];

            if (empty($date)) {
                continue;
            }

            $ptk = $this->db->select('jenis_kelamin, nama_ptk')->get_where('ptk', ['id_ptk' => $id_ptk])->row();
            if (!$ptk) {
                continue;
            }

            $date_clean = str_replace('-', '', $date);
            $gender_code = ($ptk->jenis_kelamin === 'Laki-laki') ? '001' : '002';
            $prefix = $date_clean . $gender_code;

            $new_niy = $prefix . sprintf('%03d', $global_next_seq);

            // Update both NIY and SK date
            $this->db->where('id_ptk', $id_ptk)->update('ptk', [
                'niy' => $new_niy,
                'tgl_sk_pengangkatan' => $date
            ]);

            $global_next_seq++;
            $count++;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal memproses pembuatan NIY. Terjadi kesalahan database.']);
        } else {
            $this->activity_model->add(logged('name') . " men-generate NIY untuk $count PTK");
            echo json_encode(['status' => true, 'message' => "$count NIY PTK berhasil digenerate."]);
        }
    }

    private function getGlobalNextSequence()
    {
        $query = $this->db->select('niy')->get('ptk');
        $existing = $query->result_array();
        $max_seq = 0;
        foreach ($existing as $row) {
            $niy = $row['niy'];
            if (strlen($niy) === 14) {
                $seq = (int) substr($niy, -3);
                if ($seq > $max_seq) {
                    $max_seq = $seq;
                }
            }
        }
        return $max_seq + 1;
    }
}
