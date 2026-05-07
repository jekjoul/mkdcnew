<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
            <h6 class="mb-0 text-light">Daftar Pembelajaran</h6>
            <a href="<?php echo url('pembelajaran/tambah') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="solar:add-circle-linear"></iconify-icon> Atur Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Lembaga</th>
                            <th>Tingkat</th>
                            <th>Rombel</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembelajaran as $row): ?>
                            <tr>
                                <td><?php echo $row->tahun_pelajaran ?> (<?php echo $row->semester ?>)</td>
                                <td><?php echo $row->nama_lembaga ?></td>
                                <td><?php echo $row->nama_tingkat ?></td>
                                <td><span class="badge bg-info-100 text-info-600"><?php echo $row->nama_rombel ?></span></td>
                                <td><?php echo $row->nama_wali_kelas ?: '-' ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo url('pembelajaran/edit/' . $row->id_pembelajaran) ?>" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:pen-linear"></iconify-icon> Edit
                                        </a>
                                        <a href="<?php echo url('pembelajaran/tambah_mapel/' . $row->id_pembelajaran) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:notebook-linear"></iconify-icon> Tambah Mapel
                                        </a>
                                        <a href="<?php echo url('pembelajaran/daftar_siswa/' . $row->id_pembelajaran) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Daftar Siswa
                                        </a>
                                        <a href="<?php echo url('jadwal_pelajaran/semua') ?>" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="akar-icons:schedule"></iconify-icon> Jadwal
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
<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
