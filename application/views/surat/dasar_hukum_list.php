<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-primary-600 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white">Master Dasar Hukum SK (Mengingat)</h6>
            <button type="button" class="btn btn-light btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#dasarHukumModal" onclick="resetForm()">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Tambah Dasar Hukum
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center">Urutan</th>
                            <th style="width: 160px;">Kategori</th>
                            <th>Judul / Rumusan Dasar Hukum</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dasar_hukum as $dh): ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo $dh->urutan ?></td>
                                <td><span class="badge bg-neutral-100 text-neutral-800 border radius-4"><?php echo htmlspecialchars($dh->kategori) ?></span></td>
                                <td><?php echo htmlspecialchars($dh->judul) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 radius-8 btn-edit-dh"
                                                data-id="<?php echo $dh->id_dasar_hukum ?>"
                                                data-kategori="<?php echo htmlspecialchars($dh->kategori) ?>"
                                                data-judul="<?php echo htmlspecialchars($dh->judul) ?>"
                                                data-urutan="<?php echo $dh->urutan ?>">
                                            <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Edit
                                        </button>
                                        <a href="<?php echo url('surat/dasar_hukum_hapus/' . $dh->id_dasar_hukum) ?>"
                                           class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 radius-8"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus dasar hukum ini?');">
                                            <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon> Hapus
                                        </a>
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

<!-- Modal Form Tambah / Edit Dasar Hukum -->
<div class="modal fade" id="dasarHukumModal" tabindex="-1" aria-labelledby="dasarHukumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow">
            <form action="<?php echo url('surat/dasar_hukum_simpan') ?>" method="post">
                <input type="hidden" name="id_dasar_hukum" id="dh_id" value="">
                <div class="modal-header border-bottom px-24 py-16">
                    <h6 class="modal-title fw-bold" id="dasarHukumModalLabel">Form Dasar Hukum SK</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm">Kategori</label>
                        <select name="kategori" id="dh_kategori" class="form-select radius-8">
                            <option value="Undang-Undang">Undang-Undang</option>
                            <option value="Peraturan Pemerintah">Peraturan Pemerintah</option>
                            <option value="Permendikbud">Permendikbud / Peraturan Menteri</option>
                            <option value="SK Yayasan">SK Yayasan / Keputusan Pengurus</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm">Judul / Rumusan Dasar Hukum <span class="text-danger">*</span></label>
                        <textarea name="judul" id="dh_judul" class="form-control radius-8" rows="4" required placeholder="Contoh: Undang-Undang No 20 Tahun 2003 tentang Sistem Pendidikan Nasional;"></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm">Urutan Tampil Default</label>
                        <input type="number" name="urutan" id="dh_urutan" class="form-control radius-8" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer border-top px-24 py-16">
                    <button type="button" class="btn btn-outline-neutral-400 radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 text-sm px-20">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');

    function resetForm() {
        $('#dh_id').val('');
        $('#dh_judul').val('');
        $('#dh_urutan').val('1');
        $('#dh_kategori').val('Undang-Undang');
        $('#dasarHukumModalLabel').text('Tambah Dasar Hukum SK');
    }

    $(document).on('click', '.btn-edit-dh', function() {
        const id = $(this).data('id');
        const kat = $(this).data('kategori');
        const judul = $(this).data('judul');
        const urutan = $(this).data('urutan');

        $('#dh_id').val(id);
        $('#dh_kategori').val(kat);
        $('#dh_judul').val(judul);
        $('#dh_urutan').val(urutan);
        $('#dasarHukumModalLabel').text('Edit Dasar Hukum SK');
        $('#dasarHukumModal').modal('show');
    });
</script>
