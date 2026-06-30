<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Kode Surat</h6>
            <a href="<?php echo url('surat/kode_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Lembaga</th>
                            <th>Kode</th>
                            <th>Jenis Surat</th>
                            <th>Format Nomor</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kode as $row): ?>
                            <tr>
                                <td><?php echo $row->nama_lembaga ?></td>
                                <td><?php echo $row->kode_jenis ?></td>
                                <td><?php echo $row->nama_jenis ?></td>
                                <td><code><?php echo $row->format_nomor ?></code></td>
                                <td><?php echo $row->status ?></td>
                                <td class="text-center"><a href="<?php echo url('surat/kode_edit/' . $row->id_kode_surat) ?>" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
<script>let table = new DataTable('#dataTable');</script>
