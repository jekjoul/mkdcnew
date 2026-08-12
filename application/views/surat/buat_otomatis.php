<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-4">
                <h5 class="fw-bold text-neutral-900 mb-0">Template Surat Otomatis</h5>
                <?php if (!empty($selected_lembaga)): ?>
                    <span class="badge bg-warning-100 text-warning-700 px-12 py-6 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:building-2-bold" class="text-sm"></iconify-icon>
                        <?php echo htmlspecialchars($selected_lembaga->nama_lembaga) ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-neutral-100 text-neutral-700 px-12 py-6 radius-6 text-xs fw-bold d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:buildings-2-bold" class="text-sm"></iconify-icon>
                        Semua Lembaga
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-secondary-light text-sm mb-0">Pilih judul surat berbasis database di bawah ini untuk memulai pembuatan surat menggunakan template otomatis.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo url('surat/buat') ?>" class="btn btn-outline-neutral-400 text-neutral-700 py-9 px-16 radius-8 d-flex align-items-center gap-2 text-sm fw-medium">
                <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
                <span>Ganti Lembaga / Kembali</span>
            </a>
        </div>
    </div>

    <?php if (!empty($categories)): ?>
        <!-- Grouping berdasarkan Card Kategori Surat -->
        <div class="d-flex flex-column gap-4">
            <?php foreach ($categories as $cat): ?>
                <div class="card border-0 shadow-xs radius-16 overflow-hidden">
                    <!-- Header Card Kategori -->
                    <div class="card-header bg-neutral-50 border-bottom border-neutral-200 p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-12">
                            <div class="w-40-px h-40-px radius-10 <?php echo $cat['bg_icon'] ?> d-flex align-items-center justify-content-center <?php echo $cat['icon_color'] ?> text-2xl">
                                <iconify-icon icon="<?php echo $cat['icon'] ?>"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold text-neutral-900 mb-2"><?php echo htmlspecialchars($cat['title']) ?></h6>
                                <p class="text-secondary-light text-xs mb-0"><?php echo htmlspecialchars($cat['desc']) ?></p>
                            </div>
                        </div>
                        <span class="badge bg-neutral-200 text-neutral-700 px-12 py-6 radius-pill text-xs fw-semibold">
                            <?php echo count($cat['items']) ?> Template tersedia
                        </span>
                    </div>

                    <!-- Body Card Kategori (Grid Item Template Surat) -->
                    <div class="card-body p-20">
                        <div class="row g-3">
                            <?php foreach ($cat['items'] as $tmpl): 
                                $isAvailable = ($tmpl->status === 'Aktif');
                                $targetAction = !empty($tmpl->target_url) ? $tmpl->target_url : 'surat/keluar_tambah_otomatis';
                                $targetUrl = url($targetAction);
                                $urlParams = '?template_id=' . $tmpl->id_template_surat;
                                if (!empty($id_lembaga)) {
                                    $urlParams .= '&id_lembaga=' . $id_lembaga;
                                }
                                $hasRestriction = !empty($tmpl->allowed_lembaga);
                                if ($hasRestriction) {
                                    $singkatans = array_map(function($al) {
                                        return $al->nama_lembaga_singkat ?: $al->nama_lembaga;
                                    }, $tmpl->allowed_lembaga);
                                    $scopeLabel = 'Lembaga: ' . implode(', ', $singkatans);
                                } else {
                                    $scopeLabel = 'Bisa Digunakan Bersama';
                                }
                            ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="card h-100 border-0 shadow-xs radius-12 p-16 transition-all <?php echo $isAvailable ? 'hover-template-card bg-white' : 'bg-neutral-50 opacity-75' ?>">
                                        <div class="d-flex align-items-start gap-12 mb-12">
                                            <div class="w-40-px h-40-px radius-8 <?php echo $isAvailable ? 'bg-primary-50 text-primary-600' : 'bg-neutral-200 text-neutral-500' ?> d-flex align-items-center justify-content-center text-xl flex-shrink-0">
                                                <iconify-icon icon="<?php echo htmlspecialchars($tmpl->icon ?: 'solar:document-bold-duotone') ?>"></iconify-icon>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="fw-bold <?php echo $isAvailable ? 'text-neutral-900' : 'text-neutral-600' ?> text-sm mb-1 text-truncate" title="<?php echo htmlspecialchars($tmpl->nama_template) ?>">
                                                    <?php echo htmlspecialchars($tmpl->nama_template) ?>
                                                </h6>
                                                <span class="badge <?php echo $hasRestriction ? 'bg-warning-100 text-warning-800' : 'bg-neutral-100 text-neutral-700' ?> px-8 py-2 radius-4 text-xs text-truncate max-w-100">
                                                    <?php echo htmlspecialchars($scopeLabel) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-secondary-light text-xs mb-16 line-height-1-5 flex-grow-1">
                                            <?php echo htmlspecialchars($tmpl->deskripsi ?: 'Template otomatis penerbitan surat resmi.') ?>
                                        </p>
                                        <div class="pt-10 border-top border-neutral-200 d-flex align-items-center justify-content-between">
                                            <?php if ($isAvailable): ?>
                                                <span class="text-neutral-500 text-xs">Status: <strong class="text-success-600">Aktif</strong></span>
                                                <a href="<?php echo $targetUrl . $urlParams ?>" class="btn btn-primary-600 text-white py-6 px-12 radius-8 text-xs fw-semibold d-flex align-items-center gap-1 shadow-xs">
                                                    <span>Gunakan Template</span>
                                                    <iconify-icon icon="solar:alt-arrow-right-linear" class="text-sm"></iconify-icon>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-neutral-400 text-xs">Status: <strong class="text-neutral-500">Nonaktif</strong></span>
                                                <button type="button" class="btn btn-neutral-200 text-neutral-500 py-6 px-12 radius-8 text-xs fw-semibold d-flex align-items-center gap-1" disabled style="cursor: not-allowed;">
                                                    <iconify-icon icon="solar:lock-keyhole-bold" class="text-sm"></iconify-icon>
                                                    <span>Nonaktif</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card p-40 text-center radius-16 border-0 shadow-xs">
            <iconify-icon icon="solar:document-text-linear" class="text-neutral-400 text-5xl mb-12 mx-auto"></iconify-icon>
            <h6 class="fw-bold text-neutral-800">Belum Ada Template Surat</h6>
            <p class="text-secondary-light text-sm mb-0">Belum ada template yang tersedia untuk lembaga yang dipilih.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-template-card {
        border: 1px solid #eef2f6 !important;
        transition: all 0.2s ease-in-out;
    }
    .hover-template-card:hover {
        border-color: #487fff !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(72, 127, 255, 0.12) !important;
    }
</style>

<?php include viewPath('includes/footer'); ?>
