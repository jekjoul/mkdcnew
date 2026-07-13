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
             text-transform: uppercase;
             font-weight: 550;
             line-height: 1.2;
         }
         .kop-title-lembaga {
             text-transform: uppercase;
             font-weight: bold;
             line-height: 1.2;
             margin-top: 2px;
         }
         .kop-title-sub {
             text-transform: uppercase;
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
            <a href="<?php echo url('surat/keluar_edit/' . $surat->id_surat_keluar) ?>">Edit</a>
            <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>
    <main class="paper">
        <header class="kop">
            <?php if (!empty($surat->nama_kop)): ?>
                <!-- Render Kop Surat Dinamis -->
                <?php 
                $logo_kop = !empty($surat->kop_logo) ? url('uploads/kop_logo/' . $surat->kop_logo) : url('assets/images/user-grid/guru.png');
                $logo_kop_kanan = !empty($surat->logo_kanan) ? url('uploads/kop_logo/' . $surat->logo_kanan) : url('assets/images/user-grid/guru.png');
                $sz_naungan = $surat->font_size_naungan ?: 11;
                $sz_lembaga = $surat->font_size_lembaga ?: 18;
                $sz_sub = $surat->font_size_sub ?: 13;
                $sz_alamat = $surat->font_size_alamat ?: 9;
                ?>
                
                <?php if ($surat->layout_style === 'left_logo'): ?>
                    <table style="width: 100%; border-collapse: collapse; border: 0;">
                        <tr>
                            <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 15px; border: 0;">
                                <img src="<?php echo $logo_kop ?>" alt="Logo">
                            </td>
                            <td style="vertical-align: middle; text-align: left; border: 0;">
                                <?php if (!empty($surat->naungan)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px;"><?php echo html_escape($surat->naungan) ?></div>
                                <?php endif; ?>
                                <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                                <?php if (!empty($surat->sub_nama)): ?>
                                    <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px;"><?php echo html_escape($surat->sub_nama) ?></div>
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
                                <img src="<?php echo $logo_kop ?>" alt="Logo Kiri">
                            </td>
                            <td style="vertical-align: middle; text-align: center; border: 0;">
                                <?php if (!empty($surat->naungan)): ?>
                                    <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px;"><?php echo html_escape($surat->naungan) ?></div>
                                <?php endif; ?>
                                <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                                <?php if (!empty($surat->sub_nama)): ?>
                                    <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px;"><?php echo html_escape($surat->sub_nama) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($surat->alamat_kop)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($surat->kontak)): ?>
                                    <div class="kop-text-alamat" style="font-size: <?php echo $sz_alamat ?>px; margin-top: 0;"><?php echo html_escape($surat->kontak) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="width: 70px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan">
                            </td>
                        </tr>
                    </table>
                <?php else: ?>
                    <!-- Default: Center -->
                    <div style="text-align: center; width: 100%;">
                        <div style="margin-bottom: 8px; display: flex; justify-content: center;">
                            <img src="<?php echo $logo_kop ?>" alt="Logo">
                        </div>
                        <?php if (!empty($surat->naungan)): ?>
                            <div class="kop-title-naungan" style="font-size: <?php echo $sz_naungan ?>px;"><?php echo html_escape($surat->naungan) ?></div>
                        <?php endif; ?>
                        <div class="kop-title-lembaga" style="font-size: <?php echo $sz_lembaga ?>px;"><?php echo html_escape($surat->nama_lembaga) ?></div>
                        <?php if (!empty($surat->sub_nama)): ?>
                            <div class="kop-title-sub" style="font-size: <?php echo $sz_sub ?>px;"><?php echo html_escape($surat->sub_nama) ?></div>
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
                                <img src="<?php echo url('uploads/logo_lembaga/' . $surat->logo) ?>" alt="Logo">
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

        <section class="signature">
            <div><?php echo $surat->penandatangan_jabatan ?: 'Kepala' ?></div>
            <div class="signature-space"></div>
            <strong><?php echo $surat->penandatangan_nama ?: ($surat->nama_kepsek ?: '') ?></strong>
        </section>

        <section class="validation">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?php echo rawurlencode($validasi_url) ?>" alt="QR Validasi">
            <div>
                <strong>Validasi Surat</strong><br>
                Scan QR atau buka: <?php echo $validasi_url ?><br>
                Nomor: <?php echo $surat->nomor_surat ?>
            </div>
        </section>
    </main>
</body>
</html>
