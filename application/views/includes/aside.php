<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo ">
        <a href="index-2.html" class="app-brand-link">
            <span class="app-brand-logo demo" style="width:auto; height:40px">
                <img src="<?php echo $url->assets ?>img/mkdc_long_mini.png" style="height:30px">
            </span>
            <!-- <span class="app-brand-text demo menu-text fw-bold ms-2">MKDC</span> -->
        </a>

        <a
            href="javascript:void(0);"
            class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0  "></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php if($page->menu=="Dashboard"){echo 'active';}?>" >
            <a href="<?php echo url('dashboard') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!-- End of Dashboards -->

        <!-- Lembaga -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >Kelembagaan</span>
        </li>
         
        <li class="menu-item <?php if($page->menu=="lembaga"){echo 'active open';}?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                <div>Data Lembaga</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if($page->submenu=="lembaga_list"){echo 'active';}?>">
                    <a href="<?php echo url('lembaga') ?>" class="menu-link">
                        <div>Daftar Lembaga</div>
                    </a>
                </li>
                
             
            </ul>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons fa fa-map"></i>
                <div >Tanah & Bangunan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-chair"></i>
                <div >Sarana</div>
            </a>
        </li>
        <!-- End of Lembaga -->

        <!-- Kesiswaan -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >Kesiswaan</span>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-users"></i>
                <div > Semua Siswa</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-user-tag"></i>
                <div>Data Siswa SMP</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div>VII - Syar'i</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div>VII - Walid</div>
                    </a>
                </li>
             
            </ul>
        </li>

        <!-- End of Kesiswaan -->

        <!-- Kepegawaian -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >KEPEGAWAIAN</span>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-user-tie"></i>
                <div > Data PTK</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-user-tie"></i>
                <div > Data PTK Nonaktif</div>
            </a>
        </li>

        <!-- End of Kepegawaian -->

        <!-- Pembelajaran -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >PEMBELAJARAN</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-house-chimney-user"></i>
                <div>Rombongan Belajar</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMP</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMA</div>
                    </a>
                </li>
             
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-swatchbook"></i>
                <div>Data Pembelajaran</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMP</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMA</div>
                    </a>
                </li>
             
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-fingerprint"></i>
                <div>Absensi Siswa</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMP</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMA</div>
                    </a>
                </li>
             
            </ul>
        </li>

        <!-- End of Pembelajaran -->

        <!-- Unduhan -->
         <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >SURAT</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-envelope"></i>
                <div>Surat Keluar</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >Semua Surat Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMP</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMA</div>
                    </a>
                </li>
             
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-envelope-open"></i>
                <div>Surat Masuk</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >Semua Surat Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMP</div>
                    </a>
                </li>
             
            </ul>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div >SMA</div>
                    </a>
                </li>
             
            </ul>
        </li>
        <!-- End of Unduhan -->

        <!-- Unduhan -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >UNDUHAN</span>
        </li>
        <!-- End of Unduhan -->

        <!-- Arsip -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >ARSIP</span>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-book"></i>
                <div > Buku Induk</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-users-line"></i>
                <div > Data Alumni</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon fa fa-users-viewfinder"></i>
                <div > Data Koordinator</div>
            </a>
        </li>
        <!-- End of Arsip -->

        <!-- Manajemen Aplikasi -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" >MANAJEMEN APLIKASI</span>
        </li>
        

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons fa fa-users-gear"></i>
                <div>User/Pengguna</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons fa fa-clock-rotate-left"></i>
                <div>Log Aktivitas</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-door-open"></i>
                <div>Roles</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons fa fa-elevator"></i>
                <div>Permission</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons fa fa-database"></i>
                <div>Backup</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan Aplikasi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div>General Setting</div>
                    </a>
                </li>
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div>Pengaturan Yayasan</div>
                    </a>
                </li>
                <li class="menu-item ">
                    <a href="index-2.html" class="menu-link">
                        <div>Email Template</div>
                    </a>
                </li>
             
            </ul>
        </li>

        <!-- End of Manajemen Aplikasi -->
       
        
    </ul>

</aside>