<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <form id="exportForm" action="<?php echo url('export_siswa/export_excel'); ?>" method="POST" target="_blank">

    <!-- Description & Rombel Selection Checklist Grouped by Lembaga -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="text-primary-light mb-0">Pilih Rombel / Kelas (Bisa Pilih Beberapa atau Semua)</h6>
                        <p class="text-sm text-neutral-500 mt-4">
                            Centang satu atau beberapa rombel di bawah ini untuk menggabungkan data siswa yang ingin diexport.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnSelectAllRombel" class="btn btn-sm btn-outline-primary radius-8 py-6 px-12 text-xs">Pilih Semua Rombel</button>
                        <button type="button" id="btnClearAllRombel" class="btn btn-sm btn-outline-danger radius-8 py-6 px-12 text-xs">Kosongkan Rombel</button>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Loop Grouped Lembaga -->
                        <?php if (!empty($rombel_grouped)): ?>
                            <div class="row gy-4">
                                <?php foreach ($rombel_grouped as $lg_id => $lg): ?>
                                    <div class="col-12 border-bottom pb-15 mb-10">
                                        <div class="d-flex justify-content-between align-items-center mb-10">
                                            <span class="badge bg-primary-100 text-primary-600 fw-bold px-12 py-6 text-sm">
                                                Lembaga: <?php echo html_escape($lg['nama_lembaga']); ?> (<?php echo html_escape($lg['nama_lembaga_singkat']); ?>)
                                            </span>
                                            <button type="button" class="btn-select-lembaga-rombel btn btn-xs btn-light radius-6" data-lembaga="<?php echo $lg_id; ?>">Pilih Rombel Lembaga Ini</button>
                                        </div>
                                        <div class="row g-3 row-cols-xxl-4 row-cols-lg-3 row-cols-sm-2 row-cols-1">
                                            <?php foreach ($lg['list'] as $r): ?>
                                                <div class="col">
                                                    <div class="form-check p-12 border radius-8 d-flex align-items-center gap-2 bg-hover-neutral-50 cursor-pointer">
                                                        <input type="checkbox" name="id_pembelajaran[]" value="<?php echo $r->id_pembelajaran; ?>" 
                                                               id="rombel_<?php echo $r->id_pembelajaran; ?>" 
                                                               class="rombel-checkbox form-check-input" 
                                                               data-lembaga="<?php echo $lg_id; ?>">
                                                        <label for="rombel_<?php echo $r->id_pembelajaran; ?>" class="form-check-label text-neutral-800 fw-medium text-sm mb-0 cursor-pointer w-100">
                                                            <?php echo html_escape($r->nama_tingkat . ' - ' . $r->nama_rombel); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-20 text-neutral-400">
                                Tidak ada rombel aktif saat ini.
                            </div>
                        <?php endif; ?>

                        <div class="row mt-20">
                            <div class="col-12">
                                <button type="button" id="btnLoadStudents" class="btn btn-primary-600 radius-8 py-11 px-24 d-flex align-items-center justify-content-center gap-2" disabled>
                                    <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-xl"></iconify-icon> Tampilkan Daftar Siswa Rombel Terpilih
                                </button>
                            </div>
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
                        <h6 class="text-primary-light text-md mb-0">Pilih Field / Kolom Data untuk Diexport</h6>
                        <small class="text-neutral-400">Centang kolom data siswa yang ingin dimasukkan ke file Excel</small>
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
                <p class="mt-16 text-neutral-500">Mengambil data siswa...</p>
            </div>
        </div>
    </div>

    <!-- Empty Card -->
    <div class="row gy-4 mb-24 d-none" id="emptySection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-40 text-center">
                <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-6xl text-neutral-300 mb-16"></iconify-icon>
                <h6 class="text-neutral-600">Tidak Ada Siswa Aktif</h6>
                <p class="text-neutral-400">Tidak ada siswa aktif yang ditemukan pada rombel-rombel terpilih.</p>
            </div>
        </div>
    </div>

    <!-- Student Selection Checklist Table Card -->
    <div class="row gy-4 mb-24 d-none" id="tableSection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-16 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="text-primary-light mb-0" id="tableHeaderLabel">Pilih Siswa yang Ingin Diexport</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkSelectAllStudents" checked>
                        <label class="form-check-label text-sm text-neutral-600 fw-bold" for="checkSelectAllStudents">
                            Pilih Semua Siswa
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="studentSelectionTable">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th scope="col" class="text-center" width="60">No</th>
                                    <th scope="col" class="text-center" width="80">Pilih</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">Lembaga / Rombel</th>
                                    <th scope="col" class="text-center">NISN / NIPD</th>
                                    <th scope="col" class="text-center">L/P</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
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
    let studentList = [];

    // Checkbox Rombel change listener
    $('.rombel-checkbox').on('change', function() {
        updateLoadButtonState();
        hideAllSections();
        $('#fieldsCard').addClass('d-none');
    });

    function updateLoadButtonState() {
        const checkedRombelCount = $('.rombel-checkbox:checked').length;
        if (checkedRombelCount > 0) {
            $('#btnLoadStudents').removeAttr('disabled');
        } else {
            $('#btnLoadStudents').attr('disabled', 'disabled');
        }
    }

    function updateExportButtonState() {
        const hasCheckedField = $('.field-checkbox:checked').length > 0;
        const hasCheckedStudent = $('.student-checkbox:checked').length > 0;

        if (hasCheckedField && hasCheckedStudent) {
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

    // Toggle Select All Rombel
    $('#btnSelectAllRombel').on('click', function() {
        $('.rombel-checkbox').prop('checked', true);
        updateLoadButtonState();
        hideAllSections();
        $('#fieldsCard').addClass('d-none');
    });

    // Clear All Rombel
    $('#btnClearAllRombel').on('click', function() {
        $('.rombel-checkbox').prop('checked', false);
        updateLoadButtonState();
        hideAllSections();
        $('#fieldsCard').addClass('d-none');
    });

    // Select Rombel per Lembaga
    $('.btn-select-lembaga-rombel').on('click', function() {
        const lembaga_id = $(this).data('lembaga');
        const checkboxes = $(`.rombel-checkbox[data-lembaga="${lembaga_id}"]`);
        
        // Check if all are already checked
        const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
        checkboxes.prop('checked', !allChecked);

        updateLoadButtonState();
        hideAllSections();
        $('#fieldsCard').addClass('d-none');
    });

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
    $('#btnLoadStudents').on('click', function() {
        const selectedRombels = [];
        $('.rombel-checkbox:checked').each(function() {
            selectedRombels.push($(this).val());
        });

        if (selectedRombels.length === 0) return;

        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("export_siswa/get_students") ?>',
            type: 'POST',
            data: { id_pembelajaran: selectedRombels },
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

                studentList = response.students;
                if (studentList.length === 0) {
                    $('#emptySection').removeClass('d-none');
                    return;
                }

                // Show fields card after loading students
                $('#fieldsCard').removeClass('d-none');
                renderStudentTable();
            },
            error: function() {
                $('#loadingSection').addClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal memuat daftar siswa.'
                });
            }
        });
    });

    function renderStudentTable() {
        let bodyHtml = '';
        studentList.forEach(function(s, index) {
            const rombel_info = `${s.nama_lembaga_singkat ? s.nama_lembaga_singkat : s.bentuk_pendidikan} - ${s.nama_rombel}`;
            bodyHtml += `<tr>`;
            bodyHtml += `<td class="text-center">${index + 1}</td>`;
            bodyHtml += `<td class="text-center">
                <input type="checkbox" name="students[]" value="${s.id_siswa}" class="student-checkbox form-check-input" checked>
            </td>`;
            bodyHtml += `<td><span class="fw-semibold text-neutral-800">${s.nama_siswa}</span></td>`;
            bodyHtml += `<td class="text-center"><span class="badge bg-secondary-100 text-secondary-600">${rombel_info}</span></td>`;
            bodyHtml += `<td class="text-center">${s.nisn ? s.nisn : '-'} / ${s.nipd ? s.nipd : '-'}</td>`;
            bodyHtml += `<td class="text-center">${s.jenis_kelamin === 'Laki-laki' ? 'L' : 'P'}</td>`;
            bodyHtml += `</tr>`;
        });

        $('#studentTableBody').html(bodyHtml);
        
        const selectedRombelsText = $('.rombel-checkbox:checked').length + ' Rombel';
        $('#tableHeaderLabel').text('Pilih Siswa - Gabungan ' + selectedRombelsText);
        $('#checkSelectAllStudents').prop('checked', true);
        
        // Add listener to student checkboxes
        $('.student-checkbox').on('change', function() {
            updateExportButtonState();
            // Update Select All Checkbox state
            const total = $('.student-checkbox').length;
            const checked = $('.student-checkbox:checked').length;
            $('#checkSelectAllStudents').prop('checked', total === checked);
        });

        $('#tableSection').removeClass('d-none');
        updateExportButtonState();
    }

    // Toggle Select All Students
    $('#checkSelectAllStudents').on('change', function() {
        const checked = $(this).is(':checked');
        $('.student-checkbox').prop('checked', checked);
        updateExportButtonState();
    });

    // Form Submit verification
    $('#exportForm').on('submit', function(e) {
        const hasCheckedField = $('.field-checkbox:checked').length > 0;
        const hasCheckedStudent = $('.student-checkbox:checked').length > 0;

        if (!hasCheckedField || !hasCheckedStudent) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Harap centang minimal satu kolom data dan minimal satu siswa yang ingin diexport.'
            });
        }
    });
});
</script>
