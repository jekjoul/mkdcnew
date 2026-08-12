<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <form id="exportForm" action="<?php echo url('export_ptk/export_excel'); ?>" method="POST" target="_blank">

    <!-- Description Card -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="text-primary-light mb-0">Export Data PTK Massal</h6>
                        <p class="text-sm text-neutral-500 mt-4">
                            Tentukan kolom data kepegawaian yang ingin diexport, lalu pilih PTK (Pendidik dan Tenaga Kependidikan) mana saja yang datanya ingin diunduh.
                        </p>
                    </div>
                    <div>
                        <button type="button" id="btnLoadPtk" class="btn btn-primary-600 radius-8 py-11 px-24 d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-xl"></iconify-icon> Tampilkan Daftar PTK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Selection Checklist Card Grouped by Category -->
    <div class="row gy-4 mb-24 d-none" id="fieldsCard">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-12 bg-transparent d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="text-primary-light text-md mb-0">Pilih Field / Kolom Data Kepegawaian</h6>
                        <small class="text-neutral-400">Centang kolom data PTK yang ingin dimasukkan ke file Excel</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnSelectAllFields" class="btn btn-sm btn-outline-primary radius-8 py-4 px-12 text-xs">Centang Semua Kolom</button>
                        <button type="button" id="btnClearAllFields" class="btn btn-sm btn-outline-danger radius-8 py-4 px-12 text-xs">Kosongkan Semua</button>
                    </div>
                </div>
                <div class="card-body">
                    
                    <?php foreach ($fields_grouped as $category => $fields): ?>
                        <div class="mb-24">
                            <h6 class="text-secondary-600 text-sm fw-bold mb-12 border-bottom pb-4"><?php echo html_escape($category); ?></h6>
                            <div class="row g-3">
                                <?php foreach ($fields as $key => $label): ?>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="form-check p-12 border radius-8 d-flex align-items-center gap-2 bg-hover-neutral-50 cursor-pointer">
                                            <input type="checkbox" name="fields[]" value="<?php echo $key; ?>" id="field_<?php echo $key; ?>" 
                                                   class="field-checkbox form-check-input" checked>
                                            <label for="field_<?php echo $key; ?>" class="form-check-label text-neutral-800 fw-medium text-sm mb-0 cursor-pointer w-100">
                                                <?php echo html_escape($label); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Loading Card -->
    <div class="row gy-4 mb-24 d-none" id="loadingSection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-40 text-center">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <p class="mt-16 text-neutral-500">Mengambil data PTK...</p>
            </div>
        </div>
    </div>

    <!-- Empty Card -->
    <div class="row gy-4 mb-24 d-none" id="emptySection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-40 text-center">
                <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-6xl text-neutral-300 mb-16"></iconify-icon>
                <h6 class="text-neutral-600">Tidak Ada PTK Aktif</h6>
                <p class="text-neutral-400">Tidak ada PTK berstatus aktif ditemukan di sistem.</p>
            </div>
        </div>
    </div>

    <!-- PTK Selection Checklist Table Card -->
    <div class="row gy-4 mb-24 d-none" id="tableSection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-16 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="text-primary-light mb-0" id="tableHeaderLabel">Pilih PTK yang Ingin Diexport</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkSelectAllPtk" checked>
                        <label class="form-check-label text-sm text-neutral-600 fw-bold" for="checkSelectAllPtk">
                            Pilih Semua PTK
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="ptkSelectionTable">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th scope="col" class="text-center" width="60">No</th>
                                    <th scope="col" class="text-center" width="80">Pilih</th>
                                    <th scope="col">Nama PTK</th>
                                    <th scope="col" class="text-center">NIK / NIY</th>
                                    <th scope="col" class="text-center">Penugasan</th>
                                    <th scope="col" class="text-center">L/P</th>
                                </tr>
                            </thead>
                            <tbody id="ptkTableBody">
                                <!-- Built dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-20">
                        <button type="submit" id="btnSubmitExport" class="btn btn-success-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:file-spreadsheet" class="text-xl"></iconify-icon> Export ke Excel (.xls)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    let ptkList = [];

    function updateExportButtonState() {
        const hasCheckedField = $('.field-checkbox:checked').length > 0;
        const hasCheckedPtk = $('.ptk-checkbox:checked').length > 0;

        if (hasCheckedField && hasCheckedPtk) {
            $('#btnSubmitExport').removeAttr('disabled');
        } else {
            $('#btnSubmitExport').attr('disabled', 'disabled');
        }
    }

    function hideAllSections() {
        $('#loadingSection').addClass('d-none');
        $('#emptySection').addClass('d-none');
        $('#tableSection').addClass('d-none');
    }

    // Toggle Select All Fields
    $('#btnSelectAllFields').on('click', function() {
        $('.field-checkbox').prop('checked', true);
        updateExportButtonState();
    });

    // Clear All Fields
    $('#btnClearAllFields').on('click', function() {
        $('.field-checkbox').prop('checked', false);
        updateExportButtonState();
    });

    // Load data and render Table
    $('#btnLoadPtk').on('click', function() {
        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("export_ptk/get_ptk") ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loadingSection').addClass('d-none');

                if (!response.status) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                    return;
                }

                ptkList = response.ptk;
                if (ptkList.length === 0) {
                    $('#emptySection').removeClass('d-none');
                    return;
                }

                // Show fields card after loading PTK
                $('#fieldsCard').removeClass('d-none');
                renderPtkTable();
            },
            error: function() {
                $('#loadingSection').addClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal memuat daftar PTK.'
                });
            }
        });
    });

    function renderPtkTable() {
        let bodyHtml = '';
        ptkList.forEach(function(p, index) {
            bodyHtml += `<tr>`;
            bodyHtml += `<td class="text-center">${index + 1}</td>`;
            bodyHtml += `<td class="text-center">
                <input type="checkbox" name="ptk_ids[]" value="${p.id_ptk}" class="ptk-checkbox form-check-input" checked>
            </td>`;
            bodyHtml += `<td><span class="fw-semibold text-neutral-800">${p.nama_ptk}</span></td>`;
            bodyHtml += `<td class="text-center">${p.nik ? p.nik : '-'} / ${p.niy ? p.niy : '-'}</td>`;
            bodyHtml += `<td class="text-center"><span class="badge bg-primary-100 text-primary-600">${p.penugasan ? p.penugasan : '-'}</span></td>`;
            bodyHtml += `<td class="text-center">${p.jenis_kelamin === 'Laki-laki' ? 'L' : 'P'}</td>`;
            bodyHtml += `</tr>`;
        });

        $('#ptkTableBody').html(bodyHtml);
        $('#checkSelectAllPtk').prop('checked', true);
        
        // Add listener to PTK checkboxes
        $('.ptk-checkbox').on('change', function() {
            updateExportButtonState();
            // Update Select All Checkbox state
            const total = $('.ptk-checkbox').length;
            const checked = $('.ptk-checkbox:checked').length;
            $('#checkSelectAllPtk').prop('checked', total === checked);
        });

        $('#tableSection').removeClass('d-none');
        updateExportButtonState();
    }

    // Toggle Select All PTK
    $('#checkSelectAllPtk').on('change', function() {
        const checked = $(this).is(':checked');
        $('.ptk-checkbox').prop('checked', checked);
        updateExportButtonState();
    });

    // Form Submit verification
    $('#exportForm').on('submit', function(e) {
        const hasCheckedField = $('.field-checkbox:checked').length > 0;
        const hasCheckedPtk = $('.ptk-checkbox:checked').length > 0;

        if (!hasCheckedField || !hasCheckedPtk) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Harap centang minimal satu kolom data dan minimal satu PTK yang ingin diexport.'
            });
        }
    });
});
</script>
