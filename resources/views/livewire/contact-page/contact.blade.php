<div class="page-wrapper">
    <div class="stricky-header stricked-menu main-menu main-menu-two">
        <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
    </div><!-- /.stricky-header -->

    <!--Page Header Start-->
    <section class="page-header">
        <div class="page-header__bg" style="background-image: url(../assets/images/backgrounds/page-header-bg.jpg);">
        </div>
        <div class="container">
            <div class="page-header__inner">
                <h2>Contact Us</h2>
                <div class="thm-breadcrumb__box">
                    <ul class="thm-breadcrumb list-unstyled">
                        <li><a href="/">Home</a></li>
                        <li><span>-</span></li>
                        <li>Contact Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!--Page Header End-->

    <!--Contact Page Start-->
    <section class="contact-page">
        <div class="container">
            <div class="contact-page__middle">
                <div class="row">
                    <div class="col-xl-12 col-lg-6">
                        <div class="contact-page__middle-right">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7496.076656381409!2d3.4285322512200618!3d6.4500672056926955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103bf4c4c84b52bb%3A0x5faec50ebdf1a1ea!2sIkoyi!5e0!3m2!1sen!2sus!4v1770069169534!5m2!1sen!2sus"
                                id="gmap_canvas" width="100%" height="575" style="border:0;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-6">
                        <section class="destination-one">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6">
                                    <div class="destination-one__left">
                                        <div class="icon">
                                            <span class="icon-email"></span>
                                        </div>
                                        <div class="destination-one__left-content">
                                            <p class="destination-one__left-text">Phone: <a
                                                    href="tel:+2347089091600">07089091600</a></p>
                                            <p class="destination-one__left-text">Email: <a
                                                    href="mailto:info@oncuelogistics.com">info@oncuelogistics.com</a>
                                            </p>
                                            <p class="destination-one__left-text">Website: <a
                                                    href="https://www.oncuelogistics.com">www.oncuelogistics.com</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6">
                                    <div class="destination-one__right">
                                        <div class="icon">
                                            <span class="icon-location"></span>
                                        </div>
                                        <div class="content">
                                            <h3>Our Location</h3>
                                            <p>Ikoyi, Lagos, <br>Nigeria</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="contact-page__bottom">
                <div class="contact-page__form-box">
                    <h3 class="comment-one__title">Let’s Get in Touch</h3>
                    <p class="comment-one__text">
                        Your email address will not be published. Required fields are
                        marked *
                    </p>
                    <form wire:submit.prevent="submit" class="contact-page__form contact-form-validated">
                        @if ($successMessage)
                            <div class="alert alert-success">
                                {{ $successMessage }}
                            </div>
                        @endif
                        @if ($errorMessage)
                            <div class="alert alert-danger">
                                {{ $errorMessage }}
                            </div>
                        @endif
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('subject')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('message')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-page__input-box">
                                    <input type="text" placeholder="Your Name*" wire:model="name" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-page__input-box">
                                    <input type="email" placeholder="Your Email*" wire:model="email" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-page__input-box">
                                    <input type="text" placeholder="Phone*" wire:model="phone" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-page__input-box">
                                    <input type="text" placeholder="Subject*" wire:model="subject" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="contact-page__input-box text-message-box">
                                    <textarea required wire:model="message" placeholder="Your Message"></textarea>
                                </div>
                                <div class="contact-page__btn-box">
                                    <button type="submit" class="thm-btn contact-page__btn"
                                        data-loading-text="Please wait...">
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="result"></div>
                    </form>

                    <script>
                        document.addEventListener('livewire:init', () => {
                            Livewire.on('clear-success-message', () => {
                                setTimeout(() => {
                                    @this.successMessage = '';
                                }, 5000);
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </section>
    <!--Contact Page End-->



    <!--Site Footer Start-->
    @include('partials.footer')
    <!--Site Footer End-->
</div>
