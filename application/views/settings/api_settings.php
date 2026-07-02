<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        
        <!-- Sidebar Menu Pengaturan -->
        <div class="col-lg-3">
            <?php include 'sidebar.php'; ?>
        </div>
        
        <!-- Form Pengaturan API -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-primary-600">
                    <h6 class="text-light mb-0">
                        <iconify-icon icon="solar:settings-linear" class="text-lg align-middle mr-1"></iconify-icon>
                        Pengaturan Integrasi API
                    </h6>
                </div>
                
                <?php echo form_open('settings/apiSettingsUpdate', [ 'class' => 'form-validate', 'autocomplete' => 'off', 'method' => 'post' ]); ?>
                <div class="card-body">

                    <!-- Identitas Aplikasi -->
                    <div class="mb-4">
                        <h6 class="text-primary-600 mb-2">
                            <iconify-icon icon="solar:info-circle-linear" class="align-middle"></iconify-icon>
                            Identitas Aplikasi
                        </h6>
                        <hr class="mt-1 mb-3">
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold text-secondary-light" for="company_name">Nama Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo htmlspecialchars(setting('company_name'), ENT_QUOTES, 'UTF-8') ?>" required placeholder="Masukkan nama aplikasi" />
                            <div class="text-xs text-secondary-light mt-1">Nama ini digunakan sebagai judul situs (site title), halaman login, dan subjek email sistem.</div>
                        </div>
                    </div>
                    
                    <!-- Google Auth API -->
                    <div class="mb-3">
                        <h6 class="text-primary-600 mb-2">
                            <iconify-icon icon="logos:google-icon" class="align-middle mr-1"></iconify-icon>
                            Google Authentication API
                        </h6>
                        <hr class="mt-1 mb-3">

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold text-secondary-light" for="google_client_id">Google Client ID</label>
                            <input type="text" class="form-control" name="google_client_id" id="google_client_id" value="<?php echo htmlspecialchars(setting('google_client_id'), ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan Google Client ID" />
                            <div class="text-xs text-secondary-light mt-1">ID Klien Google OAuth 2.0 untuk integrasi login Google.</div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold text-secondary-light" for="google_client_secret">Google Client Secret</label>
                            <input type="text" class="form-control" name="google_client_secret" id="google_client_secret" value="<?php echo htmlspecialchars(setting('google_client_secret'), ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan Google Client Secret" />
                            <div class="text-xs text-secondary-light mt-1">Kredensial rahasia Google API. Pastikan Authorized Redirect URI di Google Console diatur ke: <code><?php echo site_url('login/google_callback'); ?></code></div>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer text-end bg-transparent border-top-0 pt-0 pb-24 px-24">
                    <button type="submit" class="btn btn-primary-600 px-24 py-11 radius-8"><i class="fa fa-save mr-1"></i> Simpan Pengaturan API</button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>

    </div>
</div>

<?php include viewPath('includes/footer'); ?>
