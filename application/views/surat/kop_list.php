<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Pengaturan Kop Surat</h6>
            <a href="<?php echo url('surat/kop_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah Kop Surat
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nama Kop</th>
                            <th>Naungan</th>
                            <th>Nama Lembaga</th>
                            <th>Sub Nama / Instansi</th>
                            <th>Tata Letak (Layout)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kop as $row): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row->logo)): ?>
                                        <img src="<?php echo url('uploads/kop_logo/' . $row->logo) ?>" alt="Logo" class="object-fit-cover" style="width: 50px; height: 50px; border-radius: 4px; border: 1px solid #dee2e6;">
                                    <?php else: ?>
                                        <span class="text-muted text-xs">Tidak ada logo</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_kop) ?></span></td>
                                <td><span class="text-xs"><?php echo html_escape($row->naungan) ?></span></td>
                                <td><strong class="text-md"><?php echo html_escape($row->nama_lembaga) ?></strong></td>
                                <td><span class="text-xs"><?php echo html_escape($row->sub_nama) ?></span></td>
                                <td>
                                    <?php if ($row->layout_style === 'left_logo'): ?>
                                        <span class="badge bg-primary-100 text-primary-800">Logo Kiri</span>
                                    <?php elseif ($row->layout_style === 'double_logo'): ?>
                                        <span class="badge bg-info-100 text-info-800">Logo Kiri & Kanan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-100 text-secondary-800">Tengah (Center)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row->status === 'Aktif'): ?>
                                        <span class="badge bg-success-100 text-success-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-100 text-danger-800">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('surat/kop_edit/' . $row->id_kop_surat) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <a href="<?php echo url('surat/kop_hapus/' . $row->id_kop_surat) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kop surat ini?')">Hapus</a>
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
