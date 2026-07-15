<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-dark mb-0">Tugas Tambahan PTK / Guru</h6>
                    </div>
                    <a href="<?php echo url('tugas_tambahan_ptk/tambah'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:add-circle-linear" class="text-xl"></iconify-icon> Tambah Tugas Tambahan PTK
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama PTK</th>
                                    <th>Tugas Tambahan</th>
                                    <th>Nomor SK</th>
                                    <th>Tanggal SK</th>
                                    <th>TMT</th>
                                    <th>TST</th>
                                    <th class="text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo html_escape($item->nama_ptk); ?></strong></td>
                                        <td>
                                            <?php echo html_escape($item->nama_tugas); ?>
                                            <div class="text-xs text-muted">
                                                Jenis: <span class="badge bg-secondary-100 text-secondary-600"><?php echo html_escape($item->jenis); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo $item->no_sk ? html_escape($item->no_sk) : '-'; ?></td>
                                        <td>
                                            <?php 
                                                if ($item->tgl_sk) {
                                                    echo date('d F Y', strtotime($item->tgl_sk)); 
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if ($item->tmt) {
                                                    echo date('d F Y', strtotime($item->tmt)); 
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if ($item->tst) {
                                                    echo date('d F Y', strtotime($item->tst)); 
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('tugas_tambahan_ptk/edit/' . $item->id); ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Sunting">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('tugas_tambahan_ptk/hapus/' . $item->id); ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Hapus data ini?')">
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
