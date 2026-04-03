<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->

<head>
    <meta charset="UTF-8">
    <title>OSAN - SOLUCIONES INFORMATICAS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="/shop_assets/css/bootstrap.min.css" />

    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="/shop_assets/css/slick.css" />
    <link type="text/css" rel="stylesheet" href="/shop_assets/css/slick-theme.css" />

    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="/shop_assets/css/nouislider.min.css" />

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="/shop_assets/css/font-awesome.min.css">

    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="/shop_assets/css/style_osan.css" />
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

<body class="ks-navbar-fixed ks-sidebar-default ks-sidebar-position-fixed ks-page-header-fixed ks-theme-primary ks-page-loading">
    <!-- remove ks-page-header-fixed to unfix header -->
    <div id="app">
        <!-- BEGIN HEADER -->
        <!-- END HEADER -->
        <div class="ks-page-container ks-dashboard-tabbed-sidebar-fixed-tabs">
            <header>
                <mall-header :user_data="{{ json_encode($user_data) }}"></mall-header>
                <!-- MAIN HEADER -->
                <div id="header">
                    <!-- container -->
                    <div class="container">
                        <!-- row -->
                        <div class="row">
                            <!-- LOGO -->
                            <div class="col-md-4">
                                <div class="header-logo">
                                    <!-- <img width="30%" style="float:left; margin-right:20px; margin-top:15px; border-radius: 50%!important;" src="/{{ @$user_data->photo }}" alt=""> -->
                                    <a href="#" class="logo">                                       
                                        <h4 style="color: white; font-size:26px; margin-top:6px;">
                                        {{ @$user_config_page->name_ecommerce }}</h4>
                                        <div style="color: white; font-size:13px;">
                                            Lo mejor en ventas online...
                                        </div>                                        
                                    </a>
                                </div>                                
                            </div>                            
                            <!-- /LOGO -->

                            <!-- ACCOUNT -->
                            <div class="col-md-3 clearfix">
                                <div class="header-ctn">
                                    <div class="menu-toggle">
                                        <a href="#">
                                            <i class="fa fa-bars"></i>
                                            <span>Menu</span>
                                        </a>
                                    </div>
                                    <!-- /Menu Toogle -->
                                </div>
                            </div>
                            <!-- /ACCOUNT -->                            
                        </div>
                        <!-- row -->
                    </div>
                    <!-- container -->
                </div>
                <!-- /MAIN HEADER -->
            </header>

            <!-- NAVIGATION -->
            <!-- /HEADER -->
            <nav id="navigation">
                <!-- container -->
                <div class="container">
                    <!-- responsive-nav -->
                    <div id="responsive-nav">
                        <!-- NAV -->
                        <mall-categories-responsive :categories="{{ json_encode(@$user_categories) }}" />
                        <!-- /NAV -->
                    </div>
                    <!-- /responsive-nav -->
                </div>
                <!-- /container -->
            </nav>

            <!-- /NAVIGATION -->
            <div class="section">
                <div class="container">
                    <!-- store products -->
                    <div class="row">
                        <mall-content :mall="{{ @$mall_id }}" :categories="{{ json_encode(@$user_categories) }}" :items="{{ @$user_all_items }}"></mall-content>
                    </div>
                    <!-- /store products -->
                </div>
            </div>

            <!-- SECTION -->
            <div class="section">
                <!-- container -->
                <div class="container">
                    <!-- row -->
                    <div class="row">
                        <mall-best-seller :categories="{{ json_encode($best_sellers_categories) }}" :items="{{ json_encode($best_seller_items) }}" />
                    </div>
                    <!-- /row -->
                </div>
                <!-- /container -->
            </div>
            <!-- /SECTION -->

            <!-- NEWSLETTER -->
            <div id="newsletter" class="section">
                <!-- container -->
                <div class="container">
                    <!-- row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="newsletter">
                                <p>Sign Up for the <strong>NEWSLETTER</strong></p>
                                <form>
                                    <input class="input" type="email" placeholder="Enter Your Email">
                                    <button class="newsletter-btn"><i class="fa fa-envelope"></i> Subscribe</button>
                                </form>
                                <ul class="newsletter-follow">
                                    <li>
                                        <a href="#"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fa fa-instagram"></i></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="fa fa-pinterest"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /row -->
                </div>
                <!-- /container -->
            </div>
            <!-- /NEWSLETTER -->

            <!-- FOOTER -->
            <footer id="footer">
                <!-- top footer -->
                <div class="section">
                    <!-- container -->
                    <div class="container">
                        <!-- row -->
                        <div class="row">
                            <div class="col-md-3 col-xs-6">
                                <div class="footer">
                                    <h3 class="footer-title">About Us</h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>
                                    <ul class="footer-links">
                                        <li><a href="#"><i class="fa fa-map-marker"></i>1734 Stonecoal Road</a></li>
                                        <li><a href="#"><i class="fa fa-phone"></i>+021-95-51-84</a></li>
                                        <li><a href="#"><i class="fa fa-envelope-o"></i>email@email.com</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-6">
                                <div class="footer">
                                    <h3 class="footer-title">Categories</h3>
                                    <ul class="footer-links">
                                        <li><a href="#">Hot deals</a></li>
                                        <li><a href="#">Laptops</a></li>
                                        <li><a href="#">Smartphones</a></li>
                                        <li><a href="#">Cameras</a></li>
                                        <li><a href="#">Accessories</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="clearfix visible-xs"></div>

                            <div class="col-md-3 col-xs-6">
                                <div class="footer">
                                    <h3 class="footer-title">Information</h3>
                                    <ul class="footer-links">
                                        <li><a href="#">About Us</a></li>
                                        <li><a href="#">Contact Us</a></li>
                                        <li><a href="#">Privacy Policy</a></li>
                                        <li><a href="#">Orders and Returns</a></li>
                                        <li><a href="#">Terms & Conditions</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-6">
                                <div class="footer">
                                    <h3 class="footer-title">Service</h3>
                                    <ul class="footer-links">
                                        <li><a href="#">My Account</a></li>
                                        <li><a href="#">View Cart</a></li>
                                        <li><a href="#">Wishlist</a></li>
                                        <li><a href="#">Track My Order</a></li>
                                        <li><a href="#">Help</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /row -->
                    </div>
                    <!-- /container -->
                </div>
                <!-- /top footer -->

                <!-- bottom footer -->
                <div id="bottom-footer" class="section">
                    <div class="container">
                        <!-- row -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <ul class="footer-payments">
                                    <li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
                                    <li><a href="#"><i class="fa fa-credit-card"></i></a></li>
                                    <li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
                                    <li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
                                    <li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
                                    <li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
                                </ul>
                                <span class="copyright">
                                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                    Copyright &copy;2021 All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                </span>
                            </div>
                        </div>
                        <!-- /row -->
                    </div>
                    <!-- /container -->
                </div>
                <!-- /bottom footer -->
            </footer>
            <!-- /FOOTER -->
            <vue-progress-bar></vue-progress-bar>
        </div>
    </div>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="/libs/jquery/jquery.min.js"></script>
    <script src="/shop_assets/js/bootstrap.min.js"></script>
    <script src="/shop_assets/js/slick.min.js"></script>
    <script src="/shop_assets/js/nouislider.min.js"></script>
    <script src="/shop_assets/js/jquery.zoom.min.js"></script>
    <script src="/shop_assets/js/main.js"></script>
</body>

</html>