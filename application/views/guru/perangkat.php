<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card">
        <div class="card-header bg-warning-900">
            <h6 class="text-light mb-0">Perangkat Pembelajaran Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tahun/Sem</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Progress Materi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <?php
                            $percent = 0;
                            if (!empty($row->file_cp)) $percent += 10;
                            if (!empty($row->file_tp)) $percent += 10;
                            if (!empty($row->file_atp)) $percent += 10;
                            if (!empty($row->file_kktp)) $percent += 10;
                            if (!empty($row->file_kisi_sts)) $percent += 10;
                            if (!empty($row->file_soal_sts)) $percent += 10;
                            if (!empty($row->file_kisi_sas)) $percent += 10;
                            if (!empty($row->file_soal_sas)) $percent += 10;
                            if (!empty($row->total_modul_ajar) && $row->total_modul_ajar > 0) $percent += 10;
                            if (!empty($row->total_materi) && $row->total_materi > 0) $percent += 10;
                            ?>
                            <tr>
                                <td><?php echo html_escape($row->tahun_pelajaran) ?> (<?php echo html_escape($row->semester) ?>)</td>
                                <td>(<?php echo html_escape(trim((isset($row->nama_lembaga_singkat) && $row->nama_lembaga_singkat ? $row->nama_lembaga_singkat : $row->nama_lembaga) . ') ' . $row->nama_tingkat . ' - ' . $row->nama_rombel)) ?></td>
                                <td><?php echo html_escape($row->nama_mapel) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success-main" style="width: <?php echo $percent ?>%"></div>
                                        </div>
                                        <span class="text-sm"><?php echo $percent ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo url('guru/perangkat_detail/' . $row->id_pembelajaran_mapel) ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
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
