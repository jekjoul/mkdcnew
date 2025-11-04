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
                        <h6>Data Alat, Buku & Kendaraan</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('sarpras/alatTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah Alat</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Jenis Sarana</th>
                                    <th scope="col">Nama Sarana</th>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Jumlah Laik</th>
                                    <th scope="col">Digunakan</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;

                                foreach ($sarana as $row):
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= $row->nama_jenis_sarana ?></td>
                                        <td><?= $row->nama_sarana ?></td>
                                        <td><?= $row->kode_sarana ?></td>
                                        <td><?= $row->jumlah_sarana ?></td>
                                        <td><?= $row->jumlah_laik ?></td>
                                        <td>
                                            <?php $digunakan = 42;
                                            echo $digunakan ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#SaranaDetail<?= $no ?>">
                                                    <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                                                </button>
                                                <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" onclick="tampilkanModal(<?= $no ?>)">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
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

<?php
$no = 1;

foreach ($sarana as $row):
    $digunakan = 42;
    $jumlah = $row->jumlah_laik;
    $hasil = $jumlah - $digunakan;
    $tidakLaik = $row->jumlah_sarana - $jumlah;
?>
    <!--Modal Detail Ruangan -->
    <div class="modal fade" id="SaranaDetail<?= $no ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
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
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sarana</label>
                                <p><?= $row->nama_jenis_sarana ?></p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Sarana</label>
                                <p><?= $row->nama_sarana ?></p>

                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Sarana</label>
                                <p><?= $row->kode_sarana ?></p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Spesifikasi</label>
                                <p><?= $row->spesifikasi_sarana ?></p>
                            </div>

                            <div class="col-md-6 mb-20 was-validated">
                                <label for="editname"
                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah</label>
                                <p><?= $row->jumlah_sarana ?></p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Laik</label>
                                <p><?= $row->jumlah_laik ?></p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pengadaan</label>
                                <p><?= tanggal_indo($row->tgl_pengadaan) ?></p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Sumber Pengadaan</label>
                                <p><?= $row->sumber_pengadaan  ?></p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Digunakan</label>
                                <p><?= $digunakan ?> Unit</p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Laik Tidak Digunakan</label>
                                <p><?= $hasil ?> Unit</p>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label for="editname" class="form-label fw-semibold text-primary-light text-sm mb-8">Jumlah Tidak Laik</label>
                                <p><?= $tidakLaik ?> Unit</p>
                            </div>




                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Detail Ruangan -->

    <!-- Modal Sunting Ruangan -->
    <div class="modal fade" id="SaranaEdit<?= $row->id_sarana ?>" tabindex="-1" aria-labelledby="exampleModalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title fs-5" id="exampleModalEditLabel">Sunting Sarana</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24" id="modal-body<?= $row->id_sarana ?>">

                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Sunting Ruangan -->


    <!-- Script Modal Edit -->
    <script>
        function tampilkanModal(id) {
            // Permintaan Ajax
            $.ajax({
                url: '<?php echo base_url("sarpras/alatEdit/"); ?>' + id, // Sesuaikan URL controller Anda
                type: 'GET',
                success: function(response) {
                    // Masukkan konten HTML yang dikembalikan ke dalam body modal
                    $('#modal-body<?= $row->id_sarana ?>').html(response);
                    // Tampilkan modal
                    $('#SaranaEdit<?= $row->id_sarana ?>').modal('show');
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