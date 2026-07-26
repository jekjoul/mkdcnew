<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Presensi extends CI_Controller
{
    // Token keamanan API untuk otentikasi antara Bridge lokal dan Server Online
    const API_TOKEN = 'MKDC_FINGERPRINT_SECRET_KEY_2026';

    // Definisi rentang waktu sesi absensi
    const SESI_DHUHA_START  = '06:00:00';
    const SESI_DHUHA_END    = '09:00:00';
    const SESI_DZUHUR_START = '11:00:00';
    const SESI_DZUHUR_END   = '16:00:00';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // Tambahkan header CORS lengkap agar dapat dipanggil dari JS Fingerprint Bridge App
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control');
        header('Content-Type: application/json');

        // Tangani Preflight OPTIONS Request dari browser
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    // Helper: Validasi token
    private function validate_token($token)
    {
        $valid_token = self::API_TOKEN;
        if ($this->db->table_exists('fingerprint_settings')) {
            $setting = $this->db->get('fingerprint_settings')->row();
            if ($setting && !empty($setting->api_token)) {
                $valid_token = trim($setting->api_token);
            }
        }

        if (empty($token) || ($token !== $valid_token && $token !== self::API_TOKEN)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unauthorized. Invalid API Token.'
            ]);
            exit;
        }
    }

    /**
     * Tentukan sesi berdasarkan jam scan.
     * - 06:00–09:00 = dhuha
     * - 11:00–16:00 = dzuhur
     * - Di luar itu  = other
     */
    private function tentukan_sesi($jam)
    {
        // $jam format: 'HH:MM:SS'
        if ($jam >= self::SESI_DHUHA_START && $jam <= self::SESI_DHUHA_END) {
            return 'dhuha';
        } elseif ($jam >= self::SESI_DZUHUR_START && $jam <= self::SESI_DZUHUR_END) {
            return 'dzuhur';
        }
        return 'other';
    }

    /**
     * Endpoint 1: Sinkronisasi data scanlog dari mesin ke database online
     * POST /api/presensi/sync
     */
    public function sync()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $raw_input = file_get_contents('php://input');
        $data      = json_decode($raw_input, true);

        $token = isset($data['token']) ? $data['token'] : '';
        $this->validate_token($token);

        $logs = isset($data['logs']) ? $data['logs'] : [];
        if (empty($logs)) {
            echo json_encode([
                'status'    => 'success',
                'message'   => 'No logs received.',
                'inserted'  => 0,
                'overwrite' => 0,
                'ignored'   => 0,
                'total'     => 0
            ]);
            return;
        }

        $inserted_count  = 0;
        $overwrite_count = 0;
        $ignored_count   = 0;

        $batch_rows = [];
        $seen_keys  = [];

        foreach ($logs as $log) {
            $pin_raw   = trim((string)($log['pin'] ?? $log['PIN'] ?? $log['user_id'] ?? $log['UserId'] ?? ''));
            $scan_date = isset($log['scan_date']) ? trim($log['scan_date']) : (isset($log['ScanDate']) ? trim($log['ScanDate']) : '');

            if ($pin_raw === '' || empty($scan_date)) {
                $ignored_count++;
                continue;
            }

            $date     = date('Y-m-d', strtotime($scan_date));
            $jam_scan = date('H:i:s', strtotime($scan_date));
            $sesi     = $this->tentukan_sesi($jam_scan);

            $exist_key = "{$pin_raw}_{$date}_{$jam_scan}";

            if (isset($seen_keys[$exist_key])) {
                $overwrite_count++;
            } else {
                $seen_keys[$exist_key] = true;
                $batch_rows[] = [
                    'pin'        => $pin_raw,
                    'tanggal'    => $date,
                    'jam_scan'   => $jam_scan,
                    'sesi'       => $sesi,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $inserted_count++;
            }
        }

        if (!empty($batch_rows)) {
            $value_strings = [];
            foreach ($batch_rows as $row) {
                $pin_esc = $this->db->escape($row['pin']);
                $tgl_esc = $this->db->escape($row['tanggal']);
                $jam_esc = $this->db->escape($row['jam_scan']);
                $ses_esc = $this->db->escape($row['sesi']);
                $now_esc = $this->db->escape(date('Y-m-d H:i:s'));

                // id_user default 0, tipe_user default 'siswa' untuk kompatibilitas kolom DB
                $value_strings[] = "('siswa', 0, {$pin_esc}, {$tgl_esc}, {$jam_esc}, {$ses_esc}, {$now_esc}, {$now_esc})";
            }

            // Gunakan ON DUPLICATE KEY UPDATE agar 100% data mentah (RAW) langsung tersimpan
            $sql_chunks = array_chunk($value_strings, 100);
            foreach ($sql_chunks as $chunk) {
                $sql = "INSERT INTO presensi_harian (tipe_user, id_user, pin, tanggal, jam_scan, sesi, created_at, updated_at) 
                        VALUES " . implode(',', $chunk) . "
                        ON DUPLICATE KEY UPDATE 
                            sesi       = VALUES(sesi),
                            updated_at = VALUES(updated_at)";
                $this->db->query($sql);
            }
        }

        echo json_encode([
            'status'    => 'success',
            'message'   => 'Sync scanlog RAW completed.',
            'inserted'  => $inserted_count,
            'overwrite' => $overwrite_count,
            'ignored'   => $ignored_count,
            'total'     => count($logs)
        ]);
    }

    /**
     * Endpoint 2: Mengambil daftar tugas sinkronisasi user yang pending dari server
     * GET /api/presensi/pending_tasks?token=SECRET_KEY
     */
    public function pending_tasks()
    {
        $token = $this->input->get('token');
        $this->validate_token($token);

        $this->db->order_by('id', 'ASC');
        $tasks = $this->db->get_where('fingerprint_tasks', ['status' => 'pending'])->result();

        echo json_encode([
            'status' => 'success',
            'tasks'  => $tasks
        ]);
    }

    /**
     * Endpoint 3: Melaporkan hasil eksekusi tugas sinkronisasi oleh bridge ke server
     * POST /api/presensi/task_result
     */
    public function task_result()
    {
        $raw_input = file_get_contents('php://input');
        $data      = json_decode($raw_input, true);

        $token = isset($data['token']) ? $data['token'] : '';
        $this->validate_token($token);

        $task_id       = isset($data['task_id'])       ? intval($data['task_id'])    : 0;
        $status        = isset($data['status'])        ? $data['status']             : '';
        $error_message = isset($data['error_message']) ? $data['error_message']      : null;

        if ($task_id <= 0 || !in_array($status, ['success', 'failed'])) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid parameters.'
            ]);
            return;
        }

        $task = $this->db->get_where('fingerprint_tasks', ['id' => $task_id])->row();
        if (!$task) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Task not found.'
            ]);
            return;
        }

        $attempts    = $task->attempts + 1;
        $update_data = [
            'status'        => $status,
            'attempts'      => $attempts,
            'error_message' => $error_message
        ];

        if ($status === 'failed' && $attempts < 3) {
            $update_data['status'] = 'pending';
        }

        $this->db->where('id', $task_id);
        $this->db->update('fingerprint_tasks', $update_data);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Task status updated.'
        ]);
    }

    /**
     * Endpoint 4: Mengambil daftar siswa aktif untuk sinkronisasi dengan mesin
     * GET /api/presensi/active_students?token=SECRET_KEY
     */
    public function active_students()
    {
        $raw_input = file_get_contents('php://input');
        $data      = json_decode($raw_input, true);
        $token     = $this->input->get('token');
        if (empty($token) && isset($data['token'])) {
            $token = $data['token'];
        }
        if (empty($token)) {
            $token = $this->input->post('token');
        }

        $this->validate_token($token);

        $this->db->select('CAST(nipd AS UNSIGNED) as pin, nama_siswa as nama, nipd');
        $this->db->from('siswa');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->where("nipd IS NOT NULL AND nipd != ''");
        $this->db->order_by('nama_siswa', 'ASC');
        $students = $this->db->get()->result();

        foreach ($students as &$s) {
            $s->pin = (int)$s->pin;
        }

        echo json_encode([
            'status'   => 'success',
            'students' => $students
        ]);
    }

    /**
     * Endpoint 5: Mengambil daftar PTK / Guru aktif untuk sinkronisasi dengan mesin
     * GET /api/presensi/active_ptk?token=SECRET_KEY
     * PIN untuk PTK/Guru adalah NIY guru/ptk!
     */
    public function active_ptk()
    {
        $raw_input = file_get_contents('php://input');
        $data      = json_decode($raw_input, true);
        $token     = $this->input->get('token');
        if (empty($token) && isset($data['token'])) {
            $token = $data['token'];
        }
        if (empty($token)) {
            $token = $this->input->post('token');
        }

        $this->validate_token($token);

        $this->db->select('CAST(niy AS UNSIGNED) as pin, nama_ptk as nama, niy, pin_fingerprint');
        $this->db->from('ptk');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->where("niy IS NOT NULL AND niy != ''");
        $this->db->order_by('nama_ptk', 'ASC');
        $ptk_list = $this->db->get()->result();

        foreach ($ptk_list as &$p) {
            $p->pin = (int)$p->pin;
        }

        echo json_encode([
            'status' => 'success',
            'ptk'    => $ptk_list
        ]);
    }

    /**
     * Endpoint 6: Menerima data user & template sidik jari dari mesin ke server MKDC
     * POST /api/presensi/receive_machine_users
     */
    public function receive_machine_users()
    {
        $this->load->model('Fingerprint_bridge_model', 'bridge_model');
        if (method_exists($this->bridge_model, 'ensureTables')) {
            $this->bridge_model->ensureTables();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = $_POST;
            if (isset($input['users']) && is_string($input['users'])) {
                $input['users'] = json_decode($input['users'], true);
            }
        }

        $token = $input['token'] ?? $this->input->get_post('token');
        $this->validate_token($token);

        $users = $input['users'] ?? [];
        if (!is_array($users) || empty($users)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data users kosong atau format tidak valid.'
            ]);
            return;
        }

        $inserted       = 0;
        $updated        = 0;
        $template_count = 0;

        foreach ($users as $u) {
            $pin  = trim((string)($u['pin'] ?? $u['PIN'] ?? ''));
            $nama = trim($u['nama'] ?? $u['Name'] ?? $u['name'] ?? '');
            $pwd  = trim($u['pwd'] ?? $u['Password'] ?? '');
            $rfid = trim($u['rfid'] ?? $u['RFID'] ?? '');
            $priv = intval($u['privilege'] ?? $u['Privilege'] ?? 0);
            $tmpl = $u['templates'] ?? $u['Template'] ?? [];

            if (empty($pin)) continue;

            $num_tmpl = is_array($tmpl) ? count($tmpl) : 0;

            $check = $this->db->get_where('presensi_machine_users', ['pin' => $pin])->row();

            $user_data = [
                'pin'             => $pin,
                'nama'            => $nama,
                'password'        => $pwd,
                'rfid'            => $rfid,
                'privilege'       => $priv,
                'jumlah_template' => $num_tmpl,
                'updated_at'      => date('Y-m-d H:i:s')
            ];

            if ($check) {
                $this->db->where('pin', $pin);
                $this->db->update('presensi_machine_users', $user_data);
                $updated++;
            } else {
                $user_data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('presensi_machine_users', $user_data);
                $inserted++;
            }

            if (is_array($tmpl) && !empty($tmpl)) {
                foreach ($tmpl as $t) {
                    if (is_array($t)) {
                        $idx     = intval($t['finger_idx'] ?? $t['idx'] ?? $t['finger_id'] ?? 0);
                        $alg_ver = intval($t['alg_ver'] ?? $t['alg_version'] ?? 10);
                        $raw_t   = trim($t['template'] ?? $t['tmp'] ?? $t['Template'] ?? '');
                    } else {
                        $idx     = 0;
                        $alg_ver = 10;
                        $raw_t   = trim((string)$t);
                    }

                    if (!empty($raw_t)) {
                        $t_data = [
                            'pin'        => $pin,
                            'finger_idx' => $idx,
                            'alg_ver'    => $alg_ver,
                            'template'   => $raw_t
                        ];

                        $t_check = $this->db->get_where('presensi_machine_templates', [
                            'pin'        => $pin,
                            'finger_idx' => $idx
                        ])->row();

                        if ($t_check) {
                            $this->db->where('id', $t_check->id);
                            $this->db->update('presensi_machine_templates', $t_data);
                        } else {
                            $t_data['created_at'] = date('Y-m-d H:i:s');
                            $this->db->insert('presensi_machine_templates', $t_data);
                        }
                        $template_count++;
                    }
                }
            }
        }

        echo json_encode([
            'status'          => 'success',
            'message'         => "Berhasil menyimpan {$inserted} user baru, {$updated} user diperbarui, dan {$template_count} template sidik jari ke server MKDC.",
            'total_inserted'  => $inserted,
            'total_updated'   => $updated,
            'total_templates' => $template_count
        ]);
    }
}
