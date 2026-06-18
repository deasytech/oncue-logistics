<footer class="site-footer">
    <div class="site-footer__top">
        <div class="container">
            <div class="site-footer__top-inner">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 wow fadeInUp" data-wow-delay="100ms">
                        <div class="footer-widget__column footer-widget__about">
                            <div class="footer-widget__logo">
                                <a href="/"><img src="{{ asset('assets/images/resources/footer-logo-1.png') }}"
                                        alt="footer logo"></a>
                            </div>
                            <div class="footer-widget__emergency-call">
                                <a href="tel:+2347089091600">Phone: 07089091600</a>
                                <a href="mailto:info@oncuelogistics.com" class="footer-widget__emergency-mail">Email:
                                    info@oncuelogistics.com</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-3 wow fadeInUp" data-wow-delay="200ms">
                        <div class="footer-widget__column footer-widget__navigation">
                            <div class="footer-widget__title-box">
                                <h3 class="footer-widget__title">Navigation</h3>
                            </div>
                            <ul class="footer-widget__navigation-list list-unstyled">
                                <li><a href="/">Home</a></li>
                                <li><a href="who-we-are/">Who We Are</a></li>
                                <li><a href="our-services/">Our Services</a></li>
                                <li><a href="blog/">Blog</a></li>
                                <li><a href="{{ route('contact.page') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 wow fadeInUp" data-wow-delay="200ms">
                        <div class="footer-widget__column footer-widget__navigation">
                            <div class="footer-widget__title-box">
                                <h3 class="footer-widget__title">&nbsp;</h3>
                            </div>
                            <ul class="footer-widget__navigation-list list-unstyled">
                                <li><a href="{{ route('faq.page') }}">FAQ</a></li>
                                <li><a href="{{ route('privacy.page') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('refund.page') }}">Refund Policy</a></li>
                                <li><a href="{{ route('terms.page') }}">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-3 col-md-3 wow fadeInUp" data-wow-delay="400ms">
                        <div class="footer-widget__column footer-widget__newsletter">
                            <div class="footer-widget__title-box">
                                <h3 class="footer-widget__title">Newsletter</h3>
                            </div>
                            <p class="footer-widget__newsletter-text">Subscribe our newsletter to get the
                                latest news & updates</p>

                            @if (session('newsletter_success'))
                                <div class="alert alert-success mb-3" role="alert">
                                    {{ session('newsletter_success') }}
                                </div>
                            @endif

                            @if ($errors->has('email'))
                                <div class="alert alert-danger mb-3" role="alert">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif

                            <form action="{{ route('newsletter.subscribe') }}" method="POST"
                                class="footer-widget__newsletter-form">
                                @csrf
                                <div class="footer-widget__newsletter-input-box">
                                    <input type="email" placeholder="enter your email" name="email"
                                        value="{{ old('email') }}" required>
                                    <button type="submit" class="footer-widget__newsletter-btn">
                                        <i class="icon-paper-plane"></i>
                                    </button>
                                </div>
                                <div class="result"></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="site-footer__bottom-inner">
                <p class="site-footer__bottom-text">&copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> <a href="#">On-Cue Logistics.</a>
                    All
                    Rights Reserved
                </p>
                <div class="site-footer__social">
                    <a href="https://www.instagram.com/oncuelogistics?igsh=aHR4cmllMDhyMmh0" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@oncuelogistics?si=ximWqy38Tm7w-ZHp" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
