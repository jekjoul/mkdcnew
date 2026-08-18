<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-primary-600 text-white radius-top-8 gap-2">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-xl"></iconify-icon>
                <h6 class="mb-0 text-white">Daftar Agenda Pembelajaran Harian per Guru Pengampu</h6>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-warning text-primary-900 radius-8 px-12 py-8 d-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatAgenda">
                    <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
                    + Buat Agenda Pembelajaran Baru
                </button>
                <a href="<?php echo url('agenda_pembelajaran') ?>" class="btn btn-sm btn-outline-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:list-bold" class="text-lg"></iconify-icon>
                    Lihat per Mapel &amp; Rombel
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

            <div class="alert alert-primary bg-primary-50 border-primary-200 radius-8 p-16 mb-24 text-sm text-primary-900 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:info-circle-bold" class="text-xl text-primary-600 flex-shrink-0"></iconify-icon>
                <div>
                    Pilih nama <strong>Guru Pengampu</strong> di bawah ini untuk melihat <strong>Daftar Agenda Pembelajaran Harian</strong> mandiri yang telah dibuat oleh guru tersebut.
                </div>
            </div>

            <?php if (!empty($ptk_summary)): ?>
                <!-- TAMPILAN DESKTOP (Tabel Rekap PTK) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table bordered-table" id="ptkSummaryTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Guru Pengampu</th>
                                <th>NIP / NUPTK</th>
                                <th class="text-center">Jumlah Judul Agenda</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($ptk_summary as $row): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <div class="fw-bold text-primary-900"><?php echo html_escape($row->nama_ptk); ?></div>
                                    </td>
                                    <td><span class="text-xs text-secondary-light"><?php echo html_escape(!empty($row->nip) ? $row->nip : (!empty($row->nuptk) ? $row->nuptk : '-')); ?></span></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-100 text-primary-700 px-12 py-6 radius-6 fw-bold text-xs"><?php echo (int) $row->total_mapel; ?> Judul Agenda</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo url('agenda_pembelajaran/ptk/' . $row->id_ptk) ?>" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1 radius-8 px-16">
                                            <iconify-icon icon="solar:eye-bold"></iconify-icon> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TAMPILAN MOBILE (List Card PTK) -->
                <div class="d-block d-md-none">
                    <div class="mb-16">
                        <div class="position-relative">
                            <input type="text" id="mobilePtkSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Nama Guru Pengampu...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-12" id="mobilePtkList">
                        <?php foreach ($ptk_summary as $row): ?>
                            <div class="card border radius-8 p-16 mobile-ptk-card" data-search="<?php echo strtolower(html_escape($row->nama_ptk . ' ' . (!empty($row->nip) ? $row->nip : ''))); ?>">
                                <div class="d-flex align-items-center justify-content-between mb-8">
                                    <span class="fw-bold text-primary-600 text-sm"><?php echo html_escape($row->nama_ptk); ?></span>
                                    <span class="badge bg-primary-100 text-primary-700 px-8 py-2 radius-4 text-xs fw-bold"><?php echo (int) $row->total_mapel; ?> Judul Agenda</span>
                                </div>
                                <div class="text-xs text-secondary-light mb-12">
                                    NIP/NUPTK: <?php echo html_escape(!empty($row->nip) ? $row->nip : (!empty($row->nuptk) ? $row->nuptk : '-')); ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-8 border-top">
                                    <span class="text-xs text-neutral-600 fw-semibold"><?php echo (int) $row->total_mapel; ?> Judul Agenda Dimiliki</span>
                                    <a href="<?php echo url('agenda_pembelajaran/ptk/' . $row->id_ptk) ?>" class="btn btn-primary-600 btn-sm radius-8 px-16 py-4 text-xs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:eye-bold"></iconify-icon> Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- EMPTY STATE -->
                <div class="text-center p-40 border radius-12 bg-neutral-50">
                    <div class="w-64-px h-64-px bg-primary-100 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
                        <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-3xl"></iconify-icon>
                    </div>
                    <h6 class="fw-bold text-primary-900 mb-8">Belum Ada Agenda Pembelajaran yang Dibuat</h6>
                    <p class="text-secondary-light text-sm max-w-500-px mx-auto mb-20">
                        Agenda Pembelajaran berdiri sendiri secara mandiri. Guru-guru dapat membuat Agenda Pembelajaran kustom sesuai mata pelajaran &amp; rombel yang ditugaskan pada semester aktif ini.
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
    if (document.getElementById('ptkSummaryTable')) {
        new DataTable('#ptkSummaryTable', { order: [] });
    }

    $('#mobilePtkSearch').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('.mobile-ptk-card').each(function() {
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
