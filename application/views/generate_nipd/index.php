<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Filter Form Card -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent">
                    <h6 class="text-primary-light mb-0">Atur Angkatan & Rombel</h6>
                    <p class="text-sm text-neutral-500 mt-4">Pilih angkatan (4-digit) dan rombel siswa untuk men-generate NIPD secara massal.</p>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row gy-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Angkatan (Tahun Pelajaran)</label>
                            <select name="angkatan" id="angkatan" class="form-select radius-8">
                                <?php foreach ($angkatan_options as $pref => $ta_label): ?>
                                    <option value="<?php echo $pref; ?>" <?php echo ($pref == $default_angkatan) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($ta_label); ?> (Prefix: <?php echo $pref; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
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
                        <div class="col-md-3">
                            <button type="button" id="btnLoad" class="btn btn-primary-600 radius-8 w-100 py-11 d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:magnifer-linear" class="text-xl"></iconify-icon> Tampilkan Siswa
                            </button>
                        </div>
                    </form>
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
                <p class="mt-16 text-neutral-500">Mengambil data siswa dan menghitung nomor urut NIPD...</p>
            </div>
        </div>
    </div>

    <!-- Empty Card -->
    <div class="row gy-4 mb-24 d-none" id="emptySection">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-40 text-center">
                <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-6xl text-neutral-300 mb-16"></iconify-icon>
                <h6 class="text-neutral-600">Tidak Ada Siswa</h6>
                <p class="text-neutral-400">Tidak ada siswa yang terdaftar dalam rombel ini untuk tahun ajaran aktif.</p>
            </div>
        </div>
    </div>

    <!-- Results Card -->
    <div class="row gy-4 mb-24 d-none" id="studentsSection">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light mb-0" id="tableHeaderLabel">Daftar Siswa</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-neutral-200 text-neutral-800 radius-4 px-12 py-6 text-sm fw-semibold" id="prefixBadge"></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="studentsTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center" width="60">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </div>
                                    </th>
                                    <th scope="col" class="text-center" width="60">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">NISN</th>
                                    <th scope="col" class="text-center">NIPD Saat Ini</th>
                                    <th scope="col" class="text-center">Proposed NIPD (Preview Baru)</th>
                                    <th scope="col" class="text-center">Status NIPD</th>
                                </tr>
                            </thead>
                            <tbody id="studentsBody">
                                <!-- Populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-20 gap-3">
                        <div class="text-neutral-500 text-sm">
                            Total terpilih: <span id="selectedCount" class="fw-bold text-primary-600">0</span> siswa
                        </div>
                        <button type="button" id="btnGenerate" class="btn btn-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" disabled>
                            <iconify-icon icon="solar:checklist-minimalistic-linear" class="text-xl"></iconify-icon> Generate NIPD Massal
                        </button>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>

</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    // Automatically trigger fetch when Rombel is selected
    $('#id_pembelajaran').on('change', function() {
        if ($(this).val()) {
            loadStudents();
        } else {
            hideAllSections();
        }
    });

    // Or manual button trigger
    $('#btnLoad').on('click', function() {
        loadStudents();
    });

    function hideAllSections() {
        $('#loadingSection').addClass('d-none');
        $('#emptySection').addClass('d-none');
        $('#studentsSection').addClass('d-none');
    }

    function loadStudents() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const angkatan = $('#angkatan').val();

        if (!id_pembelajaran) {
            Swal.fire({
                icon: 'warning',
                title: 'Rombel Belum Dipilih',
                text: 'Silakan pilih Rombel / Kelas terlebih dahulu.'
            });
            return;
        }

        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("generate_nipd/get_students") ?>',
            type: 'POST',
            data: {
                id_pembelajaran: id_pembelajaran,
                angkatan: angkatan
            },
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

                const students = response.students;
                if (students.length === 0) {
                    $('#emptySection').removeClass('d-none');
                    return;
                }

                // Set prefix and headers
                $('#prefixBadge').text('Prefix NIPD: ' + response.prefix + 'XXXX');
                $('#tableHeaderLabel').text('Daftar Siswa - ' + response.lembaga_info);

                let html = '';
                students.forEach(function(s, index) {
                    const rowClass = s.has_nipd ? 'bg-light-focus' : '';
                    const checkboxChecked = s.has_nipd ? '' : 'checked';
                    const nipdDisplay = s.has_nipd ? `<span class="fw-semibold text-neutral-800">${s.nipd}</span>` : '<span class="text-neutral-400">-</span>';
                    
                    let statusBadge = '';
                    if (s.has_nipd) {
                        statusBadge = '<span class="badge bg-success-focus text-success-600 radius-4 px-10 py-4 text-xs">Sudah Ada NIPD</span>';
                    } else {
                        statusBadge = '<span class="badge bg-neutral-100 text-neutral-500 radius-4 px-10 py-4 text-xs">Belum Ada NIPD</span>';
                    }

                    html += `
                        <tr class="${rowClass}">
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input type="checkbox" name="siswa_ids[]" value="${s.id_siswa}" class="student-checkbox form-check-input" ${checkboxChecked}>
                                </div>
                            </td>
                            <td class="text-center">${index + 1}</td>
                            <td>${s.nama_siswa}</td>
                            <td class="text-center">${s.nisn}</td>
                            <td class="text-center">${nipdDisplay}</td>
                            <td class="text-center">
                                <span class="font-monospace fw-bold text-primary-600">${s.proposed_nipd}</span>
                            </td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>
                    `;
                });

                $('#studentsBody').html(html);
                $('#studentsSection').removeClass('d-none');

                updateSelectionState();
            },
            error: function() {
                $('#loadingSection').addClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kesalahan saat memproses permintaan data siswa.'
                });
            }
        });
    }

    // Toggle check all
    $('#checkAll').on('change', function() {
        const checked = $(this).is(':checked');
        $('.student-checkbox').prop('checked', checked);
        updateSelectionState();
    });

    // Individual checkbox change
    $(document).on('change', '.student-checkbox', function() {
        updateSelectionState();
    });

    function updateSelectionState() {
        const totalCheckboxes = $('.student-checkbox').length;
        const checkedCheckboxes = $('.student-checkbox:checked').length;

        // Sync checkAll checkbox
        $('#checkAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);

        // Update count label
        $('#selectedCount').text(checkedCheckboxes);

        // Enable/Disable Generate button
        if (checkedCheckboxes > 0) {
            $('#btnGenerate').removeAttr('disabled');
        } else {
            $('#btnGenerate').attr('disabled', 'disabled');
        }
    }

    // Click Generate Button
    $('#btnGenerate').on('click', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const angkatan = $('#angkatan').val();
        
        const siswa_ids = [];
        $('.student-checkbox:checked').each(function() {
            siswa_ids.push($(this).val());
        });

        if (siswa_ids.length === 0) return;

        Swal.fire({
            title: 'Konfirmasi Pembuatan NIPD',
            text: `Apakah Anda yakin ingin men-generate NIPD baru untuk ${siswa_ids.length} siswa terpilih? Tindakan ini akan mengupdate kolom NIPD mereka.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show generating overlay/loader
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang men-generate NIPD siswa di server.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?php echo url("generate_nipd/generate") ?>',
                    type: 'POST',
                    data: {
                        id_pembelajaran: id_pembelajaran,
                        angkatan: angkatan,
                        siswa_ids: siswa_ids
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Refresh current listing to show updated NIPDs
                                loadStudents();
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
                            text: 'Gagal menghubungi server untuk memproses pembuatan NIPD.'
                        });
                    }
                });
            }
        });
    });
});
</script>
