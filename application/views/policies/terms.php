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
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
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
        
        <h1>Syarat & Ketentuan Layanan (Terms of Service)</h1>
        <p class="text-muted text-xs">Terakhir Diperbarui: 8 Juli 2026</p>
        
        <div class="highlight-box">
            <p class="mb-0"><strong>Ketentuan Penggunaan:</strong> Dengan mengakses sistem informasi akademik dan pembelajaran <strong><?php echo $app->site_title; ?></strong>, Anda menyetujui seluruh syarat dan ketentuan penggunaan layanan operasional internal sekolah ini.</p>
        </div>

        <h2>1. Akun dan Hak Akses Pengguna</h2>
        <ul>
            <li>Akses ke dalam sistem hanya diberikan kepada staf administrator, guru (PTK), siswa aktif, serta alumni yang sah dari sekolah.</li>
            <li>Pengguna bertanggung jawab penuh atas kerahasiaan kata sandi (password) dan aktivitas yang terjadi di bawah akun mereka.</li>
            <li>Guru yang menggunakan fitur integrasi Google wajib menjaga keamanan akun email Google yang ditautkan ke sistem.</li>
        </ul>

        <h2>2. Ketentuan Unggah & Pengeditan Dokumen</h2>
        <ul>
            <li>Guru hanya diperkenankan mengunggah berkas perangkat pembelajaran (CP, TP, ATP, Modul Ajar, Soal STS/SAS) dalam format <strong>DOCX (Word)</strong> dan <strong>XLSX (Excel)</strong> untuk memastikan kompatibilitas edit langsung di Google Drive.</li>
            <li>Unggahan berkas berupa konten ilegal, berbahaya, melanggar hak cipta, atau di luar kebutuhan operasional pendidikan dilarang keras.</li>
        </ul>

        <h2>3. Pengelolaan dan Administrasi Siswa</h2>
        <ul>
            <li>Fitur pemulihan data alumni menjadi siswa aktif akan secara otomatis mendeteksi data ganda (duplikat) di sistem. Pengguna setuju bahwa duplikasi data lama akan digabungkan (merge) demi keakuratan basis data sekolah.</li>
            <li>Proses pelulusan kolektif per-rombel bersifat permanen dan memindahkan siswa ke arsip alumni. Penyuntingan kembali kelas pembelajaran nonaktif akan dibatasi demi integritas histori data raport.</li>
        </ul>

        <h2>4. Batasan Tanggung Jawab</h2>
        <p>Aplikasi ini disediakan "sebagaimana adanya" untuk menunjang aktivitas pembelajaran. Sekolah tidak bertanggung jawab atas kegagalan koneksi pihak ketiga (Google Drive API), kehilangan data akibat salah hapus oleh pengguna, atau gangguan koneksi lokal server sekolah.</p>

        <div class="footer">
            <p>© 2026 <?php echo $app->site_title; ?>. Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </div>
</div>

</body>
</html>
