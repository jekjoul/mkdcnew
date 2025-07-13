<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

         <!-- Latest Update -->
         <div class="col-md-6 ">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title mb-0">Agenda Kedinasan</h5>

                </div>
                <div class="card-body" style="height:235px; overflow:scroll;">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                            <div
                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">Rapat Koordinasi Kepala Sekolah</p>
                                    <small class="text-default"><strong>Dr. Siti Julaeha, M.Pd.,Gr.</strong></small><br>
                                    <small class="text-muted"><i class='fa fa-location-dot'></i> Dinas Pendidikan Kabupaten Ciamis</small>
                                </div>
                                <div class="item-progress">25 Juli 2025</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                            <div
                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">Bimtek ANBK Operator Sekolah</p>
                                    <small class="text-default"><strong>Zakaria Zulkarnain</strong></small><br>
                                    <small class="text-muted"><i class='fa fa-location-dot'></i> SMP Negeri 1 Panumbangan</small>
                                </div>
                                <div class="item-progress">02 Agustus 2025</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                            <div
                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">Rapat Koordinasi Kepala Sekolah</p>
                                    <small class="text-default"><strong>Dr. Siti Julaeha, M.Pd.,Gr.</strong></small><br>
                                    <small class="text-muted"><i class='fa fa-location-dot'></i> Dinas Pendidikan Kabupaten Ciamis</small>
                                </div>
                                <div class="item-progress">25 Juli 2025</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                            <div
                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">Rapat Koordinasi Kepala Sekolah</p>
                                    <small class="text-default"><strong>Dr. Siti Julaeha, M.Pd.,Gr.</strong></small><br>
                                    <small class="text-muted"><i class='fa fa-location-dot'></i> Dinas Pendidikan Kabupaten Ciamis</small>
                                </div>
                                <div class="item-progress">25 Juli 2025</div>
                            </div>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Latest Update -->
        <!-- Multi Radial Chart -->
        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Ketercapaian Pembelajaran</h5>
                </div>
                <div class="card-body">
                    <div id="visitsRadialChart"></div>
                </div>
            </div>
        </div>
        <!--/ Multi Radial Chart -->

       

        <!-- Statistics cards & Revenue Growth Chart -->
        <div class="col-lg-2 col-12">
            <div class="row">
                <!-- Statistics Cards -->
                <div class="col-12 col-md-12 col-lg-12 mb-12">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-warning">
                                    <i class="bx bx-user fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Jumlah Siswa</span>
                            <h2 class="mb-0">253</h2>
                        </div>
                    </div>
                </div>
              </div>
              <div class="row mt-4">
                <div class="col-12 col-md-12 col-lg-12 mb-12">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                    <i class="fa fa-user-tie fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Jumlah PTK</span>
                            <h2 class="mb-0">18</h2>
                        </div>
                    </div>
                </div>
               
                <!--/ Statistics Cards -->
            </div>
        </div>
        <!--/ Statistics cards & Revenue Growth Chart -->
    </div>

    <div class="row">
        <!-- Kehadiran Donut Chart -->
        <div class="col-md-4 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Kehadiran Hari Ini</h5>
                        <small class="text-muted">Persentase Kehadiran Siswa Hari Ini</small>
                    </div>
                    <div class="dropdown d-none d-sm-flex">
                        <button
                            type="button"
                            class="btn dropdown-toggle px-0"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bx bx-calendar"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body" style="min-height:400px">
                    <div id="donutChart"></div>
                </div>
            </div>

        </div>
        <!-- /Kehadiran Donut Chart -->

        <!-- Line Chart -->
        <div class="col-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Grafik Kehadiran Siswa 15 Hari Terakhir</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>
        <!-- /Line Chart -->
    </div>
    <div class="row mt-4">
        <!-- Statistics cards & Revenue Growth Chart -->
        <div class="col-lg-12 col-12">
            <div class="row">
                <!-- Statistics Cards -->
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="fa fa-users fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Siswa SMP</span>
                            <h2 class="mb-0">200</h2>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-default">
                                <i class="fa fa-users fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Siswa SMA</span>
                            <h2 class="mb-0">90</h2>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="fa fa-users fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Siswa Ponpes</span>
                            <h2 class="mb-0">156</h2>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="fa fa-house-chimney-user fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Rombel SMP</span>
                            <h2 class="mb-0">40</h2>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-default">
                                <i class="fa fa-house-chimney-user fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Rombel SMA</span>
                            <h2 class="mb-0">40</h2>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-md-2 col-lg-2 mb-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="fa fa-house-chimney-user fs-4"></i>
                                </span>
                            </div>
                            <span class="d-block text-nowrap">Rombel Ponpes</span>
                            <h2 class="mb-0">40</h2>
                        </div>
                    </div>
                </div>
                <!--/ Statistics Cards -->
            </div>
        </div>
        <!--/ Statistics cards & Revenue Growth Chart -->
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Status Pembelajaran</h5>
                </div>

                <div class="table-responsive">
                    <table class="table border-top">
                        <thead>
                            <tr>
                                <th>Rombel</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Mapel</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-nowrap">VII - Abatasa</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">VII - Jahakho</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">VIII - Syafi'i</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">VIII - Maliki</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">IX - Sonhaji</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">IX - Az Zarnuji</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">X - Abatasa</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">XI - Hanafi</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">XII - Khalid</td>
                                <td class="text-nowrap">Matematika</td>
                                <td>Zakaria Zulkarnain</td>
                                <td>
                                    <span class="text-success">Sedang Pembelajaran</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn p-0"
                                            type="button"
                                            id="action1"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                            <a class="dropdown-item" href="javascript:void(0);">Details</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Write a Review</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Download Invoice</a>
                                        </div>
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

<?php include viewPath('includes/footer'); ?>
<script src="<?php echo $url->assets ?>vendor/libs/apex-charts/apexcharts.js"></script>
<!-- <script src="<?php echo $url->assets ?>js/charts-apex.js"></script> -->
<script>
    "use strict";
    !function () {
        let e,
            o,
            r,
            s,
            a,
            t;
        t = isDarkStyle
            ? (
                e = config.colors_dark.cardColor,
                o = config.colors_dark.headingColor,
                r = config.colors_dark.textMuted,
                a = config.colors_dark.bodyColor,
                s = config.colors_dark.borderColor,
                "#36435C"
            )
            : (
                e = config.colors.cardColor,
                o = config.colors.headingColor,
                r = config.colors.textMuted,
                a = config.colors.bodyColor,
                s = config.colors.borderColor,
                config.colors_label.secondary
            );
        const l = {
                series1: "#826af9",
                series2: "#d2b0ff",
                bg: "#f8d3ff"
            },
            i = {
                series1: "#3fcc4a",
                series2: "#e9e313",
                series3: "#22d4c6",
                series4: "#db2222"
            },
            n = {
                series1: "#29dac7",
                series2: "#60f2ca",
                series3: "#a5f8cd"
            };

        function c(e, o) {
            let r = 0;
            for (var s = []; r < e;) {
                var a = "w" + (
                        r + 1
                    ).toString(),
                    t = Math.floor(Math.random() * (o.max - o.min + 1)) + o.min;
                s.push({x: a, y: t}),
                r++
            }
            return s
        }
        var d = document.querySelector("#donutChart"),
            h = {
                chart: {
                    height: 390,
                    fontFamily: "IBM Plex Sans",
                    type: "donut"
                },
                labels: [
                    "Hadir", "Tanpa Keterangan", "Sakit", "Izin"
                ],
                series: [
                    80, 20, 5, 5
                ],
                colors: [
                    i.series1, i.series4, i.series3, i.series2
                ],
                stroke: {
                    show: !1,
                    curve: "straight"
                },
                dataLabels: {
                    enabled: !0,
                    formatter: function (e, o) {
                        return parseInt(e) + "%"
                    }
                },
                legend: {
                    show: !0,
                    position: "bottom",
                    labels: {
                        colors: a,
                        useSeriesColors: !1
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: !0,
                                name: {
                                    fontSize: "2rem",
                                    color: a
                                },
                                value: {
                                    fontSize: "1.2rem",
                                    color: a,
                                    fontFamily: "IBM Plex Sans",
                                    formatter: function (e) {
                                        return parseInt(e) + "%"
                                    }
                                },
                                total: {
                                    show: !0,
                                    fontSize: "1.5rem",
                                    color: o,
                                    label: "Hadir",
                                    formatter: function (e) {
                                        return "80%"
                                    }
                                }
                            }
                        }
                    }
                },
                responsive: [
                    {
                        breakpoint: 992,
                        options: {
                            chart: {
                                height: 380
                            },
                            legend: {
                                position: "bottom",
                                labels: {
                                    colors: a,
                                    useSeriesColors: !1
                                }
                            }
                        }
                    }, {
                        breakpoint: 576,
                        options: {
                            chart: {
                                height: 320
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: !0,
                                            name: {
                                                fontSize: "1.5rem"
                                            },
                                            value: {
                                                fontSize: "1rem"
                                            },
                                            total: {
                                                fontSize: "1.5rem"
                                            }
                                        }
                                    }
                                }
                            },
                            legend: {
                                position: "bottom",
                                labels: {
                                    colors: a,
                                    useSeriesColors: !1
                                }
                            }
                        }
                    }, {
                        breakpoint: 420,
                        options: {
                            chart: {
                                height: 280
                            },
                            legend: {
                                show: !1
                            }
                        }
                    }, {
                        breakpoint: 360,
                        options: {
                            chart: {
                                height: 250
                            },
                            legend: {
                                show: !1
                            }
                        }
                    }
                ]
            },
            d = (
                null !== d && new ApexCharts(d, h).render(),
                document.querySelector("#lineChart")
            ),
            h = {
                chart: {
                    height: 400,
                    fontFamily: "IBM Plex Sans",
                    type: "line",
                    parentHeightOffset: 0,
                    zoom: {
                        enabled: !1
                    },
                    toolbar: {
                        show: !1
                    }
                },
                series: [
                    {
                        data: [
                            280,
                            200,
                            220,
                            180,
                            270,
                            250,
                            70,
                            90,
                            200,
                            150,
                            160,
                            100,
                            150,
                            100,
                            50
                        ]
                    }
                ],
                markers: {
                    strokeWidth: 7,
                    strokeOpacity: 1,
                    strokeColors: [config.colors.white],
                    colors: [config.colors.warning]
                },
                dataLabels: {
                    enabled: !1
                },
                stroke: {
                    curve: "straight"
                },
                colors: [config.colors.warning],
                grid: {
                    borderColor: s,
                    xaxis: {
                        lines: {
                            show: !0
                        }
                    },
                    padding: {
                        top: -20
                    }
                },
                tooltip: {
                    custom: function ({series: e, seriesIndex: o, dataPointIndex: r}) {
                        return '<div class="px-3 py-2"><span>' + e[o][r] + "%</span></div>"
                    }
                },
                xaxis: {
                    categories: [
                        "7/12",
                        "8/12",
                        "9/12",
                        "10/12",
                        "11/12",
                        "12/12",
                        "13/12",
                        "14/12",
                        "15/12",
                        "16/12",
                        "17/12",
                        "18/12",
                        "19/12",
                        "20/12",
                        "21/12"
                    ],
                    axisBorder: {
                        show: !1
                    },
                    axisTicks: {
                        show: !1
                    },
                    labels: {
                        style: {
                            colors: r,
                            fontSize: "13px"
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: r,
                            fontSize: "13px"
                        }
                    }
                }
            };
        null !== d && new ApexCharts(d, h).render()
    }();
</script>

<script>
    "use strict";
    !function () {
        let o,
            e,
            r,
            t,
            a,
            s;
        s = isDarkStyle
            ? (
                o = config.colors_dark.cardColor,
                e = config.colors_dark.headingColor,
                r = config.colors_dark.textMuted,
                t = config.colors_dark.bodyColor,
                a = config.colors_dark.borderColor,
                "dark"
            )
            : (
                o = config.colors.white,
                e = config.colors.headingColor,
                r = config.colors.textMuted,
                t = config.colors.bodyColor,
                a = config.colors.borderColor,
                "light"
            );
        var i = document.querySelector("#visitsRadialChart"),
            l = {
                chart: {
                    height: 270,
                    type: "radialBar"
                },
                colors: [
                    config.colors.primary, config.colors.danger, config.colors.warning
                ],
                series: [
                    30, 60, 42
                ],
                plotOptions: {
                    radialBar: {
                        offsetY: -10,
                        hollow: {
                            size: "45%"
                        },
                        track: {
                            margin: 10,
                            background: o
                        },
                        dataLabels: {
                            name: {
                                fontSize: "15px",
                                colors: [t],
                                fontFamily: "IBM Plex Sans",
                                offsetY: 25
                            },
                            value: {
                                fontSize: "2rem",
                                fontFamily: "Rubik",
                                fontWeight: 500,
                                color: e,
                                offsetY: -15
                            },
                            total: {
                                show: !0,
                                label: "Tercapai",
                                fontSize: "15px",
                                fontWeight: 400,
                                fontFamily: "IBM Plex Sans",
                                color: t
                            }
                        }
                    }
                },
                grid: {
                    padding: {
                        top: -10,
                        bottom: -10
                    }
                },
                stroke: {
                    lineCap: "round"
                },
                labels: [
                    "Pembelajaran", "Project", "Penilaian Sumatif"
                ],
                legend: {
                    show: !0,
                    position: "bottom",
                    horizontalAlign: "center",
                    labels: {
                        colors: t,
                        useSeriesColors: !1
                    },
                    itemMargin: {
                        horizontal: 15
                    },
                    markers: {
                        width: 10,
                        height: 10,
                        offsetX: -3
                    }
                }
            };
        null !== i && new ApexCharts(i, l).render()
    }();
</script>