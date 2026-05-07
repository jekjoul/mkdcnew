<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('jadwal_pelajaran/simpan_waktu') ?>" method="post" class="card">
        <div class="card-header bg-warning-900 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 text-light">Atur Kerangka Waktu Mingguan</h6>
            <button type="submit" class="btn btn-warning-600 btn-sm d-inline-flex align-items-center gap-2">
                <iconify-icon icon="lucide:save"></iconify-icon> Simpan Waktu
            </button>
        </div>
        <div class="card-body">
            <div class="row gy-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lama 1 Jam Pelajaran</label>
                    <div class="input-group">
                        <input type="number" min="1" class="form-control" name="menit_jp" value="<?php echo (int) $menit_jp ?>" required>
                        <span class="input-group-text">menit</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table bordered-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="86">Aktif</th>
                            <th width="120">Hari</th>
                            <th width="150">Jam Mulai</th>
                            <th width="150">Jumlah JP</th>
                            <th>Waktu Khusus / Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hari as $h): ?>
                            <?php $s = $settings[$h]; ?>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" name="hari_aktif[<?php echo $h ?>]" value="1" <?php echo $s['aktif'] ? 'checked' : '' ?>>
                                </td>
                                <td class="fw-semibold"><?php echo $h ?></td>
                                <td><input type="time" class="form-control" name="jam_mulai[<?php echo $h ?>]" value="<?php echo $s['jam_mulai'] ?>"></td>
                                <td><input type="number" min="1" max="20" class="form-control" name="jumlah_jp[<?php echo $h ?>]" value="<?php echo $s['jumlah_jp'] ?>"></td>
                                <td>
                                    <div class="break-list" data-hari="<?php echo $h ?>">
                                        <?php foreach ($s['istirahat'] as $break): ?>
                                            <div class="d-flex gap-2 mb-2 break-item-row">
                                                <input type="text" class="form-control" name="break_name[<?php echo $h ?>][]" value="<?php echo htmlspecialchars(isset($break['name']) ? $break['name'] : 'Istirahat', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nama, contoh: Shalat Dhuha">
                                                <input type="number" min="0" class="form-control" name="break_after[<?php echo $h ?>][]" value="<?php echo (int) $break['after'] ?>" placeholder="Setelah JP">
                                                <input type="number" min="1" class="form-control" name="break_duration[<?php echo $h ?>][]" value="<?php echo (int) $break['duration'] ?>" placeholder="Menit">
                                                <button type="button" class="btn btn-outline-danger remove-break"><iconify-icon icon="lucide:x"></iconify-icon></button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-break" data-hari="<?php echo $h ?>">
                                        <iconify-icon icon="lucide:plus"></iconify-icon> Tambah Kegiatan
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?php echo url('jadwal_pelajaran') ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary-600">Simpan Waktu</button>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $(document).on('click', '.add-break', function() {
        const hari = $(this).data('hari');
        const row = '<div class="d-flex gap-2 mb-2 break-item-row">' +
            '<input type="text" class="form-control" name="break_name[' + hari + '][]" placeholder="Nama, contoh: Shalat Dzuhur">' +
            '<input type="number" min="0" class="form-control" name="break_after[' + hari + '][]" placeholder="Setelah JP">' +
            '<input type="number" min="1" class="form-control" name="break_duration[' + hari + '][]" placeholder="Menit">' +
            '<button type="button" class="btn btn-outline-danger remove-break"><iconify-icon icon="lucide:x"></iconify-icon></button>' +
            '</div>';
        $('.break-list[data-hari="' + hari + '"]').append(row);
    });

    $(document).on('click', '.remove-break', function() {
        $(this).closest('.break-item-row').remove();
    });
</script>
