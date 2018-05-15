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
        <meta http-equiv="Content-Security-Policy" content="default-src 'self';
                          img-src 'self' blob: {{env('IMAGE_URL_AMAZON_SERVER')}} https://d3ofbylanic3d6.cloudfront.net https://s3-us-west-2.amazonaws.com;
                          frame-src 'self' https://www.youtube.com https://vimeo.com https://player.vimeo.com;
                          style-src 'self' https://fonts.googleapis.com  'unsafe-inline';
                          font-src 'self' http://fonts.gstatic.com;
                          worker-src 'none';">
        <title>{{ config('app.name', 'TicketBat Admin') }} - @yield('title')</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="{{ config('app.name', 'TicketBat.com') }}" name="author" />
        <meta content="{{config('app.theme')}}img/no-image.jpg" name="broken-image" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{config('app.theme')}}css/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/jquery.fileupload.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{config('app.theme')}}css/components.min.css" rel="stylesheet"type="text/css" />
        <link href="{{config('app.theme')}}css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{config('app.theme')}}css/layout.min.css" rel="stylesheet" type="text/css" />

        <link href="{{config('app.theme')}}css/style.css" rel="stylesheet" type="text/css" />

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="{{ asset('/themes/img/favicon.ico') }}" />
        <link rel="apple-touch-icon" href="{{ asset('/themes/img/favicon.ico') }}" />
        <link rel="apple-touch-icon-precomposed" href="{{ asset('/themes/img/favicon.ico') }}" />
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
                            <img src="{{config('app.theme')}}img/logo.png" alt="logo" class="logo-default" /> </a>
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
                            <li title="Edit my profile" style="font-weight:bold">
                                <a data-toggle="modal" href="#modal_model_update_profile">
                                    <i class="icon-user"></i>
                                    <span class="username-hide-on-mobile"> {{Auth::user()->first_name}} {{Auth::user()->last_name}}</span>
                                </a>
                            </li>
                            <li title="Go to the www.ticketbat.com" style="font-weight:bold">
                                <a href="{{route('index')}}">
                                    <i class="icon-settings"></i>
                                    <span class="username-hide-on-mobile"> Production</span>
                                </a>
                            </li>
                            <li title="Exit your session" style="font-weight:bold">
                                <a href="{{route('logout')}}">
                                    <i class="icon-logout"></i>
                                    <span class="username-hide-on-mobile"> Logout</span>
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
                    <div class="page-sidebar navbar-collapse collapse">
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            @if(array_key_exists('RESTAURANTS', Auth::user()->user_type->getACLs()))
                            <li class="sidebar-toggler-wrapper text-center uppercase bold" style="color:white">Production<br>&nbsp;</li>
                            @endif
                            @if(array_key_exists('REPORTS', Auth::user()->user_type->getACLs()))
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
                                        <a href="/admin/dashboard/coupons" class="nav-link">
                                            <i class="icon-wallet"></i>
                                            <span class="title">Coupons</span>

                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/future_liabilities" class="nav-link">
                                            <i class="icon-graph"></i>
                                            <span class="title">Future Liabilities</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start open">
                                        <a href="/admin/dashboard/channels" class="nav-link">
                                            <i class="icon-layers"></i>
                                            <span class="title">Channels</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            @if(array_key_exists('USERS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/users')===false)) active @endif">
                                <a href="/admin/users" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Users</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('BANDS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/bands')===false)) active @endif">
                                <a href="/admin/bands" class="nav-link nav-toggle">
                                    <i class="icon-music-tone"></i>
                                    <span class="title">Bands</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('VENUES', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/venues')===false)) active @endif">
                                <a href="/admin/venues" class="nav-link nav-toggle">
                                    <i class="icon-pointer"></i>
                                    <span class="title">Venues</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('SHOWS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/shows')===false)) active @endif">
                                <a href="/admin/shows" class="nav-link nav-toggle">
                                    <i class="icon-microphone"></i>
                                    <span class="title">Shows</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('TYPES', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/ticket_types')===false)) active @endif">
                                <a href="/admin/ticket_types" class="nav-link nav-toggle">
                                    <i class="icon-layers"></i>
                                    <span class="title">Ticket Types</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('CATEGORIES', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/categories')===false)) active @endif">
                                <a href="/admin/categories" class="nav-link nav-toggle">
                                    <i class="icon-trophy"></i>
                                    <span class="title">Categories</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('COUPONS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/coupons')===false)) active @endif">
                                <a href="/admin/coupons" class="nav-link nav-toggle">
                                    <i class="icon-wallet"></i>
                                    <span class="title">Coupons</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('PACKAGES', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/packages')===false)) active @endif">
                                <a href="/admin/packages" class="nav-link nav-toggle">
                                    <i class="icon-support"></i>
                                    <span class="title">Packages</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('MANIFESTS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/manifests')===false)) active @endif">
                                <a href="/admin/manifests" class="nav-link nav-toggle">
                                    <i class="icon-envelope"></i>
                                    <span class="title">Manifest Emails</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('CONTACTS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/contacts')===false)) active @endif">
                                <a href="/admin/contacts" class="nav-link nav-toggle">
                                    <i class="icon-feed"></i>
                                    <span class="title">Contact Logs</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('PURCHASES', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/purchases')===false)) active @endif">
                                <a href="/admin/purchases" class="nav-link nav-toggle">
                                    <i class="icon-basket"></i>
                                    <span class="title">Purchases</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('REFUNDS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/refunds')===false)) active @endif">
                                <a href="/admin/refunds" class="nav-link nav-toggle">
                                    <i class="icon-credit-card"></i>
                                    <span class="title">Refunds</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('SLIDERS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/sliders')===false)) active @endif">
                                <a href="/admin/sliders" class="nav-link nav-toggle">
                                    <i class="icon-camera"></i>
                                    <span class="title">Home Sliders</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('CONSIGNMENTS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/consignments')===false)) active @endif">
                                <a href="/admin/consignments" class="nav-link nav-toggle">
                                    <i class="icon-tag"></i>
                                    <span class="title">Consignments</span>
                                </a>
                            </li>
                            @endif
                            @if(array_key_exists('ACLS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/acls')===false)) active @endif">
                                <a href="/admin/acls" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">ACLs</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                        @if(array_key_exists('RESTAURANTS', Auth::user()->user_type->getACLs()))
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <li class="sidebar-toggler-wrapper text-center uppercase bold" style="color:white">Other Sites<br>&nbsp;</li>
                            @if(array_key_exists('RESTAURANTS', Auth::user()->user_type->getACLs()))
                            <li class="nav-item @if(!(strpos(url()->current(),'/admin/sites/restaurants')===false)) active @endif">
                                <a href="/admin/restaurants" class="nav-link nav-toggle">
                                    <i class="icon-cup"></i>
                                    <span class="title">Restaurants</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                        @endif                        
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
                                                                            <label class="mt-radio">
                                                                                <input type="radio" name="action" value="crop" checked="true"> Crop Image
                                                                                <span></span>
                                                                            </label>
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
                                                                            <label class="mt-radio">
                                                                                <input type="radio" name="action" value="resize"> Resize Image
                                                                                <span></span>
                                                                            </label>
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
                                                    <div class="fileinput-preview thumbnail" style="width:1360px; height: 810px; line-height: 810px; text-align: center" id="image_preview"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END MEDIA PICTURE UPLOAD MODAL-->
                            <!-- BEGIN UPDATE PROFILE MODAL-->
                            <div id="modal_model_update_profile" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog" style="width:800px !important;">
                                    <div class="modal-content portlet">
                                        <div class="modal-header alert-block bg-dark">
                                            <h4 class="modal-title bold uppercase" style="color:white;"><center>{{Auth::user()->first_name}}'s Profile</center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <!-- BEGIN FORM-->
                                            <form method="post" id="form_model_update_profile">
                                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">First Name:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="first_name" value="{{Auth::user()->first_name}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Last Name:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="last_name" value="{{Auth::user()->last_name}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Email:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="email" value="{{Auth::user()->email}}" readonly="true" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Phone:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="phone" value="{{Auth::user()->phone}}" required="false"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Password:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" type="password" name="password" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Address:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="address" value="{{Auth::user()->location->address}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">City:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="city" value="{{Auth::user()->location->city}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">State:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="state" value="{{Auth::user()->location->state}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Zip:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="zip" value="{{Auth::user()->location->zip}}" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Country:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-8 show-error">
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="country" value="{{Auth::user()->location->country}}" readonly="true" required="true"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <div class="row">
                                                        <div class="modal-footer">
                                                            <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                                            <button type="button" class="btn sbold sbold dark btn-outline" id="submit_model_update_profile">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- END FORM-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END UPDATE PROFILE MODAL-->
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
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{config('app.theme')}}js/jquery.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{config('app.theme')}}js/moment-2.21.0.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/daterangepicker.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/fullcalendar.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.Jcrop.min.js" type="text/javascript"></script>
        <!-- SCRIPT FOR SWEET ALERT -->
        <script src="{{config('app.theme')}}js/sweetalert.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{config('app.theme')}}js/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/additional-methods.min.js" type="text/javascript"></script>
        <!-- SCRIPT FOR VALIDATING ALL FORMS -->
        <script src="/js/utils/templates.js" type="text/javascript"></script>
        <!-- SCRIPT FOR UPLOAD IMAGE FILE -->
        <script src="/js/utils/index.js" type="text/javascript"></script>
        @yield('scripts')
    </body>

</html>
