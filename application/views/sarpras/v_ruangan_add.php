<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div
                    class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400">
                    <div class="text-center" style="width: 100% !important;">
                        <h6>Formulir Tambah Ruangan</h6>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <form action="#">
                        <div class="row">
                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ruangan</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Bangunan</label>
                                <select class="form-control radius-8 form-select" id="editcountry">
                                    <option>Bangunan Kelas 7</option>
                                    <option>Bangunan Administrasi</option>
                                </select>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Panjang Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Lebar Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Ruangan
                                    (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Ruangan.
                                </div>
                            </div>


                           

                            <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                <button type="submit"
                                    class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>