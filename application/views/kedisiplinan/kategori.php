<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info-600 text-white">
                    <h6 class="text-light mb-0">Daftar Kategori Pelanggaran Sekolah</h6>
                </div>
                <div class="card-body">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Pelanggaran</th>
                                <th class="text-center">Bobot Poin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($kategori as $k): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td class="fw-semibold text-secondary-light"><?php echo html_escape($k->nama_pelanggaran) ?></td>
                                    <td class="text-center"><span class="badge bg-danger-600 text-light"><?php echo $k->bobot_poin ?> Poin</span></td>
                                    <td class="text-center">
                                        <a href="<?php echo url('kedisiplinan/kategori_hapus/'.$k->id_kategori) ?>" class="btn btn-sm btn-danger text-light" onclick="return confirm('Hapus kategori ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success-600 text-white">
                    <h6 class="text-light mb-0">Tambah Kategori Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('kedisiplinan/kategori_simpan') ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pelanggaran</label>
                            <input type="text" name="nama_pelanggaran" class="form-control" required placeholder="Contoh: Merokok di lingkungan sekolah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bobot Poin Pelanggaran</label>
                            <input type="number" name="bobot_poin" class="form-control" required min="1" max="100" placeholder="Contoh: 15">
                        </div>
                        <button type="submit" class="btn btn-success text-light w-100 radius-8">Simpan Kategori</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
