<?php
$dev_cfg = getActiveDeviceConfig();
$alert_msg = '';
$alert_success = true;

// Tangani Aksi POST/GET dari Form PHP Biasa
$action = $_REQUEST['action'] ?? '';

// Cek apakah user telah menekan tombol untuk mengambil data dari mesin
$do_fetch = (isset($_REQUEST['fetch']) && $_REQUEST['fetch'] == '1') || !empty($action);

if ($action === 'set_user') {
    $pin  = intval($_POST['pin'] ?? 0);
    $nama = mb_substr(trim($_POST['nama'] ?? ''), 0, 15);
    $pwd  = trim($_POST['pwd'] ?? '');
    $rfid = trim($_POST['rfid'] ?? '');
    $priv = intval($_POST['privilege'] ?? 0);

    if ($pin > 0 && !empty($nama)) {
        $res = EasyLinkSDK::setUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin, $nama, $pwd, $rfid, $priv);
        $alert_msg = $res['message'];
        $alert_success = $res['status'];
    } else {
        $alert_msg = 'PIN dan Nama pengguna wajib diisi.';
        $alert_success = false;
    }
} elseif ($action === 'delete_user') {
    $pin = intval($_REQUEST['pin'] ?? 0);
    if ($pin > 0) {
        $res = EasyLinkSDK::deleteUser($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], $pin);
        $alert_msg = $res['message'];
        $alert_success = $res['status'];
    }
} elseif ($action === 'delete_admin') {
    $res = EasyLinkSDK::deleteAdmin($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    $alert_msg = $res['message'];
    $alert_success = $res['status'];
} elseif ($action === 'delete_all_users') {
    $res = EasyLinkSDK::deleteAllUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn']);
    $alert_msg = $res['message'];
    $alert_success = $res['status'];
}

// Ambil Data User Hanya Jika Tombol Diklik (do_fetch == true) - WAJIB Paging Limit 1
$users = [];
if ($do_fetch) {
    $res_users = EasyLinkSDK::getUsers($dev_cfg['server_IP'], $dev_cfg['server_port'], $dev_cfg['device_sn'], '0', 1);
    $users = $res_users['users'] ?? [];

    if (!empty($res_users['message']) && empty($alert_msg)) {
        $alert_msg = $res_users['message'];
        $alert_success = $res_users['status'];
    }
}
?>

<div class="card mb-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">
                <span class="iconify" data-icon="solar:users-group-two-rounded-bold" style="color: var(--primary);"></span> Daftar Pengguna Mesin (PIN & Nama)
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Menampilkan seluruh pengguna yang terdaftar di Mesin EasyLink (Termasuk pengguna tanpa sidik jari).
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <a href="index.php?p=user&fetch=1" class="btn btn-primary">
                <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span> Ambil Data User Mesin (Paging Limit 1)
            </a>

            <button class="btn btn-success" style="background: #16a34a; border-color: #16a34a; color: #fff;" onclick="sendUserToServer()">
                <span class="iconify" data-icon="solar:upload-bold"></span> Kirim Data User & Sidik Jari ke Server MKDC
            </button>

            <button class="btn btn-secondary" onclick="document.getElementById('addUserModal').style.display='flex'">
                <span class="iconify" data-icon="solar:user-plus-bold"></span> Tambah User Baru
            </button>
        </div>
    </div>

    <!-- Alert Box Notification -->
    <?php if (!empty($alert_msg)): ?>
        <div style="padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; background: <?php echo $alert_success ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $alert_success ? '#166534' : '#991b1b'; ?>; border: 1px solid <?php echo $alert_success ? '#bbf7d0' : '#fecaca'; ?>;">
            <strong><?php echo $alert_success ? 'Status:' : 'Perhatian:'; ?></strong> <?php echo htmlspecialchars($alert_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch && isset($res_users['is_match']) && $res_users['is_match'] === false && !empty($res_users['expected_count'])): ?>
        <!-- Warning Alert Selisih Data User -->
        <div style="padding: 1rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; background: #fffbeb; color: #92400e; border: 1px solid #fde68a;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <span class="iconify" data-icon="solar:danger-triangle-bold" style="font-size: 1.5rem; color: #d97706; flex-shrink: 0; margin-top: 0.1rem;"></span>
                <div>
                    <strong style="font-size: 0.95rem; display: block; margin-bottom: 0.25rem;">
                        ⚠️ Peringatan Selisih Data: Jumlah Ditarik (<?php echo $res_users['total_read']; ?> User) ≠ Info Hardware Mesin (<?php echo $res_users['expected_count']; ?> User)
                    </strong>
                    <div style="font-size: 0.85rem; line-height: 1.5; color: #78350f;">
                        Terdeteksi selisih sebanyak <strong><?php echo abs($res_users['expected_count'] - $res_users['total_read']); ?> User</strong> antara data pengguna yang berhasil dibaca via WebService SDK dengan counter memori internal hardware mesin.
                        <br><br>
                        <strong>Mengapa Selisih Ini Bisa Terjadi?</strong>
                        <ul style="margin: 0.35rem 0 0 1.25rem; padding: 0;">
                            <li><strong>Slot Memori Internal / User Terhapus (Orphan Count):</strong> Counter <code>UserCount</code> pada info mesin mencatat total slot alokasi chip memori hardware, termasuk ID user yang telah dihapus di mesin namun indeks memorinya belum ter-clear penuh.</li>
                            <li><strong>Penyaringan PIN 0 / PIN Kosong:</strong> Aplikasi Bridge secara otomatis menyaring (filter) PIN '0' atau PIN kosong untuk mencegah korupsi data user, sedangkan hardware mesin menghitung entri PIN 0 sebagai user terdaftar.</li>
                            <li><strong>Limit Halaman Paging WebService SDK:</strong> Mesin EasyLink mengirimkan data per halaman (paging limit 1). Jika koneksi terputus di tengah halaman, pengguna di halaman akhir tidak terangkut.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($do_fetch): ?>
        <!-- Action Bar & Maintenance Form -->
        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; border: 1px solid var(--border-color); flex-wrap: wrap; gap: 0.75rem;">
            <div style="font-weight: 600; font-size: 0.9rem;">
                Total User Terbaca: <span class="badge badge-primary"><?php echo count($users); ?> User</span>
                <span style="font-size: 0.8rem; color: var(--text-secondary); margin-left: 0.5rem;">(Mode: Paging Limit 1)</span>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="index.php?p=user&fetch=1&action=delete_admin" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;" onclick="return confirm('Hapus seluruh hak akses Administrator di mesin?')">
                    <span class="iconify" data-icon="solar:user-block-bold"></span> Hapus Admin Mesin
                </a>
                <a href="index.php?p=user&fetch=1&action=delete_all_users" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;" onclick="return confirm('PERHATIAN: Hapus SELURUH USER di mesin EasyLink?')">
                    <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Hapus Seluruh User Mesin
                </a>
            </div>
        </div>

        <!-- User Table (Hanya PIN dan Nama) -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th width="160">PIN (NIPD/ID)</th>
                        <th>Nama Pengguna</th>
                        <th width="120" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php $no = 1; foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge badge-primary" style="font-size: 0.9rem; padding: 0.35rem 0.65rem;"><?php echo htmlspecialchars($u['pin']); ?></span></td>
                                <td><strong style="font-size: 0.95rem; color: #0f172a;"><?php echo htmlspecialchars($u['nama']); ?></strong></td>
                                <td style="text-align: center;">
                                    <a href="index.php?p=user&fetch=1&action=delete_user&pin=<?php echo urlencode($u['pin']); ?>" class="btn btn-danger" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;" onclick="return confirm('Hapus user PIN <?php echo $u['pin']; ?>?')">
                                        <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
                                Saat ini belum ada data pengguna terdaftar di dalam mesin (0 User).
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
                <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Data Pengguna Mesin Belum Dimuat</h3>
            <p style="font-size: 0.9rem; color: #64748b; max-width: 560px; margin: 0 auto 1.5rem auto;">
                Halaman ini sengaja tidak langsung menarik data dari mesin saat pertama kali dibuka untuk menghemat *bandwidth* & mempercepat *load*. Klik tombol di bawah ini untuk mulai membaca data pengguna dari Mesin EasyLink (<?php echo htmlspecialchars($dev_cfg['server_IP']); ?>:<?php echo htmlspecialchars($dev_cfg['server_port']); ?>).
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <a href="index.php?p=user&fetch=1&mode=all" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:users-group-two-rounded-bold"></span> Ambil Data User Dari Mesin (Get All User)
                </a>
                <a href="index.php?p=user&fetch=1&mode=paging&limit=100" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    <span class="iconify" data-icon="solar:download-bold"></span> Download User Paging (100)
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah User (Form HTML POST Biasa) -->
<div id="addUserModal" class="modal-backdrop" style="display: none;">
    <div class="modal-box">
        <div class="card-header" style="padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1rem;">
            <h3 class="card-title">
                <span class="iconify" data-icon="solar:user-plus-bold" style="color: var(--primary);"></span> Tambah User Ke Mesin EasyLink
            </h3>
            <button onclick="document.getElementById('addUserModal').style.display='none'" style="background: transparent; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="index.php?p=user&fetch=1">
            <input type="hidden" name="action" value="set_user">
            <div class="form-group">
                <label class="form-label">PIN Pengguna (NIPD/ID Siswa)</label>
                <input type="number" name="pin" class="form-control" placeholder="Contoh: 1001" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Pengguna (Maksimal 15 Karakter)</label>
                <input type="text" name="nama" class="form-control" maxlength="15" placeholder="Contoh: Ahmad Subagyo" required>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addUserModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span class="iconify" data-icon="solar:upload-bold"></span> Kirim Ke Mesin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function sendUserToServer() {
    if (!confirm('Kirim seluruh data user (PIN, Nama, Password) dan Template Sidik Jari dari Mesin EasyLink ke Server MKDC?')) {
        return;
    }

    var btn = event.currentTarget;
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="iconify" data-icon="solar:restart-bold" style="animation: spin 1s linear infinite;"></span> Mengirim data ke MKDC...';

    fetch('ajax.php?action=send_machine_users_to_server', {
        method: 'POST'
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert(data.message);
        if (data.status) {
            location.reload();
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('Terjadi kesalahan jaringan atau server MKDC tidak merespon: ' + err);
    });
}
</script>
