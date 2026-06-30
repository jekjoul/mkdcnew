<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-dark mb-0">Master Tingkat Sekolah</h6>
                    </div>
                    <a href="<?php echo url('master/tingkatSekolahTambah'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon> Tambah Tingkat
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Tingkat</th>
                                    <th>Tingkat Angka</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($tingkat as $t): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $t->nama_tingkat; ?></td>
                                        <td>Tingkat <?php echo $t->tingkat_angka; ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $t->status == 'Aktif' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600'; ?>">
                                                <?php echo $t->status; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('master/tingkatSekolahEdit/' . $t->id_tingkat_sekolah); ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Sunting">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/tingkatSekolahDelete/' . $t->id_tingkat_sekolah); ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Hapus data ini?')">
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
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
</script>