<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Filters and Description -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card radius-8 border-0 p-20">
                <div class="card-header border-0 pb-0 bg-transparent">
                    <h6 class="text-primary-light mb-0">Generate Nomor Induk Yayasan (NIY)</h6>
                    <p class="text-sm text-neutral-500 mt-4">
                        Men-generate NIY secara massal untuk PTK aktif. Jika Tanggal SK Pengangkatan kosong, Anda dapat mengisinya langsung dari tabel di bawah ini sebelum men-generate.
                    </p>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" id="btnFilterAll" class="btn btn-primary-600 radius-8 px-20">Semua PTK</button>
                        <button type="button" id="btnFilterNoNiy" class="btn bg-neutral-100 text-neutral-600 hover-bg-neutral-200 radius-8 px-20">Belum Ada NIY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light mb-0">Daftar Pendidik dan Tenaga Kependidikan (PTK)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="ptkTable" data-page-length='25'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center" width="60">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </div>
                                    </th>
                                    <th scope="col" class="text-center" width="60">No</th>
                                    <th scope="col">Nama PTK</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th scope="col" class="text-center">Tgl SK Pengangkatan</th>
                                    <th scope="col" class="text-center">NIY Saat Ini</th>
                                    <th scope="col" class="text-center">Proposed NIY (Preview Baru)</th>
                                    <th scope="col" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($ptk_list as $row): ?>
                                    <?php 
                                        $has_niy = !empty($row->niy);
                                        $row_class = $has_niy ? 'has-niy-row bg-light-focus' : 'no-niy-row';
                                        $checked = $has_niy ? '' : 'checked';
                                    ?>
                                    <tr class="niy-row <?php echo $row_class; ?>" data-id="<?php echo $row->id_ptk; ?>" data-gender="<?php echo html_escape($row->jenis_kelamin); ?>">
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" value="<?php echo $row->id_ptk; ?>" class="ptk-checkbox form-check-input" <?php echo $checked; ?>>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td>
                                            <span class="fw-medium text-neutral-800"><?php echo html_escape($row->nama_ptk); ?></span>
                                            <small class="text-neutral-400 d-block"><?php echo html_escape($row->status_pegawai); ?></small>
                                        </td>
                                        <td class="text-center"><?php echo html_escape($row->jenis_kelamin); ?></td>
                                        <td class="text-center">
                                            <input type="date" class="form-control radius-8 appointment-date py-4 px-8 text-center" 
                                                   value="<?php echo html_escape($row->tgl_sk_pengangkatan); ?>" 
                                                   style="width: 160px; display: inline-block;">
                                        </td>
                                        <td class="text-center font-monospace">
                                            <?php echo $has_niy ? html_escape($row->niy) : '<span class="text-neutral-400">-</span>'; ?>
                                        </td>
                                        <td class="text-center font-monospace text-primary-600 fw-bold proposed-niy-cell">
                                            <!-- Preview dynamically calculated -->
                                            -
                                        </td>
                                        <td class="text-center">
                                            <?php if ($has_niy): ?>
                                                <span class="badge bg-success-focus text-success-600 radius-4 px-10 py-4 text-xs">Sudah Ada</span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-100 text-neutral-500 radius-4 px-10 py-4 text-xs">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-20 gap-3">
                        <div class="text-neutral-500 text-sm">
                            Total terpilih: <span id="selectedCount" class="fw-bold text-primary-600">0</span> PTK
                        </div>
                        <button type="button" id="btnGenerate" class="btn btn-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" disabled>
                            <iconify-icon icon="solar:checklist-minimalistic-linear" class="text-xl"></iconify-icon> Generate NIY Massal
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
    let datatable = new DataTable('#ptkTable', {
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [0, 4, 6] }
        ]
    });

    // Custom filtering: Show All / Show Only Belum Ada NIY
    $('#btnFilterAll').on('click', function() {
        $(this).removeClass('bg-neutral-100 text-neutral-600 hover-bg-neutral-200').addClass('btn-primary-600');
        $('#btnFilterNoNiy').removeClass('btn-primary-600').addClass('bg-neutral-100 text-neutral-600 hover-bg-neutral-200');
        
        // Show all rows
        $.fn.dataTable.ext.search.pop();
        datatable.draw();
        updateProposedNiys();
    });

    $('#btnFilterNoNiy').on('click', function() {
        $(this).removeClass('bg-neutral-100 text-neutral-600 hover-bg-neutral-200').addClass('btn-primary-600');
        $('#btnFilterAll').removeClass('btn-primary-600').addClass('bg-neutral-100 text-neutral-600 hover-bg-neutral-200');
        
        // Custom search to only show rows that do not have class has-niy-row
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let row = datatable.row(dataIndex).node();
            return !$(row).hasClass('has-niy-row');
        });
        datatable.draw();
        updateProposedNiys();
    });

    // Handle checkAll
    $('#checkAll').on('change', function() {
        const checked = $(this).is(':checked');
        // Check only visible rows on current page / filtered search
        $('.ptk-checkbox', datatable.rows({ search: 'applied' }).nodes()).prop('checked', checked);
        updateSelectionState();
    });

    // Handle individual checkbox change
    $(document).on('change', '.ptk-checkbox', function() {
        updateSelectionState();
    });

    // Handle date change
    $(document).on('change', '.appointment-date', function() {
        updateProposedNiys();
    });

    function updateSelectionState() {
        const visibleCheckboxes = $('.ptk-checkbox', datatable.rows({ search: 'applied' }).nodes());
        const checkedCheckboxes = visibleCheckboxes.filter(':checked');

        $('#checkAll').prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedCheckboxes.length);
        $('#selectedCount').text(checkedCheckboxes.length);

        if (checkedCheckboxes.length > 0) {
            $('#btnGenerate').removeAttr('disabled');
        } else {
            $('#btnGenerate').attr('disabled', 'disabled');
        }
    }

    function updateProposedNiys() {
        // Collect all visible row inputs to preview proposed NIYs
        const ptkData = [];
        const rows = $('.niy-row', datatable.rows({ search: 'applied' }).nodes());
        
        rows.each(function() {
            const row = $(this);
            ptkData.push({
                id_ptk: row.data('id'),
                date: row.find('.appointment-date').val(),
                gender: row.data('gender')
            });
        });

        if (ptkData.length === 0) return;

        $.ajax({
            url: '<?php echo url("generate_niy/get_proposed_niys") ?>',
            type: 'POST',
            data: { ptk_data: ptkData },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    const proposed = response.proposed_niys;
                    rows.each(function() {
                        const row = $(this);
                        const id = row.data('id');
                        const niyVal = proposed[id];
                        
                        if (niyVal && niyVal !== '-') {
                            row.find('.proposed-niy-cell').text(niyVal);
                        } else {
                            row.find('.proposed-niy-cell').html('<span class="text-danger-600 text-xs">Isi Tgl SK</span>');
                        }
                    });
                }
            }
        });
    }

    // Call proposed NIYs on initial load
    updateProposedNiys();
    updateSelectionState();

    // Trigger update on datatable page changes
    datatable.on('draw', function() {
        updateProposedNiys();
        updateSelectionState();
    });

    // Generate NIY Action
    $('#btnGenerate').on('click', function() {
        const ptkUpdates = [];
        let validationError = false;

        $('.ptk-checkbox:checked', datatable.rows({ search: 'applied' }).nodes()).each(function() {
            const row = $(this).closest('tr');
            const dateVal = row.find('.appointment-date').val();
            const idVal = $(this).val();

            if (!dateVal) {
                validationError = true;
                const name = row.find('td:eq(2) span').text();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal SK Kosong',
                    text: `Silakan isi Tanggal SK Pengangkatan untuk PTK "${name}" terlebih dahulu.`
                });
                return false; // Break loop
            }

            ptkUpdates.push({
                id_ptk: idVal,
                date: dateVal
            });
        });

        if (validationError || ptkUpdates.length === 0) return;

        Swal.fire({
            title: 'Konfirmasi Generate NIY',
            text: `Apakah Anda yakin ingin men-generate NIY baru untuk ${ptkUpdates.length} PTK terpilih? Data Tanggal SK Pengangkatan mereka juga akan diselaraskan di database.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang men-generate NIY PTK di server.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?php echo url("generate_niy/generate") ?>',
                    type: 'POST',
                    data: { ptk_updates: ptkUpdates },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Reload page to reflect updates
                                window.location.reload();
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
                            text: 'Gagal menghubungi server untuk memproses pembuatan NIY.'
                        });
                    }
                });
            }
        });
    });
});
</script>
