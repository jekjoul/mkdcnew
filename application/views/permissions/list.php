<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>


<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <section class="content">

        <!-- Default card -->
        <div class="card basic-data-table">
          <div class="card-header with-border">
            <h3 class="card-title"><?php echo lang('list_all_permissions') ?></h3>

            <div class="card-tools pull-right">
              <?php if (hasPermissions('permissions_add')): ?>
                <a href="<?php echo url('permissions/add') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo lang('create_permission') ?></a>
              <?php endif ?>
            </div>

          </div>
          <div class="card-body">
            <div class="table-responsive scroll-sm">
              <table class="table bordered-table sm-table mb-0" id="dataTable" data-page-length='10'>
                <thead>
                  <tr>
                    <th><?php echo lang('id') ?></th>
                    <th><?php echo lang('permission_name') ?></th>
                    <th>Parent Induk</th>
                    <th>Level</th>
                    <th><?php echo lang('permission_code') ?></th>
                    <th><?php echo lang('action') ?></th>
                  </tr>
                </thead>
                <tbody>

                  <?php $no = 1;
                  foreach ($permissions as $row): ?>
                    <tr>
                      <td width="60"><?php echo $no ?></td>
                      <td>
                        <strong><?php echo html_escape($row->title) ?></strong>
                      </td>
                      <td>
                        <?php if (!empty($row->parent_title)): ?>
                          <span class="text-secondary"><?php echo html_escape($row->parent_title) ?></span>
                        <?php else: ?>
                          <span class="text-muted" style="font-style: italic;">(Tanpa Parent)</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($row->level == 1): ?>
                          <span class="badge bg-primary-100 text-primary-600">L1: Grup</span>
                        <?php elseif ($row->level == 2): ?>
                          <span class="badge bg-info-100 text-info-600">L2: Menu</span>
                        <?php else: ?>
                          <span class="badge bg-warning-100 text-warning-600">L3: Fitur</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <code><?php echo html_escape($row->code) ?></code>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-10 justify-content-center">
                          <?php if (hasPermissions('permissions_edit')): ?>
                            <a href="<?php echo url('permissions/edit/' . $row->id) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></a>

                          <?php endif ?>
                          <?php if (hasPermissions('permissions_delete')): ?>

                            <a href="<?php echo url('permissions/delete/' . $row->id) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick='return confirm("Apakah anda yakin ingin menghapus permission ini? Pastikan permission tidak digunakan agak tidak menimbulkan error.")' title="<?php echo lang('delete_permission') ?>" data-toggle="tooltip"><iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon></a>
                          <?php endif ?>
                        </div>
                      </td>
                    </tr>
                  <?php $no++;
                  endforeach ?>

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