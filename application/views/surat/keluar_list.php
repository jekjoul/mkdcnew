<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="card border-0 shadow-xs radius-16 overflow-hidden">
        <div class="card-header bg-warning-900 d-flex justify-content-between align-items-center gap-3 py-16 px-24">
            <h6 class="mb-0 text-light fw-bold">Surat Keluar</h6>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Surat</th>
                            <th>Metode</th>
                            <th>Tujuan / Perihal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surat as $row):
                            $is_basah     = (strtolower($row->tipe_ttd ?? '') === 'basah');
                            $has_upload   = !empty($row->file_dokumen_basah);
                            $locked       = ($is_basah && $has_upload);

                            $edit_url = ($row->metode_pembuatan === 'Otomatis')
                                ? url('surat/keluar_edit_otomatis/' . $row->id_surat_keluar)
                                : url('surat/keluar_edit_manual/' . $row->id_surat_keluar);

                            $ext = $has_upload ? strtolower(pathinfo($row->file_dokumen_basah, PATHINFO_EXTENSION)) : '';
                            $doc_url = $has_upload ? url('uploads/dokumen_basah/' . $row->file_dokumen_basah) : '';
                        ?>
                        <tr>
                            <td class="text-nowrap"><?php echo date('d-m-Y', strtotime($row->tanggal_surat)) ?></td>
                            <td><span class="fw-semibold text-xs"><?php echo htmlspecialchars($row->nomor_surat) ?></span></td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <?php if ($row->metode_pembuatan === 'Otomatis'): ?>
                                        <span class="badge bg-success-100 text-success-600 radius-6">Otomatis</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-100 text-warning-600 radius-6">Manual</span>
                                    <?php endif; ?>

                                    <?php if ($is_basah): ?>
                                        <span class="badge bg-neutral-200 text-neutral-700 radius-6 d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:document-text-bold-duotone" class="text-xs"></iconify-icon> TTD Basah
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-100 text-primary-600 radius-6 d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:pen-bold-duotone" class="text-xs"></iconify-icon> TTD Digital
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-xs"><?php echo htmlspecialchars($row->tujuan_surat) ?></div>
                                <div class="text-secondary-light text-xs"><?php echo htmlspecialchars($row->perihal) ?></div>
                            </td>

                            <!-- KOLOM STATUS -->
                            <td class="text-center">
                                <?php if ($is_basah && !$has_upload): ?>
                                    <!-- TTD Basah belum diupload → Tombol Upload -->
                                    <button type="button"
                                            class="btn btn-sm btn-primary-600 text-white radius-8 d-inline-flex align-items-center gap-1 btn-open-upload"
                                            data-id="<?php echo $row->id_surat_keluar ?>"
                                            data-nomor="<?php echo htmlspecialchars($row->nomor_surat) ?>"
                                            title="Upload scan/foto surat yang sudah ditandatangani">
                                        <iconify-icon icon="solar:upload-bold" class="text-sm"></iconify-icon>
                                        <span>Upload TTD Basah</span>
                                    </button>
                                <?php elseif ($is_basah && $has_upload): ?>
                                    <!-- Sudah diupload → badge Diarsipkan + link lihat -->
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="badge bg-teal-100 text-teal-600 radius-6 d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:archive-check-bold-duotone" class="text-xs"></iconify-icon> Diarsipkan
                                        </span>
                                        <?php if ($ext === 'pdf'): ?>
                                            <a href="<?php echo $doc_url ?>" target="_blank" class="text-xs text-primary-600 fw-medium">Lihat PDF</a>
                                        <?php else: ?>
                                            <a href="<?php echo $doc_url ?>" target="_blank" class="text-xs text-primary-600 fw-medium">Lihat Gambar</a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Digital / non-basah -->
                                    <span class="badge bg-success-100 text-success-600 radius-6">
                                        <?php echo htmlspecialchars($row->status ?: 'Diterbitkan') ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- KOLOM AKSI DROPDOWN -->
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-neutral-400 radius-8 d-inline-flex align-items-center gap-1 dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon icon="solar:menu-dots-bold" class="text-base"></iconify-icon>
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 radius-12 p-8">
                                        <!-- Preview -->
                                        <li>
                                            <a class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8"
                                               href="<?php echo url('surat/keluar_preview/' . $row->id_surat_keluar) ?>">
                                                <iconify-icon icon="solar:eye-bold-duotone" class="text-primary-600 text-base"></iconify-icon>
                                                <span class="text-sm">Lihat Preview</span>
                                            </a>
                                        </li>

                                        <!-- Edit (dikunci jika TTD Basah sudah diupload) -->
                                        <?php if (!$locked): ?>
                                        <li>
                                            <a class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8"
                                               href="<?php echo $edit_url ?>">
                                                <iconify-icon icon="solar:pen-bold-duotone" class="text-secondary-light text-base"></iconify-icon>
                                                <span class="text-sm">Edit Surat</span>
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <li>
                                            <span class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8 text-neutral-400"
                                                  title="Surat terkunci. Hapus dokumen basah terlebih dahulu untuk mengedit.">
                                                <iconify-icon icon="solar:lock-keyhole-bold-duotone" class="text-neutral-400 text-base"></iconify-icon>
                                                <span class="text-sm">Edit (Terkunci)</span>
                                            </span>
                                        </li>
                                        <?php endif; ?>

                                        <?php if ($is_basah): ?>
                                        <li><hr class="dropdown-divider my-4"></li>
                                        <?php if (!$has_upload): ?>
                                        <!-- Upload Dokumen Basah -->
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8 btn-open-upload"
                                                    data-id="<?php echo $row->id_surat_keluar ?>"
                                                    data-nomor="<?php echo htmlspecialchars($row->nomor_surat) ?>">
                                                <iconify-icon icon="solar:upload-bold-duotone" class="text-primary-600 text-base"></iconify-icon>
                                                <span class="text-sm">Upload TTD Basah</span>
                                            </button>
                                        </li>
                                        <?php else: ?>
                                        <!-- Hapus Dokumen Basah -->
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8 btn-hapus-basah"
                                                    data-id="<?php echo $row->id_surat_keluar ?>"
                                                    data-nomor="<?php echo htmlspecialchars($row->nomor_surat) ?>"
                                                    data-url="<?php echo url('surat/keluar_hapus_basah/' . $row->id_surat_keluar) ?>">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" class="text-danger-600 text-base"></iconify-icon>
                                                <span class="text-sm text-danger-600">Hapus Arsip TTD Basah</span>
                                            </button>
                                        </li>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                        <li><hr class="dropdown-divider my-4"></li>

                                        <!-- Hapus Surat -->
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item radius-8 d-flex align-items-center gap-2 px-12 py-8 btn-delete-surat"
                                                    data-url="<?php echo url('surat/keluar_hapus/' . $row->id_surat_keluar) ?>"
                                                    data-title="Surat Keluar Nomor: '<?php echo htmlspecialchars($row->nomor_surat) ?>' (Perihal: <?php echo htmlspecialchars($row->perihal ?: '-') ?>)">
                                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="text-danger-600 text-base"></iconify-icon>
                                                <span class="text-sm text-danger-600">Hapus Surat</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Dokumen TTD Basah -->
<div class="modal fade" id="modalUploadBasah" tabindex="-1" aria-labelledby="modalUploadBasahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <!-- Header Modal Clean Dark Navy Theme -->
            <div class="modal-header bg-neutral-900 text-white px-24 py-16 border-0">
                <div>
                    <h6 class="modal-title fw-bold text-white mb-1 d-flex align-items-center gap-2" id="modalUploadBasahLabel">
                        <iconify-icon icon="solar:document-up-bold-duotone" class="text-primary-400 text-xl"></iconify-icon>
                        Upload Arsip Surat TTD Basah
                    </h6>
                    <span class="badge bg-neutral-800 text-neutral-300 border border-neutral-700 font-monospace text-xs" id="uploadBasahNomor">
                        Nomor Surat: -
                    </span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 bg-white">
                <!-- Info Banner High Contrast Clean Blue -->
                <div class="alert alert-primary-subtle bg-primary-50 border border-primary-200 radius-10 mb-20 p-16">
                    <div class="d-flex align-items-start gap-3">
                        <div class="w-36-px h-36-px radius-circle bg-primary-100 text-primary-600 d-flex align-items-center justify-content-center flex-shrink-0">
                            <iconify-icon icon="solar:info-square-bold" class="text-xl"></iconify-icon>
                        </div>
                        <div class="text-xs text-primary-900 lh-base">
                            <strong class="d-block mb-1 text-sm fw-bold text-primary-800">Petunjuk Upload Dokumen Fisik</strong>
                            Dokumen yang sudah ditandatangani basah dan distempel wajib di-scan atau difoto.
                            Setelah diupload, dokumen akan otomatis <strong>diarsipkan</strong> &amp; status surat terkunci dari pengeditan.
                            <div class="mt-4 text-primary-700">Format didukung: <strong>PDF, JPG, PNG</strong> &bull; Ukuran Maksimal: <strong>10 MB</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Dropzone Area Clean High Contrast -->
                <div id="dropZoneBasah"
                     class="border-2 border-dashed border-neutral-300 radius-12 p-32 text-center bg-neutral-50"
                     style="cursor: pointer; transition: all .2s;">
                    <div class="w-64-px h-64-px radius-circle bg-primary-50 text-primary-600 d-inline-flex align-items-center justify-content-center text-3xl mb-12">
                        <iconify-icon icon="solar:cloud-upload-bold-duotone"></iconify-icon>
                    </div>
                    <h6 class="fw-bold text-neutral-800 mb-4">Seret &amp; Lepas File Scan di Sini</h6>
                    <p class="text-secondary-light text-xs mb-20">atau pilih file dari komputer / gunakan MKDC Scanner Bridge untuk memindai dokumen</p>
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary-600 text-white radius-8 px-20 text-sm fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" id="btnPickFileBiasa">
                            <iconify-icon icon="solar:folder-open-bold-duotone" class="text-lg"></iconify-icon>
                            <span>Pilih File Scan</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary radius-8 px-20 text-sm fw-semibold d-inline-flex align-items-center gap-2" id="btnScanDirectTrigger">
                            <iconify-icon icon="solar:scanner-bold-duotone" class="text-lg text-primary-600"></iconify-icon>
                            <span>Scan Langsung (MKDC Scanner Bridge)</span>
                        </button>
                    </div>
                </div>

                <!-- Preview file terpilih -->
                <div id="previewBasahContainer" class="d-none mt-16">
                    <div class="d-flex align-items-center gap-3 p-16 border border-primary-200 radius-10 bg-primary-50">
                        <div class="w-44-px h-44-px radius-8 bg-white border border-primary-200 text-primary-600 d-flex align-items-center justify-content-center flex-shrink-0 text-2xl shadow-xs">
                            <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-sm text-neutral-900" id="previewBasahName">nama_file.jpg</div>
                            <div class="text-xs text-primary-700 font-monospace" id="previewBasahSize">0 KB</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger radius-8 d-inline-flex align-items-center gap-1" id="btnRemoveFilePick">
                            <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
                            <span>Ganti</span>
                        </button>
                    </div>
                </div>

                <!-- Input File Tersembunyi dengan class scan-enabled untuk scanner-plugin.js -->
                <div class="d-none">
                    <input type="file" id="inputFileBasah" accept="image/*,.pdf" class="scan-enabled d-none" capture="environment">
                </div>

                <!-- Upload Progress Bar -->
                <div id="uploadProgressContainer" class="d-none mt-16">
                    <div class="d-flex justify-content-between text-xs fw-semibold text-neutral-700 mb-6">
                        <span>Mengupload dokumen ke server...</span>
                        <span id="uploadProgressPct" class="text-primary-600 font-monospace">0%</span>
                    </div>
                    <div class="progress radius-6 bg-neutral-200" style="height: 8px;">
                        <div id="uploadProgressBar" class="progress-bar bg-primary-600 radius-6" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-neutral-200 px-24 py-16 bg-neutral-50 gap-2">
                <button type="button" class="btn btn-outline-neutral-400 radius-8 px-20 text-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnDoUploadBasah" class="btn btn-primary-600 text-white radius-8 px-24 text-sm fw-bold d-flex align-items-center gap-2 shadow-xs" disabled>
                    <iconify-icon icon="solar:upload-bold" class="text-base"></iconify-icon>
                    <span>Upload &amp; Simpan Arsip</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Surat -->
<div class="modal fade" id="deleteSuratModal" tabindex="-1" aria-labelledby="deleteSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-24 px-24 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 text-center">
                <div class="w-64-px h-64-px radius-circle bg-danger-100 text-danger-600 d-inline-flex align-items-center justify-content-center text-3xl mb-16 mx-auto">
                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                </div>
                <h5 class="modal-title fw-bold text-neutral-900 mb-8" id="deleteSuratModalLabel">Konfirmasi Hapus Surat</h5>
                <p class="text-secondary-light text-sm mb-0">
                    Apakah Anda yakin ingin menghapus <strong class="text-neutral-900" id="deleteSuratTarget">surat ini</strong>?<br>
                    <span class="text-danger-600 fw-medium">Tindakan ini tidak dapat dibatalkan.</span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-24 px-24 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-neutral-400 text-neutral-700 py-10 px-20 radius-8 fw-semibold text-sm" data-bs-dismiss="modal">
                    Batal
                </button>
                <a id="btnConfirmDeleteSurat" href="#" class="btn btn-danger-600 py-10 px-20 radius-8 fw-semibold text-sm d-flex align-items-center gap-2 shadow-xs">
                    <iconify-icon icon="solar:trash-bin-trash-bold" class="text-lg"></iconify-icon>
                    <span>Ya, Hapus Sekarang</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Arsip Basah -->
<div class="modal fade" id="modalHapusBasah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-body p-32 text-center">
                <div class="w-64-px h-64-px radius-circle bg-warning-100 text-warning-600 d-inline-flex align-items-center justify-content-center text-3xl mb-16 mx-auto">
                    <iconify-icon icon="solar:file-corrupted-bold-duotone"></iconify-icon>
                </div>
                <h5 class="fw-bold text-neutral-900 mb-8">Hapus Arsip TTD Basah?</h5>
                <p class="text-secondary-light text-sm mb-0" id="hapusBasahDesc">
                    File dokumen yang sudah diarsipkan akan dihapus dan surat dapat diedit kembali.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-24 px-24 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-neutral-400 text-neutral-700 py-10 px-20 radius-8 fw-semibold text-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmHapusBasah" class="btn btn-danger-600 text-white py-10 px-24 radius-8 fw-semibold text-sm d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:trash-bin-minimalistic-bold" class="text-lg"></iconify-icon>
                    <span>Hapus Arsip</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
$(function() {
    let table = new DataTable('#dataTable');

    // ===== HAPUS SURAT =====
    $(document).on('click', '.btn-delete-surat', function() {
        const deleteUrl   = $(this).data('url');
        const deleteTitle = $(this).data('title') || 'surat ini';
        $('#deleteSuratTarget').text(deleteTitle);
        $('#btnConfirmDeleteSurat').attr('href', deleteUrl);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteSuratModal')).show();
    });

    // ===== UPLOAD DOKUMEN TTD BASAH =====
    let selectedFile   = null;
    let currentSuratId = null;

    function resetUploadModal() {
        selectedFile = null;
        $('#dropZoneBasah').show();
        $('#previewBasahContainer').addClass('d-none');
        $('#uploadProgressContainer').addClass('d-none');
        $('#btnDoUploadBasah').prop('disabled', true);
        $('#inputFileBasah').val('');
    }

    $(document).on('click', '.btn-open-upload', function() {
        currentSuratId = $(this).data('id');
        const nomor    = $(this).data('nomor');
        resetUploadModal();
        $('#uploadBasahNomor').text('Nomor Surat: ' + nomor);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUploadBasah')).show();
    });

    // Pilih file dari komputer
    $('#btnPickFileBiasa').on('click', function(e) {
        e.stopPropagation();
        $('#inputFileBasah').trigger('click');
    });

    // Trigger MKDC Scanner Bridge dari scanner-plugin.js
    $('#btnScanDirectTrigger').on('click', function(e) {
        e.stopPropagation();
        const $directBtn = $('#inputFileBasah').parent().find('.btn-direct-scan');
        if ($directBtn.length > 0) {
            $directBtn.trigger('click');
        } else {
            $('#inputFileBasah').trigger('click');
        }
    });

    $('#dropZoneBasah').on('click', function(e) {
        if (!$(e.target).closest('button').length) {
            $('#inputFileBasah').trigger('click');
        }
    });

    // Drag & drop
    $('#dropZoneBasah').on('dragover dragenter', function(e) {
        e.preventDefault();
        $(this).addClass('bg-primary-50 border-primary-400');
    }).on('dragleave drop', function(e) {
        e.preventDefault();
        $(this).removeClass('bg-primary-50 border-primary-400');
        if (e.type === 'drop') {
            const f = e.originalEvent.dataTransfer.files[0];
            if (f) setSelectedFile(f);
        }
    });

    $('#inputFileBasah').on('change', function() {
        if (this.files && this.files[0]) {
            setSelectedFile(this.files[0]);
        }
    });

    function setSelectedFile(f) {
        selectedFile = f;
        $('#previewBasahName').text(f.name);
        $('#previewBasahSize').text((f.size / 1024).toFixed(1) + ' KB');
        $('#previewBasahContainer').removeClass('d-none');
        $('#dropZoneBasah').hide();
        $('#btnDoUploadBasah').prop('disabled', false);
    }

    $('#btnRemoveFilePick').on('click', function() {
        selectedFile = null;
        $('#inputFileBasah').val('');
        $('#previewBasahContainer').addClass('d-none');
        $('#dropZoneBasah').show();
        $('#btnDoUploadBasah').prop('disabled', true);
    });

    // Reset saat modal ditutup
    $('#modalUploadBasah').on('hidden.bs.modal', function() {
        resetUploadModal();
    });

    // ===== PROSES UPLOAD =====
    $('#btnDoUploadBasah').on('click', function() {
        if (!selectedFile || !currentSuratId) return;

        const formData = new FormData();
        formData.append('file_dokumen_basah', selectedFile);

        const $btn = $(this).prop('disabled', true).find('span').text('Mengupload...');
        $('#uploadProgressContainer').removeClass('d-none');

        $.ajax({
            url         : '<?php echo url("surat/keluar_upload_basah/") ?>' + currentSuratId,
            type        : 'POST',
            data        : formData,
            processData : false,
            contentType : false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgressBar').css('width', pct + '%');
                        $('#uploadProgressPct').text(pct + '%');
                    }
                });
                return xhr;
            },
            success: function(res) {
                if (res.status === 'success') {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUploadBasah')).hide();
                    location.reload();
                } else {
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                    $('#btnDoUploadBasah').prop('disabled', false).find('span').text('Upload & Simpan Arsip');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengupload.');
                $('#btnDoUploadBasah').prop('disabled', false).find('span').text('Upload & Simpan Arsip');
            }
        });
    });

    // ===== HAPUS ARSIP TTD BASAH =====
    let hapusBasahUrl = null;
    $(document).on('click', '.btn-hapus-basah', function() {
        hapusBasahUrl = '<?php echo url("surat/keluar_hapus_basah/") ?>' + $(this).data('id');
        const nomor   = $(this).data('nomor');
        $('#hapusBasahDesc').text('File arsip TTD Basah untuk Surat Nomor "' + nomor + '" akan dihapus dan surat dapat diedit kembali.');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHapusBasah')).show();
    });

    $('#btnConfirmHapusBasah').on('click', function() {
        if (!hapusBasahUrl) return;
        const $btn = $(this).prop('disabled', true);
        $.post(hapusBasahUrl, function(res) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHapusBasah')).hide();
            if (res.status === 'success') {
                location.reload();
            } else {
                alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
            }
        }, 'json').always(function() { $btn.prop('disabled', false); });
    });
});
</script>
