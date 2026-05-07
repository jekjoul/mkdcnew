<?php include viewPath('includes/header'); ?>
<?php
if (!function_exists('guru_mask_nilai')) {
    function guru_mask_nilai($value, $visible = 4)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '-';
        }
        if (strlen($value) <= $visible) {
            return str_repeat('*', strlen($value));
        }
        return substr($value, 0, $visible) . str_repeat('*', max(4, strlen($value) - $visible));
    }
}
?>
<div class="dashboard-main-body">
    <form action="<?php echo url('guru/simpan_nilai/' . $item->id_pembelajaran_mapel) ?>" method="post">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="text-light mb-0">Input Nilai Siswa</h6>
            </div>
            <div class="card-body">
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
                        <span class="text-secondary-light d-block">Bobot Rapor</span>
                        <strong>H <?php echo (float) $setting->persen_harian ?>% / PSTS <?php echo (float) $setting->persen_psts ?>% / PSAS <?php echo (float) $setting->persen_psas ?>%</strong>
                    </div>
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
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>NISN/NIPD</th>
                                <th>Ujian Harian</th>
                                <th>PSTS</th>
                                <th>PSAS</th>
                                <th>Rapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswa as $index => $s): ?>
                                <?php $row = isset($nilai[(int) $s->id_siswa]) ? $nilai[(int) $s->id_siswa] : null; ?>
                                <tr class="nilai-row">
                                    <td><?php echo $index + 1 ?></td>
                                    <td class="fw-semibold"><?php echo html_escape($s->nama_siswa) ?></td>
                                    <td><?php echo html_escape(guru_mask_nilai($s->nisn) . ' / ' . guru_mask_nilai($s->nipd, 3)) ?></td>
                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-harian" name="nilai[<?php echo (int) $s->id_siswa ?>][harian]" value="<?php echo $row && $row->nilai_harian !== null ? (float) $row->nilai_harian : '' ?>"></td>
                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-psts" name="nilai[<?php echo (int) $s->id_siswa ?>][psts]" value="<?php echo $row && $row->nilai_psts !== null ? (float) $row->nilai_psts : '' ?>"></td>
                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control nilai-input nilai-psas" name="nilai[<?php echo (int) $s->id_siswa ?>][psas]" value="<?php echo $row && $row->nilai_psas !== null ? (float) $row->nilai_psas : '' ?>"></td>
                                    <td><input type="text" class="form-control nilai-rapor bg-neutral-100" value="<?php echo $row && $row->nilai_rapor !== null ? (float) $row->nilai_rapor : '' ?>" readonly></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo url('guru/nilai') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Nilai</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    const persenHarian = <?php echo (float) $setting->persen_harian ?>;
    const persenPsts = <?php echo (float) $setting->persen_psts ?>;
    const persenPsas = <?php echo (float) $setting->persen_psas ?>;

    function nilaiNumber(value) {
        if (value === '') return null;
        const parsed = parseFloat(value);
        return Number.isNaN(parsed) ? null : Math.min(100, Math.max(0, parsed));
    }

    function hitungRow(row) {
        const harian = nilaiNumber(row.find('.nilai-harian').val());
        const psts = nilaiNumber(row.find('.nilai-psts').val());
        const psas = nilaiNumber(row.find('.nilai-psas').val());
        let rapor = 0;
        let hasValue = false;
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
