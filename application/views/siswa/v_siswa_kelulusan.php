<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            
            <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-<?php echo $this->session->flashdata('message_type') ?: 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light">Kelulusan Kolektif Siswa (Kelas 9 & 12)</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert bg-warning-50 text-warning-main border border-warning-main px-24 py-11 mb-20">
                        <strong>Perhatian:</strong> Data siswa yang dipilih di bawah ini akan dipindahkan ke data <strong>Alumni</strong> dengan status <strong>Lulus</strong> dan akan dihapus dari data Siswa aktif. Proses ini bisa dibatalkan dari menu Alumni jika terjadi kesalahan.
                    </div>

                    <?php echo form_open(url('siswa/proses_kelulusan'), ['id' => 'form-kelulusan']); ?>
                    
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Kelulusan</label>
                            <input type="date" name="tanggal_alumni" class="form-control radius-8" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-9 text-end">
                            <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" id="btn-submit-kelulusan">
                                <iconify-icon icon="lucide:check-circle" class="text-xl"></iconify-icon> Luluskan Siswa Terpilih
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center" width="50">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="check-all">
                                        </div>
                                    </th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">NISN/NIPD</th>
                                    <th scope="col" class="text-center">Tingkat</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col">Lembaga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($siswa)): ?>
                                    <?php foreach ($siswa as $s): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input check-siswa" type="checkbox" name="siswa_ids[]" value="<?php echo $s->id_siswa; ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?php echo html_escape($s->nama_siswa); ?></span>
                                            </td>
                                            <td class="text-center"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nipd ?: '-'); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-50 text-primary-600 px-12 py-4 radius-4">
                                                    Kelas <?php echo html_escape($s->nama_tingkat); ?>
                                                </span>
                                            </td>
                                            <td><?php echo html_escape($s->nama_tingkat . ' - ' . $s->nama_rombel); ?></td>
                                            <td><?php echo html_escape($s->nama_lembaga); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    // Handle Select All
    $('#check-all').on('change', function() {
        var isChecked = $(this).prop('checked');
        // Select all across all pages of datatable
        var table = $('#dataTable').DataTable();
        $('input[type="checkbox"].check-siswa', table.rows().nodes()).prop('checked', isChecked);
    });

    // Handle Submit Button
    $('#btn-submit-kelulusan').on('click', function(e) {
        e.preventDefault();
        
        var table = $('#dataTable').DataTable();
        var checkedCount = $('input[type="checkbox"].check-siswa:checked', table.rows().nodes()).length;
        
        if (checkedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu siswa yang akan diluluskan.',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Kelulusan',
            text: 'Anda akan meluluskan ' + checkedCount + ' siswa terpilih. Data ini akan dipindahkan ke daftar Alumni. Lanjutkan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Luluskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // If using DataTables, we need to ensure hidden checkboxes are submitted
                // The easiest way is to temporarily append them to the form
                var form = $('#form-kelulusan');
                
                // Clear any previously appended hidden inputs to avoid duplicates
                form.find('.hidden-siswa-ids').remove();
                
                $('input[type="checkbox"].check-siswa:checked', table.rows().nodes()).each(function(){
                    form.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'siswa_ids[]')
                            .attr('class', 'hidden-siswa-ids')
                            .val($(this).val())
                    );
                });
                
                form.submit();
            }
        });
    });
});
</script>
