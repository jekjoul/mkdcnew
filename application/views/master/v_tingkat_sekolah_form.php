<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-neutral-300">
                    <h6 class="text-dark mb-0"><?php echo $row ? 'Edit' : 'Tambah'; ?> Tingkat Sekolah</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('master/tingkatSekolahSimpan'); ?>" method="post">
                        <input type="hidden" name="id_tingkat_sekolah" value="<?php echo $row ? $row->id_tingkat_sekolah : ''; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Nama Tingkat <span class="text-danger">*</span></label>
                                <input type="text" name="nama_tingkat" class="form-control" value="<?php echo $row ? $row->nama_tingkat : ''; ?>" required placeholder="Contoh: VII">
                            </div>
                            <div class="col-md-3 mb-20">
                                <label class="form-label fw-semibold">Tingkat Angka <span class="text-danger">*</span></label>
                                <input type="number" name="tingkat_angka" class="form-control" value="<?php echo $row ? $row->tingkat_angka : ''; ?>" required placeholder="Contoh: 7">
                            </div>
                            <div class="col-md-3 mb-20">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Aktif" <?php echo ($row && $row->status == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Nonaktif" <?php echo ($row && $row->status == 'Nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                            <a href="<?php echo url('master/tingkatSekolah'); ?>" class="btn btn-secondary-light radius-8 px-20 py-11">Batal</a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>