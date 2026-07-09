<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi & Integrasi Pembelajaran - <?php echo $site_title; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $assets ?>css/lib/bootstrap.min.css">
    <!-- Iconify Icons -->
    <script src="<?php echo $assets ?>js/lib/iconify-icon.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #334155;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .navbar-brand img {
            max-height: 48px;
        }
        .hero-section {
            padding: 80px 0;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }
        .hero-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .hero-title {
            color: #1e3a8a;
            font-weight: 800;
            font-size: 2.5rem;
            line-height: 1.2;
        }
        .hero-subtitle {
            color: #475569;
            font-size: 1.1rem;
            margin-top: 16px;
            margin-bottom: 32px;
        }
        .feature-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            align-items: flex-start;
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .feature-text h5 {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            font-size: 1rem;
        }
        .feature-text p {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0;
        }
        .btn-login {
            background: #2563eb;
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-login:hover {
            background: #1d4ed8;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }
        footer {
            background: #ffffff;
            padding: 24px 0;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Header / Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-transparent pt-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="<?php echo $assets ?>images/logodc.png" alt="Logo <?php echo $site_title; ?>">
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <h1 class="hero-title">Sistem Data Center & Integrasi Pembelajaran</h1>
                <p class="hero-subtitle">Platform operasional akademik, manajemen data siswa, alumni, pendidik (PTK), dan sinkronisasi otomatis berkas kurikulum ke Google Drive.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo $login_url; ?>" class="btn-login">
                        <iconify-icon icon="lucide:log-in"></iconify-icon> Masuk ke Aplikasi
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-card">
                    <h3 class="mb-4 fw-bold text-primary-900" style="font-size: 1.5rem;">Fitur Utama Platform</h3>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <iconify-icon icon="logos:google-drive"></iconify-icon>
                        </div>
                        <div class="feature-text">
                            <h5>Sinkronisasi Google Drive & Docs</h5>
                            <p>Unggah perangkat pembelajaran dan edit secara langsung melalui kolaborasi Google Docs & Sheets tanpa perlu re-upload.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <iconify-icon icon="lucide:users-2"></iconify-icon>
                        </div>
                        <div class="feature-text">
                            <h5>Manajemen Siswa & Alumni</h5>
                            <p>Data administrasi komprehensif mulai dari pendaftaran, mutasi, pelulusan, hingga pencarian alumni terpadu.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <iconify-icon icon="lucide:shield-check"></iconify-icon>
                        </div>
                        <div class="feature-text">
                            <h5>Otorisasi Keamanan Google Console</h5>
                            <p>Sistem audit log internal untuk keamanan data dan pendaftaran peninjauan konsol otentikasi Google.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row align-items-center justify-content-between gy-3">
            <div class="col-md-6 text-md-start text-center">
                <p class="mb-0 text-sm text-secondary-500">© 2026 <?php echo $site_title; ?>. Seluruh Hak Cipta Dilindungi.</p>
            </div>
            <div class="col-md-6 text-md-end text-center d-flex justify-content-center justify-content-md-end gap-3">
                <a href="<?php echo $privacy_url; ?>">Kebijakan Privasi</a>
                <span class="text-secondary-300">|</span>
                <a href="<?php echo $terms_url; ?>">Syarat Layanan</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
