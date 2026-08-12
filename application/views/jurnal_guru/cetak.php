<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Pembelajaran KBM Guru</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; margin: 15px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-0 { margin-bottom: 0; }
        
        .header-kop { border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px; text-align: center; }
        .header-kop h3 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header-kop h2 { margin: 2px 0; font-size: 14pt; text-transform: uppercase; }
        .header-kop p { margin: 0; font-size: 9.5pt; font-style: italic; }

        .info-table { width: 100%; margin-bottom: 12px; font-size: 10pt; border-collapse: collapse; }
        .info-table td { padding: 3px 4px; vertical-align: top; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9.5pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }

        .ttd-table { width: 100%; margin-top: 25px; border-collapse: collapse; font-size: 10pt; page-break-inside: avoid; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; }

        @media print {
            body { margin: 8mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 16px; font-size: 13px; background: #16a34a; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Jurnal Guru (Print / PDF)
        </button>
    </div>

    <!-- Header Kop -->
    <div class="header-kop">
        <h3>JURNAL PEMBELAJARAN KBM GURU</h3>
        <h2>AGENDA HARIAN KEGIATAN KBM & PRESENSI SISWA</h2>
    </div>

    <!-- Info Filter Header -->
    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>Guru Pengampu</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><?php echo html_escape($guru_info ? $guru_info->nama_ptk : ($ptk_logged ? $ptk_logged->nama_ptk : 'Semua Guru')); ?></td>
            <td style="width: 15%;"><strong>Mata Pelajaran</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><?php echo html_escape($mapel_info ? $mapel_info->nama_mapel : 'Semua Mata Pelajaran'); ?></td>
        </tr>
        <tr>
            <td><strong>Rombel / Kelas</strong></td>
            <td>:</td>
            <td><?php echo html_escape($rombel_info ? $rombel_info->nama_rombel : 'Semua Rombel'); ?></td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td><?php echo date('d M Y H:i'); ?> WIB</td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Hari / Tanggal</th>
                <th style="width: 110px;">Rombel &amp; Mapel</th>
                <th>Materi &amp; Pokok Bahasan</th>
                <th style="width: 160px;">Hambatan KBM</th>
                <th style="width: 160px;">Pemecahan Masalah</th>
                <th style="width: 90px;">Absensi Siswa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jurnal_list)): ?>
                <?php foreach ($jurnal_list as $idx => $row): ?>
                    <tr>
                        <td class="text-center"><?php echo $idx + 1; ?></td>
                        <td>
                            <strong><?php echo html_escape($row->hari); ?></strong><br>
                            <small><?php echo date('d/m/Y', strtotime($row->tanggal)); ?></small><br>
                            <small>Ke-<?php echo $row->pertemuan_ke; ?></small>
                        </td>
                        <td>
                            <strong><?php echo html_escape($row->nama_rombel); ?></strong><br>
                            <small><?php echo html_escape($row->nama_mapel); ?></small>
                            <?php if ($is_admin && !empty($row->nama_ptk)): ?>
                                <br><small><i><?php echo html_escape($row->nama_ptk); ?></i></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo !empty($row->materi) ? html_escape(strip_tags($row->materi)) : '-'; ?></td>
                        <td><?php echo !empty($row->hambatan_fix) ? html_escape($row->hambatan_fix) : '-'; ?></td>
                        <td><?php echo !empty($row->pemecahan_fix) ? html_escape($row->pemecahan_fix) : '-'; ?></td>
                        <td class="text-center">
                            H: <?php echo $row->absensi_h; ?><br>
                            I: <?php echo $row->absensi_i; ?><br>
                            S: <?php echo $row->absensi_s; ?><br>
                            A: <?php echo $row->absensi_a; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Belum ada data jurnal KBM.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <br><br><br><br>
                <strong>________________________</strong>
            </td>
            <td>
                <?php echo date('d M Y'); ?><br>
                Guru Mata Pelajaran
                <br><br><br><br>
                <strong><?php echo html_escape($guru_info ? $guru_info->nama_ptk : ($ptk_logged ? $ptk_logged->nama_ptk : '________________________')); ?></strong>
            </td>
        </tr>
    </table>
</body>
</html>
