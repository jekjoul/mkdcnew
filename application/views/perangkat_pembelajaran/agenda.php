<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Breadcrumb -->
    <div class="d-none d-sm-block d-flex flex-wrap align-items-center justify-content-between gap-3 ">
        <div>
            <h6 class="fw-semibold mb-0 text-primary-light">Daftar Agenda Pembelajaran Saya</h6>
            <p class="text-secondary-light text-sm mb-0">Kelola dan pantau seluruh rencana serta pelaksanaan KBM harian Anda.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="<?php echo url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-secondary-light">Agenda Pembelajaran</li>
        </ul>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Stats Badges -->
    <?php
    $today_str = date('Y-m-d');
    $now_time  = date('H:i');
    
    $agendas_sudah = [];
    $agendas_belum = [];

    if (!empty($agendas)) {
        foreach ($agendas as $row) {
            if ($row->status === 'Terlaksana') {
                $agendas_sudah[] = $row;
            } else {
                $agendas_belum[] = $row;
            }
        }
    }

    $total_agenda = count($agendas);
    $total_terlaksana = count($agendas_sudah);
    $total_belum = count($agendas_belum);
    ?>
    <div class="row g-3 mb-24 mt-10">
        <div class="col-4">
            <div class="card border-0 radius-12 bg-primary-50 p-20 d-flex align-items-center flex-row justify-content-between">
                <div>
                    <span class="text-xs text-primary-600 fw-semibold text-uppercase">Total Agenda Harian</span>
                    <h3 class="mb-0 text-primary-900 fw-bold mt-1"><?php echo $total_agenda ?></h3>
                </div>
                <div class="d-none d-sm-block w-48-px h-48-px bg-primary-600 rounded-circle d-flex align-items-center justify-content-center text-white">
                    <iconify-icon icon="solar:notebook-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 radius-12 bg-warning-50 p-20 d-flex align-items-center flex-row justify-content-between">
                <div>
                    <span class="text-xs text-warning-600 fw-semibold text-uppercase">Belum Dilaksanakan</span>
                    <h3 class="mb-0 text-warning-900 fw-bold mt-1"><?php echo $total_belum ?></h3>
                </div>
                <div class="d-none d-sm-block w-48-px h-48-px bg-warning-600 rounded-circle d-flex align-items-center justify-content-center text-white">
                    <iconify-icon icon="solar:clock-circle-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 radius-12 bg-success-50 p-20 d-flex align-items-center flex-row justify-content-between">
                <div>
                    <span class="text-xs text-success-600 fw-semibold text-uppercase">Sudah Dilaksanakan</span>
                    <h3 class="mb-0 text-success-900 fw-bold mt-1"><?php echo $total_terlaksana ?></h3>
                </div>
                <div class="d-none d-sm-block w-48-px h-48-px bg-success-600 rounded-circle d-flex align-items-center justify-content-center text-white">
                    <iconify-icon icon="solar:check-circle-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Bertingkat Mapel & Rombel) -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-body p-20">
            <form method="GET" action="" id="form-filter-agenda" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Mata Pelajaran Saya</label>
                    <select name="id_mapel" id="filter-mapel" class="form-select radius-8">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?php echo $m->id_mapel ?>" <?php echo ((string)$selected_mapel === (string)$m->id_mapel) ? 'selected' : '' ?>>
                                <?php echo html_escape($m->nama_mapel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Rombongan Belajar (Rombel)</label>
                    <select name="id_rombel" id="filter-rombel" class="form-select radius-8">
                        <option value="">-- Semua Rombel --</option>
                        <?php foreach ($rombel_list as $r): ?>
                            <option value="<?php echo $r->id_rombel ?>" data-mapel="<?php echo $r->id_mapel ?>" <?php echo ((string)$selected_rombel === (string)$r->id_rombel) ? 'selected' : '' ?>>
                                <?php echo html_escape($r->nama_rombel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary radius-8 px-16 w-100" title="Terapkan Filter">
                        <iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon> Filter
                    </button>
                    <?php if ($selected_mapel || $selected_rombel || $selected_status): ?>
                        <a href="<?php echo current_url() ?>" class="btn btn-outline-secondary radius-8 px-12" title="Reset Filter">
                            <iconify-icon icon="solar:restart-bold"></iconify-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Card Tabs Status Agenda -->
    <div class="card border-0 radius-12 shadow-xs">
        <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <ul class="nav nav-tabs style-three mb-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-20 py-10 fw-semibold" id="tab-belum-btn" data-bs-toggle="tab" data-bs-target="#tab-agenda-belum" type="button" role="tab">
                        <iconify-icon icon="solar:clock-circle-bold" class="me-1 text-warning-600"></iconify-icon>
                        Belum Dilaksanakan
                        <span class="badge bg-warning-50 text-warning-600 radius-4 ms-2 px-8 py-2"><?php echo $total_belum ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-20 py-10 fw-semibold" id="tab-sudah-btn" data-bs-toggle="tab" data-bs-target="#tab-agenda-sudah" type="button" role="tab">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1 text-success-600"></iconify-icon>
                        Sudah Dilaksanakan
                        <span class="badge bg-success-50 text-success-600 radius-4 ms-2 px-8 py-2"><?php echo $total_terlaksana ?></span>
                    </button>
                </li>
            </ul>

            <!-- SATU TOMBOL UTAMA DILAKUKAN DI ATAS UNTUK SEMUA AGENDA -->
            <button type="button" class="btn btn-warning-600 text-white radius-8 px-16 py-8 d-inline-flex align-items-center gap-2 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#modalSyncAllJadwal">
                <iconify-icon icon="solar:calendar-minimalistic-bold" class="text-lg"></iconify-icon> Sesuaikan Semua Agenda dengan Jadwal Master
            </button>
        </div>

        <div class="card-body p-20">
            <div class="tab-content">
                <!-- TAB 1: BELUM DILAKSANAKAN -->
                <div class="tab-pane fade show active" id="tab-agenda-belum" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table bordered-table align-middle w-100" id="agendaTableBelum">
                            <thead>
                                <tr>
                                    <th>Hari, Tanggal & Jadwal</th>
                                    <th>Rombel & Mapel</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($agendas_belum)): ?>
                                    <?php foreach ($agendas_belum as $row): ?>
                                        <?php
                                        $is_past_date = ($row->tanggal < $today_str);
                                        $is_today     = ($row->tanggal === $today_str);
                                        $is_late      = false;

                                        if ($is_past_date) {
                                            $is_late = true;
                                        } elseif ($is_today && !empty($row->jam_mulai) && $now_time > $row->jam_mulai) {
                                            $is_late = true;
                                        }
                                        ?>
                                        <tr>
                                            <!-- 1. HARI, TANGGAL & JADWAL -->
                                            <td>
                                                <div class="mb-1">
                                                    <?php if ($row->tanggal === $today_str): ?>
                                                        <span class="badge bg-warning-50 text-warning-700 radius-4 me-1 fw-bold">HARI INI</span>
                                                    <?php elseif ($row->tanggal === date('Y-m-d', strtotime('+1 day'))): ?>
                                                        <span class="badge bg-info-50 text-info-700 radius-4 me-1 fw-bold">BESOK</span>
                                                    <?php endif; ?><br>
                                                    <span class="fw-semibold text-primary-light"><?php echo html_escape($row->hari) ?>,</span>
                                                    <span class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($row->tanggal)) ?></span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                    <?php if (!empty($row->jam_mulai)): ?>
                                                        <span class="fw-bold text-primary-900 text-xs">
                                                            <?php echo html_escape($row->jam_mulai) ?> - <?php echo html_escape($row->jam_selesai) ?> WIB
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-xs text-neutral-400">Belum diatur</span>
                                                    <?php endif; ?>
                                                </div>

                                                <div>
                                                    <?php if ($is_late): ?>
                                                        <span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4 d-inline-flex align-items-center gap-1" title="Jadwal mengajar telah lewat tetapi belum dilaksanakan">
                                                            <iconify-icon icon="solar:danger-triangle-bold" class="text-xs"></iconify-icon> Terlambat
                                                        </span>
                                                    <?php elseif ($row->status === 'Libur'): ?>
                                                        <span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4">Libur KBM</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-neutral-200 text-neutral-700 px-10 py-4 radius-4">Belum Dilaksanakan</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- 2. ROMBEL & MAPEL -->
                                            <td>
                                                <span class="badge bg-primary-50 text-primary-600 radius-4 mb-4 d-inline-block"><?php echo html_escape((!empty($row->nama_tingkat) ? $row->nama_tingkat . ' - ' : '') . $row->nama_rombel) ?></span>
                                                <div class="fw-semibold text-neutral-800 text-sm"><?php echo html_escape($row->nama_mapel) ?></div>
                                                <span class="badge bg-info-100 text-info-600 radius-4">Pert. Ke-<?php echo $row->pertemuan_ke ?></span>
                                            </td>

                                            <!-- 3. AKSI -->
                                            <td class="text-center">
                                                <?php $detail_route = (logged('role') == '1') ? 'perangkat_pembelajaran/agenda_detail/' : 'guru/agenda_detail/'; ?>
                                                <a href="<?php echo url($detail_route . $row->id_agenda) ?>" class="btn btn-sm btn-success-600 text-white radius-8 px-14 py-8 d-inline-flex align-items-center gap-1 fw-semibold shadow-xs">
                                                    <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon> Buka
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: SUDAH DILAKSANAKAN -->
                <div class="tab-pane fade" id="tab-agenda-sudah" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table bordered-table align-middle w-100" id="agendaTableSudah">
                            <thead>
                                <tr>
                                    <th>Hari, Tanggal & Jadwal</th>
                                    <th>Rombel & Mapel</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($agendas_sudah)): ?>
                                    <?php foreach ($agendas_sudah as $row): ?>
                                        <tr>
                                            <!-- 1. HARI, TANGGAL & JADWAL -->
                                            <td>
                                                <div class="mb-1">
                                                    <?php if ($row->tanggal === $today_str): ?>
                                                        <span class="badge bg-warning-50 text-warning-700 radius-4 me-1 fw-bold">HARI INI</span>
                                                    <?php elseif ($row->tanggal === date('Y-m-d', strtotime('+1 day'))): ?>
                                                        <span class="badge bg-info-50 text-info-700 radius-4 me-1 fw-bold">BESOK</span>
                                                    <?php endif; ?><br>
                                                    <span class="fw-semibold text-primary-light"><?php echo html_escape($row->hari) ?>,</span>
                                                    <span class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($row->tanggal)) ?></span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                    <?php if (!empty($row->jam_mulai)): ?>
                                                        <span class="fw-bold text-primary-900 text-xs">
                                                            <?php echo html_escape($row->jam_mulai) ?> - <?php echo html_escape($row->jam_selesai) ?> WIB
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-xs text-neutral-400">Belum diatur</span>
                                                    <?php endif; ?>
                                                </div>

                                                <div>
                                                    <span class="badge bg-success-focus text-success-main px-10 py-4 radius-4">Terlaksana</span>
                                                </div>
                                            </td>

                                            <!-- 2. ROMBEL & MAPEL -->
                                            <td>
                                                <span class="badge bg-primary-50 text-primary-600 radius-4 mb-4 d-inline-block"><?php echo html_escape((!empty($row->nama_tingkat) ? $row->nama_tingkat . ' - ' : '') . $row->nama_rombel) ?></span>
                                                <div class="fw-semibold text-neutral-800 text-sm"><?php echo html_escape($row->nama_mapel) ?></div>
                                                <span class="badge bg-info-100 text-info-600 radius-4">Pert. Ke-<?php echo $row->pertemuan_ke ?></span>
                                            </td>

                                            <!-- 3. AKSI -->
                                            <td class="text-center">
                                                <?php $detail_route = (logged('role') == '1') ? 'perangkat_pembelajaran/agenda_detail/' : 'guru/agenda_detail/'; ?>
                                                <a href="<?php echo url($detail_route . $row->id_agenda) ?>" class="btn btn-sm btn-outline-primary radius-8 px-14 py-8 d-inline-flex align-items-center gap-1 fw-semibold shadow-xs">
                                                    <iconify-icon icon="solar:eye-bold" class="text-lg"></iconify-icon> Detail
                                                </a>
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
    </div>
</div>

<!-- Modal Sync All Agenda dengan Jadwal Master -->
<div class="modal fade" id="modalSyncAllJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <div class="modal-header bg-warning-600 text-white py-14 px-20">
                <h6 class="modal-title text-white mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:calendar-minimalistic-bold" class="text-xl"></iconify-icon>
                    Sesuaikan Semua Agenda dengan Jadwal Master
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open(url((logged('role') == '1' ? 'perangkat_pembelajaran' : 'guru') . '/sync_all_agenda_jadwal')); ?>
            <input type="hidden" name="id_mapel" value="<?php echo html_escape($selected_mapel ?? '') ?>">
            <input type="hidden" name="id_rombel" value="<?php echo html_escape($selected_rombel ?? '') ?>">

            <div class="modal-body p-20">
                <div class="text-center mb-16">
                    <div class="w-56-px h-56-px bg-warning-50 text-warning-600 radius-circle d-inline-flex align-items-center justify-content-center mx-auto mb-12">
                        <iconify-icon icon="solar:calendar-minimalistic-bold" style="font-size: 28px;"></iconify-icon>
                    </div>
                    <h6 class="mb-4 text-primary-900 fw-bold">Sinkronkan Seluruh Agenda KBM?</h6>
                    <p class="text-xs text-secondary-light mb-0">Tindakan ini akan menyelaraskan jam masuk dan jam keluar seluruh agenda pembelajaran KBM dengan Jadwal Master KBM secara massal.</p>
                </div>

                <div class="p-12 radius-8 bg-neutral-50 border text-xs text-neutral-800">
                    <iconify-icon icon="solar:shield-check-bold" class="text-success-600 me-1"></iconify-icon>
                    <strong>Keutuhan Data Terjamin:</strong> Seluruh rincian Materi Pembelajaran, Kegiatan KBM, Catatan Evaluasi, Berkas Media, dan Presensi Kehadiran Siswa <strong>dijamin tetap aman dan tidak akan berubah</strong>.
                </div>
            </div>

            <div class="modal-footer bg-neutral-50 px-20 py-12">
                <button type="button" class="btn btn-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning-600 text-white radius-8 px-16">Ya, Sesuaikan Semua Agenda</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    // Filter Bertingkat: Mapel -> Rombel
    $('#filter-mapel').on('change', function() {
        var selectedMapel = $(this).val();
        $('#filter-rombel option').each(function() {
            var mapelId = $(this).data('mapel');
            if (!selectedMapel || !mapelId || mapelId == selectedMapel) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#filter-rombel').val('');
    });

    if ($('#table-agenda-pembelajaran').length > 0) {
        $('#table-agenda-pembelajaran').DataTable({
            pageLength: 25,
            order: [],
            columnDefs: [
                { orderable: false, targets: 2 }
            ]
        });
    }

    if ($('#agendaTableBelum').length > 0) {
        $('#agendaTableBelum').DataTable({
            pageLength: 25,
            order: [],
            columnDefs: [
                { orderable: false, targets: 2 }
            ]
        });
    }

    if ($('#agendaTableSudah').length > 0) {
        $('#agendaTableSudah').DataTable({
            pageLength: 25,
            order: [],
            columnDefs: [
                { orderable: false, targets: 2 }
            ]
        });
    }
});
</script>
