<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta name="description" content="Loan Alert Platform prompts notifications of upcoming Loan Payments more effectively, quickly and intelligently to all your Clients. It is designed to give you all the information you need to serve you Clients Better.">
    <meta name="keywords" content="Loan Alert , Loan-Alert, TechLegend, MicroFinance Solution, SAAS, Tanzania Platform">
    <meta name="author" content="TechLegend - Creative Inspiration">
     <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | Taking Care of What's Important!</title>

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- style css -->
    <link rel="stylesheet" href="{{ asset('vendor/landing/style.css') }}">
    <!-- modernizr js -->
    <script src="{{ asset('vendor/landing/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>

</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <!-- Loading Bar Start -->
    <div id="loading-wrap">
        <div class="loading-effect">
            <img src="{{ asset('vendor/landing/assets/img/logo.png') }}" alt="Loan Alert Logo" />
        </div>
    </div>
    <!-- Loading Bar End -->
    <div id="home"></div>
    <!-- Header Section Start -->
    <header class="clearfix gradient-1 height-100 relative fix">
        <div class="circle-shape">
            <div class="dot c1 c100"></div>
            <div class="dot c2 c53"></div>
            <div class="dot c3 c355"></div>
            <div class="dot c4 c172"></div>
            <div class="dot c5 c206"></div>
            <div class="dot c6 c84"></div>
            <div class="dot c7 c140"></div>
            <div class="dot c8 c100"></div>
            <div class="dot c9 c53"></div>
            <div class="dot c10 c100"></div>
            <div class="dot c11 c100"></div>
            <div class="dot c12 c44"></div>
            <div class="dot c13 c44"></div>
            <div class="dot c14 c34"></div>
            <div class="dot c15 c44"></div>
            <div class="dot c16 c393"></div>
        </div>
        <div id="active-sticky" class="header-top absolute">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="left floatleft">
                            <div class="logo">
                                <a href="{{ url('/') }}"><img src="{{ asset('vendor/landing/assets/img/logo.png') }}" alt="Loan Alert" /></a>
                            </div>
                        </div>
                        <div class="get-btn floatright">
                            <a class="btn border ripple-btn" href="{{ url('login') }}"><span>Login</span></a>
                        </div>
                        <div class="right floatright">
                            <nav class="mainmenu onepage-nev floatright">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>
                                <div class="navbar-collapse collapse clearfix">
                                    <ul class="navigation clearfix">
                                        <li><a href="#home">Home</a></li>
                                        <li><a href="#about">About</a></li>
                                        <li><a href="#features">Features</a></li>
                                        <li><a href="#premier">Premier</a></li>
                                        <li><a href="#support">Contact Us</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom relative angle-after height-100">
            <div class="d-table relative">
                <div class="d-table-cell">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-7 col-md-7 wow fadeInLeft" data-wow-delay=".3s" data-wow-duration="1s">
                                <div class="left m-minus pt-20">
                                    <div class="slide-text pt-200">
                                        <h1 class="font-90 we-ex-bold white-color upperLoan mb-20">Loan Alert</h1>
                                        <p class="white-color l-height-26 font-18 mb-40">Prompts notifications of Loan payments more effectively, quickly and intelligently to all your Clients in Time!
                                        </p>
                                        <a class="btn box-shadow mr-30 ripple-btn" href="{{ url('login') }}">
                                            <span>Sign In</span>
                                        </a>
                                        <a class="btn border ripple-btn" href="#support">
                                            <span>Get Free Trial</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-5 col-md-5 wow fadeInRight" data-wow-delay=".3s" data-wow-duration="1s">
                                <div class="right text-right">
                                    <div class="slide-img relative">
                                        <img src="{{ asset('vendor/landing/assets/img/slider/1.png') }}" alt="Slide Image" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section End -->
    <!-- Spacial Section Start -->
    <div class="special-area text-center pt-40 pb-110" id="about">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 wow zoomIn" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="section-title in-block mb-80">
                        <h1>Why is it <span class="theme-color">Special</span></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 mobile-mb-30 wow fadeInUpSmall" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="single-special">
                        <i class="zmdi zmdi-account-circle"></i>
                        <h4 class="mb-15">Client's First</h4>
                        <p>Loan Alert is Designed with needs of your Client First. We take care of tedious things so you can focus on serving your Clients Better, efficiently.</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 mobile-mb-30 wow fadeInUpSmall" data-wow-delay=".6s" data-wow-duration="1s">
                    <div class="single-special">
                        <i class="zmdi zmdi-wrap-text"></i>
                        <h4 class="mb-15">Greatly Adaptive</h4>
                        <p>You don't have to through away all your software, Loan Alert merge and enhance what you already have.</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 wow fadeInUpSmall" data-wow-delay=".9s" data-wow-duration="1s">
                    <div class="single-special">
                        <i class="zmdi zmdi-arrow-split"></i>
                        <h4 class="mb-15">Business Growth</h4>
                        <p>Loan Alert is the gift that keeps on growing. We're continuously adding more tools that you love (making your membership even more valuable over time).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Spacial Section End -->
    <!-- Customize Section Start -->
    <div class="customize-area ptb-80 bg-color-1">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-5 wow fadeInLeft" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="easy-img text-center">
                        <img src="{{ asset('vendor/landing/assets/img/mockup/1.png') }}" alt="Easy Image" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-7 wow fadeInRight" data-wow-delay=".2s" data-wow-duration="1s">
                    <div class="section-title easy-text pt-45 in-block">
                        <h1 class="mb-20">Simplify your life & streamline<br />
                                <span class="theme-color font-50 we-bold">Your Financial Institute</span>
                            </h1>
                        <p class="font-15 l-height-25 mb-20">Loan Alert was created with one idea in mind: the tools you need to run your business shouldn't put you out of business.</p>

                        <p class="font-15 l-height-25 mb-25">Which is why we have created a range of subscriptions option so you can enjoy our most incredible offers yet, without costing you an arm... </p>
                        <a class="btn ripple-btn" disabled=""><span><b>Subscribe Now!</b>  <i>"100% Money-Back Guarantee"</i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Customize Section End -->
    <!-- Features Section Start -->
    <div class="features-area section-padding" id="features">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center tablet-mb-50 wow zoomIn" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="section-title title-2 in-block mb-70">
                        <h1 class="mb-30 l-height-in">Best <span class="theme-color">Features</span></h1>
                        <p class="font-16 l-height-26">Loan Alert offers more than we can put down without tiring you down.
                            <br /> Here are just a few of the problems Loan Alert will solve for you:
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="left text-right mt-155 pr-28">
                        <div class="single-features pb-110 relative wow fadeInRight" data-wow-delay=".4s" data-wow-duration="1s">
                            <div class="arrow-line absolute">
                                <div class="circle-dot"></div>
                            </div>
                            <i class="zmdi zmdi-brush"></i>
                            <h4 class="font-20 l-height-24 mb-5">SMS Notification</h4>
                            <p>Worried about Your Clients? Loan Payments? The platform sends SMS notifications for every Loan progress to your client prior the Payment date/time.</p>
                        </div>
                        <div class="single-features relative wow fadeInRight" data-wow-delay=".6s" data-wow-duration="1s">
                            <div class="arrow-line absolute">
                                <div class="circle-dot"></div>
                            </div>
                            <i class="zmdi zmdi-devices"></i>
                            <h4 class="font-20 l-height-24 mb-5">Adaptability</h4>
                            <p>Do you want to be unique to your competitors? And since everyone’s Financial Institute is at a different stage, we made sure to avoid a one-size-fits-all solution.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 no-padding text-center wow fadeInUpSmall" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="features-img">
                        <img src="{{ asset('vendor/landing/assets/img/mockup/2.png') }}" alt="Features Image" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="right text-left mt-155 pl-28">
                        <div class="single-features pb-110 relative wow fadeInLeft" data-wow-delay=".4s" data-wow-duration="1s">
                            <div class="arrow-line absolute">
                                <div class="circle-dot"></div>
                            </div>
                            <i class="zmdi zmdi-collection-text"></i>
                            <h4 class="font-20 l-height-24 mb-5">Executive Summary</h4>
                            <p>Yes, Information is Power... Loan Alert gives you a summary of how your Financila Institute is doing at a click of a button. (You don't have to sniff around pile of papers again)</p>
                        </div>
                        <div class="single-features relative wow fadeInLeft" data-wow-delay=".6s" data-wow-duration="1s">
                            <div class="arrow-line absolute">
                                <div class="circle-dot"></div>
                            </div>
                            <i class="zmdi zmdi-favorite"></i>
                            <h4 class="font-20 l-height-24 mb-5">Additional Tools</h4>
                            <p>Need to provide accurate and creative products? From Client Managent to Expiration of Important Materials, We provide you will tools you need to work effectively.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features Section End -->
    <!-- Watch Support Section Start -->
    <div class="watch-area ptb-115 gradient-1 relative fix" id="premier">
        <div class="circle-shape circle-shape-2">
            <div class="dot c1 c44"></div>
            <div class="dot c4 c172"></div>
            <div class="dot c5 c206"></div>
            <div class="dot c6 c84"></div>
            <div class="dot c8 c100"></div>
            <div class="dot c9 c53"></div>
            <div class="dot c15 c84"></div>
            <div class="dot c16 c206"></div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-6 col-md-offset-1 wow fadeInLeft" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="section-title watch-text pt-45 in-block">
                        <h1 class="white-color mb-20">Premier <br />
                                <span class="font-50 we-bold">Loan Alert</span>
                            </h1>
                        <p class="font-15 l-height-25 mb-20 white-color">Your very own Custome Loan Alert! That's right, We’re giving you the all-star treatment. Just like we've done with hundreds of partners, we put your Financial Institute need First!</p>
                        <a class="btn ripple-btn" href="#support"><span>Get In-touch</span></a>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-md-offset-1 wow fadeInRight" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="watch-img text-left">
                        <img src="{{ asset('vendor/landing/assets/img/mockup/3.png') }}" alt="Watch Image" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Watch Support Section End -->
    <!-- Blog Section Start -->
    <section class="blog-area blog-one small-section-padding bg-color-2" id="blog">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center wow zoomIn" data-wow-delay=".3s" data-wow-duration="1s">
                    <div class="section-title title-2 in-block mb-70">
                        <h1 class="mb-25 l-height-1">Loan <span class="theme-color">Alert</span></h1>
                        <p class="font-16 l-height-26">Taking Care of What's Important!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Section End -->
    <!-- Contact Section Start -->
    <section class="contact-area white-bg section-padding" id="support">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5 mobile-mb-30">
                    <div class="info-text">
                        <h3 class="font-26 mb-20 in-block">Contact Info</h3>
                        <p>We know that decision making happens at all hours of the day, which is why we offer more options to get in touch with us. <br><br>Up late? So are we, with extended customer service hours.</p>
                        <hr class="line width-170" />
                        <ul class="contact-text clearfix">
                            <li>
                                <h5 class="font-16 mb-10 l-height-26 capitalize"> 
                                        Address: <span class="font-14 we-light">2nd Floor, Viva Towers <br />  Dar es Salaam, Tanzania</span>
                                    </h5>
                            </li>
                            <li>
                                <h5 class="font-16 l-height-26 capitalize we-regular">
                                        Phone: <span class="font-14 we-light"><a class="montserrat we-light" href="tel:+255 713-071267">+255 713-071 267</a></span>
                                    </h5>
                            </li>
                            <li>
                                <h5 class="font-16 l-height-26 capitalize we-regular">
                                        Email: <span class="font-14 we-light"><a class="lowercase montserrat we-light" href="mailto:ask@loan-alert.pro">ask@loan-alert.pro</a></span>
                                    </h5>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-7">
                    <div class="contact-form plr">
                        <form class="custom-input contact_form name-email" id="contact_form" action="{{ url('contact') }}" method="post">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <p class="mb-10 l-height-1">Your Name:</p>
                                    <input type="text" id="contact_name" name="name" />
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <p class="mb-10 l-height-1">Email Address:</p>
                                    <input type="email" id="contact_email" name="email" />
                                </div>
                            </div>
                            <p class="mb-10 l-height-1">Your Message:</p>
                            <textarea name="message" id="contact_message" rows="5"></textarea>
                            <button class="btn ripple-btn upperLoan" type="submit" name="submit" id="contact_submit" data-complete-text="Message Sent. Thank You!">
                                <span>Send Message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->
    <!-- Footer Section Start -->
    <div class="footer-area ptb-100 gradient-1 relative fix">
        <div class="circle-shape circle-shape-2">
            <div class="dot c1 c44"></div>
            <div class="dot c4 c172"></div>
            <div class="dot c5 c206"></div>
            <div class="dot c6 c84"></div>
            <div class="dot c8 c100"></div>
            <div class="dot c9 c53"></div>
            <div class="dot c15 c84"></div>
            <div class="dot c16 c206"></div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-6 col-text-center text-center">
                    <div class="footer-text">
                        <div class="social-icon l-height-1 mb-25">
                            <ul class="clearfix in-block">
                                <li><a href="https://web.facebook.com/Loan-Alert-172369376896376/" target="_blank"><i class="zmdi zmdi-facebook"></i></a></li>
                                <li><a href="https://twitter.com/TechLegendTz" target="_blank"><i class="zmdi zmdi-twitter"></i></a></li>
                                <li><a href="https://www.instagram.com/techlegendtz/" target="_blank"><i class="zmdi zmdi-instagram"></i></a></li>
                            </ul>
                        </div>
                        <p class="font-13 white-color we-light">Copyright &copy; {{ Carbon::now()->year }} <a class="font-13 white-color we-light capitalize" href="http://techlegend.co" target="_blank">TechLegend</a>. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Section End -->

    <!-- All JS Here -->
    <!-- jQuery Latest Version -->
    <script src="{{ asset('vendor/landing/assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/landing/assets/js/bootstrap.min.js') }}"></script>
    <!-- Form Validate -->
    <script src="{{ asset('vendor/landing/assets/js/jquery.validate.min.js') }}"></script>
    <!-- Validate Active JS -->
    <script src="{{ asset('vendor/landing/assets/js/validate-active.js') }}"></script>
    <!-- Slick Slider JS -->
    <script src="{{ asset('vendor/landing/assets/js/slick.min.js') }}"></script>
    <!-- Plugins JS -->
    <script src="{{ asset('vendor/landing/assets/js/plugins.js') }}"></script>
    <!-- main JS -->
    <script src="{{ asset('vendor/landing/assets/js/main.js') }}"></script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/5aa97756d7591465c7089165/default';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</body>

</html>