<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-info-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Manajemen Kelas Jauh (Siswa Menginduk)</h6>
            <?php if (hasPermissions('kelas_jauh_add')): ?>
                <a href="<?php echo url('kelas_jauh/tambah') ?>" class="btn btn-sm btn-primary text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon> Tambah Kelas Jauh Baru
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTableKelasJauh">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Nama Kelas Jauh</th>
                            <th>Keterangan / Deskripsi</th>
                            <th class="text-center" style="width: 150px;">Jumlah Anggota</th>
                            <th class="text-center" style="width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kelas_jauh as $row): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                <td class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_kelas_jauh) ?></td>
                                <td><?php echo html_escape($row->keterangan ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-focus text-info-main px-16 py-6 radius-4">
                                        <?php echo $row->jumlah_siswa ?> Siswa
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <?php if (hasPermissions('kelas_jauh_anggota')): ?>
                                            <a href="<?php echo url('kelas_jauh/daftar_siswa/' . $row->id_kelas_jauh) ?>" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" title="Kelola Anggota">
                                                <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Anggota
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo url('kelas_jauh/detail/' . $row->id_kelas_jauh) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" title="Lihat Anggota">
                                            <iconify-icon icon="lucide:eye"></iconify-icon> Detail
                                        </a>
                                        <?php if (hasPermissions('kelas_jauh_edit')): ?>
                                            <a href="<?php echo url('kelas_jauh/edit/' . $row->id_kelas_jauh) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" title="Edit">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (hasPermissions('kelas_jauh_delete')): ?>
                                            <a href="<?php echo url('kelas_jauh/hapus/' . $row->id_kelas_jauh) ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="return confirm('Apakah Anda yakin ingin menghapus data Kelas Jauh ini? Data anggota yang menginduk juga akan ikut terhapus.')" title="Hapus">
                                                <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                            </a>
                                        <?php endif; ?>
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
    let table = new DataTable('#dataTableKelasJauh');
</script>
