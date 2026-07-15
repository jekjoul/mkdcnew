<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Edit_inline_siswa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_edit_inline_siswa');
    }

    private function ensurePermissions()
    {
        $code = 'menu_edit_inline_siswa';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kesiswaan'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Edit Inline Siswa',
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
        $this->page_data['page']->titleUrl = 'edit_inline_siswa';
        $this->page_data['page']->subtitle = 'Edit Inline Siswa';
        $this->page_data['page']->subtitleUrl = 'edit_inline_siswa';
        $this->page_data['page']->icon = 'solar:pen-new-square-linear';

        // 1. Get active Rombel / Pembelajaran
        $this->db->select('p.id_pembelajaran, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel');
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

        // 2. Define editable fields
        $this->page_data['fields'] = [
            'nama_siswa' => ['label' => 'Nama Lengkap', 'type' => 'text'],
            'nisn' => ['label' => 'NISN', 'type' => 'text'],
            'nipd' => ['label' => 'NIPD', 'type' => 'text'],
            'nik' => ['label' => 'NIK', 'type' => 'text'],
            'no_kk' => ['label' => 'No Kartu Keluarga', 'type' => 'text'],
            'jenis_kelamin' => ['label' => 'Jenis Kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            'tempat_lahir' => ['label' => 'Tempat Lahir', 'type' => 'text'],
            'tanggal_lahir' => ['label' => 'Tanggal Lahir', 'type' => 'date'],
            'agama' => ['label' => 'Agama', 'type' => 'select', 'options' => ['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan']],
            'telepon' => ['label' => 'Telepon', 'type' => 'text'],
            'email' => ['label' => 'Email', 'type' => 'text'],
            'no_ijazah' => ['label' => 'No Ijazah', 'type' => 'text'],
            'sekolah_asal' => ['label' => 'Sekolah Asal', 'type' => 'text'],
            'anak_ke' => ['label' => 'Anak Ke', 'type' => 'number'],
            'alamat' => ['label' => 'Alamat', 'type' => 'text'],
            'rt' => ['label' => 'RT', 'type' => 'text'],
            'rw' => ['label' => 'RW', 'type' => 'text']
        ];

        $this->load->view('edit_inline_siswa/index', $this->page_data);
    }

    public function get_students()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');

        if (empty($id_pembelajaran)) {
            echo json_encode(['status' => false, 'message' => 'Rombel wajib dipilih.']);
            return;
        }

        $this->db->select('s.*');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->where('ps.id_pembelajaran', $id_pembelajaran);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $students = $this->db->get()->result_array();

        echo json_encode([
            'status' => true,
            'students' => $students
        ]);
    }

    public function update_batch()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');
        $students_data = $this->input->post('students');

        if (empty($id_pembelajaran) || empty($students_data) || !is_array($students_data)) {
            echo json_encode(['status' => false, 'message' => 'Data input tidak lengkap atau tidak valid.']);
            return;
        }

        $this->db->trans_start();

        $count = 0;
        foreach ($students_data as $id_siswa => $fields) {
            // Verify student belongs to this rombel
            $exists = $this->db->get_where('pembelajaran_siswa', [
                'id_pembelajaran' => $id_pembelajaran,
                'peserta_didik_id' => $id_siswa
            ])->num_rows();

            if ($exists > 0 && !empty($fields)) {
                $this->db->where('id_siswa', $id_siswa)->update('siswa', $fields);
                $count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui data siswa. Terjadi kesalahan database.']);
        } else {
            $this->activity_model->add(logged('name') . " melakukan update inline untuk $count data siswa.");
            echo json_encode(['status' => true, 'message' => "Berhasil memperbarui $count data siswa."]);
        }
    }
}
