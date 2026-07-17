<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Description & Rombel/Date Filter -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent">
                    <h6 class="text-primary-light mb-0">Pilih Rombel & Tanggal Pemeriksaan</h6>
                    <p class="text-sm text-neutral-500 mt-4">
                        Pilih rombel dan tanggal pemeriksaan rekam medis untuk melakukan input masal atau edit inline.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row gy-3 align-items-end">
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
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control radius-8" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="btnLoadTable" class="btn btn-primary-600 radius-8 w-100 py-11 d-flex align-items-center justify-content-center gap-2" disabled>
                                <iconify-icon icon="solar:table-double-columns-linear" class="text-xl"></iconify-icon> Tampilkan Tabel
                            </button>
                        </div>
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
                <p class="mt-16 text-neutral-500">Mengambil data rekam medis siswa...</p>
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
                    <h6 class="text-primary-light mb-0" id="tableHeaderLabel">Tabel Input Rekam Medis</h6>
                </div>
                <div class="card-body">
                    <form id="inlineEditForm">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="inlineEditTable">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th scope="col" class="text-center" width="60">No</th>
                                        <th scope="col">Nama Siswa</th>
                                        <th scope="col">Tinggi Badan (cm)</th>
                                        <th scope="col">Berat Badan (kg)</th>
                                        <th scope="col">Lingkar Kepala (cm)</th>
                                        <th scope="col">Lingkar Perut (cm)</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Built dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-20">
                            <button type="button" id="btnSave" class="btn btn-success-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:diskette-linear" class="text-xl"></iconify-icon> Simpan Rekam Medis
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

    // Enable/disable load button
    $('#id_pembelajaran, #tanggal').on('change', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const tanggal = $('#tanggal').val();
        hideAllSections();
        if (id_pembelajaran && tanggal) {
            $('#btnLoadTable').removeAttr('disabled');
        } else {
            $('#btnLoadTable').attr('disabled', 'disabled');
        }
    });

    function hideAllSections() {
        $('#loadingSection').addClass('d-none');
        $('#emptySection').addClass('d-none');
        $('#tableSection').addClass('d-none');
    }

    // Load data and render Table
    $('#btnLoadTable').on('click', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const tanggal = $('#tanggal').val();
        if (!id_pembelajaran || !tanggal) return;

        hideAllSections();
        $('#loadingSection').removeClass('d-none');

        $.ajax({
            url: '<?php echo url("input_rekam_medis/get_students") ?>',
            type: 'POST',
            data: { 
                id_pembelajaran: id_pembelajaran,
                tanggal: tanggal
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
                    text: 'Gagal memuat data rekam medis siswa.'
                });
            }
        });
    });

    function renderEditTable() {
        let bodyHtml = '';
        studentList.forEach(function(s, index) {
            const tb = s.tinggi_badan !== null ? s.tinggi_badan : '';
            const bb = s.berat_badan !== null ? s.berat_badan : '';
            const lk = s.lingkar_kepala !== null ? s.lingkar_kepala : '';
            const lp = s.lingkar_perut !== null ? s.lingkar_perut : '';

            bodyHtml += `<tr>`;
            bodyHtml += `<td class="text-center">${index + 1}</td>`;
            bodyHtml += `<td><span class="fw-semibold text-neutral-800">${s.nama_siswa}</span></td>`;
            
            bodyHtml += `<td><input type="number" name="students[${s.id_siswa}][tinggi_badan]" class="form-control radius-8 py-4 px-8" value="${tb}" placeholder="cm"></td>`;
            bodyHtml += `<td><input type="number" name="students[${s.id_siswa}][berat_badan]" class="form-control radius-8 py-4 px-8" value="${bb}" placeholder="kg"></td>`;
            bodyHtml += `<td><input type="number" name="students[${s.id_siswa}][lingkar_kepala]" class="form-control radius-8 py-4 px-8" value="${lk}" placeholder="cm"></td>`;
            bodyHtml += `<td><input type="number" name="students[${s.id_siswa}][lingkar_perut]" class="form-control radius-8 py-4 px-8" value="${lp}" placeholder="cm"></td>`;
            
            bodyHtml += `</tr>`;
        });

        $('#tableBody').html(bodyHtml);
        $('#tableHeaderLabel').text('Tabel Input Rekam Medis - ' + $('#id_pembelajaran option:selected').text() + ' (' + $('#tanggal').val() + ')');
        $('#tableSection').removeClass('d-none');
    }

    // Save batch action
    $('#btnSave').on('click', function() {
        const id_pembelajaran = $('#id_pembelajaran').val();
        const tanggal = $('#tanggal').val();
        const formData = $('#inlineEditForm').serializeArray();

        // Add additional parameters
        formData.push({ name: 'id_pembelajaran', value: id_pembelajaran });
        formData.push({ name: 'tanggal', value: tanggal });

        Swal.fire({
            title: 'Simpan Rekam Medis?',
            text: 'Tindakan ini akan memperbarui data rekam medis seluruh siswa yang diisi pada tabel untuk tanggal terpilih.',
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
                    text: 'Sedang menyimpan rekam medis siswa.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?php echo url("input_rekam_medis/update_batch") ?>',
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
                            });
                            // Reload table to reflect saved states
                            $('#btnLoadTable').trigger('click');
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
                            text: 'Gagal menyimpan data rekam medis.'
                        });
                    }
                });
            }
        });
    });
});
</script>
