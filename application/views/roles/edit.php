<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>



<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <!-- Main content -->
      <section class="content">

        <!-- Default card -->
        <div class="card">
          <div class="card-header with-border">
            <h3 class="card-title">Edit Role</h3>
          </div>

          <?php echo form_open('roles/update/' . $role->id, ['class' => 'form-validate']); ?>
          <div class="card-body">

            <div class="form-group">
              <label for="formClient-Name"><?php echo lang('role_name') ?></label>
              <input type="text" class="form-control" name="name" id="formClient-Name" required placeholder="<?php echo lang('role_name') ?>" autofocus value="<?php echo $role->title ?>" />
            </div>

            <div class="form-group">
              <label for="formClient-Table"><?php echo lang('permissions') ?></label>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-bordered table-striped" id="tablePermissions">
                    <thead>
                      <tr>
                        <th><?php echo lang('permissions') ?></th>
                        <th width="150" class="text-center">Pilih Semua <input type="checkbox" class="form-check-input check-select-all-p ms-2"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($permissions = $this->permissions_model->get())): ?>
                        <?php foreach ($permissions as $row): ?>
                          <tr>
                            <td><?php echo ucfirst(str_replace('_', ' ', $row->title)) ?></td>
                            <?php
                            $isChecked = in_array($row->code, $role_permissions) ? 'checked' : '';
                            ?>
                            <td class="text-center">
                              <input type="checkbox" class="form-check-input check-select-p" name="permission[]" value="<?php echo $row->code ?>" <?php echo $isChecked ?>>
                            </td>
                          </tr>
                        <?php endforeach ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="2" class="text-center">No Permissions Found</td>
                        </tr>
                      <?php endif ?>
                    </tbody>
                  </table>
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

    $('.check-select-all-p').on('change', function() {

      $('.check-select-p').prop('checked', $(this).is(':checked'));

    })

    $('.table-DT').DataTable({
      "ordering": true,
      'order': true,
      "paging": false,
    });

    var checked = true;
    $('.check-select-p').each(function() {

      if (!$(this).is(':checked'))
        checked = false;

    });

    if (checked) {
      $('.check-select-all-p').prop('checked', true);
    }


  })
</script>

<?php include viewPath('includes/footer'); ?>

<script>
  //Initialize Select2 Elements
  $('.select2').select2()
</script>