<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<?php
date_default_timezone_set('Asia/Jakarta');
$time = !empty($activity->created_at) ? strtotime($activity->created_at) : time();
?>

<div class="dashboard-main-body">
    <div class="card card-primary card-outline radius-12 shadow-sm border-0 mb-24">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-search text-primary mr-2"></i> Rincian Log Aktivitas #<?php echo $activity->id; ?>
            </h5>
            <a href="<?php echo url('activity_logs'); ?>" class="btn btn-sm btn-outline-secondary radius-8 px-16">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Log
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <tbody>
                        <tr>
                            <td width="220" class="bg-light font-weight-bold">ID Log Aktivitas</td>
                            <td><code>#<?php echo $activity->id; ?></code></td>
                        </tr>
                        <tr>
                            <td class="bg-light font-weight-bold">Deskripsi / Pesan Aktivitas</td>
                            <td><strong class="text-dark font-16"><?php echo htmlspecialchars($activity->title); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="bg-light font-weight-bold">Pengguna (Pelaksana)</td>
                            <td>
                                <?php if (!empty($activity->user_name)): ?>
                                    <strong class="text-primary"><?php echo htmlspecialchars($activity->user_name); ?></strong>
                                    <span class="text-muted small"> (Username: <code><?php echo htmlspecialchars($activity->user_username); ?></code> | ID: #<?php echo $activity->user; ?>)</span>
                                    <?php if (!empty($activity->user_email)): ?>
                                        <div class="text-xs text-muted mt-1"><i class="far fa-envelope mr-1"></i> <?php echo htmlspecialchars($activity->user_email); ?></div>
                                    <?php endif; ?>
                                <?php elseif ($activity->user > 0): ?>
                                    <span class="badge badge-secondary">User ID #<?php echo $activity->user; ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-robot mr-1"></i> Sistem / Pengunjung Tamu</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-light font-weight-bold">Alamat IP (IP Address)</td>
                            <td>
                                <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important;">
                                    <i class="fas fa-network-wired mr-1"></i> <?php echo htmlspecialchars($activity->ip_address); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-light font-weight-bold">Waktu &amp; Tanggal Kejadian</td>
                            <td>
                                <strong class="text-success"><i class="far fa-clock mr-1"></i> <?php echo date('d F Y, H:i:s', $time); ?> WIB</strong>
                                <span class="text-muted small ml-2">(Zona Waktu: Asia/Jakarta GMT+7)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="<?php echo url('activity_logs'); ?>" class="btn btn-secondary radius-8 px-20">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Log Aktivitas
                </a>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
