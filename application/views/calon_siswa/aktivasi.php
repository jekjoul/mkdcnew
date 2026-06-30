<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <form action="<?php echo url('calon_siswa/aktifkan_bulk') ?>" method="post" id="formBulkAktivasi">
                <div class="card basic-data-table">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                        <h6 class="text-light mb-0">Aktivasi Calon Siswa (Status Terverifikasi)</h6>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <?php if (hasPermissions('calon_siswa_aktivasi')): ?>
                            <button type="submit" class="btn btn-warning-600 text-light radius-8 px-20 py-11 d-flex align-items-center gap-2" id="btnBulkSubmit" disabled onclick="return confirm('Aktifkan semua calon siswa yang dipilih?')">
                                <iconify-icon icon="lucide:user-check" class="text-xl"></iconify-icon> Aktifkan Terpilih
                            </button>
                            <?php endif; ?>
                            <a href="<?php echo url('calon_siswa') ?>" class="btn btn-light-100 text-dark radius-8 px-20 py-11 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="dataTableAktivasi" data-page-length="10">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">
                                            <input class="form-check-input" type="checkbox" id="selectAllCalon">
                                        </th>
                                        <th class="text-center" width="60">No</th>
                                        <th>Nama Calon Siswa</th>
                                        <th class="text-center">NISN/NIK</th>
                                        <th>Lembaga Tujuan</th>
                                        <th class="text-center">Berkas</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($calon_siswa as $s): ?>
                                        <?php
                                        $lembaga_tujuan = !empty($s->id_lembaga_tujuan) && isset($lembaga_map[$s->id_lembaga_tujuan]) ? $lembaga_map[$s->id_lembaga_tujuan] : '-';
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input check-calon" type="checkbox" name="id_calon_siswa[]" value="<?php echo $s->id_calon_siswa; ?>">
                                            </td>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td>
                                                <span class="fw-semibold"><?php echo htmlspecialchars($s->nama_siswa, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td class="text-center"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nik ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($lembaga_tujuan, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-success-focus text-success-main px-16 py-6 radius-4">
                                                    <?php echo $s->jumlah_berkas; ?> Berkas
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if (hasPermissions('calon_siswa_aktivasi')): ?>
                                                <button type="button" class="btn btn-warning-600 text-light btn-sm radius-8 btn-aktifkan-single d-inline-flex align-items-center gap-1" data-id="<?php echo $s->id_calon_siswa; ?>">
                                                    <iconify-icon icon="lucide:user-plus"></iconify-icon> Aktifkan Siswa
                                                </button>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk aktivasi single -->
<form id="formSingleAktivasi" action="<?php echo url('calon_siswa/aktifkan') ?>" method="post" class="d-none">
    <input type="hidden" name="confirm" value="1">
</form>

<?php include viewPath('includes/footer'); ?>
<script>
    let tableAktivasi = new DataTable('#dataTableAktivasi');
    
    // Select/Deselect All Checkboxes
    $('#selectAllCalon').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.check-calon').prop('checked', isChecked);
        toggleBulkButton();
    });

    // Check individual items
    $(document).on('change', '.check-calon', function() {
        let totalCheckboxes = $('.check-calon').length;
        let checkedCheckboxes = $('.check-calon:checked').length;
        
        $('#selectAllCalon').prop('checked', totalCheckboxes === checkedCheckboxes);
        toggleBulkButton();
    });

    function toggleBulkButton() {
        let checkedCount = $('.check-calon:checked').length;
        if (checkedCount > 0) {
            $('#btnBulkSubmit').removeAttr('disabled');
        } else {
            $('#btnBulkSubmit').attr('disabled', 'disabled');
        }
    }

    // Single activation click handler
    $(document).on('click', '.btn-aktifkan-single', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        if (confirm('Aktifkan calon siswa ini menjadi siswa aktif? Berkas daftar ulang yang sudah diupload akan dipindahkan ke dokumen siswa.')) {
            let form = $('#formSingleAktivasi');
            form.attr('action', '<?php echo url("calon_siswa/aktifkan/"); ?>' + id);
            form.submit();
        }
    });
</script>
