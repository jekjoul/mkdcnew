<?php
$current_page = $_GET['p'] ?? 'home';
$dev_cfg      = getActiveDeviceConfig();
?>
<aside class="desktop-sidebar d-flex flex-column justify-content-between p-3">
    <div>
        <!-- Sidebar Brand Card -->
        <div class="sidebar-brand-card p-3 rounded-3 mb-4 d-flex align-items-center gap-3">
            <div class="brand-logo-box bg-primary text-white rounded-3 d-flex align-items-center justify-content-center">
                <span class="iconify fs-4" data-icon="solar:fingerprint-bold"></span>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-white brand-name">EasyLink Bridge</h6>
                <small class="text-secondary-light text-xs">Desktop Edition v2.0</small>
            </div>
        </div>

        <!-- Sidebar Navigation List -->
        <div class="sidebar-nav-group">
            <div class="sidebar-label text-uppercase text-secondary-light fw-bold text-xs mb-2 px-2">Navigasi Utama</div>
            <div class="nav flex-column nav-pills gap-1">
                <a href="index.php?p=home" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'home') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:widget-bold"></span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="index.php?p=user" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'user') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:users-group-two-rounded-bold"></span>
                    <span class="nav-text">Data User Mesin</span>
                </a>
                <a href="index.php?p=server_siswa" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'server_siswa') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:users-group-rounded-bold"></span>
                    <span class="nav-text">Data Siswa Server</span>
                </a>
                <a href="index.php?p=scanlog" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'scanlog') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:history-bold"></span>
                    <span class="nav-text">Data Scanlog</span>
                </a>
                <a href="index.php?p=sinkronisasi" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'sinkronisasi') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:sort-from-top-to-bottom-bold"></span>
                    <span class="nav-text">Sinkronisasi Siswa</span>
                </a>
            </div>

            <div class="sidebar-label text-uppercase text-secondary-light fw-bold text-xs mt-4 mb-2 px-2">Sistem & Alat</div>
            <div class="nav flex-column nav-pills gap-1">
                <a href="index.php?p=info" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'info') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:info-square-bold"></span>
                    <span class="nav-text">Device Info</span>
                </a>
                <a href="index.php?p=settings" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'settings') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:settings-bold"></span>
                    <span class="nav-text">Pengaturan Mesin</span>
                </a>
                <a href="index.php?p=auto" class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 <?php echo ($current_page === 'auto') ? 'active' : ''; ?>">
                    <span class="iconify fs-5 nav-icon" data-icon="solar:play-circle-bold"></span>
                    <span class="nav-text">Auto Sync Scheduler</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar Bottom Device Status Widget -->
    <div class="sidebar-device-widget p-3 rounded-3 mt-4 border border-secondary-subtle">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="status-dot online"></span>
            <span class="text-uppercase text-secondary-light fw-bold text-xs">Device Active</span>
        </div>
        <div class="font-monospace text-info fw-bold text-sm mb-1"><?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?></div>
        <div class="text-secondary-light text-xs">SN: <code><?php echo htmlspecialchars($dev_cfg['device_sn']); ?></code></div>
    </div>
</aside>
