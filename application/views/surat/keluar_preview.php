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
            display: grid;
            grid-template-columns: 78px 1fr;
            gap: 14px;
            align-items: center;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .kop img {
            max-width: 72px;
            max-height: 72px;
        }
        .kop h1 {
            margin: 0;
            font-size: 19px;
            text-align: center;
            text-transform: uppercase;
        }
        .kop p {
            margin: 4px 0 0;
            text-align: center;
            font-size: 12px;
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
            <div>
                <?php if (!empty($surat->logo)): ?>
                    <img src="<?php echo url('uploads/lembaga/' . $surat->logo) ?>" alt="Logo">
                <?php endif; ?>
            </div>
            <div>
                <h1><?php echo $surat->nama_lembaga ?></h1>
                <p><?php echo $surat->alamat ?: '' ?></p>
                <p><?php echo trim(($surat->telepon ? 'Telp. ' . $surat->telepon : '') . ($surat->email ? ' | Email: ' . $surat->email : '')) ?></p>
            </div>
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
