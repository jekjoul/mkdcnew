<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="row gy-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Pembelajaran Saya</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_pembelajaran ?></h4>
                    <a href="<?php echo url('guru/pembelajaran') ?>" class="btn btn-sm btn-primary-600 mt-3">Lihat Pembelajaran</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Siswa Terampu</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_siswa ?></h4>
                    <a href="<?php echo url('guru/siswa') ?>" class="btn btn-sm btn-info text-light mt-3">Lihat Siswa</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-secondary-light d-block">Total Jam Mengajar</span>
                    <h4 class="mb-0"><?php echo (int) $jumlah_jadwal ?></h4>
                    <a href="<?php echo url('guru/jadwal') ?>" class="btn btn-sm btn-warning-600 mt-3">Lihat Jadwal</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-20">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Portal Guru</h6>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-3">
                    <a href="<?php echo url('guru/siswa') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:users-group-two-rounded-linear"></iconify-icon> Data Siswa
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/pembelajaran') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:notebook-bookmark-linear"></iconify-icon> Pembelajaran
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/agenda') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:notebook-linear"></iconify-icon> Agenda Saya
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo url('guru/profil') ?>" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="icon-park-outline:user-business"></iconify-icon> Profil PTK
                    </a>
                </div>
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
                Lihat Semua Agenda Saya <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
            </a>
        </div>
        <div class="card-body p-20">
            <div class="table-responsive">
                <table class="table bordered-table align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 170px;">Hari / Tanggal</th>
                            <th class="text-center" style="width: 200px;">Jadwal Masuk</th>
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
                                    <!-- 1. HARI / TANGGAL -->
                                    <td>
                                        <?php if ($ag->tanggal === $today_str): ?>
                                            <span class="badge bg-warning-50 text-warning-700 radius-4 mb-2 d-inline-block fw-bold">HARI INI</span>
                                        <?php elseif ($ag->tanggal === date('Y-m-d', strtotime('+1 day'))): ?>
                                            <span class="badge bg-info-50 text-info-700 radius-4 mb-2 d-inline-block fw-bold">BESOK</span>
                                        <?php endif; ?>
                                        <span class="fw-semibold text-primary-light d-block"><?php echo html_escape($ag->hari) ?></span>
                                        <span class="text-xs text-secondary-light"><?php echo date('d M Y', strtotime($ag->tanggal)) ?></span>
                                        <span class="badge bg-info-100 text-info-600 radius-4 mt-4 d-inline-block">Pert. Ke-<?php echo $ag->pertemuan_ke ?></span>
                                    </td>

                                    <!-- 2. JADWAL MASUK & STATUS -->
                                    <td class="text-center">
                                        <?php if (!empty($ag->jam_mulai)): ?>
                                            <span class="fw-bold text-primary-900 d-block text-sm"><?php echo html_escape($ag->jam_mulai) ?> - <?php echo html_escape($ag->jam_selesai) ?> WIB</span>
                                        <?php else: ?>
                                            <span class="text-xs text-neutral-400 d-block">Belum diatur</span>
                                        <?php endif; ?>

                                        <div class="mt-4">
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

                                    <!-- 3. ROMBEL & MAPEL -->
                                    <td>
                                        <span class="badge bg-primary-50 text-primary-600 radius-4 mb-4 d-inline-block"><?php echo html_escape((!empty($ag->nama_tingkat) ? $ag->nama_tingkat . ' - ' : '') . $ag->nama_rombel) ?></span>
                                        <div class="fw-semibold text-neutral-800 text-sm"><?php echo html_escape($ag->nama_mapel) ?></div>
                                    </td>

                                    <!-- 4. AKSI: LAKSANAKAN PEMBELAJARAN -->
                                    <td class="text-center">
                                        <a href="<?php echo url('guru/agenda_detail/' . $ag->id_agenda) ?>" class="btn btn-sm btn-success-600 text-white radius-8 px-14 py-8 d-inline-flex align-items-center gap-1 fw-semibold shadow-xs">
                                            <iconify-icon icon="solar:play-circle-bold" class="text-lg"></iconify-icon> Laksanakan Pembelajaran
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-neutral-400 py-24">
                                    <iconify-icon icon="solar:notebook-linear" style="font-size: 28px;"></iconify-icon>
                                    <div class="mt-4 text-xs">Belum ada agenda pembelajaran terdekat yang dijadwalkan.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
});
</script>
