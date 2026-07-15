<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Description & Rombel Filter -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent">
                    <h6 class="text-primary-light mb-0">Pilih Rombel & Kolom Edit</h6>
                    <p class="text-sm text-neutral-500 mt-4">
                        Pilih rombel dan centang kolom/field yang ingin Anda edit secara massal dalam satu halaman.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row gy-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Rombel / Kelas</label>
                            <select name="id_pembelajaran" id="id_pembelajaran" class="form-select radius-8">
                                <option value="">-- Pilih Rombel --</option>
                                <?php foreach ($rombel_list as $r): ?>
                                    <option value="<?php echo $r->id_pembelajaran; ?>">
                                        <?php echo html_escape($r->nama_lembaga_singkat . ' - ' . $r->nama_tingkat . ' - ' . $r->nama_rombel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="btnLoadTable" class="btn btn-primary-600 radius-8 w-100 py-11 d-flex align-items-center justify-content-center gap-2" disabled>
                                <iconify-icon icon="solar:table-double-columns-linear" class="text-xl"></iconify-icon> Tampilkan Tabel Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Selection Checklist Card -->
    <div class="row gy-4 mb-24 d-none" id="fieldsCard">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-12 bg-transparent">
                    <h6 class="text-primary-light text-md mb-0">Pilih Field / Kolom untuk Diedit</h6>
                    <small class="text-neutral-400">Centang minimal satu field di bawah ini</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($fields as $key => $f): ?>
                            <div class="col-xxl-3 col-lg-4 col-sm-6">
                                <div class="form-check p-12 border radius-8 d-flex align-items-center gap-2 bg-hover-neutral-50 cursor-pointer">
                                    <input type="checkbox" value="<?php echo $key; ?>" id="field_<?php echo $key; ?>" 
                                           class="field-checkbox form-check-input" 
                                           data-label="<?php echo html_escape($f['label']); ?>" 
                                           data-type="<?php echo $f['type']; ?>"
                                           <?php echo isset($f['options']) ? "data-options='" . json_encode($f['options']) . "'" : ""; ?>>
                                    <label for="field_<?php echo $key; ?>" class="form-check-label text-neutral-800 fw-medium text-sm mb-0 cursor-pointer w-100">
                                        <?php echo html_escape($f['label']); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                <h6 class="text-neutral-600">Tidak Ada Siswa</h6>
                <p class="text-neutral-400">Tidak ada siswa yang terdaftar dalam rombel ini.</p>
            </div>
        </div>
    </div>

    <!-- Edit Inline Table Card -->
    <div class="row gy-4 mb-24 d-none" id="tableSection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-16 bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="text-primary-light mb-0" id="tableHeaderLabel">Tabel Edit Inline</h6>
                </div>
                <div class="card-body">
                    <form id="inlineEditForm">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="inlineEditTable">
                                <thead class="bg-neutral-50">
                                    <tr id="tableHeadRow">
                                        <!-- Built dynamically -->
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Built dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-20">
                            <button type="button" id="btnSave" class="btn btn-success-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:diskette-linear" class="text-xl"></iconify-icon> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    let studentList = [];

    // Reset page view on Rombel select change
    $('#id_pembelajaran').on('change', function() {
        const id_pembelajaran = $(this).val();
        hideAllSections();
        if (id_pembelajaran) {
            $('#fieldsCard').removeClass('d-none');
            updateLoadButtonState();
        } else {
            $('#fieldsCard').addClass('d-none');
            $('#btnLoadTable').attr('disabled', 'disabled');
        }
    });

    // Checkbox change listener
    $('.field-checkbox').on('change', function() {
        updateLoadButtonState();
    });

    function updateLoadButtonState() {
        const hasRombel = $('#id_pembelajaran').val() !== '';
        const hasCheckedField = $('.field-checkbox:checked').length > 0;

        if (hasRombel && hasCheckedField) {
            $('#btnLoadTable').removeAttr('disabled');
        } else {
            $('#btnLoadTable').attr('disabled', 'disabled');
        }
    }

    function hideAllSections() {
        $('#loadingSection').addClass('d-none');
        $('#emptySection').addClass('d-none');
        $('#tableSection').addClass('d-none');
    }

    // Load data and render Table
    $('#btnLoadTable').on('click', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        if (!id_pembelajaran) return;

        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("edit_inline_siswa/get_students") ?>',
            type: 'POST',
            data: { id_pembelajaran: id_pembelajaran },
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

                renderEditTable();
            },
            error: function() {
                $('#loadingSection').addClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal memuat data siswa.'
                });
            }
        });
    });

    function renderEditTable() {
        // 1. Get Checked Fields details
        const selectedFields = [];
        $('.field-checkbox:checked').each(function() {
            selectedFields.push({
                key: $(this).val(),
                label: $(this).data('label'),
                type: $(this).data('type'),
                options: $(this).data('options') ? $(this).data('options') : null
            });
        });

        if (selectedFields.length === 0) return;

        // 2. Build Headers
        let headHtml = '<th scope="col" class="text-center" width="60">No</th>';
        headHtml += '<th scope="col">Nama Siswa</th>';
        selectedFields.forEach(function(f) {
            headHtml += `<th scope="col">${f.label}</th>`;
        });
        $('#tableHeadRow').html(headHtml);

        // 3. Build Body
        let bodyHtml = '';
        studentList.forEach(function(s, index) {
            bodyHtml += `<tr>`;
            bodyHtml += `<td class="text-center">${index + 1}</td>`;
            bodyHtml += `<td><span class="fw-semibold text-neutral-800">${s.nama_siswa}</span></td>`;

            selectedFields.forEach(function(f) {
                const val = s[f.key] !== null ? s[f.key] : '';
                bodyHtml += `<td>`;
                
                if (f.type === 'select') {
                    bodyHtml += `<select name="students[${s.id_siswa}][${f.key}]" class="form-select radius-8 py-4 px-8">`;
                    bodyHtml += `<option value="">-- Pilih --</option>`;
                    f.options.forEach(function(opt) {
                        const selected = (val == opt) ? 'selected' : '';
                        bodyHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                    });
                    bodyHtml += `</select>`;
                } else if (f.type === 'date') {
                    bodyHtml += `<input type="date" name="students[${s.id_siswa}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                } else if (f.type === 'number') {
                    bodyHtml += `<input type="number" name="students[${s.id_siswa}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                } else {
                    bodyHtml += `<input type="text" name="students[${s.id_siswa}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                }

                bodyHtml += `</td>`;
            });
            bodyHtml += `</tr>`;
        });

        $('#tableBody').html(bodyHtml);
        $('#tableHeaderLabel').text('Tabel Edit Inline - ' + $('#id_pembelajaran option:selected').text());
        $('#tableSection').removeClass('d-none');
    }

    // Save batch action
    $('#btnSave').on('click', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const formData = $('#inlineEditForm').serializeArray();

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Tindakan ini akan memperbarui semua data siswa yang diedit pada tabel.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan perubahan data siswa.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Post data
                $.ajax({
                    url: '<?php echo url("edit_inline_siswa/update_batch") ?>',
                    type: 'POST',
                    data: $.param({ id_pembelajaran: id_pembelajaran }) + '&' + $.param(formData),
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Reload table
                                $('#btnLoadTable').trigger('click');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menyimpan perubahan data siswa.'
                        });
                    }
                });
            }
        });
    });
});
</script>
