<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->
<head>
    <meta charset="UTF-8">
    <title>OSAN - SOLUCIONES INFORMATICAS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link rel="stylesheet" type="text/css" href="/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/line-awesome/css/line-awesome_5.min.css">
    <!--<link rel="stylesheet" type="text/css" href="assets/fonts/open-sans/styles.css">-->
    <link rel="stylesheet" type="text/css" href="/assets/fonts/montserrat/styles.css">
    <link rel="stylesheet" type="text/css" href="/libs/tether/css/tether.min.css">
    <link rel="stylesheet" type="text/css" href="/libs/jscrollpane/jquery.jscrollpane.css">
    <link rel="stylesheet" type="text/css" href="/libs/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/common.min.css">
    <link rel="stylesheet" href="/css/print.min.css">
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME STYLES -->

    <!--<link rel="stylesheet" type="text/css" href="/assets/styles/themes/sidebar-black.css">-->
    <!-- END THEME STYLES -->
    <link rel="stylesheet" type="text/css" href="/assets/fonts/kosmo/styles.css">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/weather/css/weather-icons.min.css">
    <link rel="stylesheet" type="text/css" href="/libs/c3js/c3.min.css">
    <link rel="stylesheet" type="text/css" href="libs/noty/noty.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/widgets/payment.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/widgets/panels.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/dashboard/tabbed-sidebar.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/apps/crm/contact.min.css">
    </head>
    <body class="ks-navbar-fixed ks-sidebar-default ks-sidebar-position-fixed ks-page-header-fixed ks-theme-primary ks-page-loading"> <!-- remove ks-page-header-fixed to unfix header -->
      <div id="app">
      <cssconfig></cssconfig>
      <!-- BEGIN HEADER -->
      <nav-menu></nav-menu>
      <!-- END HEADER -->
      <div class="ks-page-container ks-dashboard-tabbed-sidebar-fixed-tabs">
        <menu-point-of-sale></menu-point-of-sale>
        <app></app>
        <vue-progress-bar></vue-progress-bar>
      </div>
      </div>
      <!-- BEGIN PAGE LEVEL PLUGINS -->
      <script src="{{ asset('js/app.js') }}"></script>
      <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/print.min.js') }}"></script>
      <script src="{{ asset('libs/responsejs/response.min.js') }}"></script>
      <script src="{{ asset('libs/loading-overlay/loadingoverlay.min.js') }}"></script>
      <script src="{{ asset('libs/jscrollpane/jquery.jscrollpane.min.js') }}"></script>
      <script src="{{ asset('libs/jscrollpane/jquery.mousewheel.js') }}"></script>
      <script src="{{ asset('libs/flexibility/flexibility.js') }}"></script>
      <script src="{{ asset('libs/noty/noty.min.js') }}"></script>
      <script src="{{ asset('libs/velocity/velocity.min.js') }}"></script>
      <script type="application/javascript">

      function keyadd(key, largue = 1) {
        if(key == -2){
        var texto = document.getElementById('newPrice');
        document.getElementById('newPrice').value = texto.value.slice(0, -1);
        }
        else if(key == -1){
          document.getElementById('newPrice').value = '';
        }
        else {
          for (var i = 0; i < largue; i++) {
            document.getElementById('newPrice').value += key;
          }
        }
        document.getElementById('newPrice').focus();
      }
      /*(function ($) {
          $(document).ready(function () {
              setTimeout(function () {
                  new Noty({
                      text: '<strong>Bienvenido a OSAN-POS</strong>! <br> la tienda online mas personalizada del mercado.',
                      type   : 'information',
                      theme  : 'mint',
                      layout : 'topRight',
                      timeout: 3000
                  }).show();
              }, 1500);
          });
      })(jQuery);*/
      </script>
      <script src="{{ asset('assets/scripts/common.min.js') }}"></script>
      <div class="ks-mobile-overlay"></div>
      </body>
      </html>
