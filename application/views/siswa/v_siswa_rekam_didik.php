<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- meta tags and other links -->


<div class="dashboard-main-body">

    <div class="row gy-4 mb-24">

        <div class="col-lg-12">
             <div class="card radius-12 h-100 shadow">
                                
                <div class="card-header py-16 px-24 bg-base d-flex align-items-center gap-1 justify-content-between border border-end-0 border-start-0 border-top-0 bg-gradient-start-3">
                    <h6 class="text-lg mb-0">DETAIL REKAM DIDIK SISWA</h6>
                    <button type="button" class="text-xl line-height-1">
                        <iconify-icon icon="icon-park-outline:user-business" class="text-xl"></iconify-icon> 
                    </button>
                </div>

                <div class="card-body py-16 px-24">
                    <div class="row">
                        
                        <div class="col-md-12 ">
                            <div class="row pt-20 pb-20 mb-40 py-16 px-24 bg-primary-success-gradient radius-10">
                                <div class="col-md-2">
                                    <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base">
                                        <div class="">
                                            <div class="text-center border border-top-0 border-start-0 border-end-0">
                                                <div class="card-body p-0 arrow-carousel ">
                                                    <div class=" bottom-0 start-0 h-100 radius-20">
                                                        <img src="<?php echo $url->assets ?>images/user-grid/siswa.jpg" alt="" class="w-100 h-100 object-fit-cover radius-20 ">
                                                        
                                                    </div>
                                                    
                                                
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5 mx-40 mt-40">
                                    <ul>
                                        <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">Nama</span>
                                            <span class="w-60 text-secondary-light fw-medium">: Mirna Rahmania</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">NIPD</span>
                                            <span class="w-60 text-secondary-light fw-medium">: 0778083335</span>
                                        </li>
                                         <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">NISN</span>
                                            <span class="w-60 text-secondary-light fw-medium">: 2425010040</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">Tahun Pelajaran</span>
                                            <span class="w-60 text-secondary-light fw-medium">: 2025/2026 Genap</span>
                                        </li>
                                      
                                    </ul>
                                </div>

                                <div class="col-md-4 mt-40">
                                    <ul>
                                      
                                        <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">Rombel</span>
                                            <span class="w-60 text-secondary-light fw-medium">: VIII Syafi'i</span>
                                        </li>
                                         <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">Wali Kelas</span>
                                            <span class="w-60 text-secondary-light fw-medium">: Yulianni</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-1 mb-12">
                                            <span class="w-30 text-md fw-semibold text-primary-light">Rombel</span>
                                            <span class="w-60 text-secondary-light fw-medium">: VIII Syafi'i</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-5">
                                    <ul>
                                        
                                    </ul>
                                </div>
                                
                                
                            </div>
                            <!-- <hr class="hrh mb-40 mt-20"> -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-lg mb-10">A. Presensi Siswa</h6>
                                    <div class="table-responsive">
                                        <table class="table bordered-table mb-0" >
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center">Persentasi Hadir</th>
                                                    <th scope="col" class="text-center">Sakit</th>
                                                    <th scope="col" class="text-center">Izin</th>
                                                    <th scope="col" class="text-center">Tanpa Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center bg-success-100">80%</td>
                                                    <td class="text-center">1 Hari</td>
                                                    <td class="text-center">2 Hari</td>
                                                    <td class="text-center">4 Hari</td>
                                                </tr>
                                                

                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-lg mb-10">B. Perilaku Siswa</h6>
                                    <div class="table-responsive">
                                        <table class="table bordered-table mb-0" >
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center">Ucapan</th>
                                                    <th scope="col" class="text-center">Kelakuan</th>
                                                    <th scope="col" class="text-center">Kerapihan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center bg-danger-100">Belum Baik</td>
                                                    <td class="text-center bg-success-100 ">Sudah Berkembang</td>
                                                    <td class="text-center bg-warning-100">Sedang Berkembang</td>
                                                </tr>
                                                

                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="row mt-40">
                                <div class="col-md-12">
                                    <h6 class="text-lg mb-10">C. Catatan Siswa</h6>
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0" >
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center">No</th>
                                                    <th scope="col" class="">Waktu</th>
                                                    <th scope="col" class="">Jenis Catatan</th>
                                                    <th scope="col" class="">Detail</th>
                                                    <th scope="col" class="">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>08.00 12 Mei 2025</td>
                                                    <td class="">Pelanggaran</td>
                                                    <td class="">Telat datang kesekolah, nongkrong di warung.</td>
                                                    <td class="">10 Poin Pelanggaran</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>12.45 22 Agustus 2025</td>
                                                    <td class="">Pelanggaran</td>
                                                    <td class="">Berkelahi dengan teman.</td>
                                                    <td class="">20 Poin Pelanggaran</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            
                            </div>

                            <div class="row mt-40">
                                <div class="col-md-12">
                                    <h6 class="text-lg mb-10">D. Nilai Pengetahuan</h6>
                                    <div class="table-responsive">
                                        <table class="table vertical-striped-table mb-0" >
                                            <thead>
                                                <tr>
                                                    <th scope="col" rowspan="2" class="text-center valign-middle">No</th>
                                                    <th scope="col" rowspan="2" class="text-center valign-middle">Mata Pelajaran</th>
                                                    <th scope="col" colspan="5" class="text-center th-nopadding">Nilai</th>
                                                    <th scope="col" rowspan="2" class="text-center valign-middle">Keterangan</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col"  class="text-center th-nopadding">Hr1</th>
                                                    <th scope="col"  class="text-center th-nopadding">Hr2</th>
                                                    <th scope="col"  class="text-center th-nopadding">PTS</th>
                                                    <th scope="col"  class="text-center th-nopadding">PAS</th>
                                                    <th scope="col"  class="text-center th-nopadding">NR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>Pendidikan Agama Islam & Budi Pekerti</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>Pendidikan Pancasila</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td>Bahasa Indonesia</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td>Matematika (Umum)</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td>Ilmu Pengetahuan Alam (IPA)</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td>Ilmu Pengetahuan Sosial (IPS)</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td>Bahasa Inggris</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td>Seni & Budaya</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">9</td>
                                                    <td>Pendidikan Jasmani, Olahraga dan Kesehatan</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">10</td>
                                                    <td>Informatika</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">11</td>
                                                    <td>Bahasa Sunda</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">12</td>
                                                    <td>Qur'an Hadits</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td class="text-center">87</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                                            <a href="<?php echo url('siswa/detail') ?>" class="btn rounded-pill btn-info-100 text-info-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"> 
                                                                <iconify-icon icon="bi:display-fill" class="menu-icon"></iconify-icon></iconify-icon> Lihat
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
        </div>
        

    </div>
    
   
</div>


<?php include viewPath('includes/footer'); ?>
<script>
  let table = new DataTable('#dataTable');
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]'); 
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)); 

    // Boxed Tooltip
    $(document).ready(function() {
        $('.tooltip-button').each(function () {
            var tooltipButton = $(this);
            var tooltipContent = $(this).siblings('.my-tooltip').html(); 
    
            // Initialize the tooltip
            tooltipButton.tooltip({
                title: tooltipContent,
                trigger: 'hover',
                html: true
            });
    
            // Optionally, reinitialize the tooltip if the content might change dynamically
            tooltipButton.on('mouseenter', function() {
                tooltipButton.tooltip('dispose').tooltip({
                    title: tooltipContent,
                    trigger: 'hover',
                    html: true
                }).tooltip('show');
            });
        });
    });
</script>
<script>
    var rtlDirection = $('html').attr('dir') === 'rtl';
  // ================================ Default Slider Start ================================ 
  $('.default-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: false, 
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        rtl: rtlDirection
    });

    // Arrow Carousel
  $('.arrow-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: true, 
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection
    });

    // pagination carousel
    $('.pagination-carousel').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1, 
        arrows: false, 
        dots: true,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection
    });
    
    // multiple carousel
    $('.multiple-carousel').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1, 
        arrows: false, 
        dots: true,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2000,
        speed: 600,
        gap: 24,
        prevArrow: '<button type="button" class="slick-prev"><iconify-icon icon="ic:outline-keyboard-arrow-left" class="menu-icon"></iconify-icon></button>',
        nextArrow: '<button type="button" class="slick-next"><iconify-icon icon="ic:outline-keyboard-arrow-right" class="menu-icon"></iconify-icon></button>',
        rtl: rtlDirection,
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                slidesToShow: 3,
                }
            },
            {
                breakpoint: 991,
                settings: {
                slidesToShow: 2,
                }
            },
            {
                breakpoint: 575,
                settings: {
                slidesToShow: 1,
                }
            },
        ]
    });

    // carousel with progress bar
    jQuery(document).ready(function($) {
        var sliderTimer = 5000;
        var beforeEnd = 500;
        var $imageSlider = $('.progress-carousel');
        $imageSlider.slick({
            autoplay: true,
            autoplaySpeed: sliderTimer,
            speed: 1000,
            arrows: false,
            dots: false,
            adaptiveHeight: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            rtl: rtlDirection
        });

        function progressBar(){
            $('.slider-progress').find('span').removeAttr('style');
            $('.slider-progress').find('span').removeClass('active');
            setTimeout(function(){
                $('.slider-progress').find('span').css('transition-duration', (sliderTimer/1000)+'s').addClass('active');
            }, 100);
        }
        progressBar();
        $imageSlider.on('beforeChange', function(e, slick) {
            progressBar();
        });
        $imageSlider.on('afterChange', function(e, slick, nextSlide) {
            titleAnim(nextSlide);
        });

        // Title Animation JS
        function titleAnim(ele){
            $imageSlider.find('.slick-current').find('h1').addClass('show');
            setTimeout(function(){
                $imageSlider.find('.slick-current').find('h1').removeClass('show');
            }, sliderTimer - beforeEnd);
        }
        titleAnim();
    });
  // ================================ Default Slider End ================================ 
</script>