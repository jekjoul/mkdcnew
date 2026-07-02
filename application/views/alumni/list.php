<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
            <h6 class="mb-0 text-light">Data Alumni</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Alumni</th>
                            <th class="text-center">NISN/NIPD</th>
                            <th>Rombel Terakhir</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($alumni as $row): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo html_escape($row->nama_siswa); ?></td>
                                <td class="text-center"><?php echo html_escape(($row->nisn ?: '-') . ' / ' . ($row->nipd ?: '-')); ?></td>
                                <td><?php echo html_escape((isset($row->rombel_terakhir) && $row->rombel_terakhir) ? $row->rombel_terakhir : ($row->rombel ?: '-')); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-warning-100 text-warning-700"><?php echo html_escape($row->status_alumni ?: $row->status_keaktifan ?: '-'); ?></span>
                                </td>
                                <td class="text-center"><?php echo !empty($row->tanggal_alumni) ? tanggal_indo($row->tanggal_alumni) : '-'; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-10 justify-content-center">
                                        <a href="<?php echo url('alumni/detail/' . $row->id_alumni); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <iconify-icon icon="lucide:eye"></iconify-icon>
                                        </a>
                                        <?php if (empty($row->id_siswa_kembali)): ?>
                                            <a href="<?php echo url('alumni/detail/' . $row->id_alumni); ?>" class="w-32-px h-32-px bg-success-100 text-success-600 rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Kembalikan Jadi Siswa">
                                                <iconify-icon icon="solar:user-check-linear"></iconify-icon>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo url('siswa/detail/' . $row->id_siswa_kembali); ?>" class="w-32-px h-32-px bg-success-100 text-success-600 rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Siswa Aktif">
                                                <iconify-icon icon="solar:user-linear"></iconify-icon>
                                            </a>
                                        <?php endif; ?>
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

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
</script>
