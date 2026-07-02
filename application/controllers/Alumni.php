<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Alumni extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureAlumniTables();
    }

    public function index()
    {
        ifPermissions('alumni_list');
        $this->page_data['page']->title = 'Alumni';
        $this->page_data['page']->titleUrl = 'alumni';
        $this->page_data['page']->subtitle = 'Data Alumni';
        $this->page_data['page']->subtitleUrl = 'alumni';
        $this->page_data['page']->icon = 'solar:archive-linear';

        $this->db->order_by('nama_siswa', 'ASC');
        $this->page_data['alumni'] = $this->db->get('alumni')->result();
        $this->load->view('alumni/list', $this->page_data);
    }

    public function detail($id = null)
    {
        if (!$id) {
            redirect('alumni');
        }

        $row = $this->db->get_where('alumni', ['id_alumni' => $id])->row();
        if (!$row) {
            show_404();
        }

        $this->page_data['page']->title = 'Alumni';
        $this->page_data['page']->titleUrl = 'alumni';
        $this->page_data['page']->subtitle = $row->nama_siswa;
        $this->page_data['page']->subtitleUrl = 'alumni/detail/' . $id;
        $this->page_data['page']->icon = 'solar:archive-linear';
        $this->page_data['row'] = $row;
        $this->page_data['foto'] = $this->db->get_where('alumni_foto', ['id_alumni' => $id])->result();
        $this->db->order_by('nama_jenis_dokumen', 'ASC');
        $this->page_data['jenis_dokumen'] = $this->db->get_where('master_jenis_dokumen_siswa', ['status' => 'Aktif'])->result();

        $this->db->select('alumni_dokumen.*, master_jenis_dokumen_siswa.nama_jenis_dokumen');
        $this->db->from('alumni_dokumen');
        $this->db->join('master_jenis_dokumen_siswa', 'master_jenis_dokumen_siswa.id_jenis_dokumen = alumni_dokumen.id_jenis_dokumen', 'left');
        $this->db->where('alumni_dokumen.id_alumni', $id);
        $this->db->order_by('master_jenis_dokumen_siswa.nama_jenis_dokumen', 'ASC');
        $this->page_data['dokumen'] = $this->db->get()->result();

        $this->db->select('pm.id_pembelajaran_mapel, m.nama_mapel, tp.tahun_pelajaran, tp.semester, ans.nilai_harian, ans.nilai_psts, ans.nilai_psas, ans.nilai_rapor');
        $this->db->from('alumni_nilai_siswa ans');
        $this->db->join('pembelajaran_mapel pm', 'pm.id_pembelajaran_mapel = ans.id_pembelajaran_mapel', 'left');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel', 'left');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran', 'left');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran', 'left');
        $this->db->where('ans.id_alumni', (int) $id);
        $this->db->order_by('tp.id_tahun_pelajaran', 'ASC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        $this->page_data['nilai'] = $this->db->get()->result();


        $this->load->view('alumni/detail', $this->page_data);
    }

    public function kembalikan($id_alumni)
    {
        postAllowed();
        $alumni = $this->db->get_where('alumni', ['id_alumni' => (int) $id_alumni])->row_array();
        if (!$alumni) {
            show_404();
        }

        if (!empty($alumni['id_siswa_kembali'])) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Alumni ini sudah pernah dikembalikan menjadi siswa.');
            redirect('siswa/detail/' . $alumni['id_siswa_kembali']);
            return;
        }

        $existing = $this->findExistingActiveSiswa($alumni);
        if ($existing) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', 'Siswa aktif dengan NISN/NIK yang sama sudah ada: ' . $existing->nama_siswa);
            redirect('siswa/detail/' . $existing->id_siswa);
            return;
        }

        $id_siswa = $this->restoreAlumniToSiswa($alumni);
        if ($id_siswa) {
            $this->activity_model->add(logged('name') . ' Mengembalikan alumni menjadi siswa: ' . $alumni['nama_siswa'], logged('id'));
            $this->session->set_flashdata('alert-type', 'success');
            $this->session->set_flashdata('alert', 'Alumni berhasil dikembalikan menjadi siswa aktif.');
            redirect('siswa/detail/' . $id_siswa);
            return;
        }

        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', 'Gagal mengembalikan alumni menjadi siswa.');
        redirect('alumni/detail/' . $id_alumni);
    }

    public function dokumenSimpan($id_alumni)
    {
        postAllowed();
        $alumni = $this->db->get_where('alumni', ['id_alumni' => $id_alumni])->row();
        if (!$alumni) {
            show_404();
        }

        $upload = $this->uploadDokumen($id_alumni);
        if (!$upload['status']) {
            $this->session->set_flashdata('alert-type', 'warning');
            $this->session->set_flashdata('alert', $upload['message']);
            redirect('alumni/detail/' . $id_alumni);
        }

        $data = $this->dokumenData($id_alumni);
        $data['berkas'] = $upload['file_name'];
        $this->db->insert('alumni_dokumen', $data);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Dokumen Alumni Berhasil Ditambahkan');
        redirect('alumni/detail/' . $id_alumni);
    }

    public function dokumenUpdate($id_dokumen)
    {
        postAllowed();
        $dokumen = $this->db->get_where('alumni_dokumen', ['id_dokumen_alumni' => $id_dokumen])->row();
        if (!$dokumen) {
            show_404();
        }

        $data = $this->dokumenData($dokumen->id_alumni);
        if (!empty($_FILES['berkas']['name'])) {
            $upload = $this->uploadDokumen($dokumen->id_alumni);
            if (!$upload['status']) {
                $this->session->set_flashdata('alert-type', 'warning');
                $this->session->set_flashdata('alert', $upload['message']);
                redirect('alumni/detail/' . $dokumen->id_alumni);
            }
            $data['berkas'] = $upload['file_name'];
        }

        $this->db->where('id_dokumen_alumni', $id_dokumen);
        if ($this->db->update('alumni_dokumen', $data) && !empty($data['berkas'])) {
            $this->hapusFile('uploads/alumni_dokumen/' . $dokumen->berkas);
        }
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Dokumen Alumni Berhasil Diperbarui');
        redirect('alumni/detail/' . $dokumen->id_alumni);
    }

    public function jenisDokumenSimpan()
    {
        postAllowed();
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

    private function ensureAlumniTables()
    {
        $this->load->dbforge();
        if (!$this->db->table_exists('alumni')) {
            $this->db->query('CREATE TABLE alumni LIKE siswa');
            $this->db->query('ALTER TABLE alumni CHANGE id_siswa id_alumni INT(11) NOT NULL AUTO_INCREMENT');
            $this->db->query('ALTER TABLE alumni ADD COLUMN id_siswa_asal INT(11) NULL AFTER id_alumni');
            $this->db->query("ALTER TABLE alumni ADD COLUMN status_alumni VARCHAR(30) NULL AFTER status_keaktifan");
            $this->db->query('ALTER TABLE alumni ADD COLUMN tanggal_alumni DATE NULL AFTER status_alumni');
            $this->db->query('ALTER TABLE alumni ADD COLUMN sekolah_terakhir VARCHAR(150) NULL AFTER tanggal_alumni');
            $this->db->query('ALTER TABLE alumni ADD COLUMN rombel_terakhir VARCHAR(150) NULL AFTER sekolah_terakhir');
            $this->db->query('ALTER TABLE alumni ADD COLUMN created_from_siswa_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER tanggal_alumni');
        }
        if (!$this->db->field_exists('sekolah_terakhir', 'alumni')) {
            $this->dbforge->add_column('alumni', ['sekolah_terakhir' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true]]);
        }
        if (!$this->db->field_exists('rombel_terakhir', 'alumni')) {
            $this->dbforge->add_column('alumni', ['rombel_terakhir' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true]]);
        }
        if (!$this->db->field_exists('id_siswa_kembali', 'alumni')) {
            $this->dbforge->add_column('alumni', ['id_siswa_kembali' => ['type' => 'INT', 'constraint' => 11, 'null' => true]]);
        }
        if (!$this->db->field_exists('tanggal_kembali', 'alumni')) {
            $this->dbforge->add_column('alumni', ['tanggal_kembali' => ['type' => 'DATE', 'null' => true]]);
        }
        if ($this->db->table_exists('siswa') && !$this->db->field_exists('id_alumni_asal', 'siswa')) {
            $this->dbforge->add_column('siswa', ['id_alumni_asal' => ['type' => 'INT', 'constraint' => 11, 'null' => true]]);
        }
        if ($this->db->table_exists('siswa') && !$this->db->field_exists('tanggal_kembali', 'siswa')) {
            $this->dbforge->add_column('siswa', ['tanggal_kembali' => ['type' => 'DATE', 'null' => true]]);
        }

        if (!$this->db->table_exists('alumni_foto')) {
            $this->dbforge->add_field([
                'id_foto_alumni' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_alumni' => ['type' => 'INT', 'constraint' => 11],
                'id_foto_asal' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'foto' => ['type' => 'VARCHAR', 'constraint' => 255],
                'label' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id_foto_alumni', true);
            $this->dbforge->add_key('id_alumni');
            $this->dbforge->create_table('alumni_foto', true);
        }

        if (!$this->db->table_exists('alumni_dokumen')) {
            $this->dbforge->add_field([
                'id_dokumen_alumni' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_alumni' => ['type' => 'INT', 'constraint' => 11],
                'id_dokumen_asal' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'id_jenis_dokumen' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nomor_dokumen' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal_dokumen' => ['type' => 'DATE', 'null' => true],
                'berkas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keterangan' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
                'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->dbforge->add_key('id_dokumen_alumni', true);
            $this->dbforge->add_key('id_alumni');
            $this->dbforge->create_table('alumni_dokumen', true);
        }

        if (!$this->db->table_exists('alumni_pembelajaran_siswa') && $this->db->table_exists('pembelajaran_siswa')) {
            $this->db->query('CREATE TABLE alumni_pembelajaran_siswa LIKE pembelajaran_siswa');
            $this->db->query('ALTER TABLE alumni_pembelajaran_siswa CHANGE id_pembelajaran_siswa id_alumni_pembelajaran_siswa INT(11) NOT NULL AUTO_INCREMENT');
            $this->db->query('ALTER TABLE alumni_pembelajaran_siswa CHANGE peserta_didik_id id_alumni VARCHAR(100) NOT NULL');
            $this->db->query('ALTER TABLE alumni_pembelajaran_siswa ADD COLUMN id_siswa_asal INT(11) NULL AFTER id_alumni');
        }

        if (!$this->db->table_exists('alumni_nilai_siswa') && $this->db->table_exists('nilai_siswa')) {
            $this->db->query('CREATE TABLE alumni_nilai_siswa LIKE nilai_siswa');
            $this->db->query('ALTER TABLE alumni_nilai_siswa CHANGE id_nilai_siswa id_alumni_nilai_siswa INT(11) NOT NULL AUTO_INCREMENT');
            $this->db->query('ALTER TABLE alumni_nilai_siswa CHANGE id_siswa id_alumni INT(11) NOT NULL');
            $this->db->query('ALTER TABLE alumni_nilai_siswa ADD COLUMN id_siswa_asal INT(11) NULL AFTER id_alumni');
        }
    }

    private function dokumenData($id_alumni)
    {
        return [
            'id_alumni' => $id_alumni,
            'id_jenis_dokumen' => post('id_jenis_dokumen'),
            'nomor_dokumen' => post('nomor_dokumen') ?: null,
            'tanggal_dokumen' => post('tanggal_dokumen') ?: null,
            'keterangan' => post('keterangan') ?: null,
        ];
    }

    private function uploadDokumen($id_alumni)
    {
        if (empty($_FILES['berkas']['name'])) {
            return ['status' => false, 'message' => 'Berkas dokumen wajib diunggah'];
        }
        $path = './uploads/alumni_dokumen/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'max_size' => 5120,
            'file_name' => 'alumni-' . $id_alumni . '-' . time(),
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

    private function findExistingActiveSiswa($alumni)
    {
        if (empty($alumni['nisn']) && empty($alumni['nik'])) {
            return null;
        }

        $this->db->group_start();
        if (!empty($alumni['nisn'])) {
            $this->db->or_where('nisn', $alumni['nisn']);
        }
        if (!empty($alumni['nik'])) {
            $this->db->or_where('nik', $alumni['nik']);
        }
        $this->db->group_end();
        $this->db->where('status_keaktifan', 'Aktif');
        return $this->db->get('siswa')->row();
    }

    private function restoreAlumniToSiswa($alumni)
    {
        $siswa_fields = $this->db->list_fields('siswa');
        $siswa_data = [];
        $skip = [
            'id_alumni',
            'id_siswa_asal',
            'status_alumni',
            'tanggal_alumni',
            'sekolah_terakhir',
            'rombel_terakhir',
            'created_from_siswa_at',
            'id_siswa_kembali',
            'tanggal_kembali',
        ];

        foreach ($alumni as $field => $value) {
            if (!in_array($field, $skip, true) && in_array($field, $siswa_fields, true)) {
                $siswa_data[$field] = $value;
            }
        }

        $tanggal_kembali = post('tanggal_kembali') ?: date('Y-m-d');
        $siswa_data['status_keaktifan'] = 'Aktif';
        $siswa_data['status_pendaftaran'] = post('status_pendaftaran') ?: 'Kembali';
        $siswa_data['tanggal_pendaftaran'] = $tanggal_kembali;
        $siswa_data['rombel'] = null;
        $siswa_data['nipd'] = post('nipd') !== false ? post('nipd') : (isset($alumni['nipd']) ? $alumni['nipd'] : null);
        if (in_array('id_alumni_asal', $siswa_fields, true)) {
            $siswa_data['id_alumni_asal'] = (int) $alumni['id_alumni'];
        }
        if (in_array('tanggal_kembali', $siswa_fields, true)) {
            $siswa_data['tanggal_kembali'] = $tanggal_kembali;
        }

        $this->db->trans_start();
        $this->db->insert('siswa', $siswa_data);
        $id_siswa = $this->db->insert_id();

        foreach ($this->db->get_where('alumni_foto', ['id_alumni' => (int) $alumni['id_alumni']])->result_array() as $foto) {
            $file = $this->copyAlumniFile('uploads/alumni_foto/' . $foto['foto'], 'uploads/siswa_foto', 'siswa-' . $id_siswa . '-kembali');
            if ($file) {
                $this->db->insert('siswa_foto', [
                    'id_siswa' => $id_siswa,
                    'foto' => $file,
                    'label' => $foto['label'] ?: 'Foto Kembali',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        foreach ($this->db->get_where('alumni_dokumen', ['id_alumni' => (int) $alumni['id_alumni']])->result_array() as $dokumen) {
            $file = !empty($dokumen['berkas']) ? $this->copyAlumniFile('uploads/alumni_dokumen/' . $dokumen['berkas'], 'uploads/siswa_dokumen', 'siswa-' . $id_siswa . '-kembali') : null;
            if (!$file) {
                continue;
            }
            $this->db->insert('siswa_dokumen', [
                'id_siswa' => $id_siswa,
                'id_jenis_dokumen' => (int) $dokumen['id_jenis_dokumen'],
                'nomor_dokumen' => $dokumen['nomor_dokumen'],
                'tanggal_dokumen' => $dokumen['tanggal_dokumen'],
                'berkas' => $file,
                'keterangan' => $dokumen['keterangan'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        foreach ($this->db->get_where('alumni_pembelajaran_siswa', ['id_alumni' => (string) $alumni['id_alumni']])->result_array() as $pembelajaran) {
            unset($pembelajaran['id_alumni_pembelajaran_siswa']);
            $pembelajaran['peserta_didik_id'] = (string) $id_siswa;
            unset($pembelajaran['id_alumni']);
            unset($pembelajaran['id_siswa_asal']);
            $this->db->insert('pembelajaran_siswa', $pembelajaran);
        }

        foreach ($this->db->get_where('alumni_nilai_siswa', ['id_alumni' => (int) $alumni['id_alumni']])->result_array() as $nilai) {
            unset($nilai['id_alumni_nilai_siswa']);
            $nilai['id_siswa'] = (int) $id_siswa;
            unset($nilai['id_alumni']);
            unset($nilai['id_siswa_asal']);
            $this->db->insert('nilai_siswa', $nilai);
        }

        $this->db->delete('alumni_foto', ['id_alumni' => (int) $alumni['id_alumni']]);
        $this->db->delete('alumni_dokumen', ['id_alumni' => (int) $alumni['id_alumni']]);
        $this->db->delete('alumni_pembelajaran_siswa', ['id_alumni' => (string) $alumni['id_alumni']]);
        $this->db->delete('alumni_nilai_siswa', ['id_alumni' => (int) $alumni['id_alumni']]);
        $this->db->delete('alumni', ['id_alumni' => (int) $alumni['id_alumni']]);

        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_siswa : null;
    }

    private function copyAlumniFile($relative_source, $target_dir, $prefix)
    {
        $source = FCPATH . $relative_source;
        if (!is_file($source)) {
            $fallback = str_replace(['uploads/alumni_foto/', 'uploads/alumni_dokumen/'], ['uploads/siswa_foto/', 'uploads/siswa_dokumen/'], $relative_source);
            $source = FCPATH . $fallback;
            if (!is_file($source)) {
                return null;
            }
        }

        $target_path = FCPATH . trim($target_dir, '/\\') . '/';
        if (!is_dir($target_path)) {
            mkdir($target_path, 0777, true);
        }

        $extension = pathinfo($source, PATHINFO_EXTENSION);
        $file_name = $prefix . '-' . uniqid() . ($extension ? '.' . $extension : '');
        return copy($source, $target_path . $file_name) ? $file_name : null;
    }
}
