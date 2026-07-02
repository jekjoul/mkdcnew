<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Alumni_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function ensureAlumniTables()
    {
        if (!$this->db->table_exists('alumni')) {
            $this->db->query('CREATE TABLE alumni LIKE siswa');
            $this->db->query('ALTER TABLE alumni CHANGE id_siswa id_alumni INT(11) NOT NULL AUTO_INCREMENT');
            $this->db->query('ALTER TABLE alumni ADD COLUMN status_alumni VARCHAR(30) NULL AFTER status_keaktifan');
            $this->db->query('ALTER TABLE alumni ADD COLUMN tanggal_alumni DATE NULL AFTER status_alumni');
            $this->db->query('ALTER TABLE alumni ADD COLUMN sekolah_terakhir VARCHAR(150) NULL AFTER tanggal_alumni');
            $this->db->query('ALTER TABLE alumni ADD COLUMN rombel_terakhir VARCHAR(150) NULL AFTER sekolah_terakhir');
            $this->db->query('ALTER TABLE alumni ADD COLUMN id_siswa_asal INT(11) NULL AFTER rombel_terakhir');
        }

        if ($this->db->table_exists('alumni') && $this->db->table_exists('siswa')) {
            $siswa_fields = $this->db->list_fields('siswa');
            $alumni_fields = $this->db->list_fields('alumni');
            $missing_fields = array_diff($siswa_fields, $alumni_fields);
            if (!empty($missing_fields)) {
                foreach ($missing_fields as $field) {
                    if ($field === 'id_siswa') continue;
                    $field_info = $this->db->query("SHOW COLUMNS FROM siswa WHERE Field = " . $this->db->escape($field))->row();
                    if ($field_info) {
                        $type = $field_info->Type;
                        $null = $field_info->Null === 'YES' ? 'NULL' : 'NOT NULL';
                        $default = $field_info->Default !== null ? "DEFAULT " . $this->db->escape($field_info->Default) : "";
                        $this->db->query("ALTER TABLE alumni ADD COLUMN `$field` $type $null $default");
                    }
                }
            }
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

    public function moveSiswaToAlumni($id_siswa, $status_alumni, $tanggal_alumni = null)
    {
        if (!$tanggal_alumni) $tanggal_alumni = date('Y-m-d');
        $siswa = $this->db->get_where('siswa', ['id_siswa' => $id_siswa])->row_array();
        if (!$siswa) {
            return null;
        }

        $this->ensureAlumniTables();
        $terakhir = $this->getPembelajaranTerakhirUntukAlumni($id_siswa);
        $this->db->trans_start();

        // Check duplicate alumni by NISN and NIK
        $existing_alumni = null;
        if (!empty($siswa['nisn']) || !empty($siswa['nik'])) {
            $this->db->group_start();
            if (!empty($siswa['nisn'])) {
                $this->db->where('nisn', $siswa['nisn']);
            }
            if (!empty($siswa['nik'])) {
                if (!empty($siswa['nisn'])) {
                    $this->db->or_where('nik', $siswa['nik']);
                } else {
                    $this->db->where('nik', $siswa['nik']);
                }
            }
            $this->db->group_end();
            $existing_alumni = $this->db->get('alumni')->row_array();
        }

        if ($existing_alumni) {
            $id_alumni = $existing_alumni['id_alumni'];
            $alumni_data = [];
            $alumni_fields = $this->db->list_fields('alumni');
            
            foreach ($alumni_fields as $field) {
                if ($field === 'id_alumni') continue;
                
                $old_val = isset($existing_alumni[$field]) ? $existing_alumni[$field] : null;
                $new_val = isset($siswa[$field]) ? $siswa[$field] : null;
                
                // If old is empty, fill with new
                if (in_array($old_val, [null, '', 0, '0'], true) && !in_array($new_val, [null, '', 0, '0'], true)) {
                    $alumni_data[$field] = $new_val;
                }
            }
            
            $alumni_data['id_siswa_asal'] = $id_siswa;
            $alumni_data['status_alumni'] = $status_alumni;
            $alumni_data['status_keaktifan'] = $status_alumni;
            $alumni_data['tanggal_alumni'] = $tanggal_alumni;
            if (empty($existing_alumni['sekolah_terakhir'])) {
                $alumni_data['sekolah_terakhir'] = $terakhir['sekolah'] ?: null;
            }
            if (empty($existing_alumni['rombel_terakhir'])) {
                $alumni_data['rombel_terakhir'] = $terakhir['rombel'] ?: ($siswa['rombel'] ?: null);
            }
            
            if (!empty($alumni_data)) {
                $this->db->where('id_alumni', $id_alumni);
                $this->db->update('alumni', $alumni_data);
            }
        } else {
            $alumni_data = [];
            $alumni_fields = $this->db->list_fields('alumni');
            foreach ($siswa as $field => $value) {
                if ($field !== 'id_siswa' && in_array($field, $alumni_fields, true)) {
                    $alumni_data[$field] = $value;
                }
            }
            $alumni_data['id_siswa_asal'] = $id_siswa;
            $alumni_data['status_alumni'] = $status_alumni;
            $alumni_data['status_keaktifan'] = $status_alumni;
            $alumni_data['tanggal_alumni'] = $tanggal_alumni;
            $alumni_data['sekolah_terakhir'] = $terakhir['sekolah'] ?: null;
            $alumni_data['rombel_terakhir'] = $terakhir['rombel'] ?: ($siswa['rombel'] ?: null);
            
            $this->db->insert('alumni', $alumni_data);
            $id_alumni = $this->db->insert_id();
        }

        foreach ($this->db->get_where('siswa_foto', ['id_siswa' => $id_siswa])->result_array() as $foto) {
            $foto_file = $this->moveUploadedFile('uploads/siswa_foto/' . $foto['foto'], 'uploads/alumni_foto', $id_alumni);
            $this->db->insert('alumni_foto', [
                'id_alumni' => $id_alumni,
                'id_foto_asal' => $foto['id_foto'],
                'foto' => $foto_file,
                'label' => $foto['label'],
                'created_at' => $foto['created_at'],
            ]);
        }

        foreach ($this->db->get_where('siswa_dokumen', ['id_siswa' => $id_siswa])->result_array() as $dokumen) {
            $berkas = $this->moveUploadedFile('uploads/siswa_dokumen/' . $dokumen['berkas'], 'uploads/alumni_dokumen', $id_alumni);
            $this->db->insert('alumni_dokumen', [
                'id_alumni' => $id_alumni,
                'id_dokumen_asal' => $dokumen['id_dokumen'],
                'id_jenis_dokumen' => $dokumen['id_jenis_dokumen'],
                'nomor_dokumen' => $dokumen['nomor_dokumen'],
                'tanggal_dokumen' => $dokumen['tanggal_dokumen'],
                'berkas' => $berkas,
                'keterangan' => $dokumen['keterangan'],
                'created_at' => $dokumen['created_at'],
                'updated_at' => $dokumen['updated_at'],
            ]);
        }

        foreach ($this->db->get_where('pembelajaran_siswa', ['peserta_didik_id' => (string) $id_siswa])->result_array() as $pembelajaran) {
            unset($pembelajaran['id_pembelajaran_siswa']);
            $pembelajaran['id_alumni'] = (string) $id_alumni;
            $pembelajaran['id_siswa_asal'] = $id_siswa;
            unset($pembelajaran['peserta_didik_id']);
            $this->db->insert('alumni_pembelajaran_siswa', $pembelajaran);
        }

        foreach ($this->db->get_where('nilai_siswa', ['id_siswa' => (int) $id_siswa])->result_array() as $nilai) {
            unset($nilai['id_nilai_siswa']);
            $nilai['id_alumni'] = $id_alumni;
            $nilai['id_siswa_asal'] = $id_siswa;
            unset($nilai['id_siswa']);
            $this->db->insert('alumni_nilai_siswa', $nilai);
        }

        $this->db->delete('siswa_foto', ['id_siswa' => $id_siswa]);
        $this->db->delete('siswa_dokumen', ['id_siswa' => $id_siswa]);
        $this->db->delete('pembelajaran_siswa', ['peserta_didik_id' => (string) $id_siswa]);
        $this->db->delete('nilai_siswa', ['id_siswa' => (int) $id_siswa]);
        $this->db->delete('siswa', ['id_siswa' => $id_siswa]);

        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_alumni : null;
    }

    private function getPembelajaranTerakhirUntukAlumni($id_siswa)
    {
        $this->db->select('l.nama_lembaga, t.nama_tingkat, r.nama_rombel');
        $this->db->from('pembelajaran_siswa ps');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = ps.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah', 'left');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel', 'left');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran', 'left');
        $this->db->where('ps.peserta_didik_id', (string) $id_siswa);
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $row = $this->db->get()->row();

        if (!$row) {
            return ['sekolah' => '', 'rombel' => ''];
        }

        $rombel = trim((string) $row->nama_tingkat);
        $rombel = $rombel !== '' ? $rombel . ' - ' . $row->nama_rombel : $row->nama_rombel;
        return [
            'sekolah' => $row->nama_lembaga,
            'rombel' => $rombel,
        ];
    }

    private function moveUploadedFile($source_relative_path, $target_relative_dir, $id_alumni)
    {
        $source = FCPATH . $source_relative_path;
        $original = basename($source_relative_path);
        if ($original === '' || !is_file($source)) {
            return $original;
        }

        $target_dir = FCPATH . trim($target_relative_dir, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_name = 'alumni-' . $id_alumni . '-' . uniqid('', true) . '-' . $original;
        $target = $target_dir . $target_name;
        if (rename($source, $target)) {
            return $target_name;
        }

        return $original;
    }
}
