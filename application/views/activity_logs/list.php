<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<?php
date_default_timezone_set('Asia/Jakarta');

// Format Tanggal Indonesia GMT+7
function format_indo_gmt7($datetime_str) {
    if (empty($datetime_str)) return '-';
    $time = strtotime($datetime_str);
    
    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    
    $d = date('d', $time);
    $m = $bulan[(int)date('m', $time)];
    $Y = date('Y', $time);
    $H = date('H:i:s', $time);

    // Tanggal Hari Ini Check
    if (date('Y-m-d', $time) === date('Y-m-d')) {
        return "Hari ini, {$H} WIB";
    }
    return "{$d} {$m} {$Y}, {$H} WIB";
}

// Deteksi Kategori Aksi & Badge Color
function get_action_badge($text) {
    $t = strtolower($text);
    if (strpos($t, 'menambah') !== false || strpos($t, 'input') !== false || strpos($t, 'created') !== false || strpos($t, 'baru') !== false) {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #dcfce7; color: #15803d !important; border: 1px solid #bbf7d0;"><i class="fas fa-plus-circle mr-1"></i> Tambah Data</span>';
    } elseif (strpos($t, 'mengubah') !== false || strpos($t, 'update') !== false || strpos($t, 'edit') !== false || strpos($t, 'disunting') !== false || strpos($t, 'memperbarui') !== false) {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #fef9c3; color: #854d0e !important; border: 1px solid #fef08a;"><i class="fas fa-edit mr-1"></i> Ubah Data</span>';
    } elseif (strpos($t, 'menghapus') !== false || strpos($t, 'delete') !== false || strpos($t, 'hapus') !== false) {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #fee2e2; color: #b91c1c !important; border: 1px solid #fecaca;"><i class="fas fa-trash-alt mr-1"></i> Hapus Data</span>';
    } elseif (strpos($t, 'logged in') !== false || strpos($t, 'login') !== false) {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important; border: 1px solid #bae6fd;"><i class="fas fa-sign-in-alt mr-1"></i> Login</span>';
    } elseif (strpos($t, 'logged out') !== false || strpos($t, 'logout') !== false) {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #475569 !important; border: 1px solid #e2e8f0;"><i class="fas fa-sign-out-alt mr-1"></i> Logout</span>';
    } else {
        return '<span class="badge px-10 py-4 radius-4 text-xs font-semibold" style="background-color: #f3e8ff; color: #6b21a8 !important; border: 1px solid #e9d5ff;"><i class="fas fa-tasks mr-1"></i> Aktivitas</span>';
    }
}

// Hitung Log Hari Ini
$cnt_today = 0;
$today_str = date('Y-m-d');
foreach ($activity_logs as $l) {
    if (substr($l->created_at, 0, 10) === $today_str) {
        $cnt_today++;
    }
}
?>

<div class="dashboard-main-body">
    <!-- Stat Cards Ringkasan -->
    <div class="row gy-3 mb-24">
        <div class="col-md-4">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Total Log Dimuat</span>
                        <h4 class="mb-0 mt-4 text-primary-600 fw-bold"><?php echo number_format(count($activity_logs)); ?> Entri</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:history-bold-duotone"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Diurutkan dari log paling terbaru (ID Descending)
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Aktivitas Hari Ini (GMT+7)</span>
                        <h4 class="mb-0 mt-4 text-success-600 fw-bold"><?php echo number_format($cnt_today); ?> Aktivitas</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-success-50 text-success-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:calendar-mark-bold-duotone"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Waktu Server: <strong><?php echo date('d-m-Y H:i'); ?> WIB</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Zona Waktu Sistem</span>
                        <h4 class="mb-0 mt-4 text-info-600 fw-bold">WIB (GMT+7)</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-info-50 text-info-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:clock-circle-bold-duotone"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Asia/Jakarta Standard Time
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card Log Aktivitas -->
    <div class="card card-primary card-outline radius-12 shadow-sm border-0 mb-24">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-user-shield text-primary mr-2"></i> Log Aktivitas Pengguna (Pengaturan Khusus Admin)
            </h5>
            <span class="badge px-12 py-6 radius-8 font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important; font-size: 13px;">
                <i class="fas fa-lock mr-1"></i> Khusus Administrator
            </span>
        </div>

        <div class="card-body">
            <!-- Filter Bar -->
            <div class="p-3 bg-light radius-8 mb-4 border">
                <?php echo form_open('activity_logs', ['method' => 'GET', 'autocomplete' => 'off', 'class' => 'row g-3 align-items-end']); ?>
                    <div class="col-md-4">
                        <label for="Filter-IpAddress" class="form-label fw-bold text-xs text-secondary-light">Filter IP Address</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fas fa-network-wired text-muted"></i></span>
                            <input type="text" name="ip" id="Filter-IpAddress" class="form-control radius-8" value="<?php echo htmlspecialchars($filter_ip); ?>" placeholder="Contoh: 127.0.0.1" />
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label for="Filter-User" class="form-label fw-bold text-xs text-secondary-light">Filter Pengguna (Pelaksana)</label>
                        <select name="user" id="Filter-User" class="form-control form-control-sm select2 radius-8">
                            <option value="">-- Semua Pengguna --</option>
                            <?php foreach ($this->users_model->get() as $row): ?>
                                <?php $sel = ($filter_user == $row->id) ? 'selected' : ''; ?>
                                <option value="<?php echo $row->id; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($row->name); ?> (#<?php echo $row->id; ?> - <?php echo htmlspecialchars($row->username); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary-600 px-16 radius-8 font-semibold">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?php echo url('activity_logs'); ?>" class="btn btn-sm btn-outline-secondary px-16 radius-8">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                    </div>
                <?php echo form_close(); ?>
            </div>

            <!-- Tabel Activity Logs -->
            <div class="table-responsive">
                <table id="tbl_activity_logs" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="70" class="text-center">ID</th>
                            <th width="190" class="text-center">Waktu &amp; Tanggal (GMT+7)</th>
                            <th width="200">Pengguna (Pelaksana)</th>
                            <th width="130" class="text-center">Kategori</th>
                            <th>Keterangan Aktivitas / Aksi</th>
                            <th width="130" class="text-center">IP Address</th>
                            <th width="90" class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activity_logs)): ?>
                            <?php foreach ($activity_logs as $row): ?>
                                <tr>
                                    <!-- ID Log -->
                                    <td class="text-center fw-bold">
                                        <code>#<?php echo $row->id; ?></code>
                                    </td>

                                    <!-- Waktu GMT+7 (WIB) -->
                                    <td class="text-center small">
                                        <div class="fw-bold text-dark">
                                            <i class="far fa-clock text-primary mr-1"></i>
                                            <?php echo format_indo_gmt7($row->created_at); ?>
                                        </div>
                                        <div class="text-xs text-muted mt-1">
                                            <code><?php echo date('Y-m-d H:i:s', strtotime($row->created_at)); ?></code>
                                        </div>
                                    </td>

                                    <!-- User Pelaksana -->
                                    <td>
                                        <?php if (!empty($row->user_name)): ?>
                                            <strong class="text-dark d-block"><?php echo htmlspecialchars($row->user_name); ?></strong>
                                            <span class="badge px-6 py-2 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #475569 !important; border: 1px solid #e2e8f0;">
                                                <i class="fas fa-user-circle mr-1"></i> <?php echo htmlspecialchars($row->user_username); ?> (#<?php echo $row->user; ?>)
                                            </span>
                                        <?php elseif ($row->user > 0): ?>
                                            <span class="badge px-8 py-4 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #334155 !important;">
                                                User ID #<?php echo $row->user; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fas fa-robot mr-1"></i> Sistem / Tamu</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Kategori Aksi Badge -->
                                    <td class="text-center">
                                        <?php echo get_action_badge($row->title); ?>
                                    </td>

                                    <!-- Keterangan Aktivitas -->
                                    <td>
                                        <div class="text-dark font-semibold" style="line-height: 1.4;">
                                            <?php echo htmlspecialchars($row->title); ?>
                                        </div>
                                    </td>

                                    <!-- IP Address -->
                                    <td class="text-center">
                                        <?php if (!empty($row->ip_address)): ?>
                                            <a href="<?php echo url('activity_logs?ip=' . urlencode($row->ip_address)); ?>" class="badge px-8 py-4 radius-4 text-xs font-semibold" style="background-color: #f8fafc; color: #0284c7 !important; border: 1px solid #bae6fd;" title="Filter IP Address Ini">
                                                <i class="fas fa-network-wired mr-1"></i> <?php echo htmlspecialchars($row->ip_address); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tombol Aksi Detail -->
                                    <td class="text-center">
                                        <a href="<?php echo url('activity_logs/view/' . $row->id); ?>" class="btn btn-sm btn-outline-primary radius-8 px-10 py-4 font-bold" title="Lihat Detail Log">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-history fa-3x mb-3 text-secondary d-block"></i><br>
                                    Belum ada log aktivitas yang tercatat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#tbl_activity_logs')) {
        $('#tbl_activity_logs').DataTable().destroy();
    }
    $('#tbl_activity_logs').DataTable({
        "order": [[0, "desc"]], // Mengurutkan dari ID terbesar / terbaru ke terkecil
        "pageLength": 25,
        "language": {
            "search": "Cari Log Aktivitas:",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ log",
            "zeroRecords": "Tidak ada log aktivitas yang cocok",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>