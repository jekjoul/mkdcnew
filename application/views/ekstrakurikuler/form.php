<!-- Load Select2 CSS di awal untuk visualisasi tema -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_ekskul" value="<?php echo $row ? $row->id_ekskul : ''; ?>">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="text-light mb-0"><?php echo $row ? 'Edit Kegiatan Ekstrakurikuler' : 'Tambah Kegiatan Ekstrakurikuler Baru' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ekskul" class="form-control" value="<?php echo $row ? html_escape($row->nama_ekskul) : '' ?>" required placeholder="Contoh: Pramuka Wajib, Futsal, Tari Tradisional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tahun Pelajaran / Semester <span class="text-danger">*</span></label>
                        <select name="id_tahun_pelajaran" class="form-select" required>
                            <?php foreach ($ta_list as $ta): ?>
                                <option value="<?php echo $ta->id_tahun_pelajaran ?>" <?php echo ($row && $row->id_tahun_pelajaran == $ta->id_tahun_pelajaran) || (!$row && $ta->status == 'Aktif') ? 'selected' : '' ?>>
                                    <?php echo html_escape($ta->tahun_pelajaran . ' (' . $ta->semester . ')') ?> <?php echo $ta->status == 'Aktif' ? '- (Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Guru Pembina (Dapat merangkap) <span class="text-danger">*</span></label>
                        <?php 
                        $selected_pembinas = [];
                        if ($row && !empty($row->id_ptk_pembina)) {
                            // Cek jika field lama berisi format JSON array pembina
                            $decoded = json_decode($row->id_ptk_pembina, true);
                            if (is_array($decoded)) {
                                $selected_pembinas = array_map('intval', $decoded);
                            } else {
                                $selected_pembinas = [(int) $row->id_ptk_pembina];
                            }
                        }
                        ?>
                        <select name="id_ptk_pembina[]" class="form-control select2" multiple required data-placeholder="Pilih satu atau lebih guru pembina...">
                            <?php foreach ($ptk_list as $ptk): ?>
                                <option value="<?php echo $ptk->id_ptk ?>" <?php echo in_array((int)$ptk->id_ptk, $selected_pembinas, true) ? 'selected' : '' ?>>
                                    <?php echo html_escape($ptk->nama_ptk) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <style>
                            /* Menata agar input pencarian dropdown dan tag-tag terpilih berada di baris yang berbeda */
                            .select2-container--default .select2-selection--multiple {
                                display: flex !important;
                                flex-direction: column-reverse !important; /* Tag terpilih dirender di bawah input dropdown */
                                height: auto !important;
                                padding: 6px 12px !important;
                                border: 1px solid #d1d5db !important;
                                border-radius: 8px !important;
                            }
                            .select2-container--default .select2-selection--multiple .select2-selection__rendered {
                                display: flex !important;
                                flex-wrap: wrap !important;
                                gap: 6px !important;
                                padding: 0 !important;
                            }
                            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                                background-color: #f3f4f6 !important;
                                border: 1px solid #e5e7eb !important;
                                border-radius: 6px !important;
                                padding: 4px 10px !important;
                                margin: 0 !important;
                                font-size: 13px !important;
                                color: #374151 !important;
                                display: inline-flex !important;
                                align-items: center !important;
                            }
                            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                                color: #ef4444 !important;
                                margin-right: 6px !important;
                                border: none !important;
                                background: transparent !important;
                            }
                            .select2-container--default .select2-selection--multiple .select2-search--inline {
                                width: 100% !important;
                                margin: 0 0 6px 0 !important;
                            }
                            .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
                                margin: 0 !important;
                                height: 32px !important;
                                font-size: 14px !important;
                            }
                        </style>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Logo Kegiatan</label>
                        <input type="file" name="logo" class="form-control">
                        <?php if ($row && !empty($row->logo)): ?>
                            <span class="text-xs text-secondary-light mt-1 d-block">Logo saat ini: <a href="<?php echo url('uploads/ekskul/' . $row->logo) ?>" target="_blank"><?php echo $row->logo ?></a></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi / Keterangan Singkat</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan tujuan atau keterangan program kegiatan..."><?php echo $row ? html_escape($row->keterangan) : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-1"><?php echo $row ? 'Periksa kembali data sebelum diperbarui' : 'Simpan informasi dasar ekstrakurikuler' ?></h6>
                    <p class="text-secondary-light mb-0">Anggota ekskul dan pengisian nilai evaluasi dapat diatur secara mandiri pada tombol kelola aksi list utama.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('ekstrakurikuler') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary-600 px-4">Simpan Konfigurasi</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<!-- Load Select2 JS setelah jQuery bawaan tema selesai di-load di footer -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Cek apakah fungsi select2 sudah terdefinisi secara aman
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "Pilih satu atau lebih guru pembina...",
                allowClear: true
            });
        }
    });
</script>
