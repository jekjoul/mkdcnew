<?php include viewPath('includes/header'); ?>
<?php
$siswa_pembelajaran = [];
$siswa_belum_masuk = [];
$target_rombel = trim($pembelajaran->nama_tingkat . ' - ' . $pembelajaran->nama_rombel);

foreach ($siswa as $s) {
    if (in_array($s->id_siswa, $siswa_terpilih)) {
        $siswa_pembelajaran[] = $s;
    } else {
        $siswa_belum_masuk[] = $s;
    }
}

usort($siswa_belum_masuk, function ($a, $b) use ($target_rombel, $pembelajaran) {
    $score_a = pembelajaran_rombel_similarity_score($a->rombel, $target_rombel, $pembelajaran->nama_rombel);
    $score_b = pembelajaran_rombel_similarity_score($b->rombel, $target_rombel, $pembelajaran->nama_rombel);

    if ($score_a === $score_b) {
        return strcasecmp($a->nama_siswa, $b->nama_siswa);
    }

    return $score_b - $score_a;
});

function pembelajaran_rombel_similarity_score($rombel, $target_rombel, $nama_rombel)
{
    $rombel = strtolower(trim((string) $rombel));
    $target_rombel = strtolower(trim((string) $target_rombel));
    $nama_rombel = strtolower(trim((string) $nama_rombel));

    if ($rombel === '') {
        return 0;
    }
    if ($rombel === $target_rombel) {
        return 100;
    }
    if ($rombel === $nama_rombel) {
        return 90;
    }
    if (strpos($rombel, $nama_rombel) !== false || strpos($nama_rombel, $rombel) !== false) {
        return 75;
    }

    similar_text($rombel, $target_rombel, $percent_target);
    similar_text($rombel, $nama_rombel, $percent_name);
    return (int) max($percent_target, $percent_name);
}

function pembelajaran_siswa_item($s, $show_detail = false)
{
    $id = htmlspecialchars($s->id_siswa, ENT_QUOTES, 'UTF-8');
    $nama = htmlspecialchars($s->nama_siswa, ENT_QUOTES, 'UTF-8');
    $nisn = !empty($s->nisn) ? htmlspecialchars($s->nisn, ENT_QUOTES, 'UTF-8') : '-';
    $rombel = !empty($s->rombel) ? htmlspecialchars($s->rombel, ENT_QUOTES, 'UTF-8') : '-';
?>
    <li class="siswa-item list-group-item d-flex align-items-center gap-3" data-id="<?php echo $id ?>">
        <span class="drag-handle w-32-px h-32-px bg-neutral-100 rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0">
            <iconify-icon icon="lucide:grip-vertical"></iconify-icon>
        </span>
        <div class="flex-grow-1 min-w-0">
            <div class="fw-semibold text-primary-light text-truncate"><?php echo $nama ?></div>
            <div class="siswa-extra-detail d-flex flex-wrap gap-2 mt-1 text-sm text-secondary-light <?php echo $show_detail ? '' : 'd-none' ?>">
                <span>NISN: <?php echo $nisn ?></span>
                <span>Rombel saat ini: <?php echo $rombel ?></span>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary btn-move-siswa flex-shrink-0">
            <iconify-icon icon="lucide:move-horizontal"></iconify-icon>
        </button>
    </li>
<?php
}
?>
<style>
    .siswa-roster-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 16px;
    }

    .siswa-dropzone {
        min-height: 360px;
        max-height: 560px;
        overflow-y: auto;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        padding: 12px;
        background: #f9fafb;
    }

    .siswa-dropzone .list-group-item {
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid #e5e7eb;
    }

    .siswa-placeholder {
        min-height: 58px;
        border: 1px dashed #2563eb;
        border-radius: 8px;
        background: #eff6ff;
        margin-bottom: 8px;
    }

    .drag-handle {
        cursor: grab;
    }

    .ui-sortable-helper {
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
    }

    @media (max-width: 991.98px) {
        .siswa-roster-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="dashboard-main-body">
    <form action="<?php echo url('pembelajaran/simpan_siswa/' . $pembelajaran->id_pembelajaran); ?>" method="post" id="formSiswaPembelajaran">
        <div class="card mb-4">
            <div class="card-header bg-neutral-300">
                <h6 class="text-dark mb-0">Daftar Siswa Pembelajaran</h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tahun/Semester</span>
                        <strong><?php echo $pembelajaran->tahun_pelajaran ?> (<?php echo $pembelajaran->semester ?>)</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Lembaga</span>
                        <strong><?php echo $pembelajaran->nama_lembaga ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Tingkat</span>
                        <strong><?php echo $pembelajaran->nama_tingkat ?></strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-secondary-light d-block">Rombel</span>
                        <strong><?php echo $pembelajaran->nama_rombel ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="siswa-roster-grid">
            <div class="card">
                <div class="card-header bg-neutral-100 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Siswa Pembelajaran</h6>
                    <span class="badge bg-success-100 text-success-600" id="countSiswaPembelajaran"><?php echo count($siswa_pembelajaran) ?> siswa</span>
                </div>
                <div class="card-body">
                    <ul class="list-group siswa-dropzone siswa-sortable" id="siswaPembelajaran">
                        <?php foreach ($siswa_pembelajaran as $s): ?>
                            <?php pembelajaran_siswa_item($s); ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-center text-secondary-light py-4 empty-state <?php echo !empty($siswa_pembelajaran) ? 'd-none' : '' ?>" data-target="siswaPembelajaran">
                        Tarik siswa dari daftar belum masuk ke area ini.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-neutral-100 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Siswa Belum Masuk Pembelajaran</h6>
                    <span class="badge bg-info-100 text-info-600" id="countSiswaBelumMasuk"><?php echo count($siswa_belum_masuk) ?> siswa</span>
                </div>
                <div class="card-body">
                    <ul class="list-group siswa-dropzone siswa-sortable" id="siswaBelumMasuk">
                        <?php foreach ($siswa_belum_masuk as $s): ?>
                            <?php pembelajaran_siswa_item($s, true); ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-center text-secondary-light py-4 empty-state <?php echo !empty($siswa_belum_masuk) ? 'd-none' : '' ?>" data-target="siswaBelumMasuk">
                        Semua siswa sudah masuk pembelajaran ini.
                    </div>
                </div>
            </div>
        </div>

        <div id="selectedSiswaInputs"></div>

        <div class="mt-4 text-end">
            <a href="<?php echo url('pembelajaran') ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary-600 px-4">Simpan Siswa</button>
        </div>
    </form>
</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $(function() {
        function syncSiswaInputs() {
            const inputContainer = $('#selectedSiswaInputs');
            inputContainer.empty();

            $('#siswaPembelajaran .siswa-item').each(function() {
                $('<input>', {
                    type: 'hidden',
                    name: 'siswa[]',
                    value: $(this).data('id')
                }).appendTo(inputContainer);
            });

            $('#countSiswaPembelajaran').text($('#siswaPembelajaran .siswa-item').length + ' siswa');
            $('#countSiswaBelumMasuk').text($('#siswaBelumMasuk .siswa-item').length + ' siswa');

            $('.empty-state').each(function() {
                const target = $('#' + $(this).data('target'));
                $(this).toggleClass('d-none', target.find('.siswa-item').length > 0);
            });

            $('#siswaPembelajaran .siswa-extra-detail').addClass('d-none');
            $('#siswaBelumMasuk .siswa-extra-detail').removeClass('d-none');
        }

        $('.siswa-sortable').sortable({
            connectWith: '.siswa-sortable',
            handle: '.drag-handle',
            placeholder: 'siswa-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            receive: syncSiswaInputs,
            update: syncSiswaInputs
        }).disableSelection();

        $(document).on('click', '.btn-move-siswa', function() {
            const item = $(this).closest('.siswa-item');
            const target = item.closest('#siswaPembelajaran').length ? $('#siswaBelumMasuk') : $('#siswaPembelajaran');
            item.appendTo(target);
            syncSiswaInputs();
        });

        syncSiswaInputs();
    });
</script>
