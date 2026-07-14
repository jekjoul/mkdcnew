<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-danger-600 text-white">
                    <h6 class="text-light mb-0">Daftar Pelanggaran & Poin Kedisiplinan Siswa</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="<?php echo url('kedisiplinan/kategori') ?>" class="btn btn-sm btn-dark text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:settings-linear" class="text-lg"></iconify-icon> Atur Kategori Poin
                        </a>
                        <a href="<?php echo url('kedisiplinan/tambah') ?>" class="btn btn-sm btn-primary text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon> Input Pelanggaran
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col">Pelanggaran</th>
                                    <th scope="col" class="text-center">Bobot Poin</th>
                                    <th scope="col" class="text-center">Tanggal</th>
                                    <th scope="col">Catatan</th>
                                    <th scope="col">Tindak Lanjut BK</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($pelanggaran)): ?>
                                    <?php foreach ($pelanggaran as $p): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td class="fw-semibold text-secondary-light"><?php echo html_escape($p->nama_siswa); ?></td>
                                            <td><?php echo html_escape($p->rombel ?: '-'); ?></td>
                                            <td><span class="badge bg-danger-100 text-danger-800"><?php echo html_escape($p->nama_pelanggaran); ?></span></td>
                                            <td class="text-center"><span class="badge bg-danger-600 text-light px-12 py-6"><?php echo (int) $p->bobot_poin; ?> Poin</span></td>
                                            <td class="text-center"><?php echo date('d-m-Y', strtotime($p->tanggal_pelanggaran)); ?></td>
                                            <td><?php echo html_escape($p->catatan ?: '-'); ?></td>
                                            <td>
                                                <span class="fw-medium text-warning-main"><?php echo html_escape($p->tindak_lanjut ?: '-'); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                                    <button class="btn btn-sm btn-info-100 text-info-600" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut<?php echo $p->id_pelanggaran_siswa ?>">
                                                        BK
                                                    </button>
                                                    <a href="<?php echo url('kedisiplinan/hapus/' . $p->id_pelanggaran_siswa); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="return confirm('Hapus laporan pelanggaran ini?')" title="Hapus">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                    </a>
                                                </div>

                                                <!-- Modal Tindak Lanjut BK -->
                                                <div class="modal fade" id="modalTindakLanjut<?php echo $p->id_pelanggaran_siswa ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content bg-base">
                                                            <form action="<?php echo url('kedisiplinan/edit_tindak_lanjut/' . $p->id_pelanggaran_siswa) ?>" method="post">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Tindak Lanjut / Konseling Guru BK</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <p>Siswa: <strong><?php echo html_escape($p->nama_siswa) ?></strong></p>
                                                                    <p>Pelanggaran: <span class="badge bg-danger-100 text-danger-800"><?php echo html_escape($p->nama_pelanggaran) ?></span></p>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Tindak Lanjut & Keputusan Konseling BK</label>
                                                                        <textarea name="tindak_lanjut" class="form-control" rows="4" required><?php echo html_escape($p->tindak_lanjut) ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success text-light">Simpan Keputusan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
