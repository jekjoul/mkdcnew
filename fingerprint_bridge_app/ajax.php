<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/lib/BridgeStorage.php';
require_once __DIR__ . '/lib/EasyLinkSDK.php';

// Proteksi Session
if (!isset($_SESSION['fp_bridge_admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Silakan login.']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'test_connection':
        $machine = BridgeStorage::getMachine();
        $ip   = $_POST['ip_address'] ?? $machine['ip_address'] ?? '192.168.1.201';
        $port = (int)($_POST['port'] ?? $machine['port'] ?? 4370);

        $res = EasyLinkSDK::ping($ip, $port);
        echo json_encode($res);
        break;

    case 'get_device_info':
        $machine = BridgeStorage::getMachine();
        $ip      = $_POST['ip_address'] ?? $machine['ip_address'] ?? '192.168.1.201';
        $port    = (int)($_POST['port'] ?? $machine['port'] ?? 4370);
        $sn      = $_POST['serial_number'] ?? $machine['serial_number'] ?? '';
        $comm_key= $_POST['comm_key'] ?? $machine['comm_key'] ?? '0';

        $res = EasyLinkSDK::getDeviceInfo($ip, $port, $sn, $comm_key);
        echo json_encode($res);
        break;

    case 'ajaxFetchLog':
        $machine = BridgeStorage::getMachine();
        $sn      = $_POST['sn'] ?? $machine['serial_number'] ?? '';
        $port    = (int)($_POST['port'] ?? $machine['port'] ?? 4370);
        $ip      = $_POST['ip'] ?? $machine['ip_address'] ?? '127.0.0.1';
        $mode    = $_POST['mode'] ?? 'new';

        $limit     = "500";
        $endpoint  = ($mode === 'all') ? "scanlog/all/paging" : "scanlog/new";
        $parameter = "sn=" . urlencode($sn) . "&limit=" . $limit;

        $res = EasyLinkSDK::webservice($ip, $port, $endpoint, $parameter, 10);
        echo $res['raw'] ?? json_encode(['Result' => false, 'Data' => []]);
        break;

    case 'ajaxUploadBatch':
        $data_json = $_POST['data'] ?? '';
        $settings  = BridgeStorage::getSettings();
        $api_url   = BridgeStorage::getActiveEndpointUrl();
        $api_token = $settings['api_token'] ?? '';

        $decoded  = json_decode($data_json, true);
        $new_logs = [];
        if (isset($decoded['Data']) && is_array($decoded['Data'])) {
            foreach ($decoded['Data'] as $entry) {
                $new_logs[] = [
                    'pin'       => intval($entry['pin'] ?? 0),
                    'scan_date' => $entry['tgl_scanlog'] ?? date('Y-m-d H:i:s'),
                    'sn'        => $entry['sn_device'] ?? ''
                ];
            }
        }

        $payload = json_encode([
            'token' => $api_token,
            'logs'  => $new_logs
        ]);

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            $msg = "Gagal upload batch ke server API ({$api_url}): " . $err;
            BridgeStorage::addSyncLog(['type' => 'push_presensi', 'status' => 'failed', 'message' => $msg]);
            echo json_encode(['status' => 'error', 'alert' => $msg]);
            exit;
        }

        $api_res = json_decode($response, true);
        $inserted = $api_res['inserted'] ?? count($new_logs);
        $alert_msg = "Data batch (" . count($new_logs) . " logs) tersimpan di server";
        BridgeStorage::addSyncLog(['type' => 'push_presensi', 'status' => 'success', 'message' => $alert_msg]);

        echo json_encode([
            'status' => 'success',
            'alert'  => $alert_msg,
            'count'  => count($new_logs)
        ]);
        break;

    case 'fetch_machine_scanlogs':
        $machine     = BridgeStorage::getMachine();
        $machine_res = EasyLinkSDK::getScanlog($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key']);
        echo json_encode([
            'status'  => $machine_res['status'] ? 'success' : 'error',
            'message' => $machine_res['message'],
            'logs'    => $machine_res['logs'] ?? []
        ]);
        break;

    case 'push_presensi':
        $machine  = BridgeStorage::getMachine();
        $settings = BridgeStorage::getSettings();
        $api_url  = BridgeStorage::getActiveEndpointUrl();
        $api_token= $settings['api_token'] ?? '';

        // 1. Membaca log dari mesin EasyLink
        $machine_res = EasyLinkSDK::getScanlog($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key']);

        if (!$machine_res['status']) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal membaca mesin: ' . $machine_res['message']
            ]);
            exit;
        }

        $logs = $machine_res['logs'];
        if (empty($logs)) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Koneksi mesin sukses. Tidak ada data scanlog baru di mesin.',
                'count'   => 0
            ]);
            exit;
        }

        // 2. Kirim payload logs ke Endpoint API Web
        $payload = json_encode([
            'token' => $api_token,
            'logs'  => $logs
        ]);

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            $msg = "Gagal menghubungi Server API ({$api_url}): " . $err;
            BridgeStorage::addSyncLog(['type' => 'push_presensi', 'status' => 'failed', 'message' => $msg]);
            echo json_encode(['status' => 'error', 'message' => $msg]);
            exit;
        }

        $api_res = json_decode($response, true);
        if ($api_res && isset($api_res['status'])) {
            $msg = "Sync Sukses! Total: " . count($logs) . " logs. Inserter: " . ($api_res['inserted'] ?? 0);
            BridgeStorage::addSyncLog(['type' => 'push_presensi', 'status' => 'success', 'message' => $msg]);
            echo json_encode([
                'status'   => 'success',
                'message'  => $msg,
                'response' => $api_res
            ]);
        } else {
            $msg = "Server merespons dengan HTTP {$httpCode}: " . substr($response, 0, 100);
            BridgeStorage::addSyncLog(['type' => 'push_presensi', 'status' => 'error', 'message' => $msg]);
            echo json_encode(['status' => 'error', 'message' => $msg, 'raw' => $response]);
        }
        break;

    case 'fetch_sync_diff':
        $machine  = BridgeStorage::getMachine();
        $settings = BridgeStorage::getSettings();

        // 1. Ambil data server API active_students
        $api_base = str_replace('/sync', '/active_students', BridgeStorage::getActiveEndpointUrl());
        $url      = $api_base . '?token=' . urlencode($settings['api_token']);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($ch);
        curl_close($ch);

        $server_students = [];
        if ($resp) {
            $json = json_decode($resp, true);
            if (isset($json['students']) && is_array($json['students'])) {
                $server_students = $json['students'];
            }
        }

        // 2. Ambil data mesin pengguna
        $m_res = EasyLinkSDK::getUsers($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key']);
        $machine_users = $m_res['users'] ?? [];

        // Formatting Map (PIN di mesin = NIPD di server, Nama server dipotong max 15 karakter)
        $server_map = [];
        foreach ($server_students as $s) {
            $nipd_pin = (int)($s['pin'] ?? $s['nipd'] ?? 0);
            if ($nipd_pin > 0) {
                // Potong nama server maksimal 15 karakter termasuk spasi
                $nama_15 = mb_substr(trim($s['nama'] ?? ''), 0, 15);
                $server_map[$nipd_pin] = $nama_15;
            }
        }

        $machine_map = [];
        foreach ($machine_users as $m) {
            $m_pin = (int)($m['pin'] ?? 0);
            if ($m_pin > 0) {
                $machine_map[$m_pin] = trim($m['nama'] ?? '');
            }
        }

        // Processing 3 Diff Categories:
        $machine_only  = []; // Ada di Mesin, tidak ada di Server
        $server_only   = []; // Ada di Server, tidak ada di Mesin
        $name_mismatch = []; // Ada di dua-duanya tapi nama beda

        // Check Machine Users
        foreach ($machine_map as $pin => $m_nama) {
            if (!isset($server_map[$pin])) {
                $machine_only[] = [
                    'pin'   => $pin,
                    'nama'  => $m_nama,
                    'opt'   => 'hapus_mesin'
                ];
            } else {
                $s_nama_15 = $server_map[$pin];
                if (strcasecmp($m_nama, $s_nama_15) !== 0) {
                    $name_mismatch[] = [
                        'pin'         => $pin,
                        'nama_mesin'  => $m_nama,
                        'nama_server' => $s_nama_15,
                        'opt'         => 'ubah_nama_mesin'
                    ];
                }
            }
        }

        // Check Server Users
        foreach ($server_map as $pin => $s_nama_15) {
            if (!isset($machine_map[$pin])) {
                $server_only[] = [
                    'pin'  => $pin,
                    'nama' => $s_nama_15,
                    'opt'  => 'tambah_mesin'
                ];
            }
        }

        echo json_encode([
            'status'         => 'success',
            'total_server'   => count($server_map),
            'total_machine'  => count($machine_map),
            'machine_only'   => $machine_only,
            'server_only'    => $server_only,
            'name_mismatch'  => $name_mismatch
        ]);
        break;

    case 'exec_sync_single':
        $machine = BridgeStorage::getMachine();
        $type    = $_POST['type'] ?? '';
        $pin     = (int)($_POST['pin'] ?? 0);
        // Potong nama maksimal 15 karakter termasuk spasi
        $nama    = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);

        if ($pin <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'NIPD / PIN tidak valid.']);
            exit;
        }

        if ($type === 'hapus_mesin') {
            $res = EasyLinkSDK::deleteUser($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key'], $pin);
        } elseif ($type === 'tambah_mesin' || $type === 'ubah_nama_mesin') {
            $res = EasyLinkSDK::setUser($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key'], $pin, $nama);
        } else {
            $res = ['status' => false, 'message' => 'Tipe sinkronisasi tidak dikenal.'];
        }

        BridgeStorage::addSyncLog([
            'type'    => 'user_sync',
            'action'  => $type,
            'pin'     => $pin,
            'nama'    => $nama,
            'status'  => $res['status'] ? 'success' : 'failed'
        ]);

        echo json_encode([
            'status'  => $res['status'] ? 'success' : 'error',
            'message' => $res['message']
        ]);
        break;

    case 'change_password':
        $new_pass = trim($_POST['new_password'] ?? '');
        $new_name = trim($_POST['admin_name'] ?? '');

        if (strlen($new_pass) < 4) {
            echo json_encode(['status' => 'error', 'message' => 'Password minimal 4 karakter!']);
            exit;
        }

        BridgeStorage::updatePassword($new_pass, $new_name);
        echo json_encode(['status' => 'success', 'message' => 'Password & Profil Admin Standalone berhasil diperbarui.']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal.']);
        break;
}
