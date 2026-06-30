<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light"><?php echo isset($judul_tabel) ? $judul_tabel : 'Data Siswa'; ?></h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="<?php echo url(!empty($is_nonaktif) ? 'siswa/all' : 'siswa/nonaktif') ?>" class="btn btn-warning-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>" class="text-xl"></iconify-icon>
                            <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                        </a>
                        <?php if (empty($is_nonaktif)): ?>
                            <?php if (hasPermissions('siswa_add')): ?>
                            <a href="<?php echo url(isset($tambah_url) ? $tambah_url : 'siswa/siswaAdd'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                                <iconify-icon icon="lucide:plus" class="text-xl"></iconify-icon> <?php echo isset($tambah_label) ? $tambah_label : 'Tambah Siswa'; ?>
                            </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">NISN/NIPD</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($siswa)): ?>
                                    <?php foreach ($siswa as $s): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo $s->nama_siswa; ?></td>
                                            <td class="text-center"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nipd ?: '-'); ?></td>
                                            <td><?php echo $s->rombel ?: '-'; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                    <?php if (hasPermissions('siswa_view')): ?>
                                                    <a href="<?php echo url('siswa/detail/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Detail">
                                                        <iconify-icon icon="lucide:eye"></iconify-icon>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (hasPermissions('siswa_edit')): ?>
                                                    <a href="<?php echo url('siswa/edit/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Sunting">
                                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (hasPermissions('siswa_delete')): ?>
                                                    <a href="<?php echo url('siswa/hapus/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>


<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-button').each(function() {
            var tooltipButton = $(this);
            var tooltipContent = $(this).siblings('.my-tooltip').html();

            // Initialize the tooltip
            tooltipButton.tooltip({
                title: tooltipContent,
                trigger: 'hover',
                html: true
            });

            // Optionally, reinitialize the tooltip if the content might change dynamically
            tooltipButton.on('mouseenter', function() {
                tooltipButton.tooltip('dispose').tooltip({
                    title: tooltipContent,
                    trigger: 'hover',
                    html: true
                }).tooltip('show');
            });
        });
    });
</script>
