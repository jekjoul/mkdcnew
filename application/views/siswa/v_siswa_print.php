<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_cetak = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');

// Hitung jumlah siswa laki-laki & perempuan
$count_l = 0;
$count_p = 0;
if (!empty($students)) {
    foreach ($students as $s) {
        if ($s->jenis_kelamin === 'Laki-laki') {
            $count_l++;
        } elseif ($s->jenis_kelamin === 'Perempuan') {
            $count_p++;
        }
    }
}
$total_siswa = $count_l + $count_p;

// Load data Kop Surat dinamis jika ada
$logo_kop = '';
$logo_kop_kanan = '';
$sz_naungan = 11;
$sz_naungan_2 = 11;
$sz_lembaga = 18;
$sz_sub = 13;
$sz_alamat = 9;
$transform_text = 'uppercase';
$layout_style = '';
$naungan = '';
$naungan_2 = '';
$nama_lembaga = $pembelajaran->nama_lembaga;
$sub_nama = '';
$alamat_kop = '';
$kontak = '';

if (!empty($kop)) {
    if (!empty($is_pdf)) {
        $logo_kop = !empty($kop->logo) ? FCPATH . 'uploads/kop_logo/' . $kop->logo : '';
        $logo_kop_kanan = !empty($kop->logo_kanan) ? FCPATH . 'uploads/kop_logo/' . $kop->logo_kanan : '';
    } else {
        $logo_kop = !empty($kop->logo) ? url('uploads/kop_logo/' . $kop->logo) : '';
        $logo_kop_kanan = !empty($kop->logo_kanan) ? url('uploads/kop_logo/' . $kop->logo_kanan) : '';
    }
    $sz_naungan = $kop->font_size_naungan ?: 11;
    $sz_naungan_2 = $kop->font_size_naungan_2 ?: 11;
    $sz_lembaga = $kop->font_size_lembaga ?: 18;
    $sz_sub = $kop->font_size_sub ?: 13;
    $sz_alamat = $kop->font_size_alamat ?: 9;
    $transform_text = ($kop->case_style === 'custom') ? 'none' : 'uppercase';
    $layout_style = $kop->layout_style;
    $naungan = $kop->naungan;
    $naungan_2 = $kop->naungan_2;
    $nama_lembaga = $kop->nama_lembaga;
    $sub_nama = $kop->sub_nama;
    $alamat_kop = $kop->alamat;
    $kontak = $kop->kontak;
} else {
    // Fallback: Default logo & values
    if (!empty($is_pdf)) {
        $logo_kop = FCPATH . 'assets/images/logodc.png';
        $logo_kop_kanan = !empty($pembelajaran->logo) ? FCPATH . 'uploads/logo_lembaga/' . $pembelajaran->logo : FCPATH . 'assets/images/logodc.png';
    } else {
        $logo_kop = url('assets/images/logodc.png');
        $logo_kop_kanan = !empty($pembelajaran->logo) ? url('uploads/logo_lembaga/' . $pembelajaran->logo) : url('assets/images/logodc.png');
    }
    $naungan = 'YAYASAN MIFTAHUL KHOER';
    $nama_lembaga = $pembelajaran->nama_lembaga;
    $alamat_kop = !empty($pembelajaran->alamat) ? $pembelajaran->alamat : 'Dusun Mandala No. 59 RT 017 RW 006 Desa Kertamandala Kec. Panjalu Kab. Ciamis';
    
    $contact_parts = [];
    if (!empty($pembelajaran->telepon)) $contact_parts[] = 'Tlp. ' . $pembelajaran->telepon;
    if (!empty($pembelajaran->email)) $contact_parts[] = 'Email : ' . $pembelajaran->email;
    if (!empty($pembelajaran->website)) $contact_parts[] = 'Website : ' . $pembelajaran->website;
    $kontak = implode(' | ', $contact_parts);
    $layout_style = 'double_logo';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa - <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' ' . $pembelajaran->nama_rombel) ?></title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                height: 100%;
                overflow: hidden;
                background: #fff;
                color: #000;
                padding: 0 !important;
                margin: 0 !important;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                box-sizing: border-box;
            }
        }
        @page {
            size: A4 portrait;
            margin-top: 0.5cm !important;
            margin-bottom: 0.5cm !important;
            margin-left: 1cm !important;
            margin-right: 1cm !important;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background-color: #fff;
            margin: 20px;
        }
        .container {
            max-width: 100%;
            background: #fff;
            padding: 20px;
        }
        /* Document Title & Meta */
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-table td.label {
            width: 120px;
        }
        .meta-table td.separator {
            width: 15px;
            text-align: center;
        }
        /* Student Table */
        .siswa-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        .siswa-table th, .siswa-table td {
            border: 1px solid #000;
            padding: 5px 4px;
        }
        .siswa-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .siswa-table td.text-center {
            text-align: center;
        }
        .siswa-table td.text-left {
            text-align: left;
            padding-left: 6px;
        }
        .col-no { width: 5%; text-align: center; }
        .col-nama { width: 35%; }
        .col-nisn { width: 15%; text-align: center; }
        .col-nipd { width: 15%; text-align: center; }
        .col-gender { width: 8%; text-align: center; }
        .col-ttl { width: 22%; }
        
        /* Signatures block */
        .footer-sig {
            width: 100%;
            margin-top: 20px;
            font-size: 10.5pt;
        }
        .footer-sig td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        /* Floating Button for printing */
        .print-btn-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
        }
        .print-btn {
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-size: 11pt;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease-in-out;
        }
        .print-btn:hover {
            background-color: #1d4ed8;
            transform: scale(1.05);
        }

        <?php if (!empty($is_pdf)): ?>
        @page {
            size: A4 portrait;
            margin-top: 0.5cm !important;
            margin-bottom: 0.5cm !important;
            margin-left: 1cm !important;
            margin-right: 1cm !important;
        }
        body {
            font-size: 9pt !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #fff !important;
            line-height: 1.2 !important;
        }
        .container {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }
        .kop {
            margin-bottom: 5px !important;
            padding-bottom: 3px !important;
        }
        .kop img {
            max-width: 50px !important;
            max-height: 50px !important;
        }
        .kop-title-naungan {
            font-size: 8pt !important;
        }
        .kop-title-naungan-2 {
            font-size: 8pt !important;
        }
        .kop-title-lembaga {
            font-size: 14pt !important;
        }
        .kop-title-sub {
            font-size: 10pt !important;
        }
        .kop-alamat {
            font-size: 7.5pt !important;
        }
        .kop-kontak {
            font-size: 7.5pt !important;
        }
        .doc-title {
            font-size: 11pt !important;
            margin-top: 5px !important;
        }
        .doc-subtitle {
            font-size: 10pt !important;
            margin-bottom: 10px !important;
        }
        .meta-table {
            font-size: 9pt !important;
            margin-bottom: 8px !important;
        }
        .siswa-table {
            font-size: 8.5pt !important;
            margin-bottom: 12px !important;
        }
        .siswa-table th, .siswa-table td {
            padding: 3px 2px !important;
        }
        .footer-sig {
            font-size: 9.5pt !important;
            margin-top: 10px !important;
        }
        <?php endif; ?>
    </style>
</head>
<body>

    <?php if (empty($is_pdf)): ?>
    <div class="print-btn-container no-print">
        <button onclick="window.print();" class="print-btn">
            Cetak Dokumen
        </button>
    </div>
    <?php endif; ?>

    <div class="container">
        <!-- HEADER KOP SURAT -->
        <?php if (!empty($pakai_kop)): ?>
            <div class="kop" style="width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 15px; position: relative;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <?php if ($layout_style === 'double_logo' || $layout_style === 'left_logo'): ?>
                            <td style="width: 10%; text-align: left; vertical-align: middle;">
                                <?php if ($logo_kop): ?>
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri" style="max-width: 65px; max-height: 65px; height: auto;">
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        
                        <td style="text-align: center; vertical-align: middle;">
                            <?php if ($naungan): ?>
                                <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>pt; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($naungan) ?></div>
                            <?php endif; ?>
                            <?php if ($naungan_2): ?>
                                <div class="kop-title-naungan-2" style="font-size: <?php echo $sz_naungan_2 ?>pt; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($naungan_2) ?></div>
                            <?php endif; ?>
                            
                            <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>pt; font-weight: bold; text-transform: <?php echo $transform_text ?>; line-height: 1.2; margin-top: 2px;">
                                <?php echo htmlspecialchars($nama_lembaga) ?>
                            </div>
                            
                            <?php if ($sub_nama): ?>
                                <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>pt; font-weight: bold; text-transform: uppercase; margin-top: 2px;"><?php echo htmlspecialchars($sub_nama) ?></div>
                            <?php endif; ?>
                            
                            <div class="kop-alamat" style="font-size: <?php echo $sz_alamat ?>pt; font-style: italic; margin-top: 4px; line-height: 1.2;">
                                <?php echo htmlspecialchars($alamat_kop) ?>
                            </div>
                            <?php if ($kontak): ?>
                                <div class="kop-kontak" style="font-size: <?php echo $sz_alamat - 0.5 ?>pt; font-style: italic; margin-top: 2px;">
                                    <?php echo htmlspecialchars($kontak) ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <?php if ($layout_style === 'double_logo'): ?>
                            <td style="width: 10%; text-align: right; vertical-align: middle;">
                                <?php if ($logo_kop_kanan): ?>
                                    <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan" style="max-width: 65px; max-height: 65px; height: auto;">
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <div style="margin-top: <?php echo !empty($is_pdf) ? '0' : '20px'; ?>;"></div>
        <?php endif; ?>

        <!-- TITLE DOKUMEN -->
        <div class="doc-title">DAFTAR PESERTA DIDIK (SISWA)</div>
        <div class="doc-subtitle">KELAS <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel) ?></div>

        <!-- META DATA ROMBEL -->
        <table class="meta-table">
            <tr>
                <td style="width: 50%;">
                    <table style="width: 100%;">
                        <tr>
                            <td class="label" style="width: 130px;">Lembaga</td>
                            <td class="separator">:</td>
                            <td><?php echo htmlspecialchars($pembelajaran->nama_lembaga) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Kelas / Rombel</td>
                            <td class="separator">:</td>
                            <td><?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel) ?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td class="label" style="width: 130px;">Tahun Pelajaran</td>
                            <td class="separator">:</td>
                            <td><?php echo htmlspecialchars($pembelajaran->tahun_pelajaran . ' ' . $pembelajaran->semester) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Wali Kelas</td>
                            <td class="separator">:</td>
                            <td><?php echo htmlspecialchars($pembelajaran->nama_walikelas ?: '-') ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SISWA TABLE -->
        <table class="siswa-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-nama">Nama Siswa</th>
                    <th class="col-nisn">NISN</th>
                    <th class="col-nipd">NIPD</th>
                    <th class="col-gender">L/P</th>
                    <th class="col-ttl">Tempat, Tanggal Lahir</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="text-left"><?php echo htmlspecialchars($s->nama_siswa); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($s->nisn ?: '-'); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($s->nipd ?: '-'); ?></td>
                            <td class="text-center">
                                <?php 
                                    if ($s->jenis_kelamin === 'Laki-laki') echo 'L';
                                    elseif ($s->jenis_kelamin === 'Perempuan') echo 'P';
                                    else echo '-';
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $ttl_parts = [];
                                    if (!empty($s->tempat_lahir)) $ttl_parts[] = $s->tempat_lahir;
                                    if (!empty($s->tanggal_lahir)) {
                                        $t = explode('-', $s->tanggal_lahir);
                                        if (count($t) === 3) {
                                            $ttl_parts[] = $t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
                                        } else {
                                            $ttl_parts[] = $s->tanggal_lahir;
                                        }
                                    }
                                    echo htmlspecialchars(implode(', ', $ttl_parts) ?: '-');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="font-style: italic; padding: 15px;">Tidak ada data siswa aktif dalam rombel ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- STATISTIK GENDER -->
        <div style="font-size: 9.5pt; margin-bottom: 20px; font-weight: bold;">
            Statistik: Laki-laki (L) = <?php echo $count_l; ?>, Perempuan (P) = <?php echo $count_p; ?>, Total Siswa = <?php echo $total_siswa; ?>
        </div>

        <!-- SIGNATURES BLOCK -->
        <?php if (!empty($pakai_ttd)): ?>
            <table class="footer-sig">
                <tr>
                    <td>
                        <div>Mengetahui,</div>
                        <div style="font-weight: bold; margin-bottom: 60px;">Kepala Sekolah</div>
                        <div style="font-weight: bold; text-decoration: underline;"><?php echo htmlspecialchars($kepsek) ?></div>
                        <div>NIP/NUPTK: -</div>
                    </td>
                    <td>
                        <div>Panjalu, <?php echo $tgl_cetak; ?></div>
                        <div style="font-weight: bold; margin-bottom: 60px;">Wali Kelas</div>
                        <div style="font-weight: bold; text-decoration: underline;"><?php echo htmlspecialchars($pembelajaran->nama_walikelas ?: '...........................') ?></div>
                        <div>NIP/NUPTK: -</div>
                    </td>
                </tr>
            </table>
        <?php endif; ?>

        <!-- PRINT DATE INFO (ITALIC, SMALL) -->
        <div style="font-size: 7.5pt; font-style: italic; font-family: Arial, sans-serif; text-align: right; margin-top: 15px; color: #555; border-top: 1px dotted #ccc; padding-top: 3px;">
            dicetak tanggal : <?php echo date('d-m-Y H:i:s'); ?>
        </div>
    </div>

</body>
</html>
