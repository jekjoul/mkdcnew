<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<?php
$foto_ptk = (!empty($row->foto) && $row->foto !== 'default.png') ? url('uploads/ptk_foto/' . $row->foto) : $url->assets . 'images/user-grid/guru.png';
?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <!-- Kolom Kiri: Ringkasan Data Pribadi & Foto -->
        <div class="col-lg-4">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100 shadow-sm">
                <img src="<?php echo $url->assets ?>images/user-grid/user-grid-bg-guru.jpg" alt="" class="w-100 object-fit-cover" style="height: 120px;">
                <div class="pb-24 ms-16 mb-24 me-16 mt--100 text-center">
                    <div class="border border-top-0 border-start-0 border-end-0 pb-20">
                        <img src="<?php echo $foto_ptk ?>" alt="" class="border br-white border-width-2-px w-150-px h-150-px rounded-circle object-fit-cover bg-white">
                        <h6 class="mb-0 mt-16"><?php echo ($row->gelar_depan ? $row->gelar_depan . ' ' : '') . $row->nama_ptk . ($row->gelar_belakang ? ', ' . $row->gelar_belakang : '') ?></h6>
                        <span class="text-secondary-light mb-16 d-block"><?php echo $row->penugasan ?></span>
                        <span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white"><?php echo $row->status_pegawai ?></span>
                        <span class="badge text-sm fw-semibold bg-dark-info-gradient px-20 py-9 radius-4 text-white">Aktif</span>
                    </div>
                    <div class="mt-24 text-start">
                        <h6 class="text-xl mb-16">Data Pribadi</h6>
                        <ul>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Nama</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->nama_ptk ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">NIY</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->niy ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">NIK</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->nik ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Email</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->email ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">No HP</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->telepon ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Tempat Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo $row->tempat_lahir ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-40 text-md fw-semibold text-primary-light">Tgl. Lahir</span>
                                <span class="w-60 text-secondary-light fw-medium">: <?php echo date('d F Y', strtotime($row->tanggal_lahir)) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tab Detail Profil, Dokumen, dan Edit Form -->
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-24">
                    <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24 active" id="pills-profile-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab"
                                aria-controls="pills-profile" aria-selected="true">
                                Detail Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-pendidikan-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-pendidikan" type="button" role="tab"
                                aria-controls="pills-pendidikan" aria-selected="false">
                                Pendidikan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-arsip-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-arsip" type="button" role="tab"
                                aria-controls="pills-arsip" aria-selected="false">
                                Dokumen / Arsip
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-setting-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-setting" type="button" role="tab"
                                aria-controls="pills-setting" aria-selected="false">
                                Edit Profil & Akun
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Tab 1: Detail Profil -->
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="card radius-12 border-0 bg-transparent">
                                <div class="card-header py-16 px-0 bg-transparent d-flex align-items-center justify-content-between border-0">
                                    <h6 class="text-lg mb-0">Informasi Profil PTK</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Nama Lengkap :</small>
                                                <strong class="text-primary-light"><?php echo ($row->gelar_depan ? $row->gelar_depan . ' ' : '') . $row->nama_ptk . ($row->gelar_belakang ? ', ' . $row->gelar_belakang : '') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Jenis Kelamin :</small>
                                                <strong class="text-primary-light"><?php echo $row->jenis_kelamin ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Tempat, Tanggal Lahir :</small>
                                                <strong class="text-primary-light"><?php echo $row->tempat_lahir . ', ' . date('d F Y', strtotime($row->tanggal_lahir)) ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Agama :</small>
                                                <strong class="text-primary-light"><?php echo $row->agama ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Status Perkawinan :</small>
                                                <strong class="text-primary-light"><?php echo $row->status_perkawinan ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Nama Ibu Kandung :</small>
                                                <strong class="text-primary-light"><?php echo $row->nama_ibu_kandung ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">NIK :</small>
                                                <strong class="text-primary-light"><?php echo $row->nik ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">NIY / NUPTK :</small>
                                                <strong class="text-primary-light"><?php echo $row->niy . ' / ' . ($row->nuptk ?: '-') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-16">
                                            <div class="border rounded p-12 bg-light">
                                                <small class="text-muted d-block fst-italic">Alamat :</small>
                                                <strong class="text-primary-light"><?php echo $row->alamat . ' RT ' . ($row->rt ?: '0') . ' RW ' . ($row->rw ?: '0') . ', Kel/Desa. ' . ($row->kelurahan_desa ?: '-') . ', Kec. ' . ($row->kecamatan ?: '-') . ', Kab/Kota. ' . ($row->kabupaten ?: '-') . ', Prov. ' . ($row->provinsi ?: '-') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Pendidikan -->
                        <div class="tab-pane fade" id="pills-pendidikan" role="tabpanel" aria-labelledby="pills-pendidikan-tab">
                            <div class="card radius-12 border-0 bg-transparent">
                                <div class="card-header py-16 px-0 bg-transparent d-flex align-items-center justify-content-between border-0">
                                    <h6 class="text-lg mb-0">Riwayat Pendidikan Formal</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table bordered-table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Jenjang</th>
                                                    <th>Satuan Pendidikan</th>
                                                    <th>Jurusan</th>
                                                    <th>Tahun Lulus</th>
                                                    <th>No Ijazah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($riwayat_pendidikan)): ?>
                                                    <?php foreach ($riwayat_pendidikan as $index => $edu): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1 ?></td>
                                                            <td><span class="badge bg-neutral-200 text-neutral-800"><?php echo html_escape($edu->jenjang) ?></span></td>
                                                            <td><strong><?php echo html_escape($edu->satuan_pendidikan) ?></strong></td>
                                                            <td><?php echo html_escape($edu->jurusan ?: '-') ?></td>
                                                            <td><?php echo html_escape($edu->tahun_lulus ?: '-') ?></td>
                                                            <td><span class="text-xs"><?php echo html_escape($edu->no_ijazah ?: '-') ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Belum ada riwayat pendidikan yang terdaftar.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Dokumen / Arsip -->
                        <div class="tab-pane fade" id="pills-arsip" role="tabpanel" aria-labelledby="pills-arsip-tab">
                            <div class="card radius-12 border-0 bg-transparent">
                                <div class="card-header py-16 px-0 bg-transparent d-flex align-items-center justify-content-between border-0">
                                    <h6 class="text-lg mb-0">Dokumen Pribadi & Arsip Digital</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table bordered-table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Jenis Dokumen</th>
                                                    <th>Nomor Dokumen</th>
                                                    <th>Tanggal Dokumen</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($dokumen_pribadi)): ?>
                                                    <?php foreach ($dokumen_pribadi as $index => $doc): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1 ?></td>
                                                            <td><strong><?php echo html_escape($doc->nama_jenis_dokumen ?: '-') ?></strong></td>
                                                            <td><?php echo html_escape($doc->nomor_dokumen ?: '-') ?></td>
                                                            <td><?php echo $doc->tanggal_dokumen ? date('d F Y', strtotime($doc->tanggal_dokumen)) : '-' ?></td>
                                                            <td class="text-center">
                                                                <a href="<?php echo url('uploads/ptk_dokumen_pribadi/' . $doc->berkas) ?>" target="_blank" class="btn btn-xs btn-outline-info d-inline-flex align-items-center gap-1">
                                                                    <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat Berkas
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">Belum ada dokumen yang diunggah.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Edit Profil & Akun -->
                        <div class="tab-pane fade" id="pills-setting" role="tabpanel" aria-labelledby="pills-setting-tab">
                            <div class="card radius-12 border-0 bg-transparent">
                                <div class="card-header py-16 px-0 bg-transparent d-flex align-items-center justify-content-between border-0">
                                    <h6 class="text-lg mb-0">Perbarui Data Profil & Akun Login</h6>
                                </div>
                                <div class="card-body p-0 mt-16">
                                    <form action="<?php echo url('guru/update_profil') ?>" method="post">
                                        <div class="row">
                                            <div class="col-md-6 mb-20">
                                                <label class="form-label fw-semibold text-sm">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_ptk" value="<?php echo html_escape($row->nama_ptk) ?>" required>
                                            </div>
                                            <div class="col-md-3 mb-20">
                                                <label class="form-label fw-semibold text-sm">Gelar Depan</label>
                                                <input type="text" class="form-control" name="gelar_depan" value="<?php echo html_escape($row->gelar_depan) ?>">
                                            </div>
                                            <div class="col-md-3 mb-20">
                                                <label class="form-label fw-semibold text-sm">Gelar Belakang</label>
                                                <input type="text" class="form-control" name="gelar_belakang" value="<?php echo html_escape($row->gelar_belakang) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Jenis Kelamin <span class="text-danger">*</span></label>
                                                <select class="form-select" name="jenis_kelamin" required>
                                                    <option value="Laki-laki" <?php echo $row->jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                                    <option value="Perempuan" <?php echo $row->jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Tempat Lahir <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="tempat_lahir" value="<?php echo html_escape($row->tempat_lahir) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Tanggal Lahir <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo html_escape($row->tanggal_lahir) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Agama <span class="text-danger">*</span></label>
                                                <select class="form-select" name="agama" required>
                                                    <?php foreach (['Islam', 'Katolik', 'Protestan', 'Hindu', 'Budha', 'Konghuchu', 'Kepercayaan'] as $agama): ?>
                                                        <option value="<?php echo $agama ?>" <?php echo $row->agama == $agama ? 'selected' : '' ?>><?php echo $agama ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Status Perkawinan <span class="text-danger">*</span></label>
                                                <select class="form-select" name="status_perkawinan" required>
                                                    <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status): ?>
                                                        <option value="<?php echo $status ?>" <?php echo $row->status_perkawinan == $status ? 'selected' : '' ?>><?php echo $status ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_ibu_kandung" value="<?php echo html_escape($row->nama_ibu_kandung) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">NIK <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nik" value="<?php echo html_escape($row->nik) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">NIY <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="niy" value="<?php echo html_escape($row->niy) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">NUPTK</label>
                                                <input type="text" class="form-control" name="nuptk" value="<?php echo html_escape($row->nuptk) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">No SK Pengangkatan</label>
                                                <input type="text" class="form-control" name="no_sk_pengangkatan" value="<?php echo html_escape($row->no_sk_pengangkatan) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Tgl SK Pengangkatan</label>
                                                <input type="date" class="form-control" name="tgl_sk_pengangkatan" value="<?php echo html_escape($row->tgl_sk_pengangkatan) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo html_escape($row->email) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Telepon / WhatsApp</label>
                                                <input type="text" class="form-control" name="telepon" value="<?php echo html_escape($row->telepon) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Status Pegawai <span class="text-danger">*</span></label>
                                                <select class="form-select" name="status_pegawai" required>
                                                    <option value="GTY/PTY" <?php echo $row->status_pegawai == 'GTY/PTY' ? 'selected' : '' ?>>GTY/PTY</option>
                                                    <option value="ASN" <?php echo $row->status_pegawai == 'ASN' ? 'selected' : '' ?>>ASN</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Penugasan <span class="text-danger">*</span></label>
                                                <select class="form-select" name="penugasan" required>
                                                    <?php foreach (['Guru', 'Guru & TAS', 'TAS', 'Kepala Sekolah'] as $penugasan): ?>
                                                        <option value="<?php echo $penugasan ?>" <?php echo trim($row->penugasan) == $penugasan ? 'selected' : '' ?>><?php echo $penugasan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-20">
                                                <label class="form-label fw-semibold text-sm">Alamat</label>
                                                <input type="text" class="form-control" name="alamat" value="<?php echo html_escape($row->alamat) ?>">
                                            </div>
                                            <div class="col-md-2 mb-20">
                                                <label class="form-label fw-semibold text-sm">RT</label>
                                                <input type="text" class="form-control" name="rt" value="<?php echo html_escape($row->rt) ?>">
                                            </div>
                                            <div class="col-md-2 mb-20">
                                                <label class="form-label fw-semibold text-sm">RW</label>
                                                <input type="text" class="form-control" name="rw" value="<?php echo html_escape($row->rw) ?>">
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Provinsi</label>
                                                <select class="form-select" name="provinsi" id="guru_provinsi">
                                                    <option value="<?php echo html_escape($row->provinsi) ?>"><?php echo html_escape($row->provinsi ?: 'Pilih Provinsi') ?></option>
                                                    <?php foreach ($provinsi as $p): ?>
                                                        <option value="<?php echo $p->id_prov ?>"><?php echo html_escape($p->nama) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Kabupaten</label>
                                                <select class="form-select" name="kabupaten" id="guru_kabupaten">
                                                    <option value="<?php echo html_escape($row->kabupaten) ?>"><?php echo html_escape($row->kabupaten ?: 'Pilih Kabupaten') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Kecamatan</label>
                                                <select class="form-select" name="kecamatan" id="guru_kecamatan">
                                                    <option value="<?php echo html_escape($row->kecamatan) ?>"><?php echo html_escape($row->kecamatan ?: 'Pilih Kecamatan') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Kelurahan/Desa</label>
                                                <select class="form-select" name="kelurahan_desa" id="guru_kelurahan">
                                                    <option value="<?php echo html_escape($row->kelurahan_desa) ?>"><?php echo html_escape($row->kelurahan_desa ?: 'Pilih Kelurahan') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-20">
                                                <label class="form-label fw-semibold text-sm">Password Baru</label>
                                                <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diganti">
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary-600 px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
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
            $('#guru_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $('#guru_kelurahan').html('<option value="">Pilih Kelurahan</option>');
        }, 'json');
    });

    $('#guru_kabupaten').on('change', function() {
        $.post('<?php echo url('guru/getKecamatan') ?>', { id: $(this).val() }, function(data) {
            $('#guru_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            $.each(data, function(_, value) {
                $('#guru_kecamatan').append('<option value="' + value.id_kec + '">' + value.nama + '</option>');
            });
            $('#guru_kelurahan').html('<option value="">Pilih Kelurahan</option>');
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
