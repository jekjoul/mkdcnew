<?php include viewPath('includes/header'); ?>

<style>
    .draggable-row td {
        transition: background-color 0.4s ease-in-out;
    }
    .item-swapped-highlight td {
        background-color: #fef08a !important; /* Vibrant soft yellow highlight on every cell */
    }
    .item-swapped-highlight {
        outline: 2px solid #eab308 !important;
        outline-offset: -2px !important;
    }
</style>

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

    <!-- Schedule Alert Status Banner -->
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
                        <iconify-icon icon="solar:copy-bold" class="text-base"></iconify-icon> Salin Agenda &amp; Berkas
                    </button>
                <?php endif; ?>
                
                <?php if (!empty($agenda)): ?>
                    <!-- Tombol Generate via AI HANYA TAMPIL SETELAH AGENDA DIGENERATE -->
                    <button type="button" class="btn btn-success-600 radius-8 px-14 py-8 text-xs fw-semibold text-white d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalGenerateAi">
                        <iconify-icon icon="solar:magic-stick-bold" class="text-base"></iconify-icon> Generate via AI
                    </button>
                    <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                        <button type="submit" class="btn btn-outline-secondary radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-1" onclick="return confirm('Peringatan: Reset agenda akan menyusun ulang seluruh pertemuan dari awal. Lanjutkan?')">
                            <iconify-icon icon="solar:restart-linear" class="text-base"></iconify-icon> Reset Agenda
                        </button>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-20">
            <?php if (empty($agenda)): ?>
                <!-- EMPTY STATE KETIKA AGENDA BELUM DIGENERATE -->
                <div class="text-center py-48 bg-neutral-50 radius-12 border border-dashed">
                    <iconify-icon icon="solar:notebook-square-linear" style="font-size: 56px;" class="text-primary-300 mb-12"></iconify-icon>
                    <h5 class="fw-bold text-primary-light mb-8">Agenda Harian Belum Digenerate</h5>
                    <p class="text-secondary-light text-sm max-w-500-px mx-auto mb-20">
                        Silakan buat agenda harian dasar terlebih dahulu berdasarkan jadwal pelajaran &amp; kalender hari efektif kelas ini, atau salin dari agenda terdahulu.
                    </p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <?php if (!empty($templates)): ?>
                            <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 text-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalPilihTemplate">
                                <iconify-icon icon="solar:copy-bold" class="me-1"></iconify-icon> Salin Agenda &amp; Berkas
                            </button>
                        <?php endif; ?>
                        <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                            <button type="submit" class="btn btn-outline-primary radius-8 px-20 py-10 text-sm fw-bold d-inline-flex align-items-center gap-2">
                                <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon> Generate Agenda Kosong
                            </button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- TAMPILAN FIXED READ-ONLY DENGAN TOMBOL EDIT PER ITEM -->
                <div class="table-responsive">
                    <table class="table bordered-table align-middle w-100 mb-0">
                        <thead>
                            <tr class="bg-neutral-100">
                                <th style="width: 70px;" class="text-center" title="Geser urutan materi ke atas/bawah">Urutan</th>
                                <th style="width: 50px;" class="text-center">Ke-</th>
                                <th style="width: 140px;">Hari / Tanggal</th>
                                <th style="width: 130px;">Jam KBM</th>
                                <th style="max-width: 250px; width: 250px;">Materi &amp; Pokok Bahasan</th>
                                <th style="width: 160px;">Media Pembelajaran</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="agendaTableBody">
                            <?php foreach ($agenda as $idx_row => $row): ?>
                                <?php
                                $media_items = json_decode($row->media_files ?: '[]', true) ?: [];
                                $raw_materi = !empty($row->materi) ? strip_tags($row->materi) : '';
                                $preview_materi = mb_strlen($raw_materi) > 100 ? mb_substr($raw_materi, 0, 100) . '...' : $raw_materi;
                                ?>
                                <tr class="draggable-row" data-id-agenda="<?php echo $row->id_agenda; ?>">
                                    <td class="text-center align-middle px-4">
                                        <div class="d-inline-flex flex-column gap-1 align-items-center">
                                            <button type="button" 
                                                    data-action-url="<?php echo url('agenda_pembelajaran/move_up/' . $row->id_agenda); ?>"
                                                    class="btn btn-xs btn-outline-primary p-2 radius-4 btn-action-move-up <?php echo $idx_row === 0 ? 'disabled opacity-50' : ''; ?>" 
                                                    <?php echo $idx_row === 0 ? 'disabled' : ''; ?> 
                                                    title="Geser Materi ke Pertemuan Sebelumnya (Atas)">
                                                <iconify-icon icon="solar:alt-arrow-up-bold" class="text-xs d-block" style="pointer-events: none;"></iconify-icon>
                                            </button>
                                            <button type="button" 
                                                    data-action-url="<?php echo url('agenda_pembelajaran/move_down/' . $row->id_agenda); ?>"
                                                    class="btn btn-xs btn-outline-primary p-2 radius-4 btn-action-move-down <?php echo $idx_row === count($agenda) - 1 ? 'disabled opacity-50' : ''; ?>" 
                                                    <?php echo $idx_row === count($agenda) - 1 ? 'disabled' : ''; ?> 
                                                    title="Geser Materi ke Pertemuan Berikutnya (Bawah)">
                                                <iconify-icon icon="solar:alt-arrow-down-bold" class="text-xs d-block" style="pointer-events: none;"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-primary-600 pertemuan-ke-cell align-middle"><?php echo $row->pertemuan_ke ?></td>
                                    <td class="tanggal-cell align-middle">
                                        <div class="fw-bold text-primary-900 hari-text"><?php echo html_escape($row->hari) ?></div>
                                        <div class="text-xs text-secondary-light tgl-text"><?php echo date('d M Y', strtotime($row->tanggal)) ?></div>
                                    </td>
                                    <td class="jam-cell align-middle">
                                        <div class="text-xs fw-semibold text-neutral-800 jam-text"><?php echo html_escape($row->jam_mulai ?: '-') ?> - <?php echo html_escape($row->jam_selesai ?: '-') ?></div>
                                        <div class="text-xs text-secondary-light jp-text"><?php echo $row->jumlah_jam ?: 0 ?> JP</div>
                                    </td>
                                    <td style="max-width: 250px; word-wrap: break-word; white-space: normal;" class="align-middle">
                                        <div class="text-sm fw-semibold text-primary-900 mb-2">
                                            <?php echo !empty($preview_materi) ? html_escape($preview_materi) : '<em class="text-secondary-light text-xs">- belum diisi -</em>'; ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <!-- DAFTAR MEDIA PEMBELAJARAN (FILE / LINK) -->
                                        <?php if (!empty($media_items)): ?>
                                            <div class="d-flex flex-column gap-4">
                                                <?php foreach ($media_items as $m_idx => $media): ?>
                                                    <?php if (isset($media['type']) && $media['type'] === 'file'): ?>
                                                        <a href="<?php echo base_url('uploads/agenda_media/' . $media['file_name']); ?>" target="_blank" class="badge bg-primary-50 text-primary-700 border border-primary-200 px-8 py-4 radius-4 text-xs d-inline-flex align-items-center gap-1 text-truncate" style="max-width: 150px;" title="<?php echo html_escape($media['title']); ?>">
                                                            <iconify-icon icon="solar:document-bold" class="text-sm flex-shrink-0"></iconify-icon>
                                                            <span class="text-truncate"><?php echo html_escape($media['title']); ?></span>
                                                        </a>
                                                    <?php elseif (isset($media['type']) && $media['type'] === 'link'): ?>
                                                        <a href="<?php echo html_escape($media['url']); ?>" target="_blank" class="badge bg-info-50 text-info-700 border border-info-200 px-8 py-4 radius-4 text-xs d-inline-flex align-items-center gap-1 text-truncate" style="max-width: 150px;" title="<?php echo html_escape($media['url']); ?>">
                                                            <iconify-icon icon="solar:link-bold" class="text-sm flex-shrink-0"></iconify-icon>
                                                            <span class="text-truncate"><?php echo html_escape($media['title'] ?: 'Link Media'); ?></span>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-secondary-light">- Tidak ada -</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($row->status === 'Terlaksana'): ?>
                                            <span class="badge bg-success-100 text-success-700 px-8 py-4 radius-4 text-xs fw-bold">Terlaksana</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-100 text-warning-700 px-8 py-4 radius-4 text-xs fw-bold">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <!-- TOMBOL EDIT PER ITEM -->
                                        <button type="button" 
                                                class="btn btn-sm btn-primary-600 radius-8 px-10 py-6 text-xs d-inline-flex align-items-center gap-1 btn-trigger-edit-item"
                                                data-agenda='<?php echo html_escape(json_encode($row)); ?>'>
                                            <iconify-icon icon="solar:pen-bold" class="text-sm"></iconify-icon> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL EDIT ITEM AGENDA (EDIT PER ITEM + MEDIA FILES/LINKS) -->
<div class="modal fade" id="modalEditItemAgenda" tabindex="-1" aria-labelledby="modalEditItemAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <div class="modal-header bg-primary-600 text-white radius-top-12">
                <h6 class="modal-title text-white d-flex align-items-center gap-2" id="modalEditItemAgendaLabel">
                    <iconify-icon icon="solar:pen-bold" class="text-xl"></iconify-icon>
                    Edit Item Agenda Pertemuan <span id="modalPertemuanTitle" class="fw-bold"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open_multipart('agenda_pembelajaran/simpan_item_agenda_modal', ['id' => 'formEditItemAgenda']); ?>
                <input type="hidden" name="id_agenda" id="edit_id_agenda">
                <input type="hidden" name="id_pembelajaran_mapel" value="<?php echo $item->id_pembelajaran_mapel; ?>">

                <div class="modal-body p-24">
                    <div class="alert alert-primary bg-primary-50 border-primary-200 radius-8 p-12 mb-20 text-xs text-primary-900 d-flex align-items-center justify-content-between">
                        <div>
                            <strong>Hari/Tanggal:</strong> <span id="modalHariTanggal" class="fw-bold"></span>
                        </div>
                        <div>
                            <strong>Jam KBM:</strong> <span id="modalJamKbm" class="fw-bold"></span>
                        </div>
                    </div>

                    <div class="mb-16">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-6">Status Keterlaksanaan <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select radius-8 text-sm" required>
                            <option value="Belum">Belum Terlaksana</option>
                            <option value="Terlaksana">Terlaksana</option>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-6">Materi &amp; Pokok Bahasan <span class="text-danger">*</span></label>
                        <textarea name="materi" id="edit_materi" class="form-control radius-8 text-sm" rows="3" placeholder="Tuliskan materi pokok bahasan yang diajarkan..." required></textarea>
                    </div>

                    <div class="mb-16">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-6">Kegiatan &amp; Aktivitas Pembelajaran</label>
                        <textarea name="kegiatan" id="edit_kegiatan" class="form-control radius-8 text-sm" rows="3" placeholder="Tuliskan bentuk aktivitas/kegiatan KBM..."></textarea>
                    </div>

                    <div class="row g-3 mb-20">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-danger-700 mb-6 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:danger-triangle-bold" class="text-base"></iconify-icon> Hambatan / Kendala KBM
                            </label>
                            <textarea name="hambatan" id="edit_hambatan" class="form-control radius-8 text-sm border-danger-200" rows="2" placeholder="Tuliskan hambatan/kendala selama KBM (jika ada)..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-success-700 mb-6 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon> Pemecahan Masalah / Solusi
                            </label>
                            <textarea name="pemecahan" id="edit_pemecahan" class="form-control radius-8 text-sm border-success-200" rows="2" placeholder="Tuliskan pemecahan masalah/solusi KBM (jika ada)..."></textarea>
                        </div>
                    </div>

                    <!-- SEKSI MEDIA PEMBELAJARAN (FILE UPLOAD & LINKS) -->
                    <div class="border-top pt-16 mt-20">
                        <h6 class="fw-bold text-primary-900 text-sm mb-12 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:folder-open-bold" class="text-lg text-primary-600"></iconify-icon>
                            Media &amp; Berkas Pembelajaran
                        </h6>

                        <!-- Daftar Media yang Sudah Ada -->
                        <div id="existingMediaContainer" class="mb-16 d-none">
                            <label class="form-label text-xs fw-semibold text-secondary-light mb-6">Media / Berkas Tersimpan:</label>
                            <div class="d-flex flex-column gap-2" id="existingMediaList"></div>
                        </div>

                        <!-- Form Upload File Baru -->
                        <div class="mb-16 p-12 bg-neutral-50 radius-8 border">
                            <label class="form-label text-xs fw-semibold text-primary-900 mb-6">📁 Upload File Media Baru (Dapat Lebih dari 1 File)</label>
                            <input type="file" name="media_file[]" multiple class="form-control radius-8 text-xs">
                            <span class="text-xs text-secondary-light mt-4 d-block">Format didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, ZIP, MP4.</span>
                        </div>

                        <!-- Form Sisipkan Link Baru -->
                        <div class="p-12 bg-neutral-50 radius-8 border">
                            <label class="form-label text-xs fw-semibold text-primary-900 mb-6">🔗 Sisipkan Link Media (Google Drive, YouTube, Website, DLL)</label>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="media_title[]" class="form-control radius-8 text-xs" placeholder="Nama Link (opsional, misal: Slide Google Drive)">
                                </div>
                                <div class="col-md-7">
                                    <input type="url" name="media_link[]" class="form-control radius-8 text-xs" placeholder="https://drive.google.com/... atau https://youtube.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-neutral-50 radius-bottom-12 p-16">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 fw-bold d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon> Simpan Perubahan Item
                    </button>
                </div>
            <?php echo form_close(); ?>
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
            <div class="modal-header bg-primary-600 text-white radius-top-12">
                <h6 class="modal-title text-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:copy-bold" class="text-xl"></iconify-icon>
                    Salin Agenda &amp; Berkas Pembelajaran
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($pilih_template_url); ?>
            <div class="modal-body p-20">
                <div class="alert alert-primary bg-primary-50 border-primary-200 radius-8 p-12 text-xs mb-16 text-primary-900 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:info-circle-bold" class="text-lg text-primary-600 flex-shrink-0"></iconify-icon>
                    <div>
                        Anda dapat menyalin agenda dari <strong>Tahun Pelajaran Lalu</strong> maupun dari <strong>Tahun Pelajaran yang Sama untuk Rombel Berbeda</strong> (misal dari Kelas 8A ke 8B). Seluruh materi, kegiatan, catatan, dan berkas/link media pembelajaran akan disalin secara otomatis.
                    </div>
                </div>
                <div class="list-group radius-8 gap-2">
                    <?php foreach ($templates as $tpl): ?>
                        <?php 
                        $is_same_year = (!empty($item->id_tahun_pelajaran) && (int)$tpl->id_tahun_pelajaran === (int)$item->id_tahun_pelajaran);
                        $sem_fmt = is_numeric($tpl->semester) ? 'Semester ' . $tpl->semester : (strpos(strtolower($tpl->semester), 'semester') !== false ? $tpl->semester : 'Semester ' . $tpl->semester);
                        ?>
                        <label class="list-group-item d-flex align-items-center justify-content-between p-16 cursor-pointer hover-bg-neutral-50 border radius-8 mb-0">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input flex-shrink-0 my-0" type="radio" name="source_id_pembelajaran_mapel" value="<?php echo $tpl->id_pembelajaran_mapel ?>" required>
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                                        <span class="fw-bold text-primary-900 text-sm"><?php echo html_escape($tpl->judul_agenda) ?></span>
                                        <?php if ($is_same_year): ?>
                                            <span class="badge bg-success-100 text-success-800 radius-4 text-xs px-8 py-2 fw-semibold">
                                                <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>Tahun Sama (Rombel Lain)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info-100 text-info-800 radius-4 text-xs px-8 py-2 fw-semibold">
                                                <iconify-icon icon="solar:history-bold" class="me-1"></iconify-icon>Tahun Lalu (TP <?php echo html_escape($tpl->tahun_pelajaran); ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-secondary-light">
                                        Guru Pengampu: <strong><?php echo html_escape($tpl->nama_ptk ?: '-') ?></strong> | Rombel: <strong><?php echo html_escape($tpl->nama_rombel) ?></strong> (<?php echo html_escape($sem_fmt) ?> - TP <?php echo html_escape($tpl->tahun_pelajaran) ?>)
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-100 text-primary-700 radius-4 text-xs fw-bold px-10 py-6 flex-shrink-0 ms-2"><?php echo $tpl->total_agenda ?> Pertemuan</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer bg-neutral-50 radius-bottom-12">
                <button type="button" class="btn btn-outline-secondary radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 text-sm fw-bold d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:copy-bold" class="text-base"></iconify-icon> Salin &amp; Sesuaikan Jadwal
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Konfirmasi Generate AI -->
<div class="modal fade" id="modalGenerateAi" tabindex="-1" aria-labelledby="modalGenerateAiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <div class="modal-header bg-success-600 text-white radius-top-12">
                <h6 class="modal-title text-white d-flex align-items-center gap-2" id="modalGenerateAiLabel">
                    <iconify-icon icon="solar:magic-stick-bold" class="text-xl"></iconify-icon>
                    Generate Agenda Pembelajaran via AI
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open($generate_agenda_ai_url, ['id' => 'formGenerateAi']); ?>
            <div class="modal-body p-24">
                <div class="text-center mb-20">
                    <div class="w-64-px h-64-px bg-success-50 text-success-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-12">
                        <iconify-icon icon="solar:magic-stick-3-bold" class="text-3xl"></iconify-icon>
                    </div>
                    <h6 class="fw-bold text-primary-900 mb-8">Konfirmasi Generate Agenda Pembelajaran</h6>
                </div>
                
                <div class="alert alert-success bg-success-50 border-success-200 radius-8 p-16 mb-16 text-sm text-success-900 d-flex align-items-start gap-2">
                    <iconify-icon icon="solar:info-circle-bold" class="text-xl text-success-600 flex-shrink-0 mt-2"></iconify-icon>
                    <div>
                        Agenda harian pembelajaran akan dibuat oleh AI berdasarkan CP, ATP dan Modul Pembelajaran yang telah dibuat sebelumnya.
                    </div>
                </div>

                <div id="aiGeneratingStatus" class="d-none text-center p-20 bg-neutral-100 radius-8 mt-16 border">
                    <div class="spinner-border text-success mb-12" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="fw-bold text-success-700 mb-4">AI sedang membuat agenda...</h6>
                    <p class="text-xs text-secondary-light mb-0">Mohon tunggu sebentar, AI sedang menganalisis CP, ATP &amp; Modul Pembelajaran untuk menyusun alur materi dan kegiatan KBM pertemuannya.</p>
                </div>
            </div>
            <div class="modal-footer bg-neutral-50 radius-bottom-12 p-16" id="aiModalFooter">
                <button type="button" class="btn btn-outline-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                <button type="submit" id="btnSubmitGenerateAi" class="btn btn-success-600 radius-8 px-20 fw-bold text-white d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:magic-stick-bold" class="text-lg"></iconify-icon> Ya, Generate Sekarang
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- FLOATING TOAST NOTIFICATION UNTUK DRAG AND DROP -->
<div id="dragToastNotice" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div id="dragToastAlert" class="toast show align-items-center text-white bg-success-600 border-0 radius-8 shadow-lg" role="alert">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 fw-semibold">
                <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
                <span id="dragToastMsg">Urutan materi agenda berhasil diperbarui!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="$('#dragToastNotice').fadeOut()"></button>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    $('#formGenerateAi').on('submit', function() {
        $('#aiGeneratingStatus').removeClass('d-none');
        $('#btnSubmitGenerateAi').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> AI sedang membuat agenda...');
        $('#aiModalFooter button[data-bs-dismiss]').prop('disabled', true);
    });

    // Populate Modal Edit Item Agenda
    $('.btn-trigger-edit-item').on('click', function() {
        var rowData = $(this).data('agenda');
        if (!rowData) return;

        $('#edit_id_agenda').val(rowData.id_agenda);
        $('#modalPertemuanTitle').text('#' + rowData.pertemuan_ke);
        $('#modalHariTanggal').text(rowData.hari + ', ' + rowData.tanggal);
        $('#modalJamKbm').text((rowData.jam_mulai || '-') + ' - ' + (rowData.jam_selesai || '-') + ' (' + (rowData.jumlah_jam || 0) + ' JP)');
        
        $('#edit_status').val(rowData.status || 'Belum');
        $('#edit_hambatan').val(rowData.hambatan || rowData.catatan || '');
        $('#edit_pemecahan').val(rowData.pemecahan || '');
        $('#edit_materi').val(rowData.materi || '');
        $('#edit_kegiatan').val(rowData.kegiatan || '');

        // Render existing media list inside modal
        var mediaContainer = $('#existingMediaContainer');
        var mediaListEl = $('#existingMediaList');
        mediaListEl.empty();

        var mediaItems = [];
        try {
            mediaItems = JSON.parse(rowData.media_files || '[]');
        } catch (e) {
            mediaItems = [];
        }

        if (mediaItems && mediaItems.length > 0) {
            mediaContainer.removeClass('d-none');
            $.each(mediaItems, function(idx, item) {
                var deleteUrl = '<?php echo url("agenda_pembelajaran/hapus_media_item/"); ?>' + rowData.id_agenda + '/' + idx;
                var htmlItem = '<div class="d-flex align-items-center justify-content-between p-8 bg-white border radius-6">';
                if (item.type === 'file') {
                    htmlItem += '<div class="text-xs text-truncate me-2"><iconify-icon icon="solar:document-bold" class="text-primary-600 me-1"></iconify-icon><strong>File:</strong> ' + item.title + '</div>';
                } else {
                    htmlItem += '<div class="text-xs text-truncate me-2"><iconify-icon icon="solar:link-bold" class="text-info-600 me-1"></iconify-icon><strong>Link:</strong> <a href="' + item.url + '" target="_blank">' + (item.title || item.url) + '</a></div>';
                }
                htmlItem += '<a href="' + deleteUrl + '" onclick="return confirm(\'Hapus media ini?\')" class="btn btn-xs btn-outline-danger px-8 py-2 radius-4 text-xs flex-shrink-0">Hapus</a>';
                htmlItem += '</div>';
                mediaListEl.append(htmlItem);
            });
        } else {
            mediaContainer.addClass('d-none');
        }

        var editModal = new bootstrap.Modal(document.getElementById('modalEditItemAgenda'));
        editModal.show();
    });

    // REORDER MATERI AGENDA (AJAX TANPA RELOAD HALAMAN)
    var originalSlots = [];

    function captureOriginalSlots() {
        originalSlots = [];
        var rows = document.querySelectorAll('#agendaTableBody tr.draggable-row');
        rows.forEach(function(row) {
            var cellKe = row.querySelector('.pertemuan-ke-cell');
            var cellHari = row.querySelector('.hari-text');
            var cellTgl = row.querySelector('.tgl-text');
            var cellJam = row.querySelector('.jam-text');
            var cellJp = row.querySelector('.jp-text');

            originalSlots.push({
                pertemuan_ke: cellKe ? cellKe.textContent.trim() : '',
                hari: cellHari ? cellHari.textContent.trim() : '',
                tanggal_fmt: cellTgl ? cellTgl.textContent.trim() : '',
                jam_text: cellJam ? cellJam.textContent.trim() : '',
                jp_text: cellJp ? cellJp.textContent.trim() : ''
            });
        });
    }

    function refreshSlotLabelsInDom() {
        var tbody = document.getElementById('agendaTableBody');
        if (!tbody) return;

        var rows = tbody.querySelectorAll('tr.draggable-row');
        rows.forEach(function(row, index) {
            var slot = originalSlots[index];
            if (slot) {
                var cellKe = row.querySelector('.pertemuan-ke-cell');
                var cellHari = row.querySelector('.hari-text');
                var cellTgl = row.querySelector('.tgl-text');
                var cellJam = row.querySelector('.jam-text');
                var cellJp = row.querySelector('.jp-text');

                if (cellKe) cellKe.textContent = slot.pertemuan_ke;
                if (cellHari) cellHari.textContent = slot.hari;
                if (cellTgl) cellTgl.textContent = slot.tanggal_fmt;
                if (cellJam) cellJam.textContent = slot.jam_text;
                if (cellJp) cellJp.textContent = slot.jp_text;
            }

            var btnUp = row.querySelector('.btn-action-move-up');
            var btnDown = row.querySelector('.btn-action-move-down');

            if (btnUp) {
                if (index === 0) {
                    btnUp.classList.add('disabled', 'opacity-50');
                } else {
                    btnUp.classList.remove('disabled', 'opacity-50');
                }
            }

            if (btnDown) {
                if (index === rows.length - 1) {
                    btnDown.classList.add('disabled', 'opacity-50');
                } else {
                    btnDown.classList.remove('disabled', 'opacity-50');
                }
            }
        });
    }

    function highlightSwappedPair(tr1, tr2) {
        var $pair = $(tr1).add($(tr2));
        $pair.addClass('item-swapped-highlight');
        setTimeout(function() {
            $pair.removeClass('item-swapped-highlight');
        }, 3000);
    }

    $(document).ready(function() {
        captureOriginalSlots();

        // AJAX Handler Panah Atas
        $(document).on('click', '.btn-action-move-up', function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if ($(this).is(':disabled') || $(this).hasClass('disabled')) return false;

            var targetUrl = $(this).data('action-url');
            if (!targetUrl) return false;

            var tr = this.closest('tr');
            if (!tr) return false;
            var prevTr = tr.previousElementSibling;
            if (prevTr && prevTr.classList.contains('draggable-row')) {
                // Swap DOM nodes secara fisik
                tr.parentNode.insertBefore(tr, prevTr);

                // Berikan animasi sorot kuning menyala selama 3 detik pada KEDUA baris yang ditukar
                highlightSwappedPair(tr, prevTr);

                refreshSlotLabelsInDom();

                // AJAX background GET request ke server
                $.ajax({
                    url: targetUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        if (res && res.success) {
                            $('#dragToastMsg').text(res.message || 'Urutan materi agenda berhasil dipindahkan!');
                            $('#dragToastAlert').removeClass('bg-danger-600').addClass('bg-success-600');
                            $('#dragToastNotice').stop(true, true).fadeIn().delay(2500).fadeOut();
                        } else {
                            $('#dragToastMsg').text((res && res.message) ? res.message : 'Gagal memindahkan urutan.');
                            $('#dragToastAlert').removeClass('bg-success-600').addClass('bg-danger-600');
                            $('#dragToastNotice').stop(true, true).fadeIn().delay(4000).fadeOut();
                        }
                    },
                    error: function() {
                        $('#dragToastMsg').text('Terjadi kesalahan koneksi.');
                        $('#dragToastAlert').removeClass('bg-success-600').addClass('bg-danger-600');
                        $('#dragToastNotice').stop(true, true).fadeIn().delay(4000).fadeOut();
                    }
                });
            }
        });

        // AJAX Handler Panah Bawah
        $(document).on('click', '.btn-action-move-down', function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if ($(this).is(':disabled') || $(this).hasClass('disabled')) return false;

            var targetUrl = $(this).data('action-url');
            if (!targetUrl) return false;

            var tr = this.closest('tr');
            if (!tr) return false;
            var nextTr = tr.nextElementSibling;
            if (nextTr && nextTr.classList.contains('draggable-row')) {
                // Swap DOM nodes secara fisik
                tr.parentNode.insertBefore(nextTr, tr);

                // Berikan animasi sorot kuning menyala selama 3 detik pada KEDUA baris yang ditukar
                highlightSwappedPair(tr, nextTr);

                refreshSlotLabelsInDom();

                // AJAX background GET request ke server
                $.ajax({
                    url: targetUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        if (res && res.success) {
                            $('#dragToastMsg').text(res.message || 'Urutan materi agenda berhasil dipindahkan!');
                            $('#dragToastAlert').removeClass('bg-danger-600').addClass('bg-success-600');
                            $('#dragToastNotice').stop(true, true).fadeIn().delay(2500).fadeOut();
                        } else {
                            $('#dragToastMsg').text((res && res.message) ? res.message : 'Gagal memindahkan urutan.');
                            $('#dragToastAlert').removeClass('bg-success-600').addClass('bg-danger-600');
                            $('#dragToastNotice').stop(true, true).fadeIn().delay(4000).fadeOut();
                        }
                    },
                    error: function() {
                        $('#dragToastMsg').text('Terjadi kesalahan koneksi.');
                        $('#dragToastAlert').removeClass('bg-success-600').addClass('bg-danger-600');
                        $('#dragToastNotice').stop(true, true).fadeIn().delay(4000).fadeOut();
                    }
                });
            }
        });
    });
</script>
