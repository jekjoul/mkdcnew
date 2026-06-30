<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/kode_simpan') ?>" method="post">
        <input type="hidden" name="id_kode_surat" value="<?php echo @$row->id_kode_surat ?>">
        <div class="card">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light">Form Kode Surat</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label class="form-label">Lembaga</label>
                        <select name="id_lembaga" class="form-select" required>
                            <option value="">Pilih Lembaga</option>
                            <?php foreach ($lembaga as $l): ?>
                                <option value="<?php echo $l->id_lembaga ?>" <?php echo @$row->id_lembaga == $l->id_lembaga ? 'selected' : '' ?>><?php echo $l->nama_lembaga ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kode Jenis</label>
                        <input type="text" name="kode_jenis" class="form-control" value="<?php echo @$row->kode_jenis ?>" placeholder="400.3.12.1 / 01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Jenis Surat</label>
                        <input type="text" name="nama_jenis" class="form-control" value="<?php echo @$row->nama_jenis ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode Lembaga</label>
                        <input type="text" name="kode_lembaga" class="form-control" value="<?php echo @$row->kode_lembaga ?>" placeholder="SMPMK / YMK / SMAMK" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="<?php echo @$row->lokasi ?: 'PANJALU' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Format Nomor</label>
                        <input type="text" name="format_nomor" class="form-control" value="<?php echo @$row->format_nomor ?: '{kode_jenis}/{nomor}-{kode_lembaga}/{lokasi}/{tahun}' ?>" required>
                        <small class="text-secondary-light">Variabel: {kode_jenis}, {nomor}, {kode_lembaga}, {lokasi}, {tahun}</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?php echo @$row->status != 'Nonaktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?php echo @$row->status == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?php echo url('surat/kode') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
