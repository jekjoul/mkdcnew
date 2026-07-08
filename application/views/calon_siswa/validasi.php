<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<div class="dashboard-main-body">
    <div class="row gy-4 mb-24">
        <div class="col-lg-12">
            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-info-600">
                    <h6 class="text-light mb-0">Validasi Daftar Ulang</h6>
                    <a href="<?php echo url('calon_siswa') ?>" class="btn btn-light-100 text-dark radius-8 px-20 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="dataTableValidasi" data-page-length="10">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Calon Siswa</th>
                                    <th>Lembaga Tujuan</th>
                                    <th class="text-center">Berkas</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Validasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($calon_siswa as $s): ?>
                                    <?php
                                    $badge = $s->status_daftar_ulang === 'Terverifikasi' ? 'bg-success-focus text-success-main' : ($s->status_daftar_ulang === 'Perbaikan' ? 'bg-warning-focus text-warning-main' : 'bg-info-focus text-info-main');
                                    $lembaga_tujuan = !empty($s->id_lembaga_tujuan) && isset($lembaga_map[$s->id_lembaga_tujuan]) ? $lembaga_map[$s->id_lembaga_tujuan] : '-';
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no++; ?></td>
                                        <td>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($s->nama_siswa, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php if ($s->id_siswa): ?><div class="text-success text-sm">Sudah menjadi siswa</div><?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($lembaga_tujuan, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $s->berkas_lengkap ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' ?> px-16 py-6 radius-4">
                                                <?php echo $s->jumlah_berkas . '/' . count($required_berkas); ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><span class="badge <?php echo $badge ?> px-16 py-6 radius-4"><?php echo $s->status_daftar_ulang; ?></span></td>
                                        <td class="text-center">
                                            <?php if (!$s->id_siswa): ?>
                                                <?php if (hasPermissions('calon_siswa_validasi')): ?>
                                                <button type="button" class="btn btn-primary-600 btn-sm radius-8 btn-validasi-calon d-inline-flex align-items-center gap-1" data-id="<?php echo $s->id_calon_siswa; ?>">
                                                    <iconify-icon icon="lucide:check-square"></iconify-icon> Validasi Data
                                                </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-success-focus text-success-main px-16 py-6 radius-4">Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-10 justify-content-center">
                                                <a href="<?php echo url('calon_siswa/upload/' . $s->id_calon_siswa); ?>" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Berkas">
                                                    <iconify-icon icon="lucide:folder-open"></iconify-icon>
                                                </a>
                                                <?php if ($s->id_siswa): ?>
                                                    <a href="<?php echo url('siswa/detail/' . $s->id_siswa); ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Lihat Siswa">
                                                        <iconify-icon icon="lucide:user-check"></iconify-icon>
                                                    </a>
                                                <?php elseif ($s->status_daftar_ulang === 'Terverifikasi'): ?>
                                                    <?php if (hasPermissions('calon_siswa_aktivasi')): ?>
                                                    <form action="<?php echo url('calon_siswa/pindahkan/' . $s->id_calon_siswa) ?>" method="post" class="d-inline">
                                                        <input type="hidden" name="confirm" value="1">
                                                        <button type="submit" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" data-bs-toggle="tooltip" title="Pindahkan Menjadi Siswa" onclick="return confirm('Pindahkan calon siswa ini menjadi siswa? Berkas yang sudah diupload akan ikut dipindahkan.')">
                                                            <iconify-icon icon="lucide:user-plus"></iconify-icon>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
    </div>
</div>

<!-- Modal Validasi Calon Siswa -->
<div class="modal fade" id="modalValidasiCalonSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom">
                <h5 class="modal-title fs-5">Validasi & Verifikasi Calon Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-24">
                <div class="row">
                    <!-- Left Column: Data Profil & Berkas -->
                    <div class="col-lg-6 border-end">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs mb-20 gap-2 border-bottom-0" id="validasiTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active btn btn-outline-primary radius-8 px-16 py-8" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil-pane" type="button" role="tab" aria-controls="profil-pane" aria-selected="true">
                                    <iconify-icon icon="lucide:user" class="me-1"></iconify-icon> Profil Calon Siswa
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn btn-outline-primary radius-8 px-16 py-8" id="berkas-tab" data-bs-toggle="tab" data-bs-target="#berkas-pane" type="button" role="tab" aria-controls="berkas-pane" aria-selected="false">
                                    <iconify-icon icon="lucide:file-text" class="me-1"></iconify-icon> Daftar Berkas
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Contents -->
                        <div class="tab-content" id="validasiTabContent">
                            <!-- Tab Profil -->
                            <div class="tab-pane fade show active" id="profil-pane" role="tabpanel" aria-labelledby="profil-tab">
                                <div class="row gy-3">
                                    <div class="col-12">
                                        <div class="bg-light p-12 radius-8">
                                            <span class="text-secondary-light text-xs d-block">Nama Lengkap</span>
                                            <span class="fw-bold text-md text-primary-light" id="val_nama">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">NISN</span>
                                        <span class="fw-semibold text-primary-light" id="val_nisn">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">NIK</span>
                                        <span class="fw-semibold text-primary-light" id="val_nik">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Tempat, Tanggal Lahir</span>
                                        <span class="fw-semibold text-primary-light" id="val_ttl">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Jenis Kelamin / Agama</span>
                                        <span class="fw-semibold text-primary-light" id="val_jk_agama">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Telepon / Email</span>
                                        <span class="fw-semibold text-primary-light" id="val_kontak">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Lembaga Tujuan</span>
                                        <span class="fw-semibold text-primary-light" id="val_lembaga">-</span>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-secondary-light text-xs d-block">Alamat Lengkap</span>
                                        <span class="fw-semibold text-primary-light" id="val_alamat">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Nama Ayah (Pekerjaan)</span>
                                        <span class="fw-semibold text-primary-light" id="val_ayah">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Nama Ibu (Pekerjaan)</span>
                                        <span class="fw-semibold text-primary-light" id="val_ibu">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Sekolah Asal</span>
                                        <span class="fw-semibold text-primary-light" id="val_sekolah_asal">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-secondary-light text-xs d-block">Status Saat Ini</span>
                                        <span class="badge px-16 py-6 radius-4" id="val_status">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Berkas -->
                            <div class="tab-pane fade" id="berkas-pane" role="tabpanel" aria-labelledby="berkas-tab">
                                <div class="table-responsive">
                                    <table class="table bordered-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nama Berkas</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="val_berkas_list">
                                            <!-- Will be populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Live File Preview -->
                    <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center bg-light-50 p-24" style="min-height: 400px; position: relative;">
                        <div id="preview-placeholder" class="text-center text-secondary-light">
                            <iconify-icon icon="lucide:file-search" style="font-size: 64px;" class="mb-16"></iconify-icon>
                            <h6>Preview Berkas</h6>
                            <p class="text-sm">Klik tombol preview berkas di tab "Daftar Berkas" untuk melihat berkas di sini.</p>
                        </div>
                        <div id="preview-container" class="w-100 h-100 d-none flex-column" style="height: 500px !important;">
                            <div class="d-flex justify-content-between align-items-center mb-12">
                                <span class="fw-semibold text-primary-light" id="preview-title">Nama Berkas</span>
                                <a href="#" id="preview-download-link" target="_blank" class="btn btn-outline-primary btn-sm radius-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="lucide:external-link"></iconify-icon> Buka Tab Baru
                                </a>
                            </div>
                            <div class="flex-grow-1 border radius-8 overflow-hidden bg-white d-flex align-items-center justify-content-center" style="height: 400px;">
                                <img id="preview-image" src="" alt="Preview Berkas" class="img-fluid d-none" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                <iframe id="preview-pdf" src="" class="w-100 h-100 d-none" border="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer py-16 px-24 border-top d-flex justify-content-between">
                <!-- Left side: Hapus Data -->
                <div>
                    <?php if (hasPermissions('calon_siswa_delete')): ?>
                    <button type="button" id="btnValHapus" class="btn btn-danger-600 radius-8 px-20 d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="lucide:trash-2"></iconify-icon> Hapus Data
                    </button>
                    <?php endif; ?>
                </div>
                <!-- Right side: Perbaiki & Verifikasi -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary radius-8 px-20" data-bs-dismiss="modal">Batal</button>
                    
                    <form id="formUpdateStatusValidasi" method="post" action="" class="d-inline">
                        <input type="hidden" name="status_daftar_ulang" id="val_status_input" value="">
                        
                        <?php if (hasPermissions('calon_siswa_perbaiki')): ?>
                        <button type="submit" id="btnValPerbaiki" class="btn btn-warning-600 text-light radius-8 px-20 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="lucide:edit-3"></iconify-icon> Perbaiki Kembali
                        </button>
                        <?php endif; ?>
                        
                        <?php if (hasPermissions('calon_siswa_verifikasi')): ?>
                        <button type="submit" id="btnValVerifikasi" class="btn btn-success-600 text-light radius-8 px-20 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="lucide:check-circle"></iconify-icon> Verifikasi
                        </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border-bottom-0">
                <h5 class="modal-title fs-5">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-24 text-center">
                <iconify-icon icon="lucide:alert-triangle" style="font-size: 48px;" class="text-danger mb-16"></iconify-icon>
                <h6 class="mb-8">Apakah Anda yakin ingin menghapus data calon siswa ini?</h6>
                <div class="text-secondary-light mb-0" id="deleteModalMessage">Data calon siswa akan dihapus permanen dari sistem.</div>
            </div>
            <div class="modal-footer py-16 px-24 border-top-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary radius-8 px-20" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnConfirmDeleteAction" class="btn btn-danger-600 radius-8 px-20">Hapus</a>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>
<script>
    let tableValidasi = new DataTable('#dataTableValidasi');
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    $(document).on('click', '.btn-validasi-calon', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let detailUrl = '<?php echo url("calon_siswa/get_detail_ajax/"); ?>' + id;
        
        // Reset preview panel
        $('#preview-placeholder').removeClass('d-none');
        $('#preview-container').addClass('d-none').removeClass('d-flex');
        $('#preview-image').addClass('d-none').attr('src', '');
        $('#preview-pdf').addClass('d-none').attr('src', '');
        
        // Active Tab reset to profil-tab
        $('#profil-tab').trigger('click');

        $.ajax({
            url: detailUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    let c = data.calon;
                    
                    // Fill profile fields
                    $('#val_nama').text(c.nama_siswa || '-');
                    $('#val_nisn').text(c.nisn || '-');
                    $('#val_nik').text(c.nik || '-');
                    $('#val_ttl').text((c.tempat_lahir || '-') + ', ' + (c.tanggal_lahir || '-'));
                    $('#val_jk_agama').text((c.jenis_kelamin || '-') + ' / ' + (c.agama || '-'));
                    $('#val_kontak').text((c.telepon || '-') + ' / ' + (c.email || '-'));
                    $('#val_lembaga').text(data.lembaga_tujuan || '-');
                    
                    let alamat = c.alamat || '';
                    if (c.rt) alamat += ' RT ' + c.rt;
                    if (c.rw) alamat += ' RW ' + c.rw;
                    if (c.id_kelurahan) alamat += ', Kel. ' + c.id_kelurahan;
                    if (c.id_kecamatan) alamat += ', Kec. ' + c.id_kecamatan;
                    if (c.id_kabupaten) alamat += ', Kab. ' + c.id_kabupaten;
                    if (c.id_provinsi) alamat += ', Prov. ' + c.id_provinsi;
                    $('#val_alamat').text(alamat || '-');
                    
                    $('#val_ayah').text((c.nama_ayah || '-') + ' (' + (c.pekerjaan_ayah || '-') + ')');
                    $('#val_ibu').text((c.nama_ibu || '-') + ' (' + (c.pekerjaan_ibu || '-') + ')');
                    $('#val_sekolah_asal').text(c.sekolah_asal || '-');
                    
                    // Status Badge
                    let badgeClass = c.status_daftar_ulang === 'Terverifikasi' ? 'bg-success-focus text-success-main' : 
                                     (c.status_daftar_ulang === 'Perbaikan' ? 'bg-warning-focus text-warning-main' : 
                                      'bg-info-focus text-info-main');
                    $('#val_status').text(c.status_daftar_ulang || '-').attr('class', 'badge px-16 py-6 radius-4 ' + badgeClass);
                    
                    // Populate berkas list table
                    let berkasHtml = '';
                    data.berkas.forEach(function(b) {
                        let statusBadge = b.status ? 
                            '<span class="badge bg-success-focus text-success-main px-12 py-4 radius-4">Ada</span>' : 
                            '<span class="badge bg-danger-focus text-danger-main px-12 py-4 radius-4">Belum</span>';
                            
                        let actionBtn = b.status ? 
                            '<button type="button" class="btn btn-info-100 text-info-600 btn-sm btn-preview-berkas-val d-inline-flex align-items-center justify-content-center p-8 radius-8" data-url="' + b.url + '" data-jenis="' + b.jenis + '"><iconify-icon icon="lucide:eye"></iconify-icon></button>' : 
                            '<button type="button" class="btn btn-light-100 text-secondary-light btn-sm d-inline-flex align-items-center justify-content-center p-8 radius-8" disabled><iconify-icon icon="lucide:eye-off"></iconify-icon></button>';
                            
                        berkasHtml += '<tr>';
                        berkasHtml += '<td>' + b.jenis + '</td>';
                        berkasHtml += '<td class="text-center">' + statusBadge + '</td>';
                        berkasHtml += '<td class="text-center">' + actionBtn + '</td>';
                        berkasHtml += '</tr>';
                    });
                    $('#val_berkas_list').html(berkasHtml);
                    
                    // Set up validation form action
                    $('#formUpdateStatusValidasi').attr('action', '<?php echo url("calon_siswa/statusUpdate/"); ?>' + c.id_calon_siswa);
                    
                    // Set up delete button
                    $('#btnValHapus').off('click').on('click', function(e) {
                        e.preventDefault();
                        
                        // Hide validation modal first
                        let valModalEl = document.getElementById('modalValidasiCalonSiswa');
                        let valModalInstance = bootstrap.Modal.getInstance(valModalEl);
                        if (valModalInstance) {
                            valModalInstance.hide();
                        }
                        
                        // Setup delete modal URL and message
                        let deleteUrl = '<?php echo url("calon_siswa/hapus/"); ?>' + c.id_calon_siswa;
                        let message = 'Data calon siswa <strong>' + c.nama_siswa + '</strong> akan dihapus secara permanen dari sistem.';
                        
                        document.getElementById('deleteModalMessage').innerHTML = message;
                        document.getElementById('btnConfirmDeleteAction').setAttribute('href', deleteUrl);
                        
                        let deleteModal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
                        deleteModal.show();
                    });
                    
                    // Open Validation Modal
                    let valModal = new bootstrap.Modal(document.getElementById('modalValidasiCalonSiswa'));
                    valModal.show();
                } else {
                    alert('Gagal mengambil data detail calon siswa.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan koneksi server.');
            }
        });
    });

    $(document).on('click', '.btn-preview-berkas-val', function(e) {
        e.preventDefault();
        let fileUrl = $(this).data('url');
        let jenis = $(this).data('jenis');
        
        $('#preview-placeholder').addClass('d-none');
        $('#preview-container').removeClass('d-none').addClass('d-flex');
        
        $('#preview-title').text(jenis);
        $('#preview-download-link').attr('href', fileUrl);
        
        // Detect PDF or Image
        let isPdf = fileUrl.toLowerCase().endsWith('.pdf');
        
        if (isPdf) {
            $('#preview-image').addClass('d-none').attr('src', '');
            $('#preview-pdf').removeClass('d-none').attr('src', fileUrl);
        } else {
            $('#preview-pdf').addClass('d-none').attr('src', '');
            $('#preview-image').removeClass('d-none').attr('src', fileUrl);
        }
    });

    $('#btnValPerbaiki').on('click', function() {
        $('#val_status_input').val('Perbaikan');
    });
    
    $('#btnValVerifikasi').on('click', function() {
        $('#val_status_input').val('Terverifikasi');
    });
</script>
