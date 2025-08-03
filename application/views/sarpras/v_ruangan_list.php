<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400" >
                    <div class="d-flex flex-wrap align-items-center gap-3">
                      <h6>Data Ruangan</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('sarpras/ruanganTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah Ruangan</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Jenis Prasarana/Ruangan</th>
                                    <th scope="col" class="text-center">Bangunan</th>
                                    <th scope="col">Nama Ruangan</th>
                                    <th scope="col">Luas Ruangan</th>
                                    <th scope="col" class="text-center">Kapasitas</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Ruang Kelas</td>
                                    <td>Bangunan Kelas 7</td>
                                    <td>Ruang Kelas 7A</td>
                                    <td class="text-center">63<sup>2</sup></td>
                                    <td class="text-center">35</td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahDetail">
                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                            </button>

                                            <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahEdit">
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Ruang Guru</td>
                                    <td>Bangunan Yayasan</td>
                                    <td>Ruang Guru</td>
                                    <td class="text-center">63<sup>2</sup></td>
                                    <td class="text-center">35</td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahDetail">
                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                            </button>

                                            <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahEdit">
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>

<!--Modal Detail Ruangan -->
<div class="modal fade" id="TanahDetail" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Detail Ruangan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form action="#">
                        <div class="row">
                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ruangan</label>
                                <p>Ruangan Kelas 7</p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"class="form-label fw-semibold text-primary-light text-sm mb-8">Berdiri diatas Tanah</label>
                                <p>Yayasan Miftahul Khoer El-Istohary</p>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <p>23</p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <p>25</p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Ruangan (m<sup>2</sup>)</label>
                                <p>243</p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl.
                                    Pendirian</label>
                                <p>25 Agustus 2025</p>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No.
                                    IMB/PBG</label>
                                <p>24231/2/BC.2332</p>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editcountry"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                                <p>Milik Yayasan</p>
                            </div>

                           
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
<!-- End of Modal Detail Ruangan -->

<!-- Modal Sunting Ruangan -->
<div class="modal fade" id="TanahEdit" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Ruangan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
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

                            <div class="col-md-6 mb-20">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Ruangan
                                    (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" required>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl.
                                    Pendirian</label>
                                <input type="date" class="form-control radius-8" id="editname" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Ruangan.
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
<!-- End of Modal Sunting Ruangan -->

<!-- Modal Lihat Berkas -->
<div class="modal fade" id="LihatBerkas" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
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


<!-- Modal Upload Berkas -->
<div class="modal fade" id="UnggahBerkas" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Unggah Berkas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                
            </div>
        </div>
    </div>
</div>
<!-- End of Modal Upload Berkas -->




<?php include viewPath('includes/footer'); ?>
<script>
  let table = new DataTable('#dataTable');
</script>