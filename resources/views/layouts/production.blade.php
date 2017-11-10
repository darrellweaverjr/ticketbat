<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Security-Policy" content="default-src 'self' http://freegeoip.net/json/ https://maps.google.com https://maps.gstatic.com https://maps.googleapis.com; 
                          img-src * data:'unsafe-inline' 'self' blob: http://admindev.ticketbat.com {{env('IMAGE_URL_OLDTB_SERVER')}} {{env('IMAGE_URL_AMAZON_SERVER')}} https://d3ofbylanic3d6.cloudfront.net https://s3-us-west-2.amazonaws.com;
                          frame-src 'self' https://www.youtube.com https://vimeo.com https://player.vimeo.com;
                          style-src 'self' {{env('IMAGE_URL_AMAZON_SERVER')}} http://fonts.googleapis.com 'unsafe-inline';
                          font-src 'self' http://fonts.gstatic.com;
                          child-src 'none';
                          script-src 'self' http://freegeoip.net/json/ https://maps.google.com https://maps.gstatic.com https://maps.googleapis.com https://connect.facebook.net/en_US/fbevents.js http://www.google-analytics.com/analytics.js;
                          frame-ancestors 'self';">
        <title>@yield('title') - TicketBat</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="{{ config('app.name', 'TicketBat.com') }}" name="author" />
        <meta content="{{Session::get('ua_code','')}}" name="ua-code" />
        <meta @if(!empty($ua_conversion_code)) content="{{$ua_conversion_code}}" @else content="[]" @endif name="ua-conversion_code" />
        <meta @if(!empty($analytics)) content="{{$analytics}}" @else content="[]" @endif name="analytics" />
        <meta @if(!empty($transaction)) content="{{$transaction}}" @else content="" @endif name="transaction" />
        <meta @if(!empty($totals)) content="{{$totals}}" @else content="" @endif name="totals" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{config('app.theme')}}css/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css//bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{config('app.theme')}}css/components.min.css" rel="stylesheet"type="text/css" />
        <link href="{{config('app.theme')}}css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{config('app.theme')}}css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/style_p.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="{{config('app.theme')}}img/favicon.png" /> 
        <link rel="apple-touch-icon" href="{{config('app.theme')}}img/favicon.png"/>
        <link rel="apple-touch-icon-precomposed" href="{{config('app.theme')}}img/favicon.png"/>
        @yield('styles')
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed" style="background-color:black!important">
            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-inverse navbar-fixed-top" style="background-color:black!important">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner container-fluid fixed-panel">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo">
                        <a @if(!empty(Session::get('slug',null))) href="/production/event/{{Session::get('slug')}}" @else href="{{route('index')}}" @endif title="Go to home page">
                        <img src="{{config('app.theme')}}img/_logo.png" alt="logo" class="logo-default"/>
                        </a>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <div class="navbar-header" >
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>                        
                    </div>                    
                    <!-- END RESPONSIVE MENU TOGGLER -->    
                    <!-- BEGIN HORIZANTAL MENU -->
                    <div class="collapse navbar-collapse" style="background:#000">
                        <ul class="nav navbar-nav @if(!empty(Session::get('funnel',null))) hidden @endif">
                            <li @if(preg_match('/\/home/',url()->current())) class="active" @endif>
                                <a @if(!empty(Session::get('slug',null))) href="/production/event/{{Session::get('slug')}}" @else href="{{route('index')}}" @endif class="menu_nav" title="Go to home page">
                                    <i class="icon-home"></i> Home 
                                </a>                                
                            </li>
                            <li @if(preg_match('/\/venues/',url()->current())) class="active" @endif>
                                <a href="/production/venues" class="menu_nav" title="View our venues">
                                    <i class="icon-pointer"></i> Venues 
                                </a>                                
                            </li>
                            <li @if(preg_match('/\/merchandise/',url()->current())) class="active" @endif>
                                <a href="/production/merchandises" class="menu_nav" title="View our merchandises in our venues">
                                    <i class="icon-bag"></i> Merchandise
                                </a>                                
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            @if(!Auth::check())
                            <li>
                                <a data-toggle="modal" href="#modal_register" class="menu_nav" title="Create a new account with us">
                                    <i class="icon-user-follow"></i> Register
                                </a>
                            </li>      
                            <li>
                                <a data-toggle="modal" href="#modal_login" class="menu_nav" title="Log in to your account">
                                   <i class="icon-user"></i> Login
                                </a>
                            </li>
                            @else
                            <li class="dropdown @if(preg_match('/\/user/',url()->current())) active @endif">
                                <a href="#" class="dropdown-toggle menu_nav" data-toggle="dropdown" title="View your user's options">
                                    <i class="icon-user"></i><span class="username"> {{Auth::user()->first_name}} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a data-toggle="modal" href="#modal_reset_password" title="Change your password">
                                        <i class="icon-lock"></i>&nbsp;&nbsp;&nbsp;Change password</a>
                                    </li>
                                    <li>
                                        <a href="/production/user/purchases" title="List your purchases">
                                        <i class="icon-basket"></i>&nbsp;&nbsp;&nbsp;My Purchases</a>
                                    </li>
                                    @if(in_array(Auth::user()->user_type_id,explode(',',env('SELLER_OPTION_USER_TYPE'))))
                                    <li>
                                        <a href="/production/user/consignments" title="View your consignment tickets">
                                        <i class="icon-tag"></i>&nbsp;&nbsp;&nbsp;My Consignments</a>
                                    </li>
                                    @endif
                                    <li>                                    
                                    <li>
                                        <a id="btn_logout" title="Log out session">
                                        <i class="icon-key"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            <li class="dropdown-notification @if(preg_match('/\/shoppingcart/',url()->current())) active @endif">
                                <a href="/production/shoppingcart" class="menu_nav" title="View/pay you items in the shopping cart">
                                    <i class="icon-basket"></i> Shopping Cart <span class="badge badge-danger"><b id="shoppingcart_qty_items" style="font-size:14px">Loading</b></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END HORIZANTAL MENU -->
                </div>
                <!-- END HEADER INNER -->
                
                <div id="timerClockPanel">
                    <span class="uppercase text-justify">Please complete your purchase in</span>
                    <span id="timerClock" data-countdown="{{Session::get('countdown','')}}"></span>
                    <span class="uppercase text-justify">minutes : seconds</span>
                    <a href="/production/shoppingcart" style="font-size:10px;font-weight:bold" class="btn btn-success">checkout now</a> 
                </div>  
                
            </div>
            <!-- END HEADER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container page-content-white">
                <!-- BEGIN CONTENT -->
                @yield('content')
                <!-- END CONTENT -->
            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer fixed-panel">
                <div class="page-footer-inner">
                    <a class="menu_foot" data-toggle="modal" href="#modal_privacy" title="Watch out our privacy policy">Privacy</a> &nbsp;|&nbsp;
                    <a class="menu_foot" data-toggle="modal" href="#modal_terms" title="Check our terms of use">Terms</a> &nbsp;|&nbsp;
                    <a class="menu_foot" data-toggle="modal" href="#modal_contact_us" title="If you have any issues, please, let us know" >Contact Us</a>
                </div>      
                <div class="copyright pull-right">
                    &copy; 2015-{{date('Y')}}  TicketBat. All rights reserved.
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
            <!-- BEGIN PRIVACY MODAL -->
            @includeIf('production.general.privacy')
            <!-- END PRIVACY MODAL -->
            <!-- BEGIN TERMS MODAL -->
            @includeIf('production.general.terms')
            <!-- END TERMS MODAL -->
            <!-- BEGIN CONTACT MODAL -->
            @includeIf('production.general.contact')
            <!-- END CONTACT MODAL -->
            <!-- BEGIN LOGIN MODAL -->
            @includeIf('production.user.login')
            <!-- END LOGIN MODAL -->
            <!-- BEGIN REGISTER MODAL -->
            @includeIf('production.user.register')
            <!-- END REGISTER MODAL -->
            <!-- BEGIN RECOVER PASSWORD MODAL -->
            @includeIf('production.user.recover_password')
            <!-- END RECOVER PASSWORD MODAL -->
            <!-- BEGIN RESET PASSWORD MODAL -->
            @includeIf('production.user.reset_password')
            <!-- END RECOVER RESET MODAL -->
            
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{config('app.theme')}}js/jquery.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{config('app.theme')}}js/moment.min.js" type="text/javascript"></script>
        <!-- SCRIPT FOR SWEET ALERT -->
        <script src="{{config('app.theme')}}js/sweetalert.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js//bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{config('app.theme')}}js/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <script src="{{config('app.theme')}}js/additional-methods.min.js" type="text/javascript"></script>
        <!-- SCRIPT FOR UPLOAD IMAGE FILE -->
        <script src="/js/utils/index.js" type="text/javascript"></script>
        <script src="/js/production/general/index.js" type="text/javascript"></script>
        <script src="/js/production/general/analytics.js" type="text/javascript"></script>
        <script src="/js/production/general/contact.js" type="text/javascript"></script>
        <script src="/js/production/user/login.js" type="text/javascript"></script>
        <script src="/js/production/user/register.js" type="text/javascript"></script>
        <script src="/js/production/user/recover_password.js" type="text/javascript"></script>
        <script src="/js/production/user/reset_password.js" type="text/javascript"></script>
        @if(Auth::check() && Auth::user()->force_password_reset>0) 
        <script type="text/javascript">$('#modal_reset_password').modal('show');</script>
        @endif
        @yield('scripts')
        
        @if(!empty($conversion_code))
        @foreach($conversion_code as $cc)
            echo $cc
        @endforeach
        @endif
    </body>

</html>