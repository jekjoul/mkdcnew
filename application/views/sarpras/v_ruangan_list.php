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
                                    <th scope="col">Jenis Prasarana</th>
                                    <th scope="col">Bangunan</th>
                                    <th scope="col">Nama Ruangan</th>
                                    <th scope="col">Luas Ruangan</th>
                                    <th scope="col" class="text-center">Kapasitas</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($ruangan as $row):
                                ?>
                                    <tr>
                                        <td><?= $no; ?></td>
                                        <td><?= $row->nama_jenis_ruangan ?></td>
                                        <td><?= $row->nama_bangunan ?></td>
                                        <td><?= $row->nama_ruangan ?></td>
                                        <td class="text-center"><?= $row->luas_tapak_ruangan ?><sup>2</sup></td>
                                        <td class="text-center"><?= $row->kapasitas ?></td>

                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">

                                                <a href="<?php echo url('sarpras/ruanganDetail/' . $row->id_ruangan) ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </a>

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
foreach ($ruangan as $row):
?>

    <!-- Modal Sunting Ruangan -->
    <div class="modal fade" id="TanahEdit<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Ruangan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="<?php echo url('sarpras/ruanganUpdate/' . $row->id_ruangan) ?>" method="post">
                        <div class="row">
                            <div class="col-md-12 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ruangan</label>
                                <input type="text" class="form-control radius-8" id="editname" name="nama_ruangan" value="<?= $row->nama_ruangan ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Nama Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Ruangan</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="id_jenis_ruangan">
                                    <option value=" <?= $row->id_jenis_ruangan ?>"><?= $row->nama_jenis_ruangan ?></option>
                                    <?php foreach ($jenis_ruangan as $rowt): ?>
                                        <option value=" <?= $rowt->id_jenis_ruangan ?>"><?= $rowt->nama_jenis_ruangan ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Bangunan</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="id_bangunan">
                                    <option value=" <?= $row->id_bangunan ?>"><?= $row->nama_bangunan ?></option>
                                    <?php foreach ($bangunan as $rowt): ?>
                                        <option value=" <?= $rowt->id_bangunan ?>"><?= $rowt->nama_bangunan ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="panjang_ruangan" value="<?= $row->panjang_ruangan ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Panjang Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="lebar_ruangan" value="<?= $row->lebar_ruangan ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Lebar Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Tapak Ruangan
                                    (m<sup>2</sup>)</label>
                                <input type="text" class="form-control radius-8" id="editname" name="luas_tapak_ruangan" value="<?= $row->luas_tapak_ruangan ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Luas Tapak.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas</label>
                                <input type="text" class="form-control radius-8" id="editname" name="kapasitas" value="<?= $row->kapasitas ?>" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Tanggal Pendirian Ruangan.
                                </div>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Kondisi</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="kondisi" value="<?= $row->kondisi ?>" required>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Sedang">Rusak Sedang</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                                <select class="form-control radius-8 form-select" id="editcountry" name="status" value="<?= $row->status ?>" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
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
<?php
    $no++;
endforeach
?>


<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>