<?php
$dev_cfg  = getActiveDeviceConfig();
$do_fetch = (isset($_REQUEST['fetch']) && $_REQUEST['fetch'] == '1');
$info     = [];
$raw_json = [];
$res_info = null;

if ($do_fetch) {
    $res_info = EasyLinkSDK::getDeviceInfo($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    $info     = $res_info['info'] ?? [];
    $raw_json = $res_info['raw_json'] ?? [];
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:info-square-bold" style="color: var(--primary);"></span> Detail Informasi Perangkat Mesin (DEVINFO)
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Membaca spesifikasi hardware, firmware, jam perangkat, dan kapasitas memori dari WebService EasyLink (<code>/dev/info</code>). Data hanya ditarik saat tombol diklik.
            </div>
        </div>
        <a href="index.php?p=info&fetch=1" class="btn btn-primary">
            <span class="iconify" data-icon="solar:restart-bold"></span> Ambil Device Info
        </a>
    </div>

    <!-- Alert Box Notification -->
    <?php if ($res_info !== null): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; background: <?php echo $res_info['status'] ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $res_info['status'] ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $res_info['status'] ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong>Status:</strong> <?php echo htmlspecialchars($res_info['message']); ?>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch): ?>
        <div class="grid grid-2">
            <!-- Key Device Metrics -->
            <div>
                <h3 class="card-title mb-2" style="font-size: 1rem;">
                    <span class="iconify" data-icon="solar:cpu-bold"></span> Spesifikasi Perangkat
                </h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <td width="160"><strong>Nama Perangkat</strong></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($info['device_name'] ?? '-'); ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>IP Address</strong></td>
                            <td><code><?php echo htmlspecialchars($dev_cfg['server_IP']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Port WebService</strong></td>
                            <td><code><?php echo htmlspecialchars($dev_cfg['server_port']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Serial Number (SN)</strong></td>
                            <td><code><?php echo htmlspecialchars($info['serial_number'] ?? $dev_cfg['device_sn']); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Versi Firmware</strong></td>
                            <td><code><?php echo htmlspecialchars($info['firmware'] ?? '-'); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Platform Hardware</strong></td>
                            <td><code><?php echo htmlspecialchars($info['platform'] ?? '-'); ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Perangkat</strong></td>
                            <td><strong><?php echo htmlspecialchars($info['device_time'] ?? date('Y-m-d H:i:s')); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Capacity Statistics -->
            <div>
                <h3 class="card-title mb-2" style="font-size: 1rem;">
                    <span class="iconify" data-icon="solar:pie-chart-2-bold"></span> Penggunaan Memori Mesin
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Kapasitas User Terdaftar</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #0f172a;"><?php echo number_format($info['total_user'] ?? 0); ?> User</div>
                        </div>
                        <div style="font-size: 2rem; color: var(--primary);">
                            <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Kapasitas Sidik Jari (FP)</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #0f172a;"><?php echo number_format($info['total_fp'] ?? 0); ?> Template</div>
                        </div>
                        <div style="font-size: 2rem; color: var(--success);">
                            <span class="iconify" data-icon="solar:fingerprint-bold"></span>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Kapasitas Log Presensi</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #0f172a;"><?php echo number_format($info['total_log'] ?? 0); ?> Record</div>
                        </div>
                        <div style="font-size: 2rem; color: var(--warning);">
                            <span class="iconify" data-icon="solar:history-bold"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Card Prompt Saat Pertama Kali Halaman Dibuka -->
        <div class="card text-center mb-3" style="padding: 3rem 1.5rem; background: #f8fafc; border: 1px dashed var(--border-color);">
            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:info-square-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Informasi Mesin Belum Dimuat</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 560px; margin: 0 auto 1.5rem auto;">
                Halaman ini sengaja tidak langsung mengambil spesifikasi dari mesin saat pertama kali dimuat. Klik tombol di bawah ini untuk membaca spesifikasi hardware & statistik memori dari Mesin EasyLink (<?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?>).
            </p>
            <div>
                <a href="index.php?p=info&fetch=1" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:play-bold"></span> Baca Device Info Dari Mesin
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($do_fetch && !empty($raw_json)): ?>
    <!-- Raw JSON Response Output -->
    <div class="card">
        <h3 class="card-title mb-2" style="font-size: 1rem;">
            <span class="iconify" data-icon="solar:code-circle-bold"></span> Raw Response JSON (dev/info)
        </h3>
        <pre class="terminal-box" style="max-height: 250px; overflow-y: auto; background: #0f172a; color: #38bdf8; padding: 1rem; border-radius: var(--radius-md); font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars(json_encode($raw_json, JSON_PRETTY_PRINT)); ?></pre>
    </div>
<?php endif; ?>
