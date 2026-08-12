<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <!-- Welcome Card dengan Jam Realtime & Hari Tanggal -->
    <div class="row g-2 g-md-3 mb-24">
        <div class="col-md-12">
            <div class="trail-bg h-100 p-20 radius-12 d-flex flex-wrap align-items-center justify-content-between gap-3 text-white">
                <div>
                    <p class="text-white text-sm mb-0" style="margin-bottom:0px !important">Selamat datang kembali,</p>
                    <h6 class="text-white text-xl fw-bold mb-0"><?php echo html_escape(logged('name')) ?></h6>
                </div>
                <div class="text-end ms-auto">
                    <h6 class="text-white text-xl fw-bold mb-0 realtime-clock-display">00:00:00 WIB</h6>
                    <p class="text-white text-sm mb-0 realtime-date-display" style="margin-bottom:0px !important">Hari, 00 Bulan 0000</p>
                </div>
            </div>
        </div>
    </div>
    <!-- 3 Stat Cards (3 Kolom Sejajar) -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs">
                <div class="card-body p-12 p-md-20 text-center">
                    <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                        <iconify-icon icon="solar:notebook-bookmark-bold" class="text-xl"></iconify-icon>
                    </span>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo (int) $jumlah_pembelajaran ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Pembelajaran</span>
                    <a href="<?php echo url('guru/pembelajaran') ?>" class="mobile-hide btn btn-xs btn-primary-600 text-white w-100 mt-2 radius-8 text-xs py-1">Detail</a>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs">
                <div class="card-body p-12 p-md-20 text-center">
                    <span class="w-36-px h-36-px bg-info-50 text-info-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                        <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-xl"></iconify-icon>
                    </span>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo (int) $jumlah_siswa ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Siswa Terampu</span>
                    <a href="<?php echo url('guru/siswa') ?>" class="mobile-hide btn btn-xs btn-info text-white w-100 mt-2 radius-8 text-xs py-1">Detail</a>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 border-0 radius-12 shadow-xs">
                <div class="card-body p-12 p-md-20 text-center">
                    <span class="w-36-px h-36-px bg-warning-50 text-warning-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                        <iconify-icon icon="solar:clock-circle-bold" class="text-xl"></iconify-icon>
                    </span>
                    <h5 class="mb-1 fw-bold text-primary-light text-truncate"><?php echo (int) $jumlah_jadwal ?></h5>
                    <span class="text-secondary-light text-xs d-block text-truncate fw-medium">Jam Mengajar</span>
                    <a href="<?php echo url('guru/jadwal') ?>" class="mobile-hide btn btn-xs btn-warning-600 text-white w-100 mt-2 radius-8 text-xs py-1">Detail</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Android App Grid Tiles -->
    <div class="card border-0 radius-12 shadow-xs mt-20 mb-4">
        <div class="card-header bg-white border-bottom p-16 d-flex align-items-center justify-content-between">
            <h6 class="text-primary-light mb-0 fw-bold d-flex align-items-center gap-2">
                <iconify-icon icon="solar:widget-bold" class="text-primary-600 text-xl"></iconify-icon>
                Menu Portal Guru
            </h6>
        </div>
        <div class="card-body p-16">
            <div class="row row-cols-3 row-cols-md-6 g-2 g-md-3">
                <!-- 1. Jadwal Saya -->
                <div class="col">
                    <a href="<?php echo url('guru/jadwal') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-warning-50 text-warning-600">
                            <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Jadwal Saya</span>
                    </a>
                </div>
                <!-- 2. Input Nilai -->
                <div class="col">
                    <a href="<?php echo url('guru/nilai') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-danger-50 text-danger-600">
                            <iconify-icon icon="solar:document-text-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Input Nilai</span>
                    </a>
                </div>
                <!-- 3. Agenda Saya -->
                <div class="col">
                    <a href="<?php echo url('guru/agenda') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-success-50 text-success-600">
                            <iconify-icon icon="solar:calendar-mark-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Agenda Saya</span>
                    </a>
                </div>
                <!-- 4. Perangkat Pembelajaran -->
                <div class="col">
                    <a href="<?php echo url('guru/perangkat') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-primary-50 text-primary-600">
                            <iconify-icon icon="solar:folder-with-files-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Perangkat</span>
                    </a>
                </div>
                <!-- 5. Profil PTK -->
                <div class="col">
                    <a href="<?php echo url('guru/profil') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-purple-50 text-purple-600" style="background-color: rgba(147, 51, 234, 0.1); color: #9333ea;">
                            <iconify-icon icon="solar:user-id-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Profil PTK</span>
                    </a>
                </div>
                <!-- 6. Data Siswa -->
                <div class="col">
                    <a href="<?php echo url('guru/siswa') ?>" class="mobile-app-tile">
                        <div class="mobile-app-tile-icon bg-info-50 text-info-600">
                            <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                        </div>
                        <span class="mobile-app-tile-label">Data Siswa</span>
                    </a>
                </div>
            </div>
            <!-- Tombol Laporkan Kenakalan Siswa (Full Width, Red Gradient dengan Icon) -->
            <div class="mt-3">
                <a href="<?php echo url('kedisiplinan/tambah') ?>" class="btn btn-danger radius-12 py-14 px-20 w-100 fw-bold text-white shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; font-size: 15px; letter-spacing: 0.3px;">
                    <iconify-icon icon="solar:shield-warning-bold" class="text-2xl"></iconify-icon>
                    <span>Laporkan Kenakalan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Agenda Pembelajaran Terdekat Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24 mt-24">
        <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0 text-primary-light fw-bold d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:notebook-bold" class="text-primary-600 text-xl"></iconify-icon>
                    Agenda Pembelajaran Terdekat / Hari Ini
                </h6>
                <span class="text-xs text-secondary-light">Pantau jadwal masuk KBM dan status pelaksanaan agenda harian Anda.</span>
            </div>
            <a href="<?php echo url('guru/agenda') ?>" class="btn btn-sm btn-outline-primary radius-8">
                Lihat Semua <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
            </a>
        </div>
        <div class="card-body p-20">
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table bordered-table align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Hari, Tanggal & Jadwal</th>
                            <th>Rombel & Mapel</th>
                            <th class="text-center" style="width: 210px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($agenda_terdekat)): ?>
                            <?php
                            $today_str = date('Y-m-d');
                            $now_time  = date('H:i');
                            foreach ($agenda_terdekat as $ag):
                                $is_past_date = ($ag->tanggal < $today_str);
                                $is_today     = ($ag->tanggal === $today_str);
                                $is_late      = false;

                                if ($ag->status === 'Belum') {
                                    if ($is_past_date) {
                                        $is_late = true;
                                    } elseif ($is_today && !empty($ag->jam_mulai) && $now_time > $ag->jam_mulai) {
                                        $is_late = true;
                                    }
                                }
                            ?>
                                <tr id="dash-agenda-row-<?php echo $ag->id_agenda ?>">
                                    <!-- 1. HARI, TANGGAL & JADWAL -->
                                    <td>
                                        <div class="mb-1">
                                            <?php if ($ag->tanggal === $today_str): ?>
                                                <span class="badge bg-warning-50 text-warning-700 radius-4 me-1 fw-bold">HARI INI</span>
                                            <?php elseif ($ag->tanggal === date('Y-m-d', strtotime('+1 day'))): ?>
                                                <span class="badge bg-info-50 text-info-700 radius-4 me-1 fw-bold">BESOK</span>
                                            <?php endif; ?><br>
                                            <span class="fw-semibold text-primary-light"><?php echo html_escape($ag->hari) ?>,</span>
                                            <span class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($ag->tanggal)) ?></span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <?php if (!empty($ag->jam_mulai)): ?>
                                                <span class="fw-bold text-primary-900 text-xs">
                                                    <?php echo html_escape($ag->jam_mulai) ?> - <?php echo html_escape($ag->jam_selesai) ?> WIB
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-neutral-400">Belum diatur</span>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <?php if ($ag->status === 'Terlaksana'): ?>
                                                <span class="badge bg-success-focus text-success-main px-10 py-4 radius-4">Terlaksana</span>
                                            <?php elseif ($is_late): ?>
                                                <span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4 d-inline-flex align-items-center gap-1" title="Jadwal mengajar telah lewat tetapi status belum dilaksanakan">
                                                    <iconify-icon icon="solar:danger-triangle-bold" class="text-xs"></iconify-icon> Terlambat
                                                </span>
                                            <?php elseif ($ag->status === 'Libur'): ?>
                                                <span class="badge bg-danger-focus text-danger-main px-10 py-4 radius-4">Libur KBM</span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-200 text-neutral-700 px-10 py-4 radius-4">Belum Dilaksanakan</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- 2. ROMBEL & MAPEL -->
                                    <td>
                                        <span class="badge bg-primary-50 text-primary-600 radius-4 mb-4 d-inline-block"><?php echo html_escape((!empty($ag->nama_tingkat) ? $ag->nama_tingkat . ' - ' : '') . $ag->nama_rombel) ?></span>
                                        <div class="fw-semibold text-neutral-800 text-sm"><?php echo html_escape($ag->nama_mapel) ?></div>
                                        <span class="badge bg-info-100 text-info-600 radius-4">Pert. Ke-<?php echo $ag->pertemuan_ke ?></span>
                                    </td>

                                    <!-- 3. AKSI: LAKSANAKAN PEMBELAJARAN -->
                                    <td class="text-center">
                                        <a href="<?php echo url('guru/agenda_detail/' . $ag->id_agenda) ?>" class="btn btn-sm btn-success-600 text-white radius-8 px-14 py-8 d-inline-flex align-items-center gap-1 fw-semibold shadow-xs">
                                            <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon> Buka
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-neutral-400 py-24">
                                    <iconify-icon icon="solar:notebook-linear" style="font-size: 28px;"></iconify-icon>
                                    <div class="mt-4 text-xs">Belum ada agenda pembelajaran terdekat yang dijadwalkan.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Accordion View -->
            <style>
            #accordionAgendaDashboard .accordion-button::after {
                display: none !important;
            }
            #accordionAgendaDashboard .accordion-button {
                padding-inline-end: 16px !important;
            }
            </style>
            <div class="accordion d-block d-md-none" id="accordionAgendaDashboard">
                <?php if (!empty($agenda_terdekat)): ?>
                    <?php
                    $today_str = date('Y-m-d');
                    $now_time  = date('H:i');
                    foreach ($agenda_terdekat as $idx => $ag):
                        $is_past_date = ($ag->tanggal < $today_str);
                        $is_today     = ($ag->tanggal === $today_str);
                        $is_late      = false;

                        if ($ag->status === 'Belum') {
                            if ($is_past_date) {
                                $is_late = true;
                            } elseif ($is_today && !empty($ag->jam_mulai) && $now_time > $ag->jam_mulai) {
                                $is_late = true;
                            }
                        }
                    ?>
                        <div class="accordion-item border radius-12 mb-12 shadow-xs overflow-hidden">
                            <h2 class="accordion-header" id="headingDashAgenda<?php echo $ag->id_agenda ?>">
                                <button class="accordion-button <?php echo ($idx === 0) ? '' : 'collapsed'; ?> bg-base text-primary-light px-16 py-12" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseDashAgenda<?php echo $ag->id_agenda ?>" 
                                        aria-expanded="<?php echo ($idx === 0) ? 'true' : 'false'; ?>" 
                                        aria-controls="collapseDashAgenda<?php echo $ag->id_agenda ?>">
                                    <div class="w-100 me-2">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                            <div>
                                                <?php if ($ag->tanggal === $today_str): ?>
                                                    <span class="badge bg-warning-50 text-warning-700 radius-4 me-1 fw-bold">HARI INI</span>
                                                <?php elseif ($ag->tanggal === date('Y-m-d', strtotime('+1 day'))): ?>
                                                    <span class="badge bg-info-50 text-info-700 radius-4 me-1 fw-bold">BESOK</span>
                                                <?php endif; ?>
                                                <span class="fw-bold text-primary-900 text-sm"><?php echo html_escape($ag->hari) ?>, <?php echo date('d M Y', strtotime($ag->tanggal)) ?></span>
                                            </div>
                                            <div>
                                                <?php if ($ag->status === 'Terlaksana'): ?>
                                                    <span class="badge bg-success-focus text-success-main px-8 py-3 radius-4 text-xs">Terlaksana</span>
                                                <?php elseif ($is_late): ?>
                                                    <span class="badge bg-danger-focus text-danger-main px-8 py-3 radius-4 text-xs d-inline-flex align-items-center gap-1">
                                                        <iconify-icon icon="solar:danger-triangle-bold" class="text-xs"></iconify-icon> Terlambat
                                                    </span>
                                                <?php elseif ($ag->status === 'Libur'): ?>
                                                    <span class="badge bg-danger-focus text-danger-main px-8 py-3 radius-4 text-xs">Libur KBM</span>
                                                <?php else: ?>
                                                    <span class="badge bg-neutral-200 text-neutral-700 px-8 py-3 radius-4 text-xs">Belum</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-primary-50 text-primary-600 radius-4 text-xs"><?php echo html_escape((!empty($ag->nama_tingkat) ? $ag->nama_tingkat . ' - ' : '') . $ag->nama_rombel) ?></span>
                                            <span class="fw-semibold text-neutral-800 text-xs text-truncate"><?php echo html_escape($ag->nama_mapel) ?></span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseDashAgenda<?php echo $ag->id_agenda ?>" 
                                 class="accordion-collapse collapse <?php echo ($idx === 0) ? 'show' : ''; ?>" 
                                 aria-labelledby="headingDashAgenda<?php echo $ag->id_agenda ?>" 
                                 data-bs-parent="#accordionAgendaDashboard">
                                <div class="accordion-body bg-neutral-50 p-16">
                                    <div class="row g-2 mb-12">
                                        <div class="col-6">
                                            <span class="text-secondary-light text-xs d-block">Pertemuan</span>
                                            <span class="badge bg-info-100 text-info-600 radius-4">Pert. Ke-<?php echo $ag->pertemuan_ke ?></span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-secondary-light text-xs d-block">Waktu KBM</span>
                                            <?php if (!empty($ag->jam_mulai)): ?>
                                                <span class="fw-bold text-primary-900 text-xs">
                                                    <?php echo html_escape($ag->jam_mulai) ?> - <?php echo html_escape($ag->jam_selesai) ?> WIB
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-neutral-400">Belum diatur</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mt-12 pt-12 border-top">
                                        <a href="<?php echo url('guru/agenda_detail/' . $ag->id_agenda) ?>" class="btn btn-sm btn-success-600 text-white w-100 radius-8 py-8 d-flex align-items-center justify-content-center gap-1 fw-semibold shadow-xs">
                                            <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon> Buka Agenda & Presensi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-neutral-400 py-24 bg-base radius-12 border">
                        <iconify-icon icon="solar:notebook-linear" style="font-size: 28px;"></iconify-icon>
                        <div class="mt-4 text-xs">Belum ada agenda pembelajaran terdekat yang dijadwalkan.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabel Jumlah Siswa per Lembaga & Rombel -->
    <?php if (!empty($siswa_rombel)): ?>
        <div class="row gy-4 mb-24">
            <div class="col-12">
                <div class="card-header bg-transparent border-0 px-0 pb-0 mb-0">
                    <h6 class="mt-3 mb-0 text-primary-light fw-bold d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-primary-600 text-xl"></iconify-icon>
                        Jumlah Siswa per Lembaga & Rombel
                    </h6>
                </div>
            </div>
            <?php foreach ($siswa_rombel as $lembaga => $rombels): ?>
                <div class="col-xxl-6 col-md-6">
                    <div class="card h-100 radius-12 border-0 shadow-xs">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="mb-0 fw-bold text-md text-primary-900 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:buildings-bold" class="text-primary-600"></iconify-icon>
                                <?php echo html_escape($lembaga); ?>
                            </h6>
                        </div>
                        <div class="card-body p-20">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama Rombel</th>
                                            <th scope="col" class="text-center">Laki-laki</th>
                                            <th scope="col" class="text-center">Perempuan</th>
                                            <th scope="col" class="text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_l = 0;
                                        $total_p = 0;
                                        $total_j = 0;
                                        foreach ($rombels as $r): 
                                            $total_l += $r->laki_laki;
                                            $total_p += $r->perempuan;
                                            $total_j += $r->jumlah;
                                        ?>
                                            <tr>
                                                <td><span class="text-secondary-light fw-medium"><?php echo html_escape($r->nama_rombel); ?></span></td>
                                                <td class="text-center"><span class="text-secondary-light"><?php echo number_format($r->laki_laki); ?></span></td>
                                                <td class="text-center"><span class="text-secondary-light"><?php echo number_format($r->perempuan); ?></span></td>
                                                <td class="text-center"><span class="fw-semibold text-primary-light"><?php echo number_format($r->jumlah); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold bg-neutral-100">
                                            <td>Total</td>
                                            <td class="text-center"><?php echo number_format($total_l); ?></td>
                                            <td class="text-center"><?php echo number_format($total_p); ?></td>
                                            <td class="text-center"><?php echo number_format($total_j); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    $(document).on('click', '.btn-dash-toggle-status', function() {
        var btn = $(this);
        var agendaId = btn.data('id');
        var row = $('#dash-agenda-row-' + agendaId);
        var statusCol = row.find('.status-col');
        var actionCol = row.find('.action-col');

        btn.prop('disabled', true);

        $.ajax({
            url: "<?php echo url('guru/toggle_status_agenda') ?>/" + agendaId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false);
                if (res && res.status) {
                    if (res.new_status === 'Terlaksana') {
                        statusCol.html('<span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Terlaksana</span>');
                        actionCol.html('<button type="button" class="btn btn-xs btn-warning-600 text-white radius-8 px-10 py-6 btn-dash-toggle-status" data-id="' + agendaId + '"><iconify-icon icon="solar:restart-bold" class="me-1"></iconify-icon> Belum</button>');
                    } else {
                        statusCol.html('<span class="badge bg-neutral-200 text-neutral-700 px-12 py-6 radius-4">Belum Dilaksanakan</span>');
                        actionCol.html('<button type="button" class="btn btn-xs btn-success-600 text-white radius-8 px-10 py-6 btn-dash-toggle-status" data-id="' + agendaId + '"><iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> Sudah Dilaksanakan</button>');
                    }
                } else {
                    alert('Gagal memperbarui status agenda.');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                alert('Terjadi kesalahan koneksi.');
            }
        });
    });

    // Realtime Clock & Date Script (Server Time Synchronized UTC+7 WIB)
    (function() {
        var serverStartMs = <?php echo (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->getTimestamp() * 1000; ?>;
        var clientStartMs = Date.now();

        function updateRealtimeClock() {
            var elapsed = Date.now() - clientStartMs;
            var serverNow = new Date(serverStartMs + elapsed);

            var utcMs = serverNow.getTime() + (serverNow.getTimezoneOffset() * 60000);
            var wibDate = new Date(utcMs + (7 * 3600000));

            var hours = String(wibDate.getHours()).padStart(2, '0');
            var minutes = String(wibDate.getMinutes()).padStart(2, '0');
            var seconds = String(wibDate.getSeconds()).padStart(2, '0');
            var clockStr = hours + ':' + minutes + ':' + seconds + ' WIB';

            var hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var bulanNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            var hari = hariNames[wibDate.getDay()];
            var tgl = wibDate.getDate();
            var bulan = bulanNames[wibDate.getMonth()];
            var tahun = wibDate.getFullYear();
            var dateStr = hari + ', ' + tgl + ' ' + bulan + ' ' + tahun;

            var clockEls = document.querySelectorAll('.realtime-clock-display');
            for (var i = 0; i < clockEls.length; i++) {
                clockEls[i].textContent = clockStr;
            }

            var dateEls = document.querySelectorAll('.realtime-date-display');
            for (var j = 0; j < dateEls.length; j++) {
                dateEls[j].textContent = dateStr;
            }
        }

        updateRealtimeClock();
        setInterval(updateRealtimeClock, 1000);
    })();
});
</script>
