<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends MY_Controller
{
    private $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureUsersPtkColumn();
        $this->ensureNilaiTables();
        $this->load->model('Perangkat_pembelajaran_model', 'perangkat_model');
        $this->perangkat_model->ensureTables();
    }

    public function index()
    {
        ifPermissions('menu_dashboard_guru');
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'Dashboard Guru', 'guru', 'solar:user-check-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['jumlah_pembelajaran'] = count($this->getPembelajaranMapel($ptk->id_ptk));
        $this->page_data['jumlah_siswa'] = count($this->getSiswaGuru($ptk->id_ptk));
        $this->page_data['jumlah_jadwal'] = count($this->getJadwalGuru($ptk->id_ptk));
        $this->load->view('guru/dashboard', $this->page_data);
    }

    public function siswa()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'List Siswa', 'guru/siswa', 'solar:users-group-two-rounded-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['siswa'] = $this->getSiswaGuru($ptk->id_ptk);
        $this->load->view('guru/siswa', $this->page_data);
    }

    public function pembelajaran()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'Pembelajaran Saya', 'guru/pembelajaran', 'solar:notebook-bookmark-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['items'] = $this->getPembelajaranMapel($ptk->id_ptk);
        $this->load->view('guru/pembelajaran', $this->page_data);
    }

    public function perangkat()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'Perangkat Pembelajaran', 'guru/perangkat', 'solar:document-add-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['items'] = $this->perangkat_model->getGuruItems($ptk->id_ptk);
        $this->load->view('guru/perangkat', $this->page_data);
    }

    public function perangkat_detail($id_pembelajaran_mapel)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        $agenda = $this->perangkat_model->getAgendaByMapel($id_pembelajaran_mapel);
        $modul_ajar_list = $this->perangkat_model->getModulAjarByMapel($id_pembelajaran_mapel);
        $has_schedule_and_days = $this->perangkat_model->hasScheduleAndEffectiveDays($id_pembelajaran_mapel);

        $this->setPage('Portal Guru', 'Detail Perangkat & Agenda', 'guru/perangkat_detail/' . $id_pembelajaran_mapel, 'solar:document-add-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['item'] = $item;
        $this->page_data['perangkat'] = $perangkat;
        $this->page_data['agenda'] = $agenda;
        $this->page_data['modul_ajar_list'] = $modul_ajar_list;
        $this->page_data['has_schedule_and_days'] = $has_schedule_and_days;

        // Copy features data
        $this->page_data['source_last_year_id'] = $this->perangkat_model->getSourceLastYearAgenda($id_pembelajaran_mapel);
        $this->page_data['other_active_rombel_agendas'] = $this->perangkat_model->getOtherActiveRombelAgendas($id_pembelajaran_mapel);
        $this->page_data['all_rombel'] = $this->perangkat_model->getAllRombelSameMapelTingkat($id_pembelajaran_mapel, $ptk->id_ptk);
        $this->page_data['detail_base_url'] = url('guru/perangkat_detail');
        
        $this->page_data['back_url'] = url('guru/perangkat');
        $this->page_data['save_berkas_url'] = url('guru/simpan_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['hapus_berkas_url'] = url('guru/hapus_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['unduh_berkas_url'] = url('guru/unduh_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_url'] = url('guru/generate_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_ai_url'] = url('guru/generate_agenda_ai/' . $id_pembelajaran_mapel);
        $this->page_data['save_agenda_url'] = url('guru/simpan_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['salin_perangkat_url'] = url('guru/salin_perangkat/' . $id_pembelajaran_mapel);
        $this->page_data['salin_agenda_url'] = url('guru/salin_agenda/' . $id_pembelajaran_mapel);

        $this->page_data['upload_modul_url'] = url('guru/upload_modul/' . $id_pembelajaran_mapel);
        $this->page_data['generate_modul_ai_url'] = url('guru/generate_modul_ai/' . $id_pembelajaran_mapel);
        $this->page_data['generate_berkas_ai_url'] = url('guru/generate_berkas_ai/' . $id_pembelajaran_mapel);
        $this->page_data['unduh_modul_url'] = url('guru/unduh_modul/' . $id_pembelajaran_mapel);
        $this->page_data['delete_modul_url'] = url('guru/hapus_modul/' . $id_pembelajaran_mapel);

        $this->load->view('perangkat_pembelajaran/detail', $this->page_data);
    }

    public function simpan_berkas($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $fields = [
            'file_cp', 'file_tp', 'file_atp', 'file_modul_ajar',
            'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'
        ];

        // Support single field upload from item-level forms
        $single_field = post('single_field');
        if ($single_field && in_array($single_field, $fields, true)) {
            $fields = [$single_field];
        }

        $uploaded = [];
        $drive_ids = [];
        $this->load->library('GoogleDrive_Helper');

        $drive_error = null;
        $upload_error = null;

        foreach ($fields as $field) {
            if (empty($_FILES[$field]['name'])) {
                continue;
            }

            $file_name = $this->uploadFile($field, $id_pembelajaran_mapel);
            if ($file_name) {
                $uploaded[$field] = $file_name;
                
                // Try uploading to Google Drive
                $filepath = './uploads/perangkat_pembelajaran/' . $file_name;
                $mime = mime_content_type($filepath);
                
                $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, $mime, true);
                if (isset($drive_res['id'])) {
                    $key_drive = str_replace('file_', '', $field) . '_drive_file_id';
                    $drive_ids[$key_drive] = $drive_res['id'];
                } elseif (isset($drive_res['error'])) {
                    $drive_error = $drive_res['error'];
                }
            } else {
                $upload_error = $this->upload->display_errors('', '');
            }
        }

        if ($upload_error) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal mengunggah berkas: ' . $upload_error);
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
            return;
        }

        if (!empty($uploaded)) {
            $this->perangkat_model->saveBerkas($id_pembelajaran_mapel, $uploaded);
        }
        if (!empty($drive_ids)) {
            $this->perangkat_model->saveDriveIds($id_pembelajaran_mapel, $drive_ids);
        }

        $this->activity_model->add(logged('name') . ' (Guru) Menyimpan Berkas Perangkat Pembelajaran untuk #' . $id_pembelajaran_mapel);
        
        if ($drive_error) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Berkas berhasil disimpan di server lokal, tetapi gagal sinkron ke Google Drive. Error: ' . $drive_error);
        } else {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Berkas berhasil disimpan dan disinkronkan ke Google Drive.');
        }
        
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function hapus_berkas($id_pembelajaran_mapel, $jenis)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }
        
        $fields = [
            'cp' => 'file_cp', 'tp' => 'file_tp', 'atp' => 'file_atp', 'modul_ajar' => 'file_modul_ajar',
            'kisi_sts' => 'file_kisi_sts', 'soal_sts' => 'file_soal_sts', 'kisi_sas' => 'file_kisi_sas', 'soal_sas' => 'file_soal_sas'
        ];

        if (!isset($fields[$jenis])) {
            show_404();
        }

        $this->perangkat_model->deleteBerkasFile($id_pembelajaran_mapel, $fields[$jenis]);

        $this->activity_model->add(logged('name') . ' (Guru) Menghapus Berkas ' . $jenis . ' untuk #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas berhasil dihapus.');
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function generate_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $success = $this->perangkat_model->generateAgenda($id_pembelajaran_mapel);
        if ($success) {
            $this->activity_model->add(logged('name') . ' (Guru) Generate Agenda Harian untuk #' . $id_pembelajaran_mapel);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian berhasil digenerate berdasarkan jadwal dan hari aktif.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal generate agenda harian. Pastikan jadwal pelajaran dan hari aktif telah digenerate.');
        }
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function generate_agenda_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        // Count meetings (Hanya menghitung hari 'Efektif')
        $slots_by_day = [];
        $schedules = $this->db->get_where('jadwal_pelajaran_item', ['id_pembelajaran' => $item->id_pembelajaran])->result();
        foreach ($schedules as $sched) {
            $slots_by_day[strtolower($sched->hari)] = true;
        }

        $active_days = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where('status', 'Efektif')
            ->get('pembelajaran_hari_efektif')->result();

        $meetings_count = 0;
        $day_names = [
            0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'
        ];
        foreach ($active_days as $ad) {
            $w = (int) date('w', strtotime($ad->tanggal));
            if (isset($slots_by_day[$day_names[$w]])) {
                $meetings_count++;
            }
        }

        if ($meetings_count === 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal generate. Tidak ditemukan hari aktif belajar yang sesuai dengan jadwal mingguan kelas ini.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
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
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
        }

        $success = $this->perangkat_model->generateAgendaAI($id_pembelajaran_mapel, $ai_res);
        if ($success) {
            $this->activity_model->add(logged('name') . ' (Guru) Generate Agenda Harian via Google AI untuk #' . $id_pembelajaran_mapel);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian otomatis berbasis Kurikulum Indonesia berhasil dibuat oleh Google AI (Gemini Flash).');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyimpan agenda harian hasil AI.');
        }

        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function simpan_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $id_agenda = (int) post('id_agenda');
        if ($id_agenda) {
            $this->db->where('id_agenda', $id_agenda);
            $this->db->where('id_pembelajaran_mapel', $id_pembelajaran_mapel);
            $this->db->update('agenda_pembelajaran', [
                'materi' => $this->input->post('materi'),
                'kegiatan' => $this->input->post('kegiatan'),
                'status' => post('status'),
                'catatan' => post('catatan'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->activity_model->add(logged('name') . ' (Guru) Menyimpan Agenda Harian untuk #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Agenda harian berhasil disimpan.');
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function salin_perangkat($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $success = $this->perangkat_model->copyPerangkatFromLastYear($item->id_tahun_pelajaran, $item->id_tingkat_sekolah, $item->id_mapel);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Berkas perangkat pembelajaran berhasil disalin dari tahun lalu.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyalin. Tidak ditemukan berkas perangkat dari tahun lalu.');
        }
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function salin_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $source_id = (int) post('source_id');
        if (!$source_id) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Sumber agenda tidak valid.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
        }

        $success = $this->perangkat_model->copyAgendaFromSource($id_pembelajaran_mapel, $source_id);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Isi agenda harian berhasil disalin dari sumber terpilih.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyalin agenda. Pastikan sumber agenda terisi.');
        }
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    private function uploadFile($fieldName, $id_pembelajaran_mapel)
    {
        if (empty($_FILES[$fieldName]['name'])) return null;

        $config['upload_path'] = './uploads/perangkat_pembelajaran/';
        $config['allowed_types'] = 'docx|xlsx|pdf|doc|xls|ppt|pptx|zip|rar|jpg|jpeg|png';
        $config['max_size'] = 10240; // 10MB
        $config['file_name'] = $fieldName . '_' . $id_pembelajaran_mapel . '_' . time();

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($fieldName)) {
            $data = $this->upload->data();
            return $data['file_name'];
        }
        return null;
    }

    public function jadwal()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'Jadwal Saya', 'guru/jadwal', 'akar-icons:schedule');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['items'] = $this->getJadwalGuru($ptk->id_ptk);
        $this->load->view('guru/jadwal', $this->page_data);
    }

    public function nilai()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $this->setPage('Portal Guru', 'Input Nilai', 'guru/nilai', 'solar:clipboard-list-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['items'] = $this->getNilaiMapel($ptk->id_ptk);
        $this->load->view('guru/nilai', $this->page_data);
    }

    public function input_nilai($id_pembelajaran_mapel)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->getMapelGuruDetail($ptk->id_ptk, $id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $this->setPage('Portal Guru', 'Input Nilai', 'guru/input_nilai/' . $id_pembelajaran_mapel, 'solar:clipboard-list-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['item'] = $item;
        $this->page_data['setting'] = $this->getSetting((int) $id_pembelajaran_mapel);
        $this->page_data['siswa'] = $this->getSiswaPembelajaran($item->id_pembelajaran);
        $this->page_data['nilai'] = $this->getNilaiRows((int) $id_pembelajaran_mapel);
        $this->load->view('guru/input_nilai', $this->page_data);
    }

    public function simpan_nilai($id_pembelajaran_mapel)
    {
        postAllowed();

        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->getMapelGuruDetail($ptk->id_ptk, $id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        // 1. Simpan Label Kolom jika ada post labels_tugas & labels_uh
        $labels_tugas = $this->input->post('labels_tugas');
        $labels_uh = $this->input->post('labels_uh');
        $labels_data = [
            'labels_tugas' => is_array($labels_tugas) ? json_encode(array_values($labels_tugas)) : null,
            'labels_uh' => is_array($labels_uh) ? json_encode(array_values($labels_uh)) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $existing_setting = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if ($existing_setting) {
            $this->db->where('id_pengaturan_nilai', $existing_setting->id_pengaturan_nilai);
            $this->db->update('nilai_siswa_pengaturan', $labels_data);
        } else {
            $default_setting = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row();
            $labels_data['id_pembelajaran_mapel'] = (int) $id_pembelajaran_mapel;
            $labels_data['persen_harian'] = $default_setting ? $default_setting->persen_harian : 40;
            $labels_data['persen_psts'] = $default_setting ? $default_setting->persen_psts : 30;
            $labels_data['persen_psas'] = $default_setting ? $default_setting->persen_psas : 30;
            $labels_data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('nilai_siswa_pengaturan', $labels_data);
        }

        // Tarik setting ter-update
        $setting = $this->getSetting((int) $id_pembelajaran_mapel);

        // 2. Simpan Nilai Harian Siswa
        $rows = $this->input->post('nilai');
        if (is_array($rows)) {
            foreach ($rows as $id_siswa => $row) {
                $id_siswa = (int) $id_siswa;
                if ($id_siswa <= 0 || !$this->isSiswaInPembelajaran($item->id_pembelajaran, $id_siswa)) {
                    continue;
                }

                $nilai_harian = $this->normalizeNilai(isset($row['harian']) ? $row['harian'] : null);
                $nilai_psts = $this->normalizeNilai(isset($row['psts']) ? $row['psts'] : null);
                $nilai_psas = $this->normalizeNilai(isset($row['psas']) ? $row['psas'] : null);

                // Normalisasi array dinamis extra
                $extra_tugas = isset($row['extra_tugas']) && is_array($row['extra_tugas']) ? array_map([$this, 'normalizeNilai'], $row['extra_tugas']) : null;
                $extra_uh = isset($row['extra_uh']) && is_array($row['extra_uh']) ? array_map([$this, 'normalizeNilai'], $row['extra_uh']) : null;

                $data = [
                    'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
                    'id_siswa' => $id_siswa,
                    'nilai_harian' => $nilai_harian,
                    'nilai_psts' => $nilai_psts,
                    'nilai_psas' => $nilai_psas,
                    'nilai_rapor' => $this->hitungRapor($nilai_harian, $nilai_psts, $nilai_psas, $setting),
                    'extra_tugas' => $extra_tugas ? json_encode(array_values($extra_tugas)) : null,
                    'extra_uh' => $extra_uh ? json_encode(array_values($extra_uh)) : null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $existing = $this->db->get_where('nilai_siswa', [
                    'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
                    'id_siswa' => $id_siswa,
                ])->row();

                if ($existing) {
                    $this->db->where('id_nilai_siswa', $existing->id_nilai_siswa);
                    $this->db->update('nilai_siswa', $data);
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('nilai_siswa', $data);
                }
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Nilai siswa berhasil disimpan');
        redirect('guru/input_nilai/' . $id_pembelajaran_mapel);
    }

    public function profil()
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $id_ptk = $ptk->id_ptk;
        $row = $this->db->get_where('ptk', ['id_ptk' => $id_ptk])->row();

        $this->setPage('Portal Guru', 'Profil Saya', 'guru/profil', 'solar:user-circle-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['row'] = $row;

        $this->db->order_by('tahun_lulus', 'DESC');
        $this->db->order_by('tanggal_lulus', 'DESC');
        $this->page_data['riwayat_pendidikan'] = $this->db->get_where('ptk_riwayat_pendidikan', ['id_ptk' => $id_ptk])->result();

        $this->db->select('ptk_dokumen_pribadi.*, master_jenis_dokumen_ptk.nama_jenis_dokumen');
        $this->db->from('ptk_dokumen_pribadi');
        $this->db->join('master_jenis_dokumen_ptk', 'master_jenis_dokumen_ptk.id_jenis_dokumen = ptk_dokumen_pribadi.id_jenis_dokumen', 'left');
        $this->db->where('ptk_dokumen_pribadi.id_ptk', $id_ptk);
        $this->db->order_by('master_jenis_dokumen_ptk.nama_jenis_dokumen', 'ASC');
        $this->page_data['dokumen_pribadi'] = $this->db->get()->result();

        $this->db->order_by('nama_jenis_dokumen', 'ASC');
        $this->page_data['jenis_dokumen'] = $this->db->get_where('master_jenis_dokumen_ptk', ['status' => 'Aktif'])->result();
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();

        $this->load->view('guru/profil', $this->page_data);
    }

    public function update_profil()
    {
        postAllowed();

        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $data = [
            'nama_ptk' => post('nama_ptk'),
            'gelar_depan' => post('gelar_depan'),
            'gelar_belakang' => post('gelar_belakang'),
            'jenis_kelamin' => post('jenis_kelamin'),
            'tempat_lahir' => post('tempat_lahir'),
            'tanggal_lahir' => post('tanggal_lahir'),
            'agama' => post('agama'),
            'status_perkawinan' => post('status_perkawinan'),
            'nama_ibu_kandung' => post('nama_ibu_kandung'),
            'nik' => post('nik'),
            'niy' => post('niy'),
            'nuptk' => post('nuptk'),
            'no_sk_pengangkatan' => post('no_sk_pengangkatan'),
            'tgl_sk_pengangkatan' => post('tgl_sk_pengangkatan') ?: null,
            'email' => post('email'),
            'telepon' => post('telepon'),
            'status_pegawai' => post('status_pegawai'),
            'penugasan' => post('penugasan'),
            'alamat' => post('alamat'),
            'rt' => post('rt'),
            'rw' => post('rw'),
            'provinsi' => $this->wilayahName('reg_provinsi', 'id_prov', post('provinsi'), post('provinsi')),
            'kabupaten' => $this->wilayahName('reg_kabupaten', 'id_kab', post('kabupaten'), post('kabupaten')),
            'kecamatan' => $this->wilayahName('reg_kecamatan', 'id_kec', post('kecamatan'), post('kecamatan')),
            'kelurahan_desa' => $this->wilayahName('reg_kelurahan', 'id_kel', post('kelurahan_desa'), post('kelurahan_desa')),
            'status_keaktifan' => $ptk->status_keaktifan,
        ];

        $password = post('password');
        if ($password) {
            $data['password'] = hash('sha256', $password);
        }

        $this->db->where('id_ptk', $ptk->id_ptk);
        $this->db->update('ptk', $data);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Profil PTK berhasil diperbarui');
        redirect('guru/profil');
    }

    public function getKabupaten()
    {
        $id_prov = $this->input->post('id');
        echo json_encode($this->db->get_where('reg_kabupaten', ['id_prov' => $id_prov])->result());
    }

    public function getKecamatan()
    {
        $id_kab = $this->input->post('id');
        echo json_encode($this->db->get_where('reg_kecamatan', ['id_kab' => $id_kab])->result());
    }

    public function getKelurahan()
    {
        $id_kec = $this->input->post('id');
        echo json_encode($this->db->get_where('reg_kelurahan', ['id_kec' => $id_kec])->result());
    }

    private function setPage($title, $subtitle, $subtitleUrl, $icon)
    {
        $this->page_data['page']->title = $title;
        $this->page_data['page']->titleUrl = 'guru';
        $this->page_data['page']->subtitle = $subtitle;
        $this->page_data['page']->subtitleUrl = $subtitleUrl;
        $this->page_data['page']->icon = $icon;
    }

    private function notLinked()
    {
        $this->setPage('Portal Guru', 'Akun Belum Terhubung', 'guru', 'solar:user-cross-linear');
        $this->load->view('guru/not_linked', $this->page_data);
    }

    private function currentPtk()
    {
        $user_id = (int) logged('id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        if (!$user) {
            return null;
        }

        if (!empty($user->id_ptk)) {
            $ptk = $this->db->get_where('ptk', ['id_ptk' => (int) $user->id_ptk])->row();
            if ($ptk) {
                return $ptk;
            }
        }

        $ptk = null;
        if (!empty($user->email)) {
            $this->db->where('LOWER(email)', strtolower($user->email));
            $ptk = $this->db->get('ptk')->row();
        }

        if (!$ptk && !empty($user->name)) {
            $this->db->where('LOWER(nama_ptk)', strtolower($user->name));
            $ptk = $this->db->get('ptk')->row();
        }

        if ($ptk) {
            $this->db->where('id', $user_id);
            $this->db->update('users', ['id_ptk' => $ptk->id_ptk]);
        }

        return $ptk;
    }

    private function getPembelajaranMapel($id_ptk)
    {
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_pembelajaran, pm.jumlah_jam, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, COUNT(DISTINCT ps.peserta_didik_id) AS jumlah_siswa');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran', 'left');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('tp.status', 'ASC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    private function getSiswaGuru($id_ptk)
    {
        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd, s.jenis_kelamin, s.rombel, s.status_keaktifan, GROUP_CONCAT(DISTINCT m.mapel_singkat ORDER BY m.nama_mapel SEPARATOR ", ") AS mapel');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->group_by('s.id_siswa');
        $this->db->order_by('s.nama_siswa', 'ASC');
        return $this->db->get()->result();
    }

    private function getJadwalSettings($id_pembelajaran)
    {
        $rows = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => $id_pembelajaran])->result();
        $settings = [];
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        foreach ($hari_list as $hari) {
            $settings[$hari] = [
                'aktif' => false,
                'jumlah_jp' => $hari === 'Jumat' ? 6 : 8,
            ];
        }

        foreach ($rows as $row) {
            $settings[$row->hari] = [
                'aktif' => true,
                'jumlah_jp' => (int) $row->jumlah_jp,
            ];
        }

        if (empty($rows)) {
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
                $settings[$hari]['aktif'] = true;
            }
        }

        return $settings;
    }

    private function getJadwalGuru($id_ptk)
    {
        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            return [];
        }

        $this->db->select('j.id_pembelajaran, j.hari, j.slot_ke, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, m.nama_mapel, m.mapel_singkat');
        $this->db->from('jadwal_pelajaran_item j');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran = j.id_pembelajaran AND pm.id_mapel = j.id_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = j.id_pembelajaran');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('mapel m', 'm.id_mapel = j.id_mapel');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->where('tp.status', 'Aktif');
        $this->db->order_by('FIELD(j.hari, "Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu")', '', false);
        $this->db->order_by('j.slot_ke', 'ASC');
        $rows = $this->db->get()->result();

        if (empty($rows)) {
            return [];
        }

        // Cache settings for each unique id_pembelajaran
        $pembelajaran_ids = array_unique(array_column($rows, 'id_pembelajaran'));
        $settings_cache = [];
        foreach ($pembelajaran_ids as $id_pem) {
            $settings_cache[$id_pem] = $this->getJadwalSettings($id_pem);
        }

        // Filter out items that are on disabled days or exceed JP slots
        $filtered = [];
        foreach ($rows as $row) {
            $pem_id = $row->id_pembelajaran;
            $hari = $row->hari;
            $slot = (int)$row->slot_ke;

            if (isset($settings_cache[$pem_id][$hari])) {
                $set = $settings_cache[$pem_id][$hari];
                if ($set['aktif'] && $slot <= $set['jumlah_jp']) {
                    $filtered[] = $row;
                }
            }
        }

        return $filtered;
    }

    private function getNilaiMapel($id_ptk)
    {
        $this->db->select('pm.id_pembelajaran_mapel, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, COUNT(DISTINCT ps.peserta_didik_id) AS jumlah_siswa, COUNT(DISTINCT ns.id_nilai_siswa) AS jumlah_dinilai');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran', 'left');
        $this->db->join('nilai_siswa ns', 'ns.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->where('tp.status', 'Aktif');
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    private function getMapelGuruDetail($id_ptk, $id_pembelajaran_mapel)
    {
        $this->db->select('pm.*, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->where('pm.id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        return $this->db->get()->row();
    }

    private function getSiswaPembelajaran($id_pembelajaran)
    {
        $this->db->select('s.id_siswa, s.nama_siswa, s.nisn, s.nipd');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->where('ps.id_pembelajaran', (int) $id_pembelajaran);
        $this->db->order_by('s.nama_siswa', 'ASC');
        return $this->db->get()->result();
    }

    private function getNilaiRows($id_pembelajaran_mapel)
    {
        $rows = $this->db->get_where('nilai_siswa', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->result();
        $nilai = [];
        foreach ($rows as $row) {
            $nilai[(int) $row->id_siswa] = $row;
        }
        return $nilai;
    }

    private function getSetting($id_pembelajaran_mapel)
    {
        $row = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
        if ($row) {
            return $row;
        }

        $row = $this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row();
        if ($row) {
            return $row;
        }

        return (object) ['persen_harian' => 40, 'persen_psts' => 30, 'persen_psas' => 30];
    }

    private function normalizeNilai($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = (float) str_replace(',', '.', (string) $value);
        return min(100, max(0, $value));
    }

    private function hitungRapor($harian, $psts, $psas, $setting)
    {
        $total = 0;
        $has_value = false;
        foreach ([[$harian, $setting->persen_harian], [$psts, $setting->persen_psts], [$psas, $setting->persen_psas]] as $component) {
            if ($component[0] !== null) {
                $has_value = true;
                $total += ((float) $component[0] * (float) $component[1]) / 100;
            }
        }
        return $has_value ? round($total, 2) : null;
    }

    private function isSiswaInPembelajaran($id_pembelajaran, $id_siswa)
    {
        return $this->db->get_where('pembelajaran_siswa', [
            'id_pembelajaran' => (int) $id_pembelajaran,
            'peserta_didik_id' => (string) $id_siswa,
        ])->row() ? true : false;
    }

    private function wilayahName($table, $pk, $value, $fallback)
    {
        if ($value !== false && is_numeric($value)) {
            $row = $this->db->get_where($table, [$pk => $value])->row();
            return $row ? $row->nama : $fallback;
        }
        return $fallback;
    }

    private function ensureUsersPtkColumn()
    {
        $this->load->dbforge();
        if (!$this->db->field_exists('id_ptk', 'users')) {
            $this->dbforge->add_column('users', [
                'id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'role'],
            ]);
        }
    }

    private function ensureNilaiTables()
    {
        $this->load->dbforge();
        if (!$this->db->table_exists('nilai_siswa_pengaturan')) {
            $this->dbforge->add_field([
                'id_pengaturan_nilai' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'persen_harian' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 40],
                'persen_psts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 30],
                'persen_psas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 30],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_pengaturan_nilai', true);
            $this->dbforge->create_table('nilai_siswa_pengaturan', true);
        }

        if (!$this->db->field_exists('labels_tugas', 'nilai_siswa_pengaturan')) {
            $this->dbforge->add_column('nilai_siswa_pengaturan', [
                'labels_tugas' => ['type' => 'TEXT', 'null' => true],
                'labels_uh' => ['type' => 'TEXT', 'null' => true],
            ]);
        }

        if (!$this->db->get_where('nilai_siswa_pengaturan', ['id_pembelajaran_mapel' => 0])->row()) {
            $this->db->insert('nilai_siswa_pengaturan', [
                'id_pembelajaran_mapel' => 0,
                'persen_harian' => 40,
                'persen_psts' => 30,
                'persen_psas' => 30,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if (!$this->db->table_exists('nilai_siswa')) {
            $this->dbforge->add_field([
                'id_nilai_siswa' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11],
                'id_siswa' => ['type' => 'INT', 'constraint' => 11],
                'nilai_harian' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_psts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_psas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'nilai_rapor' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_nilai_siswa', true);
            $this->dbforge->create_table('nilai_siswa', true);
        }

        if (!$this->db->field_exists('extra_tugas', 'nilai_siswa')) {
            $this->dbforge->add_column('nilai_siswa', [
                'extra_tugas' => ['type' => 'TEXT', 'null' => true],
                'extra_uh' => ['type' => 'TEXT', 'null' => true],
            ]);
        }
    }

    public function unduh_berkas($id_pembelajaran_mapel, $jenis)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $fields = [
            'cp' => 'file_cp', 'tp' => 'file_tp', 'atp' => 'file_atp', 'kktp' => 'file_kktp', 'modul_ajar' => 'file_modul_ajar',
            'kisi_sts' => 'file_kisi_sts', 'soal_sts' => 'file_soal_sts', 'kisi_sas' => 'file_kisi_sas', 'soal_sas' => 'file_soal_sas'
        ];

        if (!isset($fields[$jenis])) {
            show_404();
        }

        $field_name = $fields[$jenis];
        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        if (!$perangkat || empty($perangkat->$field_name)) {
            show_404();
        }

        $filename = $perangkat->$field_name;
        $key_drive = $jenis . '_drive_file_id';
        $drive_file_id = !empty($perangkat->$key_drive) ? $perangkat->$key_drive : null;

        if (!empty($drive_file_id)) {
            $is_xlsx = (strpos($filename, '.xlsx') !== false || strpos($filename, '.xls') !== false);
            $is_docx = (strpos($filename, '.docx') !== false || strpos($filename, '.doc') !== false);
            if ($is_xlsx) {
                $download_url = "https://docs.google.com/spreadsheets/d/{$drive_file_id}/export?format=xlsx";
            } elseif ($is_docx) {
                $download_url = "https://docs.google.com/document/d/{$drive_file_id}/export?format=docx";
            } else {
                $download_url = "https://drive.google.com/uc?export=download&id={$drive_file_id}";
            }
            redirect($download_url);
            return;
        }

        // Fallback to local
        $local_path = './uploads/perangkat_pembelajaran/' . $filename;
        $this->load->helper('download');
        if (is_file($local_path)) {
            force_download($local_path, NULL);
        } else {
            show_404();
        }
    }

    public function generate_berkas_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $field = post('field');
        $valid_fields = [
            'file_cp' => ['name' => 'Capaian Pembelajaran (CP)', 'ext' => 'docx', 'type' => 'word'],
            'file_tp' => ['name' => 'Tujuan Pembelajaran (TP)', 'ext' => 'docx', 'type' => 'word'],
            'file_atp' => ['name' => 'Alur Tujuan Pembelajaran (ATP)', 'ext' => 'docx', 'type' => 'word'],
            'file_kisi_sts' => ['name' => 'Kisi-kisi STS', 'ext' => 'docx', 'type' => 'word'],
            'file_soal_sts' => ['name' => 'Soal STS', 'ext' => 'docx', 'type' => 'word'],
            'file_kisi_sas' => ['name' => 'Kisi-kisi SAS', 'ext' => 'docx', 'type' => 'word'],
            'file_soal_sas' => ['name' => 'Soal SAS', 'ext' => 'docx', 'type' => 'word']
        ];

        if (!isset($valid_fields[$field])) {
            show_404();
        }

        $seq_order = ['file_cp', 'file_tp', 'file_atp', 'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'];
        $current_idx = array_search($field, $seq_order);
        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);

        if ($current_idx > 0) {
            $prev_field = $seq_order[$current_idx - 1];
            $prev_uploaded = $perangkat ? $perangkat->$prev_field : null;
            if (!$prev_uploaded) {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Gagal Generate! Anda harus mengisi/mengunggah dokumen sebelum berkas ini terlebih dahulu secara berurutan.');
                redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
                return;
            }
        }

        $field_info = $valid_fields[$field];

        $prev_file_context = "";
        if ($current_idx > 0) {
            $prev_field = $seq_order[$current_idx - 1];
            $prev_file_name = $perangkat ? $perangkat->$prev_field : null;
            if ($prev_file_name) {
                $prev_path = './uploads/perangkat_pembelajaran/' . $prev_file_name;
                if (is_file($prev_path)) {
                    $raw_prev = file_get_contents($prev_path);
                    $clean_prev = strip_tags($raw_prev);
                    $clean_prev = preg_replace('/\s+/', ' ', $clean_prev);
                    $clean_prev = substr($clean_prev, 0, 3000);
                    
                    $prev_doc_name = $valid_fields[$prev_field]['name'];
                    $prev_file_context = "\nSebagai referensi wajib, Anda HARUS menyelaraskan isinya agar merujuk/berkesinambungan dengan berkas sebelumnya yaitu '{$prev_doc_name}' berikut:\n--- BACAAN DOKUMEN SEBELUMNYA ---\n{$clean_prev}\n--- AKHIR DOKUMEN SEBELUMNYA ---\n";
                }
            }
        }

        $subject = $item->nama_mapel;
        $class_level = $item->nama_tingkat;
        $semester = $item->semester;
        $kurikulum = isset($item->kurikulum) ? $item->kurikulum : 'Kurikulum Merdeka';

        $is_kisi = ($field === 'file_kisi_sts' || $field === 'file_kisi_sas');
        $word_layout = "portrait";

        if ($is_kisi) {
            $word_layout = "landscape";
            $jml_pg = (int) post('jumlah_pg');
            $jml_essai = (int) post('jumlah_essai');
            $bentuk_soal = post('bentuk_soal');
            $alokasi_waktu = (int) post('alokasi_waktu');
            
            $jml_soal_str = "";
            if ($bentuk_soal === 'Pilihan Ganda') {
                $jml_soal_str = $jml_pg . " Soal Pilihan Ganda";
            } elseif ($bentuk_soal === 'Essai') {
                $jml_soal_str = $jml_essai . " Soal Essai";
            } else {
                $jml_soal_str = $jml_pg . " Soal Pilihan Ganda & " . $jml_essai . " Soal Essai";
            }

            $jenis_sumatif = (strpos($field, 'sts') !== false) ? 'SUMATIF TENGAH SEMESTER (STS)' : 'SUMATIF AKHIR SEMESTER (SAS)';

            $prompt = "Anda adalah pakar pembuat instrumen evaluasi pendidikan di Indonesia. "
                    . "Buatlah draft dokumen Kisi-kisi dalam format HTML.\n\n"
                    . "ATURAN FORMAT DOKUMEN:\n"
                    . "1. Di bagian paling atas, tuliskan judul besar berikut dengan huruf kapital tebal (bold) di tengah (center):\n"
                    . "   <h2 style='text-align: center;'>KISI-KISI PENULISAN SOAL {$jenis_sumatif}</h2>\n"
                    . "2. Setelah judul, cetak informasi berikut secara detail di bagian kiri menggunakan format teks biasa paragraf teratur (JANGAN gunakan tabel untuk informasi ini, cukup gunakan teks biasa dengan tanda titik dua ':'): \n"
                    . "   Satuan Pendidikan : {$item->nama_lembaga}\n"
                    . "   Mata Pelajaran : {$subject}\n"
                    . "   Kelas / Semester : {$class_level} / {$semester}\n"
                    . "   Kurikulum yang digunakan : {$kurikulum}\n"
                    . "   Tahun Pelajaran : {$item->tahun_pelajaran}\n"
                    . "   Bentuk Penilaian : {$field_info['name']}\n"
                    . "   Jumlah Soal : {$jml_soal_str}\n"
                    . "   Alokasi Waktu : {$alokasi_waktu} Menit\n"
                    . "   Bentuk Soal : {$bentuk_soal}\n"
                    . "   Penyusun / Penulis Soal : " . ($item->nama_ptk ?: '-') . "\n"
                    . "3. Di bawah informasi tersebut, buatlah SATU tabel utama kisi-kisi penulisan soal dengan orientasi landscape lebar (tabel didesain agar muat banyak kolom secara mendatar). Tabel ini harus memiliki kolom berurutan:\n"
                    . "   1. No\n"
                    . "   2. Tujuan Pembelajaran\n"
                    . "   3. Materi\n"
                    . "   4. Kelas/Semester\n"
                    . "   5. Indikator Soal\n"
                    . "   6. Level Kognitif\n"
                    . "   7. Dimensi Pengetahuan\n"
                    . "   8. Bentuk Soal\n"
                    . "   9. No. Soal\n"
                    . "   10. Skor\n"
                    . "4. TIDAK PERLU menuliskan penjelasan pendahuluan, deskripsi lainnya, petunjuk pengisian, atau tanda tangan penutup apapun. Cukup judul, informasi teks biasa di atas, dan tabel utama kisi-kisi saja.\n"
                    . "5. PENTING: Di dalam seluruh isi dokumen, hindari penggunaan istilah/kata 'peserta didik', ganti/gunakan kata 'murid' sebagai gantinya.\n"
                    . "6. Kirimkan langsung berupa tag HTML mentah saja (tanpa pembungkus markdown ```html).";
        } else {
            $is_soal = ($field === 'file_soal_sts' || $field === 'file_soal_sas');
            if ($is_soal) {
                $kisi_field = (strpos($field, 'sts') !== false) ? 'file_kisi_sts' : 'file_kisi_sas';
                $kisi_file_name = $perangkat ? $perangkat->$kisi_field : null;
                $kisi_context = "";
                
                if ($kisi_file_name) {
                    $kisi_path = './uploads/perangkat_pembelajaran/' . $kisi_file_name;
                    if (is_file($kisi_path)) {
                        $raw_kisi = file_get_contents($kisi_path);
                        $clean_kisi = strip_tags($raw_kisi);
                        $clean_kisi = preg_replace('/\s+/', ' ', $clean_kisi);
                        $clean_kisi = substr($clean_kisi, 0, 3000);
                        $kisi_context = "\nBerikut adalah data 'KISI-KISI SOAL' yang telah dibuat sebelumnya. Silakan baca tabel kisi-kisi ini untuk menentukan materi, indikator, bentuk, dan nomor soal:\n--- BACAAN KISI-KISI ---\n{$clean_kisi}\n--- AKHIR BACAAN KISI-KISI ---\n";
                    }
                }

                $prompt = "Anda adalah pakar pembuat evaluasi pendidikan (soal ujian) di Indonesia. "
                        . "Buatlah lembar naskah SOAL UJIAN lengkap untuk mata pelajaran '{$subject}', tingkat kelas '{$class_level}', semester '{$semester}', kurikulum '{$kurikulum}'."
                        . $kisi_context
                        . "\nATURAN PENULISAN SOAL:\n"
                        . "1. Buatlah seluruh butir soal SECARA LENGKAP SATU PER SATU. Jangan pernah melompati nomor soal, mempersingkat tulisan, atau menggunakan singkatan seperti '(dst./dan seterusnya)'. Jika di kisi-kisi terdapat 20 soal, Anda WAJIB memaparkan 20 soal tersebut secara penuh dari nomor 1 sampai 20.\n"
                        . "2. Jika tipe soal berupa Pilihan Ganda (PG), wajib menyertakan opsi pilihan jawaban lengkap (A, B, C, D, dan E untuk tingkat SMA/SMK, atau A, B, C, D untuk tingkat SMP/SD).\n"
                        . "3. Tuliskan KUNCI JAWABAN lengkap di bagian paling akhir dokumen naskah soal.\n"
                        . "4. Gunakan format HTML yang rapi dengan list tertata (ol, ul) dan spasi paragraf yang bersih untuk dibaca murid.\n"
                        . "5. PENTING: Di dalam seluruh isi dokumen, hindari penggunaan istilah/kata 'peserta didik', ganti/gunakan kata 'murid' sebagai gantinya.\n"
                        . "6. Kirimkan langsung berupa tag HTML mentah saja (tanpa pembungkus markdown ```html).";
            } else {
                $prompt = "Anda adalah pakar kurikulum dan pendidik di Indonesia. "
                        . "Buatlah draft dokumen resmi '{$field_info['name']}' yang mendalam dan komprehensif untuk mata pelajaran '{$subject}', "
                        . "tingkat kelas '{$class_level}', semester '{$semester}', dengan acuan '{$kurikulum}'."
                        . $prev_file_context
                        . "\nDesainlah isian dokumen tersebut dengan format HTML terstruktur rapi menggunakan heading (h1, h2, h3), list (ul, ol), dan tabel yang menarik untuk dibaca. "
                        . "Pastikan isinya sangat relevan dengan kurikulum dan kebutuhan sekolah formal di Indonesia saat ini. "
                        . "PENTING: Di dalam seluruh isi dokumen, hindari penggunaan istilah/kata 'peserta didik', ganti/gunakan kata 'murid' sebagai gantinya. "
                        . "Jangan gunakan pembungkus markdown code block (```html), kirimkan teks HTML mentah saja.";
            }
        }

        $api_key = setting('google_ai_api_key');
        if (empty($api_key) || $api_key === '0') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'API Key Google AI belum dikonfigurasi di Pengaturan API.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
            return;
        }

        $model_name = setting('google_ai_model') ?: 'gemini-3.1-flash-lite';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model_name}:generateContent?key=" . $api_key;
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$output || $http_code !== 200) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal memproses Google AI (Gemini) untuk berkas perangkat.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
            return;
        }

        $res = json_decode($output, true);
        $html_content = isset($res['candidates'][0]['content']['parts'][0]['text']) ? $res['candidates'][0]['content']['parts'][0]['text'] : '';
        $html_content = trim($html_content);
        if (strpos($html_content, '```') === 0) {
            $html_content = preg_replace('/^```(?:html)?|```$/i', '', $html_content);
            $html_content = trim($html_content);
        }

        if (empty($html_content)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'AI mengembalikan konten kosong.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
            return;
        }

        $file_name = $field . '_' . time() . '.' . $field_info['ext'];
        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;

        $style_extra = "";
        if ($word_layout === 'landscape') {
            $style_extra = "@page { size: landscape; margin: 1in; } @page Section1 { size: 11in 8.5in; margin: 1in; mso-header-margin: .5in; mso-footer-margin: .5in; mso-paper-source: 0; } div.Section1 { page: Section1; }";
        }
        
        $word_html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
                   . '<head><meta charset="utf-8"><title>' . html_escape($field_info['name']) . '</title>'
                   . '<style>body { font-family: "Calibri", sans-serif; font-size: 11pt; line-height: 1.5; } h1 { font-size: 18pt; color: #1f4e78; } h2 { font-size: 14pt; color: #2e74b5; } h3 { font-size: 12pt; color: #5b9bd5; } table { border-collapse: collapse; width: 100%; margin: 12px 0; } th, td { border: 1px solid #a6a6a6; padding: 8px; text-align: left; } th { background-color: #f2f2f2; } ' . $style_extra . '</style></head>'
                   . '<body><div class="Section1">' . $html_content . '</div></body></html>';

        file_put_contents($filepath, $word_html);

        $this->load->library('GoogleDrive_Helper');
        $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, 'application/msword', true);

        $uploaded = [$field => $file_name];
        
        $drive_error = null;
        if (isset($drive_res['id'])) {
            $key_drive = str_replace('file_', '', $field) . '_drive_file_id';
            $uploaded[$key_drive] = $drive_res['id'];
        } else {
            $drive_error = isset($drive_res['error']) ? $drive_res['error'] : 'Gagal terhubung ke Google Drive API.';
        }

        $db_saved = $this->perangkat_model->saveBerkas($id_pembelajaran_mapel, $uploaded);

        if (!$db_saved) {
            $db_error = $this->db->error();
            $db_err_msg = isset($db_error['message']) ? $db_error['message'] : 'Query SQL gagal dieksekusi.';
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyimpan data ke database SQL! Masalah: ' . $db_err_msg);
        } else {
            if ($drive_error) {
                $this->session->set_flashdata('alert-type', 'warning');
                $this->session->set_flashdata('alert', $field_info['name'] . ' berhasil digenerate secara lokal, tetapi gagal disinkronkan ke Google Drive. Masalah: ' . $drive_error);
            } else {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', $field_info['name'] . ' baru berhasil digenerate oleh AI, disimpan ke Drive, dan siap diedit online.');
            }
        }
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel);
    }

    public function upload_modul($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $label = post('label') ?: 'Modul Ajar';
        $file_name = $this->uploadFile('file_modul_rpp', $id_pembelajaran_mapel);
        if (!$file_name) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal mengunggah berkas modul ajar.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;
        $mime = mime_content_type($filepath);
        
        $this->load->library('GoogleDrive_Helper');
        $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, $mime, true);
        $drive_file_id = isset($drive_res['id']) ? $drive_res['id'] : null;

        $this->perangkat_model->saveModulAjar($id_pembelajaran_mapel, $file_name, $drive_file_id, $label);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Modul Ajar berhasil diupload dan disinkronkan ke Google Drive.');
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
    }

    public function hapus_modul($id_pembelajaran_mapel, $id_modul)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $row = $this->perangkat_model->deleteModulAjar($id_modul);
        if ($row && !empty($row->drive_file_id)) {
            $this->load->library('GoogleDrive_Helper');
            $this->googledrive_helper->deleteFile($row->drive_file_id);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas modul ajar berhasil dihapus.');
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
    }

    public function unduh_modul($id_pembelajaran_mapel, $id_modul)
    {
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $row = $this->db->get_where('perangkat_pembelajaran_modul_ajar', ['id_modul' => (int)$id_modul])->row();
        if (!$row) {
            show_404();
        }

        $filename = $row->nama_file;
        $drive_file_id = $row->drive_file_id;

        if (!empty($drive_file_id)) {
            $is_xlsx = (strpos($filename, '.xlsx') !== false || strpos($filename, '.xls') !== false);
            $is_docx = (strpos($filename, '.docx') !== false || strpos($filename, '.doc') !== false);
            if ($is_xlsx) {
                $download_url = "https://docs.google.com/spreadsheets/d/{$drive_file_id}/export?format=xlsx";
            } elseif ($is_docx) {
                $download_url = "https://docs.google.com/document/d/{$drive_file_id}/export?format=docx";
            } else {
                $download_url = "https://drive.google.com/uc?export=download&id={$drive_file_id}";
            }
            redirect($download_url);
            return;
        }

        // Fallback to local
        $local_path = './uploads/perangkat_pembelajaran/' . $filename;
        $this->load->helper('download');
        if (is_file($local_path)) {
            force_download($local_path, NULL);
        } else {
            show_404();
        }
    }

    public function generate_modul_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        $ptk = $this->currentPtk();
        if (!$ptk) {
            return $this->notLinked();
        }

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item || (int) $item->id_ptk !== (int) $ptk->id_ptk) {
            show_404();
        }

        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        $atp_file = $perangkat ? $perangkat->file_atp : null;

        $atp_text = "";
        if ($atp_file) {
            $atp_path = './uploads/perangkat_pembelajaran/' . $atp_file;
            if (is_file($atp_path)) {
                if (strpos($atp_file, '.docx') !== false) {
                    $atp_text = "Telah ada berkas ATP di server lokal dengan nama berkas " . $atp_file;
                }
            }
        }

        $topic = post('topic') ?: 'Materi Pokok Pertemuan Pertama';

        $prompt = "Anda adalah pakar pendidik Kurikulum Merdeka di Indonesia. "
                . "Buatlah satu Rencana Pelaksanaan Pembelajaran (RPP) / Modul Ajar interaktif yang mendalam untuk kelas '{$item->nama_tingkat}' "
                . "mata pelajaran '{$item->nama_mapel}' dengan topik spesifik '{$topic}'. "
                . "Fokus pada struktur baku Kurikulum Merdeka yang mencakup: "
                . "1. Informasi Umum (Identitas, Kompetensi Awal, Profil Pelajar Pancasila, Sarpras, Target Murid). "
                . "2. Komponen Inti (Tujuan Pembelajaran, Pemahaman Bermakna, Pertanyaan Pemantik, Kegiatan Pembelajaran Pembuka-Inti-Penutup, Asesmen). "
                . "3. Lampiran (Lembar Kerja Murid - LKM, Bahan Bacaan Guru & Murid, Glosarium, Daftar Pustaka). "
                . "Keluaran HARUS berupa HTML terstruktur rapi menggunakan heading (h1, h2, h3), list (ul, ol), dan tabel yang menarik untuk dibaca. "
                . "PENTING: Di dalam seluruh isi dokumen, hindari penggunaan istilah/kata 'peserta didik', ganti/gunakan kata 'murid' sebagai gantinya. "
                . "Jangan gunakan pembungkus markdown code block (```html), kirimkan teks HTML mentah saja.";

        $this->load->library('GoogleAI_Helper');
        
        $api_key = setting('google_ai_api_key');
        if (empty($api_key) || $api_key === '0') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'API Key Google AI belum dikonfigurasi di Pengaturan API.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        $model_name = setting('google_ai_model') ?: 'gemini-3.1-flash-lite';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model_name}:generateContent?key=" . $api_key;
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$output || $http_code !== 200) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal memproses Google AI (Gemini) untuk Modul Ajar.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        $res = json_decode($output, true);
        $html_content = isset($res['candidates'][0]['content']['parts'][0]['text']) ? $res['candidates'][0]['content']['parts'][0]['text'] : '';
        $html_content = trim($html_content);
        if (strpos($html_content, '```') === 0) {
            $html_content = preg_replace('/^```(?:html)?|```$/i', '', $html_content);
            $html_content = trim($html_content);
        }

        if (empty($html_content)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'AI mengembalikan konten kosong.');
            redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        $clean_label = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $topic);
        $file_name = 'modul_ajar_' . time() . '_' . str_replace(' ', '_', strtolower($clean_label)) . '.docx';
        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;

        $word_html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
                   . '<head><meta charset="utf-8"><title>' . html_escape($topic) . '</title>'
                   . '<style>body { font-family: "Calibri", sans-serif; font-size: 11pt; line-height: 1.5; } h1 { font-size: 18pt; color: #1f4e78; } h2 { font-size: 14pt; color: #2e74b5; } h3 { font-size: 12pt; color: #5b9bd5; } table { border-collapse: collapse; width: 100%; margin: 12px 0; } th, td { border: 1px solid #a6a6a6; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style></head>'
                   . '<body>' . $html_content . '</body></html>';

        file_put_contents($filepath, $word_html);

        $this->load->library('GoogleDrive_Helper');
        $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, 'application/msword', true);
        
        $drive_file_id = isset($drive_res['id']) ? $drive_res['id'] : null;
        $drive_error = isset($drive_res['error']) ? $drive_res['error'] : null;

        $this->perangkat_model->saveModulAjar($id_pembelajaran_mapel, $file_name, $drive_file_id, 'Modul: ' . $topic);

        if (!$drive_file_id) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Modul Ajar berhasil digenerate lokal, namun gagal disinkronkan ke Google Drive untuk Edit Online. Masalah: ' . ($drive_error ?: 'Google Drive tidak terhubung.'));
        } else {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Modul Ajar / RPP baru berhasil digenerate oleh AI, disimpan ke Drive, dan siap diedit online.');
        }
        redirect('guru/perangkat_detail/' . $id_pembelajaran_mapel . '?tab=modul');
    }
}

