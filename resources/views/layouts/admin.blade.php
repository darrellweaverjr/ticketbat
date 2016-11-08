<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{{ config('app.name', 'TicketBat Admin') }} - @yield('title')</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for statistics, charts, recent events and reports" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="/themes/admin/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="/themes/admin/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="/themes/admin/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="/themes/admin/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="/themes/admin/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />

        <link href="/themes/admin/assets/pages/css/style.css" rel="stylesheet" type="text/css" />

        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> 
        @yield('styles')
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner ">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo">
                        <a href="">
                            <img src="/themes/admin/assets/layouts/layout/img/logo.png" alt="logo" class="logo-default" /> </a>
                        <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
<!--                                     <img alt="" class="img-circle" src="/themes/admin/assets/layouts/layout/img/avatar3_small.jpg" />
 -->                                    <span class="username username-hide-on-mobile"> Hello, {{Auth::user()->first_name}} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
<!--                                 <ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a href="page_user_profile_1.html">
                                            <i class="icon-user"></i> My Profile 
                                        </a>
                                    </li>
                                    <li>
                                        <a href="app_todo.html">
                                            <i class="icon-rocket"></i> Impersonate
                                        </a>
                                    </li>
                                    <li class="divider"> </li>
                                    <li>
                                        <a href="../logout">
                                            <i class="icon-key"></i> Log Out 
                                        </a>
                                    </li>
                                </ul> -->
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-quick-sidebar-toggler">
                                <a href="../logout" class="dropdown-toggle">
                                    <i class="icon-logout"></i>
                                </a>
                            </li>
                            <!-- END QUICK SIDEBAR TOGGLER -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <!-- END HEADER -->
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>                            
                            <li class="nav-item start active open">
                                <a href="/admin/home" class="nav-link nav-toggle">
                                    <i class="icon-home"></i>
                                    <span class="title">Dashboard</span>
                                    <span class="selected"></span>
                                    <span class="arrow open"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item start active open">
                                        <a href="/admin/dashboard/ticket_sales" class="nav-link ">
                                            <i class="icon-bar-chart"></i>
                                            <span class="title">Ticket Sales</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>
                                    <li class="nav-item start ">
                                        <a href="/admin/dashboard/chargebacks" class="nav-link ">
                                            <i class="icon-briefcase"></i>
                                            <span class="title">Chargebacks</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start ">
                                        <a href="/admin/dashboard/future_liabilities" class="nav-link ">
                                            <i class="icon-graph"></i>
                                            <span class="title">Future Liabilities</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start ">
                                        <a href="/admin/dashboard/trend_pace" class="nav-link ">
                                            <i class="icon-bulb"></i>
                                            <span class="title">Trend & Pace</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start ">
                                        <a href="/admin/dashboard/referrals" class="nav-link ">
                                            <i class="icon-note"></i>
                                            <span class="title">Referrals</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('USERS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="/admin/users" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Users</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('BANDS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-music-tone"></i>
                                    <span class="title">Bands</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('VENUES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-pointer"></i>
                                    <span class="title">Venues</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('SHOWS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-microphone"></i>
                                    <span class="title">Shows</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('TYPES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-layers"></i>
                                    <span class="title">Ticket Types</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('COUPONS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="?p=" class="nav-link nav-toggle">
                                    <i class="icon-wallet"></i>
                                    <span class="title">Coupons</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('PACKAGES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-support"></i>
                                    <span class="title">Packages</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('ACLS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">ACLs</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('MANIFESTS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-envelope"></i>
                                    <span class="title">Manifest Emails</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('CONTACTS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-feed"></i>
                                    <span class="title">Contact Log</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('PURCHASES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-basket"></i>
                                    <span class="title">Purchases</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('SLIDERS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-camera"></i>
                                    <span class="title">Hero Slider</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('TICKETS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-tag"></i>
                                    <span class="title">Consignment Tickets</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('APPS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-calendar"></i>
                                    <span class="title">Mobile App</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('CONTRACTS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item  ">
                                <a href="javascript:;" class="nav-link nav-toggle">
                                    <i class="icon-docs"></i>
                                    <span class="title">Contracts</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                        <!-- END SIDEBAR MENU -->
                    </div>
                    <!-- END SIDEBAR -->
                </div>
                <!-- END SIDEBAR -->
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                    	@yield('content')
                    </div>    
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer">
                <div class="page-footer-inner"> {{date('Y')}} &copy; {{ config('app.name', 'TicketBat Admin') }} By
                    <a target="_blank" href="https://ticketbat.com">TicketBat.com</a> 
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
        <!--[if lt IE 9]>
<script src="/themes/admin/assets/global/plugins/respond.min.js"></script>
<script src="/themes/admin/assets/global/plugins/excanvas.min.js"></script> 
<script src="/themes/admin/assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="/themes/admin/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="/themes/admin/assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/horizontal-timeline/horizontal-timeline.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        {{--<script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script>--}}
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="/themes/admin/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="/themes/admin/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="/themes/admin/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        @yield('scripts')
    </body>

</html>