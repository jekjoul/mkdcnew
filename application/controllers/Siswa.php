<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends MY_Controller
{
    public $table = 'siswa';
    
    private $school_coordinate = '-7.1454257,108.2664001';
    
    private $pekerjaan_options = [
        'Wiraswasta',
        'Karyawan Swasta',
        'Buruh Harian Lepas',
        'ASN/PPPK',
        'TNI',
        'Polri',
        'Ustadz/Mubaligh',
        'Petani/Peternak',
        'Ibu Rumah Tangga',
        'Sudah Meninggal',
    ];

    private $jenis_tempat_tinggal_options = ['Bersama Orang Tua', 'Bersama Saudara', 'Pondok Pesantren', 'Panti Asuhan'];
    private $alat_transportasi_options = ['Jalan Kaki', 'Transportasi Umum', 'Kendaraan Roda Dua', 'Kendaraan Roda Empat'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSiswaBukuIndukColumns();
        $this->load->model('Alumni_model');
        $this->Alumni_model->ensureAlumniTables();
    }

    public function all()
    {
        ifPermissions('siswa_list');
        $this->loadAllByPembelajaranStatus('Aktif');
    }

    public function nonaktif()
    {
        ifPermissions('siswa_list');
        $this->loadAllByPembelajaranStatus('Nonaktif');
    }

    public function kelulusan()
    {
        ifPermissions('siswa_list'); // Assuming they need view student permissions, or maybe a new permission

        $this->page_data['page']->title = 'Kelulusan Siswa';
        $this->page_data['page']->titleUrl = 'siswa/kelulusan';
        $this->page_data['page']->subtitle = 'Daftar Calon Lulusan (Kelas 9 & 12)';
        $this->page_data['page']->subtitleUrl = 'siswa/kelulusan';
        $this->page_data['page']->icon = 'lucide:graduation-cap';

        // Ambil siswa kelas 9 dan 12 yang masih aktif
        $this->db->select('s.*, r.nama_rombel, t.nama_tingkat, l.nama_lembaga');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        
        $this->db->where('tp.status', 'Aktif');
        $this->db->where('s.status_keaktifan', 'Aktif');
        $this->db->where_in('t.tingkat_angka', [9, 12]);
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('s.nama_siswa', 'ASC');
        
        $this->page_data['siswa'] = $this->db->get()->result();

        $this->load->view('siswa/v_siswa_kelulusan', $this->page_data);
    }

    public function proses_kelulusan()
    {
        ifPermissions('siswa_edit'); 
        
        $siswa_ids = $this->input->post('siswa_ids');
        $tanggal_alumni = $this->input->post('tanggal_alumni') ?: date('Y-m-d');
        $status_alumni = 'Lulus';

        if (!empty($siswa_ids) && is_array($siswa_ids)) {
            $count = 0;
            foreach ($siswa_ids as $id_siswa) {
                // moveSiswaToAlumni returns null if failed or already moved
                $id_alumni = $this->Alumni_model->moveSiswaToAlumni($id_siswa, $status_alumni, $tanggal_alumni);
                $count++;
            }
            $this->session->set_flashdata('message', "$count siswa berhasil diluluskan dan dipindah ke data Alumni.");
            $this->session->set_flashdata('message_type', 'success');
        } else {
            $this->session->set_flashdata('message', 'Tidak ada siswa yang dipilih.');
            $this->session->set_flashdata('message_type', 'danger');
        }

        redirect('siswa/kelulusan', 'refresh');
    }

    private function loadAllByPembelajaranStatus($status_tahun)
    {
        $is_nonaktif = $status_tahun !== 'Aktif';
        $this->page_data['page']->title = 'Siswa';
        $this->page_data['page']->titleUrl = $is_nonaktif ? 'siswa/nonaktif' : 'siswa/all';
        $this->page_data['page']->subtitle = $is_nonaktif ? 'Data Siswa Tidak Aktif' : 'Daftar Siswa';
        $this->page_data['page']->subtitleUrl = $is_nonaktif ? 'siswa/nonaktif' : 'siswa/all';
        $this->page_data['page']->icon = 'icon-park-outline:user-business';

        if ($is_nonaktif) {
            $this->db->select('s.*');
            $this->db->from('siswa s');
            $this->db->where('s.status_keaktifan !=', 'Aktif');
            $this->db->order_by('s.nama_siswa', 'ASC');
            $this->page_data['siswa'] = $this->db->get()->result();
        } else {
            $this->db->select('s.*, t.nama_tingkat');
            $this->db->from('siswa s');
            $this->db->join('pembelajaran_siswa ps', 's.id_siswa = ps.peserta_didik_id AND ps.id_pembelajaran IN (
                SELECT p_active.id_pembelajaran 
                FROM pembelajaran p_active 
                JOIN pembelajaran_tahun_pelajaran tp_active ON tp_active.id_tahun_pelajaran = p_active.id_tahun_pelajaran
                WHERE tp_active.status = "Aktif"
            )', 'left', FALSE);
            $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran', 'left');
            $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
            $this->db->where('s.status_keaktifan', 'Aktif');
            $this->db->group_by('s.id_siswa');
            $this->db->order_by('s.nama_siswa', 'ASC');
            $this->page_data['siswa'] = $this->db->get()->result();
        }

        $this->page_data['judul_tabel'] = $is_nonaktif ? 'Data Siswa Tidak Aktif' : 'Data Siswa Tahun Aktif';
        $this->page_data['is_nonaktif'] = $is_nonaktif;

        $this->load->view('siswa/v_siswa_list', $this->page_data);
    }

    public function pembelajaran($id_pembelajaran = null)
    {
        if (!$id_pembelajaran) {
            redirect('siswa/all');
        }

        $pembelajaran = $this->getPembelajaranDetail($id_pembelajaran);
        if (!$pembelajaran) {
            show_404();
        }

        $rombel_label = $this->formatRombelPembelajaran($pembelajaran);

        $this->page_data['page']->title = 'Siswa';
        $this->page_data['page']->titleUrl = 'siswa/all';
        $this->page_data['page']->subtitle = $pembelajaran->nama_lembaga . ' - ' . $rombel_label;
        $this->page_data['page']->subtitleUrl = 'siswa/pembelajaran/' . $id_pembelajaran;
        $this->page_data['page']->icon = 'icon-park-outline:user-business';

        $this->db->select('s.*');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('siswa s', 's.id_siswa = ps.peserta_didik_id');
        $this->db->where('ps.id_pembelajaran', $id_pembelajaran);
        $this->db->order_by('s.nama_siswa', 'ASC');
        $this->page_data['siswa'] = $this->db->get()->result();
        $this->page_data['judul_tabel'] = 'Data Siswa ' . $rombel_label;
        $this->page_data['tambah_url'] = 'pembelajaran/daftar_siswa/' . $id_pembelajaran;
        $this->page_data['tambah_label'] = 'Atur Siswa';

        $this->load->view('siswa/v_siswa_list', $this->page_data);
    }

    public function detail($id = null)
    {
        ifPermissions('siswa_view');
        if (!$id) {
            redirect('siswa/all');
        }

        $row = $this->db->get_where($this->table, ['id_siswa' => $id])->row();
        if (!$row) {
            show_404();
        }

        $this->page_data['page']->title = 'Siswa';
        $this->page_data['page']->titleUrl = 'siswa/all';
        $this->page_data['page']->subtitle = $row->nama_siswa;
        $this->page_data['page']->subtitleUrl = 'siswa/detail/' . $id;
        $this->page_data['page']->icon = 'icon-park-outline:user-business';
        $this->page_data['row'] = $row;
        $this->page_data['school_coordinate'] = $this->school_coordinate;
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->page_data['foto'] = $this->db->get_where('siswa_foto', ['id_siswa' => $id])->result();
        $this->db->order_by('nama_jenis_dokumen', 'ASC');
        $this->page_data['jenis_dokumen'] = $this->db->get_where('master_jenis_dokumen_siswa', ['status' => 'Aktif'])->result();
        $this->db->select('siswa_dokumen.*, master_jenis_dokumen_siswa.nama_jenis_dokumen');
        $this->db->from('siswa_dokumen');
        $this->db->join('master_jenis_dokumen_siswa', 'master_jenis_dokumen_siswa.id_jenis_dokumen = siswa_dokumen.id_jenis_dokumen', 'left');
        $this->db->where('siswa_dokumen.id_siswa', $id);
        $this->db->order_by('master_jenis_dokumen_siswa.nama_jenis_dokumen', 'ASC');
        $this->page_data['dokumen'] = $this->db->get()->result();
        
        $this->db->select('r.nama_rombel, t.nama_tingkat');
        $this->db->from('pembelajaran p');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('tp.status', 'Aktif');
        $this->db->group_by('r.nama_rombel, t.nama_tingkat');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->page_data['rombel_aktif'] = $this->db->get()->result();
        
        $this->page_data['pekerjaan_options'] = $this->pekerjaan_options;
        $this->page_data['jenis_tempat_tinggal_options'] = $this->jenis_tempat_tinggal_options;
        $this->page_data['alat_transportasi_options'] = $this->alat_transportasi_options;
        $this->page_data['lembaga'] = $this->db->order_by('nama_lembaga', 'ASC')->get('lembaga')->result();
        $this->load->view('siswa/v_siswa_detail', $this->page_data);
    }

    public function siswaAdd()
    {
        ifPermissions('siswa_add');
        $this->page_data['page']->title = 'Siswa';
        $this->page_data['page']->titleUrl = 'siswa/all';
        $this->page_data['page']->subtitle = 'Tambah';
        $this->page_data['page']->subtitleUrl = 'siswa/siswaAdd';
        $this->page_data['page']->icon = 'icon-park-outline:user-business';
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->page_data['school_coordinate'] = $this->school_coordinate;
        
        $this->db->select('r.nama_rombel, t.nama_tingkat');
        $this->db->from('pembelajaran p');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('tp.status', 'Aktif');
        $this->db->group_by('r.nama_rombel, t.nama_tingkat');
        $this->db->order_by('t.tingkat_angka', 'ASC');
        $this->db->order_by('r.nama_rombel', 'ASC');
        $this->page_data['rombel_aktif'] = $this->db->get()->result();
        
        $this->page_data['pekerjaan_options'] = $this->pekerjaan_options;
        $this->page_data['jenis_tempat_tinggal_options'] = $this->jenis_tempat_tinggal_options;
        $this->page_data['alat_transportasi_options'] = $this->alat_transportasi_options;
        $this->page_data['lembaga'] = $this->db->order_by('nama_lembaga', 'ASC')->get('lembaga')->result();
        $this->load->view('siswa/v_siswa_form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        ifPermissions('siswa_add');
        $data = $this->siswaData();

        if ($this->db->insert($this->table, $data)) {
            $id = $this->db->insert_id();
            $this->uploadFotoSiswa($id);
            $this->activity_model->add(logged('name') . ' Menambah data siswa: ' . $data['nama_siswa'], logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            if ($this->isAlumniStatus($data['status_keaktifan'])) {
                $id_alumni = $this->moveSiswaToAlumni($id, $data['status_keaktifan']);
                $this->session->set_flashdata('alert', 'Data Siswa Berhasil Ditambahkan dan Dipindahkan ke Alumni');
                redirect($id_alumni ? 'alumni/detail/' . $id_alumni : 'alumni');
            }
            $this->session->set_flashdata('alert', 'Data Siswa Berhasil Ditambahkan');
            redirect('siswa/detail/' . $id);
        }

        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Data Siswa Gagal Ditambahkan');
        redirect('siswa/siswaAdd');
    }

    public function update($id)
    {
        postAllowed();
        ifPermissions('siswa_edit');
        $siswa = $this->db->get_where($this->table, ['id_siswa' => $id])->row();
        if (!$siswa) {
            show_404();
        }

        $data = $this->siswaData();
        $this->db->where('id_siswa', $id);
        if ($this->db->update($this->table, $data)) {
            $this->uploadFotoSiswa($id);
            $this->activity_model->add(logged('name') . ' Mengubah data siswa: ' . $data['nama_siswa'], logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            if ($this->isAlumniStatus($data['status_keaktifan'])) {
                $id_alumni = $this->moveSiswaToAlumni($id, $data['status_keaktifan']);
                $this->session->set_flashdata('alert', 'Data Siswa Berhasil Diperbarui dan Dipindahkan ke Alumni');
                redirect($id_alumni ? 'alumni/detail/' . $id_alumni : 'alumni');
            }
            $this->session->set_flashdata('alert', 'Data Siswa Berhasil Diperbarui');
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Data Siswa Gagal Diperbarui');
        }
        redirect('siswa/detail/' . $id);
    }

    public function mutasi($id)
    {
        postAllowed();
        ifPermissions('siswa_edit');
        $siswa = $this->db->get_where($this->table, ['id_siswa' => $id])->row();
        if (!$siswa) {
            show_404();
        }

        $status = post('status_alumni') ?: 'Keluar';
        if (!$this->isAlumniStatus($status)) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Status mutasi tidak valid');
            redirect('siswa/detail/' . $id);
        }

        $this->db->where('id_siswa', $id);
        $this->db->update($this->table, ['status_keaktifan' => $status]);
        $id_alumni = $this->moveSiswaToAlumni($id, $status);

        if ($id_alumni) {
            $this->activity_model->add(logged('name') . ' Memutasi siswa ke alumni: ' . $siswa->nama_siswa . ' (' . $status . ')', logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Siswa berhasil dimutasi ke Alumni');
            redirect('alumni/detail/' . $id_alumni);
        }

        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Siswa gagal dimutasi ke Alumni');
        redirect('siswa/detail/' . $id);
    }

    public function hapus($id)
    {
        ifPermissions('siswa_delete');
        $siswa = $this->db->get_where($this->table, ['id_siswa' => $id])->row();
        if (!$siswa) {
            show_404();
        }

        foreach ($this->db->get_where('siswa_foto', ['id_siswa' => $id])->result() as $foto) {
            $this->hapusFile('uploads/siswa_foto/' . $foto->foto);
        }
        foreach ($this->db->get_where('siswa_dokumen', ['id_siswa' => $id])->result() as $dokumen) {
            $this->hapusFile('uploads/siswa_dokumen/' . $dokumen->berkas);
        }
        $this->db->delete('siswa_foto', ['id_siswa' => $id]);
        $this->db->delete('siswa_dokumen', ['id_siswa' => $id]);
        $this->db->delete($this->table, ['id_siswa' => $id]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Data Siswa Berhasil Dihapus');
        redirect('siswa/all');
    }

    public function fotoHapus($id_foto)
    {
        ifPermissions('siswa_edit');
        $foto = $this->db->get_where('siswa_foto', ['id_foto' => $id_foto])->row();
        if (!$foto) {
            show_404();
        }
        $this->hapusFile('uploads/siswa_foto/' . $foto->foto);
        $this->db->delete('siswa_foto', ['id_foto' => $id_foto]);
        redirect('siswa/detail/' . $foto->id_siswa);
    }

    public function dokumenSimpan($id_siswa)
    {
        postAllowed();
        ifPermissions('siswa_edit');
        $upload = $this->uploadDokumen($id_siswa);
        if (!$upload['status']) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', $upload['message']);
            redirect('siswa/detail/' . $id_siswa);
        }

        $data = $this->dokumenData($id_siswa);
        $data['berkas'] = $upload['file_name'];
        $this->db->insert('siswa_dokumen', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Dokumen Siswa Berhasil Ditambahkan');
        redirect('siswa/detail/' . $id_siswa);
    }

    public function dokumenUpdate($id_dokumen)
    {
        postAllowed();
        ifPermissions('siswa_edit');
        $dokumen = $this->db->get_where('siswa_dokumen', ['id_dokumen' => $id_dokumen])->row();
        if (!$dokumen) {
            show_404();
        }

        $data = $this->dokumenData($dokumen->id_siswa);
        if (!empty($_FILES['berkas']['name'])) {
            $upload = $this->uploadDokumen($dokumen->id_siswa);
            if (!$upload['status']) {
                $this->session->set_flashdata('alert-type', 'warning');
                $this->session->set_flashdata('alert', $upload['message']);
                redirect('siswa/detail/' . $dokumen->id_siswa);
            }
            $data['berkas'] = $upload['file_name'];
        }

        $this->db->where('id_dokumen', $id_dokumen);
        if ($this->db->update('siswa_dokumen', $data) && !empty($data['berkas'])) {
            $this->hapusFile('uploads/siswa_dokumen/' . $dokumen->berkas);
        }
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Dokumen Siswa Berhasil Diperbarui');
        redirect('siswa/detail/' . $dokumen->id_siswa);
    }

    public function dokumenHapus($id_dokumen)
    {
        ifPermissions('siswa_edit');
        $dokumen = $this->db->get_where('siswa_dokumen', ['id_dokumen' => $id_dokumen])->row();
        if (!$dokumen) {
            show_404();
        }
        $this->hapusFile('uploads/siswa_dokumen/' . $dokumen->berkas);
        $this->db->delete('siswa_dokumen', ['id_dokumen' => $id_dokumen]);
        redirect('siswa/detail/' . $dokumen->id_siswa);
    }

    public function jenisDokumenSimpan()
    {
        postAllowed();
        ifPermissions('siswa_edit');
        $nama = trim((string) post('nama_jenis_dokumen'));
        if ($nama === '') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Nama jenis dokumen wajib diisi']));
            return;
        }
        $existing = $this->db->get_where('master_jenis_dokumen_siswa', ['nama_jenis_dokumen' => $nama])->row();
        if ($existing) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'id' => $existing->id_jenis_dokumen, 'nama' => $existing->nama_jenis_dokumen]));
            return;
        }
        $this->db->insert('master_jenis_dokumen_siswa', ['nama_jenis_dokumen' => $nama, 'status' => 'Aktif']);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'id' => $this->db->insert_id(), 'nama' => $nama]));
    }

    public function getKabupaten()
    {
        echo json_encode($this->db->get_where('reg_kabupaten', ['id_prov' => $this->input->post('id')])->result());
    }

    public function getKecamatan()
    {
        echo json_encode($this->db->get_where('reg_kecamatan', ['id_kab' => $this->input->post('id')])->result());
    }

    public function getKelurahan()
    {
        echo json_encode($this->db->get_where('reg_kelurahan', ['id_kec' => $this->input->post('id')])->result());
    }

    private function siswaData()
    {
        $alamat_sama_ayah = $this->input->post('alamat_ayah_sama_siswa') ? '1' : '0';
        $alamat_sama_ibu = $this->input->post('alamat_ibu_sama_siswa') ? '1' : '0';

        // Fungsi pembantu untuk mengambil nama wilayah jika input adalah ID
        $get_name = function ($table, $pk, $id) {
            if (is_numeric($id)) {
                $row = $this->db->get_where($table, [$pk => $id])->row();
                return $row ? $row->nama : $id;
            }
            return $id;
        };

        // Nama Wilayah Siswa
        $prov = $get_name('reg_provinsi', 'id_prov', post('id_provinsi'));
        $kab  = $get_name('reg_kabupaten', 'id_kab', post('id_kabupaten'));
        $kec  = $get_name('reg_kecamatan', 'id_kec', post('id_kecamatan'));
        $kel  = $get_name('reg_kelurahan', 'id_kel', post('id_kelurahan'));

        // Nama Wilayah Ayah
        $prov_a = $alamat_sama_ayah ? $prov : $get_name('reg_provinsi', 'id_prov', post('id_provinsi_ayah'));
        $kab_a  = $alamat_sama_ayah ? $kab : $get_name('reg_kabupaten', 'id_kab', post('id_kabupaten_ayah'));
        $kec_a  = $alamat_sama_ayah ? $kec : $get_name('reg_kecamatan', 'id_kec', post('id_kecamatan_ayah'));
        $kel_a  = $alamat_sama_ayah ? $kel : $get_name('reg_kelurahan', 'id_kel', post('id_kelurahan_ayah'));

        // Nama Wilayah Ibu
        $prov_i = $alamat_sama_ibu ? $prov : $get_name('reg_provinsi', 'id_prov', post('id_provinsi_ibu'));
        $kab_i  = $alamat_sama_ibu ? $kab : $get_name('reg_kabupaten', 'id_kab', post('id_kabupaten_ibu'));
        $kec_i  = $alamat_sama_ibu ? $kec : $get_name('reg_kecamatan', 'id_kec', post('id_kecamatan_ibu'));
        $kel_i  = $alamat_sama_ibu ? $kel : $get_name('reg_kelurahan', 'id_kel', post('id_kelurahan_ibu'));

        return [
            'nama_siswa' => post('nama_siswa'),
            'nisn' => post('nisn'),
            'nipd' => post('nipd'),
            'nik' => post('nik'),
            'no_kk' => post('no_kk'),
            'jenis_kelamin' => post('jenis_kelamin'),
            'tempat_lahir' => post('tempat_lahir'),
            'tanggal_lahir' => post('tanggal_lahir') ?: null,
            'agama' => post('agama'),
            'telepon' => post('telepon') ?: null,
            'email' => post('email') ?: null,
            'rombel' => post('rombel') ?: null,
            'tanggal_pendaftaran' => post('tanggal_pendaftaran') ?: null,
            'status_pendaftaran' => post('status_pendaftaran') ?: null,
            'status_keaktifan' => post('status_keaktifan') ?: 'Aktif',
            'no_ijazah' => post('no_ijazah') ?: null,
            'kewarganegaraan' => post('kewarganegaraan') ?: 'Indonesia',
            'anak_ke' => post('anak_ke') ?: null,
            'alamat' => post('alamat') ?: null,
            'rt' => post('rt') ?: null,
            'rw' => post('rw') ?: null,
            'jenis_tempat_tinggal' => post('jenis_tempat_tinggal') ?: null,
            'alat_transportasi' => post('alat_transportasi') ?: null,
            'jarak_ke_sekolah' => post('jarak_ke_sekolah') ?: null,
            'koordinat' => post('koordinat') ?: null,
            'sekolah_asal' => post('sekolah_asal') ?: null,
            'riwayat_penyakit' => post('riwayat_penyakit') ?: null,
            'prestasi_siswa' => post('prestasi_siswa') ?: null,
            'id_provinsi' => $prov,
            'id_kabupaten' => $kab,
            'id_kecamatan' => $kec,
            'id_kelurahan' => $kel,
            'nama_ayah' => post('nama_ayah') ?: null,
            'nik_ayah' => post('nik_ayah') ?: null,
            'pekerjaan_ayah' => post('pekerjaan_ayah') ?: null,
            'penghasilan_ayah' => post('penghasilan_ayah') ?: null,
            'tahun_lahir_ayah' => post('tahun_lahir_ayah') ?: null,
            'pendidikan_ayah' => post('pendidikan_ayah') ?: null,
            'alamat_ayah_sama_siswa' => $alamat_sama_ayah,
            'alamat_ayah' => $alamat_sama_ayah ? post('alamat') : post('alamat_ayah'),
            'id_provinsi_ayah' => $prov_a,
            'id_kabupaten_ayah' => $kab_a,
            'id_kecamatan_ayah' => $kec_a,
            'id_kelurahan_ayah' => $kel_a,
            'nama_ibu' => post('nama_ibu') ?: null,
            'nik_ibu' => post('nik_ibu') ?: null,
            'pekerjaan_ibu' => post('pekerjaan_ibu') ?: null,
            'penghasilan_ibu' => post('penghasilan_ibu') ?: null,
            'tahun_lahir_ibu' => post('tahun_lahir_ibu') ?: null,
            'pendidikan_ibu' => post('pendidikan_ibu') ?: null,
            'alamat_ibu_sama_siswa' => $alamat_sama_ibu,
            'alamat_ibu' => $alamat_sama_ibu ? post('alamat') : post('alamat_ibu'),
            'id_provinsi_ibu' => $prov_i,
            'id_kabupaten_ibu' => $kab_i,
            'id_kecamatan_ibu' => $kec_i,
            'id_kelurahan_ibu' => $kel_i,
        ];
    }

    private function ensureSiswaBukuIndukColumns()
    {
        $this->load->dbforge();
        $fields = [
            'no_ijazah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kewarganegaraan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Indonesia'],
            'anak_ke' => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'jenis_tempat_tinggal' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alat_transportasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jarak_ke_sekolah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'koordinat' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'sekolah_asal' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'riwayat_penyakit' => ['type' => 'TEXT', 'null' => true],
            'prestasi_siswa' => ['type' => 'TEXT', 'null' => true],
        ];

        foreach ($fields as $field => $definition) {
            if (!$this->db->field_exists($field, $this->table)) {
                $this->dbforge->add_column($this->table, [$field => $definition]);
            }
        }
    }

    private function isAlumniStatus($status)
    {
        return in_array(strtolower(trim((string) $status)), ['lulus', 'pindah', 'keluar'], true);
    }

    private function uploadFotoSiswa($id_siswa)
    {
        if (empty($_FILES['foto']['name'][0])) {
            return;
        }
        $path = './uploads/siswa_foto/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->load->library('upload');
        $files = $_FILES['foto'];
        for ($i = 0; $i < count($files['name']); $i++) {
            $_FILES['foto_item'] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
            if ($_FILES['foto_item']['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $this->upload->initialize([
                'upload_path' => $path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size' => 3072,
                'file_name' => 'siswa-' . $id_siswa . '-' . time() . '-' . $i,
                'overwrite' => false,
            ]);
            if ($this->upload->do_upload('foto_item')) {
                $data = $this->upload->data();
                $this->db->insert('siswa_foto', [
                    'id_siswa' => $id_siswa,
                    'foto' => $data['file_name'],
                    'label' => post('label_foto') ?: date('Y'),
                ]);
            }
        }
    }

    private function dokumenData($id_siswa)
    {
        return [
            'id_siswa' => $id_siswa,
            'id_jenis_dokumen' => post('id_jenis_dokumen'),
            'nomor_dokumen' => post('nomor_dokumen') ?: null,
            'tanggal_dokumen' => post('tanggal_dokumen') ?: null,
            'keterangan' => post('keterangan') ?: null,
        ];
    }

    private function uploadDokumen($id_siswa)
    {
        if (empty($_FILES['berkas']['name'])) {
            return ['status' => false, 'message' => 'Berkas dokumen wajib diunggah'];
        }
        $path = './uploads/siswa_dokumen/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'max_size' => 5120,
            'file_name' => 'siswa-' . $id_siswa . '-' . time(),
            'overwrite' => false,
        ]);
        if (!$this->upload->do_upload('berkas')) {
            return ['status' => false, 'message' => strip_tags($this->upload->display_errors())];
        }
        $data = $this->upload->data();
        return ['status' => true, 'file_name' => $data['file_name']];
    }

    private function hapusFile($relative_path)
    {
        $path = FCPATH . $relative_path;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function getPembelajaranDetail($id)
    {
        $this->db->select('p.*, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester');
        $this->db->from('pembelajaran p');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
        $this->db->join('rombel r', 'p.id_rombel = r.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'p.id_tahun_pelajaran = tp.id_tahun_pelajaran');
        $this->db->where('p.id_pembelajaran', $id);
        return $this->db->get()->row();
    }

    private function formatRombelPembelajaran($pembelajaran)
    {
        $tingkat = trim((string) $pembelajaran->nama_tingkat);
        $rombel = trim((string) $pembelajaran->nama_rombel);

        return $tingkat !== '' ? $tingkat . ' - ' . $rombel : $rombel;
    }
}
