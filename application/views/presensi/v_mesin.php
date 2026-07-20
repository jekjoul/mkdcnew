<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Row Konfigurasi API -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-600 text-light d-flex align-items-center justify-content-between">
                    <h6 class="text-light mb-0">Konfigurasi Integrasi Sidik Jari</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-15">
                            <label class="form-label fw-bold text-primary-light text-sm mb-8">URL Endpoint API Server Online</label>
                            <div class="input-group">
                                <input type="text" class="form-control radius-8 bg-light" value="<?php echo url('api/presensi') ?>" readonly id="apiUrl">
                                <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('apiUrl').value); alert('URL disalin!')">
                                    Salin URL
                                </button>
                            </div>
                            <span class="text-xs text-secondary-light">Gunakan URL ini pada file <code>config.json</code> di komputer lokal sekolah.</span>
                        </div>
                        <div class="col-md-6 mb-15">
                            <label class="form-label fw-bold text-primary-light text-sm mb-8">API Token Keamanan</label>
                            <div class="input-group">
                                <input type="text" class="form-control radius-8 bg-light" value="<?php echo $api_token ?>" readonly id="apiToken">
                                <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('apiToken').value); alert('Token disalin!')">
                                    Salin Token
                                </button>
                            </div>
                            <span class="text-xs text-secondary-light">Token rahasia ini digunakan untuk otentikasi koneksi antara komputer lokal dan server online.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row Antrean Tugas -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light">Antrean Sinkronisasi ke Mesin Lokal</h6>
                    </div>
                    <span class="badge bg-warning-focus text-warning-main px-12 py-6 radius-4 text-xs fw-semibold">
                        Menampilkan 100 Tugas Terbaru
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="tasksTable" data-page-length='25'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">ID</th>
                                    <th scope="col" class="text-center">Aksi Perubahan</th>
                                    <th scope="col" class="text-center">PIN</th>
                                    <th scope="col">Nama User</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center">Percobaan</th>
                                    <th scope="col">Keterangan Error</th>
                                    <th scope="col" class="text-center">Waktu Tugas</th>
                                    <th scope="col" class="text-center">Aksi Kontrol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tasks)): ?>
                                    <?php foreach ($tasks as $t): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $t->id; ?></td>
                                            <td class="text-center">
                                                <?php if ($t->action === 'SET_USER'): ?>
                                                    <span class="badge bg-success-focus text-success-main px-8 py-4 radius-4 text-xs">DAFTAR / UPDATE</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-focus text-danger-main px-8 py-4 radius-4 text-xs">HAPUS USER</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center fw-bold"><?php echo $t->pin; ?></td>
                                            <td><?php echo html_escape($t->nama ?: '-'); ?></td>
                                            <td class="text-center">
                                                <?php if ($t->status === 'success'): ?>
                                                    <span class="badge bg-success-focus text-success-main px-10 py-6 radius-4 text-xs">Sukses</span>
                                                <?php elseif ($t->status === 'pending'): ?>
                                                    <span class="badge bg-warning-focus text-warning-main px-10 py-6 radius-4 text-xs">Antre (Pending)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-focus text-danger-main px-10 py-6 radius-4 text-xs">Gagal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo $t->attempts; ?> / 3</td>
                                            <td class="text-danger-600 text-sm"><?php echo html_escape($t->error_message ?: '-'); ?></td>
                                            <td class="text-center"><?php echo date('d-m-Y H:i:s', strtotime($t->created_at)); ?></td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                    <?php if ($t->status === 'failed'): ?>
                                                        <a href="<?php echo url('presensi/reset_task/' . $t->id); ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Kirim Ulang">
                                                            <iconify-icon icon="solar:refresh-linear"></iconify-icon>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo url('presensi/hapus_task/' . $t->id); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hapus Tugas" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini dari antrean?')">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-secondary-light py-20">Antrean tugas sinkronisasi kosong.</td>
                                    </tr>
                                <?php endif; ?>
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
    let table = new DataTable('#tasksTable');
</script>
