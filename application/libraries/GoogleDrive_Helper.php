<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GoogleDrive_Helper
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Get or refresh Access Token using OAuth Client Credentials
     */
    public function getAccessToken()
    {
        $this->CI->load->config('google_oauth');
        
        $client_id = setting('google_client_id') ? setting('google_client_id') : $this->CI->config->item('google_oauth_client_id');
        $client_secret = setting('google_client_secret') ? setting('google_client_secret') : '';

        // If not configured, return null
        if (empty($client_id)) {
            return null;
        }

        // We can check if user has active google session with access token
        $access_token = $this->CI->session->userdata('google_access_token');
        if ($access_token) {
            return $access_token;
        }

        return null;
    }

    /**
     * Upload local file to Google Drive and convert to Google Doc/Sheet if requested
     */
    public function uploadFile($filepath, $filename, $mimeType, $convertToGoogleDoc = true)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['error' => 'Google Drive tidak terhubung. Silakan login menggunakan akun Google Anda terlebih dahulu.'];
        }

        // Map mime type to Google Docs format
        $targetMime = null;
        if ($convertToGoogleDoc) {
            if (strpos($mimeType, 'word') !== false || strpos($filename, '.docx') !== false) {
                $targetMime = 'application/vnd.google-apps.document'; // Convert to Google Docs
            } elseif (strpos($mimeType, 'excel') !== false || strpos($filename, '.xlsx') !== false) {
                $targetMime = 'application/vnd.google-apps.spreadsheet'; // Convert to Google Sheets
            }
        }

        $metadata = [
            'name' => $filename
        ];
        if ($targetMime) {
            $metadata['mimeType'] = $targetMime;
        }

        $boundary = 'foo_bar_boundary';
        $delimiter = "\r\n--" . $boundary . "\r\n";
        $closeDelimiter = "\r\n--" . $boundary . "--";

        $fields = $delimiter
            . 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n"
            . json_encode($metadata)
            . $delimiter
            . 'Content-Type: ' . $mimeType . "\r\n\r\n"
            . file_get_contents($filepath)
            . $closeDelimiter;

        $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: multipart/related; boundary=' . $boundary,
            'Content-Length: ' . strlen($fields)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $output = curl_exec($ch);
        curl_close($ch);

        if (!$output) {
            return ['error' => 'Gagal terhubung ke Google Drive API.'];
        }

        $res = json_decode($output, true);
        if (isset($res['error'])) {
            return ['error' => $res['error']['message']];
        }

        // Set file permissions so anyone with the link can edit/view (or restricted depending on organization)
        if (isset($res['id'])) {
            $this->setPermissions($res['id'], $accessToken);
        }

        return $res;
    }

    /**
     * Set anyone with link can write permission
     */
    private function setPermissions($fileId, $accessToken)
    {
        $url = "https://www.googleapis.com/drive/v3/files/{$fileId}/permissions";
        $data = [
            'role' => 'writer',
            'type' => 'anyone'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Download/export file from Google Drive to local server path
     */
    public function downloadGoogleFile($fileId, $localPath, $isXlsx = false)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken || !$fileId) return false;

        // If converted to Google Doc/Sheet, we must export it with target mime type
        $exportMime = $isXlsx 
            ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        $url = "https://www.googleapis.com/drive/v3/files/{$fileId}/export?mimeType=" . urlencode($exportMime);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $data) {
            file_put_contents($localPath, $data);
            return true;
        }

        return false;
    }

    /**
     * Delete file from Google Drive
     */
    public function deleteFile($fileId)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken || !$fileId) return false;

        $ch = curl_init("https://www.googleapis.com/drive/v3/files/{$fileId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
