<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="<?php echo url('') ?>" class="sidebar-logo shadow">
            <img src="<?php echo $url->assets ?>images/logodc.png" alt="site logo" class="light-logo">
            <img src="<?php echo $url->assets ?>images/logodc.png" alt="site logo" class="dark-logo">
            <img src="<?php echo $url->assets ?>images/logo-icon.png" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            <li>
                <a href="<?php echo url('') ?>">
                    <iconify-icon icon="solar:home-angle-2-linear" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-menu-group-title">Kelembagaan</li>

            <li>
                <a href="<?php echo url('lembaga') ?>">
                    <iconify-icon icon="solar:home-linear" class="menu-icon"></iconify-icon>
                    <span>Lembaga</span>
                </a>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:maps-square-01" class="menu-icon"></iconify-icon>
                    <span>Sarpras</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="<?php echo url('sarpras/tanah') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Tanah
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('sarpras/bangunan') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Bangunan/Gedung
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('sarpras/ruangan') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Ruangan
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('sarpras/alat') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Alat, Buku & Kendaraan
                        </a>
                    </li>

                </ul>
            </li>

            <li class="sidebar-menu-group-title">Kepegawaian</li>
            <li>
                <a href="<?php echo url('ptk/ptk') ?>">
                    <iconify-icon icon="icon-park-outline:user-business" class="menu-icon"></iconify-icon>
                    <span>Data PTK</span>
                </a>
            </li>
            <li>
                <a href="<?php echo url('ptk/ptkNonaktif') ?>">
                    <iconify-icon icon="icon-park-outline:wrong-user" class="menu-icon"></iconify-icon>
                    <span>Data PTK Nonaktif</span>
                </a>
            </li>
            <li>
                <a href="<?php echo url('sync_dapodik_ptk') ?>">
                    <iconify-icon icon="lucide:refresh-cw" class="menu-icon"></iconify-icon>
                    <span>Sinkron Dapodik GTK</span>
                </a>
            </li>

            <li class="sidebar-menu-group-title">Kesiswaan</li>


            <li>
                <a href="<?php echo url('siswa/all') ?>">
                    <iconify-icon icon="icon-park-outline:every-user" class="menu-icon"></iconify-icon>
                    <span>Data Siswa</span>
                </a>
            </li>

            <li>
                <a href="<?php echo url('sync_dapodik') ?>">
                    <iconify-icon icon="lucide:refresh-cw" class="menu-icon"></iconify-icon>
                    <span>Sinkron Dapodik</span>
                </a>
            </li>

            <?php
            $CI = &get_instance();
            $CI->db->select('p.id_pembelajaran, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel');
            $CI->db->from('pembelajaran p');
            $CI->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
            $CI->db->join('master_tingkat_sekolah t', 'p.id_tingkat_sekolah = t.id_tingkat_sekolah');
            $CI->db->join('rombel r', 'p.id_rombel = r.id_rombel');
            $CI->db->order_by('l.nama_lembaga', 'ASC');
            $CI->db->order_by('t.tingkat_angka', 'ASC');
            $CI->db->order_by('r.nama_rombel', 'ASC');
            $menu_pembelajaran_siswa = $CI->db->get()->result();
            $menu_siswa_lembaga = [];
            foreach ($menu_pembelajaran_siswa as $menu_row) {
                $menu_siswa_lembaga[$menu_row->nama_lembaga][] = $menu_row;
            }
            ?>
            <?php foreach ($menu_siswa_lembaga as $nama_lembaga => $menu_rows): ?>
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="material-symbols:article-person-outline" class="menu-icon"></iconify-icon>
                        <span>Data Siswa <?php echo $nama_lembaga ?></span>
                    </a>
                    <ul class="sidebar-submenu">
                        <?php foreach ($menu_rows as $menu_row): ?>
                            <li>
                                <a href="<?php echo url('siswa/pembelajaran/' . $menu_row->id_pembelajaran) ?>">
                                    <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                                    <?php echo $menu_row->nama_tingkat . ' - ' . $menu_row->nama_rombel ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>


            <li class="sidebar-menu-group-title">Pembelajaran</li>


            <li class="dropdown">
                <a href="#">
                    <iconify-icon icon="material-symbols:bookmark-added-outline" class="menu-icon"></iconify-icon>
                    <span>Pembelajaran</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="<?php echo url('pembelajaran') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Daftar Pembelajaran
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('pembelajaran/tambah') ?>">
                            <i class="ri-circle-fill circle-icon text-primary-main w-auto"></i>
                            Atur Pembelajaran Baru
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="<?php echo url('datasiswa') ?>">
                    <iconify-icon icon="akar-icons:schedule" class="menu-icon"></iconify-icon>
                    <span>Jadwal Pelajaran</span>
                </a>
            </li>


            <li>
                <a href="<?php echo url('tahun_pelajaran') ?>">
                    <iconify-icon icon="material-symbols:punch-clock-outline-sharp" class="menu-icon"></iconify-icon>
                    <span>Tahun Pelajaran</span>
                </a>
            </li>


            <li class="sidebar-menu-group-title">Master Data</li>


            <li>
                <a href="<?php echo url('master/lembaga') ?>">
                    <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                    <span>Master Lembaga</span>
                </a>
            </li>

            <li class="sidebar-menu-item">
                <a href="<?php echo url('master/tingkatSekolah') ?>" class="sidebar-menu-link">
                    <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                    <span>Master Tingkat</span>
                </a>
            </li>


            <li class="sidebar-menu-item">
                <a href="<?php echo url('master/rombel') ?>" class="sidebar-menu-link">
                    <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                    <span>Master Rombel</span>
                </a>
            </li>

            <li>
                <a href="<?php echo url('master/mapel') ?>">
                    <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                    <span>Master Mata Pelajaran</span>
                </a>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="material-symbols:database" class="menu-icon"></iconify-icon>
                    <span>Master Sarana</span>
                </a>

                <ul class="sidebar-submenu">
                    <li>
                        <a href="<?php echo url('master/jenisRuangan') ?>">
                            <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Jenis Ruangan
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('master/jenisSarana') ?>">
                            <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Jenis Sarana
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo url('master/standarSarana') ?>">
                            <i class="ri-circle-fill circle-icon text-default-600 w-auto"></i> Standar Sarana
                        </a>
                    </li>


                </ul>
            </li>


            <li class="sidebar-menu-group-title">Manajeman Akun</li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="clarity:users-solid" class="menu-icon"></iconify-icon>
                    <span>Akun</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="<?php echo url('users') ?>"><i class="ri-circle-fill circle-icon text-default-600 w-auto"></i>
                            Daftar Akun</a>
                    </li>
                    <li>
                        <a href="<?php echo url('users/add') ?>"><i class="ri-circle-fill circle-icon text-default-main w-auto"></i>
                            Tambah Akun</a>
                    </li>


                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="simple-icons:openaccess" class="menu-icon"></iconify-icon>
                    <span>Hak Akses</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="<?php echo url('roles') ?>"><i class="ri-circle-fill circle-icon text-default-600 w-auto"></i>
                            Roles</a>
                    </li>
                    <li>
                        <a href="<?php echo url('permissions') ?>"><i class="ri-circle-fill circle-icon text-default-main w-auto"></i>
                            Permission</a>
                    </li>


                </ul>
            </li>


            <li class="sidebar-menu-group-title">contoh</li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="index-2.html"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                            AI</a>
                    </li>
                    <li>
                        <a href="index-2.html"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i>
                            CRM</a>
                    </li>

                </ul>
            </li>


        </ul>
        </li>
        </ul>
    </div>
</aside>
