<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
            <h6 class="mb-0 text-light"><?php echo !empty($is_nonaktif) ? 'Perangkat Pembelajaran Tidak Aktif' : 'Perangkat Pembelajaran'; ?></h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?php echo url(!empty($is_nonaktif) ? 'perangkat_pembelajaran' : 'perangkat_pembelajaran/nonaktif') ?>" class="btn btn-sm btn-warning-600 text-light radius-8 px-12 py-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>" class="text-lg"></iconify-icon>
                    <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <?php
                            $total = (int) $row->total_materi;
                            $done = (int) $row->diajarkan;
                            $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                            ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran . ' (' . $row->semester . ')') ?></td>
                                <td><?php echo html_escape(trim($row->nama_lembaga . ' ' . $row->nama_tingkat . ' ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?></td>
                                <td><?php echo html_escape($row->nama_ptk ?: '-') ?></td>
                                <td>
                                    <?php if ($row->id_perangkat): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-success-main" style="width: <?php echo $percent ?>%"></div>
                                            </div>
                                            <span class="text-sm"><?php echo $done ?>/<?php echo $total ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-warning-100 text-warning-600">Belum Generate</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo url('perangkat_pembelajaran/detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:document-add-linear"></iconify-icon> Kelola
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>
