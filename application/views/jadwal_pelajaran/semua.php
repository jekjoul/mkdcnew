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

    $colors = [
        ['bg' => '#dc2626', 'border' => '#991b1b', 'text' => '#ffffff'], // Red
        ['bg' => '#2563eb', 'border' => '#1e40af', 'text' => '#ffffff'], // Blue
        ['bg' => '#16a34a', 'border' => '#166534', 'text' => '#ffffff'], // Green
        ['bg' => '#ea580c', 'border' => '#9a3412', 'text' => '#ffffff'], // Orange
        ['bg' => '#9333ea', 'border' => '#6b21a8', 'text' => '#ffffff'], // Purple
        ['bg' => '#db2777', 'border' => '#9d174d', 'text' => '#ffffff'], // Pink
        ['bg' => '#0d9488', 'border' => '#115e59', 'text' => '#ffffff'], // Teal
        ['bg' => '#4f46e5', 'border' => '#3730a3', 'text' => '#ffffff'], // Indigo
        ['bg' => '#f59e0b', 'border' => '#b45309', 'text' => '#000000'], // Yellow
        ['bg' => '#84cc16', 'border' => '#3f6212', 'text' => '#000000'], // Lime
        ['bg' => '#06b6d4', 'border' => '#155e75', 'text' => '#ffffff'], // Cyan
        ['bg' => '#d97706', 'border' => '#78350f', 'text' => '#ffffff'], // Amber
        ['bg' => '#10b981', 'border' => '#065f46', 'text' => '#ffffff'], // Emerald
        ['bg' => '#7c3aed', 'border' => '#5b21b6', 'text' => '#ffffff'], // Violet
        ['bg' => '#d946ef', 'border' => '#86198f', 'text' => '#ffffff'], // Fuchsia
        ['bg' => '#f43f5e', 'border' => '#9f1239', 'text' => '#ffffff'], // Rose
        ['bg' => '#0ea5e9', 'border' => '#0369a1', 'text' => '#ffffff'], // Sky
        ['bg' => '#475569', 'border' => '#1e293b', 'text' => '#ffffff'], // Slate
        ['bg' => '#78350f', 'border' => '#451a03', 'text' => '#ffffff'], // Brown
        ['bg' => '#9f1239', 'border' => '#4c0519', 'text' => '#ffffff'], // Crimson
        ['bg' => '#1e3a8a', 'border' => '#172554', 'text' => '#ffffff'], // Navy
        ['bg' => '#064e3b', 'border' => '#022c22', 'text' => '#ffffff'], // Forest
        ['bg' => '#7f1d1d', 'border' => '#450a0a', 'text' => '#ffffff'], // Maroon
        ['bg' => '#374151', 'border' => '#111827', 'text' => '#ffffff'], // Dark Grey
        ['bg' => '#65a30d', 'border' => '#3f6212', 'text' => '#ffffff'], // Olive
    ];

    $index = $id_ptk % count($colors);
    $color = $colors[$index];

    return '--ptk-bg:' . $color['bg'] . ';--ptk-border:' . $color['border'] . ';--ptk-text:' . $color['text'] . ';';
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
    
    .teacher-list-card {
        margin-top: 24px;
    }

    .teacher-list-card table th,
    .teacher-list-card table td {
        padding: 0 !important;
        margin: 0 !important;
    }

    .print-only-text {
        display: none !important;
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

    @media print {
        /* Sembunyikan navigasi, menu samping, footer, dan tombol-tombol */
        .sidebar,
        .navbar-header,
        .subject-bank,
        .card-header,
        footer,
        .btn,
        a,
        #saveStatus {
            display: none !important;
        }

        /* Bersihkan padding, margin, dan background layout utama */
        body, 
        .dashboard-main, 
        .dashboard-main-body, 
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            margin-left: 0 !important;
            background: #fff !important;
        }

        /* Ubah tata letak grid menjadi 100% lebar */
        .all-schedule-layout {
            display: block !important;
            grid-template-columns: 1fr !important;
        }

        .schedule-wrap {
            overflow: visible !important;
        }

        /* Sesuaikan ukuran tabel agar rapi saat dicetak */
        .weekly-table {
            min-width: 100% !important;
            width: 100% !important;
            font-size: 10px !important;
            border-collapse: collapse !important;
        }

        .weekly-table th, 
        .weekly-table td {
            border: 1px solid #000 !important;
            padding: 4px 2px !important;
        }

        /* Percantik token jadwal ketika dicetak */
        .scheduled-token {
            border: 1px solid var(--ptk-border, #000) !important;
            padding: 2px !important;
            min-height: auto !important;
            font-size: 9px !important;
            background: var(--ptk-bg, #fff) !important;
            color: var(--ptk-text, #000) !important;
            box-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .scheduled-token .teacher-conflict {
            display: none !important; /* Jangan cetak bentrok */
        }

        @page {
            size: portrait;
            margin: 0.4cm;
        }

        .print-only-title {
            display: block !important;
            text-align: center !important;
            margin-bottom: 12px !important;
            font-weight: bold !important;
            font-size: 14px !important;
            color: #000 !important;
        }

        .screen-only-text {
            display: none !important;
        }

        .print-only-text {
            display: inline !important;
        }

        .weekly-table th, 
        .weekly-table td {
            border: 1px solid #000 !important;
            padding: 2px 1px !important; /* minimized cell padding */
            margin: 0 !important;
        }

        .scheduled-token {
            border: 1px solid var(--ptk-border, #000) !important;
            padding: 1px !important; /* minimized token padding */
            margin: 0 !important;
            min-height: auto !important;
            font-size: 9px !important;
            background: var(--ptk-bg, #fff) !important;
            color: var(--ptk-text, #000) !important;
            box-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            line-height: 1.1 !important;
        }
        
        .scheduled-token .fw-semibold {
            margin: 0 !important;
            padding: 0 !important;
        }

        .teacher-list-card {
            max-width: 100% !important;
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin-top: 15px !important;
            page-break-inside: avoid;
        }
        .teacher-list-card .card-header {
            display: block !important;
            padding: 4px 8px !important;
            font-size: 10px !important;
            border-bottom: 1px solid #000 !important;
        }
        .teacher-list-card .card-body {
            max-height: none !important;
            overflow: visible !important;
            padding: 2px !important;
        }
        .teacher-list-card table {
            font-size: 8px !important;
            width: 100% !important;
        }
        .teacher-list-card th, .teacher-list-card td {
            border: 1px solid #000 !important;
            padding: 0 !important; /* minimized padding */
            margin: 0 !important;
        }
    }
</style>
<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header bg-warning-900 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 text-light">Susun Jadwal Mingguan Semua Kelas</h6>
            <div class="d-flex gap-2">
                <a href="<?php echo url('jadwal_pelajaran/print_semua') ?>" target="_blank" class="btn btn-info btn-sm d-inline-flex align-items-center gap-2 text-light border-0" style="background-color: rgba(255,255,255,0.15);">
                    <iconify-icon icon="lucide:printer"></iconify-icon> Cetak Jadwal
                </a>
                <button type="button" id="generateScheduleBtn" class="btn btn-warning-600 btn-sm d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:magic-stick-3-linear"></iconify-icon> Generate Jadwal Otomatis
                </button>
                <a href="<?php echo url('jadwal_pelajaran/waktu') ?>" class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-2 text-light border-0" style="background-color: rgba(255,255,255,0.15);">
                    <iconify-icon icon="lucide:settings"></iconify-icon> Atur Waktu
                </a>
            </div>
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
                                                    <div class="fw-semibold">
                                                        <span class="screen-only-text"><?php echo $m->mapel_singkat ?: $m->nama_mapel ?></span>
                                                        <span class="print-only-text"><?php echo ($m->mapel_singkat ?: $m->nama_mapel) . ' (' . (int) $m->id_ptk . ')' ?></span>
                                                    </div>
                                                    <div class="text-secondary-light text-sm screen-only-text"><?php echo $m->nama_ptk ?: '-' ?></div>
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
                <?php
                $active_tp = isset($pembelajaran[0]) ? $pembelajaran[0]->tahun_pelajaran : '2026/2027';
                $active_smt = isset($pembelajaran[0]) ? $pembelajaran[0]->semester : 'Ganjil';
                ?>
                <h5 class="print-only-title" style="display: none;">Jadwal Pelajaran Tahun Pelajaran <?php echo $active_tp ?> (Semester <?php echo $active_smt ?>)</h5>
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
                                                            <div class="fw-semibold">
                                                                <span class="screen-only-text"><?php echo $selected->mapel_singkat ?: $selected->nama_mapel ?></span>
                                                                <span class="print-only-text"><?php echo ($selected->mapel_singkat ?: $selected->nama_mapel) . ' (' . (int) $selected->id_ptk . ')' ?></span>
                                                            </div>
                                                            <div class="text-secondary-light text-sm screen-only-text" style="font-size: 11px; opacity: 0.85;"><?php echo $selected->nama_ptk ?: '-' ?></div>
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

                <div class="card teacher-list-card">
                    <div class="card-header bg-neutral-100 py-12 px-16">
                        <h6 class="text-md mb-0">Daftar Kode Guru</h6>
                    </div>
                    <div class="card-body p-8">
                        <?php
                        usort($teachers, function($a, $b) {
                            return (int)$a->id_ptk - (int)$b->id_ptk;
                        });
                        $N = count($teachers);
                        $cols = 3;
                        $rows_per_table = ceil($N / $cols);
                        ?>
                        <div class="d-flex flex-wrap gap-3">
                            <?php for ($c = 0; $c < $cols; $c++): ?>
                                <div style="flex: 1; min-width: 200px;">
                                    <table class="table bordered-table mb-0" style="font-size: 12px; table-layout: fixed; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 80px; padding: 0;">KD</th>
                                                <th style="padding: 0;">Nama Guru</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $start = $c * $rows_per_table;
                                            $end = min($start + $rows_per_table, $N);
                                            for ($i = $start; $i < $end; $i++): 
                                                $t = $teachers[$i];
                                                $color_style = ptk_color_style($t->id_ptk);
                                            ?>
                                                <tr>
                                                    <td class="text-center fw-semibold" style="padding: 0;  <?php echo $color_style ?> background: var(--ptk-bg); color: var(--ptk-text); border: 1px solid var(--ptk-border);"><?php echo $t->id_ptk ?></td>
                                                    <td class="text-truncate" style="padding: 0;" title="<?php echo htmlspecialchars($t->nama_ptk) ?>"><?php echo $t->nama_ptk ?></td>
                                                </tr>
                                            <?php endfor; ?>
                                            <?php
                                            $this_rows = $end - $start;
                                            for ($filler = $this_rows; $filler < $rows_per_table; $filler++):
                                            ?>
                                                <tr>
                                                    <td style="padding: 0;">&nbsp;</td>
                                                    <td style="padding: 0;">&nbsp;</td>
                                                </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
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
            const ptkId = card.data('ptk-id') || 0;
            return $('<div class="subject-token" draggable="true" style="' + escapeAttr(colorStyle) + '" data-color-style="' + escapeAttr(colorStyle) + '" data-class-id="' + card.data('class-id') + '" data-mapel-id="' + card.data('mapel-id') + '" data-ptk-id="' + card.data('ptk-id') + '" data-nama="' + escapeAttr(card.data('nama')) + '" data-ptk="' + escapeAttr(card.data('ptk')) + '" data-class-label="' + escapeAttr(card.data('class-label')) + '">' +
                '<div class="fw-semibold">' +
                    '<span class="screen-only-text">' + escapeHtml(card.data('nama')) + '</span>' +
                    '<span class="print-only-text">' + escapeHtml(card.data('nama')) + ' (' + ptkId + ')</span>' +
                '</div>' +
                '<div class="text-secondary-light text-sm screen-only-text">' + escapeHtml(card.data('ptk')) + '</div>' +
                '</div>');
        }

        function makeScheduledToken(token) {
            const colorStyle = token.attr('data-color-style') || '';
            const ptkId = token.data('ptk-id') || 0;
            return $('<div class="scheduled-token" draggable="true" style="' + escapeAttr(colorStyle) + '" data-color-style="' + escapeAttr(colorStyle) + '" data-class-id="' + token.data('class-id') + '" data-mapel-id="' + token.data('mapel-id') + '" data-ptk-id="' + token.data('ptk-id') + '" data-nama="' + escapeAttr(token.data('nama')) + '" data-ptk="' + escapeAttr(token.data('ptk')) + '" data-class-label="' + escapeAttr(token.data('class-label')) + '">' +
                '<div class="fw-semibold">' +
                    '<span class="screen-only-text">' + escapeHtml(token.data('nama')) + '</span>' +
                    '<span class="print-only-text">' + escapeHtml(token.data('nama')) + ' (' + ptkId + ')</span>' +
                '</div>' +
                '<div class="text-secondary-light text-sm screen-only-text" style="font-size: 11px; opacity: 0.85;">' + escapeHtml(token.data('ptk')) + '</div>' +
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
