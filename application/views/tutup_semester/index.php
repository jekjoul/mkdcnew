<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Breadcrumb -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0 text-primary-light">Penutupan Semester & Rekapitulasi Akhir</h6>
            <p class="text-secondary-light text-sm mb-0">Proses rekapitulasi data absensi, penguncian data akademik, serta penonaktifan semester aktif.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="<?php echo url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-secondary-light">Penutupan Semester</li>
        </ul>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Caution Banner -->
    <div class="alert alert-danger radius-12 p-20 mb-24 border border-danger-200 bg-danger-50 text-danger-900 shadow-xs">
        <div class="d-flex align-items-start gap-3">
            <div class="w-48-px h-48-px rounded-circle bg-danger-600 text-white d-flex align-items-center justify-content-center flex-shrink-0 text-2xl">
                <iconify-icon icon="solar:danger-triangle-bold"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-danger-900">PERINGATAN KEAMANAN TINGKAT TINGGI</h6>
                <p class="text-sm mb-0 text-danger-800">
                    Modul ini digunakan untuk <strong>merekapitulasi seluruh data absensi, mengunci data nilai, serta menonaktifkan periode akademik aktif saat ini</strong>. 
                    Proses ini berdampak luas pada seluruh data sekolah. Harap gunakan modul ini dengan <strong>sangat hati-hati</strong> dan hanya diproses pada akhir periode semester.
                </p>
            </div>
        </div>
    </div>

    <!-- Status Active Semester Card -->
    <div class="row g-4 mb-24">
        <div class="col-lg-6">
            <div class="card border-0 radius-12 shadow-xs h-100">
                <div class="card-header bg-primary-900 text-white p-20 radius-top-12">
                    <h6 class="mb-0 text-white fw-bold d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:calendar-bold" class="text-xl"></iconify-icon> Status Periode Akademik Saat Ini
                    </h6>
                </div>
                <div class="card-body p-24">
                    <?php if ($active_semester): ?>
                        <div class="d-flex align-items-center justify-content-between mb-20 p-16 bg-primary-50 radius-8 border border-primary-200">
                            <div>
                                <span class="text-xs text-primary-700 text-uppercase fw-bold d-block">Tahun Pelajaran & Semester Aktif</span>
                                <h3 class="mb-0 text-primary-900 fw-bold mt-1"><?php echo html_escape($active_semester->tahun_pelajaran) ?></h3>
                                <span class="badge bg-primary-600 text-white radius-4 px-10 py-4 mt-2">Semester <?php echo html_escape($active_semester->semester) ?></span>
                            </div>
                            <div class="w-56-px h-56-px radius-12 bg-primary-600 text-white d-flex align-items-center justify-content-center text-3xl">
                                <iconify-icon icon="solar:lock-keyhole-linear"></iconify-icon>
                            </div>
                        </div>

                        <div class="text-xs text-secondary-light mb-20">
                            Apabila Anda melanjutkan proses di bawah ini, Tahun Pelajaran <strong><?php echo html_escape($active_semester->tahun_pelajaran) ?> (Semester <?php echo html_escape($active_semester->semester) ?>)</strong> akan otomatis diubah statusnya menjadi <span class="badge bg-danger-50 text-danger-600">Nonaktif</span>.
                        </div>

                        <button type="button" class="btn btn-danger-600 text-white radius-8 px-20 py-12 fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSafetyStep1">
                            <iconify-icon icon="solar:lock-keyhole-bold" class="text-xl"></iconify-icon> Multi-Step Prosedur Tutup Semester Ini
                        </button>
                    <?php else: ?>
                        <div class="text-center py-32 text-neutral-400">
                            <iconify-icon icon="solar:info-circle-linear" style="font-size: 36px;"></iconify-icon>
                            <div class="mt-8 fw-semibold text-neutral-600">Tidak ada Tahun Pelajaran & Semester yang sedang AKTIF saat ini.</div>
                            <div class="text-xs text-secondary-light mt-4">Seluruh periode semester telah ditutup atau belum diaktifkan kembali.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 radius-12 shadow-xs h-100">
                <div class="card-header bg-white border-bottom p-20">
                    <h6 class="mb-0 text-primary-light fw-bold d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:checklist-bold" class="text-primary-600 text-xl"></iconify-icon>
                        Daftar Aksi Otomatis Penutupan Semester
                    </h6>
                </div>
                <div class="card-body p-20">
                    <ul class="list-group list-group-flush text-sm">
                        <li class="list-group-item d-flex align-items-center gap-3 px-0 py-10 border-bottom">
                            <span class="w-28-px h-28-px rounded-circle bg-success-50 text-success-600 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">1</span>
                            <div>
                                <span class="fw-semibold text-neutral-800 d-block">Merekap Absensi Agenda Pembelajaran Siswa</span>
                                <span class="text-xs text-secondary-light">Merangkum statistik Hadir, Izin, Sakit, Alpa, Terlambat per siswa per mapel per semester.</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0 py-10 border-bottom">
                            <span class="w-28-px h-28-px rounded-circle bg-success-50 text-success-600 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">2</span>
                            <div>
                                <span class="fw-semibold text-neutral-800 d-block">Merekap Presensi Kehadiran Fingerprint Siswa</span>
                                <span class="text-xs text-secondary-light">Merangkum akumulasi kehadiran harian mesin fingerprint siswa per semester.</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0 py-10 border-bottom">
                            <span class="w-28-px h-28-px rounded-circle bg-success-50 text-success-600 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">3</span>
                            <div>
                                <span class="fw-semibold text-neutral-800 d-block">Merekap Presensi Kehadiran Fingerprint Guru (Bulanan)</span>
                                <span class="text-xs text-secondary-light">Merangkum jam kerja & kehadiran harian guru/PTK secara khusus per bulan.</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0 py-10 border-bottom">
                            <span class="w-28-px h-28-px rounded-circle bg-warning-50 text-warning-600 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">4</span>
                            <div>
                                <span class="fw-semibold text-neutral-800 d-block">Mengunci Seluruh Agenda KBM & Pembelajaran / Jadwal</span>
                                <span class="text-xs text-secondary-light">Mengunci materi agenda dan meng-set status Pembelajaran & Jadwal menjadi <strong>Nonaktif</strong>.</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0 py-10 border-bottom">
                            <span class="w-28-px h-28-px rounded-circle bg-danger-50 text-danger-600 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">5</span>
                            <div>
                                <span class="fw-semibold text-neutral-800 d-block">Deaktivasi Seluruh Data Nilai & Tahun Pelajaran</span>
                                <span class="text-xs text-secondary-light">Mengubah status data nilai dan Tahun Pelajaran aktif saat ini menjadi <strong>Nonaktif</strong>.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log History Card -->
    <div class="card border-0 radius-12 shadow-xs">
        <div class="card-header bg-white border-bottom p-20">
            <h6 class="mb-0 text-primary-light fw-bold d-flex align-items-center gap-2">
                <iconify-icon icon="solar:history-bold" class="text-primary-600 text-xl"></iconify-icon>
                Riwayat Audit Log Penutupan Semester
            </h6>
        </div>
        <div class="card-body p-20">
            <div class="table-responsive">
                <table class="table bordered-table align-middle w-100 mb-0" id="tableLogTutupSemester">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 170px;">Tanggal & Waktu</th>
                            <th>Tahun Pelajaran & Semester</th>
                            <th>Eksekutor (Admin)</th>
                            <th class="text-center" style="width: 150px;">Status Eksekusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history_logs)): ?>
                            <?php $no = 1; foreach ($history_logs as $log): ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?php echo $no++ ?></td>
                                    <td>
                                        <span class="fw-semibold text-primary-light d-block"><?php echo date('d M Y', strtotime($log->executed_at)) ?></span>
                                        <span class="text-xs text-secondary-light"><?php echo date('H:i:s', strtotime($log->executed_at)) ?> WIB</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary-900"><?php echo html_escape($log->tahun_pelajaran) ?></span>
                                        <span class="badge bg-primary-50 text-primary-600 radius-4 ms-2">Semester <?php echo html_escape($log->semester) ?></span>
                                    </td>
                                    <td>
                                        <?php $user = $this->users_model->getById($log->executor_user_id); ?>
                                        <span class="fw-medium text-neutral-800 d-block"><?php echo html_escape($user->name ?? 'Admin System') ?></span>
                                        <span class="text-xs text-secondary-light">User ID: #<?php echo $log->executor_user_id ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Selesai & Direkap</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-neutral-400 py-24">
                                    <iconify-icon icon="solar:history-linear" style="font-size: 28px;"></iconify-icon>
                                    <div class="mt-4 text-xs">Belum ada riwayat penutupan semester yang tercatat dalam log audit.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($active_semester): ?>
    <?php $expected_phrase = 'TUTUP SEMESTER ' . strtoupper($active_semester->semester) . ' ' . $active_semester->tahun_pelajaran; ?>

    <!-- MODAL LAPIS 1: RINGKASAN & KONFIRMASI AWAL -->
    <div class="modal fade" id="modalSafetyStep1" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
                <div class="modal-header bg-danger-900 text-white p-20">
                    <h6 class="modal-title text-white mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl"></iconify-icon>
                        LAPIS 1 PERINGATAN: Konfirmasi Penutupan Semester
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="p-16 radius-8 bg-danger-50 border border-danger-200 text-danger-900 mb-20">
                        <h6 class="fw-bold mb-1 text-danger-900">Anda hendak menutup Semester <?php echo html_escape($active_semester->semester) ?> <?php echo html_escape($active_semester->tahun_pelajaran) ?></h6>
                        <p class="text-sm mb-0">Pastikan seluruh proses pembelajaran, penginputan nilai, dan presensi untuk semester ini telah selesai sepenuhnya.</p>
                    </div>

                    <h6 class="fw-bold text-neutral-900 mb-12 text-sm">Aksi yang akan otomatis diproses oleh sistem:</h6>
                    <ol class="text-sm text-neutral-700 ps-3 mb-20">
                        <li class="mb-2">Merekapitulasi absensi agenda siswa ke tabel agregat per semester.</li>
                        <li class="mb-2">Merekapitulasi presensi harian fingerprint siswa per semester.</li>
                        <li class="mb-2">Merekapitulasi presensi harian fingerprint guru/PTK secara khusus per bulan.</li>
                        <li class="mb-2">Mengunci seluruh agenda pembelajaran KBM agar tidak dapat diedit kembali.</li>
                        <li class="mb-2">Mengubah status seluruh Pembelajaran & Jadwal Pelajaran aktif menjadi <strong>Nonaktif</strong>.</li>
                        <li class="mb-2">Mengubah status seluruh Data Nilai aktif menjadi <strong>Nonaktif</strong> (mengunci nilai akhir).</li>
                        <li class="mb-2">Mengubah status Tahun Pelajaran & Semester aktif saat ini menjadi <strong>Nonaktif</strong>.</li>
                    </ol>
                </div>
                <div class="modal-footer bg-neutral-50 p-16 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger-600 text-white radius-8 px-20 py-10 fw-bold" data-bs-toggle="modal" data-bs-target="#modalSafetyStep2">
                        Saya Mengerti & Lanjut ke Lapis 2 <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL LAPIS 2 & 3: FRASA TYPING & PASSWORD VERIFICATION -->
    <div class="modal fade" id="modalSafetyStep2" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
                <div class="modal-header bg-danger-900 text-white p-20">
                    <h6 class="modal-title text-white mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:shield-warning-bold" class="text-xl"></iconify-icon>
                        LAPIS 2 & 3: Verifikasi Keamanan Akhir
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo form_open('tutup_semester/proses_tutup_semester', ['id' => 'form-tutup-semester']); ?>
                <div class="modal-body p-24">
                    <!-- LAPIS 2: FRASA TYPING -->
                    <div class="mb-20">
                        <label class="form-label fw-bold text-neutral-900 text-sm mb-6">
                            Lapis 2: Ketik Frasa Konfirmasi Persis Di Bawah Ini:
                        </label>
                        <div class="p-12 radius-8 bg-neutral-100 border text-center font-monospace fw-bold text-danger-600 mb-8 select-all">
                            <?php echo $expected_phrase ?>
                        </div>
                        <input type="text" name="confirm_phrase" id="input_confirm_phrase" class="form-control radius-8 p-12 text-center font-monospace fw-bold" placeholder="Ketik frasa konfirmasi di sini..." required autocomplete="off">
                    </div>

                    <!-- LAPIS 3: PASSWORD ADMIN VERIFICATION -->
                    <div class="mb-12">
                        <label class="form-label fw-bold text-neutral-900 text-sm mb-6">
                            Lapis 3: Masukkan Password Admin (Akun Anda)
                        </label>
                        <input type="password" name="admin_password" id="input_admin_password" class="form-control radius-8 p-12" placeholder="Masukkan password akun admin Anda..." required>
                    </div>
                </div>

                <div class="modal-footer bg-neutral-50 p-16 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger-600 text-white radius-8 px-20 py-10 fw-bold shadow-sm" id="btn-submit-tutup">
                        <iconify-icon icon="solar:lock-keyhole-bold" class="me-1"></iconify-icon> EKSEKUSI PENUTUPAN SEMESTER
                    </button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    if ($('#tableLogTutupSemester').length > 0) {
        $('#tableLogTutupSemester').DataTable({
            pageLength: 10,
            order: [[0, 'asc']]
        });
    }
});
</script>
