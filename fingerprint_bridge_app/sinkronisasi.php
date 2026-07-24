<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';

if (!isset($_SESSION['fp_bridge_admin'])) {
    header('Location: login.php');
    exit;
}

$machine  = BridgeStorage::getMachine();
$settings = BridgeStorage::getSettings();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbandingan & Sinkronisasi Siswa - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style>
        .tab-btn {
            padding: 0.65rem 1.25rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            color: #475569;
            transition: all 0.2s ease;
        }
        .tab-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        .terminal-console {
            background: #111827;
            color: #10b981;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.825rem;
            height: 280px;
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
        
        <!-- Header Info Panel -->
        <div class="card mb-3">
            <div class="card-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                <h3 class="card-title">
                    <span class="iconify" data-icon="solar:users-group-two-rounded-bold" style="color: #2563eb;"></span> Perbandingan & Sinkronisasi Siswa (Mirip MKDC Client v1.1.0)
                </h3>
                <button id="btnFetchData" class="btn btn-primary" onclick="loadData()">
                    <span class="iconify" data-icon="solar:restart-bold"></span> Muat & Bandingkan Data
                </button>
            </div>
            <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.5rem;">
                Menghubungkan data <strong>NIPD / PIN</strong> antara Mesin EasyLink (<code><?php echo htmlspecialchars($machine['ip_address'] ?? '127.0.0.1'); ?>:<?php echo htmlspecialchars($machine['port'] ?? 4370); ?></code>) dan Server API Web. Nama dari server otomatis dipotong maksimal 15 karakter.
            </div>
        </div>

        <!-- 1. Initial Prompt Panel (Tampil sebelum tombol diklik) -->
        <div id="initialPanel" class="card text-center" style="padding: 3rem 1.5rem;">
            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Siap Melakukan Perbandingan Data Siswa</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 540px; margin: 0 auto 1.5rem auto;">
                Klik tombol di bawah ini untuk memulai proses penarikan data siswa dari Server Web API dan daftar pengguna dari Mesin EasyLink.
            </p>
            <div>
                <button class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 1rem;" onclick="loadData()">
                    <span class="iconify" data-icon="solar:play-bold"></span> Muat & Bandingkan Data Sekarang
                </button>
            </div>
        </div>

        <!-- 2. Loading Panel (Tampil saat fetching data) -->
        <div id="loadingPanel" class="card text-center" style="display: none; padding: 3rem 1.5rem;">
            <div style="font-size: 2.5rem; color: #2563eb; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:loading-bold" style="animation: spin 1s linear infinite;"></span>
            </div>
            <h4 id="loadingStatus" style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Menghubungkan ke server & menarik data...</h4>
            <p style="font-size: 0.85rem; color: #64748b;">Proses ini memakan waktu beberapa saat tergantung pada jumlah data siswa dan kecepatan respon mesin.</p>
        </div>

        <!-- 3. Error Panel -->
        <div id="errorPanel" class="card" style="display: none; border-left: 4px solid #ef4444; padding: 2rem;">
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
                <div style="font-size: 2rem; color: #ef4444;">
                    <span class="iconify" data-icon="solar:danger-bold"></span>
                </div>
                <div>
                    <h4 id="errorTitle" style="font-size: 1.1rem; font-weight: 700; color: #991b1b; margin-bottom: 0.25rem;">Terjadi Kesalahan</h4>
                    <p id="errorMessage" style="font-size: 0.9rem; color: #475569; margin-bottom: 1rem;"></p>
                    <button class="btn btn-primary" onclick="loadData()">
                        <span class="iconify" data-icon="solar:restart-bold"></span> Coba Lagi
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. Main Comparison Panel (Persis MKDC Client v1.1.0) -->
        <div id="comparisonPanel" class="card" style="display: none;">
            
            <!-- Tabs Header -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; flex-wrap: wrap;">
                <button class="tab-btn active" id="tab-name-diff" onclick="switchTab('pane-name-diff', 'tab-name-diff')">
                    Beda Nama <span class="badge badge-warning" id="badge-name-diff">0</span>
                </button>
                <button class="tab-btn" id="tab-only-server" onclick="switchTab('pane-only-server', 'tab-only-server')">
                    Hanya di Server <span class="badge badge-info" id="badge-only-server">0</span>
                </button>
                <button class="tab-btn" id="tab-only-machine" onclick="switchTab('pane-only-machine', 'tab-only-machine')">
                    Hanya di Mesin <span class="badge badge-danger" id="badge-only-machine">0</span>
                </button>
                <button class="tab-btn" id="tab-in-sync" onclick="switchTab('pane-in-sync', 'tab-in-sync')">
                    Sudah Sesuai <span class="badge badge-success" id="badge-in-sync">0</span>
                </button>
            </div>

            <!-- TAB 1: BEDA NAMA -->
            <div id="pane-name-diff" class="tab-pane">
                <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.85rem; color: #92400e;">
                    <span class="iconify" data-icon="solar:info-circle-bold"></span> <strong>Beda Nama:</strong> PIN / NIPD sama tetapi nama di mesin dan server berbeda. Centang untuk memperbarui nama di mesin dengan nama dari server (max 15 char).
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40" style="text-align: center;"><input type="checkbox" id="checkAllNameDiff"></th>
                                <th width="120">PIN (NIPD)</th>
                                <th>Nama di Mesin</th>
                                <th>Nama di Server (Max 15 Char)</th>
                                <th>Aksi Terpilih</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-name-diff">
                            <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:1.5rem;">Tidak ada perbedaan nama.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: HANYA DI SERVER -->
            <div id="pane-only-server" class="tab-pane" style="display: none;">
                <div style="background: #ecfeff; border: 1px solid #cff4fc; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.85rem; color: #155e75;">
                    <span class="iconify" data-icon="solar:info-circle-bold"></span> <strong>Hanya di Server:</strong> Siswa aktif terdaftar di server tetapi belum terdaftar di mesin. Centang untuk mendaftarkan mereka ke mesin absensi.
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40" style="text-align: center;"><input type="checkbox" id="checkAllOnlyServer"></th>
                                <th width="120">PIN (NIPD)</th>
                                <th>Nama di Server (Max 15 Char)</th>
                                <th>Aksi Terpilih</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-only-server">
                            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Semua siswa server sudah ada di mesin.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: HANYA DI MESIN -->
            <div id="pane-only-machine" class="tab-pane" style="display: none;">
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.85rem; color: #991b1b;">
                    <span class="iconify" data-icon="solar:info-circle-bold"></span> <strong>Hanya di Mesin:</strong> Pengguna terdaftar di mesin tetapi tidak ada dalam daftar siswa aktif di server. Centang jika Anda ingin menghapusnya dari mesin.
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40" style="text-align: center;"><input type="checkbox" id="checkAllOnlyMachine"></th>
                                <th width="120">PIN (NIPD)</th>
                                <th>Nama di Mesin</th>
                                <th>Aksi Terpilih</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-only-machine">
                            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Tidak ada user asing di mesin.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: SUDAH SESUAI -->
            <div id="pane-in-sync" class="tab-pane" style="display: none;">
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.85rem; color: #065f46;">
                    <span class="iconify" data-icon="solar:check-circle-bold"></span> <strong>Sudah Sesuai:</strong> Data siswa yang sudah cocok sempurna antara mesin absensi dan server online. Tidak memerlukan tindakan.
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="60" style="text-align: center;">No</th>
                                <th width="120">PIN (NIPD)</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-in-sync">
                            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Belum ada data yang cocok.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Action Button -->
            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-primary" id="btnStartSyncProcess" style="padding: 0.75rem 1.5rem;" onclick="startExecution()">
                    <span class="iconify" data-icon="solar:play-circle-bold"></span> Eksekusi Sinkronisasi Terpilih (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>

        <!-- 5. Execution Panel (Tampil saat eksekusi perubahan berlangsung) -->
        <div id="executionPanel" class="card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title" style="color: #d97706;">
                    <span class="iconify" data-icon="solar:restart-bold" style="animation: spin 1s linear infinite;"></span> Eksekusi Perubahan Ke Mesin EasyLink
                </h3>
            </div>

            <!-- Progress Bar -->
            <div style="max-width: 700px; margin: 0 auto 1.5rem auto; text-align: center;">
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem;">
                    <span id="syncStatusLabel">Menyiapkan antrean eksekusi...</span>
                    <span id="syncPercentText">0%</span>
                </div>
                <div style="background: #e2e8f0; border-radius: 9999px; height: 18px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                    <div id="syncProgressBar" style="background: linear-gradient(90deg, #f59e0b, #eab308); width: 0%; height: 100%; font-weight: 700; font-size: 0.75rem; color: #000; display: flex; align-items: center; justify-content: center; transition: width 0.3s ease;">0%</div>
                </div>
            </div>

            <!-- Terminal Log Console -->
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.4rem;">
                    <span><span class="iconify" data-icon="solar:code-bold"></span> Konsol Aktivitas Sinkronisasi</span>
                    <span class="badge badge-warning" id="syncBadgeState">Berjalan</span>
                </div>
                <div class="terminal-console" id="terminalLogConsole">
                    <div class="terminal-line text-info-log">[INFO] Mempersiapkan antrean aksi sinkronisasi...</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin-top: 1rem;">
                <button onclick="location.reload()" class="btn btn-success" id="btnFinishSync" style="display: none; padding: 0.75rem 2rem;">
                    <span class="iconify" data-icon="solar:restart-bold"></span> Muat Ulang Halaman
                </button>
            </div>
        </div>

    </div>

    <script>
        let listNameDiff   = [];
        let listOnlyServer = [];
        let listOnlyMachine= [];
        let listInSync     = [];

        let queue        = [];
        let queueIndex   = 0;
        let successCount = 0;
        let failCount    = 0;

        function switchTab(paneId, tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(paneId).style.display = 'block';
            document.getElementById(tabId).classList.add('active');
        }

        function showError(title, message) {
            document.getElementById('initialPanel').style.display = 'none';
            document.getElementById('loadingPanel').style.display = 'none';
            document.getElementById('comparisonPanel').style.display = 'none';
            document.getElementById('errorPanel').style.display = 'block';
            document.getElementById('errorTitle').innerText = title;
            document.getElementById('errorMessage').innerText = message;
        }

        function loadData() {
            document.getElementById('initialPanel').style.display = 'none';
            document.getElementById('errorPanel').style.display = 'none';
            document.getElementById('loadingPanel').style.display = 'block';
            document.getElementById('comparisonPanel').style.display = 'none';
            document.getElementById('loadingStatus').innerText = 'Mengambil data siswa (NIPD) dari Server Web & Mesin EasyLink...';

            fetch('ajax.php?action=fetch_sync_diff', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        processComparison(data);
                    } else {
                        showError('Gagal Memuat Data', data.message || 'Terjadi kesalahan saat menarik data dari server atau mesin.');
                    }
                })
                .catch(err => {
                    showError('Koneksi Gagal', 'Gagal berkomunikasi dengan server lokal/mesin. Pastikan koneksi jaringan dan IP mesin terkonfigurasi dengan benar.');
                });
        }

        function processComparison(data) {
            // Replicate MKDC Client v1.1.0 EXACT comparison algorithm
            listNameDiff   = data.name_mismatch || [];
            listOnlyServer = data.server_only || [];
            listOnlyMachine= data.machine_only || [];
            listInSync     = [];

            // Update tab badges
            document.getElementById('badge-name-diff').innerText = listNameDiff.length;
            document.getElementById('badge-only-server').innerText = listOnlyServer.length;
            document.getElementById('badge-only-machine').innerText = listOnlyMachine.length;
            document.getElementById('badge-in-sync').innerText = listInSync.length;

            renderTables();

            document.getElementById('loadingPanel').style.display = 'none';
            document.getElementById('comparisonPanel').style.display = 'block';
            updateSelectedCount();
        }

        function renderTables() {
            // 1. Beda Nama
            const t1 = document.getElementById('tbody-name-diff');
            if (listNameDiff.length > 0) {
                t1.innerHTML = listNameDiff.map(item => `
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" class="chk-action" data-action="ubah_nama_mesin" data-pin="${item.pin}" data-nama="${escapeHtml(item.nama_server)}">
                        </td>
                        <td><strong>${item.pin}</strong></td>
                        <td style="color:#ef4444; font-weight:bold;">${escapeHtml(item.nama_mesin)}</td>
                        <td style="color:#10b981; font-weight:bold;">${escapeHtml(item.nama_server)}</td>
                        <td><span class="badge badge-warning">Update Nama</span></td>
                    </tr>
                `).join('');
            } else {
                t1.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:1.5rem;">Tidak ada perbedaan nama.</td></tr>';
            }

            // 2. Hanya di Server
            const t2 = document.getElementById('tbody-only-server');
            if (listOnlyServer.length > 0) {
                t2.innerHTML = listOnlyServer.map(item => `
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" class="chk-action" data-action="tambah_mesin" data-pin="${item.pin}" data-nama="${escapeHtml(item.nama)}">
                        </td>
                        <td><strong>${item.pin}</strong></td>
                        <td style="color:#2563eb; font-weight:bold;">${escapeHtml(item.nama)}</td>
                        <td><span class="badge badge-info">Tambahkan</span></td>
                    </tr>
                `).join('');
            } else {
                t2.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Semua siswa server sudah ada di mesin.</td></tr>';
            }

            // 3. Hanya di Mesin
            const t3 = document.getElementById('tbody-only-machine');
            if (listOnlyMachine.length > 0) {
                t3.innerHTML = listOnlyMachine.map(item => `
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" class="chk-action" data-action="hapus_mesin" data-pin="${item.pin}" data-nama="${escapeHtml(item.nama)}">
                        </td>
                        <td><strong>${item.pin}</strong></td>
                        <td style="color:#ef4444;">${escapeHtml(item.nama)}</td>
                        <td><span class="badge badge-danger">Hapus</span></td>
                    </tr>
                `).join('');
            } else {
                t3.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Tidak ada user asing di mesin.</td></tr>';
            }

            // Bind checkbox events
            document.querySelectorAll('.chk-action').forEach(chk => {
                chk.addEventListener('change', updateSelectedCount);
            });
        }

        // Check All Handlers
        document.getElementById('checkAllNameDiff').addEventListener('change', function() {
            document.querySelectorAll('#tbody-name-diff .chk-action').forEach(c => c.checked = this.checked);
            updateSelectedCount();
        });
        document.getElementById('checkAllOnlyServer').addEventListener('change', function() {
            document.querySelectorAll('#tbody-only-server .chk-action').forEach(c => c.checked = this.checked);
            updateSelectedCount();
        });
        document.getElementById('checkAllOnlyMachine').addEventListener('change', function() {
            document.querySelectorAll('#tbody-only-machine .chk-action').forEach(c => c.checked = this.checked);
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = document.querySelectorAll('.chk-action:checked').length;
            document.getElementById('selected-count').innerText = count;
        }

        function startExecution() {
            const selected = [];
            document.querySelectorAll('.chk-action:checked').forEach(c => {
                selected.push({
                    action: c.getAttribute('data-action'),
                    pin: c.getAttribute('data-pin'),
                    nama: c.getAttribute('data-nama')
                });
            });

            if (selected.length === 0) {
                alert('Silakan pilih minimal satu aksi sinkronisasi.');
                return;
            }

            queue = selected;
            queueIndex = 0;
            successCount = 0;
            failCount = 0;

            document.getElementById('comparisonPanel').style.display = 'none';
            document.getElementById('executionPanel').style.display = 'block';

            logConsole('Memulai proses eksekusi sinkronisasi...', 'text-warning-log');
            logConsole(`Total aksi terpilih: ${queue.length} perubahan.`, 'text-info-log');

            processNextQueue();
        }

        function logConsole(msg, textClass = 'text-success-log') {
            const time = new Date().toLocaleTimeString();
            const div = document.createElement('div');
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

        function processNextQueue() {
            if (queueIndex >= queue.length) {
                finishExecution();
                return;
            }

            const current = queue[queueIndex];
            const percent = ((queueIndex + 1) / queue.length) * 100;
            let labelAction = "";

            if (current.action === 'tambah_mesin') labelAction = `Mendaftarkan PIN NIPD: ${current.pin} - ${current.nama}`;
            if (current.action === 'ubah_nama_mesin') labelAction = `Memperbarui nama PIN NIPD: ${current.pin} menjadi ${current.nama}`;
            if (current.action === 'hapus_mesin') labelAction = `Menghapus PIN: ${current.pin} (${current.nama}) dari mesin`;

            updateProgress(percent, `Memproses (${queueIndex + 1}/${queue.length}): ${labelAction}`);

            const formData = new FormData();
            formData.append('type', current.action);
            formData.append('pin', current.pin);
            formData.append('nama', current.nama);

            fetch('ajax.php?action=exec_sync_single', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        successCount++;
                        logConsole(`[OK] Berhasil: ${labelAction}`, 'text-success-log');
                    } else {
                        failCount++;
                        logConsole(`[FAIL] Gagal: ${labelAction}. ${data.message}`, 'text-danger-log');
                    }
                    queueIndex++;
                    setTimeout(processNextQueue, 300);
                })
                .catch(err => {
                    failCount++;
                    logConsole(`[ERROR] Gagal koneksi: ${labelAction}`, 'text-danger-log');
                    queueIndex++;
                    setTimeout(processNextQueue, 300);
                });
        }

        function finishExecution() {
            updateProgress(100, `Eksekusi selesai! Sukses: ${successCount}, Gagal: ${failCount}`);
            document.getElementById('syncBadgeState').className = 'badge badge-success';
            document.getElementById('syncBadgeState').innerText = 'Selesai';
            logConsole(`===================================================`, 'text-warning-log');
            logConsole(`Proses sinkronisasi selesai. Total Sukses: ${successCount}, Total Gagal: ${failCount}.`, 'text-success-log');
            document.getElementById('btnFinishSync').style.display = 'inline-block';
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/'/g, "\\'").replace(/"/g, "&quot;");
        }
    </script>
</body>
</html>
