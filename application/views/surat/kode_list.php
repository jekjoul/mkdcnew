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
                                <td><?php echo htmlspecialchars($row->nama_lembaga) ?></td>
                                <td><?php echo htmlspecialchars($row->kode_jenis) ?></td>
                                <td><?php echo htmlspecialchars($row->nama_jenis) ?></td>
                                <td><code><?php echo htmlspecialchars($row->format_nomor) ?></code></td>
                                <td><?php echo htmlspecialchars($row->status) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('surat/kode_edit/' . $row->id_kode_surat) ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 radius-8">
                                            <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kode d-flex align-items-center gap-1 radius-8"
                                                data-url="<?php echo url('surat/kode_hapus/' . $row->id_kode_surat) ?>"
                                                data-title="<?php echo htmlspecialchars($row->kode_jenis . ' - ' . $row->nama_jenis) ?>">
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

<!-- Modal Konfirmasi Hapus Kode Surat -->
<div class="modal fade" id="deleteKodeModal" tabindex="-1" aria-labelledby="deleteKodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-24 px-24 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 text-center">
                <div class="w-64-px h-64-px radius-circle bg-danger-100 text-danger-600 d-inline-flex align-items-center justify-content-center text-3xl mb-16 mx-auto">
                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                </div>
                <h5 class="modal-title fw-bold text-neutral-900 mb-8" id="deleteKodeModalLabel">Konfirmasi Hapus Kode Surat</h5>
                <p class="text-secondary-light text-sm mb-0">
                    Apakah Anda yakin ingin menghapus Kode Surat <strong class="text-neutral-900" id="deleteKodeTarget">kode ini</strong>?<br>
                    <span class="text-secondary-light">Sistem akan memeriksa jika kode ini belum pernah digunakan oleh dokumen surat.</span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-24 px-24 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-neutral-400 text-neutral-700 py-10 px-20 radius-8 fw-semibold text-sm" data-bs-dismiss="modal">
                    Batal
                </button>
                <a id="btnConfirmDeleteKode" href="#" class="btn btn-danger-600 py-10 px-20 radius-8 fw-semibold text-sm d-flex align-items-center gap-2 shadow-xs">
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

    $(document).on('click', '.btn-delete-kode', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).data('url');
        const deleteTitle = $(this).data('title') || 'Kode Surat Ini';
        $('#deleteKodeTarget').text(deleteTitle);
        $('#btnConfirmDeleteKode').attr('href', deleteUrl);
        const modalEl = document.getElementById('deleteKodeModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            $('#deleteKodeModal').modal('show');
        }
    });
</script>
