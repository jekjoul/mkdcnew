<?php include viewPath('includes/header'); ?>
<?php
function jadwal_rows_for_day($setting)
{
    $breaks = [];
    foreach ($setting['istirahat'] as $break) {
        if (isset($break['after'], $break['duration'])) {
            $after = (int) $break['after'];
            $breaks[$after][] = [
                'name' => isset($break['name']) && $break['name'] !== '' ? $break['name'] : 'Waktu Khusus',
                'duration' => (int) $break['duration'],
            ];
        }
    }

    $time = strtotime($setting['jam_mulai']);
    $rows = [];
    if (!empty($breaks[0])) {
        foreach ($breaks[0] as $special) {
            $start = $time;
            $end = $start + ($special['duration'] * 60);
            $rows[] = [
                'type' => 'special',
                'name' => $special['name'],
                'mulai' => date('H:i', $start),
                'selesai' => date('H:i', $end),
            ];
            $time = $end;
        }
    }

    for ($i = 1; $i <= (int) $setting['jumlah_jp']; $i++) {
        $start = $time;
        $end = $start + ((int) $setting['menit_jp'] * 60);
        $rows[] = [
            'type' => 'lesson',
            'slot' => $i,
            'mulai' => date('H:i', $start),
            'selesai' => date('H:i', $end),
            'start_minutes' => ((int) date('H', $start) * 60) + (int) date('i', $start),
            'end_minutes' => ((int) date('H', $end) * 60) + (int) date('i', $end),
        ];
        $time = $end;

        if (!empty($breaks[$i])) {
            foreach ($breaks[$i] as $special) {
                $start = $time;
                $end = $start + ($special['duration'] * 60);
                $rows[] = [
                    'type' => 'special',
                    'name' => $special['name'],
                    'mulai' => date('H:i', $start),
                    'selesai' => date('H:i', $end),
                ];
                $time = $end;
            }
        }
    }

    return $rows;
}

function ptk_color_style($id_ptk)
{
    $id_ptk = (int) $id_ptk;
    if ($id_ptk <= 0) {
        return '--ptk-bg:#f8fafc;--ptk-border:#64748b;--ptk-text:#334155;';
    }

    $hash = (int) sprintf('%u', crc32((string) $id_ptk));
    $hue = $hash % 360;
    return '--ptk-bg:hsl(' . $hue . ', 86%, 92%);--ptk-border:hsl(' . $hue . ', 70%, 36%);--ptk-text:hsl(' . $hue . ', 72%, 22%);';
}

$rows_by_hari = [];
foreach ($hari as $h) {
    if (!empty($settings[$h]['aktif'])) {
        $rows_by_hari[$h] = jadwal_rows_for_day($settings[$h]);
    }
}
?>
<style>
    .all-schedule-layout {
        display: grid;
        grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .subject-bank {
        position: sticky;
        top: 90px;
        max-height: calc(100vh - 120px);
        overflow: auto;
    }

    .token-group {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
    }

    .subject-token,
    .scheduled-token {
        border: 1px solid var(--ptk-border, #111827);
        background: var(--ptk-bg, #f0f9ff);
        color: var(--ptk-text, #111827);
        border-radius: 0;
        padding: 6px 8px;
        cursor: grab;
        user-select: none;
        width: 100%;
        min-height: 38px;
        text-align: center;
    }

    .schedule-wrap {
        overflow-x: auto;
    }

    .weekly-table {
        border-collapse: collapse;
        min-width: 1180px;
        width: 100%;
        background: #fff;
    }

    .weekly-table th,
    .weekly-table td {
        border: 1px solid #111827;
        padding: 0;
        vertical-align: middle;
    }

    .weekly-table th {
        background: #fff;
        text-align: center;
        font-weight: 700;
        padding: 6px;
    }

    .meta-cell {
        text-align: center;
        padding: 6px !important;
        background: #fff;
        white-space: nowrap;
    }

    .day-cell {
        width: 72px;
        text-align: center;
        font-weight: 700;
        letter-spacing: 3px;
        writing-mode: vertical-rl;
        text-orientation: upright;
    }

    .class-col {
        min-width: 118px;
        width: 118px;
    }

    .special-row td {
        background: #10b5df;
        color: #001f2c;
        text-align: center;
        font-weight: 800;
        padding: 3px 8px;
    }

    .schedule-dropzone {
        min-height: 38px;
        background: #fff;
        transition: background .15s, outline .15s;
    }

    .schedule-dropzone.drag-over {
        background: #ecfeff;
        outline: 2px dashed #0891b2;
        outline-offset: -4px;
    }

    .schedule-dropzone.has-conflict {
        background: #fff7ed;
    }

    .teacher-conflict {
        color: #c2410c;
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, .72);
        margin-top: 3px;
    }

    .subject-token .text-secondary-light,
    .scheduled-token .text-secondary-light {
        color: var(--ptk-text, #334155) !important;
        opacity: .78;
    }

    @media (max-width: 991px) {
        .all-schedule-layout {
            grid-template-columns: 1fr;
        }

        .subject-bank {
            position: static;
            max-height: none;
        }
    }
</style>
<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header bg-warning-900 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 text-light">Susun Jadwal Mingguan Semua Kelas</h6>
            <div class="d-flex gap-2">
                <button type="button" id="generateScheduleBtn" class="btn btn-warning-600 btn-sm d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:magic-stick-3-linear"></iconify-icon> Generate Jadwal Otomatis
                </button>
                <a href="<?php echo url('jadwal_pelajaran/waktu') ?>" class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-2 text-light border-0" style="background-color: rgba(255,255,255,0.15);">
                    <iconify-icon icon="lucide:settings"></iconify-icon> Atur Waktu
                </a>
            </div>
        </div>
        <div class="card-body d-flex flex-wrap gap-2">
            <span class="badge bg-primary-100 text-primary-600"><?php echo (int) $menit_jp ?> menit / JP</span>
            <?php foreach ($rows_by_hari as $h => $rows): ?>
                <span class="badge bg-info-100 text-info-600"><?php echo $h ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <form id="scheduleForm" action="<?php echo url('jadwal_pelajaran/simpan_semua') ?>" method="post">
        <div class="all-schedule-layout">
            <div class="card subject-bank">
                <div class="card-header bg-neutral-100">
                    <h6 class="mb-0">Item Mapel per Kelas</h6>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <?php foreach ($pembelajaran as $kelas): ?>
                        <?php
                        $kelas_mapel = isset($mapel_by_pembelajaran[$kelas->id_pembelajaran]) ? $mapel_by_pembelajaran[$kelas->id_pembelajaran] : [];
                        $placed_counts = [];
                        if (!empty($items[$kelas->id_pembelajaran])) {
                            foreach ($items[$kelas->id_pembelajaran] as $day_slots) {
                                foreach ($day_slots as $id_mapel) {
                                    $placed_counts[$id_mapel] = isset($placed_counts[$id_mapel]) ? $placed_counts[$id_mapel] + 1 : 1;
                                }
                            }
                        }
                        ?>
                        <div class="token-group" data-class-id="<?php echo $kelas->id_pembelajaran ?>">
                            <div class="fw-semibold mb-2"><?php echo $kelas->nama_lembaga ?> <?php echo $kelas->nama_tingkat ?> - <?php echo $kelas->nama_rombel ?></div>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($kelas_mapel as $m): ?>
                                    <?php $remaining = max(0, (int) $m->jumlah_jam - (isset($placed_counts[$m->id_mapel]) ? (int) $placed_counts[$m->id_mapel] : 0)); ?>
                                    <div class="border rounded-3 p-2">
                                        <div class="d-flex justify-content-between gap-2 mb-2">
                                            <span class="fw-semibold"><?php echo $m->nama_mapel ?></span>
                                            <span class="badge bg-primary-100 text-primary-600"><?php echo (int) $m->jumlah_jam ?> JP</span>
                                        </div>
                                        <div class="d-flex flex-column gap-2 token-list" data-class-id="<?php echo $kelas->id_pembelajaran ?>" data-mapel-id="<?php echo $m->id_mapel ?>">
                                            <?php for ($i = 1; $i <= $remaining; $i++): ?>
                                                <?php $color_style = ptk_color_style($m->id_ptk); ?>
                                                <div class="subject-token" draggable="true" style="<?php echo $color_style ?>" data-color-style="<?php echo htmlspecialchars($color_style, ENT_QUOTES, 'UTF-8') ?>" data-class-id="<?php echo $kelas->id_pembelajaran ?>" data-mapel-id="<?php echo $m->id_mapel ?>" data-ptk-id="<?php echo (int) $m->id_ptk ?>" data-nama="<?php echo htmlspecialchars($m->nama_mapel, ENT_QUOTES, 'UTF-8') ?>" data-ptk="<?php echo htmlspecialchars($m->nama_ptk ?: '-', ENT_QUOTES, 'UTF-8') ?>" data-class-label="<?php echo htmlspecialchars($kelas->nama_lembaga . ' ' . $kelas->nama_tingkat . ' - ' . $kelas->nama_rombel, ENT_QUOTES, 'UTF-8') ?>">
                                                    <div class="fw-semibold"><?php echo $m->mapel_singkat ?: $m->nama_mapel ?></div>
                                                    <div class="text-secondary-light text-sm"><?php echo $m->nama_ptk ?: '-' ?></div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($kelas_mapel)): ?>
                                    <div class="text-secondary-light text-sm">Belum ada mapel/jumlah JP.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="schedule-wrap">
                    <table class="weekly-table">
                        <thead>
                            <tr>
                                <th style="width:42px;">NO</th>
                                <th style="width:72px;">HARI</th>
                                <th style="width:72px;">JAM KE-</th>
                                <th style="width:140px;">WAKTU</th>
                                <th colspan="<?php echo count($pembelajaran) ?>">KELAS</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <?php foreach ($pembelajaran as $kelas): ?>
                                    <th class="class-col"><?php echo $kelas->nama_tingkat ?> - <?php echo $kelas->nama_rombel ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $day_no = 1; ?>
                            <?php foreach ($rows_by_hari as $h => $rows): ?>
                                <?php $rowspan = count($rows); ?>
                                <?php foreach ($rows as $row_index => $row): ?>
                                    <?php if ($row['type'] === 'special'): ?>
                                        <tr class="special-row">
                                            <?php if ($row_index === 0): ?>
                                                <td class="meta-cell" rowspan="<?php echo $rowspan ?>"><?php echo $day_no ?></td>
                                                <td class="day-cell" rowspan="<?php echo $rowspan ?>"><?php echo strtoupper($h) ?></td>
                                            <?php endif; ?>
                                            <td class="meta-cell"></td>
                                            <td class="meta-cell"><?php echo $row['mulai'] ?> - <?php echo $row['selesai'] ?></td>
                                            <td colspan="<?php echo count($pembelajaran) ?>"><?php echo strtoupper($row['name']) ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <?php if ($row_index === 0): ?>
                                                <td class="meta-cell" rowspan="<?php echo $rowspan ?>"><?php echo $day_no ?></td>
                                                <td class="day-cell" rowspan="<?php echo $rowspan ?>"><?php echo strtoupper($h) ?></td>
                                            <?php endif; ?>
                                            <td class="meta-cell"><?php echo $row['slot'] ?></td>
                                            <td class="meta-cell"><?php echo $row['mulai'] ?> - <?php echo $row['selesai'] ?></td>
                                            <?php foreach ($pembelajaran as $kelas): ?>
                                                <?php
                                                $kelas_mapel = isset($mapel_by_pembelajaran[$kelas->id_pembelajaran]) ? $mapel_by_pembelajaran[$kelas->id_pembelajaran] : [];
                                                $mapel_by_id = [];
                                                foreach ($kelas_mapel as $m) {
                                                    $mapel_by_id[$m->id_mapel] = $m;
                                                }
                                                $selected_id = isset($items[$kelas->id_pembelajaran][$h][$row['slot']]) ? $items[$kelas->id_pembelajaran][$h][$row['slot']] : 0;
                                                $selected = $selected_id && isset($mapel_by_id[$selected_id]) ? $mapel_by_id[$selected_id] : null;
                                                $class_label = $kelas->nama_lembaga . ' ' . $kelas->nama_tingkat . ' - ' . $kelas->nama_rombel;
                                                ?>
                                                <td class="schedule-dropzone" data-class-id="<?php echo $kelas->id_pembelajaran ?>" data-class-label="<?php echo htmlspecialchars($class_label, ENT_QUOTES, 'UTF-8') ?>" data-hari="<?php echo $h ?>" data-slot="<?php echo $row['slot'] ?>" data-start="<?php echo $row['start_minutes'] ?>" data-end="<?php echo $row['end_minutes'] ?>">
                                                    <input type="hidden" name="jadwal[<?php echo $kelas->id_pembelajaran ?>][<?php echo $h ?>][<?php echo $row['slot'] ?>]" value="<?php echo $selected_id ?>">
                                                    <?php if ($selected): ?>
                                                        <?php $color_style = ptk_color_style($selected->id_ptk); ?>
                                                        <div class="scheduled-token" draggable="true" style="<?php echo $color_style ?>" data-color-style="<?php echo htmlspecialchars($color_style, ENT_QUOTES, 'UTF-8') ?>" data-class-id="<?php echo $kelas->id_pembelajaran ?>" data-mapel-id="<?php echo $selected->id_mapel ?>" data-ptk-id="<?php echo (int) $selected->id_ptk ?>" data-nama="<?php echo htmlspecialchars($selected->mapel_singkat ?: $selected->nama_mapel, ENT_QUOTES, 'UTF-8') ?>" data-ptk="<?php echo htmlspecialchars($selected->nama_ptk ?: '-', ENT_QUOTES, 'UTF-8') ?>" data-class-label="<?php echo htmlspecialchars($class_label, ENT_QUOTES, 'UTF-8') ?>">
                                                            <div class="fw-semibold"><?php echo $selected->mapel_singkat ?: $selected->nama_mapel ?></div>
                                                            <div class="text-secondary-light text-sm" style="font-size: 11px; opacity: 0.85;"><?php echo $selected->nama_ptk ?: '-' ?></div>
                                                            <div class="teacher-conflict d-none"></div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php $day_no++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 mb-4">
                    <span id="saveStatus" class="align-self-center text-success-600 me-auto" style="font-size: 13px; font-weight: 550;">
                        <iconify-icon icon="solar:check-circle-linear" class="align-middle fs-5"></iconify-icon> Semua perubahan disimpan
                    </span>
                    <a href="<?php echo url('jadwal_pelajaran') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="button" class="btn btn-outline-danger" id="clearSchedule">
                        <iconify-icon icon="lucide:trash-2"></iconify-icon> Kosongkan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    (function() {
        let dragged = null;

        function tokenList(classId, mapelId) {
            return $('.token-list[data-class-id="' + classId + '"][data-mapel-id="' + mapelId + '"]');
        }

        function makeBankToken(card) {
            const colorStyle = card.attr('data-color-style') || '';
            return $('<div class="subject-token" draggable="true" style="' + escapeAttr(colorStyle) + '" data-color-style="' + escapeAttr(colorStyle) + '" data-class-id="' + card.data('class-id') + '" data-mapel-id="' + card.data('mapel-id') + '" data-ptk-id="' + card.data('ptk-id') + '" data-nama="' + escapeAttr(card.data('nama')) + '" data-ptk="' + escapeAttr(card.data('ptk')) + '" data-class-label="' + escapeAttr(card.data('class-label')) + '">' +
                '<div class="fw-semibold">' + escapeHtml(card.data('nama')) + '</div>' +
                '<div class="text-secondary-light text-sm">' + escapeHtml(card.data('ptk')) + '</div>' +
                '</div>');
        }

        function makeScheduledToken(token) {
            const colorStyle = token.attr('data-color-style') || '';
            return $('<div class="scheduled-token" draggable="true" style="' + escapeAttr(colorStyle) + '" data-color-style="' + escapeAttr(colorStyle) + '" data-class-id="' + token.data('class-id') + '" data-mapel-id="' + token.data('mapel-id') + '" data-ptk-id="' + token.data('ptk-id') + '" data-nama="' + escapeAttr(token.data('nama')) + '" data-ptk="' + escapeAttr(token.data('ptk')) + '" data-class-label="' + escapeAttr(token.data('class-label')) + '">' +
                '<div class="fw-semibold">' + escapeHtml(token.data('nama')) + '</div>' +
                '<div class="text-secondary-light text-sm" style="font-size: 11px; opacity: 0.85;">' + escapeHtml(token.data('ptk')) + '</div>' +
                '<div class="teacher-conflict d-none"></div>' +
                '</div>');
        }

        function returnToBank(card) {
            tokenList(card.data('class-id'), card.data('mapel-id')).append(makeBankToken(card));
            card.remove();
        }

        function clearZone(zone) {
            const existing = zone.find('.scheduled-token');
            if (existing.length) {
                returnToBank(existing);
            }
            zone.find('input[type="hidden"]').val('');
            zone.removeClass('has-conflict');
        }

        function setZone(zone, card) {
            if (String(zone.data('class-id')) !== String(card.data('class-id'))) {
                return;
            }

            clearZone(zone);
            zone.append(card);
            zone.find('input[type="hidden"]').val(card.data('mapel-id'));
            refreshConflicts();
        }

        function refreshConflicts() {
            $('.schedule-dropzone').removeClass('has-conflict');
            $('.teacher-conflict').addClass('d-none').text('');

            const zones = $('.schedule-dropzone').toArray();
            zones.forEach(function(zoneEl, index) {
                const zone = $(zoneEl);
                const card = zone.find('.scheduled-token');
                const teacherId = parseInt(card.data('ptk-id'), 10) || 0;
                if (!card.length || !teacherId) return;

                for (let i = index + 1; i < zones.length; i++) {
                    const other = $(zones[i]);
                    const otherCard = other.find('.scheduled-token');
                    const otherTeacherId = parseInt(otherCard.data('ptk-id'), 10) || 0;
                    if (!otherCard.length || teacherId !== otherTeacherId || zone.data('hari') !== other.data('hari')) continue;

                    const overlaps = parseInt(zone.data('start'), 10) < parseInt(other.data('end'), 10) &&
                        parseInt(zone.data('end'), 10) > parseInt(other.data('start'), 10);
                    if (!overlaps) continue;

                    markConflict(zone, card, other.data('class-label'), otherCard.data('nama'));
                    markConflict(other, otherCard, zone.data('class-label'), card.data('nama'));
                }
            });
        }

        function markConflict(zone, card, classLabel, mapelName) {
            zone.addClass('has-conflict');
            card.find('.teacher-conflict')
                .removeClass('d-none')
                .text('Bentrok: ' + classLabel + ' - ' + mapelName);
        }

        $(document).on('dragstart', '.subject-token, .scheduled-token', function(event) {
            dragged = {
                type: $(this).hasClass('subject-token') ? 'bank' : 'scheduled',
                element: $(this)
            };
            event.originalEvent.dataTransfer.effectAllowed = 'move';
        });

        $(document).on('dragover', '.schedule-dropzone', function(event) {
            event.preventDefault();
            if (dragged && String($(this).data('class-id')) === String(dragged.element.data('class-id'))) {
                $(this).addClass('drag-over');
            }
        });

        $(document).on('dragleave drop', '.schedule-dropzone', function() {
            $(this).removeClass('drag-over');
        });

        let saveTimeout = null;
        function autoSaveSchedule() {
            if (saveTimeout) clearTimeout(saveTimeout);
            
            $('#saveStatus').removeClass('text-success-600 text-danger-600').addClass('text-secondary-600')
                .html('<iconify-icon icon="line-md:loading-twotone-loop" class="align-middle fs-5"></iconify-icon> Menyimpan perubahan...');

            saveTimeout = setTimeout(function() {
                const formData = $('#scheduleForm').serialize();
                $.post($('#scheduleForm').attr('action'), formData, function() {
                    $('#saveStatus').removeClass('text-secondary-600 text-danger-600').addClass('text-success-600')
                        .html('<iconify-icon icon="solar:check-circle-linear" class="align-middle fs-5"></iconify-icon> Semua perubahan disimpan');
                }).fail(function() {
                    $('#saveStatus').removeClass('text-secondary-600 text-success-600').addClass('text-danger-600')
                        .html('<iconify-icon icon="solar:close-circle-linear" class="align-middle fs-5"></iconify-icon> Gagal menyimpan otomatis');
                });
            }, 800); // Debounce save to prevent server hammer
        }

        $(document).on('drop', '.schedule-dropzone', function(event) {
            event.preventDefault();
            if (!dragged) return;

            const zone = $(this);
            if (String(zone.data('class-id')) !== String(dragged.element.data('class-id'))) return;

            if (dragged.type === 'bank') {
                const scheduled = makeScheduledToken(dragged.element);
                dragged.element.remove();
                setZone(zone, scheduled);
            } else {
                const origin = dragged.element.closest('.schedule-dropzone');
                origin.find('input[type="hidden"]').val('');
                setZone(zone, dragged.element.detach());
            }
            autoSaveSchedule();
        });

        $(document).on('dblclick', '.scheduled-token', function() {
            const zone = $(this).closest('.schedule-dropzone');
            returnToBank($(this));
            zone.find('input[type="hidden"]').val('');
            refreshConflicts();
            autoSaveSchedule();
        });

        function showConfirm(title, text, type, callback) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonColor: type === 'warning' ? '#d33' : '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            } else {
                if (confirm(title + '\n\n' + text)) {
                    callback();
                }
            }
        }

        function showAlert(title, text, type) {
            if (typeof Swal !== 'undefined') {
                Swal.fire(title, text, type);
            } else {
                alert(title + ': ' + text);
            }
        }

        function performClearSchedule(quiet) {
            $('.scheduled-token').each(function() {
                returnToBank($(this));
            });
            $('.schedule-dropzone input[type="hidden"]').val('');
            refreshConflicts();
            autoSaveSchedule();
            if (!quiet) {
                showAlert('Dikosongkan!', 'Jadwal telah berhasil dikosongkan.', 'success');
            }
        }

        $('#clearSchedule').on('click', function() {
            showConfirm(
                'Apakah Anda yakin?',
                'Semua jadwal yang sudah disusun akan dikosongkan kembali!',
                'warning',
                function() {
                    performClearSchedule(false);
                }
            );
        });

        $('#generateScheduleBtn').on('click', function() {
            showConfirm(
                'Generate Jadwal Otomatis?',
                'Jadwal saat ini akan dikosongkan terlebih dahulu dan digantikan dengan jadwal otomatis yang baru.',
                'question',
                function() {
                    const btn = $('#generateScheduleBtn');
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<iconify-icon icon="line-md:loading-twotone-loop" class="align-middle"></iconify-icon> Menjadwalkan...');

                    // Kosongkan jadwal yang ada saat ini secara senyap
                    performClearSchedule(true);

                    $.getJSON('<?php echo url('jadwal_pelajaran/generate_otomatis') ?>', function(res) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (res.status === 'success' && Array.isArray(res.data)) {
                            res.data.forEach(function(item) {
                                const classId = item.id_pembelajaran;
                                const hari = item.hari;
                                const slot = item.slot_ke;
                                const mapelId = item.id_mapel;

                                // Cari token pertama di bank mapel kelas yang sesuai
                                const tokenEl = $('.token-list[data-class-id="' + classId + '"][data-mapel-id="' + mapelId + '"] .subject-token').first();
                                if (tokenEl.length) {
                                    // Cari dropzone yang pas
                                    const zone = $('.schedule-dropzone[data-class-id="' + classId + '"][data-hari="' + hari + '"][data-slot="' + slot + '"]');
                                    if (zone.length) {
                                        const scheduled = makeScheduledToken(tokenEl);
                                        tokenEl.remove();
                                        zone.append(scheduled);
                                        zone.find('input[type="hidden"]').val(mapelId);
                                    }
                                }
                            });
                            refreshConflicts();
                            autoSaveSchedule();
                            showAlert('Sukses!', 'Jadwal pelajaran berhasil digenerate secara otomatis tanpa bentrok guru!', 'success');
                        } else {
                            showAlert('Gagal!', 'Gagal menghasilkan jadwal pelajaran otomatis.', 'error');
                        }
                    }).fail(function() {
                        btn.prop('disabled', false).html(originalHtml);
                        showAlert('Error!', 'Koneksi server gagal saat menggenerate jadwal pelajaran.', 'error');
                    });
                }
            );
        });

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function(match) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[match];
            });
        }

        function escapeAttr(value) {
            return escapeHtml(value).replace(/`/g, '&#096;');
        }

        refreshConflicts();
    })();
</script>
