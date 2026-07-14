<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <!-- Panel Tambah Peserta -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary-600 text-white">
                    <h6 class="text-light mb-0">Tambah Peserta Ekskul</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('ekstrakurikuler/tambah_peserta/' . $ekskul->id_ekskul) ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Siswa Aktif <span class="text-danger">*</span></label>
                            <select name="id_siswa" class="form-control select2" required data-placeholder="Cari nama siswa...">
                                <option value=""></option>
                                <?php foreach($calon_peserta as $cp): ?>
                                    <option value="<?php echo $cp->id_siswa ?>">
                                        <?php echo html_escape($cp->nama_siswa) ?> - Rombel <?php echo html_escape($cp->rombel ?: '-') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary text-light w-100 radius-8">Tambahkan ke Anggota</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Anggota & Penilaian -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success-600 text-white d-flex justify-content-between">
                    <h6 class="text-light mb-0">Evaluasi Nilai Anggota Ekskul</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('ekstrakurikuler/update_nilai/' . $ekskul->id_ekskul) ?>" method="post">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 30%">Nama Siswa</th>
                                        <th style="width: 15%">Rombel</th>
                                        <th style="width: 15%">Nilai Predikat</th>
                                        <th style="width: 30%">Evaluasi Catatan Pembina</th>
                                        <th class="text-center" style="width: 5%">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($peserta as $p): ?>
                                        <tr>
                                            <td><?php echo $no++ ?></td>
                                            <td class="fw-semibold text-secondary-light">
                                                <?php echo html_escape($p->nama_siswa) ?>
                                                <input type="hidden" name="id_ekskul_siswa[]" value="<?php echo $p->id_ekskul_siswa ?>">
                                            </td>
                                            <td><?php echo html_escape($p->rombel ?: '-') ?></td>
                                            <td>
                                                <select name="nilai[]" class="form-control form-select" required>
                                                    <option value="A" <?php echo $p->nilai == 'A' ? 'selected' : '' ?>>A (Sangat Baik)</option>
                                                    <option value="B" <?php echo $p->nilai == 'B' ? 'selected' : '' ?>>B (Baik)</option>
                                                    <option value="C" <?php echo $p->nilai == 'C' ? 'selected' : '' ?>>C (Cukup)</option>
                                                    <option value="D" <?php echo $p->nilai == 'D' ? 'selected' : '' ?>>D (Kurang)</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="catatan[]" class="form-control" value="<?php echo html_escape($p->catatan) ?>" placeholder="Tuliskan perkembangan siswa...">
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo url('ekstrakurikuler/hapus_peserta/' . $p->id_ekskul_siswa . '/' . $ekskul->id_ekskul) ?>" class="text-danger" onclick="return confirm('Keluarkan siswa dari ekskul?')">
                                                    <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($peserta)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-secondary py-4">Belum ada siswa yang bergabung di ekskul ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if(!empty($peserta)): ?>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-success text-light px-24">Simpan Nilai & Evaluasi</button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
