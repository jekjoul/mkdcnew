<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light mb-0"><?php echo $row ? 'Edit Calon Siswa' : 'Input Daftar Ulang Calon Siswa'; ?></h6>
                    <a href="<?php echo url('calon_siswa') ?>" class="btn btn-light-100 text-dark radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php include viewPath('calon_siswa/partials/form_fields'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php include viewPath('calon_siswa/partials/form_script'); ?>
