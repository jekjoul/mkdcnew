<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Breadcrumb -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0 text-primary-light">Detail Agenda Pembelajaran</h6>
            <p class="text-secondary-light text-sm mb-0">Rincian presensi siswa realtime, materi KBM, serta pengisian catatan pelaksanaan agenda harian.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="<?php echo url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">
                <a href="<?php echo url(logged('role') == '1' ? 'perangkat_pembelajaran/agenda' : 'guru/agenda') ?>" class="hover-text-primary">
                    Agenda Pembelajaran
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-secondary-light">Detail Agenda</li>
        </ul>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Back Button & Header Summary Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-body p-20">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?php echo url(logged('role') == '1' ? 'perangkat_pembelajaran/agenda' : 'guru/agenda') ?>" class="btn btn-outline-secondary radius-8 px-12 py-6 d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:alt-arrow-left-linear" class="text-lg"></iconify-icon> Kembali ke Daftar Agenda
                    </a>
                    <div>
                        <h6 class="mb-0 text-primary-900 fw-bold">Pertemuan Ke-<?php echo $agenda->pertemuan_ke ?> — <?php echo html_escape($agenda->nama_mapel) ?></h6>
                        <span class="text-xs text-secondary-light">Rombel: <strong><?php echo html_escape($agenda->nama_rombel) ?></strong> | Tahun Pelajaran <?php echo html_escape($agenda->tahun_pelajaran) ?> (Semester <?php echo html_escape($agenda->semester) ?>)</span>
                    </div>
                </div>

                <div>
                    <?php
                    $today_str = date('Y-m-d');
                    $now_time  = date('H:i');
                    $is_past_date = ($agenda->tanggal < $today_str);
                    $is_today     = ($agenda->tanggal === $today_str);
                    $is_late      = false;

                    if ($agenda->status === 'Belum') {
                        if ($is_past_date) {
                            $is_late = true;
                        } elseif ($is_today && !empty($agenda->jam_mulai) && $now_time > $agenda->jam_mulai) {
                            $is_late = true;
                        }
                    }
                    ?>
                    <?php if ($agenda->status === 'Terlaksana'): ?>
                        <span class="badge bg-success-focus text-success-main px-16 py-8 radius-6 fs-6 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Terlaksana
                        </span>
                    <?php elseif ($is_late): ?>
                        <span class="badge bg-danger-focus text-danger-main px-14 py-8 radius-6 fs-6 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:danger-triangle-bold"></iconify-icon> Terlambat / Melebihi Jadwal Masuk
                        </span>
                    <?php elseif ($agenda->status === 'Libur'): ?>
                        <span class="badge bg-danger-focus text-danger-main px-16 py-8 radius-6 fs-6">Libur KBM</span>
                    <?php else: ?>
                        <span class="badge bg-neutral-200 text-neutral-700 px-16 py-8 radius-6 fs-6">Belum Dilaksanakan</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Meta Info Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-body p-20">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="row g-3 text-center text-sm-start flex-grow-1">
                    <div class="col-sm-4 border-end-sm">
                        <span class="text-xs text-secondary-light d-block text-uppercase fw-semibold">Hari & Tanggal</span>
                        <span class="fw-bold text-primary-900 fs-6"><?php echo html_escape($agenda->hari) ?>, <?php echo date('d M Y', strtotime($agenda->tanggal)) ?></span>
                    </div>
                    <div class="col-sm-4 border-end-sm">
                        <span class="text-xs text-secondary-light d-block text-uppercase fw-semibold">Jadwal Masuk (Waktu)</span>
                        <?php if (!empty($agenda->jam_mulai)): ?>
                            <span class="fw-bold text-primary-900 fs-6"><?php echo html_escape($agenda->jam_mulai) ?> - <?php echo html_escape($agenda->jam_selesai) ?> WIB</span>
                        <?php else: ?>
                            <span class="text-neutral-400">Belum diatur</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-xs text-secondary-light d-block text-uppercase fw-semibold">Guru Pengampu</span>
                        <span class="fw-bold text-primary-900 fs-6"><?php echo html_escape($agenda->nama_ptk ?: 'Guru Mata Pelajaran') ?></span>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary radius-8 px-14 py-8 d-inline-flex align-items-center gap-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalSesuaikanJadwal">
                        <iconify-icon icon="solar:calendar-minimalistic-bold" class="text-lg"></iconify-icon> Sesuaikan Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="row g-4">
        <!-- Kolom Kiri (8): Tab 1 Presensi Siswa & Tab 2 Materi Terpadu -->
        <div class="col-lg-8">
            <div class="card border-0 radius-12 shadow-xs mb-24">
                <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <ul class="nav nav-tabs style-three mb-0" role="tablist">
                        <!-- TAB 1 (DEFAULT ACTIVE): KEHADIRAN SISWA -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-16 py-10 fw-semibold" id="tab-presensi-btn" data-bs-toggle="tab" data-bs-target="#tab-presensi-siswa" type="button" role="tab">
                                <iconify-icon icon="solar:users-group-two-rounded-bold" class="me-1 text-primary-600"></iconify-icon>
                                Kehadiran Siswa
                                <span class="badge bg-primary-50 text-primary-600 radius-4 ms-2 px-8 py-2"><?php echo count($siswa_presensi) ?> Siswa</span>
                            </button>
                        </li>
                        <!-- TAB 2: MATERI & KEGIATAN KBM (TERPADU) -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-16 py-10 fw-semibold" id="tab-materi-btn" data-bs-toggle="tab" data-bs-target="#tab-materi-kegiatan" type="button" role="tab">
                                <iconify-icon icon="solar:document-text-bold" class="me-1 text-info-600"></iconify-icon>
                                Materi & Kegiatan KBM
                            </button>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-50 text-success-700 radius-4 px-10 py-6 text-xs d-inline-flex align-items-center gap-1" id="autosave-global-indicator">
                            <iconify-icon icon="solar:diskette-bold"></iconify-icon> Realtime Auto-Save Aktif
                        </span>
                    </div>
                </div>

                <div class="card-body p-20">
                    <div class="tab-content">
                        <!-- TAB 1: KEHADIRAN SISWA (DEFAULT ACTIVE) -->
                        <div class="tab-pane fade show active" id="tab-presensi-siswa" role="tabpanel">
                            <!-- Presensi Counter Badges -->
                            <?php
                            $cnt_hadir = 0; $cnt_izin = 0; $cnt_sakit = 0; $cnt_alpa = 0; $cnt_belum = 0;
                            foreach ($siswa_presensi as $sp) {
                                if ($sp->status_presensi === 'Hadir') $cnt_hadir++;
                                else if ($sp->status_presensi === 'Izin') $cnt_izin++;
                                else if ($sp->status_presensi === 'Sakit') $cnt_sakit++;
                                else if ($sp->status_presensi === 'Alpa') $cnt_alpa++;
                                else $cnt_belum++;
                            }
                            ?>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-16 p-12 bg-neutral-50 radius-8 border">
                                <div class="d-flex gap-3 flex-wrap">
                                    <span class="badge bg-neutral-200 text-neutral-800 radius-4 px-10 py-6" id="badge-stat-belum">Belum Diabsen: <?php echo $cnt_belum ?></span>
                                    <span class="badge bg-success-focus text-success-main radius-4 px-10 py-6" id="badge-stat-hadir">Hadir: <?php echo $cnt_hadir ?></span>
                                    <span class="badge bg-info-focus text-info-main radius-4 px-10 py-6" id="badge-stat-izin">Izin: <?php echo $cnt_izin ?></span>
                                    <span class="badge bg-warning-focus text-warning-main radius-4 px-10 py-6" id="badge-stat-sakit">Sakit: <?php echo $cnt_sakit ?></span>
                                    <span class="badge bg-danger-focus text-danger-main radius-4 px-10 py-6" id="badge-stat-alpa">Tanpa Keterangan: <?php echo $cnt_alpa ?></span>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary radius-6 px-10 py-4" id="btn-set-all-hadir">
                                    <iconify-icon icon="solar:check-read-bold" class="me-1"></iconify-icon> Tandai Semua Hadir
                                </button>
                            </div>

                            <!-- Table Presensi Siswa -->
                            <div class="table-responsive">
                                <table class="table bordered-table align-middle w-100" id="tablePresensiSiswa">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 40px;">No</th>
                                            <th>Nama Siswa & NISN</th>
                                            <th class="text-center" style="width: 50px;">L/P</th>
                                            <th class="text-center" style="width: 320px;">Status Kehadiran</th>
                                            <th>Catatan / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($siswa_presensi)): ?>
                                            <?php $ns = 1; foreach ($siswa_presensi as $s): ?>
                                                <tr id="siswa-row-<?php echo $s->id_siswa ?>">
                                                    <td class="text-center fw-semibold"><?php echo $ns++ ?></td>
                                                    <td>
                                                        <span class="fw-semibold text-primary-900 d-block"><?php echo html_escape($s->nama_siswa) ?></span>
                                                        <span class="text-xs text-secondary-light">NISN: <?php echo html_escape($s->nisn ?: '-') ?> | NIPD: <?php echo html_escape($s->nipd ?: '-') ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-neutral-100 text-neutral-700"><?php echo html_escape($s->jenis_kelamin ?: 'L') ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group w-100" role="group" aria-label="Status Presensi Siswa">
                                                            <input type="radio" class="btn-check radio-presensi-siswa" name="status_s_<?php echo $s->id_siswa ?>" id="st_h_<?php echo $s->id_siswa ?>" data-siswa-id="<?php echo $s->id_siswa ?>" value="Hadir" <?php echo ($s->status_presensi === 'Hadir') ? 'checked' : '' ?>>
                                                            <label class="btn btn-xs btn-outline-success px-8 py-6" for="st_h_<?php echo $s->id_siswa ?>">Hadir</label>

                                                            <input type="radio" class="btn-check radio-presensi-siswa" name="status_s_<?php echo $s->id_siswa ?>" id="st_i_<?php echo $s->id_siswa ?>" data-siswa-id="<?php echo $s->id_siswa ?>" value="Izin" <?php echo ($s->status_presensi === 'Izin') ? 'checked' : '' ?>>
                                                            <label class="btn btn-xs btn-outline-info px-8 py-6" for="st_i_<?php echo $s->id_siswa ?>">Izin</label>

                                                            <input type="radio" class="btn-check radio-presensi-siswa" name="status_s_<?php echo $s->id_siswa ?>" id="st_s_<?php echo $s->id_siswa ?>" data-siswa-id="<?php echo $s->id_siswa ?>" value="Sakit" <?php echo ($s->status_presensi === 'Sakit') ? 'checked' : '' ?>>
                                                            <label class="btn btn-xs btn-outline-warning px-8 py-6" for="st_s_<?php echo $s->id_siswa ?>">Sakit</label>

                                                            <input type="radio" class="btn-check radio-presensi-siswa" name="status_s_<?php echo $s->id_siswa ?>" id="st_a_<?php echo $s->id_siswa ?>" data-siswa-id="<?php echo $s->id_siswa ?>" value="Alpa" <?php echo ($s->status_presensi === 'Alpa') ? 'checked' : '' ?>>
                                                            <label class="btn btn-xs btn-outline-danger px-8 py-6" for="st_a_<?php echo $s->id_siswa ?>">Alpa</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="position-relative">
                                                            <input type="text" class="form-control form-control-sm radius-6 input-catatan-siswa" data-siswa-id="<?php echo $s->id_siswa ?>" value="<?php echo html_escape($s->catatan_presensi ?? '') ?>" placeholder="Keterangan...">
                                                            <span class="position-absolute end-0 top-50 translate-middle-y me-8 text-success d-none save-indicator" id="indicator-<?php echo $s->id_siswa ?>">
                                                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-neutral-400 py-24">
                                                    <iconify-icon icon="solar:users-group-two-rounded-linear" style="font-size: 28px;"></iconify-icon>
                                                    <div class="mt-4 text-xs">Belum ada data siswa terdaftar dalam rombel ini.</div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: MATERI & KEGIATAN KBM (TERPADU) -->
                        <div class="tab-pane fade" id="tab-materi-kegiatan" role="tabpanel">
                            <!-- Section 1: Materi Pembelajaran -->
                            <div class="mb-24">
                                <label class="form-label fw-bold text-primary-900 text-sm mb-8 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:document-text-bold" class="text-primary-600"></iconify-icon> 1. Materi Pembelajaran
                                </label>
                                <div class="p-16 radius-8 border bg-neutral-50 text-neutral-900" style="min-height: 140px;">
                                    <?php echo !empty($agenda->materi) ? $agenda->materi : '<span class="text-neutral-400">Belum ada materi pembelajaran yang diisikan.</span>' ?>
                                </div>
                            </div>

                            <!-- Section 2: Kegiatan KBM -->
                            <div class="mb-24">
                                <label class="form-label fw-bold text-primary-900 text-sm mb-8 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:notes-bold" class="text-info-600"></iconify-icon> 2. Kegiatan Pembelajaran (KBM)
                                </label>
                                <div class="p-16 radius-8 border bg-neutral-50 text-neutral-900" style="min-height: 140px;">
                                    <?php echo !empty($agenda->kegiatan) ? $agenda->kegiatan : '<span class="text-neutral-400">Belum ada rincian kegiatan KBM yang diisikan.</span>' ?>
                                </div>
                            </div>

                            <!-- Section 3: Media & Berkas Slide Presentasi -->
                            <div>
                                <label class="form-label fw-bold text-primary-900 text-sm mb-8 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:videocamera-record-bold" class="text-danger-600"></iconify-icon> 3. Media & Berkas Slide Interaktif
                                </label>
                                <div class="p-20 radius-8 border bg-white">
                                    <?php if (!empty($agenda->slide_drive_id) || !empty($agenda->link_video)): ?>
                                        <div class="d-flex flex-column gap-3">
                                            <?php if (!empty($agenda->slide_drive_id)): ?>
                                                <div class="p-16 radius-8 bg-info-50 border border-info-200 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <span class="fw-semibold text-info-900 d-block mb-1">
                                                            <iconify-icon icon="logos:google-drive" class="me-1"></iconify-icon> Slide Presentasi Interaktif (Google Drive)
                                                        </span>
                                                        <span class="text-xs text-info-700">Berkas slide tersimpan di Google Drive Anda.</span>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-info-600 text-white radius-8 px-14 py-6 btn-preview-doc"
                                                            data-drive-id="<?php echo html_escape($agenda->slide_drive_id) ?>"
                                                            data-title="Slide Agenda Pertemuan Ke-<?php echo $agenda->pertemuan_ke ?>">
                                                            <iconify-icon icon="lucide:eye" class="me-1"></iconify-icon> Lihat Slide (Preview)
                                                        </button>
                                                        <?php $slide_drive_url = 'https://docs.google.com/document/d/' . html_escape($agenda->slide_drive_id) . '/edit'; ?>
                                                        <a href="<?php echo $slide_drive_url ?>" target="_blank" rel="noopener noreferrer"
                                                           class="btn btn-sm btn-success-600 text-white radius-8 px-14 py-6">
                                                            <iconify-icon icon="logos:google-drive" class="me-1"></iconify-icon> Edit Online
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($agenda->link_video)): ?>
                                                <div class="p-16 radius-8 bg-danger-50 border border-danger-200 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <span class="fw-semibold text-danger-900 d-block mb-1">
                                                            <iconify-icon icon="logos:youtube-icon" class="me-1"></iconify-icon> Video Pembelajaran (YouTube)
                                                        </span>
                                                        <span class="text-xs text-danger-700"><?php echo html_escape($agenda->link_video) ?></span>
                                                    </div>
                                                    <a href="<?php echo html_escape($agenda->link_video) ?>" target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-sm btn-danger-600 text-white radius-8 px-14 py-6">
                                                        <iconify-icon icon="solar:play-circle-bold" class="me-1"></iconify-icon> Tonton Video
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-16 text-neutral-400">
                                            <iconify-icon icon="solar:videocamera-record-linear" style="font-size: 28px;"></iconify-icon>
                                            <div class="mt-4 text-xs">Belum ada slide presentasi atau link video pembelajaran.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (4): Form Catatan Tambahan Agenda & Tombol Ubah Status -->
        <div class="col-lg-4">
            <div class="card border-0 radius-12 shadow-xs mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <h6 class="mb-0 text-primary-light fw-bold d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:pen-bold" class="text-primary-600 text-xl"></iconify-icon>
                        Catatan & Status Pelaksanaan Agenda
                    </h6>
                </div>
                <div class="card-body p-20">
                    <?php 
                    $action_url = url((logged('role') == '1' ? 'perangkat_pembelajaran' : 'guru') . '/update_status_agenda/' . $agenda->id_agenda);
                    echo form_open($action_url, ['id' => 'form-update-status']);
                    ?>
                    
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Form Catatan Tambahan KBM Agenda</label>
                        <textarea name="catatan" class="form-control radius-8 p-12 text-sm" rows="5"
                                  placeholder="Tuliskan catatan pelaksanaan KBM, respons murid, kendala, atau rangkuman evaluasi..."><?php echo html_escape($agenda->catatan) ?></textarea>
                        <div class="text-xs text-secondary-light mt-6">Catatan ini akan tersimpan saat Anda memperbarui status pelaksanaan agenda.</div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <?php if ($agenda->status === 'Terlaksana'): ?>
                            <button type="submit" name="status" value="Terlaksana" class="btn btn-primary-600 text-white radius-8 py-12 w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon> Simpan Perubahan Catatan
                            </button>
                            <button type="submit" name="status" value="Belum" class="btn btn-warning-600 text-white radius-8 py-10 w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon> Tandai Belum Dilaksanakan
                            </button>
                        <?php else: ?>
                            <button type="submit" name="status" value="Terlaksana" class="btn btn-success-600 text-white radius-8 py-14 w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon> Tandai Sudah Dilaksanakan
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Document Preview (Google Drive) -->
<div class="modal fade" id="modalPreviewDoc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw;">
        <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg" style="height: 85vh;">
            <div class="modal-header bg-primary-900 text-white py-12 px-20">
                <h6 class="modal-title text-white mb-0 d-flex align-items-center gap-2" id="preview-modal-title">
                    <iconify-icon icon="solar:document-text-bold"></iconify-icon> Pratinjau Dokumen
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="preview-open-external" target="_blank" class="btn btn-xs btn-outline-light radius-6 px-10 py-4 d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:export-linear"></iconify-icon> Buka Tab Baru
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-neutral-100 position-relative">
                <div id="preview-loading" class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-xs text-secondary-light mt-8">Memuat dokumen Google Drive...</div>
                </div>
                <iframe id="preview-iframe" src="" class="w-100 h-100 border-0" style="min-height: 100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Penyesuaian Jadwal & Waktu -->
<div class="modal fade" id="modalSesuaikanJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <div class="modal-header bg-primary-900 text-white py-14 px-20">
                <h6 class="modal-title text-white mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:calendar-minimalistic-bold"></iconify-icon> Sesuaikan Jadwal & Waktu KBM
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open(url((logged('role') == '1' ? 'perangkat_pembelajaran' : 'guru') . '/sesuaikan_jadwal_agenda/' . $agenda->id_agenda)); ?>
            <div class="modal-body p-20">
                <div class="alert alert-info radius-8 text-xs mb-16">
                    <iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon>
                    Penyesuaian ini hanya akan merubah <strong>Hari, Tanggal, Jam Masuk, dan Jam Keluar</strong>. Seluruh isi Materi, Kegiatan KBM, dan Presensi Siswa dijamin tetap utuh.
                </div>

                <div class="mb-16">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Tanggal Pembelajaran (KBM)</label>
                    <input type="date" name="tanggal" class="form-control radius-8" value="<?php echo html_escape($agenda->tanggal) ?>" required>
                </div>

                <div class="row g-3 mb-16">
                    <div class="col-6">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Jam Masuk (Mulai)</label>
                        <input type="time" name="jam_mulai" class="form-control radius-8" value="<?php echo html_escape($agenda->jam_mulai) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Jam Keluar (Selesai)</label>
                        <input type="time" name="jam_selesai" class="form-control radius-8" value="<?php echo html_escape($agenda->jam_selesai) ?>">
                    </div>
                </div>

                <div class="form-check radius-8 p-12 bg-neutral-50 border ms-0">
                    <input class="form-check-input ms-0 me-8" type="checkbox" name="sync_master" value="1" id="chkSyncMaster">
                    <label class="form-check-label text-xs text-neutral-800 fw-medium" for="chkSyncMaster">
                        Otomatis sinkronkan jam masuk & keluar dari Jadwal Master KBM
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-neutral-50 px-20 py-12">
                <button type="button" class="btn btn-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary radius-8 px-16">Simpan Perubahan Jadwal</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    var agendaId = <?php echo (int)$agenda->id_agenda ?>;
    var autosaveUrl = "<?php echo url((logged('role') == '1' ? 'perangkat_pembelajaran' : 'guru') . '/autosave_presensi_siswa/' . $agenda->id_agenda) ?>";

    var totalSiswa = <?php echo count($siswa_presensi) ?>;

    // Hitung ulang statistik presensi secara instan
    function recalcStats() {
        var h = 0, i = 0, s = 0, a = 0;
        $('.radio-presensi-siswa:checked').each(function() {
            var val = $(this).val();
            if (val === 'Hadir') h++;
            else if (val === 'Izin') i++;
            else if (val === 'Sakit') s++;
            else if (val === 'Alpa') a++;
        });
        var belum = totalSiswa - (h + i + s + a);
        $('#badge-stat-belum').text('Belum Diabsen: ' + (belum < 0 ? 0 : belum));
        $('#badge-stat-hadir').text('Hadir: ' + h);
        $('#badge-stat-izin').text('Izin: ' + i);
        $('#badge-stat-sakit').text('Sakit: ' + s);
        $('#badge-stat-alpa').text('Tanpa Keterangan: ' + a);
    }

    // Auto-save realtime via AJAX saat status atau catatan siswa diubah
    function triggerAutosave(siswaId) {
        var statusVal = $('input[name="status_s_' + siswaId + '"]:checked').val() || null;
        var catatanVal = $('#siswa-row-' + siswaId + ' .input-catatan-siswa').val();
        var indicator = $('#indicator-' + siswaId);

        recalcStats();
        $('#autosave-global-indicator').removeClass('bg-success-50 text-success-700').addClass('bg-warning-50 text-warning-700').html('<iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Menyimpan...');

        $.ajax({
            url: autosaveUrl,
            type: 'POST',
            data: {
                id_siswa: siswaId,
                status: statusVal,
                catatan: catatanVal
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.status) {
                    indicator.removeClass('d-none').fadeIn().delay(1500).fadeOut();
                    $('#autosave-global-indicator').removeClass('bg-warning-50 text-warning-700').addClass('bg-success-50 text-success-700').html('<iconify-icon icon="solar:diskette-bold"></iconify-icon> Realtime Auto-Save Aktif');
                }
            },
            error: function() {
                $('#autosave-global-indicator').removeClass('bg-warning-50 text-warning-700').addClass('bg-danger-50 text-danger-700').html('<iconify-icon icon="solar:danger-triangle-bold"></iconify-icon> Gagal menyimpan!');
            }
        });
    }

    // Event Listener Radio Status Siswa
    $(document).on('change', '.radio-presensi-siswa', function() {
        var siswaId = $(this).data('siswa-id');
        triggerAutosave(siswaId);
    });

    // Event Listener Input Catatan Siswa
    $(document).on('blur', '.input-catatan-siswa', function() {
        var siswaId = $(this).data('siswa-id');
        triggerAutosave(siswaId);
    });

    // Tombol Reset Semua Hadir
    $('#btn-set-all-hadir').on('click', function() {
        $('.radio-presensi-siswa[value="Hadir"]').prop('checked', true).trigger('change');
    });

    // Preview Google Drive Document Ajax Modal Trigger
    $(document).on('click', '.btn-preview-doc', function() {
        var driveId = $(this).data('drive-id');
        var title = $(this).data('title');
        
        if (!driveId) return;

        var previewUrl = 'https://docs.google.com/document/d/' + driveId + '/preview';
        var editUrl = 'https://docs.google.com/document/d/' + driveId + '/edit';

        $('#preview-modal-title').html('<iconify-icon icon="solar:document-text-bold"></iconify-icon> Pratinjau Dokumen — ' + (title || 'Google Drive'));
        $('#preview-open-external').attr('href', editUrl);

        $('#preview-loading').removeClass('d-none');
        $('#preview-iframe').attr('src', previewUrl);

        var modalEl = document.getElementById('modalPreviewDoc');
        var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        $('#preview-iframe').off('load').on('load', function() {
            $('#preview-loading').addClass('d-none');
        });
    });
});
</script>
