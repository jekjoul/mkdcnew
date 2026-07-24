<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';
require_once __DIR__ . '/lib/EasyLinkSDK.php';

if (!isset($_SESSION['fp_bridge_admin'])) {
    header('Location: login.php');
    exit;
}

$settings   = BridgeStorage::getSettings();
$machine    = BridgeStorage::getMachine();
$active_url = BridgeStorage::getActiveEndpointUrl();
$history    = BridgeStorage::getSyncHistory(50);
$scan_res   = EasyLinkSDK::getScanlog($machine['ip_address'], $machine['port'], $machine['serial_number'], $machine['comm_key']);
$machine_logs = $scan_res['logs'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Presensi - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style>
        .terminal-console {
            background: #111827;
            color: #10b981;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.825rem;
            height: 300px;
            overflow-y: auto;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #374151;
            line-height: 1.6;
        }
        .terminal-line {
            margin-bottom: 0.25rem;
        }
        .text-info-log { color: #38bdf8; }
        .text-success-log { color: #34d399; }
        .text-danger-log { color: #f87171; }
        .text-warning-log { color: #fbbf24; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="app-container">
        <!-- Control Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:calendar-mark-bold" style="color: #2563eb;"></span> Sinkronisasi Asinkron Presensi (Batch Mode MKDC Client v1.1.0)
                </h3>
                <button class="btn btn-outline" onclick="loadMachineScanlogs()">
                    <span class="iconify" data-icon="solar:restart-bold"></span> Muat Ulang Log Mesin
                </button>
            </div>
            
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div>
                        <span style="color: #64748b; font-size: 0.8rem; display: block;">Target Environment</span>
                        <strong style="text-transform: uppercase; color: #0f172a;"><?php echo htmlspecialchars($settings['env_mode'] ?? 'DEV'); ?> Mode</strong>
                    </div>
                    <div>
                        <span style="color: #64748b; font-size: 0.8rem; display: block;">Endpoint API URL</span>
                        <code><?php echo htmlspecialchars($active_url); ?></code>
                    </div>
                    <div>
                        <span style="color: #64748b; font-size: 0.8rem; display: block;">Mesin Sumber</span>
                        <strong><?php echo htmlspecialchars($machine['nama_mesin'] ?? 'Mesin EasyLink'); ?> (<code><?php echo htmlspecialchars($machine['ip_address'] ?? '127.0.0.1'); ?>:<?php echo htmlspecialchars($machine['port'] ?? 4370); ?></code>)</strong>
                    </div>
                </div>
            </div>

            <!-- Mode Selector & Start Button -->
            <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem;">Pilih Mode Penarikan Log:</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="sync_mode" value="new" checked> <strong>Scanlog Baru (New)</strong>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="sync_mode" value="all"> <strong>Semua Scanlog (All)</strong>
                        </label>
                    </div>
                </div>
                <div>
                    <button id="btn-start-sync" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;" onclick="startBatchSync()">
                        <span class="iconify" data-icon="solar:play-bold"></span> Mulai Penarikan Presensi (Batch Console)
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Activity Console & Batch Execution Panel (Persis v_sync_process.php MKDC Client) -->
        <div id="batchProcessPanel" class="card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title" style="color: #d97706;">
                    <span class="iconify" data-icon="solar:restart-bold" style="animation: spin 1s linear infinite;"></span> Proses Sinkronisasi Presensi (Batch Mode)
                </h3>
            </div>
            
            <div class="card-body">
                <!-- Progress Box -->
                <div style="max-width: 700px; margin: 0 auto 1.5rem auto; text-align: center;">
                    <h5 style="margin-bottom: 0.75rem; color: #475569; font-size: 0.95rem;">
                        Mesin: <span style="color: #2563eb; font-weight: 700;"><?php echo htmlspecialchars($machine['ip_address'] ?? '127.0.0.1'); ?>:<?php echo htmlspecialchars($machine['port'] ?? 4370); ?></span> (SN: <code><?php echo htmlspecialchars($machine['serial_number'] ?? '-'); ?></code>)
                    </h5>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">
                        <span id="syncStatusLabel">Menghubungkan ke mesin & memulai penarikan batch...</span>
                        <span id="syncPercentText">10%</span>
                    </div>

                    <div style="background: #e2e8f0; border-radius: 9999px; height: 20px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                        <div id="syncProgressBar" style="background: linear-gradient(90deg, #f59e0b, #eab308); width: 10%; height: 100%; font-weight: 700; font-size: 0.75rem; color: #000; display: flex; align-items: center; justify-content: center; transition: width 0.3s ease;">10%</div>
                    </div>
                </div>

                <!-- Terminal Logger Box -->
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.4rem;">
                        <span><span class="iconify" data-icon="solar:code-bold"></span> Live Activity Console</span>
                        <span class="badge badge-warning" id="syncBadgeState">Sinkronisasi Berjalan</span>
                    </div>
                    <div class="terminal-console" id="terminalLogConsole">
                        <div class="terminal-line text-info-log">[INFO] Logger diinisialisasi...</div>
                    </div>
                </div>

                <!-- Action Button -->
                <div style="text-align: center; margin-top: 1rem;">
                    <button onclick="location.reload()" class="btn btn-success" id="btnBackSync" style="display: none; padding: 0.75rem 2rem;">
                        <span class="iconify" data-icon="solar:check-circle-bold"></span> Selesai & Muat Ulang Halaman
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Live Data Scanlog dari Mesin -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:scanner-bold" style="color: #10b981;"></span> Data Scanlog Presensi Terbaca Dari Mesin EasyLink (<span id="log-count"><?php echo count($machine_logs); ?></span>)
                </h3>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>PIN / NIPD</th>
                            <th>Waktu Scan (Scan Date)</th>
                            <th>Serial Number (SN)</th>
                            <th>Verify Mode</th>
                            <th>IO Mode</th>
                        </tr>
                    </thead>
                    <tbody id="table-scanlog-body">
                        <?php if (empty($machine_logs)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada log presensi terbaca. Pastikan mesin terhubung dan memiliki log presensi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($machine_logs as $idx => $l): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><code><?php echo htmlspecialchars($l['pin']); ?></code></td>
                                    <td><strong><?php echo htmlspecialchars($l['scan_date']); ?></strong></td>
                                    <td><code><?php echo htmlspecialchars($l['sn'] ?? $machine['serial_number'] ?? '-'); ?></code></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($l['verifymode'] ?? 1); ?></span></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($l['iomode'] ?? 0); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Pengiriman Log -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:history-bold" style="color: #64748b;"></span> Riwayat Pengiriman Log Ke Server API
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Waktu Kirim</th>
                            <th>Status</th>
                            <th>Pesan / Hasil Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_push = false;
                        $counter = 1;
                        foreach ($history as $h): 
                            if (($h['type'] ?? '') === 'push_presensi'):
                                $has_push = true;
                        ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><code><?php echo htmlspecialchars($h['timestamp'] ?? '-'); ?></code></td>
                                <td>
                                    <?php if (($h['status'] ?? '') === 'success'): ?>
                                        <span class="badge badge-success">SUKSES</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">GAGAL</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($h['message'] ?? '-'); ?></td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        if (!$has_push):
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Belum ada riwayat pengiriman presensi. Klik tombol di atas untuk mengirimkan log.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let totalProcessed = 0;
        let batchNo = 1;
        const sn   = "<?php echo htmlspecialchars($machine['serial_number'] ?? '6668601649075'); ?>";
        const port = "<?php echo htmlspecialchars($machine['port'] ?? 8080); ?>";
        const ip   = "<?php echo htmlspecialchars($machine['ip_address'] ?? '127.0.0.1'); ?>";
        let mode   = 'new';

        function logConsole(msg, textClass = 'text-success-log') {
            const time = new Date().toLocaleTimeString();
            const div  = document.createElement('div');
            div.className = `terminal-line ${textClass}`;
            div.innerHTML = `[${time}] ${msg}`;
            const consoleBox = document.getElementById('terminalLogConsole');
            consoleBox.appendChild(div);
            consoleBox.scrollTop = consoleBox.scrollHeight;
        }

        function updateProgress(percent, statusText) {
            const bar = document.getElementById('syncProgressBar');
            const percentText = document.getElementById('syncPercentText');
            bar.style.width = percent + '%';
            bar.innerText = Math.round(percent) + '%';
            percentText.innerText = Math.round(percent) + '%';
            document.getElementById('syncStatusLabel').innerText = statusText;
        }

        function startBatchSync() {
            mode = document.querySelector('input[name="sync_mode"]:checked').value;
            totalProcessed = 0;
            batchNo = 1;

            document.getElementById('btn-start-sync').disabled = true;
            document.getElementById('batchProcessPanel').style.display = 'block';

            logConsole(`====================================================`, 'text-warning-log');
            logConsole(`[INFO] Memulai sinkronisasi presensi (Mode: ${mode.toUpperCase()})...`, 'text-info-log');
            
            setTimeout(doSyncBatch, 800);
        }

        function doSyncBatch() {
            updateProgress(40, `Batch #${batchNo}: Mengambil log dari mesin lokal...`);

            const formData = new FormData();
            formData.append('sn', sn);
            formData.append('port', port);
            formData.append('ip', ip);
            formData.append('mode', mode);

            fetch('ajax.php?action=ajaxFetchLog', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(response => {
                    if (!response || !response.Data) {
                        logConsole(`[WARN] Respons dari mesin kosong atau terputus.`, 'text-warning-log');
                        finishSync();
                        return;
                    }

                    const logs = response.Data;
                    const logsCount = logs.length;

                    if (logsCount === 0) {
                        logConsole(`[INFO] Tidak ada data presensi baru di mesin.`, 'text-info-log');
                        finishSync();
                        return;
                    }

                    logConsole(`[INFO] Batch #${batchNo}: Berhasil menarik ${logsCount} data dari mesin.`, 'text-warning-log');
                    updateProgress(75, `Batch #${batchNo}: Mengirimkan ${logsCount} data ke server API...`);

                    // Format log ke JSON payload persis MKDC Client v1.1.0
                    let logMentah = "";
                    logs.forEach(l => {
                        let pinVal = String(l.PIN || l.pin || '').replace(/\s+/g, '');
                        if (logMentah !== "") logMentah += ",";
                        logMentah += `{"pin":"${pinVal}","tgl_scanlog":"${l.ScanDate || l.scan_date}","sn_device":"${l.SN || sn}","verifymode":"${l.VerifyMode || 1}","iomode":"${l.IOMode || 0}"}`;
                    });
                    const dataPayload = `{"Result":true,"Presensi":${logsCount},"Data":[${logMentah}]}`;

                    const formDataUpload = new FormData();
                    formDataUpload.append('data', dataPayload);

                    fetch('ajax.php?action=ajaxUploadBatch', { method: 'POST', body: formDataUpload })
                        .then(resUpload => resUpload.json())
                        .then(uploadRes => {
                            const alertMsg = uploadRes.alert || 'Data tersimpan di server';
                            totalProcessed += logsCount;

                            logConsole(`[SUCCESS] Batch #${batchNo}: ${alertMsg} (Total Terupload: ${totalProcessed} data)`, 'text-success-log');

                            // Jika ada paging IsSession = true, panggil batch berikutnya secara rekursif
                            if (response.IsSession === true) {
                                batchNo++;
                                setTimeout(doSyncBatch, 500);
                            } else {
                                finishSync();
                            }
                        })
                        .catch(err => {
                            logConsole(`[ERROR] Batch #${batchNo} Gagal upload ke server API.`, 'text-danger-log');
                            errorSync();
                        });
                })
                .catch(err => {
                    logConsole(`[ERROR] Gagal berkomunikasi dengan mesin lokal di ${ip}:${port}`, 'text-danger-log');
                    errorSync();
                });
        }

        function finishSync() {
            updateProgress(100, `Sinkronisasi Selesai! Berhasil mengunggah total ${totalProcessed} data presensi.`);
            logConsole(`====================================================`, 'text-info-log');
            logConsole(`[SUCCESS] SINKRONISASI SELESAI`, 'text-success-log');
            logConsole(`[INFO] Total data presensi berhasil dipindahkan: ${totalProcessed} log`, 'text-success-log');
            logConsole(`====================================================`, 'text-info-log');

            const badge = document.getElementById('syncBadgeState');
            badge.className = 'badge badge-success';
            badge.innerText = 'Sinkronisasi Sukses';

            document.getElementById('btnBackSync').style.display = 'inline-block';
            document.getElementById('btn-start-sync').disabled = false;

            loadMachineScanlogs();
        }

        function errorSync() {
            document.getElementById('syncStatusLabel').innerText = "Sinkronisasi terhenti karena terjadi error. Silakan periksa koneksi.";
            logConsole(`[ERROR] SINKRONISASI GAGAL & TERHENTI`, 'text-danger-log');

            const badge = document.getElementById('syncBadgeState');
            badge.className = 'badge badge-danger';
            badge.innerText = 'Sinkronisasi Gagal';

            document.getElementById('btnBackSync').style.display = 'inline-block';
            document.getElementById('btn-start-sync').disabled = false;
        }

        function loadMachineScanlogs() {
            const tableBody = document.getElementById('table-scanlog-body');
            const countLabel = document.getElementById('log-count');
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#2563eb; padding:1.5rem;"><span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span> Membaca scanlog dari mesin EasyLink...</td></tr>';

            fetch('ajax.php?action=fetch_machine_scanlogs', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.logs && data.logs.length > 0) {
                        countLabel.innerText = data.logs.length;
                        tableBody.innerHTML = data.logs.map((l, i) => `
                            <tr>
                                <td>${i + 1}</td>
                                <td><code>${l.pin}</code></td>
                                <td><strong>${l.scan_date}</strong></td>
                                <td><code>${l.sn || '-'}</code></td>
                                <td><span class="badge badge-info">${l.verifymode || 1}</span></td>
                                <td><span class="badge badge-primary">${l.iomode || 0}</span></td>
                            </tr>
                        `).join('');
                    } else {
                        countLabel.innerText = '0';
                        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:1.5rem;">${data.message || 'Tidak ada data scanlog.'}</td></tr>`;
                    }
                })
                .catch(err => {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#ef4444; padding:1.5rem;">Terjadi kesalahan koneksi AJAX.</td></tr>';
                });
        }
    </script>
</body>
</html>
