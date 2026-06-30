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
                    <div class="col-md-6">
                        <label class="form-label">Kode Surat</label>
                        <select name="id_kode_surat" class="form-select" required>
                            <option value="">Pilih kode surat</option>
                            <?php foreach ($kode as $k): ?>
                                <option value="<?php echo $k->id_kode_surat ?>" <?php echo @$row->id_kode_surat == $k->id_kode_surat ? 'selected' : '' ?>>
                                    <?php echo $k->nama_lembaga ?> - <?php echo $k->kode_jenis ?> - <?php echo $k->nama_jenis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Template</label>
                        <input type="text" name="nama_template" class="form-control" value="<?php echo @$row->nama_template ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?php echo @$row->status != 'Nonaktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?php echo @$row->status == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Perihal Default</label>
                        <input type="text" name="perihal_default" class="form-control" value="<?php echo @$row->perihal_default ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Isi Template</label>
                        <textarea name="isi_template" class="form-control" rows="14" required><?php echo @$row->isi_template ?></textarea>
                        <small class="text-secondary-light">Placeholder: {{nomor_surat}}, {{tanggal_surat}}, {{tujuan_surat}}, {{perihal}}, {{nama_lembaga}}, {{tahun}}, {{validasi_url}}</small>
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
