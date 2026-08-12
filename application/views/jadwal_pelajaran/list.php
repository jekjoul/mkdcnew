<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row gy-4 mb-24">
        <!-- Card Kerangka Waktu -->
        <div class="col-md-5">
            <div class="card h-100 radius-12 border-0 shadow-xs">
                <div class="card-header bg-warning-900 py-16 px-20 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 text-light fw-bold">Kerangka Waktu KBM</h6>
                    <a href="<?php echo url('jadwal_pelajaran/waktu') ?>" class="btn btn-sm btn-outline-light radius-8 d-inline-flex align-items-center gap-1 text-xs">
                        <iconify-icon icon="lucide:settings"></iconify-icon> Atur Waktu
                    </a>
                </div>
                <div class="card-body p-20 d-flex flex-column justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-44-px h-44-px rounded-circle bg-warning-100 text-warning-700 d-flex align-items-center justify-content-center flex-shrink-0">
                            <iconify-icon icon="solar:clock-circle-bold" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <span class="text-secondary-light text-xs d-block">Durasi 1 Jam Pelajaran (JP)</span>
                            <h6 class="mb-0 fw-bold text-dark"><?php echo (int) $menit_jp ?> Menit</h6>
                        </div>
                    </div>
                    <div>
                        <span class="text-secondary-light text-xs d-block mb-8">Hari KBM Aktif</span>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($settings as $hari => $setting): ?>
                                <?php if (!empty($setting['aktif'])): ?>
                                    <span class="badge bg-neutral-100 text-secondary-light border px-8 py-4 text-xs">
                                        <?php echo $hari ?>: <?php echo $setting['jumlah_jp'] ?> JP
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Informatif Versi Jadwal -->
        <div class="col-md-7">
            <div class="card h-100 radius-12 border-0 shadow-xs">
                <div class="card-header bg-neutral-100 py-16 px-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <?php echo !empty($is_nonaktif) ? 'Arsip Jadwal Tahun Tidak Aktif' : 'Manajemen Versi Jadwal Pelajaran'; ?>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (empty($is_nonaktif)): ?>
                            <button type="button" class="btn btn-warning-600 text-white btn-sm radius-8 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahVersi">
                                <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon> Versi Jadwal Baru
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo url(!empty($is_nonaktif) ? 'jadwal_pelajaran' : 'jadwal_pelajaran/nonaktif') ?>" class="btn btn-sm btn-outline-secondary radius-8 d-inline-flex align-items-center gap-1 text-xs">
                            <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>"></iconify-icon>
                            <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                        </a>
                    </div>
                </div>
                <div class="card-body p-20">
                    <p class="text-secondary-light text-sm mb-0">
                        Sistem mendukung **Multi-Versi Jadwal**. Anda dapat merancang jadwal baru sebagai <strong>Draft</strong> dengan tanggal mulai efektif di masa mendatang. Ketika versi jadwal baru diaktifkan, tanggal akhir efektif jadwal lama akan terisi otomatis.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Versi Jadwal -->
    <div class="card radius-12 border-0 shadow-xs">
        <div class="card-header bg-white py-16 px-20 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark">Daftar Rancangan & Versi Jadwal Pelajaran</h6>
            <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-6 text-xs fw-semibold">
                Total: <?php echo count($headers) ?> Versi
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table hover-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-20" style="width: 50px;">NO</th>
                            <th>NAMA VERSI JADWAL</th>
                            <th>STATUS</th>
                            <th>RENTANG TANGGAL EFEKTIF</th>
                            <th>KETERATURAN</th>
                            <th>KETERANGAN</th>
                            <th class="text-end pe-20" style="width: 220px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($headers)): ?>
                            <?php $no = 1; foreach ($headers as $h): ?>
                                <tr>
                                    <td class="ps-20 fw-medium text-secondary-light"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1"><?php echo html_escape($h->nama_jadwal) ?></div>
                                        <div class="text-xs text-secondary-light">
                                            Dibuat: <?php echo date('d M Y H:i', strtotime($h->created_at ?: date('Y-m-d H:i:s'))) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($h->status === 'Aktif'): ?>
                                            <span class="badge bg-success-100 text-success-600 px-12 py-6 radius-6 text-xs fw-bold">
                                                <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon> AKTIF
                                            </span>
                                        <?php elseif ($h->status === 'Draft'): ?>
                                            <span class="badge bg-warning-100 text-warning-700 px-12 py-6 radius-6 text-xs fw-bold">
                                                <iconify-icon icon="solar:document-add-bold" class="align-middle me-1"></iconify-icon> DRAFT
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-neutral-200 text-secondary-light px-12 py-6 radius-6 text-xs fw-medium">
                                                NONAKTIF
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-xs fw-semibold text-dark mb-1">
                                            Mulai: <?php echo !empty($h->tanggal_mulai_efektif) ? date('d M Y', strtotime($h->tanggal_mulai_efektif)) : '-' ?>
                                        </div>
                                        <div class="text-xs text-secondary-light">
                                            Sampai: <?php echo !empty($h->tanggal_akhir_efektif) ? date('d M Y', strtotime($h->tanggal_akhir_efektif)) : ($h->status === 'Aktif' ? 'Seterusnya (Aktif)' : '-') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-50 text-info-700 px-8 py-4 text-xs">
                                            <?php echo (int)$h->total_kelas ?> Kelas (<?php echo (int)$h->total_slot ?> Slot)
                                        </span>
                                    </td>
                                    <td class="text-xs text-secondary-light">
                                        <?php echo html_escape($h->keterangan ?: '-') ?>
                                    </td>
                                    <td class="text-end pe-20">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="<?php echo url('jadwal_pelajaran/semua/' . $h->id_jadwal_header) ?>" class="btn btn-warning-600 text-white btn-sm px-10 py-6 radius-6 text-xs fw-semibold" title="Susun Slot Drag & Drop">
                                                <iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> Detail
                                            </a>
                                            <?php if ($h->status !== 'Aktif' && empty($is_nonaktif)): ?>
                                                <?php echo form_open(url('jadwal_pelajaran/aktifkan_versi/' . $h->id_jadwal_header), ['class' => 'd-inline']); ?>
                                                    <button type="submit" class="btn btn-success-600 text-white btn-sm px-10 py-6 radius-6 text-xs fw-semibold" title="Aktifkan Versi Jadwal Ini" onclick="return confirm('Aktifkan versi jadwal ini? Tanggal akhir efektif versi aktif saat ini akan terisi otomatis.')">
                                                        <iconify-icon icon="solar:check-read-bold" class="me-1"></iconify-icon> Aktifkan
                                                    </button>
                                                <?php echo form_close(); ?>
                                            <?php endif; ?>

                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm px-8 py-6 radius-6" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <iconify-icon icon="solar:menu-dots-bold"></iconify-icon>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 radius-8">
                                                    <li>
                                                        <?php echo form_open(url('jadwal_pelajaran/salin_versi/' . $h->id_jadwal_header), ['class' => 'd-inline']); ?>
                                                            <button type="submit" class="dropdown-item text-xs text-secondary-light d-flex align-items-center gap-2 py-8">
                                                                <iconify-icon icon="solar:copy-bold" class="text-sm"></iconify-icon> Salin ke Draft Baru
                                                            </button>
                                                        <?php echo form_close(); ?>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-xs text-secondary-light d-flex align-items-center gap-2 py-8" data-bs-toggle="modal" data-bs-target="#modalEditVersi<?php echo $h->id_jadwal_header ?>">
                                                            <iconify-icon icon="solar:pen-linear" class="text-sm"></iconify-icon> Edit Info Versi
                                                        </button>
                                                    </li>
                                                    <?php if ($h->status !== 'Aktif'): ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <?php echo form_open(url('jadwal_pelajaran/hapus_versi/' . $h->id_jadwal_header), ['class' => 'd-inline']); ?>
                                                                <button type="submit" class="dropdown-item text-xs text-danger d-flex align-items-center gap-2 py-8" onclick="return confirm('Yakin ingin menghapus versi jadwal ini?')">
                                                                    <iconify-icon icon="solar:trash-bin-trash-bold" class="text-sm"></iconify-icon> Hapus Versi
                                                                </button>
                                                            <?php echo form_close(); ?>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit Versi Header -->
                                <div class="modal fade" id="modalEditVersi<?php echo $h->id_jadwal_header ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content radius-12 border-0">
                                            <?php echo form_open(url('jadwal_pelajaran/edit_versi/' . $h->id_jadwal_header)); ?>
                                                <div class="modal-header bg-neutral-100 py-16 px-20">
                                                    <h6 class="modal-title fw-bold text-dark">Edit Informasi Versi Jadwal</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-20">
                                                    <div class="mb-16">
                                                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Nama Versi Jadwal</label>
                                                        <input type="text" name="nama_jadwal" class="form-control radius-8" value="<?php echo html_escape($h->nama_jadwal) ?>" required>
                                                    </div>
                                                    <div class="mb-16">
                                                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Tanggal Mulai Efektif</label>
                                                        <input type="date" name="tanggal_mulai_efektif" class="form-control radius-8" value="<?php echo $h->tanggal_mulai_efektif ?>" required>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Keterangan / Catatan Revisi</label>
                                                        <textarea name="keterangan" class="form-control radius-8" rows="3"><?php echo html_escape($h->keterangan) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-neutral-50 py-12 px-20">
                                                    <button type="button" class="btn btn-secondary radius-8 text-xs" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary-600 radius-8 text-xs fw-bold">Simpan Perubahan</button>
                                                </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-24 text-secondary-light text-sm">
                                    Belum ada versi jadwal pelajaran yang dibuat. Klik tombol "+ Versi Jadwal Baru" untuk membuat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Versi Baru -->
<div class="modal fade" id="modalTambahVersi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12 border-0">
            <?php echo form_open(url('jadwal_pelajaran/tambah_versi')); ?>
                <div class="modal-header bg-warning-600 text-white py-16 px-20">
                    <h6 class="modal-title text-white fw-bold">Tambah Versi Draft Jadwal Pelajaran Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-20">
                    <div class="alert alert-warning radius-8 p-12 text-xs mb-16">
                        <iconify-icon icon="solar:info-circle-bold" class="me-1 text-sm"></iconify-icon>
                        Versi baru yang dibuat akan berstatus <strong>Draft</strong>. Anda dapat mengaktifkannya kapan saja.
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Nama Versi Jadwal</label>
                        <input type="text" name="nama_jadwal" class="form-control radius-8" placeholder="Contoh: Jadwal Revisi Efektif 17 Agustus 2026" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Tanggal Mulai Efektif (Hari KBM Pertama)</label>
                        <input type="date" name="tanggal_mulai_efektif" class="form-control radius-8" value="<?php echo date('Y-m-d', strtotime('next monday')) ?>" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-secondary-light text-sm mb-8">Keterangan / Catatan Perubahan</label>
                        <textarea name="keterangan" class="form-control radius-8" rows="3" placeholder="Opsional: Alasan perubahan atau penyesuaian jadwal guru..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-neutral-50 py-12 px-20">
                    <button type="button" class="btn btn-secondary radius-8 text-xs" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning-600 text-white radius-8 text-xs fw-bold">Buat Versi Draft</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>