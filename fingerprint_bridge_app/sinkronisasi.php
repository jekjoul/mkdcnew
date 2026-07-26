<?php
$dev_cfg = getActiveDeviceConfig();
$alert_msg = '';
$alert_success = true;

// Tangani Form Action Sync Single / Batch via Standard PHP POST
$action   = $_POST['action'] ?? '';
$do_fetch = (isset($_REQUEST['fetch']) && $_REQUEST['fetch'] == '1') || !empty($action);

if ($action === 'sync_single') {
    $type = $_POST['type'] ?? '';
    $pin  = intval($_POST['pin'] ?? 0);
    $nama = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);

    if ($type === 'tambah_mesin' || $type === 'ubah_nama_mesin') {
        $res = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama);
        $alert_msg = "Berhasil memperbarui data PIN {$pin} ({$nama}) ke dalam mesin.";
        $alert_success = $res['status'];
    } elseif ($type === 'hapus_mesin') {
        $res = EasyLinkSDK::deleteUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin);
        $alert_msg = "Berhasil menghapus user PIN {$pin} di mesin.";
        $alert_success = $res['status'];
    }
} elseif ($action === 'sync_batch') {
    $items_raw = $_POST['batch_items'] ?? '[]';
    $items     = json_decode($items_raw, true);
    $count     = 0;

    if (is_array($items)) {
        foreach ($items as $it) {
            $pin  = intval($it['pin'] ?? 0);
            $nama = mb_substr(trim($it['nama'] ?? ''), 0, 15);
            $type = $it['type'] ?? '';

            if ($pin > 0) {
                if ($type === 'tambah_mesin' || $type === 'ubah_nama_mesin') {
                    $r = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama);
                    if ($r['status']) $count++;
                } elseif ($type === 'hapus_mesin') {
                    $r = EasyLinkSDK::deleteUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin);
                    if ($r['status']) $count++;
                }
            }
        }
    }

    $alert_msg = "Berhasil mengeksekusi sinkronisasi batch untuk {$count} data siswa ke dalam mesin.";
    $alert_success = true;
}

$machine_only  = [];
$server_only   = [];
$name_mismatch = [];
$api_url       = $dev_cfg['active_api'];

// Tarik data HANYA jika tombol diklik (do_fetch == true)
if ($do_fetch) {
    // LANGKAH 1: Get All User dulu dari Mesin EasyLink via PHP SDK (Wajib Paging Limit 1)
    $m_res = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
    $machine_users = $m_res['users'] ?? [];

    // LANGKAH 2: Ambil Data Siswa dari Server Web API Aktif (Dev/Prod)
    if (strpos($api_url, 'token=') === false) {
        $api_url .= (strpos($api_url, '?') === false ? '?' : '&') . 'token=MKDC_FINGERPRINT_SECRET_KEY_2026';
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    $server_students = [];
    if ($resp) {
        $json = json_decode($resp, true);
        $raw_list = null;

        if (is_array($json)) {
            if (isset($json['students']) && is_array($json['students'])) {
                $raw_list = $json['students'];
            } elseif (isset($json['data']) && is_array($json['data'])) {
                $raw_list = $json['data'];
            } elseif (isset($json['Data']) && is_array($json['Data'])) {
                $raw_list = $json['Data'];
            } elseif (isset($json[0]) && is_array($json[0])) {
                $raw_list = $json;
            }
        }

        if (is_array($raw_list)) {
            $server_students = $raw_list;
        }
    }

    // LANGKAH 3: Komparasi PIN di Mesin == NIPD di Server
    $machine_map = [];
    foreach ($machine_users as $m) {
        $m_pin = intval($m['pin'] ?? 0);
        if ($m_pin > 0) {
            $machine_map[$m_pin] = trim($m['nama'] ?? '');
        }
    }

    $server_map = [];
    foreach ($server_students as $s) {
        $nipd_pin = intval($s['nipd'] ?? $s['pin'] ?? $s['id_user'] ?? 0);
        $s_nama   = trim($s['nama_siswa'] ?? $s['nama'] ?? $s['name'] ?? '');
        if ($nipd_pin > 0 && !empty($s_nama)) {
            // Ambil maksimal 15 karakter TANPA menambah spasi jika kurang dari 15 karakter
            $nama_15 = trim(mb_substr($s_nama, 0, 15));
            $server_map[$nipd_pin] = $nama_15;
        }
    }

    $matched_data  = [];
    $machine_only  = [];
    $name_mismatch = [];

    foreach ($machine_map as $pin => $m_nama) {
        $m_nama_clean = trim($m_nama);
        if (!isset($server_map[$pin])) {
            $machine_only[] = ['pin' => $pin, 'nama' => $m_nama_clean, 'opt' => 'hapus_mesin'];
        } else {
            $s_nama_15 = $server_map[$pin];
            if (strcasecmp($m_nama_clean, $s_nama_15) === 0) {
                $matched_data[] = ['pin' => $pin, 'nama' => $m_nama_clean, 'nama_server' => $s_nama_15];
            } else {
                $name_mismatch[] = ['pin' => $pin, 'nama_mesin' => $m_nama_clean, 'nama_server' => $s_nama_15, 'opt' => 'ubah_nama_mesin'];
            }
        }
    }

    $server_only = [];
    foreach ($server_map as $pin => $s_nama_15) {
        if (!isset($machine_map[$pin])) {
            $server_only[] = ['pin' => $pin, 'nama' => $s_nama_15, 'opt' => 'tambah_mesin'];
        }
    }
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:sort-from-top-to-bottom-bold" style="color: var(--primary);"></span> Perbandingan & Sinkronisasi Siswa
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Menghubungkan data <strong>NIPD / PIN</strong> antara Mesin EasyLink (<code><?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?></code>) dan Web API Server (<code><?php echo htmlspecialchars($api_url); ?></code>).
            </div>
        </div>
        <a href="index.php?p=sinkronisasi&fetch=1" class="btn btn-primary">
            <span class="iconify" data-icon="solar:restart-bold"></span> Ambil & Bandingkan Data (Mesin & Server)
        </a>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong>Status:</strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch): ?>
        <!-- Summary Stats Bar 4 Kolom -->
        <div class="grid grid-4 mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="stat-card">
                <div class="stat-icon success" style="background: #dcfce7; color: #166534;">
                    <span class="iconify" data-icon="solar:check-circle-bold"></span>
                </div>
                <div>
                    <div class="stat-val" style="color: #166534;"><?php echo count($matched_data); ?></div>
                    <div class="stat-lbl">Sudah Sesuai (Mesin = Server)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <span class="iconify" data-icon="solar:pen-new-square-bold"></span>
                </div>
                <div>
                    <div class="stat-val"><?php echo count($name_mismatch); ?></div>
                    <div class="stat-lbl">Beda Nama (PIN Sama)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon primary">
                    <span class="iconify" data-icon="solar:user-plus-bold"></span>
                </div>
                <div>
                    <div class="stat-val"><?php echo count($server_only); ?></div>
                    <div class="stat-lbl">Hanya Ada di Server</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <span class="iconify" data-icon="solar:user-block-bold"></span>
                </div>
                <div>
                    <div class="stat-val"><?php echo count($machine_only); ?></div>
                    <div class="stat-lbl">Hanya Ada di Mesin</div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Initial Prompt Card Saat Pertama Kali Halaman Dibuka -->
        <div class="card text-center mb-3" style="padding: 3rem 1.5rem; background: #f8fafc; border: 1px dashed var(--border-color);">
            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:sort-from-top-to-bottom-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Siap Melakukan Sinkronisasi Data Siswa</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 560px; margin: 0 auto 1.5rem auto;">
                Halaman ini sengaja tidak langsung menarik data dari mesin & server Web API saat pertama kali dimuat. Klik tombol di bawah ini untuk menarik data dari Mesin EasyLink dan Web API Server (<code><?php echo htmlspecialchars($api_url); ?></code>).
            </p>
            <div>
                <a href="index.php?p=sinkronisasi&fetch=1" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:play-bold"></span> Ambil & Bandingkan Data Siswa Sekarang
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($do_fetch): ?>
    <!-- Tabs & Detail Tables -->
    <div class="card">
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; flex-wrap: wrap;">
            <button class="tab-btn active" id="tab0-btn" onclick="switchTab('pane0', 'tab0-btn')">
                Sudah Sesuai <span class="badge badge-success"><?php echo count($matched_data); ?></span>
            </button>
            <button class="tab-btn" id="tab1-btn" onclick="switchTab('pane1', 'tab1-btn')">
                Beda Nama <span class="badge badge-warning"><?php echo count($name_mismatch); ?></span>
            </button>
            <button class="tab-btn" id="tab2-btn" onclick="switchTab('pane2', 'tab2-btn')">
                Hanya di Server <span class="badge badge-info"><?php echo count($server_only); ?></span>
            </button>
            <button class="tab-btn" id="tab3-btn" onclick="switchTab('pane3', 'tab3-btn')">
                Hanya di Mesin <span class="badge badge-danger"><?php echo count($machine_only); ?></span>
            </button>
        </div>

        <!-- TAB 0: SUDAH SESUAI -->
        <div id="pane0" class="tab-pane">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th width="140">PIN (NIPD)</th>
                            <th>Nama di Mesin</th>
                            <th>Nama di Server (Max 15 Char)</th>
                            <th width="140" style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($matched_data)): ?>
                            <?php $no = 1; foreach ($matched_data as $item): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($item['pin']); ?></strong></td>
                                    <td><span class="badge badge-success" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;"><?php echo htmlspecialchars($item['nama']); ?></span></td>
                                    <td><span class="badge badge-success" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;"><?php echo htmlspecialchars($item['nama_server']); ?></span></td>
                                    <td style="text-align: center;">
                                        <span class="badge badge-success" style="padding:0.4rem 0.75rem; background:#22c55e; color:#ffffff;">
                                            <span class="iconify" data-icon="solar:check-circle-bold"></span> Tersinkronisasi
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:2rem;">Belum ada data siswa yang tersinkronisasi sempurna antara mesin dan server.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 1: BEDA NAMA -->
        <div id="pane1" class="tab-pane" style="display:none;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="120">PIN (NIPD)</th>
                            <th>Nama di Mesin</th>
                            <th>Nama di Server (Max 15 Char)</th>
                            <th width="140" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($name_mismatch)): ?>
                            <?php foreach ($name_mismatch as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['pin']); ?></strong></td>
                                    <td><span class="badge badge-warning"><?php echo htmlspecialchars($item['nama_mesin']); ?></span></td>
                                    <td><span class="badge badge-success"><?php echo htmlspecialchars($item['nama_server']); ?></span></td>
                                    <td style="text-align: center;">
                                        <form method="POST" action="index.php?p=sinkronisasi&fetch=1" style="margin:0;">
                                            <input type="hidden" name="action" value="sync_single">
                                            <input type="hidden" name="type" value="ubah_nama_mesin">
                                            <input type="hidden" name="pin" value="<?php echo $item['pin']; ?>">
                                            <input type="hidden" name="nama" value="<?php echo htmlspecialchars($item['nama_server']); ?>">
                                            <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                                                <span class="iconify" data-icon="solar:upload-bold"></span> Update Mesin
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:2rem;">Tidak ada perbedaan nama antara server dan mesin.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: HANYA DI SERVER -->
        <div id="pane2" class="tab-pane" style="display:none;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="120">PIN (NIPD)</th>
                            <th>Nama Siswa di Server</th>
                            <th width="140" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($server_only)): ?>
                            <?php foreach ($server_only as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['pin']); ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($item['nama']); ?></strong></td>
                                    <td style="text-align: center;">
                                        <form method="POST" action="index.php?p=sinkronisasi&fetch=1" style="margin:0;">
                                            <input type="hidden" name="action" value="sync_single">
                                            <input type="hidden" name="type" value="tambah_mesin">
                                            <input type="hidden" name="pin" value="<?php echo $item['pin']; ?>">
                                            <input type="hidden" name="nama" value="<?php echo htmlspecialchars($item['nama']); ?>">
                                            <button type="submit" class="btn btn-success" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                                                <span class="iconify" data-icon="solar:user-plus-bold"></span> Tambah Mesin
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:2rem;">Seluruh siswa server sudah terdaftar di mesin.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: HANYA DI MESIN -->
        <div id="pane3" class="tab-pane" style="display:none;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="120">PIN</th>
                            <th>Nama Pengguna di Mesin</th>
                            <th width="140" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($machine_only)): ?>
                            <?php foreach ($machine_only as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['pin']); ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($item['nama']); ?></strong></td>
                                    <td style="text-align: center;">
                                        <form method="POST" action="index.php?p=sinkronisasi&fetch=1" style="margin:0;">
                                            <input type="hidden" name="action" value="sync_single">
                                            <input type="hidden" name="type" value="hapus_mesin">
                                            <input type="hidden" name="pin" value="<?php echo $item['pin']; ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;" onclick="return confirm('Hapus PIN <?php echo $item['pin']; ?> dari mesin?')">
                                                <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Hapus Mesin
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:2rem;">Tidak ada pengguna tambahan di mesin.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function switchTab(paneId, btnId) {
    document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    document.getElementById(paneId).style.display = 'block';
    document.getElementById(btnId).classList.add('active');
}

function showLoadingSync(msg) {
    var overlay = document.getElementById('syncLoadingOverlay');
    if (overlay) {
        if (msg) {
            var p = overlay.querySelector('p');
            if (p) p.innerText = msg;
        }
        overlay.style.display = 'flex';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var fetchLinks = document.querySelectorAll('a[href*="fetch=1"]');
    fetchLinks.forEach(function(a) {
        a.addEventListener('click', function() {
            showLoadingSync('Sedang berkomunikasi dengan mesin fingerprint & server Web API...');
        });
    });

    var forms = document.querySelectorAll('form');
    forms.forEach(function(f) {
        f.addEventListener('submit', function() {
            showLoadingSync('Memproses sinkronisasi & memperbarui data mesin...');
        });
    });
});
</script>

<!-- Loading Overlay Modal Saat Penarikan Data Berlangsung -->
<div id="syncLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 999999; justify-content: center; align-items: center; text-align: center;">
    <div style="background: #ffffff; padding: 2.5rem 2rem; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); max-width: 480px; width: 90%;">
        <div style="display: inline-block; width: 3.5rem; height: 3.5rem; border: 0.35em solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 1.25rem;"></div>
        <style>
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
        <h4 style="font-weight: 700; color: #0f172a; font-size: 1.2rem; margin-bottom: 0.5rem;">Menarik Data dari Mesin & Web API Server...</h4>
        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.25rem; line-height: 1.5;">
            Mohon tunggu sejenak. Sistem sedang me-request data pengguna ke Mesin EasyLink (<code><?php echo htmlspecialchars($dev_cfg['server_IP']); ?></code>) & server Web API...
        </p>
        <div style="display: inline-block; padding: 0.4rem 1rem; background: #eff6ff; color: #1d4ed8; font-size: 0.8rem; font-weight: 600; border-radius: 20px;">
            ⏳ Mengumpulkan data SDK hingga selesai...
        </div>
    </div>
</div>
