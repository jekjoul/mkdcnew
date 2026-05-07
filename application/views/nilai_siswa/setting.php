<?php include viewPath('includes/header'); ?>
<?php
$persen_harian = $setting ? (float) $setting->persen_harian : ($default_setting ? (float) $default_setting->persen_harian : 40);
$persen_psts = $setting ? (float) $setting->persen_psts : ($default_setting ? (float) $default_setting->persen_psts : 30);
$persen_psas = $setting ? (float) $setting->persen_psas : ($default_setting ? (float) $default_setting->persen_psas : 30);
?>
<div class="dashboard-main-body">
    <form action="<?php echo url('nilai_siswa/simpan_setting/' . $id_pembelajaran_mapel) ?>" method="post">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light"><?php echo $id_pembelajaran_mapel > 0 ? 'Setting Persentase Nilai Mapel' : 'Setting Persentase Nilai Default' ?></h6>
            </div>
            <div class="card-body">
                <?php if ($item): ?>
                    <div class="row gy-3">
                        <div class="col-md-3">
                            <span class="text-secondary-light d-block">Tahun/Semester</span>
                            <strong><?php echo html_escape($item->tahun_pelajaran) ?> (<?php echo html_escape($item->semester) ?>)</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-secondary-light d-block">Kelas</span>
                            <strong><?php echo html_escape(trim($item->nama_lembaga . ' ' . $item->nama_tingkat . ' ' . $item->nama_rombel)) ?></strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-secondary-light d-block">Mata Pelajaran</span>
                            <strong><?php echo html_escape($item->nama_mapel) ?></strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-secondary-light d-block">Guru Mapel</span>
                            <strong><?php echo $item->nama_ptk ? html_escape($item->nama_ptk) : '-' ?></strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-secondary-light">Setting ini dipakai sebagai bawaan untuk semua guru dan mata pelajaran yang belum memiliki setting khusus.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-neutral-100">
                <h6 class="mb-0">Komponen Nilai Rapor</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label class="form-label">Ujian Harian (%)</label>
                        <input type="number" min="0" max="100" step="0.01" class="form-control persen-input" name="persen_harian" value="<?php echo $persen_harian ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Penilaian Sumatif Tengah Semester (%)</label>
                        <input type="number" min="0" max="100" step="0.01" class="form-control persen-input" name="persen_psts" value="<?php echo $persen_psts ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Penilaian Sumatif Akhir Semester (%)</label>
                        <input type="number" min="0" max="100" step="0.01" class="form-control persen-input" name="persen_psas" value="<?php echo $persen_psas ?>" required>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-secondary-light">Total Persentase</span>
                    <h5 class="mb-0" id="totalPersen">100%</h5>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo $id_pembelajaran_mapel > 0 ? url('nilai_siswa/input/' . $id_pembelajaran_mapel) : url('nilai_siswa') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Setting</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    function refreshTotal() {
        let total = 0;
        $('.persen-input').each(function() {
            const value = parseFloat($(this).val());
            total += Number.isNaN(value) ? 0 : value;
        });
        $('#totalPersen')
            .text(total.toFixed(2).replace(/\.00$/, '') + '%')
            .toggleClass('text-danger', Math.round(total * 100) / 100 !== 100)
            .toggleClass('text-success', Math.round(total * 100) / 100 === 100);
    }

    $('.persen-input').on('input', refreshTotal);
    refreshTotal();
</script>
