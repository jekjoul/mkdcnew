<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal_pelajaran extends MY_Controller
{
    private $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    public function index()
    {
        ifPermissions('jadwal_pelajaran_list');
        $this->loadJadwalIndex('Aktif');
    }

    public function nonaktif()
    {
        $this->loadJadwalIndex('Nonaktif');
    }

    private function loadJadwalIndex($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'jadwal_pelajaran/nonaktif' : 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Jadwal Tahun Tidak Aktif' : 'Daftar Versi Jadwal Pelajaran';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'jadwal_pelajaran/nonaktif' : 'jadwal_pelajaran';
        $this->page_data['page']->icon = 'akar-icons:schedule';

        $pembelajaran = $this->getAllPembelajaran($status_tahun);
        $tp_id = !empty($pembelajaran) ? (int)$pembelajaran[0]->id_tahun_pelajaran : 0;

        if ($tp_id <= 0 && !$is_nonaktif) {
            $tp_query = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif']);
            $tp_row = ($tp_query && is_object($tp_query)) ? $tp_query->row() : null;
            if ($tp_row) {
                $tp_id = (int)$tp_row->id_tahun_pelajaran;
            }
        }

        if ($tp_id > 0 && !$is_nonaktif) {
            $this->ensureDefaultHeader($tp_id);
        }

        $headers = [];
        if ($this->db->table_exists('jadwal_pelajaran_header')) {
            $this->db->select('h.*, tp.tahun_pelajaran, tp.semester, (SELECT COUNT(DISTINCT j.id_pembelajaran) FROM jadwal_pelajaran_item j WHERE j.id_jadwal_header = h.id_jadwal_header) as total_kelas, (SELECT COUNT(*) FROM jadwal_pelajaran_item j WHERE j.id_jadwal_header = h.id_jadwal_header) as total_slot');
            $this->db->from('jadwal_pelajaran_header h');
            $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = h.id_tahun_pelajaran', 'left');
            if ($status_tahun === 'Aktif') {
                $this->db->where('tp.status', 'Aktif');
            } else {
                $this->db->where('tp.status !=', 'Aktif');
            }
            $this->db->order_by('FIELD(h.status, "Aktif", "Draft", "Nonaktif")', '', false);
            $this->db->order_by('h.tanggal_mulai_efektif', 'DESC');
            $headers_query = $this->db->get();
            $headers = ($headers_query && is_object($headers_query)) ? $headers_query->result() : [];
        }

        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['headers'] = $headers;
        $this->page_data['settings'] = $this->getSettings(0);
        $this->page_data['menit_jp'] = $this->getMenitJp($this->page_data['settings']);
        $this->page_data['is_nonaktif'] = $is_nonaktif;

        $this->load->view('jadwal_pelajaran/list', $this->page_data);
    }

    public function tambah_versi()
    {
        postAllowed();
        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $tp_id = !empty($pembelajaran) ? (int)$pembelajaran[0]->id_tahun_pelajaran : 0;

        if ($tp_id <= 0) {
            $tp_query = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif']);
            $tp_row = ($tp_query && is_object($tp_query)) ? $tp_query->row() : null;
            if ($tp_row) {
                $tp_id = (int)$tp_row->id_tahun_pelajaran;
            }
        }

        if ($tp_id <= 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Tidak ditemukan Tahun Pelajaran aktif.');
            redirect('jadwal_pelajaran');
        }

        $nama_jadwal = trim(post('nama_jadwal'));
        $tanggal_mulai_efektif = post('tanggal_mulai_efektif') ?: date('Y-m-d', strtotime('next monday'));
        $keterangan = trim(post('keterangan'));

        $this->db->insert('jadwal_pelajaran_header', [
            'id_tahun_pelajaran' => $tp_id,
            'nama_jadwal' => $nama_jadwal ?: 'Versi Jadwal Baru',
            'status' => 'Draft',
            'tanggal_mulai_efektif' => $tanggal_mulai_efektif,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $new_id = $this->db->insert_id();

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Rancangan Versi Jadwal Baru berhasil ditambahkan! Silakan susun slot jam mengajar.');
        redirect('jadwal_pelajaran/semua/' . $new_id);
    }

    public function edit_versi($id_jadwal_header)
    {
        postAllowed();
        $id_jadwal_header = (int) $id_jadwal_header;
        $nama_jadwal = trim(post('nama_jadwal'));
        $tanggal_mulai_efektif = post('tanggal_mulai_efektif');
        $keterangan = trim(post('keterangan'));

        $this->db->where('id_jadwal_header', $id_jadwal_header);
        $this->db->update('jadwal_pelajaran_header', [
            'nama_jadwal' => $nama_jadwal,
            'tanggal_mulai_efektif' => $tanggal_mulai_efektif,
            'keterangan' => $keterangan,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Informasi versi jadwal berhasil diperbarui.');
        redirect('jadwal_pelajaran');
    }

    public function hapus_versi($id_jadwal_header)
    {
        postAllowed();
        $id_jadwal_header = (int) $id_jadwal_header;
        $header_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
        $header = ($header_query && is_object($header_query)) ? $header_query->row() : null;

        if ($header && $header->status !== 'Aktif') {
            $this->db->delete('jadwal_pelajaran_item', ['id_jadwal_header' => $id_jadwal_header]);
            $this->db->delete('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Versi jadwal berhasil dihapus.');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Versi jadwal yang sedang Aktif tidak dapat dihapus. Nonaktifkan atau aktifkan versi lain terlebih dahulu.');
        }
        redirect('jadwal_pelajaran');
    }

    public function salin_versi($id_jadwal_header)
    {
        postAllowed();
        $source_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => (int)$id_jadwal_header]);
        $source = ($source_query && is_object($source_query)) ? $source_query->row() : null;
        if (!$source) show_404();

        $this->db->insert('jadwal_pelajaran_header', [
            'id_tahun_pelajaran' => $source->id_tahun_pelajaran,
            'nama_jadwal' => 'Salinan ' . $source->nama_jadwal,
            'status' => 'Draft',
            'tanggal_mulai_efektif' => date('Y-m-d', strtotime('next monday')),
            'keterangan' => 'Disalin dari ' . $source->nama_jadwal,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $new_id = $this->db->insert_id();

        $items_query = $this->db->get_where('jadwal_pelajaran_item', ['id_jadwal_header' => (int)$id_jadwal_header]);
        $items = ($items_query && is_object($items_query)) ? $items_query->result() : [];
        foreach ($items as $item) {
            $this->db->insert('jadwal_pelajaran_item', [
                'id_jadwal_header' => $new_id,
                'id_pembelajaran' => $item->id_pembelajaran,
                'hari' => $item->hari,
                'slot_ke' => $item->slot_ke,
                'id_mapel' => $item->id_mapel,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Versi Jadwal berhasil diduplikasi menjadi Draft baru.');
        redirect('jadwal_pelajaran/semua/' . $new_id);
    }

    public function aktifkan_versi($id_jadwal_header)
    {
        postAllowed();
        $id_jadwal_header = (int) $id_jadwal_header;
        $target_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
        $target = ($target_query && is_object($target_query)) ? $target_query->row() : null;

        if (!$target) {
            show_404();
        }

        $tgl_mulai_target = !empty($target->tanggal_mulai_efektif) ? $target->tanggal_mulai_efektif : date('Y-m-d');
        $tgl_akhir_prev = date('Y-m-d', strtotime($tgl_mulai_target . ' -1 day'));

        // Auto-close currently active headers for the same school year
        $active_query = $this->db->get_where('jadwal_pelajaran_header', [
            'id_tahun_pelajaran' => $target->id_tahun_pelajaran,
            'status' => 'Aktif',
            'id_jadwal_header !=' => $id_jadwal_header
        ]);
        $active_headers = ($active_query && is_object($active_query)) ? $active_query->result() : [];

        foreach ($active_headers as $ah) {
            $this->db->where('id_jadwal_header', $ah->id_jadwal_header);
            $this->db->update('jadwal_pelajaran_header', [
                'status' => 'Nonaktif',
                'tanggal_akhir_efektif' => $tgl_akhir_prev,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Activate target header
        $this->db->where('id_jadwal_header', $id_jadwal_header);
        $this->db->update('jadwal_pelajaran_header', [
            'status' => 'Aktif',
            'tanggal_akhir_efektif' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Auto-sync agendas starting from effective date forward
        $this->load->model('Agenda_pembelajaran_model', 'agenda_model');
        $pm_query = $this->db->select('id_pembelajaran_mapel')->from('pembelajaran_mapel pm')
            ->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran')
            ->where('p.status', 'Aktif')->get();
        $active_pm = ($pm_query && is_object($pm_query)) ? $pm_query->result() : [];

        foreach ($active_pm as $pm) {
            $this->agenda_model->syncSchedule($pm->id_pembelajaran_mapel, $tgl_mulai_target);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Versi Jadwal "' . html_escape($target->nama_jadwal) . '" berhasil diaktifkan! Berlaku efektif mulai ' . date('d M Y', strtotime($tgl_mulai_target)) . '. Versi sebelumnya telah otomatis di-set tanggal akhirnya ke ' . date('d M Y', strtotime($tgl_akhir_prev)) . '.');
        redirect('jadwal_pelajaran');
    }

    public function waktu()
    {
        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = 'Atur Waktu Mingguan';
        $this->page_data['page']->subtitleUrl = 'jadwal_pelajaran/waktu';
        $this->page_data['page']->icon = 'akar-icons:schedule';

        $settings = $this->getSettings(0);
        $this->page_data['hari'] = $this->hari;
        $this->page_data['settings'] = $settings;
        $this->page_data['menit_jp'] = $this->getMenitJp($settings);

        $this->load->view('jadwal_pelajaran/waktu', $this->page_data);
    }

    public function simpan_waktu()
    {
        postAllowed();
        $this->saveSettings(0);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Kerangka waktu mingguan berhasil disimpan');
        redirect('jadwal_pelajaran');
    }

    public function semua($id_jadwal_header = 0)
    {
        $id_jadwal_header = (int) $id_jadwal_header;
        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $tp_id = !empty($pembelajaran) ? (int)$pembelajaran[0]->id_tahun_pelajaran : 0;

        if ($tp_id <= 0) {
            $tp_query = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif']);
            $tp_row = ($tp_query && is_object($tp_query)) ? $tp_query->row() : null;
            if ($tp_row) {
                $tp_id = (int)$tp_row->id_tahun_pelajaran;
            }
        }

        if ($id_jadwal_header <= 0) {
            $id_jadwal_header = $this->ensureDefaultHeader($tp_id);
        }

        $header_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
        $header = ($header_query && is_object($header_query)) ? $header_query->row() : null;

        if (!$header) {
            show_404();
        }

        $this->page_data['page']->title = 'Jadwal Pelajaran';
        $this->page_data['page']->titleUrl = 'jadwal_pelajaran';
        $this->page_data['page']->subtitle = 'Susun Jadwal: ' . $header->nama_jadwal;
        $this->page_data['page']->subtitleUrl = 'jadwal_pelajaran/semua/' . $id_jadwal_header;
        $this->page_data['icon'] = 'akar-icons:schedule';

        $settings = $this->getSettings(0);
        $mapel_by_pembelajaran = [];
        $items = [];

        foreach ($pembelajaran as $row) {
            $mapel_by_pembelajaran[$row->id_pembelajaran] = $this->getMapelPembelajaran($row->id_pembelajaran);
            $items[$row->id_pembelajaran] = $this->getItemsByHeader($row->id_pembelajaran, $id_jadwal_header, $settings);
        }

        $teachers_query = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk');
        $teachers = ($teachers_query && is_object($teachers_query)) ? $teachers_query->result() : [];

        $this->page_data['header'] = $header;
        $this->page_data['id_jadwal_header'] = $id_jadwal_header;
        $this->page_data['hari'] = $this->hari;
        $this->page_data['settings'] = $settings;
        $this->page_data['menit_jp'] = $this->getMenitJp($settings);
        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['mapel_by_pembelajaran'] = $mapel_by_pembelajaran;
        $this->page_data['items'] = $items;
        $this->page_data['teachers'] = $teachers;

        $this->load->view('jadwal_pelajaran/semua', $this->page_data);
    }

    public function print_semua($id_jadwal_header = 0)
    {
        if (!logged('id')) {
            redirect('login');
        }

        $id_jadwal_header = (int) $id_jadwal_header;
        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $tp_id = !empty($pembelajaran) ? (int)$pembelajaran[0]->id_tahun_pelajaran : 0;

        if ($tp_id <= 0) {
            $tp_query = $this->db->get_where('pembelajaran_tahun_pelajaran', ['status' => 'Aktif']);
            $tp_row = ($tp_query && is_object($tp_query)) ? $tp_query->row() : null;
            if ($tp_row) {
                $tp_id = (int)$tp_row->id_tahun_pelajaran;
            }
        }

        if ($id_jadwal_header <= 0) {
            $id_jadwal_header = $this->ensureDefaultHeader($tp_id);
        }

        $header_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
        $header = ($header_query && is_object($header_query)) ? $header_query->row() : null;
        $settings = $this->getSettings(0);
        $mapel_by_pembelajaran = [];
        $items = [];

        foreach ($pembelajaran as $row) {
            $mapel_by_pembelajaran[$row->id_pembelajaran] = $this->getMapelPembelajaran($row->id_pembelajaran);
            $items[$row->id_pembelajaran] = $this->getItemsByHeader($row->id_pembelajaran, $id_jadwal_header, $settings);
        }

        $teachers_query = $this->db->where('status_keaktifan', 'Aktif')->order_by('nama_ptk', 'ASC')->get('ptk');
        $teachers = ($teachers_query && is_object($teachers_query)) ? $teachers_query->result() : [];

        $this->page_data['header'] = $header;
        $this->page_data['hari'] = $this->hari;
        $this->page_data['settings'] = $settings;
        $this->page_data['menit_jp'] = $this->getMenitJp($settings);
        $this->page_data['pembelajaran'] = $pembelajaran;
        $this->page_data['mapel_by_pembelajaran'] = $mapel_by_pembelajaran;
        $this->page_data['items'] = $items;
        $this->page_data['teachers'] = $teachers;

        $this->load->view('jadwal_pelajaran/print_semua', $this->page_data);
    }

    public function simpan_semua($id_jadwal_header = 0)
    {
        postAllowed();

        $id_jadwal_header = (int) $id_jadwal_header;
        if ($id_jadwal_header <= 0) {
            $pembelajaran = $this->getAllPembelajaran('Aktif');
            $tp_id = !empty($pembelajaran) ? (int)$pembelajaran[0]->id_tahun_pelajaran : 0;
            $id_jadwal_header = $this->ensureDefaultHeader($tp_id);
        }

        $header_query = $this->db->get_where('jadwal_pelajaran_header', ['id_jadwal_header' => $id_jadwal_header]);
        $header = ($header_query && is_object($header_query)) ? $header_query->row() : null;
        if (!$header) show_404();

        $pembelajaran = $this->getAllPembelajaran('Aktif');
        $valid_ids = [];
        $mapel_limits = [];
        foreach ($pembelajaran as $row) {
            $valid_ids[] = (int) $row->id_pembelajaran;
            foreach ($this->getMapelPembelajaran($row->id_pembelajaran) as $mapel) {
                $mapel_limits[$row->id_pembelajaran][$mapel->id_mapel] = (int) $mapel->jumlah_jam;
            }
        }

        $jadwal = $this->input->post('jadwal');
        $pakai = [];
        $insert = [];
        if (is_array($jadwal)) {
            foreach ($jadwal as $id_pembelajaran => $days) {
                $id_pembelajaran = (int) $id_pembelajaran;
                if (!in_array($id_pembelajaran, $valid_ids, true) || !is_array($days)) {
                    continue;
                }

                foreach ($days as $hari => $slots) {
                    if (!in_array($hari, $this->hari, true) || !is_array($slots)) {
                        continue;
                    }

                    foreach ($slots as $slot_ke => $id_mapel) {
                        $slot_ke = (int) $slot_ke;
                        $id_mapel = (int) $id_mapel;
                        if ($slot_ke <= 0 || $id_mapel <= 0 || empty($mapel_limits[$id_pembelajaran][$id_mapel])) {
                            continue;
                        }

                        $key = $id_pembelajaran . '-' . $id_mapel;
                        $pakai[$key] = isset($pakai[$key]) ? $pakai[$key] + 1 : 1;
                        if ($pakai[$key] > $mapel_limits[$id_pembelajaran][$id_mapel]) {
                            continue;
                        }

                        $insert[] = [
                            'id_jadwal_header' => $id_jadwal_header,
                            'id_pembelajaran' => $id_pembelajaran,
                            'hari' => $hari,
                            'slot_ke' => $slot_ke,
                            'id_mapel' => $id_mapel,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }

        if (!empty($valid_ids)) {
            $this->db->where_in('id_pembelajaran', $valid_ids);
            $this->db->where('id_jadwal_header', $id_jadwal_header);
            $this->db->delete('jadwal_pelajaran_item');
        }

        foreach ($insert as $row) {
            $this->db->insert('jadwal_pelajaran_item', $row);
        }

        // Update header timestamp
        $this->db->where('id_jadwal_header', $id_jadwal_header)->update('jadwal_pelajaran_header', ['updated_at' => date('Y-m-d H:i:s')]);

        // If this header is active, trigger agenda auto-sync
        if ($header->status === 'Aktif') {
            $this->load->model('Agenda_pembelajaran_model', 'agenda_model');
            $pm_query = $this->db->select('id_pembelajaran_mapel')->from('pembelajaran_mapel pm')
                ->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran')
                ->where('p.status', 'Aktif')->get();
            $active_pm = ($pm_query && is_object($pm_query)) ? $pm_query->result() : [];

            foreach ($active_pm as $pm) {
                $this->agenda_model->syncSchedule($pm->id_pembelajaran_mapel, $header->tanggal_mulai_efektif);
            }
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Susunan jadwal mingguan untuk "' . html_escape($header->nama_jadwal) . '" berhasil disimpan.');
        redirect('jadwal_pelajaran/semua/' . $id_jadwal_header);
    }

    public function atur($id_pembelajaran)
    {
        redirect('jadwal_pelajaran');
    }

    public function generate_otomatis()
    {
        ifPermissions('jadwal_pelajaran_list');

        $settings = $this->getSettings(0);
        $pembelajaran = $this->getAllPembelajaran('Aktif');

        $hasil_jadwal = [];
        $guru_busy = [];

        foreach ($pembelajaran as $kelas) {
            $hasil_jadwal[$kelas->id_pembelajaran] = [];
            foreach ($this->hari as $hari) {
                if (!empty($settings[$hari]['aktif'])) {
                    $hasil_jadwal[$kelas->id_pembelajaran][$hari] = [];
                    $jumlah_jp = (int) $settings[$hari]['jumlah_jp'];
                    for ($slot = 1; $slot <= $jumlah_jp; $slot++) {
                        $hasil_jadwal[$kelas->id_pembelajaran][$hari][$slot] = null;
                    }
                }
            }
        }

        $daftar_tugas = [];
        foreach ($pembelajaran as $kelas) {
            $mapel_list = $this->getMapelPembelajaran($kelas->id_pembelajaran);
            foreach ($mapel_list as $m) {
                $jumlah_jam = (int) $m->jumlah_jam;
                if ($jumlah_jam <= 0) continue;

                $blok_list = [];
                while ($jumlah_jam > 0) {
                    if ($jumlah_jam >= 4) {
                        $blok_list[] = 2;
                        $jumlah_jam -= 2;
                    } else if ($jumlah_jam == 3) {
                        $blok_list[] = 3;
                        $jumlah_jam -= 3;
                    } else if ($jumlah_jam == 2) {
                        $blok_list[] = 2;
                        $jumlah_jam -= 2;
                    } else {
                        $blok_list[] = 1;
                        $jumlah_jam -= 1;
                    }
                }

                foreach ($blok_list as $ukuran_blok) {
                    $daftar_tugas[] = [
                        'id_pembelajaran' => $kelas->id_pembelajaran,
                        'id_mapel' => $m->id_mapel,
                        'id_ptk' => (int) $m->id_ptk,
                        'ukuran_blok' => $ukuran_blok,
                    ];
                }
            }
        }

        usort($daftar_tugas, function($a, $b) {
            return $b['ukuran_blok'] - $a['ukuran_blok'];
        });

        foreach ($daftar_tugas as $tugas) {
            $terpasang = false;
            $id_pem = $tugas['id_pembelajaran'];
            $id_mapel = $tugas['id_mapel'];
            $id_ptk = $tugas['id_ptk'];
            $ukuran = $tugas['ukuran_blok'];

            $daftar_hari_acak = $this->hari;
            shuffle($daftar_hari_acak);

            foreach ($daftar_hari_acak as $hari) {
                if (!isset($hasil_jadwal[$id_pem][$hari])) continue;

                $max_slot = (int) $settings[$hari]['jumlah_jp'];
                for ($slot = 1; $slot <= ($max_slot - $ukuran + 1); $slot++) {
                    $bisa_dipasang = true;
                    for ($offset = 0; $offset < $ukuran; $offset++) {
                        $s_cek = $slot + $offset;
                        if ($hasil_jadwal[$id_pem][$hari][$s_cek] !== null) {
                            $bisa_dipasang = false;
                            break;
                        }
                        if ($id_ptk > 0 && !empty($guru_busy[$hari][$s_cek][$id_ptk])) {
                            $bisa_dipasang = false;
                            break;
                        }
                    }

                    if ($bisa_dipasang) {
                        for ($offset = 0; $offset < $ukuran; $offset++) {
                            $s_pasang = $slot + $offset;
                            $hasil_jadwal[$id_pem][$hari][$s_pasang] = $id_mapel;
                            if ($id_ptk > 0) {
                                $guru_busy[$hari][$s_pasang][$id_ptk] = true;
                            }
                        }
                        $terpasang = true;
                        break;
                    }
                }
                if ($terpasang) break;
            }
        }

        $flat_result = [];
        foreach ($hasil_jadwal as $id_pem => $days) {
            foreach ($days as $hari => $slots) {
                foreach ($slots as $slot_ke => $id_mapel) {
                    if ($id_mapel !== null) {
                        $flat_result[] = [
                            'id_pembelajaran' => $id_pem,
                            'hari' => $hari,
                            'slot_ke' => $slot_ke,
                            'id_mapel' => $id_mapel,
                        ];
                    }
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data' => $flat_result,
            ]));
    }

    private function ensureDefaultHeader($id_tahun_pelajaran)
    {
        if ($id_tahun_pelajaran <= 0) return 0;
        if (!$this->db->table_exists('jadwal_pelajaran_header')) return 0;

        $existing_query = $this->db->get_where('jadwal_pelajaran_header', ['id_tahun_pelajaran' => $id_tahun_pelajaran]);
        $existing = ($existing_query && is_object($existing_query)) ? $existing_query->row() : null;

        if (!$existing) {
            $this->db->insert('jadwal_pelajaran_header', [
                'id_tahun_pelajaran' => $id_tahun_pelajaran,
                'nama_jadwal' => 'Jadwal Mingguan Utama',
                'status' => 'Aktif',
                'tanggal_mulai_efektif' => date('Y-m-d'),
                'keterangan' => 'Jadwal pelajaran utama semester ini',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $new_id = $this->db->insert_id();

            // Migrate any unlinked items to new_id
            if ($this->db->table_exists('jadwal_pelajaran_item')) {
                $this->db->where('id_jadwal_header', 0)->or_where('id_jadwal_header IS NULL', NULL, false)->update('jadwal_pelajaran_item', ['id_jadwal_header' => $new_id]);
            }
            return $new_id;
        }

        return $existing->id_jadwal_header;
    }

    private function getItemsByHeader($id_pembelajaran, $id_jadwal_header, $settings = null)
    {
        $items = [];
        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            return $items;
        }

        $this->db->where('id_pembelajaran', $id_pembelajaran);
        $this->db->where('id_jadwal_header', $id_jadwal_header);
        $rows_query = $this->db->get('jadwal_pelajaran_item');
        $rows = ($rows_query && is_object($rows_query)) ? $rows_query->result() : [];

        foreach ($rows as $row) {
            if ($settings !== null) {
                if (isset($settings[$row->hari]['aktif']) && $settings[$row->hari]['aktif'] === false) {
                    continue;
                }
            }
            $items[$row->hari][(int) $row->slot_ke] = (int) $row->id_mapel;
        }

        return $items;
    }

    private function getAllPembelajaran($status_tahun = 'Aktif')
    {
        if (!$this->db->table_exists('pembelajaran')) {
            return [];
        }

        $this->db->select('p.*, l.nama_lembaga, l.nama_lembaga_singkat, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, w.nama_ptk as nama_wali_kelas, COUNT(DISTINCT ps.peserta_didik_id) as jumlah_siswa');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel', 'left');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran', 'left');
        $this->db->join('ptk w', 'w.id_ptk = p.id_ptk_wali', 'left');
        $this->db->join('pembelajaran_siswa ps', 'ps.id_pembelajaran = p.id_pembelajaran', 'left');

        if ($status_tahun === 'Aktif') {
            $this->db->where('tp.status', 'Aktif');
            $this->db->where('p.status', 'Aktif');
        } else {
            $this->db->group_start();
            $this->db->where('tp.status !=', 'Aktif');
            $this->db->or_where('p.status !=', 'Aktif');
            $this->db->group_end();
        }

        $this->db->group_by('p.id_pembelajaran');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $query = $this->db->get();

        if ($query && is_object($query)) {
            return $query->result();
        }

        return [];
    }

    private function getMapelPembelajaran($id_pembelajaran)
    {
        if (!$this->db->table_exists('pembelajaran_mapel')) {
            return [];
        }

        $this->db->select('pm.*, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk, ptk.niy');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel', 'left');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('pm.id_pembelajaran', $id_pembelajaran);
        $this->db->where('pm.jumlah_jam >', 0);
        $this->db->order_by('m.nama_mapel', 'ASC');
        $query = $this->db->get();

        return ($query && is_object($query)) ? $query->result() : [];
    }

    private function getSettings($id_pembelajaran)
    {
        $settings = [];
        foreach ($this->hari as $hari) {
            $settings[$hari] = [
                'aktif' => false,
                'jam_mulai' => $hari === 'Jumat' ? '07:00' : '07:00',
                'menit_jp' => 40,
                'jumlah_jp' => $hari === 'Jumat' ? 6 : 8,
                'istirahat' => [],
            ];
        }

        if (!$this->db->table_exists('jadwal_pelajaran_pengaturan')) {
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
                $settings[$hari]['aktif'] = true;
                $settings[$hari]['istirahat'] = [['name' => 'Istirahat', 'after' => 4, 'duration' => 20]];
            }
            return $settings;
        }

        $rows_query = $this->db->get_where('jadwal_pelajaran_pengaturan', ['id_pembelajaran' => $id_pembelajaran]);
        $rows = ($rows_query && is_object($rows_query)) ? $rows_query->result() : [];

        foreach ($rows as $row) {
            $settings[$row->hari] = [
                'aktif' => true,
                'jam_mulai' => substr($row->jam_mulai, 0, 5),
                'menit_jp' => (int) $row->menit_jp,
                'jumlah_jp' => (int) $row->jumlah_jp,
                'istirahat' => json_decode($row->istirahat_json ?: '[]', true) ?: [],
            ];
        }

        if (empty($rows)) {
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
                $settings[$hari]['aktif'] = true;
                $settings[$hari]['istirahat'] = [['name' => 'Istirahat', 'after' => 4, 'duration' => 20]];
            }
        }

        return $settings;
    }

    private function getMenitJp($settings)
    {
        foreach ($this->hari as $hari) {
            if (!empty($settings[$hari]['aktif'])) {
                return (int) $settings[$hari]['menit_jp'];
            }
        }

        return 40;
    }

    private function saveSettings($id_pembelajaran)
    {
        $aktif = (array) post('aktif');
        $jam_mulai = (array) post('jam_mulai');
        $menit_jp = (array) post('menit_jp');
        $jumlah_jp = (array) post('jumlah_jp');
        $istirahat_nama = (array) post('istirahat_nama');
        $istirahat_after = (array) post('istirahat_after');
        $istirahat_duration = (array) post('istirahat_duration');

        foreach ($this->hari as $hari) {
            $is_aktif = !empty($aktif[$hari]);
            $start_time = isset($jam_mulai[$hari]) && trim((string) $jam_mulai[$hari]) !== '' ? trim((string) $jam_mulai[$hari]) : '07:00';
            $jp_minutes = isset($menit_jp[$hari]) ? max(1, (int) $menit_jp[$hari]) : 40;
            $total_jp = isset($jumlah_jp[$hari]) ? max(1, (int) $jumlah_jp[$hari]) : 8;
            $nama = isset($istirahat_nama[$hari]) ? $istirahat_nama[$hari] : [];
            $after = isset($istirahat_after[$hari]) ? $istirahat_after[$hari] : [];
            $duration = isset($istirahat_duration[$hari]) ? $istirahat_duration[$hari] : [];

            $this->db->where('id_pembelajaran', $id_pembelajaran);
            $this->db->where('hari', $hari);
            $this->db->delete('jadwal_pelajaran_pengaturan');

            if ($is_aktif) {
                $istirahat = $this->susunIstirahat($nama, $after, $duration);
                $this->db->insert('jadwal_pelajaran_pengaturan', [
                    'id_pembelajaran' => $id_pembelajaran,
                    'hari' => $hari,
                    'jam_mulai' => strlen($start_time) === 5 ? $start_time . ':00' : $start_time,
                    'menit_jp' => $jp_minutes,
                    'jumlah_jp' => $total_jp,
                    'istirahat_json' => json_encode($istirahat),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function susunIstirahat($name, $after, $duration)
    {
        $breaks = [];
        if (!is_array($after)) {
            return $breaks;
        }

        foreach ($after as $index => $slot) {
            $slot = (int) $slot;
            $durasi = isset($duration[$index]) ? (int) $duration[$index] : 0;
            $nama = isset($name[$index]) ? trim((string) $nama[$index]) : '';
            if ($slot >= 0 && $durasi > 0) {
                $breaks[] = ['name' => $nama !== '' ? $nama : 'Waktu Khusus', 'after' => $slot, 'duration' => $durasi];
            }
        }

        usort($breaks, function ($a, $b) {
            return $a['after'] - $b['after'];
        });

        return $breaks;
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('jadwal_pelajaran_header')) {
            $this->dbforge->add_field([
                'id_jadwal_header' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_tahun_pelajaran' => ['type' => 'INT', 'constraint' => 11],
                'nama_jadwal' => ['type' => 'VARCHAR', 'constraint' => 255],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Draft'],
                'tanggal_mulai_efektif' => ['type' => 'DATE', 'null' => true],
                'tanggal_akhir_efektif' => ['type' => 'DATE', 'null' => true],
                'keterangan' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_jadwal_header', true);
            $this->dbforge->add_key('id_tahun_pelajaran');
            $this->dbforge->create_table('jadwal_pelajaran_header', true);
        }

        if (!$this->db->table_exists('jadwal_pelajaran_pengaturan')) {
            $this->dbforge->add_field([
                'id_pengaturan' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran' => ['type' => 'INT', 'constraint' => 11],
                'hari' => ['type' => 'VARCHAR', 'constraint' => 20],
                'jam_mulai' => ['type' => 'TIME'],
                'menit_jp' => ['type' => 'INT', 'constraint' => 11, 'default' => 40],
                'jumlah_jp' => ['type' => 'INT', 'constraint' => 11, 'default' => 8],
                'istirahat_json' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_pengaturan', true);
            $this->dbforge->add_key('id_pembelajaran');
            $this->dbforge->create_table('jadwal_pelajaran_pengaturan', true);
        }

        if (!$this->db->table_exists('jadwal_pelajaran_item')) {
            $this->dbforge->add_field([
                'id_jadwal' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_jadwal_header' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'id_pembelajaran' => ['type' => 'INT', 'constraint' => 11],
                'hari' => ['type' => 'VARCHAR', 'constraint' => 20],
                'slot_ke' => ['type' => 'INT', 'constraint' => 11],
                'id_mapel' => ['type' => 'INT', 'constraint' => 11],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_jadwal', true);
            $this->dbforge->add_key('id_jadwal_header');
            $this->dbforge->add_key('id_pembelajaran');
            $this->dbforge->create_table('jadwal_pelajaran_item', true);
        } else {
            if (!$this->db->field_exists('id_jadwal_header', 'jadwal_pelajaran_item')) {
                $this->dbforge->add_column('jadwal_pelajaran_item', [
                    'id_jadwal_header' => ['type' => 'INT', 'constraint' => 11, 'default' => 0]
                ]);
            }
        }
    }
}
