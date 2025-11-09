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
            <h3 class="card-title"><?php echo lang('edit_permission') ?></h3>

          </div>

          <?php echo form_open('permissions/update/' . $permission->id, ['class' => 'form-validate']); ?>
          <div class="card-body">

            <div class="form-group">
              <label for="formClient-Name"><?php echo lang('permission_name') ?></label>
              <input type="text" class="form-control" name="name" id="formClient-Name" required placeholder="<?php echo lang('permission_name') ?>" value="<?php echo $permission->title ?>" autofocus />
            </div>

            <div class="form-group">
              <label for="formClient-Code"><?php echo lang('permission_code') ?></label>
              <input type="text" class="form-control" data-rule-remote="<?php echo url('permissions/checkIfUnique?notId=' . $permission->id) ?>" name="code" id="formClient-Code" required placeholder="<?php echo lang('permission_code') ?>" value="<?php echo $permission->code ?>" />
              <p style="color: red;">* code must be unique</p>
            </div>

          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <div class="row">
              <div class="col"><a href="<?php echo url('/permissions') ?>" onclick="return confirm('Are you sure you want to leave?')" class="btn btn-flat btn-danger"><?php echo lang('cancel') ?></a></div>
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

      $('.check-select-p').attr('checked', $(this).is(':checked'));

    })

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