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
                                    <?php $foto_path = is_file(FCPATH . 'uploads/alumni_foto/' . $f->foto) ? 'uploads/alumni_foto/' . $f->foto : 'uploads/siswa_foto/' . $f->foto; ?>
                                    <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                        <img src="<?php echo url($foto_path) ?>" alt="" class="w-100 h-100 object-fit-cover radius-20">
                                        <div class="position-absolute start-50 translate-middle-x bottom-0 pb-10 z-1 text-center w-100 radius-20">
                                            <p class="card-text text-white mx-auto text-sm"><?php echo $f->label ?: 'Foto Alumni' ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="gradient-overlay bottom-0 start-0 h-100 radius-20">
                                    <img src="<?php echo $url->assets ?>images/user-grid/siswa.jpg" alt="" class="w-100 h-100 object-fit-cover radius-20">
                                </div>
                            <?php endif; ?>
                        </div>
                        <h6 class="mb-0 mt-16"><?php echo html_escape($row->nama_siswa) ?></h6>
                        <span class="text-secondary-light mb-16"><?php echo html_escape(($row->nisn ?: '-') . ' / ' . ($row->nipd ?: '-')) ?></span><br>
                        <span class="badge text-sm fw-semibold bg-warning-100 text-warning-700 px-20 py-9 radius-4 mb-20"><?php echo html_escape($row->status_alumni ?: $row->status_keaktifan ?: '-') ?></span>
                    </div>
                </div>
                <div class="ms-24 mb-24 me-24">
                    <h6 class="text-xl mb-16">Ringkasan Alumni</h6>
                    <ul>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Nama</span><span class="w-60">: <?php echo html_escape($row->nama_siswa) ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Sekolah</span><span class="w-60">: <?php echo html_escape((isset($row->sekolah_terakhir) && $row->sekolah_terakhir) ? $row->sekolah_terakhir : '-') ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Rombel</span><span class="w-60">: <?php echo html_escape((isset($row->rombel_terakhir) && $row->rombel_terakhir) ? $row->rombel_terakhir : ($row->rombel ?: '-')) ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Status</span><span class="w-60">: <?php echo html_escape($row->status_alumni ?: '-') ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">Tanggal</span><span class="w-60">: <?php echo !empty($row->tanggal_alumni) ? tanggal_indo($row->tanggal_alumni) : '-' ?></span></li>
                        <li class="d-flex gap-1 mb-12"><span class="w-40 fw-semibold">ID Siswa Asal</span><span class="w-60">: <?php echo html_escape($row->id_siswa_asal ?: '-') ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <ul class="nav border-gradient-tab nav-pills d-inline-flex" role="tablist">
                            <li class="nav-item"><button class="nav-link px-24 active" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button">Profil</button></li>
                            <li class="nav-item"><button class="nav-link px-24" data-bs-toggle="pill" data-bs-target="#pills-arsip" type="button">Arsip</button></li>
                            <li class="nav-item"><button class="nav-link px-24" data-bs-toggle="pill" data-bs-target="#pills-nilai" type="button">Nilai</button></li>
                        </ul>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (empty($row->id_siswa_kembali)): ?>
                                <button type="button" class="btn btn-sm btn-success-100 text-success-700" data-bs-toggle="modal" data-bs-target="#modalKembalikanAlumni">
                                    <i class="ri-user-follow-line"></i> Kembalikan Jadi Siswa
                                </button>
                            <?php else: ?>
                                <a href="<?php echo url('siswa/detail/' . $row->id_siswa_kembali) ?>" class="btn btn-sm btn-success-100 text-success-700">
                                    <i class="ri-user-line"></i> Lihat Siswa Aktif
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo url('alumni') ?>" class="btn btn-sm btn-neutral-100 text-neutral-700"><i class="ri-arrow-left-line"></i> Kembali</a>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pills-profile">
                            <div class="row">
                                <?php
                                $items = [
                                    'Jenis Kelamin' => $row->jenis_kelamin,
                                    'Agama' => $row->agama,
                                    'No KK' => $row->no_kk,
                                    'No Ijazah' => isset($row->no_ijazah) ? $row->no_ijazah : null,
                                    'Tanggal Pendaftaran' => $row->tanggal_pendaftaran,
                                    'Status Pendaftaran' => $row->status_pendaftaran,
                                    'Sekolah Asal' => isset($row->sekolah_asal) ? $row->sekolah_asal : null,
                                    'Sekolah Terakhir' => isset($row->sekolah_terakhir) ? $row->sekolah_terakhir : null,
                                    'Rombel Terakhir' => isset($row->rombel_terakhir) ? $row->rombel_terakhir : null,
                                    'Tempat Lahir' => $row->tempat_lahir,
                                    'Tanggal Lahir' => $row->tanggal_lahir,
                                    'Nama Ayah' => $row->nama_ayah,
                                    'Nama Ibu' => $row->nama_ibu,
                                ];
                                foreach ($items as $label => $value): ?>
                                    <div class="col-md-6">
                                        <div class="form-switch switch-primary py-12 px-16 border radius-8 mb-16">
                                            <span class="fw-medium text-secondary-light fst-italic"><?php echo $label ?> :</span><br>
                                            <span class="fw-semibold text-primary-light"><?php echo html_escape($value ?: '-') ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="col-md-12">
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 mb-16">
                                        <span class="fw-medium text-secondary-light fst-italic">Alamat :</span><br>
                                        <span class="fw-semibold text-primary-light"><?php echo html_escape(($row->alamat ?: '') . ' RT ' . ($row->rt ?: '0') . ' RW ' . ($row->rw ?: '0') . ' Desa ' . ($row->id_kelurahan ?: '-') . ' Kec. ' . ($row->id_kecamatan ?: '-') . ' Kab. ' . ($row->id_kabupaten ?: '-') . ' Prov. ' . ($row->id_provinsi ?: '-')) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-arsip">
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <h6 class="text-lg mb-0">Arsip Dokumen Alumni</h6>
                                <button class="btn btn-sm btn-success-100 text-success" data-bs-toggle="modal" data-bs-target="#modalTambahDokumenAlumni"><i class="ri-add-line"></i> Tambah</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table bordered-table" id="dokumenTable">
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
                                        <?php $no = 1; foreach ($dokumen as $d): ?>
                                            <tr>
                                                <td><?php echo $no++ ?></td>
                                                <td><?php echo html_escape($d->nama_jenis_dokumen ?: '-') ?></td>
                                                <td><?php echo html_escape($d->nomor_dokumen ?: '-') ?></td>
                                                <td><?php echo html_escape($d->tanggal_dokumen ?: '-') ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($d->berkas)): ?>
                                                        <?php $berkas_path = is_file(FCPATH . 'uploads/alumni_dokumen/' . $d->berkas) ? 'uploads/alumni_dokumen/' . $d->berkas : 'uploads/siswa_dokumen/' . $d->berkas; ?>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <a href="<?php echo url($berkas_path) ?>" target="_blank" class="btn btn-sm btn-info-100 text-info-600">Lihat</a>
                                                            <button class="btn btn-sm btn-success-100 text-success-600 btn-edit-dokumen-alumni" data-bs-toggle="modal" data-bs-target="#modalEditDokumenAlumni" data-action="<?php echo url('alumni/dokumenUpdate/' . $d->id_dokumen_alumni) ?>" data-jenis="<?php echo $d->id_jenis_dokumen ?>" data-nomor="<?php echo htmlspecialchars($d->nomor_dokumen, ENT_QUOTES, 'UTF-8') ?>" data-tanggal="<?php echo $d->tanggal_dokumen ?>" data-keterangan="<?php echo htmlspecialchars($d->keterangan, ENT_QUOTES, 'UTF-8') ?>">Edit</button>
                                                        </div>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-success-100 text-success-600 btn-edit-dokumen-alumni" data-bs-toggle="modal" data-bs-target="#modalEditDokumenAlumni" data-action="<?php echo url('alumni/dokumenUpdate/' . $d->id_dokumen_alumni) ?>" data-jenis="<?php echo $d->id_jenis_dokumen ?>" data-nomor="<?php echo htmlspecialchars($d->nomor_dokumen, ENT_QUOTES, 'UTF-8') ?>" data-tanggal="<?php echo $d->tanggal_dokumen ?>" data-keterangan="<?php echo htmlspecialchars($d->keterangan, ENT_QUOTES, 'UTF-8') ?>">Edit</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-nilai">
                            <div class="table-responsive">
                                <table class="table bordered-table" id="nilaiTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Tahun/Semester</th>
                                            <th class="text-center">Harian</th>
                                            <th class="text-center">PSTS</th>
                                            <th class="text-center">PSAS</th>
                                            <th class="text-center">Rapor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($nilai as $n): ?>
                                            <tr>
                                                <td><?php echo $no++ ?></td>
                                                <td><?php echo html_escape($n->nama_mapel ?: '-') ?></td>
                                                <td><?php echo html_escape(trim(($n->tahun_pelajaran ?: '-') . ' ' . ($n->semester ?: ''))) ?></td>
                                                <td class="text-center"><?php echo html_escape($n->nilai_harian ?: '-') ?></td>
                                                <td class="text-center"><?php echo html_escape($n->nilai_psts ?: '-') ?></td>
                                                <td class="text-center"><?php echo html_escape($n->nilai_psas ?: '-') ?></td>
                                                <td class="text-center"><?php echo html_escape($n->nilai_rapor ?: '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('alumni/partials/dokumen_modals'); ?>

<?php if (empty($row->id_siswa_kembali)): ?>
    <div class="modal fade" id="modalKembalikanAlumni" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24">
                    <h1 class="modal-title fs-5">Kembalikan Alumni Menjadi Siswa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo url('alumni/kembalikan/' . $row->id_alumni) ?>" method="post">
                    <div class="modal-body p-24">
                        <div class="alert alert-warning bg-warning-100 text-warning-700 border-warning-100 radius-8">
                            Data alumni tetap disimpan sebagai histori mutasi/lulus. Sistem akan membuat data siswa aktif baru dari profil alumni ini.
                        </div>
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Masuk Kembali</label>
                                <input type="date" class="form-control radius-8" name="tanggal_kembali" value="<?php echo date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Pendaftaran</label>
                                <select class="form-control form-select radius-8" name="status_pendaftaran" required>
                                    <option value="Kembali">Kembali</option>
                                    <option value="Mutasi Masuk">Mutasi Masuk</option>
                                    <option value="Siswa baru">Siswa baru</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIPD Baru</label>
                                <input type="text" class="form-control radius-8" name="nipd" value="<?php echo html_escape($row->nipd) ?>" placeholder="Isi NIPD baru jika berubah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-24 py-16">
                        <button type="button" class="btn btn-secondary-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success-600" onclick="return confirm('Kembalikan alumni ini menjadi siswa aktif?')">Kembalikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include viewPath('includes/footer'); ?>
<script>
    $('.arrow-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>'
    });
    new DataTable('#dokumenTable');
    new DataTable('#nilaiTable');
    $('.btn-edit-dokumen-alumni').on('click', function() {
        $('#formEditDokumenAlumni').attr('action', $(this).data('action'));
        $('#edit_id_jenis_dokumen_alumni').val($(this).data('jenis'));
        $('#edit_nomor_dokumen_alumni').val($(this).data('nomor'));
        $('#edit_tanggal_dokumen_alumni').val($(this).data('tanggal'));
        $('#edit_keterangan_dokumen_alumni').val($(this).data('keterangan'));
    });
    $('.btn-tambah-jenis-dokumen-alumni').on('click', function() {
        const wrapper = $(this).closest('.row');
        const input = wrapper.find('.input-jenis-dokumen-baru');
        const nama = input.val().trim();
        if (!nama) return alert('Nama jenis dokumen wajib diisi');
        $.post('<?php echo url('alumni/jenisDokumenSimpan') ?>', {
            nama_jenis_dokumen: nama
        }, function(response) {
            if (!response.status) return alert(response.message);
            $('.select-jenis-dokumen-alumni').each(function() {
                if ($(this).find('option[value="' + response.id + '"]').length === 0) $(this).append(new Option(response.nama, response.id));
            });
            wrapper.find('.select-jenis-dokumen-alumni').val(response.id);
            input.val('');
        }, 'json');
    });
</script>
