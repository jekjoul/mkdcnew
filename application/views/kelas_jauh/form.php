<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <form action="<?php echo $form_action; ?>" method="post">
        <input type="hidden" name="id_kelas_jauh" value="<?php echo $row ? $row->id_kelas_jauh : ''; ?>">
        <div class="card mb-4">
            <div class="card-header bg-info-900">
                <h6 class="text-light mb-0"><?php echo $row ? 'Edit Kelas Jauh' : 'Tambah Kelas Jauh Baru' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Kelas Jauh <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas_jauh" class="form-control" value="<?php echo $row ? html_escape($row->nama_kelas_jauh) : '' ?>" required placeholder="Contoh: Kelas Jauh Ciakar, Kelas Menginduk Sukamaju">
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
                    <div class="col-12">
                        <label class="form-label fw-bold">Keterangan / Deskripsi</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan keterangan detail mengenai lokasi atau koordinat kelas jauh..."><?php echo $row ? html_escape($row->keterangan) : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Simpan Kelas Jauh</h6>
                    <p class="text-sm text-secondary-light">Pastikan semua data bertanda bintang (*) telah diisi dengan benar.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('kelas_jauh') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary text-light px-24">Simpan Data</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include viewPath('includes/footer'); ?>
