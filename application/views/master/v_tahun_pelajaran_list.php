<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6>Master Tahun Pelajaran</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('master/tahunPelajaranTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Semester</th>
                                    <th>Hari Efektif</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($tahun_pelajaran as $row): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++ ?></td>
                                        <td><?php echo html_escape($row->tahun_pelajaran) ?></td>
                                        <td><?php echo html_escape($row->semester) ?></td>
                                        <td>
                                            <?php if ($row->hari_efektif->total > 0): ?>
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <span class="badge bg-primary-100 text-primary-600"><?php echo $row->hari_efektif->total ?> Hari</span>
                                                    <span class="badge bg-success-100 text-success-600">Efektif <?php echo $row->hari_efektif->efektif ?></span>
                                                    <span class="badge bg-danger-100 text-danger-600">Libur <?php echo $row->hari_efektif->libur ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-secondary-light text-sm">Belum digenerate</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $row->status == 'Aktif' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600'; ?>">
                                                <?php echo html_escape($row->status); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <?php if ($row->hari_efektif->total > 0): ?>
                                                    <a href="<?php echo url('tahun_pelajaran/hari_efektif/' . $row->id_tahun_pelajaran) ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" title="Hari Efektif">
                                                        <iconify-icon icon="solar:calendar-mark-linear" class="menu-icon"></iconify-icon>
                                                    </a>
                                                <?php elseif ($row->status == 'Aktif'): ?>
                                                    <button type="button" class="border-0 bg-warning-100 text-warning-600 bg-hover-warning-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#generateHariEfektif<?php echo $row->id_tahun_pelajaran ?>" title="Generate Hari Efektif">
                                                        <iconify-icon icon="solar:calendar-add-linear" class="menu-icon"></iconify-icon>
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?php echo url('master/tahunPelajaranEdit/' . $row->id_tahun_pelajaran) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" title="Edit">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/tahunPelajaranDelete/' . $row->id_tahun_pelajaran) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                                    <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach ($tahun_pelajaran as $row): ?>
    <?php if ($row->hari_efektif->total == 0 && $row->status == 'Aktif'): ?>
        <div class="modal fade" id="generateHariEfektif<?php echo $row->id_tahun_pelajaran ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-8">
                    <form action="<?php echo url('tahun_pelajaran/generate_hari_efektif/' . $row->id_tahun_pelajaran) ?>" method="post">
                        <div class="modal-header">
                            <h6 class="modal-title">Generate Hari Efektif</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-16">
                                <label class="form-label fw-semibold text-sm mb-8">Tahun Pelajaran</label>
                                <div class="form-control bg-neutral-100">
                                    <?php echo html_escape($row->tahun_pelajaran . ' - ' . $row->semester) ?>
                                </div>
                            </div>
                            <div class="mb-16">
                                <label class="form-label fw-semibold text-sm mb-8">Periode Semester</label>
                                <div class="form-control bg-neutral-100">
                                    <?php echo date('d/m/Y', strtotime($row->periode_hari_efektif['awal'])) ?> - <?php echo date('d/m/Y', strtotime($row->periode_hari_efektif['akhir'])) ?>
                                </div>
                            </div>
                            <label class="form-label fw-semibold text-sm mb-8">Hari Libur Mingguan</label>
                            <div class="row gy-2">
                                <?php
                                $hari_opsi = [
                                    0 => 'Minggu',
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                ];
                                foreach ($hari_opsi as $day_number => $day_name):
                                ?>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="hari_libur[]" value="<?php echo $day_number ?>" id="hariLibur<?php echo $row->id_tahun_pelajaran . $day_number ?>" <?php echo $day_number === 0 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="hariLibur<?php echo $row->id_tahun_pelajaran . $day_number ?>">
                                                <?php echo $day_name ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary-light radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
