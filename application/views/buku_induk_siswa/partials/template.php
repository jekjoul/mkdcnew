<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = function ($value) {
    $value = trim((string) $value);
    return $value !== '' && $value !== '0' ? html_escape($value) : '';
};
$field = function ($name, $default = '') use ($siswa) {
    return isset($siswa->$name) && $siswa->$name !== null ? $siswa->$name : $default;
};
$date = function ($value) {
    return !empty($value) ? tanggal_indo($value) : '';
};
$jk = function ($value) {
    $value = strtolower(trim((string) $value));
    if ($value === 'l' || $value === 'laki-laki') return 'Laki-laki';
    if ($value === 'p' || $value === 'perempuan') return 'Perempuan';
    return $value ? ucwords($value) : '';
};
$alamat = function ($prefix = '') use ($siswa) {
    $get = function ($field) use ($siswa) {
        return isset($siswa->$field) ? $siswa->$field : '';
    };
    $field = $prefix ? 'alamat_' . $prefix : 'alamat';
    $rt = $prefix ? 'rt_' . $prefix : 'rt';
    $rw = $prefix ? 'rw_' . $prefix : 'rw';
    $kel = $prefix ? 'id_kelurahan_' . $prefix : 'id_kelurahan';
    $kec = $prefix ? 'id_kecamatan_' . $prefix : 'id_kecamatan';
    $kab = $prefix ? 'id_kabupaten_' . $prefix : 'id_kabupaten';
    $prov = $prefix ? 'id_provinsi_' . $prefix : 'id_provinsi';

    $parts = [];
    if ($get($field) !== '' && $get($field) !== '0') $parts[] = $get($field);
    if ($get($rt) !== '' || $get($rw) !== '') $parts[] = 'RT ' . ($get($rt) ?: '-') . ' RW ' . ($get($rw) ?: '-');
    if ($get($kel) !== '' && $get($kel) !== '0') $parts[] = 'Desa/Kel. ' . $get($kel);
    if ($get($kec) !== '' && $get($kec) !== '0') $parts[] = 'Kec. ' . $get($kec);
    if ($get($kab) !== '' && $get($kab) !== '0') $parts[] = 'Kab. ' . $get($kab);
    if ($get($prov) !== '' && $get($prov) !== '0') $parts[] = 'Prov. ' . $get($prov);

    return html_escape(implode("\n", $parts));
};
$formatNilai = function ($value) {
    if ($value === null || $value === '') return '';
    $number = (float) $value;
    return floor($number) == $number ? (string) (int) $number : number_format($number, 2, ',', '');
};
$lines = function ($value) {
    $rows = preg_split('/\r\n|\r|\n/', trim((string) $value));
    return array_values(array_filter($rows, function ($row) {
        return trim($row) !== '';
    }));
};
$penyakitRows = $lines($field('riwayat_penyakit'));
$prestasiRows = $lines($field('prestasi_siswa'));
$semesterLabel = function ($row, $index) {
    if (!empty($row['semester'])) {
        return stripos($row['semester'], 'genap') !== false ? 'SMT 2' : 'SMT 1';
    }
    return 'SMT ' . ($index + 1);
};
$yearPairs = [];
for ($i = 0; $i < 6; $i += 2) {
    $label = !empty($semester_columns[$i]['tahun_pelajaran']) ? $semester_columns[$i]['tahun_pelajaran'] : (!empty($semester_columns[$i + 1]['tahun_pelajaran']) ? $semester_columns[$i + 1]['tahun_pelajaran'] : '');
    $yearPairs[] = $label;
}
$foto_url = !empty($foto) ? url('uploads/siswa_foto/' . $foto->foto) : '';
?>
<style>
    @page {
        size: A3 portrait;
        margin: 8mm;
    }

    .buku-induk-page {
        width: 297mm;
        min-height: 420mm;
        margin: 16px auto;
        padding: 8mm;
        background: #fff;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        line-height: 1.08;
        box-sizing: border-box;
    }

    .buku-induk-page * {
        box-sizing: border-box;
    }

    .buku-title {
        text-align: center;
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .buku-section-title {
        font-size: 18px;
        font-weight: 700;
        margin: 5px 0 3px;
        margin-top: 20px;
    }

    .buku-header-grid {
        display: grid;
        grid-template-columns: 1fr 25mm;
        gap: 5mm;
        align-items: start;
    }

    .buku-photo {
        width: 40mm;
        height: 60mm;
        border: 1px solid #000;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        margin-top: 13px;
        font-size: 16px;
        line-height: 1.2;
    }

    .buku-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .buku-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .buku-table td,
    .buku-table th {
        border: 0.7px solid #777;
        padding: 1px 3px;
        vertical-align: top;
    }

    .buku-table.no-border td {
        border: 0;
        padding: 0;
    }

    .label-cell {
        width: 17%;
    }

    .colon-cell {
        width: 3%;
        text-align: center !important;
    }

    .value-cell {
        width: 20%;
        white-space: pre-line;
    }

    .nilai-table th {
        text-align: center;
        font-weight: 700;
    }

    .nilai-table td {
        text-align: center;
    }

    .nilai-table .mapel {
        text-align: left;
    }

    .nilai-table .group-row td {
        font-weight: 700;
        /* background: #fff; */
    }

    .buku-small-table th,
    .buku-small-table td {
        height: 10px;
    }

    @media print {
        .buku-induk-page {
            width: 100%;
            min-height: auto;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }
    }
</style>

<div class="buku-induk-page">
    <h1 class="buku-title">BUKU INDUK SISWA</h1>

    <div class="buku-header-grid">
        <div style="width:800px">
            <div class="buku-section-title">A. &nbsp; IDENTITAS SISWA</div>
            <table class="buku-table">
                <tr>
                    <td class="label-cell">Nama Lengkap</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><?php echo $v($siswa->nama_siswa); ?></td>
                    <td class="label-cell">NIS/NIPD</td>
                    <td class="colon-cell">:</td>
                    <td class="value-cell"><?php echo $v($siswa->nipd); ?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo html_escape($jk($siswa->jenis_kelamin)); ?></td>
                    <td>NISN</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($siswa->nisn); ?></td>
                </tr>
                <tr>
                    <td>Tempat, Tgl Lahir</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v(trim(($field('tempat_lahir') ?: '') . ', ' . $date($field('tanggal_lahir')), ', ')); ?></td>
                    <td>No. Ijazah</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($field('no_ijazah')); ?></td>
                </tr>
                <tr>
                    <td>Kewarganegaraan</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($field('kewarganegaraan', 'Indonesia')); ?></td>
                    <td>NIK</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($siswa->nik); ?></td>
                </tr>
                <tr>
                    <td>Anak Ke</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($field('anak_ke')); ?></td>
                    <td>No. Kartu Keluarga</td>
                    <td class="colon-cell">:</td>
                    <td><?php echo $v($siswa->no_kk); ?></td>
                </tr>
            </table>
        </div>
        <div class="buku-photo" style="margin-left: -57px;">
            <?php if ($foto_url): ?>
                <img src="<?php echo $foto_url; ?>" alt="Foto Siswa">
            <?php else: ?>
                <div>Foto<br>3 x 4</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="buku-section-title">B. &nbsp; KETERANGAN TEMPAT TINGGAL SISWA</div>
    <table class="buku-table">
        <tr>
            <td class="label-cell">Jenis Tempat Tinggal</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($field('jenis_tempat_tinggal')); ?></td>
            <td class="label-cell">Alat Transportasi</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($field('alat_transportasi')); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td class="colon-cell">:</td>
            <td><?php echo $alamat(''); ?></td>
            <td>Jarak ke Sekolah</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($field('jarak_ke_sekolah')); ?></td>
        </tr>
        <tr>
            <td>Nomor Telepon</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->telepon); ?></td>
            <td>Koordinat</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($field('koordinat')); ?></td>
        </tr>
    </table>

    <div class="buku-section-title">C. &nbsp; KETERANGAN ORANG TUA/WALI</div>
    <table class="buku-table">
        <tr>
            <td class="label-cell">Nama Ayah</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($siswa->nama_ayah); ?></td>
            <td class="label-cell">Nama Ibu</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($siswa->nama_ibu); ?></td>
        </tr>
        <tr>
            <td>Tahun Lahir Ayah</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->tahun_lahir_ayah); ?></td>
            <td>Tahun Lahir Ibu</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->tahun_lahir_ibu); ?></td>
        </tr>
        <tr>
            <td>Pendidikan Ayah</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->pendidikan_ayah); ?></td>
            <td>Pendidikan Ibu</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->pendidikan_ibu); ?></td>
        </tr>
        <tr>
            <td>Pekerjaan Ayah</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->pekerjaan_ayah); ?></td>
            <td>Pekerjaan Ibu</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->pekerjaan_ibu); ?></td>
        </tr>
        <tr>
            <td>Alamat Ayah</td>
            <td class="colon-cell">:</td>
            <td><?php echo $alamat('ayah'); ?></td>
            <td>Alamat Ibu</td>
            <td class="colon-cell">:</td>
            <td><?php echo $alamat('ibu'); ?></td>
        </tr>
    </table>

    <div class="buku-section-title">D. &nbsp; KETERANGAN PENDIDIKAN</div>
    <table class="buku-table">
        <tr>
            <td class="label-cell">Sekolah Asal</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($field('sekolah_asal')); ?></td>
            <td class="label-cell">Diterima di Kelas</td>
            <td class="colon-cell">:</td>
            <td class="value-cell"><?php echo $v($siswa->rombel); ?></td>
        </tr>
        <tr>
            <td>Status Pendaftaran</td>
            <td class="colon-cell">:</td>
            <td><?php echo $v($siswa->status_pendaftaran); ?></td>
            <td>Tanggal Masuk</td>
            <td class="colon-cell">:</td>
            <td><?php echo $date($siswa->tanggal_pendaftaran); ?></td>
        </tr>
    </table>

    <div class="buku-section-title">E. &nbsp; NILAI SISWA</div>
    <table class="buku-table nilai-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:8%">NO</th>
                <th rowspan="2" style="width:34%">MATA PELAJARAN</th>
                <?php foreach ($yearPairs as $year): ?>
                    <th colspan="2"><?php echo html_escape($year); ?></th>
                <?php endforeach; ?>
                <th rowspan="2" style="width:10%">RATA-RATA<br>NILAI RAPOR</th>
                <th rowspan="2" style="width:9%">NILAI<br>IJAZAH</th>
            </tr>
            <tr>
                <?php foreach ($semester_columns as $i => $column): ?>
                    <th><?php echo html_escape($semesterLabel($column, $i)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr class="group-row" style="background-color: #c0c0c0;">
                <td style="text-align: left;">I.</td>
                <td class="mapel" colspan="9">Penilaian Sikap</td>
            </tr>
            <?php foreach (['Sikap Spiritual', 'Sikap Sosial', 'Sikap Kebhinekaan'] as $i => $label): ?>
                <tr>
                    <td><?php echo $i + 1; ?>.</td>
                    <td class="mapel"><?php echo $label; ?></td>
                    <?php for ($c = 0; $c < 8; $c++): ?><td></td><?php endfor; ?>
                </tr>
            <?php endforeach; ?>
            <tr class="group-row" style="background-color: #c0c0c0;">
                <td style="text-align: left;">II.</td>
                <td class="mapel" colspan="9">Penilaian Pengetahuan</td>
            </tr>
            <?php $no_nilai = 1; ?>
            <?php foreach ($nilai_rows as $row): ?>
                <?php
                $values = [];
                foreach ($semester_columns as $column) {
                    $id_tahun = (int) $column['id_tahun_pelajaran'];
                    $values[] = $id_tahun && isset($row['semester'][$id_tahun]) ? $row['semester'][$id_tahun] : null;
                }
                $filled = array_filter($values, function ($value) {
                    return $value !== null && $value !== '';
                });
                $average = !empty($filled) ? array_sum($filled) / count($filled) : null;
                ?>
                <tr>
                    <td><?php echo $no_nilai++; ?>.</td>
                    <td class="mapel"><?php echo html_escape($row['nama_mapel']); ?></td>
                    <?php foreach ($values as $value): ?>
                        <td><?php echo $formatNilai($value); ?></td>
                    <?php endforeach; ?>
                    <td><?php echo $formatNilai($average); ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($nilai_rows)): ?>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <tr>
                        <td><?php echo $i; ?>.</td>
                        <td class="mapel"></td>
                        <?php for ($c = 0; $c < 8; $c++): ?><td></td><?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            <?php endif; ?>
            <tr class="group-row" style="background-color: #c0c0c0;">
                <td style="text-align: left;">III.</td>
                <td class="mapel" colspan="7">Ekstrakurikuler</td>
                <td rowspan="8" colspan="2"></td>
            </tr>
            <?php foreach (['Pramuka', '', ''] as $i => $label): ?>
                <tr>
                    <td><?php echo $i + 1; ?>.</td>
                    <td class="mapel"><?php echo $label; ?></td>
                    <?php for ($c = 0; $c < 6; $c++): ?><td></td><?php endfor; ?>
                </tr>
            <?php endforeach; ?>
            <tr class="group-row" style="background-color: #c0c0c0;">
                <td style="text-align: left;">IV.</td>
                <td class="mapel" colspan="7">Kehadiran</td>
            </tr>
            <?php foreach (['Sakit', 'Izin', 'Tanpa Keterangan'] as $i => $label): ?>
                <tr>
                    <td><?php echo $i + 1; ?>.</td>
                    <td class="mapel"><?php echo $label; ?></td>
                    <?php for ($c = 0; $c < 6; $c++): ?><td></td><?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="buku-section-title">F. &nbsp; RIWAYAT KESEHATAN/PENYAKIT SISWA</div>
    <table class="buku-table buku-small-table">
        <tr>
            <th style="width:5%">No.</th>
            <th>Penyakit Yang Diderita</th>
        </tr>
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <tr>
                <td><?php echo $i; ?>.</td>
                <td><?php echo isset($penyakitRows[$i - 1]) ? html_escape($penyakitRows[$i - 1]) : ''; ?></td>
            </tr>
        <?php endfor; ?>
    </table>

    <div class="buku-section-title">G. &nbsp; PRESTASI SISWA</div>
    <table class="buku-table buku-small-table">
        <tr>
            <th style="width:5%">No.</th>
            <th>Prestasi Siswa</th>
            <th style="width:40%">Tingkat</th>
        </tr>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <?php
            $prestasi = isset($prestasiRows[$i - 1]) ? explode('|', $prestasiRows[$i - 1], 2) : ['', ''];
            ?>
            <tr>
                <td><?php echo $i; ?>.</td>
                <td><?php echo html_escape(trim($prestasi[0])); ?></td>
                <td><?php echo html_escape(trim(isset($prestasi[1]) ? $prestasi[1] : '')); ?></td>
            </tr>
        <?php endfor; ?>
    </table>
</div>