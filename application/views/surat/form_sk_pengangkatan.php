<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/sk_pengangkatan_simpan') ?>" method="post" id="formSkPengangkatan">
        <input type="hidden" name="id_surat_keluar" value="<?php echo !empty($surat_edit) ? $surat_edit->id_surat_keluar : '' ?>">
        <div class="row gy-4">

            <!-- LEFT COLUMN: INPUT FORM -->
            <div class="col-lg-7">
                <div class="card radius-12 border">
                    <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-light d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:document-bold-duotone" class="text-xl"></iconify-icon> 
                            <?php echo !empty($surat_edit) ? 'Edit SK Pengangkatan Pegawai/Guru Yayasan' : 'Form SK Pengangkatan Pegawai/Guru Yayasan' ?>
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 radius-8" data-bs-toggle="modal" data-bs-target="#modalSavePreset">
                                <iconify-icon icon="solar:bookmark-square-linear"></iconify-icon> Simpan Preset
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-24">

                        <!-- PRESET LOADER -->
                        <?php 
                        $active_preset_id = !empty($payload_edit['id_preset']) ? $payload_edit['id_preset'] : '';
                        ?>
                        <input type="hidden" name="id_preset_loaded" id="id_preset_loaded" value="<?php echo htmlspecialchars($active_preset_id) ?>">
                        <?php if (!empty($preset_list)): ?>
                            <div class="alert alert-neutral bg-neutral-50 border border-neutral-200 radius-8 p-12 mb-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="text-primary-600 text-xl"></iconify-icon>
                                    <span class="text-sm fw-medium text-neutral-800">Muat Preset Template SK:</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <select id="presetSelector" class="form-select form-select-sm radius-8" style="min-width: 220px;">
                                        <option value="">-- Pilih Preset Template --</option>
                                        <?php foreach ($preset_list as $ps): ?>
                                            <option value="<?php echo $ps->id_preset ?>" data-nama="<?php echo htmlspecialchars($ps->nama_preset) ?>" <?php echo ($active_preset_id == $ps->id_preset) ? 'selected' : '' ?>><?php echo htmlspecialchars($ps->nama_preset) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" id="btnDeletePresetTop" class="btn btn-sm btn-outline-danger radius-8 <?php echo !empty($active_preset_id) ? '' : 'd-none' ?> d-flex align-items-center gap-1" title="Hapus Preset Terpilih">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 1. KOP & NOMOR SURAT -->
                        <div class="border radius-8 p-16 mb-20 bg-light-50">
                            <h6 class="fw-bold text-sm text-neutral-800 border-bottom pb-8 mb-16">1. Kop Surat & Nomor Keputusan</h6>
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Pilih Kop Surat</label>
                                    <select name="id_kop_surat" id="id_kop_surat" class="form-select radius-8" required>
                                        <?php foreach ($kop_list as $kp): 
                                            $kp_json = htmlspecialchars(json_encode($kp), ENT_QUOTES, 'UTF-8');
                                        ?>
                                            <option value="<?php echo $kp->id_kop_surat ?>" data-kop='<?php echo $kp_json ?>' <?php echo ($selected_kop && $selected_kop->id_kop_surat == $kp->id_kop_surat) ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($kp->nama_kop) ?> (<?php echo htmlspecialchars($kp->nama_lembaga) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Kode & Format Nomor</label>
                                    <select name="id_kode_surat" id="id_kode_surat" class="form-select radius-8" required>
                                        <?php foreach ($kode_list as $kd): ?>
                                            <option value="<?php echo $kd->id_kode_surat ?>" 
                                                    data-format="<?php echo htmlspecialchars($kd->format_nomor) ?>"
                                                    data-kodejenis="<?php echo htmlspecialchars($kd->kode_jenis) ?>"
                                                    data-kodelembaga="<?php echo htmlspecialchars($kd->kode_lembaga) ?>"
                                                    data-lokasi="<?php echo htmlspecialchars($kd->lokasi) ?>"
                                                    <?php echo ($selected_kode && $selected_kode->id_kode_surat == $kd->id_kode_surat) ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($kd->nama_lembaga) ?> - <?php echo htmlspecialchars($kd->kode_jenis) ?> (<?php echo htmlspecialchars($kd->nama_jenis) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Nomor Urut</label>
                                    <input type="number" name="nomor_urut" id="nomor_urut" class="form-control radius-8" value="<?php echo $nomor_urut ?>" required min="1">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Nomor Surat Otomatis</label>
                                    <input type="text" name="nomor_surat" id="nomor_surat" class="form-control radius-8 bg-light fw-bold text-primary-600" value="<?php echo !empty($surat_edit) ? htmlspecialchars($surat_edit->nomor_surat) : '01.' . sprintf('%03d', $nomor_urut) . '/YMK/VI/' . date('Y') ?>" required readonly>
                                </div>
                            </div>
                        </div>

                        <!-- 2. TENTANG & KONSIDERAN (MENIMBANG, MENGINGAT, MEMPERHATIKAN) -->
                        <div class="border radius-8 p-16 mb-20 bg-light-50">
                            <h6 class="fw-bold text-sm text-neutral-800 border-bottom pb-8 mb-16">2. Judul & Konsideran SK</h6>
                            
                            <div class="mb-16">
                                <label class="form-label fw-semibold text-xs text-neutral-700">Tentang SK <span class="text-danger">*</span></label>
                                <input type="text" name="tentang" id="input_tentang" class="form-control radius-8 fw-semibold" value="<?php echo !empty($payload_edit['tentang']) ? htmlspecialchars($payload_edit['tentang']) : 'PENGANGKATAN PEGAWAI / GURU TETAP YAYASAN' ?>" required>
                                <small class="text-secondary-light">Sub-judul SK akan tercetak setelah kalimat "TENTANG".</small>
                            </div>

                             <!-- MENIMBANG (DAPAT DITAMBAH SECAKA MANUAL) -->
                             <div class="mb-16">
                                 <div class="d-flex justify-content-between align-items-center mb-8">
                                     <label class="form-label fw-semibold text-xs text-neutral-700 mb-0">MENIMBANG <span class="text-danger">*</span></label>
                                     <button type="button" id="btnAddMenimbang" class="btn btn-xs btn-outline-primary radius-6 d-flex align-items-center gap-1">
                                         <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Poin Menimbang
                                     </button>
                                 </div>
                                 <div id="menimbangContainer" class="d-flex flex-column gap-2">
                                     <?php
                                     $edit_menimbang = isset($payload_edit['menimbang']) ? $payload_edit['menimbang'] : [];
                                     if (is_string($edit_menimbang)) {
                                         $decoded_m = json_decode($edit_menimbang, true);
                                         $edit_menimbang = (json_last_error() === JSON_ERROR_NONE && is_array($decoded_m)) ? $decoded_m : [$edit_menimbang];
                                     }
                                     if (empty($edit_menimbang)) {
                                         $edit_menimbang = ['Bahwa dalam rangka memperlancar kegiatan pembelajaran dan administrasi operasional pendidikan pada {nama_lembaga}, perlu mengangkat dan menugaskan Pegawai / Guru Tetap Yayasan;'];
                                     }
                                     foreach ($edit_menimbang as $idx => $m_item):
                                         $letter = chr(97 + ($idx % 26)) . '.';
                                     ?>
                                         <div class="menimbang-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                                             <span class="badge bg-primary-50 text-primary-700 font-mono item-num-m mt-1"><?php echo $letter ?></span>
                                             <textarea name="menimbang[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-menimbang-text" rows="2" style="resize: vertical; focus: outline-none;"><?php echo htmlspecialchars($m_item) ?></textarea>
                                             <div class="d-flex flex-column gap-1">
                                                 <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up-m" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                                                 <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down-m" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                                                 <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item-m" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                                             </div>
                                         </div>
                                     <?php endforeach; ?>
                                 </div>
                             </div>

                             <!-- MENGINGAT (DASAR HUKUM CHECKLIST & REORDER) -->
                             <div class="mb-16">
                                 <div class="d-flex justify-content-between align-items-center mb-8 flex-wrap gap-2">
                                     <label class="form-label fw-semibold text-xs text-neutral-700 mb-0">MENGINGAT (Dasar Hukum SK) <span class="text-danger">*</span></label>
                                     <div class="d-flex gap-2">
                                         <button type="button" id="btnAddMengingatManual" class="btn btn-xs btn-outline-primary radius-6 d-flex align-items-center gap-1">
                                             <iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Manual
                                         </button>
                                         <button type="button" class="btn btn-xs btn-primary-600 radius-6 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalDasarHukumChecklist">
                                             <iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon> Pilih dari Master DB
                                         </button>
                                     </div>
                                 </div>
                                 <div id="mengingatContainer" class="d-flex flex-column gap-2">
                                     <?php 
                                     $edit_mengingat = isset($payload_edit['mengingat']) && is_array($payload_edit['mengingat']) ? $payload_edit['mengingat'] : [];
                                     if (empty($edit_mengingat)) {
                                         $edit_mengingat = array_map(function($dh){ return $dh->judul; }, array_slice($dasar_hukum_list, 0, 4));
                                     }
                                     foreach ($edit_mengingat as $idx => $mg_item): 
                                     ?>
                                         <div class="mengingat-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                                             <span class="badge bg-neutral-100 text-neutral-700 font-mono item-num mt-1"><?php echo ($idx + 1) ?></span>
                                             <textarea name="mengingat[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-mengingat-text" rows="2" style="resize: vertical; focus: outline-none;"><?php echo htmlspecialchars($mg_item) ?></textarea>
                                             <div class="d-flex flex-column gap-1">
                                                 <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                                                 <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                                                 <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                                             </div>
                                         </div>
                                     <?php endforeach; ?>
                                 </div>
                             </div>

                             <div class="mb-16">
                                 <label class="form-label fw-semibold text-xs text-neutral-700">MEMPERHATIKAN <span class="text-danger">*</span></label>
                                 <textarea name="memperhatikan" id="input_memperhatikan" class="form-control radius-8" rows="2" required><?php echo !empty($payload_edit['memperhatikan']) ? htmlspecialchars($payload_edit['memperhatikan']) : 'Hasil Rapat Pengurus Yayasan Miftahul Khoer El-Istohary tentang penetapan dan pengangkatan Pegawai / Guru Tetap Yayasan.' ?></textarea>
                             </div>
                         </div>

                        <!-- 3. MENETAPKAN (POIN-POIN KEPUTUSAN) -->
                        <div class="border radius-8 p-16 mb-20 bg-light-50">
                            <h6 class="fw-bold text-sm text-neutral-800 border-bottom pb-8 mb-16">3. Amar Keputusan (MENETAPKAN)</h6>

                            <!-- POIN PERTAMA -->
                            <div class="card border mb-16 radius-8">
                                <div class="card-header bg-neutral-100 py-10 px-16 fw-semibold text-xs text-neutral-800">
                                    MEMUTUSKAN - Pertama : Pengangkatan Pegawai / Guru
                                </div>
                                <div class="card-body p-16">
                                    <div class="mb-12">
                                        <label class="form-label fw-semibold text-xs text-neutral-700">Lembaga Penugasan <span class="text-danger">*</span></label>
                                        <select name="id_lembaga_tujuan" id="id_lembaga_tujuan" class="form-select radius-8" required>
                                            <?php 
                                            $target_l_id = isset($payload_edit['id_lembaga_target']) ? $payload_edit['id_lembaga_target'] : 0;
                                            foreach ($lembaga_list as $l): 
                                                $sel_l = ($target_l_id > 0) ? ($l->id_lembaga == $target_l_id) : (stripos($l->nama_lembaga, 'SMP') !== false);
                                            ?>
                                                <option value="<?php echo $l->id_lembaga ?>" <?php echo $sel_l ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars($l->nama_lembaga) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-12">
                                        <label class="form-label fw-semibold text-xs text-neutral-700">Pilih Pegawai / Guru (PTK) <span class="text-danger">*</span></label>
                                        <select name="id_ptk" id="id_ptk" class="form-select radius-8" required>
                                            <option value="">-- Pilih Guru / Pegawai --</option>
                                            <?php 
                                            $target_ptk_id = isset($payload_edit['id_ptk_target']) ? $payload_edit['id_ptk_target'] : 0;
                                            $bulan_indo = [
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                            foreach ($ptk_list as $ptk): 
                                                $ptk_tempat = !empty($ptk->tempat_lahir) ? ucwords(strtolower(trim($ptk->tempat_lahir))) : '-';
                                                $ptk_tgl = '-';
                                                if (!empty($ptk->tanggal_lahir) && $ptk->tanggal_lahir !== '0000-00-00') {
                                                    $time = strtotime($ptk->tanggal_lahir);
                                                    if ($time) {
                                                        $d = date('j', $time);
                                                        $m = (int)date('n', $time);
                                                        $y = date('Y', $time);
                                                        $ptk_tgl = $d . ' ' . (isset($bulan_indo[$m]) ? $bulan_indo[$m] : date('F', $time)) . ' ' . $y;
                                                    }
                                                }

                                                // Format Alamat Lengkap dengan wrapper non-breaking (white-space: nowrap)
                                                $addr_parts = [];
                                                $raw_alamat = !empty($ptk->alamat) ? trim($ptk->alamat) : (!empty($ptk->alamat_jalan) ? trim($ptk->alamat_jalan) : '');
                                                if ($raw_alamat) {
                                                    $addr_parts[] = '<span style="white-space: nowrap;">' . htmlspecialchars(ucwords(strtolower($raw_alamat))) . '</span>';
                                                }
                                                $rt = !empty($ptk->rt) ? trim($ptk->rt) : '';
                                                $rw = !empty($ptk->rw) ? trim($ptk->rw) : '';
                                                if ($rt || $rw) {
                                                    $rtrw = '';
                                                    if ($rt) $rtrw .= 'RT&nbsp;' . sprintf('%02d', (int)$rt);
                                                    if ($rw) $rtrw .= ($rtrw ? '&nbsp;' : '') . 'RW&nbsp;' . sprintf('%02d', (int)$rw);
                                                    $addr_parts[] = '<span style="white-space: nowrap;">' . $rtrw . '</span>';
                                                }
                                                $desa = !empty($ptk->kelurahan_desa) ? trim($ptk->kelurahan_desa) : (!empty($ptk->desa) ? trim($ptk->desa) : '');
                                                if ($desa) {
                                                    $clean_desa = preg_replace('/^(DESA|KEL\.?|KELURAHAN)\s+/i', '', $desa);
                                                    $addr_parts[] = '<span style="white-space: nowrap;">Desa&nbsp;' . htmlspecialchars(ucwords(strtolower($clean_desa))) . '</span>';
                                                }
                                                $kec = !empty($ptk->kecamatan) ? trim($ptk->kecamatan) : '';
                                                if ($kec) {
                                                    $clean_kec = preg_replace('/^(KEC\.?|KECAMATAN)\s+/i', '', $kec);
                                                    $addr_parts[] = '<span style="white-space: nowrap;">Kec.&nbsp;' . htmlspecialchars(ucwords(strtolower($clean_kec))) . '</span>';
                                                }
                                                $kab = !empty($ptk->kabupaten) ? trim($ptk->kabupaten) : '';
                                                if ($kab) {
                                                    $clean_kab = preg_replace('/^(KAB\.?|KABUPATEN|KOTA)\s+/i', '', $kab);
                                                    $addr_parts[] = '<span style="white-space: nowrap;">Kab.&nbsp;' . htmlspecialchars(ucwords(strtolower($clean_kab))) . '</span>';
                                                }
                                                $prov = !empty($ptk->provinsi) ? trim($ptk->provinsi) : '';
                                                if ($prov) {
                                                    $clean_prov = preg_replace('/^(PROV\.?|PROVINSI)\s+/i', '', $prov);
                                                    $addr_parts[] = '<span style="white-space: nowrap;">Prov.&nbsp;' . htmlspecialchars(ucwords(strtolower($clean_prov))) . '</span>';
                                                }
                                                $ptk_alamat = !empty($addr_parts) ? implode(' ', $addr_parts) : '-';

                                                $ptk_jk = (!empty($ptk->jenis_kelamin) && $ptk->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan';
                                                $ptk_jenis = !empty($ptk->jenis_ptk) ? $ptk->jenis_ptk : 'Guru/Staf';
                                                $sel_ptk = ($target_ptk_id > 0) ? ($ptk->id_ptk == $target_ptk_id) : false;
                                            ?>
                                                <option value="<?php echo $ptk->id_ptk ?>"
                                                        data-nama="<?php echo htmlspecialchars($ptk->nama_ptk) ?>"
                                                        data-ttl="<?php echo htmlspecialchars(($ptk_tempat !== '-' ? $ptk_tempat : '') . ($ptk_tempat !== '-' && $ptk_tgl !== '-' ? ', ' : '') . ($ptk_tgl !== '-' ? $ptk_tgl : '-')) ?>"
                                                        data-alamat="<?php echo htmlspecialchars($ptk_alamat) ?>"
                                                        data-jk="<?php echo $ptk_jk ?>"
                                                        <?php echo $sel_ptk ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars($ptk->nama_ptk) ?> (<?php echo htmlspecialchars($ptk_jenis) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-12">
                                        <label class="form-label fw-semibold text-xs text-neutral-700">Tanggal Terhitung Mulai Tugas (TMT)</label>
                                        <input type="date" name="tmt" id="input_tmt" class="form-control radius-8" value="<?php echo !empty($payload_edit['tmt']) ? $payload_edit['tmt'] : date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- POIN KEDUA S/D KELIMA -->
                            <div class="mb-12">
                                <label class="form-label fw-semibold text-xs text-neutral-700">Kedua (Beban Tugas / Kerja)</label>
                                <textarea name="poin_kedua" id="input_poin_kedua" class="form-control radius-8" rows="2"><?php echo !empty($payload_edit['poin_kedua']) ? htmlspecialchars($payload_edit['poin_kedua']) : 'Hal mengenai pembagian dan beban tugas/kerja akan ditetapkan oleh Kepala Sekolah {nama_lembaga};' ?></textarea>
                            </div>

                            <div class="mb-12">
                                <label class="form-label fw-semibold text-xs text-neutral-700">Ketiga (Pembiayaan / Honorarium)</label>
                                <textarea name="poin_ketiga" id="input_poin_ketiga" class="form-control radius-8" rows="2"><?php echo !empty($payload_edit['poin_ketiga']) ? htmlspecialchars($payload_edit['poin_ketiga']) : 'Segala biaya yang timbul akibat pelaksanaan keputusan ini dibebankan pada anggaran Yayasan serta anggaran lainnya yang sah sesuai dengan ketentuan yang berlaku;' ?></textarea>
                            </div>

                            <div class="mb-12">
                                <label class="form-label fw-semibold text-xs text-neutral-700">Keempat (Ketentuan Perbaikan Error / Kekeliruan)</label>
                                <textarea name="poin_keempat" id="input_poin_keempat" class="form-control radius-8" rows="2"><?php echo !empty($payload_edit['poin_keempat']) ? htmlspecialchars($payload_edit['poin_keempat']) : 'Apabila dikemudian hari ternyata terdapat kekeliruan dalam keputusan ini, akan diadakan perbaikan sebagaimana mestinya;' ?></textarea>
                            </div>

                            <div class="mb-12">
                                <label class="form-label fw-semibold text-xs text-neutral-700">Kelima (Masa Berlaku Keputusan)</label>
                                <textarea name="poin_kelima" id="input_poin_kelima" class="form-control radius-8" rows="1"><?php echo !empty($payload_edit['poin_kelima']) ? htmlspecialchars($payload_edit['poin_kelima']) : 'Keputusan ini berlaku sejak tanggal ditetapkan.' ?></textarea>
                            </div>
                        </div>

                        <!-- 4. TANGGAL SURAT & PENANDATANGAN -->
                        <div class="border radius-8 p-16 mb-24 bg-light-50">
                            <h6 class="fw-bold text-sm text-neutral-800 border-bottom pb-8 mb-16">4. Lokasi, Tanggal & Pejabat Penandatangan</h6>
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Ditetapkan Di (Kabupaten Yayasan)</label>
                                    <input type="text" class="form-control radius-8 bg-light" value="<?php echo htmlspecialchars($kab_yayasan) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Pada Tanggal SK</label>
                                    <input type="date" name="tanggal_surat" id="input_tanggal_surat" class="form-control radius-8" value="<?php echo !empty($surat_edit) ? $surat_edit->tanggal_surat : date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Pejabat Penandatangan (Ketua Yayasan)</label>
                                    <select name="id_ptk_penandatangan" id="id_ptk_penandatangan" class="form-select radius-8" required>
                                        <?php 
                                        $target_ttd_id = isset($payload_edit['id_ptk_penandatangan']) ? $payload_edit['id_ptk_penandatangan'] : 0;
                                        foreach ($ptk_list as $ptk): 
                                            $ptk_jab = !empty($ptk->jenis_ptk) ? $ptk->jenis_ptk : (!empty($ptk->jabatan) ? $ptk->jabatan : '');
                                            $is_selected = ($target_ttd_id > 0) ? ($ptk->id_ptk == $target_ttd_id) : (stripos($ptk->nama_ptk, 'ROBI') !== false || stripos($ptk_jab, 'Ketua') !== false);
                                        ?>
                                            <option value="<?php echo $ptk->id_ptk ?>" <?php echo $is_selected ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($ptk->nama_ptk) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Jabatan Penandatangan</label>
                                    <input type="text" name="jabatan_penandatangan" id="input_jabatan_penandatangan" class="form-control radius-8" value="<?php echo !empty($surat_edit) ? htmlspecialchars($surat_edit->penandatangan_jabatan) : 'Ketua Yayasan' ?>" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-xs text-neutral-700">Tipe Tanda Tangan</label>
                                    <div class="d-flex gap-4">
                                        <?php $tipe_ttd_val = !empty($surat_edit) ? $surat_edit->tipe_ttd : 'basah'; ?>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipe_ttd" id="ttdBasah" value="basah" <?php echo ($tipe_ttd_val === 'basah') ? 'checked' : '' ?>>
                                            <label class="form-check-label text-sm" for="ttdBasah">TTD Basah (Kosong untuk Cap Manual)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipe_ttd" id="ttdDigital" value="digital" <?php echo ($tipe_ttd_val === 'digital') ? 'checked' : '' ?>>
                                            <label class="form-check-label text-sm" for="ttdDigital">TTD Digital & Stempel Overlay</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo url('surat/keluar') ?>" class="btn btn-outline-neutral-400 radius-8 px-20">Batal</a>
                            <button type="submit" class="btn btn-warning-600 text-white radius-8 px-24 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon> <?php echo !empty($surat_edit) ? 'Simpan Perubahan & Cetak SK' : 'Simpan & Cetak SK' ?>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: LIVE PREVIEW SIMULATION -->
            <div class="col-lg-5">
                <div class="card radius-12 border sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header bg-neutral-800 text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 text-white text-sm d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Live Preview SK Pengangkatan
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-600 text-white text-xs radius-4 d-none d-sm-inline-block">Format Resmi</span>
                            <button type="button" id="btnMaximizePreview" class="btn btn-xs btn-primary-600 radius-6 d-flex align-items-center gap-1 shadow-xs">
                                <iconify-icon icon="solar:full-screen-bold-duotone"></iconify-icon> Maximize
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-20 overflow-auto" style="max-height: 80vh; background: #525659;">
                        <!-- PAPER SIMULATION -->
                        <div id="skPaper" class="bg-white p-24 shadow-lg radius-4 text-dark" style="font-family: 'Times New Roman', Times, serif; font-size: 11px; line-height: 1.35; color: #000;">
                            
                            <!-- KOP HEADER -->
                            <div id="pvKop" class="text-center border-bottom pb-2 mb-3">
                                <h4 class="fw-bold mb-0 text-uppercase" style="font-size: 14px; font-family: serif;" id="pvKopNaungan">YAYASAN</h4>
                                <h3 class="fw-bold mb-0 text-uppercase" style="font-size: 16px; font-family: serif;" id="pvKopLembaga">MIFTAHUL KHOER EL-ISTOHARY</h3>
                                <p class="mb-0 text-muted" style="font-size: 8px;" id="pvKopSub">Akta Notaris Wawan Ridwan, SH., MKn. No. AHU-0160.AHA.02.01. 23 Januari Tahun 2010</p>
                                <p class="mb-0 text-muted" style="font-size: 8px;" id="pvKopAlamat">Dusun Mandala No. 56 RT 017 RW 006 Desa Kertamandala Kec. Panjalu Kab. Ciamis</p>
                            </div>

                            <!-- JUDUL SURAT -->
                            <div class="text-center mb-3">
                                <p class="fw-bold mb-0 text-uppercase" style="font-size: 13px; text-decoration: underline;">SURAT KEPUTUSAN</p>
                                <p class="fw-bold mb-2 text-uppercase" style="font-size: 11px;" id="pvJabatanHeader">KETUA YAYASAN MIFTAHUL KHOER EL-ISTOHARY</p>
                                <p class="mb-1 fw-bold" style="font-size: 10px;" id="pvNomorSurat">Nomor : 01.001/YMK/VI/2026</p>
                                <p class="fw-bold mb-0 text-uppercase" style="font-size: 11px;">TENTANG</p>
                                <p class="fw-bold mb-0 text-uppercase" style="font-size: 11px;" id="pvTentang">PENGANGKATAN PEGAWAI / GURU TETAP YAYASAN</p>
                                <p class="fw-bold mb-2 text-uppercase" style="font-size: 11px;" id="pvTentang">SMP MIFTAHUL KHOER BOARDING SCHOOL</p>
                                
                            </div>

                            <!-- KONSIDERAN TABLE -->
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
                                <tr>
                                    <td style="width: 110px; vertical-align: top; font-weight: bold;">MENIMBANG</td>
                                    <td style="width: 15px; vertical-align: top;">:</td>
                                    <td style="vertical-align: top; text-align: justify;" id="pvMenimbang">-</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">MENGINGAT</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; padding-top: 4px;" id="pvMengingatList">-</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">MEMPERHATIKAN</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; text-align: justify; padding-top: 4px;" id="pvMemperhatikan">-</td>
                                </tr>
                            </table>

                            <!-- MEMUTUSKAN / MENETAPKAN -->
                            <div class="text-center fw-bold mb-1" style="font-size: 11px;">MEMUTUSKAN</div>
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
                                <tr>
                                    <td style="width: 110px; vertical-align: top; font-weight: bold;">MENETAPKAN</td>
                                    <td style="width: 15px; vertical-align: top;">:</td>
                                    <td style="vertical-align: top;"></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold;">Pertama</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="vertical-align: top;">
                                        <div class="mb-1" id="pvRedaksiPertama">Mengangkat dan menugaskan Pegawai / Guru Tetap Yayasan <span id="pvLembagaTujuanName">SMP Miftahul Khoer BS</span> :</div>
                                        <table style="width: 100%; border-collapse: collapse; margin-left: 10px; margin-bottom: 6px;">
                                            <tr><td style="width: 110px;">Nama</td><td style="width: 10px;">:</td><td class="fw-bold" id="pvPtkNama">-</td></tr>
                                            <tr><td>Tempat, Tgl Lahir</td><td>:</td><td id="pvPtkTtl">-</td></tr>
                                            <tr><td>Alamat</td><td>:</td><td id="pvPtkAlamat">-</td></tr>
                                            <tr><td>Jenis Kelamin</td><td>:</td><td id="pvPtkJk">-</td></tr>
                                            <tr><td>Unit Kerja</td><td>:</td><td id="pvPtkUnit">-</td></tr>
                                            <tr><td>TMT</td><td>:</td><td class="fw-bold" id="pvPtkTmt">-</td></tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">Kedua</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; text-align: justify; padding-top: 4px;" id="pvPoinKedua">-</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">Ketiga</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; text-align: justify; padding-top: 4px;" id="pvPoinKetiga">-</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">Keempat</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; text-align: justify; padding-top: 4px;" id="pvPoinKeempat">-</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-top: 4px;">Kelima</td>
                                    <td style="vertical-align: top; padding-top: 4px;">:</td>
                                    <td style="vertical-align: top; text-align: justify; padding-top: 4px;" id="pvPoinKelima">-</td>
                                </tr>
                            </table>

                            <!-- FOOTER TTD -->
                            <div class="d-flex justify-content-end mt-4">
                                <div style="width: 220px; text-align: left;">
                                    <table style="width: 100%;">
                                        <tr><td style="width: 90px;">Ditetapkan di</td><td style="width: 10px;">:</td><td id="pvDitetapkanDi"><?php echo htmlspecialchars($kab_yayasan) ?></td></tr>
                                        <tr><td>Pada Tanggal</td><td>:</td><td id="pvTanggalSk"><?php echo date('d F Y') ?></td></tr>
                                    </table>
                                    <div class="mt-2 font-serif" id="pvJabatanTtd">Ketua Yayasan</div>
                                    <div style="height: 50px;" class="d-flex align-items-center">
                                        <em class="text-muted" style="font-size: 9px;"></em>
                                    </div>
                                    <div class="fw-bold text-uppercase" style="text-decoration: underline;" id="pvNamaTtd">HJ. SITI ROBI’AH</div>
                                </div>
                            </div>

                            <!-- QR CODE VALIDASI FOOTER (PREVIEW) -->
                            <div class="d-flex align-items-center gap-3 mt-4 pt-0" style="font-size: 7pt; color: #8f95a0; line-height: 1.2; font-family: Arial, sans-serif;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?php echo rawurlencode(url('surat/v/DEMO')) ?>" style="width: 48px; height: 48px;" alt="QR Validasi">
                                <div>
                                    <div><strong>Dokumen ini dikeluarkan dan diarsipkan melalui Aplikasi Miftahul Khoer Data Center.</strong></div>
                                    <div>Validasi surat melalui Scan QR Code disamping.</div>
                                    <div>Nomor: <span id="pvNomorSuratQr">01.001/YMK/VI/2026</span></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- MODAL MAXIMIZE PREVIEW (LAYOUT LEBAR & FIXED BOTTOM BAR) -->
<div class="modal fade" id="modalMaximizePreview" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content radius-12 border-0 shadow-lg">
            <div class="modal-header bg-neutral-900 text-white px-24 py-16">
                <h6 class="modal-title text-white d-flex align-items-center gap-2 mb-0">
                    <iconify-icon icon="solar:eye-bold-duotone" class="text-xl text-primary-400"></iconify-icon>
                    <span>Preview SK Pengangkatan (Tampilan Lebar & Resmi)</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 bg-neutral-700" style="min-height: 70vh;">
                <div class="d-flex justify-content-center">
                    <!-- Paper Max Container -->
                    <div id="skPaperMax" class="bg-white p-40 shadow-2xl radius-4 text-dark" style="width: 100%; max-width: 850px; font-family: 'Times New Roman', Times, serif; font-size: 13px; line-height: 1.45; color: #000;">
                    </div>
                </div>
            </div>
            <!-- FIXED BOTTOM BAR DI DALAM MODAL -->
            <div class="modal-footer bg-white border-top px-24 py-16 position-sticky bottom-0 d-flex justify-content-between align-items-center shadow-lg" style="z-index: 1070;">
                <div class="d-flex align-items-center gap-2 text-neutral-700 text-sm">
                    <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-lg"></iconify-icon>
                    <span>Dokumen siap diterbitkan dan dicetak dalam format resmi.</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-neutral-400 radius-8 text-sm px-20" data-bs-dismiss="modal">Tutup Preview</button>
                    <button type="button" id="btnSubmitFromModal" class="btn btn-warning-600 text-white radius-8 text-sm px-24 fw-bold d-flex align-items-center gap-2 shadow-sm">
                        <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon>
                        <span><?php echo !empty($surat_edit) ? 'Simpan Perubahan & Cetak SK' : 'Simpan & Cetak SK' ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHECKLIST DASAR HUKUM -->
<div class="modal fade" id="modalDasarHukumChecklist" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom px-24 py-16 bg-primary-600 text-white">
                <h6 class="modal-title fw-bold text-white" id="modalDasarHukumLabel">Pilih Dasar Hukum SK (Mengingat)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th style="width: 140px;">Kategori</th>
                                <th>Dasar Hukum / Regulasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dasar_hukum_list as $dh): ?>
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-dh-item" type="checkbox" value="<?php echo htmlspecialchars($dh->judul) ?>">
                                    </td>
                                    <td><span class="badge bg-neutral-100 text-neutral-800 border"><?php echo htmlspecialchars($dh->kategori) ?></span></td>
                                    <td class="text-xs fw-medium text-neutral-800"><?php echo htmlspecialchars($dh->judul) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top px-24 py-16">
                <button type="button" class="btn btn-outline-neutral-400 radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnInsertDasarHukum" class="btn btn-primary-600 radius-8 text-sm px-20">Masukan ke dalam Template</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SIMPAN PRESET -->
<div class="modal fade" id="modalSavePreset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom px-24 py-16 bg-primary-50">
                <h6 class="modal-title fw-bold text-primary-900">Simpan / Overwrite Preset Redaksi SK</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="mb-16">
                    <label class="form-label fw-semibold text-sm">Mode Penyimpanan</label>
                    <select id="selectPresetAction" class="form-select radius-8">
                        <option value="0">+ Simpan Sebagai Preset Baru</option>
                        <?php if (!empty($preset_list)): ?>
                            <?php foreach ($preset_list as $ps): ?>
                                <option value="<?php echo $ps->id_preset ?>" data-nama="<?php echo htmlspecialchars($ps->nama_preset) ?>">
                                    🔄 Timpa (Overwrite): <?php echo htmlspecialchars($ps->nama_preset) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-16">
                    <label class="form-label fw-semibold text-sm">Nama Preset Template <span class="text-danger">*</span></label>
                    <input type="text" id="inputPresetName" class="form-control radius-8" placeholder="Contoh: Preset Standard SK Pengangkatan Guru 2026" required>
                </div>
                <div class="p-12 radius-8 bg-light border text-secondary-light text-xs">
                    <iconify-icon icon="solar:info-circle-linear" class="text-primary-600 me-1"></iconify-icon>
                    Pilih <strong>Timpa (Overwrite)</strong> untuk memperbarui isi preset yang sudah ada, atau <strong>Simpan Sebagai Preset Baru</strong> untuk membuat preset baru.
                </div>
            </div>
            <div class="modal-footer border-top px-24 py-16 d-flex justify-content-between">
                <button type="button" id="btnDeletePresetModal" class="btn btn-outline-danger radius-8 text-sm d-none d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
                    <span>Hapus Preset Ini</span>
                </button>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-neutral-400 radius-8 text-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmSavePreset" class="btn btn-primary-600 radius-8 text-sm px-20 fw-semibold">Simpan Preset</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
$(document).ready(function() {

    // Force Aktifkan Menu 'Buat Surat' pada Sidebar
    setTimeout(function() {
        const buatSuratLink = $('ul#sidebar-menu a[href*="surat/buat"]');
        if (buatSuratLink.length) {
            $('ul#sidebar-menu .active-page').removeClass('active-page');
            $('ul#sidebar-menu .open').removeClass('open dropdown-open active');
            
            buatSuratLink.addClass('active-page');
            buatSuratLink.parent().addClass('active-page');
            const parentSubmenu = buatSuratLink.closest('.sidebar-submenu');
            if (parentSubmenu.length) {
                parentSubmenu.addClass('show').slideDown(0);
                parentSubmenu.parent().addClass('open dropdown-open active');
            }
        }
    }, 50);

    // Helper Escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // 0. MANAGE MENIMBANG ITEMS
    function getLetterCode(index) {
        let code = '';
        let i = index;
        while (i >= 0) {
            code = String.fromCharCode(97 + (i % 26)) + code;
            i = Math.floor(i / 26) - 1;
        }
        return code + '.';
    }

    function updateMenimbangNumbers() {
        $('#menimbangContainer .menimbang-item').each(function(index) {
            const total = $('#menimbangContainer .menimbang-item').length;
            const letter = total > 1 ? getLetterCode(index) : 'a.';
            $(this).find('.item-num-m').text(letter);
        });
        updateLivePreview();
    }

    $(document).on('click', '.btn-move-up-m', function() {
        const item = $(this).closest('.menimbang-item');
        if (item.prev('.menimbang-item').length) {
            item.insertBefore(item.prev('.menimbang-item'));
            updateMenimbangNumbers();
        }
    });

    $(document).on('click', '.btn-move-down-m', function() {
        const item = $(this).closest('.menimbang-item');
        if (item.next('.menimbang-item').length) {
            item.insertAfter(item.next('.menimbang-item'));
            updateMenimbangNumbers();
        }
    });

    $(document).on('click', '.btn-remove-item-m', function() {
        if ($('#menimbangContainer .menimbang-item').length > 1) {
            $(this).closest('.menimbang-item').remove();
            updateMenimbangNumbers();
        } else {
            alert('Minimal harus ada 1 poin Menimbang.');
        }
    });

    $('#btnAddMenimbang').on('click', function() {
        const count = $('#menimbangContainer .menimbang-item').length;
        const letter = getLetterCode(count);
        const itemHtml = `
            <div class="menimbang-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                <span class="badge bg-primary-50 text-primary-700 font-mono item-num-m mt-1">${letter}</span>
                <textarea name="menimbang[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-menimbang-text" rows="2" style="resize: vertical; focus: outline-none;" placeholder="Tulis poin pertimbangan menimbang di sini..."></textarea>
                <div class="d-flex flex-column gap-1">
                    <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up-m" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                    <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down-m" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                    <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item-m" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                </div>
            </div>
        `;
        $('#menimbangContainer').append(itemHtml);
        updateMenimbangNumbers();
    });

    // 1. REORDER & REMOVE MENGINGAT ITEMS
    function updateMengingatNumbers() {
        $('#mengingatContainer .mengingat-item').each(function(index) {
            $(this).find('.item-num').text(index + 1);
        });
        updateLivePreview();
    }

    $(document).on('click', '.btn-move-up', function() {
        const item = $(this).closest('.mengingat-item');
        if (item.prev('.mengingat-item').length) {
            item.insertBefore(item.prev('.mengingat-item'));
            updateMengingatNumbers();
        }
    });

    $(document).on('click', '.btn-move-down', function() {
        const item = $(this).closest('.mengingat-item');
        if (item.next('.mengingat-item').length) {
            item.insertAfter(item.next('.mengingat-item'));
            updateMengingatNumbers();
        }
    });

    $(document).on('click', '.btn-remove-item', function() {
        if ($('#mengingatContainer .mengingat-item').length > 1) {
            $(this).closest('.mengingat-item').remove();
            updateMengingatNumbers();
        } else {
            alert('Minimal harus ada 1 Dasar Hukum (Mengingat).');
        }
    });

    $('#btnAddMengingatManual').on('click', function() {
        const count = $('#mengingatContainer .mengingat-item').length + 1;
        const itemHtml = `
            <div class="mengingat-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                <span class="badge bg-neutral-100 text-neutral-700 font-mono item-num mt-1">${count}</span>
                <textarea name="mengingat[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-mengingat-text" rows="2" style="resize: vertical; focus: outline-none;" placeholder="Tulis dasar hukum (mengingat) secara manual di sini..."></textarea>
                <div class="d-flex flex-column gap-1">
                    <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                    <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                    <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                </div>
            </div>
        `;
        $('#mengingatContainer').append(itemHtml);
        updateMengingatNumbers();
    });

    // 2. CHECKLIST DASAR HUKUM MODAL INSERT
    $('#btnInsertDasarHukum').on('click', function() {
        const checkedVals = [];
        $('.chk-dh-item:checked').each(function() {
            checkedVals.push($(this).val());
        });

        if (checkedVals.length === 0) {
            alert('Pilih setidaknya satu dasar hukum.');
            return;
        }

        checkedVals.forEach(function(val) {
            const count = $('#mengingatContainer .mengingat-item').length + 1;
            const itemHtml = `
                <div class="mengingat-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                    <span class="badge bg-neutral-100 text-neutral-700 font-mono item-num mt-1">${count}</span>
                    <textarea name="mengingat[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-mengingat-text" rows="2" style="resize: vertical; focus: outline-none;">${val}</textarea>
                    <div class="d-flex flex-column gap-1">
                        <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                        <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                    </div>
                </div>
            `;
            $('#mengingatContainer').append(itemHtml);
        });

        $('.chk-dh-item').prop('checked', false);
        $('#modalDasarHukumChecklist').modal('hide');
        updateMengingatNumbers();
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatTanggalIndoJS(dateStr) {
        if (!dateStr || dateStr === '0000-00-00' || dateStr === '-') return '-';
        const parts = String(dateStr).split('-');
        const bulanIndo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        if (parts.length === 3) {
            const d = parseInt(parts[2], 10);
            const m = parseInt(parts[1], 10) - 1;
            const y = parts[0];
            if (m >= 0 && m < 12) {
                return d + ' ' + bulanIndo[m] + ' ' + y;
            }
        }
        const d = new Date(dateStr);
        if (!isNaN(d.getTime())) {
            return d.getDate() + ' ' + bulanIndo[d.getMonth()] + ' ' + d.getFullYear();
        }
        return dateStr;
    }

    function updateKopPreview() {
        const selectedOpt = $('#id_kop_surat option:selected');
        const rawData = selectedOpt.data('kop');
        if (!rawData) return;
        
        let kop = rawData;
        if (typeof rawData === 'string') {
            try {
                kop = JSON.parse(rawData);
            } catch(e) {
                return;
            }
        }
        
        const logoUrl = kop.logo ? '<?php echo url('uploads/kop_logo/') ?>' + kop.logo : '<?php echo url('assets/images/logodc_round.png') ?>';
        const logoKananUrl = kop.logo_kanan ? '<?php echo url('uploads/kop_logo/') ?>' + kop.logo_kanan : '';
        
        const szNaungan = (kop.font_size_naungan || 11) + 'px';
        const szNaungan2 = (kop.font_size_naungan_2 || 11) + 'px';
        const szLembaga = (kop.font_size_lembaga || 18) + 'px';
        const szSub = (kop.font_size_sub || 13) + 'px';
        const szAlamat = (kop.font_size_alamat || 9) + 'px';
        const caseStyle = (kop.case_style === 'custom') ? 'none' : 'uppercase';
        const layout = kop.layout_style || 'center';
        
        let html = '';
        
        if (layout === 'left_logo_center_text' || layout === 'center') {
            html += '<table style="width: 100%; border-collapse: collapse; border: 0;"><tr>';
            html += '<td style="width: 65px; vertical-align: middle; text-align: left; padding-right: 8px; border: 0;">';
            html += '<img src="' + logoUrl + '" style="max-width: 70px; max-height: 70px;"></td>';
            html += '<td style="vertical-align: middle; text-align: center; border: 0;">';
            if (kop.naungan) {
                html += '<div style="font-size: ' + szNaungan + '; font-weight: 550; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.naungan) + '</div>';
            }
            if (kop.naungan_2) {
                html += '<div style="font-size: ' + szNaungan2 + '; font-weight: 550; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.naungan_2) + '</div>';
            }
            if (kop.nama_lembaga) {
                html += '<div style="font-size: ' + szLembaga + '; font-weight: bold; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.nama_lembaga) + '</div>';
            }
            if (kop.sub_nama) {
                html += '<div style="font-size: ' + szSub + '; font-weight: bold; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.sub_nama) + '</div>';
            }
            if (kop.alamat) {
                html += '<div style="font-size: ' + szAlamat + '; color: #374151; margin-top: 2px;">' + escapeHtml(kop.alamat) + '</div>';
            }
            if (kop.kontak) {
                html += '<div style="font-size: ' + szAlamat + '; color: #374151;">' + escapeHtml(kop.kontak) + '</div>';
            }
            html += '</td>';
            html += '<td style="width: 65px; vertical-align: middle; text-align: right; padding-left: 8px; border: 0;">';
            if (logoKananUrl) {
                html += '<img src="' + logoKananUrl + '" style="max-width: 70px; max-height: 70px;">';
            }
            html += '</td></tr></table>';
        } else {
            html += '<table style="width: 100%; border-collapse: collapse; border: 0;"><tr>';
            html += '<td style="width: 65px; vertical-align: middle; text-align: left; padding-right: 12px; border: 0;">';
            html += '<img src="' + logoUrl + '" style="max-width: 70px; max-height: 70px;"></td>';
            html += '<td style="vertical-align: middle; text-align: left; border: 0;">';
            if (kop.naungan) {
                html += '<div style="font-size: ' + szNaungan + '; font-weight: 550; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.naungan) + '</div>';
            }
            if (kop.naungan_2) {
                html += '<div style="font-size: ' + szNaungan2 + '; font-weight: 550; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.naungan_2) + '</div>';
            }
            if (kop.nama_lembaga) {
                html += '<div style="font-size: ' + szLembaga + '; font-weight: bold; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.nama_lembaga) + '</div>';
            }
            if (kop.sub_nama) {
                html += '<div style="font-size: ' + szSub + '; font-weight: bold; text-transform: ' + caseStyle + ';">' + escapeHtml(kop.sub_nama) + '</div>';
            }
            if (kop.alamat) {
                html += '<div style="font-size: ' + szAlamat + '; color: #374151;">' + escapeHtml(kop.alamat) + '</div>';
            }
            if (kop.kontak) {
                html += '<div style="font-size: ' + szAlamat + '; color: #374151;">' + escapeHtml(kop.kontak) + '</div>';
            }
            html += '</td></tr></table>';
        }
        
        $('#pvKop').html(html);
    }

    // 3. LIVE PREVIEW UPDATE LOGIC
    function updateLivePreview() {
        // Update Kop Surat Preview
        updateKopPreview();

        // Nomor Surat
        $('#pvNomorSurat').text('Nomor : ' + $('#nomor_surat').val());
        
        // Tentang & Redaksi Lembaga
        const tentang = $('#input_tentang').val() || 'PENGANGKATAN PEGAWAI / GURU TETAP YAYASAN';
        const lembagaTujuan = $('#id_lembaga_tujuan option:selected').text().trim();
        $('#pvTentang').text(tentang );
        $('#pvLembagaTujuanName').text(lembagaTujuan);

        // Menimbang & Memperhatikan
        let menimbangItems = [];
        $('#menimbangContainer .input-menimbang-text').each(function() {
            const txt = $(this).val().trim().replace('{nama_lembaga}', lembagaTujuan);
            if (txt) {
                menimbangItems.push(txt);
            }
        });

        if (menimbangItems.length === 1) {
            $('#pvMenimbang').html(escapeHtml(menimbangItems[0]));
        } else if (menimbangItems.length > 1) {
            let mHtml = '<table style="width: 100%; border-collapse: collapse; margin: 0;">';
            menimbangItems.forEach(function(txt, idx) {
                mHtml += '<tr>';
                mHtml += '<td style="width: 18px; vertical-align: top; padding-bottom: 3px;">' + getLetterCode(idx) + '</td>';
                mHtml += '<td style="vertical-align: top; text-align: justify; padding-bottom: 3px;">' + escapeHtml(txt) + '</td>';
                mHtml += '</tr>';
            });
            mHtml += '</table>';
            $('#pvMenimbang').html(mHtml);
        } else {
            $('#pvMenimbang').text('-');
        }

        $('#pvMemperhatikan').text($('#input_memperhatikan').val());

        // Mengingat List (Tabel Penomoran Rapi - Poin 1 s/d N-1 diakhiri ';', Poin terakhir diakhiri '.')
        let dhItems = [];
        $('#mengingatContainer .input-mengingat-text').each(function() {
            const txt = $(this).val().trim();
            if (txt) {
                dhItems.push(txt);
            }
        });

        let dhHtml = '<table style="width: 100%; border-collapse: collapse; font-size: 11px; margin: 0;">';
        dhItems.forEach(function(txt, idx) {
            const cleanTxt = txt.replace(/[;.]+$/, '').trim();
            const punc = (idx === dhItems.length - 1) ? '.' : ';';
            const finalTxt = cleanTxt + punc;

            dhHtml += '<tr>';
            dhHtml += '<td style="width: 18px; vertical-align: top; padding-bottom: 2px;">' + (idx + 1) + '.</td>';
            dhHtml += '<td style="vertical-align: top; text-align: justify; padding-bottom: 2px;">' + escapeHtml(finalTxt) + '</td>';
            dhHtml += '</tr>';
        });
        dhHtml += '</table>';
        $('#pvMengingatList').html(dhHtml);

        // Data PTK (Pertama)
        const ptkOpt = $('#id_ptk option:selected');
        if (ptkOpt.val()) {
            $('#pvPtkNama').text(ptkOpt.data('nama'));
            $('#pvPtkTtl').text(ptkOpt.data('ttl'));
            $('#pvPtkAlamat').html(ptkOpt.data('alamat'));
            $('#pvPtkJk').text(ptkOpt.data('jk'));
            $('#pvPtkUnit').text(lembagaTujuan);
            
            const rawTmt = $('#input_tmt').val();
            if (rawTmt) {
                $('#pvPtkTmt').text(formatTanggalIndoJS(rawTmt));
            }
        } else {
            $('#pvPtkNama, #pvPtkTtl, #pvPtkAlamat, #pvPtkJk, #pvPtkUnit, #pvPtkTmt').text('-');
        }

        // Poin Kedua s/d Kelima (Ganti {nama_lembaga} dengan Lembaga Penugasan secara dinamis)
        $('#pvPoinKedua').text($('#input_poin_kedua').val().replace(/{nama_lembaga}/g, lembagaTujuan));
        $('#pvPoinKetiga').text($('#input_poin_ketiga').val().replace(/{nama_lembaga}/g, lembagaTujuan));
        $('#pvPoinKeempat').text($('#input_poin_keempat').val().replace(/{nama_lembaga}/g, lembagaTujuan));
        $('#pvPoinKelima').text($('#input_poin_kelima').val().replace(/{nama_lembaga}/g, lembagaTujuan));

        // Tanggal SK & Penandatangan
        const rawDate = $('#input_tanggal_surat').val();
        if (rawDate) {
            $('#pvTanggalSk').text(formatTanggalIndoJS(rawDate));
        }

        const ptkTtdOpt = $('#id_ptk_penandatangan option:selected');
        if (ptkTtdOpt.length) {
            $('#pvNamaTtd').text(ptkTtdOpt.text().trim());
        }
        $('#pvJabatanTtd').text($('#input_jabatan_penandatangan').val() || 'Ketua Yayasan');
    }

    // Trigger preview on input changes
    $(document).on('input change', 'input, textarea, select', function() {
        updateLivePreview();
    });

    // 4. KODE SURAT NOMOR GENERATOR
    $('#id_kode_surat, #nomor_urut').on('change input', function() {
        const opt = $('#id_kode_surat option:selected');
        const urut = String($('#nomor_urut').val() || '1').padStart(3, '0');
        const kodeJ = opt.data('kodejenis') || '01';
        const kodeL = opt.data('kodelembaga') || 'YMK';
        const date = new Date($('#input_tanggal_surat').val() || Date.now());
        const romanMonths = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        const monthRoman = romanMonths[date.getMonth()];
        const year = date.getFullYear();

        const formatted = `${kodeJ}.${urut}/${kodeL}/${monthRoman}/${year}`;
        $('#nomor_surat').val(formatted);
        updateLivePreview();
    });

    // Toggle Delete Preset Top Button & Sync Hidden Input
    $('#presetSelector').on('change', function() {
        const val = $(this).val();
        $('#id_preset_loaded').val(val);
        if (val) {
            $('#btnDeletePresetTop').removeClass('d-none');
        } else {
            $('#btnDeletePresetTop').addClass('d-none');
        }
    });

    // MAXIMIZE PREVIEW MODAL
    $('#btnMaximizePreview').on('click', function() {
        updateLivePreview();
        const paperHtml = $('#skPaper').html();
        $('#skPaperMax').html(paperHtml);
        $('#modalMaximizePreview').modal('show');
    });

    // SUBMIT FORM FROM MAXIMIZE MODAL
    $('#btnSubmitFromModal').on('click', function() {
        $('#modalMaximizePreview').modal('hide');
        $('#formSkPengangkatan').submit();
    });

    // Sync modal preset when opened
    $('#modalSavePreset').on('show.bs.modal', function () {
        const activeLoadedId = $('#presetSelector').val();
        if (activeLoadedId && $('#selectPresetAction option[value="' + activeLoadedId + '"]').length > 0) {
            $('#selectPresetAction').val(activeLoadedId);
            const nama = $('#selectPresetAction option:selected').data('nama');
            $('#inputPresetName').val(nama);
            $('#btnDeletePresetModal').removeClass('d-none');
        } else {
            $('#selectPresetAction').val('0');
            $('#inputPresetName').val('');
            $('#btnDeletePresetModal').addClass('d-none');
        }
    });

    $('#selectPresetAction').on('change', function() {
        const val = $(this).val();
        if (val === '0') {
            $('#inputPresetName').val('');
            $('#btnDeletePresetModal').addClass('d-none');
        } else {
            const nama = $(this).find('option:selected').data('nama');
            $('#inputPresetName').val(nama);
            $('#btnDeletePresetModal').removeClass('d-none');
        }
    });

    // 5. DELETE PRESET AJAX FUNCTION
    function doDeletePreset(idPreset, namePreset) {
        if (!idPreset || idPreset === '0') return;
        if (confirm('Apakah Anda yakin ingin menghapus preset "' + namePreset + '"? Tindakan ini tidak dapat dibatalkan.')) {
            $.post("<?php echo url('surat/preset_sk_hapus/') ?>" + idPreset, function(res) {
                if (res.status === 'success') {
                    alert(res.message || 'Preset berhasil dihapus.');
                    location.reload();
                } else {
                    alert(res.message || 'Gagal menghapus preset.');
                }
            }, 'json');
        }
    }

    $('#btnDeletePresetTop').on('click', function() {
        const idPreset = $('#presetSelector').val();
        const namePreset = $('#presetSelector option:selected').data('nama') || $('#presetSelector option:selected').text();
        doDeletePreset(idPreset, namePreset);
    });

    $('#btnDeletePresetModal').on('click', function() {
        const idPreset = $('#selectPresetAction').val();
        const namePreset = $('#selectPresetAction option:selected').data('nama') || $('#inputPresetName').val();
        doDeletePreset(idPreset, namePreset);
    });

    // 5. SAVE / OVERWRITE PRESET AJAX
    $('#btnConfirmSavePreset').on('click', function() {
        const idPreset = $('#selectPresetAction').val();
        const name = $('#inputPresetName').val().trim();
        if (!name) {
            alert('Masukkan nama preset.');
            return;
        }

        const dhArray = [];
        $('#mengingatContainer .input-mengingat-text').each(function() {
            dhArray.push($(this).val().trim());
        });

        const menimbangArray = [];
        $('#menimbangContainer .input-menimbang-text').each(function() {
            menimbangArray.push($(this).val().trim());
        });

        const payload = {
            id_preset: idPreset,
            nama_preset: name,
            tentang: $('#input_tentang').val(),
            menimbang: JSON.stringify(menimbangArray),
            mengingat_json: JSON.stringify(dhArray),
            memperhatikan: $('#input_memperhatikan').val(),
            poin_kedua: $('#input_poin_kedua').val(),
            poin_ketiga: $('#input_poin_ketiga').val(),
            poin_keempat: $('#input_poin_keempat').val(),
            poin_kelima: $('#input_poin_kelima').val(),
            id_ptk_penandatangan: $('#id_ptk_penandatangan').val(),
            jabatan_penandatangan: $('#input_jabatan_penandatangan').val()
        };

        $.post("<?php echo url('surat/preset_sk_simpan') ?>", payload, function(res) {
            if (res.status === 'success') {
                alert(res.message || 'Preset berhasil disimpan!');
                $('#modalSavePreset').modal('hide');
                $('#inputPresetName').val('');
                location.reload();
            } else {
                alert(res.message || 'Gagal menyimpan preset.');
            }
        }, 'json');
    });

    // 6. LOAD PRESET AJAX
    $('#presetSelector').on('change', function() {
        const id = $(this).val();
        if (!id) return;

        $.getJSON("<?php echo url('surat/preset_sk_load/') ?>" + id, function(res) {
            if (res.status === 'success' && res.data) {
                const d = res.data;
                if (d.tentang) $('#input_tentang').val(d.tentang);
                
                if (d.menimbang) {
                    let mItems = [];
                    try {
                        mItems = JSON.parse(d.menimbang);
                    } catch(e) {
                        mItems = [d.menimbang];
                    }
                    if (Array.isArray(mItems) && mItems.length > 0) {
                        $('#menimbangContainer').empty();
                        mItems.forEach(function(txt, idx) {
                            const letter = getLetterCode(idx);
                            const itemHtml = `
                                <div class="menimbang-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                                    <span class="badge bg-primary-50 text-primary-700 font-mono item-num-m mt-1">${letter}</span>
                                    <textarea name="menimbang[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-menimbang-text" rows="2" style="resize: vertical; focus: outline-none;">${escapeHtml(txt)}</textarea>
                                    <div class="d-flex flex-column gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up-m" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down-m" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                                        <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item-m" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                                    </div>
                                </div>
                            `;
                            $('#menimbangContainer').append(itemHtml);
                        });
                        updateMenimbangNumbers();
                    }
                }

                if (d.memperhatikan) $('#input_memperhatikan').val(d.memperhatikan);
                if (d.poin_kedua) $('#input_poin_kedua').val(d.poin_kedua);
                if (d.poin_ketiga) $('#input_poin_ketiga').val(d.poin_ketiga);
                if (d.poin_keempat) $('#input_poin_keempat').val(d.poin_keempat);
                if (d.poin_kelima) $('#input_poin_kelima').val(d.poin_kelima);
                if (d.id_ptk_penandatangan && d.id_ptk_penandatangan > 0) $('#id_ptk_penandatangan').val(d.id_ptk_penandatangan);
                if (d.jabatan_penandatangan) $('#input_jabatan_penandatangan').val(d.jabatan_penandatangan);

                if (d.mengingat_json) {
                    try {
                        const dhs = JSON.parse(d.mengingat_json);
                        if (Array.isArray(dhs) && dhs.length > 0) {
                            $('#mengingatContainer').empty();
                            dhs.forEach(function(val, idx) {
                                const itemHtml = `
                                    <div class="mengingat-item border radius-8 p-10 bg-white d-flex align-items-start gap-2 shadow-xs">
                                        <span class="badge bg-neutral-100 text-neutral-700 font-mono item-num mt-1">${idx + 1}</span>
                                        <textarea name="mengingat[]" class="form-control form-control-sm border-0 p-0 text-xs text-neutral-800 input-mengingat-text" rows="2" style="resize: vertical; focus: outline-none;">${escapeHtml(val)}</textarea>
                                        <div class="d-flex flex-column gap-1">
                                            <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-up" title="Naikkan"><iconify-icon icon="solar:alt-arrow-up-linear"></iconify-icon></button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary p-1 btn-move-down" title="Turunkan"><iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon></button>
                                            <button type="button" class="btn btn-xs btn-outline-danger p-1 btn-remove-item" title="Hapus"><iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon></button>
                                        </div>
                                    </div>
                                `;
                                $('#mengingatContainer').append(itemHtml);
                            });
                            updateMengingatNumbers();
                        }
                    } catch(e) {}
                }
                updateLivePreview();
            }
        });
    });

    // Initial trigger
    updateLivePreview();
});
</script>
