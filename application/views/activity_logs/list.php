<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card card-primary card-outline radius-12 shadow-sm border-0 mb-24">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-history text-primary mr-2"></i> Log Aktivitas Pengguna &amp; Riwayat Perubahan
            </h5>
            <span class="badge px-12 py-6 radius-8 font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important; font-size: 13px;">
                Total Log: <?php echo count($activity_logs); ?> Entri
            </span>
        </div>

        <div class="card-body">
            <!-- Filter Bar -->
            <div class="p-3 bg-light radius-8 mb-4 border">
                <?php echo form_open('activity_logs', ['method' => 'GET', 'autocomplete' => 'off', 'class' => 'row g-3 align-items-end']); ?>
                    <div class="col-md-4">
                        <label for="Filter-IpAddress" class="form-label fw-bold text-xs text-secondary-light">Filter IP Address</label>
                        <input type="text" name="ip" id="Filter-IpAddress" class="form-control form-control-sm radius-8" value="<?php echo htmlspecialchars($filter_ip); ?>" placeholder="Contoh: 127.0.0.1" />
                    </div>

                    <div class="col-md-5">
                        <label for="Filter-User" class="form-label fw-bold text-xs text-secondary-light">Filter Pengguna (User)</label>
                        <select name="user" id="Filter-User" class="form-control form-control-sm select2 radius-8">
                            <option value="">-- Semua Pengguna --</option>
                            <?php foreach ($this->users_model->get() as $row): ?>
                                <?php $sel = ($filter_user == $row->id) ? 'selected' : ''; ?>
                                <option value="<?php echo $row->id; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($row->name); ?> (#<?php echo $row->id; ?> - <?php echo htmlspecialchars($row->username); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-16 radius-8">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?php echo url('activity_logs'); ?>" class="btn btn-sm btn-outline-secondary px-16 radius-8">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                    </div>
                <?php echo form_close(); ?>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="tbl_activity_logs" class="table table-bordered table-striped table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="60" class="text-center">ID</th>
                            <th width="140" class="text-center">IP Address</th>
                            <th>Keterangan Aktivitas</th>
                            <th width="180" class="text-center">Waktu &amp; Tanggal</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activity_logs)): ?>
                            <?php foreach ($activity_logs as $row): ?>
                                <tr>
                                    <td class="text-center"><code>#<?php echo $row->id; ?></code></td>
                                    <td class="text-center">
                                        <?php if (!empty($row->ip_address)): ?>
                                            <a href="<?php echo url('activity_logs?ip=' . urlencode($row->ip_address)); ?>" class="badge px-8 py-4 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #334155 !important; border: 1px solid #e2e8f0;">
                                                <i class="fas fa-network-wired mr-1"></i> <?php echo htmlspecialchars($row->ip_address); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block"><?php echo htmlspecialchars($row->title); ?></strong>
                                    </td>
                                    <td class="text-center small text-muted">
                                        <i class="far fa-clock mr-1"></i>
                                        <?php echo date('d-m-Y H:i:s', strtotime($row->created_at)); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo url('activity_logs/view/' . $row->id); ?>" class="btn btn-sm btn-outline-primary radius-8 px-10 py-4" title="Lihat Detail Log">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($row->user > 0): ?>
                                            <a href="<?php echo url('users/view/' . $row->user); ?>" class="btn btn-sm btn-outline-secondary radius-8 px-10 py-4" title="Lihat User #" target="_blank">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
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
        "order": [[0, "desc"]],
        "pageLength": 25,
        "language": {
            "search": "Cari Log Aktivitas:",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
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