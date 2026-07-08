<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pencetakan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        ifPermissions('menu_surat_menyurat');
    }

    public function absensi()
    {
        $id_pembelajaran = $this->input->get('id_pembelajaran');

        if ($id_pembelajaran) {
            // Load detail pembelajaran
            $this->db->select('p.*, l.nama_lembaga, l.npsn, l.alamat, l.logo, l.bentuk_pendidikan, l.telepon, l.email, l.website, l.no_sk_akreditasi, l.akreditasi, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
            $this->db->from('pembelajaran p');
            $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
            $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
            $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
            $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
            $this->db->where('p.id_pembelajaran', $id_pembelajaran);
            $pembelajaran = $this->db->get()->row();

            if (!$pembelajaran) {
                show_404();
            }

            // Get students in this class
            $this->db->select('s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin');
            $this->db->from('pembelajaran_siswa ps');
            $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
            $this->db->where('ps.id_pembelajaran', $id_pembelajaran);
            $this->db->where('s.status_keaktifan', 'Aktif');
            $this->db->order_by('s.nama_siswa', 'ASC');
            $students = $this->db->get()->result();

            // Load Kepala Sekolah details
            $kepsek = null;
            if ($pembelajaran->id_lembaga) {
                $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $pembelajaran->id_lembaga])->row();
                if ($lembaga && $lembaga->id_ptk_kepsek) {
                    $ptk = $this->db->get_where('ptk', ['id_ptk' => $lembaga->id_ptk_kepsek])->row();
                    if ($ptk) {
                        $kepsek = $ptk->nama_ptk;
                    }
                }
            }

            $this->page_data['pembelajaran'] = $pembelajaran;
            $this->page_data['students'] = $students;
            $this->page_data['kepsek'] = $kepsek ?: '...........................';
            $this->load->view('pencetakan/v_absensi_print', $this->page_data);
        } else {
            // Display filter page
            $this->page_data['page']->title = 'Pencetakan';
            $this->page_data['page']->titleUrl = 'pencetakan/absensi';
            $this->page_data['page']->subtitle = 'Cetak Absensi Rombel';
            $this->page_data['page']->subtitleUrl = 'pencetakan/absensi';
            $this->page_data['page']->icon = 'solar:printer-linear';

            // Get all active classes/pembelajaran
            $this->db->select('p.id_pembelajaran, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
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
            $this->page_data['pembelajaran_list'] = $this->db->get()->result();

            $this->load->view('pencetakan/v_absensi_filter', $this->page_data);
        }
    }
}
