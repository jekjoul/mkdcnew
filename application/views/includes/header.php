<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <script>
        (function() {
            var savedTheme = localStorage.getItem("theme");
            if (savedTheme === "dark") {
                document.documentElement.setAttribute("data-theme", "dark");
            } else {
                document.documentElement.setAttribute("data-theme", "light");
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#487fff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>MKDC | <?php echo $page->title ?></title>
    <link rel="icon" type="image/png" href="<?php echo $url->assets ?>images/logodc_round.png" sizes="16x16">
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/remixicon.css">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/bootstrap.min.css">
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/apexcharts.css">
    <!-- Data Table css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/dataTables.min.css">
    <!-- Text Editor css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/editor-katex.min.css">
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/editor.atom-one-dark.min.css">
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/editor.quill.snow.css">
    <!-- Date picker css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/flatpickr.min.css">
    <!-- Calendar css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/full-calendar.css">
    <!-- Vector Map css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/jquery-jvectormap-2.0.5.css">
    <!-- Popup css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/magnific-popup.css">
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/slick.css">
    <!-- prism css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/prism.css">
    <!-- file upload css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/file-upload.css">

    <link rel="stylesheet" href="<?php echo $url->assets ?>css/lib/audioplayer.css">
    <!-- main css -->
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/style.css">
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/dark-theme.css">
    <link rel="stylesheet" href="<?php echo $url->assets ?>css/mobile-app.css">
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.2/dist/sweetalert2.min.css " rel="stylesheet">

    <!-- Offline Detector Style -->
    <style>
        #mkdc-offline-banner {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            z-index: 99999;
            background: linear-gradient(90deg, #dc2626, #ef4444);
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.84rem;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(220,38,38,0.35);
            transition: top 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #mkdc-offline-banner.show { top: 0; }
        #mkdc-offline-banner .offline-banner-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #mkdc-offline-banner .offline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            animation: mkdcDotBlink 1s ease-in-out infinite;
        }
        @keyframes mkdcDotBlink {
            0%, 100% { opacity: 1; } 50% { opacity: 0.2; }
        }
        #mkdc-offline-banner a.offline-retry-link {
            color: white;
            text-decoration: underline;
            cursor: pointer;
            font-weight: 700;
            white-space: nowrap;
        }
        #mkdc-offline-banner a.offline-retry-link:hover { opacity: 0.85; }
    </style>

    <style>
        @media screen {
            html {
                font-size: 14px !important;
            }
        }

        @media only screen and (max-width: 600px) {
            .mobile-hide {
                display: none !important;
            }
        }

        .fullwidth {
            width: 100% !important;
        }

        .card-hover:hover {
            opacity: 70% !important;
        }

        /* ── Scroll Glitch Fix ────────────────────────────────────────────────
         * Masalah: elemen position:sticky / position:fixed yang berada dalam
         * stacking context yang sama dengan konten scroll bisa menyebabkan
         * "glitch" (jitter / flicker) karena browser harus melakukan repaint
         * setiap frame scroll.
         *
         * Solusi:
         * 1. Paksa elemen fixed/sticky ke GPU layer tersendiri (translateZ/will-change)
         * 2. Gunakan contain:layout style agar browser tidak perlu recalc layout global
         * 3. Hindari backdrop-filter pada elemen yang bergerak atau menempel di sisi layar
         * ─────────────────────────────────────────────────────────────────── */

        /* Navbar header — selalu sticky di atas */
        .navbar-header {
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: transform;
            contain: layout style;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Sidebar — fixed di sisi kiri */
        .sidebar {
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Konten utama — isolasi scroll context agar tidak terpengaruh layer fixed */
        .dashboard-main-body {
            isolation: isolate;
        }

        /* Smooth scroll di seluruh app */
        html {
            scroll-behavior: smooth;
        }

        /* Hindari sub-pixel rendering artifact pada tabel saat scroll */
        table {
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }

        /* Animasi card hover — gunakan opacity bukan transform untuk performa lebih baik */
        .card-hover:hover {
            opacity: 0.72 !important;
            transition: opacity 0.18s ease;
        }
    </style>

    <!-- ── Service Worker: Cache halaman offline agar tampil saat server mati ── -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker
                    .register('/mkdcnew/service-worker.js', { scope: '/mkdcnew/' })
                    .catch(function(err) {
                        console.warn('[MKDC SW] Registrasi gagal:', err);
                    });
            });
        }
    </script>
</head>

<body>
    <!-- ── Offline Banner (muncul otomatis jika internet terputus saat di halaman ini) ── -->
    <div id="mkdc-offline-banner" role="alert" aria-live="assertive" style="display:none;">
        <div class="offline-banner-left">
            <span class="offline-dot"></span>
            <span>&#128683; Tidak ada koneksi internet &mdash; beberapa fitur mungkin tidak berfungsi.</span>
        </div>
        <a class="offline-retry-link" onclick="window.location.reload()">Muat Ulang</a>
    </div>
    <script>
        // ── MKDC Offline Detector ──────────────────────────────────────────────
        (function() {
            var banner = document.getElementById('mkdc-offline-banner');
            if (!banner) return;

            function showBanner() {
                banner.style.display = 'flex';
                setTimeout(function() { banner.classList.add('show'); }, 10);
            }

            function hideBanner() {
                banner.classList.remove('show');
                setTimeout(function() { banner.style.display = 'none'; }, 450);
            }

            if (!navigator.onLine) { showBanner(); }

            window.addEventListener('offline', showBanner);
            window.addEventListener('online', function() {
                // Verifikasi koneksi nyata sebelum sembunyikan banner
                fetch('/mkdcnew/favicon.ico?_=' + Date.now(), {
                    method: 'HEAD', cache: 'no-store', mode: 'no-cors'
                }).then(hideBanner).catch(showBanner);
            });
        })();
    </script>

    <!-- Mobile Page Loading Spinner / Preloader -->
    <div id="mobile-page-loader" class="mobile-page-loader">
        <div class="mobile-loader-container">
            <div class="mobile-loader-spinner-wrapper">
                <div class="mobile-loader-ring"></div>
                <img src="<?php echo $url->assets ?>images/logo-icon.png" alt="Logo" class="mobile-loader-logo">
            </div>
            <div class="mobile-loader-text">
                <span class="mobile-loader-label">Memuat</span>
                <span class="mobile-loader-dots"><span>.</span><span>.</span><span>.</span></span>
            </div>
        </div>
    </div>
    <main class="dashboard-main">
        <div class="navbar-header shadow">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <button type="button" class="sidebar-toggle">
                            <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active">
                            </iconify-icon>
                            <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                        </button>
                        <button type="button" class="sidebar-mobile-toggle">
                            <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                        </button>
                        <ul class="d-flex align-items-center gap-2 mobile-hide">
                            <li class="fw-medium">
                                <a href="<?php echo url($page->titleUrl) ?>" class="d-flex align-items-center gap-1 hover-text-primary">
                                    <iconify-icon icon="<?php echo $page->icon; ?>" class="icon text-lg"></iconify-icon>
                                    <?php echo $page->title; ?>
                                </a>
                            </li>
                            <?php if (isset($page->subtitle)) { ?>
                                <li>/</li>
                                <li class="fw-medium">
                                    <a href="<?php echo url($page->subtitleUrl) ?>" class="d-flex align-items-center gap-1 hover-text-primary"> <?php echo $page->subtitle; ?></a>
                                </li>
                            <?php } ?>
                            <?php if (isset($page->subsubtitle)) { ?>
                                <li>/</li>
                                <li class="fw-medium">
                                    <?php echo $page->subsubtitle; ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <?php
                        $user_id = logged('id');
                        $CI = &get_instance();
                        $current_user = $CI->db->get_where('users', ['id' => $user_id])->row();
                        $is_google_connected = ($current_user && !empty($current_user->google_id));
                        ?>
                        <div class="d-none d-sm-block d-flex align-items-center gap-2 me-12">
                            <?php if ($is_google_connected): ?>
                                <span class="badge bg-success-focus text-success-main radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="logos:google-icon" class="align-middle"></iconify-icon>
                                    <span class="text-xs fw-semibold"><?php echo html_escape($current_user->name); ?></span>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-neutral-200 text-neutral-600 radius-8 px-12 py-8 d-inline-flex align-items-center gap-1">
                                    <span class="text-xs fw-semibold"><?php echo html_escape($current_user->name); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <button type="button" data-theme-toggle
                            class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                        <!-- <div class="dropdown">
                            <button
                                class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                                type="button" data-bs-toggle="dropdown">
                                <iconify-icon icon="mage:email" class="text-primary-light text-xl"></iconify-icon>
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                                <div
                                    class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <h6 class="text-lg text-primary-light fw-semibold mb-0">Message</h6>
                                    </div>
                                    <span
                                        class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                                </div>

                                <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-3.png" alt="">
                                                <span
                                                    class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey!
                                                    there i’m...</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                            <span
                                                class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                        </div>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-4.png" alt="">
                                                <span
                                                    class="w-8-px h-8-px  bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey!
                                                    there i’m...</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                            <span
                                                class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">2</span>
                                        </div>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-5.png" alt="">
                                                <span
                                                    class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey!
                                                    there i’m...</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                            <span
                                                class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                        </div>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-6.png" alt="">
                                                <span
                                                    class="w-8-px h-8-px bg-neutral-300 rounded-circle position-absolute end-0 bottom-0"></span>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey!
                                                    there i’m...</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                            <span
                                                class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-neutral-400 rounded-circle">0</span>
                                        </div>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 position-relative">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-7.png" alt="">
                                                <span
                                                    class="w-8-px h-8-px bg-success-main rounded-circle position-absolute end-0 bottom-0"></span>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Kathryn Murphy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-100-px">hey!
                                                    there i’m...</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-sm text-secondary-light flex-shrink-0">12:30 PM</span>
                                            <span
                                                class="mt-4 text-xs text-base w-16-px h-16-px d-flex justify-content-center align-items-center bg-warning-main rounded-circle">8</span>
                                        </div>
                                    </a>

                                </div>
                                <div class="text-center py-12 px-16">
                                    <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See
                                        All Message</a>
                                </div>
                            </div>
                        </div> -->
                        <!-- Message dropdown end -->

                        <!-- <div class="dropdown">
                            <button
                                class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                                type="button" data-bs-toggle="dropdown">
                                <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                                <div
                                    class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                                    </div>
                                    <span
                                        class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                                </div>

                                <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                <iconify-icon icon="bitcoin-icons:verify-outline"
                                                    class="icon text-xxl"></iconify-icon>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Congratulations</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">Your
                                                    profile has been Verified. Your profile has been Verified</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-1.png" alt="">
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Ronald Richards</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">You can
                                                    stitch between artboards</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                AM
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Arlene McCoy</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite
                                                    you to prototyping</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                <img src="<?php echo $url->assets ?>images/notification/profile-2.png" alt="">
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Annette Black</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite
                                                    you to prototyping</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                    </a>

                                    <a href="javascript:void(0)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                        <div
                                            class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span
                                                class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                DR
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Darlene Robertson</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite
                                                    you to prototyping</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                                    </a>
                                </div>

                                <div class="text-center py-12 px-16">
                                    <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See
                                        All Notification</a>
                                </div>

                            </div>
                        </div> -->
                        <!-- Notification dropdown end -->



                        <div class="dropdown">
                            <button class="d-flex justify-content-center align-items-center rounded-circle"
                                type="button" data-bs-toggle="dropdown">
                                <img src="<?php echo userProfile(logged('id')) ?>" alt="image"
                                    class="w-40-px h-40-px object-fit-cover rounded-circle">
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-sm">
                                <div
                                    class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <h6 class="text-lg text-primary-light fw-semibold mb-2"><?php echo logged('name') ?></h6>
                                    </div>
                                    <button type="button" class="hover-text-danger">
                                        <iconify-icon icon="radix-icons:cross-1" class="icon text-xl">
                                        </iconify-icon>
                                    </button>
                                </div>
                                <ul class="to-top-list">
                                    <li>
                                        <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                            href="<?php echo url('profile') ?>">
                                            <iconify-icon icon="solar:user-linear" class="icon text-xl">
                                            </iconify-icon> My Profile
                                        </a>
                                    </li>
                                    <?php if (logged('role') == 1): ?>
                                        <!-- <li>
                                            <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                                href="email.html">
                                                <iconify-icon icon="tabler:message-check" class="icon text-xl">
                                                </iconify-icon> Inbox
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                                href="company.html">
                                                <iconify-icon icon="icon-park-outline:setting-two" class="icon text-xl">
                                                </iconify-icon> Setting
                                            </a>
                                        </li> -->
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                            href="<?php echo url('logout') ?>">
                                            <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                            Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- Profile dropdown end -->
                    </div>
                </div>
            </div>
        </div>

        <?php include viewPath('includes/aside'); ?>




        <?php if ($this->session->flashdata('alert')): $time = time();  ?>

            <?php if ($this->session->flashdata('alert-type') == "success") { ?>

                <section>
                    <div id="myAlert" class="alert alert-success bg-success-100 text-success-600 border-success-100 px-24 py-11 m-20 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between alert-fade show position-absolute top-10 end-0" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="akar-icons:double-check" class="icon text-xl"></iconify-icon>
                            <?php echo $this->session->flashdata('alert') ?>
                        </div>
                    </div>
                </section>

            <?php } else { ?>
                <section>
                    <div id="myAlert" class="alert alert-danger bg-danger-100 text-danger-600 border-danger-100 px-24 py-11 m-20 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between alert-fade show position-absolute top-10 end-0" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:danger-triangle-bold" class="icon text-xl"></iconify-icon>
                            <?php echo $this->session->flashdata('alert') ?>
                        </div>
                    </div>
                </section>

        <?php }
        endif ?>