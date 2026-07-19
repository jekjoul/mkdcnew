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
?>
<?php
// Function to format long student names by abbreviating the last name/word
if (!function_exists('format_nama_panjang')) {
    function format_nama_panjang($nama, $limit = 24) {
        // Ubah nama menjadi Capitalize Each Word
        $nama = ucwords(strtolower(trim((string)$nama)));
        if (strlen((string)$nama) <= $limit) {
            return $nama;
        }
        
        $parts = explode(' ', trim((string)$nama));
        if (count($parts) <= 1) {
            return $nama;
        }
        
        $last_index = count($parts) - 1;
        $parts[$last_index] = substr($parts[$last_index], 0, 1) . '.';
        $new_nama = implode(' ', $parts);
        
        while (strlen($new_nama) > $limit && count($parts) > 2) {
            $last_index--;
            if ($last_index <= 0) break;
            $parts[$last_index] = substr($parts[$last_index], 0, 1) . '.';
            $new_nama = implode(' ', $parts);
        }
        
        return $new_nama;
    }
}

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
    <title>Cetak Absensi - <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' ' . $pembelajaran->nama_rombel) ?></title>
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
                page-break-after: avoid;
                page-break-before: avoid;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                box-sizing: border-box;
                display: block !important;
                page-break-inside: avoid;
                page-break-after: avoid;
                page-break-before: avoid;
            }
            .absensi-table {
                page-break-inside: avoid;
            }
            .absensi-table th, .absensi-table td {
                padding: 2px 2px !important;
                font-size: 8.5pt !important;
            }
        }
        @page {
            size: <?php echo ($size === 'A4') ? 'A4 landscape' : 'landscape'; ?>;
            margin-top: 0.5cm !important;
            margin-bottom: 0.5cm !important;
            margin-left: 1cm !important;
            margin-right: 1cm !important;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #333;
            background-color: #fff;
            margin: 20px;
        }
        .container {
            max-width: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: none;
            border-radius: 0;
            border: none;
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
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10pt;
            font-weight: bold;
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
        /* Attendance Table */
        .absensi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 25px;
        }
        .absensi-table th, .absensi-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }
        .absensi-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .absensi-table td.nama {
            text-align: left;
            padding-left: 6px;
        }
        .col-no { width: 3%; }
        .col-nama { width: 18%; }
        .col-gender { width: 4%; }
        .col-date, .col-ket { width: 1.8%; }
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
            size: A4 landscape;
            margin-top: 0.5cm !important;
            margin-bottom: 0.5cm !important;
            margin-left: 1cm !important;
            margin-right: 1cm !important;
        }
        body {
            font-size: 8pt !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #fff !important;
            line-height: 1.15 !important;
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
        .kop-title-lembaga {
            font-size: 13pt !important;
            margin-top: 1px !important;
        }
        .kop-title-sub {
            font-size: 9pt !important;
            margin-top: 1px !important;
        }
        .kop-text-alamat {
            font-size: 6.5pt !important;
            margin-top: 2px !important;
        }
        .doc-title {
            font-size: 9pt !important;
            margin-top: 2px !important;
            margin-bottom: 1px !important;
        }
        .doc-subtitle {
            font-size: 9pt !important;
            margin-bottom: 5px !important;
        }
        .absensi-table {
            font-size: 6.5pt !important;
            margin-bottom: 8px !important;
        }
        .absensi-table th, .absensi-table td {
            padding: 2px 1px !important;
        }
        .absensi-table td {
            font-size: 7.5pt !important;
        }
        .absensi-table td.nama {
            font-size: 8pt !important;
            padding-left: 4px !important;
        }
        .footer-sig {
            margin-top: 5px !important;
            font-size: 8pt !important;
        }
        <?php endif; ?>
    </style>
</head>
<body>

    <?php if (empty($is_pdf)): ?>
    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print();">
            Cetak Absensi
        </button>
    </div>
    <?php endif; ?>

    <div class="container">
        <?php if ($pakai_kop): ?>
            <!-- Render Kop Surat Dinamis -->
            <header class="kop" style="border-bottom: 3px double #111827; padding-bottom: 10px; margin-bottom: 18px; width: 100%;">
                <style>
                    .kop img {
                        max-width: 85px;
                        max-height: 85px;
                    }
                    .kop-title-naungan {
                        font-weight: 550;
                        line-height: 1.2;
                        font-family: 'Times New Roman', Times, serif;
                    }
                    .kop-title-lembaga {
                        font-weight: bold;
                        line-height: 1.2;
                        margin-top: 2px;
                        font-family: 'Times New Roman', Times, serif;
                    }
                    .kop-title-sub {
                        font-weight: bold;
                        line-height: 1.2;
                        margin-top: 2px;
                        color: #333;
                        font-family: 'Times New Roman', Times, serif;
                    }
                    .kop-text-alamat {
                        line-height: 1.3;
                        margin-top: 4px;
                        color: #4b5563;
                        font-family: 'Times New Roman', Times, serif;
                    }
                </style>
                <?php if ($layout_style === 'left_logo'): ?>
                    <table style="width: 100%; border-collapse: collapse; border: 0;">
                        <tr>
                            <td style="width: 90px; vertical-align: middle; text-align: left; padding-right: 15px; border: 0;">
                                <?php if (!empty($logo_kop)): ?>
                                    <img src="<?php echo $logo_kop ?>" alt="Logo">
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle; text-align: left; border: 0;">
                                <?php if (!empty($naungan)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($naungan_2)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan_2) ?></div>
                                <?php endif; ?>
                                <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($nama_lembaga) ?></div>
                                <?php if (!empty($sub_nama)): ?>
                                    <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($sub_nama) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($alamat_kop)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($alamat_kop) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($kontak)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($kontak) ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                <?php elseif ($layout_style === 'double_logo'): ?>
                    <table style="width: 100%; border-collapse: collapse; border: 0;">
                        <tr>
                            <td style="width: 90px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                <?php if (!empty($logo_kop)): ?>
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri">
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle; text-align: center; border: 0;">
                                <?php if (!empty($naungan)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($naungan_2)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan_2) ?></div>
                                <?php endif; ?>
                                <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($nama_lembaga) ?></div>
                                <?php if (!empty($sub_nama)): ?>
                                    <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($sub_nama) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($alamat_kop)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($alamat_kop) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($kontak)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($kontak) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="width: 90px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                <?php if (!empty($logo_kop_kanan)): ?>
                                    <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                <?php else: ?>
                    <!-- Default: Center -->
                    <div style="text-align: center; width: 100%;">
                        <?php if (!empty($logo_kop)): ?>
                            <div style="margin-bottom: 8px; display: flex; justify-content: center;">
                                <img src="<?php echo $logo_kop ?>" alt="Logo">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($naungan)): ?>
                            <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($naungan_2)): ?>
                            <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($naungan_2) ?></div>
                        <?php endif; ?>
                        <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($nama_lembaga) ?></div>
                        <?php if (!empty($sub_nama)): ?>
                            <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($sub_nama) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($alamat_kop)): ?>
                            <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($alamat_kop) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($kontak)): ?>
                            <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($kontak) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <!-- Judul -->
        <div class="doc-title" style="<?php echo !$pakai_kop ? 'margin-top: 0;' : ''; ?>">DAFTAR HADIR SISWA KELAS <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel) ?></div>
        <div class="doc-subtitle">TAHUN PELAJARAN <?php echo htmlspecialchars($pembelajaran->tahun_pelajaran) ?></div>

        <!-- Table Absensi -->
        <table class="absensi-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">No</th>
                    <th rowspan="2" style="width: 12%; padding-left: 0; padding-right: 0; font-size: 8.5pt;">NISN / NIPD</th>
                    <th rowspan="2" class="col-nama">Nama</th>
                    <th rowspan="2" style="width: 4%;">JK</th>
                    <th colspan="31">Bulan : _______________________</th>
                    <th colspan="3">Jumlah</th>
                </tr>
                <tr>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <th class="col-date"><?php echo sprintf("%02d", $i) ?></th>
                    <?php endfor; ?>
                    <th class="col-ket">S</th>
                    <th class="col-ket">I</th>
                    <th class="col-ket">A</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php $no = 1; foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td style="padding-left: 0; padding-right: 0; font-size: 8.5pt;"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nipd ?: '-'); ?></td>
                            <td class="nama"><?php echo htmlspecialchars(format_nama_panjang($s->nama_siswa)) ?></td>
                            <td><?php echo ($s->jenis_kelamin === 'Laki-laki') ? 'L' : (($s->jenis_kelamin === 'Perempuan') ? 'P' : '-'); ?></td>
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <td></td>
                            <?php endfor; ?>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="38" style="padding: 20px; font-style: italic;">Tidak ada data siswa aktif dalam rombel ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Ringkasan Jumlah Siswa & Tanda Tangan Wali Kelas -->
        <table border="0" style="width: 100%; margin-top: 15px; font-size: 10pt; border-collapse: collapse; border: 0;">
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; border: 0; padding: 0;">
                    <?php if (!empty($pakai_jumlah)): ?>
                    <table style="border: 0; font-size: 10pt; line-height: 1.1; border-collapse: collapse; width: auto;">
                        <tr>
                            <td style="padding-right: 15px; border: 0; padding-top: 1px; padding-bottom: 1px;">Laki-laki</td>
                            <td style="padding-right: 10px; border: 0; padding-top: 1px; padding-bottom: 1px;">:</td>
                            <td style="font-weight: bold; border: 0; padding-top: 1px; padding-bottom: 1px;"><?php echo $count_l ?> Siswa</td>
                        </tr>
                        <tr>
                            <td style="border: 0; padding-top: 1px; padding-bottom: 1px;">Perempuan</td>
                            <td style="border: 0; padding-top: 1px; padding-bottom: 1px;">:</td>
                            <td style="font-weight: bold; border: 0; padding-top: 1px; padding-bottom: 1px;"><?php echo $count_p ?> Siswa</td>
                        </tr>
                        <tr>
                            <td style="border: 0; border-top: 1px solid #000; padding-top: 2px; padding-bottom: 1px;">Jumlah</td>
                            <td style="border: 0; border-top: 1px solid #000; padding-top: 2px; padding-bottom: 1px;">:</td>
                            <td style="font-weight: bold; border: 0; border-top: 1px solid #000; padding-top: 2px; padding-bottom: 1px;"><?php echo $total_siswa ?> Siswa</td>
                        </tr>
                    </table>
                    <?php endif; ?>
                    <div style="font-size: 7.5pt; font-style: italic; font-family: Arial, sans-serif; margin-top: 15px; color: #555;">
                        Dicetak tanggal: <?php echo $tgl_cetak ?>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; border: 0; padding: 0;">
                    <?php if ($pakai_ttd): ?>
                        <div style="display: inline-block; text-align: center; width: 250px; margin-right: 20px;">
                            Wali Kelas
                            <br><br><br><br><br>
                            <strong><u><?php echo strtoupper(htmlspecialchars($pembelajaran->nama_walikelas ?: '...........................')) ?></u></strong>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <?php if (empty($is_pdf)): ?>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.open('', '_self', '');
            window.close();
        };
    </script>
    <?php endif; ?>
</body>
</html>
