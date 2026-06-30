<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light mb-0">Data Calon Siswa Daftar Ulang</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (hasPermissions('calon_siswa_import')): ?>
                        <button type="button" class="btn btn-warning-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalImportCalonSiswa">
                            <iconify-icon icon="lucide:upload" class="text-xl"></iconify-icon> Import Excel
                        </button>
                        <?php endif; ?>
                        <?php if (hasPermissions('calon_siswa_export')): ?>
                        <a href="<?php echo url('calon_siswa/export') ?>" class="btn btn-success-600 text-light radius-8 px-20 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:download" class="text-xl"></iconify-icon> Export Excel
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermissions('calon_siswa_validasi')): ?>
                        <a href="<?php echo url('calon_siswa/validasi') ?>" class="btn btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:check-check" class="text-xl"></iconify-icon> Validasi
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermissions('calon_siswa_add')): ?>
                        <a href="<?php echo url('calon_siswa/add') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:plus" class="text-xl"></iconify-icon> Input Calon Siswa
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length="10">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Calon Siswa</th>
                                    <th class="text-center">NISN/NIK</th>
                                    <th>Sekolah Asal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Status Aktivasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($calon_siswa as $s): ?>
                                    <?php
                                    $badge = $s->status_daftar_ulang === 'Terverifikasi' ? 'bg-success-focus text-success-main' : ($s->status_daftar_ulang === 'Perbaikan' ? 'bg-warning-focus text-warning-main' : 'bg-info-focus text-info-main');
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($s->nama_siswa, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php if ($s->id_siswa): ?><div class="text-success text-sm">Sudah menjadi siswa</div><?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nik ?: '-'); ?></td>
                                        <td><?php echo $s->sekolah_asal ?: '-'; ?></td>
                                        <td class="text-center"><span class="badge <?php echo $badge ?> px-16 py-6 radius-4"><?php echo $s->status_daftar_ulang; ?></span></td>
                                        <td class="text-center">
                                            <?php if ($s->id_siswa): ?>
                                                <a href="<?php echo url('siswa/detail/' . $s->id_siswa); ?>" class="btn btn-success-600 text-light btn-sm radius-8 d-inline-flex align-items-center gap-1">
                                                    <iconify-icon icon="lucide:user-check"></iconify-icon> Lihat Siswa
                                                </a>
                                            <?php else: ?>
                                                <span class="text-secondary-light text-sm">Belum Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('calon_siswa/upload/' . $s->id_calon_siswa); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Detail & Berkas">
                                                    <iconify-icon icon="lucide:folder-open"></iconify-icon>
                                                </a>
                                                <?php if (hasPermissions('calon_siswa_edit')): ?>
                                                <a href="<?php echo url('calon_siswa/edit/' . $s->id_calon_siswa); ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit">
                                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (hasPermissions('calon_siswa_delete')): ?>
                                                <a href="<?php echo url('calon_siswa/hapus/' . $s->id_calon_siswa); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center btn-delete-calon" data-bs-toggle="tooltip" title="Hapus" data-nama="<?php echo htmlspecialchars($s->nama_siswa, ENT_QUOTES, 'UTF-8'); ?>" data-active="<?php echo $s->id_siswa ? '1' : '0'; ?>">
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
    </div>
</div>

<div class="modal fade" id="modalImportCalonSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24">
                <h1 class="modal-title fs-5">Import Calon Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo url('calon_siswa/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-24">
                    <div class="alert alert-info bg-info-100 text-info-600 border-info-100 radius-8">
                        Gunakan file .xlsx atau .csv dengan baris pertama sebagai header kolom.
                    </div>
                    <div class="mb-20">
                        <a href="<?php echo url('calon_siswa/templateImport') ?>" class="btn btn-outline-primary radius-8 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:file-spreadsheet"></iconify-icon> Download Template Import
                        </a>
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">File Excel/CSV <span class="text-danger-600">*</span></label>
                        <input type="file" class="form-control radius-8" name="file_import" accept=".xlsx,.csv" required>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>Kolom Wajib/Didukung</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_keys($import_columns) as $column): ?>
                                    <tr>
                                        <td><?php echo $column ?></td>
                                        <td><?php echo $column === 'Nama Siswa' ? 'Wajib diisi' : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning-600 radius-8">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom-0">
                <h5 class="modal-title fs-5">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-24 text-center">
                <iconify-icon icon="lucide:alert-triangle" style="font-size: 48px;" class="text-danger mb-16"></iconify-icon>
                <h6 class="mb-8">Apakah Anda yakin ingin menghapus data calon siswa ini?</h6>
                <div class="text-secondary-light mb-0" id="deleteModalMessage">Data calon siswa akan dihapus permanen dari sistem.</div>
            </div>
            <div class="modal-footer py-16 px-24 border-top-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary radius-8 px-20" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnConfirmDeleteAction" class="btn btn-danger-600 radius-8 px-20">Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    document.querySelectorAll('.btn-delete-calon').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            let url = this.getAttribute('href');
            let name = this.getAttribute('data-nama');
            let isActive = this.getAttribute('data-active') === '1';
            
            let message = 'Data calon siswa <strong>' + name + '</strong> akan dihapus permanen dari sistem.';
            if (isActive) {
                message += '<br><span class="text-warning-main fw-semibold mt-2 d-block">Catatan: Siswa ini sudah aktif. Data siswa aktif tidak akan ikut terhapus.</span>';
            }
            
            document.getElementById('deleteModalMessage').innerHTML = message;
            document.getElementById('btnConfirmDeleteAction').setAttribute('href', url);
            
            let myModal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
            myModal.show();
        });
    });
</script>
