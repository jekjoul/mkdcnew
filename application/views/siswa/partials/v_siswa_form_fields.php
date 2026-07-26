<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$is_edit = !empty($row);
$action = $is_edit ? url('siswa/update/' . $row->id_siswa) : url('siswa/simpan');
function siswa_value($row, $field, $default = '')
{
    return $row && isset($row->$field) ? htmlspecialchars($row->$field, ENT_QUOTES, 'UTF-8') : $default;
}
?>
<form action="<?php echo $action ?>" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap <span class="text-danger-600">*</span></label>
            <input type="text" class="form-control radius-8" name="nama_siswa" required value="<?php echo siswa_value($row, 'nama_siswa') ?>">
        </div>
        <div class="col-sm-2 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">PIN Sidik Jari</label>
            <input type="number" class="form-control radius-8" name="pin_fingerprint" placeholder="PIN" value="<?php echo siswa_value($row, 'pin_fingerprint') ?>">
        </div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NISN</label>
            <input type="text" class="form-control radius-8" name="nisn" value="<?php echo siswa_value($row, 'nisn') ?>">
        </div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIPD</label>
            <input type="text" class="form-control radius-8" name="nipd" value="<?php echo siswa_value($row, 'nipd') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIK</label>
            <input type="text" class="form-control radius-8" name="nik" value="<?php echo siswa_value($row, 'nik') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">No Kartu Keluarga</label>
            <input type="text" class="form-control radius-8" name="no_kk" value="<?php echo siswa_value($row, 'no_kk') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Kelamin</label>
            <select class="form-control radius-8 form-select" name="jenis_kelamin">
                <option value="Laki-laki" <?php echo siswa_value($row, 'jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo siswa_value($row, 'jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tempat Lahir</label>
            <input type="text" class="form-control radius-8" name="tempat_lahir" value="<?php echo siswa_value($row, 'tempat_lahir') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lahir</label>
            <input type="date" class="form-control radius-8" name="tanggal_lahir" value="<?php echo siswa_value($row, 'tanggal_lahir') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Agama</label>
            <select class="form-control radius-8 form-select" name="agama">
                <?php foreach (['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan'] as $agama): ?>
                    <option value="<?php echo $agama ?>" <?php echo siswa_value($row, 'agama') == $agama ? 'selected' : '' ?>><?php echo $agama ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Telepon</label><input type="text" class="form-control radius-8" name="telepon" value="<?php echo siswa_value($row, 'telepon') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label><input type="email" class="form-control radius-8" name="email" value="<?php echo siswa_value($row, 'email') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lembaga</label>
            <select class="form-control radius-8 form-select" id="id_lembaga_tujuan">
                <option value="">Pilih Lembaga</option>
                <?php if (isset($lembaga)): foreach ($lembaga as $l): ?>
                    <option value="<?php echo $l->id_lembaga ?>" data-coordinate="<?php echo htmlspecialchars($l->koordinat, ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($l->nama_lembaga) ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pendaftaran</label><input type="date" class="form-control radius-8" name="tanggal_pendaftaran" value="<?php echo siswa_value($row, 'tanggal_pendaftaran') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Pendaftaran</label><input type="text" class="form-control radius-8" name="status_pendaftaran" value="<?php echo siswa_value($row, 'status_pendaftaran') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">No Ijazah</label><input type="text" class="form-control radius-8" name="no_ijazah" value="<?php echo siswa_value($row, 'no_ijazah') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kewarganegaraan</label><input type="text" class="form-control radius-8" name="kewarganegaraan" value="<?php echo siswa_value($row, 'kewarganegaraan', 'Indonesia') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Anak Ke</label><input type="number" min="1" class="form-control radius-8" name="anak_ke" value="<?php echo siswa_value($row, 'anak_ke') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Sekolah Asal</label><input type="text" class="form-control radius-8" name="sekolah_asal" value="<?php echo siswa_value($row, 'sekolah_asal') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Keaktifan</label>
            <select class="form-control radius-8 form-select" name="status_keaktifan">
                <?php foreach (['Aktif', 'Nonaktif', 'Lulus', 'Pindah', 'Keluar'] as $status): ?>
                    <option value="<?php echo $status ?>" <?php echo siswa_value($row, 'status_keaktifan', 'Aktif') == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                <?php endforeach; ?>
            </select>
            <small class="text-secondary-light">Status Lulus, Pindah, atau Keluar akan memindahkan data siswa ke Alumni.</small>
        </div>
        <div class="col-sm-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Siswa</label><input type="text" class="form-control radius-8" name="alamat" value="<?php echo siswa_value($row, 'alamat') ?>"></div>
        <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label><input type="text" class="form-control radius-8" name="rt" value="<?php echo siswa_value($row, 'rt') ?>"></div>
        <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label><input type="text" class="form-control radius-8" name="rw" value="<?php echo siswa_value($row, 'rw') ?>"></div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Tempat Tinggal</label>
            <select class="form-control radius-8 form-select" name="jenis_tempat_tinggal">
                <option value="">Pilih Tempat Tinggal</option>
                <?php if (isset($jenis_tempat_tinggal_options)): foreach ($jenis_tempat_tinggal_options as $option): ?>
                    <option value="<?php echo $option ?>" <?php echo siswa_value($row, 'jenis_tempat_tinggal') === $option ? 'selected' : '' ?>><?php echo $option ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alat Transportasi</label>
            <select class="form-control radius-8 form-select" name="alat_transportasi">
                <option value="">Pilih Transportasi</option>
                <?php if (isset($alat_transportasi_options)): foreach ($alat_transportasi_options as $option): ?>
                    <option value="<?php echo $option ?>" <?php echo siswa_value($row, 'alat_transportasi') === $option ? 'selected' : '' ?>><?php echo $option ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Jarak ke Sekolah</label><input type="text" class="form-control radius-8" name="jarak_ke_sekolah" id="jarak_ke_sekolah" value="<?php echo siswa_value($row, 'jarak_ke_sekolah') ?>" readonly></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Koordinat</label><input type="text" class="form-control radius-8" name="koordinat" id="koordinat" value="<?php echo siswa_value($row, 'koordinat') ?>" readonly></div>
        <div class="col-sm-12 mb-20">
            <div id="map-koordinat" class="radius-8 border" style="height: 380px; min-height: 380px; width: 100%; overflow: hidden; position: relative; z-index: 1;"></div>
        </div>
        <?php foreach (['' => 'Siswa', '_ayah' => 'Ayah', '_ibu' => 'Ibu'] as $suffix => $label): ?>
            <?php if ($suffix): ?>
                <div class="col-sm-12"><hr><h6>Data <?php echo $label ?></h6></div>
                <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama <?php echo $label ?></label><input type="text" class="form-control radius-8" name="nama<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'nama' . $suffix) ?>"></div>
                <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">NIK <?php echo $label ?></label><input type="text" class="form-control radius-8" name="nik<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'nik' . $suffix) ?>"></div>
                <div class="col-sm-4 mb-20">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pekerjaan <?php echo $label ?></label>
                    <select class="form-control radius-8 form-select" name="pekerjaan<?php echo $suffix ?>">
                        <option value="">Pilih Pekerjaan</option>
                        <?php if (isset($pekerjaan_options)): foreach ($pekerjaan_options as $pekerjaan): ?>
                            <option value="<?php echo $pekerjaan ?>" <?php echo siswa_value($row, 'pekerjaan' . $suffix) === $pekerjaan ? 'selected' : '' ?>><?php echo $pekerjaan ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Penghasilan <?php echo $label ?></label><input type="text" class="form-control radius-8" name="penghasilan<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'penghasilan' . $suffix) ?>"></div>
                <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tahun Lahir <?php echo $label ?></label><input type="number" class="form-control radius-8" name="tahun_lahir<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'tahun_lahir' . $suffix) ?>"></div>
                <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Pendidikan <?php echo $label ?></label><input type="text" class="form-control radius-8" name="pendidikan<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'pendidikan' . $suffix) ?>"></div>
                <div class="col-sm-12 mb-12">
                    <label class="form-check-label"><input type="checkbox" class="form-check-input alamat-sama" data-target="<?php echo substr($suffix, 1) ?>" name="alamat<?php echo $suffix ?>_sama_siswa" <?php echo siswa_value($row, 'alamat' . $suffix . '_sama_siswa') == '1' ? 'checked' : '' ?>> Alamat <?php echo $label ?> sama dengan siswa</label>
                </div>
                <div class="alamat-orangtua alamat-<?php echo substr($suffix, 1) ?>">
                    <div class="col-sm-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat <?php echo $label ?></label><input type="text" class="form-control radius-8" name="alamat<?php echo $suffix ?>" value="<?php echo siswa_value($row, 'alamat' . $suffix) ?>"></div>
                </div>
            <?php endif; ?>
            <div class="col-sm-6 mb-20">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi <?php echo $label ?></label>
                <select class="form-control radius-8 form-select wilayah-provinsi" data-prefix="<?php echo $suffix ?>" name="id_provinsi<?php echo $suffix ?>" id="id_provinsi<?php echo $suffix ?>">
                    <option value="<?php echo siswa_value($row, 'id_provinsi' . $suffix) ?>"><?php echo siswa_value($row, 'id_provinsi' . $suffix, 'Pilih Provinsi') ?></option>
                    <?php foreach ($provinsi as $p): ?><option value="<?php echo $p->id_prov ?>"><?php echo $p->nama ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten <?php echo $label ?></label><select class="form-control radius-8 form-select wilayah-kabupaten" data-prefix="<?php echo $suffix ?>" name="id_kabupaten<?php echo $suffix ?>" id="id_kabupaten<?php echo $suffix ?>"><option value="<?php echo siswa_value($row, 'id_kabupaten' . $suffix) ?>"><?php echo siswa_value($row, 'id_kabupaten' . $suffix, 'Pilih Kabupaten') ?></option></select></div>
            <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan <?php echo $label ?></label><select class="form-control radius-8 form-select wilayah-kecamatan" data-prefix="<?php echo $suffix ?>" name="id_kecamatan<?php echo $suffix ?>" id="id_kecamatan<?php echo $suffix ?>"><option value="<?php echo siswa_value($row, 'id_kecamatan' . $suffix) ?>"><?php echo siswa_value($row, 'id_kecamatan' . $suffix, 'Pilih Kecamatan') ?></option></select></div>
            <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan <?php echo $label ?></label><select class="form-control radius-8 form-select" name="id_kelurahan<?php echo $suffix ?>" id="id_kelurahan<?php echo $suffix ?>"><option value="<?php echo siswa_value($row, 'id_kelurahan' . $suffix) ?>"><?php echo siswa_value($row, 'id_kelurahan' . $suffix, 'Pilih Kelurahan') ?></option></select></div>
        <?php endforeach; ?>
        <div class="col-sm-12"><hr><h6>Foto Siswa</h6></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Foto</label><input type="text" class="form-control radius-8" name="label_foto" placeholder="Contoh: Foto 2026"></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Upload Foto</label><input type="file" class="form-control radius-8" name="foto[]" accept=".jpg,.jpeg,.png" multiple><small class="text-secondary-light">Bisa pilih lebih dari satu foto.</small></div>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-3">
        <button type="submit" class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8">Simpan Siswa</button>
    </div>
</form>
