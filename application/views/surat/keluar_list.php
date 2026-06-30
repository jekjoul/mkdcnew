<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Surat Keluar</h6>
            <a href="<?php echo url('surat/keluar_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Buat Surat
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Surat</th>
                            <th>Lembaga</th>
                            <th>Tujuan</th>
                            <th>Perihal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surat as $row): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row->tanggal_surat)) ?></td>
                                <td><span class="fw-semibold"><?php echo $row->nomor_surat ?></span></td>
                                <td><?php echo $row->nama_lembaga ?></td>
                                <td><?php echo $row->tujuan_surat ?></td>
                                <td><?php echo $row->perihal ?></td>
                                <td><?php echo $row->status ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('surat/keluar_preview/' . $row->id_surat_keluar) ?>" class="btn btn-sm btn-outline-primary">Preview</a>
                                        <a href="<?php echo url('surat/keluar_edit/' . $row->id_surat_keluar) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
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
<script>let table = new DataTable('#dataTable');</script>
