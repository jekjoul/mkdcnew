<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Function to format long student names by abbreviating the last name/word
if (!function_exists('format_nama_panjang')) {
    function format_nama_panjang($nama, $limit = 24) {
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

// Logic to determine Kop Surat logos and text based on bentuk_pendidikan
$left_logo = '';
$right_logo = '';
$kop_title_1 = '';
$kop_title_2 = '';
$kop_title_3 = $pembelajaran->nama_lembaga;
$kop_detail_1 = 'Dusun Mandala No. 59 RT 017 RW 006 Desa Kertamandala Kec. Panjalu Kab. Ciamis'; // Fallback address
if (!empty($pembelajaran->alamat)) {
    $kop_detail_1 = $pembelajaran->alamat;
}

if ($pembelajaran->bentuk_pendidikan == 'SMP') {
    $left_logo = 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Lambang_Kabupaten_Ciamis.svg/200px-Lambang_Kabupaten_Ciamis.svg.png';
    $right_logo = url('uploads/logo_lembaga/logo_smp.png');
    $kop_title_1 = 'PEMERINTAH KABUPATEN CIAMIS';
    $kop_title_2 = 'DINAS PENDIDIKAN';
} elseif ($pembelajaran->bentuk_pendidikan == 'SMA') {
    $left_logo = 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Coat_of_arms_of_West_Java.svg/200px-Coat_of_arms_of_West_Java.svg.png';
    $right_logo = url('uploads/logo_lembaga/logo_sma.png');
    $kop_title_1 = 'PEMERINTAH PROVINSI JAWA BARAT';
    $kop_title_2 = 'DINAS PENDIDIKAN';
} elseif ($pembelajaran->bentuk_pendidikan == 'PONPES') {
    $left_logo = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Logo_Kementerian_Agama.png/200px-Logo_Kementerian_Agama.png';
    $right_logo = url('uploads/logo_lembaga/logo_ponpes.png');
    $kop_title_1 = 'KEMENTERIAN AGAMA REPUBLIK INDONESIA';
    $kop_title_2 = 'KANTOR KABUPATEN CIAMIS';
} else {
    // Default fallback
    $left_logo = url('assets/images/logodc.png');
    $right_logo = !empty($pembelajaran->logo) ? url('uploads/logo_lembaga/' . $pembelajaran->logo) : url('assets/images/logodc.png');
    $kop_title_1 = 'YAYASAN MIFTAHUL KHOER';
    $kop_title_2 = '';
}

// Build contact lines
$contact_parts = [];
if (!empty($pembelajaran->telepon)) {
    $contact_parts[] = 'Tlp. ' . $pembelajaran->telepon;
}
if (!empty($pembelajaran->email)) {
    $contact_parts[] = 'Email : ' . $pembelajaran->email;
}
if (!empty($pembelajaran->website)) {
    $contact_parts[] = 'Website : ' . $pembelajaran->website;
}
$kop_detail_2 = implode(' ', $contact_parts);
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
            body {
                background: #fff;
                color: #000;
                padding: 0;
                margin: 0;
            }
        }
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #333;
            background-color: #f9f9f9;
            margin: 20px;
        }
        .container {
            max-width: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 4px;
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
        .col-nama { width: 22%; }
        .col-gender { width: 4%; }
        .col-date { width: 2.1%; }
        .col-ket { width: 2.2%; }
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
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print();">
            Cetak Absensi
        </button>
    </div>

    <div class="container">
        <!-- Kop Surat Table format matching sample -->
        <table border="0" align="center" style="width: 100%;">
            <tr>
                <td rowspan="4" align="center" style="width: 100px;">
                    <?php if (!empty($left_logo)): ?>
                        <img src="<?php echo $left_logo ?>" height="90px">
                    <?php endif; ?>
                </td>
                <td align="center" style="font-size: 16px; font-family: 'Times New Roman', Times, serif; text-transform: uppercase;">
                    <?php echo htmlspecialchars($kop_title_1) ?>
                </td>
                <td rowspan="4" align="center" style="width: 100px;">
                    <?php if (!empty($right_logo)): ?>
                        <img src="<?php echo $right_logo ?>" height="90px">
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size: 16px; font-family: 'Times New Roman', Times, serif; text-transform: uppercase; font-weight: bold;">
                    <?php echo htmlspecialchars($kop_title_2) ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size: 19px; font-weight: bold; font-family: 'Times New Roman', Times, serif; text-transform: uppercase;">
                    <?php echo htmlspecialchars($kop_title_3) ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size: 10px; font-family: 'Times New Roman', Times, serif; line-height: 1.3;">
                    <?php if (!empty($pembelajaran->no_sk_akreditasi)): ?>
                        Nomor : <?php echo htmlspecialchars($pembelajaran->no_sk_akreditasi) ?> Terakreditasi <?php echo htmlspecialchars($pembelajaran->akreditasi) ?><br>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($kop_detail_1) ?><br>
                    <?php echo htmlspecialchars($kop_detail_2) ?>
                </td>
            </tr>
        </table>
        <hr style="border: none; border-top: 3px double #000; margin-top: 5px; margin-bottom: 15px;">

        <!-- Judul -->
        <div class="doc-title">PRESENSI SISWA KELAS <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel) ?></div>
        <div class="doc-subtitle">TAHUN PELAJARAN <?php echo htmlspecialchars($pembelajaran->tahun_pelajaran) ?></div>

        <!-- Table Absensi -->
        <table class="absensi-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">No</th>
                    <th rowspan="2" class="col-nama">Nama</th>
                    <th colspan="28">Hari / Tanggal</th>
                    <th colspan="3">Jumlah</th>
                </tr>
                <tr>
                    <?php for ($i = 1; $i <= 28; $i++): ?>
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
                            <td class="nama"><?php echo htmlspecialchars(format_nama_panjang($s->nama_siswa)) ?></td>
                            <?php for ($i = 1; $i <= 28; $i++): ?>
                                <td></td>
                            <?php endfor; ?>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="33" style="padding: 20px; font-style: italic;">Tidak ada data siswa aktif dalam rombel ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <table class="footer-sig">
            <tr>
                <td>
                    <br>
                    Mengetahui,<br>
                    Kepala Sekolah / Lembaga
                    <br><br><br><br><br>
                    <strong><u><?php echo htmlspecialchars($kepsek) ?></u></strong>
                </td>
                <td>
                    Tasikmalaya, .............................. <?php echo date('Y') ?><br>
                    Guru Mata Pelajaran / Wali Kelas
                    <br><br><br><br><br>
                    <strong><u>..................................................</u></strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
