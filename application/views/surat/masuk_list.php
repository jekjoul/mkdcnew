<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Surat Masuk</h6>
            <a href="<?php echo url('surat/masuk_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pengirim</th>
                            <th>Tujuan</th>
                            <th>Perihal</th>
                            <th>Disposisi</th>
                            <th>Berkas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surat as $row): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row->tanggal_surat)) ?></td>
                                <td><?php echo $row->pengirim ?></td>
                                <td><?php echo $row->tujuan_surat ?></td>
                                <td><?php echo $row->perihal ?: '-' ?></td>
                                <td><span class="badge bg-info-100 text-info-600"><?php echo $row->status_disposisi ?></span></td>
                                <td>
                                    <?php if ($row->scan_file): ?>
                                        <a href="<?php echo url('uploads/surat_masuk/' . $row->scan_file) ?>" target="_blank" class="text-primary-600 fw-semibold">Lihat</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo url('surat/masuk_edit/' . $row->id_surat_masuk) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
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
