<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';

if (!isset($_SESSION['fp_bridge_admin'])) {
    header('Location: login.php');
    exit;
}

$settings = BridgeStorage::getSettings();
$admin    = BridgeStorage::getAdmin();
$msg      = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_api_settings'])) {
        $env_mode           = $_POST['env_mode'] ?? 'development';
        $dev_endpoint_url   = trim($_POST['dev_endpoint_url'] ?? '');
        $prod_endpoint_url  = trim($_POST['prod_endpoint_url'] ?? '');
        $api_token          = trim($_POST['api_token'] ?? '');
        $auto_sync_interval = (int)($_POST['auto_sync_interval'] ?? 10);
        $auto_sync_active   = isset($_POST['auto_sync_active']) ? 1 : 0;

        $data = [
            'env_mode'           => in_array($env_mode, ['development', 'production']) ? $env_mode : 'development',
            'dev_endpoint_url'   => $dev_endpoint_url,
            'prod_endpoint_url'  => $prod_endpoint_url,
            'api_token'          => $api_token,
            'auto_sync_interval' => ($auto_sync_interval < 3) ? 5 : $auto_sync_interval,
            'auto_sync_active'   => $auto_sync_active
        ];

        BridgeStorage::saveSettings($data);
        $settings = BridgeStorage::getSettings();
        $msg      = 'Pengaturan Endpoint Server API berhasil disimpan.';
        $msg_type = 'success';
    } elseif (isset($_POST['save_admin_account'])) {
        $new_pass   = trim($_POST['new_password'] ?? '');
        $admin_name = trim($_POST['admin_name'] ?? '');

        if (empty($new_pass)) {
            $msg = 'Password tidak boleh kosong!';
            $msg_type = 'danger';
        } else {
            BridgeStorage::updatePassword($new_pass, $admin_name);
            $admin    = BridgeStorage::getAdmin();
            $msg      = 'Akun Admin Standalone berhasil diperbarui.';
            $msg_type = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting API & Akun - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="app-container">
        <?php if ($msg): ?>
            <div class="badge-<?php echo $msg_type; ?>" style="width: 100%; padding: 0.85rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.9rem;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Form 1: Setting Endpoint API Server (Dev & Prod) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="iconify" data-icon="solar:settings-bold" style="color: #2563eb;"></span> Pengaturan Server API (Dev & Prod)
                    </h3>
                </div>

                <form method="POST" action="setting_api.php">
                    <input type="hidden" name="save_api_settings" value="1">

                    <div class="form-group">
                        <label class="form-label">Mode Environment Server API <span style="color: red;">*</span></label>
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.4rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="env_mode" value="development" <?php echo ($settings['env_mode'] ?? '') === 'development' ? 'checked' : ''; ?>>
                                <span class="badge badge-info">DEV</span> Development
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="env_mode" value="production" <?php echo ($settings['env_mode'] ?? '') === 'production' ? 'checked' : ''; ?>>
                                <span class="badge badge-success">PROD</span> Production
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Development Endpoint URL</label>
                        <input type="url" name="dev_endpoint_url" class="form-control" value="<?php echo htmlspecialchars($settings['dev_endpoint_url'] ?? ''); ?>" required placeholder="http://localhost/mkdcnew/api/presensi/sync">
                        <span style="font-size: 0.75rem; color: #64748b;">URL server lokal saat tahap pengujian LAN.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Production Endpoint URL</label>
                        <input type="url" name="prod_endpoint_url" class="form-control" value="<?php echo htmlspecialchars($settings['prod_endpoint_url'] ?? ''); ?>" required placeholder="https://domain-sekolah.sch.id/api/presensi/sync">
                        <span style="font-size: 0.75rem; color: #64748b;">URL server live domain sekolah online.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">API Secret Token Key</label>
                        <input type="text" name="api_token" class="form-control" value="<?php echo htmlspecialchars($settings['api_token'] ?? ''); ?>" required>
                        <span style="font-size: 0.75rem; color: #64748b;">Token rahasia yang dicocokkan oleh server API.</span>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Interval Auto-Sync (Detik)</label>
                            <input type="number" name="auto_sync_interval" class="form-control" value="<?php echo (int)($settings['auto_sync_interval'] ?? 10); ?>" min="3" max="3600" required>
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="auto_sync_active" value="1" <?php echo ($settings['auto_sync_active'] ?? 0) ? 'checked' : ''; ?>>
                                <span style="font-size: 0.9rem; font-weight: 600; color: #0f172a;">Aktifkan Auto-Sync</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                        <span class="iconify" data-icon="solar:disk-bold"></span> Simpan Pengaturan API
                    </button>
                </form>
            </div>

            <!-- Form 2: Manajemen Akun Standalone (Tanpa Role & Permission) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="iconify" data-icon="solar:user-bold" style="color: #10b981;"></span> Manajemen Akun Standalone (Tanpa Role)
                    </h3>
                </div>

                <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem;">
                    Kelola akun login lokal WebDesktop. Aplikasi ini bersifat standalone tanpa hierarki role & permission.
                </p>

                <form method="POST" action="setting_api.php">
                    <input type="hidden" name="save_admin_account" value="1">

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username'] ?? 'admin'); ?>" disabled style="background: #f1f5f9; cursor: not-allowed;">
                        <span style="font-size: 0.75rem; color: #64748b;">Username akun admin bawaan sistem: admin.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap Pengelola</label>
                        <input type="text" name="admin_name" class="form-control" value="<?php echo htmlspecialchars($admin['name'] ?? 'Administrator Fingerprint'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru <span style="color: red;">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-top: 1rem;">
                        <span class="iconify" data-icon="solar:key-bold"></span> Update Password & Akun Admin
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
