<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
            <h6 class="mb-0 text-light"><?php echo !empty($is_nonaktif) ? 'Perangkat Pembelajaran Tidak Aktif' : 'Perangkat Pembelajaran'; ?></h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?php echo url(!empty($is_nonaktif) ? 'perangkat_pembelajaran' : 'perangkat_pembelajaran/nonaktif') ?>" class="btn btn-sm btn-warning-600 text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>" class="text-lg"></iconify-icon>
                    <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                </a>
            </div>
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
                            <th>Guru</th>
                            <th>Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <?php
                            $percent = 0;
                            if (!empty($row->file_cp)) $percent += 10;
                            if (!empty($row->file_tp)) $percent += 10;
                            if (!empty($row->file_atp)) $percent += 10;
                            if (!empty($row->file_kktp)) $percent += 10;
                            if (!empty($row->file_kisi_sts)) $percent += 10;
                            if (!empty($row->file_soal_sts)) $percent += 10;
                            if (!empty($row->file_kisi_sas)) $percent += 10;
                            if (!empty($row->file_soal_sas)) $percent += 10;
                            if (!empty($row->total_modul_ajar) && $row->total_modul_ajar > 0) $percent += 10;
                            if (!empty($row->total_materi) && $row->total_materi > 0) $percent += 10;
                            ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                <td><?php echo html_escape(trim( $row->nama_tingkat . ' - ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?></td>
                                <td><?php echo html_escape($row->nama_ptk ?: '-') ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success-main" style="width: <?php echo $percent ?>%"></div>
                                        </div>
                                        <span class="text-sm"><?php echo $percent ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo url('perangkat_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:document-add-linear"></iconify-icon> Kelola
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- TAMPILAN MOBILE (Accordion List + Live Search) -->
            <div class="d-block d-md-none">
                <!-- Form Search Mobile -->
                <div class="mb-16">
                    <div class="position-relative">
                        <input type="text" id="mobileAdminPerangkatSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Mapel, Kelas, Guru, atau Tahun Pelajaran...">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                            <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                        </span>
                    </div>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="accordion custom-accordion" id="accordionAdminPerangkatMobile">
                        <?php foreach ($items as $i => $row): ?>
                            <?php
                            $percent = 0;
                            if (!empty($row->file_cp)) $percent += 10;
                            if (!empty($row->file_tp)) $percent += 10;
                            if (!empty($row->file_atp)) $percent += 10;
                            if (!empty($row->file_kktp)) $percent += 10;
                            if (!empty($row->file_kisi_sts)) $percent += 10;
                            if (!empty($row->file_soal_sts)) $percent += 10;
                            if (!empty($row->file_kisi_sas)) $percent += 10;
                            if (!empty($row->file_soal_sas)) $percent += 10;
                            if (!empty($row->total_modul_ajar) && $row->total_modul_ajar > 0) $percent += 10;
                            if (!empty($row->total_materi) && $row->total_materi > 0) $percent += 10;

                            $accordionId = "collapseAdminPerangkat" . $row->id_pembelajaran_mapel;
                            $headingId   = "headingAdminPerangkat" . $row->id_pembelajaran_mapel;
                            $nama_kelas  = trim($row->nama_tingkat . ' - ' . $row->nama_rombel);
                            $searchableText = strtolower(html_escape($row->nama_mapel . ' ' . $nama_kelas . ' ' . ($row->nama_ptk ?: '') . ' ' . $row->tahun_pelajaran . ' ' . $row->semester));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-admin-perangkat-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold"><?php echo html_escape($row->nama_mapel); ?></span>
                                                <span class="badge bg-success-100 text-success-700 px-8 py-2 radius-4 text-xs"><?php echo $percent; ?>% Kelengkapan</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:user-bold" class="me-4"></iconify-icon>Guru: <strong><?php echo html_escape($row->nama_ptk ?: '-'); ?></strong></span>
                                                <span><iconify-icon icon="solar:users-group-two-rounded-linear" class="me-4"></iconify-icon><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionAdminPerangkatMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <div class="row gy-2 text-xs mb-12">
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Tahun / Sem</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Guru Pengampu</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_ptk ?: '-'); ?></span>
                                            </div>
                                            <div class="col-12 mt-8">
                                                <span class="text-secondary-light d-block mb-2">Progress Kelengkapan</span>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-success-main" style="width: <?php echo $percent ?>%"></div>
                                                    </div>
                                                    <span class="fw-bold text-success-600 text-xs"><?php echo $percent ?>%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-16 pt-12 border-top">
                                            <a href="<?php echo url('perangkat_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-outline-primary btn-sm radius-8 w-100 d-flex align-items-center justify-content-center gap-2 py-8 fw-semibold">
                                                <iconify-icon icon="solar:document-add-linear" class="text-lg"></iconify-icon> Kelola Perangkat Pembelajaran
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noMobileAdminPerangkatResult" class="text-center py-24 text-secondary-light d-none">
                        <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                        <p class="text-sm">Data perangkat pembelajaran tidak ditemukan.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-24 text-secondary-light">
                        <p class="text-sm">Belum ada data perangkat pembelajaran.</p>
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
        $('#mobileAdminPerangkatSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-admin-perangkat-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobileAdminPerangkatResult').removeClass('d-none');
            } else {
                $('#noMobileAdminPerangkatResult').addClass('d-none');
            }
        });
    });
</script>
