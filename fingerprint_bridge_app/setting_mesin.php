<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';

if (!isset($_SESSION['fp_bridge_admin'])) {
    header('Location: login.php');
    exit;
}

$machine = BridgeStorage::getMachine();
$msg     = '';
$msg_type= '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_machine'])) {
    $data = [
        'nama_mesin'    => trim($_POST['nama_mesin'] ?? ''),
        'ip_address'    => trim($_POST['ip_address'] ?? ''),
        'port'          => (int)($_POST['port'] ?? 4370),
        'comm_key'      => trim($_POST['comm_key'] ?? '0'),
        'serial_number' => trim($_POST['serial_number'] ?? ''),
        'kode_aktivasi' => trim($_POST['kode_aktivasi'] ?? '')
    ];

    if (empty($data['nama_mesin']) || empty($data['ip_address'])) {
        $msg = 'Nama Mesin dan IP Address wajib diisi!';
        $msg_type = 'danger';
    } else {
        BridgeStorage::saveMachine($data);
        $machine  = BridgeStorage::getMachine();
        $msg      = 'Pengaturan Mesin Fingerprint EasyLink berhasil disimpan.';
        $msg_type = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Mesin - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="app-container">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:scanner-bold" style="color: #2563eb;"></span> Pengaturan Mesin Fingerprint EasyLink
                </h3>
            </div>

            <?php if ($msg): ?>
                <div class="badge-<?php echo $msg_type; ?>" style="width: 100%; padding: 0.85rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="setting_mesin.php">
                <input type="hidden" name="save_machine" value="1">

                <div class="form-group">
                    <label class="form-label">Nama Mesin <span style="color: red;">*</span></label>
                    <input type="text" name="nama_mesin" class="form-control" value="<?php echo htmlspecialchars($machine['nama_mesin'] ?? ''); ?>" required placeholder="Contoh: Mesin Utama Gerbang Depan">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">IP Address Mesin <span style="color: red;">*</span></label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control" value="<?php echo htmlspecialchars($machine['ip_address'] ?? '192.168.1.201'); ?>" required placeholder="192.168.1.201">
                        <span style="font-size: 0.75rem; color: #64748b;">IP lokal mesin sidik jari di jaringan LAN.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Port Mesin</label>
                        <input type="number" name="port" id="port" class="form-control" value="<?php echo htmlspecialchars($machine['port'] ?? 4370); ?>" required placeholder="4370">
                        <span style="font-size: 0.75rem; color: #64748b;">Default Port ZK/EasyLink: 4370.</span>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Communication Key (Comm Key)</label>
                        <input type="text" name="comm_key" class="form-control" value="<?php echo htmlspecialchars($machine['comm_key'] ?? '0'); ?>" placeholder="0">
                        <span style="font-size: 0.75rem; color: #64748b;">Key keamanan mesin (Default: 0).</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Serial Number (SN)</label>
                        <input type="text" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($machine['serial_number'] ?? ''); ?>" placeholder="FS-101010101">
                        <span style="font-size: 0.75rem; color: #64748b;">Serial number resmi dari stiker mesin.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kode Aktivasi EasyLink</label>
                    <input type="text" name="kode_aktivasi" class="form-control" value="<?php echo htmlspecialchars($machine['kode_aktivasi'] ?? ''); ?>" placeholder="Masukkan kode aktivasi jika diperlukan">
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1.25rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify" data-icon="solar:disk-bold"></span> Simpan Pengaturan Mesin
                    </button>
                    <button type="button" class="btn btn-outline" onclick="testConnection()">
                        <span class="iconify" data-icon="solar:wifi-bold"></span> Tes Koneksi Mesin
                    </button>
                    <button type="button" class="btn btn-success" onclick="getDeviceInfo()">
                        <span class="iconify" data-icon="solar:info-circle-bold"></span> Get Info Mesin
                    </button>
                </div>
            </form>

            <div id="test-res" style="display: none; margin-top: 1.25rem; padding: 0.85rem; border-radius: 8px; font-size: 0.9rem;"></div>
            
            <!-- Box Informasi Mesin -->
            <div id="info-res" style="display: none; margin-top: 1.25rem; padding: 1.25rem; border-radius: 12px; background: #f8fafc; border: 1px solid #cbd5e1;">
                <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span class="iconify" data-icon="solar:scanner-bold" style="color: #2563eb;"></span> Spesifikasi & Informasi Mesin EasyLink
                </h4>
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.875rem;">
                        <tbody id="info-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testConnection() {
            const ip = document.getElementById('ip_address').value;
            const port = document.getElementById('port').value;
            const resBox = document.getElementById('test-res');

            resBox.style.display = 'block';
            resBox.className = 'badge-info';
            resBox.style.background = '#ecfeff';
            resBox.style.color = '#06b6d4';
            resBox.innerHTML = '<span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span> Menguji koneksi ke ' + ip + ':' + port + '...';

            const formData = new FormData();
            formData.append('ip_address', ip);
            formData.append('port', port);

            fetch('ajax.php?action=test_connection', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        resBox.style.background = '#ecfdf5';
                        resBox.style.color = '#10b981';
                        resBox.innerHTML = '✔ ' + data.message;
                    } else {
                        resBox.style.background = '#fef2f2';
                        resBox.style.color = '#ef4444';
                        resBox.innerHTML = '✖ ' + data.message;
                    }
                })
                .catch(err => {
                    resBox.style.background = '#fef2f2';
                    resBox.style.color = '#ef4444';
                    resBox.innerHTML = '✖ Terjadi kesalahan koneksi.';
                });
        }

        function getDeviceInfo() {
            const ip = document.getElementById('ip_address').value;
            const port = document.getElementById('port').value;
            const resBox = document.getElementById('test-res');
            const infoBox = document.getElementById('info-res');
            const tableBody = document.getElementById('info-table-body');

            resBox.style.display = 'block';
            resBox.style.background = '#ecfeff';
            resBox.style.color = '#06b6d4';
            resBox.innerHTML = '<span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span> Mengambil spesifikasi & informasi dari mesin EasyLink...';
            infoBox.style.display = 'none';

            const formData = new FormData();
            formData.append('ip_address', ip);
            formData.append('port', port);

            fetch('ajax.php?action=get_device_info', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status && data.info) {
                        resBox.style.background = '#ecfdf5';
                        resBox.style.color = '#10b981';
                        resBox.innerHTML = '✔ ' + data.message;
                        infoBox.style.display = 'block';

                        const info = data.info;
                        tableBody.innerHTML = `
                            <tr><td style="font-weight:600; width:180px;">Nama / Tipe Mesin</td><td><strong>${info.device_name}</strong></td></tr>
                            <tr><td style="font-weight:600;">Versi Firmware</td><td><code>${info.firmware}</code></td></tr>
                            <tr><td style="font-weight:600;">Serial Number (SN)</td><td><code>${info.serial_number}</code></td></tr>
                            <tr><td style="font-weight:600;">Platform Hardware</td><td>${info.platform}</td></tr>
                            <tr><td style="font-weight:600;">Jam Waktu Mesin</td><td><code>${info.device_time}</code></td></tr>
                            <tr><td style="font-weight:600;">Total User Terdaftar</td><td><span class="badge badge-primary">${info.total_user} User</span></td></tr>
                            <tr><td style="font-weight:600;">Total Sidik Jari (FP)</td><td><span class="badge badge-info">${info.total_fp} Template</span></td></tr>
                            <tr><td style="font-weight:600;">Total Record Log</td><td><span class="badge badge-success">${info.total_log} Scan Record</span></td></tr>
                        `;
                    } else {
                        resBox.style.background = '#fef2f2';
                        resBox.style.color = '#ef4444';
                        resBox.innerHTML = '✖ ' + data.message;
                    }
                })
                .catch(err => {
                    resBox.style.background = '#fef2f2';
                    resBox.style.color = '#ef4444';
                    resBox.innerHTML = '✖ Gagal mengambil informasi mesin.';
                });
        }
    </script>
</body>
</html>
