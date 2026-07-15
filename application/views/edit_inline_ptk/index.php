<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Description & Load Action -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h6 class="text-primary-light mb-0">Pilih Kolom Edit PTK</h6>
                        <p class="text-sm text-neutral-500 mt-4">
                            Centang kolom/field data PTK yang ingin Anda edit secara massal dalam satu halaman, lalu klik tombol Tampilkan Tabel.
                        </p>
                    </div>
                    <button type="button" id="btnLoadTable" class="btn btn-primary-600 radius-8 py-11 d-flex align-items-center justify-content-center gap-2" disabled>
                        <iconify-icon icon="solar:table-double-columns-linear" class="text-xl"></iconify-icon> Tampilkan Tabel Edit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Selection Checklist Card -->
    <div class="row gy-4 mb-24" id="fieldsCard">
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
                <p class="text-neutral-400">Tidak ada PTK aktif yang terdaftar di database.</p>
            </div>
        </div>
    </div>

    <!-- Edit Inline Table Card -->
    <div class="row gy-4 mb-24 d-none" id="tableSection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-16 bg-transparent">
                    <h6 class="text-primary-light mb-0">Tabel Edit Inline PTK</h6>
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
    let ptkList = [];

    // Checkbox change listener
    $('.field-checkbox').on('change', function() {
        updateLoadButtonState();
    });

    function updateLoadButtonState() {
        const hasCheckedField = $('.field-checkbox:checked').length > 0;
        if (hasCheckedField) {
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
        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("edit_inline_ptk/get_ptk") ?>',
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

                renderEditTable();
            },
            error: function() {
                $('#loadingSection').addClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal memuat data PTK.'
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
        headHtml += '<th scope="col">Nama PTK</th>';
        selectedFields.forEach(function(f) {
            headHtml += `<th scope="col">${f.label}</th>`;
        });
        $('#tableHeadRow').html(headHtml);

        // 3. Build Body
        let bodyHtml = '';
        ptkList.forEach(function(p, index) {
            bodyHtml += `<tr>`;
            bodyHtml += `<td class="text-center">${index + 1}</td>`;
            bodyHtml += `<td>`;
            bodyHtml += `<span class="fw-semibold text-neutral-800 d-block">${p.nama_ptk}</span>`;
            bodyHtml += `<small class="text-neutral-400">${p.status_pegawai} - ${p.penugasan}</small>`;
            bodyHtml += `</td>`;

            selectedFields.forEach(function(f) {
                const val = p[f.key] !== null ? p[f.key] : '';
                bodyHtml += `<td>`;
                
                if (f.type === 'select') {
                    bodyHtml += `<select name="ptk[${p.id_ptk}][${f.key}]" class="form-select radius-8 py-4 px-8">`;
                    bodyHtml += `<option value="">-- Pilih --</option>`;
                    f.options.forEach(function(opt) {
                        const selected = (val == opt) ? 'selected' : '';
                        bodyHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                    });
                    bodyHtml += `</select>`;
                } else if (f.type === 'date') {
                    bodyHtml += `<input type="date" name="ptk[${p.id_ptk}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                } else if (f.type === 'number') {
                    bodyHtml += `<input type="number" name="ptk[${p.id_ptk}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                } else {
                    bodyHtml += `<input type="text" name="ptk[${p.id_ptk}][${f.key}]" class="form-control radius-8 py-4 px-8" value="${val}">`;
                }

                bodyHtml += `</td>`;
            });
            bodyHtml += `</tr>`;
        });

        $('#tableBody').html(bodyHtml);
        $('#tableSection').removeClass('d-none');
    }

    // Save batch action
    $('#btnSave').on('click', function() {
        const formData = $('#inlineEditForm').serializeArray();

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Tindakan ini akan memperbarui semua data PTK yang diedit pada tabel.',
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
                    text: 'Sedang menyimpan perubahan data PTK.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Post data
                $.ajax({
                    url: '<?php echo url("edit_inline_ptk/update_batch") ?>',
                    type: 'POST',
                    data: formData,
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
                            text: 'Gagal menyimpan perubahan data PTK.'
                        });
                    }
                });
            }
        });
    });
});
</script>
