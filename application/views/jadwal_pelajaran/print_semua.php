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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Pelajaran</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/lib/bootstrap.min.css') ?>">
    <style>
        body {
            background: #fff;
            color: #000;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .print-container {
            padding: 0.2cm;
        }

        .print-only-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .weekly-table {
            min-width: 100%;
            width: 100%;
            font-size: 8px;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .weekly-table th, 
        .weekly-table td {
            border: 1px solid #000 !important;
            padding: 2px 1px !important;
            margin: 0 !important;
            text-align: center;
            vertical-align: middle;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .weekly-table th {
            background-color: #f3f4f6 !important;
            font-weight: bold;
        }

        .scheduled-token {
            border: 1px solid var(--ptk-border, #000) !important;
            padding: 1px !important;
            margin: 0 !important;
            min-height: auto !important;
            font-size: 9px;
            background: var(--ptk-bg, #fff) !important;
            color: var(--ptk-text, #000) !important;
            box-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            line-height: 1.1 !important;
            font-weight: 600;
        }

        .teacher-list-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .teacher-list-card .card-header {
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            background-color: #f3f4f6 !important;
        }
        .teacher-list-card .card-body {
            padding: 2px !important;
        }
        .teacher-list-card table {
            font-size: 8px;
            width: 100%;
            border-collapse: collapse;
        }
        .teacher-list-card th, .teacher-list-card td {
            border: 1px solid #000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        @page {
            size: portrait;
            margin: 0.4cm;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <?php
        $active_tp = isset($pembelajaran[0]) ? $pembelajaran[0]->tahun_pelajaran : '2026/2027';
        $active_smt = isset($pembelajaran[0]) ? $pembelajaran[0]->semester : 'Ganjil';
        ?>
        <h5 class="print-only-title">Jadwal Pelajaran Tahun Pelajaran <?php echo $active_tp ?> (Semester <?php echo $active_smt ?>)</h5>
        
        <table class="weekly-table">
            <thead>
                <tr>
                    <th style="width: 42px;">NO</th>
                    <th style="width: 42px;">HARI</th>
                    <th style="width: 42px;">JAM KE-</th>
                    <th style="width: 100px;">WAKTU</th>
                    <?php foreach ($pembelajaran as $kelas): ?>
                        <th><?php echo $kelas->nama_tingkat . ' - ' . $kelas->nama_rombel ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $day_no = 1;
                foreach ($hari as $h): 
                    $day_rows = isset($rows_by_hari[$h]) ? $rows_by_hari[$h] : [];
                    if (empty($day_rows)) continue;
                    $rowspan = count($day_rows);
                ?>
                    <?php foreach ($day_rows as $row_index => $row): ?>
                        <?php if ($row['type'] === 'special'): ?>
                            <tr style="background-color: #f3f4f6;">
                                <?php if ($row_index === 0): ?>
                                    <td rowspan="<?php echo $rowspan ?>" class="fw-bold align-middle text-center" style="font-size: 11px; writing-mode: padding: 5px !important; background-color: #10b5df; color: #001f2c;">
                                        <?php echo $day_no ?>
                                    </td>
                                    <td rowspan="<?php echo $rowspan ?>" class="fw-bold align-middle " style="font-size: 11px; writing-mode: vertical-lr; padding: 5px !important; background-color: #10b5df; color: #001f2c;">
                                        <?php echo strtoupper($h) ?>
                                    </td>
                                <?php endif; ?>
                                <td style="background-color: #10b5df; color: #001f2c;"></td>
                                <td class="text-center align-middle" style="font-size: 8px; font-weight: 500; background-color: #10b5df; color: #001f2c;">
                                    <?php echo $row['mulai'] . ' - ' . $row['selesai'] ?>
                                </td>
                                <td colspan="<?php echo count($pembelajaran) ?>" class="text-center fw-bold" style="font-size: 9px; padding: 2px !important; background-color: #10b5df; color: #001f2c;">
                                    <?php echo strtoupper($row['name']) ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <?php if ($row_index === 0): ?>
                                    <td rowspan="<?php echo $rowspan ?>" class="fw-bold align-middle text-center" style="font-size: 11px; writing-mode: vertical-lr; transform: rotate(180deg); padding: 5px !important; background-color: #10b5df; color: #001f2c;">
                                        <?php echo $day_no ?>
                                    </td>
                                    <td rowspan="<?php echo $rowspan ?>" class="fw-bold align-middle text-center" style="font-size: 11px; writing-mode: vertical-lr; transform: rotate(180deg); padding: 5px !important; background-color: #10b5df; color: #001f2c;">
                                        <?php echo strtoupper($h) ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center align-middle" style="font-size: 8px; font-weight: 500; background-color: #10b5df; color: #001f2c;">
                                    <?php echo $row['slot'] ?>
                                </td>
                                <td class="text-center align-middle" style="font-size: 8px; font-weight: 500; background-color: #10b5df; color: #001f2c;">
                                    <?php echo $row['mulai'] . ' - ' . $row['selesai'] ?>
                                </td>
                                <?php foreach ($pembelajaran as $kelas): ?>
                                    <?php
                                    $kelas_mapel = isset($mapel_by_pembelajaran[$kelas->id_pembelajaran]) ? $mapel_by_pembelajaran[$kelas->id_pembelajaran] : [];
                                    $mapel_by_id = [];
                                    foreach ($kelas_mapel as $m) {
                                        $mapel_by_id[$m->id_mapel] = $m;
                                    }
                                    $selected_id = isset($items[$kelas->id_pembelajaran][$h][$row['slot']]) ? $items[$kelas->id_pembelajaran][$h][$row['slot']] : 0;
                                    $selected = $selected_id && isset($mapel_by_id[$selected_id]) ? $mapel_by_id[$selected_id] : null;
                                    ?>
                                    <td class="text-center align-middle" style="padding: 2px 1px !important;">
                                        <?php if ($selected): ?>
                                            <?php $color_style = ptk_color_style($selected->id_ptk); ?>
                                            <div class="scheduled-token" style="<?php echo $color_style ?>">
                                                <?php echo ($selected->mapel_singkat ?: $selected->nama_mapel) . ' (' . (int) $selected->id_ptk . ')' ?>
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

        <div class="card teacher-list-card">
            <div class="card-header">
                Daftar Kode Guru
            </div>
            <div class="card-body">
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
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 80px; padding: 0; background-color: #f3f4f6;">Kode</th>
                                        <th style="padding: 0; background-color: #f3f4f6; padding-left: 4px;">Nama Guru</th>
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
                                            <td class="text-center fw-semibold" style="padding: 0; <?php echo $color_style ?> background: var(--ptk-bg); color: var(--ptk-text); border: 1px solid var(--ptk-border);"><?php echo $t->id_ptk ?></td>
                                            <td style="padding: 0; padding-left: 4px;" title="<?php echo htmlspecialchars($t->nama_ptk) ?>"><?php echo $t->nama_ptk ?></td>
                                        </tr>
                                    <?php endfor; ?>
                                    <?php
                                    $this_rows = $end - $start;
                                    for ($filler = $this_rows; $filler < $rows_per_table; $filler++):
                                    ?>
                                        <tr>
                                            <td style="padding: 0;">&nbsp;</td>
                                            <td style="padding: 0; padding-left: 4px;">&nbsp;</td>
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

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
