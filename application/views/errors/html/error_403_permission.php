<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body d-flex flex-column align-items-center justify-content-center" style="min-height: calc(100vh - 160px);">
    <div class="card border-0 shadow-lg radius-24 bg-white text-center p-32 p-sm-48" style="max-width: 560px; width: 100%;">
        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
            
            <!-- Warning Icon Wrapper Centered -->
            <div class="position-relative mb-24">
                <div class="w-96-px h-96-px radius-circle bg-warning-50 border border-warning-200 d-flex align-items-center justify-content-center shadow-xs mx-auto">
                    <div class="w-72-px h-72-px radius-circle bg-warning-100 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:shield-warning-bold-duotone" class="text-warning-600" style="font-size: 44px;"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Error Badge -->
            <div class="mb-16">
                <span class="badge bg-warning-50 text-warning-700 fw-bold px-16 py-8 radius-circle text-xs border border-warning-200 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:lock-keyhole-minimalistic-bold" class="text-warning-600 text-sm"></iconify-icon>
                    AKSES DITOLAK • ERROR 403
                </span>
            </div>

            <!-- Title -->
            <h4 class="fw-bold text-neutral-900 mb-12">Anda Tidak Diperkenankan Mengakses Halaman Ini</h4>

            <!-- Subtitle / Explanation -->
            <p class="text-secondary-light text-sm mb-32 leading-relaxed" style="max-width: 440px;">
                Maaf, akun Anda tidak memiliki hak akses atau izin yang cukup untuk membuka fitur atau halaman ini. Silakan hubungi Administrator sistem jika Anda memerlukan bantuan akses.
            </p>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center justify-content-center flex-wrap gap-12 w-100">
                <button type="button" onclick="window.history.back()" class="btn btn-outline-neutral-400 text-neutral-700 radius-12 px-20 py-10 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-sm">
                    <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
                    <span>Kembali Sebelumnya</span>
                </button>
                <a href="<?php echo url('dashboard') ?>" class="btn btn-primary-600 radius-12 px-24 py-10 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 text-sm text-white shadow-xs">
                    <iconify-icon icon="solar:home-angle-bold" class="text-lg"></iconify-icon>
                    <span>Ke Beranda</span>
                </a>
            </div>

        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
