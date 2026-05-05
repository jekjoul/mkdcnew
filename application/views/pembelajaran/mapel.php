<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('pembelajaran/simpan_mapel/' . $pembelajaran->id_pembelajaran); ?>" method="post">
        <div class="card mb-4">
            <div class="card-header bg-warning-900">
                <h6 class="text-light mb-0">Tambah Mapel Pembelajaran</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tahun/Semester</span>
                        <strong><?php echo $pembelajaran->tahun_pelajaran ?> (<?php echo $pembelajaran->semester ?>)</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Lembaga</span>
                        <strong><?php echo $pembelajaran->nama_lembaga ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tingkat</span>
                        <strong><?php echo $pembelajaran->nama_tingkat ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Rombel</span>
                        <strong><?php echo $pembelajaran->nama_rombel ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-neutral-100">
                <h6 class="mb-0">Mata Pelajaran, Jumlah Jam, dan Pengajar</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th width="80">Pilih</th>
                                <th>Mata Pelajaran</th>
                                <th width="170">Jumlah Jam</th>
                                <th width="320">PTK Pengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mapel as $m): ?>
                                <?php
                                $selected = isset($mapel_terpilih[$m->id_mapel]);
                                $row = $selected ? $mapel_terpilih[$m->id_mapel] : null;
                                ?>
                                <tr>
                                    <td>
                                        <input class="form-check-input mapel-check" type="checkbox" name="mapel[]" value="<?php echo $m->id_mapel ?>" data-mapel="<?php echo $m->id_mapel ?>" <?php echo $selected ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?php echo $m->nama_mapel ?></span>
                                        <?php if (!empty($m->mapel_singkat)): ?>
                                            <span class="text-secondary-light">(<?php echo $m->mapel_singkat ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control mapel-detail mapel-<?php echo $m->id_mapel ?>" name="jumlah_jam[<?php echo $m->id_mapel ?>]" value="<?php echo $row ? $row->jumlah_jam : '' ?>" placeholder="0" <?php echo $selected ? '' : 'disabled' ?>>
                                    </td>
                                    <td>
                                        <select class="form-select mapel-detail mapel-<?php echo $m->id_mapel ?>" name="id_ptk[<?php echo $m->id_mapel ?>]" <?php echo $selected ? '' : 'disabled' ?>>
                                            <option value="">Pilih PTK</option>
                                            <?php foreach ($ptk as $p): ?>
                                                <option value="<?php echo $p->id_ptk ?>" <?php echo $row && $row->id_ptk == $p->id_ptk ? 'selected' : '' ?>>
                                                    <?php echo $p->nama_ptk ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($mapel)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary-light py-4">Belum ada data mata pelajaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="<?php echo url('pembelajaran') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Mapel</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $('.mapel-check').on('change', function() {
        const id = $(this).data('mapel');
        $('.mapel-' + id).prop('disabled', !$(this).is(':checked'));
    });
</script>
