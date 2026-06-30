<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Validasi Surat</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 32px;
            color: #111827;
        }
        .box {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 24px;
        }
        .ok {
            color: #047857;
            font-weight: 700;
        }
        .bad {
            color: #b91c1c;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <main class="box">
        <?php if ($surat): ?>
            <h2 class="ok">Surat valid</h2>
            <p>Data surat ditemukan di aplikasi.</p>
            <table>
                <tr><td>Nomor Surat</td><td><?php echo $surat->nomor_surat ?></td></tr>
                <tr><td>Tanggal</td><td><?php echo date('d-m-Y', strtotime($surat->tanggal_surat)) ?></td></tr>
                <tr><td>Lembaga</td><td><?php echo $surat->nama_lembaga ?></td></tr>
                <tr><td>Tujuan</td><td><?php echo $surat->tujuan_surat ?></td></tr>
                <tr><td>Perihal</td><td><?php echo $surat->perihal ?></td></tr>
                <tr><td>Status</td><td><?php echo $surat->status ?></td></tr>
            </table>
        <?php else: ?>
            <h2 class="bad">Surat tidak ditemukan</h2>
            <p>Token validasi tidak cocok dengan data surat keluar di aplikasi.</p>
        <?php endif; ?>
    </main>
</body>
</html>
