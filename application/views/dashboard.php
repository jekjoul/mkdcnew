<?php
defined('BASEPATH') or exit('No direct script access allowed');

$years = [];
$smp_data = [];
$sma_data = [];
if (!empty($tren_pendaftaran)) {
    foreach ($tren_pendaftaran as $row) {
        $years[] = (string)$row->tahun;
        $smp_data[] = (int)$row->total_smp;
        $sma_data[] = (int)$row->total_sma;
    }
}
?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row g-2 g-md-3 mb-24">
        <div class="col-md-12">
            <div class="trail-bg h-100 p-20 radius-12 d-flex flex-wrap align-items-center justify-content-between gap-3 text-white">
                <div>
                    <p class="text-white text-sm mb-0" style="margin-bottom:0px !important">Selamat datang kembali,</p>
                    <h6 class="text-white text-xl fw-bold mb-0"><?php echo html_escape(logged('name')) ?></h6>
                </div>
                <div class="text-end ms-auto">
                    <h6 class="text-white text-xl fw-bold mb-0 realtime-clock-display">00:00:00 WIB</h6>
                    <p class="text-white text-sm mb-0 realtime-date-display" style="margin-bottom:0px !important">Hari, 00 Bulan 0000</p>
                </div>
            </div>
        </div>
    </div>
    <!-- ======================= Admin Stat Cards (3 Cards per Baris) =================== -->
    <div class="row g-2 g-md-3 mb-24">
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-1">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="gridicons:multiple-users" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_siswa) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Seluruh Siswa</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-2">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="material-symbols:person-apron-rounded" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_ptk) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Pegawai</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-3">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="material-symbols:person-apron-rounded" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_alumni) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Alumni</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-4">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="gridicons:multiple-users" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_smp) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Siswa SMP</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-5">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="gridicons:multiple-users" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_sma) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Siswa SMA</span>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs bg-gradient-start-1">
                <div class="card-body p-12 p-md-20 text-center">
                    <div class="w-36-px h-36-px bg-cyan rounded-circle d-inline-flex justify-content-center align-items-center mb-2 mx-auto">
                        <iconify-icon icon="gridicons:multiple-users" class="text-base text-xl mb-0"></iconify-icon>
                    </div>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo number_format($total_ponpes) ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Santri Ponpes</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-24">
        <div class="col-xxl-6 col-md-6">
            <div class="card h-100 radius-8 border-0">
                <div class="card-body p-24 d-flex flex-column justify-content-between gap-8">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Distribusi Siswa per Lembaga</h6>
                    </div>
                    <div id="userOverviewDonutChart" class="margin-16-minus y-value-left apexcharts-tooltip-z-none">
                    </div>

                    <ul class="d-flex flex-wrap align-items-center justify-content-between mt-3 gap-3">
                        <li class="d-flex flex-column gap-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-success-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Siswa SMP</span>
                            </div>
                            <span class="text-primary-light fw-bold"><?php echo number_format($total_smp) ?></span>
                        </li>
                        <li class="d-flex flex-column gap-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-warning-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Siswa SMA</span>
                            </div>
                            <span class="text-primary-light fw-bold"><?php echo number_format($total_sma) ?></span>
                        </li>
                        <li class="d-flex flex-column gap-8">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Santri Ponpes</span>
                            </div>
                            <span class="text-primary-light fw-bold"><?php echo number_format($total_ponpes) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xxl-6 col-md-6">
            <div class="card h-100">
                <div class="card-header border-0 pb-0 bg-transparent">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Tren Pendaftaran Siswa Baru (SMP vs SMA)</h6>
                    </div>
                </div>
                <div class="card-body p-24">
                    <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-3">
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-12-px rounded-circle bg-success-main"></span>
                            <span class="text-secondary-light text-sm fw-semibold">Siswa SMP</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-12-px rounded-circle bg-warning-600"></span>
                            <span class="text-secondary-light text-sm fw-semibold">Siswa SMA</span>
                        </li>
                    </ul>
                    <div id="paymentStatusChart" class="margin-16-minus y-value-left"></div>
                </div>
            </div>
        </div>

        <?php if (!empty($siswa_rombel)): ?>
            <?php foreach ($siswa_rombel as $lembaga => $rombels): ?>
                <div class="col-xxl-6 col-md-6 mb-24">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0 fw-bold text-lg"><?php echo html_escape($lembaga); ?></h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama Rombel</th>
                                            <th scope="col" class="text-center">Laki-laki</th>
                                            <th scope="col" class="text-center">Perempuan</th>
                                            <th scope="col" class="text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_l = 0;
                                        $total_p = 0;
                                        $total_j = 0;
                                        foreach ($rombels as $r): 
                                            $total_l += $r->laki_laki;
                                            $total_p += $r->perempuan;
                                            $total_j += $r->jumlah;
                                        ?>
                                            <tr>
                                                <td><span class="text-secondary-light fw-medium"><?php echo html_escape($r->nama_rombel); ?></span></td>
                                                <td class="text-center"><span class="text-secondary-light"><?php echo number_format($r->laki_laki); ?></span></td>
                                                <td class="text-center"><span class="text-secondary-light"><?php echo number_format($r->perempuan); ?></span></td>
                                                <td class="text-center"><span class="fw-semibold text-primary-light"><?php echo number_format($r->jumlah); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-neutral-50">
                                            <td><span class="fw-bold text-primary-light">Total</span></td>
                                            <td class="text-center"><span class="fw-bold text-primary-light"><?php echo number_format($total_l); ?></span></td>
                                            <td class="text-center"><span class="fw-bold text-primary-light"><?php echo number_format($total_p); ?></span></td>
                                            <td class="text-center"><span class="fw-bold text-primary-main"><?php echo number_format($total_j); ?></span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

        <!-- ================== Second Row Cards Start ======================= -->
        <!-- Top Categories Card Start -->

        <div class="col-xxl-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Top Categories</h6>
                        <a href="javascript:void(0)"
                            class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-info-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon1.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">Web Development</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-success-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon2.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">Graphic Design</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-lilac-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon3.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">UI/UX Design</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-warning-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon4.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">Digital Marketing</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-danger-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon5.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">3d Illustration & Art Design</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-0">
                        <div class="d-flex align-items-center gap-12">
                            <div
                                class="w-40-px h-40-px radius-8 flex-shrink-0 bg-primary-50 d-flex justify-content-center align-items-center">
                                <img src="<?php echo $url->assets ?>images/home-six/category-icon6.png" alt="" class="">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-normal">Logo Design</h6>
                                <span class="text-sm text-secondary-light fw-normal">40+ Courses</span>
                            </div>
                        </div>
                        <a href="#"
                            class="w-24-px h-24-px bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center text-lg bg-hover-primary-100 radius-4">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <!-- Top Categories Card End -->


        <!-- Student Progress Card Start -->
        <div class="col-xxl-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Student's Progress</h6>
                        <a href="javascript:void(0)"
                            class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img1.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Theresa Webb</h6>
                                <span class="text-sm text-secondary-light fw-medium">UI/UX Design
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="33" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">33</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img2.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Robert Fox</h6>
                                <span class="text-sm text-secondary-light fw-medium">Graphic Design
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="70" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">70</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img3.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Guy Hawkins</h6>
                                <span class="text-sm text-secondary-light fw-medium">Web developer
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="80" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">80</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img4.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Cody Fisher</h6>
                                <span class="text-sm text-secondary-light fw-medium">UI/UX Design
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="20" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">20</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img5.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Jacob Jones</h6>
                                <span class="text-sm text-secondary-light fw-medium">UI/UX Design
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="40" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">40</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-0">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $url->assets ?>images/home-six/student-img6.png" alt=""
                                class="w-40-px h-40-px radius-8 flex-shrink-0 me-12 overflow-hidden">
                            <div class="flex-grow-1">
                                <h6 class="text-md mb-0 fw-medium">Darlene Robertson</h6>
                                <span class="text-sm text-secondary-light fw-medium">UI/UX Design
                                    Course</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="text-primary-light text-sm d-block text-end">
                                <svg class="radial-progress" data-percentage="24" viewBox="0 0 80 80">
                                    <circle class="incomplete" cx="40" cy="40" r="35"></circle>
                                    <circle class="complete" cx="40" cy="40" r="35"
                                        style="stroke-dashoffset: 39.58406743523136;"></circle>
                                    <text class="percentage" x="50%" y="57%"
                                        transform="matrix(0, 1, -1, 0, 80, 0)">24</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Student Progress Card End -->
        <!-- ================== Second Row Cards End ======================= -->


        <!-- ================== Third Row Cards Start ======================= -->


        <!-- ================== Third Row Cards End ======================= -->

    </div>

</div>

<?php include viewPath('includes/footer'); ?>
<script>
    // ===================== Average Enrollment Rate Start =============================== 
    function createChartTwo(chartId, color1, color2) {
        var options = {
            series: [{
                name: 'series1',
                data: [48, 35, 55, 32, 48, 30, 55, 50, 57]
            }, {
                name: 'series2',
                data: [12, 20, 15, 26, 22, 60, 40, 48, 25]
            }],
            legend: {
                show: false
            },
            chart: {
                type: 'area',
                width: '100%',
                height: 270,
                toolbar: {
                    show: false
                },
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [color1, color2], // Use two colors for the lines
                lineCap: 'round'
            },
            grid: {
                show: true,
                borderColor: '#D1D5DB',
                strokeDashArray: 1,
                position: 'back',
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                row: {
                    colors: undefined,
                    opacity: 0.5
                },
                column: {
                    colors: undefined,
                    opacity: 0.5
                },
                padding: {
                    top: -20,
                    right: 0,
                    bottom: -10,
                    left: 0
                },
            },
            fill: {
                type: 'gradient',
                colors: [color1, color2], // Use two colors for the gradient
                // gradient: {
                //     shade: 'light',
                //     type: 'vertical',
                //     shadeIntensity: 0.5,
                //     gradientToColors: [`${color1}`, `${color2}00`], // Bottom gradient colors with transparency
                //     inverseColors: false,
                //     opacityFrom: .6,
                //     opacityTo: 0.3,
                //     stops: [0, 100],
                // },
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: [undefined, `${color2}00`], // Apply transparency to both colors
                    inverseColors: false,
                    opacityFrom: [0.4, 0.4], // Starting opacity for both colors
                    opacityTo: [0.3, 0.3], // Ending opacity for both colors
                    stops: [0, 100],
                },
            },
            markers: {
                colors: [color1, color2], // Use two colors for the markers
                strokeWidth: 3,
                size: 0,
                hover: {
                    size: 10
                }
            },
            xaxis: {
                labels: {
                    show: false
                },
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                tooltip: {
                    enabled: false
                },
                labels: {
                    formatter: function(value) {
                        return value;
                    },
                    style: {
                        fontSize: "14px"
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return "$" + value + "k";
                    },
                    style: {
                        fontSize: "14px"
                    }
                },
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
        chart.render();
    }

    createChartTwo('enrollmentChart', '#45B369', '#487fff');
    // ===================== Average Enrollment Rate End =============================== 


    // ================================ Users Overview Donut chart Start ================================ 
    var options = {
        series: [<?php echo $total_smp; ?>, <?php echo $total_sma; ?>, <?php echo $total_ponpes; ?>],
        colors: ['#45B369', '#FF9F29', '#487FFF'],
        labels: ['Siswa SMP', 'Siswa SMA', 'Santri Ponpes'],
        legend: {
            show: false
        },
        chart: {
            type: 'donut',
            height: 270,
            sparkline: {
                enabled: true // Remove whitespace
            },
            margin: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        stroke: {
            width: 0,
        },
        dataLabels: {
            enabled: false
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
    };

    var chart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), options);
    chart.render();
    // ================================ Users Overview Donut chart End ================================ 

    // ================================ Client Payment Status chart End ================================ 
    var options = {
        series: [{
            name: 'Siswa SMP',
            data: <?php echo json_encode($smp_data); ?>
        }, {
            name: 'Siswa SMA',
            data: <?php echo json_encode($sma_data); ?>
        }],
        colors: ['#45B369', '#FF9F29'],

        legend: {
            show: false
        },
        chart: {
            type: 'bar',
            height: 270,
            toolbar: {
                show: false
            },
        },
        grid: {
            show: true,
            borderColor: '#D1D5DB',
            strokeDashArray: 4, // Use a number for dashed style
            position: 'back',
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: 15,
            },
        },
        dataLabels: {
            enabled: false
        },
        states: {
            hover: {
                filter: {
                    type: 'none'
                }
            }
        },
        stroke: {
            show: true,
            width: 0,
            colors: ['transparent']
        },
        xaxis: {
            categories: <?php echo json_encode($years); ?>,
        },
        fill: {
            opacity: 1,
        },
    };

    var chart = new ApexCharts(document.querySelector("#paymentStatusChart"), options);
    chart.render();
    // ================================ Client Payment Status chart End ================================ 

    // ================================ Aminated Radial Progress Bar Start ================================ 
    $('svg.radial-progress').each(function(index, value) {
        $(this).find($('circle.complete')).removeAttr('style');
    });

    // Activate progress animation on scroll
    $(window).scroll(function() {
        $('svg.radial-progress').each(function(index, value) {
            // If svg.radial-progress is approximately 25% vertically into the window when scrolling from the top or the bottom
            if (
                $(window).scrollTop() > $(this).offset().top - ($(window).height() * 0.75) &&
                $(window).scrollTop() < $(this).offset().top + $(this).height() - ($(window)
                    .height() * 0.25)
            ) {
                // Get percentage of progress
                percent = $(value).data('percentage');
                // Get radius of the svg's circle.complete
                radius = $(this).find($('circle.complete')).attr('r');
                // Get circumference (2πr)
                circumference = 2 * Math.PI * radius;
                // Get stroke-dashoffset value based on the percentage of the circumference
                strokeDashOffset = circumference - ((percent * circumference) / 100);
                // Transition progress for 1.25 seconds
                $(this).find($('circle.complete')).animate({
                    'stroke-dashoffset': strokeDashOffset
                }, 1250);
            }
        });
    }).trigger('scroll');
    // ================================ Aminated Radial Progress Bar End ================================ 

    // Realtime Clock & Date Update Script (Server Time Synchronized UTC+7 WIB)
    (function() {
        var serverStartMs = <?php echo (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->getTimestamp() * 1000; ?>;
        var clientStartMs = Date.now();

        function updateRealtimeClock() {
            var elapsed = Date.now() - clientStartMs;
            var serverNow = new Date(serverStartMs + elapsed);

            // Convert to UTC+7 (WIB)
            var utcMs = serverNow.getTime() + (serverNow.getTimezoneOffset() * 60000);
            var wibDate = new Date(utcMs + (7 * 3600000));

            var hours = String(wibDate.getHours()).padStart(2, '0');
            var minutes = String(wibDate.getMinutes()).padStart(2, '0');
            var seconds = String(wibDate.getSeconds()).padStart(2, '0');
            var clockStr = hours + ':' + minutes + ':' + seconds + ' WIB';

            var hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var bulanNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            var hari = hariNames[wibDate.getDay()];
            var tgl = wibDate.getDate();
            var bulan = bulanNames[wibDate.getMonth()];
            var tahun = wibDate.getFullYear();
            var dateStr = hari + ', ' + tgl + ' ' + bulan + ' ' + tahun;

            var clockEls = document.querySelectorAll('.realtime-clock-display');
            for (var i = 0; i < clockEls.length; i++) {
                clockEls[i].textContent = clockStr;
            }

            var dateEls = document.querySelectorAll('.realtime-date-display');
            for (var j = 0; j < dateEls.length; j++) {
                dateEls[j].textContent = dateStr;
            }
        }

        updateRealtimeClock();
        setInterval(updateRealtimeClock, 1000);
    })();
</script>