<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-success-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Input Nilai & Deskripsi Evaluasi Ekskul: <?php echo html_escape($ekskul->nama_ekskul) ?></h6>
            <a href="<?php echo url('ekstrakurikuler') ?>" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <form action="<?php echo url('ekstrakurikuler/update_nilai/' . $ekskul->id_ekskul) ?>" method="post">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 25%">Nama Siswa</th>
                                <th style="width: 15%">Rombel</th>
                                <th style="width: 20%">Nilai Predikat</th>
                                <th style="width: 30%">Deskripsi Evaluasi Pembina</th>
                                <th class="text-center" style="width: 5%">Status Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($peserta as $p): ?>
                                <tr class="<?php echo $p->status_keaktifan === 'Alumni' ? 'bg-secondary-50 opacity-75' : '' ?>">
                                    <td><?php echo $no++ ?></td>
                                    <td class="fw-semibold">
                                        <?php echo html_escape($p->nama_siswa) ?>
                                        <input type="hidden" name="id_ekskul_siswa[]" value="<?php echo $p->id_ekskul_siswa ?>">
                                    </td>
                                    <td><?php echo html_escape($p->rombel ?: '-') ?></td>
                                    <td>
                                        <select name="nilai[]" class="form-control form-select" required>
                                            <option value="Sangat Baik" <?php echo $p->nilai == 'Sangat Baik' ? 'selected' : '' ?>>Sangat Baik</option>
                                            <option value="Baik" <?php echo $p->nilai == 'Baik' || empty($p->nilai) ? 'selected' : '' ?>>Baik</option>
                                            <option value="Cukup" <?php echo $p->nilai == 'Cukup' ? 'selected' : '' ?>>Cukup</option>
                                            <option value="Kurang" <?php echo $p->nilai == 'Kurang' ? 'selected' : '' ?>>Kurang</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="catatan[]" class="form-control" value="<?php echo html_escape($p->catatan ?: 'Menunjukkan perkembangan minat dan keaktifan yang baik dalam kegiatan ekstrakurikuler.') ?>" required placeholder="Contoh: Sangat aktif dalam kegiatan latihan berkala..." style="width: 100%;">
                                    </td>
                                    <td class="text-center">
                                        <?php if ($p->status_keaktifan === 'Alumni'): ?>
                                            <span class="badge bg-secondary-100 text-secondary-800">Alumni (Arsip)</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-100 text-success-800">Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($peserta)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada siswa yang bergabung di ekstrakurikuler ini. Silakan atur anggota terlebih dahulu.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(!empty($peserta)): ?>
                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="<?php echo url('ekstrakurikuler') ?>" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-success text-light px-24">Simpan Nilai & Deskripsi</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
