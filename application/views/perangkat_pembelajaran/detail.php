<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Header Card -->
    <div class="card mb-24 border-0 shadow-sm radius-12 overflow-hidden">
        <div class="card-header bg-warning-900 p-24 text-light d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h6 class="text-light mb-1">Perangkat & Agenda Pembelajaran</h6>
                <p class="text-light text-sm mb-0">
                    <?php echo html_escape($item->nama_mapel . ' — ' . trim($item->nama_lembaga . ' ' . $item->nama_tingkat . ' ' . $item->nama_rombel)) ?>
                </p>
            </div>
            <a href="<?php echo $back_url ?>" class="btn btn-sm btn-outline-light radius-8 px-16 py-8">
                <iconify-icon icon="solar:arrow-left-linear" class="me-1"></iconify-icon> Kembali
            </a>
        </div>
        <div class="card-body p-24">
            <div class="row gy-3">
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Tahun Ajaran / Semester</span>
                    <strong class="text-primary-light"><?php echo html_escape($item->tahun_pelajaran . ' (' . $item->semester . ')') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Guru Pengampu</span>
                    <strong class="text-primary-light"><?php echo html_escape($item->nama_ptk ?: '-') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Jadwal Kelas</span>
                    <strong class="text-primary-light">
                        <?php
                        $schedules = $this->db->get_where('jadwal_pelajaran_item', [
                            'id_pembelajaran' => $item->id_pembelajaran,
                            'id_mapel' => $item->id_mapel
                        ])->result();
                        if (!empty($schedules)) {
                            $days = array_unique(array_column($schedules, 'hari'));
                            echo implode(', ', array_map('ucfirst', $days));
                        } else {
                            echo '<span class="text-danger text-sm">Jadwal belum dibuat</span>';
                        }
                        ?>
                    </strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light text-xs d-block mb-4">Kalender Hari Aktif</span>
                    <strong class="text-primary-light">
                        <?php
                        $active_days_count = $this->db->where('id_tahun_pelajaran', $item->id_tahun_pelajaran)
                            ->where_in('status', ['Efektif', 'Daring', 'Luar Kelas'])
                            ->get('pembelajaran_hari_efektif')->num_rows();
                        if ($active_days_count > 0) {
                            echo $active_days_count . ' Hari Efektif';
                        } else {
                            echo '<span class="text-danger text-sm">Kalender akademik belum diset</span>';
                        }
                        ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs border-bottom mb-24 gap-2" id="perangkatTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active btn btn-outline-primary radius-8 px-20 py-10" id="berkas-tab" data-bs-toggle="tab" data-bs-target="#tab-berkas" type="button" role="tab">
                <iconify-icon icon="solar:folder-with-files-linear" class="me-1"></iconify-icon> Berkas Perangkat
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn btn-outline-primary radius-8 px-20 py-10" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#tab-agenda" type="button" role="tab">
                <iconify-icon icon="solar:calendar-date-linear" class="me-1"></iconify-icon> Agenda Harian
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">

        <!-- ============================================================ -->
        <!-- TAB 1: Berkas Perangkat                                      -->
        <!-- ============================================================ -->
        <div class="tab-pane fade show active" id="tab-berkas" role="tabpanel">

            <!-- Info bersama: berkas berlaku untuk tingkat & mapel yang sama -->
            <div class="alert bg-info-focus text-info-main border border-info-200 radius-12 p-16 d-flex align-items-start gap-12 mb-24">
                <iconify-icon icon="lucide:info" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                <div class="text-sm">
                    <strong>Informasi:</strong> Berkas perangkat ini berlaku bersama untuk seluruh rombel
                    <strong class="text-info-main"><?php echo html_escape($item->nama_tingkat) ?></strong>
                    yang mengampu mata pelajaran <strong class="text-info-main"><?php echo html_escape($item->nama_mapel) ?></strong>
                    di tahun pelajaran yang sama. Upload sekali, berlaku untuk semua rombel.
                </div>
            </div>

            <div class="card border-0 shadow-sm radius-12">
                <div class="card-header bg-transparent border-bottom p-24 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="mb-0 text-primary-light">Unggah & Kelola Dokumen Pembelajaran</h6>
                        <span class="text-secondary-light text-xs mt-4 d-block">
                            Berkas ini dikelola bersama untuk <strong><?php echo html_escape($item->nama_tingkat . ' — ' . $item->nama_mapel) ?></strong>
                        </span>
                    </div>
                    <!-- Tombol Salin dari Tahun Lalu -->
                    <?php echo form_open($salin_perangkat_url, ['class' => 'd-inline']); ?>
                        <input type="hidden" name="salin" value="1">
                        <button type="submit"
                            onclick="return confirm('Salin semua referensi berkas dari tahun pelajaran sebelumnya? Berkas yang sudah ada tidak akan dihapus.')"
                            class="btn btn-sm btn-outline-secondary radius-8 px-16 py-8">
                            <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin dari Tahun Lalu
                        </button>
                    <?php echo form_close(); ?>
                </div>
                <div class="card-body p-24">
                    <?php echo form_open_multipart($save_berkas_url, ['class' => 'form-validate']); ?>
                        <div class="table-responsive">
                            <table class="table bordered-table align-middle">
                                <thead>
                                    <tr>
                                        <th width="40" class="text-center">No</th>
                                        <th width="280">Nama Dokumen / Perangkat</th>
                                        <th width="150" class="text-center">Status</th>
                                        <th>File & Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $files_config = [
                                        'file_cp'        => ['label' => '1. Capaian Pembelajaran (CP)',              'key' => 'cp',        'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_tp'        => ['label' => '2. Tujuan Pembelajaran (TP)',               'key' => 'tp',        'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_atp'       => ['label' => '3. Alur Tujuan Pembelajaran (ATP)',         'key' => 'atp',       'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_modul_ajar'=> ['label' => '4. Modul Ajar / RPP',                      'key' => 'modul_ajar','accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_kisi_sts'  => ['label' => '5. Kisi-kisi Sumatif Tengah Semester (STS)','key' => 'kisi_sts', 'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_soal_sts'  => ['label' => '6. Soal Sumatif Tengah Semester (STS)',    'key' => 'soal_sts',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_kisi_sas'  => ['label' => '7. Kisi-kisi Sumatif Akhir Semester (SAS)','key' => 'kisi_sas',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                        'file_soal_sas'  => ['label' => '8. Soal Sumatif Akhir Semester (SAS)',     'key' => 'soal_sas',  'accept' => '.docx,.xlsx',                                                          'hint' => 'DOCX / XLSX saja'],
                                    ];
                                    $no = 1;
                                    foreach ($files_config as $field => $cfg):
                                        $uploaded_file = $perangkat ? $perangkat->$field : null;
                                    ?>
                                        <tr>
                                            <td class="text-center fw-semibold"><?php echo $no++ ?></td>
                                            <td>
                                                <span class="fw-semibold text-primary-light d-block"><?php echo $cfg['label'] ?></span>
                                                <span class="text-muted text-xs"><?php echo $cfg['hint'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($uploaded_file): ?>
                                                    <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Sudah Upload</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-focus text-warning-main px-12 py-6 radius-4">Belum Ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($uploaded_file): ?>
                                                    <div class="d-flex align-items-center gap-8">
                                                        <a href="<?php echo url('uploads/perangkat_pembelajaran/' . $uploaded_file) ?>"
                                                           target="_blank"
                                                           class="btn btn-sm btn-info-100 text-info-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                            <iconify-icon icon="lucide:download"></iconify-icon> Lihat/Unduh
                                                        </a>
                                                        <?php 
                                                        $key_drive = $cfg['key'] . '_drive_file_id';
                                                        $drive_file_id = $perangkat ? $perangkat->$key_drive : null;
                                                        if ($drive_file_id):
                                                        ?>
                                                            <a href="https://docs.google.com/document/d/<?php echo html_escape($drive_file_id) ?>/edit"
                                                               target="_blank"
                                                               class="btn btn-sm btn-success-100 text-success-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                                <iconify-icon icon="logos:google-drive" class="align-middle"></iconify-icon> Edit Google Docs
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="<?php echo $hapus_berkas_url . '/' . $cfg['key'] ?>"
                                                           onclick="return confirm('Hapus berkas ini? Rombel lain yang sama tingkat & mapelnya juga tidak bisa mengaksesnya.')"
                                                           class="btn btn-sm btn-danger-100 text-danger-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                                            <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus & Upload Ulang
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="file" name="<?php echo $field ?>" class="form-control radius-8 form-control-sm" accept="<?php echo $cfg['accept'] ?>">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-24">
                            <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-12">
                                <iconify-icon icon="lucide:save" class="me-1"></iconify-icon> Simpan & Upload Berkas
                            </button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div><!-- /tab-berkas -->


        <!-- ============================================================ -->
        <!-- TAB 2: Agenda Pembelajaran Harian                            -->
        <!-- ============================================================ -->
        <div class="tab-pane fade" id="tab-agenda" role="tabpanel">

            <!-- Rombel Switcher untuk Agenda -->
            <?php if (!empty($all_rombel) && count($all_rombel) > 1): ?>
                <div class="card border-0 shadow-sm radius-12 mb-24">
                    <div class="card-body p-16 d-flex align-items-center gap-16 overflow-auto">
                        <span class="text-secondary-light text-sm fw-medium whitespace-nowrap flex-shrink-0">
                            Pilih Agenda Rombel:
                        </span>
                        <div class="d-flex gap-8">
                            <?php foreach ($all_rombel as $r): ?>
                                <?php $is_active = ($r->id_pembelajaran_mapel == $item->id_pembelajaran_mapel); ?>
                                <a href="<?php echo $detail_base_url . '/' . $r->id_pembelajaran_mapel . '?tab=agenda' ?>"
                                   class="btn btn-sm radius-8 px-16 py-8 <?php echo $is_active ? 'btn-primary-600' : 'btn-outline-primary' ?>">
                                    <?php echo html_escape($r->nama_rombel) ?>
                                    <?php if ($r->total_agenda > 0): ?>
                                        <span class="badge <?php echo $is_active ? 'bg-white text-primary-600' : 'bg-primary-600 text-white' ?> ms-4 radius-4 px-8 py-2">
                                            <?php echo $r->terlaksana . '/' . $r->total_agenda ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$has_schedule_and_days): ?>
                <!-- Warning: data belum tersedia -->
                <div class="alert bg-warning-focus text-warning-main border border-warning-200 p-24 radius-12 d-flex align-items-start gap-3">
                    <iconify-icon icon="lucide:alert-triangle" class="icon text-3xl flex-shrink-0 mt-1"></iconify-icon>
                    <div>
                        <h6 class="fw-semibold text-warning-main mb-8">Data Belum Bisa Diisi!</h6>
                        <p class="text-sm mb-0">
                            Agenda pembelajaran harian belum dapat digenerate karena <strong>Jadwal Pelajaran Kelas</strong>
                            atau <strong>Hari Aktif Sekolah</strong> untuk tahun ajaran ini belum dibuat.
                            Pastikan jadwal mingguan dan kalender akademik hari efektif sudah diisi oleh kurikulum/admin.
                        </p>
                    </div>
                </div>

            <?php else: ?>

                <?php if (empty($agenda)): ?>
                    <!-- Agenda belum digenerate -->
                    <div class="card border-0 shadow-sm radius-12 text-center p-40">
                        <iconify-icon icon="solar:calendar-date-linear" class="text-neutral-300 text-64 mb-16"></iconify-icon>
                        <h5 class="fw-semibold text-primary-light mb-8">Agenda Harian Belum Digenerate</h5>
                        <p class="text-secondary-light text-sm mb-24 max-w-500 mx-auto">
                            Generate agenda harian untuk memetakan tanggal dan pertemuan sepanjang tahun ajaran berdasarkan
                            jadwal mingguan dan hari aktif sekolah.
                        </p>
                        <div class="d-flex justify-content-center gap-12 flex-wrap">
                            <!-- Generate baru -->
                            <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                                <input type="hidden" name="generate" value="1">
                                <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-12">
                                    <iconify-icon icon="lucide:refresh-cw" class="me-1"></iconify-icon> Generate Agenda Harian
                                </button>
                            <?php echo form_close(); ?>

                            <!-- Salin dari tahun lalu (jika ada) -->
                            <?php if ($source_last_year_id): ?>
                                <button type="button" class="btn btn-outline-secondary radius-8 px-24 py-12"
                                    data-bs-toggle="modal" data-bs-target="#modalSalinAgenda">
                                    <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin dari Sumber Lain
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Tabel agenda telah tersedia -->
                    <div class="card border-0 shadow-sm radius-12">
                        <div class="card-header bg-transparent border-bottom p-24 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h6 class="mb-0 text-primary-light">
                                    Jadwal Agenda Harian — Rombel <?php echo html_escape($item->nama_rombel) ?>
                                </h6>
                                <span class="text-secondary-light text-xs mt-4 d-block">
                                    Klik <strong>Edit</strong> pada baris untuk mengisi materi & kegiatan via editor TinyMCE.
                                </span>
                            </div>
                            <div class="d-flex flex-wrap gap-8">
                                <!-- Re-Generate -->
                                <?php echo form_open($generate_agenda_url, ['class' => 'd-inline']); ?>
                                    <input type="hidden" name="generate" value="1">
                                    <button type="submit"
                                        onclick="return confirm('Re-generate akan menghapus semua data agenda rombel ini. Lanjutkan?')"
                                        class="btn btn-sm btn-outline-warning radius-8 px-16 py-8">
                                        <iconify-icon icon="lucide:refresh-cw" class="me-1"></iconify-icon> Re-Generate
                                    </button>
                                <?php echo form_close(); ?>

                                <!-- Salin dari sumber -->
                                <?php if ($source_last_year_id || !empty($other_active_rombel_agendas)): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary radius-8 px-16 py-8"
                                        data-bs-toggle="modal" data-bs-target="#modalSalinAgenda">
                                        <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin Isi dari Sumber
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Progress summary bar -->
                        <?php
                        $total   = count($agenda);
                        $selesai = count(array_filter($agenda, function($a) { return $a->status === 'Terlaksana'; }));
                        $pct     = $total > 0 ? round($selesai / $total * 100) : 0;
                        ?>
                        <div class="px-24 pt-16">
                            <div class="d-flex justify-content-between text-xs text-secondary-light mb-4">
                                <span><strong><?php echo $selesai ?></strong> pertemuan terlaksana dari <strong><?php echo $total ?></strong></span>
                                <span><?php echo $pct ?>%</span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar bg-success" style="width:<?php echo $pct ?>%"></div>
                            </div>
                        </div>

                        <div class="card-body p-24">
                            <div class="table-responsive">
                                <table class="table bordered-table" id="agendaTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="70">Pert. Ke</th>
                                            <th width="150">Hari & Tanggal</th>
                                            <th width="100" class="text-center">Waktu (JP)</th>
                                            <th>Materi Pembelajaran</th>
                                            <th width="150" class="text-center">Status</th>
                                            <th width="100" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($agenda as $row): ?>
                                            <tr>
                                                <td class="text-center fw-bold text-primary-light"><?php echo $row->pertemuan_ke ?></td>
                                                <td>
                                                    <span class="d-block fw-semibold text-primary-light"><?php echo $row->hari ?></span>
                                                    <span class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($row->tanggal)) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="d-block fw-bold text-primary-light"><?php echo $row->jumlah_jam ? $row->jumlah_jam . ' JP' : '-' ?></span>
                                                    <?php if ($row->jam_mulai || $row->jam_selesai): ?>
                                                        <span class="text-xs text-secondary-light"><?php echo html_escape($row->jam_mulai . ' - ' . $row->jam_selesai) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="text-sm text-primary-light">
                                                        <?php echo !empty($row->materi) ? character_limiter(strip_tags($row->materi), 100) : '<span class="text-muted fst-italic text-xs">Materi belum diisi</span>' ?>
                                                    </div>
                                                    <textarea class="d-none hidden-materi"><?php echo html_escape($row->materi) ?></textarea>
                                                    <textarea class="d-none hidden-kegiatan"><?php echo html_escape($row->kegiatan) ?></textarea>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $sb = 'bg-neutral-100 text-neutral-600';
                                                    if ($row->status === 'Terlaksana') $sb = 'bg-success-focus text-success-main';
                                                    if ($row->status === 'Libur')      $sb = 'bg-danger-focus text-danger-main';
                                                    ?>
                                                    <span class="badge <?php echo $sb ?> px-12 py-6 radius-4"><?php echo $row->status ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary radius-8 px-12 py-8 edit-agenda-btn"
                                                            data-id="<?php echo $row->id_agenda ?>"
                                                            data-pertemuan="<?php echo $row->pertemuan_ke ?>"
                                                            data-hari="<?php echo $row->hari ?>"
                                                            data-tanggal="<?php echo date('d M Y', strtotime($row->tanggal)) ?>"
                                                            data-status="<?php echo html_escape($row->status) ?>"
                                                            data-catatan="<?php echo html_escape($row->catatan) ?>"
                                                            data-jumlah-jam="<?php echo html_escape($row->jumlah_jam) ?>"
                                                            data-jam-mulai="<?php echo html_escape($row->jam_mulai) ?>"
                                                            data-jam-selesai="<?php echo html_escape($row->jam_selesai) ?>">
                                                        <iconify-icon icon="lucide:edit"></iconify-icon> Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- /card agenda table -->

                <?php endif; ?>
            <?php endif; ?>
        </div><!-- /tab-agenda -->

    </div><!-- /tab-content -->
</div><!-- /dashboard-main-body -->


<!-- ================================================================ -->
<!-- Modal: Edit Agenda (TinyMCE)                                     -->
<!-- ================================================================ -->
<div class="modal fade" id="modalEditAgenda" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
            <?php echo form_open($save_agenda_url, ['id' => 'form-edit-agenda']); ?>
                <div class="modal-header bg-warning-900 p-20 text-light border-0">
                    <h6 class="modal-title text-light" id="modal-agenda-title">Edit Agenda Harian</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <input type="hidden" name="id_agenda" id="modal-id-agenda">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Hari / Tanggal</label>
                            <input type="text" id="modal-date-text" readonly class="form-control-plaintext fw-bold text-primary-light">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                            <select name="status" id="modal-status" class="form-select radius-8">
                                <option value="Belum">Belum Terlaksana</option>
                                <option value="Terlaksana">Terlaksana</option>
                                <option value="Libur">Libur KBM</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jml Jam</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_jam" id="modal-jumlah-jam" class="form-control radius-8">
                                <span class="input-group-text">JP</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="modal-jam-mulai" class="form-control radius-8">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="modal-jam-selesai" class="form-control radius-8">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Materi Pembelajaran</label>
                            <textarea id="modal-materi-editor" class="tinymce-editor"></textarea>
                            <input type="hidden" name="materi" id="modal-materi-submit">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kegiatan Pembelajaran</label>
                            <textarea id="modal-kegiatan-editor" class="tinymce-editor"></textarea>
                            <input type="hidden" name="kegiatan" id="modal-kegiatan-submit">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Catatan / Hambatan</label>
                            <input type="text" name="catatan" id="modal-catatan" placeholder="Misal: Siswa antusias, listrik padam..." class="form-control radius-8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-20 border-0 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">
                        <iconify-icon icon="lucide:check" class="me-1"></iconify-icon> Simpan Perubahan
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<!-- ================================================================ -->
<!-- Modal: Salin Isi Agenda dari Sumber Lain                        -->
<!-- ================================================================ -->
<?php if ($source_last_year_id || !empty($other_active_rombel_agendas)): ?>
<div class="modal fade" id="modalSalinAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 overflow-hidden shadow-lg">
            <?php echo form_open($salin_agenda_url, ['id' => 'form-salin-agenda']); ?>
                <input type="hidden" name="salin" value="1">
                <div class="modal-header bg-neutral-800 p-20 border-0">
                    <h6 class="modal-title text-white">Salin Isi Agenda dari Sumber</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <p class="text-sm text-secondary-light mb-20">
                        Pilih sumber agenda yang akan disalin isinya (materi, kegiatan, status) ke agenda rombel ini.
                        Penyalinan dilakukan berdasarkan nomor pertemuan yang cocok.
                    </p>

                    <div class="d-flex flex-column gap-12">

                        <?php if ($source_last_year_id): ?>
                            <label class="d-flex align-items-start gap-12 p-16 border border-2 border-primary radius-12 cursor-pointer salin-option" style="cursor:pointer">
                                <input type="radio" name="source_id" value="<?php echo $source_last_year_id ?>" class="form-check-input mt-1 flex-shrink-0">
                                <div>
                                    <span class="fw-semibold text-primary-light d-block">
                                        <iconify-icon icon="lucide:history" class="me-1"></iconify-icon>
                                        Salin dari Tahun Lalu
                                    </span>
                                    <span class="text-xs text-secondary-light">
                                        Rombel <?php echo html_escape($item->nama_rombel) ?> — Mapel <?php echo html_escape($item->nama_mapel) ?> — Tahun sebelumnya
                                    </span>
                                </div>
                            </label>
                        <?php endif; ?>

                        <?php foreach ($other_active_rombel_agendas as $rombel): ?>
                            <label class="d-flex align-items-start gap-12 p-16 border border-2 border-secondary rounded-3 cursor-pointer salin-option" style="cursor:pointer">
                                <input type="radio" name="source_id" value="<?php echo $rombel->id_pembelajaran_mapel ?>" class="form-check-input mt-1 flex-shrink-0">
                                <div>
                                    <span class="fw-semibold text-primary-light d-block">
                                        <iconify-icon icon="lucide:users" class="me-1"></iconify-icon>
                                        Salin dari Rombel <?php echo html_escape($rombel->nama_rombel) ?>
                                    </span>
                                    <span class="text-xs text-secondary-light">
                                        <?php echo html_escape($item->nama_mapel) ?> — <?php echo html_escape($rombel->tahun_pelajaran . ' (' . $rombel->semester . ')') ?>
                                    </span>
                                </div>
                            </label>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="modal-footer p-20 border-0 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        onclick="return confirm('Menyalin akan menimpa isi materi & kegiatan agenda saat ini. Lanjutkan?')"
                        class="btn btn-neutral-700 radius-8 px-20 py-10 text-white">
                        <iconify-icon icon="lucide:copy" class="me-1"></iconify-icon> Salin Sekarang
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>


<?php include viewPath('includes/footer'); ?>

<!-- TinyMCE CDN -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
$(document).ready(function () {

    // Handle URL tab parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab === 'agenda') {
        const agendaTabBtn = new bootstrap.Tab(document.querySelector('#agenda-tab'));
        agendaTabBtn.show();
    }

    // Initialize DataTable for agenda
    if ($('#agendaTable').length > 0) {
        $('#agendaTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']]
        });
    }

    // Initialize TinyMCE editors inside modal
    tinymce.init({
        selector: 'textarea.tinymce-editor',
        height: 220,
        menubar: false,
        branding: false,
        statusbar: false,
        plugins: 'lists link code wordcount',
        toolbar: 'undo redo | bold italic | bullist numlist | removeformat | code'
    });

    // Handle "Edit Agenda" button click
    $(document).on('click', '.edit-agenda-btn', function () {
        const btn    = $(this);
        const id     = btn.data('id');
        const pert   = btn.data('pertemuan');
        const hari   = btn.data('hari');
        const tgl    = btn.data('tanggal');
        const status = btn.data('status');
        const catatan = btn.data('catatan');
        const jmlJam = btn.data('jumlah-jam');
        const jamMulai = btn.data('jam-mulai');
        const jamSelesai = btn.data('jam-selesai');

        const materi   = btn.closest('tr').find('.hidden-materi').val() || '';
        const kegiatan = btn.closest('tr').find('.hidden-kegiatan').val() || '';

        $('#modal-agenda-title').text('Edit Agenda — Pertemuan Ke-' + pert);
        $('#modal-id-agenda').val(id);
        $('#modal-date-text').val(hari + ', ' + tgl);
        $('#modal-status').val(status);
        $('#modal-catatan').val(catatan);
        $('#modal-jumlah-jam').val(jmlJam);
        $('#modal-jam-mulai').val(jamMulai);
        $('#modal-jam-selesai').val(jamSelesai);

        // Set TinyMCE content after modal is shown
        const modalEl = document.getElementById('modalEditAgenda');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        modalEl.addEventListener('shown.bs.modal', function handler() {
            if (tinymce.get('modal-materi-editor'))   tinymce.get('modal-materi-editor').setContent(materi);
            if (tinymce.get('modal-kegiatan-editor')) tinymce.get('modal-kegiatan-editor').setContent(kegiatan);
            modalEl.removeEventListener('shown.bs.modal', handler);
        });
    });

    // Before form submits, copy TinyMCE content to hidden inputs
    $('#form-edit-agenda').on('submit', function () {
        if (tinymce.get('modal-materi-editor'))   $('#modal-materi-submit').val(tinymce.get('modal-materi-editor').getContent());
        if (tinymce.get('modal-kegiatan-editor')) $('#modal-kegiatan-submit').val(tinymce.get('modal-kegiatan-editor').getContent());
        return true;
    });

    // Salin agenda: visual selection highlight
    $(document).on('change', 'input[name="source_id"]', function () {
        $('.salin-option').removeClass('border-primary').addClass('border-secondary');
        $(this).closest('.salin-option').removeClass('border-secondary').addClass('border-primary');
    });

});
</script>
