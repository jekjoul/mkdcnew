<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>


<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <section class="content">
        <!-- Default card -->
        <div class="card">
          <div class="card-header with-border">
            <h3 class="card-title">Tambah Role</h3>
          </div>

          <?php echo form_open('roles/save', ['class' => 'form-validate']); ?>
          <div class="card-body">

            <div class="form-group">
              <label for="formClient-Name"><?php echo lang('role_name') ?></label>
              <input type="text" class="form-control" name="name" id="formClient-Name" required placeholder="<?php echo lang('role_name') ?>" autofocus />
            </div>

            <div class="form-group">
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
              </style>

              <div class="row">
                <div class="col-md-12">
                  <div class="card border shadow-none mb-3">
                    <div class="card-header bg-danger-50 d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="text-danger-800 mb-0 fw-bold"><i class="ri-node-tree"></i> Konfigurasi Hak Akses Role (Hierarchy Tree)</h6>
                      <div class="d-flex align-items-center gap-3">
                        <button type="button" id="btnSelectAllTree" class="btn btn-xs btn-primary radius-6 px-10 py-4 text-xs">Pilih Semua</button>
                        <button type="button" id="btnDeselectAllTree" class="btn btn-xs btn-outline-secondary radius-6 px-10 py-4 text-xs">Kosongkan</button>
                      </div>
                    </div>
                    <div class="card-body p-24" style="max-height: 700px; overflow-y: auto;">
                      
                      <?php
                      // Definisikan role_permissions kosong untuk mode tambah role
                      $role_permissions = [];

                      // 1. Ambil semua permissions secara dinamis dari database
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
                                  // Fallback ke Level 1 jika parent tidak ditemukan
                                  $p->level = 1;
                                  $permission_tree[$p->id] = $p;
                              }
                          } elseif ($p->level == 3) {
                              if (isset($lookup[$p->parent_id])) {
                                  $lookup[$p->parent_id]->features[] = $p;
                              } else {
                                  // Fallback ke Level 1 jika parent tidak ditemukan
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
                                        <?php $isChecked = in_array($sub->code, $role_permissions) ? 'checked' : ''; ?>
                                        <div class="form-check m-0 d-flex align-items-center gap-2">
                                          <input type="checkbox" class="form-check-input check-lvl-2" name="permission[]" value="<?php echo $sub->code ?>" <?php echo $isChecked ?> id="sub_<?php echo $sub->id ?>">
                                          <label for="sub_<?php echo $sub->id ?>" class="form-check-label text-xs fw-semibold cursor-pointer mb-0">
                                            <?php echo $sub->title ?>
                                          </label>
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
                                          <?php $isChecked = in_array($feat->code, $role_permissions) ? 'checked' : ''; ?>
                                          <label class="level-3-item m-0" for="feat_<?php echo $feat->id ?>">
                                            <input type="checkbox" class="form-check-input check-lvl-3 m-0" name="permission[]" value="<?php echo $feat->code ?>" <?php echo $isChecked ?> id="feat_<?php echo $feat->id ?>">
                                            <span><?php echo $feat->title ?></span>
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

          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <div class="row">
              <div class="col"><a href="<?php echo url('/roles') ?>" onclick="return confirm('Are you sure you want to leave?')" class="btn btn-flat btn-danger"><?php echo lang('cancel') ?></a></div>
              <div class="col text-right"><button type="submit" class="btn btn-flat btn-primary"><?php echo lang('submit') ?></button></div>
            </div>
          </div>
          <!-- /.card-footer-->

          <?php echo form_close(); ?>

        </div>
        <!-- /.card -->

      </section>
      <!-- /.content -->
    </div>
  </div>
</div>



<script>
  $(document).ready(function() {
    $('.form-validate').validate({
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });

    // Klik Level 1 (Grup Utama)
    $(document).on('change', '.check-lvl-1', function() {
      const isChecked = $(this).is(':checked');
      const container = $(this).closest('.group-container');
      container.find('.check-lvl-2, .check-lvl-3').prop('checked', isChecked);
    });

    // Klik Level 2 (Submenu / Menu)
    $(document).on('change', '.check-lvl-2', function() {
      const isChecked = $(this).is(':checked');
      const container = $(this).closest('.sub-container');
      container.find('.check-lvl-3').prop('checked', isChecked);
      
      if (isChecked) {
        $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
      } else {
        // Uncheck Level 1 jika seluruh Level 2 di grup tersebut kosong
        const group = $(this).closest('.group-container');
        if (group.find('.check-lvl-2:checked').length === 0) {
          group.find('.check-lvl-1').prop('checked', false);
        }
      }
    });

    // Klik Level 3 (Fitur / Aksi)
    $(document).on('change', '.check-lvl-3', function() {
      const isChecked = $(this).is(':checked');
      
      if (isChecked) {
        $(this).closest('.sub-container').find('.check-lvl-2').prop('checked', true);
        $(this).closest('.group-container').find('.check-lvl-1').prop('checked', true);
      } else {
        // Uncheck Level 2 jika seluruh Level 3 di submenu tersebut kosong
        const sub = $(this).closest('.sub-container');
        if (sub.find('.check-lvl-3:checked').length === 0) {
          sub.find('.check-lvl-2').prop('checked', false);
        }
        // Uncheck Level 1 jika seluruh Level 2 di grup tersebut kosong
        const group = $(this).closest('.group-container');
        if (group.find('.check-lvl-2:checked').length === 0) {
          group.find('.check-lvl-1').prop('checked', false);
        }
      }
    });

    // Tombol Pilih Semua
    $('#btnSelectAllTree').on('click', function() {
      $('.check-lvl-1, .check-lvl-2, .check-lvl-3').prop('checked', true);
    });

    // Tombol Kosongkan
    $('#btnDeselectAllTree').on('click', function() {
      $('.check-lvl-1, .check-lvl-2, .check-lvl-3').prop('checked', false);
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
  })
</script>

<?php include viewPath('includes/footer'); ?>

<script>
  //Initialize Select2 Elements
  $('.select2').select2()
</script>