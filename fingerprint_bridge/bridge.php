<?php
/**
 * MKDC Fingerprint Bridge Service (PHP Version)
 * Berjalan tanpa bergantung pada Node.js, memanfaatkan runtime PHP bawaan XAMPP.
 */

date_default_timezone_set('Asia/Jakarta');

$configPath = __DIR__ . '/config.json';

// Baca konfigurasi
if (!file_exists($configPath)) {
    log_msg("ERROR", "File config.json tidak ditemukan!");
    exit(1);
}

$config = json_decode(file_get_contents($configPath), true);
if (!$config) {
    log_msg("ERROR", "Format config.json tidak valid!");
    exit(1);
}

$device_ip = $config['device_ip'] ?? '192.168.1.100';
$device_port = $config['device_port'] ?? 80;
$device_sn = $config['device_sn'] ?? '';
$server_url = rtrim($config['server_url'] ?? '', '/');
$api_token = $config['api_token'] ?? '';
$sync_interval = intval($config['sync_interval_seconds'] ?? 30);
$delete_log_after_sync = $config['delete_device_log_after_sync'] ?? false;

// Helper untuk cetak log ke console
function log_msg($type, $message) {
    $time = date('Y-m-d H:i:s');
    $color = "\033[37m"; // White
    if ($type === 'SUCCESS') $color = "\033[32m"; // Green
    if ($type === 'ERROR') $color = "\033[31m"; // Red
    if ($type === 'WARN') $color = "\033[33m"; // Yellow
    echo "[{$time}] [{$type}] {$color}{$message}\033[0m\n";
}

// Helper untuk melakukan HTTP request (menggunakan stream context bawaan PHP)
function http_request($url, $method = 'GET', $data = null, $headers = []) {
    $curl = curl_init();
    
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge([
            "cache-control: no-cache",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        ], $headers)
    ];

    if ($method === 'POST' && $data) {
        $opts[CURLOPT_POSTFIELDS] = $data;
    }

    curl_setopt_array($curl, $opts);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($err) {
        throw new Exception("cURL Error: " . $err);
    }

    if ($status_code < 200 || $status_code >= 300) {
        throw new Exception("HTTP Error {$status_code}: {$response}");
    }

    return $response;
}

// 1. Sinkronisasi Log Presensi Baru
function syncScanlog() {
    global $device_ip, $device_port, $device_sn, $server_url, $api_token, $delete_log_after_sync;
    log_msg("INFO", "Mulai sinkronisasi scanlog dari mesin...");

    $deviceUrl = "http://{$device_ip}:{$device_port}/scanlog/new";
    $devicePostData = http_build_query(['sn' => $device_sn]);

    try {
        // Ambil data log dari mesin
        $res = http_request($deviceUrl, 'POST', $devicePostData, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $content = json_decode($res, true);
        if (!isset($content['Data'])) {
            log_msg("WARN", "Respons dari mesin tidak valid atau kosong.");
            return;
        }

        $logs = $content['Data'];
        if (empty($logs)) {
            log_msg("INFO", "Tidak ada data scanlog baru di mesin.");
            return;
        }

        log_msg("INFO", "Mendapatkan " . count($logs) . " data scanlog baru. Mengirim ke server online...");

        // Petakan log mesin ke format server
        $serverLogs = [];
        foreach ($logs as $l) {
            $serverLogs[] = [
                'pin' => $l['PIN'],
                'scan_date' => $l['ScanDate'],
                'sn' => $l['SN']
            ];
        }

        // Kirim ke server online
        $serverUrlSync = "{$server_url}/api/presensi/sync";
        $serverPayload = json_encode([
            'token' => $api_token,
            'logs' => $serverLogs
        ]);

        $serverRes = http_request($serverUrlSync, 'POST', $serverPayload, [
            'Content-Type: application/json'
        ]);

        $serverResponse = json_decode($serverRes, true);
        if (($serverResponse['status'] ?? '') === 'success') {
            log_msg("SUCCESS", "Berhasil mengirimkan data presensi! Terproses: {$serverResponse['processed']}, Diabaikan: {$serverResponse['ignored']}");

            // Hapus log di mesin jika dikonfigurasi aktif
            if ($delete_log_after_sync) {
                log_msg("INFO", "Menghapus log yang tersinkron di mesin...");
                $deleteUrl = "http://{$device_ip}:{$device_port}/scanlog/del";
                http_request($deleteUrl, 'POST', $devicePostData, [
                    'Content-Type: application/x-www-form-urlencoded'
                ]);
                log_msg("SUCCESS", "Log berhasil dibersihkan dari mesin.");
            }
        } else {
            log_msg("ERROR", "Gagal menyimpan data di server online: " . ($serverResponse['message'] ?? 'Unknown Error'));
        }

    } catch (Exception $e) {
        log_msg("ERROR", "Error pada sinkronisasi scanlog: " . $e->getMessage());
    }
}

// 2. Sinkronisasi Tugas Pending
function syncTasks() {
    global $device_ip, $device_port, $device_sn, $server_url, $api_token;
    log_msg("INFO", "Memeriksa tugas sinkronisasi dari server online...");

    try {
        $getTasksUrl = "{$server_url}/api/presensi/pending_tasks?token=" . urlencode($api_token);
        $serverRes = http_request($getTasksUrl, 'GET');

        $serverResponse = json_decode($serverRes, true);
        if (($serverResponse['status'] ?? '') !== 'success') {
            log_msg("ERROR", "Gagal mengambil tugas dari server: " . ($serverResponse['message'] ?? 'Unknown Error'));
            return;
        }

        $tasks = $serverResponse['tasks'] ?? [];
        if (empty($tasks)) {
            log_msg("INFO", "Tidak ada tugas sinkronisasi pending.");
            return;
        }

        log_msg("INFO", "Mendapatkan " . count($tasks) . " tugas pending. Mulai memproses...");

        foreach ($tasks as $task) {
            $taskId = $task['id'];
            $action = $task['action'];
            $pin = $task['pin'];
            $nama = $task['nama'];

            $success = false;
            $errorMessage = null;

            try {
                if ($action === 'SET_USER') {
                    // Daftarkan/update user ke mesin
                    $setUrl = "http://{$device_ip}:{$device_port}/user/set";
                    $setPayload = http_build_query([
                        'sn' => $device_sn,
                        'pin' => $pin,
                        'nama' => $nama,
                        'pwd' => '',
                        'rfid' => '',
                        'priv' => 0,
                        'tmp' => '[]'
                    ]);

                    log_msg("INFO", "Menjalankan SET_USER ke mesin (PIN: {$pin}, Nama: {$nama})...");
                    $res = http_request($setUrl, 'POST', $setPayload, [
                        'Content-Type: application/x-www-form-urlencoded'
                    ]);

                    $content = json_decode($res, true);
                    if (($content['Result'] ?? false) === true) {
                        $success = true;
                        log_msg("SUCCESS", "Berhasil mendaftarkan user {$nama} (PIN: {$pin}) ke mesin.");
                    } else {
                        throw new Exception($res);
                    }

                } elseif ($action === 'DEL_USER') {
                    // Hapus user dari mesin
                    $delUrl = "http://{$device_ip}:{$device_port}/user/del";
                    $delPayload = http_build_query([
                        'sn' => $device_sn,
                        'pin' => $pin
                    ]);

                    log_msg("INFO", "Menjalankan DEL_USER ke mesin (PIN: {$pin})...");
                    $res = http_request($delUrl, 'POST', $delPayload, [
                        'Content-Type: application/x-www-form-urlencoded'
                    ]);

                    $content = json_decode($res, true);
                    if (($content['Result'] ?? false) === true) {
                        $success = true;
                        log_msg("SUCCESS", "Berhasil menghapus user PIN {$pin} dari mesin.");
                    } else {
                        throw new Exception($res);
                    }
                }
            } catch (Exception $err) {
                $errorMessage = $err->getMessage();
                log_msg("ERROR", "Gagal memproses tugas {$action} (PIN: {$pin}): {$errorMessage}");
            }

            // Kirim laporan balik ke server online
            try {
                $reportUrl = "{$server_url}/api/presensi/task_result";
                $reportPayload = json_encode([
                    'token' => $api_token,
                    'task_id' => $taskId,
                    'status' => $success ? 'success' : 'failed',
                    'error_message' => $errorMessage
                ]);

                http_request($reportUrl, 'POST', $reportPayload, [
                    'Content-Type: application/json'
                ]);
                log_msg("INFO", "Laporan tugas {$taskId} berhasil dikirim ke server.");
            } catch (Exception $err) {
                log_msg("ERROR", "Gagal mengirim laporan tugas {$taskId} ke server: " . $err->getMessage());
            }
        }

    } catch (Exception $e) {
        log_msg("ERROR", "Error pada sinkronisasi tugas: " . $e->getMessage());
    }
}

// Loop Scheduler Utama
echo "=====================================================\n";
echo "    MKDC FINGERPRINT BRIDGE RUNNING (PHP VERSION)    \n";
echo "    IP Mesin    : {$device_ip}:{$device_port}\n";
echo "    SN Mesin    : {$device_sn}\n";
echo "    URL Server  : {$server_url}\n";
echo "    Interval    : {$sync_interval} detik\n";
echo "=====================================================\n";

while (true) {
    syncScanlog();
    syncTasks();
    sleep($sync_interval);
}
