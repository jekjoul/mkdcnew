<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';
require_once __DIR__ . '/lib/EasyLinkSDK.php';

if (!isset($_SESSION['fp_bridge_admin'])) {
    header('Location: login.php');
    exit;
}

$machine   = BridgeStorage::getMachine();
$settings  = BridgeStorage::getSettings();
$active_url= BridgeStorage::getActiveEndpointUrl();
$history   = BridgeStorage::getSyncHistory(10);
$ping_m    = EasyLinkSDK::ping($machine['ip_address'] ?? '192.168.1.201', $machine['port'] ?? 4370);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="app-container">
        <!-- Stat Cards Grid -->
        <div class="grid-4" style="margin-bottom: 2rem;">
            <!-- Stat 1: Server Status -->
            <div class="stat-card">
                <div>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">PHP Server Status</span>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">
                        Port <?php echo $_SERVER['SERVER_PORT'] ?? '8088'; ?> &bull; Active
                    </h3>
                    <span class="badge badge-success" style="margin-top: 0.5rem;">WebDesktop Ready</span>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <span class="iconify" data-icon="solar:server-bold"></span>
                </div>
            </div>

            <!-- Stat 2: Machine Status -->
            <div class="stat-card">
                <div>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Mesin EasyLink</span>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">
                        <?php echo htmlspecialchars($machine['nama_mesin'] ?? 'Mesin Utama'); ?>
                    </h3>
                    <?php if ($ping_m['status']): ?>
                        <span class="badge badge-success" style="margin-top: 0.5rem;">Terhubung</span>
                    <?php else: ?>
                        <span class="badge badge-danger" style="margin-top: 0.5rem;">Offline / Unreachable</span>
                    <?php endif; ?>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <span class="iconify" data-icon="solar:scanner-bold"></span>
                </div>
            </div>

            <!-- Stat 3: Target API -->
            <div class="stat-card">
                <div>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Environment API</span>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem; text-transform: uppercase;">
                        <?php echo htmlspecialchars($settings['env_mode'] ?? 'DEV'); ?>
                    </h3>
                    <span style="font-size: 0.75rem; color: #64748b; display: block; margin-top: 0.25rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?php echo htmlspecialchars($active_url); ?>
                    </span>
                </div>
                <div class="stat-icon" style="background: #ecfeff; color: #06b6d4;">
                    <span class="iconify" data-icon="solar:globus-bold"></span>
                </div>
            </div>

            <!-- Stat 4: Total Log History -->
            <div class="stat-card">
                <div>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Riwayat Aktivitas</span>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">
                        <?php echo count($history); ?> Log Terakhir
                    </h3>
                    <span class="badge badge-primary" style="margin-top: 0.5rem;">Auto-Sync Active</span>
                </div>
                <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                    <span class="iconify" data-icon="solar:history-bold"></span>
                </div>
            </div>
        </div>

        <!-- Action Panel & Auto Sync Controller -->
        <div class="grid-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="iconify" data-icon="solar:bolt-bold" style="color: #2563eb;"></span> Aksi Cepat WebDesktop
                    </h3>
                </div>
                <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem;">
                    Jalankan aksi instan untuk mengirimkan log presensi atau memeriksa perbedaan data siswa dengan server.
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <button id="btn-push-presensi" class="btn btn-primary" onclick="triggerPushPresensi()">
                        <span class="iconify" data-icon="solar:upload-bold"></span> Kirim Log Presensi Ke Server
                    </button>
                    <a href="sinkronisasi.php" class="btn btn-success">
                        <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span> Buka Sinkronisasi Siswa
                    </a>
                    <button class="btn btn-outline" onclick="triggerTestConnection()">
                        <span class="iconify" data-icon="solar:wifi-bold"></span> Tes Koneksi Mesin
                    </button>
                </div>
                <div id="action-feedback" style="margin-top: 1rem; display: none; padding: 0.75rem; border-radius: 8px; font-size: 0.875rem;"></div>
            </div>

            <!-- Machine Detail Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="iconify" data-icon="solar:info-circle-bold" style="color: #10b981;"></span> Parameter Mesin Fingerprint EasyLink
                    </h3>
                </div>
                <table class="table" style="font-size: 0.875rem;">
                    <tr>
                        <td style="font-weight: 600; width: 140px;">Nama Mesin</td>
                        <td><?php echo htmlspecialchars($machine['nama_mesin'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">IP Address & Port</td>
                        <td><code><?php echo htmlspecialchars($machine['ip_address'] ?? '192.168.1.201'); ?>:<?php echo htmlspecialchars($machine['port'] ?? 4370); ?></code></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Serial Number (SN)</td>
                        <td><code><?php echo htmlspecialchars($machine['serial_number'] ?? '-'); ?></code></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Comm Key</td>
                        <td><code><?php echo htmlspecialchars($machine['comm_key'] ?? '0'); ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Recent Activity Logs Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:history-bold" style="color: #64748b;"></span> Riwayat Log Aktivitas Bridge
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Waktu</th>
                            <th>Tipe Aktivitas</th>
                            <th>Status</th>
                            <th>Detail Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada riwayat aktivitas sync.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history as $idx => $item): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><code><?php echo htmlspecialchars($item['timestamp'] ?? '-'); ?></code></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($item['type'] ?? 'sync'); ?></span></td>
                                    <td>
                                        <?php if (($item['status'] ?? '') === 'success'): ?>
                                            <span class="badge badge-success">SUKSES</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">GAGAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['message'] ?? ($item['action'] ?? '-')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function triggerTestConnection() {
            const feedback = document.getElementById('action-feedback');
            feedback.style.display = 'block';
            feedback.className = 'badge-info';
            feedback.style.background = '#ecfeff';
            feedback.style.color = '#06b6d4';
            feedback.innerHTML = '<span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span> Menguji koneksi ke mesin EasyLink...';

            fetch('ajax.php?action=test_connection', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        feedback.style.background = '#ecfdf5';
                        feedback.style.color = '#10b981';
                        feedback.innerHTML = '✔ ' + data.message;
                    } else {
                        feedback.style.background = '#fef2f2';
                        feedback.style.color = '#ef4444';
                        feedback.innerHTML = '✖ ' + data.message;
                    }
                })
                .catch(err => {
                    feedback.style.background = '#fef2f2';
                    feedback.style.color = '#ef4444';
                    feedback.innerHTML = '✖ Terjadi kesalahan koneksi AJAX.';
                });
        }

        function triggerPushPresensi() {
            const feedback = document.getElementById('action-feedback');
            feedback.style.display = 'block';
            feedback.style.background = '#eff6ff';
            feedback.style.color = '#2563eb';
            feedback.innerHTML = '<span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span> Membaca log mesin dan mengirim ke Server API...';

            fetch('ajax.php?action=push_presensi', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        feedback.style.background = '#ecfdf5';
                        feedback.style.color = '#10b981';
                        feedback.innerHTML = '✔ ' + data.message;
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        feedback.style.background = '#fef2f2';
                        feedback.style.color = '#ef4444';
                        feedback.innerHTML = '✖ ' + data.message;
                    }
                })
                .catch(err => {
                    feedback.style.background = '#fef2f2';
                    feedback.style.color = '#ef4444';
                    feedback.innerHTML = '✖ Terjadi kesalahan AJAX.';
                });
        }
    </script>
</body>
</html>
