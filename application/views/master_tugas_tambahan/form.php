<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-neutral-300">
                    <h6 class="text-dark mb-0"><?php echo $row ? 'Edit' : 'Tambah'; ?> Master Tugas Tambahan</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('master_tugas_tambahan/simpan'); ?>" method="post">
                        <input type="hidden" name="id" value="<?php echo $row ? $row->id : ''; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Jenis Tugas Tambahan <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-select" required>
                                    <option value="" disabled <?php echo !$row ? 'selected' : ''; ?>>-- Pilih Jenis --</option>
                                    <option value="Ekstrakurikuler" <?php echo ($row && $row->jenis == 'Ekstrakurikuler') ? 'selected' : ''; ?>>Ekstrakurikuler</option>
                                    <option value="Kokurikuler" <?php echo ($row && $row->jenis == 'Kokurikuler') ? 'selected' : ''; ?>>Kokurikuler</option>
                                    <option value="Kepanitiaan" <?php echo ($row && $row->jenis == 'Kepanitiaan') ? 'selected' : ''; ?>>Kepanitiaan</option>
                                    <option value="Struktural" <?php echo ($row && $row->jenis == 'Struktural') ? 'selected' : ''; ?>>Struktural</option>
                                    <option value="Penunjang" <?php echo ($row && $row->jenis == 'Penunjang') ? 'selected' : ''; ?>>Penunjang</option>
                                    <option value="Lainnya" <?php echo ($row && $row->jenis == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Nama Tugas Tambahan <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="<?php echo $row ? html_escape($row->nama) : ''; ?>" required placeholder="Contoh: Pembina Pramuka / Wakasek Kesiswaan">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-20">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi tugas tambahan (opsional)"><?php echo $row ? html_escape($row->deskripsi) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                            <a href="<?php echo url('master_tugas_tambahan'); ?>" class="btn btn-secondary-light radius-8 px-20 py-11">Batal</a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
