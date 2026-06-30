<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('nilai_siswa/simpan_nilai/' . $item->id_pembelajaran_mapel) ?>" method="post">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
                <h6 class="mb-0 text-light">Input Nilai Siswa</h6>
                <a href="<?php echo url('nilai_siswa/setting/' . $item->id_pembelajaran_mapel) ?>" class="btn btn-light btn-sm d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-linear"></iconify-icon> Setting Persentase
                </a>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tahun/Semester</span>
                        <strong><?php echo html_escape($item->tahun_pelajaran) ?> (<?php echo html_escape($item->semester) ?>)</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Kelas</span>
                        <strong><?php echo html_escape(trim($item->nama_tingkat . ' - ' . $item->nama_rombel)) ?></strong>
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
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-info-100 text-info-600">Harian <?php echo $setting ? (float) $setting->persen_harian : 0 ?>%</span>
                    <span class="badge bg-warning-100 text-warning-600">PSTS <?php echo $setting ? (float) $setting->persen_psts : 0 ?>%</span>
                    <span class="badge bg-success-100 text-success-600">PSAS <?php echo $setting ? (float) $setting->persen_psas : 0 ?>%</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-neutral-100">
                <h6 class="mb-0">Daftar Nilai</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama Siswa</th>
                                <th width="150">NISN/NIPD</th>
                                <th width="140">Nilai Harian</th>
                                <th width="140">PSTS</th>
                                <th width="140">PSAS</th>
                                <th width="">Nilai Rapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswa as $index => $s): ?>
                                <?php $row = isset($nilai[(int) $s->id_siswa]) ? $nilai[(int) $s->id_siswa] : null; ?>
                                <tr class="nilai-row">
                                    <td><?php echo $index + 1 ?></td>
                                    <td class="fw-semibold"><?php echo html_escape($s->nama_siswa) ?></td>
                                    <td>
                                        <span class="d-block"><?php echo html_escape($s->nisn ?: '-') ?></span>
                                        <span class="text-secondary-light"><?php echo html_escape($s->nipd ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-harian" name="nilai[<?php echo (int) $s->id_siswa ?>][harian]" value="<?php echo $row && $row->nilai_harian !== null ? (float) $row->nilai_harian : '' ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-psts" name="nilai[<?php echo (int) $s->id_siswa ?>][psts]" value="<?php echo $row && $row->nilai_psts !== null ? (float) $row->nilai_psts : '' ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-psas" name="nilai[<?php echo (int) $s->id_siswa ?>][psas]" value="<?php echo $row && $row->nilai_psas !== null ? (float) $row->nilai_psas : '' ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control nilai-rapor bg-neutral-100" value="<?php echo $row && $row->nilai_rapor !== null ? (float) $row->nilai_rapor : '' ?>" readonly>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($siswa)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-secondary-light py-4">Belum ada siswa pada pembelajaran ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo url('nilai_siswa') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Nilai</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    const persenHarian = <?php echo $setting ? (float) $setting->persen_harian : 0 ?>;
    const persenPsts = <?php echo $setting ? (float) $setting->persen_psts : 0 ?>;
    const persenPsas = <?php echo $setting ? (float) $setting->persen_psas : 0 ?>;

    function nilaiNumber(value) {
        if (value === '') {
            return null;
        }
        const parsed = parseFloat(value);
        if (Number.isNaN(parsed)) {
            return null;
        }
        return Math.min(100, Math.max(0, parsed));
    }

    function hitungRow(row) {
        const harian = nilaiNumber(row.find('.nilai-harian').val());
        const psts = nilaiNumber(row.find('.nilai-psts').val());
        const psas = nilaiNumber(row.find('.nilai-psas').val());
        let hasValue = false;
        let rapor = 0;

        if (harian !== null) {
            hasValue = true;
            rapor += harian * persenHarian / 100;
        }
        if (psts !== null) {
            hasValue = true;
            rapor += psts * persenPsts / 100;
        }
        if (psas !== null) {
            hasValue = true;
            rapor += psas * persenPsas / 100;
        }

        row.find('.nilai-rapor').val(hasValue ? rapor.toFixed(2).replace(/\.00$/, '') : '');
    }

    $('.nilai-input').on('input', function() {
        hitungRow($(this).closest('.nilai-row'));
    });
</script>
