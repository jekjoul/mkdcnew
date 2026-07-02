<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        
        <!-- Sidebar Menu Pengaturan -->
        <div class="col-lg-3">
            <?php include 'sidebar.php'; ?>
        </div>
        
        <!-- Form Pengaturan Fitur -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-primary-600">
                    <h6 class="text-light mb-0">
                        <iconify-icon icon="solar:settings-linear" class="text-lg align-middle mr-1"></iconify-icon>
                        Pengaturan Fitur
                    </h6>
                </div>
                
                <?php echo form_open('settings/featureSettingsUpdate', [ 'class' => 'form-validate', 'autocomplete' => 'off', 'method' => 'post' ]); ?>
                <div class="card-body">

                    <!-- Fitur Daftar Ulang -->
                    <div class="mb-3">
                        <h6 class="text-primary-600 mb-2">
                            <iconify-icon icon="solar:user-plus-linear" class="align-middle"></iconify-icon>
                            Fitur Daftar Ulang
                        </h6>
                        <hr class="mt-1 mb-3">

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold text-secondary-light" for="daftar_ulang_status">Status Fitur Daftar Ulang</label>
                            <select name="daftar_ulang_status" id="daftar_ulang_status" class="form-control">
                                <option value="Aktif" <?php echo setting('daftar_ulang_status') === 'Aktif' ? 'selected' : '' ?>>Aktif (Tampilkan & Izinkan Akses)</option>
                                <option value="Tidak Aktif" <?php echo setting('daftar_ulang_status') !== 'Aktif' ? 'selected' : '' ?>>Tidak Aktif (Sembunyikan & Blokir Akses)</option>
                            </select>
                            <div class="text-xs text-secondary-light mt-1">Mengontrol visibilitas menu Daftar Ulang di sidebar dan memblokir akses langsung ke fitur tersebut jika di-nonaktifkan.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary-light" for="daftar_ulang_start_date">Waktu Mulai Akses</label>
                                    <?php 
                                      $start_val = setting('daftar_ulang_start_date');
                                      $start_formatted = !empty($start_val) ? date('Y-m-d\TH:i', strtotime($start_val)) : '';
                                    ?>
                                    <input type="datetime-local" class="form-control" name="daftar_ulang_start_date" id="daftar_ulang_start_date" value="<?php echo $start_formatted ?>" />
                                    <div class="text-xs text-secondary-light mt-1">Akses dibuka mulai tanggal/jam ini. Kosongkan jika tanpa batas mulai.</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-secondary-light" for="daftar_ulang_end_date">Waktu Selesai Akses</label>
                                    <?php 
                                      $end_val = setting('daftar_ulang_end_date');
                                      $end_formatted = !empty($end_val) ? date('Y-m-d\TH:i', strtotime($end_val)) : '';
                                    ?>
                                    <input type="datetime-local" class="form-control" name="daftar_ulang_end_date" id="daftar_ulang_end_date" value="<?php echo $end_formatted ?>" />
                                    <div class="text-xs text-secondary-light mt-1">Akses ditutup otomatis setelah tanggal/jam ini. Kosongkan jika tanpa batas akhir.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer text-end bg-transparent border-top-0 pt-0 pb-24 px-24">
                    <button type="submit" class="btn btn-primary-600 px-24 py-11 radius-8"><i class="fa fa-save mr-1"></i> Simpan Pengaturan Fitur</button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>

    </div>
</div>

<?php include viewPath('includes/footer'); ?>
