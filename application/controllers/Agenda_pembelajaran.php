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

        $selected_ptk = (int) $this->input->get('id_ptk');

        if (!$is_admin && $ptk_id > 0) {
            $items = $this->agenda_model->getGuruItems($ptk_id, $status_tahun);
        } else {
            $items = $this->agenda_model->getAdminItems($status_tahun, $selected_ptk);
        }

        $this->page_data['items'] = $items;
        $this->page_data['selected_ptk'] = $selected_ptk;
        $this->page_data['is_nonaktif'] = $is_nonaktif;
        $this->page_data['teachers'] = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk')->result();
        
        $filter_ptk_available = (!$is_admin && $ptk_id > 0) ? $ptk_id : $selected_ptk;
        $this->page_data['available_mapels'] = $this->agenda_model->getAvailableMapelForGuru($filter_ptk_available, $status_tahun);

        $this->load->view('agenda_pembelajaran/list', $this->page_data);
    }

    public function simpan_agenda_baru()
    {
        $id_pembelajaran_mapel = (int) $this->input->post('id_pembelajaran_mapel');
        $judul_agenda          = trim((string) $this->input->post('judul_agenda'));
        $redirect_to           = $this->input->post('redirect_to');

        if (!$id_pembelajaran_mapel || empty($judul_agenda)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Harap pilih mata pelajaran dan isi judul agenda pembelajaran!');
            redirect(!empty($redirect_to) ? $redirect_to : 'agenda_pembelajaran');
            return;
        }

        $pm_row = $this->db->get_where('pembelajaran_mapel', ['id_pembelajaran_mapel' => $id_pembelajaran_mapel])->row();
        if (!$pm_row) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Mata pelajaran tidak ditemukan.');
            redirect(!empty($redirect_to) ? $redirect_to : 'agenda_pembelajaran');
            return;
        }

        $this->db->where('id_pembelajaran_mapel', $id_pembelajaran_mapel);
        $this->db->update('pembelajaran_mapel', ['judul_agenda' => $judul_agenda]);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Agenda Pembelajaran "' . html_escape($judul_agenda) . '" berhasil disimpan!');

        if (!empty($redirect_to)) {
            redirect($redirect_to);
        } else {
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer && strpos($referer, 'guru/pengaturan_agenda') !== false) {
                redirect('guru/pengaturan_agenda');
            } else {
                redirect('agenda_pembelajaran');
            }
        }
    }

    public function hapus_header($id_pembelajaran_mapel = 0)
    {
        $id_pembelajaran_mapel = (int) $id_pembelajaran_mapel;
        if ($id_pembelajaran_mapel > 0) {
            $this->db->where('id_pembelajaran_mapel', $id_pembelajaran_mapel);
            $this->db->update('pembelajaran_mapel', ['judul_agenda' => NULL]);

            $this->db->where('id_pembelajaran_mapel', $id_pembelajaran_mapel);
            $this->db->delete('agenda_pembelajaran');

            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda Pembelajaran berhasil dihapus.');
        }

        $referer = $this->input->server('HTTP_REFERER');
        if ($referer && strpos($referer, 'guru/pengaturan_agenda') !== false) {
            redirect('guru/pengaturan_agenda');
        } else {
            redirect('agenda_pembelajaran');
        }
    }

    public function ptk($id_ptk = null)
    {
        $userId = logged('id');
        $user = $this->db->get_where('users', ['id' => $userId])->row();
        $user_ptk_id = $user ? (int) $user->id_ptk : 0;

        $user_roles = [];
        foreach ($this->db->get_where('user_roles', ['user_id' => $userId])->result() as $ur) {
            $r_row = $this->db->get_where('roles', ['id' => $ur->role_id])->row();
            if ($r_row) {
                $user_roles[] = strtolower((string) $r_row->title);
            }
        }
        $is_admin = in_array('admin', $user_roles, true) || logged('role') == 1 || hasPermissions('pembelajaran_list');

        if (!$is_admin && $user_ptk_id > 0) {
            $id_ptk = $user_ptk_id;
        }

        $id_ptk = (int) ($id_ptk ?: $this->input->get('id_ptk'));

        if (!$id_ptk) {
            // Tampilkan rekap per Guru Pengampu
            $this->page_data['page']->title = 'Agenda Pembelajaran per Guru Pengampu';
            $this->page_data['page']->titleUrl = 'agenda_pembelajaran/ptk';
            $this->page_data['page']->subtitle = 'Rekapitulasi Agenda Harian Seluruh Guru Pengampu';
            $this->page_data['page']->subtitleUrl = 'agenda_pembelajaran/ptk';
            $this->page_data['page']->icon = 'solar:users-group-two-rounded-bold';

            $this->page_data['ptk_summary'] = $this->agenda_model->getPtkAgendaSummary('Aktif');
            $this->load->view('agenda_pembelajaran/ptk_summary', $this->page_data);
            return;
        }

        $ptk_row = $this->db->get_where('ptk', ['id_ptk' => $id_ptk])->row();
        if (!$ptk_row) {
            show_404();
        }

        $status = $this->input->get('status');
        $bulan  = $this->input->get('bulan');
        $id_tp  = $this->input->get('id_tahun_pelajaran');

        $agendas = $this->agenda_model->getAgendaHarianPerPtk($id_ptk, $id_tp, $status, $bulan);
        $guru_mapel_list = $this->agenda_model->getGuruItems($id_ptk, 'Aktif');
        if (empty($guru_mapel_list)) {
            $guru_mapel_list = $this->agenda_model->getGuruItems($id_ptk, null);
        }
        $tp_active = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif'])->row();

        $this->page_data['page']->title = 'Agenda Harian Guru Pengampu';
        $this->page_data['page']->titleUrl = 'agenda_pembelajaran/ptk/' . $id_ptk;
        $this->page_data['page']->subtitle = 'Daftar Agenda Harian Guru Pengampu: ' . $ptk_row->nama_ptk;
        $this->page_data['page']->subtitleUrl = 'agenda_pembelajaran/ptk/' . $id_ptk;
        $this->page_data['page']->icon = 'solar:calendar-date-bold';

        $this->page_data['ptk'] = $ptk_row;
        $this->page_data['agendas'] = $agendas;
        $this->page_data['guru_mapel_list'] = $guru_mapel_list;
        $this->page_data['selected_status'] = $status;
        $this->page_data['selected_bulan'] = $bulan;
        $this->page_data['tp_active'] = $tp_active;
        $this->page_data['teachers'] = $this->db->order_by('nama_ptk', 'ASC')->get('ptk')->result();

        $this->load->view('agenda_pembelajaran/ptk_detail', $this->page_data);
    }

    public function cetak_ptk($id_ptk)
    {
        $ptk_row = $this->db->get_where('ptk', ['id_ptk' => (int) $id_ptk])->row();
        if (!$ptk_row) {
            show_404();
        }

        $status = $this->input->get('status');
        $bulan  = $this->input->get('bulan');
        $id_tp  = $this->input->get('id_tahun_pelajaran');

        $agendas = $this->agenda_model->getAgendaHarianPerPtk($id_ptk, $id_tp, $status, $bulan);
        $tp_active = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif'])->row();
        $lembaga = $this->db->get('lembaga')->row();

        $this->page_data['ptk'] = $ptk_row;
        $this->page_data['agendas'] = $agendas;
        $this->page_data['tp_active'] = $tp_active;
        $this->page_data['lembaga'] = $lembaga;

        $this->load->view('agenda_pembelajaran/cetak_ptk', $this->page_data);
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

    public function simpan_item_agenda_modal()
    {
        postAllowed();
        $id_agenda = (int) post('id_agenda');
        $id_pembelajaran_mapel = (int) post('id_pembelajaran_mapel');

        $item_agenda = $this->agenda_model->getItemAgenda($id_agenda);
        if (!$item_agenda) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Item agenda tidak ditemukan.');
            redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
            return;
        }

        $materi   = post('materi');
        $kegiatan = post('kegiatan');
        $status   = post('status');
        $catatan  = post('catatan');

        $media_list = json_decode($item_agenda->media_files ?: '[]', true) ?: [];

        $link_urls   = post('media_link');
        $link_titles = post('media_title');
        if (!empty($link_urls) && is_array($link_urls)) {
            foreach ($link_urls as $idx => $url_val) {
                $url_clean = trim((string)$url_val);
                if (!empty($url_clean)) {
                    $title_clean = !empty($link_titles[$idx]) ? trim((string)$link_titles[$idx]) : 'Link Media Pembelajaran';
                    $media_list[] = [
                        'type' => 'link',
                        'title' => $title_clean,
                        'url' => $url_clean
                    ];
                }
            }
        }

        if (!empty($_FILES['media_file']['name'][0])) {
            $upload_dir = './uploads/agenda_media/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filesCount = count($_FILES['media_file']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                if (!empty($_FILES['media_file']['name'][$i])) {
                    $_FILES['file']['name']     = $_FILES['media_file']['name'][$i];
                    $_FILES['file']['type']     = $_FILES['media_file']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['media_file']['tmp_name'][$i];
                    $_FILES['file']['error']    = $_FILES['media_file']['error'][$i];
                    $_FILES['file']['size']     = $_FILES['media_file']['size'][$i];

                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $new_name = 'agenda_' . $id_agenda . '_' . time() . '_' . rand(100, 999) . '.' . $ext;

                    $config['upload_path']   = $upload_dir;
                    $config['allowed_types'] = 'pdf|doc|docx|ppt|pptx|xls|xlsx|jpg|jpeg|png|gif|zip|rar|mp4|webm|txt';
                    $config['file_name']     = $new_name;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file')) {
                        $uploadData = $this->upload->data();
                        $media_list[] = [
                            'type' => 'file',
                            'title' => $_FILES['media_file']['name'][$i],
                            'file_name' => $uploadData['file_name'],
                            'file_size' => $uploadData['file_size']
                        ];
                    }
                }
            }
        }

        $update_data = [
            'materi'      => $materi,
            'kegiatan'    => $kegiatan,
            'status'      => $status,
            'catatan'     => $catatan,
            'media_files' => json_encode($media_list),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->agenda_model->updateItemAgenda($id_agenda, $update_data);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Agenda pertemuan ke-' . $item_agenda->pertemuan_ke . ' berhasil diperbarui.');
        redirect('agenda_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function hapus_media_item($id_agenda, $index_media)
    {
        $id_agenda = (int) $id_agenda;
        $index_media = (int) $index_media;

        $item_agenda = $this->agenda_model->getItemAgenda($id_agenda);
        if (!$item_agenda) {
            show_404();
        }

        $media_list = json_decode($item_agenda->media_files ?: '[]', true) ?: [];
        if (isset($media_list[$index_media])) {
            $item = $media_list[$index_media];
            if (isset($item['type']) && $item['type'] === 'file' && !empty($item['file_name'])) {
                $file_path = './uploads/agenda_media/' . $item['file_name'];
                if (is_file($file_path)) {
                    @unlink($file_path);
                }
            }
            array_splice($media_list, $index_media, 1);
            $this->agenda_model->updateItemAgenda($id_agenda, ['media_files' => json_encode($media_list)]);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Media pembelajaran berhasil dihapus.');
        }

        redirect('agenda_pembelajaran/detail/' . $item_agenda->id_pembelajaran_mapel);
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
