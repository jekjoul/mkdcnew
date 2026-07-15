<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-neutral-300">
                    <h6 class="text-dark mb-0"><?php echo $row ? 'Edit' : 'Tambah'; ?> Tugas Tambahan PTK / Guru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('tugas_tambahan_ptk/simpan'); ?>" method="post">
                        <input type="hidden" name="id" value="<?php echo $row ? $row->id : ''; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Nama PTK / Guru <span class="text-danger">*</span></label>
                                <select name="id_ptk" class="form-select select2-dropdown" required>
                                    <option value="" disabled <?php echo !$row ? 'selected' : ''; ?>>-- Pilih PTK / Guru --</option>
                                    <?php foreach ($ptk_list as $p): ?>
                                        <option value="<?php echo $p->id_ptk; ?>" <?php echo ($row && $row->id_ptk == $p->id_ptk) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($p->nama_ptk); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Tugas Tambahan <span class="text-danger">*</span></label>
                                <select name="id_tugas_tambahan" class="form-select select2-dropdown" required>
                                    <option value="" disabled <?php echo !$row ? 'selected' : ''; ?>>-- Pilih Tugas Tambahan --</option>
                                    <?php foreach ($tugas_list as $t): ?>
                                        <option value="<?php echo $t->id; ?>" <?php echo ($row && $row->id_tugas_tambahan == $t->id) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($t->nama); ?> (<?php echo html_escape($t->jenis); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Nomor SK Tugas Tambahan</label>
                                <input type="text" name="no_sk" class="form-control" value="<?php echo $row ? html_escape($row->no_sk) : ''; ?>" placeholder="Contoh: 800/123/415.28/2026">
                            </div>
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">Tanggal SK Tugas Tambahan</label>
                                <input type="date" name="tgl_sk" class="form-control" value="<?php echo $row ? $row->tgl_sk : ''; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">TMT (Tanggal Mulai Tugas)</label>
                                <input type="date" name="tmt" class="form-control" value="<?php echo $row ? $row->tmt : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-20">
                                <label class="form-label fw-semibold">TST (Tanggal Selesai Tugas)</label>
                                <input type="date" name="tst" class="form-control" value="<?php echo $row ? $row->tst : ''; ?>">
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                            <a href="<?php echo url('tugas_tambahan_ptk'); ?>" class="btn btn-secondary-light radius-8 px-20 py-11">Batal</a>
                            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    // Inisialisasi select2 jika ada di project ini
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-dropdown').select2();
        }
    });
</script>
