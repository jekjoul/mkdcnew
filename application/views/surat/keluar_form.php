<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/keluar_simpan') ?>" method="post">
        <input type="hidden" name="id_surat_keluar" value="<?php echo @$row->id_surat_keluar ?>">
        <input type="hidden" name="token_validasi" value="<?php echo @$row->token_validasi ?>">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light">Buat Surat Keluar</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Surat</label>
                        <select name="id_kode_surat" id="kodeSelect" class="form-select" required>
                            <option value="">Pilih kode surat</option>
                            <?php foreach ($kode as $k): ?>
                                <option value="<?php echo $k->id_kode_surat ?>" <?php echo @$row->id_kode_surat == $k->id_kode_surat ? 'selected' : '' ?>>
                                    <?php echo $k->nama_lembaga ?> - <?php echo $k->kode_jenis ?> - <?php echo $k->nama_jenis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Template</label>
                        <select name="id_template_surat" id="templateSelect" class="form-select">
                            <option value="">Tanpa template</option>
                            <?php foreach ($template as $t): ?>
                                <option value="<?php echo $t->id_template_surat ?>" data-kode="<?php echo $t->id_kode_surat ?>" <?php echo @$row->id_template_surat == $t->id_template_surat ? 'selected' : '' ?>><?php echo $t->nama_template ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" value="<?php echo @$row->tanggal_surat ?: date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nomor Urut</label>
                        <input type="number" min="1" name="nomor_urut" class="form-control" value="<?php echo @$row->nomor_urut ?>" placeholder="Otomatis jika kosong">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Nomor Custom</label>
                        <input type="text" name="nomor_custom" class="form-control" value="<?php echo @$row->nomor_custom ?>" placeholder="Kosongkan untuk nomor otomatis">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['Draft', 'Final', 'Terkirim'] as $status): ?>
                                <option value="<?php echo $status ?>" <?php echo @$row->status == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tujuan Surat</label>
                        <input type="text" name="tujuan_surat" class="form-control" value="<?php echo @$row->tujuan_surat ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Perihal</label>
                        <input type="text" name="perihal" id="perihalInput" class="form-control" value="<?php echo @$row->perihal ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Isi Surat</label>
                        <textarea name="isi_surat" id="isiSurat" class="form-control" rows="16" required><?php echo @$row->isi_surat ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Penandatangan</label>
                        <input type="text" name="penandatangan_nama" class="form-control" value="<?php echo @$row->penandatangan_nama ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan Penandatangan</label>
                        <input type="text" name="penandatangan_jabatan" class="form-control" value="<?php echo @$row->penandatangan_jabatan ?>">
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
<script>
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

    $('#kodeSelect').on('change', filterTemplateByKode);

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

    filterTemplateByKode();
</script>
