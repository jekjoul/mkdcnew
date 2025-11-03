<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6>Data Bangunan</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('sarpras/bangunanTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah Bangunan</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Bangunan</th>
                                    <th scope="col">Nomor PBG/IMB</th>
                                    <th scope="col">Luas Bangunan</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center">Berkas PBG</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($bangunan as $row):
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= $row->nama_bangunan ?></td>
                                        <td>
                                            <?php
                                            if (isset($row->no_pbg)) {
                                                echo "Belum PBG";
                                            } else {
                                                echo $row->no_pbg;
                                            }

                                            ?>
                                        </td>
                                        <td><?= $row->luas_tapak ?> m<sup>2</sup></td>
                                        <td class="text-center">
                                            <?= $row->status_bangunan ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row->berkas_bangunan != null) { ?>
                                                <div class="">
                                                    <button type="button" class="btn btn-outline-info-600 text-info-700 radius-8 px-14 py-6 mb-1 mx-auto text-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#LihatBerkas<?= $no ?>">
                                                        <iconify-icon icon="line-md:clipboard-list-twotone" class="text-xl"></iconify-icon>Lihat
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning-600 text-warning-600 radius-8 px-14 py-6 mx-auto text-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#UnggahBerkas<?= $no ?>">
                                                        <iconify-icon icon="line-md:edit-filled" class="text-xl"></iconify-icon>Ubah
                                                    </button>
                                                </div>


                                            <?php } else { ?>
                                                <button type="button" class="btn btn-success-100 text-success-600 radius-8 px-14 py-6 mx-auto text-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#UnggahBerkas<?= $no ?>">
                                                    <iconify-icon icon="line-md:upload-loop" class="text-xl"></iconify-icon>Unggah Berkas
                                                </button>
                                            <?php } ?>

                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#BangunanDetail<?= $no ?>">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </button>

                                                <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#BangunanEdit<?= $no ?>">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>

<?php
$no = 1;
foreach ($bangunan as $row):
?>

    <!--Modal Detail Bangunan -->
    <div class="modal fade" id="BangunanDetail<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Detail Bangunan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="#">
                        <div class="row">
                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Bangunan</label>
                                <p><?= $row->nama_bangunan ?></p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Berdiri diatas Tanah</label>
                                <p><?= $row->atas_nama ?></p>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <p><?= $row->panjang ?></p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <p><?= $row->lebar ?></p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Bangunan (m<sup>2</sup>)</label>
                                <p>243</p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl.
                                    Pendirian</label>
                                <p><?= $row->tgl_pendirian ?></p>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No.
                                    IMB/PBG</label>
                                <p><?php if (isset($row->no_pbg)) {
                                        echo $row->no_pbg;
                                    } else {
                                        echo "Belum PBG";
                                    } ?></p>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editcountry"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                                <p><?= $row->status_bangunan ?></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Detail bangunan -->

    <!-- Modal Sunting bangunan -->
    <div class="modal fade" id="BangunanEdit<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Bangunan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="<?php echo url('sarpras/bangunanUpdate/' . $row->id_bangunan) ?>" method="post" id="BangunanEdit<?= $no ?>">
                        <div class="row">
                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Bangunan</label>
                                <input type="text" class="form-control radius-8" id="editname" name="nama_bangunan" value="<?= $row->nama_bangunan ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Berdiri diatas
                                    Tanah</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="tanah">
                                    <option value="<?= $row->id_tanah ?>"><?= $row->atas_nama ?></option>
                                    <?php foreach ($tanah as $rowt): ?>
                                        <option value=" <?= $rowt->id_tanah ?>"><?= $rowt->atas_nama ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="panjang" value="<?= $row->panjang ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Panjang Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="lebar" value="<?= $row->lebar ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Lebar Bangunan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Bangunan
                                    (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="luas_tapak" value="<?= $row->luas_tapak ?>" required>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl.
                                    Pendirian</label>
                                <input type="date" class="form-control radius-8" id="editname" name="tgl_pendirian" value="<?= $row->tgl_pendirian ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Bangunan.
                                </div>
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No.
                                    IMB/PBG</label>
                                <input type="text" class="form-control radius-8" id="editname" name="no_pbg" value="<?= $row->no_pbg ?>">
                            </div>


                            <div class="col-md-6 mb-20">
                                <label for="editcountry"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="status_bangunan">
                                    <option value="Milik Sekolah">Milik Sekolah</option>
                                    <option value="Milik Yayasan">Milik Yayasan</option>
                                    <option value="Milik Perorangan">Milik Perorangan</option>
                                    <option value="Milik Perusahaan/Swasta">Milik Perusahaan/Swasta</option>
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
    <!-- End of Modal Sunting bangunan -->

    <!-- Modal Lihat Berkas -->
    <div class="modal fade" id="LihatBerkas<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Lihat Berkas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <object style="width: 100%;height: 100%;" data="<?php echo url('uploads/bangunan_berkas/' . $row->berkas_bangunan) ?>" type="application/pdf" id="pdf_content" style="pointer-events: none;">
                        <iframe src="<?php echo url('uploads/bangunan_berkas/' . $row->berkas_bangunan) ?>&embedded=true"></iframe>
                    </object>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Lihat Berkas -->


    <!-- Modal Upload Berkas -->
    <div class="modal fade" id="UnggahBerkas<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Unggah Berkas PBG</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row gy-3">
                        <?php echo form_open_multipart('sarpras/bangunanBerkasUpdate/' . $row->id_bangunan) ?>
                        <div class="col-12">
                            <input type="file" name="berkas" class="form-control form-control-lg">
                            <small>Unggah dalam format .pdf dengan ukuran maksimal 10 MB</small>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8" for="UnggahBerkas<?= $no ?>">
                                Simpan
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Upload Berkas -->

<?php
    $no++;
endforeach
?>


<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>