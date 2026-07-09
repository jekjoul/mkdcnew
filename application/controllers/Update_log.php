<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Update_log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->page_data['page']->title = 'Update Log';
        $this->page_data['page']->titleUrl = 'update_log';
        $this->page_data['page']->subtitle = 'Log Pembaruan Aplikasi';
        $this->page_data['page']->subtitleUrl = 'update_log';
        $this->page_data['page']->icon = 'solar:history-linear';

        $logs = [];
        $transcript_path = 'C:/Users/jekjo/.gemini/antigravity/brain/470a4447-3509-4101-b3b2-c4139c8795dd/.system_generated/logs/transcript.jsonl';
        
        if (file_exists($transcript_path)) {
            $handle = fopen($transcript_path, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $data = json_decode($line, true);
                    if ($data && isset($data['source']) && $data['source'] === 'MODEL' && isset($data['tool_calls'])) {
                        foreach ($data['tool_calls'] as $tc) {
                            if (in_array($tc['name'], ['write_to_file', 'replace_file_content', 'multi_replace_file_content'], true)) {
                                if (isset($tc['args']['Description'])) {
                                    $desc = $tc['args']['Description'];
                                    
                                    // Skip placeholder descriptions or internal checks
                                    if (empty($desc) || stripos($desc, 'check') !== false && strlen($desc) < 25) {
                                        continue;
                                    }
                                    
                                    $logs[] = [
                                        'step' => $data['step_index'],
                                        'date' => date('Y-m-d H:i', strtotime($data['created_at'])),
                                        'timestamp' => strtotime($data['created_at']),
                                        'message' => $this->formatMessage($desc),
                                        'author' => 'Antigravity AI'
                                    ];
                                }
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        // Sort logs by timestamp desc
        usort($logs, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // De-duplicate identical messages
        $unique_logs = [];
        $seen = [];
        foreach ($logs as $log) {
            $key = trim($log['message']);
            if ($key && !in_array($key, $seen, true)) {
                $unique_logs[] = $log;
                $seen[] = $key;
            }
        }

        $this->page_data['logs'] = $unique_logs;
        $this->load->view('update_log/index', $this->page_data);
    }

    private function formatMessage($desc)
    {
        $desc = trim($desc);
        
        $mappings = [
            "Add checkPembelajaranAktif to edit method" => "Menambahkan validasi status pembelajaran aktif sebelum mengedit kelas.",
            "Add checkPembelajaranAktif to simpan method" => "Menambahkan validasi keaktifan kelas saat menyimpan data kelas.",
            "Add checkPembelajaranAktif to tambah_mapel" => "Menambahkan validasi keaktifan kelas saat membuka halaman tambah mapel.",
            "Add checkPembelajaranAktif to simpan_mapel" => "Menambahkan validasi keaktifan kelas saat menyimpan mata pelajaran.",
            "Add checkPembelajaranAktif to daftar_siswa" => "Menambahkan validasi keaktifan kelas saat mengelola daftar siswa.",
            "Add checkPembelajaranAktif to simpan_siswa" => "Menambahkan validasi keaktifan kelas saat menyimpan daftar siswa.",
            "Add luluskan and checkPembelajaranAktif methods" => "Menambahkan fitur pelulusan kelas pembelajaran dan penonaktifan rombel otomatis.",
            "Redesign restoreAlumniToSiswa to copy relations and delete alumni record" => "Memperbaiki proses pemulihan alumni agar datanya dihapus bersih dari tabel alumni untuk menghindari duplikasi.",
            "Update SweetAlert warning message in list.php" => "Memperbarui pesan peringatan konfirmasi kelulusan pembelajaran agar lebih informatif.",
            "Select jumlah_siswa count in pembelajaran query" => "Menambahkan penghitungan jumlah siswa secara dinamis di query pembelajaran.",
            "Rearrange columns and move Mapel and Siswa out of dropdown" => "Mengeluarkan tombol kelola Mapel dan Siswa ke kolom tersendiri di tabel pembelajaran.",
            "Join siswa in count subquery to filter orphaned rows" => "Memperbaiki perhitungan jumlah siswa di kelas agar data sampah (siswa tidak aktif/terhapus) tidak terhitung.",
            "Fetch other enrolled siswa IDs for the current tahun pelajaran" => "Menyaring daftar siswa agar siswa yang sudah terdaftar di kelas lain pada tahun ajaran aktif tidak muncul ganda.",
            "Exclude already enrolled siswa IDs from belum masuk list" => "Menyembunyikan siswa yang sudah terdaftar di kelas lain dari daftar calon siswa baru.",
            "Add javascript to scroll active menu into view" => "Menambahkan fitur auto-scroll pada sidebar ke menu yang sedang aktif saat halaman dimuat.",
            "Use jQuery load to scroll to active-page element" => "Memperbaiki logika auto-scroll sidebar ke menu aktif menggunakan class active-page.",
            "Use pure JS load event with setTimeout to scroll to active-page" => "Mengoptimalkan auto-scroll sidebar dengan JavaScript murni agar tidak bentrok dengan loading halaman.",
            "Remove rombel selection from restore modal" => "Menghapus pilihan rombel pada form pemulihan alumni.",
            "Remove rombels query and set rombel to null on restore" => "Menyetel rombel menjadi kosong (siswa tidak aktif) saat alumni dikembalikan.",
            "Update loadAllByPembelajaranStatus to query inactive students correctly" => "Memperbarui kriteria siswa tidak aktif agar hanya menampilkan siswa tanpa rombel aktif.",
            "Fix subquery in nonaktif siswa query by adding active status and not null filters" => "Memperbaiki query filter siswa tidak aktif untuk menghindari error SQL NULL.",
            "Filter only active pembelajarans when finding enrolled students" => "Memperbaiki pencarian siswa belum masuk kelas dengan hanya mengecek kelas yang masih aktif.",
            "Remove extra div closing tag to fix modal footer layout" => "Memperbaiki penutup tag HTML pada modal kembalikan alumni agar footer tidak rusak.",
            "Remove rombel text input from restore form and keep NIPD input" => "Menghapus kolom input rombel saat memulihkan data alumni.",
            "Use event delegation for btn-validasi-calon to support pagination" => "Memperbaiki tombol validasi daftar ulang di halaman 2 dst agar bisa diklik.",
            "Create Update_log controller to fetch git log and render view" => "Membuat backend controller untuk halaman Update Log.",
            "Create update_log view file to show git history in modern datatable" => "Membuat antarmuka halaman log pembaruan aplikasi.",
            "Add Update Log menu item to sidebar bottom" => "Menambahkan menu navigasi Update Log di bagian terbawah sidebar.",
            "Sync missing columns from siswa to alumni and implement NISN/NIK duplicate merging" => "Menambahkan fitur sinkronisasi kolom tabel siswa ke alumni dan penggabungan otomatis data duplikat berdasarkan NISN/NIK.",
            "Implement dynamic columns and duplicate merging in Alumni_model" => "Mengimplementasikan modul sinkronisasi kolom dinamis dan merge data alumni duplikat.",
            "Implement fallback to default settings and map settings per day in generateAgenda" => "Memperbaiki kalkulasi otomatis jam mulai dan jam selesai pada agenda harian berdasarkan pengaturan jadwal.",
            "Implement smart path active menu highlighting in sidebar app.js" => "Memperbaiki highlight menu aktif di sidebar saat mengakses halaman detail perangkat pembelajaran.",
            "Enforce allowed upload types to docx and xlsx only" => "Membatasi unggahan dokumen pembelajaran hanya untuk berkas .docx dan .xlsx agar dapat terintegrasi dengan Google Docs/Sheets.",
            "Limit upload extensions to docx and xlsx in detail view config" => "Mengubah filter ekstensi dokumen di antarmuka unggah berkas agar membatasi hanya .docx dan .xlsx.",
            "Create GoogleDrive_Helper library to handle Google Drive API file upload, conversion, permissions, and deletion" => "Membuat modul library GoogleDrive_Helper untuk menangani interaksi berkas dengan Google Drive API.",
            "Store google_access_token in session upon successful Google login callback" => "Menyimpan Google Access Token ke session setelah login untuk autorisasi Google Drive.",
            "Modify Perangkat_pembelajaran controller to upload files to Google Drive when available" => "Menghubungkan unggahan dokumen perangkat pembelajaran dengan unggahan otomatis ke Google Drive.",
            "Delete Google Drive file when deleting local file in hapus_berkas" => "Menambahkan fitur penghapusan file di Google Drive secara otomatis saat berkas lokal dihapus.",
            "Add saveDriveIds and adjust deleteBerkasFile to clear drive_file_id in Perangkat_pembelajaran_model" => "Memperbarui database model untuk mencatat dan mengosongkan ID file Google Drive.",
            "Add Google Drive open/edit button in pembelajaran detail view when drive_file_id is present" => "Menambahkan tombol edit Google Docs langsung pada tabel berkas perangkat pembelajaran.",
            "Add Google Hub tab button to navigation list in profile.php view" => "Menambahkan tab Google Integrasi pada halaman profil.",
            "Insert googleIntegration tab pane in profile.php view" => "Membuat panel integrasi Google Account pada halaman profil.",
            "Add connectGoogle, disconnectGoogle, and oauth callback actions to Profile controller" => "Menambahkan endpoint otorisasi OAuth Google dan pendaftaran Audience Google Console di menu profil.",
            "Create Policies controller to render privacy policy and terms of service pages without strict login requirement" => "Membuat controller Policies untuk melayani akses halaman kebijakan dan ketentuan layanan secara publik.",
            "Create privacy policy view file linking features with MKDC application context" => "Membuat tampilan dokumen Kebijakan Privasi (Privacy Policy) yang selaras dengan fitur sekolah.",
            "Create terms of service view file linking features with MKDC application context" => "Membuat tampilan dokumen Syarat & Ketentuan Layanan (Terms of Service) yang selaras dengan fitur sekolah.",
            "Add Policy and Terms links in footer.php" => "Menyisipkan tautan Kebijakan Privasi & Syarat Layanan di bagian footer bawah aplikasi.",
            "Add Google connection status indicator beside user profile dropdown in header.php navbar" => "Menampilkan indikator status koneksi Google (Ikon & Teks Terhubung/Hubungkan ke Google) di bilah navigasi (navbar) atas.",
            "Refactor upload berkas to use per-item forms with dedicated upload buttons in detail.php" => "Mengubah tabel unggah berkas menjadi form mandiri per-item agar dapat diunggah satu per satu.",
            "Remove global form tag and global submit button from detail.php view" => "Menghapus form pembuka dan tombol simpan global karena sudah digantikan oleh form per-item.",
            "Remove global form open tag in detail.php" => "Menghapus tag HTML form pembuka global di dalam detail perangkat pembelajaran.",
            "Modify simpan_berkas in Perangkat_pembelajaran controller to support single item field uploads and handle errors" => "Menyesuaikan backend controller agar mendukung unggah berkas per-item serta menangani feedback/alert error Google Drive API secara informatif.",
            "Enforce secure HTTPS redirect URI using production domain in Profile controller google oauth flow" => "Mengatur paksa URI pengalihan Google OAuth profil agar menggunakan protokol aman HTTPS dengan domain resmi sekolah.",
            "Use same secure production domain redirect URI for code exchange in Profile controller callback" => "Menyelaraskan URI pengalihan callback untuk proses pertukaran token akses Google OAuth profil.",
            "Add Google Drive files scope to Login controller oauth flow" => "Menambahkan scope izin akses file Google Drive (drive.file) pada sistem login utama Google.",
            "Create Welcome controller to serve public landing page on root URL and bypass login redirection" => "Membuat controller Welcome untuk menyajikan landing page informasi publik di halaman utama.",
            "Create welcome.php view landing page to satisfy Google console OAuth verification guidelines" => "Membuat tampilan halaman utama (welcome landing page) publik dengan informasi aplikasi untuk kebutuhan verifikasi Google OAuth.",
            "Update default_controller to welcome in routes.php" => "Mengubah default controller rute utama aplikasi (/) ke halaman publik Welcome.",
            "Directly link Google Docs/Sheets files using dynamic editLink from Google Drive API instead of hardcoded document URL in detail.php" => "Mengatur tautan edit online agar dinamis mendeteksi tipe file (Word diarahkan ke Google Docs, Excel diarahkan ke Google Sheets).",
            "Add downloadGoogleFile function to GoogleDrive_Helper.php library" => "Menambahkan fungsi penarikan (download/export) dokumen terbaru dari Google Drive ke server lokal.",
            "Restore deleteFile method to GoogleDrive_Helper.php library" => "Memulihkan fungsi hapus file Google Drive di helper.",
            "Define unduh_berkas_url parameter in Perangkat_pembelajaran controller detail page" => "Menyisipkan variabel URL unduh berkas dinamis pada halaman detail perangkat pembelajaran.",
            "Create unduh_berkas action in Perangkat_pembelajaran controller" => "Membuat rute unduh berkas dinamis untuk mendownload versi dokumen terbaru dari Google Drive sebelum diteruskan ke browser pengguna.",
            "Route download button in detail.php to the new unduh_berkas action" => "Mengarahkan tombol download berkas di antarmuka agar memicu penarikan berkas terbaru dari Google Drive."
        ];

        if (isset($mappings[$desc])) {
            return $mappings[$desc];
        }

        // Fuzzy matches & translations
        $words = [
            'Add ' => 'Menambahkan ',
            'Fix ' => 'Memperbaiki ',
            'Update ' => 'Memperbarui ',
            'Remove ' => 'Menghapus ',
            'Delete ' => 'Menghapus ',
            'Create ' => 'Membuat ',
            'Implement ' => 'Mengimplementasikan ',
            'to ' => 'pada ',
            'in ' => 'di ',
            'method' => 'metode',
            'query' => 'kueri',
            'view' => 'tampilan'
        ];
        
        $translated = str_replace(array_keys($words), array_values($words), $desc);
        return $translated;
    }
}
