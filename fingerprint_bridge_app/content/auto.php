<?php
$dev_cfg = getActiveDeviceConfig();
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:play-circle-bold" style="color: var(--primary);"></span> Auto Download & Real-Time Sync Timer
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Penarikan data scanlog & user dari mesin EasyLink secara otomatis berkala (Sesuai fitur <code>timer_jadwal</code> pada Client SDK D7).
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button id="btnStartAuto" class="btn btn-success" onclick="toggleAutoDownload()">
                <span class="iconify" data-icon="solar:play-bold"></span> Mulai Auto Sync
            </button>
            <button id="btnStopAuto" class="btn btn-danger" onclick="stopAutoDownload()" style="display: none;">
                <span class="iconify" data-icon="solar:stop-bold"></span> Hentikan Auto Sync
            </button>
        </div>
    </div>

    <!-- Interval & Countdown Settings -->
    <div class="grid grid-3 mb-3">
        <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div class="form-label">Interval Sync (Detik)</div>
            <input type="number" id="autoInterval" class="form-control" value="10" min="5" max="300">
        </div>
        <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div class="form-label">Countdown Panggilan Berikutnya</div>
            <div style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--primary);" id="countdownVal">00:10</div>
        </div>
        <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div class="form-label">Status Auto Download</div>
            <div id="autoStatusBadge">
                <span class="badge badge-secondary">Non-Aktif / Idle</span>
            </div>
        </div>
    </div>

    <!-- Live Terminal Console -->
    <h4 class="card-title mb-2" style="font-size: 0.95rem;">
        <span class="iconify" data-icon="solar:terminal-bold"></span> Live Terminal Log Console (Sync Events)
    </h4>
    <div class="terminal-console" id="autoTerminalLog">
        <div class="terminal-line"><span class="log-time">[<?php echo date('H:i:s'); ?>]</span> Live console siap. Klik <strong>"Mulai Auto Sync"</strong> untuk memicu penarikan berkala.</div>
    </div>
</div>

<script>
let autoTimer = null;
let countdownTimer = null;
let remainingSeconds = 10;
let isRunning = false;

function appendLog(msg, type = 'info') {
    const consoleBox = document.getElementById('autoTerminalLog');
    const now = new Date().toLocaleTimeString();
    let typeClass = 'log-time';
    if (type === 'success') typeClass = 'log-success';
    if (type === 'danger') typeClass = 'log-danger';
    if (type === 'warning') typeClass = 'log-warning';

    const line = document.createElement('div');
    line.className = 'terminal-line';
    line.innerHTML = `<span class="log-time">[${now}]</span> <span class="${typeClass}">${msg}</span>`;
    consoleBox.appendChild(line);
    consoleBox.scrollTop = consoleBox.scrollHeight;
}

function toggleAutoDownload() {
    if (isRunning) return;

    isRunning = true;
    document.getElementById('btnStartAuto').style.display = 'none';
    document.getElementById('btnStopAuto').style.display = 'inline-flex';
    document.getElementById('autoStatusBadge').innerHTML = '<span class="badge badge-success">Berjalan (Active)</span>';

    appendLog('Auto download timer diaktifkan.', 'success');
    runAutoFetch();
    startCountdown();
}

function stopAutoDownload() {
    isRunning = false;
    clearInterval(autoTimer);
    clearInterval(countdownTimer);

    document.getElementById('btnStartAuto').style.display = 'inline-flex';
    document.getElementById('btnStopAuto').style.display = 'none';
    document.getElementById('autoStatusBadge').innerHTML = '<span class="badge badge-secondary">Non-Aktif / Idle</span>';
    document.getElementById('countdownVal').innerText = '00:00';

    appendLog('Auto download timer dihentikan.', 'warning');
}

function startCountdown() {
    const intervalSec = parseInt(document.getElementById('autoInterval').value) || 10;
    remainingSeconds = intervalSec;

    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        if (!isRunning) return;
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            remainingSeconds = intervalSec;
            runAutoFetch();
        }
        const formatted = remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds;
        document.getElementById('countdownVal').innerText = `00:${formatted}`;
    }, 1000);
}

function runAutoFetch() {
    appendLog('Memicu penarikan log presensi baru (scanlog/new)...', 'info');

    const formData = new FormData();
    formData.append('action', 'download_scanlog');
    formData.append('mode', 'new');

    fetch('ajax.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                appendLog(data.message, 'success');
            } else {
                appendLog(data.message, 'danger');
            }
        })
        .catch(err => appendLog('Error koneksi saat memicu auto fetch.', 'danger'));
}
</script>
