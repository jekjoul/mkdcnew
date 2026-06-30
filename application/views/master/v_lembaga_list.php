<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6>Master Lembaga</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo url('master/lembagaTambah') ?>" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lembaga</th>
                                    <th>NPSN</th>
                                    <th>Bentuk</th>
                                    <th>Status</th>
                                    <th>Akreditasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($lembaga as $row): ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if ($row->logo): ?>
                                                    <img src="<?php echo url('uploads/lembaga/' . $row->logo) ?>" class="w-40-px h-40-px rounded-circle">
                                                <?php endif; ?>
                                                <?php echo $row->nama_lembaga ?>
                                            </div>
                                        </td>
                                        <td><?php echo $row->npsn ?></td>
                                        <td><?php echo $row->bentuk_pendidikan ?></td>
                                        <td><?php echo $row->status ?></td>
                                        <td><?php echo $row->akreditasi ?></td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('master/lembagaEdit/' . $row->id_lembaga) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/lembagaDelete/' . $row->id_lembaga) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="return confirm('Hapus lembaga ini?')">
                                                    <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>