<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php include viewPath('includes/header'); ?>

<style>
    #spinner {
        display: none;
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .iframe-container iframe{
        height: 25vmax;
        margin-bottom: 15px;
    }

    .iframe-block{
        width: 100%; 
        background:white; 
       
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Sarpras /</span>
        Tanah
    </h4>

    <div class="row">
        <div class="col-xl-12 col-12">
            <div class="card mb-4 d-flex align-items-stretch">
                <h5 class="card-header">Daftar Tanah Lembaga</h5>
                <div class="card-body">
                    <div class="demo-inline-spacing">
                        <button id="profil" class="btn btn-primary btn-section-block">
                            Default
                        </button>
                        <button id="siswa" class="btn btn-primary btn-section-block-overlay">
                            Overlay Color
                        </button>
                    </div>

                    <div class="border p-3 mb-2" id="section-block">
                        <div class="iframe-container">
                            <div class="spinner" id="spinner"></div>
                            <iframe id="myIframe" class="iframe-block" src="<?php echo url('sarpras/tanahList') ?> "></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--/ Project Cards -->
</div>


<script>
    button = document.getElementById('profil');
    spinner = document.getElementById('spinner');
    iframe = document.getElementById('myIframe');

    button.addEventListener('click', () => {
        iframe.style.display = 'none';
        spinner.style.display = 'block'; // Tampilkan spinner

        // Ganti URL di sini dengan URL yang ingin dimuat
        iframe.src = '<?php echo url('sarpras/tanahList') ?>';

        iframe.onload = function () {
            iframe.style.display = 'block';
            spinner.style.display = 'none'; // Sembunyikan spinner setelah iframe selesai memuat
        };
    });
</script>

<script>
    button = document.getElementById('siswa');
    spinner = document.getElementById('spinner');
    iframe = document.getElementById('myIframe');

    button.addEventListener('click', () => {
        iframe.style.display = 'none';
        spinner.style.display = 'block'; // Tampilkan spinner

        // Ganti URL di sini dengan URL yang ingin dimuat
        iframe.src = '<?php echo url('lembaga/detailLembagaSiswa') ?>';

        iframe.onload = function () {
            iframe.style.display = 'block';
            spinner.style.display = 'none'; // Sembunyikan spinner setelah iframe selesai memuat
        };
    });
</script>

<script>
    button = document.getElementById('ptk');
    spinner = document.getElementById('spinner');
    iframe = document.getElementById('myIframe');

    button.addEventListener('click', () => {
        iframe.style.display = 'none';
        spinner.style.display = 'block'; // Tampilkan spinner

        // Ganti URL di sini dengan URL yang ingin dimuat
        iframe.src = '<?php echo url('
        lembaga / detailLembagaPTK ') ?>';

        iframe.onload = function () {
            iframe.style.display = 'block';
            spinner.style.display = 'none'; // Sembunyikan spinner setelah iframe selesai memuat
        };
    });
</script>

<script>
    button = document.getElementById('sarpras');
    spinner = document.getElementById('spinner');
    iframe = document.getElementById('myIframe');

    button.addEventListener('click', () => {
        iframe.style.display = 'none';
        spinner.style.display = 'block'; // Tampilkan spinner

        // Ganti URL di sini dengan URL yang ingin dimuat
        iframe.src = '<?php echo url('
        lembaga / detailLembagaSarpras ') ?>';

        iframe.onload = function () {
            iframe.style.display = 'block';
            spinner.style.display = 'none'; // Sembunyikan spinner setelah iframe selesai memuat
        };
    });
</script>
<?php include viewPath('includes/footer'); ?>