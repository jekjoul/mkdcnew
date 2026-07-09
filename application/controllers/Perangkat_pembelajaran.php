<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat_pembelajaran extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Perangkat_pembelajaran_model', 'perangkat_model');
        $this->perangkat_model->ensureTables();
        $this->load->helper('text');
    }

    public function index()
    {
        ifPermissions('perangkat_pembelajaran_list');
        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->subtitle = 'Perangkat Pembelajaran';
        $this->page_data['page']->subtitleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->icon = 'solar:document-add-linear';
        $this->page_data['items'] = $this->perangkat_model->getAdminItems();
        $this->load->view('perangkat_pembelajaran/list', $this->page_data);
    }

    public function detail($id_pembelajaran_mapel)
    {
        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        $agenda = $this->perangkat_model->getAgendaByMapel($id_pembelajaran_mapel);
        $has_schedule_and_days = $this->perangkat_model->hasScheduleAndEffectiveDays($id_pembelajaran_mapel);

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->subtitle = 'Detail Perangkat & Agenda';
        $this->page_data['page']->subtitleUrl = 'perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:document-add-linear';
        
        $this->page_data['item'] = $item;
        $this->page_data['perangkat'] = $perangkat;
        $this->page_data['agenda'] = $agenda;
        $this->page_data['has_schedule_and_days'] = $has_schedule_and_days;

        // Copy features data & rombel switcher
        $this->page_data['source_last_year_id'] = $this->perangkat_model->getSourceLastYearAgenda($id_pembelajaran_mapel);
        $this->page_data['other_active_rombel_agendas'] = $this->perangkat_model->getOtherActiveRombelAgendas($id_pembelajaran_mapel);
        $this->page_data['all_rombel'] = $this->perangkat_model->getAllRombelSameMapelTingkat($id_pembelajaran_mapel);
        $this->page_data['detail_base_url'] = url('perangkat_pembelajaran/detail');
        
        $this->page_data['back_url'] = url('perangkat_pembelajaran');
        $this->page_data['save_berkas_url'] = url('perangkat_pembelajaran/simpan_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['hapus_berkas_url'] = url('perangkat_pembelajaran/hapus_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['unduh_berkas_url'] = url('perangkat_pembelajaran/unduh_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_url'] = url('perangkat_pembelajaran/generate_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['save_agenda_url'] = url('perangkat_pembelajaran/simpan_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['salin_perangkat_url'] = url('perangkat_pembelajaran/salin_perangkat/' . $id_pembelajaran_mapel);
        $this->page_data['salin_agenda_url'] = url('perangkat_pembelajaran/salin_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_ai_url'] = url('perangkat_pembelajaran/generate_agenda_ai/' . $id_pembelajaran_mapel);
        
        $this->load->view('perangkat_pembelajaran/detail', $this->page_data);
    }

    public function simpan_berkas($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
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
        foreach ($fields as $field) {
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
            }
        }

        if (!empty($uploaded)) {
            $this->perangkat_model->saveBerkas($id_pembelajaran_mapel, $uploaded);
        }
        if (!empty($drive_ids)) {
            $this->perangkat_model->saveDriveIds($id_pembelajaran_mapel, $drive_ids);
        }

        $this->activity_model->add(logged('name') . ' Menyimpan Berkas Perangkat Pembelajaran untuk #' . $id_pembelajaran_mapel);
        
        if ($drive_error) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Berkas berhasil disimpan di server lokal, tetapi gagal sinkron ke Google Drive. Error: ' . $drive_error);
        } else {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Berkas berhasil disimpan dan disinkronkan ke Google Drive.');
        }
        
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function hapus_berkas($id_pembelajaran_mapel, $jenis)
    {
        ifPermissions('perangkat_pembelajaran_edit');
        
        $fields = [
            'cp' => 'file_cp', 'tp' => 'file_tp', 'atp' => 'file_atp', 'modul_ajar' => 'file_modul_ajar',
            'kisi_sts' => 'file_kisi_sts', 'soal_sts' => 'file_soal_sts', 'kisi_sas' => 'file_kisi_sas', 'soal_sas' => 'file_soal_sas'
        ];

        if (!isset($fields[$jenis])) {
            show_404();
        }

        // Get Google Drive File ID and delete it
        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        if ($perangkat) {
            $key_drive = $jenis . '_drive_file_id';
            if (!empty($perangkat->$key_drive)) {
                $this->load->library('GoogleDrive_Helper');
                $this->googledrive_helper->deleteFile($perangkat->$key_drive);
            }
        }

        $this->perangkat_model->deleteBerkasFile($id_pembelajaran_mapel, $fields[$jenis]);

        $this->activity_model->add(logged('name') . ' Menghapus Berkas ' . $jenis . ' untuk #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas lokal dan di Google Drive berhasil dihapus.');
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function unduh_berkas($id_pembelajaran_mapel, $jenis)
    {
        ifPermissions('perangkat_pembelajaran_list');

        $fields = [
            'cp' => 'file_cp', 'tp' => 'file_tp', 'atp' => 'file_atp', 'modul_ajar' => 'file_modul_ajar',
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
        $local_path = './uploads/perangkat_pembelajaran/' . $filename;

        // Try downloading/exporting from Google Drive first to get latest online changes
        $key_drive = $jenis . '_drive_file_id';
        if (!empty($perangkat->$key_drive)) {
            $this->load->library('GoogleDrive_Helper');
            $is_xlsx = (strpos($filename, '.xlsx') !== false);
            $this->googledrive_helper->downloadGoogleFile($perangkat->$key_drive, $local_path, $is_xlsx);
        }

        // Force download to browser
        $this->load->helper('download');
        if (is_file($local_path)) {
            force_download($local_path, NULL);
        } else {
            show_404();
        }
    }

    public function generate_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $success = $this->perangkat_model->generateAgenda($id_pembelajaran_mapel);
        if ($success) {
            $this->activity_model->add(logged('name') . ' Generate Agenda Harian untuk #' . $id_pembelajaran_mapel);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian berhasil digenerate berdasarkan jadwal dan hari aktif.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal generate agenda harian. Pastikan jadwal pelajaran dan hari aktif telah digenerate.');
        }
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function generate_agenda_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        // Count meetings
        $slots_by_day = [];
        $schedules = $this->db->get_where('jadwal_pelajaran_item', ['id_pembelajaran' => $item->id_pembelajaran])->result();
        foreach ($schedules as $sched) {
            $slots_by_day[strtolower($sched->hari)] = true;
        }

        $active_days = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
            ->where_in('status', ['Efektif', 'Daring', 'Luar Kelas'])
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
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
        }

        $this->load->library('GoogleAI_Helper');
        $ai_res = $this->googleai_helper->generateAgenda($item->nama_mapel, $item->nama_tingkat, $meetings_count);

        if (isset($ai_res['error'])) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal memproses Google AI (Gemini): ' . $ai_res['error']);
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
        }

        $success = $this->perangkat_model->generateAgendaAI($id_pembelajaran_mapel, $ai_res);
        if ($success) {
            $this->activity_model->add(logged('name') . ' Generate Agenda Harian via Google AI untuk #' . $id_pembelajaran_mapel);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Agenda harian otomatis berbasis Kurikulum Indonesia berhasil dibuat oleh Google AI (Gemini Flash).');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyimpan agenda harian hasil AI.');
        }

        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function simpan_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

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

        $this->activity_model->add(logged('name') . ' Menyimpan Agenda Harian untuk #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Agenda harian berhasil disimpan.');
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function salin_perangkat($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) show_404();

        $success = $this->perangkat_model->copyPerangkatFromLastYear($item->id_tahun_pelajaran, $item->id_tingkat_sekolah, $item->id_mapel);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Berkas perangkat pembelajaran berhasil disalin dari tahun lalu.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyalin. Tidak ditemukan berkas perangkat dari tahun lalu.');
        }
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function salin_agenda($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $source_id = (int) post('source_id');
        if (!$source_id) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Sumber agenda tidak valid.');
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
        }

        $success = $this->perangkat_model->copyAgendaFromSource($id_pembelajaran_mapel, $source_id);
        if ($success) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Isi agenda harian berhasil disalin dari sumber terpilih.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal menyalin agenda. Pastikan sumber agenda terisi.');
        }
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    private function uploadFile($fieldName, $id_pembelajaran_mapel)
    {
        if (empty($_FILES[$fieldName]['name'])) return null;

        $config['upload_path'] = './uploads/perangkat_pembelajaran/';
        $config['allowed_types'] = 'docx|xlsx';
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
}
