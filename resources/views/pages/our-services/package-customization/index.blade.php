<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Package Customization | Our Services | On-Cue Logistics </title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" />
    <meta name="description" content="On-Cue Logistics, Automated Aso-Ebi Deliveries For Your Events" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,200;0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,100;1,9..40,200;1,9..40,300;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900;1,9..40,1000&amp;display=swap"
        rel="stylesheet">



    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/custom-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jarallax/jarallax.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/odometer/odometer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/swiper/swiper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/flowtrack-icons/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-select/css/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/nice-select/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jquery-ui/jquery-ui.css') }}" />

    <!-- template styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/flowtrack.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/flowtrack-responsive.css') }}" />
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
                style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }})">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>Package Customization</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/">Home</a></li>
                            <li><span>-</span></li>
                            <li><a href="../../our-services/">Our Services</a></li>
                            <li><span>-</span></li>
                            <li>Package Customization</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Services Details Start-->
        <section class="services-details">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="services-details__left">
                            <div class="services-details__catagories-box">
                                <h3 class="services-details__catagories-title">Our Services</h3>
                                <ul class="services-details__catagories-list list-unstyled">
                                    <li>
                                        <a href="{{ url('our-services/social-and-corporate-logistics') }}">Social
                                            &amp; Corporate
                                            Logistics<span class="icon-next"></span></a>
                                    </li>
                                    <li class="active">
                                        <a href="{{ url('our-services/package-customization') }}">Package
                                            Customization<span class="icon-next"></span></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="services-details__contact">
                                <div class="services-details__contact-icon">
                                    <span class="icon-phone-call"></span>
                                </div>
                                <div class="services-details__contact-content">
                                    <span>Call Us </span>
                                    <p><a href="tel:+2347089091600">+234 708 909 1600</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="services-details__right">
                            <h3 class="services-details__title-1">Make Every Package Personal. Memorable. On-Cue.</h3>
                            <p class="services-details__text-1">At On-Cue Logistics, packaging isn’t just a box — it’s
                                a storytelling opportunity. Whether you’re sending Aso-Ebi, invites, souvenirs, or
                                curated event bundles, our custom packaging options are designed to elevate the guest
                                experience from the moment they receive it.</p>

                            <h3 class="services-details__title-1">What You Can Do on Our Platform:</h3>

                            <section class="services-page">
                                <div class="container">
                                    <div class="row">
                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Make every delivery feel like
                                                            a gift.</h3>

                                                        <p class="services-one__text">We provide a full-service event
                                                            packaging experience that allows you or your guests to:</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Browse our catalog of premium packaging designs</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Select materials, colors, fonts, and ribbon types</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Add personal messages (e.g., "Sade Weds Emeka")</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>View a 3D live preview of the custom box</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Place orders individually or in bulk</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Enjoy automatic discounting on large orders</p>
                                                            </li>
                                                        </ul>
                                                        <p class="services-one__text">It’s perfect for weddings,
                                                            birthdays, baby showers, and corporate events seeking a
                                                            unique, branded touch.</p>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->
                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Add A Personal Message</h3>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Include a special note or greeting on every box —
                                                                    from “Titi Weds Dami” to “Thank You for Celebrating
                                                                    With Us.”</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Custom messages are printed on inner lids, labels, or
                                                                    sleeves.</p>
                                                            </li>

                                                        </ul>

                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->

                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Choose Fonts &amp; Styles
                                                        </h3>
                                                        <p class="services-one__text">Personalize your packaging with
                                                            elegant or playful typography.</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Select from curated font styles: calligraphy, serif,
                                                                    minimalist sans-serif, or Afro-modern.</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->

                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Live 3D Packaging Preview
                                                        </h3>
                                                        <p class="services-one__text">Visualize your packaging before
                                                            confirming.</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Rotate, zoom, and view your box from all angles in
                                                                    real time.</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Preview materials, colors, and messaging exactly as
                                                                    your guests will receive them.</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->


                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Packaging Options </h3>
                                                        <p class="services-one__text">Visualize your packaging before
                                                            confirming.</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Box Types: Rigid boxes, magnetic closure,
                                                                    eco-friendly kraft, drawer boxes</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Finishes: Matte, gloss, linen-textured, foil stamping
                                                                </p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Interior Options: Tissue wrap, filler crinkle paper,
                                                                    branded stickers, scent inserts</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->

                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Design Support</h3>
                                                        <p class="services-one__text">Need help with layout or color
                                                            coordination? Our in-house packaging consultants assist
                                                            with:</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Logo placement</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Themed color matching</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Gift ensemble arrangement</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->

                                        <!--services One Single Start-->
                                        <div class="col-xl-12 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                                            <div class="services-one__single">
                                                <div class="services-one__img-box">
                                                    <div class="services-one__icon-box">
                                                        <div class="services-one__icon">
                                                            <span class="icon-payment"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="services-one__content">
                                                    <div class="services-one__title-box">
                                                        <h3 class="services-one__title-x">Bulk Orders, Smart Sorting
                                                        </h3>
                                                        <p class="services-one__text">Our system auto-sorts packages
                                                            based on guest names and delivery preferences. Enjoy:</p>
                                                        <ul class="services-details__points list-unstyled">
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Bulk customisation for 100–1,000+ units</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Tiered discounts</p>
                                                            </li>
                                                            <li>
                                                                <div class="icon">
                                                                    <span class="fas fa-check"></span>
                                                                </div>
                                                                <p>Automated tagging for seamless dispatch</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--<div class="services-one__read-more">
                                    <a href="#">read More<span class="icon-right-arrow"></span></a>
                                </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--services One Single End-->


                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Services Details End-->



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
                <a href="/" aria-label="logo image"><img src="{{ asset('assets/images/resources/logo.png') }}"
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


    <script src="{{ asset('assets/vendors/jquery/jquery-latest.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jarallax/jarallax.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-appear/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/odometer/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/swiper/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/wnumb/wNumb.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/wow/wow.js') }}"></script>
    <script src="{{ asset('assets/vendors/isotope/isotope.js') }}"></script>
    <script src="{{ asset('assets/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.circle-type/jquery.circleType.html') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.circle-type/jquery.lettering.min.html') }}"></script>
    <script src="{{ asset('assets/vendors/nice-select/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sidebar-content/jquery-sidebar-content.js') }}"></script>


    <script src="{{ asset('assets/vendors/gsap/gsap.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/ScrollTrigger.js') }}"></script>
    <script src="{{ asset('assets/vendors/gsap/SplitText.js') }}"></script>
    <script src="{{ asset('assets/vendors/scroll/lenis.min.js') }}"></script>



    <!-- template js -->
    <script src="{{ asset('assets/js/flowtrack.js') }}"></script>
</body>

</html>
