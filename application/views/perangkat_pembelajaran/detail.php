<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 bg-warning-900">
            <div>
                <h6 class="text-light mb-1">Perangkat Pembelajaran</h6>
                <p class="text-light text-sm mb-0">
                    <?php echo html_escape($item->nama_mapel . ' - ' . trim($item->nama_lembaga . ' ' . $item->nama_tingkat . ' ' . $item->nama_rombel)) ?>
                </p>
            </div>
            <a href="<?php echo $back_url ?>" class="btn btn-sm btn-warning-600">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-3">
                    <span class="text-secondary-light d-block">Tahun/Semester</span>
                    <strong><?php echo html_escape($item->tahun_pelajaran . ' (' . $item->semester . ')') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light d-block">Guru</span>
                    <strong><?php echo html_escape($item->nama_ptk ?: '-') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light d-block">Jumlah JP</span>
                    <strong><?php echo (int) $item->jumlah_jam ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary-light d-block">Status</span>
                    <?php if ($perangkat): ?>
                        <span class="badge bg-success-100 text-success-600">Sudah Generate</span>
                    <?php else: ?>
                        <span class="badge bg-warning-100 text-warning-600">Belum Generate</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$perangkat): ?>
        <div class="card">
            <div class="card-header bg-neutral-100">
                <h6 class="mb-0">Generate Otomatis</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo $generate_url ?>" method="post" class="row gy-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Cadangan Hari</label>
                        <input type="number" min="0" class="form-control" name="cadangan_hari" value="28">
                        <small class="text-secondary-light">Default 28 hari untuk PTS 1 minggu, PAS 1 minggu, dan hari terganggu 2 minggu.</small>
                    </div>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-primary-600 px-4">Generate CP, ATP, Modul, dan Materi Harian</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <form action="<?php echo $save_url ?>" method="post">
            <div class="card mb-4">
                <div class="card-header bg-neutral-100 d-flex flex-wrap justify-content-between gap-2">
                    <h6 class="mb-0">Dokumen Perangkat</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary-100 text-primary-600">Hari Efektif <?php echo (int) $perangkat->hari_efektif ?></span>
                        <span class="badge bg-warning-100 text-warning-600">Cadangan <?php echo (int) $perangkat->cadangan_hari ?></span>
                        <span class="badge bg-success-100 text-success-600">Pertemuan <?php echo (int) $perangkat->jumlah_pertemuan ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-cp" type="button">CP</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-atp" type="button">ATP</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-modul" type="button">Modul Ajar</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-cp">
                            <textarea class="form-control" rows="12" name="cp"><?php echo html_escape($perangkat->cp) ?></textarea>
                        </div>
                        <div class="tab-pane fade" id="tab-atp">
                            <textarea class="form-control" rows="12" name="atp"><?php echo html_escape($perangkat->atp) ?></textarea>
                        </div>
                        <div class="tab-pane fade" id="tab-modul">
                            <textarea class="form-control" rows="12" name="modul_ajar"><?php echo html_escape($perangkat->modul_ajar) ?></textarea>
                        </div>
                    </div>
                    <?php if (!empty($perangkat->sumber_url)): ?>
                        <p class="text-secondary-light text-sm mt-3 mb-0">Sumber template: <?php echo nl2br(html_escape($perangkat->sumber_url)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-neutral-100">
                    <h6 class="mb-0">Materi Harian</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Ke</th>
                                    <th>Tanggal</th>
                                    <th>Materi</th>
                                    <th>Tujuan</th>
                                    <th>Catatan</th>
                                    <th class="text-center">Diajarkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materi as $row): ?>
                                    <tr>
                                        <td class="text-center"><?php echo (int) $row->pertemuan_ke ?></td>
                                        <td data-order="<?php echo html_escape($row->tanggal) ?>"><?php echo $row->tanggal ? date('d/m/Y', strtotime($row->tanggal)) : '-' ?></td>
                                        <td>
                                            <input type="text" class="form-control" name="materi[<?php echo $row->id_materi_harian ?>][materi]" value="<?php echo html_escape($row->materi) ?>">
                                        </td>
                                        <td>
                                            <textarea class="form-control" rows="2" name="materi[<?php echo $row->id_materi_harian ?>][tujuan]"><?php echo html_escape($row->tujuan) ?></textarea>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="materi[<?php echo $row->id_materi_harian ?>][catatan]" value="<?php echo html_escape($row->catatan) ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input" name="materi[<?php echo $row->id_materi_harian ?>][status]" value="1" <?php echo $row->status === 'Diajarkan' ? 'checked' : '' ?>>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary-600 px-4">Simpan Perangkat</button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    if (document.querySelector('#dataTable')) {
        let table = new DataTable('#dataTable', {
            pageLength: 25,
            order: [
                [0, 'asc']
            ]
        });
    }
</script>
