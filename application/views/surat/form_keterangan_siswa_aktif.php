<!-- Load Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-neutral-900 mb-4">Buat Surat Keterangan Siswa Aktif SMP</h5>
            <p class="text-secondary-light text-sm mb-0">Isi formulir di bawah ini untuk menerbitkan Surat Keterangan Siswa Aktif resmi.</p>
        </div>
        <div>
            <a href="<?php echo url('surat/buat_otomatis') ?>" class="btn btn-outline-neutral-400 text-neutral-700 py-9 px-16 radius-8 d-flex align-items-center gap-2 text-sm fw-medium">
                <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
                <span>Kembali ke Template</span>
            </a>
        </div>
    </div>

    <form action="<?php echo url('surat/keluar_simpan') ?>" method="post" enctype="multipart/form-data" id="formSiswaAktif">
        <input type="hidden" name="id_surat_keluar" value="<?php echo @$row->id_surat_keluar ?>">
        <input type="hidden" name="token_validasi" value="<?php echo @$row->token_validasi ?>">
        <input type="hidden" name="metode_pembuatan" value="Otomatis">
        <input type="hidden" name="jenis_template" value="keterangan_siswa_aktif">
        <input type="hidden" name="id_kode_surat" id="kodeSelect" value="<?php echo @$kode_surat_smp->id_kode_surat ?>">
        <input type="hidden" name="perihal" value="Surat Keterangan Siswa Aktif">
        <input type="hidden" name="tujuan_surat" value="Siswa Bersangkutan">

        <div class="card border-0 shadow-xs radius-16 mb-4">
            <div class="card-header bg-warning-900 py-16 px-24">
                <h6 class="mb-0 text-light fw-bold">Formulir Surat Keterangan Siswa Aktif SMP</h6>
            </div>
            <div class="card-body p-24">
                <div class="row gy-4">
                    <!-- Dropdown Kop Surat Tersimpan -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-neutral-900">Pilih Kop Surat <span class="text-danger">*</span></label>
                        <select name="id_kop_surat" id="kopSelect" class="form-select radius-8 py-10" required>
                            <?php foreach ($kop_list as $kp): 
                                $isSmpDefault = (strpos(strtolower($kp->nama_kop), 'smp') !== false || $kp->id_kop_surat == @$kop_smp->id_kop_surat);
                            ?>
                                <option value="<?php echo $kp->id_kop_surat ?>" 
                                        data-nama="<?php echo htmlspecialchars($kp->nama_kop) ?>"
                                        data-naungan="<?php echo htmlspecialchars($kp->naungan ?: 'PEMERINTAH KABUPATEN CIAMIS') ?>"
                                        data-naungan2="<?php echo htmlspecialchars($kp->naungan_2 ?: 'DINAS PENDIDIKAN') ?>"
                                        data-lembaga="<?php echo htmlspecialchars($kp->nama_lembaga ?: 'SMP MIFTAHUL KHOER BOARDING SCHOOL') ?>"
                                        data-sub="<?php echo htmlspecialchars($kp->sub_nama ?: '') ?>"
                                        data-alamat="<?php echo htmlspecialchars($kp->alamat ?: '') ?>"
                                        data-kontak="<?php echo htmlspecialchars($kp->kontak ?: '') ?>"
                                        data-logo="<?php echo !empty($kp->logo) ? url('uploads/kop_logo/' . $kp->logo) : url('assets/images/logodc_round.png') ?>"
                                        data-logo-kanan="<?php echo !empty($kp->logo_kanan) ? url('uploads/kop_logo/' . $kp->logo_kanan) : (!empty($kp->logo) ? url('uploads/kop_logo/' . $kp->logo) : url('assets/images/logodc_round.png')) ?>"
                                        <?php echo $isSmpDefault ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($kp->nama_kop) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="p-12 radius-12 bg-neutral-50 border d-flex align-items-center gap-16">
                            <div class="w-48-px h-48-px radius-10 bg-primary-100 d-flex align-items-center justify-content-center text-primary-600 text-2xl">
                                <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-secondary-light text-xs d-block">Nomor Surat (Otomatis dari Sistem)</span>
                                <input type="text" id="nomorSuratDisplay" class="form-control form-control-sm bg-white border-0 fw-bold text-neutral-900 p-0" readonly placeholder="Generasi nomor otomatis...">
                                <input type="hidden" name="nomor_surat" id="nomorSuratHidden">
                                <input type="hidden" name="nomor_urut" id="nomorUrutHidden">
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Surat -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-neutral-900">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggalSurat" class="form-control radius-8" value="<?php echo date('Y-m-d') ?>" required>
                    </div>

                    <!-- Pilih Siswa (Select2) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-neutral-900">Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="id_siswa" id="siswaSelect" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Cari & Pilih Nama Siswa --</option>
                            <?php foreach ($siswa_list as $s): ?>
                                <option value="<?php echo $s->id_siswa ?>"
                                        data-nama="<?php echo htmlspecialchars($s->nama_siswa) ?>"
                                        data-nisn="<?php echo htmlspecialchars($s->nisn ?: '-') ?>"
                                        data-rombel="<?php echo htmlspecialchars($s->rombel ?: '-') ?>"
                                        data-tempat="<?php echo htmlspecialchars($s->tempat_lahir ?: '-') ?>"
                                        data-tanggal="<?php echo $s->tanggal_lahir ?>">
                                    <?php echo htmlspecialchars($s->nama_siswa) ?> (NISN: <?php echo $s->nisn ?: '-' ?> - Rombel: <?php echo $s->rombel ?: '-' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Card Ringkasan Detail Siswa Terpilih -->
                    <div class="col-md-12" id="boxDetailSiswa" style="display: none;">
                        <div class="p-16 radius-12 bg-primary-50 border border-primary-200">
                            <h6 class="fw-bold text-primary-900 text-sm mb-12 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:user-bold" class="text-primary-600 text-lg"></iconify-icon>
                                Data Siswa Terpilih
                            </h6>
                            <div class="row g-2 text-xs">
                                <div class="col-md-3">
                                    <span class="text-secondary-light d-block">Nama Siswa:</span>
                                    <strong class="text-neutral-900" id="previewNamaSiswa">-</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-secondary-light d-block">Tempat, Tanggal Lahir:</span>
                                    <strong class="text-neutral-900" id="previewTtlSiswa">-</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-secondary-light d-block">Kelas / Rombel:</span>
                                    <strong class="text-neutral-900" id="previewRombelSiswa">-</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-secondary-light d-block">NISN:</span>
                                    <strong class="text-neutral-900" id="previewNisnSiswa">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Tanda Tangan -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-neutral-900 d-block mb-12">Metode Tanda Tangan <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-24">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe_ttd" id="ttdManual" value="manual" checked>
                                <label class="form-check-label fw-medium text-neutral-800" for="ttdManual">
                                    Tanda Tangan Manual (Cetak Tangan)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe_ttd" id="ttdDigital" value="digital">
                                <label class="form-check-label fw-medium text-neutral-800" for="ttdDigital">
                                    Tanda Tangan Digital (Upload Berkas)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Upload File TTD Digital (Kondisional) -->
                    <div class="col-md-6" id="wrapperUploadTtd" style="display: none;">
                        <label class="form-label fw-semibold text-neutral-900">Upload File Tanda Tangan Digital <small class="text-muted">(PNG / JPG transparan)</small></label>
                        <input type="file" name="file_ttd_digital" id="fileTtdDigital" class="form-control radius-8" accept="image/png, image/jpeg">
                        <div class="mt-8 text-xs text-muted">Disarankan file bertipe PNG transparan untuk hasil gambar tanda tangan yang rapi.</div>
                    </div>

                    <!-- Pejabat Penandatangan -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold d-block mb-8 text-neutral-900">Pejabat Penandatangan <span class="text-danger">*</span></label>
                        <select name="id_ptk_penandatangan[]" id="penandatanganSelect" class="form-control select2" multiple required data-placeholder="-- Pilih Pejabat Penandatangan (misal: Kepala Sekolah) --" style="width: 100%;">
                            <?php foreach ($ptk as $p): 
                                $isSelected = in_array($p->id_ptk, $selected_penandatangan);
                                $jabatanVal = isset($penandatangan_jabatan_map[$p->id_ptk]) ? $penandatangan_jabatan_map[$p->id_ptk] : 'Kepala Sekolah';
                                $gDepan = !empty($p->gelar_depan) ? trim($p->gelar_depan) . ' ' : '';
                                $gBelakang = !empty($p->gelar_belakang) ? ', ' . trim($p->gelar_belakang) : '';
                                $namaPtkLengkap = $gDepan . $p->nama_ptk . $gBelakang;
                            ?>
                                <option value="<?php echo $p->id_ptk ?>" data-nama="<?php echo htmlspecialchars($namaPtkLengkap) ?>" data-niy="<?php echo htmlspecialchars($p->niy ?: ($p->nik ?: '-')) ?>" data-jabatan="<?php echo htmlspecialchars($jabatanVal) ?>" <?php echo $isSelected ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($namaPtkLengkap) ?> (NIY/NIK: <?php echo $p->niy ?: ($p->nik ?: '-') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12" id="wrapperFormJabatan" style="display: none;">
                        <label class="form-label fw-semibold d-block mb-8 text-primary-600">Jabatan Pejabat Penandatangan Terpilih <span class="text-danger">*</span></label>
                        <div class="row g-3 p-16 bg-light radius-8 border" id="containerFormJabatan">
                            <!-- Input jabatan dinamis -->
                        </div>
                    </div>
                </div>

                <div class="mt-24 pt-20 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <a href="<?php echo url('surat/buat_otomatis') ?>" class="btn btn-neutral-200 text-neutral-700 radius-8 px-20">Batal</a>
                    
                    <div class="d-flex align-items-center gap-2">
                        <!-- Tombol Open Live Preview Modal -->
                        <button type="button" id="btnLivePreview" class="btn btn-warning-600 text-white radius-8 px-20 fw-semibold d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:eye-bold" class="text-lg"></iconify-icon>
                            <span>Pratinjau Surat (Live Preview)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- MODAL LIVE PREVIEW A4 VIRTUAL -->
<div class="modal fade" id="modalLivePreview" tabindex="-1" aria-labelledby="modalLivePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header bg-neutral-900 text-white py-16 px-24">
                <div class="d-flex align-items-center gap-12">
                    <div class="w-36-px h-36-px radius-8 bg-warning-500 text-white d-flex align-items-center justify-content-center text-xl">
                        <iconify-icon icon="solar:document-text-bold"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="modalLivePreviewLabel">Pratinjau Surat Keterangan Siswa Aktif (Live Preview)</h6>
                        <span class="text-neutral-400 text-xs">Periksa kembali tampilan dan isi surat sebelum disimpan ke sistem.</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-neutral-200" style="min-height: 500px;">
                <!-- KERTAS VIRTUAL A4 -->
                <div class="paper-preview mx-auto my-4 bg-white p-4 shadow-sm" style="position: relative; width: 210mm; min-height: 297mm; padding: 18mm 20mm 30mm 20mm; box-sizing: border-box; font-family: 'Times New Roman', Times, serif; color: #000;">
                    
                    <!-- KOP SURAT DINAMIS -->
                    <div class="kop-header pb-12 mb-20 text-center" style="border-bottom: 3px double #000;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 75px; vertical-align: middle; text-align: left;">
                                    <img src="<?php echo url('assets/images/logodc_round.png') ?>" id="pvKopLogoLeft" style="max-width: 75px; max-height: 75px;">
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase;" id="pvKopNaungan">PEMERINTAH KABUPATEN CIAMIS</div>
                                    <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;" id="pvKopNaungan2">DINAS PENDIDIKAN</div>
                                    <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; color: #000; margin-top: 2px;" id="pvKopLembaga">SMP MIFTAHUL KHOER BOARDING SCHOOL</div>
                                    <div style="font-size: 9pt; font-weight: normal; margin-top: 2px;" id="pvKopSub"></div>
                                    <div style="font-size: 8.5pt; font-weight: normal;" id="pvKopAlamat">Dusun Mandala No. 59 RT 017 RW 006 Desa Kertamandala Kec. Panjalu Kab. Ciamis</div>
                                    <div style="font-size: 8.5pt; font-weight: normal;" id="pvKopKontak">Tlp. 082120073033 Email : smpemka@gmail.com</div>
                                </td>
                                <td style="width: 75px; vertical-align: middle; text-align: right;">
                                    <img src="<?php echo url('assets/images/logodc_round.png') ?>" id="pvKopLogoRight" style="max-width: 75px; max-height: 75px;">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- JUDUL & NOMOR SURAT -->
                    <div class="text-center mb-24" style="margin-top: 25px;">
                        <h4 style="margin: 0; font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; font-family: 'Times New Roman', serif;">SURAT KETERANGAN</h4>
                        <div style="font-size: 12px; margin-top: 4px;" id="pvNomorSurat">Nomor : -</div>
                    </div>

                    <!-- REDAKSI PEMBUKA DENGAN INDENTASI 1 TAB -->
                    <div style="font-size: 12px; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 16px;">
                        Yang bertanda tangan dibawah ini, <span class="pvLembagaText">SMP Miftahul Khoer Boarding School</span> menerangkan bahwa :
                    </div>

                    <!-- TABEL DATA SISWA -->
                    <table style="width: 90%; margin-left: 30px; margin-bottom: 20px; font-size: 12px; line-height: 1.8; border-collapse: collapse;">
                        <tr>
                            <td style="width: 170px; vertical-align: top;">Nama</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
                            <td style="font-weight: bold; vertical-align: top;" id="pvNamaSiswa">-</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Tempat tanggal lahir</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;" id="pvTtlSiswa">-</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Kelas</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;" id="pvRombelSiswa">-</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">NISN</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;" id="pvNisnSiswa">-</td>
                        </tr>
                    </table>

                    <!-- REDAKSI PENUTUP DENGAN INDENTASI 1 TAB -->
                    <div style="font-size: 12px; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 16px;">
                        Yang bersangkutan adalah benar-benar Siswa <span class="pvLembagaText">SMP Miftahul Khoer Boarding School</span> Panjalu Ciamis.
                    </div>
                    <div style="font-size: 12px; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 35px;">
                        Demikian Surat Keterangan ini kami buat dengan sebenarnya, dan diberikan kepada yang bersangkutan dipergunakan sebaik-baiknya.
                    </div>

                    <!-- BLOK TANDA TANGAN DENGAN OVERLAY STEMPEL / TTD DIGITAL (BEHIND / OVER TEXT) & NAMA HURUF KAPITAL -->
                    <?php 
                    $default_kab = !empty($lembaga_smp->kabupaten) ? $lembaga_smp->kabupaten : '';
                    if (empty($default_kab) && !empty($lembaga_smp->alamat)) {
                        if (preg_match('/((?:Kab\.|Kabupaten|Kota)\s+[A-Za-z\s]+?)(?:[\.,\d]|$)/i', $lembaga_smp->alamat, $m)) {
                            $default_kab = $m[1];
                        }
                    }
                    $default_kab_formatted = ucwords(strtolower(trim($default_kab ?: 'Kab. Ciamis')));
                    ?>
                    <div style="float: right; width: 280px; text-align: left; font-size: 12px; line-height: 1.4; margin-bottom: 30px; position: relative;">
                        <!-- Image TTD Digital Overlay Behind / Over Text (Presisi Seperti Stempel Asli) -->
                        <img src="" id="pvImgTtdDigital" style="position: absolute; left: -35px; top: 15px; width: 190px; max-height: 120px; object-fit: contain; pointer-events: none; z-index: 1; opacity: 0.92; display: none;" alt="TTD Digital">

                        <div style="position: relative; z-index: 2;">
                            <div><span id="pvLokasiTtd" data-default-kab="<?php echo htmlspecialchars($default_kab_formatted) ?>"><?php echo htmlspecialchars($default_kab_formatted) ?></span>, <span id="pvTanggalSurat">30 Juli 2026</span></div>
                            <div id="pvJabatanPenandatangan">Kepala Sekolah,</div>
                            
                            <div style="height: 65px;"></div>

                            <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;" id="pvNamaPenandatangan">-</div>
                            <div id="pvNiyPenandatangan">NIY. -</div>
                        </div>
                    </div>

                    <div style="clear: both;"></div>

                    <!-- FOOTER VALIDASI FIXED DI PALING BAWAH KERTAS A4 -->
                    <div style="position: absolute; bottom: 15mm; left: 20mm; right: 20mm; border-top: none; padding-top: 0; display: flex; align-items: center; gap: 14px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=MiftahulKhoerDataCenter" style="width: 58px; height: 58px;" alt="QR Code">
                        <div style="font-size: 8.5pt; color: #6b7280; line-height: 1.45; font-family: Arial, sans-serif;">
                            <div><strong>Dokumen ini dikeluarkan dan disahkan melalui aplikasi Miftahul Khoer Data Center.</strong></div>
                            <div>Validasi surat melalui Scan QR Code disamping.</div>
                            <div id="pvFooterNomorSurat">Nomor: -</div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-white py-16 px-24 border-top">
                <button type="button" class="btn btn-neutral-300 text-neutral-800 radius-8 px-20" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:pen-linear" class="text-md"></iconify-icon>
                    <span>Edit Kembali</span>
                </button>
                <button type="button" id="btnConfirmSubmit" class="btn btn-primary-600 radius-8 px-24 fw-semibold d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                    <span>Konfirmasi & Simpan Surat</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php include viewPath('includes/footer'); ?>

<!-- Load Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#siswaSelect, #penandatanganSelect').select2({
                placeholder: "-- Pilih Data --",
                allowClear: true
            });
        }

        // Handle TTD Digital radio toggle
        $('input[name="tipe_ttd"]').on('change', function() {
            if ($(this).val() === 'digital') {
                $('#wrapperUploadTtd').slideDown(200);
            } else {
                $('#wrapperUploadTtd').slideUp(200);
            }
        });

        // Handle Penandatangan Dynamic Jabatan Forms
        function renderJabatanForms() {
            const selectedVals = $('#penandatanganSelect').val() || [];
            const container = $('#containerFormJabatan');
            const wrapper = $('#wrapperFormJabatan');

            if (selectedVals.length === 0) {
                container.empty();
                wrapper.hide();
                return;
            }

            wrapper.show();

            const existingValues = {};
            container.find('.input-jabatan-field').each(function() {
                const ptkId = $(this).data('ptk-id');
                existingValues[ptkId] = $(this).val();
            });

            container.empty();

            selectedVals.forEach(function(ptkId) {
                const option = $('#penandatanganSelect option[value="' + ptkId + '"]');
                const namaPtk = option.data('nama') || 'Pejabat';
                const defaultJabatan = option.data('jabatan') || 'Kepala Sekolah';
                const currentVal = (existingValues[ptkId] !== undefined) ? existingValues[ptkId] : defaultJabatan;

                const html = `
                    <div class="col-md-6 item-jabatan-group" data-ptk-id="${ptkId}">
                        <div class="p-12 border radius-8 bg-white shadow-xs">
                            <label class="form-label fw-semibold text-primary-600 mb-1">
                                Jabatan untuk: <strong>${namaPtk}</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="jabatan_penandatangan[${ptkId}]" 
                                   class="form-control input-jabatan-field" 
                                   data-ptk-id="${ptkId}" 
                                   placeholder="Ketik Jabatan (misal: Kepala Sekolah)" 
                                   value="${currentVal}" 
                                   required>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        $('#penandatanganSelect').on('change', renderJabatanForms);
        renderJabatanForms();

        // Auto Generate Nomor Surat
        function generateNomorSurat() {
            var id_kode_surat = $('#kodeSelect').val();
            var tanggal_surat = $('#tanggalSurat').val();
            var exclude_id = '<?php echo @$row->id_surat_keluar ?: 0 ?>';

            if (!id_kode_surat) return;

            $.getJSON('<?php echo url("surat/get_next_nomor_ajax") ?>', {
                id_kode_surat: id_kode_surat,
                tanggal_surat: tanggal_surat,
                exclude_id: exclude_id
            }, function(res) {
                if (res.nomor_surat) {
                    $('#nomorSuratDisplay').val(res.nomor_surat);
                    $('#nomorSuratHidden').val(res.nomor_surat);
                    $('#nomorUrutHidden').val(res.nomor_urut);
                }
            });
        }

        $('#tanggalSurat').on('change', generateNomorSurat);
        generateNomorSurat();

        // Update Detail Siswa Terpilih
        $('#siswaSelect').on('change', function() {
            const id_siswa = $(this).val();
            if (!id_siswa) {
                $('#boxDetailSiswa').hide();
                return;
            }

            $.getJSON('<?php echo url("surat/get_siswa_detail_ajax") ?>', { id_siswa: id_siswa }, function(res) {
                if (res.success) {
                    $('#previewNamaSiswa').text(res.nama_siswa);
                    $('#previewTtlSiswa').text(res.ttl_formatted);
                    $('#previewRombelSiswa').text(res.rombel);
                    $('#previewNisnSiswa').text(res.nisn);
                    $('#boxDetailSiswa').slideDown(200);
                }
            });
        });

        // Trigger live preview modal
        $('#btnLivePreview').on('click', function() {
            const id_siswa = $('#siswaSelect').val();
            if (!id_siswa) {
                alert('Peringatan: Silakan pilih siswa terlebih dahulu!');
                return;
            }

            const ptkVals = $('#penandatanganSelect').val() || [];
            if (ptkVals.length === 0) {
                alert('Peringatan: Silakan pilih Pejabat Penandatangan!');
                return;
            }

            // Formatter Nama Lembaga (SMP KAPITAL, Sisanya Title Case)
            function formatNamaLembaga(str) {
                if (!str) return 'SMP Miftahul Khoer Boarding School';
                const words = str.trim().split(/\s+/);
                const acronyms = ['SMP', 'SMA', 'SMK', 'MTS', 'MA', 'SD', 'MI'];
                return words.map(w => {
                    const u = w.toUpperCase();
                    if (acronyms.includes(u)) return u;
                    return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
                }).join(' ');
            }

            // Extract Kabupaten (Title Case) dari Teks Alamat tanpa membuang imbuhan
            function getKabupatenFromAddress(alamat) {
                if (alamat) {
                    let match = alamat.match(/((?:Kab\.|Kabupaten|Kota)\s+[A-Za-z\s]+?)(?:[\.,\d]|$)/i);
                    if (match && match[1]) {
                        let kab = match[1].trim();
                        return kab.split(/\s+/).map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                    }
                }
                return $('#pvLokasiTtd').data('default-kab') || 'Kab. Ciamis';
            }

            // Kop Surat Info dari Option Terpilih
            const kopOpt = $('#kopSelect option:selected');
            if (kopOpt.length) {
                $('#pvKopNaungan').text(kopOpt.data('naungan') || '');
                $('#pvKopNaungan2').text(kopOpt.data('naungan2') || '');
                $('#pvKopLembaga').text(kopOpt.data('lembaga') || 'SMP MIFTAHUL KHOER BOARDING SCHOOL');
                $('#pvKopSub').text(kopOpt.data('sub') || '');
                $('#pvKopAlamat').text(kopOpt.data('alamat') || '');
                $('#pvKopKontak').text(kopOpt.data('kontak') || '');
                $('#pvKopLogoLeft').attr('src', kopOpt.data('logo'));
                $('#pvKopLogoRight').attr('src', kopOpt.data('logo-kanan'));
                $('.pvLembagaText').text(formatNamaLembaga(kopOpt.data('lembaga')));
                $('#pvLokasiTtd').text(getKabupatenFromAddress(kopOpt.data('alamat')));
            }

            // Populate Live Modal Data
            const nomorSurat = $('#nomorSuratDisplay').val() || '-';
            $('#pvNomorSurat').text('Nomor : ' + nomorSurat);
            $('#pvFooterNomorSurat').text('Nomor: ' + nomorSurat);
            $('#pvNamaSiswa').text($('#previewNamaSiswa').text());
            $('#pvTtlSiswa').text($('#previewTtlSiswa').text());
            $('#pvRombelSiswa').text($('#previewRombelSiswa').text());
            $('#pvNisnSiswa').text($('#previewNisnSiswa').text());

            // Tanggal
            const tglVal = $('#tanggalSurat').val();
            if (tglVal) {
                const parts = tglVal.split('-');
                if (parts.length === 3) {
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const mIdx = parseInt(parts[1]) - 1;
                    $('#pvTanggalSurat').text(parts[2] + ' ' + (months[mIdx] || parts[1]) + ' ' + parts[0]);
                }
            }

            // Penandatangan (Format Nama Huruf Besar Semua)
            const ptkId = ptkVals[0];
            const ptkOpt = $('#penandatanganSelect option[value="' + ptkId + '"]');
            const namaPtk = (ptkOpt.data('nama') || '-').toUpperCase();
            const niyPtk = ptkOpt.data('niy') || '-';
            const jabatanInput = $('input[name="jabatan_penandatangan[' + ptkId + ']"]').val() || 'Kepala Sekolah,';

            $('#pvNamaPenandatangan').text(namaPtk);
            $('#pvNiyPenandatangan').text('NIY. ' + niyPtk);
            $('#pvJabatanPenandatangan').text(jabatanInput.endsWith(',') ? jabatanInput : jabatanInput + ',');

            // Image TTD Digital Preview (Overlay Behind Text)
            if ($('input[name="tipe_ttd"]:checked').val() === 'digital') {
                const fileInput = document.getElementById('fileTtdDigital');
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#pvImgTtdDigital').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(fileInput.files[0]);
                } else {
                    $('#pvImgTtdDigital').hide();
                }
            } else {
                $('#pvImgTtdDigital').hide();
            }

            $('#modalLivePreview').modal('show');
        });

        // Submit form from Live Preview modal
        $('#btnConfirmSubmit').on('click', function() {
            $('#modalLivePreview').modal('hide');
            $('#formSiswaAktif').submit();
        });
    });
</script>
