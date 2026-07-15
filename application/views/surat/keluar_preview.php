<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo $surat->nomor_surat ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 0;
            background: #e5e7eb;
        }
        .toolbar {
            padding: 12px 18px;
            background: #111827;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .toolbar a,
        .toolbar button {
            border: 1px solid #fff;
            background: transparent;
            color: #fff;
            padding: 8px 12px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 18px auto;
            background: #fff;
            padding: 20mm 22mm;
            box-sizing: border-box;
        }
         .kop {
             border-bottom: 3px double #111827;
             padding-bottom: 10px;
             margin-bottom: 18px;
             width: 100%;
         }
         .kop img {
             max-width: 80px;
             max-height: 80px;
         }
         .kop-title-naungan {
             font-weight: 550;
             line-height: 1.2;
         }
         .kop-title-lembaga {
             font-weight: bold;
             line-height: 1.2;
             margin-top: 2px;
         }
         .kop-title-sub {
             font-weight: bold;
             line-height: 1.2;
             margin-top: 2px;
             color: #333;
         }
         .kop-text-alamat {
             line-height: 1.3;
             margin-top: 4px;
             color: #4b5563;
         }
        .meta {
            width: 100%;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .meta td {
            vertical-align: top;
            padding: 2px 0;
        }
        .content {
            white-space: pre-line;
            font-size: 14px;
            line-height: 1.55;
            min-height: 420px;
        }
        .signature {
            width: 280px;
            margin-left: auto;
            margin-top: 34px;
            font-size: 14px;
        }
        .signature-space {
            height: 76px;
        }
        .validation {
            display: grid;
            grid-template-columns: 82px 1fr;
            gap: 10px;
            align-items: center;
            margin-top: 34px;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            font-size: 11px;
            color: #4b5563;
        }
        .validation img {
            width: 78px;
            height: 78px;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .paper {
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }
            @page {
                size: A4;
                margin: 20mm 22mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <strong><?php echo $surat->nomor_surat ?></strong>
        <div>
            <a href="<?php echo url('surat/keluar') ?>">Kembali</a>
            <?php 
                $edit_url = ($surat->metode_pembuatan === 'Otomatis') 
                    ? url('surat/keluar_edit_otomatis/' . $surat->id_surat_keluar) 
                    : url('surat/keluar_edit_manual/' . $surat->id_surat_keluar);
            ?>
            <a href="<?php echo $edit_url ?>">Edit</a>
            <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>
    <main class="paper">
        <?php if ($surat->metode_pembuatan === 'Manual'): ?>
            <!-- TAMPILAN PRATINJAU SURAT MANUAL -->
            <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 24px;">
                <h3 style="margin: 0; text-transform: uppercase;">Bukti Catatan Surat Keluar (Manual)</h3>
                <div style="font-size: 14px; margin-top: 4px; color: #4b5563;">Dicatat pada Sistem: <?php echo date('d-m-Y H:i', strtotime($surat->created_at ?: date('Y-m-d H:i'))) ?> WIB</div>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 15px; margin-bottom: 24px;">
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold; width: 180px;">Nomor Surat</td>
                    <td style="padding: 10px 0; width: 15px;">:</td>
                    <td style="padding: 10px 0; font-size: 16px; font-weight: bold; color: #111827;"><?php echo $surat->nomor_surat ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Lembaga Asal</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0;"><?php echo $surat->nama_lembaga ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Kategori / Kode Surat</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0;"><?php echo $surat->kode_jenis ?> - <?php echo $surat->nama_jenis ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Tanggal Surat</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0;"><?php echo date('d-m-Y', strtotime($surat->tanggal_surat)) ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Tujuan Surat</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0;"><?php echo $surat->tujuan_surat ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Perihal</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0; font-weight: 550;"><?php echo $surat->perihal ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 0; font-weight: bold;">Keterangan</td>
                    <td style="padding: 10px 0;">:</td>
                    <td style="padding: 10px 0; white-space: pre-line;"><?php echo $surat->keterangan ?: '<span class="text-muted">- Tidak ada keterangan -</span>' ?></td>
                </tr>
            </table>

            <div style="margin-top: 32px;">
                <h4 style="margin-bottom: 12px; border-bottom: 1px solid #ccc; padding-bottom: 6px;">Pejabat Penandatangan</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6; text-align: left;">
                            <th style="padding: 8px; border: 1px solid #d1d5db; font-size: 13px;">Nama Pejabat</th>
                            <th style="padding: 8px; border: 1px solid #d1d5db; font-size: 13px;">NIP / NIY</th>
                            <th style="padding: 8px; border: 1px solid #d1d5db; font-size: 13px;">Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($surat->penandatangan)): ?>
                            <?php foreach ($surat->penandatangan as $ptk): ?>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #d1d5db;"><?php echo $ptk->nama_ptk ?></td>
                                    <td style="padding: 8px; border: 1px solid #d1d5db;"><?php echo $ptk->nik ?: ($ptk->niy ?: '-') ?></td>
                                    <td style="padding: 8px; border: 1px solid #d1d5db; font-weight: 550;"><?php echo $ptk->jabatan ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="padding: 8px; border: 1px solid #d1d5db; text-align: center;" class="text-muted">Tidak ada penandatangan terpilih</td>
                             </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <section class="validation">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?php echo rawurlencode($validasi_url) ?>" alt="QR Validasi">
                <div>
                    <strong>Validasi Catatan Surat</strong><br>
                    Scan QR atau buka: <?php echo $validasi_url ?><br>
                    Nomor: <?php echo $surat->nomor_surat ?>
                </div>
            </section>

        <?php else: ?>
            <!-- TAMPILAN PRATINJAU SURAT OTOMATIS -->
            <header class="kop">
                <?php if (!empty($surat->nama_kop)): ?>
                    <!-- Render Kop Surat Dinamis -->
                    <?php 
                    $logo_kop = !empty($surat->kop_logo) ? url('uploads/kop_logo/' . $surat->kop_logo) : url('assets/images/user-grid/guru.png');
                    $logo_kop_kanan = !empty($surat->logo_kanan) ? url('uploads/kop_logo/' . $surat->logo_kanan) : url('assets/images/user-grid/guru.png');
                    $sz_naungan = $surat->font_size_naungan ?: 11;
                    $sz_naungan_2 = $surat->font_size_naungan_2 ?: 11;
                    $sz_lembaga = $surat->font_size_lembaga ?: 18;
                    $sz_sub = $surat->font_size_sub ?: 13;
                    $sz_alamat = $surat->font_size_alamat ?: 9;
                    $transform_text = ($surat->case_style === 'custom') ? 'none' : 'uppercase';
                    ?>
                    
                    <?php if ($surat->layout_style === 'left_logo'): ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 15px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo" style="max-width: 80px; max-height: 80px;">
                                </td>
                                <td style="vertical-align: middle; text-align: left; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php elseif ($surat->layout_style === 'double_logo'): ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 70px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri" style="max-width: 70px; max-height: 70px;">
                                </td>
                                <td style="vertical-align: middle; text-align: center; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 70px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan" style="max-width: 70px; max-height: 70px;">
                                </td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <!-- Default: Center -->
                        <div style="text-align: center; width: 100%;">
                            <div style="margin-bottom: 8px; display: flex; justify-content: center;">
                                <img src="<?php echo $logo_kop ?>" alt="Logo" style="max-width: 80px; max-height: 80px;">
                            </div>
                            <?php if (!empty($surat->naungan)): ?>
                                <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->naungan_2)): ?>
                                <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan_2 ?>px; margin-top: 1px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                            <?php endif; ?>
                            <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                            <?php if (!empty($surat->sub_nama)): ?>
                                <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->alamat_kop)): ?>
                                <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($surat->alamat_kop) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->kontak)): ?>
                                <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($surat->kontak) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Fallback: Kop Lembaga Lama -->
                    <table style="width: 100%; border-collapse: collapse; border: 0;">
                        <tr>
                            <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 15px; border: 0;">
                                <?php if (!empty($surat->logo)): ?>
                                    <img src="<?php echo url('uploads/logo_lembaga/' . $surat->logo) ?>" alt="Logo" style="max-width: 80px; max-height: 80px;">
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle; text-align: left; border: 0;">
                                <div class="kop-title-lembaga" style="font-size: 18px;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                                <?php if (!empty($surat->alamat)): ?>
                                    <div class="kop-text-alamat" style="font-size: 10px;"><?php echo html_escape($surat->alamat) ?></div>
                                <?php endif; ?>
                                <div class="kop-text-alamat" style="font-size: 10px; margin-top: 0;">
                                    <?php echo trim(($surat->telepon ? 'Telp. ' . $surat->telepon : '') . ($surat->email ? ' | Email: ' . $surat->email : '')) ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
            </header>

            <table class="meta">
                <tr>
                    <td style="width:90px;">Nomor</td>
                    <td style="width:12px;">:</td>
                    <td><?php echo $surat->nomor_surat ?></td>
                    <td style="text-align:right;"><?php echo $surat->lokasi ?: 'Panjalu' ?>, <?php echo date('d-m-Y', strtotime($surat->tanggal_surat)) ?></td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td colspan="2"><?php echo $surat->perihal ?></td>
                </tr>
                <tr>
                    <td>Kepada</td>
                    <td>:</td>
                    <td colspan="2"><?php echo $surat->tujuan_surat ?></td>
                </tr>
            </table>

            <section class="content"><?php echo html_escape($isi_render) ?></section>

            <!-- Tanda Tangan Multi-Penandatangan -->
            <div style="width: 100%; margin-top: 40px; font-size: 14px;">
                <table style="width: 100%; border: 0; border-collapse: collapse;">
                    <tr>
                        <?php 
                        $count = count($surat->penandatangan);
                        $width = $count > 0 ? (100 / $count) . '%' : '100%';
                        if (!empty($surat->penandatangan)): 
                            foreach ($surat->penandatangan as $ptk):
                        ?>
                            <td style="width: <?php echo $width ?>; text-align: center; vertical-align: top; border: 0; padding: 10px;">
                                <div><?php echo $ptk->jabatan ?: 'Kepala' ?></div>
                                <div style="height: 76px;"></div>
                                <strong><u><?php echo htmlspecialchars($ptk->nama_ptk) ?></u></strong>
                                <div>NIK/NIY: <?php echo $ptk->nik ?: ($ptk->niy ?: '-') ?></div>
                            </td>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <td style="text-align: right; border: 0; width: 280px; padding: 10px;">
                                <div><?php echo $surat->penandatangan_jabatan ?: 'Kepala' ?></div>
                                <div style="height: 76px;"></div>
                                <strong><u><?php echo $surat->penandatangan_nama ?: ($surat->nama_kepsek ?: '') ?></u></strong>
                            </td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>

            <section class="validation">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?php echo rawurlencode($validasi_url) ?>" alt="QR Validasi">
                <div>
                    <strong>Validasi Surat</strong><br>
                    Scan QR atau buka: <?php echo $validasi_url ?><br>
                    Nomor: <?php echo $surat->nomor_surat ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
