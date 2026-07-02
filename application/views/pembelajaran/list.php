<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
            <h6 class="mb-0 text-light"><?php echo !empty($is_nonaktif) ? 'Data Pembelajaran Tidak Aktif' : 'Daftar Pembelajaran Aktif'; ?></h6>
            <a href="<?php echo url(!empty($is_nonaktif) ? 'pembelajaran' : 'pembelajaran/nonaktif') ?>" class="btn btn-warning-600 btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>"></iconify-icon>
                <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Lembaga</th>
                            <th>Rombel</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Mapel</th>
                            <th class="text-center">Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembelajaran as $row): ?>
                            <tr>
                                <td><?php echo $row->tahun_pelajaran ?> (<?php echo $row->semester ?>)</td>
                                <td><?php echo $row->nama_lembaga_singkat ?></td>
                                <td><span class="badge bg-info-100 text-info-600"><?php echo $row->nama_tingkat . ' - ' . $row->nama_rombel ?></span></td>
                                <td><?php echo $row->nama_wali_kelas ?: '-' ?></td>
                                <td class="text-center"><span class="badge bg-success-100 text-success-600"><?php echo $row->jumlah_siswa ?></span></td>
                                <td class="text-center">
                                    <a href="<?php echo url('pembelajaran/tambah_mapel/' . $row->id_pembelajaran) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:notebook-linear"></iconify-icon> Mapel
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo url('pembelajaran/daftar_siswa/' . $row->id_pembelajaran) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Siswa
                                    </a>
                                </td>
                                <td>
                                    <div class="dropdown text-center">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo url('pembelajaran/edit/' . $row->id_pembelajaran) ?>">
                                                    <iconify-icon icon="solar:pen-linear"></iconify-icon> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo url('jadwal_pelajaran/semua') ?>">
                                                    <iconify-icon icon="akar-icons:schedule"></iconify-icon> Jadwal
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo url('nilai_siswa') ?>">
                                                    <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Nilai
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo url('perangkat_pembelajaran') ?>">
                                                    <iconify-icon icon="solar:document-add-linear"></iconify-icon> Perangkat
                                                </a>
                                            </li>
                                            <?php if (!$is_nonaktif): ?>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger btn-luluskan" href="#" data-id="<?php echo $row->id_pembelajaran; ?>">
                                                    <iconify-icon icon="lucide:graduation-cap"></iconify-icon> Luluskan
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
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
    
    $(document).on('click', '.btn-luluskan', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let href = "<?php echo url('pembelajaran/luluskan/') ?>" + id;
        
        Swal.fire({
            title: 'Luluskan Pembelajaran Ini?',
            text: "Apakah Anda yakin ingin meluluskan pembelajaran ini? Semua siswa di dalam rombel ini akan dipindahkan ke Data Alumni beserta seluruh nilai dan berkasnya. Rombel dan Pembelajaran ini juga akan dinonaktifkan secara permanen dan tidak bisa diubah atau diedit lagi. Aksi ini tidak dapat dibatalkan secara massal.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Luluskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
</script>