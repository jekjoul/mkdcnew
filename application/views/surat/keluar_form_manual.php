<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/keluar_simpan') ?>" method="post" id="formSuratManual">
        <input type="hidden" name="id_surat_keluar" value="<?php echo @$row->id_surat_keluar ?>">
        <input type="hidden" name="token_validasi" value="<?php echo @$row->token_validasi ?>">
        <input type="hidden" name="metode_pembuatan" value="Manual">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light"><?php echo @$row->id_surat_keluar ? 'Edit Surat Keluar Manual' : 'Buat Surat Keluar Manual' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode/Kategori Surat <span class="text-danger">*</span></label>
                        <select name="id_kode_surat" id="kodeSelect" class="form-select" required>
                            <option value="">Pilih Kode/Kategori Surat</option>
                            <?php foreach ($kode as $k): ?>
                                <option value="<?php echo $k->id_kode_surat ?>" <?php echo @$row->id_kode_surat == $k->id_kode_surat ? 'selected' : '' ?>>
                                    <?php echo $k->nama_lembaga ?> - <?php echo $k->kode_jenis ?> - <?php echo $k->nama_jenis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggalSurat" class="form-control" value="<?php echo @$row->tanggal_surat ?: date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nomor Surat (Otomatis dari Sistem)</label>
                        <input type="text" id="nomorSuratDisplay" class="form-control bg-light" value="<?php echo @$row->nomor_surat ?>" readonly placeholder="Nomor otomatis terisi ketika kode dipilih">
                        <input type="hidden" name="nomor_surat" id="nomorSuratHidden" value="<?php echo @$row->nomor_surat ?>">
                        <input type="hidden" name="nomor_urut" id="nomorUrutHidden" value="<?php echo @$row->nomor_urut ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tujuan Surat <span class="text-danger">*</span></label>
                        <input type="text" name="tujuan_surat" class="form-control" value="<?php echo @$row->tujuan_surat ?>" required placeholder="Contoh: Orang Tua Siswa / Kepala Dinas">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Perihal Surat <span class="text-danger">*</span></label>
                        <input type="text" name="perihal" class="form-control" value="<?php echo @$row->perihal ?>" required placeholder="Contoh: Undangan Rapat / Surat Pemberitahuan">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Keterangan / Deskripsi Ringkas</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Tuliskan catatan atau deskripsi ringkas surat keluar jika diperlukan..."><?php echo @$row->keterangan ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold d-block mb-8">Pejabat Penandatangan <span class="text-danger">*</span> <small class="text-muted">(Bisa memilih lebih dari satu)</small></label>
                        <div class="row bg-light p-16 radius-8 g-3 border">
                            <?php foreach ($ptk as $p): 
                                $isChecked = in_array($p->id_ptk, $selected_penandatangan);
                                $jabatanVal = isset($penandatangan_jabatan_map[$p->id_ptk]) ? $penandatangan_jabatan_map[$p->id_ptk] : '';
                            ?>
                                <div class="col-md-6">
                                    <div class="border p-12 radius-8 bg-white h-100 d-flex flex-column justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input ptk-checkbox" type="checkbox" name="id_ptk_penandatangan[]" value="<?php echo $p->id_ptk ?>" id="ptk_<?php echo $p->id_ptk ?>" <?php echo $isChecked ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold" for="ptk_<?php echo $p->id_ptk ?>">
                                                <?php echo $p->nama_ptk ?>
                                            </label>
                                            <div class="text-muted text-xs">NIK/NIY: <?php echo $p->nik ?: ($p->niy ?: '-') ?></div>
                                        </div>
                                        <div class="mt-8">
                                            <input type="text" name="jabatan_penandatangan[<?php echo $p->id_ptk ?>]" class="form-control form-control-sm" placeholder="Ketik Jabatan (misal: Kepala Sekolah / Bendahara)" value="<?php echo htmlspecialchars($jabatanVal) ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?php echo url('surat/keluar') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-600">Simpan & Preview</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        function generateNomorSurat() {
            var id_kode_surat = $('#kodeSelect').val();
            var tanggal_surat = $('#tanggalSurat').val();
            var exclude_id = '<?php echo @$row->id_surat_keluar ?: 0 ?>';

            if (!id_kode_surat) {
                $('#nomorSuratDisplay').val('');
                $('#nomorSuratHidden').val('');
                $('#nomorUrutHidden').val('');
                return;
            }

            $.getJSON('<?php echo url("surat/get_next_nomor_ajax") ?>', {
                id_kode_surat: id_kode_surat,
                tanggal_surat: tanggal_surat,
                exclude_id: exclude_id
            }, function(res) {
                if (res.nomor_surat) {
                    $('#nomorSuratDisplay').val(res.nomor_surat);
                    $('#nomorSuratHidden').val(res.nomor_surat);
                    $('#nomorUrutHidden').val(res.nomor_urut);
                }
            });
        }

        $('#kodeSelect, #tanggalSurat').on('change', generateNomorSurat);

        // Trigger generate jika edit
        if ($('#kodeSelect').val()) {
            // Hanya trigger jika belum memiliki nomor surat atau ingin me-refresh
            <?php if (empty($row->nomor_surat)): ?>
                generateNomorSurat();
            <?php endif; ?>
        }

        // Validasi minimal 1 penandatangan dipilih sebelum submit
        $('#formSuratManual').on('submit', function(e) {
            if ($('.ptk-checkbox:checked').length === 0) {
                e.preventDefault();
                alert('Peringatan: Silakan pilih minimal 1 Pejabat Penandatangan!');
            }
        });
    });
</script>
