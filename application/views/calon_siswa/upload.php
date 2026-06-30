<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header py-16 px-24 bg-info-600">
                    <h6 class="text-light mb-0">Calon Siswa</h6>
                </div>
                <div class="card-body">
                    <?php $badge = $row->status_daftar_ulang === 'Terverifikasi' ? 'bg-success-focus text-success-main' : ($row->status_daftar_ulang === 'Perbaikan' ? 'bg-warning-focus text-warning-main' : 'bg-info-focus text-info-main'); ?>
                    <h6 class="mb-8"><?php echo htmlspecialchars($row->nama_siswa, ENT_QUOTES, 'UTF-8') ?></h6>
                    <span class="badge <?php echo $badge ?> px-16 py-6 radius-4 mb-20"><?php echo $row->status_daftar_ulang ?></span>
                    <div class="mt-20">
                        <div class="mb-12"><span class="text-secondary-light text-sm">NISN/NIK</span><br><span class="fw-semibold"><?php echo ($row->nisn ?: '-') . ' / ' . ($row->nik ?: '-') ?></span></div>
                        <div class="mb-12"><span class="text-secondary-light text-sm">Tempat, Tanggal Lahir</span><br><span class="fw-semibold"><?php echo ($row->tempat_lahir ?: '-') . ', ' . ($row->tanggal_lahir ?: '-') ?></span></div>
                        <div class="mb-12"><span class="text-secondary-light text-sm">Sekolah Asal</span><br><span class="fw-semibold"><?php echo $row->sekolah_asal ?: '-' ?></span></div>
                        <div class="mb-12"><span class="text-secondary-light text-sm">Ayah</span><br><span class="fw-semibold"><?php echo ($row->nama_ayah ?: '-') . ' / ' . ($row->pekerjaan_ayah ?: '-') ?></span></div>
                        <div class="mb-12"><span class="text-secondary-light text-sm">Ibu</span><br><span class="fw-semibold"><?php echo ($row->nama_ibu ?: '-') . ' / ' . ($row->pekerjaan_ibu ?: '-') ?></span></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-20">
                        <a href="<?php echo url('calon_siswa') ?>" class="btn btn-outline-secondary radius-8">Kembali</a>
                        <a href="<?php echo url('calon_siswa/edit/' . $row->id_calon_siswa) ?>" class="btn btn-success-600 text-light radius-8">Edit Data</a>
                        <a href="<?php echo url('calon_siswa/validasi') ?>" class="btn btn-primary-600 radius-8">Validasi</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header py-16 px-24 bg-base d-flex flex-wrap justify-content-between gap-3">
                    <h6 class="text-lg mb-0">Upload Berkas Daftar Ulang</h6>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <?php foreach ($required_berkas as $jenis): ?>
                            <?php $item = isset($berkas[$jenis]) ? $berkas[$jenis] : null; ?>
                            <div class="col-md-12">
                                <div class="border radius-8 p-16">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-12">
                                        <div>
                                            <h6 class="text-md mb-0"><?php echo $jenis ?></h6>
                                            <span class="text-sm <?php echo $item ? 'text-success' : 'text-danger' ?>"><?php echo $item ? 'Sudah diupload' : 'Belum diupload' ?></span>
                                        </div>
                                        <?php if ($item): ?>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-info-100 text-info-600 btn-lihat-berkas" data-bs-toggle="modal" data-bs-target="#modalLihatBerkas" data-file="<?php echo url('uploads/calon_siswa_berkas/' . $item->berkas) ?>"><iconify-icon icon="lucide:eye"></iconify-icon></button>
                                                <a href="<?php echo url('calon_siswa/berkasHapus/' . $item->id_berkas) ?>" class="btn btn-danger-100 text-danger-600" onclick="return confirm('Hapus berkas ini?')"><iconify-icon icon="lucide:trash-2"></iconify-icon></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!$row->id_siswa): ?>
                                        <form action="<?php echo url('calon_siswa/berkasSimpan/' . $row->id_calon_siswa) ?>" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="jenis_berkas" value="<?php echo htmlspecialchars($jenis, ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="row">
                                                <div class="col-md-7 mb-12"><input type="file" class="form-control radius-8" name="berkas" accept=".pdf,.jpg,.jpeg,.png" required></div>
                                                <div class="col-md-3 mb-12"><input type="text" class="form-control radius-8" name="keterangan" placeholder="Keterangan"></div>
                                                <div class="col-md-2 mb-12"><button type="submit" class="btn btn-success-600 text-light radius-8 w-100"><?php echo $item ? 'Ganti' : 'Upload' ?></button></div>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($row->id_siswa): ?>
                        <div class="alert alert-success bg-success-100 text-success-600 border-success-100 mt-20">Calon siswa ini sudah dipindahkan menjadi siswa.</div>
                        <a href="<?php echo url('siswa/detail/' . $row->id_siswa) ?>" class="btn btn-primary-600 radius-8">Lihat Data Siswa</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLihatBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Lihat Berkas</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-24"><object style="width:100%;height:100%;" id="berkas_content"><iframe id="berkas_frame"></iframe></object></div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    $('.btn-lihat-berkas').on('click', function() {
        $('#berkas_content').attr('data', $(this).data('file'));
        $('#berkas_frame').attr('src', $(this).data('file'));
    });
</script>
