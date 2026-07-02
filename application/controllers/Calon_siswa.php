<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calon_siswa extends MY_Controller
{
    private $table = 'calon_siswa';
    private $berkas_table = 'calon_siswa_berkas';

    private $status_options = ['Sedang Input', 'Perbaikan', 'Terverifikasi'];
    private $jenis_pendidikan_options = ['Hanya Sekolah', 'Sekolah & Pesantren', 'Hanya Pesantren'];
    private $jenis_tempat_tinggal_options = ['Bersama Orang Tua', 'Bersama Saudara', 'Pondok Pesantren', 'Panti Asuhan'];
    private $alat_transportasi_options = ['Jalan Kaki', 'Transportasi Umum', 'Kendaraan Roda Dua', 'Kendaraan Roda Empat'];
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
    private $required_berkas = [
        'Kartu Keluarga',
        'Akta Kelahiran',
        'KTP Orang Tua',
        'Ijazah/Surat Kelulusan',
        'Surat Perjanjian Orang Tua',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
        if (!is_daftar_ulang_aktif()) {
            show_404();
        }
    }

    public function index()
    {
        ifPermissions('calon_siswa_list');
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Calon Siswa', 'calon_siswa');
        $this->db->order_by('created_at', 'DESC');
        $rows = $this->db->get($this->table)->result();
        $this->appendBerkasStatus($rows);
        $this->page_data['calon_siswa'] = $rows;
        $this->page_data['status_options'] = $this->status_options;
        $this->page_data['import_columns'] = $this->importColumns();
        $this->load->view('calon_siswa/list', $this->page_data);
    }

    public function validasi()
    {
        ifPermissions('calon_siswa_validasi');
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Validasi Daftar Ulang', 'calon_siswa/validasi');
        $this->db->order_by('created_at', 'DESC');
        $rows = $this->db->get($this->table)->result();
        $this->appendBerkasStatus($rows);

        $lembaga_map = [];
        foreach ($this->getLembagaOptions() as $lembaga) {
            $lembaga_map[$lembaga->id_lembaga] = $lembaga->nama_lembaga;
        }

        $this->page_data['calon_siswa'] = $rows;
        $this->page_data['status_options'] = $this->status_options;
        $this->page_data['required_berkas'] = $this->required_berkas;
        $this->page_data['lembaga_map'] = $lembaga_map;
        $this->load->view('calon_siswa/validasi', $this->page_data);
    }

    public function add()
    {
        ifPermissions('calon_siswa_add');
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Input Calon Siswa', 'calon_siswa/add');
        $this->page_data['row'] = null;
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->page_data['pekerjaan_options'] = $this->pekerjaan_options;
        $this->page_data['lembaga'] = $this->getLembagaOptions();
        $this->page_data['jenis_pendidikan_options'] = $this->jenis_pendidikan_options;
        $this->page_data['jenis_tempat_tinggal_options'] = $this->jenis_tempat_tinggal_options;
        $this->page_data['alat_transportasi_options'] = $this->alat_transportasi_options;
        $this->page_data['school_coordinate'] = $this->school_coordinate;
        $this->load->view('calon_siswa/form', $this->page_data);
    }

    public function edit($id = null)
    {
        ifPermissions('calon_siswa_edit');
        $row = $this->getRow($id);
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Edit Calon Siswa', 'calon_siswa/edit/' . $id);
        $this->page_data['row'] = $row;
        $this->page_data['provinsi'] = $this->db->get('reg_provinsi')->result();
        $this->page_data['pekerjaan_options'] = $this->pekerjaan_options;
        $this->page_data['lembaga'] = $this->getLembagaOptions();
        $this->page_data['jenis_pendidikan_options'] = $this->jenis_pendidikan_options;
        $this->page_data['jenis_tempat_tinggal_options'] = $this->jenis_tempat_tinggal_options;
        $this->page_data['alat_transportasi_options'] = $this->alat_transportasi_options;
        $this->page_data['school_coordinate'] = $this->school_coordinate;
        $this->load->view('calon_siswa/form', $this->page_data);
    }

    public function simpan()
    {
        postAllowed();
        ifPermissions('calon_siswa_add');
        $data = $this->calonSiswaData();
        $data['status_daftar_ulang'] = 'Sedang Input';

        if ($this->db->insert($this->table, $data)) {
            $id = $this->db->insert_id();
            $this->activity_model->add(logged('name') . ' Menambah calon siswa: ' . $data['nama_siswa'], logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Data calon siswa berhasil disimpan. Silakan upload berkas.');
            redirect('calon_siswa/upload/' . $id);
        }

        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Data calon siswa gagal disimpan');
        redirect('calon_siswa/add');
    }

    public function update($id = null)
    {
        postAllowed();
        ifPermissions('calon_siswa_edit');
        $row = $this->getRow($id);
        $data = $this->calonSiswaData();

        $this->db->where('id_calon_siswa', $row->id_calon_siswa);
        if ($this->db->update($this->table, $data)) {
            $this->activity_model->add(logged('name') . ' Mengubah calon siswa: ' . $data['nama_siswa'], logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Data calon siswa berhasil diperbarui');
            redirect('calon_siswa/upload/' . $row->id_calon_siswa);
        }

        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Data calon siswa gagal diperbarui');
        redirect('calon_siswa/edit/' . $row->id_calon_siswa);
    }

    public function upload($id = null)
    {
        ifPermissions('calon_siswa_edit');
        $row = $this->getRow($id);
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Upload Berkas', 'calon_siswa/upload/' . $id);
        $this->page_data['row'] = $row;
        $this->page_data['required_berkas'] = $this->required_berkas;
        $this->page_data['berkas'] = $this->getBerkasMap($row->id_calon_siswa);
        $this->page_data['status_options'] = $this->status_options;
        $this->load->view('calon_siswa/upload', $this->page_data);
    }

    public function berkasSimpan($id = null)
    {
        postAllowed();
        ifPermissions('calon_siswa_edit');
        $row = $this->getRow($id);
        $jenis = trim((string) post('jenis_berkas'));
        if (!in_array($jenis, $this->required_berkas, true)) {
            show_404();
        }

        $upload = $this->uploadBerkas($row->id_calon_siswa, $jenis);
        if (!$upload['status']) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', $upload['message']);
            redirect('calon_siswa/upload/' . $row->id_calon_siswa);
        }

        $old = $this->db->get_where($this->berkas_table, [
            'id_calon_siswa' => $row->id_calon_siswa,
            'jenis_berkas' => $jenis,
        ])->row();

        $data = [
            'id_calon_siswa' => $row->id_calon_siswa,
            'jenis_berkas' => $jenis,
            'berkas' => $upload['file_name'],
            'keterangan' => post('keterangan') ?: null,
        ];

        if ($old) {
            $this->db->where('id_berkas', $old->id_berkas);
            $this->db->update($this->berkas_table, $data);
            $this->hapusFile('uploads/calon_siswa_berkas/' . $old->berkas);
        } else {
            $this->db->insert($this->berkas_table, $data);
        }

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas berhasil diupload');
        redirect('calon_siswa/upload/' . $row->id_calon_siswa);
    }

    public function berkasHapus($id_berkas = null)
    {
        ifPermissions('calon_siswa_edit');
        $berkas = $this->db->get_where($this->berkas_table, ['id_berkas' => $id_berkas])->row();
        if (!$berkas) {
            show_404();
        }
        $this->hapusFile('uploads/calon_siswa_berkas/' . $berkas->berkas);
        $this->db->delete($this->berkas_table, ['id_berkas' => $berkas->id_berkas]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Berkas berhasil dihapus');
        redirect('calon_siswa/upload/' . $berkas->id_calon_siswa);
    }

    public function statusUpdate($id = null)
    {
        postAllowed();
        ifPermissions('calon_siswa_validasi');
        $row = $this->getRow($id);
        $status = trim((string) post('status_daftar_ulang'));
        if (!in_array($status, $this->status_options, true)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Status daftar ulang tidak valid');
            redirect('calon_siswa/validasi');
        }

        $this->db->where('id_calon_siswa', $row->id_calon_siswa);
        $this->db->update($this->table, ['status_daftar_ulang' => $status]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Status calon siswa berhasil diperbarui');
        redirect('calon_siswa/validasi');
    }

    public function pindahkan($id = null)
    {
        postAllowed();
        ifPermissions('calon_siswa_aktivasi');
        $row = $this->getRow($id);
        $this->aktifkanCalonSiswa($row, 'calon_siswa/validasi');
    }

    public function aktifkan($id = null)
    {
        postAllowed();
        ifPermissions('calon_siswa_aktivasi');
        $row = $this->getRow($id);
        $this->aktifkanCalonSiswa($row, 'calon_siswa/aktivasi');
    }

    private function aktifkanCalonSiswa($row, $fallback_url)
    {
        if ($row->id_siswa) {
            redirect('siswa/detail/' . $row->id_siswa);
        }
        if ($row->status_daftar_ulang !== 'Terverifikasi') {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Calon siswa harus berstatus Terverifikasi sebelum dipindahkan menjadi siswa');
            redirect($fallback_url);
        }
        $siswa_data = $this->buildSiswaDataFromCalon($row);
        $this->db->trans_begin();
        $this->db->insert('siswa', $siswa_data);
        $id_siswa = $this->db->insert_id();
        $copy_status = $this->copyBerkasToSiswaDokumen($row->id_calon_siswa, $id_siswa);
        $this->db->where('id_calon_siswa', $row->id_calon_siswa);
        $this->db->update($this->table, ['id_siswa' => $id_siswa]);

        if ($this->db->trans_status() && $copy_status) {
            $this->db->trans_commit();
            $this->activity_model->add(logged('name') . ' Memindahkan calon siswa menjadi siswa: ' . $row->nama_siswa, logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Calon siswa berhasil dipindahkan menjadi siswa');
            redirect('siswa/detail/' . $id_siswa);
        }

        $this->db->trans_rollback();
        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Calon siswa gagal dipindahkan menjadi siswa. Pastikan semua file berkas masih tersedia.');
        redirect($fallback_url);
    }

    public function hapus($id = null)
    {
        ifPermissions('calon_siswa_delete');
        $row = $this->getRow($id);
        foreach ($this->db->get_where($this->berkas_table, ['id_calon_siswa' => $row->id_calon_siswa])->result() as $berkas) {
            $this->hapusFile('uploads/calon_siswa_berkas/' . $berkas->berkas);
        }
        $this->db->delete($this->berkas_table, ['id_calon_siswa' => $row->id_calon_siswa]);
        $this->db->delete($this->table, ['id_calon_siswa' => $row->id_calon_siswa]);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Data calon siswa berhasil dihapus');
        redirect('calon_siswa');
    }

    public function get_detail_ajax($id = null)
    {
        ifPermissions('calon_siswa_view');
        $row = $this->getRow($id);
        $berkas = $this->getBerkasMap($row->id_calon_siswa);
        
        $lembaga_name = '-';
        if (!empty($row->id_lembaga_tujuan)) {
            $lembaga = $this->db->get_where('lembaga', ['id_lembaga' => $row->id_lembaga_tujuan])->row();
            if ($lembaga) {
                $lembaga_name = $lembaga->nama_lembaga;
            }
        }
        
        $berkas_list = [];
        foreach ($this->required_berkas as $jenis) {
            $uploaded = isset($berkas[$jenis]);
            $berkas_list[] = [
                'jenis' => $jenis,
                'status' => $uploaded,
                'file_name' => $uploaded ? $berkas[$jenis]->berkas : null,
                'url' => $uploaded ? url('uploads/calon_siswa_berkas/' . $berkas[$jenis]->berkas) : null,
                'keterangan' => $uploaded ? $berkas[$jenis]->keterangan : null,
            ];
        }
        
        $data = [
            'status' => true,
            'calon' => $row,
            'lembaga_tujuan' => $lembaga_name,
            'berkas' => $berkas_list,
        ];
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function aktivasi()
    {
        ifPermissions('calon_siswa_aktivasi');
        $this->setPage('Daftar Ulang Siswa', 'calon_siswa', 'Aktivasi Calon Siswa', 'calon_siswa/aktivasi');
        
        $this->db->order_by('created_at', 'DESC');
        $this->db->where('status_daftar_ulang', 'Terverifikasi');
        $this->db->group_start();
        $this->db->where('id_siswa', NULL);
        $this->db->or_where('id_siswa', 0);
        $this->db->group_end();
        
        $rows = $this->db->get($this->table)->result();
        $this->appendBerkasStatus($rows);
        
        $lembaga_map = [];
        foreach ($this->getLembagaOptions() as $lembaga) {
            $lembaga_map[$lembaga->id_lembaga] = $lembaga->nama_lembaga;
        }

        $this->page_data['calon_siswa'] = $rows;
        $this->page_data['lembaga_map'] = $lembaga_map;
        $this->load->view('calon_siswa/aktivasi', $this->page_data);
    }

    public function aktifkan_bulk()
    {
        postAllowed();
        ifPermissions('calon_siswa_aktivasi');
        $ids = post('id_calon_siswa');
        if (empty($ids) || !is_array($ids)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Pilih minimal satu calon siswa untuk diaktifkan');
            redirect('calon_siswa/aktivasi');
        }

        $success_count = 0;
        $fail_count = 0;

        foreach ($ids as $id) {
            $row = $this->db->get_where($this->table, ['id_calon_siswa' => $id])->row();
            if (!$row || $row->id_siswa || $row->status_daftar_ulang !== 'Terverifikasi') {
                $fail_count++;
                continue;
            }

            $siswa_data = $this->buildSiswaDataFromCalon($row);
            $this->db->trans_begin();
            $this->db->insert('siswa', $siswa_data);
            $id_siswa = $this->db->insert_id();
            $copy_status = $this->copyBerkasToSiswaDokumen($row->id_calon_siswa, $id_siswa);
            
            $this->db->where('id_calon_siswa', $row->id_calon_siswa);
            $this->db->update($this->table, ['id_siswa' => $id_siswa]);

            if ($this->db->trans_status() && $copy_status) {
                $this->db->trans_commit();
                $this->activity_model->add(logged('name') . ' Memindahkan calon siswa menjadi siswa: ' . $row->nama_siswa, logged('id'));
                $success_count++;
            } else {
                $this->db->trans_rollback();
                $fail_count++;
            }
        }

        if ($success_count > 0) {
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', $success_count . ' calon siswa berhasil diaktifkan menjadi siswa.' . ($fail_count > 0 ? ' (' . $fail_count . ' gagal)' : ''));
        } else {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Gagal mengaktifkan calon siswa.');
        }

        redirect('calon_siswa/aktivasi');
    }

    public function export()
    {
        ifPermissions('calon_siswa_export');
        $this->db->order_by('nama_siswa', 'ASC');
        $rows = $this->db->get($this->table)->result_array();
        $columns = $this->importColumns();

        $filename = 'calon-siswa-daftar-ulang-' . date('Ymd-His') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo "\xEF\xBB\xBF";
        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        echo '<tr>';
        foreach (array_keys($columns) as $header) {
            echo '<th style="background:#d9eaf7;font-weight:bold;">' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        echo '</tr>';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($columns as $field) {
                echo '<td style="mso-number-format:\@;">' . htmlspecialchars((string) ($row[$field] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
            }
            echo '</tr>';
        }
        echo '</table></body></html>';
        exit;
    }

    public function templateImport()
    {
        ifPermissions('calon_siswa_import');
        $columns = $this->importColumns();
        $sample = array_fill_keys(array_values($columns), '');
        $sample['nama_siswa'] = 'Contoh Nama';
        $sample['jenis_kelamin'] = 'Laki-laki';
        $sample['tanggal_lahir'] = '2012-07-15';
        $sample['tanggal_pendaftaran'] = date('Y-m-d');
        $sample['status_daftar_ulang'] = 'Sedang Input';
        $sample['pekerjaan_ayah'] = 'Wiraswasta';
        $sample['pekerjaan_ibu'] = 'Ibu Rumah Tangga';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="template-import-calon-siswa.xls"');
        header('Cache-Control: max-age=0');

        echo "\xEF\xBB\xBF";
        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1"><tr>';
        foreach (array_keys($columns) as $header) {
            echo '<th style="background:#d9eaf7;font-weight:bold;">' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        echo '</tr><tr>';
        foreach ($columns as $field) {
            echo '<td style="mso-number-format:\@;">' . htmlspecialchars((string) $sample[$field], ENT_QUOTES, 'UTF-8') . '</td>';
        }
        echo '</tr></table></body></html>';
        exit;
    }

    public function import()
    {
        postAllowed();
        ifPermissions('calon_siswa_import');
        if (empty($_FILES['file_import']['name'])) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'File import wajib dipilih');
            redirect('calon_siswa');
        }

        $file = $_FILES['file_import'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'csv'], true)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Format import yang didukung adalah .xlsx dan .csv');
            redirect('calon_siswa');
        }

        $rows = $ext === 'xlsx' ? $this->readXlsxRows($file['tmp_name']) : $this->readCsvRows($file['tmp_name']);
        $result = $this->importRows($rows);

        $this->session->set_flashdata('alert-type', $result['inserted'] > 0 ? 'success' : 'danger');
        $message = $result['inserted'] . ' data calon siswa berhasil diimport';
        if (!empty($result['errors'])) {
            $message .= '. Catatan: ' . implode(' ', array_slice($result['errors'], 0, 3));
        }
        $this->session->set_flashdata('alert', $message);
        redirect('calon_siswa');
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

    private function setPage($title, $title_url, $subtitle, $subtitle_url)
    {
        $this->page_data['page']->title = $title;
        $this->page_data['page']->titleUrl = $title_url;
        $this->page_data['page']->subtitle = $subtitle;
        $this->page_data['page']->subtitleUrl = $subtitle_url;
        $this->page_data['page']->icon = 'solar:user-plus-linear';
    }

    private function getRow($id)
    {
        if (!$id) {
            show_404();
        }
        $row = $this->db->get_where($this->table, ['id_calon_siswa' => $id])->row();
        if (!$row) {
            show_404();
        }
        return $row;
    }

    private function calonSiswaData()
    {
        $get_name = function ($table, $pk, $id) {
            if (is_numeric($id)) {
                $row = $this->db->get_where($table, [$pk => $id])->row();
                return $row ? $row->nama : $id;
            }
            return $id;
        };

        return [
            'nama_siswa' => post('nama_siswa'),
            'nisn' => post('nisn') ?: null,
            'nik' => post('nik') ?: null,
            'no_kk' => post('no_kk') ?: null,
            'jenis_kelamin' => post('jenis_kelamin') ?: null,
            'tempat_lahir' => post('tempat_lahir') ?: null,
            'tanggal_lahir' => post('tanggal_lahir') ?: null,
            'agama' => post('agama') ?: null,
            'telepon' => post('telepon') ?: null,
            'email' => post('email') ?: null,
            'id_lembaga_tujuan' => post('id_lembaga_tujuan') ?: null,
            'jenis_pendidikan' => $this->normalizeJenisPendidikan(post('jenis_pendidikan')),
            'tanggal_pendaftaran' => post('tanggal_pendaftaran') ?: date('Y-m-d'),
            'status_pendaftaran' => 'Daftar Ulang',
            'kewarganegaraan' => post('kewarganegaraan') ?: 'Indonesia',
            'alamat' => post('alamat') ?: null,
            'rt' => post('rt') ?: null,
            'rw' => post('rw') ?: null,
            'jenis_tempat_tinggal' => post('jenis_tempat_tinggal') ?: null,
            'alat_transportasi' => post('alat_transportasi') ?: null,
            'jarak_ke_sekolah' => post('jarak_ke_sekolah') ?: null,
            'koordinat' => post('koordinat') ?: null,
            'sekolah_asal' => post('sekolah_asal') ?: null,
            'id_provinsi' => $get_name('reg_provinsi', 'id_prov', post('id_provinsi')),
            'id_kabupaten' => $get_name('reg_kabupaten', 'id_kab', post('id_kabupaten')),
            'id_kecamatan' => $get_name('reg_kecamatan', 'id_kec', post('id_kecamatan')),
            'id_kelurahan' => $get_name('reg_kelurahan', 'id_kel', post('id_kelurahan')),
            'nama_ayah' => post('nama_ayah') ?: null,
            'nik_ayah' => post('nik_ayah') ?: null,
            'pekerjaan_ayah' => $this->normalizePekerjaan(post('pekerjaan_ayah')),
            'nama_ibu' => post('nama_ibu') ?: null,
            'nik_ibu' => post('nik_ibu') ?: null,
            'pekerjaan_ibu' => $this->normalizePekerjaan(post('pekerjaan_ibu')),
        ];
    }

    private function normalizePekerjaan($value)
    {
        $value = trim((string) $value);
        return in_array($value, $this->pekerjaan_options, true) ? $value : null;
    }

    private function importColumns()
    {
        return [
            'Nama Siswa' => 'nama_siswa',
            'NISN' => 'nisn',
            'NIK' => 'nik',
            'No KK' => 'no_kk',
            'Jenis Kelamin' => 'jenis_kelamin',
            'Tempat Lahir' => 'tempat_lahir',
            'Tanggal Lahir' => 'tanggal_lahir',
            'Agama' => 'agama',
            'Telepon' => 'telepon',
            'Email' => 'email',
            'Lembaga Tujuan' => 'id_lembaga_tujuan',
            'Jenis Pendidikan' => 'jenis_pendidikan',
            'Tanggal Pendaftaran' => 'tanggal_pendaftaran',
            'Status Daftar Ulang' => 'status_daftar_ulang',
            'Kewarganegaraan' => 'kewarganegaraan',
            'Alamat' => 'alamat',
            'RT' => 'rt',
            'RW' => 'rw',
            'Jenis Tempat Tinggal' => 'jenis_tempat_tinggal',
            'Alat Transportasi' => 'alat_transportasi',
            'Jarak Ke Sekolah' => 'jarak_ke_sekolah',
            'Koordinat' => 'koordinat',
            'Sekolah Asal' => 'sekolah_asal',
            'Provinsi' => 'id_provinsi',
            'Kabupaten' => 'id_kabupaten',
            'Kecamatan' => 'id_kecamatan',
            'Kelurahan' => 'id_kelurahan',
            'Nama Ayah' => 'nama_ayah',
            'NIK Ayah' => 'nik_ayah',
            'Pekerjaan Ayah' => 'pekerjaan_ayah',
            'Nama Ibu' => 'nama_ibu',
            'NIK Ibu' => 'nik_ibu',
            'Pekerjaan Ibu' => 'pekerjaan_ibu',
        ];
    }

    private function importRows($rows)
    {
        $inserted = 0;
        $errors = [];
        $columns = $this->importColumns();

        if (count($rows) < 2) {
            return ['inserted' => 0, 'errors' => ['File import tidak memiliki baris data.']];
        }

        $headers = array_map([$this, 'normalizeHeader'], $rows[0]);
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if ($this->isEmptyImportRow($row)) {
                continue;
            }

            $data = [];
            foreach ($headers as $index => $header) {
                foreach ($columns as $label => $field) {
                    if ($header === $this->normalizeHeader($label)) {
                        $data[$field] = isset($row[$index]) ? trim((string) $row[$index]) : null;
                        break;
                    }
                }
            }

            if (empty($data['nama_siswa'])) {
                $errors[] = 'Baris ' . ($i + 1) . ' dilewati karena Nama Siswa kosong.';
                continue;
            }

            $data = $this->sanitizeImportData($data);
            $this->db->insert($this->table, $data);
            $inserted++;
        }

        if ($inserted > 0) {
            $this->activity_model->add(logged('name') . ' Import calon siswa: ' . $inserted . ' data', logged('id'));
        }

        return ['inserted' => $inserted, 'errors' => $errors];
    }

    private function sanitizeImportData($data)
    {
        $fields = array_values($this->importColumns());
        $clean = [];
        foreach ($fields as $field) {
            $clean[$field] = isset($data[$field]) && $data[$field] !== '' ? $data[$field] : null;
        }

        $clean['tanggal_lahir'] = $this->normalizeDateValue($clean['tanggal_lahir']);
        $clean['tanggal_pendaftaran'] = $this->normalizeDateValue($clean['tanggal_pendaftaran']) ?: date('Y-m-d');
        $clean['status_pendaftaran'] = 'Daftar Ulang';
        $clean['id_lembaga_tujuan'] = $this->normalizeLembagaTujuan($clean['id_lembaga_tujuan']);
        $clean['jenis_pendidikan'] = $this->normalizeJenisPendidikan($clean['jenis_pendidikan']);
        $clean['status_daftar_ulang'] = in_array($clean['status_daftar_ulang'], $this->status_options, true) ? $clean['status_daftar_ulang'] : 'Sedang Input';
        $clean['kewarganegaraan'] = $clean['kewarganegaraan'] ?: 'Indonesia';
        $clean['pekerjaan_ayah'] = $this->normalizePekerjaan($clean['pekerjaan_ayah']);
        $clean['pekerjaan_ibu'] = $this->normalizePekerjaan($clean['pekerjaan_ibu']);

        return $clean;
    }

    private function getLembagaOptions()
    {
        $this->db->order_by('nama_lembaga', 'ASC');
        return $this->db->get('lembaga')->result();
    }

    private function normalizeLembagaTujuan($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }
        $this->db->like('nama_lembaga', $value);
        $row = $this->db->get('lembaga')->row();
        return $row ? $row->id_lembaga : null;
    }

    private function normalizeJenisPendidikan($value)
    {
        $value = trim((string) $value);
        return in_array($value, $this->jenis_pendidikan_options, true) ? $value : null;
    }

    private function normalizeHeader($value)
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }

    private function normalizeDateValue($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (is_numeric($value)) {
            $timestamp = ((int) $value - 25569) * 86400;
            return gmdate('Y-m-d', $timestamp);
        }
        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function isEmptyImportRow($row)
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function readCsvRows($path)
    {
        $rows = [];
        $handle = fopen($path, 'r');
        if (!$handle) {
            return $rows;
        }
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            $rows[] = $data;
        }
        fclose($handle);
        return $rows;
    }

    private function readXlsxRows($path)
    {
        if (!class_exists('ZipArchive')) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $shared_strings = [];
        $shared_xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($shared_xml !== false) {
            $shared = simplexml_load_string($shared_xml);
            if ($shared) {
                foreach ($shared->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string) $si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                    }
                    $shared_strings[] = $text;
                }
            }
        }

        $sheet_xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet_xml === false) {
            return [];
        }

        $sheet = simplexml_load_string($sheet_xml);
        if (!$sheet) {
            return [];
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $col_index = $this->xlsxColumnIndex($ref);
                while (count($values) < $col_index) {
                    $values[] = '';
                }
                $type = (string) $cell['t'];
                $raw = isset($cell->v) ? (string) $cell->v : '';
                if ($type === 's') {
                    $values[] = $shared_strings[(int) $raw] ?? '';
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $values[] = (string) $cell->is->t;
                } else {
                    $values[] = $raw;
                }
            }
            $rows[] = $values;
        }

        return $rows;
    }

    private function xlsxColumnIndex($cell_ref)
    {
        preg_match('/^[A-Z]+/i', $cell_ref, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    private function buildSiswaDataFromCalon($row)
    {
        $fields = $this->db->list_fields('siswa');
        $data = [];
        foreach ((array) $row as $field => $value) {
            if (in_array($field, $fields, true) && !in_array($field, ['id_siswa', 'created_at', 'updated_at'], true)) {
                $data[$field] = $value;
            }
        }
        $data['status_keaktifan'] = 'Aktif';
        $data['status_pendaftaran'] = 'Daftar Ulang';
        return $data;
    }

    private function getBerkasMap($id_calon_siswa)
    {
        $items = [];
        foreach ($this->db->get_where($this->berkas_table, ['id_calon_siswa' => $id_calon_siswa])->result() as $row) {
            $items[$row->jenis_berkas] = $row;
        }
        return $items;
    }

    private function hasAllRequiredBerkas($id_calon_siswa)
    {
        $items = $this->getBerkasMap($id_calon_siswa);
        foreach ($this->required_berkas as $jenis) {
            if (empty($items[$jenis]) || !is_file(FCPATH . 'uploads/calon_siswa_berkas/' . $items[$jenis]->berkas)) {
                return false;
            }
        }
        return true;
    }

    private function appendBerkasStatus(&$rows)
    {
        $counts = [];
        $berkas_counts = $this->db->select('id_calon_siswa, COUNT(*) AS total')
            ->from($this->berkas_table)
            ->group_by('id_calon_siswa')
            ->get()->result();
        foreach ($berkas_counts as $item) {
            $counts[$item->id_calon_siswa] = (int) $item->total;
        }
        foreach ($rows as $row) {
            $row->jumlah_berkas = isset($counts[$row->id_calon_siswa]) ? $counts[$row->id_calon_siswa] : 0;
            $row->berkas_lengkap = $this->hasAllRequiredBerkas($row->id_calon_siswa);
        }
    }

    private function uploadBerkas($id_calon_siswa, $jenis)
    {
        if (empty($_FILES['berkas']['name'])) {
            return ['status' => false, 'message' => 'Berkas wajib diupload'];
        }

        $path = './uploads/calon_siswa_berkas/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $jenis));
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'max_size' => 5120,
            'file_name' => 'calon-' . $id_calon_siswa . '-' . $slug . '-' . time(),
            'overwrite' => false,
        ]);

        if (!$this->upload->do_upload('berkas')) {
            return ['status' => false, 'message' => strip_tags($this->upload->display_errors())];
        }

        $data = $this->upload->data();
        return ['status' => true, 'file_name' => $data['file_name']];
    }

    private function copyBerkasToSiswaDokumen($id_calon_siswa, $id_siswa)
    {
        $target_path = './uploads/siswa_dokumen/';
        if (!is_dir($target_path)) {
            mkdir($target_path, 0777, true);
        }

        $copied_files = [];
        foreach ($this->db->get_where($this->berkas_table, ['id_calon_siswa' => $id_calon_siswa])->result() as $berkas) {
            $source = FCPATH . 'uploads/calon_siswa_berkas/' . $berkas->berkas;
            if (!is_file($source)) {
                $this->hapusCopiedFiles($copied_files);
                return false;
            }
            $ext = pathinfo($berkas->berkas, PATHINFO_EXTENSION);
            $target_name = 'siswa-' . $id_siswa . '-' . time() . '-' . $berkas->id_berkas . ($ext ? '.' . $ext : '');
            $target_file = FCPATH . 'uploads/siswa_dokumen/' . $target_name;
            if (!copy($source, $target_file)) {
                $this->hapusCopiedFiles($copied_files);
                return false;
            }
            $copied_files[] = $target_file;
            $inserted = $this->db->insert('siswa_dokumen', [
                'id_siswa' => $id_siswa,
                'id_jenis_dokumen' => $this->ensureJenisDokumen($berkas->jenis_berkas),
                'berkas' => $target_name,
                'keterangan' => $berkas->keterangan,
            ]);
            if (!$inserted) {
                $this->hapusCopiedFiles($copied_files);
                return false;
            }
        }
        return true;
    }

    private function hapusCopiedFiles($files)
    {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function ensureJenisDokumen($nama)
    {
        $existing = $this->db->get_where('master_jenis_dokumen_siswa', ['nama_jenis_dokumen' => $nama])->row();
        if ($existing) {
            return $existing->id_jenis_dokumen;
        }
        $this->db->insert('master_jenis_dokumen_siswa', [
            'nama_jenis_dokumen' => $nama,
            'status' => 'Aktif',
        ]);
        return $this->db->insert_id();
    }

    private function hapusFile($relative_path)
    {
        $path = FCPATH . $relative_path;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function ensureTables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `calon_siswa` (
            `id_calon_siswa` int(11) NOT NULL AUTO_INCREMENT,
            `id_siswa` int(11) DEFAULT NULL,
            `nama_siswa` varchar(150) NOT NULL,
            `nisn` varchar(30) DEFAULT NULL,
            `nipd` varchar(30) DEFAULT NULL,
            `nik` varchar(30) DEFAULT NULL,
            `no_kk` varchar(30) DEFAULT NULL,
            `jenis_kelamin` varchar(20) DEFAULT NULL,
            `tempat_lahir` varchar(100) DEFAULT NULL,
            `tanggal_lahir` date DEFAULT NULL,
            `agama` varchar(30) DEFAULT NULL,
            `telepon` varchar(30) DEFAULT NULL,
            `email` varchar(100) DEFAULT NULL,
            `id_lembaga_tujuan` int(11) DEFAULT NULL,
            `jenis_pendidikan` varchar(50) DEFAULT NULL,
            `rombel` varchar(100) DEFAULT NULL,
            `tanggal_pendaftaran` date DEFAULT NULL,
            `status_pendaftaran` varchar(100) DEFAULT NULL,
            `status_daftar_ulang` varchar(30) NOT NULL DEFAULT 'Sedang Input',
            `kewarganegaraan` varchar(50) DEFAULT 'Indonesia',
            `anak_ke` int(3) DEFAULT NULL,
            `alamat` text,
            `rt` varchar(5) DEFAULT NULL,
            `rw` varchar(5) DEFAULT NULL,
            `jenis_tempat_tinggal` varchar(100) DEFAULT NULL,
            `alat_transportasi` varchar(100) DEFAULT NULL,
            `jarak_ke_sekolah` varchar(100) DEFAULT NULL,
            `koordinat` varchar(100) DEFAULT NULL,
            `sekolah_asal` varchar(150) DEFAULT NULL,
            `riwayat_penyakit` text,
            `prestasi_siswa` text,
            `id_provinsi` varchar(100) DEFAULT NULL,
            `id_kabupaten` varchar(100) DEFAULT NULL,
            `id_kecamatan` varchar(100) DEFAULT NULL,
            `id_kelurahan` varchar(100) DEFAULT NULL,
            `nama_ayah` varchar(150) DEFAULT NULL,
            `nik_ayah` varchar(30) DEFAULT NULL,
            `pekerjaan_ayah` varchar(100) DEFAULT NULL,
            `nama_ibu` varchar(150) DEFAULT NULL,
            `nik_ibu` varchar(30) DEFAULT NULL,
            `pekerjaan_ibu` varchar(100) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_calon_siswa`),
            KEY `idx_calon_siswa_status` (`status_daftar_ulang`),
            KEY `idx_calon_siswa_lembaga_tujuan` (`id_lembaga_tujuan`),
            KEY `idx_calon_siswa_id_siswa` (`id_siswa`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        if (!$this->db->field_exists('id_lembaga_tujuan', $this->table)) {
            $this->db->query("ALTER TABLE `calon_siswa` ADD `id_lembaga_tujuan` int(11) DEFAULT NULL AFTER `email`");
        }
        if (!$this->db->field_exists('jenis_pendidikan', $this->table)) {
            $this->db->query("ALTER TABLE `calon_siswa` ADD `jenis_pendidikan` varchar(50) DEFAULT NULL AFTER `id_lembaga_tujuan`");
        }
        if ($this->db->table_exists('siswa') && !$this->db->field_exists('jenis_pendidikan', 'siswa')) {
            $this->db->query("ALTER TABLE `siswa` ADD `jenis_pendidikan` varchar(50) DEFAULT NULL AFTER `email`");
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `calon_siswa_berkas` (
            `id_berkas` int(11) NOT NULL AUTO_INCREMENT,
            `id_calon_siswa` int(11) NOT NULL,
            `jenis_berkas` varchar(150) NOT NULL,
            `berkas` varchar(255) NOT NULL,
            `keterangan` text,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_berkas`),
            UNIQUE KEY `uniq_calon_jenis` (`id_calon_siswa`, `jenis_berkas`),
            KEY `idx_calon_siswa_berkas_id` (`id_calon_siswa`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }
}
