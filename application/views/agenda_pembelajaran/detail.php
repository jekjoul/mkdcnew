<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Header Title Banner -->
    <div class="card border-0 radius-12 bg-primary-600 text-white p-20 mb-24 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-primary-600 fw-bold px-8 py-4 radius-4 text-xs">
                        <?php echo html_escape($item->nama_tingkat . ' - ' . $item->nama_rombel) ?>
                    </span>
                    <span class="badge bg-primary-700 text-white px-8 py-4 radius-4 text-xs">
                        Semester <?php echo html_escape($item->semester) ?> (<?php echo html_escape($item->tahun_pelajaran) ?>)
                    </span>
                    <?php if (!empty($item->status_takeover) && $item->status_takeover === 'Ya'): ?>
                        <span class="badge bg-warning-500 text-white px-8 py-4 radius-4 text-xs">Take-Over Active</span>
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-1 text-white text-wrap" id="displayJudulAgenda">
                    <?php echo html_escape($item->judul_agenda) ?>
                    <button class="btn btn-sm btn-link text-white p-0 ms-2" data-bs-toggle="modal" data-bs-target="#modalJudulAgenda" title="Sunting Judul Agenda">
                        <iconify-icon icon="solar:pen-bold" class="text-lg"></iconify-icon>
                    </button>
                </h4>
                <p class="text-white-75 text-sm mb-0">
                    <iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon> Guru Pengampu: <strong><?php echo html_escape($item->nama_guru_pengampu ?: '-') ?></strong>
                    <?php if (!empty($item->nama_guru_pemilik) && $item->nama_guru_pemilik !== $item->nama_guru_pengampu): ?>
                        <span class="ms-2 opacity-75">(Pemilik Asli: <?php echo html_escape($item->nama_guru_pemilik) ?>)</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?php echo $back_url ?>" class="btn btn-light text-primary-600 radius-8 px-16 py-8 d-flex align-items-center gap-2 fw-semibold text-sm">
                    <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon> Kembali
                </a>
                <button class="btn btn-warning-500 text-white radius-8 px-16 py-8 d-flex align-items-center gap-2 fw-semibold text-sm" data-bs-toggle="modal" data-bs-target="#modalTakeover">
                    <iconify-icon icon="solar:user-hand-up-bold" class="text-lg"></iconify-icon> Take Over Pengampu
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Schedule Alert Status Banner (Peringatan Perubahan Jadwal / Draft Pending) -->
    <?php if (!empty($schedule_status) && $schedule_status['status'] === 'draft_pending'): ?>
        <div class="alert alert-info border-0 radius-12 p-20 mb-24 shadow-xs d-flex align-items-center gap-3">
            <div class="w-48-px h-48-px rounded-circle bg-info-100 text-info-700 d-flex align-items-center justify-content-center flex-shrink-0">
                <iconify-icon icon="solar:clock-circle-bold" class="text-2xl"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold text-info-900 mb-1">Rencana Draft Jadwal Baru Berada dalam Antrean</h6>
                <p class="text-info-800 text-sm mb-0">
                    Jadwal baru untuk mata pelajaran ini telah dirancang sebagai Draft dan akan mulai berlaku efektif pada tanggal <strong><?php echo date('d M Y', strtotime($schedule_status['tanggal_efektif'])) ?></strong>. Agenda saat ini tetap mengikuti jadwal aktif sampai tanggal efektif tiba.
                </p>
            </div>
        </div>
    <?php elseif (!empty($schedule_status) && $schedule_status['status'] === 'warning_schedule_changed'): ?>
        <div class="alert alert-warning border-0 radius-12 p-20 mb-24 shadow-xs d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-48-px h-48-px rounded-circle bg-warning-100 text-warning-700 d-flex align-items-center justify-content-center flex-shrink-0">
                    <iconify-icon icon="solar:bell-bing-bold" class="text-2xl"></iconify-icon>
                </div>
                <div>
                    <h6 class="fw-bold text-warning-900 mb-1">Terdeteksi Perubahan Jadwal Pelajaran!</h6>
                    <p class="text-warning-800 text-sm mb-0">Jadwal mingguan mata pelajaran ini telah diperbarui di menu Jadwal Pelajaran. Klik tombol di samping untuk menyelaraskan tanggal & jam agenda yang belum terlaksana tanpa menghapus isinya.</p>
                </div>
            </div>
            <?php echo form_open($resync_jadwal_url, ['class' => 'd-inline']); ?>
                <button type="submit" class="btn btn-warning-600 text-white radius-8 px-16 py-10 fw-bold d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon> Sesuaikan Jadwal (Sync Otomatis)
                </button>
            <?php echo form_close(); ?>
        </div>
    <?php elseif (!empty($schedule_status) && $schedule_status['status'] === 'warning_no_schedule'): ?>
        <div class="alert alert-danger border-0 radius-12 p-20 mb-24 shadow-xs d-flex align-items-center gap-3">
            <div class="w-48-px h-48-px rounded-circle bg-danger-100 text-danger-700 d-flex align-items-center justify-content-center flex-shrink-0">
                <iconify-icon icon="solar:danger-bold" class="text-2xl"></iconify-icon>
            </div>
            <div>
                <h6 class="fw-bold text-danger-900 mb-1">Jadwal Pelajaran Belum Diatur</h6>
                <p class="text-danger-800 text-sm mb-0">Atur terlebih dahulu jadwal mingguan untuk kelas dan mata pelajaran ini pada menu <strong>Jadwal Pelajaran</strong> sebelum melakukan sync atau generate agenda.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-header bg-white border-bottom p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold text-primary-light mb-1">Jadwal & Agenda Pertemuan KBM</h6>
                <p class="text-secondary-light text-xs mb-0">Tersusun berdasarkan kalender hari efektif dan alokasi jam mengajar mingguan.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (!empty($templates)): ?>
                    <button type="button" class="btn btn-outline-primary radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalPilihTemplate">
                        <iconify-icon icon="solar:copy-bold" class="text-base"></iconify-icon> Pilih / Salin Agenda
                    </button>
                <?php endif; ?>
                <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                    <button type="submit" class="btn btn-outline-secondary radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-1" onclick="return confirm('Peringatan: Reset agenda akan menyusun ulang seluruh pertemuan dari awal. Lanjutkan?')">
                        <iconify-icon icon="solar:restart-linear" class="text-base"></iconify-icon> Reset / Regenerate
                    </button>
                <?php echo form_close(); ?>
                <?php echo form_open($generate_agenda_ai_url, ['class' => 'd-inline']); ?>
                    <button type="submit" class="btn btn-success-600 radius-8 px-14 py-8 text-xs fw-semibold text-white d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:magic-stick-bold" class="text-base"></iconify-icon> Generate via AI
                    </button>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="card-body p-20">
            <?php if (empty($agenda)): ?>
                <div class="text-center py-48 bg-neutral-50 radius-12 border border-dashed">
                    <iconify-icon icon="solar:notebook-square-linear" style="font-size: 56px;" class="text-primary-300 mb-12"></iconify-icon>
                    <h5 class="fw-bold text-primary-light mb-8">Agenda Harian Belum Digenerate</h5>
                    <p class="text-secondary-light text-sm max-w-500-px mx-auto mb-20">Pilih dari agenda yang tersimpan, buat otomatis via Google AI, atau jalankan regenerate berdasarkan kalender efektif.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <?php if (!empty($templates)): ?>
                            <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 text-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalPilihTemplate">
                                <iconify-icon icon="solar:copy-bold" class="me-1"></iconify-icon> Pilih dari Agenda Tersimpan
                            </button>
                        <?php endif; ?>
                        <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                            <button type="submit" class="btn btn-outline-primary radius-8 px-20 py-10 text-sm fw-bold">
                                Generate Agenda Baru
                            </button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo form_open($save_agenda_url); ?>
                    <div class="table-responsive">
                        <table class="table bordered-table align-middle">
                            <thead>
                                <tr class="bg-neutral-100">
                                    <th style="width: 50px;" class="text-center">Ke-</th>
                                    <th style="width: 140px;">Hari / Tanggal</th>
                                    <th style="width: 120px;">Jam KBM</th>
                                    <th>Materi & Pokok Bahasan</th>
                                    <th>Kegiatan & Aktivitas Pembelajaran</th>
                                    <th style="width: 130px;" class="text-center">Status</th>
                                    <th style="width: 150px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agenda as $row): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-primary-600"><?php echo $row->pertemuan_ke ?></td>
                                        <td>
                                            <div class="fw-bold text-primary-900"><?php echo html_escape($row->hari) ?></div>
                                            <div class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($row->tanggal)) ?></div>
                                        </td>
                                        <td>
                                            <div class="text-xs fw-semibold text-neutral-800"><?php echo html_escape($row->jam_mulai ?: '-') ?> - <?php echo html_escape($row->jam_selesai ?: '-') ?></div>
                                            <div class="text-xs text-secondary-light"><?php echo $row->jumlah_jam ?: 0 ?> JP</div>
                                        </td>
                                        <td>
                                            <textarea name="agenda[<?php echo $row->id_agenda ?>][materi]" class="form-control text-sm radius-8" rows="2" placeholder="Isi materi pokok..."><?php echo html_escape($row->materi) ?></textarea>
                                        </td>
                                        <td>
                                            <textarea name="agenda[<?php echo $row->id_agenda ?>][kegiatan]" class="form-control text-sm radius-8" rows="2" placeholder="Isi bentuk kegiatan KBM..."><?php echo html_escape($row->kegiatan) ?></textarea>
                                        </td>
                                        <td class="text-center">
                                            <select name="agenda[<?php echo $row->id_agenda ?>][status]" class="form-select text-xs radius-8 fw-semibold <?php echo $row->status === 'Terlaksana' ? 'border-success text-success-700 bg-success-50' : 'border-warning text-warning-700 bg-warning-50' ?>">
                                                <option value="Belum" <?php echo $row->status === 'Belum' ? 'selected' : '' ?>>Belum</option>
                                                <option value="Terlaksana" <?php echo $row->status === 'Terlaksana' ? 'selected' : '' ?>>Terlaksana</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="agenda[<?php echo $row->id_agenda ?>][catatan]" value="<?php echo html_escape($row->catatan) ?>" class="form-control text-xs radius-8" placeholder="Catatan kelas...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-20 text-end">
                        <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-12 fw-bold text-sm">
                            <iconify-icon icon="solar:disk-bold" class="me-1"></iconify-icon> Simpan Perubahan Agenda
                        </button>
                    </div>
                <?php echo form_close(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Sunting Judul Agenda -->
<div class="modal fade" id="modalJudulAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0">
            <div class="modal-header bg-primary-600 text-white">
                <h6 class="modal-title text-white fw-bold">Sunting Judul Agenda Harian</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($update_judul_url); ?>
            <div class="modal-body p-20">
                <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Judul Agenda Harian Pembelajaran</label>
                <input type="text" name="judul_agenda" value="<?php echo html_escape($item->judul_agenda) ?>" class="form-control radius-8" required placeholder="Contoh: Agenda Harian Mapel Informatika Kelas 7 Semester 1 Tahun 2026/2027">
                <p class="text-xs text-secondary-light mt-8 mb-0">Judul ini akan melekat pada rekap absensi dan jurnal agenda rombel ini.</p>
            </div>
            <div class="modal-footer bg-neutral-50">
                <button type="button" class="btn btn-secondary radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 text-sm fw-bold">Simpan Judul</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal Take Over Pengampu -->
<div class="modal fade" id="modalTakeover" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0">
            <div class="modal-header bg-warning-600 text-white">
                <h6 class="modal-title text-white fw-bold">Take Over Guru Pengampu Agenda</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($takeover_url); ?>
            <div class="modal-body p-20">
                <div class="alert alert-warning radius-8 p-12 text-xs mb-16">
                    <iconify-icon icon="solar:info-circle-bold" class="me-1 text-sm"></iconify-icon>
                    Take-over akan memindahkan hak pengelolaan agenda ini ke Guru Pengampu baru tanpa menghapus riwayat jurnal dan absensi yang pernah diisi sebelumnya.
                </div>
                <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Pilih Guru Pengampu Baru</label>
                <select name="id_ptk" class="form-select radius-8" required>
                    <option value="">-- Pilih Guru Baru --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t->id_ptk ?>" <?php echo ((int)$item->id_ptk === (int)$t->id_ptk) ? 'selected' : '' ?>>
                            <?php echo html_escape($t->nama_ptk) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer bg-neutral-50">
                <button type="button" class="btn btn-secondary radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning-600 text-white radius-8 text-sm fw-bold">Konfirmasi Take Over</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal Pilih / Salin Agenda Template -->
<?php if (!empty($templates)): ?>
<div class="modal fade" id="modalPilihTemplate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-12 border-0">
            <div class="modal-header bg-primary-600 text-white">
                <h6 class="modal-title text-white fw-bold">Pilih & Salin Agenda Tersimpan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($pilih_template_url); ?>
            <div class="modal-body p-20">
                <p class="text-xs text-secondary-light mb-16">Pilih agenda tersimpan untuk mapel <strong><?php echo html_escape($item->nama_mapel) ?> (Tingkat <?php echo html_escape($item->nama_tingkat) ?>)</strong>. Materi & kegiatan akan disalin, dan jadwal tanggal/jamnya akan disesuaikan otomatis dengan kelas ini.</p>
                <div class="list-group radius-8">
                    <?php foreach ($templates as $tpl): ?>
                        <label class="list-group-item d-flex align-items-center justify-content-between p-16 cursor-pointer hover-bg-neutral-50">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input flex-shrink-0" type="radio" name="source_id_pembelajaran_mapel" value="<?php echo $tpl->id_pembelajaran_mapel ?>" required>
                                <div>
                                    <div class="fw-bold text-primary-900 text-sm"><?php echo html_escape($tpl->judul_agenda) ?></div>
                                    <div class="text-xs text-secondary-light mt-1">
                                        Guru: <strong><?php echo html_escape($tpl->nama_ptk ?: '-') ?></strong> | Rombel: <?php echo html_escape($tpl->nama_rombel) ?> (Semester <?php echo $tpl->semester ?> - <?php echo $tpl->tahun_pelajaran ?>)
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-100 text-primary-700 radius-4 text-xs"><?php echo $tpl->total_agenda ?> Pertemuan</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer bg-neutral-50">
                <button type="button" class="btn btn-secondary radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 text-sm fw-bold">Salin & Sesuaikan Jadwal</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include viewPath('includes/footer'); ?>
