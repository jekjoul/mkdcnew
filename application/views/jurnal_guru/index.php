<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <!-- Breadcrumb -->
    <div class="d-none d-sm-block d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0 text-primary-light">Jurnal Pembelajaran Guru</h6>
            <p class="text-secondary-light text-sm mb-0">Catatan rekam jejak KBM yang telah dilaksanakan beserta hambatan, pemecahan, dan presensi siswa.</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="<?php echo url('dashboard') ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-secondary-light">Jurnal Guru</li>
        </ul>
    </div>

    <!-- Alert Flash Data -->
    <?php if ($this->session->flashdata('alert')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('alert-type') ?: 'info' ?> alert-dismissible fade show radius-8 mb-24" role="alert">
            <?php echo $this->session->flashdata('alert') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Card Filter Bertingkat -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-header bg-white border-bottom p-20">
            <h6 class="mb-0 text-primary-900 fw-bold d-flex align-items-center gap-2">
                <iconify-icon icon="solar:filter-bold-duotone" class="text-primary-600 text-xl"></iconify-icon>
                Filter Jurnal KBM
                <span class="badge bg-primary-50 text-primary-600 radius-4 ms-1 px-8 py-2 text-xs fw-semibold">Bertingkat</span>
            </h6>
            <p class="text-xs text-secondary-light mb-0 mt-4">
                <?php if ($is_admin): ?>
                    Pilih <strong>Tahun Pelajaran</strong> &rarr; <strong>Guru</strong> &rarr; <strong>Mata Pelajaran</strong> &rarr; <strong>Rombel</strong> secara berurutan.
                <?php else: ?>
                    Pilih <strong>Tahun Pelajaran</strong> &rarr; <strong>Mata Pelajaran</strong> &rarr; <strong>Rombel</strong> secara berurutan.
                <?php endif; ?>
            </p>
        </div>
        <div class="card-body p-20">
            <div class="row g-3 align-items-end">

                <!-- STEP 0: Tahun Pelajaran (semua role) -->
                <div class="col-md-<?php echo $is_admin ? '2' : '3' ?>">
                    <label class="form-label text-xs fw-bold text-secondary-light mb-6 d-flex align-items-center gap-1">
                        <span class="badge bg-warning-600 text-white radius-full px-8 py-3 me-1">1</span>
                        <iconify-icon icon="solar:calendar-bold" class="text-warning-500 text-sm"></iconify-icon>
                        Tahun Pelajaran
                    </label>
                    <select id="filter_tahun" class="form-select radius-8 text-sm">
                        <option value="">— Pilih Tahun Pelajaran —</option>
                        <?php foreach ($tahun_pelajaran_list as $tp): ?>
                            <option value="<?php echo $tp->id_tahun_pelajaran ?>"><?php echo html_escape($tp->label_tahun) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- STEP 1: Guru (Admin Only) -->
                <?php if ($is_admin): ?>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-secondary-light mb-6 d-flex align-items-center gap-1">
                        <span class="badge bg-primary-600 text-white radius-full px-8 py-3 me-1">2</span>
                        <iconify-icon icon="solar:user-bold" class="text-primary-500 text-sm"></iconify-icon>
                        Guru / PTK Pengampu
                        <span id="spinner-guru" class="d-none ms-1"><iconify-icon icon="svg-spinners:ring-resize" class="text-sm text-primary-600"></iconify-icon></span>
                    </label>
                    <select id="filter_ptk" class="form-select radius-8 text-sm" disabled>
                        <option value="">— Pilih Tahun dulu —</option>
                    </select>
                </div>
                <?php endif; ?>

                <!-- STEP 2: Mapel -->
                <div class="col-md-<?php echo $is_admin ? '2' : '3' ?>">
                    <label class="form-label text-xs fw-bold text-secondary-light mb-6 d-flex align-items-center gap-1">
                        <span class="badge bg-info-600 text-white radius-full px-8 py-3 me-1"><?php echo $is_admin ? '3' : '2' ?></span>
                        <iconify-icon icon="solar:book-bold" class="text-info-500 text-sm"></iconify-icon>
                        Mata Pelajaran
                        <span id="spinner-mapel" class="d-none ms-1"><iconify-icon icon="svg-spinners:ring-resize" class="text-sm text-info-600"></iconify-icon></span>
                    </label>
                    <select id="filter_mapel" class="form-select radius-8 text-sm" disabled>
                        <option value="">— Pilih <?php echo $is_admin ? 'Guru' : 'Tahun' ?> dulu —</option>
                    </select>
                </div>

                <!-- STEP 3: Rombel -->
                <div class="col-md-<?php echo $is_admin ? '2' : '3' ?>">
                    <label class="form-label text-xs fw-bold text-secondary-light mb-6 d-flex align-items-center gap-1">
                        <span class="badge bg-success-600 text-white radius-full px-8 py-3 me-1"><?php echo $is_admin ? '4' : '3' ?></span>
                        <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-success-500 text-sm"></iconify-icon>
                        Rombel / Kelas
                        <span id="spinner-rombel" class="d-none ms-1"><iconify-icon icon="svg-spinners:ring-resize" class="text-sm text-success-600"></iconify-icon></span>
                    </label>
                    <select id="filter_rombel" class="form-select radius-8 text-sm" disabled>
                        <option value="">— Pilih Mapel dulu —</option>
                    </select>
                </div>

                <!-- Tombol Reset -->
                <div class="col-md-<?php echo $is_admin ? '3' : '3' ?> d-flex gap-2">
                    <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary radius-8 px-14 py-8 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:restart-bold" class="text-lg"></iconify-icon> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jurnal -->
    <div class="card border-0 radius-12 shadow-xs mb-24">
        <div class="card-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="mb-0 text-primary-900 fw-bold d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:notebook-bold" class="text-primary-600 text-xl"></iconify-icon>
                    Daftar Jurnal Pembelajaran
                    <span id="table-loading" class="d-none ms-1">
                        <iconify-icon icon="svg-spinners:ring-resize" class="text-primary-600"></iconify-icon>
                    </span>
                </h6>
                <span class="text-xs text-secondary-light">
                    Total: <strong id="jurnal-total" class="text-primary-700">0 Kegiatan KBM</strong>
                </span>
            </div>
            <div>
                <a id="btn-cetak-header" href="#" target="_blank"
                   class="btn btn-success-600 text-white radius-8 px-16 py-8 d-none align-items-center gap-2 fw-semibold shadow-xs">
                    <iconify-icon icon="solar:printer-bold" class="text-lg"></iconify-icon> Cetak Jurnal
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table bordered-table align-middle w-100 mb-0">
                    <thead>
                        <tr class="bg-neutral-100">
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 140px;">Hari / Tanggal</th>
                            <th style="width: 160px;">Rombel</th>
                            <th style="max-width: 250px; width: 250px;">Materi &amp; Pokok Bahasan</th>
                            <th style="max-width: 200px; width: 200px;">Hambatan KBM</th>
                            <th style="max-width: 200px; width: 200px;">Pemecahan Masalah</th>
                            <th style="width: 180px;" class="text-center">Absensi Siswa</th>
                        </tr>
                    </thead>
                    <tbody id="jurnal-tbody" style="transition: opacity 0.2s;">
                        <tr id="row-placeholder">
                            <td colspan="7" class="text-center py-48 text-neutral-400">
                                <iconify-icon icon="solar:filter-bold-duotone" style="font-size: 40px;" class="text-primary-200"></iconify-icon>
                                <div class="mt-10 fw-semibold text-sm text-neutral-500">Silakan pilih filter terlebih dahulu</div>
                                <div class="text-xs text-secondary-light mt-4">
                                    <?php if ($is_admin): ?>
                                        Pilih Tahun Pelajaran &rarr; Guru &rarr; Mata Pelajaran &rarr; Rombel untuk menampilkan jurnal KBM.
                                    <?php else: ?>
                                        Pilih Tahun Pelajaran &rarr; Mata Pelajaran &rarr; Rombel untuk menampilkan jurnal KBM Anda.
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const IS_ADMIN         = <?php echo $is_admin ? 'true' : 'false' ?>;
    const AJAX_GURU_URL    = '<?php echo url('jurnal_guru/get_guru_options') ?>';
    const AJAX_MAPEL_URL   = '<?php echo url('jurnal_guru/get_mapel_options') ?>';
    const AJAX_ROMBEL_URL  = '<?php echo url('jurnal_guru/get_rombel_options') ?>';
    const AJAX_DATA_URL    = '<?php echo url('jurnal_guru/get_data') ?>';
    const CETAK_BASE_URL   = '<?php echo url('jurnal_guru/cetak') ?>';

    const selTahun   = document.getElementById('filter_tahun');
    const selPtk     = document.getElementById('filter_ptk');
    const selMapel   = document.getElementById('filter_mapel');
    const selRombel  = document.getElementById('filter_rombel');
    const spinGuru   = document.getElementById('spinner-guru');
    const spinMapel  = document.getElementById('spinner-mapel');
    const spinRombel = document.getElementById('spinner-rombel');
    const tbody      = document.getElementById('jurnal-tbody');
    const totalBadge = document.getElementById('jurnal-total');
    const tableLoad  = document.getElementById('table-loading');
    const cetakBtn   = document.getElementById('btn-cetak-header');
    const btnReset   = document.getElementById('btn-reset-filter');

    const PLACEHOLDER_HTML = document.getElementById('row-placeholder').outerHTML;

    function spin(el, show) { if (el) el.classList.toggle('d-none', !show); }

    function resetSelect(sel, placeholder) {
        if (!sel) return;
        sel.innerHTML = '<option value="">' + placeholder + '</option>';
        sel.disabled = true;
    }

    function fillSelect(sel, data, valKey, labelKey) {
        if (!sel) return;
        sel.innerHTML = '<option value="">— Semua —</option>';
        data.forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item[valKey];
            opt.textContent = item[labelKey];
            sel.appendChild(opt);
        });
        sel.disabled = (data.length === 0);
    }

    function getFilters() {
        return {
            id_tahun_pelajaran: selTahun  ? selTahun.value  : '',
            id_ptk:             IS_ADMIN && selPtk   ? selPtk.value   : '',
            id_mapel:           selMapel  ? selMapel.value  : '',
            id_rombel:          selRombel ? selRombel.value : '',
        };
    }

    function buildQS(params) {
        return Object.entries(params)
            .filter(function(e){ return e[1] !== ''; })
            .map(function(e){ return encodeURIComponent(e[0]) + '=' + encodeURIComponent(e[1]); })
            .join('&');
    }

    // ── Render tabel ─────────────────────────────────────────────────
    function renderEmpty(msg, sub) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-40 text-neutral-400">'
            + '<iconify-icon icon="solar:notebook-linear" style="font-size:36px;"></iconify-icon>'
            + '<div class="mt-8 text-sm fw-medium">' + msg + '</div>'
            + (sub ? '<div class="text-xs text-secondary-light mt-4">' + sub + '</div>' : '')
            + '</td></tr>';
        totalBadge.textContent = '0 Kegiatan KBM';
        cetakBtn.classList.add('d-none'); cetakBtn.classList.remove('d-flex');
    }

    function renderTable(data, is_admin) {
        if (data.length === 0) { renderEmpty('Tidak ada data jurnal sesuai filter.'); return; }
        tbody.innerHTML = data.map(function(row, idx) {
            const hambHtml = row.hambatan
                ? '<div class="text-xs text-danger-800 bg-danger-50 px-8 py-6 radius-6 border border-danger-200">' + row.hambatan + '</div>'
                : '<span class="text-xs text-secondary-light">- Tidak ada -</span>';
            const pecHtml = row.pemecahan
                ? '<div class="text-xs text-success-800 bg-success-50 px-8 py-6 radius-6 border border-success-200">' + row.pemecahan + '</div>'
                : '<span class="text-xs text-secondary-light">- Tidak ada -</span>';
            const guruHtml = (is_admin && row.nama_ptk)
                ? '<div class="text-xs text-secondary-light mt-1"><iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon>' + row.nama_ptk + '</div>'
                : '';
            return '<tr>'
                + '<td class="text-center fw-semibold align-middle text-secondary-light">' + (idx + 1) + '</td>'
                + '<td class="align-middle">'
                    + '<div class="fw-bold text-primary-900 text-sm">' + row.hari + '</div>'
                    + '<div class="text-xs text-secondary-light">' + row.tanggal + '</div>'
                    + '<span class="badge bg-neutral-100 text-neutral-700 text-xs mt-1">Ke-' + row.pertemuan_ke + '</span>'
                + '</td>'
                + '<td class="align-middle">'
                    + '<div class="fw-bold text-primary-900 text-sm">' + row.label_kelas + '</div>'
                    + '<div class="text-xs text-primary-600">' + row.nama_mapel + '</div>'
                    + guruHtml
                + '</td>'
                + '<td style="max-width:250px;word-wrap:break-word;white-space:normal;" class="align-middle">'
                    + '<div class="text-sm text-neutral-800 fw-medium">' + (row.materi || '<em class="text-secondary-light text-xs">-</em>') + '</div>'
                + '</td>'
                + '<td style="max-width:200px;word-wrap:break-word;white-space:normal;" class="align-middle">' + hambHtml + '</td>'
                + '<td style="max-width:200px;word-wrap:break-word;white-space:normal;" class="align-middle">' + pecHtml + '</td>'
                + '<td class="text-center align-middle">'
                    + '<div class="d-flex justify-content-center gap-1 flex-wrap">'
                    + '<span class="badge bg-success-focus text-success-main radius-4 px-8 py-4 text-xs"><strong>H:</strong> ' + row.absensi_h + '</span>'
                    + '<span class="badge bg-info-focus text-info-main radius-4 px-8 py-4 text-xs"><strong>I:</strong> ' + row.absensi_i + '</span>'
                    + '<span class="badge bg-warning-focus text-warning-main radius-4 px-8 py-4 text-xs"><strong>S:</strong> ' + row.absensi_s + '</span>'
                    + '<span class="badge bg-danger-focus text-danger-main radius-4 px-8 py-4 text-xs"><strong>A:</strong> ' + row.absensi_a + '</span>'
                    + '</div>'
                + '</td>'
                + '</tr>';
        }).join('');
        totalBadge.textContent = data.length + ' Kegiatan KBM';
    }

    // ── Load data tabel ───────────────────────────────────────────────
    function loadData() {
        const params = getFilters();
        spin(tableLoad, true);
        tbody.style.opacity = '0.4';
        const qs = buildQS(params);
        fetch(AJAX_DATA_URL + (qs ? '?' + qs : ''))
            .then(function(r){ return r.json(); })
            .then(function(res) {
                spin(tableLoad, false);
                tbody.style.opacity = '1';
                renderTable(res.data || [], res.is_admin);
                if ((res.total || 0) > 0) {
                    cetakBtn.href = CETAK_BASE_URL + (qs ? '?' + qs : '');
                    cetakBtn.classList.remove('d-none'); cetakBtn.classList.add('d-flex');
                } else {
                    cetakBtn.classList.add('d-none'); cetakBtn.classList.remove('d-flex');
                }
            })
            .catch(function(){ spin(tableLoad, false); tbody.style.opacity = '1'; });
    }

    // ── Load Guru (Admin) ─────────────────────────────────────────────
    function loadGuru(id_tahun) {
        spin(spinGuru, true);
        resetSelect(selPtk, '— Memuat guru… —');
        resetSelect(selMapel, '— Pilih Guru dulu —');
        resetSelect(selRombel, '— Pilih Mapel dulu —');
        renderEmpty('Pilih Guru untuk melanjutkan filter.');

        fetch(AJAX_GURU_URL + '?id_tahun_pelajaran=' + (id_tahun || ''))
            .then(function(r){ return r.json(); })
            .then(function(data) {
                spin(spinGuru, false);
                data.length > 0
                    ? fillSelect(selPtk, data, 'id_ptk', 'nama_ptk')
                    : resetSelect(selPtk, '— Tidak ada guru —');
            })
            .catch(function(){ spin(spinGuru, false); resetSelect(selPtk, '— Error —'); });
    }

    // ── Load Mapel ────────────────────────────────────────────────────
    function loadMapel(id_tahun, id_ptk) {
        spin(spinMapel, true);
        resetSelect(selMapel, '— Memuat mapel… —');
        resetSelect(selRombel, '— Pilih Mapel dulu —');
        renderEmpty('Pilih Mata Pelajaran untuk melanjutkan filter.');

        const qs = 'id_tahun_pelajaran=' + (id_tahun || '') + '&id_ptk=' + (id_ptk || '');
        fetch(AJAX_MAPEL_URL + '?' + qs)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                spin(spinMapel, false);
                data.length > 0
                    ? fillSelect(selMapel, data, 'id_mapel', 'nama_mapel')
                    : resetSelect(selMapel, '— Tidak ada mapel —');
            })
            .catch(function(){ spin(spinMapel, false); resetSelect(selMapel, '— Error —'); });
    }

    // ── Load Rombel ───────────────────────────────────────────────────
    function loadRombel(id_tahun, id_ptk, id_mapel) {
        spin(spinRombel, true);
        resetSelect(selRombel, '— Memuat rombel… —');
        renderEmpty('Pilih Rombel untuk menampilkan data.');

        const qs = 'id_tahun_pelajaran=' + (id_tahun || '') + '&id_ptk=' + (id_ptk || '') + '&id_mapel=' + (id_mapel || '');
        fetch(AJAX_ROMBEL_URL + '?' + qs)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                spin(spinRombel, false);
                data.length > 0
                    ? fillSelect(selRombel, data, 'id_rombel', 'label_rombel')
                    : resetSelect(selRombel, '— Tidak ada rombel —');
                // Langsung tampilkan data (semua rombel, tanpa filter rombel dulu)
                loadData();
            })
            .catch(function(){ spin(spinRombel, false); resetSelect(selRombel, '— Error —'); });
    }

    // ── EVENTS ────────────────────────────────────────────────────────

    // Tahun Pelajaran dipilih
    selTahun.addEventListener('change', function() {
        const val = this.value;
        if (!val) {
            if (IS_ADMIN) resetSelect(selPtk, '— Pilih Tahun dulu —');
            resetSelect(selMapel, '— Pilih ' + (IS_ADMIN ? 'Guru' : 'Tahun') + ' dulu —');
            resetSelect(selRombel, '— Pilih Mapel dulu —');
            tbody.innerHTML = PLACEHOLDER_HTML;
            totalBadge.textContent = '0 Kegiatan KBM';
            cetakBtn.classList.add('d-none'); cetakBtn.classList.remove('d-flex');
            return;
        }
        if (IS_ADMIN) {
            loadGuru(val);
        } else {
            // Guru non-admin: langsung load mapel berdasarkan tahun + id_ptk sendiri
            const id_ptk = ''; // handled server-side
            loadMapel(val, id_ptk);
        }
    });

    // Guru dipilih (Admin only)
    if (IS_ADMIN && selPtk) {
        selPtk.addEventListener('change', function() {
            const id_tahun = selTahun ? selTahun.value : '';
            if (!this.value) {
                resetSelect(selMapel, '— Pilih Guru dulu —');
                resetSelect(selRombel, '— Pilih Mapel dulu —');
                renderEmpty('Pilih Guru untuk melanjutkan filter.');
                return;
            }
            loadMapel(id_tahun, this.value);
        });
    }

    // Mapel dipilih
    if (selMapel) {
        selMapel.addEventListener('change', function() {
            const id_tahun = selTahun ? selTahun.value : '';
            const id_ptk   = IS_ADMIN && selPtk ? selPtk.value : '';
            if (!this.value) {
                resetSelect(selRombel, '— Pilih Mapel dulu —');
                renderEmpty('Pilih Mata Pelajaran untuk melanjutkan filter.');
                return;
            }
            loadRombel(id_tahun, id_ptk, this.value);
        });
    }

    // Rombel dipilih → refresh data
    if (selRombel) {
        selRombel.addEventListener('change', function() {
            loadData();
        });
    }

    // Reset semua filter
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            selTahun.value = '';
            if (IS_ADMIN && selPtk) resetSelect(selPtk, '— Pilih Tahun dulu —');
            resetSelect(selMapel, '— Pilih ' + (IS_ADMIN ? 'Guru' : 'Tahun') + ' dulu —');
            resetSelect(selRombel, '— Pilih Mapel dulu —');
            tbody.innerHTML = PLACEHOLDER_HTML;
            totalBadge.textContent = '0 Kegiatan KBM';
            cetakBtn.classList.add('d-none'); cetakBtn.classList.remove('d-flex');
        });
    }

})();
</script>

<?php include viewPath('includes/footer'); ?>
