<?php
header('Content-Type: text/plain; charset=utf-8');

$mysqli = new mysqli('localhost', 'root', '', 'mkdcnew');

if ($mysqli->connect_error) {
    die("Koneksi Database Gagal: " . $mysqli->connect_error);
}

echo "=== MEMULAI SEED SAMPLE DATA AGENDA INFORMATIKA KELAS 7 SEMESTER 1 TAHUN LALU ===\n\n";

// 1. Dapatkan atau buat Tahun Pelajaran Lalu (2025/2026 Semester 1)
$res_tp = $mysqli->query("SELECT * FROM `pembelajaran_tahun_pelajaran` WHERE (`tahun_pelajaran` = '2025/2026' OR `tahun_pelajaran` = '2024/2025') AND (`semester` = '1' OR `semester` = 'Ganjil') LIMIT 1");
if ($res_tp && $res_tp->num_rows > 0) {
    $tp_row = $res_tp->fetch_assoc();
    $id_tp_lalu = $tp_row['id_tahun_pelajaran'];
} else {
    $mysqli->query("INSERT INTO `pembelajaran_tahun_pelajaran` (`tahun_pelajaran`, `semester`, `status`) VALUES ('2025/2026', '1', 'Tidak Aktif')");
    $id_tp_lalu = $mysqli->insert_id;
}
echo "Tahun Pelajaran Lalu ID: {$id_tp_lalu}\n";

// 2. Dapatkan Mapel Informatika
$res_mapel = $mysqli->query("SELECT * FROM `mapel` WHERE `nama_mapel` LIKE '%Informatika%' LIMIT 1");
if ($res_mapel && $res_mapel->num_rows > 0) {
    $mapel_row = $res_mapel->fetch_assoc();
    $id_mapel = $mapel_row['id_mapel'];
    $nama_mapel = $mapel_row['nama_mapel'];
} else {
    $res_mapel = $mysqli->query("SELECT * FROM `mapel` LIMIT 1");
    $mapel_row = $res_mapel->fetch_assoc();
    $id_mapel = $mapel_row ? $mapel_row['id_mapel'] : 1;
    $nama_mapel = $mapel_row ? $mapel_row['nama_mapel'] : 'Informatika';
}
echo "Mapel: {$nama_mapel} (ID: {$id_mapel})\n";

// 3. Dapatkan Tingkat Sekolah Kelas 7
$res_tingkat = $mysqli->query("SELECT * FROM `tingkat_sekolah` WHERE `nama_tingkat` LIKE '%7%' LIMIT 1");
if ($res_tingkat && $res_tingkat->num_rows > 0) {
    $tingkat_row = $res_tingkat->fetch_assoc();
    $id_tingkat = $tingkat_row['id_tingkat_sekolah'];
    $nama_tingkat = $tingkat_row['nama_tingkat'];
} else {
    $res_tingkat = $mysqli->query("SELECT * FROM `tingkat_sekolah` LIMIT 1");
    $tingkat_row = $res_tingkat->fetch_assoc();
    $id_tingkat = $tingkat_row ? $tingkat_row['id_tingkat_sekolah'] : 1;
    $nama_tingkat = $tingkat_row ? $tingkat_row['nama_tingkat'] : 'Kelas 7';
}
echo "Tingkat: {$nama_tingkat} (ID: {$id_tingkat})\n";

// 4. Dapatkan Rombel Kelas 7
$res_rombel = $mysqli->query("SELECT * FROM `rombel` WHERE `id_tingkat_sekolah` = '{$id_tingkat}' LIMIT 1");
if ($res_rombel && $res_rombel->num_rows > 0) {
    $rombel_row = $res_rombel->fetch_assoc();
    $id_rombel = $rombel_row['id_rombel'];
    $nama_rombel = $rombel_row['nama_rombel'];
} else {
    $res_rombel = $mysqli->query("SELECT * FROM `rombel` LIMIT 1");
    $rombel_row = $res_rombel->fetch_assoc();
    $id_rombel = $rombel_row ? $rombel_row['id_rombel'] : 1;
    $nama_rombel = $rombel_row ? $rombel_row['nama_rombel'] : '7A';
}
echo "Rombel: {$nama_rombel} (ID: {$id_rombel})\n";

// 5. Dapatkan PTK / Guru
$res_ptk = $mysqli->query("SELECT * FROM `ptk` LIMIT 1");
$ptk_row = $res_ptk ? $res_ptk->fetch_assoc() : null;
$id_ptk = $ptk_row ? $ptk_row['id_ptk'] : 1;
$nama_ptk = $ptk_row ? $ptk_row['nama_ptk'] : 'Guru Pengampu';
echo "Guru: {$nama_ptk} (ID: {$id_ptk})\n";

// 6. Buat record pembelajaran & pembelajaran_mapel untuk Tahun Lalu
$res_pemb = $mysqli->query("SELECT * FROM `pembelajaran` WHERE `id_tahun_pelajaran` = '{$id_tp_lalu}' AND `id_rombel` = '{$id_rombel}' LIMIT 1");
if ($res_pemb && $res_pemb->num_rows > 0) {
    $pemb_row = $res_pemb->fetch_assoc();
    $id_pembelajaran = $pemb_row['id_pembelajaran'];
} else {
    $mysqli->query("INSERT INTO `pembelajaran` (`id_tahun_pelajaran`, `id_rombel`, `id_tingkat_sekolah`, `status`) VALUES ('{$id_tp_lalu}', '{$id_rombel}', '{$id_tingkat}', 'Aktif')");
    $id_pembelajaran = $mysqli->insert_id;
}

$res_pm = $mysqli->query("SELECT * FROM `pembelajaran_mapel` WHERE `id_pembelajaran` = '{$id_pembelajaran}' AND `id_mapel` = '{$id_mapel}' LIMIT 1");
$judul_sample = "Agenda Master Informatika Kelas 7 Semester 1 (2025/2026)";

if ($res_pm && $res_pm->num_rows > 0) {
    $pm_row = $res_pm->fetch_assoc();
    $id_pm = $pm_row['id_pembelajaran_mapel'];
    $stmt = $mysqli->prepare("UPDATE `pembelajaran_mapel` SET `judul_agenda` = ? WHERE `id_pembelajaran_mapel` = ?");
    $stmt->bind_param("si", $judul_sample, $id_pm);
    $stmt->execute();
} else {
    $stmt = $mysqli->prepare("INSERT INTO `pembelajaran_mapel` (`id_pembelajaran`, `id_mapel`, `id_ptk`, `jumlah_jam`, `judul_agenda`) VALUES (?, ?, ?, 2, ?)");
    $stmt->bind_param("iiis", $id_pembelajaran, $id_mapel, $id_ptk, $judul_sample);
    $stmt->execute();
    $id_pm = $mysqli->insert_id;
}
echo "Pembelajaran Mapel ID Sample: {$id_pm}\n";

// 7. Bersihkan agenda sampel lama di pembelajaran_mapel ini
$mysqli->query("DELETE FROM `agenda_pembelajaran` WHERE `id_pembelajaran_mapel` = '{$id_pm}'");

// 8. Buat 8 Contoh Pertemuan Agenda Pembelajaran Lengkap dengan Berkas & Link Media
$sample_agendas = [
    [
        'pertemuan_ke' => 1,
        'hari' => 'Senin',
        'tanggal' => '2025-07-21',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Pengantar Perangkat Keras dan Komponen Utama Komputer (CPU, RAM, Storage, Input/Output)',
        'kegiatan' => 'Diskusi kelompok mengamati komponen fisik komputer di laboratorium komputer dan mengidentifikasi fungsi masing-masing komponen.',
        'status' => 'Terlaksana',
        'catatan' => 'Murid sangat antusias saat praktik membongkar cashing CPU sampel.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Modul 1 - Hardware Komputer.pdf',
                'file_name' => 'sample_modul_hardware.pdf'
            ],
            [
                'type' => 'link',
                'title' => 'Slide Presentasi Google Drive (Hardware)',
                'url' => 'https://drive.google.com/file/d/sample_hardware_slides/view'
            ],
            [
                'type' => 'link',
                'title' => 'Video YouTube: Cara Kerja Komputer & CPU',
                'url' => 'https://www.youtube.com/watch?v=sample_hardware_vid'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 2,
        'hari' => 'Senin',
        'tanggal' => '2025-07-28',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Sistem Operasi Komputer & Pengelolaan File/Folder (File Management & Directory Structure)',
        'kegiatan' => 'Praktik mandiri membuat struktur direktori folder tugas sekolah, rename file, dan ekstrak berkas ZIP.',
        'status' => 'Terlaksana',
        'catatan' => 'Beberapa murid masih belum terbiasa dengan direktori hirarki Windows Explorer.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Panduan Manajemen File & Folder.docx',
                'file_name' => 'sample_panduan_file.docx'
            ],
            [
                'type' => 'link',
                'title' => 'Link Latihan Online Struktur File',
                'url' => 'https://drive.google.com/drive/folders/sample_folder_exercise'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 3,
        'hari' => 'Senin',
        'tanggal' => '2025-08-04',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Aplikasi Pengolah Kata (Word Processing) - Formatting Dokumen, Paragraf, & Laporan',
        'kegiatan' => 'Murid mengetik cerpen pendek dan mengaplikasikan font style, heading, margin, serta membuat daftar isi otomatis.',
        'status' => 'Terlaksana',
        'catatan' => 'KBM berjalan lancar.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Lembar Kerja Murid - Word Processing.pdf',
                'file_name' => 'sample_lkm_word.pdf'
            ],
            [
                'type' => 'link',
                'title' => 'Video Tutorial Mengetik Rapi & Daftar Isi Otomatis',
                'url' => 'https://www.youtube.com/watch?v=sample_word_tutorial'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 4,
        'hari' => 'Senin',
        'tanggal' => '2025-08-11',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Aplikasi Pengolah Lembar Kerja (Spreadsheet) - Formulas, SUM, AVERAGE, MIN, MAX',
        'kegiatan' => 'Praktik menghitung nilai rata-rata laporan keuangan sederhana dan nilai rapor menggunakan Microsoft Excel / Google Sheets.',
        'status' => 'Terlaksana',
        'catatan' => 'Semua murid berhasil membuat formula SUM dan AVERAGE.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Template Spreadsheet Latihan Nilai.xlsx',
                'file_name' => 'sample_template_excel.xlsx'
            ],
            [
                'type' => 'link',
                'title' => 'Google Drive Folder Lembar Kerja Excel',
                'url' => 'https://drive.google.com/drive/folders/sample_excel_materials'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 5,
        'hari' => 'Senin',
        'tanggal' => '2025-08-18',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Berpikir Komputasional (Computational Thinking) - Abstraksi, Dekomposisi, & Pola Problem Solving',
        'kegiatan' => 'Studi kasus menyelesaikan teka-teki logika Bebras dan simulasi pembagian tugas projek.',
        'status' => 'Terlaksana',
        'catatan' => 'Latihan Bebras sangat diminati murid.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Kumpulan Soal Bebras CT Kelas 7.pdf',
                'file_name' => 'sample_soal_bebras.pdf'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 6,
        'hari' => 'Senin',
        'tanggal' => '2025-08-25',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Algoritma Pemrograman Visual Scratch - Elemen Sprite, Stage, & Logic Loop',
        'kegiatan' => 'Praktik membuat animasi objek bergerak dan percakapan interaktif menggunakan Scratch Block.',
        'status' => 'Terlaksana',
        'catatan' => 'Tugas mandiri selesai tepat waktu.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Panduan Scratch 3.0 Bahasa Indonesia.pdf',
                'file_name' => 'sample_scratch_guide.pdf'
            ],
            [
                'type' => 'link',
                'title' => 'Link Projek Scratch Contoh Animasi',
                'url' => 'https://scratch.mit.edu/projects/sample_animation'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 7,
        'hari' => 'Senin',
        'tanggal' => '2025-09-01',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Jaringan Komputer dan Internet - Perangkat Jaringan (Router, Switch) & Konsep IP Address',
        'kegiatan' => 'Simulasi koneksi wifi dan pengenalan keamanan browser saat melakukan pencarian di internet.',
        'status' => 'Terlaksana',
        'catatan' => 'Penjelasan IP address dianalogikan dengan nomor rumah murid.',
        'media_files' => json_encode([
            [
                'type' => 'file',
                'title' => 'Modul Jaringan Komputer Dasar.pdf',
                'file_name' => 'sample_modul_jaringan.pdf'
            ]
        ])
    ],
    [
        'pertemuan_ke' => 8,
        'hari' => 'Senin',
        'tanggal' => '2025-09-08',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:35',
        'jumlah_jam' => 2,
        'materi' => 'Etika Bermedia Digital & Keamanan Data Pribadi (Cyber Security & Etika Medsos)',
        'kegiatan' => 'Diskusi interaktif tentang bahaya phishing, menjaga kerahasiaan password, dan etika bersosial media.',
        'status' => 'Terlaksana',
        'catatan' => 'Murid sepakat membuat poster kampanye etika internet.',
        'media_files' => json_encode([
            [
                'type' => 'link',
                'title' => 'Video Edukasi Cyber Security Kemdikbud',
                'url' => 'https://www.youtube.com/watch?v=sample_cybersecurity'
            ]
        ])
    ]
];

$stmt_ins = $mysqli->prepare("INSERT INTO `agenda_pembelajaran` (`id_pembelajaran_mapel`, `tanggal`, `hari`, `pertemuan_ke`, `materi`, `kegiatan`, `status`, `catatan`, `jumlah_jam`, `jam_mulai`, `jam_selesai`, `media_files`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

foreach ($sample_agendas as $ag) {
    $stmt_ins->bind_param(
        "ississssisss",
        $id_pm,
        $ag['tanggal'],
        $ag['hari'],
        $ag['pertemuan_ke'],
        $ag['materi'],
        $ag['kegiatan'],
        $ag['status'],
        $ag['catatan'],
        $ag['jumlah_jam'],
        $ag['jam_mulai'],
        $ag['jam_selesai'],
        $ag['media_files']
    );
    $stmt_ins->execute();
}

echo "\nSUKSES: BERHASIL MEMASUKKAN " . count($sample_agendas) . " PERTEMUAN AGENDA INFORMATIKA KELAS 7 SEMESTER 1 TAHUN LALU!\n";
echo "Judul Agenda: {$judul_sample}\n";
echo "ID Pembelajaran Mapel Sample: {$id_pm}\n";
