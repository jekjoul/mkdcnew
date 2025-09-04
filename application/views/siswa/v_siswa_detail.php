<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                <img src="<?php echo $url->assets ?>images/user-grid/user-grid-bg-siswa.jpg" alt="" class="w-100 object-fit-cover">
                <div class="ms-60 mb-24 me-60  mt--75">
                    <div class="text-center border border-top-0 border-start-0 border-end-0 mb-20">
                        <div class="card-body p-0 arrow-carousel ">

                            <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                <img src="<?php echo $url->assets ?>images/user-grid/siswa.jpg" alt="" class="w-100 h-100 object-fit-cover radius-20 ">
                                <div class="position-absolute start-50 translate-middle-x bottom-0 pb-10 z-1 text-center w-100 radius-20">
                                    <p class="card-text text-white mx-auto text-sm">FOTO 2026</p>
                                </div>
                            </div>

                            <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                <img src="<?php echo $url->assets ?>images/user-grid/siswa.jpg" alt="" class="w-100 h-100 object-fit-cover radius-20 ">
                                <div class="position-absolute start-50 translate-middle-x bottom-0 pb-10 z-1 text-center w-100 radius-20">
                                    <p class="card-text text-white mx-auto text-sm">FOTO 2025</p>
                                </div>
                            </div>
                           
                        </div>
                
                        <h6 class="mb-0 mt-16">Mirna Rahmania</h6>
                        <span class="text-secondary-light mb-16">0778083335 / 2425010040</span><br>
                        <span class="badge text-sm fw-semibold bg-dark-info-gradient px-20 py-9 radius-4 text-white  mb-20">VIII - Syafi'i</span>
                    </div>
                </div>
                <div class="ms-24 mb-24 me-24">
                    <div class="mt-24">
                        <h6 class="text-xl mb-16">Data Pribadi</h6>
                        <ul>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Nama</span>
                                <span class="w-60 text-secondary-light fw-medium">: Mirna Rahmania</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">NISN</span>
                                <span class="w-60 text-secondary-light fw-medium">: 20210720002017</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> NIPD</span>
                                <span class="w-60 text-secondary-light fw-medium">: 3207336003020001</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> NIK</span>
                                <span class="w-60 text-secondary-light fw-medium">:  5652780681230012 </span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> No HP</span>
                                <span class="w-60 text-secondary-light fw-medium">: +6292240213444  &nbsp
                                    <a class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Kirim Pesan Whatsapp">
                                        <iconify-icon icon="tabler:brand-whatsapp-filled" class="text-md"></iconify-icon> 
                                    </a>
                                </span>
                                
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Tempat Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: Ciamis</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Tgl. Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: 20 Maret 2001</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Tgl Pendaftaran</span>
                                <span class="w-60 text-secondary-light fw-medium">: 07 Januari 2021</span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Status Pendaftaran</span>
                                <span class="w-60 text-secondary-light fw-medium">: Siswa Baru
                            </li>
                            <li class="d-flex align-items-center gap-1">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Status Dapodik</span>
                                <span class="w-60 text-secondary-light fw-medium">
                                    : Sudah  Dapodik
                                    <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                        <iconify-icon icon="material-symbols:check-box" class="text-md"></iconify-icon> 
                                    </button>
                                </span>
                                
                            </li>

                           
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body p-24">
                    <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24 active" id="pills-profile-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab"
                                aria-controls="pills-profile" aria-selected="true">
                                Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-rekam-didik-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-rekam-didik" type="button" role="tab"
                                aria-controls="pills-rekam-didik" aria-selected="false" tabindex="-1">
                                Rekam Didik
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-riwayat-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-riwayat" type="button" role="tab"
                                aria-controls="pills-riwayat" aria-selected="false" tabindex="-1">
                                Rekam Medis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-arsip-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-catatan" type="button" role="tab"
                                aria-controls="pills-catatan" aria-selected="false" tabindex="-1">
                                Catatan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-arsip-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-arsip" type="button" role="tab"
                                aria-controls="pills-arsip" aria-selected="false" tabindex="-1">
                                Arsip
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-setting-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-setting" type="button" role="tab"
                                aria-controls="pills-setting" aria-selected="false" tabindex="-1">
                                Setting
                            </button>
                        </li>
                        
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Profil -->
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class="card radius-12 h-100 shadow">
                                
                                <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                    <h6 class="text-lg mb-0">Profil Siswa</h6>
                                    <button type="button" class="text-xl line-height-1">
                                        <iconify-icon icon="icon-park-outline:user-business" class="text-xl"></iconify-icon> 
                                    </button>
                                </div>

                                <div class="card-body py-16 px-24">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                         <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            Nama Lengkap :
                                                        </span>
                                                        <br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Mirna Rahmania
                                                        </span>&nbsp
                                                    </div>
                                                   
                                                    <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                   
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            Jenis Kelamin :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Perempuan
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            Tempat Tanggal Lahir :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Ciamis, 07 November 2011
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            Agama :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Islam
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            NIK :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            3207084711110002
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            No Kartu Keluarga :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            3207084711110002
                                                        </span>
                                                    </div>
                                                    <button class="text-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" data-bs-title="Tidak Sesuai Dapodik : Yani">
                                                       <iconify-icon icon="ion:warning" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            NISN :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            0778083335 
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                            NIPD :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            2425010040
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Tanggal Pendaftaran :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            15 Juli 2024
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Jenis Pendaftaran :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                           Siswa Baru
                                                        </span>
                                                    </div> 
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Asal Sekolah :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            SD Negeri 1 Mandalare
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Alamat :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Dusun Mandalare 008/004 RT RW Desa/Kel. Mandalare Kec. Panjalu Kab. Ciamis Prov. Jawa Barat 
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Siswa Induk :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            Ya
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <div class="col-md-6">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                <div class="align-items-center gap-3 d-flex justify-content-between">
                                                    <div>
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                        Nomor Ponsel :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            +6292240213444  
                                                        </span>
                                                    </div>
                                                    <button class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                       <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                
                            </div>
                           
                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Data Ayah</h6>
                                            <button type="button" class="text-xl line-height-1">
                                                <iconify-icon icon="material-symbols:book-5-rounded" class="text-xl"></iconify-icon> 
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Nama Lengkap Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Maman Maulana 
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    NIK Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    3207084711110002
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Pekerjaan Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Wiraswasta
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Penghasilan Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Rp 500.000 - Rp 1.000.000
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Tahun Lahir Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    1980
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Pendidikan Ayah :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    SLTA Sederajat
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Alamat  :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Dusun Mandalare 008/004 RT RW Desa/Kel. Mandalare Kec. Panjalu Kab. Ciamis Prov. Jawa Barat 
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Data Ibu</h6>
                                            <button type="button" class="text-xl line-height-1">
                                                <iconify-icon icon="material-symbols:book-5-rounded" class="text-xl"></iconify-icon> 
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Nama Lengkap Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Cucu maryam 
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    NIK Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    3207084711110002
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Pekerjaan Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Tidak Bekerja
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Penghasilan Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Tidak Berpenghasilan
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Tahun Lahir Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    1980
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Pendidikan Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    SLTA Sederajat
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-switch switch-primary py-12 px-16 border radius-8  mb-16">
                                                        <div class="align-items-center gap-3 d-flex justify-content-between">
                                                            <div>
                                                                <span class="form-check-label line-height-1 fw-medium text-secondary-light fst-italic">
                                                                    Alamat Ibu :
                                                                </span>
                                                                <br>
                                                                <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                                    Dusun Mandalare 008/004 RT RW Desa/Kel. Mandalare Kec. Panjalu Kab. Ciamis Prov. Jawa Barat 
                                                                </span>&nbsp
                                                            </div>
                                                            <button class="text-success " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sesuai Dapodik">
                                                            <iconify-icon icon="material-symbols:check-box" class="text-xl"></iconify-icon> 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-body">

                                            <div class="card-footer text-center bg-transparent border border-end-0 border-start-0 border-bottom-0 py-16 px-24 ">
                                                <a href="javascript:void(0)" class="btn btn-success text-light  ">
                                                    <span class="d-flex">
                                                        <iconify-icon icon="material-symbols:print-rounded" class="text-xl d-flex"> </iconify-icon> 
                                                        &nbsp Cetak Profil
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-success text-light  ">
                                                    <span class="d-flex">
                                                        <iconify-icon icon="material-symbols:print-rounded" class="text-xl d-flex"> </iconify-icon> 
                                                        &nbsp Cetak Surat Keterangan Aktif
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            
                        </div>
                        <!-- End of Profil -->

                        <!-- Rekam Didik -->
                        <div class="tab-pane fade" id="pills-rekam-didik" role="tabpanel" aria-labelledby="pills-rekam-didik-tab" tabindex="0">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Rekam Didik</h6>
                                            <button type="button" class="text-xl line-height-1">
                                                <iconify-icon icon="material-symbols:book-5-rounded" class="text-xl"></iconify-icon> 
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table"  data-page-length='10'>
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Sekolah</th>
                                                            <th scope="col">Tingkat</th>
                                                            <th scope="col">Tahun Pelajaran</th>
                                                            <th scope="col" class="text-center">Detail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>SMP Miftahul Khoer Boarding School</td>
                                                            <td>VIII</td>
                                                            <td>2025/2026 Ganjil</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <a href="<?php echo url('siswa/rekamDidik') ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Detail Pembelajaran">
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                                    </a>
                                                                   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td>SMP Miftahul Khoer Boarding School</td>
                                                            <td>VII</td>
                                                            <td>2024/2025 Genap</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <a href="<?php echo url('siswa/rekamDidik') ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Detail Pembelajaran">
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                                    </a>
                                                                   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td>SMP Miftahul Khoer Boarding School</td>
                                                            <td>VII</td>
                                                            <td>2024/2025 Ganjil</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <a href="<?php echo url('siswa/rekamDidik') ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Detail Pembelajaran">
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                                    </a>
                                                                   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                      
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                        </div>
                        <!-- End of Rekam Didik -->
                        
                        <!-- Rekam Medis -->
                        <div class="tab-pane fade" id="pills-riwayat" role="tabpanel" aria-labelledby="pills-riwayat-tab" tabindex="0">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Riwayat Medis Siswa</h6>
                                            <a href="<?php echo url('ptk/ptkTambah') ?>" class="btn btn-sm btn-info-100 text-dark"><i class="ri-add-line"></i> Tambah Rekam Medis</a>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table"  data-page-length='10'>
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Pemeriksa</th>
                                                            <th scope="col">Keterangan</th>
                                                            <th scope="col">Tanggal</th>
                                                            <th scope="col">Penyakit</th>
                                                            <th scope="col" class="text-center">Detail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>Puskesmas</td>
                                                            <td>Cek Kesehatan Puskesmas</td>
                                                            <td>04 September 2025</td>
                                                            <td>-</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <a href="<?php echo url('ptk/ptkDetail') ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Detail Pembelajaran">
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                                    </a>
                                                                   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td>Sekolah</td>
                                                            <td>Cek Tinggi & Berat Badan</td>
                                                            <td>04 Agustus 2025</td>
                                                            <td>-</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <a href="<?php echo url('ptk/ptkDetail') ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Detail Pembelajaran">
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                                    </a>
                                                                   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Rekam Medis -->

                        <!-- Catatan -->
                        <div class="tab-pane fade" id="pills-catatan" role="tabpanel" aria-labelledby="pills-catatan-tab" tabindex="0">
                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card basic-data-table shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Catatan Siswa</h6>
                                            <button id="openModalBtn" class="btn btn-sm btn-info-100 text-dark"><i class="ri-add-line"></i> Tambah Catatan</button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table " id="dataPribadi" data-page-length='5' style="width:100% !important">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Jenis Catatan</th>
                                                            <th scope="col">Tanggal</th>
                                                            <th scope="col">Keterangan</th>
                                                            <th scope="col" class="text-center" style="max-width:130px !important">Detail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Pelanggaran</td>
                                                            <td>21 Agustus 2025</td>
                                                            <td><span class="float-left badge bg-warning">Bolos Sekolah</span></td>
                                                            <td style="max-width:130px !important">
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <button type="button" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#detailIjazah"> 
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                                    </button>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                       
                                                        
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of catatan -->

                        <!-- Arsip -->
                        <div class="tab-pane fade" id="pills-arsip" role="tabpanel" aria-labelledby="pills-arsip-tab" tabindex="0">
                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card basic-data-table shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Data Pribadi</h6>
                                            <a href="<?php echo url('ptk/ptkTambah') ?>" class="btn btn-sm btn-info-100 text-dark"><i class="ri-add-line"></i> Tambah Arsip</a>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table " id="dataPribadi" data-page-length='5' style="width:100% !important">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Nama Dokumen</th>
                                                            <th scope="col">Status Dokumen</th>
                                                            <th scope="col" class="text-center" style="max-width:130px !important">Berkas</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>KTP</td>
                                                            <td><span class="float-left badge bg-success">Sudah Upload</span></td>
                                                            <td style="max-width:130px !important">
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <button type="button" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#detailIjazah"> 
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                                    </button>

                                                                    
                                                                    <button type="button" class="btn rounded-pill btn-success-100 text-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#detailIjazah"> 
                                                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></iconify-icon> Sunting
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                       
                                                        <tr>
                                                            <td>Kartu Keluarga</td>
                                                            <td><span class="float-left badge bg-success">Sudah Upload</span></td>
                                                            <td style="max-width:130px !important">
                                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                    <button type="button" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#detailIjazah"> 
                                                                        <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                                    </button>

                                                                    
                                                                    <button type="button" class="btn rounded-pill btn-success-100 text-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#detailIjazah"> 
                                                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></iconify-icon> Sunting
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                       

                        </div>
                        <!-- End of Arsip -->

                        <!-- Setting -->
                        <div class="tab-pane fade" id="pills-setting" role="tabpanel"
                            aria-labelledby="pills-setting-tab" tabindex="0">
                            
                            <div class="mb-24 mt-16">
                                <div class="avatar-upload">
                                    <div
                                        class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                        <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" hidden>
                                        <label for="imageUpload"
                                            class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                            <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                        </label>
                                    </div>
                                    <div class="avatar-preview">
                                        <div id="imagePreview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Upload Image End -->
                            <form action="#">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name
                                                <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="name"
                                                placeholder="Enter Full Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="email"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Email
                                                <span class="text-danger-600">*</span></label>
                                            <input type="email" class="form-control radius-8" id="email"
                                                placeholder="Enter email address">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="number"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Phone</label>
                                            <input type="email" class="form-control radius-8" id="number"
                                                placeholder="Enter phone number">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="depart"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Department
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="form-control radius-8 form-select" id="depart">
                                                <option>Enter Event Title </option>
                                                <option>Enter Event Title One </option>
                                                <option>Enter Event Title Two</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="desig"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Designation
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="form-control radius-8 form-select" id="desig">
                                                <option>Enter Designation Title </option>
                                                <option>Enter Designation Title One </option>
                                                <option>Enter Designation Title Two</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="Language"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Language
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="form-control radius-8 form-select" id="Language">
                                                <option> English</option>
                                                <option> Bangla </option>
                                                <option> Hindi</option>
                                                <option> Arabic</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-20">
                                            <label for="desc"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                            <textarea name="#0" class="form-control radius-8" id="desc"
                                                placeholder="Write description..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button"
                                        class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8">
                                        Simpan Profil PTK
                                    </button>
                                </div>
                            </form>


                            <hr class="mt-40 mb-20">
                            <h6>Ganti Password</h6>
                            <div class="mb-20 mt-2">
                                <label for="your-password"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">New Password <span
                                        class="text-danger-600">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="form-control radius-8" id="your-password"
                                        placeholder="Enter New Password*">
                                    <span
                                        class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                        data-toggle="#your-password"></span>
                                </div>
                            </div>
                            <div class="mb-20">
                                <label for="confirm-password"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Confirmed Password
                                    <span class="text-danger-600">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="form-control radius-8" id="confirm-password"
                                        placeholder="Confirm Password*">
                                    <span
                                        class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                        data-toggle="#confirm-password"></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <button type="button"
                                    class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8">
                                    Ganti Password
                                </button>
                            </div>
                        </div>
                        <!-- End of Setting -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Berkas -->
<div class="modal fade" id="detailIjazah" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Lihat Berkas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <object style="width: 100%;height: 100%;" data="<?php echo url('uploads/berkas.pdf')?>" type="application/pdf" id="pdf_content" style="pointer-events: none;">
                    <iframe src="<?php echo url('uploads/berkas.pdf')?>&embedded=true"></iframe>
                </object>
            </div>
        </div>
    </div>
</div>
<!-- End of Modal Lihat Berkas -->

<!-- Modal Ajax input catatan -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <div id="modal-body" class="p-4">
            <!-- Konten akan dimuat di sini -->
        </div>
    </div>
</div>
<!-- End of Modal Ajax input catatan -->

<?php include viewPath('includes/footer'); ?>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]'); 
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)); 

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-dapo').each(function () {
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

<script>
    // ======================== Upload Image Start =====================
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function() {
        readURL(this);
    });
    // ======================== Upload Image End =====================

    // ================== Password Show Hide Js Start ==========
    function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on('click', function() {
            $(this).toggleClass("ri-eye-off-line");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    // Call the function
    initializePasswordToggle('.toggle-password');
  // ========================= Password Show Hide Js End ===========================
</script>


<script>
  let table = new DataTable('#dataTable');
  let table2 = new DataTable('#skPengangkatan');
  let table3 = new DataTable('#skTugas');
  let table4 = new DataTable('#dataPribadi');

</script>

<script>
    var rtlDirection = $('html').attr('dir') === 'rtl';
  // ================================ Default Slider Start ================================ 
  $('.default-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: false, 
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        rtl: rtlDirection
    });

    // Arrow Carousel
  $('.arrow-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: true, 
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection
    });

    // pagination carousel
    $('.pagination-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: false, 
        dots: true,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection
    });
    
    // multiple carousel
    $('.multiple-carousel').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1, 
        arrows: false, 
        dots: true,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        gap: 24,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection,
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                slidesToShow: 3,
                }
            },
            {
                breakpoint: 991,
                settings: {
                slidesToShow: 2,
                }
            },
            {
                breakpoint: 575,
                settings: {
                slidesToShow: 1,
                }
            },
        ]
    });

    // carousel with progress bar
    jQuery(document).ready(function($) {
        var sliderTimer = 5000;
        var beforeEnd = 500;
        var $imageSlider = $('.progress-carousel');
        $imageSlider.slick({
            autoplay: true,
            autoplaySpeed: sliderTimer,
            speed: 1000,
            arrows: false,
            dots: false,
            adaptiveHeight: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            rtl: rtlDirection
        });

        function progressBar(){
            $('.slider-progress').find('span').removeAttr('style');
            $('.slider-progress').find('span').removeClass('active');
            setTimeout(function(){
                $('.slider-progress').find('span').css('transition-duration', (sliderTimer/1000)+'s').addClass('active');
            }, 100);
        }
        progressBar();
        $imageSlider.on('beforeChange', function(e, slick) {
            progressBar();
        });
        $imageSlider.on('afterChange', function(e, slick, nextSlide) {
            titleAnim(nextSlide);
        });

        // Title Animation JS
        function titleAnim(ele){
            $imageSlider.find('.slick-current').find('h1').addClass('show');
            setTimeout(function(){
                $imageSlider.find('.slick-current').find('h1').removeClass('show');
            }, sliderTimer - beforeEnd);
        }
        titleAnim();
    });
  // ================================ Default Slider End ================================ 
</script>

<script>
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const myModal = document.getElementById('myModal');
    const modalBody = document.getElementById('modal-body');

    // Fungsi untuk memuat konten menggunakan AJAX
    function loadModalContent() {
        // Tampilkan spinner loading
        modalBody.innerHTML = '<div class="spinner"></div>';

        // Menggunakan setTimeout untuk mensimulasikan jeda dari permintaan AJAX
        setTimeout(() => {
            // Gunakan fetch() untuk memuat konten dari file eksternal (simulasi)
            fetch('<?php echo url('siswa/inputCatatan') ?>')
                .then(response => {
                    // Periksa apakah responsnya OK
                    if (!response.ok) {
                        throw new Error('Jaringan tidak responsif atau terjadi kesalahan.');
                    }
                    return response.text();
                })
                .then(data => {
                    // Masukkan data ke dalam elemen modal-body
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    // Tangani kesalahan
                    console.error('Ada masalah dengan operasi fetch:', error);
                    modalBody.innerHTML = '<p class="text-red-500">Gagal memuat konten. Silakan coba lagi.</p>';
                });
        }, 1000); // Tunda selama 1 detik untuk simulasi loading
    }

    // Ketika tombol "Buka Modal" ditekan, tampilkan modal dan muat konten
    openModalBtn.addEventListener('click', () => {
        myModal.style.display = 'flex';
        loadModalContent();
    });

    // Ketika tombol "Tutup" ditekan, sembunyikan modal
    closeModalBtn.addEventListener('click', () => {
        myModal.style.display = 'none';
    });

    // Ketika pengguna mengklik area di luar modal, sembunyikan modal
    window.addEventListener('click', (event) => {
        if (event.target === myModal) {
            myModal.style.display = 'none';
        }
    });
</script>


