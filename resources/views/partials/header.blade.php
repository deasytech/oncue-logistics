<header class="main-header">
    <style>
        /* Prevent horizontal scrolling in header */
        .main-header {
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        .main-menu__wrapper-inner {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .main-menu__list {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Ensure login button is visible on iPad and desktop screens (768px+) */
        @media (min-width: 768px) {
            .main-menu__right {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .main-menu__btn-box {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .main-menu__btn {
                display: inline-block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }

        /* Fix left menu positioning at 1280px - keep it on the left */
        @media only screen and (min-width: 1200px) and (max-width: 1499px) {
            .main-menu__left {
                justify-content: flex-start !important;
                flex: 0 0 auto !important;
            }
        }

        /* Hide mobile login button and initials in main menu list on desktop screens */
        @media (min-width: 768px) {
            .main-menu__list .mobile__login_btn {
                display: none !important;
            }

            /* Hide initials in main menu list on desktop */
            .main-menu__list .mobile-initials,
            .main-menu__list .mobile-user-btn-left {
                display: none !important;
            }
        }

        /* Show login button on mobile screens with enhanced styling */
        @media (max-width: 767px) {
            /* .main-menu__right {
                display: block !important;
            } */

            /* Make login button stand out on mobile */
            .mobile__login_btn {
                background: linear-gradient(135deg, #65044D 0%, #AB1E7C 100%) !important;
                color: white !important;
                border: 2px solid white !important;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
                font-weight: bold !important;
                padding: 12px 18px !important;
                border-radius: 25px !important;
                transition: all 0.3s ease !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                margin-top: 10px;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .mobile__login_btn:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6) !important;
                background: linear-gradient(135deg, #AB1E7C 0%, #65044D 100%) !important;
            }

            /* Enhanced initials styling for mobile */
            .user-initials-btn .initials-circle {
                background: linear-gradient(135deg, #65044D 0%, #AB1E7C 100%) !important;
                border: 3px solid white !important;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
                width: 45px !important;
                height: 45px !important;
                font-size: 16px !important;
            }

            .user-initials-btn:hover .initials-circle {
                transform: scale(1.15) !important;
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6) !important;
            }
        }
    </style>
    <div class="main-menu__top">
        <div class="main-menu__top-inner">
            <ul class="list-unstyled main-menu__contact-list">
                <li>
                    <div class="icon">
                        <i class="icon-location"></i>
                    </div>
                    <div class="text">
                        <p>Lagos, Nigeria</p>
                    </div>
                </li>
                <li>
                    <div class="icon">
                        <i class="icon-email"></i>
                    </div>
                    <div class="text">
                        <p><a href="mailto:info@oncuelogistics.com">info@oncuelogistics.com</a>
                        </p>
                    </div>
                </li>
            </ul>
            <div class="main-menu__top-right">
                <div class="main-menu__social-box">
                    <div class="main-menu__social-box-inner">
                        <h4 class="main-menu__social-box-title">Follow us:</h4>
                        <div class="main-menu__social">
                            <a href="https://www.instagram.com/oncuelogistics?igsh=aHR4cmllMDhyMmh0" target="_blank">
                                <i class="icon-instagram"></i>
                            </a>
                            <a href="https://youtube.com/@oncuelogistics?si=ximWqy38Tm7w-ZHp" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="main-menu__wrapper-inner">
                <div class="main-menu__left">
                    <div class="main-menu__logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('assets/images/resources/logo.png') }}" alt="">
                        </a>
                    </div>
                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li class="@if (Route::currentRouteName() === 'home') current @endif">
                                <a href="{{ route('home') }}">Home </a>
                            </li>
                            <li class="@if (Route::currentRouteName() === 'who-we-are.index') current @endif">
                                <a href="{{ route('who-we-are.index') }}">Who We Are</a>
                            </li>
                            <li class="@if (Route::currentRouteName() === 'our-services.index') current @endif">
                                <a href="{{ route('our-services.index') }}">Our Services</a>
                            </li>
                            <li class="@if (Route::currentRouteName() === 'blog.index') current @endif">
                                <a href="{{ route('blog.index') }}">Blog</a>
                            </li>
                            <li class="@if (Route::currentRouteName() === 'contact.page') current @endif">
                                <a href="{{ route('contact.page') }}">Contact Us</a>
                            </li>
                            @if (auth()->check())
                                <li>
                                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                                        class="mobile-user-btn-left">
                                        <span
                                            class="initials-circle mobile-initials">{{ auth()->user()->initials() }}</span>
                                    </a>
                                </li>
                            @else
                                <li class="@if (Route::currentRouteName() === 'login') current @endif">
                                    <a href="{{ route('login') }}" class="mobile__login_btn">Login</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="main-menu__right">
                    <div class="main-menu__search-cart-btn-box">
                        <div class="main-menu__btn-box">
                            @if (auth()->check())
                                <a href="{{ route('dashboard') }}" class="thm-btn main-menu__btn user-initials-btn"
                                    title="{{ auth()->user()->name }}">
                                    <span>{{ auth()->user()->initials() }}</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="thm-btn main-menu__btn">Login</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
