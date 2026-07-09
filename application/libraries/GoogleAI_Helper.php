<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GoogleAI_Helper
{
    protected $CI;
    protected $api_key;
    protected $model = 'gemini-2.0-flash';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->api_key = setting('google_ai_api_key') ?: '';
    }

    /**
     * Generate structured learning agenda based on class, subject and Indonesian curriculum
     */
    public function generateAgenda($subjectName, $classLevel, $meetingsCount)
    {
        if (empty($this->api_key) || $this->api_key === '0') {
            return ['error' => 'API Key Google AI belum dikonfigurasikan di halaman Pengaturan API.'];
        }

        $prompt = "Anda adalah pakar kurikulum nasional Indonesia (Kurikulum Merdeka). "
                . "Buatlah silabus rencana pertemuan pembelajaran yang teratur dan berurutan untuk mata pelajaran '{$subjectName}' "
                . "pada tingkat kelas '{$classLevel}' (sesuaikan dengan Fase kurikulum merdeka saat ini di Indonesia). "
                . "Sediakan tepat sebanyak {$meetingsCount} pertemuan pembelajaran. "
                . "Keluaran HARUS berupa JSON array of objects yang valid tanpa markdown code block formatting (hanya raw JSON string). "
                . "Setiap object harus memiliki atribut: "
                . "1. 'pertemuan' (integer, urutan pertemuan dari 1 sampai {$meetingsCount}) "
                . "2. 'materi' (string, ringkasan materi pelajaran yang diajarkan, maksimal 100 karakter) "
                . "3. 'kegiatan' (string, ringkasan kegiatan belajar mengajar atau metode ajar, maksimal 200 karakter). "
                . "Pastikan urutan materi logis dan bermakna.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$output) {
            return ['error' => 'Gagal terhubung ke Google AI API (Gemini).'];
        }

        $res = json_decode($output, true);
        if ($http_code !== 200) {
            $msg = isset($res['error']['message']) ? $res['error']['message'] : 'Terjadi kesalahan otentikasi atau limitasi API Google AI.';
            return ['error' => $msg];
        }

        if (empty($res['candidates'][0]['content']['parts'][0]['text'])) {
            return ['error' => 'Google AI mengembalikan respon kosong atau tidak valid.'];
        }

        $raw_json = $res['candidates'][0]['content']['parts'][0]['text'];
        $clean_json = trim($raw_json);
        
        // Remove markdown block if AI somehow returned it
        if (strpos($clean_json, '```') === 0) {
            $clean_json = preg_replace('/^```(?:json)?|```$/i', '', $clean_json);
            $clean_json = trim($clean_json);
        }

        $agenda_data = json_decode($clean_json, true);
        if (!is_array($agenda_data)) {
            return ['error' => 'Format JSON respon AI tidak valid untuk diproses.'];
        }

        return $agenda_data;
    }
}
