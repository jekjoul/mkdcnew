<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Lembaga / Detail Lembaga /</span>
        SMP Miftahul Khoer Boarding School
    </h4>


    <!-- Header -->
    <div class="row">

        <div class="col-12">
            <div class="card mb-4">
                <div class="user-profile-header-banner">
                    <img src="<?php echo $url->assets ?>img/pages/profile-banner.png" alt="Banner image"
                        class="rounded-top" style="height:150px !important">
                </div>
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img src="<?php echo $url->assets ?>img/logo_smp.png" alt="user image"
                            class="d-block h-auto ms-0 ms-sm-4 rounded-3 user-profile-img">
                    </div>
                    <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div
                            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4>SMP Miftahul Khoer Boarding School</h4>
                                <ul
                                    class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                    <li class="list-inline-item fw-medium">
                                        <i class='bx bx-pen'></i>
                                        NPSN : 69948104
                                    </li>
                                    <li class="list-inline-item fw-medium">
                                        <i class='bx bx-map'></i>
                                        Panjalu, Ciamis
                                    </li>
                                    <li class="list-inline-item fw-medium">
                                        <i class='bx bx-calendar-alt'></i>
                                        Didirikan Pada 15 Juli 2015
                                    </li>
                                </ul>
                            </div>
                            <a href="javascript:void(0)" class="btn btn-primary text-nowrap">
                                <i class='bx bx-user-check me-1'></i>Connected
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link <?php $page = $this->uri->segment('4'); if ($page=="profil"){ echo "active";} ?>"
                        href="<?php echo url('lembaga/detailLembaga/1/profil') ?>">
                        <i class="bx bx-user me-1"></i>
                        Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php $page = $this->uri->segment('4'); if ($page=="siswa"){ echo "active";} ?>"
                        href="<?php echo url('lembaga/detailLembaga/1/siswa') ?>">
                        <i class="bx bx-lock-alt me-1"></i>
                        Siswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php $page = $this->uri->segment('4'); if ($page=="ptk"){ echo "active";} ?>"
                        href="<?php echo url('lembaga/detailLembaga/1/ptk') ?>">
                        <i class="bx bx-detail me-1"></i>
                        PTK</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php $page = $this->uri->segment('4'); if ($page=="sarpras"){ echo "active";} ?>"
                        href="<?php echo url('lembaga/detailLembaga/1/sarpras') ?>">
                        <i class="bx bx-bell me-1"></i>
                        Sarpras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php $page = $this->uri->segment('4'); if ($page=="grafik"){ echo "active";} ?>"
                        href="<?php echo url('lembaga/detailLembaga/1/grafik') ?>">
                        <i class="bx bx-link-alt me-1"></i>
                        Grafik</a>
                </li>
            </ul>

            <?php $page = $this->uri->segment('4'); if ($page=="profil"){ ?>
            <!-- Profil -->
            <div class="card mb-4">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-4 mb-1">
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Kepala Sekolah :</h6>
                                <p>Dr. Siti Julaeha, M.Pd., Gr.</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Status Sekolah :</h6>
                                <p>Swasta</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">SK Pendirian :</h6>
                                <p>421.1/2687-Kpts/Disdikbud/2015</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Tanggal SK Pendirian :</h6>
                                <p>6 Juli 2015</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">SK Izin Operasional :</h6>
                                <p>421.1/4093- Kpts/Disdikbud/2016</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Tanggal SK Izin Operasional :</h6>
                                <p>29 Juni 2016</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Implementasi Kurikulum :</h6>
                                <p>Kurikulum Merdeka</p>
                            </div>

                        </div>

                        <div class="col-md-4 mb-1">


                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Operator Sekolah :</h6>
                                <p>Zakaria Zulkarnain</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Status BOSP :</h6>
                                <p>Bersedia Menerima BOSP</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Bendahara Bos :</h6>
                                <p>Kiki Baehaqi Saepul Millah, S.Pd.</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">NPWP :</h6>
                                <p>001200161442000</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Akreditasi :</h6>
                                <p>555/BAN-SM/SK/2023 (B)</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">TMT-TST Akreditasi :</h6>
                                <p>25 Mei 2023 - 25 Mei 2028</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Email :</h6>
                                <p>smpemka@gmail.com</p>
                            </div>

                        </div>

                        <div class="col-md-4 mb-1">
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Alamat :</h6>
                                <p>Dusun Mandala RT 017 RW 006</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Desa/Kelurahan :</h6>
                                <p>Kertamandala</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Kecamatan :</h6>
                                <p>Panjalu</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Kabupaten :</h6>
                                <p>Ciamis</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Provinsi :</h6>
                                <p>Jawa Barat</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Lintang :</h6>
                                <p>-7.1462</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-medium mb-2">Bujur :</h6>
                                <p>108.2668</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start justify-content-md-end align-items-baseline mb-3">
                            <button class="btn btn-warning me-2 mt-2" data-bs-toggle="modal"
                                data-bs-target="#pricingModal">Sunting Data</button>
                        </div>
                    </div>
                </div>
                <!-- /Current Plan -->
            </div>
            <!-- End Of Profil -->

            <!-- Siswa -->
            <?php }elseif($page=="siswa"){?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <h5 class="card-header">Tabel Keadaan Siswa</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-light">
                                        <th width="5">No</th>
                                        <th>Nama Rombel</th>
                                        <th style="text-align: center;">Tingkat</th>
                                        <th style="text-align: center;">Laki-laki</th>
                                        <th style="text-align: center;">Perempuan</th>
                                        <th style="text-align: center;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">VII - Al Maturidi</span></td>
                                        <td>7 (Tujuh)</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><span class="fw-medium">VII - Az Zahrawi</span></td>
                                        <td>7 (Tujuh)</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><span class="fw-medium">VIII - Maliki</span></td>
                                        <td>8 (Delapan)</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><span class="fw-medium">VIII - Syafi'i</span></td>
                                        <td>8 (Delapan)</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><span class="fw-medium">IX - Az Zarnuji</span></td>
                                        <td>9 (Sembilan)</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">IX - Sonhaji</span></td>
                                        <td>9 (Sembilan)</td>
                                        <td style="text-align: center;">10</td>
                                        <td style="text-align: center;">15</td>
                                        <td style="text-align: center;"><span class="fw-medium">25</span></td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-border-bottom-0">
                                    <tr class="table-dark">
                                        <th colspan="3" >Jumlah</th>
                                        <th style="text-align: center;">70</th>
                                        <th style="text-align: center;">80</th>
                                        <th style="text-align: center;">150</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Current Plan -->
            </div>
            <!-- End Of Siswa -->
            
            <!-- PTK -->
            <?php }elseif($page=="ptk"){?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <h5 class="card-header">Jumlah PTK</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-light">
                                        <th width="5">No</th>
                                        <th>Jenis PTK</th>
                                        <th style="text-align: center;">Laki-laki</th>
                                        <th style="text-align: center;">Perempuan</th>
                                        <th style="text-align: center;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">Guru</span></td>
                                        <td style="text-align: center;">7</td>
                                        <td style="text-align: center;">9</td>
                                        <td style="text-align: center;"><span class="fw-medium">16</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><span class="fw-medium">Tenaga Administrasi</span></td>
                                        <td style="text-align: center;">2</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                                    </tr>
                                    
                                </tbody>
                                <tfoot class="table-border-bottom-0">
                                    <tr class="table-dark">
                                        <th colspan="2" >Jumlah</th>
                                        <th style="text-align: center;">9</th>
                                        <th style="text-align: center;">10</th>
                                        <th style="text-align: center;">19</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <h5 class="card-header">Pendidikan PTK</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-light">
                                        <th width="5">No</th>
                                        <th>Tingkat Pendidikan Terakhir</th>
                                        <th style="text-align: center;">Laki-laki</th>
                                        <th style="text-align: center;">Perempuan</th>
                                        <th style="text-align: center;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">Strata 3/S3</span></td>
                                        <td style="text-align: center;">7</td>
                                        <td style="text-align: center;">9</td>
                                        <td style="text-align: center;"><span class="fw-medium">16</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><span class="fw-medium">Strata 2/S2</span></td>
                                        <td style="text-align: center;">2</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><span class="fw-medium">Strata 1/S1</span></td>
                                        <td style="text-align: center;">2</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><span class="fw-medium">Ahli Madya/Diploma</span></td>
                                        <td style="text-align: center;">2</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><span class="fw-medium">SLTA</span></td>
                                        <td style="text-align: center;">2</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"><span class="fw-medium">3</span></td>
                                    </tr>
                                    
                                </tbody>
                                <tfoot class="table-border-bottom-0">
                                    <tr class="table-dark">
                                        <th colspan="2" >Jumlah</th>
                                        <th style="text-align: center;">9</th>
                                        <th style="text-align: center;">10</th>
                                        <th style="text-align: center;">19</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Current Plan -->
            </div>
            <!-- End Of PTK -->

            <!-- Sarpras -->
            <?php }elseif($page=="sarpras"){?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <h5 class="card-header">Tanah</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-light">
                                        <th width="5">No</th>
                                        <th>Kepemilikan</th>
                                        <th style="text-align: center;">Luas</th>
                                        <th style="text-align: center;">No Sertifikat</th>
                                        <th style="text-align: center;">Nama di Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">Yayasan</span></td>
                                        <td style="text-align: center;">10.000 m</td>
                                        <td style="text-align: center;">20971/2312/1<sup class="mt-3 mb-0 me-1">2</sup></td>
                                        <td style="text-align: center;"><span class="fw-medium">Siti Robiah</span></td>
                                    </tr>
                                   
                                </tbody>
                                
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <h5 class="card-header">Bangunan</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-light">
                                        <th width="5">No</th>
                                        <th>Nama Bangunan</th>
                                        <th style="text-align: center;">Luas</th>
                                        <th style="text-align: center;">Jumlah Ruangan</th>
                                        <th style="text-align: center;">Kepemilikan</th>
                                        <th style="text-align: center;">Kondisi</th>
                                        <th style="text-align: center;">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td>1</td>
                                        <td><span class="fw-medium">Bangunan Kelas 7A</span></td>
                                        <td style="text-align: center;">75 m<sup class="mt-3 mb-0 me-1">2</sup></td>
                                        <td style="text-align: center;">1 Ruang</td>
                                        <td style="text-align: center;">SMP</td>
                                        <td style="text-align: center;">Rusak Ringan</span></td>
                                        <td style="text-align: center;"><a href="#">Detail</a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><span class="fw-medium">Bangunan Kelas Yayasan</span></td>
                                        <td style="text-align: center;">75 m<sup class="mt-3 mb-0 me-1">2</sup></td>
                                        <td style="text-align: center;">2 Ruang</td>
                                        <td style="text-align: center;">Yayasan</td>
                                        <td style="text-align: center;">Rusak Sedang</span></td>
                                        <td style="text-align: center;"><a href="#">Detail</a></td>
                                    </tr>
                                   
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Of Sarpras -->

            <!-- Grafik -->
            <?php }else{?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        Grafik
                    </div>
                </div>
                <!-- /Current Plan -->
            </div>
            <?php }; ?>
            <!-- End Of Grafik -->

        </div>
    </div>
    <!--/ Project Cards -->
</div>

<?php include viewPath('includes/footer'); ?>