<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <h6 class="text-dark"><?php echo !empty($is_nonaktif) ? 'Daftar Rombongan Belajar Nonaktif' : 'Daftar Rombongan Belajar Aktif'; ?></h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="<?php echo url(!empty($is_nonaktif) ? 'master/rombel' : 'master/rombelNonaktif') ?>" class="btn btn-sm btn-warning-600 text-light">
                            <i class="<?php echo !empty($is_nonaktif) ? 'ri-arrow-left-line' : 'ri-archive-line'; ?>"></i>
                            <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Nonaktif'; ?>
                        </a>
                        <?php if (empty($is_nonaktif)): ?>
                            <button type="button" class="btn btn-sm btn-info text-light" data-bs-toggle="modal" data-bs-target="#modalTambahRombel">
                                <i class="ri-add-line"></i> Tambah Rombel
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Rombel</th>
                                    <th scope="col">Wali Kelas</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($rombel as $r): ?>
                                    <?php
                                    $wk = '-';
                                    if (!empty($r->id_ptk_walikelas)) {
                                        $ptk_wk = $this->db->get_where('ptk', ['id_ptk' => $r->id_ptk_walikelas])->row();
                                        if ($ptk_wk) {
                                            $wk = $ptk_wk->nama_ptk;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td><?php echo $r->nama_rombel; ?></td>
                                        <td><span class="fw-semibold text-secondary-light"><?php echo html_escape($wk) ?></span></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $r->status == 'Aktif' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600'; ?>">
                                                <?php echo $r->status; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('master/rombelEdit/' . $r->id_rombel) ?>" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" title="Edit">
                                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                </a>
                                                <a href="<?php echo url('master/rombelDelete/' . $r->id_rombel) ?>" class="bg-danger-100 text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="return confirm('Hapus rombel ini?')" title="Hapus">
                                                    <iconify-icon icon="mingcute:delete-2-line" class="menu-icon"></iconify-icon>
                                                </a>
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
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahRombel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <form action="<?php echo url('master/rombelSimpan') ?>" method="post">
                <div class="modal-header py-16 px-24 border-bottom">
                    <h1 class="modal-title fs-5">Tambah Rombel</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8">Nama Rombel <span class="text-danger-600">*</span></label>
                        <input type="text" class="form-control radius-8" name="nama_rombel" required placeholder="Contoh: Al Farabi (tanpa tingkat)">
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8">Wali Kelas</label>
                        <select class="form-control radius-8 form-select select2" name="id_ptk_walikelas" data-placeholder="Belum ditentukan">
                            <option value="">Belum ditentukan</option>
                            <?php 
                            $ptk_list = $this->db->order_by('nama_ptk', 'ASC')->get_where('ptk', ['status_keaktifan' => 'Aktif'])->result();
                            foreach ($ptk_list as $ptk): 
                            ?>
                                <option value="<?php echo $ptk->id_ptk ?>"><?php echo html_escape($ptk->nama_ptk) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-8">Status</label>
                        <select class="form-control radius-8 form-select" name="status">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-16 px-24">
                    <button type="submit" class="btn btn-success text-light radius-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
