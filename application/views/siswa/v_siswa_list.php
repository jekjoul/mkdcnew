<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600" >
                    <div class="d-flex flex-wrap align-items-center gap-3">
                      <h6 class="text-light">Data Siswa</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('ptk/ptkTambah') ?>" class="btn btn-sm btn-info-100 text-dark"><i class="ri-add-line"></i> Tambah Siswa</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col"  class="text-center">NISN/NIPD</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Mirna Rahmania</td>
                                    <td class="text-center">0085216452 / 2324252624</td>
                                    <td>VIII - Syafi'i</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Abdullah Faiz Az Zaidan</td>
                                    <td class="text-center">0085216452 / 2324252624</td>
                                    <td>XI - Hanafi</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <a type="button" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                            </a>
                                        </div>
                                    </td>
                                </tr>


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
        $('.tooltip-button').each(function () {
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
