<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h6 class="text-light mb-0">Data Siswa Terampu</h6>
        </div>
        <div class="card-body">
            <!-- TAMPILAN DESKTOP (Tabel) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table bordered-table mb-0" id="dataTable">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col">NISN</th>
                            <th scope="col">NIPD</th>
                            <th scope="col">JK</th>
                            <th scope="col">Rombel</th>
                            <th scope="col">Mapel Saya</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?php echo $i + 1 ?></td>
                                <td class="fw-semibold"><?php echo html_escape($row->nama_siswa) ?></td>
                                <td><?php echo html_escape($row->nisn ?: '-') ?></td>
                                <td><?php echo html_escape($row->nipd ?: '-') ?></td>
                                <td><?php echo html_escape($row->jenis_kelamin ?: '-') ?></td>
                                <td><?php echo html_escape($row->rombel ?: '-') ?></td>
                                <td><?php echo html_escape($row->mapel ?: '-') ?></td>
                                <td><span class="badge bg-success-100 text-success-600"><?php echo html_escape($row->status_keaktifan ?: '-') ?></span></td>
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
                        <input type="text" id="mobileGuruSiswaSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Nama Siswa, NISN, NIPD, Rombel, atau Mapel...">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                            <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                        </span>
                    </div>
                </div>

                <?php if (!empty($siswa)): ?>
                    <div class="accordion custom-accordion" id="accordionGuruSiswaMobile">
                        <?php foreach ($siswa as $i => $row): ?>
                            <?php
                            $accordionId = "collapseGuruSiswa" . $i;
                            $headingId   = "headingGuruSiswa" . $i;
                            $searchableText = strtolower(html_escape($row->nama_siswa . ' ' . $row->nisn . ' ' . $row->nipd . ' ' . $row->rombel . ' ' . $row->mapel));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-guru-siswa-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false" aria-controls="<?php echo $accordionId; ?>">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold"><?php echo html_escape($row->nama_siswa); ?></span>
                                                <span class="badge bg-success-100 text-success-600 px-8 py-2 radius-4 text-xs"><?php echo html_escape($row->status_keaktifan ?: 'Aktif'); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:user-id-linear" class="me-4"></iconify-icon>NIPD: <strong><?php echo html_escape($row->nipd ?: '-'); ?></strong></span>
                                                <span><iconify-icon icon="solar:users-group-two-rounded-linear" class="me-4"></iconify-icon>Rombel: <strong><?php echo html_escape($row->rombel ?: '-'); ?></strong></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionGuruSiswaMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <div class="row gy-2 text-xs">
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">NISN</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->nisn ?: '-'); ?></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">NIPD</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->nipd ?: '-'); ?></span>
                                            </div>
                                            <div class="col-6 mt-12">
                                                <span class="text-secondary-light d-block mb-2">Jenis Kelamin</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->jenis_kelamin ?: '-'); ?></span>
                                            </div>
                                            <div class="col-6 mt-12">
                                                <span class="text-secondary-light d-block mb-2">Rombel</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->rombel ?: '-'); ?></span>
                                            </div>
                                            <div class="col-12 mt-12">
                                                <span class="text-secondary-light d-block mb-2">Mapel Saya</span>
                                                <span class="badge bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs"><?php echo html_escape($row->mapel ?: '-'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noMobileGuruSearchResult" class="text-center py-24 text-secondary-light d-none">
                        <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                        <p class="text-sm">Data siswa terampu tidak ditemukan.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-24 text-secondary-light">
                        <p class="text-sm">Belum ada data siswa terampu.</p>
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
        $('#mobileGuruSiswaSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-guru-siswa-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobileGuruSearchResult').removeClass('d-none');
            } else {
                $('#noMobileGuruSearchResult').addClass('d-none');
            }
        });
    });
</script>
