<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>



<div class="dashboard-main-body">
  <div class="row gy-4 mb-24">
    <div class="col-lg-12">
      <div class="card h-100 p-0 radius-12  basic-data-table">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
          <div class="d-flex align-items-center flex-wrap gap-3">
            <h3 class="card-title">Daftar Akun</h3>
          </div>
          <a href="add-user.html" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
            <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
            Add New User
          </a>
        </div>
        <div class="card-body p-24">
          <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0" id="dataTable" data-page-length='10'>
              <thead>
                <tr>
                  <th scope="col">
                    <div class="d-flex align-items-center gap-10">
                      No
                    </div>
                  </th>
                  <th scope="col">Foto</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Email</th>
                  <th scope="col">Role</th>
                  <th scope="col">Terakhir Login</th>
                  <th scope="col" class="text-center">Status</th>
                  <th scope="col" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1;
                foreach ($users as $row): ?>
                  <tr>
                    <td><?php echo $no ?></td>
                    <td width="50" class="text-center">
                      <img src="<?php echo userProfile($row->id) ?>" width="40" height="40" alt="" class="img-avtar">

                    </td>
                    <td>
                      <?php echo $row->name ?>
                    </td>
                    <td><?php echo $row->email ?></td>
                    <td><?php echo ucfirst($this->roles_model->getById($row->role)->title) ?></td>
                    <td><?php echo ($row->last_login != '0000-00-00 00:00:00') ? date(setting('date_format'), strtotime($row->last_login)) : 'No Record' ?></td>
                    <td>
                      <?php if ($row->status == 1): ?>
                        Aktif
                      <?php else: ?>
                        Nonaktif
                      <?php endif ?>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-10 justify-content-center">
                        <?php if (hasPermissions('users_edit')): ?>
                          <a href="<?php echo url('users/edit/' . $row->id) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="Sunting Data PTK">
                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon></a>
                        <?php endif ?>
                        <?php if (hasPermissions('users_view')): ?>

                          <a href="<?php echo url('users/view/' . $row->id) ?>" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="Lihat Data PTK">
                            <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon>
                          </a>
                        <?php endif ?>
                        <?php if (hasPermissions('users_delete')): ?>
                          <?php if ($row->id != 1 && logged('id') != $row->id): ?>
                            <a href="<?php echo url('users/delete/' . $row->id) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="return confirm('Do you really want to delete this user ?')">
                              <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                            </a>
                          <?php else: ?>
                            <a href="#" class="bg-light-100 text-dark-100 bg-hover-light-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle disabled">
                              <iconify-icon icon="material-symbols:delete" class="menu-icon"></iconify-icon>
                            </a>
                          <?php endif ?>
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
      </div>
    </div>
  </div>
</div>








<?php include viewPath('includes/footer'); ?>

<script>
  let table = new DataTable('#dataTable');
</script>

<script>
  window.updateUserStatus = (id, status) => {
    $.get('<?php echo url('users/change_status') ?>/' + id, {
      status: status
    }, (data, status) => {
      if (data == 'done') {
        // code
      } else {
        alert('<?php echo lang('user_unable_change_status') ?>');
      }
    })
  }
</script>