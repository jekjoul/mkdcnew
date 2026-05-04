<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-neutral-300">
            <h6>Formulir Lembaga</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo isset($row) ? url('master/lembagaUpdate/' . $row->id_lembaga) : url('master/lembagaSimpan') ?>" method="post" enctype="multipart/form-data">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lembaga</label>
                        <input type="text" name="nama_lembaga" class="form-control" value="<?php echo @$row->nama_lembaga ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">NPSN</label>
                        <input type="text" name="npsn" class="form-control" value="<?php echo @$row->npsn ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kepala Sekolah</label>
                        <select name="id_ptk_kepsek" class="form-control select2">
                            <option value="">Pilih Kepsek</option>
                            <?php foreach ($ptk as $p): ?>
                                <option value="<?php echo $p->id_ptk ?>" <?php echo @$row->id_ptk_kepsek == $p->id_ptk ? 'selected' : '' ?>><?php echo $p->nama_ptk ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bentuk Pendidikan</label>
                        <input type="text" name="bentuk_pendidikan" class="form-control" value="<?php echo @$row->bentuk_pendidikan ?>" placeholder="Contoh: SMP">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Swasta" <?php echo @$row->status == 'Swasta' ? 'selected' : '' ?>>Swasta</option>
                            <option value="Negeri" <?php echo @$row->status == 'Negeri' ? 'selected' : '' ?>>Negeri</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Akreditasi</label>
                        <input type="text" name="akreditasi" class="form-control" value="<?php echo @$row->akreditasi ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No. SK Akreditasi</label>
                        <input type="text" name="no_sk_akreditasi" class="form-control" value="<?php echo @$row->no_sk_akreditasi ?>">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2"><?php echo @$row->alamat ?></textarea>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">RT</label>
                        <input type="text" name="rt" class="form-control" value="<?php echo @$row->rt ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">RW</label>
                        <input type="text" name="rw" class="form-control" value="<?php echo @$row->rw ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-control select2">
                            <option value="<?php echo @$row->provinsi ?>"><?php echo @$row->provinsi ?: 'Pilih Provinsi' ?></option>
                            <?php foreach ($provinsi as $p): ?> <option value="<?php echo $p->id_prov ?>"><?php echo $p->nama ?></option> <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kabupaten</label>
                        <select name="kabupaten" id="kabupaten" class="form-control select2">
                            <option value="<?php echo @$row->kabupaten ?>"><?php echo @$row->kabupaten ?: 'Pilih Kabupaten' ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" class="form-control select2">
                            <option value="<?php echo @$row->kecamatan ?>"><?php echo @$row->kecamatan ?: 'Pilih Kecamatan' ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kelurahan</label>
                        <select name="kelurahan" id="kelurahan" class="form-control select2">
                            <option value="<?php echo @$row->kelurahan ?>"><?php echo @$row->kelurahan ?: 'Pilih Kelurahan' ?></option>
                        </select>
                    </div>

                    <div class="col-md-4"><label class="form-label">Telepon</label><input type="text" name="telepon" class="form-control" value="<?php echo @$row->telepon ?>"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo @$row->email ?>"></div>
                    <div class="col-md-4"><label class="form-label">Website</label><input type="text" name="website" class="form-control" value="<?php echo @$row->website ?>"></div>

                    <div class="col-md-4"><label class="form-label">Instagram</label><input type="text" name="instagram" class="form-control" value="<?php echo @$row->instagram ?>" placeholder="Username/Link"></div>
                    <div class="col-md-4"><label class="form-label">TikTok</label><input type="text" name="tiktok" class="form-control" value="<?php echo @$row->tiktok ?>" placeholder="Username/Link"></div>
                    <div class="col-md-4"><label class="form-label">YouTube</label><input type="text" name="youtube" class="form-control" value="<?php echo @$row->youtube ?>" placeholder="Channel/Link"></div>

                    <div class="col-md-4">
                        <label class="form-label">Logo Lembaga</label>
                        <input type="file" name="logo" class="form-control">
                        <?php if (isset($row) && $row->logo): ?>
                            <div class="mt-2">
                                <img src="<?php echo url('uploads/lembaga/' . $row->logo) ?>" class="w-80-px radius-8 border">
                                <p class="text-xs text-secondary-light mt-1">Logo saat ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto Kepsek</label>
                        <input type="file" name="foto_kepsek" class="form-control">
                        <?php if (isset($row) && $row->foto_kepsek): ?>
                            <div class="mt-2">
                                <img src="<?php echo url('uploads/lembaga/' . $row->foto_kepsek) ?>" class="w-80-px radius-8 border">
                                <p class="text-xs text-secondary-light mt-1">Foto saat ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berkas Akreditasi (PDF)</label>
                        <input type="file" name="berkas_akreditasi" class="form-control">
                        <?php if (isset($row) && $row->berkas_akreditasi): ?>
                            <div class="mt-2">
                                <a href="<?php echo url('uploads/lembaga/' . $row->berkas_akreditasi) ?>" target="_blank" class="text-info text-sm d-flex align-items-center gap-1 fw-semibold">
                                    <iconify-icon icon="lucide:file-text"></iconify-icon> Lihat Berkas Akreditasi
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="<?php echo url('master/lembaga') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $('#provinsi').on('change', function() {
            $.post('<?php echo url('ptk/getKabupaten') ?>', {
                id: $(this).val()
            }, function(data) {
                $('#kabupaten').html(data.map(i => `<option value="${i.id_kab}">${i.nama}</option>`))
            }, 'json');
        });
        $('#kabupaten').on('change', function() {
            $.post('<?php echo url('ptk/getKecamatan') ?>', {
                id: $(this).val()
            }, function(data) {
                $('#kecamatan').html(data.map(i => `<option value="${i.id_kec}">${i.nama}</option>`))
            }, 'json');
        });
        $('#kecamatan').on('change', function() {
            $.post('<?php echo url('ptk/getKelurahan') ?>', {
                id: $(this).val()
            }, function(data) {
                $('#kelurahan').html(data.map(i => `<option value="${i.id_kel}">${i.nama}</option>`))
            }, 'json');
        });
    });
</script>