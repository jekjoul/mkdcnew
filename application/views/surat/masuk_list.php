<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card border-0 shadow-xs radius-16 overflow-hidden">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center py-16 px-24">
            <h6 class="mb-0 text-light fw-bold">Surat Masuk</h6>
            <a href="<?php echo url('surat/masuk_tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2 radius-8 fw-semibold">
                <iconify-icon icon="solar:add-circle-linear" class="text-lg"></iconify-icon> Tambah Surat Masuk
            </a>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable">
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
                                <td><?php echo htmlspecialchars($row->pengirim) ?></td>
                                <td><?php echo htmlspecialchars($row->tujuan_surat) ?></td>
                                <td><?php echo htmlspecialchars($row->perihal ?: '-') ?></td>
                                <td><span class="badge bg-info-100 text-info-600"><?php echo htmlspecialchars($row->status_disposisi) ?></span></td>
                                <td>
                                    <?php if ($row->scan_file): ?>
                                        <a href="<?php echo url('uploads/surat_masuk/' . $row->scan_file) ?>" target="_blank" class="text-primary-600 fw-semibold">Lihat Berkas</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('surat/masuk_edit/' . $row->id_surat_masuk) ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 radius-8">
                                            <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-surat d-flex align-items-center gap-1 radius-8"
                                                data-url="<?php echo url('surat/masuk_hapus/' . $row->id_surat_masuk) ?>"
                                                data-title="Surat Masuk dari '<?php echo htmlspecialchars($row->pengirim) ?>' (Perihal: <?php echo htmlspecialchars($row->perihal ?: '-') ?>)">
                                            <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon> Hapus
                                        </button>
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

<!-- Modal Konfirmasi Hapus Surat -->
<div class="modal fade" id="deleteSuratModal" tabindex="-1" aria-labelledby="deleteSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-24 px-24 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 text-center">
                <div class="w-64-px h-64-px radius-circle bg-danger-100 text-danger-600 d-inline-flex align-items-center justify-content-center text-3xl mb-16 mx-auto">
                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                </div>
                <h5 class="modal-title fw-bold text-neutral-900 mb-8" id="deleteSuratModalLabel">Konfirmasi Hapus Surat</h5>
                <p class="text-secondary-light text-sm mb-0">
                    Apakah Anda yakin ingin menghapus <strong class="text-neutral-900" id="deleteSuratTarget">surat ini</strong>?<br>
                    <span class="text-danger-600 fw-medium">Tindakan ini tidak dapat dibatalkan.</span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-24 px-24 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-neutral-400 text-neutral-700 py-10 px-20 radius-8 fw-semibold text-sm" data-bs-dismiss="modal">
                    Batal
                </button>
                <a id="btnConfirmDeleteSurat" href="#" class="btn btn-danger-600 py-10 px-20 radius-8 fw-semibold text-sm d-flex align-items-center gap-2 shadow-xs">
                    <iconify-icon icon="solar:trash-bin-trash-bold" class="text-lg"></iconify-icon>
                    <span>Ya, Hapus Sekarang</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');

    $(document).on('click', '.btn-delete-surat', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).data('url');
        const deleteTitle = $(this).data('title') || 'surat ini';
        $('#deleteSuratTarget').text(deleteTitle);
        $('#btnConfirmDeleteSurat').attr('href', deleteUrl);
        const modalEl = document.getElementById('deleteSuratModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            $('#deleteSuratModal').modal('show');
        }
    });
</script>
