<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <h6 class="text-light"><?php echo isset($judul_tabel) ? $judul_tabel : 'Data Siswa'; ?></h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- <a href="<?php echo url(!empty($is_nonaktif) ? 'siswa/all' : 'siswa/nonaktif') ?>" class="btn btn-warning-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                            <iconify-icon icon="<?php echo !empty($is_nonaktif) ? 'solar:arrow-left-linear' : 'solar:archive-linear'; ?>" class="text-xl"></iconify-icon>
                            <?php echo !empty($is_nonaktif) ? 'Kembali ke Aktif' : 'Data Tidak Aktif'; ?>
                        </a> -->
                        <?php if (empty($is_nonaktif)): ?>
                            <?php if (!empty($id_pembelajaran)): ?>
                                <button type="button" class="btn btn-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#cetakModal">
                                    <iconify-icon icon="solar:printer-linear" class="text-xl"></iconify-icon> Cetak Absensi Rombel
                                </button>
                            <?php endif; ?>
                            <?php if (hasPermissions('siswa_add') && empty($id_pembelajaran)): ?>
                                <a href="<?php echo url(isset($tambah_url) ? $tambah_url : 'siswa/siswaAdd'); ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                                    <iconify-icon icon="lucide:plus" class="text-xl"></iconify-icon> <?php echo isset($tambah_label) ? $tambah_label : 'Tambah Siswa'; ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- TAMPILAN DESKTOP (Tabel) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col" class="text-center">NISN/NIPD</th>
                                    <th scope="col">Rombel</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($siswa)): ?>
                                    <?php foreach ($siswa as $s): ?>
                                        <tr class="<?php echo ($s->pekerjaan_ayah === 'Sudah Meninggal' || $s->pekerjaan_ibu === 'Sudah Meninggal') ? 'bg-warning-50' : ''; ?>">
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td>
                                                <?php echo html_escape($s->nama_siswa); ?>
                                                <?php
                                                $CI = &get_instance();
                                                $menginduk = $CI->db->select('kj.nama_kelas_jauh')
                                                    ->from('kelas_jauh_siswa kjs')
                                                    ->join('kelas_jauh kj', 'kj.id_kelas_jauh = kjs.id_kelas_jauh')
                                                    ->where('kjs.id_siswa', $s->id_siswa)
                                                    ->get()->row();
                                                if ($menginduk):
                                                ?>
                                                    <span class="badge bg-warning-focus text-warning-main px-8 py-2 radius-4 text-xs ms-1" data-bs-toggle="tooltip" title="Menginduk di: <?php echo html_escape($menginduk->nama_kelas_jauh) ?>">Menginduk</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo ($s->nisn ?: '-') . ' / ' . ($s->nipd ?: '-'); ?></td>
                                            <td><?php echo $s->rombel; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                    <?php if (hasPermissions('siswa_view')): ?>
                                                        <a href="<?php echo url('siswa/detail/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Detail">
                                                            <iconify-icon icon="lucide:eye"></iconify-icon>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (hasPermissions('siswa_delete')): ?>
                                                        <a href="<?php echo url('siswa/hapus/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- TAMPILAN MOBILE (Accordion List + Real-time Search) -->
                    <div class="d-block d-md-none">
                        <!-- Form Search Mobile -->
                        <div class="mb-16">
                            <div class="position-relative">
                                <input type="text" id="mobileSiswaSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Nama Siswa, NISN, NIPD, atau Rombel...">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                    <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($siswa)): ?>
                            <div class="accordion custom-accordion" id="accordionSiswaMobile">
                                <?php foreach ($siswa as $idx => $s): ?>
                                    <?php
                                    $accordionId = "collapseSiswa" . $s->id_siswa;
                                    $headingId   = "headingSiswa" . $s->id_siswa;
                                    $searchableText = strtolower(html_escape($s->nama_siswa . ' ' . $s->nisn . ' ' . $s->nipd . ' ' . $s->rombel));
                                    ?>
                                    <div class="accordion-item border radius-8 mb-12 mobile-siswa-card" data-search="<?php echo $searchableText; ?>">
                                        <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                            <button class="accordion-button collapsed px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="false" aria-controls="<?php echo $accordionId; ?>">
                                                <div class="d-flex flex-column gap-1 w-100 me-12">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="text-primary-600 fw-bold"><?php echo html_escape($s->nama_siswa); ?></span>
                                                        <?php
                                                        $CI = &get_instance();
                                                        $menginduk = $CI->db->select('kj.nama_kelas_jauh')
                                                            ->from('kelas_jauh_siswa kjs')
                                                            ->join('kelas_jauh kj', 'kj.id_kelas_jauh = kjs.id_kelas_jauh')
                                                            ->where('kjs.id_siswa', $s->id_siswa)
                                                            ->get()->row();
                                                        if ($menginduk):
                                                        ?>
                                                            <span class="badge bg-warning-focus text-warning-main px-8 py-2 radius-4 text-xs">Menginduk</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-12 text-xs text-secondary-light mt-4">
                                                        <span><iconify-icon icon="solar:user-id-linear" class="me-4"></iconify-icon>NIPD: <strong><?php echo $s->nipd ?: '-'; ?></strong></span>
                                                        <span><iconify-icon icon="solar:users-group-two-rounded-linear" class="me-4"></iconify-icon>Rombel: <strong><?php echo $s->rombel ?: '-'; ?></strong></span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionSiswaMobile">
                                            <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                                <div class="row gy-2 text-xs mb-12">
                                                    <div class="col-6">
                                                        <span class="text-secondary-light d-block mb-2">NISN</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo $s->nisn ?: '-'; ?></span>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="text-secondary-light d-block mb-2">NIPD</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo $s->nipd ?: '-'; ?></span>
                                                    </div>
                                                    <div class="col-6 mt-12">
                                                        <span class="text-secondary-light d-block mb-2">Rombel / Kelas</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo $s->rombel ?: '-'; ?></span>
                                                    </div>
                                                    <div class="col-6 mt-12">
                                                        <span class="text-secondary-light d-block mb-2">Jenis Kelamin</span>
                                                        <span class="fw-semibold text-primary-light"><?php echo isset($s->jenis_kelamin) ? $s->jenis_kelamin : '-'; ?></span>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-8 mt-16 pt-12 border-top">
                                                    <?php if (hasPermissions('siswa_view')): ?>
                                                        <a href="<?php echo url('siswa/detail/' . $s->id_siswa); ?>" class="btn btn-primary-600 btn-sm radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-2 py-8">
                                                            <iconify-icon icon="lucide:eye" class="text-base"></iconify-icon> Detail Siswa
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (hasPermissions('siswa_delete')): ?>
                                                        <a href="<?php echo url('siswa/hapus/' . $s->id_siswa); ?>" class="btn btn-outline-danger btn-sm radius-8 d-flex align-items-center justify-content-center gap-2 px-12 py-8" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon> Hapus
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="noMobileSearchResult" class="text-center py-24 text-secondary-light d-none">
                                <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                                <p class="text-sm">Data siswa tidak ditemukan.</p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-24 text-secondary-light">
                                <p class="text-sm">Belum ada data siswa.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
</div>

<!-- Modal Cetak Rombel -->
<?php if (!empty($id_pembelajaran)): ?>
<div class="modal fade" id="cetakModal" tabindex="-1" aria-labelledby="cetakModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success-600">
                <h6 class="modal-title text-light" id="cetakModalLabel">Opsi Cetak Absensi Rombel</h6>
                <button type="button" class="btn-close text-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="mb-20">
                    <label class="form-label fw-bold text-primary-light mb-8">Pilihan Tampilan Dokumen:</label>
                    <div class="form-check mb-8">
                        <input class="form-check-input" type="checkbox" value="1" id="checkKop">
                        <label class="form-check-label text-sm text-secondary-light" for="checkKop">
                            Gunakan Kop Surat Lembaga
                        </label>
                    </div>
                    <div class="form-check mb-8">
                        <input class="form-check-input" type="checkbox" value="1" id="checkTtd">
                        <label class="form-check-label text-sm text-secondary-light" for="checkTtd">
                            Tampilkan Tanda Tangan (Kepala Sekolah & Wali Kelas)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="checkJumlah">
                        <label class="form-check-label text-sm text-secondary-light" for="checkJumlah">
                            Tampilkan Statistik Jumlah Siswa (L/P/Total)
                        </label>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-column gap-10 mt-15">
                    <button type="button" id="btnCetakHtml" class="btn btn-primary radius-8 d-flex align-items-center justify-content-center gap-2 py-10">
                        <iconify-icon icon="solar:printer-linear" class="text-xl"></iconify-icon> Cetak / Print HTML (A4)
                    </button>
                    <button type="button" id="btnCetakPdf" class="btn btn-danger radius-8 d-flex align-items-center justify-content-center gap-2 py-10">
                        <iconify-icon icon="solar:document-add-linear" class="text-xl"></iconify-icon> Export ke PDF
                    </button>
                    <button type="button" id="btnCetakExcel" class="btn btn-success radius-8 d-flex align-items-center justify-content-center gap-2 py-10">
                        <iconify-icon icon="lucide:file-spreadsheet" class="text-xl"></iconify-icon> Export ke Excel (.xls)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include viewPath('includes/footer'); ?>
<script>
    let table = new DataTable('#dataTable');
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-button').each(function() {
            var tooltipButton = $(this);
            var tooltipContent = $(this).siblings('.my-tooltip').html();

            // Initialize the tooltip
            tooltipButton.tooltip({
                title: tooltipContent,
                trigger: 'hover',
                html: true
            });

            // Optionally, reinitialize the tooltip if the content might change dynamically
            tooltipButton.on('mouseenter', function() {
                tooltipButton.tooltip('dispose').tooltip({
                    title: tooltipContent,
                    trigger: 'hover',
                    html: true
                }).tooltip('show');
            });
        });

        // Real-time Live Search untuk Accordion Mobile Data Siswa
        $('#mobileSiswaSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-siswa-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobileSearchResult').removeClass('d-none');
            } else {
                $('#noMobileSearchResult').addClass('d-none');
            }
        });

        <?php if (!empty($id_pembelajaran)): ?>
        function getPrintUrl(format) {
            let pakaiKop = $('#checkKop').is(':checked') ? '1' : '0';
            let pakaiTtd = $('#checkTtd').is(':checked') ? '1' : '0';
            let pakaiJumlah = $('#checkJumlah').is(':checked') ? '1' : '0';
            let id_pembelajaran = "<?php echo $id_pembelajaran; ?>";
            return "<?php echo url('pencetakan/absensi') ?>?id_pembelajaran=" + id_pembelajaran + "&format=" + format + "&pakai_kop=" + pakaiKop + "&pakai_ttd=" + pakaiTtd + "&pakai_jumlah=" + pakaiJumlah;
        }

        $('#btnCetakHtml').on('click', function() {
            let url = getPrintUrl('html');
            window.open(url, '_blank');
            $('#cetakModal').modal('hide');
        });

        $('#btnCetakPdf').on('click', function() {
            let url = getPrintUrl('pdf');
            window.open(url, '_blank');
            $('#cetakModal').modal('hide');
        });

        $('#btnCetakExcel').on('click', function() {
            let url = getPrintUrl('excel');
            window.location.href = url;
            $('#cetakModal').modal('hide');
        });
        <?php endif; ?>
    });
</script>