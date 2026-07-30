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
                <?php endif; ?>
            </div>
            <p class="text-secondary-light text-sm mb-0">Pilih judul surat di bawah ini berdasarkan kategori untuk memulai pembuatan surat menggunakan template otomatis.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo url('surat/buat') ?>" class="btn btn-outline-neutral-400 text-neutral-700 py-9 px-16 radius-8 d-flex align-items-center gap-2 text-sm fw-medium">
                <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
                <span>Ganti Lembaga / Kembali</span>
            </a>
        </div>
    </div>

    <?php
    $categories = [
        [
            'title' => 'Kategori Kesiswaan',
            'icon' => 'solar:users-group-two-rounded-bold-duotone',
            'icon_color' => 'text-info-600',
            'bg_icon' => 'bg-info-50',
            'desc' => 'Daftar template dokumen administrasi & pelayanan santri/siswa aktif.',
            'items' => [
                [
                    'id' => 1,
                    'icon' => 'solar:user-speak-bold-duotone',
                    'title' => 'Surat Keterangan Siswa Aktif',
                    'desc' => 'Surat keterangan resmi yang menyatakan bahwa siswa bersangkutan masih terdaftar aktif di sekolah.',
                ],
                [
                    'id' => 2,
                    'icon' => 'solar:verified-check-bold-duotone',
                    'title' => 'Surat Keterangan Kelakuan Baik',
                    'desc' => 'Surat menerangkan rekam jejak perilaku baik dan tidak pernah melanggar tata tertib sekolah.',
                ],
                [
                    'id' => 6,
                    'icon' => 'solar:transfer-horizontal-bold-duotone',
                    'title' => 'Surat Mutasi / Pindah Sekolah',
                    'desc' => 'Surat keterangan pindah/keluar siswa ke satuan pendidikan lain beserta alasan kepindahan.',
                ],
                [
                    'id' => 9,
                    'icon' => 'solar:medal-star-bold-duotone',
                    'title' => 'Surat Keterangan Beasiswa',
                    'desc' => 'Surat rujukan permohonan atau penerimaan beasiswa siswa berprestasi / kurang mampu.',
                ],
            ]
        ],
        [
            'title' => 'Kategori Kepegawaian & Penugasan',
            'icon' => 'solar:user-id-bold-duotone',
            'icon_color' => 'text-primary-600',
            'bg_icon' => 'bg-primary-50',
            'desc' => 'Daftar template dokumen tugas dinas dan keputusan Kepala Sekolah.',
            'items' => [
                [
                    'id' => 3,
                    'icon' => 'solar:document-bold-duotone',
                    'title' => 'Surat Perintah Tugas (SPT)',
                    'desc' => 'Surat penugasan resmi dari Kepala Sekolah kepada Guru/Staf untuk menghadiri tugas dinas luar.',
                ],
                [
                    'id' => 5,
                    'icon' => 'solar:diploma-bold-duotone',
                    'title' => 'Surat Keputusan (SK) Kepala Sekolah',
                    'desc' => 'Dokumen penetapan legalitas penetapan struktur panitia, guru pembina, atau keputusan internal.',
                ],
            ]
        ],
        [
            'title' => 'Kategori Kedinasan & Komunikasi',
            'icon' => 'solar:buildings-2-bold-duotone',
            'icon_color' => 'text-success-600',
            'bg_icon' => 'bg-success-50',
            'desc' => 'Daftar template undangan resmi dan surat edaran pemberitahuan.',
            'items' => [
                [
                    'id' => 4,
                    'icon' => 'solar:letter-opened-bold-duotone',
                    'title' => 'Surat Undangan Rapat / Kegiatan',
                    'desc' => 'Surat undangan resmi perihal rapat wali murid, rapat guru, atau kegiatan operasional sekolah.',
                ],
                [
                    'id' => 10,
                    'icon' => 'solar:bell-bing-bold-duotone',
                    'title' => 'Surat Pemberitahuan Orang Tua',
                    'desc' => 'Pemberitahuan resmi perihal edaran libur, pembayaran, ujian semester, atau agenda sekolah.',
                ],
            ]
        ],
        [
            'title' => 'Kategori Akademik & Kelulusan',
            'icon' => 'solar:diploma-bold-duotone',
            'icon_color' => 'text-purple-600',
            'bg_icon' => 'bg-purple-50',
            'desc' => 'Daftar template dokumen bukti akademis dan kelulusan siswa.',
            'items' => [
                [
                    'id' => 7,
                    'icon' => 'solar:square-academic-cap-bold-duotone',
                    'title' => 'Surat Keterangan Lulus (SKL)',
                    'desc' => 'Surat keterangan kelulusan sementara sebelum ijazah asli diterbitkan resmi oleh dinas.',
                ],
                [
                    'id' => 12,
                    'icon' => 'solar:shield-check-bold-duotone',
                    'title' => 'Surat Pernyataan Tanggung Jawab',
                    'desc' => 'Surat pertanggungjawaban mutlak atas keabsahan dokumen atau penggunaan dana kegiatan.',
                ],
            ]
        ],
        [
            'title' => 'Kategori Rekomendasi & Perjanjian',
            'icon' => 'solar:handshake-bold-duotone',
            'icon_color' => 'text-warning-600',
            'bg_icon' => 'bg-warning-50',
            'desc' => 'Daftar template surat rekomendasi dan naskah perjanjian kerjasama (MoU).',
            'items' => [
                [
                    'id' => 8,
                    'icon' => 'solar:star-bold-duotone',
                    'title' => 'Surat Rekomendasi / Pengantar',
                    'desc' => 'Surat pengantar atau rekomendasi siswa/guru untuk mengikuti lomba, beasiswa, atau seleksi.',
                ],
                [
                    'id' => 11,
                    'icon' => 'solar:handshake-bold-duotone',
                    'title' => 'Surat Perjanjian Kerja (MoU)',
                    'desc' => 'Dokumen naskah kesepakatan kerjasama antara sekolah dengan pihak ketiga / mitra industri.',
                ],
            ]
        ],
    ];
    ?>

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
                            $targetUrl = ($tmpl['id'] == 1) ? url('surat/keterangan_siswa_aktif') : url('surat/keluar_tambah_otomatis');
                            $urlParams = '?template_id=' . $tmpl['id'];
                            if (!empty($id_lembaga)) {
                                $urlParams .= '&id_lembaga=' . $id_lembaga;
                            }
                        ?>
                            <div class="col-xl-4 col-md-6">
                                <div class="card h-100 border-0 shadow-xs radius-12 p-16 hover-template-card transition-all bg-white">
                                    <div class="d-flex align-items-start gap-12 mb-12">
                                        <div class="w-40-px h-40-px radius-8 bg-neutral-100 d-flex align-items-center justify-content-center text-primary-600 text-xl flex-shrink-0">
                                            <iconify-icon icon="<?php echo $tmpl['icon'] ?>"></iconify-icon>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-neutral-900 text-sm mb-0 text-truncate" title="<?php echo htmlspecialchars($tmpl['title']) ?>">
                                                <?php echo $tmpl['title'] ?>
                                            </h6>
                                        </div>
                                    </div>
                                    <p class="text-secondary-light text-xs mb-16 line-height-1-5 flex-grow-1">
                                        <?php echo $tmpl['desc'] ?>
                                    </p>
                                    <div class="pt-10 border-top border-neutral-200 d-flex align-items-center justify-content-between">
                                        <span class="text-neutral-500 text-xs">Status: <strong class="text-success-600">Aktif</strong></span>
                                        <a href="<?php echo $targetUrl . $urlParams ?>" class="btn btn-outline-primary-600 text-primary-600 hover-bg-primary-600 hover-text-white py-6 px-12 radius-8 text-xs fw-semibold d-flex align-items-center gap-1">
                                            <span>Gunakan Template</span>
                                            <iconify-icon icon="solar:alt-arrow-right-linear" class="text-sm"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
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
