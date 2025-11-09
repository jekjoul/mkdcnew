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
                        <h6>Formulir Tambah Alat, Buku & Kendaraan</h6>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <form action="<?php echo url('sarpras/alatSimpan') ?>" method="post">
                        <div class="row">

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Sarana</label>
                                <input type="text" class="form-control radius-8" id="editname" name="nama_sarana" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Sarana.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Sarana</label>
                                <input type="text" class="form-control radius-8" id="editname" name="kode_sarana" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Sarana.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sarana</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="id_jenis_sarana">
                                    <?php foreach ($jenis_sarana as $rowt): ?>
                                        <option value=" <?= $rowt->id_jenis_sarana ?>"><?= $rowt->nama_jenis_sarana ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Spesifikasi</label>
                                <input type="text" class="form-control radius-8" id="editname" name="spesifikasi_sarana" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Spesifikasi Sarana.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pengadaan</label>
                                <input type="date" class="form-control radius-8" id="editname" name="tgl_pengadaan" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pengadaan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Sumber Pengadaan</label>
                                <input type="text" class="form-control radius-8" id="editname" name="sumber_pengadaan" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Sumber Pengadaan.
                                </div>
                            </div>


                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah</label>
                                <input type="text" class="form-control radius-8" id="editname" name="jumlah_sarana" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Jumlah Sarana.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Laik</label>
                                <input type="text" class="form-control radius-8" id="editname" name="jumlah_laik" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Jumlah Sarana Laik.
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