<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-success">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light">Data PTK</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('ptk/ptkTambah') ?>" class="btn btn-sm btn-success-100 text-success"><i class="ri-add-line"></i> Tambah PTK</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Jenis PTK</th>
                                    <th scope="col">Nama PTK</th>
                                    <th scope="col">NIY</th>
                                    <th scope="col" class="text-center">Akun MKDC</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $no = 1;
                                foreach ($ptk as $row): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row->status_pegawai; ?></td>
                                        <td><?php echo (!empty($row->gelar_depan) ? trim($row->gelar_depan) . ' ' : '') . $row->nama_ptk . (!empty($row->gelar_belakang) ? ', ' . trim($row->gelar_belakang) : ''); ?></td>
                                        <td><?php echo $row->niy; ?></td>
                                        <td class="text-center">
                                            <?php if (isset($user_map[$row->id_ptk])): ?>
                                                <span class="badge bg-success-focus text-success-main px-16 py-6 radius-4">Akun sudah dibuat (<?php echo $user_map[$row->id_ptk]; ?>)</span>
                                            <?php else: ?>
                                                <?php if (hasPermissions('ptk_buat_akun')): ?>
                                                    <a href="<?php echo url('ptk/buat_akun/' . $row->id_ptk) ?>" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1" onclick="return confirm('Buatkan akun login aplikasi untuk PTK ini? Password default adalah NUPTK (jika ada) atau 123456.')">
                                                        <iconify-icon icon="lucide:user-plus"></iconify-icon> Buatkan Akun MKDC
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-focus text-danger-main px-16 py-6 radius-4">Belum ada akun</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('ptk/ptkDetail/' . $row->id_ptk) ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Data PTK">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('ptk/ptkDetail/' . $row->id_ptk . '#pills-setting') ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sunting Data PTK">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></a>
                                                <?php if (isset($user_map[$row->id_ptk]) && hasPermissions('users_edit')): ?>
                                                    <a href="<?php echo url('ptk/hak_akses/' . $row->id_ptk) ?>" class="bg-warning-100 text-warning-600 bg-hover-warning-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" data-bs-title="Hak Akses Individual">
                                                        <iconify-icon icon="mdi:shield-key" class="menu-icon"></iconify-icon>
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