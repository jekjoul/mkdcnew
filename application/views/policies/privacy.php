<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo $app->site_title; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/bootstrap.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333f48;
            line-height: 1.6;
        }
        .policy-container {
            max-width: 800px;
            margin: 40px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        h1 {
            color: #1e3a8a;
            font-weight: 700;
            margin-bottom: 24px;
            font-size: 28px;
        }
        h2 {
            color: #2563eb;
            font-weight: 600;
            margin-top: 32px;
            margin-bottom: 16px;
            font-size: 20px;
        }
        p, li {
            font-size: 15px;
            color: #4b5563;
        }
        ul {
            padding-left: 20px;
            margin-bottom: 16px;
        }
        li {
            margin-bottom: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 13px;
            color: #9ca3af;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #2563eb;
            font-weight: 500;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .back-link:hover {
            color: #1d4ed8;
        }
        .highlight-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="policy-container">
        <a href="javascript:history.back()" class="back-link">
            ← Kembali
        </a>
        
        <h1>Kebijakan Privasi (Privacy Policy)</h1>
        <p class="text-muted text-xs">Terakhir Diperbarui: 8 Juli 2026</p>
        
        <div class="highlight-box">
            <p class="mb-0"><strong>Pemberitahuan Penting:</strong> Aplikasi ini dirancang khusus untuk operasional pendidikan di lingkungan <strong><?php echo $app->site_title; ?></strong>. Kebijakan ini menjelaskan bagaimana data siswa, alumni, guru (PTK), dan file pembelajaran Anda dikelola di platform kami.</p>
        </div>

        <h2>1. Informasi yang Kami Kumpulkan</h2>
        <p>Kami mengumpulkan data yang diperlukan untuk kepentingan administrasi akademik dan pengelolaan pembelajaran:</p>
        <ul>
            <li><strong>Data Siswa & Alumni:</strong> Nama lengkap, NISN, NIK, Rombel, Alamat, data orang tua (nama ayah/ibu), status keaktifan, berkas alumni, serta riwayat kelulusan kolektif.</li>
            <li><strong>Data Pendidik (PTK/Guru):</strong> Nama, Kontak/No. HP, Alamat, email Google yang digunakan untuk otorisasi, dan data aktivitas mengajar.</li>
            <li><strong>Berkas Pembelajaran:</strong> Dokumen silabus, CP, TP, ATP, Modul Ajar/RPP, Kisi-kisi, dan Soal STS/SAS yang diunggah oleh guru.</li>
        </ul>

        <h2>2. Penggunaan Integrasi Google Drive API</h2>
        <p>Aplikasi ini mengintegrasikan layanan pihak ketiga, yaitu Google Drive API, untuk memfasilitasi penyimpanan dan pengeditan daring:</p>
        <ul>
            <li><strong>Unggah & Simpan:</strong> Saat dokumen pembelajaran diunggah (.docx/.xlsx), sistem akan mengunggah salinan secara otomatis ke penyimpanan Google Drive Anda.</li>
            <li><strong>Edit Online:</strong> Layanan ini memungkinkan pengeditan dokumen pembelajaran secara langsung di Google Docs/Sheets tanpa proses unduh & unggah ulang secara manual.</li>
            <li><strong>Google Console Audience:</strong> Menghubungkan profil pengguna ke Google Console mendaftarkan email Anda sebagai Audience yang sah untuk mengakses Google Drive API secara aman dan terbatas.</li>
        </ul>

        <h2>3. Pengamanan Data & Pencegahan Duplikasi</h2>
        <ul>
            <li>Data alumni yang dikembalikan menjadi siswa aktif secara otomatis diverifikasi berdasarkan NIK & NISN. Jika ditemukan kecocokan, sistem melakukan <em>merging</em> data otomatis untuk menghindari duplikasi database.</li>
            <li>Kami mengamankan data pembelajaran tidak aktif dari manipulasi atau penyuntingan yang tidak sah melalui enkripsi/hak akses berjenjang.</li>
        </ul>

        <h2>4. Penghapusan dan Penyimpanan Data</h2>
        <p>Data siswa aktif, siswa tidak aktif, dan alumni disimpan selama dibutuhkan oleh instansi pendidikan. Berkas pembelajaran yang dihapus dari aplikasi secara otomatis juga akan dihapus permanen dari direktori penyimpanan Google Drive guna menjaga keamanan kapasitas penyimpanan Anda.</p>

        <div class="footer">
            <p>© 2026 <?php echo $app->site_title; ?>. Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </div>
</div>

</body>
</html>
