<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Template Surat</h6>
            <a href="<?php echo url('surat/template_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Cakupan Lembaga</th>
                            <th>Kategori</th>
                            <th>Nama Template</th>
                            <th>Perihal Default</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($template as $idx => $row): ?>
                            <tr>
                                <td><?php echo $idx + 1 ?></td>
                                <td>
                                    <?php if (!empty($row->allowed_lembaga)): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($row->allowed_lembaga as $al): 
                                                $isYys = (strtoupper(trim($al->nama_lembaga_singkat)) === 'YAYASAN');
                                            ?>
                                                <span class="badge <?php echo $isYys ? 'bg-warning-100 text-warning-800' : 'bg-primary-50 text-primary-700' ?> px-8 py-4 radius-4 text-xs">
                                                    <?php echo htmlspecialchars($al->nama_lembaga_singkat ?: $al->nama_lembaga) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-700 px-8 py-4 radius-4 text-xs">
                                            Semua Lembaga (Umum)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($row->kategori ?: 'Kesiswaan') ?></span></td>
                                <td>
                                    <div class="fw-bold text-neutral-900"><?php echo htmlspecialchars($row->nama_template) ?></div>
                                    <?php if (!empty($row->deskripsi)): ?>
                                        <div class="text-xs text-secondary-light"><?php echo htmlspecialchars($row->deskripsi) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row->perihal_default ?: '-') ?></td>
                                <td>
                                    <?php if ($row->status === 'Aktif'): ?>
                                        <span class="badge bg-success-100 text-success-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-100 text-danger-800">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><a href="<?php echo url('surat/template_edit/' . $row->id_template_surat) ?>" class="btn btn-sm btn-outline-secondary">Edit</a></td>
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
