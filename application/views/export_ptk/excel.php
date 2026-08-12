<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('in_select_force_text')) {
    function in_select_force_text($key) {
        $force_text_fields = ['nik', 'niy', 'nuptk', 'telepon', 'rt', 'rw', 'no_sk_pengangkatan', 'nik_ayah', 'nik_ibu'];
        return in_array($key, $force_text_fields);
    }
}
?>
<table border="0" style="font-family: Arial, sans-serif;">
    <tr>
        <td colspan="<?php echo count($selected_fields) + 1; ?>" style="text-align: center; font-size: 14pt; font-weight: bold;">
            EXPORT DATA PTK MASSAL
        </td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 1; ?>" style="text-align: center; font-size: 10pt; font-style: italic;">
            Dicetak tanggal: <?php echo date('d-m-Y H:i:s'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 1; ?>"></td>
    </tr>
</table>

<table border="1" cellpadding="5" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="font-weight: bold; text-align: center; width: 50px;">No</th>
            <?php foreach ($selected_fields as $field_key): ?>
                <th style="font-weight: bold; text-align: left; min-width: 120px;">
                    <?php echo html_escape(isset($fields_map[$field_key]) ? $fields_map[$field_key] : $field_key); ?>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php if (!empty($ptk_list)): ?>
            <?php foreach ($ptk_list as $p): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <?php foreach ($selected_fields as $field_key): ?>
                        <?php 
                        $val = isset($p[$field_key]) ? $p[$field_key] : '';
                        
                        // Formatting custom fields
                        if ($field_key === 'tanggal_lahir' && !empty($val) && $val !== '0000-00-00') {
                            $t = explode('-', $val);
                            if (count($t) === 3) {
                                $val = $t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
                            }
                        }
                        if ($field_key === 'tgl_sk_pengangkatan' && !empty($val) && $val !== '0000-00-00') {
                            $t = explode('-', $val);
                            if (count($t) === 3) {
                                $val = $t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
                            }
                        }
                        
                        // Force excel cell to treat numbers (NIK, NIY, NUPTK, HP, RT, RW) as String
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
                <td colspan="<?php echo count($selected_fields) + 1; ?>" style="text-align: center; font-style: italic;">
                    Tidak ada data PTK yang diexport.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<table border="0" style="font-family: Arial, sans-serif; font-size: 8pt;">
    <tr>
        <td colspan="<?php echo count($selected_fields) + 1; ?>"></td>
    </tr>
    <tr>
        <td colspan="<?php echo count($selected_fields) + 1; ?>" style="text-align: right; font-style: italic; color: #555;">
            Dicetak massal tanggal: <?php echo date('d-m-Y H:i:s'); ?>
        </td>
    </tr>
</table>
