<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Pembelajaran Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>JP</th>
                            <th>Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</td>
                                <td><?php echo html_escape(trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?> <?php echo $row->mapel_singkat ? '(' . html_escape($row->mapel_singkat) . ')' : '' ?></td>
                                <td><?php echo (int) $row->jumlah_jam ?></td>
                                <td><?php echo (int) $row->jumlah_siswa ?></td>
                                <td>
                                    <a href="<?php echo url('guru/input_nilai/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Input Nilai
                                    </a>
                                    <a href="<?php echo url('guru/perangkat_detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:document-add-linear"></iconify-icon> Perangkat
                                    </a>
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
