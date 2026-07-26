<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<style type="text/css">
.table-grid {
    overflow-x: auto;
    max-height: 65vh;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    background: #fff;
}
.table-grid table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}
.table-grid th, .table-grid td {
    border-right: 1px solid #e3e6f0;
    border-bottom: 1px solid #e3e6f0;
    padding: 5px 7px;
    white-space: nowrap;
    text-align: center;
}
.table-grid thead th {
    position: sticky;
    top: 0;
    background: #f8f9fc;
    z-index: 10;
    font-weight: 600;
    font-size: 11px;
}
.table-grid .sticky-col-1 {
    position: sticky;
    left: 0;
    background: #f8f9fc;
    z-index: 11;
    min-width: 36px;
    width: 36px;
}
.table-grid .sticky-col-2 {
    position: sticky;
    left: 36px;
    background: #f8f9fc;
    z-index: 11;
    min-width: 160px;
    text-align: left;
}
.table-grid tbody td.sticky-col-1,
.table-grid tbody td.sticky-col-2 {
    background: #fff;
    z-index: 5;
}
.table-grid tbody td.sticky-col-2 { text-align: left; }

/* Tombol sel grid */
.cell-btn {
    border: none;
    background: transparent;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
    min-width: 26px;
    line-height: 1.6;
}
.cell-empty   { background-color: #f1f3f5; color: #adb5bd; }
.cell-hadir   { background-color: #d4edda; color: #155724; }   /* H - Hadir Lengkap */
.cell-dhuha   { background-color: #cfe2ff; color: #084298; }   /* D - Hanya Dhuha */
.cell-dzuhur  { background-color: #fff3cd; color: #664d03; }   /* Z - Hanya Dzuhur */
.cell-sakit   { background-color: #fff3cd; color: #856404; }
.cell-izin    { background-color: #d1ecf1; color: #0c5460; }
.cell-alfa    { background-color: #f8d7da; color: #721c24; }
.cell-libur   { background-color: #e9ecef; color: #6c757d; cursor: not-allowed; }
.table-grid .cell-override { background-color: #e2d9f3; color: #432874; }
.table-grid td.cell-libur-col {
    background-color: #f8fafc;
    color: #64748b;
    vertical-align: middle;
    text-align: center;
    padding: 12px 2px;
    font-size: 11px;
    font-weight: 600;
    min-width: 32px;
}
.libur-vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    text-align: center;
    white-space: nowrap;
    margin: 0 auto;
    letter-spacing: 0.5px;
}

/* Legenda */
.legend-box { display: inline-flex; align-items: center; gap: 6px; margin-right: 12px; font-size: 12px; }
.legend-color { width: 18px; height: 18px; border-radius: 3px; display: inline-block; }
</style>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">

            <!-- Card Rekap Bulanan Siswa (Grid Rombel) -->
            <div class="card basic-data-table">
                <div class="card-header bg-info-600 p-16">
                    <form method="get" action="<?php echo url('presensi/siswa') ?>" class="row gy-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label text-xs text-light mb-4 d-block">Rombongan Belajar (Rombel)</label>
                            <select class="form-select text-sm radius-8" name="rombel" required onchange="this.form.submit()">
                                <option value="">— Pilih Rombel —</option>
                                <?php foreach ($rombel_list as $rl): ?>
                                    <option value="<?php echo html_escape($rl->rombel) ?>"
                                        <?php echo ($selected_rombel ?? '') == $rl->rombel ? 'selected' : '' ?>>
                                        <?php echo html_escape($rl->rombel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs text-light mb-4 d-block">
                                Bulan (TP. <?php echo html_escape($ta_active->tahun_pelajaran ?? '') ?> - <?php echo html_escape($ta_active->semester ?? '') ?>)
                            </label>
                            <select class="form-select text-sm radius-8" name="bulan_tahun" required onchange="this.form.submit()">
                                <option value="">— Pilih Bulan —</option>
                                <?php foreach ($bulan_list as $bl): ?>
                                    <option value="<?php echo $bl->bulan_tahun ?>"
                                        <?php echo ($selected_month ?? '') == $bl->bulan_tahun ? 'selected' : '' ?>>
                                        <?php echo html_escape($bl->nama_bulan ?? $bl->bulan_tahun) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-600 text-sm radius-8 px-20">
                                <iconify-icon icon="solar:filter-linear" class="me-1"></iconify-icon> Tampilkan Rekap
                            </button>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-check form-switch d-inline-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" name="show_menginduk" value="1" id="showMengindukSiswa"
                                    <?php echo (!empty($show_menginduk)) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label text-xs text-light fw-medium" for="showMengindukSiswa">
                                    Tampilkan Siswa Menginduk (Kelas Jauh)
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <!-- Legenda -->
                    <div class="d-flex flex-wrap gap-2 mb-16 text-sm">
                        <span class="legend-box"><span class="legend-color cell-hadir"></span> H = Hadir Lengkap</span>
                        <span class="legend-box"><span class="legend-color cell-dhuha"></span> D = Hanya Dhuha</span>
                        <span class="legend-box"><span class="legend-color cell-dzuhur"></span> Z = Hanya Dzuhur</span>
                        <span class="legend-box"><span class="legend-color cell-sakit"></span> S = Sakit</span>
                        <span class="legend-box"><span class="legend-color cell-izin"></span> I = Izin</span>
                        <span class="legend-box"><span class="legend-color cell-alfa"></span> A = Alfa</span>
                        <span class="legend-box"><span class="legend-color cell-libur"></span> L = Libur</span>
                    </div>

                    <?php if (!empty($siswa_list) && !empty($tanggal_list)): ?>
                        <?php
                        $total_siswa = count($siswa_list);
                        $pure_holiday_map = [];
                        foreach ($tanggal_list as $t_chk) {
                            $tgl_chk = $t_chk->tanggal_absensi;
                            $is_lib = (isset($t_chk->status) && $t_chk->status == 'Libur');
                            if ($is_lib) {
                                $has_tap = false;
                                foreach ($siswa_list as $s_chk) {
                                    if (isset($presensi_matrix[$s_chk->id_siswa][$tgl_chk]) ||
                                        (!empty($s_chk->nipd) && isset($presensi_matrix_by_pin[(string)$s_chk->nipd][$tgl_chk])) ||
                                        (!empty($s_chk->pin_fingerprint) && isset($presensi_matrix_by_pin[(string)$s_chk->pin_fingerprint][$tgl_chk]))) {
                                        $has_tap = true;
                                        break;
                                    }
                                }
                                if (!$has_tap) {
                                    $pure_holiday_map[$tgl_chk] = true;
                                }
                            }
                        }
                        ?>
                        <div class="table-grid">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sticky-col-1">No</th>
                                        <th class="sticky-col-2">Nama Siswa</th>
                                        <?php foreach ($tanggal_list as $t): ?>
                                            <th title="<?php echo date('d-m-Y', strtotime($t->tanggal_absensi)) . ($t->keterangan ? ' – '.$t->keterangan : '') ?>">
                                                <?php echo date('d', strtotime($t->tanggal_absensi)) ?>
                                            </th>
                                        <?php endforeach; ?>
                                        <th title="Hadir">H</th>
                                        <th title="Sakit">S</th>
                                        <th title="Izin">I</th>
                                        <th title="Alfa">A</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no_s = 0; ?>
                                    <?php foreach ($siswa_list as $s): ?>
                                        <?php
                                        $h_c = 0; $s_c = 0; $i_c = 0; $a_c = 0;
                                        $row_idx = $no_s;
                                        $no_s++;
                                        ?>
                                        <tr>
                                            <td class="sticky-col-1 text-xs"><?php echo $no_s ?></td>
                                            <td class="sticky-col-2 text-xs fw-semibold"><?php echo html_escape($s->nama_siswa) ?></td>

                                            <?php foreach ($tanggal_list as $t):
                                                $tgl = $t->tanggal_absensi;
                                                $is_pure_holiday = isset($pure_holiday_map[$tgl]);

                                                if ($is_pure_holiday) {
                                                    if ($row_idx === 0) {
                                                        $ket_libur = !empty($t->keterangan) ? $t->keterangan : 'Libur Akhir Pekan';
                                                        echo '<td rowspan="' . $total_siswa . '" class="cell-libur-col"><div class="libur-vertical-text">' . html_escape($ket_libur) . '</div></td>';
                                                    }
                                                    continue;
                                                }

                                                $po = isset($presensi_matrix[$s->id_siswa][$tgl]) ? $presensi_matrix[$s->id_siswa][$tgl] : null;
                                                if (!$po && !empty($s->nipd) && isset($presensi_matrix_by_pin[(string)$s->nipd][$tgl])) {
                                                    $po = $presensi_matrix_by_pin[(string)$s->nipd][$tgl];
                                                }
                                                if (!$po && !empty($s->pin_fingerprint) && isset($presensi_matrix_by_pin[(string)$s->pin_fingerprint][$tgl])) {
                                                    $po = $presensi_matrix_by_pin[(string)$s->pin_fingerprint][$tgl];
                                                }

                                                // Tentukan tampilan sel
                                                $cell_class = 'cell-empty';
                                                $cell_text  = '–';
                                                $cell_tip   = date('d-m-Y', strtotime($tgl));
                                                $is_libur   = (isset($t->status) && $t->status == 'Libur');

                                                if ($po) {
                                                    if ($po->keterangan === 'Hanya Dhuha') {
                                                        $cell_class = 'cell-dhuha';
                                                        $cell_text  = 'D';
                                                        $h_c++;
                                                    } elseif ($po->keterangan === 'Hanya Dzuhur') {
                                                        $cell_class = 'cell-dzuhur';
                                                        $cell_text  = 'Z';
                                                        $h_c++;
                                                    } elseif ($po->status === 'Hadir') {
                                                        $cell_class = 'cell-hadir';
                                                        $cell_text  = 'H';
                                                        $h_c++;
                                                    } elseif ($po->status === 'Sakit') {
                                                        $cell_class = 'cell-sakit';
                                                        $cell_text  = 'S';
                                                        $s_c++;
                                                    } elseif ($po->status === 'Izin') {
                                                        $cell_class = 'cell-izin';
                                                        $cell_text  = 'I';
                                                        $i_c++;
                                                    } elseif ($po->status === 'Alfa') {
                                                        $cell_class = 'cell-alfa';
                                                        $cell_text  = 'A';
                                                        $a_c++;
                                                    } else {
                                                        $cell_class = 'cell-hadir';
                                                        $cell_text  = 'H';
                                                        $h_c++;
                                                    }
                                                    if ($po->keterangan) {
                                                        $cell_tip .= ' – ' . $po->keterangan;
                                                    }
                                                    if ($po->jam_dhuha)  $cell_tip .= ' | Dhuha: ' . $po->jam_dhuha;
                                                    if ($po->jam_dzuhur) $cell_tip .= ' | Dzuhur: ' . $po->jam_dzuhur;
                                                } elseif ($is_libur) {
                                                    $cell_class = 'cell-libur';
                                                    $cell_text  = 'L';
                                                }
                                            ?>
                                                <td>
                                                    <?php if ($is_libur && !$po): ?>
                                                        <span class="cell-btn cell-libur"
                                                            title="Libur<?php echo !empty($t->keterangan) ? ': '.$t->keterangan : '' ?>">L</span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                            class="cell-btn <?php echo $cell_class ?>"
                                                            title="<?php echo htmlspecialchars($cell_tip) ?>"
                                                            onclick="openEditModal(
                                                                'siswa',
                                                                '<?php echo $s->id_siswa ?>',
                                                                '<?php echo htmlspecialchars($s->nama_siswa, ENT_QUOTES) ?>',
                                                                '<?php echo $tgl ?>',
                                                                '<?php echo $po ? $po->status : '' ?>',
                                                                '<?php echo $po ? htmlspecialchars($po->keterangan ?? '', ENT_QUOTES) : '' ?>'
                                                            )">
                                                            <?php echo $cell_text ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <!-- Rekap -->
                                            <td class="text-xs fw-bold text-success-main"><?php echo $h_c ?></td>
                                            <td class="text-xs fw-bold text-warning-main"><?php echo $s_c ?></td>
                                            <td class="text-xs fw-bold text-info-main"><?php echo $i_c ?></td>
                                            <td class="text-xs fw-bold text-danger-main"><?php echo $a_c ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-40 text-secondary-light">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" style="font-size:40px" class="mb-12 d-block"></iconify-icon>
                            <p class="text-sm">Pilih Rombel dan Bulan untuk menampilkan rekap presensi.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Edit Presensi Manual -->
<div class="modal fade" id="modalEditPresensi" tabindex="-1" aria-labelledby="modalEditPresensiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?php echo url('presensi/simpan_manual') ?>" class="modal-content">
            <div class="modal-header bg-primary-600">
                <h6 class="modal-title text-light" id="modalEditPresensiLabel">Perbarui Presensi Manual</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tipe_user"   id="modal_tipe_user">
                <input type="hidden" name="id_user"     id="modal_id_user">
                <input type="hidden" name="tanggal"     id="modal_tanggal">
                <input type="hidden" name="rombel"      value="<?php echo html_escape($selected_rombel ?? '') ?>">
                <input type="hidden" name="bulan_tahun" value="<?php echo html_escape($selected_month  ?? '') ?>">

                <div class="mb-16">
                    <label class="form-label text-sm fw-bold">Nama Siswa</label>
                    <input type="text" class="form-control text-sm" id="modal_nama" readonly>
                </div>
                <div class="mb-16">
                    <label class="form-label text-sm fw-bold">Tanggal</label>
                    <input type="text" class="form-control text-sm" id="modal_tanggal_fmt" readonly>
                </div>
                <div class="mb-16">
                    <label class="form-label text-sm fw-bold">Status Kehadiran</label>
                    <select class="form-select text-sm" name="status" id="modal_status" required>
                        <option value="Hadir">Hadir (Override Manual)</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Alfa">Alfa (Tanpa Keterangan)</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label text-sm fw-bold">Keterangan Tambahan</label>
                    <textarea class="form-control text-sm" name="keterangan" id="modal_keterangan"
                              rows="3" placeholder="Sakit flu, izin keluarga, dll."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
function openEditModal(tipeUser, idUser, nama, tanggal, currentStatus, currentKet) {
    $('#modal_tipe_user').val(tipeUser);
    $('#modal_id_user').val(idUser);
    $('#modal_tanggal').val(tanggal);
    $('#modal_nama').val(nama);
    let d = new Date(tanggal);
    $('#modal_tanggal_fmt').val(
        d.getDate().toString().padStart(2,'0') + '-' +
        (d.getMonth()+1).toString().padStart(2,'0') + '-' +
        d.getFullYear()
    );
    $('#modal_status').val(currentStatus || 'Hadir');
    $('#modal_keterangan').val(currentKet || '');
    new bootstrap.Modal(document.getElementById('modalEditPresensi')).show();
}
</script>
