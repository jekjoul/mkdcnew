<?php
$dev_cfg  = getActiveDeviceConfig();
$test_conn = $_GET['test'] ?? '';
$test_api  = $_GET['test_api'] ?? '';

$ping_res = null;
if ($test_conn === '1') {
    $ping_res = EasyLinkSDK::ping($dev_cfg['server_IP'], $dev_cfg['server_port'], 3);
}

$api_test_res = null;
if ($test_api === '1') {
    $api_test_res = testApiConnection($dev_cfg['active_api']);
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:widget-bold" style="color: var(--primary);"></span> Dashboard Client EasyLink SDK (Standard PHP Edition)
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Aplikasi Pengelola Komunikasi Mesin Fingerprint EasyLink / Fingerspot / Revo (100% Server-Side PHP - Tanpa AJAX / Tanpa MySQL).
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="index.php?p=home&test=1" class="btn btn-primary">
                <span class="iconify" data-icon="solar:bolt-bold"></span> Tes Koneksi Mesin
            </a>
            <a href="index.php?p=home&test_api=1" class="btn btn-success">
                <span class="iconify" data-icon="solar:global-bold"></span> Tes Koneksi API Web
            </a>
        </div>
    </div>

    <!-- Alert Ping Test Result -->
    <?php if ($ping_res !== null): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; background: <?php echo $ping_res['status'] ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $ping_res['status'] ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $ping_res['status'] ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $ping_res['status'] ? 'Berhasil Terhubung Ke Mesin:' : 'Gagal Terhubung Ke Mesin:'; ?></strong> <?php echo htmlspecialchars($ping_res['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Alert API Test Result -->
    <?php if ($api_test_res !== null): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; background: <?php echo $api_test_res['status'] ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $api_test_res['status'] ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $api_test_res['status'] ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $api_test_res['status'] ? 'Berhasil Terhubung Ke Web API:' : 'Gagal Terhubung Ke Web API:'; ?></strong> <?php echo htmlspecialchars($api_test_res['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Quick Stats Grid -->
    <div class="grid grid-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <span class="iconify" data-icon="solar:server-square-bold"></span>
            </div>
            <div>
                <div class="stat-val"><?php echo htmlspecialchars($dev_cfg['server_IP']); ?></div>
                <div class="stat-lbl">IP Mesin Active</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <span class="iconify" data-icon="solar:link-bold"></span>
            </div>
            <div>
                <div class="stat-val"><?php echo htmlspecialchars($dev_cfg['server_port']); ?></div>
                <div class="stat-lbl">Port WebService</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <span class="iconify" data-icon="solar:shield-check-bold"></span>
            </div>
            <div>
                <div class="stat-val"><?php echo htmlspecialchars($dev_cfg['device_sn']); ?></div>
                <div class="stat-lbl">Serial Number Mesin</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon info">
                <span class="iconify" data-icon="solar:global-bold"></span>
            </div>
            <div>
                <div class="stat-val"><?php echo strtoupper($dev_cfg['api_env']); ?> Mode</div>
                <div class="stat-lbl"><?php echo htmlspecialchars($dev_cfg['active_api']); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Grid Shortcut Modules -->
<div class="grid grid-2">
    <div class="card">
        <h3 class="card-title mb-2">
            <span class="iconify" data-icon="solar:play-stream-bold" style="color: var(--success);"></span> Akses Cepat Fitur SDK
        </h3>
        <div class="grid grid-2">
            <a href="index.php?p=user" class="btn btn-secondary btn-block">
                <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span> Data User Mesin
            </a>
            <a href="index.php?p=server_siswa" class="btn btn-secondary btn-block">
                <span class="iconify" data-icon="solar:users-group-rounded-bold"></span> Data Siswa Server
            </a>
            <a href="index.php?p=scanlog" class="btn btn-secondary btn-block">
                <span class="iconify" data-icon="solar:history-bold"></span> Log Presensi
            </a>
            <a href="index.php?p=sinkronisasi" class="btn btn-secondary btn-block">
                <span class="iconify" data-icon="solar:sort-from-top-to-bottom-bold"></span> Sinkron Siswa
            </a>
            <a href="index.php?p=info" class="btn btn-secondary btn-block">
                <span class="iconify" data-icon="solar:info-square-bold"></span> Device Info
            </a>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title mb-2">
            <span class="iconify" data-icon="solar:settings-bold" style="color: var(--warning);"></span> Pengaturan Perangkat & API
        </h3>
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
            Ubah Alamat IP Mesin, Port, Serial Number, serta URL Web API Server (Development & Production). Seluruh perubahan konfigurasi tersimpan ke <code>data/config.json</code>.
        </p>
        <a href="index.php?p=settings" class="btn btn-primary">
            <span class="iconify" data-icon="solar:settings-bold"></span> Buka Pengaturan Perangkat & API
        </a>
    </div>
</div>
