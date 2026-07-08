<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light mb-0">Update Log</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($logs)): ?>
                        <div class="text-center py-48 text-secondary-light">
                            <iconify-icon icon="solar:history-linear" style="font-size: 64px;" class="mb-16"></iconify-icon>
                            <h6>Belum ada log pembaruan</h6>
                            <p class="text-sm">Tidak dapat membaca riwayat perubahan dari repositori Git.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="dataTableUpdateLog" data-page-length="10">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 120px;">Langkah</th>
                                        <th style="width: 150px;">Tanggal</th>
                                        <th>Keterangan Pembaruan</th>
                                        <th style="width: 180px;">Pengembang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <?php
                                        // Detect type of commit to show dynamic badge
                                        $msg = strtolower($log['message']);
                                        $badge = 'bg-info-focus text-info-main';
                                        $type = 'Update';
                                        if (strpos($msg, 'fix') !== false || strpos($msg, 'perbaikan') !== false || strpos($msg, 'bug') !== false || strpos($msg, 'error') !== false) {
                                            $badge = 'bg-danger-focus text-danger-main';
                                            $type = 'Perbaikan';
                                        } elseif (strpos($msg, 'add') !== false || strpos($msg, 'tambah') !== false || strpos($msg, 'buat') !== false || strpos($msg, 'fitur') !== false || strpos($msg, 'new') !== false) {
                                            $badge = 'bg-success-focus text-success-main';
                                            $type = 'Fitur Baru';
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-neutral-100 text-neutral-600 border border-neutral-300 px-12 py-6 fw-mono font-bold">#<?php echo htmlspecialchars($log['step'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <span class="text-secondary-light"><iconify-icon icon="solar:calendar-linear" class="me-1"></iconify-icon> <?php echo htmlspecialchars($log['date'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge <?php echo $badge ?> px-8 py-4 radius-4 text-xs" style="white-space: nowrap;"><?php echo $type ?></span>
                                                    <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($log['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary-light"><iconify-icon icon="solar:user-linear" class="me-1"></iconify-icon> <?php echo htmlspecialchars($log['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    if ($('#dataTableUpdateLog').length) {
        new DataTable('#dataTableUpdateLog', {
            order: [[1, 'desc']]
        });
    }
</script>
