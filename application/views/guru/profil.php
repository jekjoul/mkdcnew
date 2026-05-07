<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Edit Profil PTK Saya</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo url('guru/update_profil') ?>" method="post">
                <div class="row">
                    <div class="col-md-6 mb-20">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_ptk" value="<?php echo html_escape($row->nama_ptk) ?>" required>
                    </div>
                    <div class="col-md-3 mb-20">
                        <label class="form-label">Gelar Depan</label>
                        <input type="text" class="form-control" name="gelar_depan" value="<?php echo html_escape($row->gelar_depan) ?>">
                    </div>
                    <div class="col-md-3 mb-20">
                        <label class="form-label">Gelar Belakang</label>
                        <input type="text" class="form-control" name="gelar_belakang" value="<?php echo html_escape($row->gelar_belakang) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin">
                            <option value="Laki-laki" <?php echo $row->jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo $row->jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" value="<?php echo html_escape($row->tempat_lahir) ?>" required>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo html_escape($row->tanggal_lahir) ?>" required>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Agama</label>
                        <select class="form-select" name="agama">
                            <?php foreach (['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan'] as $agama): ?>
                                <option value="<?php echo $agama ?>" <?php echo $row->agama == $agama ? 'selected' : '' ?>><?php echo $agama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Status Perkawinan</label>
                        <select class="form-select" name="status_perkawinan">
                            <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status): ?>
                                <option value="<?php echo $status ?>" <?php echo $row->status_perkawinan == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Nama Ibu Kandung</label>
                        <input type="text" class="form-control" name="nama_ibu_kandung" value="<?php echo html_escape($row->nama_ibu_kandung) ?>" required>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" name="nik" value="<?php echo html_escape($row->nik) ?>" required>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">NIY</label>
                        <input type="text" class="form-control" name="niy" value="<?php echo html_escape($row->niy) ?>" required>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">NUPTK</label>
                        <input type="text" class="form-control" name="nuptk" value="<?php echo html_escape($row->nuptk) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">No SK Pengangkatan</label>
                        <input type="text" class="form-control" name="no_sk_pengangkatan" value="<?php echo html_escape($row->no_sk_pengangkatan) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Tgl SK Pengangkatan</label>
                        <input type="date" class="form-control" name="tgl_sk_pengangkatan" value="<?php echo html_escape($row->tgl_sk_pengangkatan) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo html_escape($row->email) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" name="telepon" value="<?php echo html_escape($row->telepon) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Status Pegawai</label>
                        <select class="form-select" name="status_pegawai">
                            <option value="GTY/PTY" <?php echo $row->status_pegawai == 'GTY/PTY' ? 'selected' : '' ?>>GTY/PTY</option>
                            <option value="ASN" <?php echo $row->status_pegawai == 'ASN' ? 'selected' : '' ?>>ASN</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Penugasan</label>
                        <select class="form-select" name="penugasan">
                            <?php foreach (['Guru', 'Guru & TAS', 'TAS', 'Kepala Sekolah'] as $penugasan): ?>
                                <option value="<?php echo $penugasan ?>" <?php echo trim($row->penugasan) == $penugasan ? 'selected' : '' ?>><?php echo $penugasan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12 mb-20">
                        <label class="form-label">Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="<?php echo html_escape($row->alamat) ?>">
                    </div>
                    <div class="col-md-2 mb-20">
                        <label class="form-label">RT</label>
                        <input type="text" class="form-control" name="rt" value="<?php echo html_escape($row->rt) ?>">
                    </div>
                    <div class="col-md-2 mb-20">
                        <label class="form-label">RW</label>
                        <input type="text" class="form-control" name="rw" value="<?php echo html_escape($row->rw) ?>">
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Provinsi</label>
                        <select class="form-select" name="provinsi" id="guru_provinsi">
                            <option value="<?php echo html_escape($row->provinsi) ?>"><?php echo html_escape($row->provinsi ?: 'Pilih Provinsi') ?></option>
                            <?php foreach ($provinsi as $p): ?>
                                <option value="<?php echo $p->id_prov ?>"><?php echo html_escape($p->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Kabupaten</label>
                        <select class="form-select" name="kabupaten" id="guru_kabupaten">
                            <option value="<?php echo html_escape($row->kabupaten) ?>"><?php echo html_escape($row->kabupaten ?: 'Pilih Kabupaten') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Kecamatan</label>
                        <select class="form-select" name="kecamatan" id="guru_kecamatan">
                            <option value="<?php echo html_escape($row->kecamatan) ?>"><?php echo html_escape($row->kecamatan ?: 'Pilih Kecamatan') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Kelurahan/Desa</label>
                        <select class="form-select" name="kelurahan_desa" id="guru_kelurahan">
                            <option value="<?php echo html_escape($row->kelurahan_desa) ?>"><?php echo html_escape($row->kelurahan_desa ?: 'Pilih Kelurahan') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-20">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diganti">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary-600 px-4">Simpan Profil</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $('#guru_provinsi').on('change', function() {
        $.post('<?php echo url('guru/getKabupaten') ?>', { id: $(this).val() }, function(data) {
            $('#guru_kabupaten').html('<option value="">Pilih Kabupaten</option>');
            $.each(data, function(_, value) {
                $('#guru_kabupaten').append('<option value="' + value.id_kab + '">' + value.nama + '</option>');
            });
        }, 'json');
    });

    $('#guru_kabupaten').on('change', function() {
        $.post('<?php echo url('guru/getKecamatan') ?>', { id: $(this).val() }, function(data) {
            $('#guru_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $.each(data, function(_, value) {
                $('#guru_kecamatan').append('<option value="' + value.id_kec + '">' + value.nama + '</option>');
            });
        }, 'json');
    });

    $('#guru_kecamatan').on('change', function() {
        $.post('<?php echo url('guru/getKelurahan') ?>', { id: $(this).val() }, function(data) {
            $('#guru_kelurahan').html('<option value="">Pilih Kelurahan</option>');
            $.each(data, function(_, value) {
                $('#guru_kelurahan').append('<option value="' + value.id_kel + '">' + value.nama + '</option>');
            });
        }, 'json');
    });
</script>
