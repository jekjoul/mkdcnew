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
                        <h6>Formulir Tambah Bangunan</h6>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <form action="#">
                        <div class="row">
                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Bangunan</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Berdiri diatas
                                    Tanah</label>
                                <select class="form-control radius-8 form-select" id="editcountry">
                                    <option>Yayasan Miftahul Khoer El-Istohary</option>
                                    <option>Siti Robiah</option>
                                </select>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Panjang Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Lebar Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Bangunan
                                    (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl.
                                    Pendirian</label>
                                <input type="date" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Bangunan.
                                </div>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No.
                                    IMB/PBG</label>
                                <input type="text" class="form-control radius-8" id="editname">
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editcountry"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                                <select class="form-control radius-8 form-select" id="editcountry">
                                    <option>Milik Sekolah</option>
                                    <option>Milik Yayasan</option>
                                    <option>Milik Perorangan</option>
                                    <option>Milik Perusahaan/Swasta</option>
                                </select>
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