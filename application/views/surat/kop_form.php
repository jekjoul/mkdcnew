<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">
    <form action="<?php echo url('surat/kop_simpan') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_kop_surat" value="<?php echo @$row->id_kop_surat ?>">
        
        <div class="row">
            <!-- Kolom Kiri: Input Form Pengaturan Kop -->
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning-900">
                        <h6 class="mb-0 text-light"><?php echo @$row ? 'Edit Kop Surat' : 'Tambah Kop Surat Baru' ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nama Pengaturan Kop <span class="text-danger">*</span></label>
                                <input type="text" name="nama_kop" class="form-control" value="<?php echo @$row->nama_kop ?>" placeholder="Contoh: Kop Surat SMP Utama" required>
                                <small class="text-secondary-light">Nama unik untuk mempermudah pemilihan kop saat membuat surat.</small>
                            </div>
                            
                            <div class="col-md-12">
                                <hr class="my-2">
                                <h6 class="text-md text-primary-light">Konten Tulisan Kop</h6>
                            </div>

                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Lembaga Naungan Baris 1</label>
                                <input type="text" name="naungan" id="in_naungan" class="form-control" value="<?php echo @$row->naungan ?>" placeholder="Contoh: DINAS PENDIDIKAN KABUPATEN CIAMIS">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ukuran Font</label>
                                <input type="number" name="font_size_naungan" id="sz_naungan" class="form-control text-center" min="8" max="40" value="<?php echo @$row->font_size_naungan ?: 11 ?>">
                            </div>

                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Lembaga Naungan Baris 2</label>
                                <input type="text" name="naungan_2" id="in_naungan_2" class="form-control" value="<?php echo @$row->naungan_2 ?>" placeholder="Contoh: KELOMPOK KERJA KEPALA SEKOLAH (K3S)">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ukuran Font</label>
                                <input type="number" name="font_size_naungan_2" id="sz_naungan_2" class="form-control text-center" min="8" max="40" value="<?php echo @$row->font_size_naungan_2 ?: 11 ?>">
                            </div>

                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Nama Lembaga Utama (Baris 2) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lembaga" id="in_lembaga" class="form-control" value="<?php echo @$row->nama_lembaga ?>" placeholder="Contoh: SMP MIFTAHUL KHOER" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ukuran Font</label>
                                <input type="number" name="font_size_lembaga" id="sz_lembaga" class="form-control text-center" min="8" max="40" value="<?php echo @$row->font_size_lembaga ?: 18 ?>">
                            </div>

                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Sub-Instansi / Keterangan Lembaga (Baris 3)</label>
                                <input type="text" name="sub_nama" id="in_sub" class="form-control" value="<?php echo @$row->sub_nama ?>" placeholder="Contoh: TERAKREDITASI A - NPSN: 12345678">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ukuran Font</label>
                                <input type="number" name="font_size_sub" id="sz_sub" class="form-control text-center" min="8" max="40" value="<?php echo @$row->font_size_sub ?: 13 ?>">
                            </div>

                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Alamat Lengkap</label>
                                <textarea name="alamat" id="in_alamat" class="form-control" rows="2" placeholder="Contoh: Jl. Pasir Mukti No. 45 RT. 02/03 Panjalu - Ciamis"><?php echo @$row->alamat ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Ukuran Font</label>
                                <input type="number" name="font_size_alamat" id="sz_alamat" class="form-control text-center" min="8" max="40" value="<?php echo @$row->font_size_alamat ?: 9 ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Kontak & Media (Telepon, Email, Website)</label>
                                <input type="text" name="kontak" id="in_kontak" class="form-control" value="<?php echo @$row->kontak ?>" placeholder="Contoh: Telp: (0265) 12345 | Email: smp@miftahulkhoer.sch.id">
                            </div>

                            <div class="col-md-12">
                                <hr class="my-2">
                                <h6 class="text-md text-primary-light">Desain Tata Letak & Media</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tata Letak (Layout)</label>
                                <select name="layout_style" id="in_layout" class="form-select">
                                    <option value="center" <?php echo @$row->layout_style === 'center' ? 'selected' : '' ?>>Tengah (Logo Kiri di Atas)</option>
                                    <option value="left_logo" <?php echo @$row->layout_style === 'left_logo' ? 'selected' : '' ?>>Logo Kiri, Teks Kanan</option>
                                    <option value="double_logo" <?php echo @$row->layout_style === 'double_logo' ? 'selected' : '' ?>>Logo Kiri & Kanan (Teks Tengah)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Kop</label>
                                <select name="status" class="form-select">
                                    <option value="Aktif" <?php echo @$row->status !== 'Nonaktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Nonaktif" <?php echo @$row->status === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>

                            <!-- Upload Logo Kiri -->
                            <div class="col-md-6" id="logoKiriContainer">
                                <label class="form-label fw-semibold" id="logoKiriLabel">Upload Logo (Kiri)</label>
                                <input type="file" name="logo" id="logoUpload" class="form-control" accept="image/*">
                                <?php if (!empty($row->logo)): ?>
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="<?php echo url('uploads/kop_logo/' . $row->logo) ?>" style="height: 40px; border-radius:4px;" id="imgLogoKiriCurrent">
                                        <span class="text-xs text-secondary-light">Logo kiri saat ini.</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Upload Logo Kanan (Hanya muncul/penting ketika double_logo dipilih) -->
                            <div class="col-md-6" id="logoKananContainer" style="<?php echo @$row->layout_style === 'double_logo' ? '' : 'display:none;' ?>">
                                <label class="form-label fw-semibold">Upload Logo Kanan</label>
                                <input type="file" name="logo_kanan" id="logoKananUpload" class="form-control" accept="image/*">
                                <?php if (!empty($row->logo_kanan)): ?>
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="<?php echo url('uploads/kop_logo/' . $row->logo_kanan) ?>" style="height: 40px; border-radius:4px;" id="imgLogoKananCurrent">
                                        <span class="text-xs text-secondary-light">Logo kanan saat ini.</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <a href="<?php echo url('surat/kop') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary-600">Simpan Kop Surat</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Live Preview Kop Surat -->
            <div class="col-lg-5">
                <div class="card shadow-sm sticky-top" style="top: 80px; z-index: 1;">
                    <div class="card-header bg-neutral-100">
                        <h6 class="mb-0 text-neutral-800 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:eye-linear"></iconify-icon> Live Preview Kop Surat
                        </h6>
                    </div>
                    <div class="card-body bg-light" style="min-height: 250px;">
                        <div class="bg-white p-4 shadow-sm border rounded" id="kopContainer" style="font-family: Arial, sans-serif; color: #000;">
                            
                            <!-- Area render dynamic via JS -->
                            <div id="liveKopRender">
                                <!-- Rendered dynamically by script below -->
                            </div>
                            
                            <hr style="border: 0; border-top: 3px double #000; margin-top: 10px; margin-bottom: 0;">
                        </div>
                        <div class="text-center mt-3 text-xs text-muted">
                            * Tampilan di atas disimulasikan sesuai setelan ukuran font dan gaya tata letak.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include viewPath('includes/footer'); ?>

<script>
    let rawLogoKiri = '<?php echo !empty($row->logo) ? url('uploads/kop_logo/' . $row->logo) : '' ?>';
    let rawLogoKanan = '<?php echo !empty($row->logo_kanan) ? url('uploads/kop_logo/' . $row->logo_kanan) : '' ?>';

    function updatePreview() {
        const layout = $('#in_layout').val();
        const naungan = $('#in_naungan').val() || '';
        const naungan2 = $('#in_naungan_2').val() || '';
        const lembaga = $('#in_lembaga').val() || 'NAMA LEMBAGA UTAMA';
        const sub = $('#in_sub').val() || '';
        const alamat = $('#in_alamat').val() || '';
        const kontak = $('#in_kontak').val() || '';

        const szNaungan = $('#sz_naungan').val() || 11;
        const szNaungan2 = $('#sz_naungan_2').val() || 11;
        const szLembaga = $('#sz_lembaga').val() || 18;
        const szSub = $('#sz_sub').val() || 13;
        const szAlamat = $('#sz_alamat').val() || 9;

        const defaultLogo = '<?php echo $url->assets ?>images/user-grid/guru.png';
        
        // Atur penampakan input file logo kanan
        if (layout === 'double_logo') {
            $('#logoKananContainer').show();
            $('#logoKiriLabel').text('Upload Logo Kiri');
        } else {
            $('#logoKananContainer').hide();
            $('#logoKiriLabel').text('Upload Logo Lembaga');
        }

        const logoKiriSrc = rawLogoKiri || defaultLogo;
        const logoKananSrc = rawLogoKanan || defaultLogo;

        let contentHtml = '';

        if (layout === 'left_logo') {
            contentHtml = `
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 15px;">
                            <img src="${logoKiriSrc}" style="max-width: 75px; max-height: 75px; display: block;" id="previewLogoImage">
                        </td>
                        <td style="vertical-align: middle; text-align: left;">
                            ${naungan ? `<div style="font-size: ${szNaungan}px; font-weight: 550; text-transform: uppercase; line-height: 1.2;">${naungan}</div>` : ''}
                            ${naungan2 ? `<div style="font-size: ${szNaungan2}px; font-weight: 550; text-transform: uppercase; line-height: 1.2; margin-top: 1px;">${naungan2}</div>` : ''}
                            <div style="font-size: ${szLembaga}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px;">${lembaga}</div>
                            ${sub ? `<div style="font-size: ${szSub}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px; color:#333;">${sub}</div>` : ''}
                            ${alamat ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; margin-top: 4px; color:#555;">${alamat}</div>` : ''}
                            ${kontak ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; color:#555;">${kontak}</div>` : ''}
                        </td>
                    </tr>
                </table>
            `;
        } else if (layout === 'double_logo') {
            contentHtml = `
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70px; vertical-align: middle; text-align: left; padding-right: 10px;">
                            <img src="${logoKiriSrc}" style="max-width: 65px; max-height: 65px; display: block;" id="previewLogoImageLeft">
                        </td>
                        <td style="vertical-align: middle; text-align: center;">
                            ${naungan ? `<div style="font-size: ${szNaungan}px; font-weight: 550; text-transform: uppercase; line-height: 1.2;">${naungan}</div>` : ''}
                            ${naungan2 ? `<div style="font-size: ${szNaungan2}px; font-weight: 550; text-transform: uppercase; line-height: 1.2; margin-top: 1px;">${naungan2}</div>` : ''}
                            <div style="font-size: ${szLembaga}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px;">${lembaga}</div>
                            ${sub ? `<div style="font-size: ${szSub}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px; color:#333;">${sub}</div>` : ''}
                            ${alamat ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; margin-top: 4px; color:#555;">${alamat}</div>` : ''}
                            ${kontak ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; color:#555;">${kontak}</div>` : ''}
                        </td>
                        <td style="width: 70px; vertical-align: middle; text-align: right; padding-left: 10px;">
                            <img src="${logoKananSrc}" style="max-width: 65px; max-height: 65px; display: block;" id="previewLogoImageRight">
                        </td>
                    </tr>
                </table>
            `;
        } else {
            // Default: Center layout
            contentHtml = `
                <div style="text-align: center; width: 100%;">
                    <div style="margin-bottom: 8px; display: flex; justify-content: center;">
                        <img src="${logoKiriSrc}" style="max-width: 70px; max-height: 70px;" id="previewLogoImageCenter">
                    </div>
                    ${naungan ? `<div style="font-size: ${szNaungan}px; font-weight: 550; text-transform: uppercase; line-height: 1.2;">${naungan}</div>` : ''}
                    ${naungan2 ? `<div style="font-size: ${szNaungan2}px; font-weight: 550; text-transform: uppercase; line-height: 1.2; margin-top: 1px;">${naungan2}</div>` : ''}
                    <div style="font-size: ${szLembaga}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px;">${lembaga}</div>
                    ${sub ? `<div style="font-size: ${szSub}px; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin-top: 2px; color:#333;">${sub}</div>` : ''}
                    ${alamat ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; margin-top: 4px; color:#555;">${alamat}</div>` : ''}
                    ${kontak ? `<div style="font-size: ${szAlamat}px; line-height: 1.3; color:#555;">${kontak}</div>` : ''}
                </div>
            `;
        }

        $('#liveKopRender').html(contentHtml);
    }

    // Event listener for inputs change
    $('input, textarea, select').on('input change', updatePreview);

    // FileReader to show uploaded left image in live preview
    $('#logoUpload').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                rawLogoKiri = e.target.result;
                updatePreview();
            }
            reader.readAsDataURL(file);
        }
    });

    // FileReader to show uploaded right image in live preview
    $('#logoKananUpload').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                rawLogoKanan = e.target.result;
                updatePreview();
            }
            reader.readAsDataURL(file);
        }
    });

    // Run preview once on page load
    updatePreview();
</script>
