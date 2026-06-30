<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light mb-0"><?php echo $row ? 'Edit' : 'Tambah'; ?> Mata Pelajaran</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('master/mapelSimpan'); ?>" method="post">
                        <input type="hidden" name="id_mapel" value="<?php echo $row ? $row->id_mapel : ''; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                <input type="text" name="nama_mapel" class="form-control" value="<?php echo $row ? $row->nama_mapel : ''; ?>" required placeholder="Contoh: Matematika">
                            </div>
                            <div class="col-md-3 mb-20">
                                <label class="form-label fw-semibold">Singkatan</label>
                                <input type="text" name="mapel_singkat" class="form-control" value="<?php echo $row ? $row->mapel_singkat : ''; ?>" placeholder="Contoh: MTK">
                            </div>
                            <div class="col-md-3 mb-20">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Aktif" <?php echo ($row && $row->status == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Tidak Aktif" <?php echo ($row && $row->status == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                            <a href="<?php echo url('master/mapel'); ?>" class="btn btn-secondary-light radius-8 px-20 py-11">Batal</a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
