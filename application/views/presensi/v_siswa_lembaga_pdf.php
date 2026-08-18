<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi <?php echo html_escape($lembaga->nama_lembaga_singkat ?: $lembaga->nama_lembaga) . ' - ' . html_escape($judul_bulan) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin-top: 0.6cm;
            margin-bottom: 0.6cm;
            margin-left: 0.8cm;
            margin-right: 0.8cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .page-container {
            width: 100%;
        }
        .page-break {
            page-break-after: always;
        }
        .header-title {
            text-align: center;
            margin-bottom: 10px;
        }
        .header-title h2 {
            margin: 0 0 2px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h3 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.grid-table th, table.grid-table td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
        }
        table.grid-table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 7.5pt;
        }
        table.grid-table td.col-nama {
            text-align: left;
            padding-left: 4px;
            white-space: nowrap;
            overflow: hidden;
            font-size: 7.5pt;
        }
        .libur-cell {
            background-color: #f8fafc;
            color: #475569;
            font-size: 7pt;
            font-weight: bold;
        }
        .ket-footer {
            font-size: 7.5pt;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px solid #000;
            color: #000;
        }
    </style>
</head>
<body>
<?php
$total_rombel = count($rekap_rombel_data);
$r_idx = 0;

foreach ($rekap_rombel_data as $rd):
    $r_idx++;
    $judul_rombel        = $rd['judul_rombel'];
    $siswa_list          = $rd['siswa_list'];
    $presensi_matrix     = $rd['presensi_matrix'];
    $presensi_matrix_pin = $rd['presensi_matrix_by_pin'];
    $total_siswa         = count($siswa_list);

    // Map pure holiday
    $pure_holiday_map = [];
    foreach ($tanggal_list as $t_chk) {
        $tgl_chk = $t_chk->tanggal_absensi;
        $is_lib = (isset($t_chk->status) && $t_chk->status == 'Libur');
        if ($is_lib) {
            $has_tap = false;
            foreach ($siswa_list as $s_chk) {
                if (isset($presensi_matrix[$s_chk->id_siswa][$tgl_chk]) ||
                    (!empty($s_chk->nipd) && isset($presensi_matrix_pin[(string)$s_chk->nipd][$tgl_chk])) ||
                    (!empty($s_chk->pin_fingerprint) && isset($presensi_matrix_pin[(string)$s_chk->pin_fingerprint][$tgl_chk]))) {
                    $has_tap = true;
                    break;
                }
            }
            if (!$has_tap) {
                $pure_holiday_map[$tgl_chk] = true;
            }
        }
    }
?>
    <div class="page-container <?php echo ($r_idx < $total_rombel) ? 'page-break' : ''; ?>">
        <!-- Header Judul -->
        <div class="header-title">
            <h2><?php echo html_escape($judul_rombel); ?></h2>
            <h3><?php echo html_escape($judul_bulan); ?></h3>
        </div>

        <!-- Tabel Grid Presensi -->
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 20px;">No</th>
                    <th style="width: 140px;">Nama Siswa</th>
                    <?php foreach ($tanggal_list as $t): ?>
                        <th style="width: 15px;"><?php echo date('d', strtotime($t->tanggal_absensi)) ?></th>
                    <?php endforeach; ?>
                    <th style="width: 18px;">H</th>
                    <th style="width: 18px;">S</th>
                    <th style="width: 18px;">I</th>
                    <th style="width: 18px;">A</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                <?php foreach ($siswa_list as $s): ?>
                    <?php
                    $no++;
                    $h_c = 0; $s_c = 0; $i_c = 0; $a_c = 0;
                    $row_idx = $no - 1;
                    ?>
                    <tr>
                        <td><?php echo $no ?></td>
                        <td class="col-nama"><?php echo html_escape($s->nama_siswa) ?></td>
                        <?php foreach ($tanggal_list as $t):
                            $tgl = $t->tanggal_absensi;
                            $is_pure_holiday = isset($pure_holiday_map[$tgl]);

                            if ($is_pure_holiday) {
                                if ($row_idx === 0) {
                                    $ket_libur = !empty($t->keterangan) ? $t->keterangan : 'Libur';
                                    echo '<td rowspan="' . $total_siswa . '" class="libur-cell" style="font-size:6pt; width: 15px;">L</td>';
                                }
                                continue;
                            }

                            $po = isset($presensi_matrix[$s->id_siswa][$tgl]) ? $presensi_matrix[$s->id_siswa][$tgl] : null;
                            if (!$po && !empty($s->nipd) && isset($presensi_matrix_pin[(string)$s->nipd][$tgl])) {
                                $po = $presensi_matrix_pin[(string)$s->nipd][$tgl];
                            }
                            if (!$po && !empty($s->pin_fingerprint) && isset($presensi_matrix_pin[(string)$s->pin_fingerprint][$tgl])) {
                                $po = $presensi_matrix_pin[(string)$s->pin_fingerprint][$tgl];
                            }

                            $cell_text = '-';
                            $is_libur = (isset($t->status) && $t->status == 'Libur');

                            if ($po) {
                                if ($po->keterangan === 'Hanya Dhuha') {
                                    $cell_text = 'D';
                                    $h_c++;
                                } elseif ($po->keterangan === 'Hanya Dzuhur') {
                                    $cell_text = 'Z';
                                    $h_c++;
                                } elseif ($po->status === 'Hadir') {
                                    $cell_text = 'H';
                                    $h_c++;
                                } elseif ($po->status === 'Sakit') {
                                    $cell_text = 'S';
                                    $s_c++;
                                } elseif ($po->status === 'Izin') {
                                    $cell_text = 'I';
                                    $i_c++;
                                } elseif ($po->status === 'Alfa') {
                                    $cell_text = 'A';
                                    $a_c++;
                                } else {
                                    $cell_text = 'H';
                                    $h_c++;
                                }
                            } elseif ($is_libur) {
                                $cell_text = 'L';
                            }
                        ?>
                            <td><?php echo $cell_text ?></td>
                        <?php endforeach; ?>

                        <!-- Rekap Total -->
                        <td style="font-weight: bold;"><?php echo $h_c ?></td>
                        <td style="font-weight: bold;"><?php echo $s_c ?></td>
                        <td style="font-weight: bold;"><?php echo $i_c ?></td>
                        <td style="font-weight: bold;"><?php echo $a_c ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Keterangan Absen di Bawah Tabel -->
        <div class="ket-footer">
            <strong>Keterangan Absen:</strong> &nbsp;
            <span><strong>H</strong> = Hadir Lengkap</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>D</strong> = Hanya Dhuha</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>Z</strong> = Hanya Dzuhur</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>S</strong> = Sakit</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>I</strong> = Izin</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>A</strong> = Alfa</span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span><strong>L</strong> = Libur</span>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
