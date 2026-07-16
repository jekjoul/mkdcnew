<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="row gy-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Pembelajaran Saya</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_pembelajaran ?></h4>
                    <a href="<?php echo url('guru/pembelajaran') ?>" class="btn btn-sm btn-primary-600 mt-3">Lihat Pembelajaran</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Siswa Terampu</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_siswa ?></h4>
                    <a href="<?php echo url('guru/siswa') ?>" class="btn btn-sm btn-info text-light mt-3">Lihat Siswa</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Total Jam Mengajar</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_jadwal ?></h4>
                    <a href="<?php echo url('guru/jadwal') ?>" class="btn btn-sm btn-warning-600 mt-3">Lihat Jadwal</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-20">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Portal Guru</h6>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-3">
                    <a href="<?php echo url('guru/siswa') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Data Siswa
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/pembelajaran') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:notebook-bookmark-linear"></iconify-icon> Pembelajaran
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/nilai') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Input Nilai
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/profil') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="icon-park-outline:user-business"></iconify-icon> Profil PTK
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
