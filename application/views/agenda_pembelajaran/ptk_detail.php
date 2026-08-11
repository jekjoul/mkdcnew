<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Breadcrumb & Header Card -->
    <div class="card mb-24 radius-12 border-0 shadow-xs">
        <div class="card-body p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-primary-900">Agenda Pembelajaran: <?php echo html_escape($ptk->nama_ptk); ?></h5>
                <p class="text-secondary-light text-sm mb-0">Daftar agenda pembelajaran harian mandiri per mata pelajaran &amp; rombel yang diampu oleh Guru ini.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-warning text-primary-900 radius-8 px-12 py-8 d-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatAgenda">
                    <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
                    + Buat Agenda Pembelajaran Baru
                </button>
                <a href="<?php echo url('agenda_pembelajaran/ptk') ?>" class="btn btn-outline-secondary radius-8 px-16 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon> Kembali ke Rekap Guru
                </a>
                <a href="<?php echo url('agenda_pembelajaran/cetak_ptk/' . $ptk->id_ptk) ?>" target="_blank" class="btn btn-success radius-8 px-16 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:printer-bold" class="text-lg"></iconify-icon> Cetak Daftar Agenda Guru
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Badge -->
    <?php $total_mapel = count($guru_mapel_list); ?>
    <div class="row g-3 mb-24">
        <div class="col-md-6 col-12">
            <div class="card border-0 radius-12 bg-primary-50 p-16 d-flex align-items-center flex-row justify-content-between">
                <div>
                    <span class="text-xs text-primary-600 fw-semibold text-uppercase">Guru Pengampu</span>
                    <h4 class="mb-0 text-primary-900 fw-bold mt-1"><?php echo html_escape($ptk->nama_ptk); ?></h4>
                    <span class="text-xs text-secondary-light">NIP: <?php echo html_escape(!empty($ptk->nip) ? $ptk->nip : (!empty($ptk->nuptk) ? $ptk->nuptk : '-')); ?></span>
                </div>
                <div class="w-48-px h-48-px bg-primary-600 rounded-circle d-flex align-items-center justify-content-center text-white">
                    <iconify-icon icon="solar:user-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card border-0 radius-12 bg-info-50 p-16 d-flex align-items-center flex-row justify-content-between">
                <div>
                    <span class="text-xs text-info-600 fw-semibold text-uppercase">Total Judul Agenda Dibuat</span>
                    <h3 class="mb-0 text-info-900 fw-bold mt-1"><?php echo $total_mapel; ?> Judul Agenda</h3>
                </div>
                <div class="w-48-px h-48-px bg-info-600 rounded-circle d-flex align-items-center justify-content-center text-white">
                    <iconify-icon icon="solar:notebook-bookmark-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Switch Teacher Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-body p-20">
            <div class="row align-items-center">
                <div class="col-md-3 text-secondary-light text-sm fw-semibold mb-2 mb-md-0">
                    <iconify-icon icon="solar:user-bold" class="me-1 text-primary-600"></iconify-icon> Pilih Guru Pengampu Lain:
                </div>
                <div class="col-md-9">
                    <select class="form-select radius-8 text-sm" onchange="if(this.value) window.location.href='<?php echo url('agenda_pembelajaran/ptk/') ?>' + this.value;">
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t->id_ptk; ?>" <?php echo ((int)$t->id_ptk === (int)$ptk->id_ptk) ? 'selected' : ''; ?>>
                                <?php echo html_escape($t->nama_ptk); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List Card -->
    <div class="card border-0 radius-12 shadow-xs">
        <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:calendar-date-bold" class="text-xl text-primary-600"></iconify-icon>
                <h6 class="mb-0 text-primary-900">Daftar Judul Agenda Pembelajaran Guru</h6>
            </div>
            <span class="badge bg-primary-100 text-primary-700 px-12 py-6 radius-6 text-xs fw-bold">
                Total <?php echo count($guru_mapel_list); ?> Agenda Dibuat
            </span>
        </div>
        <div class="card-body p-20">
            <?php if (!empty($guru_mapel_list)): ?>
                <!-- TAMPILAN DESKTOP (Tabel) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table bordered-table" id="agendaPtkTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Tahun/Sem</th>
                                <th>Kelas</th>
                                <th>Judul Agenda / Mapel</th>
                                <th>Pengampu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($guru_mapel_list as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?php echo $no++; ?></td>
                                    <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                    <td><span class="badge bg-info-100 text-info-700 px-8 py-4 radius-4 fw-semibold"><?php echo html_escape($row->nama_tingkat . ' - ' . $row->nama_rombel) ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-primary-900 mb-1"><?php echo html_escape($row->judul_agenda) ?></div>
                                        <span class="text-xs text-secondary-light"><iconify-icon icon="solar:book-bookmark-linear" class="me-1"></iconify-icon><?php echo html_escape($row->nama_mapel) ?></span>
                                        <?php if (!empty($row->status_takeover) && $row->status_takeover === 'Ya'): ?>
                                            <span class="badge bg-warning-100 text-warning-700 ms-2 text-xs">Take-Over</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($ptk->nama_ptk ?: '-') ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?php echo url('agenda_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1 radius-8 px-12">
                                                <iconify-icon icon="solar:calendar-date-bold"></iconify-icon> Kelola Agenda
                                            </a>
                                            <a href="<?php echo url('agenda_pembelajaran/hapus_header/' . $row->id_pembelajaran_mapel) ?>" onclick="return confirm('Hapus Agenda Pembelajaran ini?');" class="btn btn-sm btn-outline-danger radius-8 px-8" title="Hapus Agenda">
                                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TAMPILAN MOBILE (Accordion List + Live Search) -->
                <div class="d-block d-md-none">
                    <div class="mb-16">
                        <div class="position-relative">
                            <input type="text" id="mobileAgendaPtkSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Judul Agenda, Kelas, atau Mapel...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="accordion custom-accordion" id="accordionAgendaPtkMobile">
                        <?php foreach ($guru_mapel_list as $row): ?>
                            <?php
                            $accordionId = "collapseAgendaPtk" . $row->id_pembelajaran_mapel;
                            $headingId   = "headingAgendaPtk" . $row->id_pembelajaran_mapel;
                            $nama_kelas  = trim($row->nama_tingkat . ' - ' . $row->nama_rombel);
                            $searchableText = strtolower(html_escape($row->judul_agenda . ' ' . $row->nama_mapel . ' ' . $nama_kelas . ' ' . $ptk->nama_ptk . ' ' . $row->tahun_pelajaran));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-agenda-ptk-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold text-truncate" style="max-width: 220px;"><?php echo html_escape($row->judul_agenda); ?></span>
                                                <span class="badge bg-info-100 text-info-700 px-8 py-2 radius-4 text-xs"><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:user-bold" class="me-4"></iconify-icon><strong><?php echo html_escape($ptk->nama_ptk ?: '-'); ?></strong></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionAgendaPtkMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <div class="row gy-2 text-xs mb-12">
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Tahun / Semester</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-secondary-light d-block mb-2">Mata Pelajaran</span>
                                                <span class="fw-semibold text-primary-light"><?php echo html_escape($row->nama_mapel); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-16 pt-12 border-top d-flex align-items-center justify-content-between">
                                            <a href="<?php echo url('agenda_pembelajaran/hapus_header/' . $row->id_pembelajaran_mapel) ?>" onclick="return confirm('Hapus Agenda ini?');" class="btn btn-outline-danger btn-sm radius-8 px-10 text-xs">
                                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon> Hapus
                                            </a>
                                            <a href="<?php echo url('agenda_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-primary-600 btn-sm radius-8 px-16 py-6 text-xs d-inline-flex align-items-center gap-1">
                                                <iconify-icon icon="solar:calendar-date-bold"></iconify-icon> Kelola Agenda
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- EMPTY STATE -->
                <div class="text-center p-40 border radius-12 bg-neutral-50">
                    <div class="w-64-px h-64-px bg-primary-100 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
                        <iconify-icon icon="solar:notebook-square-bold" class="text-3xl"></iconify-icon>
                    </div>
                    <h6 class="fw-bold text-primary-900 mb-8">Guru Ini Belum Membuat Agenda Pembelajaran</h6>
                    <p class="text-secondary-light text-sm max-w-500-px mx-auto mb-20">
                        Guru Pengampu ini belum membuat Agenda Pembelajaran baru. Klik tombol "+ Buat Agenda Pembelajaran Baru" di bawah ini untuk memilih penugasan mata pelajaran &amp; memberikan judul agenda kustom.
                    </p>
                    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalBuatAgenda">
                        <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
                        + Buat Agenda Pembelajaran Baru
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL BUAT AGENDA PEMBELAJARAN BARU -->
<div class="modal fade" id="modalBuatAgenda" tabindex="-1" aria-labelledby="modalBuatAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-12 border-0">
            <div class="modal-header bg-primary-600 text-white radius-top-12">
                <h6 class="modal-title text-white d-flex align-items-center gap-2" id="modalBuatAgendaLabel">
                    <iconify-icon icon="solar:notebook-bookmark-bold" class="text-xl"></iconify-icon>
                    Buat Agenda Pembelajaran Baru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('agenda_pembelajaran/simpan_agenda_baru'); ?>" method="POST">
                <div class="modal-body p-24">
                    <div class="alert alert-primary bg-primary-50 border-primary-200 radius-8 p-16 mb-20 text-sm text-primary-900 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:info-circle-bold" class="text-xl text-primary-600 flex-shrink-0"></iconify-icon>
                        <div>
                            Pilih penugasan <strong>Mata Pelajaran &amp; Kelas/Rombel</strong> yang diberikan oleh Kurikulum/Wakasek, lalu berikan <strong>Judul Agenda Pembelajaran</strong> kustom sesuai kebutuhan Anda.
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-8">Pilih Penugasan Mapel &amp; Kelas / Rombel <span class="text-danger">*</span></label>
                        <select name="id_pembelajaran_mapel" class="form-select radius-8 text-sm" required>
                            <option value="">-- Pilih Penugasan Mata Pelajaran &amp; Kelas --</option>
                            <?php if (!empty($available_mapels)): ?>
                                <?php foreach ($available_mapels as $amp): ?>
                                    <option value="<?php echo $amp->id_pembelajaran_mapel; ?>">
                                        <?php echo html_escape('[' . $amp->tahun_pelajaran . ' - Sem ' . $amp->semester . '] ' . $amp->nama_tingkat . ' ' . $amp->nama_rombel . ' - ' . $amp->nama_mapel . ' (' . $amp->nama_ptk . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-4 d-block">Pilihan ini diambil dari penugasan pembelajaran semester aktif.</span>
                    </div>

                    <div class="mb-12">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-8">Judul Agenda Pembelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="judul_agenda" class="form-control radius-8 text-sm" placeholder="Contoh: Agenda Pembelajaran Matematika Wajib Kelas X IPA 1 Semester Ganjil" required>
                        <span class="text-xs text-secondary-light mt-4 d-block">Nama atau judul kustom untuk Agenda Pembelajaran Harian yang Anda buat.</span>
                    </div>
                </div>
                <div class="modal-footer bg-neutral-50 radius-bottom-12 p-16">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-16" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 fw-bold d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon> Simpan &amp; Mulai Kelola Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    if (document.getElementById('agendaPtkTable')) {
        new DataTable('#agendaPtkTable', { order: [] });
    }

    $('#mobileAgendaPtkSearch').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('.mobile-agenda-ptk-card').each(function() {
            var searchData = $(this).attr('data-search') || '';
            if (searchData.indexOf(term) !== -1) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });
</script>
