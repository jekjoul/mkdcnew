<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('pembelajaran/simpan'); ?>" method="post">
        <input type="hidden" name="id_tahun_pelajaran" value="<?php echo $ta_aktif->id_tahun_pelajaran; ?>">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="text-light mb-0">Konfigurasi Rombongan Belajar (<?php echo $ta_aktif->tahun_pelajaran; ?>)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Lembaga</label>
                        <select name="id_lembaga" class="form-select" required>
                            <?php foreach ($lembaga as $l): ?>
                                <option value="<?php echo $l->id_lembaga ?>"><?php echo $l->nama_lembaga ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Tingkat</label>
                        <select name="id_tingkat_sekolah" class="form-select" required>
                            <?php foreach ($tingkat as $t): ?>
                                <option value="<?php echo $t->id_tingkat_sekolah ?>"><?php echo $t->nama_tingkat ?> (<?php echo $t->tingkat_angka ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Rombel</label>
                        <select name="id_rombel" class="form-select" required>
                            <?php foreach ($rombel as $r): ?>
                                <option value="<?php echo $r->id_rombel ?>"><?php echo $r->nama_rombel ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-1">Simpan rombongan belajar terlebih dahulu</h6>
                    <p class="text-secondary-light mb-0">Mapel dan daftar siswa ditambahkan dari tombol aksi pada list pembelajaran.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('pembelajaran') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary-600 px-4">Simpan Pembelajaran</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
