<?php
/**
 * EasyLinkSDK - Driver Komunikasi Mesin Fingerprint EasyLink / Fingerspot / Revo
 * Disesuaikan secara presisi dengan Spesifikasi SDK EasyLink & Delphi 7 Client SDK D7
 */

class EasyLinkSDK
{
    /**
     * WebService HTTP cURL Request Ke Mesin EasyLink
     */
    public static function webservice($ip, $port, $endpoint, $parameter, $timeout = 0)
    {
        @set_time_limit(180);
        $endpoint    = ltrim($endpoint, '/');
        $url         = "http://{$ip}/{$endpoint}";
        $max_retries = 30; // Jeda retry hingga 30 kali saat SDK memuat data dari mesin
        $retry_count = 0;
        $response    = "";
        $eff_timeout = ($timeout > 0) ? (int)$timeout : 30;

        while ($retry_count < $max_retries) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_PORT           => (int)$port,
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_CONNECTTIMEOUT => 6,
                CURLOPT_TIMEOUT        => $eff_timeout,
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
                $retry_count++;
                if ($retry_count < $max_retries) {
                    sleep(1);
                    continue;
                }
                return [
                    'status'  => false,
                    'message' => "Gagal menghubungi mesin ({$ip}:{$port}): " . $err,
                    'raw'     => null
                ];
            }

            $decoded = json_decode($response, true);
            if (isset($decoded['Result']) && $decoded['Result'] === false && isset($decoded['message_code']) && $decoded['message_code'] == 3) {
                // Mesin / SDK sedang sibuk menarik data dari hardware, tunggu 1 detik lalu retry
                $retry_count++;
                sleep(1);
            } else {
                break;
            }
        }

        return [
            'status'  => true,
            'message' => 'OK',
            'raw'     => $response
        ];
    }

    /**
     * Ping Socket ke IP & Port Mesin
     */
    public static function ping($ip, $port = 8080, $timeout = 3)
    {
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($fp) {
            fclose($fp);
            return [
                'status'  => true,
                'message' => "Terhubung ke mesin EasyLink {$ip}:{$port}"
            ];
        }

        return [
            'status'  => false,
            'message' => "Gagal terhubung ke {$ip}:{$port} - Error: {$errstr} ({$errno})"
        ];
    }

    /**
     * Detail Informasi & Spesifikasi Mesin (Get Info Mesin - dev/info)
     */
    public static function getDeviceInfo($ip, $port = 8080, $sn = '', $comm_key = '0')
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'dev/info', $parameter, 10);

        if ($res['status'] && !empty($res['raw'])) {
            $json = json_decode($res['raw'], true);
            if (is_array($json)) {
                return [
                    'status'   => true,
                    'message'  => 'OK',
                    'info'     => [
                        'device_name'   => $json['DevName'] ?? $json['dev_name'] ?? 'EasyLink Revo Series',
                        'firmware'      => $json['FWVer'] ?? $json['fw_ver'] ?? 'Ver 8.4.3',
                        'serial_number' => $json['SN'] ?? $json['sn'] ?? $sn,
                        'platform'      => $json['Platform'] ?? $json['platform'] ?? 'Linux',
                        'device_time'   => $json['DevTime'] ?? $json['dev_time'] ?? date('Y-m-d H:i:s'),
                        'total_user'    => intval($json['UserCount'] ?? $json['user_count'] ?? 0),
                        'total_fp'      => intval($json['FPCount'] ?? $json['fp_count'] ?? 0),
                        'total_log'     => intval($json['LogCount'] ?? $json['log_count'] ?? 0),
                        'ip_address'    => $ip,
                        'port'          => $port
                    ],
                    'raw_json' => $json
                ];
            }
        }

        $ping = self::ping($ip, $port, 3);
        return [
            'status'  => $ping['status'],
            'message' => $ping['status'] ? 'Mesin online, namun WebService dev/info tidak merespons.' : $ping['message'],
            'info'    => [
                'device_name'   => 'Fingerspot Revo / EasyLink Series',
                'firmware'      => 'Ver 8.4.3-EL-2026',
                'serial_number' => !empty($sn) ? $sn : 'FS-EASYLINK',
                'platform'      => 'ZMM220_Linux',
                'device_time'   => date('Y-m-d H:i:s'),
                'total_user'    => 0,
                'total_fp'      => 0,
                'total_log'     => 0,
                'ip_address'    => $ip,
                'port'          => $port
            ],
            'raw_json' => []
        ];
    }

    /**
     * Membaca Scanlog Presensi dari Mesin (scanlog/all/paging, scanlog/new)
     */
    public static function getScanlog($ip, $port = 8080, $sn = '', $limit = 500, $mode = 'all')
    {
        $sn_list  = array_filter(explode(';', $sn));
        $sn_query = !empty($sn_list) ? reset($sn_list) : $sn;

        $endpoint        = ($mode === 'new') ? 'scanlog/new' : 'scanlog/all/paging';
        $master_logs_map = [];
        $max_pages       = 300; // Dapat menarik hingga 300 halaman (30.000+ log)
        $page            = 0;
        $session         = true;
        $batch_limit     = ($limit > 0) ? intval($limit) : 200;
        $error_detail    = '';

        do {
            @set_time_limit(60);
            $parameter = "sn=" . urlencode($sn_query) . "&limit=" . $batch_limit;
            $res       = self::webservice($ip, $port, $endpoint, $parameter, 20);

            if (!$res['status'] || empty($res['raw'])) {
                $error_detail = $res['message'] ?? 'Mesin tidak merespons WebService request.';
                break;
            }

            $decoded = json_decode(trim($res['raw']), true);
            $entries = null;
            if (is_array($decoded)) {
                if (isset($decoded['Data']) && is_array($decoded['Data'])) {
                    $entries = $decoded['Data'];
                } elseif (isset($decoded['data']) && is_array($decoded['data'])) {
                    $entries = $decoded['data'];
                } elseif (isset($decoded[0]) && is_array($decoded[0])) {
                    $entries = $decoded;
                }
            }

            if (is_array($entries) && count($entries) > 0) {
                $count_before = count($master_logs_map);

                foreach ($entries as $entry) {
                    $pin_raw = trim((string)($entry['PIN'] ?? $entry['pin'] ?? ''));
                    $date    = trim($entry['ScanDate'] ?? $entry['scan_date'] ?? $entry['date'] ?? '');
                    if (!empty($pin_raw) && $pin_raw !== '0' && !empty($date)) {
                        $log_key = $pin_raw . '_' . $date;
                        if (!isset($master_logs_map[$log_key])) {
                            $master_logs_map[$log_key] = [
                                'sn'         => $sn_query,
                                'pin'        => $pin_raw,
                                'scan_date'  => $date,
                                'verifymode' => intval($entry['VerifyMode'] ?? $entry['verifymode'] ?? 1),
                                'iomode'     => intval($entry['IOMode'] ?? $entry['iomode'] ?? 0),
                                'workcode'   => intval($entry['WorkCode'] ?? $entry['workcode'] ?? 0)
                            ];
                        }
                    }
                }

                // Jika tidak ada data unik baru yang ditambahkan di iterasi ini, hentikan loop
                if (count($master_logs_map) === $count_before) {
                    $session = false;
                    break;
                }

                if ($mode === 'new') {
                    $session = false;
                } else {
                    $is_sess_val = $decoded['IsSession'] ?? $decoded['is_session'] ?? null;
                    if ($is_sess_val !== null) {
                        $session_bool = is_string($is_sess_val) ? (strtolower($is_sess_val) === 'true' || $is_sess_val === '1') : (bool)$is_sess_val;
                        if (!$session_bool) {
                            $session = false;
                        }
                    }
                }
            } else {
                $session = false;
            }

            $page++;
        } while ($session && $page < $max_pages);

        $has_data = count($master_logs_map) > 0;
        return [
            'status'      => $has_data || empty($error_detail),
            'message'     => $has_data ? 'Berhasil membaca scanlog.' : ($error_detail ?: 'Tidak ada log presensi pada mesin.'),
            'total_read'  => count($master_logs_map),
            'total_pages' => $page,
            'data'        => array_values($master_logs_map)
        ];
    }

    /**
     * Membaca Seluruh User dari Mesin (Wajib Paging Limit 1)
     */
    public static function getAllUsers($ip, $port = 8080, $sn = '', $comm_key = '0')
    {
        return self::getUsers($ip, $port, $sn, $comm_key, 1);
    }

    /**
     * Membaca Daftar Pengguna dari Mesin EasyLink (Wajib Paging Limit 1 via user/all/paging)
     */
    public static function getUsers($ip, $port = 8080, $sn = '', $comm_key = '0', $limit = 1)
    {
        $sn_list  = array_filter(explode(';', $sn));
        $sn_query = !empty($sn_list) ? reset($sn_list) : $sn;

        $master_users_map = [];
        $last_error_msg   = '';
        $machine_online   = false;

        // Wajib Paging Limit = 1 sesuai permintaan
        $batch_limit = 1;

        // Panggil endpoint user/all/paging persis seperti Client_EasyLinkSDK_PHP (download_user_with_timer.php & user.php)
        $session   = true;
        $page      = 0;
        $max_pages = 300;

        do {
            @set_time_limit(0);
            $parameter = "sn=" . urlencode($sn_query) . "&limit=" . $batch_limit;

            $res = self::webservice($ip, $port, 'user/all/paging', $parameter, 30);

            if (!$res['status'] || empty($res['raw'])) {
                if (!$res['status']) {
                    $last_error_msg = $res['message'] ?? 'Koneksi ke WebService mesin gagal.';
                }
                break;
            }

            $machine_online = true;
            $raw_text       = trim($res['raw']);
            $decoded        = json_decode($raw_text, true);

            $entries = null;
            if (is_array($decoded)) {
                if (isset($decoded['Data']) && is_array($decoded['Data'])) {
                    $entries = $decoded['Data'];
                } elseif (isset($decoded['data']) && is_array($decoded['data'])) {
                    $entries = $decoded['data'];
                } elseif (isset($decoded['users']) && is_array($decoded['users'])) {
                    $entries = $decoded['users'];
                } elseif (isset($decoded[0]) && is_array($decoded[0])) {
                    $entries = $decoded;
                }
            }

            if (is_array($entries) && count($entries) > 0) {
                foreach ($entries as $entry) {
                    $pin_raw = $entry['PIN'] ?? $entry['pin'] ?? $entry['user_id'] ?? '';
                    $pin     = trim((string)$pin_raw);
                    $nama    = trim($entry['Name'] ?? $entry['name'] ?? $entry['nama'] ?? '');
                    $pwd     = trim($entry['Password'] ?? $entry['pwd'] ?? '');
                    $rfid    = trim($entry['RFID'] ?? $entry['rfid'] ?? '');
                    $priv    = intval($entry['Privilege'] ?? $entry['privilege'] ?? 0);
                    $tmpl    = $entry['Template'] ?? $entry['template'] ?? [];

                    if ($pin !== '') {
                        if (!isset($master_users_map[$pin])) {
                            $master_users_map[$pin] = [
                                'pin'       => $pin,
                                'nama'      => $nama,
                                'pwd'       => $pwd,
                                'rfid'      => $rfid,
                                'privilege' => $priv,
                                'templates' => is_array($tmpl) ? $tmpl : []
                            ];
                        } else {
                            if (!empty($nama) && empty($master_users_map[$pin]['nama'])) {
                                $master_users_map[$pin]['nama'] = $nama;
                            }
                            if (is_array($tmpl) && !empty($tmpl)) {
                                foreach ($tmpl as $t) {
                                    $master_users_map[$pin]['templates'][] = $t;
                                }
                            }
                        }
                    }
                }
            } else {
                $session = false;
            }

            // Paging loop dikendalikan murni oleh IsSession persis seperti Client_EasyLinkSDK_PHP ($session = $content->IsSession)
            $is_sess_val = $decoded['IsSession'] ?? $decoded['is_session'] ?? null;
            if ($is_sess_val !== null) {
                $session_bool = is_string($is_sess_val) ? (strtolower($is_sess_val) === 'true' || $is_sess_val === '1') : (bool)$is_sess_val;
                $session = $session_bool;
            } else {
                $session = false;
            }

            $page++;
        } while ($session && $page < $max_pages);

        $all_users    = array_values($master_users_map);
        $actual_count = count($all_users);

        // Ambil Info Mesin (dev/info) setelah penarikan paging selesai untuk perbandingan akurat
        $dev_info       = self::getDeviceInfo($ip, $port, $sn_query, $comm_key);
        $expected_count = intval($dev_info['info']['total_user'] ?? 0);

        // 3. Verifikasi & Perbandingan Jumlah Data Ditarik vs Jumlah di Info Mesin
        if (!empty($master_users_map)) {
            $is_match = ($expected_count > 0) ? ($actual_count === $expected_count) : true;
            if ($expected_count > 0) {
                if ($is_match) {
                    $msg = "Berhasil menarik seluruh {$actual_count} pengguna dari mesin. Jumlah data SESUAI 100% dengan spesifikasi info mesin ({$expected_count} User).";
                } else {
                    $msg = "Berhasil menarik {$actual_count} pengguna dari mesin via EasyLink SDK Paging. (Info Mesin: {$expected_count} User | Selisih: " . abs($expected_count - $actual_count) . " User).";
                }
            } else {
                $msg = "Berhasil menarik seluruh {$actual_count} pengguna dari mesin EasyLink.";
            }

            return [
                'status'         => true,
                'message'        => $msg,
                'total_read'     => $actual_count,
                'expected_count' => $expected_count,
                'is_match'       => $is_match,
                'device_info'    => $dev_info['info'] ?? [],
                'users'          => $all_users
            ];
        }

        if ($machine_online) {
            $is_match = ($expected_count === 0);
            return [
                'status'         => true,
                'message'        => "Terhubung ke mesin EasyLink {$ip}:{$port}. Saat ini belum ada data pengguna terdaftar di dalam mesin (0 User | Info Mesin: {$expected_count} User).",
                'total_read'     => 0,
                'expected_count' => $expected_count,
                'is_match'       => $is_match,
                'device_info'    => $dev_info['info'] ?? [],
                'users'          => []
            ];
        }

        $error_detail = !empty($last_error_msg) ? " ({$last_error_msg})" : "";
        return [
            'status'         => false,
            'message'        => "Gagal membaca data dari mesin EasyLink {$ip}:{$port}{$error_detail}.",
            'total_read'     => 0,
            'expected_count' => $expected_count,
            'is_match'       => false,
            'device_info'    => $dev_info['info'] ?? [],
            'users'          => []
        ];
    }

    /**
     * Menambahkan / Mengunggah User ke Mesin (user/set)
     */
    public static function setUser($ip, $port, $sn, $pin, $name, $pwd = '', $rfid = '', $priv = 0, $template_json = '')
    {
        $parameter = "sn=" . urlencode($sn) .
                     "&pin=" . urlencode($pin) .
                     "&nama=" . urlencode(trim($name)) .
                     "&pwd=" . urlencode($pwd) .
                     "&rfid=" . urlencode($rfid) .
                     "&priv=" . intval($priv);

        if (!empty($template_json)) {
            $parameter .= "&tmp=" . urlencode($template_json);
        }

        $res = self::webservice($ip, $port, 'user/set', $parameter, 8);
        if ($res['status'] && !empty($res['raw'])) {
            $decoded = json_decode($res['raw'], true);
            if (isset($decoded['Result']) && $decoded['Result'] === true) {
                return [
                    'status'  => true,
                    'message' => "Berhasil mengunggah user PIN {$pin} ({$name}) ke mesin."
                ];
            }
        }

        return [
            'status'  => false,
            'message' => "Gagal mengunggah user ke mesin: " . ($res['message'] ?? 'Response tidak valid')
        ];
    }

    /**
     * Menghapus User Single per PIN dari Mesin (user/del)
     */
    public static function deleteUser($ip, $port, $sn, $pin)
    {
        $parameter = "sn=" . urlencode($sn) . "&pin=" . urlencode($pin);
        $res       = self::webservice($ip, $port, 'user/del', $parameter, 8);

        if ($res['status'] && !empty($res['raw'])) {
            $decoded = json_decode($res['raw'], true);
            if (isset($decoded['Result']) && $decoded['Result'] === true) {
                return [
                    'status'  => true,
                    'message' => "Berhasil menghapus user PIN {$pin} dari mesin."
                ];
            }
        }

        return [
            'status'  => false,
            'message' => "Gagal menghapus user PIN {$pin} dari mesin."
        ];
    }

    /**
     * Menghapus SELURUH User dari Mesin (user/delall)
     */
    public static function deleteAllUsers($ip, $port, $sn)
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'user/delall', $parameter, 10);

        if ($res['status'] && !empty($res['raw'])) {
            $decoded = json_decode($res['raw'], true);
            if (isset($decoded['Result']) && $decoded['Result'] === true) {
                return [
                    'status'  => true,
                    'message' => "Berhasil menghapus seluruh pengguna di dalam mesin."
                ];
            }
        }

        return [
            'status'  => false,
            'message' => "Gagal menghapus seluruh pengguna di mesin."
        ];
    }

    /**
     * Menghapus Hak Akses Admin Mesin (user/deladmin atau dev/deladmin)
     */
    public static function deleteAdmin($ip, $port, $sn)
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'dev/deladmin', $parameter, 8);

        if (!$res['status'] || empty($res['raw'])) {
            $res = self::webservice($ip, $port, 'user/deladmin', $parameter, 8);
        }

        return [
            'status'  => $res['status'],
            'message' => $res['status'] ? 'Berhasil menghapus hak akses administrator pada mesin.' : $res['message']
        ];
    }

    /**
     * Menghapus Log Presensi di Mesin (scanlog/del atau log/del)
     */
    public static function deleteScanlog($ip, $port, $sn)
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'scanlog/del', $parameter, 8);

        if (!$res['status'] || empty($res['raw'])) {
            $res = self::webservice($ip, $port, 'log/del', $parameter, 8);
        }

        return [
            'status'  => $res['status'],
            'message' => $res['status'] ? 'Berhasil menghapus log presensi pada mesin.' : $res['message']
        ];
    }

    /**
     * Sinkronisasi Jam Mesin (dev/settime)
     */
    public static function setTime($ip, $port, $sn)
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'dev/settime', $parameter, 8);

        return [
            'status'  => $res['status'],
            'message' => $res['status'] ? 'Jam mesin berhasil disinkronkan dengan waktu server.' : $res['message']
        ];
    }

    /**
     * Inisialisasi / Reset Mesin (dev/init)
     */
    public static function initDevice($ip, $port, $sn)
    {
        $parameter = "sn=" . urlencode($sn);
        $res       = self::webservice($ip, $port, 'dev/init', $parameter, 10);

        return [
            'status'  => $res['status'],
            'message' => $res['status'] ? 'Perintah inisialisasi mesin telah dikirimkan.' : $res['message']
        ];
    }
}
