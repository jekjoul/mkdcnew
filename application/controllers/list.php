<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-neutral-300">
            <h6 class="mb-0">Daftar Pembelajaran</h6>
            <a href="<?php echo url('pembelajaran/tambah') ?>" class="btn btn-primary-600 btn-sm d-flex align-items-center gap-2">
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
                            <th class="text-center">Mapel</th>
                            <th class="text-center">Siswa</th>
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
                                <td class="text-center"><span class="badge bg-primary-100 text-primary-600"><?php echo $row->jumlah_mapel ?> Mapel</span></td>
                                <td class="text-center"><span class="badge bg-success-100 text-success-600"><?php echo $row->jumlah_siswa ?> Siswa</span></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Detail</a>
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