<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/template_simpan') ?>" method="post">
        <input type="hidden" name="id_template_surat" value="<?php echo @$row->id_template_surat ?>">
        <div class="card">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light">Form Template Surat</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <!-- PENGATURAN HAK AKSES LEMBAGA (CHECKLIST / ROLE & PERMISSION STYLE) -->
                    <div class="col-md-12 mb-2">
                        <div class="card border border-neutral-200 radius-12 p-20 bg-neutral-50 shadow-none">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-16 pb-12 border-bottom border-neutral-200">
                                <div>
                                    <h6 class="fw-bold text-neutral-900 mb-1">Hak Akses & Penggunaan Lembaga / Unit</h6>
                                    <p class="text-secondary-light text-xs mb-0">Centang lembaga yang diperbolehkan menggunakan template surat ini. Jika tidak ada yang dicentang, template berlaku untuk <strong>Semua Lembaga (Umum / Bebas Digunakan Bersama)</strong>.</p>
                                </div>
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="check_all_lembaga">
                                    <label class="form-check-label fw-bold text-xs cursor-pointer text-primary-600 mb-0" for="check_all_lembaga">
                                        Pilih Semua Lembaga
                                    </label>
                                </div>
                            </div>

                            <div class="row g-3">
                                <?php foreach ($lembaga_list as $lg): 
                                    $isChecked = (!empty($selected_lembaga_ids) && in_array($lg->id_lembaga, $selected_lembaga_ids)) ? 'checked' : '';
                                    $isYayasan = (strtoupper(trim($lg->nama_lembaga_singkat)) === 'YAYASAN');
                                ?>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-check custom-lembaga-card p-12 radius-8 border border-neutral-200 bg-white d-flex align-items-center gap-2 m-0 h-100">
                                            <input class="form-check-input check-lembaga-item flex-shrink-0" type="checkbox" name="id_lembaga[]" value="<?php echo $lg->id_lembaga ?>" id="lg_<?php echo $lg->id_lembaga ?>" <?php echo $isChecked ?>>
                                            <label class="form-check-label cursor-pointer flex-grow-1 min-w-0 mb-0" for="lg_<?php echo $lg->id_lembaga ?>">
                                                <div class="fw-bold text-xs text-neutral-900 text-truncate" title="<?php echo htmlspecialchars($lg->nama_lembaga) ?>">
                                                    <?php echo htmlspecialchars($lg->nama_lembaga) ?>
                                                </div>
                                                <span class="badge <?php echo $isYayasan ? 'bg-warning-100 text-warning-800' : 'bg-primary-50 text-primary-700' ?> px-6 py-2 radius-4 text-xs mt-1">
                                                    <?php echo htmlspecialchars($lg->nama_lembaga_singkat) ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kategori Surat <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <?php 
                            $cats = [
                                'Kesiswaan',
                                'Kepegawaian & Penugasan',
                                'Kedinasan & Komunikasi',
                                'Akademik & Kelulusan',
                                'Rekomendasi & Perjanjian',
                                'Yayasan & Kelembagaan'
                            ];
                            $currCat = @$row->kategori ?: 'Kesiswaan';
                            foreach ($cats as $ct):
                            ?>
                                <option value="<?php echo $ct ?>" <?php echo $currCat === $ct ? 'selected' : '' ?>><?php echo $ct ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?php echo @$row->status !== 'Nonaktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?php echo @$row->status === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Template Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_template" class="form-control" value="<?php echo htmlspecialchars(@$row->nama_template ?: '') ?>" placeholder="Contoh: Surat Keterangan Siswa Aktif" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Jenis Surat (Opsional)</label>
                        <select name="id_kode_surat" class="form-select">
                            <option value="">-- Tanpa Kode Surat Khusus --</option>
                            <?php foreach ($kode as $k): ?>
                                <option value="<?php echo $k->id_kode_surat ?>" <?php echo @$row->id_kode_surat == $k->id_kode_surat ? 'selected' : '' ?>>
                                    <?php echo $k->nama_lembaga ?> - <?php echo $k->kode_jenis ?> - <?php echo $k->nama_jenis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Deskripsi Singkat Template</label>
                        <input type="text" name="deskripsi" class="form-control" value="<?php echo htmlspecialchars(@$row->deskripsi ?: '') ?>" placeholder="Contoh: Surat keterangan resmi yang menyatakan bahwa siswa bersangkutan masih terdaftar aktif.">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Iconify Icon Name</label>
                        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars(@$row->icon ?: 'solar:document-bold-duotone') ?>" placeholder="Contoh: solar:document-bold-duotone">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Action Custom URL (Opsional)</label>
                        <input type="text" name="target_url" class="form-control" value="<?php echo htmlspecialchars(@$row->target_url ?: '') ?>" placeholder="Kosongkan jika menggunakan form standar otomatis (misal: surat/keterangan_siswa_aktif)">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Perihal Default</label>
                        <input type="text" name="perihal_default" class="form-control" value="<?php echo htmlspecialchars(@$row->perihal_default ?: '') ?>" placeholder="Contoh: Surat Keterangan Siswa Aktif">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Isi Konten Template (Naskah Surat)</label>
                        <textarea name="isi_template" class="form-control" rows="12"><?php echo @$row->isi_template ?></textarea>
                        <small class="text-secondary-light">Variabel yang tersedia: {{nomor_surat}}, {{tanggal_surat}}, {{tujuan_surat}}, {{perihal}}, {{nama_lembaga}}, {{tahun}}, {{validasi_url}}</small>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?php echo url('surat/template') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check_all_lembaga');
    const itemChecks = document.querySelectorAll('.check-lembaga-item');

    function updateCheckAllState() {
        if (!checkAll || itemChecks.length === 0) return;
        const checkedCount = document.querySelectorAll('.check-lembaga-item:checked').length;
        checkAll.checked = (checkedCount === itemChecks.length && itemChecks.length > 0);
        checkAll.indeterminate = (checkedCount > 0 && checkedCount < itemChecks.length);
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            itemChecks.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    }

    itemChecks.forEach(cb => {
        cb.addEventListener('change', updateCheckAllState);
    });

    updateCheckAllState();
});
</script>
