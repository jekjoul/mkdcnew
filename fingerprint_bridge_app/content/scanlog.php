<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dev_cfg = getActiveDeviceConfig();
$alert_msg = '';
$alert_success = true;

// Tangani Aksi POST/GET dari Form PHP Biasa
$action   = $_REQUEST['action'] ?? '';
$mode     = $_REQUEST['mode'] ?? 'all';

// Jalankan fetch dari mesin HANYA jika tombol fetch diklik DAN BUKAN saat tombol kirim ke server diklik
$do_fetch = (isset($_REQUEST['fetch']) && $_REQUEST['fetch'] == '1') && ($action !== 'send_logs_to_server');

require_once __DIR__ . '/../lib/BridgeDB.php';

// Aksi Pengiriman Log Ke Web API Server (POST /api/presensi/sync) - SQLite DB Engine
if ($action === 'send_logs_to_server') {
    // Prioritas Utama: Ambil data pending dari SQLite Database Bridge App
    $db_pending = BridgeDB::getPendingScanlogs();
    $raw_logs   = $db_pending;

    // Fallback: POST payload / Cache jika SQLite DB kosong
    if (empty($raw_logs)) {
        $raw_logs_json = $_POST['payload_logs'] ?? '';
        if (!empty($raw_logs_json)) {
            $decoded = json_decode(base64_decode($raw_logs_json), true);
            if (is_array($decoded)) {
                $raw_logs = $decoded;
            }
        }
    }
    if (empty($raw_logs) && file_exists($cache_file)) {
        $cache_data = json_decode(@file_get_contents($cache_file), true);
        if (is_array($cache_data)) {
            $raw_logs = $cache_data['logs_to_send'] ?? $cache_data['logs'] ?? [];
        }
    }

    // Formatkan parameter payload
    $logs_array = [];
    $log_ids    = [];
    if (is_array($raw_logs)) {
        foreach ($raw_logs as $item) {
            if (!is_array($item)) continue;
            $p = trim((string)($item['pin'] ?? $item['PIN'] ?? $item['user_id'] ?? $item['UserId'] ?? ''));
            $d = trim((string)($item['scan_date'] ?? $item['ScanDate'] ?? $item['date'] ?? $item['Date'] ?? ''));
            if (!empty($p) && $p !== '0' && !empty($d)) {
                $logs_array[] = [
                    'pin'       => $p,
                    'scan_date' => $d
                ];
                if (isset($item['id'])) {
                    $log_ids[] = intval($item['id']);
                }
            }
        }
    }

    if (!empty($logs_array)) {
        $sync_url = $dev_cfg['active_sync_api'];
        $api_key  = $dev_cfg['active_api_key'];

        // Bagi data menjadi batch/chunk berukuran 300 item per request agar tidak timeout
        $chunks          = array_chunk($logs_array, 300);
        $total_inserted  = 0;
        $total_overwrite = 0;
        $total_ignored   = 0;
        $total_sent      = 0;
        $errors          = [];

        foreach ($chunks as $idx => $chunk) {
            $payload_data = [
                'token' => $api_key,
                'logs'  => $chunk
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $sync_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode($payload_data),
                CURLOPT_HTTPHEADER     => [
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
                CURLOPT_TIMEOUT        => 25,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response  = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err       = curl_error($ch);
            curl_close($ch);

            if ($err) {
                $errors[] = "Batch " . ($idx + 1) . " gagal: {$err}";
            } else {
                $json_res = json_decode($response, true);
                if (is_array($json_res) && (isset($json_res['status']) && strtolower($json_res['status']) === 'success')) {
                    $total_inserted  += intval($json_res['inserted'] ?? 0);
                    $total_overwrite += intval($json_res['overwrite'] ?? 0);
                    $total_ignored   += intval($json_res['ignored'] ?? 0);
                    $total_sent      += count($chunk);
                } else {
                    $err_msg = is_array($json_res) && isset($json_res['message']) ? $json_res['message'] : "HTTP {$http_code}";
                    $errors[] = "Batch " . ($idx + 1) . " gagal: {$err_msg}";
                }
            }
        }

        if (empty($errors)) {
            // SETELAH DIKIRIM KE API, LANGSUNG DIHAPUS DARI DATABASE LOKAL BRIDGE APP
            BridgeDB::deleteSentLogs($log_ids);
            @unlink($cache_file);
            unset($_SESSION['bridge_raw_logs'], $_SESSION['bridge_logs_to_send']);

            $alert_msg = "Berhasil mengirim " . number_format($total_sent) . " log presensi ke Server Web API! Data log di database lokal Bridge App telah dihapus secara otomatis. (Baris Baru: {$total_inserted}, Diperbarui: {$total_overwrite}, Dilewati: {$total_ignored}).";
            $alert_success = true;
        } else {
            $err_summary = implode("; ", array_slice($errors, 0, 3));
            $alert_msg = "Selesai mengirim " . number_format($total_sent) . " log presensi, tetapi terdapat kesalahan: {$err_summary}";
            $alert_success = false;
        }
    } else {
        $alert_msg = "Tidak ada log presensi valid yang siap untuk dikirim di database lokal. Silakan klik tombol 'Ambil Log Mesin (Paging All)' terlebih dahulu.";
        $alert_success = false;
    }
} elseif ($action === 'delete_device_scanlog') {
    $res = EasyLinkSDK::deleteScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    $alert_msg = $res['message'];
    $alert_success = $res['status'];
    BridgeDB::clearAllLogs();
    @unlink($cache_file);
    unset($_SESSION['bridge_raw_logs'], $_SESSION['bridge_logs_to_send']);
} elseif ($action === 'generate_dummy_logs') {
    // Buat data dummy scanlog untuk dimasukkan ke database lokal Bridge App
    $today = date('Y-m-d');
    $dummy_samples = [
        ['pin' => '2526010035', 'scan_date' => $today . ' 06:45:12', 'sn' => $dev_cfg['device_sn'], 'verifymode' => 1, 'iomode' => 0, 'workcode' => 0],
        ['pin' => '2526010043', 'scan_date' => $today . ' 06:50:30', 'sn' => $dev_cfg['device_sn'], 'verifymode' => 1, 'iomode' => 0, 'workcode' => 0],
        ['pin' => '2526010002', 'scan_date' => $today . ' 12:15:00', 'sn' => $dev_cfg['device_sn'], 'verifymode' => 1, 'iomode' => 0, 'workcode' => 0]
    ];
    $inserted_db = BridgeDB::insertScanlogs($dummy_samples);
    $alert_msg = "Berhasil menyimpan {$inserted_db} data dummy scanlog presensi ke database lokal Bridge App. Siap dikirim ke Server Web API.";
    $alert_success = true;
}

// Ambil Scanlog Langsung dari Mesin HANYA jika tombol diklik (do_fetch == true)
if ($do_fetch) {
    $fetch_mode = ($mode === 'new') ? 'new' : 'all';
    $res = EasyLinkSDK::getScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], 500, $fetch_mode);

    if ($res['status'] && !empty($res['data'])) {
        $fetched_logs = $res['data'];
        $inserted_db  = BridgeDB::insertScanlogs($fetched_logs);

        $total_pages = $res['total_pages'] ?? 1;
        $mode_label  = ($fetch_mode === 'new') ? 'Presensi Baru' : "All Paging ({$total_pages} Halaman)";
        $alert_msg   = "Berhasil membaca " . count($fetched_logs) . " transaksi log dari mesin dan menyimpan {$inserted_db} record baru ke database lokal Bridge App.";
        $alert_success = true;
    } else {
        $alert_msg   = "Gagal/Tidak ada data log presensi terbaca dari mesin: " . ($res['message'] ?? 'Respons mesin kosong.');
        $alert_success = false;
    }
}

// Selalu baca data log presensi pending dari SQLite Database Bridge App untuk tampilan UI
$logs = BridgeDB::getPendingScanlogs();
$logs_to_send = [];
foreach ($logs as $l) {
    $p = trim((string)($l['pin'] ?? ''));
    $d = trim((string)($l['scan_date'] ?? ''));
    if (!empty($p) && $p !== '0' && !empty($d)) {
        $logs_to_send[] = [
            'pin'       => $p,
            'scan_date' => $d
        ];
    }
}

$json_textarea_val = !empty($logs_to_send) ? json_encode($logs_to_send, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Data Scanlog Mesin Presensi</h2>
            <p style="font-size: 0.9rem; color: #64748b; margin: 0;">Integrasi Transaksi Presensi EasyLink SDK ke Web API Data Center</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="index.php?p=scanlog&fetch=1&mode=all" class="btn btn-primary">
                <span class="iconify" data-icon="solar:download-bold"></span> Ambil Log Mesin (Paging All)
            </a>
            <a href="index.php?p=scanlog&fetch=1&mode=new" class="btn btn-success">
                <span class="iconify" data-icon="solar:refresh-bold"></span> Ambil Log Baru (Get New)
            </a>
            <a href="index.php?p=scanlog&action=generate_dummy_logs" class="btn btn-secondary">
                <span class="iconify" data-icon="solar:test-tube-bold"></span> Dummy Scanlog Test
            </a>
            <a href="index.php?p=scanlog&fetch=1&action=delete_device_scanlog" class="btn btn-danger" onclick="return confirm('PERHATIAN: Hapus SELURUH LOG PRESENSI di database lokal Bridge App?')">
                <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Clear Log Database
            </a>
        </div>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $alert_success ? 'Status:' : 'Perhatian:'; ?></strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

<!-- Form Textarea JSON Payload Siap Kirim Ke Web API -->
    <div>
        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 0.2rem;">
                        Payload JSON Data Scanlog Siap Kirim (<?php echo number_format(count($logs_to_send)); ?> Record Pending)
                    </h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
                        Target Server Web API: <code><?php echo htmlspecialchars($dev_cfg['active_sync_api']); ?></code>
                    </p>
                </div>
                
                <?php if (!empty($logs_to_send)): ?>
                    <button type="button" class="btn btn-success" onclick="startBatchSync()" style="padding: 0.65rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                        <span class="iconify" data-icon="solar:upload-bold" style="font-size: 1.1rem; margin-right: 0.3rem;"></span> Kirim Scanlog Ke Server Web API (<?php echo number_format(count($logs_to_send)); ?>)
                    </button>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem; display: block;">
                    Format JSON Payload Data Log (Dapat Diperiksa / Diedit Sebelum Dikirim):
                </label>
                <textarea name="payload_json" id="payloadJsonArea" class="form-control" rows="15" style="font-family: 'Fira Code', 'Courier New', monospace; font-size: 0.88rem; background: #0f172a; color: #38bdf8; border: 1px solid #1e293b; padding: 1rem; border-radius: 8px; width: 100%; white-space: pre;" placeholder="Saat ini belum ada log presensi yang dimuat. Klik tombol 'Ambil Log Mesin (Paging All)' atau 'Dummy Scanlog Test' terlebih dahulu."><?php echo htmlspecialchars($json_textarea_val); ?></textarea>
            </div>

            <?php if (!empty($logs_to_send)): ?>
                <div style="display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-success" onclick="startBatchSync()" style="padding: 0.75rem 1.75rem; font-size: 1rem; font-weight: 600; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                        <span class="iconify" data-icon="solar:upload-bold" style="font-size: 1.2rem; margin-right: 0.4rem;"></span> Kirim Scanlog Ke Server Web API (<?php echo number_format(count($logs_to_send)); ?>)
                    </button>
                </div>
            <?php else: ?>
                <div style="padding: 1rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; text-align: center; color: #64748b; font-size: 0.9rem;">
                    Belum ada log presensi di database lokal Bridge App. Silakan klik tombol <strong>Ambil Log Mesin (Paging All)</strong> atau <strong>Dummy Scanlog Test</strong> untuk memuat data.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Live Progress Sync Batch & Terminal Log Console -->
<div id="modalBatchSync" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 99999; justify-content: center; align-items: center;">
    <div style="background: #ffffff; padding: 2rem; border-radius: 1rem; width: 92%; max-width: 680px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3); text-align: center;">
        <div style="font-size: 2.5rem; color: #2563eb; margin-bottom: 0.5rem;">
            <span class="iconify" data-icon="solar:upload-bold-duotone"></span>
        </div>
        <h4 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;" id="syncModalTitle">Mengirim Log Presensi ke Server Web API</h4>
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;" id="syncModalSub">Proses pengiriman berkala dalam batch (300 item/request) dengan pemantauan log langsung.</p>

        <!-- Progress Bar Container -->
        <div style="background: #e2e8f0; border-radius: 9999px; height: 16px; overflow: hidden; margin-bottom: 0.75rem; position: relative;">
            <div id="syncProgressBar" style="background: linear-gradient(90deg, #2563eb 0%, #16a34a 100%); width: 0%; height: 100%; transition: width 0.3s ease;"></div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div style="font-weight: 700; font-size: 1.1rem; color: #1e293b;" id="syncProgressPercent">0%</div>
            <div style="font-size: 0.85rem; color: #475569;" id="syncProgressText">Mempersiapkan pengiriman batch...</div>
        </div>

        <!-- Live Terminal Console Log Box -->
        <div id="syncLogConsole" style="background: #0f172a; color: #38bdf8; font-family: 'Fira Code', 'Courier New', monospace; font-size: 0.82rem; padding: 1rem; border-radius: 8px; text-align: left; height: 200px; overflow-y: auto; white-space: pre-wrap; margin-bottom: 1rem; border: 1px solid #1e293b;"></div>

        <!-- Detail Badge Stats -->
        <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem; flex-wrap: wrap;" id="syncStatsContainer">
            <span class="badge badge-success" id="badgeInserted">Baris Baru: 0</span>
            <span class="badge badge-warning" id="badgeOverwrite">Diperbarui: 0</span>
            <span class="badge badge-secondary" id="badgeIgnored">Dilewati: 0</span>
        </div>

        <div style="margin-top: 1.25rem; display: none;" id="syncDoneBtnContainer">
            <button type="button" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 600;" onclick="window.location.reload();">Selesai &amp; Reload Halaman</button>
        </div>
    </div>
</div>

<script>
function logToConsole(message, type = 'info') {
    const consoleBox = document.getElementById('syncLogConsole');
    const now = new Date();
    const timeStr = now.toTimeString().split(' ')[0];
    
    let icon = 'ℹ️';
    if (type === 'success') icon = '✅';
    if (type === 'error')   icon = '❌';
    if (type === 'start')   icon = '🚀';
    if (type === 'finish')  icon = '🎉';
    if (type === 'clean')   icon = '✨';

    const logLine = `[${timeStr}] ${icon} ${message}\n`;
    consoleBox.innerText += logLine;
    consoleBox.scrollTop = consoleBox.scrollHeight;
}

async function startBatchSync() {
    const textareaVal = document.getElementById('payloadJsonArea').value.trim();
    let rawLogs = [];

    try {
        if (textareaVal !== '') {
            const parsed = JSON.parse(textareaVal);
            rawLogs = Array.isArray(parsed) ? parsed : (parsed.logs || []);
        }
    } catch (e) {
        alert("Format JSON di textarea tidak valid! Periksa kembali sintaks JSON.");
        return false;
    }

    if (!rawLogs || rawLogs.length === 0) {
        alert("Belum ada data log presensi valid di dalam JSON. Silakan klik tombol 'Ambil Log Mesin (Paging All)' atau 'Dummy Scanlog Test' terlebih dahulu.");
        return false;
    }

    if (!confirm("Kirim " + rawLogs.length.toLocaleString() + " log presensi dalam batch ke Server Web API?")) {
        return false;
    }

    const batchSize = 300;
    const totalLogs = rawLogs.length;
    const totalBatches = Math.ceil(totalLogs / batchSize);

    document.getElementById('modalBatchSync').style.display = 'flex';
    document.getElementById('syncDoneBtnContainer').style.display = 'none';
    document.getElementById('syncLogConsole').innerText = '';

    logToConsole("Memulai pengiriman " + totalLogs.toLocaleString() + " log presensi (" + totalBatches + " batch) ke Web API Server...", "start");

    let totalInserted  = 0;
    let totalOverwrite = 0;
    let totalIgnored   = 0;
    let errors = [];

    for (let b = 0; b < totalBatches; b++) {
        const start = b * batchSize;
        const end = Math.min(start + batchSize, totalLogs);
        const chunk = rawLogs.slice(start, end);

        const currentBatchNum = b + 1;
        const progressPercent = Math.round((currentBatchNum / totalBatches) * 100);

        document.getElementById('syncProgressBar').style.width = progressPercent + '%';
        document.getElementById('syncProgressPercent').innerText = progressPercent + '%';
        document.getElementById('syncProgressText').innerText = 'Mengirim Batch ' + currentBatchNum + ' dari ' + totalBatches + ' (' + chunk.length + ' log)...';

        logToConsole("Mengirim Batch " + currentBatchNum + "/" + totalBatches + " (" + chunk.length + " record)...", "info");

        try {
            const formData = new FormData();
            formData.append('action', 'sync_batch_chunk');
            formData.append('chunk_logs', JSON.stringify(chunk));

            const response = await fetch('ajax.php', {
                method: 'POST',
                body: formData
            });

            const resJson = await response.json();
            if (resJson && (resJson.status === 'success' || (typeof resJson.status === 'string' && resJson.status.toLowerCase() === 'success'))) {
                const ins = resJson.inserted || 0;
                const ovr = resJson.overwrite || 0;
                const ign = resJson.ignored || 0;

                totalInserted  += ins;
                totalOverwrite += ovr;
                totalIgnored   += ign;

                document.getElementById('badgeInserted').innerText = 'Baris Baru: ' + totalInserted.toLocaleString();
                document.getElementById('badgeOverwrite').innerText = 'Diperbarui: ' + totalOverwrite.toLocaleString();
                document.getElementById('badgeIgnored').innerText = 'Dilewati: ' + totalIgnored.toLocaleString();

                logToConsole("Batch " + currentBatchNum + " Sukses! (Baris Baru: " + ins + ", Diperbarui: " + ovr + ", Dilewati: " + ign + ")", "success");
            } else {
                const errMsg = resJson.message || 'Respons server tidak valid';
                errors.push("Batch " + currentBatchNum + ": " + errMsg);
                logToConsole("Batch " + currentBatchNum + " Gagal: " + errMsg, "error");
            }
        } catch (err) {
            errors.push("Batch " + currentBatchNum + ": " + err.message);
            logToConsole("Batch " + currentBatchNum + " Error Network: " + err.message, "error");
        }
    }

    if (errors.length === 0) {
        logToConsole("Menghapus data log yang terkirim dari database lokal SQLite Bridge App...", "clean");
        try {
            const clearData = new FormData();
            clearData.append('action', 'clear_sent_logs_db');
            await fetch('ajax.php', { method: 'POST', body: clearData });
            logToConsole("Database lokal Bridge App berhasil dibersihkan (0 Record Pending)!", "clean");
        } catch (e) {}

        logToConsole("🎉 Selesai 100%! Seluruh " + totalLogs.toLocaleString() + " log presensi berhasil dikirim ke Server Web API MKDC.", "finish");
        document.getElementById('syncModalTitle').innerText = 'Pengiriman Log Presensi Selesai 100%';
    } else {
        logToConsole("⚠ Pengiriman selesai dengan kendala pada " + errors.length + " batch.", "error");
        document.getElementById('syncModalTitle').innerText = 'Pengiriman Selesai dengan Kendala';
    }

    document.getElementById('syncDoneBtnContainer').style.display = 'block';
    return false;
}
</script>
