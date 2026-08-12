<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">

            <!-- Info PTK / User -->
            <div class="card mb-24">
                <div class="card-header bg-primary-600 d-flex align-items-center gap-3">
                    <iconify-icon icon="mdi:shield-account" class="text-white text-xl"></iconify-icon>
                    <h6 class="text-white mb-0">Hak Akses Individual — <?php echo htmlspecialchars($ptk_data->nama_ptk) ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width: 140px;">Nama PTK</td>
                                    <td style="width: 10px;">:</td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($ptk_data->nama_ptk) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">NIY / NIP</td>
                                    <td>:</td>
                                    <td><?php echo $ptk_data->niy ?: ($ptk_data->nip ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Jenis PTK</td>
                                    <td>:</td>
                                    <td><?php echo $ptk_data->status_pegawai ?: '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width: 140px;">Username</td>
                                    <td style="width: 10px;">:</td>
                                    <td><code><?php echo htmlspecialchars($user_data->username) ?></code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Role Utama</td>
                                    <td>:</td>
                                    <td>
                                        <?php foreach ($role_names as $rn): ?>
                                            <span class="badge bg-info-focus text-info-main px-10 py-4 radius-4"><?php echo htmlspecialchars($rn) ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Hak Akses Tambahan</td>
                                    <td>:</td>
                                    <td>
                                        <?php if (count($user_permissions) > 0): ?>
                                            <span class="badge bg-success-focus text-success-main px-10 py-4 radius-4"><?php echo count($user_permissions) ?> permission individual</span>
                                        <?php else: ?>
                                            <span class="badge bg-neutral-focus text-neutral-main px-10 py-4 radius-4">Belum ada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Hak Akses -->
            <div class="card">
                <div class="card-header bg-base d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="ri-shield-keyhole-line"></i> Konfigurasi Hak Akses Individual</h6>
                    <div>
                        <a href="<?php echo url('ptk/ptk') ?>" class="btn btn-sm btn-outline-secondary radius-6 px-12 py-6">
                            <i class="ri-arrow-left-line"></i> Kembali
                        </a>
                    </div>
                </div>

                <?php echo form_open('ptk/save_hak_akses/' . $ptk_data->id_ptk, ['class' => 'form-validate']); ?>
                <div class="card-body">

                    <div class="alert alert-info-600 d-flex align-items-start gap-2 mb-24 py-10 px-16 radius-8" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                        <iconify-icon icon="mdi:information-outline" class="text-info-600 text-xl mt-1 flex-shrink-0"></iconify-icon>
                        <div class="text-sm text-neutral-800">
                            <strong>Petunjuk:</strong> Permission yang sudah dimiliki dari <strong>Role bawaan</strong> ditandai dengan badge <span class="badge bg-primary-focus text-primary-main px-6 py-2 radius-4" style="font-size:10px;">Bawaan Role</span> dan otomatis terceklis (tidak bisa diubah dari sini, kelola di menu Role).
                            <br>Centang permission tambahan yang ingin diberikan secara khusus untuk PTK ini.
                        </div>
                    </div>

                    <style>
                        .permission-tree {
                            list-style: none;
                            padding-left: 0;
                        }
                        .permission-tree ul {
                            list-style: none;
                            padding-left: 28px;
                            border-left: 1px dashed #cbd5e1;
                            margin-top: 8px;
                            margin-bottom: 8px;
                            position: relative;
                        }
                        .permission-tree li {
                            margin-bottom: 12px;
                            position: relative;
                        }
                        .permission-tree li::before {
                            content: "";
                            position: absolute;
                            left: -28px;
                            top: 12px;
                            width: 20px;
                            height: 1px;
                            border-top: 1px dashed #cbd5e1;
                        }
                        .permission-tree > li::before {
                            display: none;
                        }
                        .level-1-title {
                            font-size: 15px;
                            font-weight: 700;
                            color: #1e293b;
                            background: #f1f5f9;
                            padding: 10px 16px;
                            border-radius: 8px;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            gap: 8px;
                            border: 1px solid #e2e8f0;
                        }
                        .level-2-title {
                            font-size: 14px;
                            font-weight: 600;
                            color: #334155;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            gap: 8px;
                            padding: 4px 0;
                        }
                        .level-3-box {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 8px;
                            padding: 10px 14px;
                            background: #f8fafc;
                            border: 1px solid #e2e8f0;
                            border-radius: 8px;
                            margin-top: 6px;
                            margin-bottom: 6px;
                        }
                        .level-3-item {
                            font-size: 12px;
                            color: #475569;
                            background: #ffffff;
                            border: 1px solid #cbd5e1;
                            padding: 5px 12px;
                            border-radius: 6px;
                            display: inline-flex;
                            align-items: center;
                            gap: 6px;
                            transition: all 0.2s;
                            cursor: pointer;
                        }
                        .level-3-item:hover {
                            border-color: #94a3b8;
                            background: #f1f5f9;
                        }
                        .form-check-input {
                            cursor: pointer;
                        }
                        /* Gaya untuk permission bawaan role */
                        .role-inherited {
                            opacity: 0.7;
                        }
                        .role-inherited .form-check-input {
                            pointer-events: none;
                        }
                        .badge-role {
                            font-size: 9px;
                            padding: 2px 6px;
                            border-radius: 4px;
                            margin-left: 4px;
                        }
                    </style>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-warning-50 d-flex justify-content-between align-items-center py-2 px-3">
                                    <h6 class="text-warning-800 mb-0 fw-bold"><i class="ri-node-tree"></i> Daftar Permission (Hierarchy Tree)</h6>
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" id="btnSelectAllTree" class="btn btn-xs btn-primary radius-6 px-10 py-4 text-xs">Pilih Semua</button>
                                        <button type="button" id="btnDeselectAllTree" class="btn btn-xs btn-outline-secondary radius-6 px-10 py-4 text-xs">Kosongkan</button>
                                    </div>
                                </div>
                                <div class="card-body p-24" style="max-height: 700px; overflow-y: auto;">
                                    
                                    <?php
                                    // Ambil semua permissions secara dinamis dari database
                                    $raw_permissions = $this->db->order_by('id', 'asc')->get('permissions')->result();

                                    $permission_tree = [];
                                    $lookup = [];

                                    // Buat map index agar pencarian parent cepat
                                    foreach ($raw_permissions as $p) {
                                        $p->sub = [];
                                        $p->features = [];
                                        $lookup[$p->id] = $p;
                                    }

                                    // Susun struktur pohonnya (Level 1, 2, dan 3)
                                    foreach ($raw_permissions as $p) {
                                        if ($p->level == 1) {
                                            $permission_tree[$p->id] = $p;
                                        } elseif ($p->level == 2) {
                                            if (isset($lookup[$p->parent_id])) {
                                                $lookup[$p->parent_id]->sub[] = $p;
                                            } else {
                                                $p->level = 1;
                                                $permission_tree[$p->id] = $p;
                                            }
                                        } elseif ($p->level == 3) {
                                            if (isset($lookup[$p->parent_id])) {
                                                $lookup[$p->parent_id]->features[] = $p;
                                            } else {
                                                $p->level = 1;
                                                $permission_tree[$p->id] = $p;
                                            }
                                        }
                                    }
                                    ?>

                                    <ul class="permission-tree">
                                        <?php foreach ($permission_tree as $gId => $group): ?>
                                            <!-- LEVEL 1 -->
                                            <li class="group-container mb-20">
                                                <div class="level-1-title d-flex align-items-center justify-content-start gap-2">
                                                    <div class="form-check m-0 d-flex align-items-center gap-2">
                                                        <input type="checkbox" class="form-check-input check-lvl-1" id="group_<?php echo $gId ?>">
                                                        <label for="group_<?php echo $gId ?>" class="form-check-label text-sm fw-bold cursor-pointer mb-0">
                                                            <?php echo $group->title ?>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- LEVEL 2 -->
                                                <?php if (!empty($group->sub)): ?>
                                                    <ul>
                                                        <?php foreach ($group->sub as $sub): ?>
                                                            <li class="sub-container">
                                                                <div class="level-2-title d-flex align-items-center justify-content-start gap-2">
                                                                    <?php if (!empty($sub->code)): ?>
                                                                        <?php 
                                                                        $isRoleInherited = in_array($sub->code, $role_permissions);
                                                                        $isUserPerm = in_array($sub->code, $user_permissions);
                                                                        $isChecked = ($isRoleInherited || $isUserPerm) ? 'checked' : '';
                                                                        ?>
                                                                        <div class="form-check m-0 d-flex align-items-center gap-2 <?php echo $isRoleInherited ? 'role-inherited' : '' ?>">
                                                                            <?php if ($isRoleInherited): ?>
                                                                                <!-- Hidden input untuk tetap mengirim value bawaan role -->
                                                                                <input type="checkbox" class="form-check-input check-lvl-2" checked disabled id="sub_<?php echo $sub->id ?>">
                                                                            <?php else: ?>
                                                                                <input type="checkbox" class="form-check-input check-lvl-2" name="permission[]" value="<?php echo $sub->code ?>" <?php echo $isChecked ?> id="sub_<?php echo $sub->id ?>">
                                                                            <?php endif; ?>
                                                                            <label for="sub_<?php echo $sub->id ?>" class="form-check-label text-xs fw-semibold cursor-pointer mb-0">
                                                                                <?php echo $sub->title ?>
                                                                            </label>
                                                                            <?php if ($isRoleInherited): ?>
                                                                                <span class="badge bg-primary-focus text-primary-main badge-role">Bawaan Role</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <span class="text-xs fw-semibold text-secondary-light">
                                                                            <?php echo $sub->title ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <!-- LEVEL 3 -->
                                                                <?php if (!empty($sub->features)): ?>
                                                                    <div class="level-3-box">
                                                                        <?php foreach ($sub->features as $feat): ?>
                                                                            <?php 
                                                                            $isRoleInherited = in_array($feat->code, $role_permissions);
                                                                            $isUserPerm = in_array($feat->code, $user_permissions);
                                                                            $isChecked = ($isRoleInherited || $isUserPerm) ? 'checked' : '';
                                                                            ?>
                                                                            <label class="level-3-item m-0 <?php echo $isRoleInherited ? 'role-inherited' : '' ?>" for="feat_<?php echo $feat->id ?>">
                                                                                <?php if ($isRoleInherited): ?>
                                                                                    <input type="checkbox" class="form-check-input check-lvl-3 m-0" checked disabled id="feat_<?php echo $feat->id ?>">
                                                                                <?php else: ?>
                                                                                    <input type="checkbox" class="form-check-input check-lvl-3 m-0" name="permission[]" value="<?php echo $feat->code ?>" <?php echo $isChecked ?> id="feat_<?php echo $feat->id ?>">
                                                                                <?php endif; ?>
                                                                                <span><?php echo $feat->title ?></span>
                                                                                <?php if ($isRoleInherited): ?>
                                                                                    <span class="badge bg-primary-focus text-primary-main badge-role">Bawaan Role</span>
                                                                                <?php endif; ?>
                                                                            </label>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>

                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?php echo url('ptk/ptk') ?>" onclick="return confirm('Yakin ingin kembali? Perubahan yang belum disimpan akan hilang.')" class="btn btn-outline-danger radius-6 px-16">
                        <i class="ri-close-line"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-6 px-24">
                        <i class="ri-save-line"></i> Simpan Hak Akses
                    </button>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    // Klik Level 1 (Grup Utama) — hanya untuk yang bisa diedit (non role-inherited)
    $(document).on('change', '.check-lvl-1', function() {
        const isChecked = $(this).is(':checked');
        const container = $(this).closest('.group-container');
        container.find('.check-lvl-2:not(:disabled), .check-lvl-3:not(:disabled)').prop('checked', isChecked);
    });

    // Klik Level 2 (Submenu / Menu)
    $(document).on('change', '.check-lvl-2:not(:disabled)', function() {
        const isChecked = $(this).is(':checked');
        const container = $(this).closest('.sub-container');
        container.find('.check-lvl-3:not(:disabled)').prop('checked', isChecked);
        
        if (isChecked) {
            $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
        } else {
            const group = $(this).closest('.group-container');
            if (group.find('.check-lvl-2:checked').length === 0) {
                group.find('.check-lvl-1').prop('checked', false);
            }
        }
    });

    // Klik Level 3 (Fitur / Aksi)
    $(document).on('change', '.check-lvl-3:not(:disabled)', function() {
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            $(this).closest('.sub-container').find('.check-lvl-2:not(:disabled)').prop('checked', true);
            $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
        } else {
            const sub = $(this).closest('.sub-container');
            if (sub.find('.check-lvl-3:checked').length === 0) {
                sub.find('.check-lvl-2:not(:disabled)').prop('checked', false);
            }
            const group = $(this).closest('.group-container');
            if (group.find('.check-lvl-2:checked').length === 0) {
                group.find('.check-lvl-1').prop('checked', false);
            }
        }
    });

    // Tombol Pilih Semua (hanya yang bisa diedit)
    $('#btnSelectAllTree').on('click', function() {
        $('.check-lvl-1, .check-lvl-2:not(:disabled), .check-lvl-3:not(:disabled)').prop('checked', true);
    });

    // Tombol Kosongkan (hanya yang bisa diedit)
    $('#btnDeselectAllTree').on('click', function() {
        $('.check-lvl-2:not(:disabled), .check-lvl-3:not(:disabled)').prop('checked', false);
        // Re-check Level 1 jika masih ada yang checked (bawaan role)
        $('.group-container').each(function() {
            const group = $(this);
            if (group.find('.check-lvl-2:checked, .check-lvl-3:checked').length > 0) {
                group.find('.check-lvl-1').prop('checked', true);
            } else {
                group.find('.check-lvl-1').prop('checked', false);
            }
        });
    });

    // Sinkronisasi status checked awal saat halaman dimuat
    $('.group-container').each(function() {
        const group = $(this);
        if (group.find('.check-lvl-2:checked, .check-lvl-3:checked').length > 0) {
            group.find('.check-lvl-1').prop('checked', true);
        }
        
        group.find('.sub-container').each(function() {
            const sub = $(this);
            if (sub.find('.check-lvl-3:checked').length > 0) {
                sub.find('.check-lvl-2').prop('checked', true);
                group.find('.check-lvl-1').prop('checked', true);
            }
        });
    });
});
</script>
