<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6>Master Jenis Ruangan</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <button type="button" class="btn btn-sm btn-primary-600" data-bs-toggle="modal" data-bs-target="#addJenisRuangan">
                            <i class="ri-add-line"></i>Tambah Jenis Ruangan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Jenis Ruangan</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;

                                foreach ($jenis_ruangan as $row):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no ?></td>
                                        <td><?= $row->nama_jenis_ruangan ?></td>
                                        <td><?= $row->status ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" onclick="tampilkanModal(<?= $row->id_jenis_ruangan ?>)">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </button>
                                                <button type="button" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#hapus<?= $no ?>">
                                                    <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>

<!--Modal Detail Ruangan -->
<div class="modal fade" id="addJenisRuangan" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="exampleModalEditLabel">Detail Ruangan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form action="<?php echo url('master/jenisRuanganSimpan/') ?>" method="post">
                    <div class="row">
                        <div class="col-md-12 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Jenis Ruangan</label>
                            <input type="text" class="form-control radius-8" id="editname" name="nama_jenis_ruangan" required>
                            <div class="invalid-feedback">
                                Silahkan masukan Nama Jenis Ruangan.
                            </div>
                        </div>

                        <div class="col-md-12 mb-20 was-validated">
                            <label for="editname"
                                class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                            <select class="form-control radius-8 form-select" id="editcountry" name="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
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
<!-- End of Modal Detail Ruangan -->

<?php
$no = 1;

foreach ($jenis_ruangan as $row):
?>

    <!--Modal Hapus -->
    <div class="modal fade" id="hapus<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
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
                            <a href="<?php echo url('master/jenisRuanganDelete/' . $row->id_jenis_ruangan)  ?>" class="btn btn-sm btn-danger-600 px-20 py-11"> Hapus</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Hapus -->

    <!-- Modal Sunting Ajax-->
    <div class="modal fade" id="ModalEdit<?= $row->id_jenis_ruangan ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Jenis Ruangan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24" id="modal-body<?= $row->id_jenis_ruangan ?>">

                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Sunting-->


    <!-- Script Modal Edit -->
    <script>
        function tampilkanModal(id) {
            // Permintaan Ajax
            $.ajax({
                url: '<?php echo base_url("master/jenisRuanganEdit/"); ?>' + id, // Sesuaikan URL controller Anda
                type: 'GET',
                success: function(response) {
                    // Masukkan konten HTML yang dikembalikan ke dalam body modal
                    $('#modal-body<?= $row->id_jenis_ruangan ?>').html(response);
                    // Tampilkan modal
                    $('#ModalEdit<?= $row->id_jenis_ruangan ?>').modal('show');
                },
                error: function(xhr, status, error) {
                    // Tangani error jika terjadi
                    console.error("Terjadi kesalahan: " + error);
                    alert("Gagal memuat konten modal.");
                }
            });
        }
    </script>
    <!-- End of Script Modal Edit -->
<?php
    $no++;
endforeach ?>



<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>