<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0"><?php echo lang('my_account') ?></h6>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="<?php echo url('/') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-angle-2-linear" class="icon text-lg"></iconify-icon>
          Home
        </a>
      </li>
      <li class="text-secondary-light">/</li>
      <li class="text-secondary-light">Profil Saya</li>
    </ul>
  </div>

  <div class="row gy-4">
    <!-- Left Column: User Profile Card -->
    <div class="col-lg-4">
      <div class="card border-0 radius-12 shadow-sm h-100">
        <div class="card-body p-24 text-center">
          <div class="mb-20 d-inline-block position-relative">
            <img class="w-120-px h-120-px rounded-circle object-fit-cover border border-3 border-primary-light" src="<?php echo userProfile($user->id) ?>" alt="Avatar" />
          </div>

          <h5 class="fw-semibold text-primary-light mb-8"><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></h5>
          <span class="badge bg-success-focus text-success-main px-16 py-6 radius-4 mb-24"><?php echo $user->role->title ?></span>

          <div class="border-top pt-20 text-start">
            <div class="mb-16">
              <span class="text-secondary-light text-xs d-block mb-4">Username</span>
              <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="mb-16">
              <span class="text-secondary-light text-xs d-block mb-4">Login Terakhir</span>
              <span class="fw-semibold text-primary-light"><?php echo $user->last_login != '0000-00-00 00:00:00' ? date('d M Y H:i', strtotime($user->last_login)) : '-' ?></span>
            </div>
            <div class="mb-0">
              <span class="text-secondary-light text-xs d-block mb-4">Bergabung Sejak</span>
              <span class="fw-semibold text-primary-light"><?php echo date('d M Y', strtotime($user->created_at)) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Tabs and Forms -->
    <div class="col-lg-8">
      <div class="card border-0 radius-12 shadow-sm h-100">
        <div class="card-header border-bottom bg-transparent p-24 pb-0">
          <ul class="nav nav-tabs border-bottom-0 gap-2 mb-20" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active btn btn-outline-primary radius-8 px-16 py-8" id="view-tab" data-bs-toggle="tab" data-bs-target="#viewProfile" type="button" role="tab">
                <iconify-icon icon="lucide:user" class="me-1"></iconify-icon> <?php echo lang('profile') ?>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link btn btn-outline-primary radius-8 px-16 py-8" id="edit-tab" data-bs-toggle="tab" data-bs-target="#editProfile" type="button" role="tab">
                <iconify-icon icon="lucide:edit" class="me-1"></iconify-icon> <?php echo lang('edit') ?>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link btn btn-outline-primary radius-8 px-16 py-8" id="pic-tab" data-bs-toggle="tab" data-bs-target="#editProfilePic" type="button" role="tab">
                <iconify-icon icon="lucide:image" class="me-1"></iconify-icon> Foto Profil
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link btn btn-outline-primary radius-8 px-16 py-8" id="password-tab" data-bs-toggle="tab" data-bs-target="#changePassword" type="button" role="tab">
                <iconify-icon icon="lucide:lock" class="me-1"></iconify-icon> Password
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link btn btn-outline-primary radius-8 px-16 py-8 <?php echo $activeTab === 'google' ? 'active' : '' ?>" id="google-tab" data-bs-toggle="tab" data-bs-target="#googleIntegration" type="button" role="tab">
                <iconify-icon icon="logos:google-icon" class="me-1"></iconify-icon> Google Integrasi
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body p-24">
          <div class="tab-content" id="profileTabsContent">
            
            <!-- Tab 1: View Profile -->
            <div class="tab-pane fade show active" id="viewProfile" role="tabpanel">
              <div class="row gy-3">
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Nama Lengkap</span>
                    <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Username</span>
                    <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Email</span>
                    <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Hak Akses / Role</span>
                    <span class="fw-semibold text-primary-light"><?php echo $user->role->title ?></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Kontak / Telepon</span>
                    <span class="fw-semibold text-primary-light"><?php echo htmlspecialchars($user->phone ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="bg-light p-12 radius-8">
                    <span class="text-secondary-light text-xs d-block mb-4">Alamat</span>
                    <span class="fw-semibold text-primary-light"><?php echo !empty($user->address) ? nl2br(htmlspecialchars($user->address, ENT_QUOTES, 'UTF-8')) : '-' ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab 2: Edit Profile -->
            <div class="tab-pane fade" id="editProfile" role="tabpanel">
              <?php echo form_open('/profile/updateProfile', ['method' => 'POST', 'autocomplete' => 'off', 'class' => 'form-validate']); ?>
                <div class="row gy-3">
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap</label>
                    <input type="text" name="name" required class="form-control radius-8" value="<?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nama Lengkap">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Username</label>
                    <input type="text" class="form-control radius-8" minlength="5" data-rule-remote="<?php echo url('users/check?notId='.$user->id) ?>" data-msg-remote="<?php echo lang('user_username_taken') ?>" name="username" required placeholder="Username" value="<?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?>"/>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                    <input type="email" name="email" required data-rule-remote="<?php echo url('users/check?notId='.$user->id) ?>" data-msg-remote="<?php echo lang('user_email_exists') ?>" class="form-control radius-8" placeholder="Alamat Email" value="<?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kontak / No. Telp</label>
                    <input type="text" name="contact" class="form-control radius-8" value="<?php echo htmlspecialchars($user->phone ?: '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nomor Telepon">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                    <textarea class="form-control radius-8" name="address" placeholder="Alamat Tinggal" rows="3"><?php echo htmlspecialchars($user->address ?: '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                  </div>
                  <div class="col-12 hidden">
                    <input type="hidden" name="role" value="<?php echo $user->role->id ?>">
                  </div>
                  <div class="col-12 mt-24">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                  </div>
                </div>
              <?php echo form_close(); ?>
            </div>

            <!-- Tab 3: Change Profile Pic -->
            <div class="tab-pane fade" id="editProfilePic" role="tabpanel">
              <?php echo form_open('/profile/updateProfilePic', ['method' => 'POST', 'autocomplete' => 'off', 'class' => 'form-validate', 'enctype' => 'multipart/form-data']); ?>
                <div class="row gy-3">
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Foto Baru</label>
                    <input type="file" class="form-control radius-8" name="image" required accept="image/*" onchange="previewImage(this, '#imagePreview img')">
                    <div class="form-text text-secondary-light mt-8">Hanya file gambar (JPG, PNG) dengan ukuran maksimal 2MB.</div>
                  </div>
                  <div class="col-12 my-24" id="imagePreview">
                    <img src="<?php echo userProfile($user->id) ?>" class="rounded-circle object-fit-cover border" width="150" height="150" alt="Preview">
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Unggah Foto</button>
                  </div>
                </div>
              <?php echo form_close(); ?>
            </div>

            <!-- Tab 4: Change Password -->
            <div class="tab-pane fade" id="changePassword" role="tabpanel">
              <?php echo form_open('/profile/updatePassword', ['method' => 'POST', 'autocomplete' => 'off', 'class' => 'form-validate']); ?>
                
                <div class="alert bg-warning-focus text-warning-main border border-warning-200 px-16 py-12 radius-8 mb-16 d-flex align-items-start gap-2">
                  <iconify-icon icon="lucide:alert-triangle" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                  <div>
                    <div class="fw-semibold">Perhatian!</div>
                    <div class="text-sm"><?php echo lang('message_login_again_after_password') ?></div>
                  </div>
                </div>

                <div class="alert bg-info-focus text-info-main border border-info-200 px-16 py-12 radius-8 mb-24 d-flex align-items-start gap-2">
                  <iconify-icon icon="lucide:info" class="icon text-xl flex-shrink-0 mt-1"></iconify-icon>
                  <div>
                    <div class="fw-semibold">Aturan Password</div>
                    <div class="text-sm"><?php echo lang('message_password_atleast_long') ?></div>
                  </div>
                </div>

                <div class="row gy-3">
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Password Lama</label>
                    <input type="password" class="form-control radius-8" placeholder="Password Lama" minlength="6" name="old_password" required id="old_password" />
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Password Baru</label>
                    <input type="password" class="form-control radius-8" placeholder="Password Baru" minlength="6" name="password" required id="password" />
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ulangi Password Baru</label>
                    <input type="password" class="form-control radius-8" equalTo="#password" placeholder="Konfirmasi Password Baru" required name="password_confirm" />
                  </div>
                  <div class="col-12 mt-24">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Ganti Password</button>
                  </div>
                </div>
              <?php echo form_close(); ?>
            </div>

            <!-- Tab 5: Google Integration -->
            <div class="tab-pane fade <?php echo $activeTab === 'google' ? 'show active' : '' ?>" id="googleIntegration" role="tabpanel">
              <div class="p-24 border radius-12 bg-light-50">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
                  <div>
                    <h6 class="text-primary-light mb-4">Hubungkan Akun Google</h6>
                    <p class="text-secondary-light text-xs mb-0">Menghubungkan akun Google memungkinkan Anda masuk dengan sekali klik dan sinkronisasi file pembelajaran langsung ke Google Drive.</p>
                  </div>
                  <?php if (!empty($user->google_id)): ?>
                    <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4">Terhubung</span>
                  <?php else: ?>
                    <span class="badge bg-neutral-200 text-neutral-600 px-12 py-6 radius-4">Belum Terhubung</span>
                  <?php endif; ?>
                </div>

                <?php if (!empty($user->google_id)): ?>
                  <div class="bg-success-50 border border-success-100 p-16 radius-8 mb-24 d-flex align-items-center gap-12">
                    <iconify-icon icon="logos:google-icon" style="font-size: 24px;"></iconify-icon>
                    <div>
                      <span class="d-block text-success-800 fw-semibold text-sm">Akun Google Anda Berhasil Terintegrasi</span>
                      <span class="text-secondary-light text-xs">Anda saat ini terdaftar sebagai Audience di Google Console. Login cepat dan sinkronisasi Google Docs/Sheets siap digunakan.</span>
                    </div>
                  </div>

                  <a href="<?php echo url('profile/disconnectGoogle') ?>" 
                     onclick="return confirm('Apakah Anda yakin ingin mematikan sinkronisasi & memutuskan integrasi Google?')" 
                     class="btn btn-outline-danger radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                     <iconify-icon icon="lucide:link-2-off"></iconify-icon> Putuskan Akun Google
                  </a>
                <?php else: ?>
                  <div class="bg-warning-50 border border-warning-100 p-16 radius-8 mb-24 d-flex align-items-start gap-12">
                    <iconify-icon icon="lucide:info" class="text-warning-main mt-1" style="font-size: 20px;"></iconify-icon>
                    <div class="text-sm">
                      <strong class="text-warning-800">Menghubungkan Profil Anda:</strong>
                      <span class="d-block text-secondary-light text-xs mt-4">Proses ini akan mendaftarkan data profil Anda sebagai Audience di Google Console untuk otorisasi API internal.</span>
                    </div>
                  </div>

                  <a href="<?php echo url('profile/connectGoogle') ?>" 
                     class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                     <iconify-icon icon="logos:google-icon" class="bg-white p-2 rounded"></iconify-icon> Hubungkan Profil ke Google
                  </a>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('.form-validate').each(function() {
      $(this).validate();
    });
  });

  function previewImage(input, previewImgSelector) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $(previewImgSelector).attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>

<?php include viewPath('includes/footer'); ?>