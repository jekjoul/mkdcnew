<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header bg-neutral-300">
            <h6 class="text-dark mb-0">Sinkronisasi Dapodik</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo url('sync_dapodik/fetch') ?>" method="post">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Base URL Dapodik</label>
                        <input type="text" class="form-control" name="base_url" value="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Endpoint Peserta Didik</label>
                        <input type="text" class="form-control" name="endpoint" value="<?php echo htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Bearer Token Dapodik</label>
                        <input type="text" class="form-control" name="api_key" value="<?php echo htmlspecialchars($api_key, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">NPSN</label>
                        <input type="text" class="form-control" name="npsn" value="<?php echo htmlspecialchars($npsn, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary-600">
                            <iconify-icon icon="lucide:refresh-cw"></iconify-icon>
                            Ambil Preview Dapodik
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($run): ?>
        <?php
        $summary = ['baru' => 0, 'berbeda' => 0, 'sama' => 0];
        foreach ($summary_items as $item) {
            if (isset($summary[$item->match_status])) {
                $summary[$item->match_status]++;
            }
        }
        ?>
        <div class="row gy-3 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <span class="text-secondary-light d-block">Data Dapodik</span>
                        <h5 class="mb-0"><?php echo $run->total_remote ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <span class="text-secondary-light d-block">Siswa Baru</span>
                        <h5 class="mb-0 text-info-600"><?php echo $summary['baru'] ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <span class="text-secondary-light d-block">Data Berbeda</span>
                        <h5 class="mb-0 text-warning-600"><?php echo $summary['berbeda'] ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <span class="text-secondary-light d-block">Data Sama</span>
                        <h5 class="mb-0 text-success-600"><?php echo $summary['sama'] ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo url('sync_dapodik/apply') ?>" method="post" onsubmit="return confirm('Terapkan data Dapodik yang dipilih ke data siswa lokal?')">
            <input type="hidden" name="run_id" value="<?php echo $run->id_run ?>">
            <div class="card">
                <div class="card-header bg-neutral-100 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="mb-1">Tabel Pembanding Data Siswa</h6>
                        <span class="text-secondary-light text-sm">
                            Preview dibuat <?php echo $run->created_at ?>. Data yang sudah sama tidak ditampilkan di tabel ini.
                        </span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-info-100 text-info-600" id="selectAllVisible">
                            Pilih Semua Perubahan
                        </button>
                        <button type="submit" class="btn btn-success-600">
                            <iconify-icon icon="lucide:check"></iconify-icon>
                            Terapkan Data Terpilih
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="90">
                                        <label class="d-flex align-items-center gap-2 mb-0">
                                            <input type="checkbox" id="checkAllSync" class="form-check-input">
                                            <span>Terapkan</span>
                                        </label>
                                    </th>
                                    <th>Status</th>
                                    <th>Nama Dapodik</th>
                                    <th>NISN Lokal</th>
                                    <th>NISN Dapodik</th>
                                    <th>Rombel Lokal</th>
                                    <th>Rombel Dapodik</th>
                                    <th>Jumlah Perbedaan</th>
                                    <th>Detail Pembanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <?php
                                    $badge = $item->match_status === 'baru' ? 'bg-info-100 text-info-600' : ($item->match_status === 'berbeda' ? 'bg-warning-100 text-warning-600' : 'bg-success-100 text-success-600');
                                    $diff_details = !empty($item->diff_details) ? json_decode($item->diff_details, true) : [];
                                    $dapodik_payload = !empty($item->payload) ? json_decode($item->payload, true) : [];
                                    $diff_count = $item->match_status === 'baru' ? count(array_filter($dapodik_payload, function ($value) {
                                        return $value !== '';
                                    })) : count($diff_details);
                                    ?>
                                    <tr>
                                        <td>
                                            <label class="d-inline-flex align-items-center gap-2 mb-0">
                                                <input type="checkbox" class="form-check-input sync-item-check" name="item[]" value="<?php echo $item->id_item ?>">
                                                <span>Pilih</span>
                                            </label>
                                        </td>
                                        <td><span class="badge <?php echo $badge ?>"><?php echo ucfirst($item->match_status) ?></span></td>
                                        <td><?php echo htmlspecialchars($item->nama_dapodik ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?php echo htmlspecialchars($item->nisn_lokal ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?php echo htmlspecialchars($item->nisn_dapodik ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?php echo htmlspecialchars($item->rombel_lokal ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?php echo htmlspecialchars($item->rombel_dapodik ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?php echo $diff_count ?> field</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#diffModal-<?php echo $item->id_item ?>">
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-secondary-light py-4">Tidak ada perubahan. Semua data yang dapat dibandingkan sudah sama.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>

        <?php foreach ($items as $item): ?>
            <?php
            $diff_details = !empty($item->diff_details) ? json_decode($item->diff_details, true) : [];
            $dapodik_payload = !empty($item->payload) ? json_decode($item->payload, true) : [];
            ?>
            <div class="modal fade" id="diffModal-<?php echo $item->id_item ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h6 class="modal-title mb-1">Detail Pembanding Data Siswa</h6>
                                <span class="text-secondary-light text-sm"><?php echo htmlspecialchars($item->nama_dapodik ?: '-', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm bordered-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Data Lokal</th>
                                            <th>Data Dapodik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($item->match_status === 'baru'): ?>
                                            <?php foreach ($dapodik_payload as $field => $value): ?>
                                                <?php if ($value === '') continue; ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td>-</td>
                                                    <td><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php foreach ($diff_details as $field => $detail): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars(isset($detail['label']) ? $detail['label'] : $field, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($detail['local'] !== '' ? $detail['local'] : '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($detail['dapodik'] !== '' ? $detail['dapodik'] : '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center text-secondary-light py-5">
                Belum ada preview sinkronisasi. Ambil data Dapodik terlebih dahulu untuk melihat tabel pembanding.
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    if (document.querySelector('#dataTable')) {
        let table = new DataTable('#dataTable');
    }

    $('#checkAllSync').on('change', function() {
        $('.sync-item-check').prop('checked', $(this).is(':checked'));
    });

    $('#selectAllVisible').on('click', function() {
        const allChecked = $('.sync-item-check').length === $('.sync-item-check:checked').length;
        $('.sync-item-check, #checkAllSync').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua Perubahan' : 'Batalkan Semua Pilihan');
    });
</script>
