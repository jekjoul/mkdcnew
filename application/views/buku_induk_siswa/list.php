<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
            <h6 class="mb-0 text-light">Buku Induk Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">NISN/NIPD</th>
                            <th>Rombel</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($siswa as $row): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo html_escape($row->nama_siswa); ?></td>
                                <td class="text-center"><?php echo html_escape(($row->nisn ?: '-') . ' / ' . ($row->nipd ?: '-')); ?></td>
                                <td><?php echo html_escape($row->rombel ?: '-'); ?></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $row->status_keaktifan == 'Aktif' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600'; ?>">
                                        <?php echo html_escape($row->status_keaktifan ?: '-'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-10 justify-content-center">
                                        <a href="<?php echo url('buku_induk_siswa/view/' . $row->id_siswa); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="View">
                                            <iconify-icon icon="lucide:eye"></iconify-icon>
                                        </a>
                                        <a href="<?php echo url('buku_induk_siswa/export_pdf/' . $row->id_siswa); ?>" target="_blank" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Print / Export PDF">
                                            <iconify-icon icon="lucide:printer"></iconify-icon>
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

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
</script>
