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
        $has_schedule_and_days = $this->perangkat_model->hasScheduleAndEffectiveDays($id_pembelajaran_mapel);

        $this->setPage('Portal Guru', 'Detail Perangkat & Agenda', 'guru/perangkat_detail/' . $id_pembelajaran_mapel, 'solar:document-add-linear');
        $this->page_data['ptk'] = $ptk;
        $this->page_data['item'] = $item;
        $this->page_data['perangkat'] = $perangkat;
        $this->page_data['agenda'] = $agenda;
        $this->page_data['has_schedule_and_days'] = $has_schedule_and_days;

        // Copy features data
        $this->page_data['source_last_year_id'] = $this->perangkat_model->getSourceLastYearAgenda($id_pembelajaran_mapel);
        $this->page_data['other_active_rombel_agendas'] = $this->perangkat_model->getOtherActiveRombelAgendas($id_pembelajaran_mapel);
        $this->page_data['all_rombel'] = $this->perangkat_model->getAllRombelSameMapelTingkat($id_pembelajaran_mapel);
        $this->page_data['detail_base_url'] = url('guru/detail_perangkat');
        
        $this->page_data['back_url'] = url('guru/perangkat');
        $this->page_data['save_berkas_url'] = url('guru/simpan_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['hapus_berkas_url'] = url('guru/hapus_berkas/' . $id_pembelajaran_mapel);
        $this->page_data['generate_agenda_url'] = url('guru/generate_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['save_agenda_url'] = url('guru/simpan_agenda/' . $id_pembelajaran_mapel);
        $this->page_data['salin_perangkat_url'] = url('guru/salin_perangkat/' . $id_pembelajaran_mapel);
        $this->page_data['salin_agenda_url'] = url('guru/salin_agenda/' . $id_pembelajaran_mapel);

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

        $uploaded = [];
        foreach ($fields as $field) {
            $file_name = $this->uploadFile($field, $id_pembelajaran_mapel);
            $uploaded[$field] = $file_name;
        }

        $this->perangkat_model->saveBerkas($id_pembelajaran_mapel, $uploaded);

        $this->activity_model->add(logged('name') . ' (Guru) Menyimpan Berkas Perangkat Pembelajaran untuk #' . $id_pembelajaran_mapel);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas perangkat pembelajaran berhasil disimpan.');
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
        $config['allowed_types'] = ($fieldName === 'file_cp') ? 'pdf' : 'pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|jpg|jpeg|png';
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

        $setting = $this->getSetting((int) $id_pembelajaran_mapel);
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
                $data = [
                    'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
                    'id_siswa' => $id_siswa,
                    'nilai_harian' => $nilai_harian,
                    'nilai_psts' => $nilai_psts,
                    'nilai_psas' => $nilai_psas,
                    'nilai_rapor' => $this->hitungRapor($nilai_harian, $nilai_psts, $nilai_psas, $setting),
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

        $this->setPage('Portal Guru', 'Profil Saya', 'guru/profil', 'icon-park-outline:user-business');
        $this->page_data['row'] = $ptk;
        $this->page_data['ptk'] = $ptk;
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
        $this->db->select('pm.id_pembelajaran_mapel, pm.id_pembelajaran, pm.jumlah_jam, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, COUNT(DISTINCT ps.peserta_didik_id) AS jumlah_siswa');
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

    private function getJadwalGuru($id_ptk)
    {
        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            return [];
        }

        $this->db->select('j.hari, j.slot_ke, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, m.nama_mapel, m.mapel_singkat');
        $this->db->from('jadwal_pelajaran_item j');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran = j.id_pembelajaran AND pm.id_mapel = j.id_mapel');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = j.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('mapel m', 'm.id_mapel = j.id_mapel');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->order_by('FIELD(j.hari, "Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu")', '', false);
        $this->db->order_by('j.slot_ke', 'ASC');
        return $this->db->get()->result();
    }

    private function getNilaiMapel($id_ptk)
    {
        $this->db->select('pm.id_pembelajaran_mapel, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, COUNT(DISTINCT ps.peserta_didik_id) AS jumlah_siswa, COUNT(DISTINCT ns.id_nilai_siswa) AS jumlah_dinilai');
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
        $this->db->group_by('pm.id_pembelajaran_mapel');
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
    }
}
