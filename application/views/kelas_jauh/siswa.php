<?php include viewPath('includes/header'); ?>
<?php
$siswa_kelas_jauh = [];
$siswa_belum_masuk = [];

foreach ($siswa as $s) {
    if (in_array((int) $s->id_siswa, $siswa_terpilih, true)) {
        $siswa_kelas_jauh[] = $s;
    } else {
        $siswa_belum_masuk[] = $s;
    }
}

function kelas_jauh_siswa_item($s)
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
            <div class="siswa-extra-detail d-flex flex-wrap gap-2 mt-1 text-sm text-secondary-light">
                <span>NISN: <?php echo $nisn ?></span>
                <span>Rombel: <?php echo $rombel ?></span>
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
        padding: 24px;
        text-align: center;
        color: #9ca3af;
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        background: #fff;
    }
    
    .drag-handle {
        cursor: grab;
    }
</style>

<div class="dashboard-main-body">
    <div class="card mb-4">
        <div class="card-header bg-info-900 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-light">Kelola Anggota Kelas Jauh: <?php echo html_escape($kelas_jauh->nama_kelas_jauh) ?></h6>
            <span class="badge bg-neutral-200 text-dark"><?php echo count($siswa_kelas_jauh) ?> Terdaftar</span>
        </div>
        <div class="card-body">
            <div class="siswa-roster-grid">
                <!-- Sisi Kiri: Calon Anggota (Belum Terdaftar) -->
                <div>
                    <h6 class="text-md fw-bold mb-12">Calon Anggota (Siswa Aktif)</h6>
                    <div class="mb-12">
                        <input type="text" id="search-calon" class="form-control" placeholder="Cari nama siswa atau rombel...">
                    </div>
                    <ul id="source-students" class="siswa-dropzone list-group">
                        <?php if (empty($siswa_belum_masuk)): ?>
                            <div class="siswa-placeholder">Tidak ada siswa yang tersedia</div>
                        <?php else: ?>
                            <?php foreach ($siswa_belum_masuk as $s) kelas_jauh_siswa_item($s); ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Sisi Kanan: Anggota Terdaftar -->
                <div>
                    <h6 class="text-md fw-bold mb-12">Anggota Terdaftar di Kelas Jauh</h6>
                    <div class="mb-12">
                        <input type="text" id="search-anggota" class="form-control" placeholder="Cari nama siswa atau rombel...">
                    </div>
                    <ul id="target-students" class="siswa-dropzone list-group">
                        <?php if (empty($siswa_kelas_jauh)): ?>
                            <div class="siswa-placeholder">Belum ada anggota terdaftar. Drag siswa ke sini.</div>
                        <?php else: ?>
                            <?php foreach ($siswa_kelas_jauh as $s) kelas_jauh_siswa_item($s); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Simpan form hidden -->
    <form id="form-roster" action="<?php echo url('kelas_jauh/simpan_siswa/' . $kelas_jauh->id_kelas_jauh) ?>" method="post">
        <div id="hidden-inputs">
            <?php foreach ($siswa_kelas_jauh as $s): ?>
                <input type="hidden" name="siswa[]" value="<?php echo $s->id_siswa ?>">
            <?php endforeach; ?>
        </div>
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Simpan perubahan daftar anggota</h6>
                    <p class="text-sm text-secondary-light">Perubahan daftar anggota menginduk baru akan disimpan setelah Anda menekan tombol simpan.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('kelas_jauh') ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-success text-light px-24">Simpan Anggota</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include viewPath('includes/footer'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi drag-and-drop sortable
        const sourceEl = document.getElementById('source-students');
        const targetEl = document.getElementById('target-students');

        try {
            if (typeof Sortable !== 'undefined') {
                new Sortable(sourceEl, {
                    group: 'roster',
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        updateHiddenInputs();
                    }
                });

                new Sortable(targetEl, {
                    group: 'roster',
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        updateHiddenInputs();
                    }
                });
            } else {
                console.warn("Sortable library is not loaded.");
            }
        } catch (e) {
            console.error("Failed to initialize Sortable:", e);
        }

        // Event click button move
        $(document).on('click', '.btn-move-siswa', function () {
            const li = $(this).closest('li');
            const parent = li.parent();
            if (parent.attr('id') === 'source-students') {
                $('#target-students').append(li);
            } else {
                $('#source-students').append(li);
            }
            updateHiddenInputs();
            // Trigger input search kembali setelah perpindahan agar filter pencarian tetap konsisten
            $('#search-calon').trigger('input');
            $('#search-anggota').trigger('input');
        });

        // Realtime filter pencarian calon anggota
        $('#search-calon').on('input', function () {
            const query = $(this).val().toLowerCase().trim();
            $('#source-students .siswa-item').each(function () {
                const name = $(this).find('.fw-semibold').text().toLowerCase();
                const extra = $(this).find('.siswa-extra-detail').text().toLowerCase();
                const isMatch = name.includes(query) || extra.includes(query);
                $(this).toggle(isMatch);
            });
        });

        // Realtime filter pencarian anggota terdaftar
        $('#search-anggota').on('input', function () {
            const query = $(this).val().toLowerCase().trim();
            $('#target-students .siswa-item').each(function () {
                const name = $(this).find('.fw-semibold').text().toLowerCase();
                const extra = $(this).find('.siswa-extra-detail').text().toLowerCase();
                const isMatch = name.includes(query) || extra.includes(query);
                $(this).toggle(isMatch);
            });
        });

        function updateHiddenInputs() {
            // Bersihkan placeholder jika ada item
            checkPlaceholder(sourceEl, 'Tidak ada siswa yang tersedia');
            checkPlaceholder(targetEl, 'Belum ada anggota terdaftar. Drag siswa ke sini.');

            const container = $('#hidden-inputs');
            container.empty();
            $('#target-students li').each(function () {
                const id = $(this).data('id');
                container.append(`<input type="hidden" name="siswa[]" value="${id}">`);
            });
        }

        function checkPlaceholder(el, text) {
            const list = $(el);
            list.find('.siswa-placeholder').remove();
            if (list.children('li').length === 0) {
                list.append(`<div class="siswa-placeholder">${text}</div>`);
            }
        }
    });
</script>
