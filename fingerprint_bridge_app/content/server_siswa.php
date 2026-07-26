<?php
$dev_cfg = getActiveDeviceConfig();
$alert_msg = '';
$alert_success = true;

$action   = $_POST['action'] ?? '';
$do_fetch = (isset($_REQUEST['fetch']) && $_REQUEST['fetch'] == '1') || !empty($action);
$api_url  = $dev_cfg['active_api'];

// Tangani Aksi Upload Single / Upload All Siswa Server Ke Mesin
if ($action === 'upload_single_to_machine') {
    $pin  = intval($_POST['pin'] ?? 0);
    $nama = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);

    if ($pin > 0 && !empty($nama)) {
        $res = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama);
        $alert_msg = "Berhasil mengunggah siswa PIN {$pin} ({$nama}) ke mesin EasyLink.";
        $alert_success = $res['status'];
    }
} elseif ($action === 'upload_all_to_machine') {
    $items_raw = $_POST['all_students'] ?? '[]';
    $items     = json_decode($items_raw, true);
    $count     = 0;

    if (is_array($items)) {
        foreach ($items as $st) {
            $pin  = intval($st['pin'] ?? $st['nipd'] ?? 0);
            $nama = mb_substr(trim($st['nama'] ?? ''), 0, 15);

            if ($pin > 0 && !empty($nama)) {
                $r = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama);
                if ($r['status']) $count++;
            }
        }
    }

    $alert_msg = "Berhasil mengunggah {$count} data siswa dari server ke mesin EasyLink.";
    $alert_success = true;
}

// Ambil Data Siswa & PTK Langsung dari Web API Server HANYA jika tombol diklik (do_fetch == true)
$students = [];
if ($do_fetch) {
    if (strpos($api_url, 'token=') === false) {
        $api_url .= (strpos($api_url, '?') === false ? '?' : '&') . 'token=MKDC_FINGERPRINT_SECRET_KEY_2026';
    }

    // 1. Ambil Data Siswa Aktif (PIN = NIPD)
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if (!$err && $resp) {
        $json = json_decode($resp, true);
        $raw_list = $json['students'] ?? $json['data'] ?? $json['Data'] ?? (is_array($json) && isset($json[0]) ? $json : null);
        if (is_array($raw_list)) {
            foreach ($raw_list as $item) {
                $pin  = trim((string)($item['nipd'] ?? $item['pin'] ?? $item['id_user'] ?? ''));
                $nama = trim((string)($item['nama_siswa'] ?? $item['nama'] ?? $item['name'] ?? ''));
                if ($pin !== '' && !empty($nama)) {
                    $students[] = [
                        'pin'   => $pin,
                        'nipd'  => $pin,
                        'nama'  => $nama,
                        'peran' => 'Siswa'
                    ];
                }
            }
        }
    }

    // 2. Ambil Data PTK / Guru Aktif (PIN = NIY Guru/PTK)
    $ptk_api_url = str_replace('active_students', 'active_ptk', $api_url);
    if (strpos($ptk_api_url, 'active_ptk') !== false) {
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL            => $ptk_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $resp2 = curl_exec($ch2);
        curl_close($ch2);

        if ($resp2) {
            $json2 = json_decode($resp2, true);
            $raw_ptk = $json2['ptk'] ?? $json2['data'] ?? null;
            if (is_array($raw_ptk)) {
                foreach ($raw_ptk as $item) {
                    $pin  = trim((string)($item['niy'] ?? $item['pin'] ?? ''));
                    $nama = trim((string)($item['nama_ptk'] ?? $item['nama'] ?? ''));
                    if ($pin !== '' && !empty($nama)) {
                        $students[] = [
                            'pin'   => $pin,
                            'nipd'  => $pin,
                            'nama'  => $nama . ' (Guru)',
                            'peran' => 'Guru/PTK'
                        ];
                    }
                }
            }
        }
    }

    if (!empty($students)) {
        if (empty($alert_msg)) {
            $alert_msg = "Berhasil membaca " . count($students) . " data pengguna (Siswa & Guru/PTK dengan PIN NIY) dari Web API Server.";
            $alert_success = true;
        }
    } else {
        if (empty($alert_msg)) {
            $alert_msg = "Gagal mengambil data dari Web API Server atau data kosong (URL: {$api_url}).";
            $alert_success = false;
        }
    }
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:users-group-rounded-bold" style="color: var(--primary);"></span> Data Siswa Server (Web API Direct)
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Membaca daftar siswa teregistrasi secara langsung dari Server Web API (<code><?php echo htmlspecialchars($api_url); ?></code>). Data hanya ditarik saat tombol diklik.
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="index.php?p=server_siswa&fetch=1" class="btn btn-primary">
                <span class="iconify" data-icon="solar:download-bold"></span> Ambil Data Siswa Server
            </a>
            <?php if (!empty($students)): ?>
                <form method="POST" action="index.php?p=server_siswa&fetch=1" style="margin:0;" onsubmit="return confirm('Unggah seluruh <?php echo count($students); ?> siswa server ke mesin EasyLink?')">
                    <input type="hidden" name="action" value="upload_all_to_machine">
                    <input type="hidden" name="all_students" value="<?php echo htmlspecialchars(json_encode($students)); ?>">
                    <button type="submit" class="btn btn-success">
                        <span class="iconify" data-icon="solar:upload-bold"></span> Upload Seluruh Siswa ke Mesin
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong>Status:</strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch): ?>
        <!-- Search & Filter Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; border: 1px solid var(--border-color); flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="iconify" data-icon="solar:magnifer-bold"></span>
                <input type="text" id="inputSearchSiswa" class="form-control" placeholder="Cari NIPD / Nama Siswa..." onkeyup="filterSiswaTable()" style="width: 260px;">
            </div>
            <div style="font-weight: 600; font-size: 0.9rem;">
                Total Siswa Terbaca: <span class="badge badge-primary"><?php echo count($students); ?> Siswa</span>
                <span style="font-size: 0.8rem; color: var(--text-secondary); margin-left: 0.5rem;">(Server API: <?php echo strtoupper($dev_cfg['api_env']); ?>)</span>
            </div>
        </div>

        <!-- Tabel Data Siswa Server -->
        <div class="table-responsive">
            <table class="table" id="tableServerSiswa">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th width="160">NIPD / PIN</th>
                        <th>Nama Siswa (Lengkap Server)</th>
                        <th>Nama Format Mesin (Max 15 Char)</th>
                        <th width="160" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php $no = 1; foreach ($students as $s): ?>
                            <?php 
                                $pin     = htmlspecialchars($s['nipd'] ?? $s['pin'] ?? '-');
                                $nama    = htmlspecialchars($s['nama'] ?? '-');
                                $nama_15 = htmlspecialchars(mb_substr($s['nama'] ?? '-', 0, 15));
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge badge-primary" style="font-size: 0.9rem; padding: 0.35rem 0.65rem;"><?php echo $pin; ?></span></td>
                                <td><strong style="font-size: 0.95rem; color: #0f172a;"><?php echo $nama; ?></strong></td>
                                <td><span class="badge badge-success" style="font-size: 0.85rem; font-family: monospace;"><?php echo $nama_15; ?></span></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="index.php?p=server_siswa&fetch=1" style="margin:0;">
                                        <input type="hidden" name="action" value="upload_single_to_machine">
                                        <input type="hidden" name="pin" value="<?php echo $pin; ?>">
                                        <input type="hidden" name="nama" value="<?php echo $nama_15; ?>">
                                        <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                                            <span class="iconify" data-icon="solar:upload-bold"></span> Upload Ke Mesin
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
                                Tidak ada data siswa yang ditemukan pada Web API Server.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Initial Prompt Card Saat Pertama Kali Halaman Dibuka -->
        <div class="card text-center mb-3" style="padding: 3rem 1.5rem; background: #f8fafc; border: 1px dashed var(--border-color);">
            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;">
                <span class="iconify" data-icon="solar:users-group-rounded-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Data Siswa Server Belum Dimuat</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 560px; margin: 0 auto 1.5rem auto;">
                Halaman ini sengaja tidak langsung mengambil data dari Web API server saat pertama kali dimuat. Klik tombol di bawah ini untuk membaca data siswa teregistrasi dari Web API Server (<code><?php echo htmlspecialchars($api_url); ?></code>).
            </p>
            <div>
                <a href="index.php?p=server_siswa&fetch=1" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:download-bold"></span> Ambil Data Siswa Dari Server Sekarang
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function filterSiswaTable() {
    const query = document.getElementById('inputSearchSiswa').value.toLowerCase();
    const rows = document.querySelectorAll('#tableServerSiswa tbody tr');

    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(query) ? '' : 'none';
    });
}
</script>
