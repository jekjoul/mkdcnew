<?php
$dev_cfg = getActiveDeviceConfig();
$alert_msg = '';
$alert_success = true;

// Tangani Form Settings & Maintenance Actions via Standard PHP POST
$action = $_REQUEST['action'] ?? '';
if ($action === 'save_config') {
    $ip           = trim($_POST['ip_address'] ?? '10.10.10.10');
    $port         = trim($_POST['port'] ?? '8080');
    $sn           = trim($_POST['serial_number'] ?? '616202024171114');
    $api_env      = trim($_POST['api_env'] ?? 'dev');
    $api_dev_url  = trim($_POST['api_dev_url'] ?? 'http://localhost/mkdc_new_draft/api/presensi/active_students');
    $api_prod_url = trim($_POST['api_prod_url'] ?? 'https://presensi.sekolah.sch.id/api/presensi/active_students');
    $api_dev_key  = trim($_POST['api_dev_key'] ?? 'MKDC_FINGERPRINT_SECRET_KEY_2026');
    $api_prod_key = trim($_POST['api_prod_key'] ?? 'MKDC_FINGERPRINT_SECRET_KEY_2026');

    saveActiveDeviceConfig($ip, $port, $sn, $api_env, $api_dev_url, $api_prod_url, $api_dev_key, $api_prod_key);
    $dev_cfg = getActiveDeviceConfig();
    $alert_msg = "Konfigurasi perangkat & API Key (Mode API: " . strtoupper($api_env) . ") berhasil disimpan.";
    $alert_success = true;
} elseif ($action === 'test_api') {
    $target_url = $_REQUEST['target_url'] ?? $dev_cfg['active_api'];
    $res = testApiConnection($target_url);
    $alert_msg = $res['message'];
    $alert_success = $res['status'];
} elseif ($action === 'exec_maintenance') {
    $type = $_POST['type'] ?? '';
    if ($type === 'sync_time') {
        $res = EasyLinkSDK::setTime($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    } elseif ($type === 'del_admin') {
        $res = EasyLinkSDK::deleteAdmin($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    } elseif ($type === 'del_log') {
        $res = EasyLinkSDK::deleteScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    } elseif ($type === 'init_device') {
        $res = EasyLinkSDK::initDevice($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    } else {
        $res = ['status' => false, 'message' => 'Perintah tidak dikenal.'];
    }

    $alert_msg = $res['message'];
    $alert_success = $res['status'];
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:settings-bold" style="color: var(--primary);"></span> Pengaturan Perangkat & Web API Endpoint Key
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Mengatur IP/Port mesin serta API Key untuk lingkungan Development dan Production secara mandiri.
            </div>
        </div>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $alert_success ? 'Status:' : 'Perhatian:'; ?></strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-2">
        <!-- Form Update Connection Config (Standard PHP POST) -->
        <div>
            <h3 class="card-title mb-2" style="font-size: 1rem;">
                <span class="iconify" data-icon="solar:server-square-bold"></span> Konfigurasi Mesin & Web API
            </h3>
            <form method="POST" action="index.php?p=settings">
                <input type="hidden" name="action" value="save_config">
                
                <!-- Section 1: Mesin Fingerprint -->
                <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 1.25rem;">
                    <div style="font-weight: 700; font-size: 0.875rem; color: var(--primary); margin-bottom: 0.75rem;">
                        1. Mesin Fingerprint EasyLink
                    </div>
                    <div class="form-group">
                        <label class="form-label">Server IP Address (Mesin Fingerprint)</label>
                        <input type="text" name="ip_address" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['server_IP']); ?>" placeholder="10.10.10.10" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Server Port WebService</label>
                        <input type="number" name="port" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['server_port']); ?>" placeholder="8080" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Device Serial Number (SN)</label>
                        <input type="text" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['device_sn']); ?>" placeholder="616202024171114" required>
                    </div>
                </div>

                <!-- Section 2: Web API Endpoints & API Keys -->
                <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 1.25rem;">
                    <div style="font-weight: 700; font-size: 0.875rem; color: var(--success); margin-bottom: 0.75rem;">
                        2. Web API Endpoint & Security Keys
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mode Lingkungan Aktif (Environment)</label>
                        <select name="api_env" class="form-control">
                            <option value="dev" <?php echo ($dev_cfg['api_env'] === 'dev') ? 'selected' : ''; ?>>Development (Lokal / Testing)</option>
                            <option value="prod" <?php echo ($dev_cfg['api_env'] === 'prod') ? 'selected' : ''; ?>>Production (Server Live / Public Domain)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Development URL</label>
                        <input type="text" name="api_dev_url" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['api_dev_url']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Key / Token (Development)</label>
                        <input type="text" name="api_dev_key" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['api_dev_key']); ?>" placeholder="MKDC_FINGERPRINT_SECRET_KEY_2026" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Production URL</label>
                        <input type="text" name="api_prod_url" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['api_prod_url']); ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">API Key / Token (Production)</label>
                        <input type="text" name="api_prod_key" class="form-control" value="<?php echo htmlspecialchars($dev_cfg['api_prod_key']); ?>" placeholder="MKDC_FINGERPRINT_SECRET_KEY_2026" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <span class="iconify" data-icon="solar:diskette-bold"></span> Simpan Seluruh Konfigurasi
                </button>
            </form>
        </div>

        <!-- Maintenance Commands & API Tester -->
        <div>
            <h3 class="card-title mb-2" style="font-size: 1rem;">
                <span class="iconify" data-icon="solar:global-bold"></span> Tes Koneksi Web API Server
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 1.5rem;">
                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>API Aktif Saat Ini (<?php echo strtoupper($dev_cfg['api_env']); ?>)</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">
                            <code><?php echo htmlspecialchars($dev_cfg['active_api']); ?></code>
                        </div>
                    </div>
                    <a href="index.php?p=settings&action=test_api&target_url=<?php echo urlencode($dev_cfg['active_api']); ?>" class="btn btn-success" style="font-size: 0.8rem;">
                        <span class="iconify" data-icon="solar:bolt-bold"></span> Tes API Aktif
                    </a>
                </div>

                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>API Development (DEV)</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">
                            Key: <code><?php echo htmlspecialchars($dev_cfg['api_dev_key']); ?></code>
                        </div>
                    </div>
                    <a href="index.php?p=settings&action=test_api&target_url=<?php echo urlencode($dev_cfg['api_dev_url']); ?>" class="btn btn-secondary" style="font-size: 0.8rem;">
                        <span class="iconify" data-icon="solar:link-bold"></span> Tes DEV API
                    </a>
                </div>

                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>API Production (PROD)</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">
                            Key: <code><?php echo htmlspecialchars($dev_cfg['api_prod_key']); ?></code>
                        </div>
                    </div>
                    <a href="index.php?p=settings&action=test_api&target_url=<?php echo urlencode($dev_cfg['api_prod_url']); ?>" class="btn btn-secondary" style="font-size: 0.8rem;">
                        <span class="iconify" data-icon="solar:shield-check-bold"></span> Tes PROD API
                    </a>
                </div>
            </div>

            <h3 class="card-title mb-2" style="font-size: 1rem;">
                <span class="iconify" data-icon="solar:tuning-square-2-bold"></span> Aksi Pemeliharaan Mesin (Device Control)
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Sinkronisasi Jam Mesin</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">Set jam mesin (dev/settime) ke waktu PC server saat ini.</div>
                    </div>
                    <form method="POST" action="index.php?p=settings" style="margin:0;">
                        <input type="hidden" name="action" value="exec_maintenance">
                        <input type="hidden" name="type" value="sync_time">
                        <button type="submit" class="btn btn-primary" style="font-size: 0.8rem;">
                            <span class="iconify" data-icon="solar:clock-circle-bold"></span> Sync Time
                        </button>
                    </form>
                </div>

                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Hapus Hak Akses Admin</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">Mereset hak akses administrator mesin menjadi user biasa.</div>
                    </div>
                    <form method="POST" action="index.php?p=settings" style="margin:0;">
                        <input type="hidden" name="action" value="exec_maintenance">
                        <input type="hidden" name="type" value="del_admin">
                        <button type="submit" class="btn btn-secondary" style="font-size: 0.8rem;" onclick="return confirm('Hapus seluruh hak akses admin di mesin?')">
                            <span class="iconify" data-icon="solar:user-block-bold"></span> Delete Admin
                        </button>
                    </form>
                </div>

                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Hapus Log Presensi Mesin</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">Membersihkan seluruh riwayat scanlog pada memori mesin.</div>
                    </div>
                    <form method="POST" action="index.php?p=settings" style="margin:0;">
                        <input type="hidden" name="action" value="exec_maintenance">
                        <input type="hidden" name="type" value="del_log">
                        <button type="submit" class="btn btn-danger" style="font-size: 0.8rem;" onclick="return confirm('Hapus seluruh log presensi di mesin?')">
                            <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Delete Log
                        </button>
                    </form>
                </div>

                <div style="background: #f8fafc; padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Inisialisasi Perangkat (Reset)</strong>
                        <div style="font-size: 0.775rem; color: var(--text-secondary);">Mereset konfigurasi internal mesin ke pengaturan pabrik.</div>
                    </div>
                    <form method="POST" action="index.php?p=settings" style="margin:0;">
                        <input type="hidden" name="action" value="exec_maintenance">
                        <input type="hidden" name="type" value="init_device">
                        <button type="submit" class="btn btn-danger" style="font-size: 0.8rem;" onclick="return confirm('Inisialisasi reset perangkat mesin ke factory default?')">
                            <span class="iconify" data-icon="solar:restart-bold"></span> Initialization
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
