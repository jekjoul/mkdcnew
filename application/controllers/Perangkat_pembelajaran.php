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
        $modul_ajar_list = $this->perangkat_model->getModulAjarByMapel($id_pembelajaran_mapel);
        $has_schedule_and_days = $this->perangkat_model->hasScheduleAndEffectiveDays($id_pembelajaran_mapel);

        $this->page_data['page']->title = 'Pembelajaran';
        $this->page_data['page']->titleUrl = 'perangkat_pembelajaran';
        $this->page_data['page']->subtitle = 'Detail Perangkat & Agenda';
        $this->page_data['page']->subtitleUrl = 'perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel;
        $this->page_data['page']->icon = 'solar:document-add-linear';
        
        $this->page_data['item'] = $item;
        $this->page_data['perangkat'] = $perangkat;
        $this->page_data['agenda'] = $agenda;
        $this->page_data['modul_ajar_list'] = $modul_ajar_list;
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
        
        // Modul ajar URLs
        $this->page_data['upload_modul_url'] = url('perangkat_pembelajaran/upload_modul_ajar/' . $id_pembelajaran_mapel);
        $this->page_data['delete_modul_url'] = url('perangkat_pembelajaran/delete_modul_ajar/' . $id_pembelajaran_mapel);
        $this->page_data['generate_modul_ai_url'] = url('perangkat_pembelajaran/generate_modul_ai/' . $id_pembelajaran_mapel);
        $this->page_data['unduh_modul_url'] = url('perangkat_pembelajaran/unduh_modul_ajar/' . $id_pembelajaran_mapel);

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

    public function generate_berkas_ai($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
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

        // Sequential validation check
        $seq_order = ['file_cp', 'file_tp', 'file_atp', 'file_kisi_sts', 'file_soal_sts', 'file_kisi_sas', 'file_soal_sas'];
        $current_idx = array_search($field, $seq_order);
        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);

        if ($current_idx > 0) {
            $prev_field = $seq_order[$current_idx - 1];
            $prev_uploaded = $perangkat ? $perangkat->$prev_field : null;
            if (!$prev_uploaded) {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Gagal Generate! Anda harus mengisi/mengunggah dokumen sebelum berkas ini terlebih dahulu secara berurutan.');
                redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
                return;
            }
        }

        $field_info = $valid_fields[$field];

        // Fetch previous file content as reference to force consistency
        $prev_file_context = "";
        if ($current_idx > 0) {
            $prev_field = $seq_order[$current_idx - 1];
            $prev_file_name = $perangkat ? $perangkat->$prev_field : null;
            if ($prev_file_name) {
                $prev_path = './uploads/perangkat_pembelajaran/' . $prev_file_name;
                if (is_file($prev_path)) {
                    // Try reading first 4000 characters of local text for prompt context
                    $raw_prev = file_get_contents($prev_path);
                    $clean_prev = strip_tags($raw_prev);
                    $clean_prev = preg_replace('/\s+/', ' ', $clean_prev);
                    $clean_prev = substr($clean_prev, 0, 3000);
                    
                    $prev_doc_name = $valid_fields[$prev_field]['name'];
                    $prev_file_context = "\nSebagai referensi wajib, Anda HARUS menyelaraskan isinya agar merujuk/berkesinambungan dengan berkas sebelumnya yaitu '{$prev_doc_name}' berikut:\n--- BACAAN DOKUMEN SEBELUMNYA ---\n{$clean_prev}\n--- AKHIR DOKUMEN SEBELUMNYA ---\n";
                }
            }
        }

        // Construct context info
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

            $prompt = "Anda adalah pakar pembuat instrumen evaluasi pendidikan di Indonesia. "
                    . "Buatlah DOKUMEN KISI-KISI SOAL evaluasi untuk:\n"
                    . "Satuan Pendidikan : {$item->nama_lembaga}\n"
                    . "Mata Pelajaran : {$subject}\n"
                    . "Kelas/Semester : {$class_level} / {$semester}\n"
                    . "Kurikulum yang digunakan : {$kurikulum}\n"
                    . "Tahun Pelajaran : {$item->tahun_pelajaran}\n"
                    . "Bentuk Penilaian : {$field_info['name']}\n"
                    . "Jumlah Soal : {$jml_soal_str}\n"
                    . "Alokasi Waktu : {$alokasi_waktu} Menit\n"
                    . "Bentuk Soal : {$bentuk_soal}\n"
                    . "Penyusun / Penulis Soal : " . ($item->nama_ptk ?: '-') . "\n\n"
                    . "ATURAN FORMAT DOKUMEN:\n"
                    . "1. Di bagian teratas, cetak informasi detail keterangan di atas dalam format daftar atau tabel profil yang bersih.\n"
                    . "2. Setelah keterangan di atas, buatlah SATU tabel utama dengan kolom berurutan:\n"
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
                    . "3. TIDAK PERLU menuliskan penjelasan pendahuluan, deskripsi lainnya, petunjuk, atau penutup apapun. Cukup keterangan atas dan tabel utama kisi-kisi saja.\n"
                    . "4. Sesuaikan indikator, tujuan pembelajaran, dan no soal secara teratur logis sesuai kurikulum yang dipilih.\n"
                    . "5. Tuliskan keluaran langsung berupa tag HTML mentah saja (tanpa pembungkus markdown ```html).";
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

        $api_key = setting('google_ai_api_key');
        if (empty($api_key) || $api_key === '0') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'API Key Google AI belum dikonfigurasi di Pengaturan API.');
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
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
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
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
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
            return;
        }

        // Build file
        $file_name = $field . '_' . time() . '.' . $field_info['ext'];
        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;

        // CodeIgniter wrapper HTML-to-Word with Landscape option support
        $style_extra = "";
        if ($word_layout === 'landscape') {
            $style_extra = "@page { size: landscape; margin: 1in; } @page Section1 { size: 11in 8.5in; margin: 1in; mso-header-margin: .5in; mso-footer-margin: .5in; mso-paper-source: 0; } div.Section1 { page: Section1; }";
        }
        
        $word_html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
                   . '<head><meta charset="utf-8"><title>' . html_escape($field_info['name']) . '</title>'
                   . '<style>body { font-family: "Calibri", sans-serif; font-size: 11pt; line-height: 1.5; } h1 { font-size: 18pt; color: #1f4e78; } h2 { font-size: 14pt; color: #2e74b5; } h3 { font-size: 12pt; color: #5b9bd5; } table { border-collapse: collapse; width: 100%; margin: 12px 0; } th, td { border: 1px solid #a6a6a6; padding: 8px; text-align: left; } th { background-color: #f2f2f2; } ' . $style_extra . '</style></head>'
                   . '<body><div class="Section1">' . $html_content . '</div></body></html>';

        file_put_contents($filepath, $word_html);

        // Upload to Drive
        $this->load->library('GoogleDrive_Helper');
        $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, 'application/msword', true);

        // Save local & Drive IDs together
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
            $this->session->set_flashdata('alert', 'Gagal menyimpan data ke database SQL! Silakan periksa tabel database Anda. Masalah: ' . $db_err_msg);
        } else {
            if ($drive_error) {
                $this->session->set_flashdata('alert-type', 'warning');
                $this->session->set_flashdata('alert', $field_info['name'] . ' berhasil digenerate secara lokal, tetapi gagal disinkronkan ke Google Drive untuk Edit Online. Masalah: ' . $drive_error);
            } else {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', $field_info['name'] . ' baru berhasil digenerate oleh AI, disimpan ke Drive, dan siap diedit online.');
            }
        }
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel);
    }

    public function upload_modul_ajar($id_pembelajaran_mapel)
    {
        postAllowed();
        ifPermissions('perangkat_pembelajaran_edit');

        $label = post('label') ?: 'Modul Ajar';
        $file_name = $this->uploadFile('file_modul_rpp', $id_pembelajaran_mapel);
        if (!$file_name) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal mengunggah berkas modul ajar.');
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;
        $mime = mime_content_type($filepath);
        
        $this->load->library('GoogleDrive_Helper');
        $drive_res = $this->googledrive_helper->uploadFile($filepath, $file_name, $mime, true);
        $drive_file_id = isset($drive_res['id']) ? $drive_res['id'] : null;

        $this->perangkat_model->saveModulAjar($id_pembelajaran_mapel, $file_name, $drive_file_id, $label);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Modul Ajar berhasil diupload dan disinkronkan ke Google Drive.');
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
    }

    public function delete_modul_ajar($id_pembelajaran_mapel, $id_modul)
    {
        ifPermissions('perangkat_pembelajaran_edit');

        $row = $this->perangkat_model->deleteModulAjar($id_modul);
        if ($row && !empty($row->drive_file_id)) {
            $this->load->library('GoogleDrive_Helper');
            $this->googledrive_helper->deleteFile($row->drive_file_id);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas modul ajar berhasil dihapus.');
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
    }

    public function unduh_modul_ajar($id_pembelajaran_mapel, $id_modul)
    {
        ifPermissions('perangkat_pembelajaran_list');

        $row = $this->db->get_where('perangkat_pembelajaran_modul_ajar', ['id_modul' => (int)$id_modul])->row();
        if (!$row) {
            show_404();
        }

        $local_path = './uploads/perangkat_pembelajaran/' . $row->nama_file;
        if (!empty($row->drive_file_id)) {
            $this->load->library('GoogleDrive_Helper');
            $this->googledrive_helper->downloadGoogleFile($row->drive_file_id, $local_path, false);
        }

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
        ifPermissions('perangkat_pembelajaran_edit');

        $item = $this->perangkat_model->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            show_404();
        }

        $perangkat = $this->perangkat_model->getPerangkatByMapel($id_pembelajaran_mapel);
        $atp_file = $perangkat ? $perangkat->file_atp : null;

        $atp_text = "";
        if ($atp_file) {
            $atp_path = './uploads/perangkat_pembelajaran/' . $atp_file;
            if (is_file($atp_path)) {
                // Reading docx plain text or excel text
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
        
        // Temporarily override JSON generation constraint of AI helper by replacing cURL payload
        $api_key = setting('google_ai_api_key');
        if (empty($api_key) || $api_key === '0') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'API Key Google AI belum dikonfigurasi di Pengaturan API.');
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
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
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
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
            redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
        }

        // Generate Word docx from HTML (We can wrap HTML content into a downloadable document file format)
        $clean_label = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $topic);
        $file_name = 'modul_ajar_' . time() . '_' . str_replace(' ', '_', strtolower($clean_label)) . '.docx';
        $filepath = './uploads/perangkat_pembelajaran/' . $file_name;

        // CodeIgniter wrapper for simple HTML-to-Word conversion format
        $word_html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
                   . '<head><meta charset="utf-8"><title>' . html_escape($topic) . '</title>'
                   . '<style>body { font-family: "Calibri", sans-serif; font-size: 11pt; line-height: 1.5; } h1 { font-size: 18pt; color: #1f4e78; } h2 { font-size: 14pt; color: #2e74b5; } h3 { font-size: 12pt; color: #5b9bd5; } table { border-collapse: collapse; width: 100%; margin: 12px 0; } th, td { border: 1px solid #a6a6a6; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style></head>'
                   . '<body>' . $html_content . '</body></html>';

        file_put_contents($filepath, $word_html);

        // Upload to Google Drive and convert to editable Google Doc
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
        redirect('perangkat_pembelajaran/detail/' . $id_pembelajaran_mapel . '?tab=modul');
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
