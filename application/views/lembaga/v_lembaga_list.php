<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    
    <div class="row gy-4 mb-24">
        <!-- ======================= First Row Cards Start =================== -->
        <div class="col-xxl-12 col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="row gy-4">
                    <div class="col-xxl-12 col-lg-12">
                        <div class="row mt-3">
                            <div class="col-xxl-4 col-lg-4 pb-3">
                                <a href="<?php echo url('lembaga/detail') ?>" class="fullwidth card-hover">
                                    <div class="card shadow-none border bg-gradient-start-1">
                                        <div class="card-body p-20">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                                <div>
                                                    <p class="fw-medium text-primary-light mb-1">SMP Miftahul Khoer Boarding School</p>
                                                    <h5 class="mb-0">368 Siswa</h5>
                                                </div>
                                                <div
                                                    class="rounded-circle d-flex justify-content-center align-items-center">
                                                    <img src="<?php echo $url->assets ?>logo_smp.png" class="w-80-px h-80-px radius-8 object-fit-cover" alt="Avatar">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div><!-- card end -->
                                </a>
                                
                            </div>
                            <div class="col-xxl-4 col-lg-4 pb-3">
                                <a href="<?php echo url('lembaga/detail') ?>" class="fullwidth card-hover">
                                    <div class="card shadow-none border bg-gradient-start-2">
                                        <div class="card-body p-20">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                            <div>
                                                    <p class="fw-medium text-primary-light mb-1">SMA Miftahul Khoer Boarding School</p>
                                                    <h5 class="mb-0">125 Siswa</h5>
                                                </div>
                                                <div
                                                    class="rounded-circle d-flex justify-content-center align-items-center">
                                                    <img src="<?php echo $url->assets ?>logo_sma.png" class="w-80-px h-80-px radius-8 object-fit-cover" alt="Avatar">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div><!-- card end -->

                                </a>
                                
                            </div>
                            <div class="col-xxl-4 col-lg-4 pb-3">
                                <a href="<?php echo url('lembaga/detail') ?>" class="fullwidth card-hover">
                                    <div class="card shadow-none border bg-gradient-start-3">
                                        <div class="card-body p-20">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                                <div>
                                                    <p class="fw-medium text-primary-light mb-1">Pondok Pesantren Miftahul Khoer</p>
                                                    <h5 class="mb-0">352 Siswa</h5>
                                                </div>
                                                <div class="rounded-circle d-flex justify-content-center align-items-center">
                                                    <img src="<?php echo $url->assets ?>logo_ponpes.png" class="w-80-px h-80-px radius-8 object-fit-cover" alt="Avatar">
                                                </div>
                                            </div>
                                        
                                        </div>
                                    </div><!-- card end -->
                                </a>
                            </div>
                            
                        </div>

                       
                      
                    </div>


                </div>
            </div>
        </div>


    </div>

</div>

<?php include viewPath('includes/footer'); ?>
