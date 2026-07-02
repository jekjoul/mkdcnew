<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card mb-24">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
            <h6 class="mb-0 text-light">View Buku Induk Siswa</h6>
            <div class="d-flex gap-2">
                <a href="<?php echo url('buku_induk_siswa'); ?>" class="btn btn-sm btn-neutral-100 text-neutral-700">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <a href="<?php echo url('buku_induk_siswa/export_pdf/' . $siswa->id_alumni); ?>" target="_blank" class="btn btn-sm btn-warning-600 text-light">
                    <i class="ri-printer-line"></i> Print / Export PDF
                </a>
            </div>
        </div>
        <div class="card-body bg-neutral-100">
            <div class="buku-induk-preview">
                <?php include viewPath('buku_induk_siswa/partials/template'); ?>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
