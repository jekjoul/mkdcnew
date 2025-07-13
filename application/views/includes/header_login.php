<?php

defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!DOCTYPE html>

<html lang="<?php echo getUserlang() ?>">

<head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title><?php echo $page->title ?> | <?php echo $app->site_title ?> </title>

  <!-- Tell the browser to be responsive to screen width -->

  <meta name="viewport" content="width=device-width, initial-scale=1">



  <meta name="csrf_token_name" content="<?php echo $this->security->get_csrf_token_name(); ?>" />

  <meta name="csrf_token_hash" content="<?php echo $this->security->get_csrf_hash(); ?>" />



  <!-- Font Awesome -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/fontawesome-free/css/all.min.css">

  <!-- Ionicons -->

  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

  <!-- Tempusdominus Bbootstrap 4 -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

  <!-- SweetAlert2 -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

  <!-- Toastr -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/toastr/toastr.min.css">

  <!-- iCheck -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">

  <!-- JQVMap -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/jqvmap/jqvmap.min.css">

  <!-- overlayScrollbars -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

  <!-- Daterange picker -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/daterangepicker/daterangepicker.css">

  <!-- summernote -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/summernote/summernote-bs4.css">

  

  <!-- Select2 -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/select2/css/select2.min.css" />



  <!-- DataTables -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">



  <!-- iCheck for checkboxes and radio inputs -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">



  <!-- Theme style -->

  <link rel="stylesheet" href="<?php echo $url->assets ?>css/adminlte.min.css">



  



  <link rel="stylesheet" href="<?php echo $url->assets ?>css/app.css">
  



  <!-- Google Font: Source Sans Pro -->

  <!-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> -->

  
  

<!-- jQuery -->

<script src="<?php echo $url->assets ?>plugins/jquery/jquery.min.js"></script>

<!-- jQuery UI 1.11.4 -->

<script src="<?php echo $url->assets ?>plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<script>

  $.widget.bridge('uibutton', $.ui.button)

</script>

<!-- Bootstrap 4 -->

<script src="<?php echo $url->assets ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<style type="text/css">
  body .body{
     background: rgb(176,255,243) !important;
    background: linear-gradient(90deg, rgba(176,255,243,1) 0%, rgba(230,255,252,1) 100%) !important; 
  }
</style>

<style type="text/css">
  .btn-primary {
    color: #fff;
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    box-shadow: none;
  }

  .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
    color: #fff !important;
    background-color: #17a2b8;
  }

   .nav-treeview > .nav-item > .nav-link.active {
    background-color: #99cdd9 !important;
  }

  .nav-pills .nav-link:not(.active):hover {
    color: #17a2b8;
  }

  #page-loader {
    position: absolute;
    height: 100%;
    left: 0;
    right: 0;
    z-index: 10000;
    display: none;
    text-align: center;
    justify-content: center;
    width: 100%;
    padding-top: 25px;
    background-color: rgba(255, 255, 255, 0.9); 

  }
</style>
</head>

<div id="page-loader">
  <h3 style="padding-top:200px;">Memuat Data...</h3>
  <img width="100" src="<?php echo $url->assets ?>loading.gif" alt="loader">
  <p><small>...Proses pengambilan data bisa membutuhkan beberapa waktu tergantung banyaknya data yang akan diproses.</small></p>
</div>

<body class="login hold-transition sidebar-mini <?php echo isset($page->body_classes) ? $page->body_classes : 'layout-fixed' ?>">

<div class="wrapper">



  <!-- Navbar -->

  <nav class="main-header navbar navbar-expand navbar-info navbar-dark">



  <!-- Left navbar links -->

  <ul class="navbar-nav">

      <li class="nav-item">

        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>

      </li>

      <li class="nav-item d-none d-sm-inline-block">

        <a href="<?php echo url('/logout') ?>" class="nav-link">Logout</a>

      </li>

    </ul>





    <!-- Right navbar links -->

    <ul class="navbar-nav ml-auto">

      <!-- Notifications Dropdown Menu -->

      

      <!-- Messages Dropdown Menu -->

      <li class="nav-item dropdown">

        <a class="nav-link" data-toggle="dropdown" href="#">

          <i class="far fa-comments"></i>

          <span class="badge badge-danger navbar-badge">3</span>

        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <a href="#" class="dropdown-item">

            <!-- Message Start -->

            <div class="media">

              <img src="<?php echo $url->assets ?>img/user1-128x128.jpg" alt="<?php echo lang('user_image') ?>" class="img-size-50 mr-3 img-circle">

              <div class="media-body">

                <h3 class="dropdown-item-title">

                  <?php echo lang('sample_brad') ?>

                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>

                </h3>

                <p class="text-sm"><?php echo lang('sample_call') ?></p>

                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo lang('sample_4h_ago') ?></p>

              </div>

            </div>

            <!-- Message End -->

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">

            <!-- Message Start -->

            <div class="media">

              <img src="<?php echo $url->assets ?>img/user8-128x128.jpg" alt="<?php echo lang('user_image') ?>" class="img-size-50 img-circle mr-3">

              <div class="media-body">

                <h3 class="dropdown-item-title">

                <?php echo lang('sample_john') ?>

                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>

                </h3>

                <p class="text-sm"><?php echo lang('sample_message_bro') ?></p>

                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo lang('sample_4h_ago') ?></p>

              </div>

            </div>

            <!-- Message End -->

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">

            <!-- Message Start -->

            <div class="media">

              <img src="<?php echo $url->assets ?>img/user3-128x128.jpg" alt="<?php echo lang('user_image') ?>" class="img-size-50 img-circle mr-3">

              <div class="media-body">

                <h3 class="dropdown-item-title">

                <?php echo lang('sample_nora') ?>

                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>

                </h3>

                <p class="text-sm"><?php echo lang('sample_subject') ?></p>

                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo lang('sample_4h_ago') ?></p>

              </div>

            </div>

            <!-- Message End -->

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item dropdown-footer"><?php echo lang('see_all_messages') ?></a>

        </div>

      </li>

      <!-- Notifications Dropdown Menu -->

      <li class="nav-item dropdown">

        <a class="nav-link" data-toggle="dropdown" href="#">

          <i class="far fa-bell"></i>

          <span class="badge badge-warning navbar-badge">15</span>

        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <span class="dropdown-item dropdown-header">15 <?php echo lang('notifications') ?></span>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">

            <i class="fas fa-envelope mr-2"></i> 4 new messages

            <span class="float-right text-muted text-sm">3 mins</span>

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">

            <i class="fas fa-users mr-2"></i> 8 friend requests

            <span class="float-right text-muted text-sm">12 hours</span>

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">

            <i class="fas fa-file mr-2"></i> 3 new reports

            <span class="float-right text-muted text-sm">2 days</span>

          </a>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item dropdown-footer"><?php echo lang('see_all_notifications') ?></a>

        </div>

      </li>

    
      <!-- User Account: style can be found in dropdown.less -->

      <li class="nav-item dropdown user-menu">

        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">

          <img src="<?php echo userProfile(logged('id')) ?>" class="user-image img-circle elevation-2" alt="User Image">

          <span class="d-none d-md-inline"><?php echo logged('name') ?></span>

        </a>

        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <!-- User image -->

          <li class="user-header bg-primary">

            <img src="<?php echo userProfile(logged('id')) ?>" class="img-circle elevation-2" alt="User Image">



            <p>

              <?php echo logged('name') ?>

              <small>Member since Nov. 2012</small>

            </p>

          </li>

          

          <!-- Menu Footer-->

          <li class="user-footer">

            <a href="<?php echo url('profile') ?>" class="btn btn-default btn-flat"><?php echo lang('profile') ?></a>

            <a href="<?php echo url('/logout') ?>" class="btn btn-default btn-flat float-right"><?php echo lang('signout') ?></a>

          </li>

        </ul>

      </li>

    </ul>

  </nav>

  <!-- /.navbar -->



  <!-- Main Sidebar Container -->

  <aside class="main-sidebar sidebar-light-info elevation-4">

    <!-- Brand Logo -->

    <a href="<?php echo url('/') ?>" class="brand-link navbar-info">

      <img src="<?php echo $url->assets ?>/img/mkdc.png" alt="AdminLTE Logo" class="brand-image "

           style="opacity: .8">

      <span class="brand-text font-weight-dark" style="color:white;"><?php echo setting('company_name') ?></span>

    </a>



    <!-- Sidebar -->

    <div class="sidebar">

      



      <!-- Sidebar Menu -->

      <nav class="mt-2">

      <?php include 'nav.php' ?>

        

      </nav>

      <!-- /.sidebar-menu -->

    </div>

    <!-- /.sidebar -->

  </aside>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">



  <?php include 'notifications.php'; ?>

