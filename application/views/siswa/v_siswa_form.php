<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light">Formulir Tambah Siswa</h6>
                </div>
                <div class="card-body">
                    <?php $row = null; include viewPath('siswa/partials/v_siswa_form_fields'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<?php include viewPath('siswa/partials/v_siswa_form_script'); ?>
