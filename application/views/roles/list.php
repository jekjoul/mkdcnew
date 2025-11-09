<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>


<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <!-- Main content -->
      <section class="content">

        <!-- Default card -->
        <div class="card basic-data-table">
          <div class="card-header with-border">
            <h3 class="card-title"><?php echo lang('list_roles') ?></h3>

            <div class="card-tools pull-right">
              <a href="<?php echo url('roles/add') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus pr-1"></i> <?php echo lang('create_role') ?></a>
            </div>

          </div>
          <div class="card-body">
            <div class="table-responsive scroll-sm">
              <table class="table bordered-table sm-table mb-0" id="dataTable" data-page-length='10'>
                <thead>
                  <tr>
                    <th><?php echo lang('id') ?></th>
                    <th><?php echo lang('role_name') ?></th>
                    <th><?php echo lang('action') ?></th>
                  </tr>
                </thead>
                <tbody>

                  <?php foreach ($roles as $row): ?>
                    <tr>
                      <td width="60"><?php echo $row->id ?></td>
                      <td>
                        <?php echo $row->title ?>
                      </td>
                      <td>
                        <?php if (hasPermissions('roles_edit')): ?>
                          <a href="<?php echo url('roles/edit/' . $row->id) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></a>
                        <?php endif ?>
                      </td>
                    </tr>
                  <?php endforeach ?>

                </tbody>
              </table>
            </div>

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

      </section>
      <!-- /.content -->
    </div>
  </div>
</div>



<?php include viewPath('includes/footer'); ?>

<script>
  $('#dataTable1').DataTable()
</script>