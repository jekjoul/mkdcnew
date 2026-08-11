<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agenda_pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Agenda_pembelajaran_model', 'agenda_model');
        $this->load->model('Perangkat_pembelajaran_model', 'perangkat_model');
        $this->agenda_model->ensureTables();
    }

    public function index()
    {
        $this->loadAgendaList('Aktif');
    }

    public function nonaktif()
    {
        $this->loadAgendaList('Nonaktif');
    }

    private function loadAgendaList($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Agenda Pembelajaran';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'agenda_pembelajaran/nonaktif' : 'agenda_pembelajaran';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Agenda Pembelajaran Tidak Aktif' : 'Agenda Pembelajaran Harian';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'agenda_pembelajaran/nonaktif' : 'agenda_pembelajaran';
        $this->page_data['page']->icon = 'solar:calendar-date-linear';

        $userId = logged('id');
        $user = $this->db->get_where('users', ['id' => $userId])->row();
        $ptk_id = $user ? (int) $user->id_ptk : 0;

        $user_roles = [];
        foreach ($this->db->get_where('user_roles', ['user_id' => $userId])->result() as $ur) {
            $r_row = $this->db->get_where('roles', ['id' => $ur->role_id])->row();
            if ($r_row) {
                $user_roles[] = strtolower((string) $r_row->title);
            }
        }
        $is_admin = in_array('admin', $user_roles, true) || logged('role') == 1 || hasPermissions('pembelajaran_list');

        if (!$is_admin && $ptk_id > 0) {
            $items = $this->agenda_model->getGuruItems($ptk_id, $status_tahun);
        } else {
            $items = $this->agenda_model->getAdminItems($status_tahun);
        }

        $this->page_data['items'] = $items;
        $this->page_data['is_nonaktif'] = $is_nonaktif;
        $this->page_data['teachers'] = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk')->result();
        
        $this->load->view('agenda_pembelajaran/list', $this->page_data);
    }

    public function detail($id_pembelajaran_mapel)
    {
        $item = $this->agenda_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $agenda = $this->agenda_model->getAgendaByMapel($id_pembelajaran_mapel);
        $schedule_status = $this->agenda_model->detectScheduleStatus($id_pembelajaran_mapel);
        $templates = $this->agenda_model->getAvailableTemplates($id_pembelajaran_mapel);
        $teachers = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk')->result();

        $this->page_data['page']->title = 'Agenda Pembelajaran';
        $this->page_data['page']->titleUrl = 'agenda_pembelajaran';
        $this->page_data['page']->subtitle = 'Kelola Agenda Harian';
        $this->page_data['page']->subtitleUrl = 'agenda_pembelajaran/detail/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:calendar-date-linear';

        $this->page_data['item'] = $item;
        $this->page_data['agenda'] = $agenda;
        $this->page_data['schedule_status'] = $schedule_status;
        $this->page_data['templates'] = $templates;
        $this->page_data['teachers'] = $teachers;

        $this->page_data['back_url'] = url('agenda_pembelajaran');
        $this->page_data['save_agenda_url'] = url('agenda_pembelajaran/simpan_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['update_judul_url'] = url('agenda_pembelajaran/simpan_judul/' . $id_pembelajaran_mapel);
        $this->page_data['takeover_url'] = url('agenda_pembelajaran/takeover/' . $id_pembelajaran_mapel);
        $this->page_data['pilih_template_url'] = url('agenda_pembelajaran/pilih_template/' . $id_pembelajaran_mapel);
        $this->page_data['resync_jadwal_url'] = url('agenda_pembelajaran/resync_jadwal/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_url'] = url('agenda_pembelajaran/generate_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_ai_url'] = url('agenda_pembelajaran/generate_agenda_ai/' . $id_pembelajaran_mapel);

        $this->load->view('agenda_pembelajaran/detail', $this->page_data);
    }

    public function simpan_judul($id_pembelajaran_mapel)
    {
        postAllowed();
        $judul = post('judul_agenda');
        if (!empty($judul)) {
            $this->agenda_model->updateJudulAgenda($id_pembelajaran_mapel, $judul);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Judul agenda harian berhasil diperbarui.');
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function takeover($id_pembelajaran_mapel)
    {
        postAllowed();
        $new_ptk_id = (int) post('id_ptk');
        if ($new_ptk_id > 0) {
            $success = $this->agenda_model->takeoverAgenda($id_pembelajaran_mapel, $new_ptk_id);
            if ($success) {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Agenda harian berhasil ditake-over oleh guru pengampu baru. Riwayat absensi dan jurnal tetap aman.');
            }
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function pilih_template($id_pembelajaran_mapel)
    {
        postAllowed();
        $source_id = (int) post('source_id_pembelajaran_mapel');
        if ($source_id > 0) {
            $success = $this->agenda_model->copyAndAdaptAgenda($id_pembelajaran_mapel, $source_id);
            if ($success) {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Agenda harian berhasil disalin dan disesuaikan tanggal/jamnya secara otomatis dengan jadwal rombel ini.');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Gagal menyalin agenda. Pastikan jadwal pelajaran rombel target dan hari efektif sudah dikonfigurasi.');
            }
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function resync_jadwal($id_pembelajaran_mapel)
    {
        postAllowed();
        $success = $this->agenda_model->syncSchedule($id_pembelajaran_mapel);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Jadwal tanggal & jam agenda berstatus "Belum" berhasil diselaraskan dengan susunan jadwal pelajaran baru.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyelaraskan jadwal. Pastikan jadwal pelajaran rombel dan hari efektif sudah dikonfigurasi.');
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function simpan_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        $agenda_post = post('agenda');
        if (is_array($agenda_post)) {
            $this->perangkat_model->saveAgenda($id_pembelajaran_mapel, $agenda_post);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Data agenda pembelajaran harian berhasil disimpan.');
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function generate_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        $success = $this->perangkat_model->generateAgenda($id_pembelajaran_mapel);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian berhasil digenerate berdasarkan jadwal pelajaran dan hari aktif.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal generate agenda. Pastikan jadwal pelajaran dan hari aktif telah diatur.');
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function generate_agenda_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        $item = $this->agenda_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $slots_by_day = [];
        $schedules = $this->db->get_where('jadwal_pelajaran_item', ['id_pembelajaran' => $item->id_pembelajaran])->result();
        foreach ($schedules as $sched) {
            $slots_by_day[strtolower($sched->hari)] = true;
        }

        $active_days = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where('status', 'Efektif')
            ->get('pembelajaran_hari_efektif')->result();

        $meetings_count = 0;
        $day_names = [0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'];
        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            if (isset($slots_by_day[$day_names[$w]])) {
                $meetings_count++;
            }
        }

        if ($meetings_count === 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal generate AI. Tidak ditemukan hari aktif belajar yang sesuai dengan jadwal mingguan kelas ini.');
            redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
        }

        $modul_ajar_list = $this->db->get_where('perangkat_pembelajaran_modul_ajar', [
            'id_tahun_pelajaran' => $item->id_tahun_pelajaran,
            'id_tingkat_sekolah' => $item->id_tingkat_sekolah,
            'id_mapel' => $item->id_mapel
        ])->result();
        $modul_list_str = "";
        if (!empty($modul_ajar_list)) {
            $labels = [];
            foreach ($modul_ajar_list as $m) {
                $labels[] = "- " . $m->label;
            }
            $modul_list_str = implode("\n", $labels);
        }

        $this->load->library('GoogleAI_Helper');
        $ai_res = $this->googleai_helper->generateAgenda($item->nama_mapel, $item->nama_tingkat, $meetings_count, $modul_list_str);

        if (isset($ai_res['error'])) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal memproses Google AI (Gemini): ' . $ai_res['error']);
            redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
        }

        $success = $this->perangkat_model->generateAgendaAI($id_pembelajaran_mapel, $ai_res);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian otomatis berbasis Kurikulum Merdeka berhasil dibuat oleh Google AI.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyimpan hasil AI ke agenda.');
        }
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }
}
