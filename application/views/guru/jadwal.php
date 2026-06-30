<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Jadwal Mengajar Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>JP Ke</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?php echo html_escape($row->hari) ?></td>
                                <td><?php echo (int) $row->slot_ke ?></td>
                                <td><?php echo html_escape(trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?> <?php echo $row->mapel_singkat ? '(' . html_escape($row->mapel_singkat) . ')' : '' ?></td>
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
