<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sync_dapodik extends MY_Controller
{
    private $default_base_url = 'http://localhost:5774';
    private $default_api_key = '30Fgk2Lpd2pqx6f';
    private $default_npsn = '69948104';
    private $default_endpoint = 'WebService/getPesertaDidik';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    public function index()
    {
        $this->page_data['page']->title = 'Kesiswaan';
        $this->page_data['page']->titleUrl = 'siswa/all';
        $this->page_data['page']->subtitle = 'Sinkronisasi Dapodik';
        $this->page_data['page']->subtitleUrl = 'sync_dapodik';
        $this->page_data['page']->icon = 'lucide:refresh-cw';

        $run_id = $this->input->get('run_id');
        $run = $run_id ? $this->db->get_where('dapodik_sync_runs', ['id_run' => $run_id])->row() : $this->getLastRun();

        $items = [];
        $summary_items = [];
        if ($run) {
            $summary_items = $this->db->get_where('dapodik_sync_items', ['id_run' => $run->id_run])->result();
            $this->db->order_by('match_status', 'ASC');
            $this->db->order_by('nama_dapodik', 'ASC');
            $items = $this->db->get_where('dapodik_sync_items', ['id_run' => $run->id_run, 'match_status !=' => 'sama'])->result();
        }

        $this->page_data['run'] = $run;
        $this->page_data['items'] = $items;
        $this->page_data['summary_items'] = $summary_items;
        $this->page_data['base_url'] = $this->session->userdata('dapodik_base_url') ?: $this->default_base_url;
        $this->page_data['api_key'] = $this->session->userdata('dapodik_api_key') ?: $this->default_api_key;
        $this->page_data['npsn'] = $this->session->userdata('dapodik_npsn') ?: $this->default_npsn;
        $this->page_data['endpoint'] = $this->session->userdata('dapodik_endpoint') ?: $this->default_endpoint;

        $this->load->view('sync_dapodik/index', $this->page_data);
    }

    public function fetch()
    {
        postAllowed();

        $base_url = rtrim((string) post('base_url'), '/');
        $api_key = trim((string) post('api_key'));
        $npsn = trim((string) post('npsn'));
        $endpoint = trim((string) post('endpoint'));

        $this->session->set_userdata('dapodik_base_url', $base_url);
        $this->session->set_userdata('dapodik_api_key', $api_key);
        $this->session->set_userdata('dapodik_npsn', $npsn);
        $this->session->set_userdata('dapodik_endpoint', $endpoint);

        $response = $this->requestDapodik($base_url, $endpoint, $api_key, $npsn);
        if (!$response['success']) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', $response['message']);
            redirect('sync_dapodik');
            return;
        }

        $rows = $this->extractRows($response['data']);
        if (empty($rows)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Data Dapodik berhasil dihubungi, tetapi daftar peserta didik kosong atau format respons belum dikenali.');
            redirect('sync_dapodik');
            return;
        }

        $run_id = $this->createPreviewRun($base_url, $endpoint, $npsn, $rows);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Preview sinkronisasi berhasil dibuat. Data lokal belum berubah.');
        redirect('sync_dapodik?run_id=' . $run_id);
    }

    public function apply()
    {
        postAllowed();

        $run_id = post('run_id');
        $item_ids = $this->input->post('item');
        if (empty($item_ids)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Pilih minimal satu baris data untuk diterapkan.');
            redirect('sync_dapodik?run_id=' . $run_id);
            return;
        }

        $created = 0;
        $updated = 0;

        $this->db->where('id_run', $run_id);
        $this->db->where_in('id_item', $item_ids);
        $items = $this->db->get('dapodik_sync_items')->result();
        $locals = $this->db->get('siswa')->result();

        foreach ($items as $item) {
            $data = json_decode($item->payload, true);
            if (empty($data)) {
                continue;
            }

            $siswa_data = $this->buildSiswaData($data);
            $local = $item->local_id_siswa ? $this->db->get_where('siswa', ['id_siswa' => $item->local_id_siswa])->row() : null;
            if (!$local) {
                $local = $this->findLocalSiswa($data, $locals);
            }

            if ($local) {
                $this->db->where('id_siswa', $local->id_siswa);
                $this->db->update('siswa', $siswa_data);
                $updated++;
            } else {
                $this->db->insert('siswa', $siswa_data);
                $data['id_siswa'] = $this->db->insert_id();
                $created++;
            }

            $locals = $this->db->get('siswa')->result();
        }

        $this->db->where('id_run', $run_id);
        $this->db->update('dapodik_sync_runs', ['applied_at' => date('Y-m-d H:i:s')]);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', "Sinkronisasi diterapkan: $created siswa baru, $updated siswa diperbarui.");
        redirect('sync_dapodik?run_id=' . $run_id);
    }

    private function createPreviewRun($base_url, $endpoint, $npsn, $rows)
    {
        $this->db->insert('dapodik_sync_runs', [
            'base_url' => $base_url,
            'endpoint' => $endpoint,
            'npsn' => $npsn,
            'total_remote' => count($rows),
            'created_by' => logged('id') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $run_id = $this->db->insert_id();

        $locals = $this->db->get('siswa')->result();
        foreach ($rows as $row) {
            $dapodik = $this->normalizeDapodikSiswa($row);
            $local = $this->findLocalSiswa($dapodik, $locals);
            $diff = $this->diffSiswa($local, $dapodik);
            $status = !$local ? 'baru' : (empty($diff) ? 'sama' : 'berbeda');
            $diff_fields = array_keys($diff);

            $this->db->insert('dapodik_sync_items', [
                'id_run' => $run_id,
                'match_status' => $status,
                'local_id_siswa' => $local ? $local->id_siswa : null,
                'nama_lokal' => $local ? $local->nama_siswa : null,
                'nama_dapodik' => $dapodik['nama_siswa'],
                'nisn_lokal' => $local ? $local->nisn : null,
                'nisn_dapodik' => $dapodik['nisn'],
                'rombel_lokal' => $local ? $local->rombel : null,
                'rombel_dapodik' => $dapodik['rombel'],
                'diff_fields' => implode(', ', $diff_fields),
                'diff_details' => json_encode($diff),
                'local_payload' => json_encode($local ? $this->normalizeLocalSiswa($local) : []),
                'payload' => json_encode($dapodik),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $run_id;
    }

    private function requestDapodik($base_url, $endpoint, $api_key, $npsn)
    {
        if ($base_url === '' || $endpoint === '' || $api_key === '' || $npsn === '') {
            return ['success' => false, 'message' => 'Base URL, endpoint, bearer token, dan NPSN wajib diisi.'];
        }

        $url = $base_url . '/' . ltrim($endpoint, '/');
        $separator = strpos($url, '?') === false ? '?' : '&';
        $url .= $separator . http_build_query(['npsn' => $npsn]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 30,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\nAuthorization: Bearer " . $api_key . "\r\n",
            ],
        ]);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            return ['success' => false, 'message' => 'Gagal menghubungi Dapodik. Pastikan aplikasi Dapodik berjalan di ' . $base_url . '.'];
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => 'Respons Dapodik bukan JSON: ' . substr(strip_tags($content), 0, 180)];
        }

        if (isset($data['success']) && $data['success'] === false) {
            return ['success' => false, 'message' => isset($data['message']) ? $data['message'] : 'Dapodik menolak request.'];
        }

        return ['success' => true, 'data' => $data];
    }

    private function extractRows($data)
    {
        if (!is_array($data)) {
            return [];
        }

        foreach (['rows', 'data', 'result', 'results', 'peserta_didik'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                return $this->isList($data[$key]) ? $data[$key] : [$data[$key]];
            }
        }

        return $this->isList($data) ? $data : [];
    }

    private function normalizeDapodikSiswa($row)
    {
        return [
            'nama_siswa' => $this->pick($row, ['nama_siswa', 'nama', 'nama_pd']),
            'nisn' => $this->pick($row, ['nisn']),
            'nipd' => $this->pick($row, ['nipd', 'no_induk']),
            'nik' => $this->pick($row, ['nik']),
            'no_kk' => $this->pick($row, ['no_kk', 'nomor_kk']),
            'jenis_kelamin' => $this->normalizeJenisKelamin($this->pick($row, ['jenis_kelamin', 'jk'])),
            'tempat_lahir' => $this->pick($row, ['tempat_lahir']),
            'tanggal_lahir' => $this->normalizeDate($this->pick($row, ['tanggal_lahir', 'tgl_lahir'])),
            'agama' => $this->pick($row, ['agama', 'agama_id_str']),
            'telepon' => $this->pick($row, ['telepon', 'nomor_telepon', 'no_hp', 'hp']),
            'email' => $this->pick($row, ['email']),
            'tanggal_pendaftaran' => $this->normalizeDate($this->pick($row, ['tanggal_pendaftaran', 'tanggal_masuk_sekolah', 'tgl_masuk_sekolah'])),
            'status_pendaftaran' => $this->pick($row, ['status_pendaftaran', 'jenis_pendaftaran_id_str', 'jenis_pendaftaran']),
            'alamat' => $this->pick($row, ['alamat', 'alamat_jalan']),
            'rt' => $this->pick($row, ['rt']),
            'rw' => $this->pick($row, ['rw']),
            'id_provinsi' => $this->pick($row, ['provinsi', 'provinsi_str']),
            'id_kabupaten' => $this->pick($row, ['kabupaten', 'kabupaten_kota', 'kabupaten_kota_str']),
            'id_kecamatan' => $this->pick($row, ['kecamatan', 'kecamatan_str']),
            'id_kelurahan' => $this->pick($row, ['desa_kelurahan', 'kelurahan', 'desa_kelurahan_str']),
            'nama_ayah' => $this->pick($row, ['nama_ayah']),
            'nik_ayah' => $this->pick($row, ['nik_ayah']),
            'pekerjaan_ayah' => $this->pick($row, ['pekerjaan_ayah', 'pekerjaan_ayah_id_str']),
            'penghasilan_ayah' => $this->pick($row, ['penghasilan_ayah', 'penghasilan_ayah_id_str']),
            'tahun_lahir_ayah' => $this->pick($row, ['tahun_lahir_ayah']),
            'pendidikan_ayah' => $this->pick($row, ['pendidikan_ayah', 'jenjang_pendidikan_ayah_id_str']),
            'alamat_ayah' => $this->pick($row, ['alamat_ayah']),
            'nama_ibu' => $this->pick($row, ['nama_ibu', 'nama_ibu_kandung']),
            'nik_ibu' => $this->pick($row, ['nik_ibu']),
            'pekerjaan_ibu' => $this->pick($row, ['pekerjaan_ibu', 'pekerjaan_ibu_id_str']),
            'penghasilan_ibu' => $this->pick($row, ['penghasilan_ibu', 'penghasilan_ibu_id_str']),
            'tahun_lahir_ibu' => $this->pick($row, ['tahun_lahir_ibu']),
            'pendidikan_ibu' => $this->pick($row, ['pendidikan_ibu', 'jenjang_pendidikan_ibu_id_str']),
            'alamat_ibu' => $this->pick($row, ['alamat_ibu']),
            'rombel' => $this->pick($row, ['rombel', 'nama_rombel', 'rombongan_belajar']),
            'status_keaktifan' => $this->pick($row, ['status_keaktifan', 'status_pd', 'status_peserta_didik']),
        ];
    }

    private function buildSiswaData($data)
    {
        $siswa_data = [];
        foreach ($this->comparableFields() as $field => $label) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $siswa_data[$field] = $field === 'jenis_kelamin' ? $this->normalizeJenisKelamin($data[$field]) : $data[$field];
            }
        }

        if (!isset($siswa_data['status_keaktifan'])) {
            $siswa_data['status_keaktifan'] = 'Aktif';
        }

        return $siswa_data;
    }

    private function findLocalSiswa($dapodik, $locals)
    {
        foreach ($locals as $local) {
            if ($dapodik['nisn'] !== '' && $local->nisn === $dapodik['nisn']) {
                return $local;
            }
            if ($dapodik['nik'] !== '' && $local->nik === $dapodik['nik']) {
                return $local;
            }
        }

        foreach ($locals as $local) {
            if ($this->norm($local->nama_siswa) === $this->norm($dapodik['nama_siswa']) && $local->tanggal_lahir === $dapodik['tanggal_lahir']) {
                return $local;
            }
        }

        return null;
    }

    private function diffSiswa($local, $dapodik)
    {
        if (!$local) {
            return [];
        }

        $fields = [
            'nama_siswa', 'nisn', 'nipd', 'nik', 'no_kk', 'jenis_kelamin', 'tempat_lahir',
            'tanggal_lahir', 'agama', 'telepon', 'email', 'rombel', 'tanggal_pendaftaran',
            'status_pendaftaran', 'alamat', 'rt', 'rw', 'id_provinsi', 'id_kabupaten',
            'id_kecamatan', 'id_kelurahan', 'nama_ayah', 'nik_ayah', 'pekerjaan_ayah',
            'penghasilan_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'alamat_ayah',
            'nama_ibu', 'nik_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'tahun_lahir_ibu',
            'pendidikan_ibu', 'alamat_ibu', 'status_keaktifan',
        ];
        $diff = [];
        foreach ($fields as $field) {
            if (!isset($dapodik[$field]) || $dapodik[$field] === '') {
                continue;
            }

            $local_value = isset($local->$field) ? $local->$field : '';
            $local_compare = $field === 'jenis_kelamin' ? $this->normalizeJenisKelamin($local_value) : $local_value;
            $dapodik_compare = $field === 'jenis_kelamin' ? $this->normalizeJenisKelamin($dapodik[$field]) : $dapodik[$field];
            if ($this->norm($local_compare) !== $this->norm($dapodik_compare)) {
                $diff[$field] = [
                    'label' => isset($this->comparableFields()[$field]) ? $this->comparableFields()[$field] : $field,
                    'local' => $local_value,
                    'dapodik' => $field === 'jenis_kelamin' ? $dapodik_compare : $dapodik[$field],
                ];
            }
        }

        return $diff;
    }

    private function normalizeLocalSiswa($local)
    {
        $data = [];
        foreach ($this->comparableFields() as $field => $label) {
            $data[$field] = isset($local->$field) ? $local->$field : '';
        }

        return $data;
    }

    private function comparableFields()
    {
        return [
            'nama_siswa' => 'Nama Siswa',
            'nisn' => 'NISN',
            'nipd' => 'NIPD',
            'nik' => 'NIK',
            'no_kk' => 'No KK',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'telepon' => 'Telepon',
            'email' => 'Email',
            'rombel' => 'Rombel',
            'tanggal_pendaftaran' => 'Tanggal Pendaftaran',
            'status_pendaftaran' => 'Status Pendaftaran',
            'status_keaktifan' => 'Status Keaktifan',
            'alamat' => 'Alamat Siswa',
            'rt' => 'RT',
            'rw' => 'RW',
            'id_provinsi' => 'Provinsi',
            'id_kabupaten' => 'Kabupaten',
            'id_kecamatan' => 'Kecamatan',
            'id_kelurahan' => 'Kelurahan',
            'nama_ayah' => 'Nama Ayah',
            'nik_ayah' => 'NIK Ayah',
            'pekerjaan_ayah' => 'Pekerjaan Ayah',
            'penghasilan_ayah' => 'Penghasilan Ayah',
            'tahun_lahir_ayah' => 'Tahun Lahir Ayah',
            'pendidikan_ayah' => 'Pendidikan Ayah',
            'alamat_ayah' => 'Alamat Ayah',
            'nama_ibu' => 'Nama Ibu',
            'nik_ibu' => 'NIK Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'penghasilan_ibu' => 'Penghasilan Ibu',
            'tahun_lahir_ibu' => 'Tahun Lahir Ibu',
            'pendidikan_ibu' => 'Pendidikan Ibu',
            'alamat_ibu' => 'Alamat Ibu',
        ];
    }

    private function pick($row, $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                return trim((string) $row[$key]);
            }
        }

        return '';
    }

    private function normalizeDate($value)
    {
        if ($value === '') {
            return '';
        }

        $time = strtotime($value);
        return $time ? date('Y-m-d', $time) : $value;
    }

    private function normalizeJenisKelamin($value)
    {
        $value = strtoupper(trim((string) $value));
        if ($value === 'L' || $value === 'LAKI-LAKI' || $value === 'LAKI LAKI') {
            return 'Laki-laki';
        }

        if ($value === 'P' || $value === 'PEREMPUAN') {
            return 'Perempuan';
        }

        return trim((string) $value);
    }

    private function norm($value)
    {
        return strtolower(trim((string) $value));
    }

    private function isList($array)
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    private function getLastRun()
    {
        $this->db->order_by('id_run', 'DESC');
        return $this->db->get('dapodik_sync_runs', 1)->row();
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('dapodik_sync_runs')) {
            $this->dbforge->add_field([
                'id_run' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'base_url' => ['type' => 'VARCHAR', 'constraint' => 255],
                'endpoint' => ['type' => 'VARCHAR', 'constraint' => 255],
                'npsn' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'total_remote' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'applied_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_run', true);
            $this->dbforge->create_table('dapodik_sync_runs', true);
        }

        if (!$this->db->table_exists('dapodik_sync_items')) {
            $this->dbforge->add_field([
                'id_item' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'id_run' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'match_status' => ['type' => 'VARCHAR', 'constraint' => 20],
                'local_id_siswa' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nama_lokal' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'nama_dapodik' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'nisn_lokal' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'nisn_dapodik' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'rombel_lokal' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'rombel_dapodik' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'diff_fields' => ['type' => 'TEXT', 'null' => true],
                'diff_details' => ['type' => 'TEXT', 'null' => true],
                'local_payload' => ['type' => 'TEXT', 'null' => true],
                'payload' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_item', true);
            $this->dbforge->add_key('id_run');
            $this->dbforge->create_table('dapodik_sync_items', true);
        } else {
            if (!$this->db->field_exists('diff_details', 'dapodik_sync_items')) {
                $this->dbforge->add_column('dapodik_sync_items', [
                    'diff_details' => ['type' => 'TEXT', 'null' => true, 'after' => 'diff_fields'],
                ]);
            }

            if (!$this->db->field_exists('local_payload', 'dapodik_sync_items')) {
                $this->dbforge->add_column('dapodik_sync_items', [
                    'local_payload' => ['type' => 'TEXT', 'null' => true, 'after' => 'diff_details'],
                ]);
            }
        }
    }
}
