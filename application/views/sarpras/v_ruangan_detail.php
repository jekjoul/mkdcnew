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
                        <div class="col-md-4 col-xl-3 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ruangan</label>
                            <p><?= $row->nama_ruangan ?></p>
                        </div>

                        <div class="col-md-4 col-xl-3 mb-20 was-validated">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Ruangan</label>
                            <p><?= $row->nama_jenis_ruangan ?></p>

                        </div>

                        <div class="col-md-4 col-xl-3 mb-20 was-validated">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Bangunan</label>
                            <p><?= $row->nama_bangunan ?></p>

                        </div>

                        <div class="col-md-4 col-xl-3 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Luas Ruangan (m<sup>2</sup>)</label>
                            <p><?= $row->luas_tapak_ruangan ?></p>
                        </div>

                        <div class="col-md-4 col-xl-3 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Panjang (m)</label>
                            <p><?= $row->panjang_ruangan ?></p>
                        </div>

                        <div class="col-md-4 col-xl-3 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Lebar (m)</label>
                            <p><?= $row->lebar_ruangan ?></p>
                        </div>

                        <div class="col-md-4 col-xl-3 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas </label>
                            <p><?= $row->kapasitas ?></p>
                        </div>

                        <div class="col-md-4 col-xl-3 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Kondisi </label>
                            <p><?= $row->kondisi ?></p>
                        </div>

                    </div>
                    <hr>

                    <div class="card basic-data-table  mt-20">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <h6>Sarana <?= $row->nama_ruangan ?></h6>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <button type="button" class="btn btn-sm btn-primary-600" data-bs-toggle="modal" data-bs-target="#add">
                                    <i class="ri-add-line"></i>Tambah Sarana
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table bordered-table" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Jenis Sarana</th>
                                            <th scope="col">Nama Sarana</th>
                                            <th scope="col">Kode Sarana</th>
                                            <th scope="col">Jumlah</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($sarana as $row):  ?>
                                            <tr>
                                                <td><?= $no ?></td>
                                                <td><?= $row->nama_jenis_sarana ?></td>
                                                <td><?= $row->nama_sarana ?></td>
                                                <td><?= $row->kode_sarana ?></td>
                                                <td><?= $row->jumlah_sarana_ruangan ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-10 justify-content-center">
                                                        <button class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#edit<?= $row->id_ruangan_sarana ?>">
                                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php $no++;
                                        endforeach ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $no = 1;
foreach ($sarana as $row):  ?>
    <!--Modal edit -->
    <div class="modal fade" id="edit<?= $row->id_ruangan_sarana ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Jumlah Sarana</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="<?php echo url('sarpras/ruanganSaranaUpdate/' . $row->id_ruangan_sarana . "/" . $row->id_ruangan) ?>" method="post">
                        <div class="row">
                            <div class="col-md-12 mb-20 ">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sarana</label>
                                <input type="text" class="form-control radius-8" id="editname" value="<?= $row->nama_jenis_sarana ?>" name="jenis_sarana" readonly>
                            </div>

                            <div class="col-md-12 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Sarana</label>
                                <input type="text" class="form-control radius-8" id="editname" value="<?= $row->nama_sarana ?> (<?= $row->kode_sarana ?>)" name="nama_sarana" readonly>
                            </div>

                            <div class="col-md-12 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah</label>
                                <input type="text" class="form-control radius-8" id="editname" value="<?= $row->jumlah_sarana_ruangan ?>" name="jumlah" required>
                                <div class="invalid-feedback">
                                    Silahkan masukan Jumlah Sarana.
                                </div>
                            </div>


                            <div class="w-50-px h-50-px mx-auto my-4 d-flex justify-content-center align-items-center gap-3">
                                <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-neutral-500 px-20 py-11"> Batal</button>
                                <button type="submit" class="btn btn-sm btn-info-500 px-20 py-11"> Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End of Modal edit -->

    <!--Modal Hapus -->
    <div class="modal fade" id="hapus<?= $row->id_ruangan_sarana ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">

                <div class="modal-body p-24 my-3">
                    <div class="row">
                        <div class="w-50-px h-50-px mx-auto my-4 mt-2 bg-danger rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="line-md:trash" class="text-base text-2xl mb-0"></iconify-icon>
                        </div>
                        <h7 class="text-center mt-3 mb-2">Apakah anda yakin akan menghapus data ini?</h7>
                        <div class="w-50-px h-50-px mx-auto my-4 d-flex justify-content-center align-items-center gap-3">
                            <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-neutral-500 px-20 py-11"> Batal</button>
                            <a href="<?php echo url('sarpras/ruanganSaranaDelete/' . $row->id_ruangan_sarana . "/" . $row->id_ruangan) ?>" class="btn btn-sm btn-danger-600 px-20 py-11"> Hapus</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Hapus -->
<?php $no++;
endforeach ?>


<!--Modal Add -->
<div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Tambah Sarana</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form action="<?php echo url('sarpras/ruanganSaranaSimpan/' . $row->id_ruangan) ?>" method="post">
                    <div class="row">
                        <div class="col-md-12 mb-20 ">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sarana</label>
                            <select class="form-control radius-8 form-select" id="jenis_sarana" name="id_jenis_sarana">
                                <option>-Pilih Jenis Sarana-</option>
                                <?php foreach ($jenis_sarana as $row):  ?>
                                    <option value="<?= $row->id_jenis_sarana ?>"><?= $row->nama_jenis_sarana ?></option>
                                <?php endforeach ?>
                            </select>
                            <small>Jika sarana belum ada, silahkan input di menu Alat, Buku & Kendaraan</small>
                        </div>

                        <div class="col-md-12 mb-20">
                            <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Sarana</label>
                            <select class="form-control radius-8 form-select sarana" id="editcountry" name="id_sarana">
                            </select>
                            <img src="<?php echo url('/assets/loading.gif') ?>" width="35" id="load2" style="display:none;" />
                        </div>

                        <div class="col-md-12 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah</label>
                            <input type="text" class="form-control radius-8" id="editname" name="jumlah" required>
                            <div class="invalid-feedback">
                                Silahkan masukan Jumlah Sarana.
                            </div>
                        </div>


                        <div class="w-50-px h-50-px mx-auto my-4 d-flex justify-content-center align-items-center gap-3">
                            <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-neutral-500 px-20 py-11"> Batal</button>
                            <button type="submit" class="btn btn-sm btn-info-500 px-20 py-11"> Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End of Modal add -->
<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $('#jenis_sarana').change(function() {
            $("img#load2").show();
            var id = $(this).val();
            $.ajax({
                url: "<?php echo base_url(); ?>sarpras/alatGetSome",
                method: "POST",
                data: {
                    id: id
                },
                async: false,
                dataType: 'json',
                success: function(data) {
                    var html = '';
                    var i;
                    for (i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id_sarana + '">' + data[i].nama_sarana + ' (' + data[i].kode_sarana + ')</option>';
                    }
                    $('.sarana').html(html);
                    $("img#load2").hide();
                }
            });
        });
    });
</script>