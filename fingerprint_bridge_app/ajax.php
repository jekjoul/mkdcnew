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
        // LANGKAH 1: Get all user dulu dari mesin dengan paging limit 1
        $m_res         = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
        $machine_users = $m_res['users'] ?? [];

        // LANGKAH 2: Ambil data siswa dari server web API
        $api_url = "http://localhost/mkdcnew/api/presensi/sync";
        $ch      = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
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

        // LANGKAH 3: Bandingkan datanya berdasarkan PIN di mesin sama dengan NIPD di server
        $machine_map = [];
        foreach ($machine_users as $m) {
            $m_pin = intval($m['pin'] ?? 0);
            if ($m_pin > 0) {
                $machine_map[$m_pin] = trim($m['nama'] ?? '');
            }
        }

        $server_map = [];
        foreach ($server_students as $s) {
            $nipd_pin = intval($s['nipd'] ?? $s['pin'] ?? 0);
            $s_nama   = trim($s['nama_siswa'] ?? $s['nama'] ?? $s['name'] ?? '');
            if ($nipd_pin > 0 && !empty($s_nama)) {
                // Ambil maksimal 15 karakter TANPA menambah spasi jika kurang dari 15 karakter
                $nama_15 = trim(mb_substr($s_nama, 0, 15));
                $server_map[$nipd_pin] = $nama_15;
            }
        }

        $matched_data  = [];
        $machine_only  = [];
        $server_only   = [];
        $name_mismatch = [];

        // Check user mesin vs server
        foreach ($machine_map as $pin => $m_nama) {
            $m_nama_clean = trim($m_nama);
            if (!isset($server_map[$pin])) {
                $machine_only[] = ['pin' => $pin, 'nama' => $m_nama_clean, 'opt' => 'hapus_mesin'];
            } else {
                $s_nama_15 = $server_map[$pin];
                if (strcasecmp($m_nama_clean, $s_nama_15) === 0) {
                    $matched_data[] = ['pin' => $pin, 'nama' => $m_nama_clean, 'nama_server' => $s_nama_15];
                } else {
                    $name_mismatch[] = ['pin' => $pin, 'nama_mesin' => $m_nama_clean, 'nama_server' => $s_nama_15, 'opt' => 'ubah_nama_mesin'];
                }
            }
        }

        // Check siswa server vs mesin
        foreach ($server_map as $pin => $s_nama_15) {
            if (!isset($machine_map[$pin])) {
                $server_only[] = ['pin' => $pin, 'nama' => $s_nama_15, 'opt' => 'tambah_mesin'];
            }
        }

        echo json_encode([
            'status'         => 'success',
            'total_server'   => count($server_map),
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

    default:
        echo json_encode(['status' => false, 'message' => 'Action tidak valid.']);
        break;
}
