<?php
$current_page = basename($_SERVER['PHP_SELF']);
$admin = BridgeStorage::getAdmin();
$settings = BridgeStorage::getSettings();
$env_mode = strtoupper($settings['env_mode'] ?? 'DEV');
?>
<header class="top-navbar">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="background: #2563eb; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <span class="iconify" data-icon="solar:scanner-bold"></span>
        </div>
        <div>
            <h1 class="navbar-brand-title" style="font-size: 1.1rem; color: #ffffff; margin: 0;">Fingerprint WebDesktop</h1>
            <span style="font-size: 0.7rem; color: #94a3b8; display: block;">EasyLink SDK Bridge App &bull; <span class="badge <?php echo $env_mode === 'PRODUCTION' ? 'badge-success' : 'badge-info'; ?>"><?php echo $env_mode; ?></span></span>
        </div>
    </div>

    <nav class="nav-links">
        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <span class="iconify" data-icon="solar:widget-add-bold"></span> Dashboard
        </a>
        <a href="presensi.php" class="<?php echo $current_page === 'presensi.php' ? 'active' : ''; ?>">
            <span class="iconify" data-icon="solar:calendar-mark-bold"></span> Presensi API
        </a>
        <a href="sinkronisasi.php" class="<?php echo $current_page === 'sinkronisasi.php' ? 'active' : ''; ?>">
            <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span> Sinkronisasi Siswa
        </a>
        <a href="setting_mesin.php" class="<?php echo $current_page === 'setting_mesin.php' ? 'active' : ''; ?>">
            <span class="iconify" data-icon="solar:scanner-linear"></span> Setting Mesin
        </a>
        <a href="setting_api.php" class="<?php echo $current_page === 'setting_api.php' ? 'active' : ''; ?>">
            <span class="iconify" data-icon="solar:settings-bold"></span> Setting API & Akun
        </a>
        <a href="logout.php" style="color: #ef4444;" onclick="return confirm('Yakin ingin keluar dari WebDesktop?')">
            <span class="iconify" data-icon="solar:logout-bold"></span> Keluar
        </a>
    </nav>
</header>
