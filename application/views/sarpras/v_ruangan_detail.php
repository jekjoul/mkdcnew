<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div
                    class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400">
                    <div class="text-center" style="width: 100% !important;">
                        <h6>Detail Ruangan <?= $row->nama_ruangan ?></h6>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="row">
                        <div class="col-md-4 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ruangan</label>
                            <p><?= $row->nama_ruangan ?></p>
                        </div>

                        <div class="col-md-4 mb-20 was-validated">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Ruangan</label>
                            <p><?= $row->nama_jenis_ruangan ?></p>

                        </div>

                        <div class="col-md-4 mb-20 was-validated">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Bangunan</label>
                            <p><?= $row->nama_bangunan ?></p>

                        </div>

                        <div class="col-md-4 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Ruangan (m<sup>2</sup>)</label>
                            <p><?= $row->luas_tapak_ruangan ?></p>
                        </div>

                        <div class="col-md-4 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                            <p><?= $row->panjang_ruangan ?></p>
                        </div>

                        <div class="col-md-4 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                            <p><?= $row->lebar_ruangan ?></p>
                        </div>

                        <div class="col-md-4 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas </label>
                            <p><?= $row->kapasitas ?></p>
                        </div>

                        <div class="col-md-4 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Kondisi </label>
                            <p><?= $row->kondisi ?></p>
                        </div>

                        <div class="col-md-4 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                            <p><?= $row->status ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-20">
                        <h6 class="text-center">Sarana</h6>

                        <div class="table-responsive">
                            <table class="table bordered-table" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Jenis Sarana</th>
                                        <th scope="col">Jumlah</th>
                                        <th scope="col" class="text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Kursi Siswa</td>
                                        <td>29 Unit</td>
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
</div>
<?php include viewPath('includes/footer'); ?>