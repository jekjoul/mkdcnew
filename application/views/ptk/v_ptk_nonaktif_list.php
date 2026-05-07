<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light">Data PTK Nonaktif</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('ptk/ptk') ?>" class="btn btn-sm btn-neutral-100 text-neutral-600"><i class="ri-arrow-left-line"></i> Kembali ke Daftar Aktif</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama PTK</th>
                                    <th scope="col">Penugasan</th>
                                    <th scope="col">Status Pegawai</th>
                                    <th scope="col">NIK</th>
                                    <th scope="col">NIY</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($ptk as $row): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <h6 class="text-md mb-0 fw-medium"><?php echo $row->nama_ptk; ?></h6>
                                        </td>
                                        <td><?php echo $row->penugasan; ?></td>
                                        <td><?php echo $row->status_pegawai; ?></td>
                                        <td><?php echo $row->nik; ?></td>
                                        <td><?php echo $row->niy; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('ptk/ptkDetail/' . $row->id_ptk) ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Lihat Data PTK">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('ptk/ptkEdit/' . $row->id_ptk) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sunting Data PTK">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></a>
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