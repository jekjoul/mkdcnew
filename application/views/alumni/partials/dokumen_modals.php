<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
function alumni_dokumen_form($mode, $row, $jenis_dokumen)
{
    $is_edit = $mode === 'edit';
?>
    <form action="<?php echo $is_edit ? '#' : url('alumni/dokumenSimpan/' . $row->id_alumni) ?>" method="post" enctype="multipart/form-data" id="<?php echo $is_edit ? 'formEditDokumenAlumni' : 'formTambahDokumenAlumni' ?>">
        <div class="row">
            <div class="col-md-7 mb-20">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Dokumen <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select select-jenis-dokumen-alumni" name="id_jenis_dokumen" id="<?php echo $is_edit ? 'edit_id_jenis_dokumen_alumni' : 'add_id_jenis_dokumen_alumni' ?>" required>
                    <option value="">Pilih Jenis Dokumen</option>
                    <?php foreach ($jenis_dokumen as $jenis): ?>
                        <option value="<?php echo $jenis->id_jenis_dokumen ?>"><?php echo $jenis->nama_jenis_dokumen ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5 mb-20">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tambah Jenis</label>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control radius-8 input-jenis-dokumen-baru" placeholder="Contoh: Ijazah">
                    <button type="button" class="btn btn-info text-light radius-8 btn-tambah-jenis-dokumen-alumni">Tambah</button>
                </div>
            </div>
            <div class="col-md-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen</label><input type="text" class="form-control radius-8" name="nomor_dokumen" id="<?php echo $is_edit ? 'edit_nomor_dokumen_alumni' : 'add_nomor_dokumen_alumni' ?>"></div>
            <div class="col-md-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Dokumen</label><input type="date" class="form-control radius-8" name="tanggal_dokumen" id="<?php echo $is_edit ? 'edit_tanggal_dokumen_alumni' : 'add_tanggal_dokumen_alumni' ?>"></div>
            <div class="col-md-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8"><?php echo $is_edit ? 'Ganti Berkas' : 'Upload Berkas' ?> <?php echo $is_edit ? '' : '<span class="text-danger-600">*</span>' ?></label><input type="file" class="form-control radius-8 scan-enabled" name="berkas" accept=".pdf,.jpg,.jpeg,.png" <?php echo $is_edit ? '' : 'required' ?>><small class="text-secondary-light"><?php echo $is_edit ? 'Kosongkan jika tidak diganti. ' : '' ?>PDF/JPG/PNG maksimal 5 MB.</small></div>
            <div class="col-md-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label><textarea class="form-control radius-8" name="keterangan" id="<?php echo $is_edit ? 'edit_keterangan_dokumen_alumni' : 'add_keterangan_dokumen_alumni' ?>" rows="3"></textarea></div>
        </div>
        <div class="modal-footer py-16 px-24"><button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success text-light radius-8">Simpan</button></div>
    </form>
<?php } ?>

<div class="modal fade" id="modalTambahDokumenAlumni" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Tambah Dokumen Alumni</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-24"><?php alumni_dokumen_form('add', $row, $jenis_dokumen); ?></div></div></div>
</div>

<div class="modal fade" id="modalEditDokumenAlumni" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Sunting Dokumen Alumni</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-24"><?php alumni_dokumen_form('edit', $row, $jenis_dokumen); ?></div></div></div>
</div>
