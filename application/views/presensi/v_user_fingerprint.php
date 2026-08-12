<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <?php
    // Hitung Statistik Ringkasan Data Merged
    $cnt_total        = count($users);
    $cnt_siswa        = 0;
    $cnt_ptk          = 0;
    $cnt_mesin_only   = 0;
    $cnt_terdaftar    = 0;
    $cnt_belum_reg    = 0;
    $cnt_belum_isi_fp = 0;

    foreach ($users as $u) {
        if ($u->tipe_user === 'Siswa')      $cnt_siswa++;
        if ($u->tipe_user === 'PTK / Guru') $cnt_ptk++;
        if ($u->tipe_user === 'User Mesin Saja') $cnt_mesin_only++;

        if ($u->in_machine) {
            $cnt_terdaftar++;
            $u_tmpls = $templates_by_pin[(string)$u->pin] ?? [];
            $valid_fp = array_filter($u_tmpls, function($t) { return (int)$t->finger_idx !== 10; });
            if (count($valid_fp) === 0) {
                $cnt_belum_isi_fp++;
            }
        } else {
            $cnt_belum_reg++;
        }
    }
    ?>

    <div class="row gy-3 mb-24">
        <!-- Stat 1: Total Pengguna -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Total Database Pengguna</span>
                        <h4 class="mb-0 mt-4 text-primary-600 fw-bold"><?php echo number_format($cnt_total); ?> User</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Siswa: <strong><?php echo $cnt_siswa; ?></strong> | PTK: <strong><?php echo $cnt_ptk; ?></strong> | Mesin: <strong><?php echo $cnt_mesin_only; ?></strong>
                </div>
            </div>
        </div>

        <!-- Stat 2: Terdaftar di Mesin -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Terdaftar di Mesin</span>
                        <h4 class="mb-0 mt-4 text-success-600 fw-bold"><?php echo number_format($cnt_terdaftar); ?> User</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-success-50 text-success-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Pengguna terdaftar di memori mesin EasyLink
                </div>
            </div>
        </div>

        <!-- Stat 3: Belum Teregistrasi -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Belum Teregistrasi ke Mesin</span>
                        <h4 class="mb-0 mt-4 text-warning-600 fw-bold"><?php echo number_format($cnt_belum_reg); ?> User</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-warning-50 text-warning-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:danger-triangle-bold"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Siswa / PTK aktif yang belum dimasukkan ke mesin
                </div>
            </div>
        </div>

        <!-- Stat 4: Belum Mengisi FP (Idx 10) -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-20 shadow-sm border-0 radius-12 bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light text-sm d-block fw-medium">Belum Mengisi FP (Idx 10)</span>
                        <h4 class="mb-0 mt-4 text-danger-600 fw-bold"><?php echo number_format($cnt_belum_isi_fp); ?> User</h4>
                    </div>
                    <div class="w-48-px h-48-px radius-8 bg-danger-50 text-danger-600 d-flex align-items-center justify-content-center text-2xl">
                        <iconify-icon icon="solar:fingerprint-remove-bold"></iconify-icon>
                    </div>
                </div>
                <div class="mt-12 text-xs text-muted">
                    Pengguna di mesin tanpa sidik jari asli
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline radius-12 shadow-sm border-0 mb-24">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-users-cog text-primary mr-2"></i> Integrasi Data Pengguna Mesin Fingerprint, Siswa, &amp; PTK
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge px-12 py-6 radius-8 font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important; font-size: 13px;">
                    PIN = NIPD (Siswa) &amp; NIY (PTK)
                </span>
            </div>
        </div>

        <div class="card-body">
            <!-- Notification Banner -->
            <div class="alert alert-info border-info bg-light text-dark mb-3 p-3 radius-8">
                <i class="fas fa-info-circle text-info mr-2"></i>
                <strong>Informasi Integrasi Data Pengguna:</strong>
                <ul class="mb-0 mt-1 pl-3 text-sm">
                    <li><strong>Siswa Aktif:</strong> Menggunakan <code>NIPD</code> atau <code>PIN Fingerprint</code> sebagai PIN mesin.</li>
                    <li><strong>PTK / Guru Aktif:</strong> Menggunakan <code>NIY</code> atau <code>PIN Fingerprint</code> sebagai PIN mesin.</li>
                    <li><strong>Belum Teregistrasi ke Mesin:</strong> Menandakan data Siswa / PTK ada di server database sekolah, namun belum ada record penggunanya di mesin.</li>
                    <li><strong>Finger Index = 10:</strong> Menandakan pengguna terdaftar di mesin namun <strong>belum merekam / mengisi sidik jari asli</strong>.</li>
                </ul>
            </div>

            <!-- Quick Filter Buttons -->
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <span class="fw-bold text-sm text-secondary-light me-1"><i class="fas fa-filter mr-1"></i> Filter Tampilan:</span>
                <button type="button" class="btn btn-sm btn-outline-primary active filter-btn" onclick="filterUserTable('all', this)">Semua (<?php echo $cnt_total; ?>)</button>
                <button type="button" class="btn btn-sm btn-outline-info filter-btn" onclick="filterUserTable('Siswa', this)">Siswa (<?php echo $cnt_siswa; ?>)</button>
                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" onclick="filterUserTable('PTK / Guru', this)">PTK / Guru (<?php echo $cnt_ptk; ?>)</button>
                <button type="button" class="btn btn-sm btn-outline-success filter-btn" onclick="filterUserTable('terdaftar', this)">Terdaftar di Mesin (<?php echo $cnt_terdaftar; ?>)</button>
                <button type="button" class="btn btn-sm btn-outline-warning filter-btn" onclick="filterUserTable('belum_reg', this)">Belum Teregistrasi (<?php echo $cnt_belum_reg; ?>)</button>
                <button type="button" class="btn btn-sm btn-outline-danger filter-btn" onclick="filterUserTable('belum_isi_fp', this)">Belum Mengisi FP (<?php echo $cnt_belum_isi_fp; ?>)</button>
            </div>

            <div class="table-responsive">
                <table id="tbl_user_fp" class="table table-bordered table-striped table-hover align-middle" data-page-length="25">
                    <thead class="bg-light">
                        <tr>
                            <th width="45" class="text-center">No</th>
                            <th width="150">PIN (NIPD / NIY)</th>
                            <th>Nama Pengguna</th>
                            <th width="180" class="text-center">Nama Rombel &amp; Tingkat</th>
                            <th width="180" class="text-center">Status Registrasi Mesin</th>
                            <th width="180" class="text-center">Jumlah Sidik Jari</th>
                            <th width="110" class="text-center">Privilege</th>
                            <th width="120" class="text-center">Aksi / Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php $no = 1; foreach ($users as $u): ?>
                                <?php 
                                    $user_tmpls = $templates_by_pin[(string)$u->pin] ?? []; 
                                    $valid_fp_tmpls = array_filter($user_tmpls, function($t) {
                                        return (int)$t->finger_idx !== 10;
                                    });
                                    $has_idx_10 = false;
                                    foreach ($user_tmpls as $t) {
                                        if ((int)$t->finger_idx === 10) {
                                            $has_idx_10 = true;
                                            break;
                                        }
                                    }
                                    $valid_fp_count = count($valid_fp_tmpls);

                                    // Data Filter Class Tags
                                    $filter_tags = [];
                                    if ($u->tipe_user === 'Siswa') $filter_tags[] = 'tag-Siswa';
                                    if ($u->tipe_user === 'PTK / Guru') $filter_tags[] = 'tag-PTK';
                                    if ($u->in_machine) {
                                        $filter_tags[] = 'tag-terdaftar';
                                        if ($valid_fp_count === 0 && $has_idx_10) {
                                            $filter_tags[] = 'tag-belum_isi_fp';
                                        }
                                    } else {
                                        $filter_tags[] = 'tag-belum_reg';
                                    }
                                    $filter_class = implode(' ', $filter_tags);
                                ?>
                                <tr class="user-row <?php echo $filter_class; ?>">
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td>
                                        <strong><code><?php echo htmlspecialchars($u->pin); ?></code></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($u->nama); ?></strong>
                                        <?php if (!empty($u->nama_mesin) && strcasecmp($u->nama, $u->nama_mesin) !== 0): ?>
                                            <div class="text-xs text-muted mt-1">
                                                <i class="fas fa-desktop mr-1"></i> Nama Mesin: <code><?php echo htmlspecialchars($u->nama_mesin); ?></code>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u->tipe_user === 'Siswa'): ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #e0f2fe; color: #0369a1 !important; border: 1px solid #bae6fd;">
                                                <i class="fas fa-user-graduate mr-1"></i> <?php echo htmlspecialchars($u->rombel_tingkat ?? '-'); ?>
                                            </span>
                                        <?php elseif ($u->tipe_user === 'PTK / Guru'): ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #f3e8ff; color: #6b21a8 !important; border: 1px solid #e9d5ff;">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i> PTK
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #334155 !important; border: 1px solid #e2e8f0;">
                                                <i class="fas fa-desktop mr-1"></i> Mesin Saja
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u->in_machine): ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #dcfce7; color: #15803d !important; border: 1px solid #bbf7d0;">
                                                <i class="fas fa-check-circle mr-1"></i> Terdaftar di Mesin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #fef3c7; color: #b45309 !important; border: 1px solid #fde68a;">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Belum Teregistrasi ke Mesin
                                            </span>
                                            <div class="text-xs text-danger mt-1">
                                                (Siswa/PTK Tidak Ada di Mesin)
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u->in_machine): ?>
                                            <?php if ($valid_fp_count > 0): ?>
                                                <span class="badge px-12 py-6 radius-4 text-xs font-semibold" style="background-color: #dcfce7; color: #15803d !important; border: 1px solid #bbf7d0;">
                                                    <i class="fas fa-fingerprint mr-1"></i> <?php echo $valid_fp_count; ?> Sidik Jari
                                                </span>
                                                <?php if ($has_idx_10): ?>
                                                    <span class="badge px-8 py-4 radius-4 text-xs mt-1 d-block font-semibold" style="background-color: #fef9c3; color: #854d0e !important; border: 1px solid #fef08a;" title="Terdeteksi Finger Index 10">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Ada Idx 10
                                                    </span>
                                                <?php endif; ?>
                                            <?php elseif ($has_idx_10): ?>
                                                <span class="badge px-12 py-6 radius-4 text-xs font-semibold" style="background-color: #fee2e2; color: #b91c1c !important; border: 1px solid #fecaca;">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> Belum Mengisi Fingerprint
                                                </span>
                                                <span class="badge px-8 py-4 radius-4 text-xs mt-1 d-block font-semibold" style="background-color: #fef9c3; color: #854d0e !important; border: 1px solid #fef08a;">
                                                    <i class="fas fa-info-circle mr-1"></i> (Finger Index: 10)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #475569 !important; border: 1px solid #e2e8f0;">
                                                    <i class="fas fa-times mr-1"></i> 0 Template
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #fee2e2; color: #b91c1c !important; border: 1px solid #fecaca;">
                                                <i class="fas fa-user-slash mr-1"></i> Belum Mengisi FP
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u->privilege == 1 || $u->privilege == 3): ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #fef9c3; color: #854d0e !important; border: 1px solid #fef08a;">
                                                <i class="fas fa-user-shield mr-1"></i> Admin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-10 py-6 radius-4 text-xs font-semibold" style="background-color: #f1f5f9; color: #334155 !important; border: 1px solid #e2e8f0;">
                                                <i class="fas fa-user mr-1"></i> User
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u->in_machine): ?>
                                            <button type="button" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 font-bold" data-toggle="modal" data-target="#modal_tmpl_<?php echo md5($u->pin); ?>">
                                                <i class="fas fa-eye mr-1"></i> Detail FP
                                            </button>

                                            <!-- Modal Detail Sidik Jari -->
                                            <div class="modal fade" id="modal_tmpl_<?php echo md5($u->pin); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title font-weight-bold">
                                                                <i class="fas fa-fingerprint mr-2"></i> Detail Template Sidik Jari - PIN <?php echo htmlspecialchars($u->pin); ?> (<?php echo htmlspecialchars($u->nama); ?>)
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php if (!empty($user_tmpls)): ?>
                                                                <div class="accordion" id="acc_<?php echo md5($u->pin); ?>">
                                                                    <?php foreach ($user_tmpls as $idx => $t): ?>
                                                                        <?php $is_idx_10 = ((int)$t->finger_idx === 10); ?>
                                                                        <div class="card mb-2 border <?php echo $is_idx_10 ? 'border-danger' : ''; ?>">
                                                                            <div class="card-header <?php echo $is_idx_10 ? 'bg-warning-light' : 'bg-light'; ?> p-2" id="heading_<?php echo $t->id; ?>">
                                                                                <h6 class="mb-0">
                                                                                    <button class="btn btn-link <?php echo $is_idx_10 ? 'text-danger' : 'text-primary'; ?> font-weight-bold text-left btn-block" type="button" data-toggle="collapse" data-target="#collapse_<?php echo $t->id; ?>">
                                                                                        <i class="fas fa-fingerprint mr-2"></i> Finger Index: <?php echo $t->finger_idx; ?>
                                                                                        <?php if ($is_idx_10): ?>
                                                                                            <span class="badge px-8 py-4 radius-4 text-xs font-semibold ml-2" style="background-color: #fee2e2; color: #b91c1c !important;">
                                                                                                <i class="fas fa-times-circle mr-1"></i> Belum Mengisi Fingerprint
                                                                                            </span>
                                                                                        <?php endif; ?>
                                                                                        | Algoritma: VX<?php echo $t->alg_ver; ?>
                                                                                        <span class="float-right badge <?php echo $is_idx_10 ? 'badge-danger' : 'badge-primary'; ?>">Template ID #<?php echo $t->id; ?></span>
                                                                                    </button>
                                                                                </h6>
                                                                            </div>
                                                                            <div id="collapse_<?php echo $t->id; ?>" class="collapse <?php echo ($idx === 0) ? 'show' : ''; ?>" data-parent="#acc_<?php echo md5($u->pin); ?>">
                                                                                <div class="card-body p-3 bg-dark">
                                                                                    <?php if ($is_idx_10): ?>
                                                                                        <div class="alert alert-danger p-2 mb-2 small">
                                                                                            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Keterangan:</strong> Template ini memiliki <code>Finger Index = 10</code>, yang menandakan pengguna ini <strong>belum merekam / belum mengisi sidik jari asli</strong> di mesin.
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                    <label class="text-warning small mb-1">RAW Base64 Fingerprint Template:</label>
                                                                                    <textarea class="form-control form-control-sm text-monospace bg-black text-success border-secondary" rows="4" readonly><?php echo htmlspecialchars($t->template); ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-center py-4 text-muted">
                                                                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                                                                    Belum ada template sidik jari tersimpan untuk pengguna ini di database.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal -->
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fas fa-ban mr-1"></i> Tidak Ada di Mesin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i><br>
                                    Belum ada data pengguna yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
var activeFilterType = 'all';
var userFpTable = null;

$(document).ready(function() {
    if (typeof $.fn !== 'undefined' && $.fn.dataTable) {
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'tbl_user_fp') {
                    return true;
                }
                if (activeFilterType === 'all') {
                    return true;
                }
                
                var rowNode = settings.aoData[dataIndex].nTr;
                if (!rowNode) return true;

                if (activeFilterType === 'Siswa') {
                    return rowNode.classList.contains('tag-Siswa');
                } else if (activeFilterType === 'PTK / Guru') {
                    return rowNode.classList.contains('tag-PTK');
                } else if (activeFilterType === 'terdaftar') {
                    return rowNode.classList.contains('tag-terdaftar');
                } else if (activeFilterType === 'belum_reg') {
                    return rowNode.classList.contains('tag-belum_reg');
                } else if (activeFilterType === 'belum_isi_fp') {
                    return rowNode.classList.contains('tag-belum_isi_fp');
                }
                return true;
            }
        );
    }

    if ($('#tbl_user_fp').length) {
        userFpTable = $('#tbl_user_fp').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'asc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }
});

function filterUserTable(type, btn) {
    var buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(b => b.classList.remove('active', 'btn-primary', 'btn-info', 'btn-secondary', 'btn-success', 'btn-warning', 'btn-danger'));
    buttons.forEach(b => b.classList.add('btn-outline-secondary'));

    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('active', 'btn-primary');

    activeFilterType = type;
    if (userFpTable) {
        userFpTable.draw();
    } else {
        var rows = document.querySelectorAll('.user-row');
        rows.forEach(r => {
            if (type === 'all') {
                r.style.display = '';
            } else if (type === 'Siswa') {
                r.style.display = r.classList.contains('tag-Siswa') ? '' : 'none';
            } else if (type === 'PTK / Guru') {
                r.style.display = r.classList.contains('tag-PTK') ? '' : 'none';
            } else if (type === 'terdaftar') {
                r.style.display = r.classList.contains('tag-terdaftar') ? '' : 'none';
            } else if (type === 'belum_reg') {
                r.style.display = r.classList.contains('tag-belum_reg') ? '' : 'none';
            } else if (type === 'belum_isi_fp') {
                r.style.display = r.classList.contains('tag-belum_isi_fp') ? '' : 'none';
            }
        });
    }
}
</script>
