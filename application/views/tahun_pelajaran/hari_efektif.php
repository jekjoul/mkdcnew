<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<?php
$status_options = ['Efektif', 'Libur', 'Daring', 'Luar Kelas'];
$badge_class = [
    'Efektif' => 'bg-success-100 text-success-600',
    'Libur' => 'bg-danger-100 text-danger-600',
    'Daring' => 'bg-info-100 text-info-600',
    'Luar Kelas' => 'bg-warning-100 text-warning-600',
];
?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-neutral-300">
                    <div>
                        <h6 class="mb-1">Hari Efektif Sekolah</h6>
                        <p class="text-sm text-secondary-light mb-0">
                            <?php echo html_escape($row->tahun_pelajaran . ' - Semester ' . $row->semester) ?>
                            (<?php echo date('d/m/Y', strtotime($periode['awal'])) ?> - <?php echo date('d/m/Y', strtotime($periode['akhir'])) ?>)
                        </p>
                    </div>
                    <a href="<?php echo url('tahun_pelajaran') ?>" class="btn btn-sm btn-secondary-light">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row gy-3 mb-24">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-16 border radius-8">
                                <span class="text-secondary-light text-sm">Total Hari</span>
                                <h5 class="mb-0"><?php echo $summary->total ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-16 border radius-8">
                                <span class="text-secondary-light text-sm">Efektif</span>
                                <h5 class="mb-0 text-success-600"><?php echo $summary->efektif ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-16 border radius-8">
                                <span class="text-secondary-light text-sm">Libur</span>
                                <h5 class="mb-0 text-danger-600"><?php echo $summary->libur ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-16 border radius-8">
                                <span class="text-secondary-light text-sm">Daring / Luar Kelas</span>
                                <h5 class="mb-0 text-info-600"><?php echo $summary->daring + $summary->luar_kelas ?></h5>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($hari_efektif)): ?>
                        <div class="alert alert-warning radius-8 mb-0">Data hari efektif belum digenerate.</div>
                    <?php else: ?>
                        <form action="<?php echo url('tahun_pelajaran/update_hari_efektif/' . $row->id_tahun_pelajaran) ?>" method="post">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Tanggal</th>
                                            <th>Hari</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($hari_efektif as $item): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no++ ?></td>
                                                <td data-order="<?php echo html_escape($item->tanggal) ?>"><?php echo date('d/m/Y', strtotime($item->tanggal)) ?></td>
                                                <td><?php echo html_escape($item->nama_hari) ?></td>
                                                <td>
                                                    <select name="status[<?php echo $item->id_hari_efektif ?>]" class="form-control form-select radius-8 hari-status <?php echo $badge_class[$item->status] ?? '' ?>">
                                                        <?php foreach ($status_options as $status): ?>
                                                            <option value="<?php echo $status ?>" <?php echo $item->status === $status ? 'selected' : '' ?>>
                                                                <?php echo $status ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="keterangan[<?php echo $item->id_hari_efektif ?>]" class="form-control radius-8" value="<?php echo html_escape($item->keterangan) ?>" placeholder="Keterangan">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-24">
                                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    if (document.querySelector('#dataTable')) {
        let table = new DataTable('#dataTable', {
            pageLength: 31,
            order: [
                [1, 'asc']
            ]
        });
    }

    const statusClass = {
        'Efektif': 'bg-success-100 text-success-600',
        'Libur': 'bg-danger-100 text-danger-600',
        'Daring': 'bg-info-100 text-info-600',
        'Luar Kelas': 'bg-warning-100 text-warning-600'
    };

    $('.hari-status').on('change', function() {
        $(this).removeClass(Object.values(statusClass).join(' '));
        $(this).addClass(statusClass[$(this).val()] || '');
    });
</script>
