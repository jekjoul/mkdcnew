<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Kegiatan Pembelajaran Guru</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 10pt; 
            color: #000; 
            margin: 15px; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-0 { margin-bottom: 0; }
        
        /* Kop Surat Styles */
        .kop-wrapper {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: 0;
        }
        .kop-table td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo-left {
            width: 80px;
            text-align: left;
            padding-right: 12px;
        }
        .kop-logo-right {
            width: 80px;
            text-align: right;
            padding-left: 12px;
        }
        .kop-logo-img {
            max-width: 78px;
            max-height: 78px;
            object-fit: contain;
        }

        .info-table { 
            width: 100%; 
            margin-bottom: 14px; 
            font-size: 10pt; 
            border-collapse: collapse; 
        }
        .info-table td { 
            padding: 3px 4px; 
            vertical-align: top; 
        }

        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 18px; 
            font-size: 9.5pt; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 5px 6px; 
            vertical-align: top; 
        }
        .data-table th { 
            background-color: #f0f0f0; 
            text-align: center; 
            font-weight: bold; 
        }

        .ttd-table { 
            width: 100%; 
            margin-top: 25px; 
            border-collapse: collapse; 
            font-size: 10pt; 
            page-break-inside: avoid; 
        }
        .ttd-table td { 
            width: 50%; 
            text-align: center; 
            vertical-align: top; 
        }

        @media print {
            body { margin: 8mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print();" style="padding: 8px 18px; font-size: 13px; background: #16a34a; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
            🖨️ Cetak Jurnal Guru (Print / PDF)
        </button>
    </div>

    <?php
    // Helper function format nama dan gelar kapital
    if (!function_exists('formatNamaGelarKapital')) {
        function formatNamaGelarKapital($ptk) {
            if (!$ptk) return '________________________';
            $nama = strtoupper(trim((string)$ptk->nama_ptk));
            $gd = trim((string)($ptk->gelar_depan ?? ''));
            $gb = trim((string)($ptk->gelar_belakang ?? ''));

            if ($gd === '0' || $gd === '-') $gd = '';
            if ($gb === '0' || $gb === '-') $gb = '';

            $res = $nama;
            if (!empty($gd)) {
                $res = $gd . ' ' . $res;
            }
            if (!empty($gb)) {
                $res = $res . ', ' . $gb;
            }
            return $res;
        }
    }

    $formatted_guru_name   = formatNamaGelarKapital($guru_info ?: $ptk_logged);
    $formatted_kepsek_name = formatNamaGelarKapital($kepsek_ptk);

    $guru_niy = ($guru_info && !empty($guru_info->niy) && $guru_info->niy !== '-') 
        ? $guru_info->niy 
        : (($ptk_logged && !empty($ptk_logged->niy) && $ptk_logged->niy !== '-') ? $ptk_logged->niy : '-');
    $kepsek_niy = ($kepsek_ptk && !empty($kepsek_ptk->niy) && $kepsek_ptk->niy !== '-') ? $kepsek_ptk->niy : '-';

    // Format Tanggal Indonesia
    $bulan_indo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $tgl_hari_ini = date('j') . ' ' . $bulan_indo[(int)date('n')] . ' ' . date('Y');
    ?>

    <!-- KOP SURAT RESMI SESUAI PENGATURAN LEMBAGA -->
    <div class="kop-wrapper">
        <?php if (!empty($kop_surat)): ?>
            <?php
            $logo_kop = !empty($kop_surat->logo) ? base_url('uploads/kop_logo/' . $kop_surat->logo) : base_url('assets/images/logodc_round.png');
            $logo_kanan = !empty($kop_surat->logo_kanan) ? base_url('uploads/kop_logo/' . $kop_surat->logo_kanan) : null;
            
            $sz_naungan   = $kop_surat->font_size_naungan ?: 12;
            $sz_naungan_2 = $kop_surat->font_size_naungan_2 ?: 12;
            $sz_lembaga   = $kop_surat->font_size_lembaga ?: 17;
            $sz_sub       = $kop_surat->font_size_sub ?: 11;
            $sz_alamat    = $kop_surat->font_size_alamat ?: 8.5;
            $transform_text = ($kop_surat->case_style === 'custom') ? 'none' : 'uppercase';
            $layout = $kop_surat->layout_style ?: 'center';
            ?>
            <table class="kop-table">
                <tr>
                    <?php if (!empty($logo_kop)): ?>
                        <td class="kop-logo-left">
                            <img src="<?php echo $logo_kop; ?>" class="kop-logo-img" alt="Logo">
                        </td>
                    <?php endif; ?>
                    <td class="text-center" style="width: 100%;">
                        <?php if (!empty($kop_surat->naungan)): ?>
                            <div style="font-size: <?php echo $sz_naungan; ?>pt; font-weight: bold; text-transform: <?php echo $transform_text; ?>; line-height: 1.2;">
                                <?php echo html_escape($kop_surat->naungan); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($kop_surat->naungan_2)): ?>
                            <div style="font-size: <?php echo $sz_naungan_2; ?>pt; font-weight: bold; text-transform: <?php echo $transform_text; ?>; line-height: 1.2;">
                                <?php echo html_escape($kop_surat->naungan_2); ?>
                            </div>
                        <?php endif; ?>
                        <div style="font-size: <?php echo $sz_lembaga; ?>pt; font-weight: bold; text-transform: <?php echo $transform_text; ?>; line-height: 1.2; margin-top: 1px;">
                            <?php echo html_escape($kop_surat->nama_lembaga); ?>
                        </div>
                        <?php if (!empty($kop_surat->sub_nama)): ?>
                            <div style="font-size: <?php echo $sz_sub; ?>pt; font-weight: bold; text-transform: <?php echo $transform_text; ?>; line-height: 1.2; margin-top: 1px;">
                                <?php echo html_escape($kop_surat->sub_nama); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($kop_surat->alamat)): ?>
                            <div style="font-size: <?php echo $sz_alamat; ?>pt; font-style: normal; line-height: 1.25; margin-top: 2px;">
                                <?php echo html_escape($kop_surat->alamat); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($kop_surat->kontak)): ?>
                            <div style="font-size: <?php echo $sz_alamat; ?>pt; font-style: italic; line-height: 1.2;">
                                <?php echo html_escape($kop_surat->kontak); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <?php if (!empty($logo_kanan)): ?>
                        <td class="kop-logo-right">
                            <img src="<?php echo $logo_kanan; ?>" class="kop-logo-img" alt="Logo Kanan">
                        </td>
                    <?php elseif (!empty($logo_kop)): ?>
                        <td class="kop-logo-right" style="visibility: hidden;">
                            <img src="<?php echo $logo_kop; ?>" class="kop-logo-img" alt="">
                        </td>
                    <?php endif; ?>
                </tr>
            </table>
        <?php else: ?>
            <div class="text-center">
                <h2 style="margin: 0; text-transform: uppercase; font-size: 14pt;"><?php echo html_escape($lembaga_info ? $lembaga_info->nama_lembaga : 'YAYASAN MIFTAHUL KHOER EL-ISTOHARY'); ?></h2>
                <p style="margin: 0; font-size: 9pt; font-style: italic;">Dusun Mandala RT 018 RW 006 Desa Kertamandala Kecamatan Panjalu Kab. Ciamis</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- JUDUL DOKUMEN CETAK -->
    <div class="text-center" style="margin-bottom: 14px;">
        <h3 style="margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; text-decoration: underline;">JURNAL KEGIATAN PEMBELAJARAN GURU</h3>
        <div style="font-size: 9.5pt; font-weight: bold; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">
            AGENDA HARIAN KEGIATAN &amp; PRESENSI SISWA
        </div>
    </div>

    <!-- INFO FILTER / METADATA HEADER -->
    <table class="info-table">
        <tr>
            <td style="width: 16%;"><strong>Guru Pengampu</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 37%;"><?php echo html_escape($guru_info ? $guru_info->nama_ptk : ($ptk_logged ? $ptk_logged->nama_ptk : 'Semua Guru')); ?></td>
            <td style="width: 16%;"><strong>Mata Pelajaran</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 27%;"><?php echo html_escape($mapel_info ? $mapel_info->nama_mapel : 'Semua Mata Pelajaran'); ?></td>
        </tr>
        <tr>
            <td><strong>Rombel / Kelas</strong></td>
            <td>:</td>
            <td>
                <?php 
                if ($rombel_info) {
                    $label_rombel = (!empty($tingkat_nama) ? $tingkat_nama . ' - ' : '') . $rombel_info->nama_rombel;
                } else {
                    $label_rombel = 'Semua Rombel';
                }
                echo html_escape($label_rombel);
                ?>
            </td>
            <td><strong>Tahun Pelajaran</strong></td>
            <td>:</td>
            <td>
                <?php 
                if ($tahun_info) {
                    $sem_label = is_numeric($tahun_info->semester) ? 'Semester ' . $tahun_info->semester : (strpos(strtolower($tahun_info->semester), 'semester') !== false ? $tahun_info->semester : 'Semester ' . $tahun_info->semester);
                    echo html_escape($tahun_info->tahun_pelajaran . ' (' . $sem_label . ')');
                } else {
                    echo '-';
                }
                ?>
            </td>
        </tr>
    </table>

    <!-- DATA TABEL JURNAL -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 95px;">Hari / Tanggal</th>
                <th style="width: 120px;">Rombel &amp; Mapel</th>
                <th>Materi &amp; Pokok Bahasan</th>
                <th style="width: 150px;">Hambatan KBM</th>
                <th style="width: 150px;">Pemecahan Masalah</th>
                <th style="width: 85px;">Absensi Siswa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jurnal_list)): ?>
                <?php foreach ($jurnal_list as $idx => $row): ?>
                    <?php 
                    $row_rombel = (!empty($row->nama_tingkat) ? $row->nama_tingkat . ' - ' : '') . $row->nama_rombel;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $idx + 1; ?></td>
                        <td>
                            <strong><?php echo html_escape($row->hari); ?></strong><br>
                            <span style="font-size: 8.5pt; color: #333;"><?php echo date('d/m/Y', strtotime($row->tanggal)); ?></span><br>
                            <span style="font-size: 8.5pt; font-weight: bold;">Ke-<?php echo $row->pertemuan_ke; ?></span>
                        </td>
                        <td>
                            <strong><?php echo html_escape($row_rombel); ?></strong><br>
                            <span style="font-size: 8.5pt;"><?php echo html_escape($row->nama_mapel); ?></span>
                            <?php if ($is_admin && !empty($row->nama_ptk)): ?>
                                <br><span style="font-size: 8pt; font-style: italic; color: #555;"><?php echo html_escape($row->nama_ptk); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo !empty($row->materi) ? html_escape(strip_tags($row->materi)) : '-'; ?></td>
                        <td><?php echo !empty($row->hambatan_fix) ? html_escape($row->hambatan_fix) : '-'; ?></td>
                        <td><?php echo !empty($row->pemecahan_fix) ? html_escape($row->pemecahan_fix) : '-'; ?></td>
                        <td class="text-center" style="font-size: 9pt; line-height: 1.3;">
                            H: <strong><?php echo $row->absensi_h; ?></strong> | 
                            I: <strong><?php echo $row->absensi_i; ?></strong><br>
                            S: <strong><?php echo $row->absensi_s; ?></strong> | 
                            A: <strong><?php echo $row->absensi_a; ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 24px;">Belum ada data jurnal kegiatan pembelajaran.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN KEPALA SEKOLAH & GURU MAPEL -->
    <table class="ttd-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                Kepala <?php echo html_escape($lembaga_info ? $lembaga_info->nama_lembaga : 'Sekolah'); ?>
                <br><br><br><br><br>
                <strong style="text-decoration: underline;"><?php echo html_escape($formatted_kepsek_name); ?></strong><br>
                <span style="font-size: 9pt;">NIY. <?php echo html_escape($kepsek_niy); ?></span>
            </td>
            <td style="width: 50%;">
                Panjalu, <?php echo $tgl_hari_ini; ?><br>
                Guru Mata Pelajaran
                <br><br><br><br><br>
                <strong style="text-decoration: underline;"><?php echo html_escape($formatted_guru_name); ?></strong><br>
                <span style="font-size: 9pt;">NIY. <?php echo html_escape($guru_niy); ?></span>
            </td>
        </tr>
    </table>

    <!-- TANGGAL CETAK DI BAGIAN PALING BAWAH SETELAH TTD -->
    <div style="margin-top: 24px; font-size: 8pt; font-style: italic; color: #666; text-align: right;">
        Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?> WIB
    </div>
</body>
</html>
