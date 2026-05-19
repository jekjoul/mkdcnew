<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="row gy-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-warning-900">
                    <h6 class="mb-0 text-light">Setting Default Nilai Rapor</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-info-100 text-info-600">Harian <?php echo $default_setting ? (float) $default_setting->persen_harian : 0 ?>%</span>
                        <span class="badge bg-warning-100 text-warning-600">PSTS <?php echo $default_setting ? (float) $default_setting->persen_psts : 0 ?>%</span>
                        <span class="badge bg-success-100 text-success-600">PSAS <?php echo $default_setting ? (float) $default_setting->persen_psas : 0 ?>%</span>
                    </div>
                    <a href="<?php echo url('nilai_siswa/setting/0') ?>" class="btn btn-primary-600 btn-sm d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:settings-linear"></iconify-icon> Atur Default
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header bg-warning-900">
                    <h6 class="mb-0 text-light">Pengisian Nilai</h6>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <span class="text-secondary-light d-block">Komponen</span>
                            <strong>Harian, PSTS, PSAS, Rapor</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-secondary-light d-block">Basis Data</span>
                            <strong>Pembelajaran dan Guru Mapel</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-secondary-light d-block">Nilai Rapor</span>
                            <strong>Dihitung Otomatis</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-warning-900">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h6 class="mb-0 text-light"><?php echo !empty($is_nonaktif) ? 'Daftar Nilai Tahun Tidak Aktif' : 'Daftar Mata Pelajaran Pembelajaran Aktif'; ?></h6>
                <a href="<?php echo url(!empty($is_nonaktif) ? 'nilai_siswa' : 'nilai_siswa/nonaktif') ?>" class="btn btn-sm btn-warning-600 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>"></iconify-icon>
                    <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tahun/Sem</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th class="text-center">Siswa</th>
                            <th class="text-center">Terisi</th>
                            <th class="text-center">Setting</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?php echo html_escape(trim($row->nama_tingkat . ' - ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                <td>
                                    <span class="fw-semibold"><?php echo html_escape($row->nama_mapel) ?></span>
                                    <?php if (!empty($row->mapel_singkat)): ?>
                                        <span class="text-secondary-light">(<?php echo html_escape($row->mapel_singkat) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row->nama_ptk ? html_escape($row->nama_ptk) : '-' ?></td>
                                <td class="text-center"><?php echo (int) $row->jumlah_siswa ?></td>
                                <td class="text-center"><?php echo (int) $row->jumlah_dinilai ?></td>
                                <td class="text-center">
                                    <?php if ($row->id_pengaturan_nilai): ?>
                                        <span class="badge bg-success-100 text-success-600">Khusus</span>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-200 text-secondary-light">Default</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('nilai_siswa/input/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:pen-linear"></iconify-icon> Input
                                        </a>
                                        <a href="<?php echo url('nilai_siswa/setting/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:settings-linear"></iconify-icon> Persentase
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
