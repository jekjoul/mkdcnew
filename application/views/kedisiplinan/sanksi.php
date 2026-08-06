<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Nav Tab Pengaturan Kedisiplinan -->
    <ul class="nav nav-pills mb-24 border-bottom pb-12 gap-2" role="tablist">
        <li class="nav-item">
            <a class="nav-link radius-8 fw-semibold" href="<?php echo url('kedisiplinan/kategori'); ?>">
                <iconify-icon icon="solar:settings-linear" class="text-lg me-1"></iconify-icon> Kategori Pelanggaran (Poin)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active radius-8 fw-semibold" href="<?php echo url('kedisiplinan/sanksi'); ?>">
                <iconify-icon icon="solar:shield-warning-bold-duotone" class="text-lg me-1"></iconify-icon> Aturan Status Sanksi & Pembinaan Poin
            </a>
        </li>
    </ul>

    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning-600 text-white d-flex justify-content-between align-items-center">
                    <h6 class="text-light mb-0">Daftar Aturan Status Sanksi Berdasarkan Akumulasi Poin</h6>
                    <a href="<?php echo url('kedisiplinan'); ?>" class="btn btn-sm btn-light text-dark radius-8">
                        ← Kembali ke Data Kedisiplinan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th class="text-center" width="160">Rentang Akumulasi Poin</th>
                                    <th>Status Sanksi / Tindakan Pembinaan</th>
                                    <th class="text-center" width="160">Tampilan Badge</th>
                                    <th class="text-center" width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($sanksi as $s): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++ ?></td>
                                        <td class="text-center font-bold text-primary-600">
                                            <?php echo $s->min_poin ?> - <?php echo ($s->max_poin >= 999) ? '∞ (Seterusnya)' : $s->max_poin; ?> Poin
                                        </td>
                                        <td class="fw-semibold text-secondary-light">
                                            <?php echo html_escape($s->nama_sanksi) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo $s->warna_badge ?: 'warning'; ?>-100 text-<?php echo $s->warna_badge ?: 'warning'; ?>-800 px-10 py-6 text-xs">
                                                <?php echo html_escape($s->nama_sanksi) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo url('kedisiplinan/sanksi_hapus/'.$s->id_sanksi) ?>" class="btn btn-sm btn-danger text-light px-8 py-4 text-xs" onclick="return confirm('Hapus aturan sanksi ini?')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($sanksi)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-24">Belum ada aturan sanksi poin yang ditentukan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success-600 text-white">
                    <h6 class="text-light mb-0">Tambah Aturan Sanksi Poin</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('kedisiplinan/sanksi_simpan') ?>" method="post">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Min Poin <span class="text-danger">*</span></label>
                                <input type="number" name="min_poin" class="form-control" required min="1" placeholder="Contoh: 16">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Max Poin <span class="text-danger">*</span></label>
                                <input type="number" name="max_poin" class="form-control" required min="1" placeholder="Contoh: 30 (isi 999 untuk tak terbatas)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Sanksi / Keputusan Pembinaan <span class="text-danger">*</span></label>
                            <textarea name="nama_sanksi" class="form-control" rows="3" required placeholder="Contoh: Peringatan I & Pemanggilan Orang Tua ke Sekolah"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warna Badge Status</label>
                            <select name="warna_badge" class="form-select">
                                <option value="success">Hijau (Pembinaan Ringan / Normal)</option>
                                <option value="warning" selected>Kuning (Peringatan Sedang / Panggilan)</option>
                                <option value="danger">Merah (Sanksi Berat / Skorsing / DO)</option>
                                <option value="info">Biru (Informasi / Perhatian)</option>
                                <option value="neutral">Abu-abu (Netral)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success text-light w-100 radius-8">Simpan Aturan Sanksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
