<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fingerprint_Helper Library
 * Menangani komunikasi socket TCP/IP langsung ke Mesin Sidik Jari (Fingerspot / ZK)
 * serta sinkronisasi data log ke API Server MKDC.
 */
class Fingerprint_Helper
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Menguji keterhubungan (Ping/Socket Handshake) ke IP & Port Mesin
     */
    public function pingMachine($ip, $port = 4370, $timeout = 3)
    {
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($fp) {
            fclose($fp);
            return [
                'status'  => true,
                'message' => "Terhubung ke mesin {$ip}:{$port}"
            ];
        }

        return [
            'status'  => false,
            'message' => "Gagal terhubung ke {$ip}:{$port} - Error: {$errstr} ({$errno})"
        ];
    }

    /**
     * Membaca Log Absensi dari Mesin ZK / Fingerspot via Socket TCP
     */
    public function getAttendanceLogs($ip, $port = 4370, $commKey = 0)
    {
        // 1. Cek keterhubungan dasar terlebih dahulu
        $ping = $this->pingMachine($ip, $port, 3);
        if (!$ping['status']) {
            return [
                'status'  => false,
                'message' => $ping['message'],
                'logs'    => []
            ];
        }

        // 2. Buka koneksi socket TCP
        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
        if (!$socket) {
            return [
                'status'  => false,
                'message' => "Socket error: {$errstr}",
                'logs'    => []
            ];
        }

        stream_set_timeout($socket, 5);

        // Standard ZK protocol commands
        // CMD_CONNECT = 1000, CMD_EXIT = 1001, CMD_ATTLOG_RRQ = 13
        $command = 13; // Read Attendance Log
        $command_string = '';
        $chksum = 0;
        $session_id = 0;
        $reply_id = 0;

        $buf = $this->createHeader($command, $chksum, $session_id, $reply_id, $command_string);
        fwrite($socket, $buf);

        $response = fread($socket, 1024);
        
        // Catatan: Pada mesin fisik ZK/Fingerspot asli, data biner yang diterima akan di-parse.
        // Jika mesin menggunakan protokol ZK standard atau UDP/TCP socket stream.
        $logs = $this->parseZkLogData($response);

        fclose($socket);

        return [
            'status'  => true,
            'message' => 'Berhasil berkomunikasi dengan mesin.',
            'logs'    => $logs
        ];
    }

    /**
     * Header Biner Protokol ZK
     */
    private function createHeader($command, $chksum, $session_id, $reply_id, $command_string)
    {
        $buf = pack('SSSS', $command, $chksum, $session_id, $reply_id) . $command_string;
        $buf = unpack('C*', $buf);
        $u_chksum = $this->calculateChecksum($buf);

        $reply_id += 1;
        if ($reply_id >= 65535) {
            $reply_id -= 65535;
        }

        $buf = pack('SSSS', $command, $u_chksum, $session_id, $reply_id) . $command_string;
        return $buf;
    }

    private function calculateChecksum($p)
    {
        $l = count($p);
        $chksum = 0;
        $i = 1;
        while ($i < $l) {
            $chksum += ($p[$i] + ($p[$i + 1] << 8));
            $i += 2;
        }

        $chksum = ($chksum >> 16) + ($chksum & 0xffff);

        return (~$chksum) & 0xffff;
    }

    /**
     * Helper Parser Data Log Absensi ZK Biner
     */
    private function parseZkLogData($binaryData)
    {
        $logs = [];
        if (empty($binaryData) || strlen($binaryData) < 8) {
            return $logs;
        }

        // Memotong header balasan ZK (8 byte)
        $data = substr($binaryData, 8);
        $recordSize = 40; // Standard 40-byte record log di ZK/Fingerspot TCP

        while (strlen($data) >= $recordSize) {
            $record = substr($data, 0, $recordSize);
            $data   = substr($data, $recordSize);

            $arr = unpack('vpin/Cstatus/Cverified/Vtime', substr($record, 0, 8));
            if (isset($arr['pin']) && isset($arr['time']) && $arr['pin'] > 0) {
                $logs[] = [
                    'pin'       => (int) $arr['pin'],
                    'scan_date' => date('Y-m-d H:i:s', $arr['time'])
                ];
            }
        }

        return $logs;
    }

    /**
     * Mengirim payload logs ke Endpoint API yang dikonfigurasi (Dev / Prod)
     */
    public function sendToApi($logs, $endpointUrl, $apiToken)
    {
        if (empty($logs)) {
            return [
                'status'  => 'success',
                'message' => 'Tidak ada log untuk dikirim.'
            ];
        }

        $payload = json_encode([
            'token' => $apiToken,
            'logs'  => $logs
        ]);

        $ch = curl_init($endpointUrl);
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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return [
                'status'  => 'error',
                'message' => "cURL Error: {$err}"
            ];
        }

        $resData = json_decode($response, true);
        if ($resData) {
            return $resData;
        }

        return [
            'status'    => ($httpCode === 200) ? 'success' : 'error',
            'message'   => "Server merespons dengan HTTP Code {$httpCode}",
            'raw_reply' => $response
        ];
    }
}
