<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->

<?php
$foto_ptk = (!empty($row->foto) && $row->foto !== 'default.png') ? url('uploads/ptk_foto/' . $row->foto) : $url->assets . 'images/user-grid/guru.png';
if (empty($row->id_ptk)) {
    $foto_ptk = userProfile($user->id);
}
?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                <img src="<?php echo $url->assets ?>images/user-grid/user-grid-bg-guru.jpg" alt="" class="w-100 object-fit-cover">
                <div class="pb-24 ms-16 mb-24 me-16  mt--100">
                    <div class="text-center border border-top-0 border-start-0 border-end-0 mb-20">
                        <img src="<?php echo $foto_ptk ?>" alt=""
                            class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                        <h6 class="mb-0 mt-16"><?php echo ($row->gelar_depan ? $row->gelar_depan . ' ' : '') . $row->nama_ptk . ($row->gelar_belakang ? ', ' . $row->gelar_belakang : '') ?></h6>
                        <span class="text-secondary-light mb-16"><?php echo $row->penugasan ?> </span><br>
                        <span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white"><?php echo $row->status_pegawai ?></span>
                        <span class="badge text-sm fw-semibold bg-dark-info-gradient px-20 py-9 radius-4 text-white  mb-20">Guru Mapel</span>
                    </div>
                    <div class="mt-24">
                        <h6 class="text-xl mb-16">Data Pribadi</h6>
                        <ul>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Nama</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->nama_ptk ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">NIY</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->niy ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> NIK</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->nik ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Email</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->email ?> </span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> No HP</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->telepon ?> &nbsp
                                    <a class="text-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Kirim Pesan Whatsapp">
                                        <iconify-icon icon="tabler:brand-whatsapp-filled" class="text-md"></iconify-icon>
                                    </a>
                                </span>

                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Tempat Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->tempat_lahir ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Tgl. Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo date('d F Y', strtotime($row->tanggal_lahir)) ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light"> TMT</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo ($row->tgl_sk_pengangkatan ? date('d F Y', strtotime($row->tgl_sk_pengangkatan)) : '-') ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1">
                                <span class="w-40 text-md fw-semibold text-primary-light"> Status Dapodik</span>
                                <span class="w-60 text-secondary-light fw-medium">: Belum Masuk Dapodik
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
                            <button class="nav-link d-flex align-items-center px-24" id="pills-riwayat-pendidikan-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-riwayat-pendidikan" type="button" role="tab"
                                aria-controls="pills-riwayat-pendidikan" aria-selected="false" tabindex="-1">
                                Riwayat Pendidikan
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-password-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-password" type="button" role="tab"
                                aria-controls="pills-password" aria-selected="false" tabindex="-1">
                                Keamanan
                            </button>
                        </li>
                        
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Profil -->
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class="card radius-12 h-100 shadow">

                                <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                    <h6 class="text-lg mb-0">Profil PTK</h6>
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
                                                            <?php echo $row->nama_ptk ?>
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
                                                            <?php echo $row->jenis_kelamin ?>
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
                                                            <?php echo $row->tempat_lahir . ', ' . date('d F Y', strtotime($row->tanggal_lahir)) ?>
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
                                                            <?php echo $row->agama ?>
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
                                                            Status Perkawinan :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo $row->status_perkawinan ?>
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
                                                            Nama Ibu Kandung :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo $row->nama_ibu_kandung ?>
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
                                                            NIK :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo $row->nik ?>
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
                                                            NUPTK :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo ($row->nuptk ? $row->nuptk : '-') ?>
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
                                                            SK Pengangkatan :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo ($row->no_sk_pengangkatan ? $row->no_sk_pengangkatan : '-') ?>
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
                                                            TMT Pengangkatan :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo ($row->tgl_sk_pengangkatan ? date('d F Y', strtotime($row->tgl_sk_pengangkatan)) : '-') ?>
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
                                                            Sekolah Induk :
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
                                                            Alamat :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo $row->alamat . ' RT ' . $row->rt . ' RW ' . $row->rw . ' Desa ' . $row->kelurahan_desa . ' Kec. ' . $row->kecamatan . ' Kab. ' . $row->kabupaten . ' Prov. ' . $row->provinsi ?>
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
                                                            Status Kepegawaian :
                                                        </span><br>
                                                        <span class="form-check-label line-height-1 fw-semibold text-primary-light ">
                                                            <?php echo $row->penugasan . ' (' . $row->status_pegawai . ')' ?>
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

                            



                        </div>
                        <!-- End of Profil -->

                        <!-- Riwayat Pendidikan -->
                        <div class="tab-pane fade" id="pills-riwayat-pendidikan" role="tabpanel" aria-labelledby="pills-riwayat-pendidikan-tab" tabindex="0">
                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Riwayat Pendidikan</h6>
                                            <button type="button" class="btn btn-sm btn-success-100 text-success" data-bs-toggle="modal" data-bs-target="#modalTambahPendidikan">
                                                <iconify-icon icon="ri:add-line" class="text-xl"></iconify-icon> Tambah
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table w-100" id="riwayatPendidikanTable" data-page-length='10' width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Jenjang</th>
                                                            <th scope="col">Satuan Pendidikan</th>
                                                            <th scope="col">Jurusan</th>
                                                            <th scope="col">Tahun</th>
                                                            <th scope="col">No Ijazah</th>
                                                            <th scope="col">Ijazah</th>
                                                            <th scope="col" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($riwayat_pendidikan)): ?>
                                                            <?php $no_pendidikan = 1;
                                                            foreach ($riwayat_pendidikan as $pendidikan): ?>
                                                                <tr>
                                                                    <td><?php echo $no_pendidikan++ ?></td>
                                                                    <td><?php echo $pendidikan->jenjang ?></td>
                                                                    <td><?php echo $pendidikan->satuan_pendidikan ?></td>
                                                                    <td><?php echo $pendidikan->jurusan ?: '-' ?></td>
                                                                    <td>
                                                                        <?php echo ($pendidikan->tahun_masuk ? $pendidikan->tahun_masuk : '-') . ' - ' . ($pendidikan->tahun_lulus ? $pendidikan->tahun_lulus : '-') ?>
                                                                    </td>
                                                                    <td><?php echo $pendidikan->no_ijazah ?: '-' ?></td>
                                                                    <td>
                                                                        <?php if (!empty($pendidikan->berkas)): ?>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-info-100 text-info-600 radius-8 px-12 py-6 d-flex align-items-center gap-2 btn-lihat-dokumen"
                                                                                data-bs-toggle="modal" data-bs-target="#detailIjazah"
                                                                                data-file="<?php echo url('uploads/ptk_dokumen_pribadi/' . $pendidikan->berkas) ?>">
                                                                                <iconify-icon icon="bi:display-fill"></iconify-icon> Lihat
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-warning-100 text-warning-600 radius-8 px-12 py-6 d-flex align-items-center gap-2 btn-upload-ijazah"
                                                                                data-bs-toggle="modal" data-bs-target="#modalUploadPendidikan"
                                                                                data-action="<?php echo url('ptk/ptkPendidikanUpload/' . $pendidikan->id_pendidikan) ?>">
                                                                                <iconify-icon icon="ri:upload-2-line"></iconify-icon> Upload
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                            <button type="button"
                                                                                class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle btn-edit-pendidikan"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalEditPendidikan"
                                                                                data-action="<?php echo url('ptk/ptkPendidikanUpdate/' . $pendidikan->id_pendidikan) ?>"
                                                                                data-jenjang="<?php echo htmlspecialchars($pendidikan->jenjang, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-satuan="<?php echo htmlspecialchars($pendidikan->satuan_pendidikan, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-jurusan="<?php echo htmlspecialchars($pendidikan->jurusan, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-tahun-masuk="<?php echo htmlspecialchars($pendidikan->tahun_masuk, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-tahun-lulus="<?php echo htmlspecialchars($pendidikan->tahun_lulus, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-tanggal-lulus="<?php echo htmlspecialchars($pendidikan->tanggal_lulus, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-no-ijazah="<?php echo htmlspecialchars($pendidikan->no_ijazah, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-keterangan="<?php echo htmlspecialchars($pendidikan->keterangan, ENT_QUOTES, 'UTF-8') ?>">
                                                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                                            </button>
                                                                            <?php if (!empty($pendidikan->berkas)): ?>
                                                                                <button type="button"
                                                                                    class="bg-warning-100 text-warning-600 bg-hover-warning-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle btn-upload-ijazah"
                                                                                    data-bs-toggle="modal" data-bs-target="#modalUploadPendidikan"
                                                                                    data-action="<?php echo url('ptk/ptkPendidikanUpload/' . $pendidikan->id_pendidikan) ?>"
                                                                                    data-bs-toggle="tooltip" data-bs-title="Ganti Ijazah">
                                                                                    <iconify-icon icon="ri:refresh-line" class="menu-icon"></iconify-icon>
                                                                                </button>
                                                                            <?php endif; ?>
                                                                            <a href="<?php echo url('ptk/ptkPendidikanHapus/' . $pendidikan->id_pendidikan) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="return confirm('Hapus riwayat pendidikan ini?')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" data-bs-title="Hapus Riwayat Pendidikan">
                                                                                <iconify-icon icon="mingcute:delete-2-line" class="menu-icon"></iconify-icon>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="card-footer text-center bg-transparent border border-end-0 border-start-0 border-bottom-0 py-16 px-24 ">
                                                <!-- <a href="javascript:void(0)" class="btn btn-success text-light  ">
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
                                                </a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Riwayat Pendidikan -->

                        <!-- Arsip -->
                        <div class="tab-pane fade" id="pills-arsip" role="tabpanel" aria-labelledby="pills-arsip-tab" tabindex="0">
                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card basic-data-table shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">Data Pribadi</h6>
                                            <button type="button" class="btn btn-sm btn-success-100 text-success" data-bs-toggle="modal" data-bs-target="#modalTambahDokumenPribadi">
                                                <iconify-icon icon="ri:add-line" class="text-xl"></iconify-icon> Tambah
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table " id="dataPribadi" data-page-length='5' style="width:100% !important">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Jenis Dokumen</th>
                                                            <th scope="col">Nomor Dokumen</th>
                                                            <th scope="col">Tanggal Dokumen</th>
                                                            <th scope="col" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($dokumen_pribadi)): ?>
                                                            <?php $no_dokumen = 1;
                                                            foreach ($dokumen_pribadi as $dokumen): ?>
                                                                <tr>
                                                                    <td><?php echo $no_dokumen++ ?></td>
                                                                    <td><?php echo $dokumen->nama_jenis_dokumen ?: '-' ?></td>
                                                                    <td><?php echo $dokumen->nomor_dokumen ?: '-' ?></td>
                                                                    <td><?php echo $dokumen->tanggal_dokumen ? date('d F Y', strtotime($dokumen->tanggal_dokumen)) : '-' ?></td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                                            <button type="button"
                                                                                class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2 btn-lihat-dokumen"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#detailIjazah"
                                                                                data-file="<?php echo url('uploads/ptk_dokumen_pribadi/' . $dokumen->berkas) ?>">
                                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon> Lihat
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn rounded-pill btn-success-100 text-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2 btn-edit-dokumen"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalEditDokumenPribadi"
                                                                                data-action="<?php echo url('ptk/ptkDokumenUpdate/' . $dokumen->id_dokumen) ?>"
                                                                                data-id-jenis="<?php echo htmlspecialchars($dokumen->id_jenis_dokumen, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-nomor="<?php echo htmlspecialchars($dokumen->nomor_dokumen, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-tanggal="<?php echo htmlspecialchars($dokumen->tanggal_dokumen, ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-keterangan="<?php echo htmlspecialchars($dokumen->keterangan, ENT_QUOTES, 'UTF-8') ?>">
                                                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon> Sunting
                                                                            </button>
                                                                            <a href="<?php echo url('ptk/ptkDokumenHapus/' . $dokumen->id_dokumen) ?>" class="btn rounded-pill btn-danger-100 text-danger-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" onclick="return confirm('Hapus dokumen ini?')">
                                                                                <iconify-icon icon="mingcute:delete-2-line" class="menu-icon"></iconify-icon> Hapus
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card basic-data-table shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">SK Pengangkatan</h6>
                                            <button type="button" class="text-xl line-height-1">
                                                <iconify-icon icon="material-symbols:book-5-rounded" class="text-xl"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table " id="skPengangkatan" data-page-length='5' style="width:100% !important">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Nama Dokumen</th>
                                                            <th scope="col">Tanggal Dokumen</th>
                                                            <th scope="col">Penandatangan</th>
                                                            <th scope="col">Jenis TTD</th>
                                                            <th scope="col" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        


                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-xl-12">
                                    <div class="card basic-data-table shadow">
                                        <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0">
                                            <h6 class="text-lg mb-0">SK Tugas Mengajar</h6>
                                            <button type="button" class="text-xl line-height-1">
                                                <iconify-icon icon="material-symbols:book-5-rounded" class="text-xl"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table bordered-table " id="skTugas" data-page-length='5' style="width:100% !important">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">No</th>
                                                            <th scope="col">Nama Dokumen</th>
                                                            <th scope="col">Tanggal Dokumen</th>
                                                            <th scope="col">Penandatangan</th>
                                                            <th scope="col">Jenis TTD</th>
                                                            <th scope="col" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                       

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
                            <?php include viewPath('ptk/partials/v_ptk_setting_form'); ?>
                        </div>
                        <div class="tab-pane fade" id="pills-password" role="tabpanel" aria-labelledby="pills-password-tab" tabindex="0">
                            <div class="card radius-12 h-100 shadow">
                                <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                                    <h6 class="text-lg mb-0">Keamanan Akun (Reset Password oleh Admin)</h6>
                                </div>
                                <div class="card-body p-24">
                                    <?php echo form_open('ptk/ptkUpdatePassword/' . $row->id_ptk, ['method' => 'POST', 'autocomplete' => 'off', 'class' => 'form-validate']); ?>
                                        <div class="alert bg-warning-focus text-warning-main border border-warning-200 px-16 py-12 radius-8 mb-16 d-flex align-items-start gap-2">
                                            <iconify-icon icon="lucide:alert-triangle" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                                            <div>
                                                <div class="fw-semibold">Perhatian!</div>
                                                <div class="text-sm">Ubah password ini akan memperbarui kredensial masuk PTK bersangkutan secara langsung.</div>
                                            </div>
                                        </div>

                                        <div class="alert bg-info-focus text-info-main border border-info-200 px-16 py-12 radius-8 mb-24 d-flex align-items-start gap-2">
                                            <iconify-icon icon="lucide:info" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                                            <div>
                                                <div class="fw-semibold">Aturan Password</div>
                                                <div class="text-sm">Password minimal terdiri dari 6 karakter.</div>
                                            </div>
                                        </div>

                                        <div class="row gy-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Password Baru</label>
                                                <input type="password" class="form-control radius-8" placeholder="Password Baru" minlength="6" name="password" required id="password" />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ulangi Password Baru</label>
                                                <input type="password" class="form-control radius-8" equalTo="#password" placeholder="Konfirmasi Password Baru" required name="password_confirm" />
                                            </div>
                                            <div class="col-12 mt-24">
                                                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Ganti Password</button>
                                            </div>
                                        </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                        <!-- End Keamanan -->

                        


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Riwayat Pendidikan -->
<div class="modal fade" id="modalTambahPendidikan" tabindex="-1" aria-labelledby="modalTambahPendidikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="<?php echo url('ptk/ptkPendidikanSimpan/' . $row->id_ptk) ?>" method="post">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="modalTambahPendidikanLabel">Tambah Riwayat Pendidikan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenjang <span class="text-danger-600">*</span></label>
                                <select class="form-control radius-8 form-select" name="jenjang" required>
                                    <option value="">Pilih Jenjang</option>
                                    <option value="SD/MI">SD/MI</option>
                                    <option value="SMP/MTs">SMP/MTs</option>
                                    <option value="SMA/MA/SMK">SMA/MA/SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4/S1">D4/S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Satuan Pendidikan <span class="text-danger-600">*</span></label>
                                <input type="text" class="form-control radius-8" name="satuan_pendidikan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jurusan</label>
                                <input type="text" class="form-control radius-8" name="jurusan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Masuk</label>
                                <input type="number" class="form-control radius-8" name="tahun_masuk" min="1900" max="2100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Lulus</label>
                                <input type="number" class="form-control radius-8" name="tahun_lulus" min="1900" max="2100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lulus</label>
                                <input type="date" class="form-control radius-8" name="tanggal_lulus">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">No Ijazah</label>
                                <input type="text" class="form-control radius-8" name="no_ijazah">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label>
                                <textarea class="form-control radius-8" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-light radius-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Tambah Riwayat Pendidikan -->

<!-- Modal Edit Riwayat Pendidikan -->
<div class="modal fade" id="modalEditPendidikan" tabindex="-1" aria-labelledby="modalEditPendidikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="#" method="post" id="formEditPendidikan">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="modalEditPendidikanLabel">Sunting Riwayat Pendidikan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenjang <span class="text-danger-600">*</span></label>
                                <select class="form-control radius-8 form-select" name="jenjang" id="edit_jenjang" required>
                                    <option value="">Pilih Jenjang</option>
                                    <option value="SD/MI">SD/MI</option>
                                    <option value="SMP/MTs">SMP/MTs</option>
                                    <option value="SMA/MA/SMK">SMA/MA/SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4/S1">D4/S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Satuan Pendidikan <span class="text-danger-600">*</span></label>
                                <input type="text" class="form-control radius-8" name="satuan_pendidikan" id="edit_satuan_pendidikan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jurusan</label>
                                <input type="text" class="form-control radius-8" name="jurusan" id="edit_jurusan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Masuk</label>
                                <input type="number" class="form-control radius-8" name="tahun_masuk" id="edit_tahun_masuk" min="1900" max="2100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Lulus</label>
                                <input type="number" class="form-control radius-8" name="tahun_lulus" id="edit_tahun_lulus" min="1900" max="2100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lulus</label>
                                <input type="date" class="form-control radius-8" name="tanggal_lulus" id="edit_tanggal_lulus">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">No Ijazah</label>
                                <input type="text" class="form-control radius-8" name="no_ijazah" id="edit_no_ijazah">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label>
                                <textarea class="form-control radius-8" name="keterangan" id="edit_keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-light radius-8">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Edit Riwayat Pendidikan -->

<!-- Modal Tambah Dokumen Pribadi -->
<div class="modal fade" id="modalTambahDokumenPribadi" tabindex="-1" aria-labelledby="modalTambahDokumenPribadiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="<?php echo url('ptk/ptkDokumenSimpan/' . $row->id_ptk) ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="modalTambahDokumenPribadiLabel">Tambah Dokumen Pribadi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Dokumen <span class="text-danger-600">*</span></label>
                                <select class="form-control radius-8 form-select select-jenis-dokumen" name="id_jenis_dokumen" required>
                                    <option value="">Pilih Jenis Dokumen</option>
                                    <?php foreach ($jenis_dokumen as $jenis): ?>
                                        <option value="<?php echo $jenis->id_jenis_dokumen ?>"><?php echo $jenis->nama_jenis_dokumen ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tambah Jenis</label>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control radius-8 input-jenis-dokumen-baru" placeholder="Contoh: NPWP">
                                    <button type="button" class="btn btn-info text-light radius-8 btn-tambah-jenis-dokumen">Tambah</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen</label>
                                <input type="text" class="form-control radius-8" name="nomor_dokumen">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Dokumen</label>
                                <input type="date" class="form-control radius-8" name="tanggal_dokumen">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Upload Berkas <span class="text-danger-600">*</span></label>
                                <input type="file" class="form-control radius-8 scan-enabled" name="berkas" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-secondary-light">Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label>
                                <textarea class="form-control radius-8" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-light radius-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Tambah Dokumen Pribadi -->

<!-- Modal Edit Dokumen Pribadi -->
<div class="modal fade" id="modalEditDokumenPribadi" tabindex="-1" aria-labelledby="modalEditDokumenPribadiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="#" method="post" enctype="multipart/form-data" id="formEditDokumenPribadi">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="modalEditDokumenPribadiLabel">Sunting Dokumen Pribadi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Dokumen <span class="text-danger-600">*</span></label>
                                <select class="form-control radius-8 form-select select-jenis-dokumen" name="id_jenis_dokumen" id="edit_id_jenis_dokumen" required>
                                    <option value="">Pilih Jenis Dokumen</option>
                                    <?php foreach ($jenis_dokumen as $jenis): ?>
                                        <option value="<?php echo $jenis->id_jenis_dokumen ?>"><?php echo $jenis->nama_jenis_dokumen ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tambah Jenis</label>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control radius-8 input-jenis-dokumen-baru" placeholder="Contoh: NPWP">
                                    <button type="button" class="btn btn-info text-light radius-8 btn-tambah-jenis-dokumen">Tambah</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen</label>
                                <input type="text" class="form-control radius-8" name="nomor_dokumen" id="edit_nomor_dokumen">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Dokumen</label>
                                <input type="date" class="form-control radius-8" name="tanggal_dokumen" id="edit_tanggal_dokumen">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ganti Berkas</label>
                                <input type="file" class="form-control radius-8 scan-enabled" name="berkas" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-secondary-light">Kosongkan jika berkas tidak diganti. Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-20">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label>
                                <textarea class="form-control radius-8" name="keterangan" id="edit_keterangan_dokumen" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-light radius-8">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Edit Dokumen Pribadi -->

<!-- Modal Upload Ijazah -->
<div class="modal fade" id="modalUploadPendidikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="#" method="post" enctype="multipart/form-data" id="formUploadPendidikan">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5">Unggah Berkas Ijazah</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <input type="hidden" name="upload_ijazah" value="1">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Berkas <span class="text-danger-600">*</span></label>
                        <input type="file" class="form-control radius-8 scan-enabled" name="berkas" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-secondary-light">Format: PDF, JPG, PNG. Maksimal 5 MB.</small>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-light radius-8">Mulai Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal Upload Ijazah -->

<!-- Modal Lihat Berkas -->
<div class="modal fade" id="detailIjazah" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Lihat Berkas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <object style="width: 100%;height: 100%;" data="" id="pdf_content">
                    <iframe src="" id="pdf_frame" style="width: 100%; height: 100%;"></iframe>
                </object>
                <div id="image_container" class="text-center h-100" style="display: none; overflow: auto;">
                    <img src="" id="image_view" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0px 4px 12px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Modal Lihat Berkas -->

<?php include viewPath('includes/footer'); ?>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-dapo').each(function() {
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
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
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

    $('.btn-edit-pendidikan').on('click', function() {
        $('#formEditPendidikan').attr('action', $(this).data('action'));
        $('#edit_jenjang').val($(this).data('jenjang'));
        $('#edit_satuan_pendidikan').val($(this).data('satuan'));
        $('#edit_jurusan').val($(this).data('jurusan'));
        $('#edit_tahun_masuk').val($(this).data('tahun-masuk'));
        $('#edit_tahun_lulus').val($(this).data('tahun-lulus'));
        $('#edit_tanggal_lulus').val($(this).data('tanggal-lulus'));
        $('#edit_no_ijazah').val($(this).data('no-ijazah'));
        $('#edit_keterangan').val($(this).data('keterangan'));
    });

    $('.btn-edit-dokumen').on('click', function() {
        $('#formEditDokumenPribadi').attr('action', $(this).data('action'));
        $('#edit_id_jenis_dokumen').val($(this).data('id-jenis'));
        $('#edit_nomor_dokumen').val($(this).data('nomor'));
        $('#edit_tanggal_dokumen').val($(this).data('tanggal'));
        $('#edit_keterangan_dokumen').val($(this).data('keterangan'));
    });

    $('.btn-upload-ijazah').on('click', function() {
        $('#formUploadPendidikan').attr('action', $(this).data('action'));
    });

    $('.btn-lihat-dokumen').on('click', function() {
        const fileUrl = $(this).data('file');
        const extension = fileUrl.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            $('#pdf_content').hide();
            $('#image_container').show();
            $('#image_view').attr('src', fileUrl);
        } else {
            $('#image_container').hide();
            $('#pdf_content').show();
            $('#pdf_content').attr('data', fileUrl);
            $('#pdf_frame').attr('src', fileUrl);
        }
    });

    $('.btn-tambah-jenis-dokumen').on('click', function() {
        const wrapper = $(this).closest('.row');
        const input = wrapper.find('.input-jenis-dokumen-baru');
        const nama = input.val().trim();

        if (!nama) {
            alert('Nama jenis dokumen wajib diisi');
            return;
        }

        $.post('<?php echo url('ptk/ptkJenisDokumenSimpan') ?>', {
            nama_jenis_dokumen: nama
        }, function(response) {
            if (!response.status) {
                alert(response.message);
                return;
            }

            $('.select-jenis-dokumen').each(function() {
                if ($(this).find('option[value="' + response.id + '"]').length === 0) {
                    $(this).append(new Option(response.nama, response.id));
                }
            });

            wrapper.find('.select-jenis-dokumen').val(response.id);
            input.val('');
        }, 'json').fail(function() {
            alert('Jenis dokumen gagal ditambahkan');
        });
    });

    $('#setting_provinsi').on('change', function() {
        var id_prov = $(this).val();
        if (id_prov) {
            $.ajax({
                url: "<?php echo url('ptk/getKabupaten') ?>",
                type: "POST",
                data: {
                    id: id_prov
                },
                dataType: "json",
                success: function(data) {
                    $('#setting_kabupaten').html('<option value="">Pilih Kabupaten</option>');
                    $.each(data, function(key, value) {
                        $('#setting_kabupaten').append('<option value="' + value.id_kab + '">' + value.nama + '</option>');
                    });
                    $('#setting_kecamatan').html('<option value="">Pilih Kecamatan</option>');
                    $('#setting_kelurahan_desa').html('<option value="">Pilih Kelurahan</option>');
                }
            });
        }
    });

    $('#setting_kabupaten').on('change', function() {
        var id_kab = $(this).val();
        if (id_kab) {
            $.ajax({
                url: "<?php echo url('ptk/getKecamatan') ?>",
                type: "POST",
                data: {
                    id: id_kab
                },
                dataType: "json",
                success: function(data) {
                    $('#setting_kecamatan').html('<option value="">Pilih Kecamatan</option>');
                    $.each(data, function(key, value) {
                        $('#setting_kecamatan').append('<option value="' + value.id_kec + '">' + value.nama + '</option>');
                    });
                    $('#setting_kelurahan_desa').html('<option value="">Pilih Kelurahan</option>');
                }
            });
        }
    });

    $('#setting_kecamatan').on('change', function() {
        var id_kec = $(this).val();
        if (id_kec) {
            $.ajax({
                url: "<?php echo url('ptk/getKelurahan') ?>",
                type: "POST",
                data: {
                    id: id_kec
                },
                dataType: "json",
                success: function(data) {
                    $('#setting_kelurahan_desa').html('<option value="">Pilih Kelurahan</option>');
                    $.each(data, function(key, value) {
                        $('#setting_kelurahan_desa').append('<option value="' + value.id_kel + '">' + value.nama + '</option>');
                    });
                }
            });
        }
    });
</script>


<script>
    $(document).ready(function() {
        if ($('#dataTable').length) { $('#dataTable').DataTable(); }
        if ($('#skPengangkatan').length) { $('#skPengangkatan').DataTable(); }
        if ($('#skTugas').length) { $('#skTugas').DataTable(); }
        if ($('#dataPribadi').length) { $('#dataPribadi').DataTable(); }
        if ($('#riwayatPendidikanTable').length) { $('#riwayatPendidikanTable').DataTable(); }
    });

    // Auto active tab from window hash URL
    var hash = window.location.hash;
    if (hash) {
        $('.nav-pills button[data-bs-target="' + hash + '"]').tab('show');
    }
</script>