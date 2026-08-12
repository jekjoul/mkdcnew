<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-24">
        <div>
            <h5 class="fw-bold text-neutral-900 mb-4">Pilih Metode Pembuatan Surat</h5>
            <p class="text-secondary-light text-sm mb-0">Silakan pilih metode pembuatan surat yang sesuai dengan kebutuhan Anda.</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Card Surat Manual -->
        <div class="col-xl-5 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-16 overflow-hidden position-relative hover-card-effect">
                <div class="card-body p-32 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-24">
                            <div class="w-64-px h-64-px rounded-16 bg-primary-50 d-flex align-items-center justify-content-center text-primary-600 text-3xl">
                                <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                            </div>
                            <span class="badge bg-primary-100 text-primary-600 px-12 py-6 rounded-pill text-xs fw-semibold">Bebas / Custom</span>
                        </div>
                        <h4 class="fw-bold text-neutral-900 mb-12">Buat Surat Manual</h4>
                        <p class="text-secondary-light text-sm mb-24 line-height-1-6">
                            Buat surat secara kustom dengan mengisi informasi nomor, perihal, keterangan, dan memilih pejabat penandatangan beserta jabatannya secara fleksibel.
                        </p>
                        <div class="d-flex flex-column gap-2 mb-24 text-xs text-neutral-600">
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Penandatangan multiselect & jabatan dinamis</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Bisa kustom nomor dan keterangan surat</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Sesuai untuk surat fisik / non-template</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo url('surat/keluar_tambah_manual') ?>" class="btn btn-primary-600 w-100 py-12 radius-8 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <span>Mulai Buat Surat Manual</span>
                            <iconify-icon icon="solar:arrow-right-linear" class="text-xl"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Surat Otomatis -->
        <div class="col-xl-5 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-16 overflow-hidden position-relative hover-card-effect">
                <div class="card-body p-32 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-24">
                            <div class="w-64-px h-64-px rounded-16 bg-warning-50 d-flex align-items-center justify-content-center text-warning-600 text-3xl">
                                <iconify-icon icon="solar:magic-stick-3-linear"></iconify-icon>
                            </div>
                            <span class="badge bg-warning-100 text-warning-600 px-12 py-6 rounded-pill text-xs fw-semibold">Instan / Template</span>
                        </div>
                        <h4 class="fw-bold text-neutral-900 mb-12">Buat Surat Otomatis</h4>
                        <p class="text-secondary-light text-sm mb-24 line-height-1-6">
                            Pilih dari berbagai daftar template judul surat instan yang sudah tersedia untuk mempercepat proses penerbitan dokumen resmi lembaga.
                        </p>
                        <div class="d-flex flex-column gap-2 mb-24 text-xs text-neutral-600">
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Pilihan lembaga / instansi penerbit</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Pilihan judul surat fix & terstruktur</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-lg"></iconify-icon>
                                <span>Format isi surat otomatis terisi</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <!-- Buka Modal Pilihan Lembaga -->
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalPilihLembaga" class="btn btn-warning-600 text-white w-100 py-12 radius-8 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <span>Pilih Template Surat Otomatis</span>
                            <iconify-icon icon="solar:arrow-right-linear" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Lembaga -->
<div class="modal fade" id="modalPilihLembaga" tabindex="-1" aria-labelledby="modalPilihLembagaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-24 px-24">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-40-px h-40-px rounded-circle bg-warning-50 d-flex align-items-center justify-content-center text-warning-600 text-xl">
                        <iconify-icon icon="solar:buildings-3-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-neutral-900" id="modalPilihLembagaLabel">Pilih Lembaga / Instansi</h6>
                        <p class="text-secondary-light text-xs mb-0">Pilih lembaga penerbit sebelum masuk ke template surat.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('surat/buat_otomatis') ?>" method="get" id="formPilihLembaga">
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-neutral-800">Lembaga / Unit Pendidikan <span class="text-danger">*</span></label>
                        <select name="id_lembaga" id="selectLembagaModal" class="form-select radius-8 py-10" required>
                            <option value="">-- Pilih Lembaga Tujuan --</option>
                            <?php if (!empty($lembaga)): ?>
                                <?php foreach ($lembaga as $l): ?>
                                    <option value="<?php echo $l->id_lembaga ?>"><?php echo htmlspecialchars($l->nama_lembaga) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="row g-2">
                        <?php if (!empty($lembaga)): ?>
                            <?php foreach ($lembaga as $idx => $l): ?>
                                <div class="col-12">
                                    <label class="border radius-8 p-12 d-flex align-items-center justify-content-between cursor-pointer hover-bg-light position-relative select-lembaga-card w-100 mb-0">
                                        <div class="d-flex align-items-center gap-12">
                                            <input type="radio" name="id_lembaga_radio" value="<?php echo $l->id_lembaga ?>" class="form-check-input radio-lembaga" <?php echo $idx === 0 ? 'checked' : '' ?>>
                                            <span class="fw-semibold text-neutral-800 text-sm"><?php echo htmlspecialchars($l->nama_lembaga) ?></span>
                                        </div>
                                        <iconify-icon icon="solar:building-2-linear" class="text-neutral-400 text-lg"></iconify-icon>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-24 px-24">
                    <button type="button" class="btn btn-neutral-200 text-neutral-700 radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning-600 text-white radius-8 px-20 fw-semibold d-flex align-items-center gap-2">
                        <span>Lanjutkan Ke Template Surat</span>
                        <iconify-icon icon="solar:arrow-right-linear" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-card-effect {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-card-effect:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        border-color: #ff9f43 !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<?php include viewPath('includes/footer'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Sinkronisasi radio button dan select dropdown di modal
        const firstRadioVal = $('.radio-lembaga:checked').val();
        if (firstRadioVal) {
            $('#selectLembagaModal').val(firstRadioVal);
        }

        $('.radio-lembaga').on('change', function() {
            const val = $(this).val();
            $('#selectLembagaModal').val(val);
        });

        $('#selectLembagaModal').on('change', function() {
            const val = $(this).val();
            $('.radio-lembaga[value="' + val + '"]').prop('checked', true);
        });
    });
</script>
