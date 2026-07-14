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
              <label class="fw-bold mb-3"><?php echo lang('permissions') ?></label>
              <div class="row">
                <?php 
                $all_perms = $this->permissions_model->get();
                $menu_perms = [];
                $feature_perms = [];
                if (!empty($all_perms)) {
                    foreach ($all_perms as $p) {
                        if (strpos($p->code, 'menu_') === 0) {
                            $menu_perms[] = $p;
                        } else {
                            $feature_perms[] = $p;
                        }
                    }
                }
                ?>
                <!-- Tabel 1: Hak Akses Menu Sidebar -->
                <div class="col-md-6">
                  <div class="card border shadow-none mb-3">
                    <div class="card-header bg-info-50 d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="text-info-800 mb-0 fw-bold"><i class="ri-side-bar-line"></i> Hak Akses Menu Sidebar</h6>
                      <div class="form-check m-0">
                        <input type="checkbox" id="checkAllMenu" class="form-check-input">
                        <label for="checkAllMenu" class="form-check-label text-xs fw-bold cursor-pointer">Pilih Semua</label>
                      </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                      <table class="table table-bordered table-striped m-0">
                        <tbody>
                          <?php if (!empty($menu_perms)): ?>
                            <?php foreach ($menu_perms as $row): ?>
                              <tr>
                                <td><?php echo ucfirst(str_replace('_', ' ', $row->title)) ?></td>
                                <td class="text-center" width="80">
                                  <input type="checkbox" class="form-check-input check-menu-p" name="permission[]" value="<?php echo $row->code ?>">
                                </td>
                              </tr>
                            <?php endforeach ?>
                          <?php else: ?>
                            <tr>
                              <td class="text-center py-3 text-secondary">Tidak ada hak akses menu.</td>
                            </tr>
                          <?php endif ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Tabel 2: Hak Akses Fitur & Aksi -->
                <div class="col-md-6">
                  <div class="card border shadow-none mb-3">
                    <div class="card-header bg-success-50 d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="text-success-800 mb-0 fw-bold"><i class="ri-key-2-line"></i> Hak Akses Fitur & Aksi</h6>
                      <div class="form-check m-0">
                        <input type="checkbox" id="checkAllFeature" class="form-check-input">
                        <label for="checkAllFeature" class="form-check-label text-xs fw-bold cursor-pointer">Pilih Semua</label>
                      </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                      <table class="table table-bordered table-striped m-0">
                        <tbody>
                          <?php if (!empty($feature_perms)): ?>
                            <?php foreach ($feature_perms as $row): ?>
                              <tr>
                                <td><?php echo ucfirst(str_replace('_', ' ', $row->title)) ?></td>
                                <td class="text-center" width="80">
                                  <input type="checkbox" class="form-check-input check-feature-p" name="permission[]" value="<?php echo $row->code ?>">
                                </td>
                              </tr>
                            <?php endforeach ?>
                          <?php else: ?>
                            <tr>
                              <td class="text-center py-3 text-secondary">Tidak ada hak akses fitur.</td>
                            </tr>
                          <?php endif ?>
                        </tbody>
                      </table>
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

    $('#checkAllMenu').on('change', function() {
      $('.check-menu-p').prop('checked', $(this).is(':checked'));
    });

    $('#checkAllFeature').on('change', function() {
      $('.check-feature-p').prop('checked', $(this).is(':checked'));
    });

    $('.table-DT').DataTable({
      "ordering": false,
    });
  })
</script>

<?php include viewPath('includes/footer'); ?>

<script>
  //Initialize Select2 Elements
  $('.select2').select2()
</script>