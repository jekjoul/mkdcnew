<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pencetakan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        ifPermissions('menu_pencetakan');
    }

    public function absensi()
    {
        ifPermissions('pencetakan_absensi');
        $id_pembelajaran = $this->input->get('id_pembelajaran');

        if ($id_pembelajaran) {
            // Load detail pembelajaran
            $this->db->select('p.*, l.nama_lembaga, l.npsn, l.alamat, l.logo, l.bentuk_pendidikan, l.telepon, l.email, l.website, l.no_sk_akreditasi, l.akreditasi, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, w.nama_ptk as nama_walikelas');
            $this->db->from('pembelajaran p');
            $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
            $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
            $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
            $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
            $this->db->join('ptk w', 'p.id_ptk_wali = w.id_ptk', 'left');
            $this->db->where('p.id_pembelajaran', $id_pembelajaran);
            $pembelajaran = $this->db->get()->row();

            if (!$pembelajaran) {
                show_404();
            }

            // Get students in this class
            $show_menginduk = $this->input->get('show_menginduk') == '1';
            $menginduk_ids  = [];
            if (!$show_menginduk && $this->db->table_exists('kelas_jauh_siswa')) {
                $q_kj = $this->db->select('id_siswa')->get('kelas_jauh_siswa');
                if ($q_kj && $q_kj->num_rows() > 0) {
                    $menginduk_ids = array_column($q_kj->result_array(), 'id_siswa');
                }
            }

            $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin');
            $this->db->from('pembelajaran_siswa ps');
            $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
            $this->db->where('ps.id_pembelajaran', $id_pembelajaran);
            $this->db->where('s.status_keaktifan', 'Aktif');

            if (!empty($menginduk_ids)) {
                $this->db->where_not_in('s.id_siswa', $menginduk_ids);
            }

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

            // Load Kop Surat aktif
            $kop = $this->db->get_where('surat_kop', ['status' => 'Aktif'])->row();
            if (!$kop) {
                $kop = $this->db->get('surat_kop')->row();
            }

            $has_submit = $this->input->get('id_pembelajaran') !== null;
            $pakai_kop = $has_submit ? ($this->input->get('pakai_kop') === '1') : true;
            $pakai_ttd = $has_submit ? ($this->input->get('pakai_ttd') === '1') : true;
            $pakai_jumlah = $has_submit ? ($this->input->get('pakai_jumlah') === '1') : true;
            $size = $this->input->get('size') ?: 'landscape';

            $this->page_data['pembelajaran'] = $pembelajaran;
            $this->page_data['students'] = $students;
            $this->page_data['kepsek'] = $kepsek ?: '...........................';
            $this->page_data['kop'] = $kop;
            $this->page_data['pakai_kop'] = $pakai_kop;
            $this->page_data['pakai_ttd'] = $pakai_ttd;
            $this->page_data['pakai_jumlah'] = $pakai_jumlah;
            $this->page_data['size'] = $size;

            $format = $this->input->get('format');
            if ($format === 'pdf') {
                $this->page_data['is_pdf'] = true;
                $html = $this->load->view('pencetakan/v_absensi_print', $this->page_data, true);
                
                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                
                $filename = 'Daftar_Hadir_Siswa_' . str_replace(' ', '_', $pembelajaran->nama_tingkat . '_' . $pembelajaran->nama_rombel) . '.pdf';
                $dompdf->stream($filename, array("Attachment" => 1));
                return;
            } elseif ($format === 'excel') {
                $filename = 'Daftar_Hadir_Siswa_' . str_replace(' ', '_', $pembelajaran->nama_tingkat . '_' . $pembelajaran->nama_rombel) . '.xls';
                header("Content-Type: application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=\"$filename\"");
                header("Cache-Control: max-age=0");
                
                $this->page_data['is_excel'] = true;
                $this->load->view('pencetakan/v_absensi_excel', $this->page_data);
                return;
            } else {
                $this->page_data['is_pdf'] = false;
                $this->load->view('pencetakan/v_absensi_print', $this->page_data);
            }
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
            $this->db->order_by('t.tingkat_angka', 'ASC');
            $this->db->order_by('l.nama_lembaga', 'ASC');
            $this->db->order_by('r.nama_rombel', 'ASC');
            $this->page_data['pembelajaran_list'] = $this->db->get()->result();

            $this->load->view('pencetakan/v_absensi_filter', $this->page_data);
        }
    }
}
