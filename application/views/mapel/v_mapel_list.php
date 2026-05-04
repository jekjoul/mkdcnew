<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light mb-0">Data Mata Pelajaran</h6>
                    </div>
                    <a href="<?php echo url('master/mapelTambah'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon> Tambah Mapel
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Singkatan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($mapel as $m): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $m->nama_mapel; ?></td>
                                        <td><?php echo $m->mapel_singkat ?: '-'; ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $m->status == 'Aktif' ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main'; ?>">
                                                <?php echo $m->status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('master/mapelEdit/' . $m->id_mapel); ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Sunting">
                                                    <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/mapelDelete/' . $m->id_mapel); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
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