<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Input_rekam_medis extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensurePermissions();
        ifPermissions('menu_edit_inline_siswa');
    }

    private function ensurePermissions()
    {
        $code = 'menu_input_rekam_medis';
        $exists = $this->db->get_where('permissions', ['code' => $code])->num_rows();
        if ($exists == 0) {
            $parent = $this->db->get_where('permissions', ['code' => 'group_kesiswaan'])->row();
            $parent_id = $parent ? $parent->id : NULL;

            $this->db->insert('permissions', [
                'code' => $code,
                'title' => 'Input Rekam Medis',
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
        $this->page_data['page']->titleUrl = 'input_rekam_medis';
        $this->page_data['page']->subtitle = 'Input Rekam Medis';
        $this->page_data['page']->subtitleUrl = 'input_rekam_medis';
        $this->page_data['page']->icon = 'solar:heart-pulse-linear';

        // Get active Rombel / Pembelajaran
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

        $this->load->view('input_rekam_medis/index', $this->page_data);
    }

    public function get_students()
    {
        postAllowed();
        $id_pembelajaran = $this->input->post('id_pembelajaran');
        $tanggal = $this->input->post('tanggal') ?: date('Y-m-d');

        if (empty($id_pembelajaran)) {
            echo json_encode(['status' => false, 'message' => 'Rombel wajib dipilih.']);
            return;
        }

        $this->db->select('s.id_siswa, s.nama_siswa, rm.tinggi_badan, rm.berat_badan, rm.lingkar_kepala, rm.lingkar_perut');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('siswa_rekam_medis rm', 'rm.id_siswa = s.id_siswa AND rm.tanggal = ' . $this->db->escape($tanggal), 'left');
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
        $tanggal = $this->input->post('tanggal');
        $students_data = $this->input->post('students');

        if (empty($id_pembelajaran) || empty($tanggal) || empty($students_data) || !is_array($students_data)) {
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

            if ($exists > 0) {
                $has_data = (!empty($fields['tinggi_badan']) || !empty($fields['berat_badan']) || !empty($fields['lingkar_kepala']) || !empty($fields['lingkar_perut']));
                
                $existing = $this->db->get_where('siswa_rekam_medis', [
                    'id_siswa' => $id_siswa,
                    'tanggal' => $tanggal
                ])->row();

                if ($existing) {
                    if ($has_data) {
                        $this->db->where('id_rekam_medis', $existing->id_rekam_medis)->update('siswa_rekam_medis', [
                            'tinggi_badan' => $fields['tinggi_badan'] ?: null,
                            'berat_badan' => $fields['berat_badan'] ?: null,
                            'lingkar_kepala' => $fields['lingkar_kepala'] ?: null,
                            'lingkar_perut' => $fields['lingkar_perut'] ?: null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        $count++;
                    } else {
                        $this->db->delete('siswa_rekam_medis', ['id_rekam_medis' => $existing->id_rekam_medis]);
                        $count++;
                    }
                } else {
                    if ($has_data) {
                        $this->db->insert('siswa_rekam_medis', [
                            'id_siswa' => $id_siswa,
                            'tanggal' => $tanggal,
                            'tinggi_badan' => $fields['tinggi_badan'] ?: null,
                            'berat_badan' => $fields['berat_badan'] ?: null,
                            'lingkar_kepala' => $fields['lingkar_kepala'] ?: null,
                            'lingkar_perut' => $fields['lingkar_perut'] ?: null,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        $count++;
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui data rekam medis. Terjadi kesalahan database.']);
        } else {
            $this->activity_model->add(logged('name') . " melakukan update inline rekam medis untuk $count data siswa pada tanggal $tanggal.");
            echo json_encode(['status' => true, 'message' => "Berhasil memperbarui $count data rekam medis siswa."]);
        }
    }
}
