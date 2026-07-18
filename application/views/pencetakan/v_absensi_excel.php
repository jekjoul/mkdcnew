<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_cetak = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');

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
<table border="0">
    <tr>
        <td colspan="37" style="font-weight: bold; font-size: 14pt; text-align: center;">DAFTAR HADIR SISWA KELAS <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel) ?></td>
    </tr>
    <tr>
        <td colspan="37" style="font-weight: bold; font-size: 12pt; text-align: center;">TAHUN PELAJARAN <?php echo htmlspecialchars($pembelajaran->tahun_pelajaran) ?></td>
    </tr>
    <tr>
        <td colspan="37"></td>
    </tr>
</table>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th rowspan="2">No</th>
            <th rowspan="2">NISN</th>
            <th rowspan="2">NIPD</th>
            <th rowspan="2">Nama</th>
            <th rowspan="2">JK</th>
            <th colspan="31">Tanggal</th>
            <th colspan="3">Jumlah</th>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <?php for ($i = 1; $i <= 31; $i++): ?>
                <th><?php echo sprintf("%02d", $i) ?></th>
            <?php endfor; ?>
            <th>S</th>
            <th>I</th>
            <th>A</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($students)): ?>
            <?php $no = 1; foreach ($students as $s): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++ ?></td>
                    <td style="text-align: center; mso-number-format:'\@';"><?php echo $s->nisn ?: '-'; ?></td>
                    <td style="text-align: center; mso-number-format:'\@';"><?php echo $s->nipd ?: '-'; ?></td>
                    <td><?php echo htmlspecialchars($s->nama_siswa); ?></td>
                    <td style="text-align: center;"><?php echo ($s->jenis_kelamin === 'Laki-laki') ? 'L' : (($s->jenis_kelamin === 'Perempuan') ? 'P' : '-'); ?></td>
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
                <td colspan="37" style="text-align: center; font-style: italic;">Tidak ada data siswa aktif dalam rombel ini.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<br>

<table border="0">
    <tr>
        <td colspan="4" style="font-weight: bold;">Laki-laki</td>
        <td>:</td>
        <td><?php echo $count_l ?> Siswa</td>
        <td colspan="23"></td>
        <td colspan="8" style="text-align: center; font-weight: bold;">
            <?php if ($pakai_ttd): ?>
                Wali Kelas
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;">Perempuan</td>
        <td>:</td>
        <td><?php echo $count_p ?> Siswa</td>
        <td colspan="31"></td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold; border-top: 1px solid #000;">Jumlah</td>
        <td style="border-top: 1px solid #000;">:</td>
        <td style="font-weight: bold; border-top: 1px solid #000;"><?php echo $total_siswa ?> Siswa</td>
        <td colspan="31"></td>
    </tr>
    <tr>
        <td colspan="29"></td>
        <td colspan="8" style="text-align: center; font-weight: bold;">
            <br><br><br>
            <?php if ($pakai_ttd): ?>
                <u><?php echo strtoupper(htmlspecialchars($pembelajaran->nama_walikelas ?: '...........................')) ?></u>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td colspan="37" style="font-size: 9pt; color: #555; font-style: italic;">Dicetak tanggal: <?php echo $tgl_cetak ?></td>
    </tr>
</table>
