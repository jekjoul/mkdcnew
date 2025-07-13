<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $url->assets ?>" data-template="vertical-menu-template">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        
        <meta name="csrf_token_name" content="<?php echo $this->security->get_csrf_token_name(); ?>" />

        <meta name="csrf_token_hash" content="<?php echo $this->security->get_csrf_hash(); ?>" />
        <title>MKDC | <?php echo $page->title ?></title>


        <meta name="description" content="Miftahul Khoer Data Center" />
        <!-- Canonical SEO -->
    

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?php echo $url->assets ?>img/mkdc_mini.png" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap"
            rel="stylesheet">

        <!-- Icons -->
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/fonts/boxicons.css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/fonts/fontawesome.css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/fonts/flag-icons.css" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/css/rtl/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/css/rtl/theme-default.css"
            class="template-customizer-theme-css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>css/demo.css" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/libs/typeahead-js/typeahead.css" />
        

        <!-- Page CSS -->
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/libs/apex-charts/apex-charts.css" />
        <link rel="stylesheet" href="<?php echo $url->assets ?>vendor/css/pages/page-profile.css" />

        <!-- Helpers -->
        <script src="<?php echo $url->assets ?>vendor/js/helpers.js"></script>
        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
        <!-- <script src="<?php echo $url->assets ?>vendor/js/template-customizer.js"></script> -->
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="<?php echo $url->assets ?>js/config.js"></script>

    </head>

    <body>

        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar  ">
            <div class="layout-container">

                <!-- Menu -->
                    <?php include 'aside.php'; ?>
                <!-- / Menu -->



                <!-- Layout container -->
                <div class="layout-page">





                    <!-- Navbar -->
                        <?php include 'nav.php'; ?>
                    <!-- / Navbar -->



                    <!-- Content wrapper -->
                    <div class="content-wrapper">

                        <!-- Content -->

                        
                        <!-- / Content -->


