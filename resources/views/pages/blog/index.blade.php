<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Blog | On-Cue Logistics </title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicons/favicon-16x16.png" />
    <meta name="description" content="On-Cue Logistics, Automated Aso-Ebi Deliveries For Your Events" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,200;0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,100;1,9..40,200;1,9..40,300;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900;1,9..40,1000&amp;display=swap"
        rel="stylesheet">



    <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/vendors/animate/animate.min.css" />
    <link rel="stylesheet" href="../assets/vendors/animate/custom-animate.css" />
    <link rel="stylesheet" href="../assets/vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="../assets/vendors/jarallax/jarallax.css" />
    <link rel="stylesheet" href="../assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="../assets/vendors/odometer/odometer.min.css" />
    <link rel="stylesheet" href="../assets/vendors/swiper/swiper.min.css" />
    <link rel="stylesheet" href="../assets/vendors/flowtrack-icons/style.css">
    <link rel="stylesheet" href="../assets/vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet" href="../assets/vendors/owl-carousel/owl.theme.default.min.css" />
    <link rel="stylesheet" href="../assets/vendors/bootstrap-select/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="../assets/vendors/nice-select/nice-select.css" />
    <link rel="stylesheet" href="../assets/vendors/jquery-ui/jquery-ui.css" />

    <!-- template styles -->
    <link rel="stylesheet" href="../assets/css/flowtrack.css" />
    <link rel="stylesheet" href="../assets/css/flowtrack-responsive.css" />
</head>

<body class="custom-cursor">

    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>


    <div class="chat-icon"><button type="button" class="chat-toggler"><i class="fa fa-comment"></i></button></div>
    <!--Chat Popup-->
    <div id="chat-popup" class="chat-popup">
        <div class="popup-inner">
            <div class="close-chat"><i class="fa fa-times"></i></div>
            <div class="chat-form">
                <p>Please fill out the form below and we will get back to you as soon as possible.</p>
                <form action="https://html.scriptfusions.com/flowtrack/main-html/assets/inc/sendemail.php"
                    method="POST" class="contact-form-validated">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Text"></textarea>
                    </div>
                    <div class="form-group message-btn">
                        <button type="submit" class="thm-btn">Submit +</button>
                    </div>
                    <div class="result"></div>
                </form>
            </div>
        </div>
    </div>


    <div class="page-wrapper">
        @include('partials.header')

        <div class="stricky-header stricked-menu main-menu main-menu-two">
            <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
        </div><!-- /.stricky-header -->

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg"
                style="background-image: url(../assets/images/backgrounds/page-header-bg.jpg);">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>Blog</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/">Home</a></li>
                            <li><span>-</span></li>
                            <li>Blog</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Blog One Start -->
        <section class="blog-one">
            <div class="container">
                <div class="row">
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="../assets/images/news/blog-1-3.jpg" alt="">
                                </div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a
                                        href="blog/the-benefits-of-an-automated-payment-system-in-event-management/">The
                                        Benefits of an Automated Payment System in Event Management</a></h3>
                                <!--<div class="blog-one__meta-and-arrow">
                                    <ul class="blog-one__meta list-unstyled">
                                        <li>
                                            <a href="the-benefits-of-an-automated-payment-system-in-event-management/"><span class="fas fa-user"></span>admin</a>
                                        </li>
                                    </ul>
                                    <div class="blog-one__arrow">
                                        <a href="the-benefits-of-an-automated-payment-system-in-event-management/"><span class="icon-right-arrow"></span></a>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="../assets/images/news/blog-1-2.jpg" alt="">
                                </div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a
                                        href="blog/redefining-event-management-through-customization/">Redefining Event
                                        Management Through Customization</a></h3>
                                <!--<div class="blog-one__meta-and-arrow">
                                    <ul class="blog-one__meta list-unstyled">
                                        <li>
                                            <a href="the-rise-of-customization-in-event-management/"><span class="fas fa-user"></span>admin</a>
                                        </li>
                                    </ul>
                                    <div class="blog-one__arrow">
                                        <a href="the-rise-of-customization-in-event-management/"><span class="icon-right-arrow"></span></a>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->
                    <!--Blog One Single Start-->
                    <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInRight" data-wow-delay="300ms">
                        <div class="blog-one__single">
                            <div class="blog-one__img-box">
                                <div class="blog-one__img">
                                    <img src="../assets/images/news/blog-1-1.jpg" alt="">
                                </div>
                            </div>
                            <div class="blog-one__content">
                                <h3 class="blog-one__title"><a
                                        href="blog/why-event-logistics-is-the-unsung-hero-of-every-successful-celebration/">Why
                                        Event Logistics is the Unsung Hero of Every Successful Celebration</a></h3>
                                <!--<div class="blog-one__meta-and-arrow">
                                    <ul class="blog-one__meta list-unstyled">
                                        <li>
                                            <a href="why-event-logistics-is-the-unsung-hero-of-every-successful-celebration/"><span class="fas fa-user"></span>admin</a>
                                        </li>
                                    </ul>
                                    <div class="blog-one__arrow">
                                        <a href="why-event-logistics-is-the-unsung-hero-of-every-successful-celebration/"><span class="icon-right-arrow"></span></a>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                    <!--Blog One Single End-->

                </div>
            </div>
        </section>
        <!--Blog One End -->



        <!--Site Footer Start-->
        @include('partials.footer')
        <!--Site Footer End-->


    </div><!-- /.page-wrapper -->


    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div>
        <!-- /.mobile-nav__overlay -->
        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>

            <div class="logo-box">
                <a href="/" aria-label="logo image"><img src="../assets/images/resources/logo.png"
                        width="150" alt="" /></a>
            </div>
            <!-- /.logo-box -->
            <div class="mobile-nav__container"></div>
            <!-- /.mobile-nav__container -->

            <ul class="mobile-nav__contact list-unstyled">
                <li>
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:info@oncuelogistics.com">info@oncuelogistics.com</a>
                </li>
                <li>
                    <i class="fa fa-phone-alt"></i>
                    <a href="tel:+2347089091600">+234 708 909 1600</a>
                </li>
            </ul><!-- /.mobile-nav__contact -->
            <div class="mobile-nav__top">
                <div class="mobile-nav__social">
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-facebook-square"></a>
                    <a href="#" class="fab fa-pinterest-p"></a>
                    <a href="#" class="fab fa-instagram"></a>
                </div><!-- /.mobile-nav__social -->
            </div><!-- /.mobile-nav__top -->



        </div>
        <!-- /.mobile-nav__content -->
    </div>
    <!-- /.mobile-nav__wrapper -->

    <div class="search-popup">
        <div class="search-popup__overlay search-toggler"></div>
        <!-- /.search-popup__overlay -->
        <div class="search-popup__content">
            <form action="#">
                <label for="search" class="sr-only">search here</label><!-- /.sr-only -->
                <input type="text" id="search" placeholder="Search Here..." />
                <button type="submit" aria-label="search submit" class="thm-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <!-- /.search-popup__content -->
    </div>
    <!-- /.search-popup -->

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fas fa-arrow-up"></i></a>


    <script src="../assets/vendors/jquery/jquery-latest.js"></script>
    <script src="../assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendors/jarallax/jarallax.min.js"></script>
    <script src="../assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
    <script src="../assets/vendors/jquery-appear/jquery.appear.min.js"></script>
    <script src="../assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
    <script src="../assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="../assets/vendors/jquery-validate/jquery.validate.min.js"></script>
    <script src="../assets/vendors/odometer/odometer.min.js"></script>
    <script src="../assets/vendors/swiper/swiper.min.js"></script>
    <script src="../assets/vendors/wnumb/wNumb.min.js"></script>
    <script src="../assets/vendors/wow/wow.js"></script>
    <script src="../assets/vendors/isotope/isotope.js"></script>
    <script src="../assets/vendors/owl-carousel/owl.carousel.min.js"></script>
    <script src="../assets/vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="../assets/vendors/jquery-ui/jquery-ui.js"></script>
    <script src="../assets/vendors/jquery.circle-type/jquery.circleType.html"></script>
    <script src="../assets/vendors/jquery.circle-type/jquery.lettering.min.html"></script>
    <script src="../assets/vendors/nice-select/jquery.nice-select.min.js"></script>
    <script src="../assets/vendors/sidebar-content/jquery-sidebar-content.js"></script>


    <script src="../assets/vendors/gsap/gsap.js"></script>
    <script src="../assets/vendors/gsap/ScrollTrigger.js"></script>
    <script src="../assets/vendors/gsap/SplitText.js"></script>
    <script src="../assets/vendors/scroll/lenis.min.js"></script>



    <!-- template js -->
    <script src="../assets/js/flowtrack.js"></script>
</body>

</html>
