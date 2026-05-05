<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model', 'master_model');
    }

    public $jenis_ruangan = 'master_jenis_ruangan';
    public $jenis_sarana = 'master_jenis_sarana';
    public $standar_sarana = 'master_standar_sarana';
    public $rombel = 'rombel';
    public $mapel = 'mapel';
    public $lembaga = 'lembaga';
    public $tingkat_sekolah = 'master_tingkat_sekolah';

    public function tingkatSekolah()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->subtitle = 'Tingkat Sekolah';
        $this->page_data['page']->subtitleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->icon = 'solar:layers-linear';
        $this->page_data['tingkat'] = $this->master_model->getTingkatSekolah();
        $this->load->view('master/v_tingkat_sekolah_list', $this->page_data);
    }

    public function tingkatSekolahTambah()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->subtitle = 'Tambah Tingkat Sekolah';
        $this->page_data['page']->subtitleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->icon = 'solar:layers-linear';
        $this->page_data['row'] = null;
        $this->load->view('master/v_tingkat_sekolah_form', $this->page_data);
    }

    public function tingkatSekolahEdit($id)
    {
        $this->page_data['row'] = $this->master_model->getDetailTingkatSekolah($id);
        if (!$this->page_data['row']) show_404();

        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->subtitle = 'Edit Tingkat Sekolah';
        $this->page_data['page']->subtitleUrl = 'master/tingkatSekolah';
        $this->page_data['page']->icon = 'solar:layers-linear';
        $this->load->view('master/v_tingkat_sekolah_form', $this->page_data);
    }

    public function tingkatSekolahSimpan()
    {
        postAllowed();
        $id = post('id_tingkat_sekolah');
        $nama = post('nama_tingkat');

        // Cek duplikat
        $this->db->where('nama_tingkat', $nama);
        if ($id) $this->db->where('id_tingkat_sekolah !=', $id);
        $check = $this->db->get($this->tingkat_sekolah);
        if ($check && $check->num_rows() > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Gagal! Tingkat '$nama' sudah ada.");
            redirect('master/tingkatSekolah');
            return;
        }

        $data = [
            'nama_tingkat'  => $nama,
            'tingkat_angka' => post('tingkat_angka'),
            'status'        => post('status'),
        ];

        if ($id) {
            $this->db->where('id_tingkat_sekolah', $id);
            $this->db->update($this->tingkat_sekolah, $data);
            $this->activity_model->add(logged('name') . ' Mengubah Tingkat Sekolah: ' . $nama);
            $this->session->set_flashdata('alert', 'Data berhasil diperbarui');
        } else {
            $this->db->insert($this->tingkat_sekolah, $data);
            $this->activity_model->add(logged('name') . ' Menambah Tingkat Sekolah: ' . $nama);
            $this->session->set_flashdata('alert', 'Data berhasil ditambahkan');
        }

        $this->session->set_flashdata('alert-type', 'success');
        redirect('master/tingkatSekolah');
    }

    public function tingkatSekolahDelete($id)
    {
        $row = $this->master_model->getDetailTingkatSekolah($id);
        if ($row) {
            $this->db->delete($this->tingkat_sekolah, ['id_tingkat_sekolah' => $id]);
            $this->activity_model->add(logged('name') . ' Menghapus Tingkat Sekolah: ' . $row->nama_tingkat);
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Data berhasil dihapus');
        }
        redirect('master/tingkatSekolah');
    }

    public function lembaga()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/lembaga';
        $this->page_data['page']->subtitle = 'Master Lembaga';
        $this->page_data['page']->subtitleUrl = 'master/lembaga';
        $this->page_data['page']->icon = 'solar:buildings-2-linear';
        $this->page_data['lembaga'] = $this->master_model->getAllLembaga();
        $this->load->view('master/v_lembaga_list', $this->page_data);
    }

    public function lembagaTambah()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/lembaga';
        $this->page_data['page']->subtitle = 'Tambah Lembaga';
        $this->page_data['page']->icon = 'solar:buildings-2-linear';
        $this->page_data['page']->subtitleUrl = 'master/lembaga';
        $this->page_data['ptk'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->load->view('master/v_lembaga_form', $this->page_data);
    }

    public function lembagaEdit($id)
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/lembaga';
        $this->page_data['page']->subtitle = 'Edit Lembaga';
        $this->page_data['page']->icon = 'solar:buildings-2-linear';
        $this->page_data['page']->subtitleUrl = 'master/lembaga';
        $this->page_data['row'] = $this->master_model->getDetailLembaga($id);
        if (!$this->page_data['row']) show_404();
        $this->page_data['ptk'] = $this->db->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->load->view('master/v_lembaga_form', $this->page_data);
    }

    public function lembagaSimpan()
    {
        postAllowed();
        $data = $this->_lembagaData();
        if ($this->db->insert($this->lembaga, $data)) {
            $id = $this->db->insert_id();
            $this->_uploadLembagaFiles($id);
            $this->activity_model->add(logged('name') . ' Menambah Lembaga: ' . $data['nama_lembaga'], logged('id'));
            if (!$this->session->flashdata('alert')) {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Lembaga Berhasil Ditambahkan');
            }
        } else {
            $error = $this->db->error();
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal Menambah Lembaga: ' . $error['message']);
        }
        redirect('master/lembaga');
    }

    public function lembagaUpdate($id)
    {
        postAllowed();
        $data = $this->_lembagaData();
        $this->db->where('id_lembaga', $id);
        if ($this->db->update($this->lembaga, $data)) {
            $this->_uploadLembagaFiles($id);
            $this->activity_model->add(logged('name') . ' Mengubah Lembaga: ' . $data['nama_lembaga'], logged('id'));
            if (!$this->session->flashdata('alert')) {
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Lembaga Berhasil Diperbarui');
            }
        } else {
            $error = $this->db->error();
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal Memperbarui Lembaga: ' . $error['message']);
        }
        redirect('master/lembaga');
    }

    public function lembagaDelete($id)
    {
        $row = $this->master_model->getDetailLembaga($id);
        if ($row) {
            $this->_hapusFileLembaga($row->berkas_akreditasi);
            $this->_hapusFileLembaga($row->logo);
            $this->_hapusFileLembaga($row->foto_kepsek);
            $this->db->delete($this->lembaga, ['id_lembaga' => $id]);
            $this->activity_model->add(logged('name') . ' Menghapus Lembaga: ' . $row->nama_lembaga, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Lembaga Berhasil Dihapus');
        }
        redirect('master/lembaga');
    }

    private function _lembagaData()
    {
        $get_name = function ($table, $pk, $id) {
            if (!empty($id) && is_numeric($id)) {
                $row = $this->db->get_where($table, [$pk => $id])->row();
                return $row ? $row->nama : $id;
            }
            return $id ?: null;
        };

        return [
            'nama_lembaga' => post('nama_lembaga'),
            'id_ptk_kepsek' => post('id_ptk_kepsek') ?: null,
            'npsn' => post('npsn') ?: null,
            'bentuk_pendidikan' => post('bentuk_pendidikan') ?: null,
            'status' => post('status'),
            'akreditasi' => post('akreditasi') ?: null,
            'no_sk_akreditasi' => post('no_sk_akreditasi') ?: null,
            'alamat' => post('alamat') ?: null,
            'rt' => post('rt') ?: null,
            'rw' => post('rw') ?: null,
            'kelurahan' => $get_name('reg_kelurahan', 'id_kel', post('kelurahan')),
            'kecamatan' => $get_name('reg_kecamatan', 'id_kec', post('kecamatan')),
            'kabupaten' => $get_name('reg_kabupaten', 'id_kab', post('kabupaten')),
            'provinsi' => $get_name('reg_provinsi', 'id_prov', post('provinsi')),
            'telepon' => post('telepon') ?: null,
            'email' => post('email') ?: null,
            'website' => post('website') ?: null,
            'instagram' => post('instagram') ?: null,
            'tiktok' => post('tiktok') ?: null,
            'youtube' => post('youtube') ?: null,
        ];
    }

    private function _uploadLembagaFiles($id)
    {
        $this->load->library('upload');
        $files = ['berkas_akreditasi', 'logo', 'foto_kepsek'];
        $data_update = [];

        // Ambil data saat ini untuk proses penghapusan file lama jika ada update
        $current = $this->master_model->getDetailLembaga($id);

        $upload_path = FCPATH . 'uploads/lembaga/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        foreach ($files as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $config = [
                    'upload_path' => $upload_path,
                    'allowed_types' => 'pdf|jpg|jpeg|png',
                    'file_name' => $field . '-' . $id . '-' . time(),
                    'overwrite' => false
                ];
                $this->upload->initialize($config);
                if ($this->upload->do_upload($field)) {
                    // Hapus file lama jika field tersebut sebelumnya sudah ada isinya
                    if ($current && !empty($current->$field)) {
                        $this->_hapusFileLembaga($current->$field);
                    }
                    $data_update[$field] = $this->upload->data('file_name');
                } else {
                    // Tangkap pesan error dari CI
                    $error = strip_tags($this->upload->display_errors());
                    $this->session->set_flashdata('alert-type', 'danger');
                    $this->session->set_flashdata('alert', "Upload $field gagal: " . $error);
                }
            }
        }
        if (!empty($data_update)) {
            $this->db->update($this->lembaga, $data_update, ['id_lembaga' => $id]);
        }
    }

    private function _hapusFileLembaga($file_name)
    {
        if ($file_name) {
            $path = FCPATH . 'uploads/lembaga/' . $file_name;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }



    public function jenisRuangan()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_ruangan'] = $this->master_model->getJenisRuangan();
        $this->load->view('master/v_jenis_ruangan_list', $this->page_data);
    }

    public function jenisRuanganTambah()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->subsubtitle = 'Tambah';
        $this->page_data['page']->subsubtitleUrl = 'sarpras/tanahTabah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->load->view('sarpras/v_tanah_add', $this->page_data);
    }

    public function jenisRuanganSimpan()
    {
        $nama = $this->input->post('nama_jenis_ruangan');

        $caridata = $this->master_model->jenisRuanganNamaExist($nama);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Update Gagal! Jenis ' . $nama . ' sudah tersedia.');
            redirect('master/jenisRuangan');
        } else {

            $data = array(
                'nama_jenis_ruangan' => $this->input->post('nama_jenis_ruangan'),
                'status' => $this->input->post('status'),
            );
            $this->db->insert($this->jenis_ruangan, $data);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data jenis ruangan baru - ' . $nama, logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Tambah Jenis Ruangan Berhasil');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Tambah Jenis Ruangan Gagal!');
            }
        }
        redirect('master/jenisRuangan');
    }

    public function jenisRuanganEdit($id)
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_ruangan'] = $this->master_model->getDetailJenisRuangan($id);
        $this->load->view('master/v_jenis_ruangan_form', $this->page_data);
    }

    public function jenisRuanganUpdate($id)
    {
        $nama = $this->input->post('nama_jenis_ruangan');

        $caridata = $this->master_model->jenisRuanganNamaExist($nama);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Update Gagal! Jenis ' . $nama . ' sudah tersedia.');
            redirect('master/jenisRuangan');
        } else {

            $data = array(
                'nama_jenis_ruangan' => $this->input->post('nama_jenis_ruangan'),
                'status' => $this->input->post('status'),
            );
            $this->db->where('id_jenis_ruangan', $id);
            $this->db->update($this->jenis_ruangan, $data);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data jenis ruangan baru - ' . $nama, logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Update Jenis Ruangan Berhasil');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Update Jenis Ruangan Gagal!');
            }

            redirect('master/jenisRuangan');
        }
    }

    public function jenisRuanganDelete($id)
    {

        $caridata = $this->master_model->jenisRuanganExist($id);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Tidak bisa dihapus! Terdapat " . $caridata . " ruangan yang menggunakan jenis ini.");
            redirect('master/jenisRuangan');
        } else {
            $this->db->where('id_jenis_ruangan', $id);
            $this->db->delete($this->jenis_ruangan);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan hapus data jenis ruangan', logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Jenis Ruangan Berhasil Dihapus');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Jenis Ruangan Gagal Dihapus!');
            }

            redirect('master/jenisRuangan');
        }
    }

    public function jenisSarana()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_sarana'] = $this->master_model->getJenisSarana();
        $this->load->view('master/v_jenis_sarana_list', $this->page_data);
    }

    public function jenisSaranaTambah()
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->subsubtitle = 'Tambah';
        $this->page_data['page']->subsubtitleUrl = 'sarpras/tanahTabah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->load->view('sarpras/v_tanah_add', $this->page_data);
    }

    public function jenisSaranaSimpan()
    {
        $nama = $this->input->post('nama_jenis_sarana');
        $data = array(
            'nama_jenis_sarana' => $this->input->post('nama_jenis_sarana'),
            'status' => $this->input->post('status'),
        );
        $this->db->insert($this->jenis_sarana, $data);

        $dbaseerror = $this->db->error();
        $numbererror = $dbaseerror['code'];
        $messagerror = $dbaseerror['message'];

        if (!$numbererror) {
            $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan input data jenis Sarana baru - ' . $nama, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Tambah Jenis Sarana Berhasil');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Tambah Jenis Sarana Gagal!');
        }

        redirect('master/jenisSarana');
    }

    public function jenisSaranaEdit($id)
    {
        $this->page_data['page']->title = 'Sarpras';
        $this->page_data['page']->titleUrl = 'sarpras/tanah';
        $this->page_data['page']->subtitle = 'Tanah';
        $this->page_data['page']->subtitleUrl = 'sarpras/tanah';
        $this->page_data['page']->icon = 'hugeicons:maps-square-01';
        $this->page_data['jenis_sarana'] = $this->master_model->getDetailJenisSarana($id);
        $this->load->view('master/v_jenis_sarana_form', $this->page_data);
    }

    public function jenisSaranaUpdate($id)
    {
        $nama = $this->input->post('nama_jenis_sarana');

        $caridata = $this->master_model->jenisSaranaNamaExist($nama);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Update Gagal! Jenis ' . $nama . ' sudah tersedia.');
            redirect('master/jenisSarana');
        } else {

            $data = array(
                'nama_jenis_sarana' => $this->input->post('nama_jenis_sarana'),
                'status' => $this->input->post('status'),
            );
            $this->db->where('id_jenis_sarana', $id);
            $this->db->update($this->jenis_sarana, $data);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan update data jenis sarana baru - ' . $nama, logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Update Jenis Sarana Berhasil');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Update Jenis Sarana Gagal!');
            }

            redirect('master/jenisSarana');
        }
    }

    public function jenisSaranaDelete($id)
    {

        $caridata = $this->master_model->jenisSaranaNamaExist($id);

        if ($caridata > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Tidak bisa dihapus! Jenis ini masih digunakan.");
            redirect('master/jenisSarana');
        } else {
            $this->db->where('id_jenis_sarana', $id);
            $this->db->delete($this->jenis_sarana);

            $dbaseerror = $this->db->error();
            $numbererror = $dbaseerror['code'];
            $messagerror = $dbaseerror['message'];

            if (!$numbererror) {
                $this->activity_model->add(logged('name') . ' (' . logged('username') . ') Melakukan hapus data jenis sarana', logged('id'));
                $this->session->set_flashdata('alert-type', 'success');
                $this->session->set_flashdata('alert', 'Jenis Sarana Berhasil Dihapus');
            } else {
                $this->session->set_flashdata('alert-type', 'danger');
                $this->session->set_flashdata('alert', 'Jenis Sarana Gagal Dihapus!');
            }

            redirect('master/jenisSarana');
        }
    }

    public function rombel()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/rombel';
        $this->page_data['page']->subtitle = 'Rombongan Belajar';
        $this->page_data['page']->subtitleUrl = 'master/rombel';
        $this->page_data['page']->icon = 'solar:users-group-two-rounded-linear';
        $this->page_data['rombel'] = $this->db->get($this->rombel)->result();
        $this->load->view('master/v_rombel_list', $this->page_data);
    }

    public function rombelSimpan()
    {
        postAllowed();
        $nama = post('nama_rombel');

        $data = [
            'nama_rombel' => $nama,
            'status' => post('status'),
        ];

        if ($this->db->insert($this->rombel, $data)) {
            $this->activity_model->add(logged('name') . ' Menambah Rombel Baru: ' . $nama, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Rombel Berhasil Ditambahkan');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal Menambah Rombel');
        }
        redirect('master/rombel');
    }

    public function rombelEdit($id)
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/rombel';
        $this->page_data['page']->subtitle = 'Edit Rombel';
        $this->page_data['page']->subtitleUrl = 'master/rombelEdit/' . $id;
        $this->page_data['page']->icon = 'solar:users-group-two-rounded-linear';
        $this->page_data['row'] = $this->db->get_where($this->rombel, ['id_rombel' => $id])->row();

        if (!$this->page_data['row']) {
            show_404();
        }

        $this->load->view('master/v_rombel_form', $this->page_data);
    }

    public function rombelUpdate($id)
    {
        postAllowed();
        $nama = post('nama_rombel');

        $data = [
            'nama_rombel' => $nama,
            'status' => post('status'),
        ];

        $this->db->where('id_rombel', $id);
        if ($this->db->update($this->rombel, $data)) {
            $this->activity_model->add(logged('name') . ' Mengubah data Rombel: ' . $nama, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Rombel Berhasil Diperbarui');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal Memperbarui Rombel');
        }
        redirect('master/rombel');
    }

    public function rombelDelete($id)
    {
        $row = $this->db->get_where($this->rombel, ['id_rombel' => $id])->row();
        if (!$row) {
            show_404();
        }

        // Cek apakah rombel sedang digunakan oleh siswa
        $cekSiswa = $this->db->get_where('siswa', ['rombel' => $row->nama_rombel])->num_rows();

        if ($cekSiswa > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Rombel tidak bisa dihapus karena masih memiliki ' . $cekSiswa . ' siswa.');
            redirect('master/rombel');
            return;
        }

        $this->db->where('id_rombel', $id);
        if ($this->db->delete($this->rombel)) {
            $this->activity_model->add(logged('name') . ' Menghapus Rombel: ' . $row->nama_rombel, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Rombel Berhasil Dihapus');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal Menghapus Rombel');
        }
        redirect('master/rombel');
    }

    public function mapel()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/mapel';
        $this->page_data['page']->subtitle = 'Mata Pelajaran';
        $this->page_data['page']->subtitleUrl = 'master/mapel';
        $this->page_data['page']->icon = 'solar:notebook-linear';
        $this->page_data['mapel'] = $this->master_model->getMapel();
        $this->load->view('mapel/v_mapel_list', $this->page_data);
    }

    public function mapelTambah()
    {
        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/mapel';
        $this->page_data['page']->subtitle = 'Tambah Mapel';
        $this->page_data['page']->subtitleUrl = 'master/mapelTambah';
        $this->page_data['page']->icon = 'solar:notebook-linear';
        $this->page_data['row'] = null;
        $this->load->view('mapel/v_mapel_form', $this->page_data);
    }

    public function mapelEdit($id)
    {
        $this->page_data['row'] = $this->master_model->getDetailMapel($id);
        if (!$this->page_data['row']) show_404();

        $this->page_data['page']->title = 'Master Data';
        $this->page_data['page']->titleUrl = 'master/mapel';
        $this->page_data['page']->subtitle = 'Edit Mapel';
        $this->page_data['page']->subtitleUrl = 'master/mapelEdit/' . $id;
        $this->page_data['page']->icon = 'solar:notebook-linear';
        $this->load->view('mapel/v_mapel_form', $this->page_data);
    }

    public function mapelSimpan()
    {
        postAllowed();
        $nama = post('nama_mapel');
        $id = post('id_mapel');

        // Cek Duplikat Nama Mapel
        $this->db->where('nama_mapel', $nama);
        if ($id) $this->db->where('id_mapel !=', $id);
        $exists = $this->db->get($this->mapel)->num_rows();

        if ($exists > 0) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', "Gagal! Mata Pelajaran '$nama' sudah terdaftar.");
            redirect('master/mapel');
            return;
        }

        $data = [
            'nama_mapel'    => $nama,
            'mapel_singkat' => post('mapel_singkat'),
            'status'        => post('status'),
        ];

        if ($id) {
            $this->db->where('id_mapel', $id);
            $this->db->update($this->mapel, $data);
            $this->activity_model->add(logged('name') . ' Mengubah Mapel: ' . $data['nama_mapel'], logged('id'));
            $this->session->set_flashdata('alert', 'Data Mapel berhasil diperbarui');
        } else {
            $this->db->insert($this->mapel, $data);
            $this->activity_model->add(logged('name') . ' Menambah Mapel: ' . $data['nama_mapel'], logged('id'));
            $this->session->set_flashdata('alert', 'Data Mapel berhasil ditambahkan');
        }

        $this->session->set_flashdata('alert-type', 'success');
        redirect('master/mapel');
    }

    public function mapelDelete($id)
    {
        $row = $this->master_model->getDetailMapel($id);
        if ($row) {
            $this->db->delete($this->mapel, ['id_mapel' => $id]);
            $this->activity_model->add(logged('name') . ' Menghapus Mapel: ' . $row->nama_mapel, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Data Mapel berhasil dihapus');
        }
        redirect('master/mapel');
    }
}
