<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_ekskul" value="<?php echo $row ? $row->id_ekskul : ''; ?>">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="text-light mb-0"><?php echo $row ? 'Edit Kegiatan Ekstrakurikuler' : 'Tambah Kegiatan Ekstrakurikuler Baru' ?></h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ekskul" class="form-control" value="<?php echo $row ? html_escape($row->nama_ekskul) : '' ?>" required placeholder="Contoh: Pramuka Wajib, Futsal, Tari Tradisional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tahun Pelajaran / Semester <span class="text-danger">*</span></label>
                        <select name="id_tahun_pelajaran" class="form-select" required>
                            <?php foreach ($ta_list as $ta): ?>
                                <option value="<?php echo $ta->id_tahun_pelajaran ?>" <?php echo ($row && $row->id_tahun_pelajaran == $ta->id_tahun_pelajaran) || (!$row && $ta->status == 'Aktif') ? 'selected' : '' ?>>
                                    <?php echo html_escape($ta->tahun_pelajaran . ' (' . $ta->semester . ')') ?> <?php echo $ta->status == 'Aktif' ? '- (Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Guru Pembina (Dapat merangkap) <span class="text-danger">*</span></label>
                        <?php 
                        $selected_pembinas = [];
                        if ($row && !empty($row->id_ptk_pembina)) {
                            // Cek jika field lama berisi format JSON array pembina
                            $decoded = json_decode($row->id_ptk_pembina, true);
                            if (is_array($decoded)) {
                                $selected_pembinas = array_map('intval', $decoded);
                            } else {
                                $selected_pembinas = [(int) $row->id_ptk_pembina];
                            }
                        }
                        ?>
                        <select name="id_ptk_pembina[]" class="form-control select2" multiple required data-placeholder="Pilih satu atau lebih guru pembina...">
                            <?php foreach ($ptk_list as $ptk): ?>
                                <option value="<?php echo $ptk->id_ptk ?>" <?php echo in_array((int)$ptk->id_ptk, $selected_pembinas, true) ? 'selected' : '' ?>>
                                    <?php echo html_escape($ptk->nama_ptk) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Logo Kegiatan</label>
                        <input type="file" name="logo" class="form-control">
                        <?php if ($row && !empty($row->logo)): ?>
                            <span class="text-xs text-secondary-light mt-1 d-block">Logo saat ini: <a href="<?php echo url('uploads/ekskul/' . $row->logo) ?>" target="_blank"><?php echo $row->logo ?></a></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi / Keterangan Singkat</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan tujuan atau keterangan program kegiatan..."><?php echo $row ? html_escape($row->keterangan) : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-1"><?php echo $row ? 'Periksa kembali data sebelum diperbarui' : 'Simpan informasi dasar ekstrakurikuler' ?></h6>
                    <p class="text-secondary-light mb-0">Anggota ekskul dan pengisian nilai evaluasi dapat diatur secara mandiri pada tombol kelola aksi list utama.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('ekstrakurikuler') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary-600 px-4">Simpan Konfigurasi</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih satu atau lebih guru pembina...",
            allowClear: true
        });
    });
</script>
