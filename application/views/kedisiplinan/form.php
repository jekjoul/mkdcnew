<!-- Load Select2 CDN untuk menjamin dropdown search berfungsi di semua peramban -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php include viewPath('includes/header'); ?>

<?php 
$userId = logged('id');
$is_admin_or_bk = (logged('role') == 1);
if ($this->db->table_exists('user_roles')) {
    $roles_res = $this->db->get_where('user_roles', ['user_id' => $userId])->result();
    foreach ($roles_res as $r) {
        $r_title = strtolower($this->db->get_where('roles', ['id' => $r->role_id])->row()->title ?? '');
        if ($r_title === 'admin' || $r_title === 'guru bk' || $r_title === 'bk') {
            $is_admin_or_bk = true;
        }
    }
}
?>

<div class="dashboard-main-body">
    <div class="row gy-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-danger-600 text-white">
                    <h6 class="text-light mb-0">Form Input Pelanggaran Siswa Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('kedisiplinan/simpan') ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Siswa Pelanggar <span class="text-danger">*</span></label>
                            <select name="id_siswa" class="form-control select2" required data-placeholder="Cari siswa berdasarkan nama...">
                                <option value=""></option>
                                <?php foreach($siswa as $s): ?>
                                    <option value="<?php echo $s->id_siswa ?>">
                                        <?php echo html_escape($s->nama_siswa) ?> - Rombel <?php echo html_escape($s->rombel ?: '-') ?> (NISN: <?php echo html_escape($s->nisn) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php if ($is_admin_or_bk): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Jenis / Kategori Pelanggaran <span class="text-danger">*</span></label>
                                <select name="id_kategori" class="form-control select2" required data-placeholder="Pilih jenis pelanggaran...">
                                    <option value=""></option>
                                    <?php foreach($kategori as $k): ?>
                                        <option value="<?php echo $k->id_kategori ?>">
                                            <?php echo html_escape($k->nama_pelanggaran) ?> (Bobot: <?php echo $k->bobot_poin ?> Poin)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <!-- Guru biasa tidak perlu memilih jenis pelanggaran, di-set ke 0 (BK yang akan menentukan nanti) -->
                            <input type="hidden" name="id_kategori" value="0">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pelanggaran</label>
                            <input type="date" name="tanggal_pelanggaran" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Kronologi / Detail Pelanggaran</label>
                            <textarea name="catatan" class="form-control" rows="3" required placeholder="Sebutkan detail kejadian secara kronologis..."></textarea>
                        </div>

                        <?php if ($is_admin_or_bk): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rencana Tindak Lanjut Awal</label>
                                <textarea name="tindak_lanjut" class="form-control" rows="2" placeholder="Contoh: Pemanggilan orang tua siswa / Konseling I..."></textarea>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="tindak_lanjut" value="Menunggu verifikasi dan konseling BK">
                        <?php endif; ?>

                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <a href="<?php echo url('kedisiplinan') ?>" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-danger text-light px-24">Simpan Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<!-- Load Select2 JS dan Inisialisasi Dropdown Search -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "Cari siswa berdasarkan nama...",
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
