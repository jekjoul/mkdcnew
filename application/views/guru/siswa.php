<?php include viewPath('includes/header'); ?>
<?php
if (!function_exists('guru_mask')) {
    function guru_mask($value, $visible = 4)
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
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Data Siswa Terampu</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>NIPD</th>
                            <th>JK</th>
                            <th>Rombel</th>
                            <th>Mapel Saya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $i => $row): ?>
                            <tr>
                                <td><?php echo $i + 1 ?></td>
                                <td class="fw-semibold"><?php echo html_escape($row->nama_siswa) ?></td>
                                <td><?php echo html_escape(guru_mask($row->nisn)) ?></td>
                                <td><?php echo html_escape(guru_mask($row->nipd, 3)) ?></td>
                                <td><?php echo html_escape($row->jenis_kelamin ?: '-') ?></td>
                                <td><?php echo html_escape($row->rombel ?: '-') ?></td>
                                <td><?php echo html_escape($row->mapel ?: '-') ?></td>
                                <td><span class="badge bg-success-100 text-success-600"><?php echo html_escape($row->status_keaktifan ?: '-') ?></span></td>
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
