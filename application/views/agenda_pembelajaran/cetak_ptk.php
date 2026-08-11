<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Agenda Pembelajaran Guru - <?php echo html_escape($ptk->nama_ptk); ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; margin: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-0 { margin-bottom: 0; }
        .mb-4 { margin-bottom: 4px; }
        .mb-10 { margin-bottom: 10px; }
        .mb-20 { margin-bottom: 20px; }
        
        .header-kop { border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; text-align: center; }
        .header-kop h3 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header-kop h2 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; }
        .header-kop p { margin: 0; font-size: 10pt; font-style: italic; }

        .info-table { width: 100%; margin-bottom: 15px; font-size: 10.5pt; border-collapse: collapse; }
        .info-table td { padding: 3px 6px; vertical-align: top; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .ttd-table { width: 100%; margin-top: 30px; border-collapse: collapse; font-size: 10.5pt; page-break-inside: avoid; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; }
        
        @media print {
            body { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 16px; font-size: 14px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak Daftar Agenda Guru
        </button>
    </div>

    <!-- Header Kop Surat -->
    <div class="header-kop">
        <h3>DAFTAR AGENDA PEMBELAJARAN GURU PENGAMPU</h3>
        <h2><?php echo html_escape(!empty($lembaga->nama_lembaga) ? $lembaga->nama_lembaga : 'MIFTAHUL KHOER BOARDING SCHOOL'); ?></h2>
        <p><?php echo html_escape(!empty($lembaga->alamat) ? $lembaga->alamat : 'Tahun Pelajaran ' . ($tp_active ? $tp_active->tahun_pelajaran . ' (' . $tp_active->semester . ')' : '')); ?></p>
    </div>

    <!-- Info Guru / PTK -->
    <table class="info-table">
        <tr>
            <td style="width: 18%;"><strong>Nama Guru Pengampu</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;"><?php echo html_escape($ptk->nama_ptk); ?></td>
            <td style="width: 18%;"><strong>Tahun Pelajaran</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;"><?php echo html_escape($tp_active ? $tp_active->tahun_pelajaran . ' - Semester ' . $tp_active->semester : '-'); ?></td>
        </tr>
        <tr>
            <td><strong>NIP / NUPTK</strong></td>
            <td>:</td>
            <td><?php echo html_escape(!empty($ptk->nip) ? $ptk->nip : (!empty($ptk->nuptk) ? $ptk->nuptk : '-')); ?></td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td><?php echo date('d F Y'); ?></td>
        </tr>
    </table>

    <!-- Tabel Data Judul Agenda Pembelajaran -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 120px;">Tahun / Sem</th>
                <th style="width: 120px;">Tingkat / Rombel</th>
                <th>Judul Agenda &amp; Mata Pelajaran</th>
                <th style="width: 180px;">Pengampu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($guru_mapel_list)): ?>
                <?php $no = 1; foreach ($guru_mapel_list as $row): ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td>
                            <strong><?php echo html_escape($row->tahun_pelajaran); ?></strong><br>
                            <small>Semester <?php echo html_escape($row->semester); ?></small>
                        </td>
                        <td class="text-center">
                            <strong><?php echo html_escape($row->nama_tingkat . ' ' . $row->nama_rombel); ?></strong>
                        </td>
                        <td>
                            <strong><?php echo html_escape($row->judul_agenda); ?></strong><br>
                            <small><?php echo html_escape($row->nama_mapel); ?></small>
                        </td>
                        <td class="text-center">
                            <strong><?php echo html_escape($ptk->nama_ptk); ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center"><em>Guru Pengampu ini belum memiliki penugasan mata pelajaran / agenda.</em></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah / Kurikulum
                <br><br><br><br><br>
                ( ________________________ )<br>
                NIP. ........................................
            </td>
            <td>
                Tasikmalaya, <?php echo date('d F Y'); ?><br>
                Guru Pengampu
                <br><br><br><br><br>
                <strong><u><?php echo html_escape($ptk->nama_ptk); ?></u></strong><br>
                NIP. <?php echo html_escape($ptk->nip ?: '-'); ?>
            </td>
        </tr>
    </table>
</body>
</html>
