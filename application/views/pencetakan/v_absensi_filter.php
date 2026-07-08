<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light text-md mb-0">Cetak Absensi Rombel Pembelajaran</h6>
                </div>
                <div class="card-body p-24">
                    <form action="<?php echo url('pencetakan/absensi') ?>" method="get" target="_blank">
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Rombel / Pembelajaran Aktif</label>
                                <select class="form-control radius-8 form-select" name="id_pembelajaran" required>
                                    <option value="">Pilih Kelas / Rombel</option>
                                    <?php foreach ($pembelajaran_list as $p): ?>
                                        <option value="<?php echo $p->id_pembelajaran ?>">
                                            <?php echo htmlspecialchars($p->nama_lembaga_singkat . ' - ' . $p->nama_tingkat . ' (' . $p->nama_rombel . ') - TP. ' . $p->tahun_pelajaran . ' ' . $p->semester) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-20">
                                <button type="submit" class="btn btn-primary w-100 text-md py-12 radius-8 d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:printer-linear" class="text-xl"></iconify-icon>
                                    Buka Lembar Cetak
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
