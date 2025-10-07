<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

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

                            <?php
                            $no = 1;
                            foreach ($lembaga as $row):
                            ?>
                                <div class="col-xxl-4 col-lg-4 pb-3">
                                    <a href="<?php echo url('lembaga/detail/' . $row->id_lembaga) ?>" class="fullwidth card-hover">
                                        <div class="card shadow-none border bg-gradient-start-<?= $no; ?>">
                                            <div class="card-body p-20">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                                    <div>
                                                        <p class="fw-medium text-primary-light mb-1"><?= $row->nama_lembaga ?></p>
                                                        <h5 class="mb-0">368 Siswa [hc]</h5>
                                                    </div>
                                                    <div
                                                        class="rounded-circle d-flex justify-content-center align-items-center">
                                                        <img src="<?= urlUpload('logo_lembaga/'); ?><?= $row->logo ?>" class="w-80-px  radius-8 object-fit-cover" alt="Avatar">
                                                    </div>
                                                </div>

                                            </div>
                                        </div><!-- card end -->
                                    </a>
                                </div>
                            <?php
                                $no++;
                            endforeach
                            ?>


                        </div>



                    </div>


                </div>
            </div>
        </div>


    </div>

</div>

<?php include viewPath('includes/footer'); ?>