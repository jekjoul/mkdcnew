<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$is_edit = !empty($row);
$action = $is_edit ? url('calon_siswa/update/' . $row->id_calon_siswa) : url('calon_siswa/simpan');
if (!function_exists('calon_siswa_value')) {
    function calon_siswa_value($row, $field, $default = '')
    {
        return $row && isset($row->$field) ? htmlspecialchars($row->$field, ENT_QUOTES, 'UTF-8') : $default;
    }
}
$pekerjaan_options = isset($pekerjaan_options) ? $pekerjaan_options : [
    'Wiraswasta',
    'Karyawan Swasta',
    'Buruh Harian Lepas',
    'ASN/PPPK',
    'TNI',
    'Polri',
    'Ustadz/Mubaligh',
    'Petani/Peternak',
    'Ibu Rumah Tangga',
];
$jenis_tempat_tinggal_options = isset($jenis_tempat_tinggal_options) ? $jenis_tempat_tinggal_options : ['Bersama Orang Tua', 'Bersama Saudara', 'Pondok Pesantren', 'Panti Asuhan'];
$alat_transportasi_options = isset($alat_transportasi_options) ? $alat_transportasi_options : ['Jalan Kaki', 'Transportasi Umum', 'Kendaraan Roda Dua', 'Kendaraan Roda Empat'];
$jenis_pendidikan_options = isset($jenis_pendidikan_options) ? $jenis_pendidikan_options : ['Hanya Sekolah', 'Sekolah & Pesantren', 'Hanya Pesantren'];
$lembaga = isset($lembaga) ? $lembaga : [];
?>
<form action="<?php echo $action ?>" method="post">
    <div class="row">
        <div class="col-sm-12 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap <span class="text-danger-600">*</span></label>
            <input type="text" class="form-control radius-8" name="nama_siswa" required value="<?php echo calon_siswa_value($row, 'nama_siswa') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NISN</label>
            <input type="text" class="form-control radius-8" name="nisn" value="<?php echo calon_siswa_value($row, 'nisn') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIK</label>
            <input type="text" class="form-control radius-8" name="nik" value="<?php echo calon_siswa_value($row, 'nik') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">No Kartu Keluarga</label>
            <input type="text" class="form-control radius-8" name="no_kk" value="<?php echo calon_siswa_value($row, 'no_kk') ?>">
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Kelamin</label>
            <select class="form-control radius-8 form-select" name="jenis_kelamin">
                <option value="Laki-laki" <?php echo calon_siswa_value($row, 'jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo calon_siswa_value($row, 'jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tempat Lahir</label><input type="text" class="form-control radius-8" name="tempat_lahir" value="<?php echo calon_siswa_value($row, 'tempat_lahir') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lahir</label><input type="date" class="form-control radius-8" name="tanggal_lahir" value="<?php echo calon_siswa_value($row, 'tanggal_lahir') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Agama</label>
            <select class="form-control radius-8 form-select" name="agama">
                <?php foreach (['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan'] as $agama): ?>
                    <option value="<?php echo $agama ?>" <?php echo calon_siswa_value($row, 'agama') == $agama ? 'selected' : '' ?>><?php echo $agama ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Telepon</label><input type="text" class="form-control radius-8" name="telepon" value="<?php echo calon_siswa_value($row, 'telepon') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label><input type="email" class="form-control radius-8" name="email" value="<?php echo calon_siswa_value($row, 'email') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lembaga Tujuan</label>
            <select class="form-control radius-8 form-select" name="id_lembaga_tujuan">
                <option value="">Pilih Lembaga</option>
                <?php foreach ($lembaga as $l): ?>
                    <option value="<?php echo $l->id_lembaga ?>" <?php echo calon_siswa_value($row, 'id_lembaga_tujuan') == $l->id_lembaga ? 'selected' : '' ?>><?php echo htmlspecialchars($l->nama_lembaga, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Pendidikan</label>
            <select class="form-control radius-8 form-select" name="jenis_pendidikan">
                <option value="">Pilih Jenis Pendidikan</option>
                <?php foreach ($jenis_pendidikan_options as $option): ?>
                    <option value="<?php echo $option ?>" <?php echo calon_siswa_value($row, 'jenis_pendidikan') === $option ? 'selected' : '' ?>><?php echo $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Pendaftaran</label><input type="date" class="form-control radius-8" name="tanggal_pendaftaran" value="<?php echo calon_siswa_value($row, 'tanggal_pendaftaran', date('Y-m-d')) ?>" readonly></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kewarganegaraan</label><input type="text" class="form-control radius-8" name="kewarganegaraan" value="<?php echo calon_siswa_value($row, 'kewarganegaraan', 'Indonesia') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Sekolah Asal</label><input type="text" class="form-control radius-8" name="sekolah_asal" value="<?php echo calon_siswa_value($row, 'sekolah_asal') ?>"></div>
        <div class="col-sm-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat Siswa</label><input type="text" class="form-control radius-8" name="alamat" value="<?php echo calon_siswa_value($row, 'alamat') ?>"></div>
        <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label><input type="text" class="form-control radius-8" name="rt" value="<?php echo calon_siswa_value($row, 'rt') ?>"></div>
        <div class="col-sm-3 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label><input type="text" class="form-control radius-8" name="rw" value="<?php echo calon_siswa_value($row, 'rw') ?>"></div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Tempat Tinggal</label>
            <select class="form-control radius-8 form-select" name="jenis_tempat_tinggal">
                <option value="">Pilih Tempat Tinggal</option>
                <?php foreach ($jenis_tempat_tinggal_options as $option): ?>
                    <option value="<?php echo $option ?>" <?php echo calon_siswa_value($row, 'jenis_tempat_tinggal') === $option ? 'selected' : '' ?>><?php echo $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-3 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alat Transportasi</label>
            <select class="form-control radius-8 form-select" name="alat_transportasi">
                <option value="">Pilih Transportasi</option>
                <?php foreach ($alat_transportasi_options as $option): ?>
                    <option value="<?php echo $option ?>" <?php echo calon_siswa_value($row, 'alat_transportasi') === $option ? 'selected' : '' ?>><?php echo $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Jarak ke Sekolah</label><input type="text" class="form-control radius-8" name="jarak_ke_sekolah" id="jarak_ke_sekolah" value="<?php echo calon_siswa_value($row, 'jarak_ke_sekolah') ?>" readonly></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Koordinat</label><input type="text" class="form-control radius-8" name="koordinat" id="koordinat" value="<?php echo calon_siswa_value($row, 'koordinat') ?>" readonly></div>
        <div class="col-sm-12 mb-20">
            <div id="map-koordinat" class="radius-8 border" style="height: 360px; overflow: hidden;"></div>
        </div>
        <div class="col-sm-6 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi Siswa</label>
            <select class="form-control radius-8 form-select wilayah-provinsi" name="id_provinsi" id="id_provinsi">
                <option value="<?php echo calon_siswa_value($row, 'id_provinsi') ?>"><?php echo calon_siswa_value($row, 'id_provinsi', 'Pilih Provinsi') ?></option>
                <?php foreach ($provinsi as $p): ?><option value="<?php echo $p->id_prov ?>"><?php echo $p->nama ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten Siswa</label><select class="form-control radius-8 form-select wilayah-kabupaten" name="id_kabupaten" id="id_kabupaten">
                <option value="<?php echo calon_siswa_value($row, 'id_kabupaten') ?>"><?php echo calon_siswa_value($row, 'id_kabupaten', 'Pilih Kabupaten') ?></option>
            </select></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan Siswa</label><select class="form-control radius-8 form-select wilayah-kecamatan" name="id_kecamatan" id="id_kecamatan">
                <option value="<?php echo calon_siswa_value($row, 'id_kecamatan') ?>"><?php echo calon_siswa_value($row, 'id_kecamatan', 'Pilih Kecamatan') ?></option>
            </select></div>
        <div class="col-sm-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan Siswa</label><select class="form-control radius-8 form-select" name="id_kelurahan" id="id_kelurahan">
                <option value="<?php echo calon_siswa_value($row, 'id_kelurahan') ?>"><?php echo calon_siswa_value($row, 'id_kelurahan', 'Pilih Kelurahan') ?></option>
            </select></div>
        <div class="col-sm-12">
            <hr>
            <h6 class="mt-3">Data Ayah</h6>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ayah</label><input type="text" class="form-control radius-8" name="nama_ayah" value="<?php echo calon_siswa_value($row, 'nama_ayah') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">NIK Ayah</label><input type="text" class="form-control radius-8" name="nik_ayah" value="<?php echo calon_siswa_value($row, 'nik_ayah') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pekerjaan Ayah</label>
            <select class="form-control radius-8 form-select" name="pekerjaan_ayah">
                <option value="">Pilih Pekerjaan</option>
                <?php foreach ($pekerjaan_options as $pekerjaan): ?>
                    <option value="<?php echo $pekerjaan ?>" <?php echo calon_siswa_value($row, 'pekerjaan_ayah') === $pekerjaan ? 'selected' : '' ?>><?php echo $pekerjaan ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-12">
            <hr>
            <h6 class="mt-3">Data Ibu</h6>
        </div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ibu</label><input type="text" class="form-control radius-8" name="nama_ibu" value="<?php echo calon_siswa_value($row, 'nama_ibu') ?>"></div>
        <div class="col-sm-4 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">NIK Ibu</label><input type="text" class="form-control radius-8" name="nik_ibu" value="<?php echo calon_siswa_value($row, 'nik_ibu') ?>"></div>
        <div class="col-sm-4 mb-20">
            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pekerjaan Ibu</label>
            <select class="form-control radius-8 form-select" name="pekerjaan_ibu">
                <option value="">Pilih Pekerjaan</option>
                <?php foreach ($pekerjaan_options as $pekerjaan): ?>
                    <option value="<?php echo $pekerjaan ?>" <?php echo calon_siswa_value($row, 'pekerjaan_ibu') === $pekerjaan ? 'selected' : '' ?>><?php echo $pekerjaan ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-3">
        <button type="submit" class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8"><?php echo $is_edit ? 'Simpan Perubahan' : 'Simpan & Upload Berkas'; ?></button>
    </div>
</form>