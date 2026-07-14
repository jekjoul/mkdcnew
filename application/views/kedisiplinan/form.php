<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-danger-600 text-white">
                    <h6 class="text-light mb-0">Form Input Pelanggaran Siswa Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('kedisiplinan/simpan') ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Siswa Pelanggar <span class="text-danger">*</span></label>
                            <select name="id_siswa" class="form-control select2" required data-placeholder="Cari siswa berdasarkan nama...">
                                <option value=""></option>
                                <?php foreach($siswa as $s): ?>
                                    <option value="<?php echo $s->id_siswa ?>">
                                        <?php echo html_escape($s->nama_siswa) ?> - Rombel <?php echo html_escape($s->rombel ?: '-') ?> (NISN: <?php echo html_escape($s->nisn) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Jenis / Kategori Pelanggaran <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-control select2" required data-placeholder="Pilih jenis pelanggaran...">
                                <option value=""></option>
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?php echo $k->id_kategori ?>">
                                        <?php echo html_escape($k->nama_pelanggaran) ?> (Bobot: <?php echo $k->bobot_poin ?> Poin)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pelanggaran</label>
                            <input type="date" name="tanggal_pelanggaran" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Kronologi / Detail Pelanggaran</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Sebutkan detail kejadian secara kronologis..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rencana Tindak Lanjut Awal</label>
                            <textarea name="tindak_lanjut" class="form-control" rows="2" placeholder="Contoh: Pemanggilan orang tua siswa / Konseling I..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <a href="<?php echo url('kedisiplinan') ?>" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-danger text-light px-24">Simpan Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
