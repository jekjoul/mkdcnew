<?php
header('Content-Type: application/json');
set_time_limit(90);

require_once __DIR__ . '/koneksidb.php';
require_once __DIR__ . '/lib/EasyLinkSDK.php';

$action  = $_REQUEST['action'] ?? '';
$dev_cfg = getActiveDeviceConfig($conn);

switch ($action) {
    case 'test_connection':
        $ip   = $_POST['ip_address'] ?? $dev_cfg['server_IP'];
        $port = intval($_POST['port'] ?? $dev_cfg['server_port']);
        $res  = EasyLinkSDK::ping($ip, $port, 3);
        echo json_encode($res);
        break;

    case 'get_device_info':
        $ip   = $_POST['ip_address'] ?? $dev_cfg['server_IP'];
        $port = intval($_POST['port'] ?? $dev_cfg['server_port']);
        $sn   = $_POST['serial_number'] ?? $dev_cfg['device_sn'];
        $res  = EasyLinkSDK::getDeviceInfo($ip, $port, $sn);
        echo json_encode($res);
        break;

    case 'get_all_user':
    case 'download_users':
        $res = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
        echo json_encode($res);
        break;

    case 'send_machine_users_to_server':
        $res_users = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
        $m_users   = $res_users['users'] ?? [];

        if (empty($m_users)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal mengambil data user dari mesin atau data di mesin kosong: ' . ($res_users['message'] ?? '')
            ]);
            break;
        }

        $active_key = $dev_cfg['active_api_key'] ?? $dev_cfg['active_key'] ?? 'MKDC_FINGERPRINT_SECRET_KEY_2026';
        $parsed_url = parse_url($dev_cfg['active_api'] ?? '');
        $scheme     = $parsed_url['scheme'] ?? 'http';
        $host       = $parsed_url['host'] ?? 'localhost';
        $port_str   = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $path       = $parsed_url['path'] ?? '/mkdc_new_draft/api/presensi';
        $base_path  = preg_replace('#/(active_students|sync|active_ptk|receive_machine_users|pending_tasks|task_result).*$#', '', $path);
        $api_url    = "{$scheme}://{$host}{$port_str}" . rtrim($base_path, '/') . '/receive_machine_users';

        $payload = json_encode([
            'token' => $active_key,
            'users' => array_values($m_users)
        ]);

        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal mengirim data ke server MKDC: ' . $err
            ]);
            break;
        }

        $res_json = json_decode($resp, true);
        if ($res_json && isset($res_json['status']) && $res_json['status'] === 'success') {
            echo json_encode([
                'status'  => true,
                'message' => $res_json['message'] ?? 'Berhasil mengirim data user mesin ke server MKDC.'
            ]);
        } else {
            echo json_encode([
                'status'  => false,
                'message' => 'Respon dari server MKDC: ' . ($res_json['message'] ?? $resp)
            ]);
        }
        break;

    case 'set_user':
        $pin  = intval($_POST['pin'] ?? 0);
        $nama = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);
        $pwd  = trim($_POST['pwd'] ?? '');
        $rfid = trim($_POST['rfid'] ?? '');
        $priv = intval($_POST['privilege'] ?? 0);

        if ($pin <= 0 || empty($nama)) {
            echo json_encode(['status' => false, 'message' => 'PIN dan Nama wajib diisi.']);
            exit;
        }

        $res = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama, $pwd, $rfid, $priv);
        echo json_encode($res);
        break;

    case 'upload_all_users':
        $users_json = $_POST['users_data'] ?? '[]';
        $users      = json_decode($users_json, true);
        $uploaded   = 0;

        if (is_array($users) && !empty($users)) {
            foreach ($users as $u) {
                $pin  = intval($u['pin'] ?? 0);
                $nama = mb_substr(trim($u['nama'] ?? ''), 0, 15);
                if ($pin > 0 && !empty($nama)) {
                    $r = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama, $u['pwd'] ?? '', $u['rfid'] ?? '', intval($u['privilege'] ?? 0));
                    if ($r['status']) $uploaded++;
                }
            }
        }

        echo json_encode([
            'status'  => true,
            'message' => "Berhasil mengunggah {$uploaded} pengguna ke dalam mesin."
        ]);
        break;

    case 'delete_user':
        $pin = intval($_POST['pin'] ?? 0);
        $res = EasyLinkSDK::deleteUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin);
        echo json_encode($res);
        break;

    case 'delete_all_users':
        $res = EasyLinkSDK::deleteAllUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        echo json_encode($res);
        break;

    case 'delete_admin':
        $res = EasyLinkSDK::deleteAdmin($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        echo json_encode($res);
        break;

    case 'download_scanlog':
        $mode = $_POST['mode'] ?? 'all';
        $res  = EasyLinkSDK::getScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], 500, $mode);
        echo json_encode($res);
        break;

    case 'delete_device_scanlog':
        $res = EasyLinkSDK::deleteScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        echo json_encode($res);
        break;

    case 'save_device_config':
        $ip   = trim($_POST['ip_address'] ?? '10.10.10.10');
        $port = trim($_POST['port'] ?? '8080');
        $sn   = trim($_POST['serial_number'] ?? '616202024171114');

        saveActiveDeviceConfig($ip, $port, $sn);

        echo json_encode([
            'status'  => true,
            'message' => "Konfigurasi perangkat berhasil diperbarui (IP: {$ip}, Port: {$port}, SN: {$sn})."
        ]);
        break;

    case 'exec_maintenance':
        $type = $_POST['type'] ?? '';
        if ($type === 'sync_time') {
            $res = EasyLinkSDK::setTime($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        } elseif ($type === 'del_admin') {
            $res = EasyLinkSDK::deleteAdmin($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        } elseif ($type === 'del_log') {
            $res = EasyLinkSDK::deleteScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        } elseif ($type === 'init_device') {
            $res = EasyLinkSDK::initDevice($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
        } else {
            $res = ['status' => false, 'message' => 'Perintah tidak dikenal.'];
        }
        echo json_encode($res);
        break;

    case 'fetch_sync_diff':
        // LANGKAH 1: Get all user dari mesin dengan paging limit 1
        $m_res         = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
        $machine_users = $m_res['users'] ?? [];

        $active_key = $dev_cfg['active_api_key'] ?? $dev_cfg['active_key'] ?? 'MKDC_FINGERPRINT_SECRET_KEY_2026';
        $active_api = $dev_cfg['active_api'] ?? '';

        if (strpos($active_api, 'token=') === false) {
            $active_api .= (strpos($active_api, '?') === false ? '?' : '&') . 'token=' . urlencode($active_key);
        }

        // LANGKAH 2: Ambil data Siswa Aktif (PIN = NIPD) dari Server Web API
        $students_url = $active_api;
        if (strpos($students_url, 'active_students') === false) {
            $parsed = parse_url($active_api);
            $scheme = $parsed['scheme'] ?? 'http';
            $host   = $parsed['host'] ?? 'localhost';
            $port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            $path   = preg_replace('#/(sync|active_ptk|receive_machine_users).*$#', '', $parsed['path'] ?? '');
            $students_url = "{$scheme}://{$host}{$port}" . rtrim($path, '/') . '/active_students?token=' . urlencode($active_key);
        }

        $ch = curl_init($students_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $server_users = [];
        if ($resp) {
            $json = json_decode($resp, true);
            $raw_st = $json['students'] ?? $json['data'] ?? [];
            if (is_array($raw_st)) {
                foreach ($raw_st as $st) {
                    $pin  = trim((string)($st['nipd'] ?? $st['pin'] ?? ''));
                    $nama = trim((string)($st['nama_siswa'] ?? $st['nama'] ?? ''));
                    if (!empty($pin) && !empty($nama)) {
                        $server_users[$pin] = trim(mb_substr($nama, 0, 15));
                    }
                }
            }
        }

        // LANGKAH 3: Ambil data PTK / Guru Aktif (PIN = NIY) dari Server Web API
        $ptk_url = str_replace('active_students', 'active_ptk', $students_url);
        $ch2 = curl_init($ptk_url);
        curl_setopt_array($ch2, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $resp2 = curl_exec($ch2);
        curl_close($ch2);

        if ($resp2) {
            $json2 = json_decode($resp2, true);
            $raw_ptk = $json2['ptk'] ?? $json2['data'] ?? [];
            if (is_array($raw_ptk)) {
                foreach ($raw_ptk as $ptk) {
                    $pin  = trim((string)($ptk['niy'] ?? $ptk['pin'] ?? ''));
                    $nama = trim((string)($ptk['nama_ptk'] ?? $ptk['nama'] ?? ''));
                    if (!empty($pin) && !empty($nama)) {
                        $server_users[$pin] = trim(mb_substr($nama, 0, 15));
                    }
                }
            }
        }

        // LANGKAH 4: Bandingkan datanya berdasarkan PIN di mesin vs PIN di server (NIPD & NIY)
        $machine_map = [];
        foreach ($machine_users as $m) {
            $raw_m_pin   = trim((string)($m['pin'] ?? ''));
            $clean_m_pin = ltrim($raw_m_pin, '0');
            if ($clean_m_pin === '') $clean_m_pin = '0';

            if (!empty($raw_m_pin) && $raw_m_pin !== '0') {
                $machine_map[$raw_m_pin]   = trim($m['nama'] ?? '');
                $machine_map[$clean_m_pin] = trim($m['nama'] ?? '');
            }
        }

        $matched_data   = [];
        $machine_only   = [];
        $server_only    = [];
        $name_mismatch  = [];
        $processed_pins = [];

        // Check user mesin vs server
        foreach ($machine_users as $m) {
            $raw_pin   = trim((string)($m['pin'] ?? ''));
            $clean_pin = ltrim($raw_pin, '0');
            if ($clean_pin === '') $clean_pin = '0';

            if (empty($raw_pin) || $raw_pin === '0' || isset($processed_pins[$raw_pin])) continue;
            $processed_pins[$raw_pin]   = true;
            $processed_pins[$clean_pin] = true;

            $m_nama_clean = trim($m['nama'] ?? '');
            $s_nama_15    = $server_users[$raw_pin] ?? $server_users[$clean_pin] ?? null;

            if ($s_nama_15 === null) {
                $machine_only[] = ['pin' => $raw_pin, 'nama' => $m_nama_clean, 'opt' => 'hapus_mesin'];
            } else {
                if (strcasecmp($m_nama_clean, $s_nama_15) === 0) {
                    $matched_data[] = ['pin' => $raw_pin, 'nama' => $m_nama_clean, 'nama_server' => $s_nama_15];
                } else {
                    $name_mismatch[] = ['pin' => $raw_pin, 'nama_mesin' => $m_nama_clean, 'nama_server' => $s_nama_15, 'opt' => 'ubah_nama_mesin'];
                }
            }
        }

        // Check siswa & PTK server vs mesin
        foreach ($server_users as $raw_pin => $s_nama_15) {
            $clean_pin = ltrim($raw_pin, '0');
            if ($clean_pin === '') $clean_pin = '0';

            if (isset($processed_pins[$raw_pin]) || isset($processed_pins[$clean_pin])) continue;
            $processed_pins[$raw_pin]   = true;
            $processed_pins[$clean_pin] = true;

            if (!isset($machine_map[$raw_pin]) && !isset($machine_map[$clean_pin])) {
                $server_only[] = ['pin' => $raw_pin, 'nama' => $s_nama_15, 'opt' => 'tambah_mesin'];
            }
        }

        echo json_encode([
            'status'         => 'success',
            'total_server'   => count($server_users),
            'total_machine'  => count($machine_map),
            'matched_data'   => $matched_data,
            'name_mismatch'  => $name_mismatch,
            'server_only'    => $server_only,
            'machine_only'   => $machine_only,
            'machine_status' => $m_res['status'] ?? false,
            'machine_msg'    => $m_res['message'] ?? ''
        ]);
        break;

    case 'exec_sync_single':
        $type = $_POST['type'] ?? '';
        $pin  = intval($_POST['pin'] ?? 0);
        $nama = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);

        if ($type === 'tambah_mesin' || $type === 'ubah_nama_mesin') {
            $res = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama);
        } elseif ($type === 'hapus_mesin') {
            $res = EasyLinkSDK::deleteUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin);
        } else {
            $res = ['status' => false, 'message' => 'Tipe aksi tidak valid.'];
        }
        echo json_encode($res);
        break;

    case 'sync_batch_chunk':
        require_once __DIR__ . '/lib/BridgeDB.php';
        $chunk_raw = $_POST['chunk_logs'] ?? '[]';
        $chunk     = json_decode($chunk_raw, true);

        if (!is_array($chunk) || empty($chunk)) {
            echo json_encode(['status' => 'error', 'message' => 'Chunk payload kosong atau tidak valid.']);
            break;
        }

        $sync_url = $dev_cfg['active_sync_api'];
        $api_key  = $dev_cfg['active_api_key'];

        $payload_data = [
            'token' => $api_key,
            'logs'  => $chunk
        ];

        // Daftar target URL pengiriman (Konfigurasi Aktif + Dual-Sync Local Dev)
        $target_urls = [$sync_url];
        $local_sync_url = 'http://localhost/mkdc_new_draft/api/presensi/sync';
        if ($sync_url !== $local_sync_url) {
            $target_urls[] = $local_sync_url;
        }

        $success_res = null;
        $last_err    = '';

        foreach ($target_urls as $t_url) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $t_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode($payload_data),
                CURLOPT_HTTPHEADER     => [
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response  = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err       = curl_error($ch);
            curl_close($ch);

            if (!$err && $http_code === 200) {
                $json_res = json_decode($response, true);
                if (is_array($json_res) && (isset($json_res['status']) && strtolower($json_res['status']) === 'success')) {
                    $success_res = $json_res;
                }
            } else {
                $last_err = $err ? "cURL Error: {$err}" : "HTTP {$http_code}";
            }
        }

        if ($success_res !== null) {
            echo json_encode($success_res);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Gagal mengirim batch: {$last_err}"]);
        }
        break;

    case 'clear_sent_logs_db':
        require_once __DIR__ . '/lib/BridgeDB.php';
        $deleted = BridgeDB::clearAllLogs();
        echo json_encode(['status' => 'success', 'deleted' => $deleted]);
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Action tidak valid.']);
        break;
}
