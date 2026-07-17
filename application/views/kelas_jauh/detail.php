<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header bg-info-900 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 text-light">Detail Anggota Kelas Jauh: <?php echo html_escape($kelas_jauh->nama_kelas_jauh) ?></h6>
                <p class="text-sm text-neutral-200 mb-0">Daftar siswa yang menginduk (kelas jauh) namun tetap berada di rombel reguler sekolah induk.</p>
            </div>
            <a href="<?php echo url('kelas_jauh') ?>" class="btn btn-sm btn-light text-dark radius-8 px-16 py-8 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon> Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTableDetailKelasJauh">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Rombel Utama (Sekolah Induk)</th>
                            <th>Status Keaktifan</th>
                            <th>Catatan Khusus</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peserta)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary-light py-24">Belum ada siswa terdaftar di Kelas Jauh ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($peserta as $row): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-neutral-200 d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 36px; height: 36px;">
                                                <?php echo strtoupper(substr($row->nama_siswa, 0, 2)) ?>
                                            </div>
                                            <span class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_siswa) ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo html_escape($row->nisn ?: '-') ?></td>
                                    <td>
                                        <span class="badge bg-neutral-200 text-dark px-12 py-6 radius-4">
                                            <?php echo html_escape($row->rombel ?: 'Belum diatur') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row->status_keaktifan === 'Aktif'): ?>
                                            <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-focus text-danger-main px-12 py-6 radius-4"><?php echo html_escape($row->status_keaktifan) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($row->catatan ?: '-') ?></td>
                                    <td class="text-center">
                                        <?php if (hasPermissions('menu_kesiswaan_data_siswa')): ?>
                                            <a href="<?php echo url('siswa/detail/' . $row->id_siswa) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" target="_blank" title="Lihat Profil Siswa">
                                                <iconify-icon icon="lucide:user"></iconify-icon> Profil
                                            </a>
                                        <?php else: ?>
                                            <span class="text-secondary-light text-sm">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTableDetailKelasJauh');
</script>
