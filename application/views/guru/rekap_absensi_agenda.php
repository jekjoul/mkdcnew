<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<style>
@media print {
    .dashboard-main-body > div:first-child,
    #form-filter-rekap,
    .btn-print-rekap,
    footer,
    .sidebar {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<div class="dashboard-main-body">
    <!-- Breadcrumb -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0 text-primary-light">Rekap Absensi Agenda Pembelajaran</h6>
            <p class="text-secondary-light text-sm mb-0">Akumulasi statistik kehadiran siswa per mata pelajaran dan per bulan KBM.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="<?php echo url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-secondary-light">Rekap Absensi Agenda</li>
        </ul>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 radius-12 shadow-xs mb-24" id="form-filter-rekap">
        <div class="card-body p-20">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Mata Pelajaran Saya</label>
                    <select name="id_mapel" id="filter-mapel" class="form-select radius-8" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <?php if (is_object($m) && isset($m->id_mapel)): ?>
                                <option value="<?php echo $m->id_mapel ?>" <?php echo ((string)$selected_mapel === (string)$m->id_mapel) ? 'selected' : '' ?>>
                                    <?php echo html_escape($m->nama_mapel) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Rombel / Kelas</label>
                    <select name="id_rombel" id="filter-rombel" class="form-select radius-8">
                        <option value="">-- Semua Rombel --</option>
                        <?php foreach ($rombel_list as $r): ?>
                            <?php if (is_object($r) && isset($r->id_rombel)): ?>
                                <option value="<?php echo $r->id_rombel ?>" <?php echo ((string)$selected_rombel === (string)$r->id_rombel) ? 'selected' : '' ?>>
                                    <?php echo html_escape($r->nama_rombel) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Bulan KBM</label>
                    <select name="bulan" id="filter-bulan" class="form-select radius-8">
                        <?php
                        $nama_bulan = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        foreach ($nama_bulan as $k => $v):
                        ?>
                            <option value="<?php echo $k ?>" <?php echo ((string)$selected_bulan === (string)$k) ? 'selected' : '' ?>>
                                <?php echo $v ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-6">Tahun</label>
                    <select name="tahun" id="filter-tahun" class="form-select radius-8">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?php echo $y ?>" <?php echo ((string)$selected_tahun === (string)$y) ? 'selected' : '' ?>>
                                <?php echo $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary radius-8 px-16 w-100" title="Terapkan Filter">
                        <iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards & Cetak Button -->
    <?php
    $total_siswa = count($rekap_siswa);
    $sum_pct = 0;
    foreach ($rekap_siswa as $rs) {
        $sum_pct += $rs->persentase;
    }
    $avg_pct = $total_siswa > 0 ? round($sum_pct / $total_siswa, 1) : 0;
    ?>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-24">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="p-12 px-20 bg-primary-50 radius-8 border border-primary-200">
                <span class="text-xs text-primary-700 fw-bold d-block text-uppercase">Total Pertemuan KBM</span>
                <span class="fs-5 fw-bold text-primary-900"><?php echo $total_pertemuan ?> Pertemuan</span>
            </div>
            <div class="p-12 px-20 bg-info-50 radius-8 border border-info-200">
                <span class="text-xs text-info-700 fw-bold d-block text-uppercase">Total Siswa Terdaftar</span>
                <span class="fs-5 fw-bold text-info-900"><?php echo $total_siswa ?> Siswa</span>
            </div>
            <div class="p-12 px-20 bg-success-50 radius-8 border border-success-200">
                <span class="text-xs text-success-700 fw-bold d-block text-uppercase">Rata-rata Kehadiran Kelas</span>
                <span class="fs-5 fw-bold text-success-900"><?php echo $avg_pct ?>%</span>
            </div>
        </div>

        <button type="button" onclick="window.print()" class="btn btn-outline-primary radius-8 px-16 py-8 btn-print-rekap d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:printer-bold" class="text-lg"></iconify-icon> Cetak Rekapitulasi Absensi
        </button>
    </div>

    <!-- Main Rekap Table -->
    <div class="card border-0 radius-12 shadow-xs">
        <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0 text-primary-light fw-bold">
                    Rekapitulasi Absensi Bulan <?php echo $nama_bulan[$selected_bulan] ?? $selected_bulan ?> <?php echo $selected_tahun ?>
                </h6>
                <span class="text-xs text-secondary-light">Akumulasi presensi harian per pertemuan KBM.</span>
            </div>
            <span class="badge bg-primary-50 text-primary-600 radius-6 px-12 py-6">Periode <?php echo $selected_bulan ?>/<?php echo $selected_tahun ?></span>
        </div>

        <div class="card-body p-20">
            <div class="table-responsive">
                <table class="table bordered-table align-middle w-100 mb-0" id="tableRekapAbsensiGuru">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" style="width: 160px;">NISN / NIPD</th>
                            <th class="text-center" style="width: 50px;">L/P</th>
                            <th class="text-center" style="width: 90px;">Hadir (H)</th>
                            <th class="text-center" style="width: 90px;">Izin (I)</th>
                            <th class="text-center" style="width: 90px;">Sakit (S)</th>
                            <th class="text-center" style="width: 90px;">Alpa (A)</th>
                            <th class="text-center" style="width: 140px;">Kehadiran (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rekap_siswa)): ?>
                            <?php $nr = 1; foreach ($rekap_siswa as $r): ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?php echo $nr++ ?></td>
                                    <td>
                                        <span class="fw-semibold text-primary-900 d-block"><?php echo html_escape($r->nama_siswa) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-xs font-monospace"><?php echo html_escape($r->nisn ?: $r->nipd ?: '-') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-neutral-100 text-neutral-700"><?php echo html_escape($r->jenis_kelamin ?: 'L') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-focus text-success-main px-12 py-6 radius-4 fw-bold"><?php echo $r->total_hadir ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-focus text-info-main px-12 py-6 radius-4 fw-bold"><?php echo $r->total_izin ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-focus text-warning-main px-12 py-6 radius-4 fw-bold"><?php echo $r->total_sakit ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-focus text-danger-main px-12 py-6 radius-4 fw-bold"><?php echo $r->total_alpa ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="fw-bold <?php echo ($r->persentase >= 80) ? 'text-success-main' : (($r->persentase >= 60) ? 'text-warning-main' : 'text-danger-main') ?>">
                                                <?php echo $r->persentase ?>%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-neutral-400 py-32">
                                    <iconify-icon icon="solar:chart-square-linear" style="font-size: 32px;"></iconify-icon>
                                    <div class="mt-8 text-xs">Belum ada data presensi agenda KBM yang tercatat pada bulan terpilih.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    if ($('#tableRekapAbsensiGuru').length > 0) {
        $('#tableRekapAbsensiGuru').DataTable({
            pageLength: 50,
            order: [[0, 'asc']],
            language: {
                emptyTable: "Belum ada data rekapitulasi absensi agenda."
            }
        });
    }
});
</script>
