<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary-600 text-white radius-top-8 gap-2">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:calendar-date-bold" class="text-xl"></iconify-icon>
                <h6 class="mb-0 text-white"><?php echo !empty($is_nonaktif) ? 'Agenda Pembelajaran Tidak Aktif' : 'Daftar Agenda Pembelajaran Harian'; ?></h6>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="form-check form-switch d-inline-flex align-items-center gap-2 px-12 py-6 radius-8 bg-white-10 border border-white-20 mb-0">
                    <input class="form-check-input cursor-pointer my-0" type="checkbox" role="switch" id="checkAgendaTidakAktif" <?php echo !empty($is_nonaktif) ? 'checked' : ''; ?> onchange="window.location.href = this.checked ? '<?php echo url('agenda_pembelajaran/nonaktif') ?>' : '<?php echo url('agenda_pembelajaran') ?>';">
                    <label class="form-check-label text-xs text-white fw-semibold cursor-pointer mb-0" for="checkAgendaTidakAktif">
                        Lihat agenda pembelajaran tidak aktif
                    </label>
                </div>
                <button type="button" class="btn btn-sm btn-warning text-primary-900 radius-8 px-12 py-8 d-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatAgenda">
                    <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
                    + Buat Agenda Pembelajaran Baru
                </button>
                <a href="<?php echo url('agenda_pembelajaran/ptk') ?>" class="btn btn-sm btn-light text-primary-600 radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-lg"></iconify-icon>
                    Agenda per Guru Pengampu
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Alert Flash Data -->
            <?php if ($this->session->flashdata('alert')): ?>
                <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
                    <?php echo $this->session->flashdata('alert') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Filter Bar per PTK / Guru -->
            <div class="p-16 bg-neutral-50 radius-8 border mb-24">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label text-xs fw-semibold text-secondary-light mb-6">Filter Pengampu Guru / PTK</label>
                        <select name="id_ptk" class="form-select radius-8 text-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Guru Pengampu --</option>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?php echo $t->id_ptk; ?>" <?php echo (!empty($selected_ptk) && (int)$selected_ptk === (int)$t->id_ptk) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($t->nama_ptk); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary radius-8 px-16 w-100 text-sm">
                            <iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon> Filter PTK
                        </button>
                        <?php if (!empty($selected_ptk)): ?>
                            <a href="<?php echo url('agenda_pembelajaran') ?>" class="btn btn-outline-secondary radius-8 px-12" title="Reset Filter">
                                <iconify-icon icon="solar:restart-bold"></iconify-icon>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12 d-flex flex-wrap align-items-center justify-content-between pt-12 border-top mt-12 gap-2">
                        <div class="form-check me-3">
                            <input class="form-check-input cursor-pointer" type="checkbox" id="checkAgendaFilterNonaktif" <?php echo !empty($is_nonaktif) ? 'checked' : ''; ?> onchange="window.location.href = this.checked ? '<?php echo url('agenda_pembelajaran/nonaktif') ?>' : '<?php echo url('agenda_pembelajaran') ?>';">
                            <label class="form-check-label text-xs fw-semibold text-primary-900 cursor-pointer" for="checkAgendaFilterNonaktif">
                                Lihat agenda pembelajaran tidak aktif (tahun &amp; semester terdahulu)
                            </label>
                        </div>
                        <?php if (!empty($is_nonaktif)): ?>
                            <span class="badge bg-warning-100 text-warning-800 radius-4 text-xs px-10 py-6">
                                <iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> Menampilkan Agenda Pembelajaran dari Tahun Pelajaran &amp; Semester Tidak Aktif
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success-100 text-success-800 radius-4 text-xs px-10 py-6">
                                <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> Menampilkan Agenda Pembelajaran Tahun Pelajaran &amp; Semester Aktif
                            </span>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($items)): ?>
                <!-- TAMPILAN DESKTOP (Tabel) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table bordered-table" id="agendaDataTable">
                        <thead>
                            <tr>
                                <th>Tahun/Sem</th>
                                <th>Kelas</th>
                                <th>Judul Agenda / Mapel</th>
                                <th>Pengampu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $row): ?>
                                <tr>
                                    <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                    <td><span class="badge bg-info-100 text-info-700 px-8 py-4 radius-4 fw-semibold"><?php echo html_escape($row->nama_tingkat . ' - ' . $row->nama_rombel) ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-primary-900 mb-1"><?php echo html_escape($row->judul_agenda) ?></div>
                                        <span class="text-xs text-secondary-light"><iconify-icon icon="solar:book-bookmark-linear" class="me-1"></iconify-icon><?php echo html_escape($row->nama_mapel) ?></span>
                                        <?php if (!empty($row->status_takeover) && $row->status_takeover === 'Ya'): ?>
                                            <span class="badge bg-warning-100 text-warning-700 ms-2 text-xs">Take-Over</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($row->nama_ptk ?: '-') ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?php echo url('agenda_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1 radius-8 px-12">
                                                <iconify-icon icon="solar:calendar-date-bold"></iconify-icon> Kelola Agenda
                                            </a>
                                            <a href="<?php echo url('agenda_pembelajaran/hapus_header/' . $row->id_pembelajaran_mapel) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus Agenda Pembelajaran ini?');" class="btn btn-sm btn-outline-danger radius-8 px-8" title="Hapus Agenda">
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
                            <input type="text" id="mobileAgendaSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Judul Agenda, Kelas, Mapel, atau Guru...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="accordion custom-accordion" id="accordionAgendaMobile">
                        <?php foreach ($items as $row): ?>
                            <?php
                            $accordionId = "collapseAgenda" . $row->id_pembelajaran_mapel;
                            $headingId   = "headingAgenda" . $row->id_pembelajaran_mapel;
                            $nama_kelas  = trim($row->nama_tingkat . ' - ' . $row->nama_rombel);
                            $searchableText = strtolower(html_escape($row->judul_agenda . ' ' . $row->nama_mapel . ' ' . $nama_kelas . ' ' . ($row->nama_ptk ?: '') . ' ' . $row->tahun_pelajaran));
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-agenda-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false">
                                        <div class="d-flex flex-column gap-1 w-100 me-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-primary-600 fw-bold text-truncate" style="max-width: 200px;"><?php echo html_escape($row->judul_agenda); ?></span>
                                                <span class="badge bg-info-100 text-info-700 px-8 py-2 radius-4 text-xs"><?php echo html_escape($nama_kelas); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                <span><iconify-icon icon="solar:user-bold" class="me-4"></iconify-icon><strong><?php echo html_escape($row->nama_ptk ?: '-'); ?></strong></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionAgendaMobile">
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
                                            <a href="<?php echo url('agenda_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-primary-600 btn-sm radius-8 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
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
                    <h6 class="fw-bold text-primary-900 mb-8">Belum Ada Agenda Pembelajaran Dibuat</h6>
                    <p class="text-secondary-light text-sm max-w-500-px mx-auto mb-20">
                        Agenda Pembelajaran berdiri sendiri secara mandiri. Silakan buat Agenda Pembelajaran Baru berdasarkan penugasan Mata Pelajaran &amp; Rombel yang telah diberikan pada semester aktif ini.
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
                            Pilih <strong>Tahun Pelajaran &amp; Semester</strong> dari master database, lalu pilih penugasan <strong>Mata Pelajaran &amp; Kelas/Rombel</strong> yang sesuai.
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-8">Pilih Tahun Pelajaran &amp; Semester <span class="text-danger">*</span></label>
                        <select name="id_tahun_pelajaran_modal" class="form-select radius-8 text-sm select-tp-modal" onchange="filterMapelOptionsByTp(this)">
                            <option value="">-- Semua Tahun Pelajaran &amp; Semester --</option>
                            <?php if (!empty($master_tahun_pelajaran)): ?>
                                <?php foreach ($master_tahun_pelajaran as $tp): ?>
                                    <?php 
                                    $sem_label = is_numeric($tp->semester) ? 'Semester ' . $tp->semester : (strpos(strtolower($tp->semester), 'semester') !== false ? $tp->semester : 'Semester ' . $tp->semester);
                                    $is_active = ($tp->status === 'Aktif');
                                    ?>
                                    <option value="<?php echo $tp->id_tahun_pelajaran; ?>" <?php echo $is_active ? 'selected' : ''; ?>>
                                        <?php echo html_escape($tp->tahun_pelajaran . ' ' . $sem_label . ($is_active ? ' (Aktif)' : ' (Tidak Aktif)')); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-4 d-block">Pilihan master Tahun Pelajaran &amp; Semester dari database.</span>
                    </div>

                    <div class="mb-20">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-8">Pilih Penugasan Mapel &amp; Kelas / Rombel <span class="text-danger">*</span></label>
                        <select name="id_pembelajaran_mapel" class="form-select radius-8 text-sm select-mapel-modal" required>
                            <option value="">-- Pilih Penugasan Mata Pelajaran &amp; Kelas --</option>
                            <?php if (!empty($available_mapels)): ?>
                                <?php foreach ($available_mapels as $amp): ?>
                                    <?php
                                    if ($amp->semester == '1' || strtolower($amp->semester) == 'ganjil') {
                                        $sem_fmt = 'Semester Ganjil';
                                    } elseif ($amp->semester == '2' || strtolower($amp->semester) == 'genap') {
                                        $sem_fmt = 'Semester Genap';
                                    } else {
                                        $sem_fmt = 'Semester ' . $amp->semester;
                                    }
                                    $tp_label = $amp->tahun_pelajaran . ' ' . $sem_fmt;
                                    $label_agenda = trim($tp_label . ' - ' . $amp->nama_tingkat . ' ' . $amp->nama_rombel . ' - ' . $amp->nama_mapel);
                                    ?>
                                    <option value="<?php echo $amp->id_pembelajaran_mapel; ?>" data-tp-id="<?php echo $amp->id_tahun_pelajaran; ?>" data-default-title="<?php echo html_escape($label_agenda); ?>">
                                        <?php echo html_escape('[' . $tp_label . '] ' . $amp->nama_tingkat . ' ' . $amp->nama_rombel . ' - ' . $amp->nama_mapel . ' (' . $amp->nama_ptk . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-4 d-block">Pilihan penugasan disesuaikan berdasarkan Tahun Pelajaran &amp; Semester yang dipilih di atas.</span>
                    </div>

                    <div class="mb-12">
                        <label class="form-label text-sm fw-semibold text-primary-900 mb-8">Judul Agenda Pembelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="judul_agenda" class="form-control radius-8 text-sm" placeholder="Contoh: 2026/2027 Semester Ganjil - VII Al Ghifari - Informatika" required>
                        <span class="text-xs text-secondary-light mt-4 d-block">Judul terisi otomatis dari penugasan yang dipilih di atas, dan tetap dapat Anda sesuaikan/edit jika diperlukan.</span>
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
    if (document.getElementById('agendaDataTable')) {
        new DataTable('#agendaDataTable', { order: [] });
    }

    $('#mobileAgendaSearch').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('.mobile-agenda-card').each(function() {
            var searchData = $(this).attr('data-search') || '';
            if (searchData.indexOf(term) !== -1) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    function filterMapelOptionsByTp(selectEl) {
        var tpId = $(selectEl).val();
        var modal = $(selectEl).closest('.modal');
        var mapelSelect = modal.find('.select-mapel-modal');
        
        mapelSelect.find('option').each(function() {
            var optTpId = $(this).attr('data-tp-id');
            if (!optTpId || !tpId || optTpId == tpId) {
                $(this).show().prop('disabled', false);
            } else {
                $(this).hide().prop('disabled', true);
            }
        });

        var selectedOpt = mapelSelect.find('option:selected');
        if (selectedOpt.length && selectedOpt.is(':disabled')) {
            mapelSelect.val('');
        }
    }

    $(document).ready(function() {
        $('.modal').on('shown.bs.modal', function () {
            var tpSelect = $(this).find('.select-tp-modal');
            if (tpSelect.length) {
                filterMapelOptionsByTp(tpSelect[0]);
            }
        });

        $('select[name="id_pembelajaran_mapel"]').on('change', function() {
            var selectedOpt = $(this).find('option:selected');
            var defaultTitle = selectedOpt.attr('data-default-title');
            var modal = $(this).closest('.modal');
            var inputJudul = modal.find('input[name="judul_agenda"]');
            if (defaultTitle) {
                inputJudul.val(defaultTitle);
            }
        });
    });
</script>
