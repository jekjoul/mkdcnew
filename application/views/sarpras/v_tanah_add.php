<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400">
                    <div class="text-center" style="width: 100% !important;">
                        <h6>Formulir Tambah Tanah</h6> 
                    </div>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row gy-3">
                            <div class="col-12 was-validated">
                                <label class="form-label">Nomor Sertifikat</label>
                                <input type="text" name="no_sertifikat" class="form-control" required="">
                                <div class="invalid-feedback">
                                    Silahkan masukan Nomor Sertifikat.
                                </div>
                            </div>
                            <div class="col-12 was-validated">
                                <label class="form-label">Pemilik Sertifikat</label>
                                <input type="text" name="pemilik_sertifikat" class="form-control" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Pemilik Sertifikat.
                                </div>
                            </div>

                            <div class="col-md-6 was-validated">
                                <label class="form-label">Luas Tanah (m<sup>2</sup>)</label>
                                <input type="text" name="luas" class="form-control" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Luas Tanah.
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <label class="form-label">Nomor Surat Ukur</label>
                                <input type="text" name="no_surat_ukur" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal pembukuan</label>
                                <input type="date" name="tanggal_pembukuan" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="editcountry"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status
                                    Kepemilikan</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="status">
                                    <option readonly>-</option>
                                    <option>Milik</option>
                                    <option>Pinjam</option>
                                    <option>Sewa</option>
                                </select>
                            </div>
                        </div>

                        <div class="row gy-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Batas Tanah Utara</label>
                                <input type="text" name="luas" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batas Tanah Timur</label>
                                <input type="text" name="no_surat_ukur" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batas Tanah Selatan</label>
                                <input type="text" name="luas" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batas Tanah Barat</label>
                                <input type="text" name="no_surat_ukur" class="form-control">
                            </div>
                        </div>

                        <div class="row gy-3 mt-3">
                            <div class="text-end gap-3 mt-24">
                                <a href="<?php echo url('sarpras/tanah') ?>"
                                    class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                    Batal
                                </a>
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