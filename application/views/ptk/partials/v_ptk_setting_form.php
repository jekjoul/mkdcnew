<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php 
$action_url = !empty($row->id_ptk) ? url('ptk/ptkUpdate/' . $row->id_ptk) : url('profile/updateProfileFallback'); 
?>
<form action="<?php echo $action_url ?>" method="post" enctype="multipart/form-data">
    <div class="mb-24 mt-16">
        <div class="avatar-upload">
            <div class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                <input type="file" id="imageUpload" name="foto" accept=".png, .jpg, .jpeg" hidden>
                <label for="imageUpload" class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                    <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                </label>
            </div>
            <div class="avatar-preview">
                <div id="imagePreview" style="background-image: url('<?php echo $foto_ptk ?>');"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="mb-20">
                <label for="setting_nama_ptk" class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control radius-8" id="setting_nama_ptk" name="nama_ptk" required value="<?php echo htmlspecialchars($row->nama_ptk, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="mb-20">
                <label for="setting_pin_fingerprint" class="form-label fw-semibold text-primary-light text-sm mb-8">PIN Sidik Jari</label>
                <input type="number" class="form-control radius-8" id="setting_pin_fingerprint" name="pin_fingerprint" value="<?php echo htmlspecialchars($row->pin_fingerprint ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_jenis_kelamin" class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Kelamin <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_jenis_kelamin" name="jenis_kelamin" required>
                    <option value="Laki-laki" <?php echo $row->jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?php echo $row->jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_gelar_depan" class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Depan</label>
                <input type="text" class="form-control radius-8" id="setting_gelar_depan" name="gelar_depan" value="<?php echo htmlspecialchars($row->gelar_depan, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_gelar_belakang" class="form-label fw-semibold text-primary-light text-sm mb-8">Gelar Belakang</label>
                <input type="text" class="form-control radius-8" id="setting_gelar_belakang" name="gelar_belakang" value="<?php echo htmlspecialchars($row->gelar_belakang, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_tempat_lahir" class="form-label fw-semibold text-primary-light text-sm mb-8">Tempat Lahir</label>
                <input type="text" class="form-control radius-8" id="setting_tempat_lahir" name="tempat_lahir" value="<?php echo htmlspecialchars($row->tempat_lahir, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_tanggal_lahir" class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Lahir</label>
                <input type="date" class="form-control radius-8" id="setting_tanggal_lahir" name="tanggal_lahir" value="<?php echo $row->tanggal_lahir ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_agama" class="form-label fw-semibold text-primary-light text-sm mb-8">Agama <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_agama" name="agama" required>
                    <?php foreach (['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan'] as $agama): ?>
                        <option value="<?php echo $agama ?>" <?php echo $row->agama == $agama ? 'selected' : '' ?>><?php echo $agama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_status_perkawinan" class="form-label fw-semibold text-primary-light text-sm mb-8">Status Perkawinan <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_status_perkawinan" name="status_perkawinan" required>
                    <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status_perkawinan): ?>
                        <option value="<?php echo $status_perkawinan ?>" <?php echo $row->status_perkawinan == $status_perkawinan ? 'selected' : '' ?>><?php echo $status_perkawinan ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_nama_ibu_kandung" class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ibu Kandung</label>
                <input type="text" class="form-control radius-8" id="setting_nama_ibu_kandung" name="nama_ibu_kandung" value="<?php echo htmlspecialchars($row->nama_ibu_kandung, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_nik" class="form-label fw-semibold text-primary-light text-sm mb-8">NIK</label>
                <input type="text" class="form-control radius-8" id="setting_nik" name="nik" value="<?php echo htmlspecialchars($row->nik, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_niy" class="form-label fw-semibold text-primary-light text-sm mb-8">NIY</label>
                <input type="text" class="form-control radius-8" id="setting_niy" name="niy" value="<?php echo htmlspecialchars($row->niy, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_nuptk" class="form-label fw-semibold text-primary-light text-sm mb-8">NUPTK</label>
                <input type="text" class="form-control radius-8" id="setting_nuptk" name="nuptk" value="<?php echo htmlspecialchars($row->nuptk, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_no_sk_pengangkatan" class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor SK Pengangkatan</label>
                <input type="text" class="form-control radius-8" id="setting_no_sk_pengangkatan" name="no_sk_pengangkatan" value="<?php echo htmlspecialchars($row->no_sk_pengangkatan, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_tgl_sk_pengangkatan" class="form-label fw-semibold text-primary-light text-sm mb-8">Tgl SK Pengangkatan</label>
                <input type="date" class="form-control radius-8" id="setting_tgl_sk_pengangkatan" name="tgl_sk_pengangkatan" value="<?php echo $row->tgl_sk_pengangkatan ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_email" class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                <input type="email" class="form-control radius-8" id="setting_email" name="email" value="<?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_telepon" class="form-label fw-semibold text-primary-light text-sm mb-8">No Ponsel</label>
                <input type="text" class="form-control radius-8" id="setting_telepon" name="telepon" value="<?php echo htmlspecialchars($row->telepon, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_status_pegawai" class="form-label fw-semibold text-primary-light text-sm mb-8">Status Pegawai <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_status_pegawai" name="status_pegawai" required>
                    <option value="GTY/PTY" <?php echo $row->status_pegawai == 'GTY/PTY' ? 'selected' : '' ?>>GTY/PTY</option>
                    <option value="ASN" <?php echo $row->status_pegawai == 'ASN' ? 'selected' : '' ?>>ASN</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_penugasan" class="form-label fw-semibold text-primary-light text-sm mb-8">Penugasan <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_penugasan" name="penugasan" required>
                    <?php foreach (['Guru', 'Guru & TAS', 'TAS', 'Kepala Sekolah'] as $penugasan): ?>
                        <option value="<?php echo $penugasan ?>" <?php echo trim($row->penugasan) == $penugasan ? 'selected' : '' ?>><?php echo $penugasan ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_status_keaktifan" class="form-label fw-semibold text-primary-light text-sm mb-8">Status Keaktifan <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_status_keaktifan" name="status_keaktifan" required>
                    <option value="Aktif" <?php echo $row->status_keaktifan == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Nonaktif" <?php echo $row->status_keaktifan == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="mb-20">
                <label for="setting_alamat" class="form-label fw-semibold text-primary-light text-sm mb-8">Alamat</label>
                <input type="text" class="form-control radius-8" id="setting_alamat" name="alamat" value="<?php echo htmlspecialchars($row->alamat, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_rt" class="form-label fw-semibold text-primary-light text-sm mb-8">RT</label>
                <input type="text" class="form-control radius-8" id="setting_rt" name="rt" value="<?php echo htmlspecialchars($row->rt, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_rw" class="form-label fw-semibold text-primary-light text-sm mb-8">RW</label>
                <input type="text" class="form-control radius-8" id="setting_rw" name="rw" value="<?php echo htmlspecialchars($row->rw, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_provinsi" class="form-label fw-semibold text-primary-light text-sm mb-8">Provinsi <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_provinsi" name="provinsi" >
                    <option value="<?php echo htmlspecialchars($row->provinsi, ENT_QUOTES, 'UTF-8') ?>"><?php echo $row->provinsi ?: 'Pilih Provinsi' ?></option>
                    <?php foreach ($provinsi as $p): ?>
                        <option value="<?php echo $p->id_prov ?>"><?php echo $p->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_kabupaten" class="form-label fw-semibold text-primary-light text-sm mb-8">Kabupaten <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_kabupaten" name="kabupaten" >
                    <option value="<?php echo htmlspecialchars($row->kabupaten, ENT_QUOTES, 'UTF-8') ?>"><?php echo $row->kabupaten ?: 'Pilih Kabupaten' ?></option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_kecamatan" class="form-label fw-semibold text-primary-light text-sm mb-8">Kecamatan <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_kecamatan" name="kecamatan" >
                    <option value="<?php echo htmlspecialchars($row->kecamatan, ENT_QUOTES, 'UTF-8') ?>"><?php echo $row->kecamatan ?: 'Pilih Kecamatan' ?></option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-20">
                <label for="setting_kelurahan_desa" class="form-label fw-semibold text-primary-light text-sm mb-8">Kelurahan/Desa <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select" id="setting_kelurahan_desa" name="kelurahan_desa" >
                    <option value="<?php echo htmlspecialchars($row->kelurahan_desa, ENT_QUOTES, 'UTF-8') ?>"><?php echo $row->kelurahan_desa ?: 'Pilih Kelurahan' ?></option>
                </select>
            </div>
        </div>
        
    </div>

    <div class="d-flex align-items-center justify-content-center gap-3">
        <button type="submit" class="btn btn-success border border-success-600 text-md px-56 py-12 radius-8">
            Simpan Perubahan PTK
        </button>
    </div>
</form>
