<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tutup_semester extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tutup_semester_model', 'tutup_model');
        
        // Memastikan hanya Role Admin / Super Admin yang bisa mengakses
        if (logged('role') != '1' && logged('role') != 'admin') {
            show_error('Anda tidak memiliki hak akses untuk mengakses Modul Penutupan Semester.', 403, 'Akses Ditolak');
        }
    }

    public function index()
    {
        $this->setPage('Pengaturan', 'Penutupan Semester & Rekapitulasi Akhir', 'tutup_semester', 'solar:lock-keyhole-bold');

        $active_semester = $this->tutup_model->getActiveSemester();
        $history_logs    = $this->db->order_by('id_log', 'DESC')->get('tutup_semester_log')->result();

        $this->page_data['active_semester'] = $active_semester;
        $this->page_data['history_logs']    = $history_logs;

        $this->load->view('tutup_semester/index', $this->page_data);
    }

    public function proses_tutup_semester()
    {
        postAllowed();

        $active_semester = $this->tutup_model->getActiveSemester();
        if (!$active_semester) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal: Tidak ditemukan Tahun Pelajaran & Semester yang berstatus Aktif.');
            redirect('tutup_semester');
        }

        $expected_phrase = 'TUTUP SEMESTER ' . strtoupper($active_semester->semester) . ' ' . $active_semester->tahun_pelajaran;
        $confirm_phrase  = trim(post('confirm_phrase'));
        $admin_password  = post('admin_password');

        // Lapis 2: Verifikasi Frasa Konfirmasi
        if (strtoupper($confirm_phrase) !== strtoupper($expected_phrase)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal: Frasa konfirmasi yang Anda ketikkan tidak cocok. Harap ketik persis: ' . $expected_phrase);
            redirect('tutup_semester');
        }

        // Lapis 3: Verifikasi Password Admin yang sedang login
        $logged_user = signedInUser();
        $password_valid = false;

        if (function_exists('password_verify')) {
            $password_valid = password_verify($admin_password, $logged_user->password);
        }
        if (!$password_valid && md5($admin_password) === $logged_user->password) {
            $password_valid = true;
        }
        if (!$password_valid && $admin_password === $logged_user->password) {
            $password_valid = true;
        }

        if (!$password_valid) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal: Kata sandi Admin yang Anda masukkan salah. Proses penutupan semester dibatalkan demi keamanan.');
            redirect('tutup_semester');
        }

        // Jalankan Proses Penutupan dalam Transaksi MySQL
        $this->db->trans_start();

        // 1. Rekap Absensi Agenda Siswa
        $rekap_agenda = $this->tutup_model->rekapAbsensiAgendaSiswa($active_semester->id_tahun_pelajaran);

        // 2. Rekap Presensi Fingerprint Siswa
        $rekap_fp_siswa = $this->tutup_model->rekapFingerprintSiswa($active_semester->id_tahun_pelajaran);

        // 3. Rekap Presensi Fingerprint Guru Bulanan
        $rekap_fp_guru = $this->tutup_model->rekapFingerprintGuruBulanan(date('Y'));

        // 4. Lock Agenda Pembelajaran
        $locked_agendas = $this->tutup_model->lockAgendaPembelajaran($active_semester->id_tahun_pelajaran);

        // 5. Deaktivasi Pembelajaran & Jadwal
        $this->tutup_model->deaktivasiPembelajaranDanJadwal($active_semester->id_tahun_pelajaran);

        // 6. Deaktivasi Data Nilai
        $this->tutup_model->deaktivasiNilai($active_semester->id_tahun_pelajaran);

        // 7. Deaktivasi Tahun Pelajaran Aktif
        $this->tutup_model->deaktivasiTahunPelajaran($active_semester->id_tahun_pelajaran);

        // 8. Pencatatan Audit Log Penutupan Semester
        $log_data = [
            'id_tahun_pelajaran' => $active_semester->id_tahun_pelajaran,
            'tahun_pelajaran'    => $active_semester->tahun_pelajaran,
            'semester'           => $active_semester->semester,
            'executor_user_id'   => $logged_user->id,
            'executed_at'        => date('Y-m-d H:i:s'),
            'details_json'       => json_encode([
                'rekap_agenda'   => $rekap_agenda,
                'rekap_fp_siswa' => $rekap_fp_siswa,
                'rekap_fp_guru'  => $rekap_fp_guru,
                'locked_agendas' => $locked_agendas,
                'ip_address'     => $this->input->ip_address()
            ])
        ];
        $this->tutup_model->logPenutupanSemester($log_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Terjadi kesalahan sistem database saat memproses penutupan semester. Seluruh perubahan telah dibatalkan (rollbacked).');
        } else {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'BERHASIL! Penutupan Semester ' . $active_semester->semester . ' ' . $active_semester->tahun_pelajaran . ' telah selesai diproses dan seluruh data akademik aktif telah direkap & dinonaktifkan.');
        }

        redirect('tutup_semester');
    }
}
