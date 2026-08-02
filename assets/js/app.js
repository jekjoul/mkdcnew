(function ($) {
  'use strict';

  // sidebar submenu collapsible js
  $(".sidebar-menu .dropdown").on("click", function(){
    var item = $(this);
    item.siblings(".dropdown").children(".sidebar-submenu").slideUp();

    item.siblings(".dropdown").removeClass("dropdown-open");

    item.siblings(".dropdown").removeClass("open");

    item.children(".sidebar-submenu").slideToggle();

    item.toggleClass("dropdown-open");
  });

  $(".sidebar-toggle").on("click", function(){
    $(this).toggleClass("active");
    $(".sidebar").toggleClass("active");
    $(".dashboard-main").toggleClass("active");
  });

  $(".sidebar-mobile-toggle").on("click", function(){
    $(".sidebar").addClass("sidebar-open");
    $("body").addClass("overlay-active");
  });

  $(".sidebar-close-btn").on("click", function(){
    $(".sidebar").removeClass("sidebar-open");
    $("body").removeClass("overlay-active");
  });

  //to keep the current page active
  $(function () {
    let currentUrl = window.location.href.split('?')[0].replace(/\/$/, "");
    let bestMatch = null;
    let maxMatchLen = -1;

    let originUrl = window.location.origin;
    // Normalize root path URL (like http://localhost/mkdc_new_draft)
    let pathSegments = window.location.pathname.split('/').filter(Boolean);
    let isRootPath = (pathSegments.length === 0 || (pathSegments.length === 1 && pathSegments[0] === 'mkdc_new_draft'));

    $("ul#sidebar-menu a").each(function () {
      let menuUrl = this.href.split('?')[0].replace(/\/$/, "");
      
      // If it is the dashboard/root link
      let menuPathSegments = this.pathname.split('/').filter(Boolean);
      let isMenuRoot = (menuPathSegments.length === 0 || (menuPathSegments.length === 1 && menuPathSegments[0] === 'mkdc_new_draft'));

      if (isMenuRoot) {
        // Dashboard only matches exactly
        if (currentUrl === menuUrl) {
          bestMatch = $(this);
          maxMatchLen = menuUrl.length;
        }
      } else {
        if (currentUrl === menuUrl) {
          bestMatch = $(this);
          maxMatchLen = menuUrl.length;
        } else if (currentUrl.startsWith(menuUrl + "/") && menuUrl.length > maxMatchLen) {
          bestMatch = $(this);
          maxMatchLen = menuUrl.length;
        } else if (menuUrl.endsWith("/surat/buat")) {
          // Khusus alur Buat Surat (surat/buat_otomatis, surat/keterangan_siswa_aktif, surat/keluar_tambah_*)
          if (
            currentUrl.indexOf("/surat/buat_otomatis") !== -1 ||
            currentUrl.indexOf("/surat/keterangan_siswa_aktif") !== -1 ||
            currentUrl.indexOf("/surat/keluar_tambah") !== -1
          ) {
            bestMatch = $(this);
            maxMatchLen = 9999;
          }
        } else if (menuUrl.endsWith("/surat/template")) {
          // Khusus kelola template (surat/template_tambah, surat/template_edit)
          if (
            currentUrl.indexOf("/surat/template_tambah") !== -1 ||
            currentUrl.indexOf("/surat/template_edit") !== -1
          ) {
            bestMatch = $(this);
            maxMatchLen = 9999;
          }
        } else if (menuUrl.endsWith("/surat/kop")) {
          // Khusus kelola kop (surat/kop_tambah, surat/kop_edit)
          if (
            currentUrl.indexOf("/surat/kop_tambah") !== -1 ||
            currentUrl.indexOf("/surat/kop_edit") !== -1
          ) {
            bestMatch = $(this);
            maxMatchLen = 9999;
          }
        } else if (menuUrl.endsWith("/surat/kode")) {
          // Khusus kelola kode (surat/kode_tambah, surat/kode_edit)
          if (
            currentUrl.indexOf("/surat/kode_tambah") !== -1 ||
            currentUrl.indexOf("/surat/kode_edit") !== -1
          ) {
            bestMatch = $(this);
            maxMatchLen = 9999;
          }
        }
      }
    });

    if (bestMatch) {
      bestMatch.addClass("active-page");
      let parentLi = bestMatch.parent().addClass("active-page");
      
      // Traverse up to open parent dropdowns
      let curr = parentLi;
      while (curr.length > 0 && !curr.is("#sidebar-menu")) {
        if (curr.is("li")) {
          curr.addClass("open");
        }
        if (curr.is("ul") && curr.hasClass("sidebar-submenu")) {
          curr.addClass("show").slideDown(0);
          curr.parent().addClass("open dropdown-open");
        }
        curr = curr.parent();
      }
    }
  });

/**
* Utility function to calculate the current theme setting based on localStorage.
*/
function calculateSettingAsThemeString({ localStorageTheme }) {
  if (localStorageTheme !== null) {
    return localStorageTheme;
  }
  const htmlTheme = document.querySelector("html").getAttribute("data-theme");
  if (htmlTheme) {
    return htmlTheme;
  }
  return "light";
}

/**
* Utility function to update the button text and aria-label.
*/
function updateButton({ buttonEl, isDark }) {
  const newCta = isDark ? "dark" : "light";
  buttonEl.setAttribute("aria-label", newCta);
  buttonEl.innerText = newCta;
}

/**
* Utility function to update the theme setting on the html tag.
*/
function updateThemeOnHtmlEl({ theme }) {
  document.querySelector("html").setAttribute("data-theme", theme);
}

/**
* 1. Grab what we need from the DOM and system settings on page load.
*/
const button = document.querySelector("[data-theme-toggle]");
const localStorageTheme = localStorage.getItem("theme");

/**
* 2. Work out the current site settings.
*/
let currentThemeSetting = calculateSettingAsThemeString({ localStorageTheme });

/**
* 3. If the button exists, update the theme setting and button text according to current settings.
*/
if (button) {
  updateButton({ buttonEl: button, isDark: currentThemeSetting === "dark" });
  updateThemeOnHtmlEl({ theme: currentThemeSetting });

  /**
  * 4. Add an event listener to toggle the theme.
  */
  button.addEventListener("click", (event) => {
    const newTheme = currentThemeSetting === "dark" ? "light" : "dark";

    localStorage.setItem("theme", newTheme);
    updateButton({ buttonEl: button, isDark: newTheme === "dark" });
    updateThemeOnHtmlEl({ theme: newTheme });

    currentThemeSetting = newTheme;
  });
} else {
  // If no button is found, just apply the current theme to the page
  updateThemeOnHtmlEl({ theme: currentThemeSetting });
}


// =========================== Table Header Checkbox checked all js Start ================================
$('#selectAll').on('change', function () {
  $('.form-check .form-check-input').prop('checked', $(this).prop('checked')); 
}); 

  // Remove Table Tr when click on remove btn start
  $('.remove-btn').on('click', function () {
    $(this).closest('tr').remove(); 

    // Check if the table has no rows left
    if ($('.table tbody tr').length === 0) {
      $('.table').addClass('bg-danger');

      // Show notification
      $('.no-items-found').show();
    }
  });
  // Remove Table Tr when click on remove btn end

  // =========================== Dedicated Mobile Loader Handler ================================
  function hideMobileLoader() {
    var loader = $('#mobile-page-loader');
    if (loader.length) {
      loader.addClass('loaded');
    }
  }

  function showMobileLoader() {
    if (window.innerWidth <= 768) {
      var loader = $('#mobile-page-loader');
      if (loader.length) {
        loader.removeClass('loaded');
      }
    }
  }

  // Hide loader when document ready & window loaded
  hideMobileLoader();
  $(window).on('load', function() {
    hideMobileLoader();
  });

  // Safety fallback: ensure loader is hidden after max 1.5 seconds
  setTimeout(hideMobileLoader, 1500);

  // Show loader when navigating via links on mobile
  $(document).on('click', 'a', function(e) {
    if (window.innerWidth > 768) return;

    var href = $(this).attr('href');
    var target = $(this).attr('target');
    var isToggle = $(this).attr('data-bs-toggle') || $(this).attr('data-toggle');

    if (href && href !== '#' && href !== 'javascript:void(0)' && !href.startsWith('#') && !href.startsWith('javascript:') && target !== '_blank' && !isToggle) {
      showMobileLoader();
    }
  });

  // Show loader when submitting forms on mobile
  $(document).on('submit', 'form', function() {
    if (window.innerWidth <= 768) {
      showMobileLoader();
    }
  });
})(jQuery);