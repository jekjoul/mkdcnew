<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-warning-400">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6>Data Tanah</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <select class="form-select form-select-sm w-auto">
                            <option>-Status-</option>
                            <option>Milik</option>
                            <option>Pinjam</option>
                            <option>Sewa</option>
                        </select>
                        <a href="<?php echo url('sarpras/tanahTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah Tanah</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nomor Sertifikat</th>
                                    <th scope="col">Atas Nama</th>
                                    <th scope="col">Luas</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center">Berkas</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($tanah as $row):
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= $row->nomor_sertifikat ?></td>
                                        <td><?= $row->atas_nama ?></td>
                                        <td><?= $row->luas ?> m<sup>2</sup></td>
                                        <td class="text-center"><?= $row->status ?> </td>
                                        <td class="text-center">
                                            <?php if ($row->berkas != null) { ?>
                                                <button type="button" class="btn btn-info-100 text-info-600 radius-8 px-14 py-6 text-sm" data-bs-toggle="modal" data-bs-target="#LihatBerkas<?= $no ?>">Lihat Berkas</button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-success-100 text-success-600 radius-8 px-14 py-6 text-sm" data-bs-toggle="modal" data-bs-target="#UnggahBerkas<?= $no ?>">Unggah Berkas</button>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahDetail<?= $no ?>">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </button>

                                                <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#TanahEdit<?= $no ?>">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach
                                ?>

                                <tr>
                                    <td>2</td>
                                    <td>10021/2/DS.0012</td>
                                    <td>Siti Robiah</td>
                                    <td>1.242 m<sup>2</sup></td>
                                    <td class="text-center">
                                        <span class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Pinjam</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success-100 text-success-600 radius-8 px-14 py-6 text-sm" data-bs-toggle="modal" data-bs-target="#UnggahBerkas">Unggah Berkas</button>
                                    </td>
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

<?php
$no = 1;
foreach ($tanah as $row):
?>
    <!--Modal Detail Tanah -->
    <div class="modal fade" id="TanahDetail<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Detail Tanah</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="row">
                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Sertifikat</label>
                            <p><?= $row->nomor_sertifikat ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Atas Nama Sertifikat</label>
                            <p><?= $row->atas_nama ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas (m<sup>2</sup>)</label>
                            <p><?= $row->luas ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No. Surat Ukur</label>
                            <p><?= $row->no_surat_ukur ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl. Pembukuan</label>
                            <p><?= $row->tgl_pembukuan ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editcountry" class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                            <p><?= $row->status ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Utara</label>
                            <p><?= $row->batas_utara ?></p>
                        </div>

                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Barat</label>
                            <p><?= $row->batas_barat ?></p>
                        </div>
                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Selatan</label>
                            <p><?= $row->batas_selatan ?></p>
                        </div>
                        <div class="col-6 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Timur</label>
                            <p><?= $row->batas_timur ?></p>
                        </div>



                        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                            <button type="reset" data-bs-dismiss="modal" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                Close
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Detail Tanah -->

    <!-- Modal Sunting Tanah -->
    <div class="modal fade" id="TanahEdit<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Tanah</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="#">
                        <div class="row">
                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Sertifikat</label>
                                <input type="text" class="form-control radius-8" id="editname" value="24231/2/BC.2332">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Atas Nama Sertifikat</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Yayasan Miftahul Khoer El-Istohary">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" value="9.242 ">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">No. Surat Ukur</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Yayasan Miftahul Khoer El-Istohary">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl. Pembukuan</label>
                                <input type="text" class="form-control radius-8" id="editname" value="9.242 ">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editcountry" class="form-label fw-semibold text-primary-light text-sm mb-8">Status </label>
                                <select class="form-control radius-8 form-select" id="editcountry">
                                    <option>Milik</option>
                                    <option>Pinjam</option>
                                    <option>Sewa</option>
                                </select>
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Utara</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Sungai">
                            </div>

                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Barat</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Tanah Warga">
                            </div>
                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Selatan</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Tanah Warga">
                            </div>
                            <div class="col-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Batas Sebelah Timur</label>
                                <input type="text" class="form-control radius-8" id="editname" value="Tanah Warga">
                            </div>



                            <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                <button type="reset" data-bs-dismiss="modal" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Sunting Tanah -->

    <!-- Modal Lihat Berkas -->
    <div class="modal fade" id="LihatBerkas<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Lihat Berkas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <object style="width: 100%;height: 100%;" data="<?php echo url('uploads/berkas.pdf') ?>" type="application/pdf" id="pdf_content" style="pointer-events: none;">
                        <iframe src="<?php echo url('uploads/berkas.pdf') ?>&embedded=true"></iframe>
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
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Unggah Berkas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">

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