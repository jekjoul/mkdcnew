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
        $this->model = setting('google_ai_model') ?: 'gemini-2.0-flash';
    }

    /**
     * Generate structured learning agenda based on class, subject and Indonesian curriculum
     */
    public function generateAgenda($subjectName, $classLevel, $meetingsCount, $modulListStr = "")
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', 180);

        if (empty($this->api_key) || $this->api_key === '0') {
            return ['error' => 'API Key Google AI belum dikonfigurasikan di halaman Pengaturan API.'];
        }

        $rpp_context = "";
        if (!empty($modulListStr)) {
            $rpp_context = "\nSebagai acuan wajib, Anda HARUS menyelaraskan topik materi pertemuan harian dengan daftar Rencana Pelaksanaan Pembelajaran (RPP) / Modul Ajar berikut yang sudah disusun sebelumnya:\n--- DAFTAR MODUL AJAR (RPP) ---\n{$modulListStr}\n--- AKHIR DAFTAR ---\nPastikan agenda pertemuan harian memetakan pembahasan di atas secara sinkron.\n";
        }

        $prompt = "Anda adalah pakar kurikulum nasional Indonesia (Kurikulum Merdeka). "
                . "Buatlah silabus rencana pertemuan pembelajaran yang teratur dan berurutan untuk mata pelajaran '{$subjectName}' "
                . "pada tingkat kelas '{$classLevel}' (sesuaikan dengan Fase kurikulum merdeka saat ini di Indonesia). "
                . "Sediakan tepat sebanyak {$meetingsCount} pertemuan pembelajaran. "
                . $rpp_context
                . "\nKeluaran HARUS berupa JSON array of objects yang valid tanpa markdown code block formatting (hanya raw JSON string). "
                . "Setiap object harus memiliki atribut: "
                . "1. 'pertemuan' (integer, urutan pertemuan dari 1 sampai {$meetingsCount}) "
                . "2. 'materi' (string, ringkasan materi pelajaran yang diajarkan. Rumuskan materi secara interaktif, kreatif, sebutkan aplikasi penerapannya di dunia nyata, serta wajib sertakan 1 contoh judul/topik video pembelajaran YouTube yang relevan untuk ditonton, maksimal 250 karakter) "
                . "3. 'kegiatan' (string, ringkasan metode/kegiatan belajar mengajar aktif dan interaktif, praktikum, diskusi kelompok, atau simulasi nyata yang dipraktikkan murid di kelas, maksimal 400 karakter). "
                . "PENTING: Di dalam seluruh isi data materi dan kegiatan, hindari penggunaan istilah/kata 'peserta didik', ganti/gunakan kata 'murid' sebagai gantinya. "
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

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
        
        // Remove markdown block if AI returned it (even if inside the string)
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $clean_json, $matches)) {
            $clean_json = trim($matches[1]);
        } else {
            // Fallback: search for first [ or { and last ] or } to extract JSON
            $first_bracket = strpos($clean_json, '[');
            $first_curly = strpos($clean_json, '{');
            $start = false;
            
            if ($first_bracket !== false && $first_curly !== false) {
                $start = min($first_bracket, $first_curly);
            } elseif ($first_bracket !== false) {
                $start = $first_bracket;
            } elseif ($first_curly !== false) {
                $start = $first_curly;
            }
            
            if ($start !== false) {
                $last_bracket = strrpos($clean_json, ']');
                $last_curly = strrpos($clean_json, '}');
                $end = false;
                
                if ($last_bracket !== false && $last_curly !== false) {
                    $end = max($last_bracket, $last_curly);
                } elseif ($last_bracket !== false) {
                    $end = $last_bracket;
                } elseif ($last_curly !== false) {
                    $end = $last_curly;
                }
                
                if ($end !== false && $end > $start) {
                    $clean_json = substr($clean_json, $start, $end - $start + 1);
                }
            }
        }

        $agenda_data = json_decode($clean_json, true);
        if (!is_array($agenda_data)) {
            return ['error' => 'Format JSON respon AI tidak valid untuk diproses. Respon mentah: ' . substr($raw_json, 0, 200)];
        }

        return $agenda_data;
    }

    /**
     * Generate learning media / content (diagram SVG/HTML, slide HTML, summary/quiz, or YouTube link)
     */
    public function generateAgendaMedia($jenisMedia, $topicMateri, $customPrompt = "")
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', 180);

        if (empty($this->api_key) || $this->api_key === '0') {
            return ['error' => 'API Key Google AI belum dikonfigurasikan di halaman Pengaturan API.'];
        }

        $systemInstruction = "Anda adalah pakar media pembelajaran digital interaktif untuk Kurikulum Merdeka di Indonesia. "
            . "DILARANG KERAS menghasilkan atau membuat skrip/file audio dan video. "
            . "Khusus video, Anda HANYA diperbolehkan mencarikan dan merekomendasikan link video dari YouTube yang relevan. ";

        $promptInfo = !empty($customPrompt) ? " Instruksi Tambahan Guru: {$customPrompt}." : "";

        if ($jenisMedia === 'youtube') {
            $userPrompt = $systemInstruction
                . "Tugas Anda: Cari dan rekomendasikan tepat 1 link video pembelajaran dari YouTube yang paling relevan dan berkualitas tinggi untuk topik materi: '{$topicMateri}'."
                . $promptInfo
                . "\nKeluaran HARUS berupa RAW JSON string tanpa markdown block dengan format object persis berikut:"
                . "\n{"
                . "\n  \"type\": \"youtube\","
                . "\n  \"url\": \"https://www.youtube.com/watch?v=... (isi dengan URL video YouTube aktual/valid)\","
                . "\n  \"title\": \"Judul Video YouTube\","
                . "\n  \"description\": \"Ringkasan singkat isi video\""
                . "\n}";
        } elseif ($jenisMedia === 'gambar') {
            $userPrompt = $systemInstruction
                . "Tugas Anda: Buatlah diagram visual SVG yang indah atau ilustrasi visual dalam format HTML (dapat menggunakan SVG inline atau visual HTML card dengan warna dan styling profesional) untuk menjelaskan topik materi: '{$topicMateri}'."
                . $promptInfo
                . "\nKeluaran HARUS berupa RAW JSON string tanpa markdown block dengan format object persis berikut:"
                . "\n{"
                . "\n  \"type\": \"html\","
                . "\n  \"title\": \"Diagram / Visual '{$topicMateri}'\","
                . "\n  \"content\": \"<div class='ai-media-diagram'>...isi kode HTML SVG/visual card...</div>\""
                . "\n}";
        } elseif ($jenisMedia === 'slide') {
            $userPrompt = $systemInstruction
                . "Tugas Anda: Buatlah slide presentasi ringkas dan interaktif dalam format HTML yang indah (terdiri dari 3-4 slide card interaktif dengan judul slide, poin utama, badge visual, dan styling CSS inline yang rapi) untuk topik materi: '{$topicMateri}'."
                . $promptInfo
                . "\nKeluaran HARUS berupa RAW JSON string tanpa markdown block dengan format object persis berikut:"
                . "\n{"
                . "\n  \"type\": \"html\","
                . "\n  \"title\": \"Slide Presentasi '{$topicMateri}'\","
                . "\n  \"content\": \"<div class='ai-media-slides'>...isi kode HTML slide presentasi...</div>\""
                . "\n}";
        } else {
            // materi / quiz / lembar kerja
            $userPrompt = $systemInstruction
                . "Tugas Anda: Buatlah rangkuman materi pembelajaran yang terstruktur, poin-poin utama, serta 3 soal kuis latihan pilihan ganda interaktif beserta kunci jawabannya dalam format HTML yang rapi untuk topik materi: '{$topicMateri}'."
                . $promptInfo
                . "\nKeluaran HARUS berupa RAW JSON string tanpa markdown block dengan format object persis berikut:"
                . "\n{"
                . "\n  \"type\": \"html\","
                . "\n  \"title\": \"Rangkuman Materi & Kuis '{$topicMateri}'\","
                . "\n  \"content\": \"<div class='ai-media-materi'>...isi kode HTML rangkuman dan kuis...</div>\""
                . "\n}";
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $userPrompt]
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

        // Remove markdown block if AI returned it (even if inside the string)
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $clean_json, $matches)) {
            $clean_json = trim($matches[1]);
        } else {
            // Fallback: search for first [ or { and last ] or } to extract JSON
            $first_bracket = strpos($clean_json, '[');
            $first_curly = strpos($clean_json, '{');
            $start = false;
            
            if ($first_bracket !== false && $first_curly !== false) {
                $start = min($first_bracket, $first_curly);
            } elseif ($first_bracket !== false) {
                $start = $first_bracket;
            } elseif ($first_curly !== false) {
                $start = $first_curly;
            }
            
            if ($start !== false) {
                $last_bracket = strrpos($clean_json, ']');
                $last_curly = strrpos($clean_json, '}');
                $end = false;
                
                if ($last_bracket !== false && $last_curly !== false) {
                    $end = max($last_bracket, $last_curly);
                } elseif ($last_bracket !== false) {
                    $end = $last_bracket;
                } elseif ($last_curly !== false) {
                    $end = $last_curly;
                }
                
                if ($end !== false && $end > $start) {
                    $clean_json = substr($clean_json, $start, $end - $start + 1);
                }
            }
        }

        $media_data = json_decode($clean_json, true);
        if (!is_array($media_data)) {
            return ['error' => 'Format JSON respon AI tidak valid untuk diproses. Respon mentah: ' . substr($raw_json, 0, 200)];
        }

        return $media_data;
    }
}
