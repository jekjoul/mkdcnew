<?php
/**
 * BridgeStorage - Engine Database Offline Flat-File JSON
 * Menyimpan data akun, setting mesin, setting API, dan log secara offline tanpa MySQL.
 */

class BridgeStorage
{
    private static $db_file = __DIR__ . '/../data/bridge_db.json';

    /**
     * Inisialisasi awal database file jika belum ada
     */
    private static function init()
    {
        $dir = dirname(self::$db_file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$db_file)) {
            $default_data = [
                'admin' => [
                    'username'      => 'admin',
                    'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                    'name'          => 'Administrator Fingerprint'
                ],
                'settings' => [
                    'env_mode'           => 'development',
                    'dev_endpoint_url'   => 'http://localhost/mkdcnew/api/presensi/sync',
                    'prod_endpoint_url'  => 'https://domain-sekolah.sch.id/api/presensi/sync',
                    'api_token'          => 'MKDC_FINGERPRINT_SECRET_KEY_2026',
                    'auto_sync_interval' => 10,
                    'auto_sync_active'   => 1
                ],
                'machine' => [
                    'nama_mesin'    => 'Mesin Utama Fingerspot Revo',
                    'ip_address'    => '127.0.0.1',
                    'port'          => 8080,
                    'comm_key'      => '0',
                    'serial_number' => '6668601649075',
                    'kode_aktivasi' => ''
                ],
                'history_sync' => []
            ];
            file_put_contents(self::$db_file, json_encode($default_data, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Membaca seluruh data dari file JSON
     */
    public static function getData()
    {
        self::init();
        $json = file_get_contents(self::$db_file);
        return json_decode($json, true) ?: [];
    }

    /**
     * Menyimpan seluruh array data ke file JSON
     */
    public static function saveData($data)
    {
        self::init();
        return file_put_contents(self::$db_file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Mengambil data akun admin
     */
    public static function getAdmin()
    {
        $data = self::getData();
        return $data['admin'] ?? [];
    }

    /**
     * Memverifikasi login admin
     */
    public static function verifyLogin($username, $password)
    {
        $admin = self::getAdmin();
        if ($admin && $admin['username'] === $username) {
            if (password_verify($password, $admin['password_hash'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update password admin
     */
    public static function updatePassword($new_password, $new_name = null)
    {
        $data = self::getData();
        $data['admin']['password_hash'] = password_hash($new_password, PASSWORD_BCRYPT);
        if ($new_name) {
            $data['admin']['name'] = trim($new_name);
        }
        return self::saveData($data);
    }

    /**
     * Mengambil data pengaturan API
     */
    public static function getSettings()
    {
        $data = self::getData();
        return $data['settings'] ?? [];
    }

    /**
     * Mengambil URL Endpoint aktif berdasarkan env_mode (Dev vs Prod)
     */
    public static function getActiveEndpointUrl()
    {
        $s = self::getSettings();
        $env = $s['env_mode'] ?? 'development';
        if ($env === 'production') {
            return $s['prod_endpoint_url'] ?? '';
        }
        return $s['dev_endpoint_url'] ?? '';
    }

    /**
     * Menyimpan pengaturan API
     */
    public static function saveSettings($settings_data)
    {
        $data = self::getData();
        $data['settings'] = array_merge($data['settings'] ?? [], $settings_data);
        return self::saveData($data);
    }

    /**
     * Mengambil pengaturan mesin
     */
    public static function getMachine()
    {
        $data = self::getData();
        return $data['machine'] ?? [];
    }

    /**
     * Menyimpan pengaturan mesin
     */
    public static function saveMachine($machine_data)
    {
        $data = self::getData();
        $data['machine'] = array_merge($data['machine'] ?? [], $machine_data);
        return self::saveData($data);
    }

    /**
     * Menambahkan log aktivitas sync
     */
    public static function addSyncLog($log_entry)
    {
        $data = self::getData();
        if (!isset($data['history_sync'])) {
            $data['history_sync'] = [];
        }
        $log_entry['timestamp'] = date('Y-m-d H:i:s');
        array_unshift($data['history_sync'], $log_entry);
        // Batasi 100 log terbaru
        $data['history_sync'] = array_slice($data['history_sync'], 0, 100);
        return self::saveData($data);
    }

    /**
     * Mengambil riwayat sync
     */
    public static function getSyncHistory($limit = 20)
    {
        $data = self::getData();
        $history = $data['history_sync'] ?? [];
        return array_slice($history, 0, $limit);
    }
}
