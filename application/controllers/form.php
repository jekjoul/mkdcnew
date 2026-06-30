<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <form action="<?php echo url('pembelajaran/simpan'); ?>" method="post">
        <input type="hidden" name="id_tahun_pelajaran" value="<?php echo $ta_aktif->id_tahun_pelajaran; ?>">

        <div class="card mb-4">
            <div class="card-header bg-neutral-300">
                <h6 class="text-dark mb-0">Konfigurasi Rombongan Belajar (<?php echo $ta_aktif->tahun_pelajaran; ?>)</h6>
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

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-neutral-100">
                        <h6 class="mb-0">Mata Pelajaran</h6>
                    </div>
                    <div class="card-body scroll-sm" style="max-height: 400px; overflow-y: auto;">
                        <div class="list-group">
                            <?php foreach ($mapel as $m): ?>
                                <label class="list-group-item d-flex gap-2">
                                    <input class="form-check-input flex-shrink-0" type="checkbox" name="mapel[]" value="<?php echo $m->id_mapel ?>">
                                    <span><?php echo $m->nama_mapel ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-neutral-100">
                        <h6 class="mb-0">Daftar Siswa</h6>
                    </div>
                    <div class="card-body scroll-sm" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th width="30">#</th>
                                    <th>Nama Siswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($siswa as $s): ?>
                                    <tr>
                                        <td><input type="checkbox" name="siswa[]" value="<?php echo $s->peserta_didik_id ?>"></td>
                                        <td><?php echo $s->nama ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo url('pembelajaran') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Pembelajaran</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>