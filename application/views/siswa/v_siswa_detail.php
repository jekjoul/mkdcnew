<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                <img src="<?php echo $url->assets ?>images/user-grid/user-grid-bg-siswa.jpg" alt="" class="w-100 object-fit-cover">
                <div class="ms-60 mb-24 me-60 mt--75">
                    <div class="text-center border border-top-0 border-start-0 border-end-0 mb-20">
                        <div class="card-body p-0 arrow-carousel">
                            <?php if (!empty($foto)): ?>
                                <?php foreach ($foto as $f): ?>
                                    <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                        <img src="<?php echo url('uploads/siswa_foto/' . $f->foto) ?>" alt="" class="w-100 h-100 object-fit-cover radius-20">
                                        <div class="position-absolute start-50 translate-middle-x bottom-0 pb-10 z-1 text-center w-100 radius-20">
                                            <p class="card-text text-white mx-auto text-sm"><?php echo $f->label ?: 'Foto Siswa' ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                    <img src="<?php echo $url->assets ?>images/user-grid/siswa.jpg" alt="" class="w-100 h-100 object-fit-cover radius-20">
                                </div>
                            <?php endif; ?>
                        </div>
                        <h6 class="mb-0 mt-16"><?php echo $row->nama_siswa ?></h6>
                        <span class="text-secondary-light mb-16"><?php echo ($row->nisn ?: '-') . ' / ' . ($row->nipd ?: '-') ?></span><br>
                        <span class="badge text-sm fw-semibold bg-dark-info-gradient px-20 py-9 radius-4 text-white mb-20"><?php echo $row->rombel ?: '-' ?></span>
                    </div>
                </div>
                <div class="ms-24 mb-24 me-24">
                    <h6 class="text-xl mb-16">Data Pribadi</h6>
                    <ul>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Nama</span><span class="w-60">: <?php echo $row->nama_siswa ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">NISN</span><span class="w-60">: <?php echo $row->nisn ?: '-' ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">NIPD</span><span class="w-60">: <?php echo $row->nipd ?: '-' ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">NIK</span><span class="w-60">: <?php echo $row->nik ?: '-' ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">No HP</span><span class="w-60">: <?php echo $row->telepon ?: '-' ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Tempat Lahir</span><span class="w-60">: <?php echo ($row->tempat_lahir ?: '-') ?> </span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Tanggal lahir</span><span class="w-60">: <?php echo (tanggal_indo($row->tanggal_lahir) ?: '-') ?></span></li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                        <ul class="nav border-gradient-tab nav-pills d-inline-flex" role="tablist">
                            <li class="nav-item"><button class="nav-link px-24 active" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button">Profil</button></li>
                            <li class="nav-item"><button class="nav-link px-24" data-bs-toggle="pill" data-bs-target="#pills-arsip" type="button">Arsip</button></li>
                            <li class="nav-item"><button class="nav-link px-24" data-bs-toggle="pill" data-bs-target="#pills-setting" type="button">Setting</button></li>
                        </ul>
                        <button type="button" class="btn btn-warning-600 text-light radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalMutasiSiswa">
                            <iconify-icon icon="solar:logout-3-linear" class="text-xl"></iconify-icon>
                            Keluar / Mutasi
                        </button>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pills-profile">
                            <div class="card shadow">
                                <div class="card-header py-16 px-24 bg-base">
                                    <h6 class="text-lg mb-0">Profil Siswa</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php
                                        $items = [
                                            'Jenis Kelamin' => $row->jenis_kelamin,
                                            'Agama' => $row->agama,
                                            'No KK' => $row->no_kk,
                                            'No Ijazah' => isset($row->no_ijazah) ? $row->no_ijazah : null,
                                            'Kewarganegaraan' => isset($row->kewarganegaraan) ? $row->kewarganegaraan : null,
                                            'Anak Ke' => isset($row->anak_ke) ? $row->anak_ke : null,
                                            'Tanggal Pendaftaran' => $row->tanggal_pendaftaran,
                                            'Status Pendaftaran' => $row->status_pendaftaran,
                                            'Sekolah Asal' => isset($row->sekolah_asal) ? $row->sekolah_asal : null,
                                            'Status Keaktifan' => $row->status_keaktifan,
                                            'Jenis Tempat Tinggal' => isset($row->jenis_tempat_tinggal) ? $row->jenis_tempat_tinggal : null,
                                            'Alat Transportasi' => isset($row->alat_transportasi) ? $row->alat_transportasi : null,
                                            'Jarak ke Sekolah' => isset($row->jarak_ke_sekolah) ? $row->jarak_ke_sekolah : null,
                                            'Koordinat' => isset($row->koordinat) ? $row->koordinat : null,
                                        ];
                                        foreach ($items as $label => $value): ?>
                                            <div class="col-md-6">
                                                <div class="form-switch switch-primary py-12 px-16 border radius-8 mb-16">
                                                    <span class="fw-medium text-secondary-light fst-italic"><?php echo $label ?> :</span><br>
                                                    <span class="fw-semibold text-primary-light"><?php echo $value ?: '-' ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="col-md-12">
                                            <div class="form-switch switch-primary py-12 px-16 border radius-8 mb-16">
                                                <span class="fw-medium text-secondary-light fst-italic">Alamat :</span><br>
                                                <span class="fw-semibold text-primary-light"><?php echo ($row->alamat ?: '') . ' RT ' . ($row->rt ?: '0') . ' RW ' . ($row->rw ?: '0') . ' Desa ' . ($row->id_kelurahan ?: '-') . ' Kec. ' . ($row->id_kecamatan ?: '-') . ' Kab. ' . ($row->id_kabupaten ?: '-') . ' Prov. ' . ($row->id_provinsi ?: '-') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-20 gy-4">
                                        <div class="col-md-6">
                                            <div class="card border radius-12 shadow-none bg-base">
                                                <div class="card-header py-12 px-24 border-bottom">
                                                    <h6 class="text-md mb-0">Data Ayah</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-12"><span class="text-secondary-light text-sm">Nama Lengkap :</span><br><span class="fw-semibold"><?php echo $row->nama_ayah ?: '-' ?></span></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-12"><span class="text-secondary-light text-sm">NIK :</span><br><span class="fw-semibold"><?php echo $row->nik_ayah ?: '-' ?></span></div>
                                                        <div class="col-6 mb-12"><span class="text-secondary-light text-sm">Thn Lahir :</span><br><span class="fw-semibold"><?php echo $row->tahun_lahir_ayah ?: '-' ?></span></div>
                                                    </div>
                                                    <div class="mb-12"><span class="text-secondary-light text-sm">Pekerjaan :</span><br><span class="fw-semibold"><?php echo $row->pekerjaan_ayah ?: '-' ?></span></div>
                                                    <div class="mb-0"><span class="text-secondary-light text-sm">Penghasilan :</span><br><span class="fw-semibold"><?php echo $row->penghasilan_ayah ?: '-' ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border radius-12 shadow-none bg-base">
                                                <div class="card-header py-12 px-24 border-bottom">
                                                    <h6 class="text-md mb-0">Data Ibu</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-12"><span class="text-secondary-light text-sm">Nama Lengkap :</span><br><span class="fw-semibold"><?php echo $row->nama_ibu ?: '-' ?></span></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-12"><span class="text-secondary-light text-sm">NIK :</span><br><span class="fw-semibold"><?php echo $row->nik_ibu ?: '-' ?></span></div>
                                                        <div class="col-6 mb-12"><span class="text-secondary-light text-sm">Thn Lahir :</span><br><span class="fw-semibold"><?php echo $row->tahun_lahir_ibu ?: '-' ?></span></div>
                                                    </div>
                                                    <div class="mb-12"><span class="text-secondary-light text-sm">Pekerjaan :</span><br><span class="fw-semibold"><?php echo $row->pekerjaan_ibu ?: '-' ?></span></div>
                                                    <div class="mb-0"><span class="text-secondary-light text-sm">Penghasilan :</span><br><span class="fw-semibold"><?php echo $row->penghasilan_ibu ?: '-' ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow mt-20">
                                <div class="card-header py-16 px-24 bg-base">
                                    <h6 class="text-lg mb-0">Foto Siswa</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <?php foreach ($foto as $f): ?>
                                            <div class="col-md-4">
                                                <img src="<?php echo url('uploads/siswa_foto/' . $f->foto) ?>" class="w-100 radius-8 object-fit-cover" style="height:180px">
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <span><?php echo $f->label ?: 'Foto' ?></span>
                                                    <a href="<?php echo url('siswa/fotoHapus/' . $f->id_foto) ?>" onclick="return confirm('Hapus foto ini?')" class="text-danger">Hapus</a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-arsip">
                            <div class="card basic-data-table shadow">
                                <div class="card-header py-16 px-24 bg-base d-flex justify-content-between">
                                    <h6 class="text-lg mb-0">Data Pribadi</h6>
                                    <button class="btn btn-sm btn-success-100 text-success" data-bs-toggle="modal" data-bs-target="#modalTambahDokumen"><i class="ri-add-line"></i> Tambah</button>
                                </div>
                                <div class="card-body">
                                    <table class="table bordered-table" id="dataPribadi">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis Dokumen</th>
                                                <th>Nomor</th>
                                                <th>Tanggal</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($dokumen as $d): ?>
                                                <tr>
                                                    <td><?php echo $no++ ?></td>
                                                    <td><?php echo $d->nama_jenis_dokumen ?></td>
                                                    <td><?php echo $d->nomor_dokumen ?: '-' ?></td>
                                                    <td><?php echo $d->tanggal_dokumen ?: '-' ?></td>
                                                    <td>
                                                      
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button class="btn btn-info-100 text-info-600 btn-lihat-dokumen" data-bs-toggle="modal" data-bs-target="#detailIjazah" data-file="<?php echo url('uploads/siswa_dokumen/' . $d->berkas) ?>"><iconify-icon icon="lucide:eye"></iconify-icon></button>
                                                            <button class="btn btn-success-100 text-success-600 btn-edit-dokumen" data-bs-toggle="modal" data-bs-target="#modalEditDokumen" data-action="<?php echo url('siswa/dokumenUpdate/' . $d->id_dokumen) ?>" data-jenis="<?php echo $d->id_jenis_dokumen ?>" data-nomor="<?php echo htmlspecialchars($d->nomor_dokumen, ENT_QUOTES, 'UTF-8') ?>" data-tanggal="<?php echo $d->tanggal_dokumen ?>" data-keterangan="<?php echo htmlspecialchars($d->keterangan, ENT_QUOTES, 'UTF-8') ?>"><iconify-icon icon="lucide:edit"></iconify-icon></button>
                                                            <a class="btn btn-danger-100 text-danger-600" href="<?php echo url('siswa/dokumenHapus/' . $d->id_dokumen) ?>" onclick="return confirm('Hapus dokumen ini?')"><iconify-icon icon="lucide:trash-2"></iconify-icon></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-setting">
                            <?php include viewPath('siswa/partials/v_siswa_form_fields'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMutasiSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24">
                <h1 class="modal-title fs-5">Keluar / Mutasi Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo url('siswa/mutasi/' . $row->id_siswa) ?>" method="post">
                <div class="modal-body p-24">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Alumni</label>
                    <select class="form-control radius-8 form-select" name="status_alumni" required>
                        <option value="Keluar">Keluar</option>
                        <option value="Pindah">Pindah / Mutasi</option>
                        <option value="Lulus">Lulus</option>
                    </select>
                    <div class="alert alert-warning mt-16 mb-0">
                        Data siswa, foto, dokumen, nilai, dan riwayat pembelajaran akan dipindahkan ke data alumni.
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning-600 text-light radius-8" onclick="return confirm('Pindahkan siswa ini ke data alumni?')">Pindahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('siswa/partials/v_siswa_dokumen_modals'); ?>

<?php include viewPath('includes/footer'); ?>
<?php include viewPath('siswa/partials/v_siswa_form_script'); ?>
<script>
    let table = new DataTable('#dataPribadi');
    $('.arrow-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>'
    });
    $('.btn-lihat-dokumen').on('click', function() {
        $('#pdf_content').attr('data', $(this).data('file'));
        $('#pdf_frame').attr('src', $(this).data('file'));
    });
    $('.btn-edit-dokumen').on('click', function() {
        $('#formEditDokumen').attr('action', $(this).data('action'));
        $('#edit_id_jenis_dokumen').val($(this).data('jenis'));
        $('#edit_nomor_dokumen').val($(this).data('nomor'));
        $('#edit_tanggal_dokumen').val($(this).data('tanggal'));
        $('#edit_keterangan_dokumen').val($(this).data('keterangan'));
    });
    $('.btn-tambah-jenis-dokumen').on('click', function() {
        const wrapper = $(this).closest('.row');
        const input = wrapper.find('.input-jenis-dokumen-baru');
        const nama = input.val().trim();
        if (!nama) return alert('Nama jenis dokumen wajib diisi');
        $.post('<?php echo url('siswa/jenisDokumenSimpan') ?>', {
            nama_jenis_dokumen: nama
        }, function(response) {
            if (!response.status) return alert(response.message);
            $('.select-jenis-dokumen').each(function() {
                if ($(this).find('option[value="' + response.id + '"]').length === 0) $(this).append(new Option(response.nama, response.id));
            });
            wrapper.find('.select-jenis-dokumen').val(response.id);
            input.val('');
        }, 'json');
    });
</script>
