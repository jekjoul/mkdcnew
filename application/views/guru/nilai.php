<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Input Nilai Siswa</h6>
        </div>
        <div class="card-body">
            <!-- TAMPILAN DESKTOP (Tabel) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Siswa</th>
                            <th>Terisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</td>
                                <td>(<?php echo html_escape(trim((isset($row->nama_lembaga_singkat) && $row->nama_lembaga_singkat ? $row->nama_lembaga_singkat : $row->nama_lembaga) . ') ' . $row->nama_tingkat . ' - ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?> </td>
                                <td><?php echo (int) $row->jumlah_siswa ?></td>
                                <td><?php echo (int) $row->jumlah_dinilai ?></td>
                                <td>
                                    <a href="<?php echo url('guru/input_nilai/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:pen-linear"></iconify-icon> Input
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- TAMPILAN MOBILE (Accordion List + Live Search) -->
            <style>
            .accordion-button::after {
                display: none !important;
            }
            .accordion-button {
                padding-inline-end: 16px !important;
            }
            </style>
            <div class="d-block d-md-none">
                <!-- Form Search Mobile -->
                <div class="mb-16">
                    <div class="position-relative">
                        <input type="text" id="mobileNilaiSelectSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Kelas, Mapel, atau Tahun Pelajaran...">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                            <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                        </span>
                    </div>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="accordion custom-accordion" id="accordionNilaiSelectMobile">
                        <?php foreach ($items as $i => $row): ?>
                            <?php
                            $accordionId = "collapseNilaiSelect" . $row->id_pembelajaran_mapel;
                            $headingId   = "headingNilaiSelect" . $row->id_pembelajaran_mapel;
                            $nama_kelas  = trim((isset($row->nama_lembaga_singkat) && $row->nama_lembaga_singkat ? $row->nama_lembaga_singkat : $row->nama_lembaga) . ' ' . $row->nama_tingkat . ' - ' . $row->nama_rombel);
                            $searchableText = strtolower(html_escape($row->nama_mapel . ' ' . $nama_kelas . ' ' . $row->tahun_pelajaran . ' ' . $row->semester));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-nilai-select-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold"><?php echo html_escape($row->nama_mapel); ?></span>
                                                <span class="badge bg-info-100 text-info-700 px-8 py-2 radius-4 text-xs">Terisi: <?php echo (int)$row->jumlah_dinilai; ?>/<?php echo (int)$row->jumlah_siswa; ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:users-group-two-rounded-linear" class="me-4"></iconify-icon><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionNilaiSelectMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <div class="row gy-2 text-xs mb-12">
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Tahun / Sem</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Progres Nilai</span>
                                                <span class="fw-semibold text-primary-light"><?php echo (int)$row->jumlah_dinilai; ?> dari <?php echo (int)$row->jumlah_siswa; ?> Siswa</span>
                                            </div>
                                            <div class="col-12 mt-8">
                                                <span class="text-secondary-light d-block mb-2">Kelas / Rombel</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                        </div>

                                        <div class="mt-16 pt-12 border-top">
                                            <a href="<?php echo url('guru/input_nilai/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-primary-600 btn-sm text-white radius-8 w-100 d-flex align-items-center justify-content-center gap-2 py-8 fw-semibold">
                                                <iconify-icon icon="solar:pen-linear" class="text-lg"></iconify-icon> Input Nilai Siswa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noMobileNilaiSelectResult" class="text-center py-24 text-secondary-light d-none">
                        <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                        <p class="text-sm">Data pembelajaran tidak ditemukan.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-24 text-secondary-light">
                        <p class="text-sm">Belum ada kelas pembelajaran untuk input nilai.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');

    $(document).ready(function() {
        $('#mobileNilaiSelectSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-nilai-select-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobileNilaiSelectResult').removeClass('d-none');
            } else {
                $('#noMobileNilaiSelectResult').addClass('d-none');
            }
        });
    });
</script>