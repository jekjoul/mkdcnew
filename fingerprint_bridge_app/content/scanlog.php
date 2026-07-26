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

// Aksi Pengiriman Log Ke Web API Server (POST /api/presensi/sync) - Fallback Batch Chunking via PHP / Session
if ($action === 'send_logs_to_server') {
    $raw_logs_json = $_POST['payload_logs'] ?? '';
    $raw_logs      = !empty($raw_logs_json) ? json_decode($raw_logs_json, true) : ($_SESSION['bridge_logs_to_send'] ?? []);

    // Formatkan parameter secara presisi: { "pin": string, "scan_date": "YYYY-MM-DD HH:MM:SS" }
    $logs_array = [];
    if (is_array($raw_logs)) {
        foreach ($raw_logs as $item) {
            $p = trim((string)($item['pin'] ?? ''));
            $d = trim($item['scan_date'] ?? '');
            if (!empty($p) && $p !== '0' && !empty($d)) {
                $logs_array[] = [
                    'pin'       => $p,
                    'scan_date' => $d
                ];
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
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode($payload_data),
                CURLOPT_HTTPHEADER     => [
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
                CURLOPT_TIMEOUT        => 20,
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
            $alert_msg = "Berhasil mengirim " . number_format($total_sent) . " log presensi secara berkala (batch) ke Web API Server! (Baris Baru: {$total_inserted}, Diperbarui: {$total_overwrite}, Dilewati: {$total_ignored}).";
            $alert_success = true;
        } else {
            $err_summary = implode("; ", array_slice($errors, 0, 3));
            $alert_msg = "Selesai mengirim " . number_format($total_sent) . " log presensi, tetapi terdapat kesalahan: {$err_summary}";
            $alert_success = false;
        }
    } else {
        $alert_msg = "Tidak ada log presensi valid yang siap untuk dikirim. Silakan klik tombol 'Ambil Log Mesin (Paging All)' atau 'Ambil Log Baru' terlebih dahulu untuk menarik data presensi dari mesin.";
        $alert_success = false;
    }
} elseif ($action === 'delete_device_scanlog') {
    $res = EasyLinkSDK::deleteScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    $alert_msg = $res['message'];
    $alert_success = $res['status'];
}

// Ambil Scanlog Langsung dari Mesin HANYA jika tombol diklik (do_fetch == true)
$logs = [];
$logs_to_send = [];
if ($do_fetch) {
    $fetch_mode = ($mode === 'new') ? 'new' : 'all';
    $res = EasyLinkSDK::getScanlog($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], 500, $fetch_mode);

    if ($res['status'] && !empty($res['data'])) {
        $logs = $res['data'];
        $total_read  = $res['total_read'] ?? count($logs);
        $total_pages = $res['total_pages'] ?? 1;
        $mode_label  = ($fetch_mode === 'new') ? 'Presensi Baru' : "All Paging ({$total_pages} Halaman)";
        $alert_msg   = "Berhasil membaca " . count($logs) . " transaksi log presensi ({$mode_label}).";
        $alert_success = true;
    } else {
        $alert_msg   = "Gagal mengambil log presensi dari mesin: " . ($res['message'] ?? 'Respons mesin kosong.');
        $alert_success = false;
    }

    // Persiapkan array payload siap kirim ke server Web API
    foreach ($logs as $l) {
        $p = trim((string)($l['pin'] ?? ''));
        $d = trim($l['scan_date'] ?? '');
        if (!empty($p) && $p !== '0' && !empty($d)) {
            $logs_to_send[] = [
                'pin'       => $p,
                'scan_date' => $d
            ];
        }
    }

    // Simpan ke session untuk fallback POST biasa
    $_SESSION['bridge_logs_to_send'] = $logs_to_send;
} elseif (isset($_SESSION['bridge_logs_to_send']) && is_array($_SESSION['bridge_logs_to_send'])) {
    // Ambil data log dari session jika sebelumnya sudah di-fetch
    $logs_to_send = $_SESSION['bridge_logs_to_send'];
}
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
            <a href="index.php?p=scanlog&fetch=1&action=delete_device_scanlog" class="btn btn-danger" onclick="return confirm('PERHATIAN: Hapus SELURUH LOG PRESENSI di mesin EasyLink?')">
                <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Clear Log Mesin
            </a>
        </div>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $alert_success ? 'Status:' : 'Perhatian:'; ?></strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch || !empty($logs_to_send)): ?>
        <!-- Panel Statistik & Form Kirim Ke Web API Server -->
        <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a; margin-bottom: 0.25rem;">
                    Array Log Presensi Siap Kirim
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    Total Terbaca: <span class="badge badge-success"><?php echo number_format(count($logs)); ?> Record</span> |
                    Siap Dikirim: <span class="badge badge-primary"><?php echo number_format(count($logs_to_send)); ?> Item Payload</span> |
                    Target API: <code><?php echo htmlspecialchars($dev_cfg['active_sync_api']); ?></code>
                </div>
            </div>
            <?php if (!empty($logs_to_send)): ?>
                <form method="POST" action="index.php?p=scanlog" style="margin:0;" onsubmit="return startBatchSync()">
                    <input type="hidden" name="action" value="send_logs_to_server">
                    <button type="submit" class="btn btn-success" style="padding: 0.6rem 1.25rem; font-size: 0.9rem;">
                        <span class="iconify" data-icon="solar:upload-bold"></span> Kirim Scanlog Ke Server Web API (<?php echo number_format(count($logs_to_send)); ?>)
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Search & Filter Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; border: 1px solid var(--border-color); flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="iconify" data-icon="solar:magnifer-bold"></span>
                <input type="text" id="inputSearchScan" class="form-control" placeholder="Cari PIN / Date / SN..." onkeyup="filterScanlogTable()" style="width: 250px;">
            </div>
            <div style="font-weight: 600; font-size: 0.9rem;">
                Tampilan Data: <span class="badge badge-primary"><?php echo number_format(count($logs)); ?> Record</span>
                <span style="font-size: 0.8rem; color: var(--text-secondary); margin-left: 0.5rem;">(Mode: <?php echo htmlspecialchars(strtoupper($mode)); ?>)</span>
            </div>
        </div>

        <!-- Scanlog Table -->
        <div class="table-responsive">
            <table class="table" id="tableScanlog">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th width="160">Serial Number</th>
                        <th width="180">Waktu Scan (Date & Time)</th>
                        <th width="120">PIN (NIPD)</th>
                        <th width="130">Verify Mode</th>
                        <th width="130">IO Mode</th>
                        <th width="100">Work Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php $i = 1; foreach ($logs as $l): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><code><?php echo htmlspecialchars($l['sn'] ?? $dev_cfg['device_sn']); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($l['scan_date']); ?></strong></td>
                                <td><span class="badge badge-primary"><?php echo htmlspecialchars($l['pin']); ?></span></td>
                                <td>
                                    <?php if (($l['verifymode'] ?? 1) == 1): ?>
                                        <span class="badge badge-info">Sidik Jari (FP)</span>
                                    <?php elseif (($l['verifymode'] ?? 1) == 2): ?>
                                        <span class="badge badge-warning">Password</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">RFID Kartu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($l['iomode'] ?? 0) == 0): ?>
                                        <span class="badge badge-success">Masuk (In)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Keluar (Out)</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars($l['workcode'] ?? 0); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
                                Saat ini belum ada transaksi presensi terdaftar di dalam memori mesin (0 Record).
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Card Prompt Saat Pertama Kali Halaman Dibuka -->
        <div class="card text-center mb-3" style="padding: 3rem 1.5rem; background: #f8fafc; border: 1px dashed var(--border-color);">
            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:history-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Data Log Presensi Belum Dimuat</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 560px; margin: 0 auto 1.5rem auto;">
                Halaman ini sengaja tidak langsung menarik data dari mesin saat pertama kali dibuka. Klik tombol di bawah ini untuk mulai membaca transaksi scanlog via <code>scanlog/all/paging</code> dari Mesin EasyLink (<?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?>).
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <a href="index.php?p=scanlog&fetch=1&mode=all" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:download-bold"></span> Ambil Seluruh Log Presensi (Get All Scanlog Paging)
                </a>
                <a href="index.php?p=scanlog&fetch=1&mode=new" class="btn btn-success" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:refresh-bold"></span> Ambil Log Presensi Baru (Get New Scanlog)
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Live Progress Sync Batch -->
<div id="modalBatchSync" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 99999; justify-content: center; align-items: center;">
    <div style="background: #ffffff; padding: 2rem; border-radius: 1rem; width: 90%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); text-align: center;">
        <div style="font-size: 2.5rem; color: #2563eb; margin-bottom: 0.75rem;">
            <span class="iconify" data-icon="solar:upload-bold-duotone"></span>
        </div>
        <h4 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;" id="syncModalTitle">Mengirim Scanlog Ke Server Web API</h4>
        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem;" id="syncModalSub">Proses pengiriman berkala dalam batch agar aman dari timeout server.</p>

        <!-- Progress Bar Container -->
        <div style="background: #e2e8f0; border-radius: 9999px; height: 16px; overflow: hidden; margin-bottom: 1rem; position: relative;">
            <div id="syncProgressBar" style="background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%); width: 0%; height: 100%; transition: width 0.3s ease;"></div>
        </div>

        <div style="font-weight: 700; font-size: 1.25rem; color: #1e293b; margin-bottom: 0.5rem;" id="syncProgressPercent">0%</div>
        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 1rem;" id="syncProgressText">Mempersiapkan batch...</div>

        <!-- Detail Badge Stats -->
        <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem; flex-wrap: wrap;" id="syncStatsContainer">
            <span class="badge badge-success" id="badgeInserted">Baris Baru: 0</span>
            <span class="badge badge-warning" id="badgeOverwrite">Diperbarui: 0</span>
            <span class="badge badge-secondary" id="badgeIgnored">Dilewati: 0</span>
        </div>

        <div style="margin-top: 1.5rem; display: none;" id="syncDoneBtnContainer">
            <button type="button" class="btn btn-primary" onclick="window.location.reload();">Selesai & Muat Ulang</button>
        </div>
    </div>
</div>

<script>
const rawLogsToSend = <?php echo json_encode($logs_to_send, JSON_UNESCAPED_SLASHES); ?>;
const activeSyncApi = <?php echo json_encode($dev_cfg['active_sync_api']); ?>;
const activeApiKey  = <?php echo json_encode($dev_cfg['active_api_key']); ?>;

function filterScanlogTable() {
    const query = document.getElementById('inputSearchScan').value.toLowerCase();
    const rows = document.querySelectorAll('#tableScanlog tbody tr');

    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(query) ? '' : 'none';
    });
}

async function startBatchSync() {
    if (!rawLogsToSend || rawLogsToSend.length === 0) {
        alert("Belum ada log presensi yang dimuat. Silakan klik tombol 'Ambil Log Mesin (Paging All)' atau 'Ambil Log Baru' terlebih dahulu untuk menarik data presensi dari mesin.");
        return false;
    }

    if (!confirm("Kirim " + rawLogsToSend.length.toLocaleString() + " log presensi secara berkala (batch) ke Web API Server?")) {
        return false;
    }

    const batchSize = 300;
    const totalLogs = rawLogsToSend.length;
    const totalBatches = Math.ceil(totalLogs / batchSize);

    document.getElementById('modalBatchSync').style.display = 'flex';
    document.getElementById('syncDoneBtnContainer').style.display = 'none';

    let totalInserted  = 0;
    let totalOverwrite = 0;
    let totalIgnored   = 0;
    let errors = [];

    for (let b = 0; b < totalBatches; b++) {
        const start = b * batchSize;
        const end = Math.min(start + batchSize, totalLogs);
        const chunk = rawLogsToSend.slice(start, end);

        const currentBatchNum = b + 1;
        const progressPercent = Math.round((currentBatchNum / totalBatches) * 100);

        document.getElementById('syncProgressBar').style.width = progressPercent + '%';
        document.getElementById('syncProgressPercent').innerText = progressPercent + '%';
        document.getElementById('syncProgressText').innerText = 'Mengirim Batch ' + currentBatchNum + ' dari ' + totalBatches + ' (' + chunk.length + ' log)...';

        try {
            const payload = {
                token: activeApiKey,
                logs: chunk.map(item => ({
                    pin: item.pin,
                    scan_date: item.scan_date
                }))
            };

            const response = await fetch(activeSyncApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify(payload)
            });

            const resJson = await response.json();
            if (resJson && resJson.status && resJson.status.toLowerCase() === 'success') {
                totalInserted  += (resJson.inserted || 0);
                totalOverwrite += (resJson.overwrite || 0);
                totalIgnored   += (resJson.ignored || 0);

                document.getElementById('badgeInserted').innerText = 'Baris Baru: ' + totalInserted.toLocaleString();
                document.getElementById('badgeOverwrite').innerText = 'Diperbarui: ' + totalOverwrite.toLocaleString();
                document.getElementById('badgeIgnored').innerText = 'Dilewati: ' + totalIgnored.toLocaleString();
            } else {
                errors.push('Batch ' + currentBatchNum + ': ' + (resJson.message || 'Error'));
            }
        } catch (err) {
            errors.push('Batch ' + currentBatchNum + ': ' + err.message);
        }
    }

    document.getElementById('syncProgressBar').style.width = '100%';
    document.getElementById('syncProgressPercent').innerText = '100%';

    if (errors.length === 0) {
        document.getElementById('syncModalTitle').innerText = 'Pengiriman Presensi Selesai!';
        document.getElementById('syncModalSub').innerText = 'Berhasil mengunggah ' + totalLogs.toLocaleString() + ' log presensi secara berkala (batch) ke Web API Server.';
    } else {
        document.getElementById('syncModalTitle').innerText = 'Pengiriman Selesai dengan Kendala';
        document.getElementById('syncModalSub').innerText = 'Beberapa batch mengalami masalah: ' + errors.slice(0, 2).join(', ');
    }

    document.getElementById('syncDoneBtnContainer').style.display = 'block';
    return false;
}
</script>
