<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary-600 text-white d-flex justify-content-between">
                    <h6 class="text-light mb-0">Daftar Ekstrakurikuler Sekolah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Ekskul</th>
                                    <th>Pembina (PTK)</th>
                                    <th>Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($ekskul as $e): ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td class="fw-semibold text-primary-light"><?php echo html_escape($e->nama_ekskul) ?></td>
                                        <td><span class="text-secondary-light fw-medium"><?php echo html_escape($e->nama_pembina ?: 'Belum ditentukan') ?></span></td>
                                        <td><?php echo html_escape($e->keterangan ?: '-') ?></td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                                <a href="<?php echo url('ekstrakurikuler/detail/'.$e->id_ekskul) ?>" class="btn btn-sm btn-info-100 text-info-600">
                                                    Kelola Nilai & Peserta
                                                </a>
                                                <?php if($is_admin): ?>
                                                    <a href="<?php echo url('ekstrakurikuler/hapus/'.$e->id_ekskul) ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="return confirm('Hapus program ekskul ini?')">
                                                        <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if($is_admin): ?>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success-600 text-white">
                        <h6 class="text-light mb-0">Tambah Kegiatan Ekskul</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo url('ekstrakurikuler/simpan') ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                                <input type="text" name="nama_ekskul" class="form-control" required placeholder="Contoh: PMR, Pramuka, Futsal">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Guru Pembina (PTK)</label>
                                <select name="id_ptk_pembina" class="form-control select2" data-placeholder="Pilih guru pembina...">
                                    <option value="">Pilih guru pembina...</option>
                                    <?php foreach($ptk_list as $ptk): ?>
                                        <option value="<?php echo $ptk->id_ptk ?>"><?php echo html_escape($ptk->nama_ptk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keterangan / Deskripsi Singkat</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan tujuan ekskul..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success text-light w-100 radius-8">Simpan Kegiatan</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
