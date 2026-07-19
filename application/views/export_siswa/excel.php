<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('in_select_force_text')) {
    function in_select_force_text($key) {
        $force_text_fields = ['nisn', 'nipd', 'nik', 'no_kk', 'telepon', 'rt', 'rw', 'no_ijazah', 'nik_ayah', 'nik_ibu', 'tahun_lahir_ayah', 'tahun_lahir_ibu'];
        return in_array($key, $force_text_fields);
    }
}
?>
<table border="0" style="font-family: Arial, sans-serif;">
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>" style="text-align: center; font-size: 14pt; font-weight: bold;">
            EXPORT DATA GABUNGAN SISWA MASSAL
        </td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>" style="text-align: center; font-size: 11pt; font-weight: bold;">
            Tahun Pelajaran: <?php echo html_escape($tahun_pelajaran . ' ' . $semester); ?>
        </td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>" style="text-align: center; font-size: 10pt; font-style: italic;">
            Daftar Kelas Terlampir di Bawah
        </td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>"></td>
    </tr>
</table>

<table border="1" cellpadding="5" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="font-weight: bold; text-align: center; width: 50px;">No</th>
            <th style="font-weight: bold; text-align: left; min-width: 150px; background-color: #e2e8f0;">Lembaga</th>
            <th style="font-weight: bold; text-align: left; min-width: 100px; background-color: #e2e8f0;">Rombel</th>
            <?php foreach ($selected_fields as $field_key): ?>
                <th style="font-weight: bold; text-align: left; min-width: 120px;">
                    <?php echo html_escape(isset($fields_map[$field_key]) ? $fields_map[$field_key] : $field_key); ?>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td style="background-color: #f8fafc;"><?php echo html_escape(!empty($s['nama_lembaga_singkat']) ? $s['nama_lembaga_singkat'] : (!empty($s['bentuk_pendidikan']) ? $s['bentuk_pendidikan'] : $s['nama_lembaga'])); ?></td>
                    <td style="background-color: #f8fafc;"><?php echo html_escape($s['nama_rombel']); ?></td>
                    <?php foreach ($selected_fields as $field_key): ?>
                        <?php 
                        $val = isset($s[$field_key]) ? $s[$field_key] : '';
                        
                        // Formatting custom fields
                        if ($field_key === 'tanggal_lahir' && !empty($val) && $val !== '0000-00-00') {
                            $t = explode('-', $val);
                            if (count($t) === 3) {
                                $val = $t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
                            }
                        }
                        
                        // Force excel cell to treat numbers (NISN, NIPD, NIK, No KK, Telepon, RT, RW) as String
                        $is_numeric_string = in_select_force_text($field_key);
                        $mso_format = $is_numeric_string ? "style=\"mso-number-format:'\@';\"" : '';
                        ?>
                        <td <?php echo $mso_format; ?>>
                            <?php echo html_escape($val !== null ? $val : '-'); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo count($selected_fields) + 3; ?>" style="text-align: center; font-style: italic;">
                    Tidak ada data siswa yang diexport.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<table border="0" style="font-family: Arial, sans-serif; font-size: 8pt;">
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>"></td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 3; ?>" style="text-align: right; font-style: italic; color: #555;">
            Dicetak massal tanggal: <?php echo date('d-m-Y H:i:s'); ?>
        </td>
    </tr>
</table>
