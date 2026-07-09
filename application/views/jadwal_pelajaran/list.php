<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-warning-900">
                    <h6 class="mb-0 text-light">Atur Kerangka Waktu</h6>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <span class="text-secondary-light d-block">Lama 1 Jam Pelajaran</span>
                        <h5 class="mb-0"><?php echo (int) $menit_jp ?> menit</h5>
                    </div>
                    <div>
                        <span class="text-secondary-light d-block">Hari Aktif</span>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($settings as $hari => $setting): ?>
                                <?php if (!empty($setting['aktif'])): ?>
                                    <span class="badge bg-info-100 text-info-600"><?php echo $hari ?>: <?php echo $setting['jumlah_jp'] ?> JP</span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="<?php echo url('jadwal_pelajaran/waktu') ?>" class="btn btn-primary-600 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:settings"></iconify-icon> Atur Kerangka Waktu
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-neutral-100">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <h6 class="mb-0"><?php echo !empty($is_nonaktif) ? 'Jadwal Tahun Tidak Aktif' : 'Susun Jadwal Semua Kelas'; ?></h6>
                        <a href="<?php echo url(!empty($is_nonaktif) ? 'jadwal_pelajaran' : 'jadwal_pelajaran/nonaktif') ?>" class="btn btn-sm btn-warning-600 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>"></iconify-icon>
                            <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                        </a>
                    </div>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <span class="text-secondary-light d-block">Jumlah Kelas Pembelajaran</span>
                        <h5 class="mb-0"><?php echo count($pembelajaran) ?> kelas</h5>
                    </div>
                    <div>
                        <span class="text-secondary-light d-block">Sumber item jadwal</span>
                        <div class="text-primary-light mt-2">Mapel, jumlah JP, dan guru dari data pembelajaran.</div>
                    </div>
                    <div class="mt-auto">
                        <?php if (empty($is_nonaktif)): ?>
                            <a href="<?php echo url('jadwal_pelajaran/semua') ?>" class="btn btn-warning-600 d-inline-flex align-items-center gap-2">
                                <iconify-icon icon="akar-icons:schedule"></iconify-icon> Drag & Drop Jadwal
                            </a>
                        <?php else: ?>
                            <span class="badge bg-neutral-200 text-secondary-light">Arsip jadwal tahun tidak aktif</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>