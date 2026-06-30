<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-neutral-300">
                    <h6>Formulir Tahun Pelajaran</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo isset($row) ? url('tahun_pelajaran/update/' . $row->id_tahun_pelajaran) : url('tahun_pelajaran/save') ?>" method="post">
                        <input type="hidden" id="id_tahun_pelajaran" value="<?php echo @$row->id_tahun_pelajaran ?>">
                        <div class="mb-20">
                            <label class="form-label fw-semibold text-sm mb-8">Tahun Pelajaran <span class="text-danger-600">*</span></label>
                            <input type="text" class="form-control radius-8" id="input_tahun" name="tahun_pelajaran" value="<?php echo @$row->tahun_pelajaran ?>" required placeholder="Contoh: 2024/2025">
                            <div id="error_msg" class="text-danger-600 text-xs mt-1" style="display:none;">Data tahun pelajaran dan semester ini sudah ada!</div>
                        </div>
                        <div class="mb-20">
                            <label class="form-label fw-semibold text-sm mb-8">Semester <span class="text-danger-600">*</span></label>
                            <select class="form-control radius-8 form-select" id="input_semester" name="semester" required>
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil" <?php echo @$row->semester == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                <option value="Genap" <?php echo @$row->semester == 'Genap' ? 'selected' : '' ?>>Genap</option>
                            </select>
                        </div>
                        <div class="mb-20">
                            <label class="form-label fw-semibold text-sm mb-8">Status</label>
                            <select class="form-control radius-8 form-select" name="status">
                                <option value="Aktif" <?php echo @$row->status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?php echo @$row->status == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                            <a href="<?php echo url('tahun_pelajaran') ?>" class="btn btn-secondary-light radius-8 px-20 py-11">Batal</a>
                            <button type="submit" id="btn_submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="alert alert-info radius-8" role="alert">
                <h6 class="alert-heading text-sm fw-bold">Petunjuk:</h6>
                <p class="text-sm mb-0">Format tahun pelajaran biasanya menggunakan tahun ajaran berjalan (Contoh: 2024/2025). Pastikan hanya satu tahun pelajaran yang berstatus <strong>Aktif</strong> untuk menghindari kesalahan data pada laporan akademik.</p>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
    $(document).ready(function() {
        function checkUniqueness() {
            const tahun = $('#input_tahun').val();
            const semester = $('#input_semester').val();
            const id = $('#id_tahun_pelajaran').val();

            if (tahun && semester) {
                $.get('<?php echo url("tahun_pelajaran/checkIfUnique") ?>', {
                    tahun_pelajaran: tahun,
                    semester: semester,
                    id_tahun_pelajaran: id
                }, function(res) {
                    if (res === 'false') {
                        $('#error_msg').fadeIn();
                        $('#btn_submit').attr('disabled', true);
                    } else {
                        $('#error_msg').fadeOut();
                        $('#btn_submit').attr('disabled', false);
                    }
                });
            }
        }

        $('#input_tahun, #input_semester').on('change keyup', function() {
            checkUniqueness();
        });
    });
</script>