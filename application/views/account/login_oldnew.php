<!DOCTYPE html>


<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $assets ?>" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>MKDC | Login</title>

    
    <meta name="description" content="Miftahul Khoer Data Center" />
    <!-- Canonical SEO -->
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $assets ?>img/mkdc_mini.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">
    
    <!--Local font <link href="<?php echo $assets ?>css/Font.css" rel="stylesheet"> -->

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo $assets ?>css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo $assets ?>vendor/libs/typeahead-js/typeahead.css" /> 
    <!-- Vendor -->
<link rel="stylesheet" href="<?php echo $assets ?>vendor/libs/%40form-validation/umd/styles/index.min.css" />

    <!-- Page CSS -->
    <!-- Page -->
<link rel="stylesheet" href="<?php echo $assets ?>vendor/css/pages/page-auth.css">

    <!-- Helpers -->
    <script src="<?php echo $assets ?>vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="<?php echo $assets ?>vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo $assets ?>js/config.js"></script>
    
</head>

<body>

  
  <!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DDHKGP" height="0" width="0" style="display: none; visibility: hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  
  <!-- Content -->

<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center">
      <div class="flex-row text-center mx-auto">
        <img src="<?php echo $assets ?>img/pages/login-light.png" alt="Auth Cover Bg color" width="520" class="img-fluid authentication-cover-img" data-app-light-img="pages/login-light.png" data-app-dark-img="pages/login-dark.html">
        <div class="mx-auto">
          <h3>Miftahul Khoer Data Center</h3>
          <p>
            Ekosistem data dalam satu gengaman.
          </p>
        </div>
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand mb-4">
          <a href="index-2.html" class="app-brand -link gap-2 mb-2">
            <span class="app-brand-logo demo">
              <img src="<?php echo $assets ?>img/mkdc_mini.png" style="width:25px !important">
            </span>
            <span class="app-brand-text demo h3 mb-0 fw-bold">MKDC</span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-2">Selamat Datang! 👋</h4>
        <p class="mb-4">Silahkan masuk dengan menggunakan akun anda.</p>

        <?php if(isset($message)): ?>
          <div class="alert alert-solid-<?php echo $message_type ?>">
            <p style="margin: bottom 0px !important;"><?php echo $message ?></p>
          </div>
        <?php endif; ?>

        <?php if(!empty($this->session->flashdata('message'))): ?>
          <div class="alert alert-solid-<?php echo $this->session->flashdata('message_type'); ?>">
            <p  style="margin: bottom 0px !important;"><?php echo $this->session->flashdata('message') ?></p>
          </div>
        <?php endif; ?>


        <form id="formAuthentication" class="mb-3" action="<?php echo url('/login/check') ?>" method="POST" autocomplete="off">
          <div class="mb-3">
            <label for="email" class="form-label">Username</label>
            <input type="text" class="form-control" id="email" name="username" placeholder="Masukan username anda" autofocus>
          </div>
          <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
              <a href="auth-forgot-password-cover.html">
                <small>Lupa Password?</small>
              </a>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember-me">
              <label class="form-check-label" for="remember-me">
                Ingat Saya
              </label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100">
            Masuk
          </button>
        </form>

        <!-- <p class="text-center">
          <span>New on our platform?</span>
          <a href="auth-register-cover.html">
            <span>Create an account</span>
          </a>
        </p> -->

        <div class="divider my-4">
          <div class="divider-text">or</div>
        </div>

        <div class="d-flex justify-content-center">
          <!-- <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
            <i class="tf-icons bx bxl-facebook"></i>
          </a> -->

          <button type="button" class="btn rounded-pill btn-google-plus">
                <i class="tf-icons bx bxl-google me-1"></i>Akun Belajar.id
              </button>
          
          </a>
        </div>
      </div>
    </div>
    <!-- /Login -->
  </div>
</div>

<!-- / Content -->

  
  

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  
  <script src="<?php echo $assets ?>vendor/libs/jquery/jquery.js"></script>
  <script src="<?php echo $assets ?>vendor/libs/popper/popper.js"></script>
  <script src="<?php echo $assets ?>vendor/js/bootstrap.js"></script>
  <script src="<?php echo $assets ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="<?php echo $assets ?>vendor/libs/hammer/hammer.js"></script>
  <script src="<?php echo $assets ?>vendor/libs/i18n/i18n.js"></script>
  <script src="<?php echo $assets ?>vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="<?php echo $assets ?>vendor/js/menu.js"></script>
  
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="<?php echo $assets ?>vendor/libs/%40form-validation/umd/bundle/popular.min.js"></script>
<script src="<?php echo $assets ?>vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="<?php echo $assets ?>vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js"></script>

  <!-- Main JS -->
  <script src="<?php echo $assets ?>js/main.js"></script>
  

  <!-- Page JS -->
  <script src="<?php echo $assets ?>js/pages-auth.js"></script>
  
</body>


</html>

<!-- beautify ignore:end -->

