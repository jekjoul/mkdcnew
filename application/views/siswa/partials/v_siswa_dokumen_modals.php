<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
function siswa_dokumen_form($mode, $row, $jenis_dokumen)
{
    $is_edit = $mode === 'edit';
?>
    <form action="<?php echo $is_edit ? '#' : url('siswa/dokumenSimpan/' . $row->id_siswa) ?>" method="post" enctype="multipart/form-data" id="<?php echo $is_edit ? 'formEditDokumen' : 'formTambahDokumen' ?>">
        <div class="row">
            <div class="col-md-7 mb-20">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Dokumen <span class="text-danger-600">*</span></label>
                <select class="form-control radius-8 form-select select-jenis-dokumen" name="id_jenis_dokumen" id="<?php echo $is_edit ? 'edit_id_jenis_dokumen' : 'add_id_jenis_dokumen' ?>" required>
                    <option value="">Pilih Jenis Dokumen</option>
                    <?php foreach ($jenis_dokumen as $jenis): ?>
                        <option value="<?php echo $jenis->id_jenis_dokumen ?>"><?php echo $jenis->nama_jenis_dokumen ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5 mb-20">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tambah Jenis</label>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control radius-8 input-jenis-dokumen-baru" placeholder="Contoh: KIP">
                    <button type="button" class="btn btn-info text-light radius-8 btn-tambah-jenis-dokumen">Tambah</button>
                </div>
            </div>
            <div class="col-md-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Dokumen</label><input type="text" class="form-control radius-8" name="nomor_dokumen" id="<?php echo $is_edit ? 'edit_nomor_dokumen' : 'add_nomor_dokumen' ?>"></div>
            <div class="col-md-6 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Dokumen</label><input type="date" class="form-control radius-8" name="tanggal_dokumen" id="<?php echo $is_edit ? 'edit_tanggal_dokumen' : 'add_tanggal_dokumen' ?>"></div>
            <div class="col-md-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8"><?php echo $is_edit ? 'Ganti Berkas' : 'Upload Berkas' ?> <?php echo $is_edit ? '' : '<span class="text-danger-600">*</span>' ?></label><div class="d-flex align-items-center gap-2"><input type="file" class="form-control radius-8 scan-enabled" name="berkas" accept=".pdf,.jpg,.jpeg,.png" <?php echo $is_edit ? '' : 'required' ?>></div><small class="text-secondary-light"><?php echo $is_edit ? 'Kosongkan jika tidak diganti. ' : '' ?>PDF/JPG/PNG maksimal 5 MB.</small></div>
            <div class="col-md-12 mb-20"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label><textarea class="form-control radius-8" name="keterangan" id="<?php echo $is_edit ? 'edit_keterangan_dokumen' : 'add_keterangan_dokumen' ?>" rows="3"></textarea></div>
        </div>
        <div class="modal-footer py-16 px-24"><button type="button" class="btn btn-outline-secondary radius-8" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success text-light radius-8">Simpan</button></div>
    </form>
<?php } ?>

<div class="modal fade" id="modalTambahDokumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Tambah Dokumen Siswa</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-24"><?php siswa_dokumen_form('add', $row, $jenis_dokumen); ?></div></div></div>
</div>

<div class="modal fade" id="modalEditDokumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Sunting Dokumen Siswa</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-24"><?php siswa_dokumen_form('edit', $row, $jenis_dokumen); ?></div></div></div>
</div>

<div class="modal fade" id="detailIjazah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24"><h1 class="modal-title fs-5">Lihat Berkas</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-24"><object style="width:100%;height:100%;" id="pdf_content"><iframe id="pdf_frame"></iframe></object></div>
        </div>
    </div>
</div>
