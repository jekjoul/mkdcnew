<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Pembelajaran Saya</h6>
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
                            <th>JP</th>
                            <th>Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</td>
                                <td><?php echo html_escape(trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?> <?php echo $row->mapel_singkat ? '(' . html_escape($row->mapel_singkat) . ')' : '' ?></td>
                                <td><?php echo (int) $row->jumlah_jam ?></td>
                                <td><?php echo (int) $row->jumlah_siswa ?></td>
                                <td>
                                    <a href="<?php echo url('guru/input_nilai/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Input Nilai
                                    </a>
                                    <a href="<?php echo url('guru/perangkat_detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:document-add-linear"></iconify-icon> Perangkat
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
                        <input type="text" id="mobilePembelajaranSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Kelas, Mapel, atau Tahun Pelajaran...">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                            <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                        </span>
                    </div>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="accordion custom-accordion" id="accordionPembelajaranMobile">
                        <?php foreach ($items as $i => $row): ?>
                            <?php
                            $accordionId = "collapsePembelajaran" . $row->id_pembelajaran_mapel;
                            $headingId   = "headingPembelajaran" . $row->id_pembelajaran_mapel;
                            $nama_kelas  = trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel);
                            $searchableText = strtolower(html_escape($row->nama_mapel . ' ' . $nama_kelas . ' ' . $row->tahun_pelajaran . ' ' . $row->semester));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-pembelajaran-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold"><?php echo html_escape($row->nama_mapel); ?></span>
                                                <span class="badge bg-primary-50 text-primary-600 px-8 py-2 radius-4 text-xs"><?php echo (int)$row->jumlah_jam; ?> JP</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:users-group-two-rounded-linear" class="me-4"></iconify-icon><?php echo html_escape($nama_kelas); ?></span>
                                                <span><iconify-icon icon="solar:user-bold" class="me-4"></iconify-icon>Siswa: <strong><?php echo (int)$row->jumlah_siswa; ?></strong></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionPembelajaranMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <div class="row gy-2 text-xs mb-12">
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Tahun / Sem</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Jam Mengajar</span>
                                                <span class="fw-semibold text-primary-light"><?php echo (int)$row->jumlah_jam; ?> JP / Minggu</span>
                                            </div>
                                            <div class="col-12 mt-8">
                                                <span class="text-secondary-light d-block mb-2">Kelas / Rombel</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-8 mt-16 pt-12 border-top">
                                            <a href="<?php echo url('guru/input_nilai/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-outline-primary btn-sm radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1 py-8 text-xs fw-semibold">
                                                <iconify-icon icon="solar:clipboard-list-linear"></iconify-icon> Input Nilai
                                            </a>
                                            <a href="<?php echo url('guru/perangkat_detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-outline-success btn-sm radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1 py-8 text-xs fw-semibold">
                                                <iconify-icon icon="solar:document-add-linear"></iconify-icon> Perangkat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noMobilePembelajaranResult" class="text-center py-24 text-secondary-light d-none">
                        <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                        <p class="text-sm">Data pembelajaran tidak ditemukan.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-24 text-secondary-light">
                        <p class="text-sm">Belum ada data pembelajaran saya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable', { order: [] });

    $(document).ready(function() {
        $('#mobilePembelajaranSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-pembelajaran-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobilePembelajaranResult').removeClass('d-none');
            } else {
                $('#noMobilePembelajaranResult').addClass('d-none');
            }
        });
    });
</script>
