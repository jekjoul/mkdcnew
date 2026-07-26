<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">

    <!-- Nav Tabs Bootstrap 5 -->
    <ul class="nav nav-tabs mb-20 gap-2 border-0" id="disciplineTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active radius-8 px-20 py-10 fw-semibold text-sm d-flex align-items-center gap-2 border" id="laporan-tab" data-bs-toggle="tab" data-bs-target="#laporan-pane" type="button" role="tab" aria-controls="laporan-pane" aria-selected="true">
                <iconify-icon icon="solar:document-text-linear" class="text-lg"></iconify-icon> Laporan Pelanggaran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link radius-8 px-20 py-10 fw-semibold text-sm d-flex align-items-center gap-2 border" id="catatan-tab" data-bs-toggle="tab" data-bs-target="#catatan-pane" type="button" role="tab" aria-controls="catatan-pane" aria-selected="false">
                <iconify-icon icon="solar:shield-warning-linear" class="text-lg"></iconify-icon> Catatan / Rekap Kedisiplinan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="disciplineTabContent">
        
        <!-- Tab Pane 1: Laporan Pelanggaran -->
        <div class="tab-pane fade show active" id="laporan-pane" role="tabpanel" aria-labelledby="laporan-tab" tabindex="0">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-danger-600 text-white">
                    <h6 class="text-light mb-0">Daftar Pelanggaran & Poin Kedisiplinan Siswa</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (hasPermissions('master_list')): ?>
                            <a href="<?php echo url('kedisiplinan/kategori') ?>" class="btn btn-sm btn-dark text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:settings-linear" class="text-lg"></iconify-icon> Atur Kategori Poin
                            </a>
                        <?php endif; ?>
                        <?php if (hasPermissions('kedisiplinan_add')): ?>
                            <a href="<?php echo url('kedisiplinan/tambah') ?>" class="btn btn-sm btn-primary text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                                <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon> Laporkan Kenakalan Murid
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <style>
                    .accordion-button::after {
                        display: none !important;
                    }
                    .accordion-button {
                        padding-inline-end: 16px !important;
                    }
                    </style>

                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table bordered-table mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col">Pelanggaran</th>
                                    <th scope="col" class="text-center">Bobot Poin</th>
                                    <th scope="col" class="text-center">Tanggal</th>
                                    <th scope="col">Catatan Laporan</th>
                                    <th scope="col">Pelapor</th>
                                    <th scope="col">Tindak Lanjut BK</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($pelanggaran)): ?>
                                    <?php foreach ($pelanggaran as $p): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td class="fw-semibold text-secondary-light"><?php echo html_escape($p->nama_siswa); ?></td>
                                            <td><?php echo html_escape($p->rombel ?: '-'); ?></td>
                                            <td>
                                                <?php if (empty($p->nama_pelanggaran) || $p->id_kategori == 0): ?>
                                                    <span class="badge bg-warning-100 text-warning-800">Belum Diklasifikasi BK</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-100 text-danger-800"><?php echo html_escape($p->nama_pelanggaran); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (empty($p->nama_pelanggaran) || $p->id_kategori == 0): ?>
                                                    <span class="badge bg-neutral-200 text-neutral-800">- Poin</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-600 text-light px-12 py-6"><?php echo (int) $p->bobot_poin; ?> Poin</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo date('d-m-Y', strtotime($p->tanggal_pelanggaran)); ?></td>
                                            <td><?php echo html_escape($p->catatan ?: '-'); ?></td>
                                            <td>
                                                <span class="badge bg-info-100 text-info-800 px-10 py-6"><?php echo html_escape($p->pelapor ?: '-'); ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-warning-main"><?php echo html_escape($p->tindak_lanjut ?: '-'); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                                     <?php if (hasPermissions('kedisiplinan_bk')): ?>
                                                         <button class="btn btn-sm btn-info-100 text-info-600" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut<?php echo $p->id_pelanggaran_siswa ?>">
                                                             BK
                                                         </button>
                                                     <?php endif; ?>
                                                     <?php if (hasPermissions('kedisiplinan_delete')): ?>
                                                         <a href="<?php echo url('kedisiplinan/hapus/' . $p->id_pelanggaran_siswa); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" onclick="return confirm('Hapus laporan pelanggaran ini?')" title="Hapus">
                                                             <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                         </a>
                                                     <?php endif; ?>
                                                     <?php if (!hasPermissions('kedisiplinan_bk') && !hasPermissions('kedisiplinan_delete')): ?>
                                                         <span class="text-xs text-secondary-light">-</span>
                                                     <?php endif; ?>
                                                </div>

                                                <!-- Modal Tindak Lanjut BK -->
                                                <div class="modal fade" id="modalTindakLanjut<?php echo $p->id_pelanggaran_siswa ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content bg-base">
                                                            <form action="<?php echo url('kedisiplinan/edit_tindak_lanjut/' . $p->id_pelanggaran_siswa) ?>" method="post">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Tindak Lanjut / Konseling Guru BK</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <p>Siswa: <strong><?php echo html_escape($p->nama_siswa) ?></strong></p>
                                                                    
                                                                    <?php if (empty($p->nama_pelanggaran) || $p->id_kategori == 0): ?>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold text-danger">Tentukan Kategori Pelanggaran (Menentukan Poin) <span class="text-danger">*</span></label>
                                                                            <?php 
                                                                            $all_kategori = $this->db->order_by('nama_pelanggaran', 'ASC')->get('kedisiplinan_pelanggaran_kategori')->result();
                                                                            ?>
                                                                            <select name="id_kategori" class="form-select" required>
                                                                                <option value="">Pilih kategori pelanggaran...</option>
                                                                                <?php foreach ($all_kategori as $kat): ?>
                                                                                    <option value="<?php echo $kat->id_kategori ?>">
                                                                                        <?php echo html_escape($kat->nama_pelanggaran) ?> (<?php echo $kat->bobot_poin ?> Poin)
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <p>Pelanggaran: <span class="badge bg-danger-100 text-danger-800"><?php echo html_escape($p->nama_pelanggaran) ?></span></p>
                                                                        <input type="hidden" name="id_kategori" value="<?php echo $p->id_kategori ?>">
                                                                    <?php endif; ?>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Tindak Lanjut & Keputusan Konseling BK</label>
                                                                        <textarea name="tindak_lanjut" class="form-control" rows="4" required placeholder="Tuliskan hasil konseling siswa..."><?php echo html_escape($p->tindak_lanjut !== 'Menunggu verifikasi dan konseling BK' ? $p->tindak_lanjut : '') ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success text-light">Simpan Keputusan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Accordion View -->
                    <div class="d-block d-md-none">
                        <div class="mb-16">
                            <div class="position-relative">
                                <input type="text" id="mobilePelanggaranSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari nama siswa, rombel, atau pelanggaran...">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                    <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($pelanggaran)): ?>
                            <div class="accordion custom-accordion" id="accordionPelanggaranMobile">
                                <?php foreach ($pelanggaran as $idx => $p): ?>
                                    <?php
                                    $accordionId = "collapsePelanggaran" . $p->id_pelanggaran_siswa;
                                    $headingId   = "headingPelanggaran" . $p->id_pelanggaran_siswa;
                                    $searchableText = strtolower(html_escape($p->nama_siswa . ' ' . $p->rombel . ' ' . $p->nama_pelanggaran . ' ' . $p->catatan . ' ' . $p->pelapor));
                                    ?>
                                    <div class="accordion-item border radius-8 mb-12 mobile-pelanggaran-card" data-search="<?php echo $searchableText; ?>">
                                        <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                            <button class="accordion-button <?php echo ($idx === 0) ? '' : 'collapsed'; ?> px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="<?php echo ($idx === 0) ? 'true' : 'false'; ?>">
                                                <div class="d-flex flex-column gap-1 w-100 me-12">
                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <span class="text-primary-600 fw-bold"><?php echo html_escape($p->nama_siswa); ?></span>
                                                        <span class="badge bg-neutral-100 text-neutral-800 text-xs px-8 py-2 radius-4"><?php echo html_escape($p->rombel ?: '-'); ?></span>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                                        <?php if (empty($p->nama_pelanggaran) || $p->id_kategori == 0): ?>
                                                            <span class="badge bg-warning-100 text-warning-800 text-xs">Belum Diklasifikasi BK</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-100 text-danger-800 text-xs"><?php echo html_escape($p->nama_pelanggaran); ?></span>
                                                        <?php endif; ?>

                                                        <?php if (empty($p->nama_pelanggaran) || $p->id_kategori == 0): ?>
                                                            <span class="badge bg-neutral-200 text-neutral-800 text-xs">- Poin</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-600 text-light text-xs px-8 py-2"><?php echo (int) $p->bobot_poin; ?> Poin</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse <?php echo ($idx === 0) ? 'show' : ''; ?>" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionPelanggaranMobile">
                                            <div class="accordion-body bg-neutral-50 p-16 radius-b-8">
                                                <div class="d-flex flex-column gap-8 text-xs">
                                                    <div class="d-flex justify-content-between border-bottom pb-8">
                                                        <span class="text-secondary-light">Tanggal Pelanggaran:</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo date('d-m-Y', strtotime($p->tanggal_pelanggaran)); ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between border-bottom pb-8">
                                                        <span class="text-secondary-light">Pelapor:</span>
                                                        <span class="badge bg-info-100 text-info-800 px-8 py-2"><?php echo html_escape($p->pelapor ?: '-'); ?></span>
                                                    </div>
                                                    <div class="border-bottom pb-8">
                                                        <span class="text-secondary-light d-block mb-4">Catatan Laporan:</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo html_escape($p->catatan ?: '-'); ?></span>
                                                    </div>
                                                    <div class="border-bottom pb-8">
                                                        <span class="text-secondary-light d-block mb-4">Tindak Lanjut BK:</span>
                                                        <span class="fw-medium text-warning-main"><?php echo html_escape($p->tindak_lanjut ?: '-'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-end gap-2 mt-12 pt-8">
                                                    <?php if (hasPermissions('kedisiplinan_bk')): ?>
                                                        <button class="btn btn-sm btn-info-100 text-info-600 d-inline-flex align-items-center gap-1 radius-8 px-12 py-6 text-xs" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut<?php echo $p->id_pelanggaran_siswa ?>">
                                                            <iconify-icon icon="solar:user-speak-linear"></iconify-icon> BK
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if (hasPermissions('kedisiplinan_delete')): ?>
                                                        <a href="<?php echo url('kedisiplinan/hapus/' . $p->id_pelanggaran_siswa); ?>" class="btn btn-sm btn-danger-100 text-danger-600 d-inline-flex align-items-center gap-1 radius-8 px-12 py-6 text-xs" onclick="return confirm('Hapus laporan pelanggaran ini?')">
                                                            <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-secondary-light py-24 bg-base radius-8 border">
                                Belum ada laporan pelanggaran.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Pane 2: Catatan Kedisiplinan -->
        <div class="tab-pane fade" id="catatan-pane" role="tabpanel" aria-labelledby="catatan-tab" tabindex="0">
            <div class="card basic-data-table">
                <div class="card-header bg-danger-600 text-white">
                    <h6 class="text-light mb-0">Catatan & Akumulasi Poin Kedisiplinan Siswa</h6>
                </div>
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table bordered-table mb-0" id="rekapTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center" width="60">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">NISN</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col" class="text-center">Jumlah Pelanggaran</th>
                                    <th scope="col" class="text-center">Total Akumulasi Poin</th>
                                    <th scope="col" class="text-center">Status Pembinaan / Sanksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no_rekap = 1; ?>
                                <?php if (!empty($rekap_siswa)): ?>
                                    <?php foreach ($rekap_siswa as $r): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no_rekap++; ?></td>
                                            <td class="fw-semibold text-secondary-light"><?php echo html_escape($r->nama_siswa); ?></td>
                                            <td class="text-center"><?php echo html_escape($r->nisn ?: '-'); ?></td>
                                            <td><?php echo html_escape($r->rombel ?: '-'); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-neutral-100 text-neutral-800 font-bold px-12 py-6"><?php echo $r->total_pelanggaran; ?> Kali</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger-600 text-light px-16 py-8 font-bold text-xs"><?php echo (int) $r->total_poin; ?> Poin</span>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                $poin = (int) $r->total_poin;
                                                if ($poin <= 15) {
                                                    echo '<span class="badge bg-success-100 text-success-800 px-12 py-6">Pembinaan Ringan</span>';
                                                } elseif ($poin <= 30) {
                                                    echo '<span class="badge bg-warning-100 text-warning-800 px-12 py-6">Peringatan I</span>';
                                                } elseif ($poin <= 50) {
                                                    echo '<span class="badge bg-warning-200 text-warning-900 px-12 py-6">Peringatan II</span>';
                                                } elseif ($poin <= 75) {
                                                    echo '<span class="badge bg-danger-100 text-danger-800 px-12 py-6">Peringatan Keras (Panggil Ortu)</span>';
                                                } else {
                                                    echo '<span class="badge bg-danger-600 text-light px-12 py-6">Skorsing / Drop Out</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Accordion View -->
                    <div class="d-block d-md-none">
                        <div class="mb-16">
                            <div class="position-relative">
                                <input type="text" id="mobileRekapKedisiplinanSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari siswa, NISN, atau rombel...">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                    <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($rekap_siswa)): ?>
                            <div class="accordion custom-accordion" id="accordionRekapKedisiplinanMobile">
                                <?php foreach ($rekap_siswa as $ridx => $r): ?>
                                    <?php
                                    $accordionRekapId = "collapseRekap" . $r->id_siswa;
                                    $headingRekapId   = "headingRekap" . $r->id_siswa;
                                    $searchableText = strtolower(html_escape($r->nama_siswa . ' ' . $r->nisn . ' ' . $r->rombel));
                                    ?>
                                    <div class="accordion-item border radius-8 mb-12 mobile-rekap-card" data-search="<?php echo $searchableText; ?>">
                                        <h2 class="accordion-header" id="<?php echo $headingRekapId; ?>">
                                            <button class="accordion-button <?php echo ($ridx === 0) ? '' : 'collapsed'; ?> px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionRekapId; ?>" aria-expanded="<?php echo ($ridx === 0) ? 'true' : 'false'; ?>">
                                                <div class="d-flex flex-column gap-1 w-100 me-12">
                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <span class="text-primary-600 fw-bold"><?php echo html_escape($r->nama_siswa); ?></span>
                                                        <span class="badge bg-neutral-100 text-neutral-800 text-xs px-8 py-2 radius-4"><?php echo html_escape($r->rombel ?: '-'); ?></span>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                                        <span class="badge bg-neutral-100 text-neutral-800 font-bold px-8 py-2 text-xs"><?php echo $r->total_pelanggaran; ?> Kali Pelanggaran</span>
                                                        <span class="badge bg-danger-600 text-light px-10 py-4 font-bold text-xs"><?php echo (int) $r->total_poin; ?> Poin</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="<?php echo $accordionRekapId; ?>" class="accordion-collapse collapse <?php echo ($ridx === 0) ? 'show' : ''; ?>" aria-labelledby="<?php echo $headingRekapId; ?>" data-bs-parent="#accordionRekapKedisiplinanMobile">
                                            <div class="accordion-body bg-neutral-50 p-16 radius-b-8">
                                                <div class="d-flex flex-column gap-8 text-xs">
                                                    <div class="d-flex justify-content-between border-bottom pb-8">
                                                        <span class="text-secondary-light">NISN:</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo html_escape($r->nisn ?: '-'); ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center pt-4">
                                                        <span class="text-secondary-light">Status Pembinaan / Sanksi:</span>
                                                        <div>
                                                            <?php 
                                                            $poin = (int) $r->total_poin;
                                                            if ($poin <= 15) {
                                                                echo '<span class="badge bg-success-100 text-success-800 px-10 py-4 text-xs">Pembinaan Ringan</span>';
                                                            } elseif ($poin <= 30) {
                                                                echo '<span class="badge bg-warning-100 text-warning-800 px-10 py-4 text-xs">Peringatan I</span>';
                                                            } elseif ($poin <= 50) {
                                                                echo '<span class="badge bg-warning-200 text-warning-900 px-10 py-4 text-xs">Peringatan II</span>';
                                                            } elseif ($poin <= 75) {
                                                                echo '<span class="badge bg-danger-100 text-danger-800 px-10 py-4 text-xs">Peringatan Keras (Panggil Ortu)</span>';
                                                            } else {
                                                                echo '<span class="badge bg-danger-600 text-light px-10 py-4 text-xs">Skorsing / Drop Out</span>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-secondary-light py-24 bg-base radius-8 border">
                                Belum ada rekap data kedisiplinan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        let table = new DataTable('#dataTable');
        let rekapTable = new DataTable('#rekapTable');

        $('#mobilePelanggaranSearch').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('.mobile-pelanggaran-card').filter(function() {
                $(this).toggle($(this).data('search').indexOf(value) > -1);
            });
        });

        $('#mobileRekapKedisiplinanSearch').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('.mobile-rekap-card').filter(function() {
                $(this).toggle($(this).data('search').indexOf(value) > -1);
            });
        });
    });
</script>
