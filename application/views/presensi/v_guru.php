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
    min-width: 180px;
    text-align: left;
}
.table-grid tbody td.sticky-col-1,
.table-grid tbody td.sticky-col-2 { background: #fff; z-index: 5; }
.table-grid tbody td.sticky-col-2 { text-align: left; }

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
.cell-empty  { background-color: #f1f3f5; color: #adb5bd; }
.cell-hadir  { background-color: #d4edda; color: #155724; }
.cell-dhuha  { background-color: #cfe2ff; color: #084298; }
.cell-dzuhur { background-color: #fff3cd; color: #664d03; }
.cell-sakit  { background-color: #fff3cd; color: #856404; }
.cell-izin   { background-color: #d1ecf1; color: #0c5460; }
.cell-alfa   { background-color: #f8d7da; color: #721c24; }
.cell-libur  { background-color: #e9ecef; color: #6c757d; cursor: not-allowed; }
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

.legend-box { display: inline-flex; align-items: center; gap: 6px; margin-right: 12px; font-size: 12px; }
.legend-color { width: 18px; height: 18px; border-radius: 3px; display: inline-block; }
</style>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">

            <!-- Card Rekap Bulanan PTK (Grid Bulanan) -->
            <div class="card basic-data-table">
                <div class="card-header bg-info-600 p-16">
                    <form method="get" action="<?php echo url('presensi/guru') ?>" class="row gy-3 align-items-center">
                        <div class="col-md-5 col-lg-4">
                            <label class="form-label text-xs text-light mb-4 d-block">
                                Bulan (TP. <?php echo html_escape($ta_active->tahun_pelajaran ?? '') ?> - <?php echo html_escape($ta_active->semester ?? '') ?>)
                            </label>
                            <select class="form-select text-sm radius-8" name="bulan_tahun" required onchange="this.form.submit()">
                                <option value="">— Pilih Bulan —</option>
                                <?php
                                $b_names = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                                foreach ($bulan_list as $bl):
                                    $by = substr($bl->bulan_tahun, 0, 4);
                                    $bm = substr($bl->bulan_tahun, 5, 2);
                                ?>
                                    <option value="<?php echo $bl->bulan_tahun ?>"
                                        <?php echo ($selected_month ?? '') == $bl->bulan_tahun ? 'selected' : '' ?>>
                                        <?php echo ($b_names[$bm] ?? $bm) . ' ' . $by ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary-600 text-sm radius-8 px-20">
                                <iconify-icon icon="solar:filter-linear" class="me-1"></iconify-icon> Tampilkan Rekap
                            </button>
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

                    <?php if (!empty($guru_list) && !empty($tanggal_list)): ?>
                        <?php
                        $total_guru = count($guru_list);
                        $pure_holiday_map = [];
                        foreach ($tanggal_list as $t_chk) {
                            $tgl_chk = $t_chk->tanggal_absensi;
                            $is_lib = (isset($t_chk->status) && $t_chk->status == 'Libur');
                            if ($is_lib) {
                                $has_tap = false;
                                foreach ($guru_list as $g_chk) {
                                    if (isset($presensi_matrix[$g_chk->id_ptk][$tgl_chk]) ||
                                        (!empty($g_chk->niy) && isset($presensi_matrix_by_pin[(string)$g_chk->niy][$tgl_chk])) ||
                                        (!empty($g_chk->pin_fingerprint) && isset($presensi_matrix_by_pin[(string)$g_chk->pin_fingerprint][$tgl_chk]))) {
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
                                        <th class="sticky-col-2">Nama PTK (Guru & Staf)</th>
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
                                    <?php $no_g = 0; ?>
                                    <?php foreach ($guru_list as $g): ?>
                                        <?php
                                        $h_c = 0; $s_c = 0; $i_c = 0; $a_c = 0;
                                        $row_idx = $no_g;
                                        $no_g++;
                                        ?>
                                        <tr>
                                            <td class="sticky-col-1 text-xs"><?php echo $no_g ?></td>
                                            <td class="sticky-col-2 text-xs fw-semibold"><?php echo html_escape($g->nama_ptk) ?></td>

                                            <?php foreach ($tanggal_list as $t):
                                                $tgl = $t->tanggal_absensi;
                                                $is_pure_holiday = isset($pure_holiday_map[$tgl]);

                                                if ($is_pure_holiday) {
                                                    if ($row_idx === 0) {
                                                        $ket_libur = !empty($t->keterangan) ? $t->keterangan : 'Libur Akhir Pekan';
                                                        echo '<td rowspan="' . $total_guru . '" class="cell-libur-col"><div class="libur-vertical-text">' . html_escape($ket_libur) . '</div></td>';
                                                    }
                                                    continue;
                                                }

                                                $po  = isset($presensi_matrix[$g->id_ptk][$tgl]) 
                                                    ? $presensi_matrix[$g->id_ptk][$tgl] 
                                                    : (isset($presensi_matrix_by_pin[(string)$g->niy][$tgl]) 
                                                        ? $presensi_matrix_by_pin[(string)$g->niy][$tgl] 
                                                        : (isset($presensi_matrix_by_pin[(string)$g->pin_fingerprint][$tgl]) 
                                                            ? $presensi_matrix_by_pin[(string)$g->pin_fingerprint][$tgl] 
                                                            : null));

                                                $cell_class = 'cell-empty';
                                                $cell_text  = '–';
                                                $cell_tip   = date('d-m-Y', strtotime($tgl));
                                                $is_libur   = (isset($t->status) && $t->status == 'Libur');

                                                if ($is_libur) {
                                                    $cell_class = 'cell-libur';
                                                    $cell_text  = 'L';
                                                } elseif ($po) {
                                                    if ($po->keterangan === 'Hanya Dhuha') {
                                                        $cell_class = 'cell-dhuha'; $cell_text = 'D'; $h_c++;
                                                    } elseif ($po->keterangan === 'Hanya Dzuhur') {
                                                        $cell_class = 'cell-dzuhur'; $cell_text = 'Z'; $h_c++;
                                                    } elseif ($po->status === 'Hadir') {
                                                        $cell_class = 'cell-hadir'; $cell_text = 'H'; $h_c++;
                                                    } elseif ($po->status === 'Sakit') {
                                                        $cell_class = 'cell-sakit'; $cell_text = 'S'; $s_c++;
                                                    } elseif ($po->status === 'Izin') {
                                                        $cell_class = 'cell-izin'; $cell_text = 'I'; $i_c++;
                                                    } elseif ($po->status === 'Alfa') {
                                                        $cell_class = 'cell-alfa'; $cell_text = 'A'; $a_c++;
                                                    }
                                                    if ($po->keterangan) $cell_tip .= ' – ' . $po->keterangan;
                                                    if ($po->jam_dhuha)  $cell_tip .= ' | Dhuha: ' . $po->jam_dhuha;
                                                    if ($po->jam_dzuhur) $cell_tip .= ' | Dzuhur: ' . $po->jam_dzuhur;
                                                }
                                            ?>
                                                <td>
                                                    <?php if ($is_libur): ?>
                                                        <span class="cell-btn cell-libur"
                                                            title="Libur<?php echo $t->keterangan ? ': '.$t->keterangan : '' ?>">L</span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                            class="cell-btn <?php echo $cell_class ?>"
                                                            title="<?php echo htmlspecialchars($cell_tip) ?>"
                                                            onclick="openEditModal(
                                                                'ptk',
                                                                '<?php echo $g->id_ptk ?>',
                                                                '<?php echo htmlspecialchars($g->nama_ptk, ENT_QUOTES) ?>',
                                                                '<?php echo $tgl ?>',
                                                                '<?php echo $po ? $po->status : '' ?>',
                                                                '<?php echo $po ? htmlspecialchars($po->keterangan ?? '', ENT_QUOTES) : '' ?>'
                                                            )">
                                                            <?php echo $cell_text ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>

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
                            <iconify-icon icon="icon-park-outline:user-business" style="font-size:40px" class="mb-12 d-block"></iconify-icon>
                            <p class="text-sm">Pilih Bulan Presensi untuk menampilkan rekap kehadiran PTK (Guru & Staf).</p>
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
                <h6 class="modal-title text-light" id="modalEditPresensiLabel">Perbarui Presensi Guru Manual</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tipe_user"   id="modal_tipe_user">
                <input type="hidden" name="id_user"     id="modal_id_user">
                <input type="hidden" name="tanggal"     id="modal_tanggal">
                <input type="hidden" name="bulan_tahun" value="<?php echo html_escape($selected_month ?? '') ?>">

                <div class="mb-16">
                    <label class="form-label text-sm fw-bold">Nama Guru / Staf</label>
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
$(document).ready(function () {
    if ($('#presensiGuruTable').length) {
        new DataTable('#presensiGuruTable', {
            language: {
                emptyTable: "Belum ada data kehadiran guru pada tanggal terpilih."
            }
        });
    }
});

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
