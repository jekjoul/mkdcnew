<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center gap-3">
            <h6 class="mb-0 text-light">Surat Keluar</h6>
            <div class="d-flex gap-2">
                <a href="<?php echo url('surat/keluar_tambah_manual') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-linear" class="text-lg"></iconify-icon> Buat Surat Manual
                </a>
                <a href="<?php echo url('surat/keluar_tambah_otomatis') ?>" class="btn btn-primary-600 btn-sm d-flex align-items-center gap-2 text-white">
                    <iconify-icon icon="solar:document-add-linear" class="text-lg"></iconify-icon> Buat Surat Otomatis
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Surat</th>
                            <th>Metode</th>
                            <th>Lembaga</th>
                            <th>Tujuan</th>
                            <th>Perihal</th>
                            <th>Penandatangan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surat as $row): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row->tanggal_surat)) ?></td>
                                <td><span class="fw-semibold"><?php echo $row->nomor_surat ?></span></td>
                                <td>
                                    <?php if ($row->metode_pembuatan === 'Otomatis'): ?>
                                        <span class="badge bg-success-100 text-success-600">Otomatis</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-100 text-warning-600">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row->nama_lembaga ?></td>
                                <td><?php echo $row->tujuan_surat ?></td>
                                <td><?php echo $row->perihal ?></td>
                                <td>
                                    <div class="text-xs">
                                        <?php if (!empty($row->penandatangan)): ?>
                                            <?php foreach ($row->penandatangan as $ptk): ?>
                                                <div>• <?php echo $ptk->nama_ptk ?> <small class="text-muted">(<?php echo $ptk->jabatan ?: '-' ?>)</small></div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo $row->status ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('surat/keluar_preview/' . $row->id_surat_keluar) ?>" class="btn btn-sm btn-outline-primary">Preview</a>
                                        <?php 
                                            $edit_url = ($row->metode_pembuatan === 'Otomatis') 
                                                ? url('surat/keluar_edit_otomatis/' . $row->id_surat_keluar) 
                                                : url('surat/keluar_edit_manual/' . $row->id_surat_keluar);
                                        ?>
                                        <a href="<?php echo $edit_url ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
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
