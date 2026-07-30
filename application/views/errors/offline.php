<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <script>
        (function() {
            var savedTheme = localStorage.getItem("theme");
            if (savedTheme === "dark") {
                document.documentElement.setAttribute("data-theme", "dark");
            } else {
                document.documentElement.setAttribute("data-theme", "light");
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#487fff">
    <meta name="description" content="Tidak ada koneksi internet - MKDC Aplikasi Manajemen Sekolah">
    <title>MKDC | Tidak Ada Koneksi Internet</title>
    <link rel="icon" type="image/png" href="/mkdcnew/assets/images/logodc_round.png" sizes="16x16">

    <!-- Remix Icon -->
    <link rel="stylesheet" href="/mkdcnew/assets/css/remixicon.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-600: #487fff;
            --primary-700: #486cea;
            --primary-50: #E4F1FF;
            --primary-100: #BFDCFF;
            --neutral-50: #F5F6FA;
        }

        [data-theme="dark"] {
            --bg-base: #1a1d2e;
            --bg-card: #232640;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --border-color: rgba(255,255,255,0.08);
            --blob-1: rgba(72, 127, 255, 0.12);
            --blob-2: rgba(139, 92, 246, 0.08);
            --blob-3: rgba(239, 68, 68, 0.06);
        }

        [data-theme="light"] {
            --bg-base: #f0f4ff;
            --bg-card: #ffffff;
            --text-primary: #1e2a4a;
            --text-secondary: #64748b;
            --border-color: rgba(72, 127, 255, 0.12);
            --blob-1: rgba(72, 127, 255, 0.08);
            --blob-2: rgba(139, 92, 246, 0.05);
            --blob-3: rgba(239, 68, 68, 0.04);
        }

        html, body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-base);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color 0.3s ease;
        }

        /* ── Background Blobs ── */
        .bg-blobs {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: blobFloat 8s ease-in-out infinite;
        }

        .blob-1 {
            width: 600px;
            height: 600px;
            background: var(--blob-1);
            top: -150px;
            right: -100px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            background: var(--blob-2);
            bottom: -100px;
            left: -80px;
            animation-delay: 3s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: var(--blob-3);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 6s;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -20px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.95); }
        }

        /* ── Main Container ── */
        .offline-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5rem 1rem 2rem;
        }

        /* ── Card ── */
        .offline-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(72, 127, 255, 0.08),
                        0 4px 16px rgba(0,0,0,0.06);
            animation: cardEntrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Logo ── */
        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease 0.1s both;
        }

        .brand-logo img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-600);
            letter-spacing: -0.3px;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Wifi Icon ── */
        .wifi-icon-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            animation: fadeInScale 0.6s ease 0.2s both;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.7); }
            to { opacity: 1; transform: scale(1); }
        }

        .wifi-icon-bg {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.05));
            animation: pulseRing 2.5s ease-in-out infinite;
        }

        @keyframes pulseRing {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.7; }
        }

        .wifi-svg { position: relative; z-index: 1; }

        .wifi-arc {
            animation: wifiFlicker 1.8s ease-in-out infinite;
        }
        .wifi-arc-1 { animation-delay: 0s; }
        .wifi-arc-2 { animation-delay: 0.3s; }
        .wifi-arc-3 { animation-delay: 0.6s; }

        @keyframes wifiFlicker {
            0%, 100% { opacity: 0.15; }
            50% { opacity: 0.5; }
        }

        .wifi-cross-line {
            animation: crossPulse 2s ease-in-out infinite;
        }

        @keyframes crossPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ── Text ── */
        .offline-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.2;
            animation: fadeInUp 0.6s ease 0.3s both;
        }

        .offline-subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease 0.4s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Status Badges ── */
        .status-badges {
            display: flex;
            gap: 0.625rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease 0.45s both;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #b45309;
            border-color: rgba(245, 158, 11, 0.2);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: dotBlink 1.2s ease-in-out infinite;
        }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        /* ── Tips Box ── */
        .tips-box {
            background: var(--bg-base);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
            animation: fadeInUp 0.6s ease 0.5s both;
        }

        .tips-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.875rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tips-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
        }

        .tips-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .tip-icon {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(72, 127, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
            font-size: 0.7rem;
            color: var(--primary-600);
        }

        /* ── Buttons ── */
        .btn-group-offline {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease 0.55s both;
        }

        .btn-retry {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.75rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #487fff, #6b5ce7);
            color: white;
            box-shadow: 0 4px 15px rgba(72, 127, 255, 0.35);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 127, 255, 0.45);
            color: white;
        }

        .btn-primary-custom:active {
            transform: translateY(0);
        }

        .btn-primary-custom.loading .btn-icon {
            animation: spinIcon 1s linear infinite;
        }

        @keyframes spinIcon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        .btn-secondary-custom {
            background: var(--bg-base);
            color: var(--text-secondary);
            border: 1.5px solid var(--border-color);
        }

        .btn-secondary-custom:hover {
            background: rgba(72, 127, 255, 0.08);
            color: var(--primary-600);
            border-color: var(--primary-100);
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* ── Connection Bar ── */
        .connection-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            padding: 0.625rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            transition: all 0.4s ease;
        }

        .connection-bar.offline {
            background: linear-gradient(90deg, #dc2626, #ef4444);
            color: white;
            box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
        }

        .connection-bar.online {
            background: linear-gradient(90deg, #16a34a, #22c55e);
            color: white;
            box-shadow: 0 2px 10px rgba(22, 163, 74, 0.3);
            animation: slideUpOut 0.5s ease 2.5s both;
        }

        @keyframes slideUpOut {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-100%); opacity: 0; }
        }

        .status-dot-bar {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            animation: dotBlink 1s ease-in-out infinite;
        }

        /* ── Online Overlay ── */
        .online-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.95), rgba(5, 150, 105, 0.95));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .online-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .online-check {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            animation: checkBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes checkBounce {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .online-overlay p {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }

        /* ── Footer ── */
        .offline-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: var(--text-secondary);
            opacity: 0.6;
            animation: fadeIn 0.6s ease 0.7s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 0.6; }
        }

        /* ── Responsive ── */
        @media (max-width: 576px) {
            .offline-card {
                padding: 2rem 1.5rem;
                border-radius: 18px;
            }

            .offline-title {
                font-size: 1.4rem;
            }

            .wifi-icon-wrap {
                width: 100px;
                height: 100px;
            }

            .btn-group-offline {
                flex-direction: column;
            }

            .btn-retry {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- ── Status Bar ── -->
    <div class="connection-bar offline" id="connectionBar">
        <div class="status-dot-bar"></div>
        <span id="connectionBarText">Tidak ada koneksi internet</span>
    </div>

    <!-- ── Background Decorative Blobs ── -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- ── Online Transition Overlay ── -->
    <div class="online-overlay" id="onlineOverlay">
        <div class="online-check">
            <i class="ri-check-line" style="font-size: 2rem; color: white;"></i>
        </div>
        <p>Koneksi berhasil! Mengalihkan halaman...</p>
    </div>

    <!-- ── Main Content ── -->
    <main class="offline-container">
        <div class="offline-card">

            <!-- Brand Logo -->
            <div class="brand-logo">
                <img src="/mkdcnew/assets/images/logo-icon.png" alt="MKDC Logo" onerror="this.style.display='none'">
                <span class="brand-name">MKDC</span>
            </div>

            <!-- Wifi Offline SVG Icon -->
            <div class="wifi-icon-wrap">
                <div class="wifi-icon-bg"></div>
                <svg class="wifi-svg" width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Wifi Arcs -->
                    <path class="wifi-arc wifi-arc-3"
                        d="M10 28C18.2 19.2 27.2 15 35 15C42.8 15 51.8 19.2 60 28"
                        stroke="#EF4444" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <path class="wifi-arc wifi-arc-2"
                        d="M17 35C22.8 28.5 28.8 25 35 25C41.2 25 47.2 28.5 53 35"
                        stroke="#EF4444" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <path class="wifi-arc wifi-arc-1"
                        d="M24 42C27.5 37.8 31.1 35.5 35 35.5C38.9 35.5 42.5 37.8 46 42"
                        stroke="#EF4444" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <!-- Dot -->
                    <circle cx="35" cy="52" r="3.5" fill="#EF4444"/>
                    <!-- Slash Cross -->
                    <line class="wifi-cross-line" x1="14" y1="56" x2="56" y2="14"
                        stroke="#EF4444" stroke-width="3.5" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="offline-title">Tidak Ada Koneksi</h1>
            <p class="offline-subtitle">
                Perangkat kamu sedang tidak terhubung ke internet.<br>
                Periksa sambungan Wi-Fi atau data seluler kamu.
            </p>

            <!-- Status Badges -->
            <div class="status-badges">
                <span class="status-badge badge-danger">
                    <span class="badge-dot"></span>
                    Internet Terputus
                </span>
                <span class="status-badge badge-warning">
                    <span class="badge-dot" style="animation-delay: 0.4s;"></span>
                    Sesi Tidak Aktif
                </span>
            </div>

            <!-- Tips Box -->
            <div class="tips-box">
                <div class="tips-title">
                    <i class="ri-lightbulb-line"></i>
                    Langkah Pengecekan
                </div>
                <ul class="tips-list">
                    <li>
                        <span class="tip-icon"><i class="ri-wifi-line"></i></span>
                        <span>Pastikan Wi-Fi atau hotspot perangkat kamu sudah aktif dan terhubung</span>
                    </li>
                    <li>
                        <span class="tip-icon"><i class="ri-router-line"></i></span>
                        <span>Coba restart router atau modem jika menggunakan jaringan kabel</span>
                    </li>
                    <li>
                        <span class="tip-icon"><i class="ri-signal-wifi-error-line"></i></span>
                        <span>Pastikan tidak ada pemblokiran koneksi oleh firewall atau proxy</span>
                    </li>
                    <li>
                        <span class="tip-icon"><i class="ri-refresh-line"></i></span>
                        <span>Jika masalah berlanjut, hubungi administrator jaringan sekolah</span>
                    </li>
                </ul>
            </div>

            <!-- Buttons -->
            <div class="btn-group-offline">
                <button class="btn-retry btn-primary-custom" id="retryBtn" onclick="handleRetry()">
                    <i class="ri-refresh-line btn-icon" id="retryIcon"></i>
                    <span id="retryText">Coba Lagi</span>
                </button>
                <a href="javascript:history.back()" class="btn-retry btn-secondary-custom">
                    <i class="ri-arrow-left-line"></i>
                    Kembali
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div class="offline-footer">
            &copy; <?php echo date('Y'); ?> MKDC &mdash; Aplikasi Manajemen Data Sekolah
        </div>
    </main>

    <script>
        // ── Connection Monitor ────────────────────────────────────────────
        const connectionBar     = document.getElementById('connectionBar');
        const connectionBarText = document.getElementById('connectionBarText');
        const onlineOverlay     = document.getElementById('onlineOverlay');
        const retryBtn          = document.getElementById('retryBtn');
        const retryText         = document.getElementById('retryText');

        let lastStatus = 'offline';

        function handleOnline() {
            if (lastStatus === 'online') return;
            lastStatus = 'online';

            // Update bar → hijau
            connectionBar.classList.remove('offline');
            connectionBar.classList.add('online');
            connectionBarText.textContent = 'Koneksi pulih! Mengalihkan halaman...';

            // Tampilkan overlay sukses
            setTimeout(() => {
                onlineOverlay.classList.add('show');
                setTimeout(() => { window.location.reload(); }, 1800);
            }, 400);
        }

        function handleOffline() {
            if (lastStatus === 'offline') return;
            lastStatus = 'offline';

            connectionBar.classList.remove('online');
            connectionBar.classList.add('offline');
            connectionBarText.textContent = 'Tidak ada koneksi internet';
        }

        // ── Retry ─────────────────────────────────────────────────────────
        function handleRetry() {
            if (retryBtn.disabled) return;

            retryBtn.disabled = true;
            retryBtn.classList.add('loading');
            retryText.textContent = 'Memeriksa...';

            checkConnection().then(isConnected => {
                if (isConnected) {
                    handleOnline();
                } else {
                    retryBtn.style.animation = 'shake 0.4s ease';
                    setTimeout(() => {
                        retryBtn.style.animation = '';
                        retryBtn.disabled = false;
                        retryBtn.classList.remove('loading');
                        retryText.textContent = 'Coba Lagi';
                    }, 500);
                }
            });
        }

        function checkConnection() {
            return new Promise((resolve) => {
                if (!navigator.onLine) { resolve(false); return; }
                fetch('/mkdcnew/favicon.ico?_=' + Date.now(), {
                    method: 'HEAD',
                    cache: 'no-store',
                    mode: 'no-cors'
                })
                .then(() => resolve(true))
                .catch(() => resolve(false));
            });
        }

        // ── Event Listeners ───────────────────────────────────────────────
        window.addEventListener('online',  handleOnline);
        window.addEventListener('offline', handleOffline);

        // Polling setiap 10 detik
        setInterval(() => {
            checkConnection().then(ok => { if (ok) handleOnline(); });
        }, 10000);

        // Keyboard shortcut: tekan R untuk retry
        document.addEventListener('keydown', e => {
            if ((e.key === 'r' || e.key === 'R') && !e.ctrlKey && !e.metaKey) handleRetry();
        });
    </script>
</body>

</html>
