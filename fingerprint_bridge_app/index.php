<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_time_limit(0);
ini_set('max_execution_time', '0');

require_once __DIR__ . '/koneksidb.php';
require_once __DIR__ . '/lib/EasyLinkSDK.php';
require_once __DIR__ . '/lib/BridgeDB.php';

$page  = $_GET['p'] ?? 'home';
$valid_pages = ['home', 'user', 'server_siswa', 'scanlog', 'sinkronisasi', 'info', 'settings', 'auto'];
if (!in_array($page, $valid_pages)) {
    $page = 'home';
}
$dev_cfg = getActiveDeviceConfig();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyLink SDK Desktop Bridge v2.0</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Iconify Icons -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <!-- Custom Modern Desktop Style -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="desktop-body">

    <!-- DESKTOP APP WINDOW -->
    <div class="desktop-window">
        
        <!-- 1. DESKTOP WINDOW TITLEBAR -->
        <header class="desktop-titlebar d-flex align-items-center justify-content-between px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <div class="titlebar-brand-icon">
                    <span class="iconify text-white" data-icon="solar:fingerprint-bold"></span>
                </div>
                <span class="titlebar-app-title me-2">EasyLink SDK Bridge v2.0 <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1 text-xs">Desktop Manager</span></span>
                <span class="badge bg-dark border border-secondary-subtle text-light rounded-pill px-3 py-1 text-xs d-inline-flex align-items-center gap-2">
                    <span class="status-dot online"></span> <?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?>
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="titlebar-clock-badge font-monospace" id="desktopClock">00:00:00</div>
                <div class="window-controls d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-dark win-btn p-1" title="Minimize" onclick="alert('Client Desktop Manager berjalan aktif.')"><span class="iconify" data-icon="solar:minus-bold"></span></button>
                    <button class="btn btn-sm btn-dark win-btn p-1" title="Maximize" onclick="document.body.classList.toggle('is-fullscreen')"><span class="iconify" data-icon="solar:square-bold"></span></button>
                    <button class="btn btn-sm btn-danger win-btn win-close p-1" title="Tutup Desktop Bridge" onclick="alert('Client Desktop Bridge berjalan di latar belakang.')"><span class="iconify" data-icon="solar:close-bold"></span></button>
                </div>
            </div>
        </header>

        <!-- 2. DESKTOP WORKSPACE (SIDEBAR + MAIN CONTENT) -->
        <div class="desktop-workspace d-flex flex-fill overflow-hidden">
            
            <!-- DESKTOP SIDEBAR NAVIGATION -->
            <?php include __DIR__ . '/includes/navbar.php'; ?>

            <!-- DESKTOP CONTENT STAGE -->
            <main class="desktop-content-stage flex-fill d-flex flex-column overflow-hidden">
                <div class="content-scrollable p-4 flex-fill overflow-y-auto">
                    <?php
                    $content_file = __DIR__ . '/content/' . $page . '.php';
                    if ($page === 'sinkronisasi') {
                        $content_file = __DIR__ . '/sinkronisasi.php';
                    }

                    if (file_exists($content_file)) {
                        include $content_file;
                    } else {
                        include __DIR__ . '/content/home.php';
                    }
                    ?>
                </div>
            </main>
        </div>

        <!-- 3. DESKTOP FOOTER STATUSBAR -->
        <footer class="desktop-statusbar d-flex align-items-center justify-content-between px-3 py-1">
            <div class="d-flex align-items-center gap-2 text-xs text-secondary-emphasis">
                <span class="status-dot online"></span>
                <span>Mesin Active: <strong class="text-light"><?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?></strong> (SN: <code class="text-info"><?php echo htmlspecialchars($dev_cfg['device_sn']); ?></code>)</span>
            </div>
            <div class="d-none d-md-flex align-items-center gap-2 text-xs text-secondary-emphasis">
                <span class="iconify text-primary" data-icon="solar:server-square-bold"></span>
                <span>Web API: <code class="text-primary-light"><?php echo htmlspecialchars($dev_cfg['active_api']); ?></code></span>
            </div>
            <div class="d-flex align-items-center gap-2 text-xs text-secondary-emphasis">
                <span class="iconify text-info" data-icon="solar:cpu-bold"></span>
                <span>SDK Driver: <strong class="text-light">Delphi D7 cURL WebService</strong></span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function updateClock() {
        var now = new Date();
        var hrs = String(now.getHours()).padStart(2, '0');
        var mins = String(now.getMinutes()).padStart(2, '0');
        var secs = String(now.getSeconds()).padStart(2, '0');
        var clk = document.getElementById('desktopClock');
        if (clk) clk.innerText = hrs + ':' + mins + ':' + secs;
    }
    setInterval(updateClock, 1000);
    updateClock();
    </script>
</body>
</html>
