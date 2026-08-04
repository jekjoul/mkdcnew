<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo $surat->nomor_surat ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
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
            font-family: Arial, Helvetica, sans-serif;
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
            padding: 20mm 22mm 30mm 22mm;
            box-sizing: border-box;
            position: relative;
            font-family: 'Times New Roman', Times, serif;
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
            body { 
                background: #fff; 
                margin: 0;
                padding: 0;
            }
            .toolbar { 
                display: none; 
            }
            .paper {
                margin: 0 !important;
                width: 100% !important;
                min-height: 255mm !important;
                padding: 0 !important;
                position: relative !important;
                box-sizing: border-box !important;
            }
            @page {
                size: A4;
                margin: 12mm 22mm 15mm 22mm;
            }
            .qr-footer-fixed {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                margin: 0 !important;
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
            <button type="button" onclick="showDeleteModal()" style="background: #dc2626; border-color: #dc2626; color: #fff; cursor: pointer;">Hapus Surat</button>
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

        <?php elseif ($surat->jenis_template === 'sk_pengangkatan'): ?>
            <!-- TAMPILAN CETAK SURAT KEPUTUSAN (SK) PENGANGKATAN PEGAWAI / GURU YAYASAN -->
            <?php
            $payload = json_decode($surat->isi_surat, true) ?: [];
            $tentang = isset($payload['tentang']) ? $payload['tentang'] : 'PENGANGKATAN PEGAWAI / GURU TETAP YAYASAN';
            $menimbang = isset($payload['menimbang']) ? $payload['menimbang'] : '';
            $mengingat = isset($payload['mengingat']) && is_array($payload['mengingat']) ? $payload['mengingat'] : [];
            $memperhatikan = isset($payload['memperhatikan']) ? $payload['memperhatikan'] : '';
            $nama_lembaga_target = isset($payload['nama_lembaga_target']) ? $payload['nama_lembaga_target'] : '';
            $nama_ptk_target = isset($payload['nama_ptk_target']) ? $payload['nama_ptk_target'] : '';
            $ttl_ptk_target = isset($payload['ttl_ptk_target']) ? $payload['ttl_ptk_target'] : '';
            $alamat_ptk_target = isset($payload['alamat_ptk_target']) ? $payload['alamat_ptk_target'] : '';
            $jk_ptk_target = isset($payload['jk_ptk_target']) ? $payload['jk_ptk_target'] : '';
            $tmt_raw = isset($payload['tmt']) ? $payload['tmt'] : '';
            $tmt_fmt = (!empty($tmt_raw) && $tmt_raw !== '-') ? date('d F Y', strtotime($tmt_raw)) : '-';
            $poin_kedua = isset($payload['poin_kedua']) ? $payload['poin_kedua'] : '';
            $poin_ketiga = isset($payload['poin_ketiga']) ? $payload['poin_ketiga'] : '';
            $poin_keempat = isset($payload['poin_keempat']) ? $payload['poin_keempat'] : '';
            $poin_kelima = isset($payload['poin_kelima']) ? $payload['poin_kelima'] : '';

            // Lokasi Kabupaten Yayasan
            $kab_penutup = 'Ciamis';
            if (!empty($surat->kabupaten)) {
                $raw_k = preg_replace('/^(KAB\.?|KABUPATEN|KOTA)\s+/i', '', trim($surat->kabupaten));
                $kab_penutup = ucwords(strtolower($raw_k ?: 'Ciamis'));
            }
            ?>

            <!-- KOP SURAT YAYASAN -->
            <header class="kop" style="border-bottom: 3px double #111827; padding-bottom: 10px; margin-bottom: 20px;">
                <?php if (!empty($surat->nama_kop)): ?>
                    <?php 
                    $logo_kop = !empty($surat->kop_logo) ? url('uploads/kop_logo/' . $surat->kop_logo) : url('assets/images/logodc_round.png');
                    $logo_kop_kanan = !empty($surat->logo_kanan) ? url('uploads/kop_logo/' . $surat->logo_kanan) : url('assets/images/logodc_round.png');
                    $sz_naungan = $surat->font_size_naungan ?: 12;
                    $sz_naungan_2 = $surat->font_size_naungan_2 ?: 12;
                    $sz_lembaga = $surat->font_size_lembaga ?: 18;
                    $sz_sub = $surat->font_size_sub ?: 13;
                    $sz_alamat = $surat->font_size_alamat ?: 9;
                    $transform_text = ($surat->case_style === 'custom') ? 'none' : 'uppercase';
                    ?>
                    <?php if ($surat->layout_style === 'left_logo_center_text' || $surat->layout_style === 'center'): ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri" style="max-width: 85px; max-height: 85px;">
                                </td>
                                <td style="width: 100% ; vertical-align: middle; text-align: center; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #374151; margin-top: 2px;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #374151;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 80px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                    <?php if (!empty($surat->logo_kanan)): ?>
                                        <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan" style="max-width: 90px; max-height: 90px;">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 80px; vertical-align: middle; text-align: left; padding-right: 15px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo" style="max-width: 85px; max-height: 85px;">
                                </td>
                                <td style="width:100% !important; vertical-align: middle; text-align: left; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #374151;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #374151;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align: center;">
                        <h4 style="margin:0; text-transform:uppercase; font-size:16px;">YAYASAN MIFTAHUL KHOER EL-ISTOHARY</h4>
                        <p style="margin:0; font-size:10px; color:#4b5563;">Kecamatan Panjalu Kabupaten Ciamis Provinsi Jawa Barat</p>
                    </div>
                <?php endif; ?>
            </header>

            <!-- JUDUL SK -->
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="margin: 0; text-transform: uppercase; font-size: 15px; font-weight: bold; text-decoration: underline;">SURAT KEPUTUSAN</h3>
                <h4 style="margin: 2px 0 0 0; text-transform: uppercase; font-size: 13px; font-weight: bold;">KETUA YAYASAN MIFTAHUL KHOER EL-ISTOHARY</h4>
                <div style="font-size: 12px; font-weight: bold; margin-top: 4px;">Nomor : <?php echo htmlspecialchars($surat->nomor_surat) ?></div>
                <div style="font-size: 12px; font-weight: bold; margin-top: 8px; text-transform: uppercase;">TENTANG</div>
                <div style="font-size: 13px; font-weight: bold; margin-top: 2px; text-transform: uppercase; padding: 0 20px;">
                    <?php echo htmlspecialchars($tentang) ?> <br> <?php echo htmlspecialchars($nama_lembaga_target) ?>
                </div>
            </div>

            <!-- KONSIDERAN (MENIMBANG, MENGINGAT, MEMPERHATIKAN) -->
            <?php
            $menimbang_list = [];
            if (is_array($menimbang)) {
                $menimbang_list = $menimbang;
            } elseif (is_string($menimbang) && !empty($menimbang)) {
                $decoded_m = json_decode($menimbang, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_m)) {
                    $menimbang_list = $decoded_m;
                } else {
                    $menimbang_list = [$menimbang];
                }
            }
            ?>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 12px; line-height: 1.4;">
                <tr>
                    <td style="width: 130px; vertical-align: top; font-weight: bold;">MENIMBANG</td>
                    <td style="width: 15px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        <?php if (!empty($menimbang_list) && count($menimbang_list) > 1): ?>
                            <table style="width: 100%; border-collapse: collapse; margin: 0; ">
                                <?php foreach ($menimbang_list as $idx => $m_item): 
                                    $m_text = str_replace('{nama_lembaga}', $nama_lembaga_target, $m_item);
                                    $letter = chr(97 + ($idx % 26)) . '.';
                                ?>
                                    <tr>
                                        <td style="width: 22px; vertical-align: top; padding-bottom: 4px;"><?php echo $letter ?></td>
                                        <td style="line-height: 1 !important; vertical-align: top; text-align: justify; padding-bottom: 4px;"><?php echo htmlspecialchars($m_text) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php elseif (!empty($menimbang_list)): ?>
                            <div style="text-align: justify; line-height: 1.1 !important;"><?php echo nl2br(htmlspecialchars(str_replace('{nama_lembaga}', $nama_lembaga_target, $menimbang_list[0]))) ?></div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 6px;">MENGINGAT</td>
                    <td style="vertical-align: top; padding-top: 6px;">:</td>
                    <td style="vertical-align: top; padding-top: 6px;">
                        <?php if (!empty($mengingat)): ?>
                            <table style="width: 100%; border-collapse: collapse; margin: 0;">
                                <?php 
                                $total_mg = count($mengingat);
                                foreach ($mengingat as $idx => $mg): 
                                    $clean_mg = rtrim(trim($mg), ';.');
                                    $punc = ($idx === $total_mg - 1) ? '.' : ';';
                                    $final_mg = $clean_mg . $punc;
                                ?>
                                    <tr>
                                        <td style="width: 22px; vertical-align: top; padding-bottom: 4px;"><?php echo ($idx + 1) ?>.</td>
                                        <td style="line-height: 1.1 !important; vertical-align: top; text-align: justify; padding-bottom: 4px;"><?php echo htmlspecialchars($final_mg) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 6px;">MEMPERHATIKAN</td>
                    <td style="vertical-align: top; padding-top: 6px;">:</td>
                    <td style="line-height: 1.1 !important;vertical-align: top; text-align: justify; padding-top: 6px;"><?php echo nl2br(htmlspecialchars($memperhatikan)) ?></td>
                </tr>
            </table>

            <!-- MEMUTUSKAN / MENETAPKAN -->
            <div style="text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 2px;">MEMUTUSKAN</div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; font-size: 12px; line-height: 1.3;">
                <tr>
                    <td style="width: 130px; vertical-align: top; font-weight: bold;">MENETAPKAN</td>
                    <td style="width: 15px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;"></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold;">Pertama</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        <div style="margin-bottom: 6px; line-height: 1.1 !important;">Mengangkat dan menugaskan Pegawai / Guru Tetap Yayasan <?php echo htmlspecialchars($nama_lembaga_target) ?> :</div>
                        <table style="width: 100%; border-collapse: collapse; margin-left: 10px; margin-bottom: 8px;">
                            <tr><td style="width: 140px; padding: 0px 0; vertical-align: top;">Nama</td><td style="width: 15px; padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; font-weight: bold; vertical-align: top;"><?php echo htmlspecialchars($nama_ptk_target) ?></td></tr>
                            <tr><td style="padding: 0px 0; vertical-align: top;">Tempat, Tanggal lahir</td><td style="padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; vertical-align: top;"><?php echo htmlspecialchars($ttl_ptk_target) ?></td></tr>
                            <tr><td style="padding: 0px 0; vertical-align: top;">Alamat</td><td style="padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; vertical-align: top;"><?php echo $alamat_ptk_target ?></td></tr>
                            <tr><td style="padding: 0px 0; vertical-align: top;">Jenis Kelamin</td><td style="padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; vertical-align: top;"><?php echo htmlspecialchars($jk_ptk_target) ?></td></tr>
                            <tr><td style="padding: 0px 0; vertical-align: top;">Unit Kerja</td><td style="padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; vertical-align: top;"><?php echo htmlspecialchars($nama_lembaga_target) ?></td></tr>
                            <tr><td style="padding: 0px 0; vertical-align: top;">TMT</td><td style="padding: 0px 0; vertical-align: top;">:</td><td style="padding: 0px 0; font-weight: bold; vertical-align: top;"><?php echo $tmt_fmt ?></td></tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 2px;">Kedua</td>
                    <td style="vertical-align: top; padding-top: 2px;">:</td>
                    <td style="vertical-align: top; text-align: justify; padding-top: 2px; line-height: 1.1 !important;"><?php echo nl2br(htmlspecialchars(str_replace('{nama_lembaga}', $nama_lembaga_target, $poin_kedua))) ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 2px;">Ketiga</td>
                    <td style="vertical-align: top; padding-top: 2px;">:</td>
                    <td style="vertical-align: top; text-align: justify; padding-top: 2px; line-height: 1.1 !important;"><?php echo nl2br(htmlspecialchars(str_replace('{nama_lembaga}', $nama_lembaga_target, $poin_ketiga))) ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 2px; line-height: 1.1 !important;">Keempat</td>
                    <td style="vertical-align: top; padding-top: 2px;">:</td>
                    <td style="vertical-align: top; text-align: justify; padding-top: 2px;"><?php echo nl2br(htmlspecialchars(str_replace('{nama_lembaga}', $nama_lembaga_target, $poin_keempat))) ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; font-weight: bold; padding-top: 2px;">Kelima</td>
                    <td style="vertical-align: top; padding-top: 2px;">:</td>
                    <td style="vertical-align: top; text-align: justify; padding-top: 2px; line-height: 1.1 !important;"><?php echo nl2br(htmlspecialchars(str_replace('{nama_lembaga}', $nama_lembaga_target, $poin_kelima))) ?></td>
                </tr>
            </table>

            <!-- FOOTER TTD PEJABAT -->
            <div style="display: flex; justify-content: flex-end; margin-top: 13px;">
                <div style="width: 250px; text-align: left; font-size: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="width: 95px; padding: 1px 0;">Ditetapkan di</td><td style="width: 10px; padding: 1px 0;">:</td><td style="padding: 1px 0;"><?php echo htmlspecialchars($kab_penutup) ?></td></tr>
                        <tr><td style="padding: 1px 0;">Pada Tanggal</td><td style="padding: 1px 0;">:</td><td style="padding: 1px 0;"><?php echo date('d F Y', strtotime($surat->tanggal_surat)) ?></td></tr>
                    </table>
                    <div style="margin-top: 10px; font-weight: bold; text-transform: uppercase;">
                        <?php echo htmlspecialchars($surat->penandatangan_jabatan ?: 'Ketua Yayasan') ?>
                    </div>
                    
                    <div style="height: 65px; display: flex; align-items: center; position: relative;">
                        <?php if ($surat->tipe_ttd === 'digital'): ?>
                            <div style="font-size: 10px; color: #059669; font-style: italic; border: 1px dashed #059669; padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                [ TTD Digital & Stempel Sah ]
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="font-weight: bold; text-transform: uppercase; text-decoration: underline;">
                        <?php 
                        if (!empty($surat->penandatangan) && isset($surat->penandatangan[0])) {
                            echo htmlspecialchars($surat->penandatangan[0]->nama_ptk);
                        } else {
                            echo "HJ. SITI ROBI’AH";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Footer Dokumen (Fixed di Paling Bawah Kertas, Tanpa Garis, Teks Abu-abu, plus Nomor Surat) -->
            <div class="qr-footer-fixed" style="position: absolute; bottom: 15mm; left: 22mm; right: 22mm; border-top: none; padding-top: 0; display: flex; align-items: center; gap: 14px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?php echo rawurlencode($validasi_url) ?>" style="width: 48px; height: 48px;" alt="QR Validasi">
                <div style="font-size: 7pt; color: #8f95a0ff; line-height: 1.2; font-family: Arial, sans-serif;">
                    <div><strong>Dokumen ini dikeluarkan dan diarsipkan melalui Aplikasi Miftahul Khoer Data Center.</strong></div>
                    <div>Validasi surat melalui Scan QR Code disamping.</div>
                    <div>Nomor: <?php echo html_escape($surat->nomor_surat) ?></div>
                </div>
            </div>

        <?php elseif ($surat->jenis_template === 'keterangan_siswa_aktif'): ?>
            <!-- TAMPILAN PRATINJAU SURAT KETERANGAN SISWA AKTIF SMP -->
            <header class="kop" style="border-bottom: 3px double #111827; padding-bottom: 10px; margin-bottom: 24px;">
                <?php if (!empty($surat->nama_kop)): ?>
                    <!-- Render Kop Surat Dinamis dari Settingan Menu Kop -->
                    <?php 
                    $logo_kop = !empty($surat->kop_logo) ? url('uploads/kop_logo/' . $surat->kop_logo) : url('assets/images/logodc_round.png');
                    $logo_kop_kanan = !empty($surat->logo_kanan) ? url('uploads/kop_logo/' . $surat->logo_kanan) : url('assets/images/logodc_round.png');
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
                                    <img src="<?php echo $logo_kop ?>" alt="Logo" style="max-width: 90px; max-height: 90px;">
                                </td>
                                <td style="vertical-align: middle; text-align: left; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php elseif ($surat->layout_style === 'left_logo_center_text'): ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 75px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri" style="max-width: 90px; max-height: 90px;">
                                </td>
                                <td style="vertical-align: middle; text-align: center; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php elseif ($surat->layout_style === 'double_logo'): ?>
                        <table style="width: 100%; border-collapse: collapse; border: 0;">
                            <tr>
                                <td style="width: 65px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop ?>" alt="Logo Kiri" style="max-width: 85px; max-height: 85px;">
                                </td>
                                <td style="vertical-align: middle; text-align: center; border: 0;">
                                    <?php if (!empty($surat->naungan)): ?>
                                        <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->naungan_2)): ?>
                                        <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                                    <?php endif; ?>
                                    <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                                    <?php if (!empty($surat->sub_nama)): ?>
                                        <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->alamat_kop)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->alamat_kop) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($surat->kontak)): ?>
                                        <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->kontak) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 65px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                    <img src="<?php echo $logo_kop_kanan ?>" alt="Logo Kanan" style="max-width: 85px; max-height: 85px;">
                                </td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <!-- Center Layout -->
                        <div style="text-align: center; width: 100%;">
                            <?php if (!empty($surat->naungan)): ?>
                                <div style="font-size: <?php echo $sz_naungan ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->naungan_2)): ?>
                                <div style="font-size: <?php echo $sz_naungan_2 ?>px; font-weight: 550; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->naungan_2) ?></div>
                            <?php endif; ?>
                            <div style="font-size: <?php echo $sz_lembaga ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->kop_nama_lembaga ?: $surat->nama_lembaga) ?></div>
                            <?php if (!empty($surat->sub_nama)): ?>
                                <div style="font-size: <?php echo $sz_sub ?>px; font-weight: bold; text-transform: <?php echo $transform_text ?>;"><?php echo html_escape($surat->sub_nama) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->alamat_kop)): ?>
                                <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->alamat_kop) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($surat->kontak)): ?>
                                <div style="font-size: <?php echo $sz_alamat ?>px; color: #4b5563;"><?php echo html_escape($surat->kontak) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Default Kop SMP Fallback -->
                    <table style="width: 100%; border-collapse: collapse; border: 0;">
                        <tr>
                            <td style="width: 75px; vertical-align: middle; text-align: left; padding-right: 10px; border: 0;">
                                <img src="<?php echo url('assets/images/logodc_round.png') ?>" style="max-width: 80px; max-height: 80px;">
                            </td>
                            <td style="vertical-align: middle; text-align: center; border: 0; line-height: 1.3;">
                                <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase;">PEMERINTAH KABUPATEN CIAMIS</div>
                                <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">DINAS PENDIDIKAN</div>
                                <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-top: 2px;">SMP MIFTAHUL KHOER BOARDING SCHOOL</div>
                                <div style="font-size: 8.5pt; font-weight: normal;">Nomor : 555/BAN-SM/SK/2023 Terakreditasi B</div>
                                <div style="font-size: 8.5pt; font-weight: normal;">Dusun Mandala No. 59 RT 017 RW 006 Desa Kertamandala Kec. Panjalu Kab. Ciamis</div>
                                <div style="font-size: 8.5pt; font-weight: normal;">Tlp. 082120073033 Email : smpemka@gmail.com Website : https://smp.miftahulkhoer.org</div>
                            </td>
                            <td style="width: 75px; vertical-align: middle; text-align: right; padding-left: 10px; border: 0;">
                                <img src="<?php echo url('assets/images/logodc_round.png') ?>" style="max-width: 75px; max-height: 75px;">
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
            </header>

            <?php 
            $raw_lembaga = $surat->kop_nama_lembaga ?: $surat->nama_lembaga;
            $words_l = explode(' ', trim($raw_lembaga ?: 'SMP Miftahul Khoer Boarding School'));
            $formatted_words_l = [];
            foreach ($words_l as $wl) {
                $ul = strtoupper($wl);
                if (in_array($ul, ['SMP', 'SMA', 'SMK', 'MTS', 'MA', 'SD', 'MI'])) {
                    $formatted_words_l[] = $ul;
                } else {
                    $formatted_words_l[] = ucfirst(strtolower($wl));
                }
            }
            $nama_lembaga_formatted = implode(' ', $formatted_words_l);

            $ptk_first = (!empty($surat->penandatangan) && isset($surat->penandatangan[0])) ? $surat->penandatangan[0] : null;
            $jabatan_ptk = $ptk_first ? ($ptk_first->jabatan ?: 'Kepala Sekolah,') : 'Kepala Sekolah,';
            $jabatan_clean = trim(rtrim($jabatan_ptk, ','));
            if (empty($jabatan_clean)) {
                $jabatan_clean = 'Kepala Sekolah';
            }
            ?>

            <div style="text-align: center; margin-top: 24px; margin-bottom: 50px;">
                <h3 style="margin: 0; font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; font-family: 'Times New Roman', serif;">SURAT KETERANGAN</h3>
                <div style="font-size: 12pt; margin-top: 4px;">Nomor : <?php echo $surat->nomor_surat ?></div>
            </div>
            
            <div style="font-size: 12pt; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 16px;">
                Yang bertanda tangan dibawah ini, <?php echo html_escape($jabatan_clean) ?> <?php echo html_escape($nama_lembaga_formatted) ?> menerangkan bahwa :
            </div>

            <table style="width: 90%; margin-left: 50px; margin-bottom: 20px; font-size: 16px; line-height: 1.8; border-collapse: collapse;">
                <tr>
                    <td style="width: 170px; vertical-align: top;">Nama</td>
                    <td style="width: 15px; vertical-align: top;">:</td>
                    <td style="font-weight: bold; vertical-align: top;"><?php echo html_escape(@$surat->nama_siswa ?: '-') ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Tempat tanggal lahir</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        <?php 
                        $tgl_str = '-';
                        if (!empty($surat->tanggal_lahir)) {
                            $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $t = strtotime($surat->tanggal_lahir);
                            $m = (int)date('n', $t);
                            $tgl_str = date('d', $t) . ' ' . (isset($months[$m]) ? $months[$m] : date('F', $t)) . ' ' . date('Y', $t);
                        }
                        $tempat = !empty($surat->tempat_lahir) ? $surat->tempat_lahir : '-';
                        echo html_escape($tempat . ', ' . $tgl_str);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Kelas</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;"><?php echo html_escape(@$surat->rombel ?: '-') ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">NISN</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;"><?php echo html_escape(@$surat->nisn ?: '-') ?></td>
                </tr>
            </table>

            <?php 
            $kec_text = !empty($surat->kecamatan_lembaga) ? trim($surat->kecamatan_lembaga) : (!empty($surat->lokasi) ? trim($surat->lokasi) : 'Panjalu');
            if (is_numeric($kec_text)) {
                $reg_k_row = $this->db->get_where('reg_kecamatan', ['id_kec' => $kec_text])->row();
                if ($reg_k_row && !empty($reg_k_row->nama)) {
                    $kec_text = $reg_k_row->nama;
                }
            }
            $clean_kec = preg_replace('/^(KEC\.?|KECAMATAN)\s+/i', '', trim($kec_text));
            $formatted_kec = ucwords(strtolower($clean_kec ?: 'Panjalu'));

            $kab_text = !empty($surat->kabupaten_lembaga) ? trim($surat->kabupaten_lembaga) : 'Ciamis';
            if (is_numeric($kab_text)) {
                $reg_k_row2 = $this->db->get_where('reg_kabupaten', ['id_kab' => $kab_text])->row();
                if ($reg_k_row2 && !empty($reg_k_row2->nama)) {
                    $kab_text = $reg_k_row2->nama;
                }
            }
            $clean_kab = preg_replace('/^(KAB\.?|KABUPATEN|KOTA)\s+/i', '', trim($kab_text));
            $formatted_kab = ucwords(strtolower($clean_kab ?: 'Ciamis'));
            ?>

            <div style="font-size: 12pt; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 16px;">
                Yang bersangkutan adalah benar-benar Siswa <?php echo html_escape($nama_lembaga_formatted) ?> Kecamatan <?php echo html_escape($formatted_kec) ?> Kabupaten <?php echo html_escape($formatted_kab) ?>.
            </div>
            <div style="font-size: 12pt; line-height: 1.6; text-align: justify; text-indent: 36pt; margin-bottom: 35px;">
                Demikian Surat Keterangan ini kami buat dengan sebenarnya, dan diberikan kepada yang bersangkutan dipergunakan sebaik-baiknya.
            </div>

            <!-- TTD Section dengan TTD Digital Overlay Behind/Over Text & Nama Huruf Kapital Semua -->
            <?php 
            $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $ts = strtotime($surat->tanggal_surat);
            $ms = (int)date('n', $ts);
            $tgl_surat_str = date('d', $ts) . ' ' . (isset($months[$ms]) ? $months[$ms] : date('F', $ts)) . ' ' . date('Y', $ts);
            
            $ptk_first = (!empty($surat->penandatangan) && isset($surat->penandatangan[0])) ? $surat->penandatangan[0] : null;
            $jabatan_ptk = $ptk_first ? ($ptk_first->jabatan ?: 'Kepala Sekolah,') : 'Kepala Sekolah,';
            
            if ($ptk_first) {
                $g_depan = !empty($ptk_first->gelar_depan) ? trim($ptk_first->gelar_depan) . ' ' : '';
                $g_belakang = !empty($ptk_first->gelar_belakang) ? ', ' . trim($ptk_first->gelar_belakang) : '';
                $nama_ptk_only = mb_strtoupper(trim($ptk_first->nama_ptk), 'UTF-8');
                $nama_ptk_full = $g_depan . $nama_ptk_only . $g_belakang;
            } else {
                $g_depan = !empty($surat->kepsek_gelar_depan) ? trim($surat->kepsek_gelar_depan) . ' ' : '';
                $g_belakang = !empty($surat->kepsek_gelar_belakang) ? ', ' . trim($surat->kepsek_gelar_belakang) : '';
                $nama_ptk_only = mb_strtoupper(trim($surat->nama_kepsek ?: 'Kepala Sekolah'), 'UTF-8');
                $nama_ptk_full = $g_depan . $nama_ptk_only . $g_belakang;
            }

            $niy_ptk = $ptk_first ? ($ptk_first->niy ?: ($ptk_first->nik ?: '-')) : '-';

            // Ambil data kabupaten lembaga, atau fallback ke Ciamis (tanpa menggunakan lokasi kecamatan)
            $raw_kab = !empty($surat->kabupaten_lembaga) ? trim($surat->kabupaten_lembaga) : 'Ciamis';
            if (is_numeric($raw_kab)) {
                $reg_k = $this->db->get_where('reg_kabupaten', ['id_kab' => $raw_kab])->row();
                if ($reg_k && !empty($reg_k->nama)) {
                    $raw_kab = $reg_k->nama;
                }
            }
            $clean_kab = preg_replace('/^(KAB\.?|KABUPATEN|KOTA)\s+/i', '', trim($raw_kab));
            $formatted_loc = ucwords(strtolower($clean_kab ?: 'Ciamis'));
            ?>

            <div style="float: right; width: 280px; text-align: left; font-size: 11pt; line-height: 1.4; margin-bottom: 30px; position: relative;">
                <?php if ($surat->tipe_ttd === 'digital' && !empty($surat->file_ttd_digital)): ?>
                    <!-- Image TTD Digital Overlay Behind/Over Text (Presisi Seperti Stempel Asli) -->
                    <img src="<?php echo url('uploads/ttd/' . $surat->file_ttd_digital) ?>" 
                         style="position: absolute; left: -35px; top: 15px; width: 190px; max-height: 120px; object-fit: contain; pointer-events: none; z-index: 1; opacity: 0.92;" 
                         alt="TTD & Stempel Digital">
                <?php endif; ?>

                <div style="position: relative; z-index: 2;">
                    <div><?php echo html_escape($formatted_loc) ?>, <?php echo $tgl_surat_str ?></div>
                    <div><?php echo rtrim($jabatan_ptk, ',') . ',' ?></div>
                    
                    <div style="height: 65px;"></div>

                    <div style="font-weight: bold; text-decoration: underline;"><?php echo html_escape($nama_ptk_full) ?></div>
                    <div>NIY. <?php echo html_escape($niy_ptk) ?></div>
                </div>
            </div>

            <div style="clear: both;"></div>

            <!-- Footer Dokumen (Fixed di Paling Bawah Kertas, Tanpa Garis, Teks Abu-abu, plus Nomor Surat) -->
            <div class="qr-footer-fixed" style="position: absolute; bottom: 15mm; left: 22mm; right: 22mm; border-top: none; padding-top: 0; display: flex; align-items: center; gap: 14px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?php echo rawurlencode($validasi_url) ?>" style="width: 48px; height: 48px;" alt="QR Validasi">
                <div style="font-size: 7pt; color: #8f95a0ff; line-height: 1.2; font-family: Arial, sans-serif;">
                    <div><strong>Dokumen ini dikeluarkan dan diarsipkan melalui Aplikasi Miftahul Khoer Data Center.</strong></div>
                    <div>Validasi surat melalui Scan QR Code disamping.</div>
                    <div>Nomor: <?php echo html_escape($surat->nomor_surat) ?></div>
                </div>
            </div>

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
                    <?php elseif ($surat->layout_style === 'left_logo_center_text'): ?>
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

    <!-- Modal Konfirmasi Hapus Surat -->
    <div id="previewDeleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.55); z-index: 9999; align-items: center; justify-content: center; font-family: Arial, sans-serif;">
        <div style="background: #fff; border-radius: 16px; max-width: 420px; width: 90%; padding: 24px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
            <div style="width: 56px; height: 56px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; font-weight: bold; margin-bottom: 16px;">
                !
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #111827; font-weight: bold;">Konfirmasi Hapus Surat</h3>
            <p style="margin: 0 0 20px 0; font-size: 14px; color: #4b5563; line-height: 1.5;">
                Apakah Anda yakin ingin menghapus <strong>Surat Keluar Nomor: <?php echo html_escape($surat->nomor_surat) ?></strong>?<br>
                <span style="color: #dc2626; font-weight: 600;">Tindakan ini tidak dapat dibatalkan.</span>
            </p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" onclick="hideDeleteModal()" style="padding: 9px 18px; border: 1px solid #d1d5db; background: #fff; color: #374151; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Batal</button>
                <a href="<?php echo url('surat/keluar_hapus/' . $surat->id_surat_keluar) ?>" style="padding: 9px 18px; background: #dc2626; border: 1px solid #dc2626; color: #fff; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">Ya, Hapus Sekarang</a>
            </div>
        </div>
    </div>
    <script>
        function showDeleteModal() {
            document.getElementById('previewDeleteModal').style.display = 'flex';
        }
        function hideDeleteModal() {
            document.getElementById('previewDeleteModal').style.display = 'none';
        }
    </script>
</body>
</html>
