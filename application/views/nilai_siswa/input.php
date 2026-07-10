<?php include viewPath('includes/header'); ?>
<?php
// Decode labels jika ada
$labels_tugas = ['Tugas 1'];
$labels_uh = ['UH 1'];
if ($setting) {
    if (!empty($setting->labels_tugas)) {
        $labels_tugas = json_decode($setting->labels_tugas, true) ?: ['Tugas 1'];
    }
    if (!empty($setting->labels_uh)) {
        $labels_uh = json_decode($setting->labels_uh, true) ?: ['UH 1'];
    }
}
?>
<style>
    .editable-header {
        border-bottom: 1px dashed #405189;
        cursor: pointer;
        padding: 2px 4px;
        display: inline-block;
        min-width: 60px;
    }

    .editable-header:focus {
        outline: none;
        border-bottom: 2px solid #405189;
        background: #fff;
        color: #333;
    }

    .th-action-btn {
        padding: 0px 4px;
        font-size: 11px;
        line-height: 1;
        border-radius: 3px;
        margin-left: 2px;
    }

    /* Fixed header & Sticky columns styles */
    .table-container-fixed {
        max-height: 550px;
        overflow: auto;
        position: relative;
    }

    .table-container-fixed table {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Sticky headers */
    .table-container-fixed thead tr:nth-child(1) th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6;
    }

    .table-container-fixed thead tr:nth-child(2) th {
        position: sticky;
        top: 43px; /* tinggi baris pertama thead */
        z-index: 10;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6;
    }

    /* Sticky columns (No & Nama) */
    .table-container-fixed thead tr:nth-child(1) th:nth-child(1),
    .table-container-fixed tbody tr td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 5;
        background-color: #fff !important;
        border-right: 1px solid #dee2e6;
    }
    
    .table-container-fixed thead tr:nth-child(1) th:nth-child(2),
    .table-container-fixed tbody tr td:nth-child(2) {
        position: sticky;
        left: 50px; /* Lebar kolom No */
        z-index: 5;
        background-color: #fff !important;
        border-right: 1px solid #dee2e6;
    }

    /* Z-Index adjustment for headers of sticky columns */
    .table-container-fixed thead tr:nth-child(1) th:nth-child(1) {
        z-index: 12 !important;
        background-color: #f8f9fa !important;
    }
    .table-container-fixed thead tr:nth-child(1) th:nth-child(2) {
        z-index: 12 !important;
        background-color: #f8f9fa !important;
    }

    /* Row span compensation for sticky header row 2 for student name & no which spans 2 rows */
    .table-container-fixed thead tr:nth-child(1) th:nth-child(3) {
        position: sticky;
        left: 230px; /* 50px No + 180px Nama */
        z-index: 5;
        background-color: #fff !important;
        border-right: 1px solid #dee2e6;
    }
    .table-container-fixed tbody tr td:nth-child(3) {
        position: sticky;
        left: 230px;
        z-index: 5;
        background-color: #fff !important;
        border-right: 1px solid #dee2e6;
    }
    .table-container-fixed thead tr:nth-child(1) th:nth-child(3) {
        z-index: 12 !important;
        background-color: #f8f9fa !important;
    }

    /* Hover effect on rows to maintain visible background */
    .table-container-fixed tbody tr:hover td {
        background-color: #f1f3f7 !important;
    }
</style>
<div class="dashboard-main-body">
    <form action="<?php echo url('nilai_siswa/simpan_nilai/' . $item->id_pembelajaran_mapel) ?>" method="post" id="formNilai">
        <!-- Input Hidden untuk menyimpan label nama kolom tugas dan uh yang diedit inline -->
        <div id="hiddenLabelsContainer">
            <?php foreach ($labels_tugas as $idx => $lbl): ?>
                <input type="hidden" name="labels_tugas[]" class="lbl-tugas-input" value="<?php echo html_escape($lbl) ?>">
            <?php endforeach; ?>
            <?php foreach ($labels_uh as $idx => $lbl): ?>
                <input type="hidden" name="labels_uh[]" class="lbl-uh-input" value="<?php echo html_escape($lbl) ?>">
            <?php endforeach; ?>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-warning-900">
                <h6 class="mb-0 text-light">Input Nilai Siswa</h6>
                <a href="<?php echo url('nilai_siswa/setting/' . $item->id_pembelajaran_mapel) ?>" class="btn btn-light btn-sm d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:settings-linear"></iconify-icon> Setting Persentase
                </a>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tahun/Semester</span>
                        <strong><?php echo html_escape($item->tahun_pelajaran) ?> (<?php echo html_escape($item->semester) ?>)</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Kelas</span>
                        <strong><?php echo html_escape(trim($item->nama_tingkat . ' - ' . $item->nama_rombel)) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Mata Pelajaran</span>
                        <strong><?php echo html_escape($item->nama_mapel) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Guru Mapel</span>
                        <strong><?php echo $item->nama_ptk ? html_escape($item->nama_ptk) : '-' ?></strong>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                    <span class="badge bg-info-100 text-info-600">Harian <?php echo $setting ? (float) $setting->persen_harian : 0 ?>%</span>
                    <span class="badge bg-warning-100 text-warning-600">PSTS <?php echo $setting ? (float) $setting->persen_psts : 0 ?>%</span>
                    <span class="badge bg-success-100 text-success-600">PSAS <?php echo $setting ? (float) $setting->persen_psas : 0 ?>%</span>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-xs btn-outline-info d-flex align-items-center gap-1" id="addTugasBtn">
                            <iconify-icon icon="lucide:plus"></iconify-icon> Tambah Tugas
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-primary d-flex align-items-center gap-1" id="addUhBtn">
                            <iconify-icon icon="lucide:plus"></iconify-icon> Tambah UH
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-neutral-100">
                <h6 class="mb-0">Daftar Nilai</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive table-container-fixed">
                    <table class="table bordered-table mb-0 align-middle" id="tableNilai">
                        <thead>
                            <tr class="bg-neutral-50">
                                <th width="50" rowspan="2" class="text-center align-middle">No</th>
                                <th rowspan="2" class="align-middle" style="min-width: 180px;">Nama Siswa</th>
                                <th width="120" rowspan="2" class="text-center align-middle">NISN/NIPD</th>
                                <th colspan="<?php echo count($labels_tugas) ?>" class="text-center table-info text-info-800" id="headerTugasParent">
                                    Nilai Tugas
                                </th>
                                <th colspan="<?php echo count($labels_uh) ?>" class="text-center table-primary text-primary-800" id="headerUhParent">
                                    Nilai Ujian Harian
                                </th>
                                <th width="100" rowspan="2" class="text-center align-middle table-info text-info-900">Rata2 Harian (Tugas + UH)</th>
                                <th width="100" rowspan="2" class="text-center align-middle table-warning text-warning-900">PSTS</th>
                                <th width="100" rowspan="2" class="text-center align-middle table-success text-success-900">PSAS</th>
                                <th width="100" rowspan="2" class="text-center align-middle bg-neutral-200">Nilai Rapor</th>
                            </tr>
                            <tr id="subHeaderRow" class="bg-neutral-50">
                                <!-- Kolom Tugas -->
                                <?php foreach ($labels_tugas as $idx => $lbl): ?>
                                    <th class="text-center th-tugas" data-index="<?php echo $idx ?>" width="100">
                                        <span class="editable-header" contenteditable="true" onblur="updateLabel('tugas', <?php echo $idx ?>, this)"><?php echo html_escape($lbl) ?></span>
                                        <?php if (count($labels_tugas) > 1): ?>
                                            <button type="button" class="btn btn-xs btn-danger th-action-btn" onclick="removeCol('tugas', <?php echo $idx ?>)">&times;</button>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                                <!-- Kolom UH -->
                                <?php foreach ($labels_uh as $idx => $lbl): ?>
                                    <th class="text-center th-uh" data-index="<?php echo $idx ?>" width="100">
                                        <span class="editable-header" contenteditable="true" onblur="updateLabel('uh', <?php echo $idx ?>, this)"><?php echo html_escape($lbl) ?></span>
                                        <?php if (count($labels_uh) > 1): ?>
                                            <button type="button" class="btn btn-xs btn-danger th-action-btn" onclick="removeCol('uh', <?php echo $idx ?>)">&times;</button>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswa as $index => $s): ?>
                                <?php
                                $row = isset($nilai[(int) $s->id_siswa]) ? $nilai[(int) $s->id_siswa] : null;
                                $extra_tugas_data = [];
                                $extra_uh_data = [];
                                if ($row) {
                                    if (!empty($row->extra_tugas)) {
                                        $extra_tugas_data = json_decode($row->extra_tugas, true) ?: [];
                                    }
                                    if (!empty($row->extra_uh)) {
                                        $extra_uh_data = json_decode($row->extra_uh, true) ?: [];
                                    }
                                }
                                ?>
                                <tr class="nilai-row" data-siswa-id="<?php echo (int) $s->id_siswa ?>">
                                    <td class="text-center"><?php echo $index + 1 ?></td>
                                    <td class="fw-semibold"><?php echo html_escape($s->nama_siswa) ?></td>
                                    <td class="text-center">
                                        <span class="d-block text-xs"><?php echo html_escape($s->nisn ?: '-') ?></span>
                                        <span class="text-secondary-light text-xs"><?php echo html_escape($s->nipd ?: '-') ?></span>
                                    </td>
                                    <!-- Input Tugas -->
                                    <?php foreach ($labels_tugas as $idx => $lbl): ?>
                                        <td class="td-tugas" data-index="<?php echo $idx ?>">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input sub-tugas" name="nilai[<?php echo (int) $s->id_siswa ?>][extra_tugas][<?php echo $idx ?>]" value="<?php echo isset($extra_tugas_data[$idx]) && $extra_tugas_data[$idx] !== '' ? (float) $extra_tugas_data[$idx] : '' ?>">
                                        </td>
                                    <?php endforeach; ?>
                                    <!-- Input UH -->
                                    <?php foreach ($labels_uh as $idx => $lbl): ?>
                                        <td class="td-uh" data-index="<?php echo $idx ?>">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input sub-uh" name="nilai[<?php echo (int) $s->id_siswa ?>][extra_uh][<?php echo $idx ?>]" value="<?php echo isset($extra_uh_data[$idx]) && $extra_uh_data[$idx] !== '' ? (float) $extra_uh_data[$idx] : '' ?>">
                                        </td>
                                    <?php endforeach; ?>
                                    <!-- Rata2 Harian -->
                                    <td>
                                        <input type="text" class="form-control text-center nilai-harian bg-neutral-100" name="nilai[<?php echo (int) $s->id_siswa ?>][harian]" value="<?php echo $row && $row->nilai_harian !== null ? (float) $row->nilai_harian : '' ?>" readonly>
                                    </td>
                                    <!-- PSTS -->
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input nilai-psts" name="nilai[<?php echo (int) $s->id_siswa ?>][psts]" value="<?php echo $row && $row->nilai_psts !== null ? (float) $row->nilai_psts : '' ?>">
                                    </td>
                                    <!-- PSAS -->
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input nilai-psas" name="nilai[<?php echo (int) $s->id_siswa ?>][psas]" value="<?php echo $row && $row->nilai_psas !== null ? (float) $row->nilai_psas : '' ?>">
                                    </td>
                                    <!-- Rapor -->
                                    <td>
                                        <input type="text" class="form-control text-center nilai-rapor bg-neutral-100 fw-bold" value="<?php echo $row && $row->nilai_rapor !== null ? (float) $row->nilai_rapor : '' ?>" readonly>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($siswa)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-secondary-light py-4">Belum ada siswa pada pembelajaran ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end mb-5">
            <a href="<?php echo url('nilai_siswa') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Nilai</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    const persenHarian = <?php echo $setting ? (float) $setting->persen_harian : 0 ?>;
    const persenPsts = <?php echo $setting ? (float) $setting->persen_psts : 0 ?>;
    const persenPsas = <?php echo $setting ? (float) $setting->persen_psas : 0 ?>;

    function nilaiNumber(value) {
        if (value === '') return null;
        const parsed = parseFloat(value);
        return Number.isNaN(parsed) ? null : Math.min(100, Math.max(0, parsed));
    }

    function hitungRow(row) {
        // Hitung Rata-rata Tugas
        let sumTugas = 0;
        let countTugas = 0;
        row.find('.sub-tugas').each(function() {
            const val = nilaiNumber($(this).val());
            if (val !== null) {
                sumTugas += val;
                countTugas++;
            }
        });
        const avgTugas = countTugas > 0 ? (sumTugas / countTugas) : null;

        // Hitung Rata-rata UH
        let sumUh = 0;
        let countUh = 0;
        row.find('.sub-uh').each(function() {
            const val = nilaiNumber($(this).val());
            if (val !== null) {
                sumUh += val;
                countUh++;
            }
        });
        const avgUh = countUh > 0 ? (sumUh / countUh) : null;

        // Rata2 Harian = Rata2 Tugas & UH
        let harian = null;
        if (avgTugas !== null && avgUh !== null) {
            harian = (avgTugas + avgUh) / 2;
        } else if (avgTugas !== null) {
            harian = avgTugas;
        } else if (avgUh !== null) {
            harian = avgUh;
        }

        row.find('.nilai-harian').val(harian !== null ? harian.toFixed(2).replace(/\.00$/, '') : '');

        // Hitung Rapor
        const psts = nilaiNumber(row.find('.nilai-psts').val());
        const psas = nilaiNumber(row.find('.nilai-psas').val());
        let hasValue = false;
        let rapor = 0;

        if (harian !== null) {
            hasValue = true;
            rapor += harian * persenHarian / 100;
        }
        if (psts !== null) {
            hasValue = true;
            rapor += psts * persenPsts / 100;
        }
        if (psas !== null) {
            hasValue = true;
            rapor += psas * persenPsas / 100;
        }

        row.find('.nilai-rapor').val(hasValue ? rapor.toFixed(2).replace(/\.00$/, '') : '');
    }

    // Event listener untuk perubahan nilai input
    $(document).on('input', '.nilai-input', function() {
        hitungRow($(this).closest('.nilai-row'));
    });

    // Inisialisasi hitungan awal untuk seluruh baris
    $('.nilai-row').each(function() {
        hitungRow($(this));
    });

    // Perbarui hidden input untuk diserahkan ke server
    function updateLabel(type, idx, element) {
        const value = $(element).text().trim() || (type === 'tugas' ? 'Tugas ' + (idx + 1) : 'UH ' + (idx + 1));
        $(element).text(value);
        if (type === 'tugas') {
            $(`#hiddenLabelsContainer .lbl-tugas-input`).eq(idx).val(value);
        } else {
            $(`#hiddenLabelsContainer .lbl-uh-input`).eq(idx).val(value);
        }
    }

    // Hapus Kolom
    function removeCol(type, idx) {
        if (confirm('Apakah Anda yakin ingin menghapus kolom ini? Seluruh nilai di kolom ini akan hilang.')) {
            // Hapus dari sub-header
            if (type === 'tugas') {
                $(`.th-tugas[data-index="${idx}"]`).remove();
                $(`.td-tugas[data-index="${idx}"]`).remove();
                $(`#hiddenLabelsContainer .lbl-tugas-input`).eq(idx).remove();

                // Re-indexing
                reindexCols('tugas');
            } else {
                $(`.th-uh[data-index="${idx}"]`).remove();
                $(`.td-uh[data-index="${idx}"]`).remove();
                $(`#hiddenLabelsContainer .lbl-uh-input`).eq(idx).remove();

                // Re-indexing
                reindexCols('uh');
            }

            // Recalculate colspan
            updateColspans();

            // Hitung ulang semua baris
            $('.nilai-row').each(function() {
                hitungRow($(this));
            });
        }
    }

    // Susun ulang indeks kolom setelah penghapusan
    function reindexCols(type) {
        if (type === 'tugas') {
            let count = 0;
            $('.th-tugas').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('.th-action-btn').attr('onclick', `removeCol('tugas', ${i})`);
                $(this).find('.editable-header').attr('onblur', `updateLabel('tugas', ${i}, this)`);
                count++;
            });
            $('.nilai-row').each(function() {
                $(this).find('.td-tugas').each(function(i) {
                    $(this).attr('data-index', i);
                    $(this).find('input').attr('name', `nilai[${$(this).closest('.nilai-row').data('siswa-id')}][extra_tugas][${i}]`);
                });
            });
            // Update hidden input class/index
            $('#hiddenLabelsContainer .lbl-tugas-input').each(function(i) {
                $(this).attr('name', 'labels_tugas[]');
            });
            // Tampilkan tombol hapus jika kolom > 1
            if (count <= 1) {
                $('.th-tugas .th-action-btn').remove();
            }
        } else {
            let count = 0;
            $('.th-uh').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('.th-action-btn').attr('onclick', `removeCol('uh', ${i})`);
                $(this).find('.editable-header').attr('onblur', `updateLabel('uh', ${i}, this)`);
                count++;
            });
            $('.nilai-row').each(function() {
                $(this).find('.td-uh').each(function(i) {
                    $(this).attr('data-index', i);
                    $(this).find('input').attr('name', `nilai[${$(this).closest('.nilai-row').data('siswa-id')}][extra_uh][${i}]`);
                });
            });
            // Update hidden input
            $('#hiddenLabelsContainer .lbl-uh-input').each(function(i) {
                $(this).attr('name', 'labels_uh[]');
            });
            // Tampilkan tombol hapus jika kolom > 1
            if (count <= 1) {
                $('.th-uh .th-action-btn').remove();
            }
        }
    }

    // Perbarui Colspan parent header
    function updateColspans() {
        const countTugas = $('.th-tugas').length;
        $('#headerTugasParent').attr('colspan', countTugas);

        const countUh = $('.th-uh').length;
        $('#headerUhParent').attr('colspan', countUh);
    }

    // Tambah Kolom Tugas
    $('#addTugasBtn').on('click', function() {
        const newIdx = $('.th-tugas').length;
        const colTitle = 'Tugas ' + (newIdx + 1);

        // Tambah hidden input
        $('#hiddenLabelsContainer').append(`<input type="hidden" name="labels_tugas[]" class="lbl-tugas-input" value="${colTitle}">`);

        // Tambah th
        const newTh = `<th class="text-center th-tugas" data-index="${newIdx}" width="100">
            <span class="editable-header" contenteditable="true" onblur="updateLabel('tugas', ${newIdx}, this)">${colTitle}</span>
            <button type="button" class="btn btn-xs btn-danger th-action-btn" onclick="removeCol('tugas', ${newIdx})">&times;</button>
        </th>`;

        // Sisipkan th di akhir kolom tugas sebelum th uh pertama
        if ($('.th-tugas').length > 0) {
            $('.th-tugas').last().after(newTh);
        } else {
            $('#subHeaderRow').prepend(newTh);
        }

        // Tambah td input di setiap baris siswa
        $('.nilai-row').each(function() {
            const siswaId = $(this).data('siswa-id');
            const newTd = `<td class="td-tugas" data-index="${newIdx}">
                <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input sub-tugas" name="nilai[${siswaId}][extra_tugas][${newIdx}]" value="">
            </td>`;
            if ($(this).find('.td-tugas').length > 0) {
                $(this).find('.td-tugas').last().after(newTd);
            } else {
                $(this).prepend(newTd); // fallback
            }
        });

        reindexCols('tugas');
        updateColspans();
    });

    // Tambah Kolom Ujian Harian (UH)
    $('#addUhBtn').on('click', function() {
        const newIdx = $('.th-uh').length;
        const colTitle = 'UH ' + (newIdx + 1);

        // Tambah hidden input
        $('#hiddenLabelsContainer').append(`<input type="hidden" name="labels_uh[]" class="lbl-uh-input" value="${colTitle}">`);

        // Tambah th
        const newTh = `<th class="text-center th-uh" data-index="${newIdx}" width="100">
            <span class="editable-header" contenteditable="true" onblur="updateLabel('uh', ${newIdx}, this)">${colTitle}</span>
            <button type="button" class="btn btn-xs btn-danger th-action-btn" onclick="removeCol('uh', ${newIdx})">&times;</button>
        </th>`;

        // Sisipkan th
        if ($('.th-uh').length > 0) {
            $('.th-uh').last().after(newTh);
        } else if ($('.th-tugas').length > 0) {
            $('.th-tugas').last().after(newTh);
        } else {
            $('#subHeaderRow').prepend(newTh);
        }

        // Tambah td input di setiap baris siswa
        $('.nilai-row').each(function() {
            const siswaId = $(this).data('siswa-id');
            const newTd = `<td class="td-uh" data-index="${newIdx}">
                <input type="number" min="0" max="100" step="0.01" class="form-control text-center nilai-input sub-uh" name="nilai[${siswaId}][extra_uh][${newIdx}]" value="">
            </td>`;
            if ($(this).find('.td-uh').length > 0) {
                $(this).find('.td-uh').last().after(newTd);
            } else if ($(this).find('.td-tugas').length > 0) {
                $(this).find('.td-tugas').last().after(newTd);
            } else {
                $(this).prepend(newTd);
            }
        });

        reindexCols('uh');
        updateColspans();
    });
</script>