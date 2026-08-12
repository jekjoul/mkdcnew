<!-- Alert Banner Opsi A: Aplikasi Desktop Native -->
<div class="alert bg-primary-50 border border-primary-200 radius-12 p-20 mb-24 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="w-48-px h-48-px radius-8 bg-primary-600 text-white d-flex align-items-center justify-content-center text-2xl">
            <iconify-icon icon="solar:laptop-minimalistic-bold"></iconify-icon>
        </div>
        <div>
            <h6 class="mb-4 fw-bold text-primary-light">OPSI A: Aplikasi Desktop Native Windows (FingerspotBridgeApp.exe)</h6>
            <div class="text-xs text-secondary-light">Aplikasi Desktop Resmi terintegrasi langsung dengan <code>C:\Program Files (x86)\EasyLink SDK</code> &amp; ActiveX Revo W-202BNC.</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo base_url('../fingerprint_bridge/FingerspotBridgeApp.exe') ?>" download class="btn btn-primary-600 radius-8 px-20 py-10 fw-bold">
            <iconify-icon icon="solar:download-bold" class="me-1"></iconify-icon> Download Executable (.exe)
        </a>
    </div>
</div>

<div class="row gy-4 mb-24">
    <!-- Stat 1: Mode Environment -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card p-20 shadow-sm border-0 radius-12 bg-gradient-start-1">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm d-block fw-medium">Environment API</span>
                    <h5 class="mb-0 mt-4 text-primary-600 text-uppercase fw-bold">
                        <iconify-icon icon="<?php echo $settings->env_mode === 'production' ? 'solar:cloud-storage-bold' : 'solar:code-file-bold' ?>"></iconify-icon>
                        <?php echo html_escape($settings->env_mode) ?>
                    </h5>
                </div>
                <div class="w-48-px h-48-px radius-8 bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:globus-linear"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-xs text-muted text-truncate" title="<?php echo html_escape($active_url) ?>">
                Target: <code><?php echo html_escape($active_url) ?></code>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Mesin Terdaftar -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card p-20 shadow-sm border-0 radius-12 bg-gradient-start-2">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm d-block fw-medium">Mesin Terhubung</span>
                    <h4 class="mb-0 mt-4 text-neutral-800 fw-bold"><?php echo count($machines) ?> <span class="text-sm fw-normal text-muted">Mesin</span></h4>
                </div>
                <div class="w-48-px h-48-px radius-8 bg-success-50 text-success-600 d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:scanner-linear"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 d-flex align-items-center gap-2">
                <a href="<?php echo url('fingerprint_bridge/setting') ?>" class="text-xs text-success-600 text-decoration-underline">Kelola & Tambah Mesin &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 3: Mode Sync -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card p-20 shadow-sm border-0 radius-12 bg-gradient-start-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm d-block fw-medium">Mode Sinkronisasi</span>
                    <h5 class="mb-0 mt-4 fw-bold text-primary-600">
                        <iconify-icon icon="solar:hand-stars-bold"></iconify-icon>
                        Manual Trigger
                    </h5>
                </div>
                <div class="w-48-px h-48-px radius-8 bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:restart-bold"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-xs text-muted">
                Tarik log via tombol manual
            </div>
        </div>
    </div>

    <!-- Stat 4: Absensi Hari Ini -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card p-20 shadow-sm border-0 radius-12 bg-gradient-start-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm d-block fw-medium">Log Presensi Hari Ini</span>
                    <h4 class="mb-0 mt-4 text-neutral-800 fw-bold"><?php echo number_format($total_today) ?> <span class="text-sm fw-normal text-muted">Scan</span></h4>
                </div>
                <div class="w-48-px h-48-px radius-8 bg-warning-50 text-warning-600 d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:calendar-mark-linear"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-xs text-muted">
                Tanggal: <?php echo date('d M Y') ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Control Panel -->
<div class="row gy-4">
    <!-- Left Column: Status Mesin & Manual Trigger -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm radius-12 h-100">
            <div class="card-header bg-transparent border-bottom p-24 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 text-primary-light">Daftar Mesin Fingerprint</h6>
                    <span class="text-xs text-muted">Status koneksi socket LAN pada masing-masing unit</span>
                </div>
                <button type="button" id="btn-sync-now" class="btn btn-sm btn-primary-600 radius-8 px-16 py-8 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:restart-linear" id="icon-sync-spin"></iconify-icon> Tarik Log Sekarang
                </button>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table bordered-table align-middle">
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Nama & Lokasi Mesin</th>
                                <th width="140">IP & Port LAN</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="110" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($machines as $m): 
                            ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?php echo $no++ ?></td>
                                    <td>
                                        <span class="fw-semibold text-primary-light d-block"><?php echo html_escape($m->nama_mesin) ?></span>
                                        <span class="text-xs text-muted"><?php echo html_escape($m->lokasi ?: 'Lokasi belum diset') ?> &bull; Tipe: <?php echo html_escape($m->tipe_mesin) ?></span>
                                    </td>
                                    <td>
                                        <code class="text-primary-600 fw-medium"><?php echo html_escape($m->ip_address) ?></code>
                                        <span class="text-xs text-muted d-block">Port: <?php echo $m->port ?></span>
                                    </td>
                                    <td class="text-center" id="status-badge-<?php echo $m->id_machine ?>">
                                        <?php if ($m->status === 'Online'): ?>
                                            <span class="badge bg-success-focus text-success-main px-10 py-4 radius-4">Online</span>
                                        <?php elseif ($m->status === 'Offline'): ?>
                                            <span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4">Offline</span>
                                        <?php else: ?>
                                            <span class="badge bg-neutral-200 text-neutral-600 px-10 py-4 radius-4">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-xs btn-outline-info radius-8 btn-ping-machine" data-id="<?php echo $m->id_machine ?>" title="Tes Ping Socket">
                                                <iconify-icon icon="solar:wifi-router-minimalistic-linear"></iconify-icon> PING
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary radius-8 btn-info-machine" data-id="<?php echo $m->id_machine ?>" title="Lihat Detail Info Mesin">
                                                <iconify-icon icon="solar:info-square-linear"></iconify-icon> Info
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($machines)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-24 text-muted">
                                        Belum ada mesin sidik jari yang terdaftar. <a href="<?php echo url('fingerprint_bridge/setting') ?>">Klik di sini untuk menambah mesin</a>.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Console Log activity -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm radius-12 h-100">
            <div class="card-header bg-transparent border-bottom p-24 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 text-primary-light">Konsol Live Worker Sync</h6>
                    <span class="text-xs text-muted">Aktivitas penarikan & pengiriman log ke API</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="switch-live-sync" <?php echo $settings->auto_sync_active ? 'checked' : '' ?>>
                    <label class="form-check-label text-xs fw-semibold text-secondary-light" for="switch-live-sync">Auto-Sync</label>
                </div>
            </div>
            <div class="card-body p-16 bg-neutral-900 text-light radius-bottom-12" style="font-family: monospace; font-size: 12px; min-height: 280px; max-height: 400px; overflow-y: auto;" id="console-output">
                <div class="text-success-main">[SYSTEM INITIALIZED] Fingerprint Bridge Worker Ready...</div>
                <div class="text-muted">[ENV MODE] Active: <?php echo strtoupper($settings->env_mode) ?> -> <?php echo html_escape($active_url) ?></div>
                <div class="text-muted">[INFO] Auto-sync interval: <?php echo $settings->auto_sync_interval ?> detik</div>
                <hr class="border-secondary my-8">
            </div>
        </div>
<!-- Modal Detail Info Mesin -->
<div class="modal fade" id="modalInfoMesin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light px-24 py-16">
                <h6 class="modal-title fw-semibold text-primary-light d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:info-square-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <span id="info_nama_mesin">Spesifikasi & Info Mesin Sidik Jari</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24" id="info_mesin_body">
                <div class="text-center py-24">
                    <iconify-icon icon="line-md:loading-twotone-loop" class="text-3xl text-primary-600 mb-2"></iconify-icon>
                    <div class="text-secondary-light text-sm">Menghubungi mesin via socket TCP LAN...</div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light px-24 py-16">
                <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function() {
    function appendConsole(msg, type) {
        var colorClass = 'text-light';
        if (type === 'success') colorClass = 'text-success-main';
        if (type === 'danger' || type === 'error') colorClass = 'text-danger-main';
        if (type === 'warning') colorClass = 'text-warning-main';
        if (type === 'info') colorClass = 'text-info-main';

        var timeStr = new Date().toLocaleTimeString();
        var html = '<div class="' + colorClass + '">[' + timeStr + '] ' + msg + '</div>';
        var consoleEl = $('#console-output');
        consoleEl.append(html);
        consoleEl.scrollTop(consoleEl[0].scrollHeight);
    }

    function triggerProcessSync() {
        $('#icon-sync-spin').addClass('spin-anim');
        
        $.ajax({
            url: "<?php echo url('fingerprint_bridge/process_sync') ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {
                $('#icon-sync-spin').removeClass('spin-anim');
                if (res.status === 'success') {
                    appendConsole('Sync OK | Read: ' + res.total_read + ' logs | Synced to Server: ' + res.total_synced + ' baris', 'success');
                    
                    if (res.machine_details && res.machine_details.length > 0) {
                        res.machine_details.forEach(function(m) {
                            appendConsole('  -> ' + m.nama + ' (' + m.ip + '): Status ' + m.status + ' (' + m.logs + ' logs)', m.status === 'Online' ? 'info' : 'warning');
                        });
                    }
                } else {
                    appendConsole('Sync Warning: ' + res.message, 'warning');
                }
            },
            error: function(xhr, status, err) {
                $('#icon-sync-spin').removeClass('spin-anim');
                appendConsole('Gagal terhubung ke endpoint sync lokal: ' + err, 'danger');
            }
        });
    }

    // Manual Trigger Button
    $('#btn-sync-now').on('click', function() {
        appendConsole('Menjalankan sinkronisasi manual...', 'info');
        triggerProcessSync();
    });

    // Individual Machine Ping
    $('.btn-ping-machine').on('click', function() {
        var id = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true);
        appendConsole('Ping socket mesin #' + id + '...', 'info');

        $.ajax({
            url: "<?php echo url('fingerprint_bridge/tes_koneksi_mesin/') ?>" + id,
            type: "GET",
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false);
                if (res.status) {
                    appendConsole('Hasil Ping #' + id + ': ' + res.message, 'success');
                    $('#status-badge-' + id).html('<span class="badge bg-success-focus text-success-main px-10 py-4 radius-4">Online</span>');
                } else {
                    appendConsole('Hasil Ping #' + id + ': ' + res.message, 'danger');
                    $('#status-badge-' + id).html('<span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4">Offline</span>');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                appendConsole('Error menghubungi server saat ping.', 'danger');
            }
        });
    });

    // Info Mesin Event Handler
    $('.btn-info-machine').on('click', function() {
        var id = $(this).data('id');
        var modal = new bootstrap.Modal(document.getElementById('modalInfoMesin'));
        $('#info_mesin_body').html('<div class="text-center py-24"><iconify-icon icon="line-md:loading-twotone-loop" class="text-3xl text-primary-600 mb-2"></iconify-icon><div class="text-secondary-light text-sm">Menghubungi mesin via socket TCP LAN...</div></div>');
        modal.show();

        $.ajax({
            url: "<?php echo url('fingerprint_bridge/info_mesin/') ?>" + id,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $('#info_nama_mesin').text(res.nama_mesin);

                    var html = '<div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between radius-8">';
                    html += '<span class="fw-semibold text-dark"><i class="bi bi-clock me-1"></i> Tanggal jam mesin:</span>';
                    html += '<code class="fw-bold fs-6 text-primary-600">' + res.tanggal_jam_mesin + '</code>';
                    html += '</div>';

                    html += '<div class="card mb-3 border radius-8 shadow-sm">';
                    html += '<div class="card-header bg-neutral-100 py-2 fw-bold text-dark text-xs text-uppercase">Statistik Data Mesin (Fingerspot Personnel)</div>';
                    html += '<div class="card-body p-0">';
                    html += '<table class="table table-striped table-sm align-middle mb-0 text-sm">';
                    html += '<tr><td class="ps-3 text-secondary">Total Administrator</td><td class="pe-3 text-end fw-bold">' + res.total_admin + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total User</td><td class="pe-3 text-end fw-bold text-success-600">' + res.total_user + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total FP (Fingerprint)</td><td class="pe-3 text-end fw-bold text-info-600">' + res.total_fp + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total RFID card</td><td class="pe-3 text-end fw-bold">' + res.total_rfid_card + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total Password</td><td class="pe-3 text-end fw-bold">' + res.total_password + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total Wajah</td><td class="pe-3 text-end fw-bold">' + res.total_wajah + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total Telapak Tangan</td><td class="pe-3 text-end fw-bold">' + res.total_telapak_tangan + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total Data Operasional Mesin</td><td class="pe-3 text-end fw-bold">' + res.total_data_operasional + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total Data Presensi</td><td class="pe-3 text-end fw-bold text-primary-600">' + res.total_data_presensi + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total History Data Operasional Mesin</td><td class="pe-3 text-end fw-bold">' + res.total_history_operasional + '</td></tr>';
                    html += '<tr><td class="ps-3 text-secondary">Total History Data Presensi</td><td class="pe-3 text-end fw-bold text-dark">' + res.total_history_presensi + '</td></tr>';
                    html += '</table>';
                    html += '</div></div>';

                    html += '<table class="table bordered-table align-middle mb-0 text-sm">';
                    html += '<tr><th width="170" class="bg-neutral-50 text-secondary-light text-sm">Nama Mesin</th><td class="fw-semibold text-primary-light">' + res.nama_mesin + '</td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Kode Aktivasi</th><td><span class="badge bg-warning-focus text-warning-main font-monospace px-10 py-4 radius-4">' + res.kode_aktivasi + '</span></td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">IP Address & Port</th><td><code class="text-primary-600 fw-medium">' + res.ip_address + ':' + res.port + '</code></td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Tipe Driver</th><td><span class="badge bg-primary-focus text-primary-600 px-10 py-4 radius-4">' + res.tipe_mesin + '</span></td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Platform / Model</th><td>' + res.platform + '</td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Serial Number (SN)</th><td><code>' + res.serial_number + '</code></td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Firmware Version</th><td>' + res.firmware + '</td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Lokasi Unit</th><td>' + res.lokasi + '</td></tr>';
                    html += '<tr><th class="bg-neutral-50 text-secondary-light text-sm">Status LAN</th><td><span class="badge bg-success-focus text-success-main px-10 py-4 radius-4">' + res.status_lan + '</span></td></tr>';
                    html += '</table>';
                    $('#info_mesin_body').html(html);
                } else {
                    $('#info_mesin_body').html('<div class="alert bg-danger-focus text-danger-main border border-danger-200 radius-8 p-16"><strong>Gagal Membaca Info Mesin!</strong><br>' + res.message + '</div>');
                }
            },
            error: function() {
                $('#info_mesin_body').html('<div class="alert bg-danger-focus text-danger-main border border-danger-200 radius-8 p-16">Error menghubungi controller local bridge.</div>');
            }
        });
    });

});
</script>

<style>
.spin-anim {
    animation: spin 1s infinite linear;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
