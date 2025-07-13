<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Lembaga /</span>
        Daftar Lembaga
    </h4>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div
                class="d-flex justify-content-start justify-content-md-end align-items-baseline mb-3">
                <a href="" class="btn btn-primary">
                    <span>
                        <i class="bx bx-plus"></i>
                        Tambah Lembaga
                    </span>
                </a>
            </div>

            <!-- Connection Cards -->
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mx-auto mb-3">
                                <img
                                    src="<?php echo $url->assets ?>img/logo_smp.png"
                                    alt="Avatar Image"
                                    class=" h-px-100"/>
                            </div>
                            <h6 class="mb-1 card-title">SMP Miftahul Khoer Boarding School</h6>
                            <span>Dr. Siti Julaeha, M.Pd., Gr.</span>
                            <div class="d-flex align-items-center justify-content-center my-3 gap-2">
                                <a href="javascript:;" class="me-1">
                                    <span class="badge bg-label-secondary">Figma</span>
                                </a>
                                <a href="javascript:;">
                                    <span class="badge bg-label-warning">Sketch</span>
                                </a>
                            </div>

                            <div class="d-flex align-items-center justify-content-around my-4 py-2">
                                <div>
                                    <h4 class="mb-1">263</h4>
                                    <span>Siswa</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">12</h4>
                                    <span>Rombel</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">20</h4>
                                    <span>Pegawai</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <a
                                    href="<?php echo url('lembaga/detailLembaga/1/profil') ?>"
                                    class="btn btn-primary d-flex align-items-center me-3">
                                    <i class="bx bx-building-house me-1"></i>Detail Lembaga
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mx-auto mb-3">
                                <img
                                    src="<?php echo $url->assets ?>img/logo_sma.png"
                                    alt="Avatar Image"
                                    class=" h-px-100"/>
                            </div>
                            <h6 class="mb-1 card-title">SMA Miftahul Khoer Boarding School</h6>
                            <span>Kiki Baehaqi Saepul Millah, S.Pd.</span>
                            <div class="d-flex align-items-center justify-content-center my-3 gap-2">
                                <a href="javascript:;" class="me-1">
                                    <span class="badge bg-label-secondary">Figma</span>
                                </a>
                                <a href="javascript:;">
                                    <span class="badge bg-label-warning">Sketch</span>
                                </a>
                            </div>

                            <div class="d-flex align-items-center justify-content-around my-4 py-2">
                                <div>
                                    <h4 class="mb-1">263</h4>
                                    <span>Siswa</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">12</h4>
                                    <span>Rombel</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">20</h4>
                                    <span>Pegawai</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3">
                                    <i class="bx bx-building-house me-1"></i>Detail Lembaga
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mx-auto mb-3">
                                <img
                                    src="<?php echo $url->assets ?>img/logo_ponpes.png"
                                    alt="Avatar Image"
                                    class=" h-px-100"/>
                            </div>
                            <h6 class="mb-1 card-title">Pondok Pesantren Miftahul Khoer</h6>
                            <span>Kiki Baehaqi Saepul Millah, S.Pd.</span>
                            <div class="d-flex align-items-center justify-content-center my-3 gap-2">
                                <a href="javascript:;" class="me-1">
                                    <span class="badge bg-label-secondary">Figma</span>
                                </a>
                                <a href="javascript:;">
                                    <span class="badge bg-label-warning">Sketch</span>
                                </a>
                            </div>

                            <div class="d-flex align-items-center justify-content-around my-4 py-2">
                                <div>
                                    <h4 class="mb-1">263</h4>
                                    <span>Siswa</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">12</h4>
                                    <span>Rombel</span>
                                </div>
                                <div>
                                    <h4 class="mb-1">20</h4>
                                    <span>Pegawai</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3">
                                    <i class="bx bx-building-house me-1"></i>Detail Lembaga
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Connection Cards -->

        </div>
        <!-- / Content -->
    </div>
</div>

<?php include viewPath('includes/footer'); ?>