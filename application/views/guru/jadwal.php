<?php include viewPath('includes/header'); ?>
<div class="dashboard-main-body">

    <?php
    // ── Bangun struktur data: slot_ke → hari → [entry, ...]
    $hariList  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $slotSet   = [];
    $hariSet   = [];
    $grid      = [];   // $grid[slot_ke][hari] = [row, ...]
    $mapelKeys = [];   // nama_mapel → indeks warna

    foreach ($items as $row) {
        $s = (int) $row->slot_ke;
        $h = $row->hari;
        $slotSet[$s] = true;
        $hariSet[$h] = true;
        $grid[$s][$h][] = $row;
        if (!isset($mapelKeys[$row->nama_mapel])) {
            $mapelKeys[$row->nama_mapel] = count($mapelKeys);
        }
    }

    ksort($slotSet);
    $slots = array_keys($slotSet);

    // Hanya tampilkan hari yang benar-benar ada di data (urut sesuai urutan baku)
    $hariTampil = array();
    foreach ($hariList as $h) {
        if (isset($hariSet[$h])) {
            $hariTampil[] = $h;
        }
    }

    // Palet warna — 12 warna berbeda, loop jika lebih
    $palette = [
        ['bg' => '#EDE9FE', 'border' => '#7C3AED', 'text' => '#4C1D95'],  // violet
        ['bg' => '#DCFCE7', 'border' => '#16A34A', 'text' => '#14532D'],  // green
        ['bg' => '#FEF9C3', 'border' => '#CA8A04', 'text' => '#713F12'],  // yellow
        ['bg' => '#FEE2E2', 'border' => '#DC2626', 'text' => '#7F1D1D'],  // red
        ['bg' => '#E0F2FE', 'border' => '#0284C7', 'text' => '#0C4A6E'],  // sky
        ['bg' => '#FCE7F3', 'border' => '#DB2777', 'text' => '#831843'],  // pink
        ['bg' => '#FFEDD5', 'border' => '#EA580C', 'text' => '#7C2D12'],  // orange
        ['bg' => '#D1FAE5', 'border' => '#059669', 'text' => '#064E3B'],  // emerald
        ['bg' => '#E0E7FF', 'border' => '#4338CA', 'text' => '#1E1B4B'],  // indigo
        ['bg' => '#FDF4FF', 'border' => '#A21CAF', 'text' => '#4A044E'],  // fuchsia
        ['bg' => '#F0FDF4', 'border' => '#15803D', 'text' => '#14532D'],  // lime
        ['bg' => '#FFF7ED', 'border' => '#C2410C', 'text' => '#7C2D12'],  // amber
    ];

    // ── Ambil pengaturan waktu dari DB (jam_mulai, menit_jp, istirahat per hari)
    $CI = &get_instance();
    $waktuSettings = array();
    if ($CI->db->table_exists('jadwal_pelajaran_pengaturan')) {
        $wRows = $CI->db->get('jadwal_pelajaran_pengaturan')->result();
        foreach ($wRows as $wr) {
            if (!isset($waktuSettings[$wr->hari])) {
                $waktuSettings[$wr->hari] = array(
                    'jam_mulai' => substr($wr->jam_mulai, 0, 5),
                    'menit_jp'  => (int) $wr->menit_jp,
                    'istirahat' => json_decode($wr->istirahat_json ?: '[]', true) ?: array(),
                );
            }
        }
    }

    // ── Hitung waktu mulai & selesai tiap slot berdasarkan pengaturan hari
    function hitungWaktuSlot($hari, $slot_ke, $waktuSettings) {
        if (!isset($waktuSettings[$hari])) return null;
        $s      = $waktuSettings[$hari];
        $menit  = (int) $s['menit_jp'] > 0 ? (int) $s['menit_jp'] : 40;
        $breaks = isset($s['istirahat']) ? $s['istirahat'] : array();
        // Map: setelah JP berapa, total menit istirahat
        $breakMap = array();
        foreach ($breaks as $b) {
            if (isset($b['after'], $b['duration'])) {
                $after = (int) $b['after'];
                $breakMap[$after] = isset($breakMap[$after]) ? $breakMap[$after] + (int)$b['duration'] : (int)$b['duration'];
            }
        }
        // Mulai dari jam_mulai
        $parts   = explode(':', $s['jam_mulai']);
        $timeMin = ((int)$parts[0] * 60) + (int)(isset($parts[1]) ? $parts[1] : 0);
        if (isset($breakMap[0])) $timeMin += $breakMap[0];
        for ($i = 1; $i <= (int)$slot_ke; $i++) {
            $mulai   = $timeMin;
            $selesai = $mulai + $menit;
            if ($i == (int)$slot_ke) {
                return array(
                    'mulai'   => sprintf('%02d.%02d', floor($mulai / 60), $mulai % 60),
                    'selesai' => sprintf('%02d.%02d', floor($selesai / 60), $selesai % 60),
                );
            }
            $timeMin = $selesai;
            if (isset($breakMap[$i])) $timeMin += $breakMap[$i];
        }
        return null;
    }

    function getMapelColor($idx, $palette) {
        return $palette[$idx % count($palette)];
    }
    ?>

    <style>
        .schedule-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .schedule-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 700px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
        }
        .schedule-table thead tr th {
            padding: 14px 10px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 2px solid #e5e7eb;
            vertical-align: middle;
        }
        .schedule-table thead th.th-slot {
            background: var(--bs-warning, #d4920a);
            color: #fff;
            text-align: center;
            width: 80px;
        }
        .schedule-table thead th.th-hari {
            background: var(--bs-warning, #d4920a);
            filter: brightness(0.88);
            color: #fff;
            text-align: center;
        }
        .schedule-table tbody tr:nth-child(even) td.td-slot {
            background: #fef9ec;
        }
        .schedule-table tbody tr:nth-child(odd) td.td-slot {
            background: #fdf3d7;
        }
        .td-slot {
            font-size: 13px;
            font-weight: 700;
            color: #7a4e00;
            text-align: center;
            padding: 10px 8px;
            border-right: 2px solid #e2e8f0;
            white-space: nowrap;
            vertical-align: middle;
        }
        .td-cell {
            padding: 6px 8px;
            vertical-align: top;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            min-width: 130px;
        }
        .td-cell:last-child {
            border-right: none;
        }
        .schedule-table tbody tr:last-child td {
            border-bottom: none;
        }
        .mapel-chip {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 8px 10px;
            border-radius: 10px;
            border-left: 4px solid;
            margin-bottom: 4px;
            transition: transform .15s, box-shadow .15s;
        }
        .mapel-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .mapel-chip:last-child {
            margin-bottom: 0;
        }
        .chip-mapel {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.3;
        }
        .chip-kelas {
            font-size: 11px;
            font-weight: 500;
            opacity: .75;
            line-height: 1.2;
        }
        .empty-cell {
            text-align: center;
            color: #cbd5e1;
            font-size: 18px;
        }
        .slot-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            color: #7a4e00;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 1px 4px rgba(0,0,0,.15);
        }

        /* Legend */
        .legend-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 20px;
            border-left: 4px solid;
            font-size: 12px;
            font-weight: 600;
        }
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .header-card {
            padding: 0;
        }
        .header-card h5 { margin: 0; font-weight: 700; font-size: 16px; }
        .header-card p  { margin: 4px 0 0; font-size: 13px; opacity: .75; }

        @media (max-width: 576px) {
            .td-slot, .td-cell { padding: 4px 4px; }
            .mapel-chip { padding: 6px 7px; }
            .chip-mapel { font-size: 11px; }
            .chip-kelas { font-size: 10px; }
        }
    </style>

    <div class="card" style="border-radius:16px; overflow:hidden;">
        <div class="card-header bg-warning-900 d-flex align-items-center justify-content-between gap-3">
            <div>
                <h6 class="text-light mb-0">Jadwal Mengajar Saya</h6>
                <small class="text-light mobile-hide" style="opacity:.75;">Tampilan tabel mingguan &mdash; jam pelajaran &times; hari</small>
            </div>
            <div class="text-end text-light" style="white-space:nowrap; font-size:13px; opacity:.8;">
                <?php echo date('l, d M Y'); ?>
            </div>
        </div>

        <div class="card-body" style="padding: 20px 20px;">

            <?php if (empty($items)): ?>
                <div class="text-center py-5">
                    <iconify-icon icon="akar-icons:schedule" style="font-size:48px;color:#94a3b8;"></iconify-icon>
                    <p class="text-muted mt-3">Belum ada jadwal mengajar yang ditetapkan.</p>
                </div>
            <?php else: ?>

                <!-- Legenda warna mata pelajaran -->
                <div class="legend-wrap mb-3">
                    <?php foreach ($mapelKeys as $nama => $idx):
                        $c = getMapelColor($idx, $palette);
                    ?>
                    <span class="legend-item" style="background:<?= $c['bg'] ?>;border-color:<?= $c['border'] ?>;color:<?= $c['text'] ?>;">
                        <span class="legend-dot" style="background:<?= $c['border'] ?>;"></span>
                        <?= html_escape($nama) ?>
                    </span>
                    <?php endforeach; ?>
                </div>

                <!-- TAMPILAN DESKTOP (Tabel Jadwal) -->
                <div class="schedule-wrapper d-none d-md-block">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th class="th-slot">
                                    <iconify-icon icon="bi:clock" style="font-size:16px;display:block;margin:0 auto 2px;"></iconify-icon>
                                    JP Ke
                                </th>
                                <?php foreach ($hariTampil as $hari): ?>
                                <th class="th-hari"><?= html_escape($hari) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($slots as $slot): ?>
                            <tr>
                                <td class="td-slot">
                                    <span class="slot-badge"><?= (int)$slot ?></span>
                                </td>
                                <?php foreach ($hariTampil as $hari): ?>
                                <td class="td-cell">
                                    <?php if (!empty($grid[$slot][$hari])): ?>
                                        <?php foreach ($grid[$slot][$hari] as $entry):
                                            $idx = $mapelKeys[$entry->nama_mapel];
                                            $c   = getMapelColor($idx, $palette);
                                            $kelasLabel = trim($entry->nama_tingkat . ' - ' . $entry->nama_rombel);
                                            $waktu = hitungWaktuSlot($hari, $slot, $waktuSettings);
                                            if ($waktu) {
                                                $kelasLabel .= ' (' . $waktu['mulai'] . '-' . $waktu['selesai'] . ')';
                                            }
                                            $mapelLabel = $entry->mapel_singkat ?: $entry->nama_mapel;
                                        ?>
                                        <div class="mapel-chip"
                                             style="background:<?= $c['bg'] ?>;border-color:<?= $c['border'] ?>;color:<?= $c['text'] ?>;"
                                             title="<?= html_escape($entry->nama_mapel) ?> — <?= html_escape(trim($entry->nama_tingkat . ' ' . $entry->nama_rombel)) ?>">
                                            <span class="chip-mapel"><?= html_escape($mapelLabel) ?></span>
                                            <span class="chip-kelas"><?= html_escape($kelasLabel) ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="empty-cell">–</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TAMPILAN MOBILE (Accordion Per Hari + Live Search) -->
                <div class="d-block d-md-none">
                    <!-- Form Search Mobile -->
                    <div class="mb-16">
                        <div class="position-relative">
                            <input type="text" id="mobileJadwalSearch" class="form-control text-sm radius-8 ps-40" placeholder="🔍 Cari Hari, Mapel, atau Rombel...">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light d-flex align-items-center">
                                <iconify-icon icon="solar:magnifer-linear" class="text-lg"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <div class="accordion custom-accordion" id="accordionGuruJadwalMobile">
                        <?php foreach ($hariTampil as $hIdx => $hari): ?>
                            <?php
                            // Kumpulkan semua jadwal mengajar pada hari ini
                            $jadwalHari = [];
                            foreach ($slots as $slot) {
                                if (!empty($grid[$slot][$hari])) {
                                    foreach ($grid[$slot][$hari] as $entry) {
                                        $entry->slot_ke = $slot;
                                        $jadwalHari[] = $entry;
                                    }
                                }
                            }
                            $accordionId = "collapseJadwalHari" . $hIdx;
                            $headingId   = "headingJadwalHari" . $hIdx;
                            $searchableText = strtolower(html_escape($hari));
                            foreach ($jadwalHari as $jh) {
                                $searchableText .= ' ' . strtolower(html_escape($jh->nama_mapel . ' ' . $jh->nama_tingkat . ' ' . $jh->nama_rombel));
                            }
                            ?>
                            <div class="accordion-item border radius-8 mb-12 mobile-jadwal-card" data-search="<?php echo $searchableText; ?>">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button <?php echo ($hIdx !== 0) ? 'collapsed' : ''; ?> px-16 py-12 text-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>" aria-expanded="<?php echo ($hIdx === 0) ? 'true' : 'false'; ?>">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-12">
                                            <span class="text-primary-600 fw-bold fs-6"><?php echo html_escape($hari); ?></span>
                                            <span class="badge bg-primary-50 text-primary-700 px-10 py-4 radius-4 text-xs" style="position: relative;right: 40px;"><?php echo count($jadwalHari); ?> Jam Mengajar</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse <?php echo ($hIdx === 0) ? 'show' : ''; ?>" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#accordionGuruJadwalMobile">
                                    <div class="accordion-body p-16 bg-neutral-50 radius-bottom-8">
                                        <?php if (!empty($jadwalHari)): ?>
                                            <div class="d-flex flex-column gap-12">
                                                <?php foreach ($jadwalHari as $entry): ?>
                                                    <?php
                                                    $idx = $mapelKeys[$entry->nama_mapel];
                                                    $c   = getMapelColor($idx, $palette);
                                                    $waktu = hitungWaktuSlot($hari, $entry->slot_ke, $waktuSettings);
                                                    $rombelStr = trim($entry->nama_tingkat . ' - ' . $entry->nama_rombel);
                                                    ?>
                                                    <div class="p-12 radius-8 border-start border-4 shadow-xs" style="background:<?php echo $c['bg']; ?>; border-color:<?php echo $c['border']; ?>!important; color:<?php echo $c['text']; ?>;">
                                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                                            <span class="badge px-8 py-2 radius-4 text-xs fw-bold" style="background:<?php echo $c['border']; ?>; color:#fff;">
                                                                JP Ke-<?php echo $entry->slot_ke; ?> <?php echo $waktu ? '(' . $waktu['mulai'] . ' - ' . $waktu['selesai'] . ' WIB)' : ''; ?>
                                                            </span>
                                                            <span class="fw-bold text-xs"><iconify-icon icon="solar:users-group-two-rounded-bold" class="me-2"></iconify-icon><?php echo html_escape($rombelStr); ?></span>
                                                        </div>
                                                        <div class="fw-bold text-sm mt-4"><?php echo html_escape($entry->nama_mapel); ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-12 text-secondary-light">
                                                <p class="text-xs mb-0">Tidak ada jadwal mengajar pada hari <?php echo html_escape($hari); ?>.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noMobileJadwalResult" class="text-center py-24 text-secondary-light d-none">
                        <iconify-icon icon="solar:magnifer-bug-linear" style="font-size: 32px;" class="mb-8"></iconify-icon>
                        <p class="text-sm">Jadwal mengajar tidak ditemukan.</p>
                    </div>
                </div>

                <!-- Ringkasan total -->
                <div class="mt-3 d-flex flex-wrap gap-3">
                    <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div style="font-size:22px;font-weight:800;color:#1e293b;"><?= count($items) ?></div>
                        <div style="font-size:12px;color:#64748b;">Total Jam Mengajar</div>
                    </div>
                    <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div style="font-size:22px;font-weight:800;color:#1e293b;"><?= count($mapelKeys) ?></div>
                        <div style="font-size:12px;color:#64748b;">Mata Pelajaran</div>
                    </div>
                    <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div style="font-size:22px;font-weight:800;color:#1e293b;"><?= count($hariTampil) ?></div>
                        <div style="font-size:12px;color:#64748b;">Hari Mengajar</div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>

</div>
<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $('#mobileJadwalSearch').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            let matchCount = 0;

            $('.mobile-jadwal-card').each(function() {
                let text = $(this).attr('data-search') || '';
                if (text.indexOf(q) !== -1) {
                    $(this).removeClass('d-none');
                    matchCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (matchCount === 0) {
                $('#noMobileJadwalResult').removeClass('d-none');
            } else {
                $('#noMobileJadwalResult').addClass('d-none');
            }
        });
    });
</script>
