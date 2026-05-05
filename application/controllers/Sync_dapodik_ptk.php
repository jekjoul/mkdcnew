<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sync_dapodik_ptk extends MY_Controller
{
    private $default_base_url = 'http://localhost:5774';
    private $default_token = '30Fgk2Lpd2pqx6f';
    private $default_npsn = '69948104';
    private $default_endpoint = 'WebService/getGtk';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    public function index()
    {
        $this->page_data['page']->title = 'PTK';
        $this->page_data['page']->titleUrl = 'ptk/ptk';
        $this->page_data['page']->subtitle = 'Sinkronisasi Dapodik GTK';
        $this->page_data['page']->subtitleUrl = 'sync_dapodik_ptk';
        $this->page_data['page']->icon = 'lucide:refresh-cw';

        $run_id = $this->input->get('run_id');
        $run = $run_id ? $this->db->get_where('dapodik_ptk_sync_runs', ['id_run' => $run_id])->row() : $this->getLastRun();

        $items = [];
        $summary_items = [];
        if ($run) {
            $summary_items = $this->db->get_where('dapodik_ptk_sync_items', ['id_run' => $run->id_run])->result();
            $this->db->order_by('match_status', 'ASC');
            $this->db->order_by('nama_dapodik', 'ASC');
            $items = $this->db->get_where('dapodik_ptk_sync_items', ['id_run' => $run->id_run, 'match_status !=' => 'sama'])->result();
        }

        $this->page_data['run'] = $run;
        $this->page_data['items'] = $items;
        $this->page_data['summary_items'] = $summary_items;
        $this->page_data['base_url'] = $this->session->userdata('dapodik_ptk_base_url') ?: $this->default_base_url;
        $this->page_data['api_key'] = $this->session->userdata('dapodik_ptk_api_key') ?: $this->default_token;
        $this->page_data['npsn'] = $this->session->userdata('dapodik_ptk_npsn') ?: $this->default_npsn;
        $this->page_data['endpoint'] = $this->session->userdata('dapodik_ptk_endpoint') ?: $this->default_endpoint;

        $this->load->view('sync_dapodik_ptk/index', $this->page_data);
    }

    public function fetch()
    {
        postAllowed();

        $base_url = rtrim((string) post('base_url'), '/');
        $api_key = trim((string) post('api_key'));
        $npsn = trim((string) post('npsn'));
        $endpoint = trim((string) post('endpoint'));

        $this->session->set_userdata('dapodik_ptk_base_url', $base_url);
        $this->session->set_userdata('dapodik_ptk_api_key', $api_key);
        $this->session->set_userdata('dapodik_ptk_npsn', $npsn);
        $this->session->set_userdata('dapodik_ptk_endpoint', $endpoint);

        $response = $this->requestDapodik($base_url, $endpoint, $api_key, $npsn);
        if (!$response['success']) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', $response['message']);
            redirect('sync_dapodik_ptk');
            return;
        }

        $rows = $this->extractRows($response['data']);
        if (empty($rows)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Data GTK Dapodik kosong atau format respons belum dikenali.');
            redirect('sync_dapodik_ptk');
            return;
        }

        $run_id = $this->createPreviewRun($base_url, $endpoint, $npsn, $rows);
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', 'Preview sinkronisasi GTK berhasil dibuat. Data PTK lokal belum berubah.');
        redirect('sync_dapodik_ptk?run_id=' . $run_id);
    }

    public function apply()
    {
        postAllowed();

        $run_id = post('run_id');
        $item_ids = $this->input->post('item');
        if (empty($item_ids)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', 'Pilih minimal satu data GTK untuk diterapkan.');
            redirect('sync_dapodik_ptk?run_id=' . $run_id);
            return;
        }

        $created = 0;
        $updated = 0;

        $this->db->where('id_run', $run_id);
        $this->db->where_in('id_item', $item_ids);
        $items = $this->db->get('dapodik_ptk_sync_items')->result();
        $locals = $this->db->get('ptk')->result();

        foreach ($items as $item) {
            $data = json_decode($item->payload, true);
            if (empty($data)) {
                continue;
            }

            $local = $item->local_id_ptk ? $this->db->get_where('ptk', ['id_ptk' => $item->local_id_ptk])->row() : null;
            if (!$local) {
                $local = $this->findLocalPtk($data, $locals);
            }

            if ($local) {
                $ptk_data = $this->buildPtkUpdateData($data);
                if (!empty($ptk_data)) {
                    $this->db->where('id_ptk', $local->id_ptk);
                    $this->db->update('ptk', $ptk_data);
                    $updated++;
                }
            } else {
                $this->db->insert('ptk', $this->buildPtkInsertData($data));
                $created++;
            }

            $locals = $this->db->get('ptk')->result();
        }

        $this->db->where('id_run', $run_id);
        $this->db->update('dapodik_ptk_sync_runs', ['applied_at' => date('Y-m-d H:i:s')]);

        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', "Sinkronisasi GTK diterapkan: $created PTK baru, $updated PTK diperbarui.");
        redirect('sync_dapodik_ptk?run_id=' . $run_id);
    }

    private function createPreviewRun($base_url, $endpoint, $npsn, $rows)
    {
        $this->db->insert('dapodik_ptk_sync_runs', [
            'base_url' => $base_url,
            'endpoint' => $endpoint,
            'npsn' => $npsn,
            'total_remote' => count($rows),
            'created_by' => logged('id') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $run_id = $this->db->insert_id();

        $locals = $this->db->get('ptk')->result();
        foreach ($rows as $row) {
            $dapodik = $this->normalizeDapodikPtk($row);
            $local = $this->findLocalPtk($dapodik, $locals);
            $diff = $this->diffPtk($local, $dapodik);
            $status = !$local ? 'baru' : (empty($diff) ? 'sama' : 'berbeda');

            $this->db->insert('dapodik_ptk_sync_items', [
                'id_run' => $run_id,
                'match_status' => $status,
                'local_id_ptk' => $local ? $local->id_ptk : null,
                'nama_lokal' => $local ? $local->nama_ptk : null,
                'nama_dapodik' => $dapodik['nama_ptk'],
                'nik_lokal' => $local ? $local->nik : null,
                'nik_dapodik' => $dapodik['nik'],
                'nuptk_lokal' => $local ? $local->nuptk : null,
                'nuptk_dapodik' => $dapodik['nuptk'],
                'penugasan_lokal' => $local ? $local->penugasan : null,
                'penugasan_dapodik' => $dapodik['penugasan'],
                'diff_fields' => implode(', ', array_keys($diff)),
                'diff_details' => json_encode($diff),
                'local_payload' => json_encode($local ? $this->normalizeLocalPtk($local) : []),
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

        foreach (['rows', 'data', 'result', 'results', 'gtk', 'ptk'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                return $this->isList($data[$key]) ? $data[$key] : [$data[$key]];
            }
        }

        return $this->isList($data) ? $data : [];
    }

    private function normalizeDapodikPtk($row)
    {
        return [
            'nama_ptk' => $this->pick($row, ['nama', 'nama_ptk']),
            'jenis_kelamin' => $this->normalizeJenisKelamin($this->pick($row, ['jenis_kelamin', 'jk'])),
            'tempat_lahir' => $this->pick($row, ['tempat_lahir']),
            'tanggal_lahir' => $this->normalizeDate($this->pick($row, ['tanggal_lahir', 'tgl_lahir'])),
            'agama' => $this->pick($row, ['agama_id_str', 'agama']),
            'nik' => $this->pick($row, ['nik']),
            'niy' => $this->pick($row, ['niy', 'nip']),
            'nuptk' => $this->pick($row, ['nuptk']),
            'tgl_sk_pengangkatan' => $this->normalizeDate($this->pick($row, ['tanggal_surat_tugas', 'tgl_sk_pengangkatan'])),
            'status_pegawai' => $this->normalizeStatusPegawai($this->pick($row, ['status_kepegawaian_id_str', 'status_pegawai'])),
            'penugasan' => $this->normalizePenugasan($row),
            'status_keaktifan' => 'Aktif',
        ];
    }

    private function buildPtkUpdateData($data)
    {
        $ptk_data = [];
        foreach ($this->comparableFields() as $field => $label) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $ptk_data[$field] = $data[$field];
            }
        }

        return $ptk_data;
    }

    private function buildPtkInsertData($data)
    {
        $insert = $this->buildPtkUpdateData($data);
        $nik = isset($insert['nik']) && $insert['nik'] !== '' ? $insert['nik'] : 'Dapodik-' . time() . rand(100, 999);

        return array_merge([
            'nama_ptk' => 'Tanpa Nama',
            'gelar_depan' => null,
            'gelar_belakang' => null,
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => '-',
            'tanggal_lahir' => date('Y-m-d'),
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Kawin',
            'nama_ibu_kandung' => '-',
            'nik' => $nik,
            'niy' => '-',
            'nuptk' => null,
            'email' => $this->makeFallbackEmail($nik),
            'status_pegawai' => 'GTY/PTY',
            'penugasan' => 'Guru',
            'password' => hash('sha256', $nik),
            'status_keaktifan' => 'Aktif',
            'foto' => 'default.png',
        ], $insert);
    }

    private function findLocalPtk($dapodik, $locals)
    {
        foreach ($locals as $local) {
            if ($dapodik['nik'] !== '' && $local->nik === $dapodik['nik']) {
                return $local;
            }
            if ($dapodik['nuptk'] !== '' && $local->nuptk === $dapodik['nuptk']) {
                return $local;
            }
        }

        foreach ($locals as $local) {
            if ($this->norm($local->nama_ptk) === $this->norm($dapodik['nama_ptk']) && $local->tanggal_lahir === $dapodik['tanggal_lahir']) {
                return $local;
            }
        }

        return null;
    }

    private function diffPtk($local, $dapodik)
    {
        if (!$local) {
            return [];
        }

        $diff = [];
        foreach ($this->comparableFields() as $field => $label) {
            if (!isset($dapodik[$field]) || $dapodik[$field] === '') {
                continue;
            }

            $local_value = isset($local->$field) ? $local->$field : '';
            if ($this->norm($local_value) !== $this->norm($dapodik[$field])) {
                $diff[$field] = [
                    'label' => $label,
                    'local' => $local_value,
                    'dapodik' => $dapodik[$field],
                ];
            }
        }

        return $diff;
    }

    private function normalizeLocalPtk($local)
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
            'nama_ptk' => 'Nama PTK',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'nik' => 'NIK',
            'niy' => 'NIY/NIP',
            'nuptk' => 'NUPTK',
            'tgl_sk_pengangkatan' => 'Tanggal SK/Penugasan',
            'status_pegawai' => 'Status Pegawai',
            'penugasan' => 'Penugasan',
            'status_keaktifan' => 'Status Keaktifan',
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

    private function normalizeStatusPegawai($value)
    {
        $upper = strtoupper(trim((string) $value));
        if (strpos($upper, 'ASN') !== false || strpos($upper, 'PNS') !== false || strpos($upper, 'PPPK') !== false) {
            return 'ASN';
        }

        return $value !== '' ? 'GTY/PTY' : '';
    }

    private function normalizePenugasan($row)
    {
        $jenis = strtoupper($this->pick($row, ['jenis_ptk_id_str']));
        $jabatan = strtoupper($this->pick($row, ['jabatan_ptk_id_str']));

        if (strpos($jabatan, 'KEPALA SEKOLAH') !== false || strpos($jenis, 'KEPALA SEKOLAH') !== false) {
            return 'Kepala Sekolah';
        }
        if (strpos($jenis, 'TENAGA KEPENDIDIKAN') !== false || strpos($jabatan, 'TENAGA ADMINISTRASI') !== false) {
            return 'TAS';
        }
        if (strpos($jenis, 'GURU') !== false || strpos($jabatan, 'GURU') !== false) {
            return 'Guru';
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

    private function makeFallbackEmail($seed)
    {
        $email = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', (string) $seed)) . '@dapodik.local';
        if (!$this->db->get_where('ptk', ['email' => $email])->row()) {
            return $email;
        }

        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', (string) $seed)) . '-' . time() . '@dapodik.local';
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
        return $this->db->get('dapodik_ptk_sync_runs', 1)->row();
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('dapodik_ptk_sync_runs')) {
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
            $this->dbforge->create_table('dapodik_ptk_sync_runs', true);
        }

        if (!$this->db->table_exists('dapodik_ptk_sync_items')) {
            $this->dbforge->add_field([
                'id_item' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'id_run' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'match_status' => ['type' => 'VARCHAR', 'constraint' => 20],
                'local_id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nama_lokal' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'nama_dapodik' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'nik_lokal' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'nik_dapodik' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'nuptk_lokal' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'nuptk_dapodik' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'penugasan_lokal' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'penugasan_dapodik' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'diff_fields' => ['type' => 'TEXT', 'null' => true],
                'diff_details' => ['type' => 'TEXT', 'null' => true],
                'local_payload' => ['type' => 'TEXT', 'null' => true],
                'payload' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_item', true);
            $this->dbforge->add_key('id_run');
            $this->dbforge->create_table('dapodik_ptk_sync_items', true);
        }
    }
}
