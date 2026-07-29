<?php include viewPath('includes/header'); ?>

<style>
/* ─── Page Styles ─────────────────────────────────────── */
.verif-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.verif-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
}
.verif-hero h4 { font-size: 1.35rem; font-weight: 700; margin-bottom: 4px; }
.verif-hero p  { font-size: .88rem; opacity: .85; margin: 0; }

/* Filter Card */
.filter-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(30,58,138,.08);
    padding: 24px 28px;
    margin-bottom: 24px;
    border: 1px solid #e8edf5;
}
.filter-section-title {
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280;
    margin-bottom: 10px;
}

/* Jenis Dokumen chips */
.doc-chip-wrap { display: flex; flex-wrap: wrap; gap: 8px; }
.doc-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    border: 1.5px solid #d1d5db;
    background: #f9fafb;
    cursor: pointer;
    font-size: .82rem;
    font-weight: 500;
    color: #374151;
    transition: all .18s;
    user-select: none;
}
.doc-chip:hover { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }
.doc-chip.selected {
    border-color: #2563eb;
    background: #dbeafe;
    color: #1d4ed8;
}
.doc-chip.selected .chip-check { display: inline-flex; }
.chip-check { display: none; color: #2563eb; }
.doc-chip input[type="checkbox"] { display: none; }

/* Result Table */
.result-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 20px rgba(30,58,138,.09);
    border: 1px solid #e8edf5;
    overflow: hidden;
}
.result-card-header {
    background: linear-gradient(90deg, #1e3a8a, #3b82f6);
    padding: 16px 24px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.result-card-header h6 { margin: 0; font-size: 1rem; font-weight: 700; }
.result-card-header .badge-count {
    background: rgba(255,255,255,.2);
    color: #fff;
    border-radius: 50px;
    font-size: .78rem;
    padding: 3px 12px;
}

/* Table Styles */
.verif-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.verif-table thead th {
    background: #f1f5f9;
    color: #374151;
    font-weight: 700;
    padding: 12px 14px;
    white-space: nowrap;
    border-bottom: 2px solid #e2e8f0;
    text-align: center;
}
.verif-table thead th:first-child,
.verif-table thead th:nth-child(2) { text-align: left; }
.verif-table tbody tr:hover { background: #f8faff; }
.verif-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    text-align: center;
}
.verif-table tbody td:first-child,
.verif-table tbody td:nth-child(2) { text-align: left; }

.no-siswa { font-size: .8rem; color: #6b7280; }
.verif-no-badge {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: #e2e8f0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700; color: #374151;
}

/* Status badges */
.status-check { font-size: 1.3rem; color: #16a34a; }
.status-x     { font-size: 1.3rem; color: #dc2626; }

/* Summary badges di kolom header */
.col-stat { display: block; font-size: .7rem; font-weight: 500; color: #6b7280; margin-top: 2px; }

/* Sticky header */
.table-sticky-wrap { overflow-x: auto; max-height: 70vh; overflow-y: auto; }
.verif-table thead { position: sticky; top: 0; z-index: 2; }

/* Stat summary bar */
.stat-bar {
    display: flex; gap: 12px; flex-wrap: wrap;
    padding: 12px 24px;
    background: #f8faff;
    border-top: 1px solid #e2e8f0;
}
.stat-item {
    display: flex; align-items: center; gap: 6px;
    font-size: .82rem; color: #374151;
}
.stat-dot { width: 10px; height: 10px; border-radius: 50%; }
.stat-dot.green { background: #16a34a; }
.stat-dot.red   { background: #dc2626; }

/* Rombel select */
#rombelSelect { font-size: .9rem; }

/* Print btn floating */
@media print {
    .no-print { display: none !important; }
    .verif-hero, .filter-card { display: none !important; }
    .result-card { box-shadow: none !important; border: 1px solid #ccc; }
}
</style>

<div class="dashboard-main-body">

    <!-- Hero -->
    <div class="verif-hero no-print">
        <div style="position:relative;z-index:1;">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <iconify-icon icon="solar:checklist-minimalistic-bold" style="font-size:26px;color:#fff;"></iconify-icon>
                </div>
                <div>
                    <h4>Verifikasi Kelengkapan Dokumen Siswa</h4>
                    <p>Pilih jenis dokumen & rombel, lalu cek status kelengkapan berkas siswa secara massal.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card no-print">
        <form method="get" action="<?php echo url('siswa/verifikasiDokumen') ?>" id="filterForm">
            <div class="row g-4">

                <!-- Pilih Jenis Dokumen -->
                <div class="col-12">
                    <div class="filter-section-title">
                        <iconify-icon icon="solar:document-bold" class="me-1"></iconify-icon>
                        Pilih Jenis Dokumen yang Diverifikasi
                    </div>
                    <div class="doc-chip-wrap" id="chipWrap">
                        <?php foreach ($jenis_dokumen as $jd): ?>
                            <?php $checked = in_array($jd->id_jenis_dokumen, $selected_dokumen_ids); ?>
                            <label class="doc-chip <?php echo $checked ? 'selected' : '' ?>" id="chip-<?php echo $jd->id_jenis_dokumen ?>">
                                <input type="checkbox"
                                    name="jenis_dokumen[]"
                                    value="<?php echo $jd->id_jenis_dokumen ?>"
                                    <?php echo $checked ? 'checked' : '' ?>
                                    onchange="syncChip(this)">
                                <iconify-icon icon="solar:check-circle-bold" class="chip-check" style="<?php echo $checked ? '' : 'display:none' ?>"></iconify-icon>
                                <iconify-icon icon="solar:document-linear" style="font-size:14px;opacity:.6;<?php echo $checked ? 'display:none' : '' ?>" class="chip-icon-doc"></iconify-icon>
                                <?php echo html_escape($jd->nama_jenis_dokumen) ?>
                            </label>
                        <?php endforeach; ?>
                        <?php if (empty($jenis_dokumen)): ?>
                            <span class="text-secondary-light text-sm">Belum ada jenis dokumen. Tambah dulu di halaman detail siswa.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol pilih semua / hapus pilihan -->
                    <div class="d-flex gap-2 mt-10">
                        <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" onclick="selectAllChips()">
                            <iconify-icon icon="solar:check-square-linear"></iconify-icon> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" onclick="clearAllChips()">
                            <iconify-icon icon="solar:close-square-linear"></iconify-icon> Hapus Pilihan
                        </button>
                    </div>
                </div>

                <!-- Pilih Rombel -->
                <div class="col-md-6">
                    <div class="filter-section-title">
                        <iconify-icon icon="solar:users-group-rounded-bold" class="me-1"></iconify-icon>
                        Pilih Rombel
                    </div>
                    <select name="id_pembelajaran" id="rombelSelect" class="form-select">
                        <option value="">-- Pilih Rombel --</option>
                        <?php foreach ($daftar_rombel as $r): ?>
                            <option value="<?php echo $r->id_pembelajaran ?>"
                                <?php echo ($selected_pembelajaran == $r->id_pembelajaran) ? 'selected' : '' ?>>
                                <?php echo html_escape($r->nama_lembaga_singkat . ' — ' . $r->nama_tingkat . ' ' . $r->nama_rombel . ' (' . $r->tahun_pelajaran . ' Sem.' . $r->semester . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tombol Tampilkan -->
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-20">
                        <iconify-icon icon="solar:magnifer-bold"></iconify-icon>
                        Tampilkan
                    </button>
                    <?php if (!empty($siswa_list)): ?>
                        <button type="button" onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-16">
                            <iconify-icon icon="solar:printer-minimalistic-bold"></iconify-icon>
                            Cetak
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Result Table -->
    <?php if ($selected_pembelajaran && !empty($selected_dokumen_ids)): ?>

        <?php if (empty($siswa_list)): ?>
            <div class="result-card">
                <div class="result-card-header">
                    <iconify-icon icon="solar:users-group-rounded-bold" style="font-size:20px;"></iconify-icon>
                    <h6>Hasil Verifikasi</h6>
                </div>
                <div class="p-40 text-center">
                    <iconify-icon icon="solar:inbox-unread-bold" style="font-size:48px;color:#d1d5db;"></iconify-icon>
                    <p class="text-secondary-light mt-3">Tidak ada siswa aktif ditemukan di rombel ini.</p>
                </div>
            </div>

        <?php else:
            // Hitung statistik per dokumen
            $total_siswa = count($siswa_list);
            $doc_stats = [];
            foreach ($filtered_jenis as $jd) {
                $doc_stats[$jd->id_jenis_dokumen] = ['lengkap' => 0, 'kurang' => 0];
            }
            foreach ($siswa_list as $s) {
                foreach ($filtered_jenis as $jd) {
                    if (!empty($s->dokumen_status[$jd->id_jenis_dokumen])) {
                        $doc_stats[$jd->id_jenis_dokumen]['lengkap']++;
                    } else {
                        $doc_stats[$jd->id_jenis_dokumen]['kurang']++;
                    }
                }
            }

            // Temukan nama rombel yang dipilih
            $selected_rombel_label = '';
            foreach ($daftar_rombel as $r) {
                if ($r->id_pembelajaran == $selected_pembelajaran) {
                    $selected_rombel_label = $r->nama_lembaga_singkat . ' — ' . $r->nama_tingkat . ' ' . $r->nama_rombel . ' (' . $r->tahun_pelajaran . ' Sem.' . $r->semester . ')';
                    break;
                }
            }
        ?>

        <div class="result-card">
            <div class="result-card-header">
                <iconify-icon icon="solar:checklist-minimalistic-bold" style="font-size:20px;"></iconify-icon>
                <h6>Hasil Verifikasi</h6>
                <span class="badge-count"><?php echo $total_siswa ?> Siswa</span>
                <span style="font-size:.82rem;opacity:.8;margin-left:4px;"><?php echo html_escape($selected_rombel_label) ?></span>
            </div>

            <div class="table-sticky-wrap">
                <table class="verif-table">
                    <thead>
                        <tr>
                            <th style="min-width:36px;">#</th>
                            <th style="min-width:200px;">Nama Siswa</th>
                            <th style="min-width:110px;">NISN</th>
                            <?php foreach ($filtered_jenis as $jd): ?>
                                <th style="min-width:110px;">
                                    <?php echo html_escape($jd->nama_jenis_dokumen) ?>
                                    <span class="col-stat">
                                        <span style="color:#16a34a;"><?php echo $doc_stats[$jd->id_jenis_dokumen]['lengkap'] ?></span>
                                        /
                                        <span style="color:#dc2626;"><?php echo $doc_stats[$jd->id_jenis_dokumen]['kurang'] ?></span>
                                    </span>
                                </th>
                            <?php endforeach; ?>
                            <th style="min-width:80px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_list as $no => $s):
                            $total_lengkap = count(array_filter($s->dokumen_status));
                            $total_dok = count($filtered_jenis);
                            $all_complete = ($total_lengkap === $total_dok);
                        ?>
                        <tr>
                            <td>
                                <span class="verif-no-badge"><?php echo $no + 1 ?></span>
                            </td>
                            <td>
                                <a href="<?php echo url('siswa/detail/' . $s->id_siswa) ?>" target="_blank"
                                    class="text-primary-600 fw-semibold text-decoration-none"
                                    title="Buka detail siswa">
                                    <?php echo html_escape($s->nama_siswa) ?>
                                    <iconify-icon icon="solar:arrow-right-up-linear" style="font-size:11px;opacity:.6;"></iconify-icon>
                                </a>
                            </td>
                            <td>
                                <span class="no-siswa"><?php echo html_escape($s->nisn ?: '-') ?></span>
                            </td>
                            <?php foreach ($filtered_jenis as $jd): ?>
                                <td>
                                    <?php if (!empty($s->dokumen_status[$jd->id_jenis_dokumen])): ?>
                                        <iconify-icon icon="solar:check-circle-bold" class="status-check"
                                            title="Sudah diupload"></iconify-icon>
                                    <?php else: ?>
                                        <iconify-icon icon="solar:close-circle-bold" class="status-x"
                                            title="Belum diupload"></iconify-icon>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <?php if ($all_complete): ?>
                                    <span class="badge bg-success-100 text-success-600 radius-4 fw-semibold px-10">
                                        <?php echo $total_lengkap ?>/<?php echo $total_dok ?> Lengkap
                                    </span>
                                <?php elseif ($total_lengkap === 0): ?>
                                    <span class="badge bg-danger-100 text-danger-600 radius-4 fw-semibold px-10">
                                        0/<?php echo $total_dok ?> Kosong
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-100 text-warning-600 radius-4 fw-semibold px-10">
                                        <?php echo $total_lengkap ?>/<?php echo $total_dok ?> Sebagian
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan -->
            <div class="stat-bar">
                <?php
                    $grand_lengkap = 0; $grand_kurang = 0;
                    foreach ($doc_stats as $st) {
                        $grand_lengkap += $st['lengkap'];
                        $grand_kurang  += $st['kurang'];
                    }
                    $grand_total = $grand_lengkap + $grand_kurang;
                    $pct = $grand_total > 0 ? round($grand_lengkap / $grand_total * 100) : 0;
                ?>
                <div class="stat-item">
                    <span class="stat-dot green"></span>
                    <strong><?php echo $grand_lengkap ?></strong> berkas tersedia
                </div>
                <div class="stat-item">
                    <span class="stat-dot red"></span>
                    <strong><?php echo $grand_kurang ?></strong> berkas belum diupload
                </div>
                <div class="stat-item" style="margin-left:auto;">
                    <div style="width:120px;height:8px;background:#e2e8f0;border-radius:50px;overflow:hidden;">
                        <div style="width:<?php echo $pct ?>%;height:100%;background:linear-gradient(90deg,#16a34a,#4ade80);border-radius:50px;"></div>
                    </div>
                    <span style="font-weight:700;color:#1e3a8a;"><?php echo $pct ?>% Lengkap</span>
                </div>
            </div>
        </div>

        <?php endif; ?>

    <?php elseif ($selected_pembelajaran || !empty($selected_dokumen_ids)): ?>
        <!-- Pesan jika filter belum lengkap -->
        <div class="result-card">
            <div class="p-32 text-center">
                <iconify-icon icon="solar:filter-bold-duotone" style="font-size:48px;color:#93c5fd;"></iconify-icon>
                <p class="text-secondary-light mt-3 mb-0">
                    <?php if (empty($selected_dokumen_ids)): ?>
                        Pilih minimal satu jenis dokumen untuk diverifikasi.
                    <?php else: ?>
                        Pilih rombel untuk menampilkan data siswa.
                    <?php endif; ?>
                </p>
            </div>
        </div>

    <?php else: ?>
        <!-- Instruksi awal -->
        <div class="result-card">
            <div class="p-40 text-center">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <iconify-icon icon="solar:checklist-minimalistic-bold" style="font-size:36px;color:#2563eb;"></iconify-icon>
                </div>
                <h6 class="text-primary-900 fw-bold mb-8">Cara Menggunakan</h6>
                <ol class="text-start text-sm text-secondary-light d-inline-block" style="max-width:420px;">
                    <li class="mb-6">Pilih jenis dokumen yang ingin diverifikasi (mis. Ijazah SD, KK, KTP, dll)</li>
                    <li class="mb-6">Pilih rombel dari daftar pembelajaran aktif</li>
                    <li>Klik <strong>Tampilkan</strong> — tabel ceklis kelengkapan berkas akan muncul</li>
                </ol>
            </div>
        </div>
    <?php endif; ?>

</div><!-- end dashboard-main-body -->

<script>
function syncChip(checkbox) {
    const label = checkbox.closest('.doc-chip');
    const checkIcon = label.querySelector('.chip-check');
    const docIcon   = label.querySelector('.chip-icon-doc');

    if (checkbox.checked) {
        label.classList.add('selected');
        if (checkIcon) checkIcon.style.display = 'inline-flex';
        if (docIcon)   docIcon.style.display   = 'none';
    } else {
        label.classList.remove('selected');
        if (checkIcon) checkIcon.style.display = 'none';
        if (docIcon)   docIcon.style.display   = '';
    }
}

function selectAllChips() {
    document.querySelectorAll('#chipWrap input[type="checkbox"]').forEach(cb => {
        if (!cb.checked) { cb.checked = true; syncChip(cb); }
    });
}

function clearAllChips() {
    document.querySelectorAll('#chipWrap input[type="checkbox"]').forEach(cb => {
        if (cb.checked) { cb.checked = false; syncChip(cb); }
    });
}
</script>

<?php include viewPath('includes/footer'); ?>
