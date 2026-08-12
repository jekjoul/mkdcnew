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
<table border="0" style="font-family: Arial, sans-serif;">
    <?php if (!empty($pakai_kop)): ?>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 14pt; font-weight: bold;"><?php echo !empty($kop) ? htmlspecialchars($kop->naungan) : 'YAYASAN MIFTAHUL KHOER'; ?></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 16pt; font-weight: bold;"><?php echo htmlspecialchars($pembelajaran->nama_lembaga); ?></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 10pt; font-style: italic;"><?php echo htmlspecialchars($pembelajaran->alamat); ?></td>
        </tr>
        <tr>
            <td colspan="6" style="border-bottom: 2px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    <?php endif; ?>
    
    <tr>
        <td colspan="6" style="text-align: center; font-size: 12pt; font-weight: bold;">DAFTAR PESERTA DIDIK (SISWA)</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center; font-size: 11pt; font-weight: bold;">KELAS <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel); ?></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    
    <tr>
        <td style="font-weight: bold;">Lembaga</td>
        <td colspan="2">: <?php echo htmlspecialchars($pembelajaran->nama_lembaga); ?></td>
        <td style="font-weight: bold;">Tahun Pelajaran</td>
        <td colspan="2">: <?php echo htmlspecialchars($pembelajaran->tahun_pelajaran . ' ' . $pembelajaran->semester); ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Kelas/Rombel</td>
        <td colspan="2">: <?php echo htmlspecialchars($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel); ?></td>
        <td style="font-weight: bold;">Wali Kelas</td>
        <td colspan="2">: <?php echo htmlspecialchars($pembelajaran->nama_walikelas ?: '-'); ?></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
</table>

<table border="1" cellpadding="5" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="font-weight: bold; text-align: center; width: 50px;">No</th>
            <th style="font-weight: bold; text-align: left; width: 250px;">Nama Siswa</th>
            <th style="font-weight: bold; text-align: center; width: 120px;">NISN</th>
            <th style="font-weight: bold; text-align: center; width: 120px;">NIPD</th>
            <th style="font-weight: bold; text-align: center; width: 70px;">L/P</th>
            <th style="font-weight: bold; text-align: left; width: 200px;">Tempat, Tanggal Lahir</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($s->nama_siswa); ?></td>
                    <td style="text-align: center; mso-number-format:'\@';"><?php echo htmlspecialchars($s->nisn ?: '-'); ?></td>
                    <td style="text-align: center; mso-number-format:'\@';"><?php echo htmlspecialchars($s->nipd ?: '-'); ?></td>
                    <td style="text-align: center;">
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
                <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data siswa aktif dalam rombel ini.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<table border="0" style="font-family: Arial, sans-serif; font-size: 10pt;">
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;">Statistik: Laki-laki (L) = <?php echo $count_l; ?>, Perempuan (P) = <?php echo $count_p; ?>, Total Siswa = <?php echo $total_siswa; ?></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    
    <?php if (!empty($pakai_ttd)): ?>
        <tr>
            <td colspan="3" style="text-align: center;">Mengetahui,</td>
            <td colspan="3" style="text-align: center;">Panjalu, <?php echo $tgl_cetak; ?></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold;">Kepala Sekolah</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">Wali Kelas</td>
        </tr>
        <tr>
            <td colspan="3" style="height: 50px;"></td>
            <td colspan="3" style="height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;"><?php echo htmlspecialchars($kepsek); ?></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;"><?php echo htmlspecialchars($pembelajaran->nama_walikelas ?: '...........................'); ?></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">NIP/NUPTK: -</td>
            <td colspan="3" style="text-align: center;">NIP/NUPTK: -</td>
        </tr>
    <?php endif; ?>
    
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6" style="font-size: 8pt; font-style: italic; text-align: right; color: #555;">dicetak tanggal : <?php echo date('d-m-Y H:i:s'); ?></td>
    </tr>
</table>
