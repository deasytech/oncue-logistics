<div class="page-wrapper">
    <div class="stricky-header stricked-menu main-menu main-menu-two">
        <div class="sticky-header__content"></div>
    </div>

    <!--Page Header Start-->
    <section class="page-header">
        <div class="page-header__bg"
            style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});">
        </div>
        <div class="container">
            <div class="page-header__inner">
                <h2>Frequently Asked Questions (FAQs)</h2>
                <div class="thm-breadcrumb__box">
                    <ul class="thm-breadcrumb list-unstyled">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><span>-</span></li>
                        <li>Frequently Asked Questions (FAQs)</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--Page Header End-->

    <!--FAQ Page Start-->
    <section class="faq-page">
        <div class="container">
            <div class="faq-page__text-box">
                <div class="faq-page__text-box-shape-1">
                    <img src="assets/images/shapes/faq-page-text-box-shape-1.png" alt="">
                </div>
                <div class="section-title text-left sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <span class="section-title__tagline">Our question and answer</span>
                    </div>
                    <h2 class="section-title__title title-animation">Frequently Asked Question
                        <br> & Answer Here
                    </h2>
                </div>
            </div>
            <div class="faq-page__bottom">
                <div class="row">

                    <div class="col-xl-12 col-lg-12">
                        <div class="faq-page__bottom-right">
                            <div class="accrodion-grp faq-one-accrodion" data-grp-name="faq-one-accrodion-1">
                                <div class="accrodion active">
                                    <div class="accrodion-title">
                                        <h4>What services does On-Cue Logistics provide?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>On-Cue Logistics offers same-day and scheduled dispatch services, event
                                                logistics coordination, Aso-Ebi bulk distribution, corporate delivery
                                                solutions, and dedicated rider services.</p>
                                        </div><!-- /.inner -->
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>What areas do you operate in?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>We operate across Lagos State, with expansion plans into other major
                                                Nigerian cities.</p>
                                        </div><!-- /.inner -->
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>Does On Cue Logistics sell Aso Ebi?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>No. We provide packaging, coordination, and delivery services. Fabric
                                                sales remain the responsibility of the event owner.</p>
                                        </div><!-- /.inner -->
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>How do I book a delivery?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>You can book via our Website/Corporate dispatch portal, WhatsApp, Email,
                                                or Instagram page.</p>
                                        </div><!-- /.inner -->
                                    </div>
                                </div>
                                <div class="accrodion">
                                    <div class="accrodion-title">
                                        <h4>What is the minimum number of recipients?</h4>
                                    </div>
                                    <div class="accrodion-content">
                                        <div class="inner">
                                            <p>60 recipients per event engagement.</p>
                                        </div><!-- /.inner -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--FAQ Page End-->

    @include('partials.footer')
</div>
