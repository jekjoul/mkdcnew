<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buku Induk Siswa - <?php echo html_escape($siswa->nama_siswa); ?></title>
    <style>
        body {
            margin: 0;
            background: #e5e7eb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: #ffffff;
            border-bottom: 1px solid #d1d5db;
        }

        .print-toolbar button,
        .print-toolbar a {
            border: 1px solid #9ca3af;
            background: #ffffff;
            color: #111827;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }

        .print-toolbar .primary {
            background: #f59e0b;
            border-color: #f59e0b;
            color: #ffffff;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .print-toolbar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-toolbar">
        <button type="button" class="primary" onclick="window.print()">Print / Save PDF</button>
        <a href="<?php echo url('buku_induk_siswa/view/' . $siswa->id_siswa); ?>">Kembali ke View</a>
    </div>

    <?php include viewPath('buku_induk_siswa/partials/template'); ?>

    <?php if (!empty($auto_print)): ?>
        <script>
            window.addEventListener('load', function() {
                window.print();
            });
        </script>
    <?php endif; ?>
</body>

</html>
