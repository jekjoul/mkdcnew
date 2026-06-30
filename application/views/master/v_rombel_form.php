<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info-600">
                    <h6 class="text-light mb-0">Form Edit Rombongan Belajar</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('master/rombelUpdate/' . $row->id_rombel) ?>" method="post">
                        <div class="mb-20">
                            <label class="form-label fw-semibold text-sm mb-8">Nama Rombel <span class="text-danger-600">*</span></label>
                            <input type="text" class="form-control radius-8" name="nama_rombel" value="<?php echo $row->nama_rombel ?>" required>
                        </div>
                        <div class="mb-20">
                            <label class="form-label fw-semibold text-sm mb-8">Status</label>
                            <select class="form-control radius-8 form-select" name="status">
                                <option value="Aktif" <?php echo $row->status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?php echo $row->status == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?php echo url('master/rombel') ?>" class="btn btn-outline-secondary radius-8">Kembali</a>
                            <button type="submit" class="btn btn-success text-light radius-8">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>