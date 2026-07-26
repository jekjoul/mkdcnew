<?php
/**
 * Configuration & Helper Engine - Direct Machine WebService (Tanpa MySQL Database)
 * Aplikasi ini bekerja secara langsung terhubung ke Mesin EasyLink via HTTP WebService SDK.
 */

set_time_limit(0);
ini_set('max_execution_time', '0');
ini_set("display_errors", 0);
error_reporting(0);

$conn = null; // Tidak menggunakan koneksi MySQL database

/**
 * Membaca Konfigurasi Mesin Aktif dari File Konfigurasi JSON
 */
function getActiveDeviceConfig($conn = null)
{
    $default_key      = 'MKDC_FINGERPRINT_SECRET_KEY_2026';
    $default_dev_api  = 'http://localhost/mkdc_new_draft/api/presensi/active_students?token=' . $default_key;
    $default_prod_api = 'https://presensi.sekolah.sch.id/api/presensi/active_students?token=' . $default_key;

    $json_file = __DIR__ . '/data/config.json';
    if (file_exists($json_file)) {
        $json = json_decode(file_get_contents($json_file), true);
        if (is_array($json) && !empty($json['server_IP'])) {
            $api_env      = trim($json['api_env'] ?? 'dev');
            $api_dev_url  = trim($json['api_dev_url'] ?? $default_dev_api);
            $api_prod_url = trim($json['api_prod_url'] ?? $default_prod_api);
            $api_dev_key  = trim($json['api_dev_key'] ?? $default_key);
            $api_prod_key = trim($json['api_prod_key'] ?? $default_key);

            $active_api   = ($api_env === 'prod') ? $api_prod_url : $api_dev_url;
            $active_key   = ($api_env === 'prod') ? $api_prod_key : $api_dev_key;

            // Build base sync URL (POST /api/presensi/sync)
            $parsed_url      = parse_url($active_api);
            $scheme          = $parsed_url['scheme'] ?? 'http';
            $host            = $parsed_url['host'] ?? 'localhost';
            $port_str        = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
            $path            = $parsed_url['path'] ?? '';
            $base_path       = preg_replace('#/(active_students|sync).*$#', '', $path);
            $active_sync_api = "{$scheme}://{$host}{$port_str}" . rtrim($base_path, '/') . '/sync';

            // Pastikan parameter token terpasang jika belum ada
            if (strpos($active_api, 'token=') === false) {
                $active_api .= (strpos($active_api, '?') === false ? '?' : '&') . 'token=' . urlencode($active_key);
            }

            return [
                'server_IP'       => trim($json['server_IP']),
                'server_port'     => trim($json['server_port'] ?? '8080'),
                'device_sn'       => trim($json['device_sn'] ?? '616202024171114'),
                'api_env'         => $api_env,
                'api_dev_url'     => $api_dev_url,
                'api_prod_url'    => $api_prod_url,
                'api_dev_key'     => $api_dev_key,
                'api_prod_key'    => $api_prod_key,
                'active_api'      => $active_api,
                'active_api_key'  => $active_key,
                'active_sync_api' => $active_sync_api
            ];
        }
    }

    $json_file_alt = __DIR__ . '/data/bridge_db.json';
    if (file_exists($json_file_alt)) {
        $json = json_decode(file_get_contents($json_file_alt), true);
        if (isset($json['machine'])) {
            return [
                'server_IP'   => trim($json['machine']['ip_address'] ?? '10.10.10.10'),
                'server_port' => trim($json['machine']['port'] ?? '8080'),
                'device_sn'   => trim($json['machine']['serial_number'] ?? '616202024171114')
            ];
        }
    }

    $default_sync_api = 'http://localhost/mkdc_new_draft/api/presensi/sync';
    return [
        'server_IP'       => '10.10.10.10',
        'server_port'     => '8080',
        'device_sn'       => '616202024171114',
        'api_env'         => 'dev',
        'api_dev_url'     => $default_dev_api,
        'api_prod_url'    => $default_prod_api,
        'api_dev_key'     => $default_key,
        'api_prod_key'    => $default_key,
        'active_api'      => $default_dev_api,
        'active_api_key'  => $default_key,
        'active_sync_api' => $default_sync_api
    ];
}

/**
 * Menyimpan Konfigurasi Mesin Aktif & Endpoint API ke File JSON
 */
function saveActiveDeviceConfig($ip, $port, $sn, $api_env = 'dev', $api_dev_url = '', $api_prod_url = '', $api_dev_key = '', $api_prod_key = '')
{
    $data_dir = __DIR__ . '/data';
    if (!is_dir($data_dir)) {
        @mkdir($data_dir, 0777, true);
    }

    $default_key      = 'MKDC_FINGERPRINT_SECRET_KEY_2026';
    $default_dev_api  = 'http://localhost/mkdc_new_draft/api/presensi/active_students?token=' . $default_key;
    $default_prod_api = 'https://presensi.sekolah.sch.id/api/presensi/active_students?token=' . $default_key;

    $cfg = [
        'server_IP'    => trim($ip),
        'server_port'  => trim($port),
        'device_sn'    => trim($sn),
        'api_env'      => trim($api_env ?: 'dev'),
        'api_dev_url'  => trim($api_dev_url ?: $default_dev_api),
        'api_prod_url' => trim($api_prod_url ?: $default_prod_api),
        'api_dev_key'  => trim($api_dev_key ?: $default_key),
        'api_prod_key' => trim($api_prod_key ?: $default_key),
        'updated_at'   => date('Y-m-d H:i:s')
    ];

    $json_file = $data_dir . '/config.json';
    @file_put_contents($json_file, json_encode($cfg, JSON_PRETTY_PRINT));
    return true;
}

/**
 * Functions Helper WebService cURL untuk EasyLink SDK
 */
if (!function_exists('webservice')) {
    function webservice($port, $url, $parameter, $timeout = 10)
    {
        $curl = curl_init();
        $full_url = (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) ? $url : "http://" . ltrim($url, '/');
        
        curl_setopt_array($curl, [
            CURLOPT_PORT           => (int)$port,
            CURLOPT_URL            => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $parameter,
            CURLOPT_HTTPHEADER     => [
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "Error #: " . $err;
        }

        return $response;
    }
}

/**
 * Tester Koneksi Web API Server (Dev & Prod)
 */
if (!function_exists('testApiConnection')) {
    function testApiConnection($url)
    {
        if (empty($url)) {
            return [
                'status'    => false,
                'message'   => 'URL Web API Server tidak boleh kosong.',
                'http_code' => 0,
                'latency'   => 0
            ];
        }

        $start_time = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'EasyLinkSDK-Bridge/2.0'
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err       = curl_error($ch);
        curl_close($ch);

        $duration = round((microtime(true) - $start_time) * 1000, 2);

        if ($err) {
            return [
                'status'    => false,
                'message'   => "Gagal terhubung ke Web API Server: {$err} (URL: {$url})",
                'http_code' => 0,
                'latency'   => $duration
            ];
        }

        if ($http_code >= 200 && $http_code < 400) {
            return [
                'status'    => true,
                'message'   => "Berhasil terhubung ke Web API Server (HTTP {$http_code}, Waktu Respons: {$duration} ms).",
                'http_code' => $http_code,
                'latency'   => $duration
            ];
        } else {
            return [
                'status'    => false,
                'message'   => "Web API Server merespon dengan HTTP Code {$http_code} (URL: {$url}).",
                'http_code' => $http_code,
                'latency'   => $duration
            ];
        }
    }
}
