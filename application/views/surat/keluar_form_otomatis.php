<!-- Load Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/keluar_simpan') ?>" method="post" id="formSuratOtomatis">
        <input type="hidden" name="id_surat_keluar" value="<?php echo @$row->id_surat_keluar ?>">
        <input type="hidden" name="token_validasi" value="<?php echo @$row->token_validasi ?>">
        <input type="hidden" name="metode_pembuatan" value="Otomatis">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light"><?php echo @$row->id_surat_keluar ? 'Edit Surat Keluar Otomatis' : 'Buat Surat Keluar Otomatis' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Pilih Kop Surat <span class="text-danger">*</span></label>
                        <select name="id_kop_surat" class="form-select" required>
                            <option value="">Pilih Kop Surat</option>
                            <?php foreach ($kop_list as $kp): ?>
                                <option value="<?php echo $kp->id_kop_surat ?>" <?php echo @$row->id_kop_surat == $kp->id_kop_surat ? 'selected' : '' ?>>
                                    <?php echo $kp->nama_kop ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Kategori/Kode Surat <span class="text-danger">*</span></label>
                        <select name="id_kode_surat" id="kodeSelect" class="form-select" required>
                            <option value="">Pilih Kode/Kategori Surat</option>
                            <?php foreach ($kode as $k): ?>
                                <option value="<?php echo $k->id_kode_surat ?>" <?php echo @$row->id_kode_surat == $k->id_kode_surat ? 'selected' : '' ?>>
                                    <?php echo $k->nama_lembaga ?> - <?php echo $k->kode_jenis ?> - <?php echo $k->nama_jenis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Pilih Template Surat</label>
                        <select name="id_template_surat" id="templateSelect" class="form-select">
                            <option value="">Tanpa template (Ketik manual)</option>
                            <?php foreach ($template as $t): ?>
                                <option value="<?php echo $t->id_template_surat ?>" data-kode="<?php echo $t->id_kode_surat ?>" <?php echo @$row->id_template_surat == $t->id_template_surat ? 'selected' : '' ?>><?php echo $t->nama_template ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggalSurat" class="form-control" value="<?php echo @$row->tanggal_surat ?: date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nomor Surat (Otomatis dari Sistem)</label>
                        <input type="text" id="nomorSuratDisplay" class="form-control bg-light" value="<?php echo @$row->nomor_surat ?>" readonly placeholder="Nomor otomatis terisi ketika kode dipilih">
                        <input type="hidden" name="nomor_surat" id="nomorSuratHidden" value="<?php echo @$row->nomor_surat ?>">
                        <input type="hidden" name="nomor_urut" id="nomorUrutHidden" value="<?php echo @$row->nomor_urut ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nomor Custom (Manual)</label>
                        <input type="text" name="nomor_custom" class="form-control" value="<?php echo @$row->nomor_custom ?>" placeholder="Isi untuk override nomor sistem">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status Surat</label>
                        <select name="status" class="form-select">
                            <?php foreach (['Draft', 'Final', 'Terkirim'] as $status): ?>
                                <option value="<?php echo $status ?>" <?php echo @$row->status == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tujuan Surat <span class="text-danger">*</span></label>
                        <input type="text" name="tujuan_surat" class="form-control" value="<?php echo @$row->tujuan_surat ?>" required placeholder="Contoh: Orang Tua Siswa / Kepala Dinas">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Perihal Surat <span class="text-danger">*</span></label>
                        <input type="text" name="perihal" id="perihalInput" class="form-control" value="<?php echo @$row->perihal ?>" required placeholder="Contoh: Undangan Rapat / Surat Pemberitahuan">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Isi Surat <span class="text-danger">*</span></label>
                        <textarea name="isi_surat" id="isiSurat" class="form-control" rows="16" required><?php echo @$row->isi_surat ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold d-block mb-8">Pejabat Penandatangan <span class="text-danger">*</span> <small class="text-muted">(Pilih satu atau lebih pejabat)</small></label>
                        <select name="id_ptk_penandatangan[]" id="penandatanganSelect" class="form-control select2" multiple required data-placeholder="-- Pilih Pejabat Penandatangan --" style="width: 100%;">
                            <?php foreach ($ptk as $p): 
                                $isSelected = in_array($p->id_ptk, $selected_penandatangan);
                                $jabatanVal = isset($penandatangan_jabatan_map[$p->id_ptk]) ? $penandatangan_jabatan_map[$p->id_ptk] : '';
                            ?>
                                <option value="<?php echo $p->id_ptk ?>" data-nama="<?php echo htmlspecialchars($p->nama_ptk) ?>" data-jabatan="<?php echo htmlspecialchars($jabatanVal) ?>" <?php echo $isSelected ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($p->nama_ptk) ?> (NIK/NIY: <?php echo $p->nik ?: ($p->niy ?: '-') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12" id="wrapperFormJabatan" style="display: none;">
                        <label class="form-label fw-semibold d-block mb-8 text-primary-600">Jabatan Pejabat Penandatangan Terpilih <span class="text-danger">*</span></label>
                        <div class="row g-3 p-16 bg-light radius-8 border" id="containerFormJabatan">
                            <!-- Form Jabatan akan dipopulasikan otomatis via JS -->
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

<!-- Load Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#penandatanganSelect').select2({
                placeholder: "-- Pilih Pejabat Penandatangan --",
                allowClear: true
            });
        }

        function renderJabatanForms() {
            const selectedVals = $('#penandatanganSelect').val() || [];
            const container = $('#containerFormJabatan');
            const wrapper = $('#wrapperFormJabatan');

            if (selectedVals.length === 0) {
                container.empty();
                wrapper.hide();
                return;
            }

            wrapper.show();

            const existingValues = {};
            container.find('.input-jabatan-field').each(function() {
                const ptkId = $(this).data('ptk-id');
                existingValues[ptkId] = $(this).val();
            });

            container.empty();

            selectedVals.forEach(function(ptkId) {
                const option = $('#penandatanganSelect option[value="' + ptkId + '"]');
                const namaPtk = option.data('nama') || 'Pejabat';
                const defaultJabatan = option.data('jabatan') || '';
                const currentVal = (existingValues[ptkId] !== undefined) ? existingValues[ptkId] : defaultJabatan;

                const html = `
                    <div class="col-md-6 item-jabatan-group" data-ptk-id="${ptkId}">
                        <div class="p-12 border radius-8 bg-white shadow-xs">
                            <label class="form-label fw-semibold text-primary-600 mb-1">
                                Jabatan untuk: <strong>${namaPtk}</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="jabatan_penandatangan[${ptkId}]" 
                                   class="form-control input-jabatan-field" 
                                   data-ptk-id="${ptkId}" 
                                   placeholder="Ketik Jabatan (misal: Kepala Sekolah / Bendahara)" 
                                   value="${currentVal}" 
                                   required>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        $('#penandatanganSelect').on('change', function() {
            renderJabatanForms();
        });

        renderJabatanForms();

        function filterTemplateByKode() {
            const kode = $('#kodeSelect').val();
            $('#templateSelect option').each(function() {
                const optionKode = $(this).data('kode');
                const show = !optionKode || !kode || String(optionKode) === String(kode);
                $(this).toggle(show);
            });

            const selected = $('#templateSelect option:selected');
            if (selected.data('kode') && String(selected.data('kode')) !== String(kode)) {
                $('#templateSelect').val('');
            }
        }

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

        $('#kodeSelect').on('change', function() {
            filterTemplateByKode();
            generateNomorSurat();
        });

        $('#tanggalSurat').on('change', generateNomorSurat);

        $('#templateSelect').on('change', function() {
            const id = $(this).val();
            if (!id) return;
            $.getJSON('<?php echo url('surat/template_json') ?>/' + id, function(row) {
                if (row.perihal_default && !$('#perihalInput').val()) {
                    $('#perihalInput').val(row.perihal_default);
                }
                if (row.isi_template) {
                    $('#isiSurat').val(row.isi_template);
                }
            });
        });

        if ($('#kodeSelect').val()) {
            filterTemplateByKode();
            <?php if (empty($row->nomor_surat)): ?>
                generateNomorSurat();
            <?php endif; ?>
        }

        $('#formSuratOtomatis').on('submit', function(e) {
            const selectedVals = $('#penandatanganSelect').val() || [];
            if (selectedVals.length === 0) {
                e.preventDefault();
                alert('Peringatan: Silakan pilih minimal 1 Pejabat Penandatangan!');
            }
        });
    });
</script>
