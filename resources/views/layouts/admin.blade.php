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
        <link href="/themes/admin/assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/jcrop/css/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="/themes/admin/assets/global/css/components.min.css" rel="stylesheet"type="text/css" />
        <link href="/themes/admin/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="/themes/admin/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css"/>
        <link href="/themes/admin/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />

        <link href="/themes/admin/assets/pages/css/style.css" rel="stylesheet" type="text/css" />

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="/themes/admin/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="/themes/admin/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        
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
                    <a class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user">
                                <a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <span class="username username-hide-on-mobile"> Hello, {{Auth::user()->first_name}} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
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
                                </ul>
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
                            <li class="nav-item start open @if(!(strpos(url()->current(),'/admin/home')===false) || !(strpos(url()->current(),'/admin/dashboard')===false)) active @endif">
                                <a href="/admin/home" class="nav-link nav-toggle">
                                    <i class="icon-home"></i>
                                    <span class="title">Dashboard</span>
                                    <span class="selected"></span>
                                    <span class="arrow open"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item active">
                                        <a href="/admin/dashboard/ticket_sales" class="nav-link">
                                            <i class="icon-bar-chart"></i>
                                            <span class="title">Ticket Sales</span>
                                            
                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/chargebacks" class="nav-link">
                                            <i class="icon-briefcase"></i>
                                            <span class="title">Chargebacks</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/future_liabilities" class="nav-link">
                                            <i class="icon-graph"></i>
                                            <span class="title">Future Liabilities</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/trend_pace" class="nav-link">
                                            <i class="icon-bulb"></i>
                                            <span class="title">Trend & Pace</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/referrals" class="nav-link">
                                            <i class="icon-note"></i>
                                            <span class="title">Referrals</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('USERS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/users')===false)) active @endif">
                                <a href="/admin/users" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Users</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('BANDS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/bands')===false)) active @endif">
                                <a href="/admin/bands" class="nav-link nav-toggle">
                                    <i class="icon-music-tone"></i>
                                    <span class="title">Bands</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('VENUES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/venues')===false)) active @endif">
                                <a href="/admin/venues" class="nav-link nav-toggle">
                                    <i class="icon-pointer"></i>
                                    <span class="title">Venues</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('SHOWS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/shows')===false)) active @endif">
                                <a href="/admin/shows" class="nav-link nav-toggle">
                                    <i class="icon-microphone"></i>
                                    <span class="title">Shows</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('TYPES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/ticket_types')===false)) active @endif">
                                <a href="/admin/ticket_types" class="nav-link nav-toggle">
                                    <i class="icon-layers"></i>
                                    <span class="title">Ticket Types</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('COUPONS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/coupons')===false)) active @endif">
                                <a href="/admin/coupons" class="nav-link nav-toggle">
                                    <i class="icon-wallet"></i>
                                    <span class="title">Coupons</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('PACKAGES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/packages')===false)) active @endif">
                                <a href="/admin/packages" class="nav-link nav-toggle">
                                    <i class="icon-support"></i>
                                    <span class="title">Packages</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('ACLS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/acls')===false)) active @endif">
                                <a href="/admin/acls" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">ACLs</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('MANIFESTS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/manifests')===false)) active @endif">
                                <a href="/admin/manifests" class="nav-link nav-toggle">
                                    <i class="icon-envelope"></i>
                                    <span class="title">Manifest Emails</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('CONTACTS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/contacts')===false)) active @endif">
                                <a href="/admin/contacts" class="nav-link nav-toggle">
                                    <i class="icon-feed"></i>
                                    <span class="title">Contact Logs</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('PURCHASES', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/purchases')===false)) active @endif">
                                <a href="/admin/purchases" class="nav-link nav-toggle">
                                    <i class="icon-basket"></i>
                                    <span class="title">Purchases</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('SLIDERS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/sliders')===false)) active @endif">
                                <a href="/admin/sliders" class="nav-link nav-toggle">
                                    <i class="icon-camera"></i>
                                    <span class="title">Home Sliders</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('TICKETS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/consignments')===false)) active @endif">
                                <a href="/admin/consignments" class="nav-link nav-toggle">
                                    <i class="icon-tag"></i>
                                    <span class="title">Consignment Tickets</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type->id == 1 || array_key_exists('APPS', Auth::user()->user_type->getACLs()['acl_codes']))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/apps')===false)) active @endif">
                                <a href="/admin/apps" class="nav-link nav-toggle">
                                    <i class="icon-calendar"></i>
                                    <span class="title">Apps</span>
                                    <span class="selected"></span>
                                    <span class="arrow open"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item active">
                                        <a href="/admin/apps/deals" class="nav-link">
                                            <i class="icon-bar-chart"></i>
                                            <span class="title">Deals</span>
                                        </a>
                                    </li>
                                </ul>
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
                            <!-- BEGIN MEDIA PICTURE UPLOAD MODAL--> 
                            <div id="modal_media_picture_load" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog" style="width:1400px !important;">
                                    <div class="modal-content portlet">
                                        <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title bold uppercase"><center>LOAD MEDIA</center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    <form id="form_media_picture_load" action="" method="post" enctype="multipart/form-data">
                                                        <input name="tmp" type="hidden" value="1"/>
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <center><span class="btn btn-lg green fileinput-button"><i class="fa fa-plus"></i><span> Add </span>
                                                                    <input type="file" name="image" id="file_media_picture_upload" accept="image/*"> 
                                                                </span></center><hr>
                                                                <button type="button" id="btn_reset_image" class="btn btn-lg sbold dark btn-outline"> Reset </button>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-green-hoki">
                                                                        <div class="mt-head-desc" id="input_media_picture_name"> 
                                                                            - No image yet -
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="pic_size" style="border-style:none;width:50px !important" readonly="true"/>
                                                                                </span>SIZE (kB)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="pic_width" style="border-style:none;width:50px !important" readonly="true"/>
                                                                                </span>WIDTH (px)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="pic_height" style="border-style:none;width:50px !important" readonly="true"/>
                                                                                </span>HEIGHT (px)</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-red">
                                                                        <div class="mt-head-desc">
                                                                            <input type="radio" name="action" value="crop"checked="true"><label for="crop"> Crop Image</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="crop_width" style="border-style:none;width:40px !important" readonly="true"/>
                                                                                </span>WIDTH (px)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="crop_height" style="border-style:none;width:40px !important" readonly="true"/>
                                                                                </span>HEIGHT (px)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="crop_x" style="border-style:none;width:40px !important" readonly="true"/>
                                                                                </span>X (Coord)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="crop_y" style="border-style:none;width:40px !important" readonly="true"/>
                                                                                </span>Y (Coord)</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mt-widget-3">
                                                                    <div class="mt-head bg-yellow">
                                                                        <div class="mt-head-desc"> 
                                                                            <input type="radio" name="action" value="resize"><label for="resize"> Resize Image</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-body-actions-icons">
                                                                        <div class="btn-group btn-group btn-group-justified">
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="resize_width" style="border-style:none;width:60px !important" readonly="true"/>
                                                                                </span>WIDTH (px)</a>
                                                                            <a class="btn ">
                                                                                <span class="mt-icon">
                                                                                    <input name="resize_height" style="border-style:none;width:60px !important" readonly="true"/>
                                                                                </span>HEIGHT (px)</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <button type="button" id="btn_upload_image" class="btn btn-lg blue">Submit</button><hr>
                                                                <button type="button" id="btn_close_image" class="btn btn-lg sbold dark btn-outline">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>    
                                                    <div class="fileinput-preview thumbnail" style="width:1350px; height: 800px; line-height: 372px;" id="image_preview"></div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END MEDIA PICTURE UPLOAD MODAL--> 
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
        <script src="/themes/admin/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jcrop/js/jquery.color.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jcrop/js/jquery.Jcrop.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <!-- SCRIPT FOR SWEET ALERT -->
        <script src="/themes/admin/assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
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
        <!-- SCRIPT FOR UPLOAD IMAGE FILE -->
        <script src="/js/utils/index.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <script src="/themes/admin/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

        <script src="/themes/admin/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="/themes/admin/assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
        @yield('scripts')
    </body>

</html>