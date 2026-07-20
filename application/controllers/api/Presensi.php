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
        header('Content-Type: application/json');
    }

    // Helper: Validasi token
    private function validate_token($token)
    {
        if (empty($token) || $token !== self::API_TOKEN) {
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
     *
     * Format log yang diterima:
     * { "token": "...", "logs": [ { "pin": 123, "scan_date": "2026-07-20 07:15:00" }, ... ] }
     *
     * Aturan penyimpanan:
     * - Setiap log → 1 baris di presensi_harian (raw data)
     * - UNIQUE INDEX di (tipe_user, id_user, tanggal, sesi)
     * - Jika tap ulang di sesi yang sama → OVERWRITE (UPDATE jam_scan)
     * - Jika tap di sesi berbeda dalam 1 hari → INSERT baris baru
     * - Klasifikasi sesi otomatis berdasarkan jam: dhuha (06-09), dzuhur (11-16), other
     */
    public function sync()
    {
        $raw_input = file_get_contents('php://input');
        $data      = json_decode($raw_input, true);

        $token = isset($data['token']) ? $data['token'] : '';
        $this->validate_token($token);

        $logs = isset($data['logs']) ? $data['logs'] : [];
        if (empty($logs)) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'No logs received.'
            ]);
            return;
        }

        $inserted_count  = 0;
        $overwrite_count = 0;
        $ignored_count   = 0;

        foreach ($logs as $log) {
            $pin       = isset($log['pin']) ? intval($log['pin']) : 0;
            $scan_date = isset($log['scan_date']) ? trim($log['scan_date']) : '';

            // Lewati data tidak valid
            if ($pin <= 0 || empty($scan_date)) {
                $ignored_count++;
                continue;
            }

            // --- Identifikasi user berdasarkan PIN (= NIPD siswa tanpa leading zeros) ---
            // pin_fingerprint di tabel siswa sudah diisi dengan NIPD sebagai BIGINT
            // sehingga langsung bisa dicocokkan dengan PIN integer dari mesin
            $tipe_user = null;
            $id_user   = null;

            // 1. Cari siswa berdasarkan pin_fingerprint = NIPD
            $siswa = $this->db->get_where('siswa', ['pin_fingerprint' => $pin])->row();

            if ($siswa) {
                $tipe_user = 'siswa';
                $id_user   = $siswa->id_siswa;
            } else {
                // 2. Cek apakah PIN milik PTK
                $ptk = $this->db->get_where('ptk', ['pin_fingerprint' => $pin])->row();
                if ($ptk) {
                    $tipe_user = 'ptk';
                    $id_user   = $ptk->id_ptk;
                } else {
                    // 3. PIN tidak dikenal → simpan sebagai unidentified (id_user = 0)
                    // Admin dapat memetakan pin ini nanti lewat halaman manajemen siswa
                    $tipe_user = 'siswa';
                    $id_user   = 0;
                }
            }


            // --- Parsing tanggal dan jam ---
            $date     = date('Y-m-d', strtotime($scan_date));
            $jam_scan = date('H:i:s', strtotime($scan_date));

            // --- Klasifikasi sesi berdasarkan jam ---
            $sesi = $this->tentukan_sesi($jam_scan);

            // --- Cek duplikat: SAMA persis = PIN + tanggal + jam_scan identik ---
            // (bukan per sesi — tap berbeda waktu di sesi yang sama = baris baru)
            $existing = $this->db->get_where('presensi_harian', [
                'tipe_user' => $tipe_user,
                'id_user'   => $id_user,
                'tanggal'   => $date,
                'jam_scan'  => $jam_scan
            ])->row();

            if ($existing) {
                // OVERWRITE: Tap yang benar-benar identik (jam sama persis)
                $this->db->where('id_presensi', $existing->id_presensi);
                $this->db->update('presensi_harian', [
                    'pin'  => $pin,
                    'sesi' => $sesi
                ]);
                $overwrite_count++;
            } else {
                // INSERT: Tap baru (jam berbeda = data baru, simpan semua)
                $this->db->insert('presensi_harian', [
                    'tipe_user' => $tipe_user,
                    'id_user'   => $id_user,
                    'pin'       => $pin,
                    'tanggal'   => $date,
                    'jam_scan'  => $jam_scan,
                    'sesi'      => $sesi
                ]);
                $inserted_count++;
            }
        }

        echo json_encode([
            'status'    => 'success',
            'message'   => 'Sync scanlog completed.',
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

        // Jika gagal dan attempts < 3, kembalikan ke pending agar dicoba lagi
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
        $token = $this->input->get('token');
        $this->validate_token($token);

        $this->db->select('pin_fingerprint as pin, nama_siswa as nama');
        $this->db->from('siswa');
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->where('pin_fingerprint >', 0);
        $this->db->order_by('nama_siswa', 'ASC');
        $students = $this->db->get()->result();

        echo json_encode([
            'status'   => 'success',
            'students' => $students
        ]);
    }
}
?>
