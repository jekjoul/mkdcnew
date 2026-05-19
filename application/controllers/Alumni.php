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
}
