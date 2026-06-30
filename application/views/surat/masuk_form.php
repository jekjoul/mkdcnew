<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/masuk_simpan') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_surat_masuk" value="<?php echo @$row->id_surat_masuk ?>">
        <div class="card">
            <div class="card-header bg-warning-900">
                <h6 class="mb-0 text-light"><?php echo $row ? 'Edit Surat Masuk' : 'Tambah Surat Masuk' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" value="<?php echo @$row->tanggal_surat ?: date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pengirim</label>
                        <input type="text" name="pengirim" class="form-control" value="<?php echo @$row->pengirim ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tujuan Surat</label>
                        <input type="text" name="tujuan_surat" class="form-control" value="<?php echo @$row->tujuan_surat ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control" value="<?php echo @$row->nomor_surat ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Perihal</label>
                        <input type="text" name="perihal" class="form-control" value="<?php echo @$row->perihal ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Disposisi</label>
                        <select name="status_disposisi" class="form-select">
                            <?php foreach (['Belum Disposisi', 'Proses', 'Selesai'] as $status): ?>
                                <option value="<?php echo $status ?>" <?php echo @$row->status_disposisi == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Catatan Disposisi</label>
                        <textarea name="catatan_disposisi" class="form-control" rows="3"><?php echo @$row->catatan_disposisi ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Scan/Foto Surat</label>
                        <input type="file" name="scan_surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <?php if (!empty($row->scan_file)): ?>
                            <a href="<?php echo url('uploads/surat_masuk/' . $row->scan_file) ?>" target="_blank" class="text-primary-600 text-sm d-inline-block mt-2">Lihat berkas saat ini</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="<?php echo url('surat/masuk') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
