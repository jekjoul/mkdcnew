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
            "Route download button in detail.php to the new unduh_berkas action" => "Mengarahkan tombol download berkas di antarmuka agar memicu penarikan berkas terbaru dari Google Drive.",
            "Separate Download and View buttons and implement Ajax Document Preview modal in detail.php" => "Memecah tombol unduh dan lihat dokumen, serta mengimplementasikan Ajax dokumen preview.",
            "Add Document Preview Modal HTML and Ajax handler in detail.php" => "Menambahkan elemen modal pratinjau (preview iframe) dokumen Google Drive terintegrasi di halaman detail perangkat pembelajaran.",
            "Filter getAllPembelajaran to fetch only active pembelajaran entries having count_mapel > 0" => "Menyaring kelas/rombel pada penyusunan jadwal agar hanya memunculkan kelas yang memiliki mata pelajaran aktif.",
            "Add Google AI API Key form to api_settings view" => "Menyediakan input konfigurasi Google AI API Key di halaman pengaturan API.",
            "Update Settings controller to save google_ai_api_key in settings database" => "Menyimpan konfigurasi Google AI API Key ke database.",
            "Create GoogleAI_Helper library to communicate with Gemini API and fetch structured curriculum agenda" => "Membuat pustaka library GoogleAI_Helper untuk komunikasi dengan REST API Gemini.",
            "Add generate_agenda_ai_url in Perangkat_pembelajaran controller detail page" => "Menyediakan rute aksi pemicu AI di halaman detail pembelajaran.",
            "Add generateAgendaAI method in Perangkat_pembelajaran_model" => "Menambahkan model pengisian materi dan kegiatan agenda harian yang dirumuskan oleh AI.",
            "Add generate_agenda_ai action to Perangkat_pembelajaran controller" => "Menambahkan endpoint controller untuk memproses silabus agenda harian via Google AI.",
            "Add Google AI Agenda generator buttons in empty and populated agenda states in detail.php" => "Menampilkan tombol 'Generate dengan Google AI' pada antarmuka manajemen agenda harian guru.",
            "Switch model name to gemini-1.5-flash-latest to support free tier API version" => "Mengubah target model Gemini ke gemini-2.0-flash demi keandalan kueri versi gratis (free tier).",
            "Add Google AI Model selection dropdown to api_settings view" => "Menambahkan dropdown pemilihan versi model Gemini AI di halaman pengaturan API.",
            "Save google_ai_model settings via Settings controller post handler" => "Menyimpan pengaturan dropdown versi model Gemini AI ke database.",
            "Dynamically load google_ai_model in GoogleAI_Helper constructor" => "Memuat versi model Gemini AI secara dinamis berdasarkan konfigurasi pengaturan.",
            "Add google_ai_model config to update_production.sql migration script" => "Menyertakan migrasi query sql insert ignore google_ai_model untuk pembaruan server produksi.",
            "Fix schedule matching logic and populate exact class times in generateAgendaAI model" => "Memperbaiki pencarian jadwal pelajaran dan kalkulasi jam pelajaran di generator agenda AI.",
            "Sync istirahat_json property decoding key in generateAgendaAI to match setlah_jp_ke key name" => "Menyelaraskan kunci pembacaan data JSON istirahat pada penentuan jadwal agar tidak macet.",
            "Sync istirahat_json key check in main generateAgenda model" => "Menyelaraskan kunci pembacaan data JSON istirahat di fungsi utama jadwal pelajaran.",
            "Escape HTML content for materi and kegiatan display to prevent code execution XSS" => "Melakukan proteksi (escape HTML) materi agenda harian agar coding tidak dieksekusi oleh web browser.",
            "Load text helper in Perangkat_pembelajaran constructor to fix undefined character_limiter" => "Memuat helper teks bawaan CodeIgniter untuk mendukung pembatasan panjang kalimat materi agenda harian.",
            "Add Modul Ajar navigation tab to tab headers in detail.php view" => "Menambahkan Tab Modul Ajar / RPP terpisah di halaman detail perangkat pembelajaran.",
            "Remove single file modul_ajar from files_config array in detail.php view" => "Memindahkan file modul ajar dari berkas perangkat tunggal ke sistem multi-file.",
            "Add TAB 2: Modul Ajar / RPP View and Forms to detail.php view" => "Menyediakan antarmuka pengelolaan multifile Modul Ajar & formulir generator AI.",
            "Add Modul Ajar RPP CRUD methods to Perangkat_pembelajaran_model" => "Menambahkan model penanganan data CRUD modul ajar / RPP multifile.",
            "Pass modul_ajar list and CRUD URLs to detail view in Perangkat_pembelajaran controller" => "Mengirimkan data modul ajar dan rute aksi detail ke antarmuka guru.",
            "Implement Modul Ajar upload, delete, download, and AI generation endpoints in Perangkat_pembelajaran controller" => "Mengimplementasikan fitur unggah, hapus, unduh, serta pembuatan RPP otomatis berbasis AI (Gemini) yang langsung dikonversi menjadi berkas DOCX di Google Drive.",
            "Handle activeTab = 'modul' parameter in URL parameters Javascript" => "Menambahkan navigasi JavaScript untuk menjaga fokus pada tab Modul Ajar pasca aksi.",
            "Add table creation script for perangkat_pembelajaran_modul_ajar to update_production.sql" => "Menambahkan skrip migrasi database tabel modul ajar multifile untuk server produksi.",
            "Add kurikulum select dropdown to v_tahun_pelajaran_form.php" => "Menambahkan pilihan input kurikulum di formulir tahun pelajaran.",
            "Add Kurikulum column to v_tahun_pelajaran_list.php table view" => "Menambahkan kolom kurikulum di tabel daftar master tahun pelajaran.",
            "Capture and save 'kurikulum' column input in Master controller tahunPelajaran actions" => "Menyimpan pilihan kurikulum (K-13 atau Kurikulum Merdeka) di database tahun pelajaran.",
            "Add alter table kurikulum column migration query to update_production.sql" => "Menambahkan query migrasi kolom kurikulum ke database produksi.",
            "Add Kurikulum column to master tahun_pelajaran/list.php view" => "Menampilkan data kurikulum di menu navigasi utama Tahun Pelajaran.",
            "Capture and save 'kurikulum' column input in Tahun_pelajaran controller actions" => "Menyimpan konfigurasi kurikulum pada controller utama Tahun Pelajaran.",
            "Add kurikulum select dropdown input to tahun_pelajaran/form.php view" => "Menambahkan dropdown pemilihan kurikulum di form input utama Tahun Pelajaran.",
            "Add tp.kurikulum to db select in getPembelajaranMapel model query" => "Menyertakan kurikulum aktif tahun pelajaran ke dalam model data perangkat pembelajaran.",
            "Implement generate_berkas_ai endpoint in Perangkat_pembelajaran controller for sequential docx/xlsx file generation" => "Menyediakan endpoint untuk membuat berkas perangkat pembelajaran secara otomatis via Google AI.",
            "Implement sequential AI generation buttons with lock/unlock state based on previous file upload in detail.php view" => "Mengunci/membuka akses tombol 'Generate via AI' secara berurutan berdasarkan kelengkapan berkas sebelumnya (CP -> TP -> ATP -> Kisi/Soal STS/SAS).",
            "Show warning feedback if Google Drive upload fails during AI generation" => "Menampilkan pesan peringatan jika berkas perangkat AI gagal disinkronkan ke Google Drive.",
            "Show detailed error feedback in generate_modul_ai when Google Drive upload fails" => "Menampilkan pesan peringatan rinci jika berkas modul ajar AI gagal disinkronkan ke Google Drive.",
            "Fix getPembelajaranMapel query return type handling in model" => "Memperbaiki penanganan tipe data kembalian kueri pembelajaran mapel di model.",
            "Inject previous file reference content into sequential AI prompt context" => "Menyisipkan isi berkas sebelumnya sebagai referensi wajib prompt AI agar isinya berkesinambungan.",
            "Add loading spinner modal markup and handle form submit trigger-ai in detail.php" => "Menambahkan modal loading spinner global dan penanganan aksi pemicu progres AI di JavaScript.",
            "Add trigger-ai class and data-label attribute to AI generation buttons in detail.php view" => "Menambahkan selektor pemicu progress loader pada tombol pemicu berkas AI.",
            "Add trigger-ai class to Modul Ajar AI generator button in detail.php" => "Menambahkan selektor pemicu progress loader pada tombol modul ajar AI.",
            "Add trigger-ai class to Agenda AI generator buttons in detail.php" => "Menambahkan selektor pemicu progress loader pada tombol agenda AI.",
            "Update saveBerkas model function to return boolean status indicating DB insert/update success" => "Mengembalikan status boolean pada method penyimpanan berkas model perangkat.",
            "Add database insert/update confirmation logic and notification alert in generate_berkas_ai" => "Menambahkan penangkapan status kueri SQL dan pelaporan alert error jika database menolak input data.",
            "Create parent folder 'MKDC - Berkas Pembelajaran' on Google Drive if it doesn't exist and upload files into it" => "Mencari atau membuat folder 'MKDC - Berkas Pembelajaran' di Google Drive secara otomatis untuk meletakkan berkas unggahan.",
            "Add custom form properties and specific layout structure to AI prompt for Kisi-kisi generation" => "Menyematkan parameter kisi-kisi (jumlah soal, bentuk soal, alokasi waktu) dan struktur 10 kolom persis ke dalam instruksi AI.",
            "Apply Word landscape CSS styling conditionally based on layout type in generate_berkas_ai" => "Menerapkan format halaman mendatar (landscape) pada CSS berkas Word kisi-kisi hasil AI.",
            "Differentiate Kisi-kisi AI buttons to trigger configuration modal in detail.php" => "Membuat tombol generate AI khusus untuk kisi-kisi agar memicu dialog parameter input.",
            "Add modal dialog and Javascript click triggers for Kisi-kisi AI parameter configuration in detail.php" => "Menambahkan dialog konfigurasi input jumlah soal, bentuk, alokasi waktu, serta dynamic field toggle di detail.php.",
            "Differentiate Soal AI prompt to force full questions matching previous Kisi-kisi reference content in generate_berkas_ai" => "Membuat prompt naskah soal AI terstruktur lengkap tanpa lompatan nomor berdasarkan berkas kisi-kisi sebelumnya.",
            "Make AI agenda generation prompt more creative, interactive and include YouTube examples and application context" => "Mengonfigurasi prompt agenda AI agar menyajikan rencana pertemuan yang lebih kreatif, interaktif, aplikatif, serta menyertakan referensi video YouTube.",
            "Fix Preview document modal click trigger in list modul ajar and fix layout of modul ajar table to avoid overflow-x" => "Memperbaiki pemicu tombol preview berkas modul ajar dan merapikan layout lebar kolom tabel agar tidak meluap.",
            "Convert Modul Ajar RPP list table into a DataTable to allow clean sorting, pagination, and prevent overflow-x layout break" => "Mengubah tabel modul ajar manual & AI menjadi DataTable agar memiliki fitur sorting, pencarian, paginasi, dan lebar kolom yang proporsional.",
            "Initialize DataTable for modulAjarTable in JavaScript on detail.php document.ready" => "Menginisialisasi JQuery DataTable untuk tabel modul ajar pada javascript dokumen.",
            "Update generate_agenda_ai controller logic to extract and inject Modul Ajar (RPP) labels as reference context to AI prompt" => "Mengambil daftar topik modul ajar (RPP) yang telah dibuat untuk dijadikan referensi wajib dalam menyelaraskan rencana pertemuan harian.",
            "Update GoogleAI_Helper.php generateAgenda signature and insert Modul Ajar context into agenda generation prompt" => "Menambahkan parameter opsional modulListStr pada method generateAgenda serta menyuntikkan instruksi sinkronisasi silabus dengan topik RPP.",
            "Combine 'Jadwal Pelajaran' and 'Jadwal Tidak Aktif' into a single sidebar dropdown menu" => "Menggabungkan menu samping (sidebar) 'Jadwal Pelajaran' dan 'Jadwal Tidak Aktif' ke dalam satu menu dropdown Jadwal Pelajaran agar lebih teratur.",
            "Adjust active page JQuery path matching logic in app.js to accurately match and differentiate specific subpaths" => "Memperbaiki kalkulasi penentuan menu aktif (active-page) pada sidebar agar tidak terjadi tabrakan penandaan aktif antara halaman 'Calon Siswa' dengan halaman 'Validasi Daftar Ulang'.",
            "Refine sidebar active menu path matching to prevent root dashboard path and partial segments from clashing" => "Membatasi pencocokan otomatis menu aktif pada tautan root/dashboard agar tidak ikut menyala ketika halaman sub-fitur lainnya sedang dimuat."
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
