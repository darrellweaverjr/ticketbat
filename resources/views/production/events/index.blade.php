@php $page_title=$event->name.' at '.$event->venue @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')


<!-- BEGIN QUICK NAV -->
<!--        <nav class="quick-nav">
            <a class="quick-nav-trigger" href="#0x">
                <span aria-hidden="true"></span>
            </a>
            <ul>
                <li>
                    <a href="https://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes" target="_blank" class="active">
                        <span>Purchase Metronic</span>
                        <i class="icon-basket"></i>
                    </a>
                </li>
                <li>
                    <a href="https://themeforest.net/item/metronic-responsive-admin-dashboard-template/reviews/4021469?ref=keenthemes" target="_blank">
                        <span>Customer Reviews</span>
                        <i class="icon-users"></i>
                    </a>
                </li>
                <li>
                    <a href="http://keenthemes.com/showcast/" target="_blank">
                        <span>Showcase</span>
                        <i class="icon-user"></i>
                    </a>
                </li>
                <li>
                    <a href="http://keenthemes.com/metronic-theme/changelog/" target="_blank">
                        <span>Changelog</span>
                        <i class="icon-graph"></i>
                    </a>
                </li>
            </ul>
            <span aria-hidden="true" class="quick-nav-bg"></span>
        </nav>
        <div id="0x" class="quick-nav-overlay"></div>-->
        <!-- END QUICK NAV -->


<!-- BEGIN TOP HEADER -->
<div class="top_header" >
    <div class="carousel-inner">
        <div class="item active" >
            <img style="margin:auto" src="{{$event->header->url}}" alt="{{$event->header->caption}}">
        </div>
    </div>
</div>
<!-- END TOP HEADER -->
<!-- BEGIN NAME BAR-->
<div class="row widget-row">
    <div class="widget-thumb widget-bg-color-white text-uppercase" title="Name of the event">                
        <div class="widget-thumb-wrap text-center uppercase" style="font-size:44px">{{$event->name}}
            <p style="margin-top:-25px">
                @if(!empty($event->twitter)) <a class="social-icon social-icon-color twitter" href="https://twitter.com/{{$event->twitter}}" target="_blank"></a> @endif
                @if(!empty($event->googleplus)) <a class="social-icon social-icon-color googleplus" href="https://plus.google.com/{{$event->googleplus}}" target="_blank"></a> @endif
                @if(!empty($event->facebook)) <a class="social-icon social-icon-color facebook" href="http://www.facebook.com/{{$event->facebook}}" target="_blank"></a> @endif
                @if(!empty($event->yelpbadge)) <a class="social-icon social-icon-color yahoo " href="http://yelp.com/biz/{{$event->yelpbadge}}" target="_blank"></a> @endif
                @if(!empty($event->instagram)) <a class="social-icon social-icon-color instagram" href="http://instagram.com/{{$event->instagram}}" target="_blank"></a> @endif
                @if(!empty($event->youtube)) <a class="social-icon social-icon-color youtube" href="http://www.youtube.com/user/{{$event->youtube}}" target="_blank"></a> @endif
            </p>
        </div>
    </div>
</div>
<!-- END NAME BAR-->
<div class="page-content color-panel">  
    <!-- BEGIN TEXT -->
    <div class="row margin-bottom-40 fixed-panel">
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4>
                    <i class="fa fa-check icon-info"></i> Event details
                    <div class="actions pull-right">
                        <div class="btn-group">
                            <a data-toggle="dropdown"><i class="fa fa-share icon-share"></i></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="https://twitter.com/intent/tweet?text={{$event->name}} {{url()->current()}}" target="_blank">
                                        <i class="social-icon social-icon-color twitter"></i> Twitter
                                    </a></li>
                                <li><a href="https://plus.google.com/share?url={{url()->current()}}" target="_blank">
                                        <i class="social-icon social-icon-color googleplus"></i> Google+
                                    </a></li>
                                <li><a href="http://www.facebook.com/sharer/sharer.php?u={{url()->current()}}" target="_blank">
                                        <i class="social-icon social-icon-color facebook"></i> Facebook
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </h4>  
                <p class="margin-top-20">{!! $event->description !!}</p>
                <!-- END DESCRIPTION -->
                <!-- BEGIN BANDS -->
                <div class="timeline" style="margin:5px">
                    @foreach($event->bands as $b)
                    <!-- BAND ITEM -->
                    <div class="timeline-item">
                        <div class="timeline-badge">
                            <img class="timeline-badge-userpic" src="{{$b->image_url}}"> </div>
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"> </div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    <a href="javascript:;" class="timeline-body-title font-blue-madison">{{$b->name}}</a>
                                    <span class="timeline-body-time font-grey-cascade">({{$b->category}})</span>
                                </div>
                                <div class="timeline-body-head-actions">
                                    <div class="btn-group">
                                        @if(!empty($b->website)) <a class="social-icon social-icon-color rss" href="{{$b->website}}" target="_blank"></a> @endif
                                        @if(!empty($b->twitter)) <a class="social-icon social-icon-color twitter" href="{{$b->twitter}}" target="_blank"></a> @endif
                                        @if(!empty($b->my_space)) <a class="social-icon social-icon-color myspace" href="{{$b->my_space}}" target="_blank"></a> @endif
                                        @if(!empty($b->facebook)) <a class="social-icon social-icon-color facebook" href="{{$b->facebook}}" target="_blank"></a> @endif
                                        @if(!empty($b->flickr)) <a class="social-icon social-icon-color flickr" href="{{$b->flickr}}" target="_blank"></a> @endif
                                        @if(!empty($b->instagram)) <a class="social-icon social-icon-color instagram" href="{{$b->instagram}}" target="_blank"></a> @endif
                                        @if(!empty($b->youtube)) <a class="social-icon social-icon-color youtube" href="{{$b->youtube}}" target="_blank"></a> @endif
                                        @if(!empty($b->soundcloud)) <a class="social-icon social-icon-color jolicloud" href="{{$b->soundcloud}}" target="_blank"></a> @endif
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-body-content">
                                <span class="font-grey-cascade">{!! $b->short_description !!}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END BAND ITEM -->
                    @endforeach
                </div>
                <!-- ENDS BANDS -->
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4>
                    <i class="fa fa-check icon-calendar"></i> Show times
                    <div class="actions pull-right">
                        <div class="btn-group">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#showtimes_list" data-toggle="tab"><i class="fa fa-list icon-list"></i></a>
                                </li>
                                <li>
                                    <a href="#showtimes_calendar" data-times="{{json_encode($event->showtimes)}}" data-toggle="tab"><i class="fa fa-calendar icon-calendar"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </h4> 
                @if($event->restrictions!="None")
                <div class="margin-top-20 alert alert-danger" style="margin:5px">
                    <b>RESTRICTIONS:</b> {{preg_replace('~\D~','',$event->restrictions)}} years of age or older to attend the event.
                </div>
                @endif
                    <div class="tab-content">
                        <!-- SHOW TIMES AS LIST -->
                        <div class="tab-pane active" id="showtimes_list" >   
                            <div class="portlet-body light portlet-fit" style="margin-top:-30px;">
                                <table class="table table-hover table-responsive" id="tb_model">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->showtimes as $index=>$st)
                                        <tr>
                                            <td>{{$st->show_day}}</td>
                                            <td>{{$st->show_date}}</td>
                                            <td>{{$st->show_hour}}</td>
                                            <td><center><a class="btn btn-outline btn-success">BUY TICKETS <i class="fa fa-arrow-circle-right"></i></a></center></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- SHOW TIMES AS CALENDAR -->
                        <div class="tab-pane" id="showtimes_calendar">
                            <div class="portlet-body light portlet-fit calendar" >
                                <div id="cal_model" class="has-toolbar"> </div>
                            </div>
                        </div>
                    </div>                               
                
            </div>
        </div>
    </div>
    <!-- END TEXT -->
    
    
    
    
    <!-- BEGIN TEXT -->
    <div class="row margin-bottom-40 fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-check icon-calendar"></i> Bands
            </h4>  
            
            <!-- END DESCRIPTION -->
            <!-- BEGIN BANDS -->
            
            <!-- ENDS BANDS -->
        </div>
    </div>
    <!-- END TEXT -->
    
    
    
    
    <!-- BEGIN BODY GRID-->
    <div class="portfolio-content body_grid text-center">        
        <div id="myEvent" class="cbp text-center">
            
            <div class="cbp-item event_section">
                <div class="cbp-caption">
                    <div class="cbp-caption-defaultWrap">
                        <h1>Event Details:</h1><hr>
                        <div class="modal-social-btns">
                            <a class="social-icon social-icon-color twitter" href="https://twitter.com/intent/tweet?text={{$event->name}} {{url()->current()}}" target="_blank"></a>
                            <a class="social-icon social-icon-color googleplus" href="https://plus.google.com/share?url={{url()->current()}}" target="_blank"></a>
                            <a class="social-icon social-icon-color facebook" href="http://www.facebook.com/sharer/sharer.php?u={{url()->current()}}" target="_blank"></a>
                        </div>
                    </div>
                    <div class="cbp-caption-activeWrap">
                        <div class="cbp-l-caption-alignCenter">
                            <div class="cbp-l-caption-body">
                                <span class="cbp-l-caption-buttonLeft btn green">aaaaaaaaaaaaaaaaa</span>
                                <span class="cbp-l-caption-buttonLeft btn red">Next on <b>{{date('m/d/y')}}</b></span>
                                <a href="" class="cbp-lightbox cbp-l-caption-buttonRight btn yellow" data-title="aaaaaaa<br>aaaaaaa"><i class="icon-size-fullscreen"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cbp-item event_section">
                <div class="cbp-caption">
                    <div class="cbp-caption-defaultWrap">
                        <img src="http://ticketbat.s3-website-us-west-2.amazonaws.com/images/mjlive-header.jpg1" alt="No Image Preview"> </div>
                    <div class="cbp-caption-activeWrap">
                        <div class="cbp-l-caption-alignCenter">
                            <div class="cbp-l-caption-body">
                                <span class="cbp-l-caption-buttonLeft btn green">aaaaaaaaaaaaaaaaa</span>
                                <span class="cbp-l-caption-buttonLeft btn red">Next on <b>{{date('m/d/y')}}</b></span>
                                <a href="" class="cbp-lightbox cbp-l-caption-buttonRight btn yellow" data-title="aaaaaaa<br>aaaaaaa"><i class="icon-size-fullscreen"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cbp-item event_section">
                <div class="cbp-caption">
                    <div class="cbp-caption-defaultWrap">
                        <img src="http://ticketbat.s3-website-us-west-2.amazonaws.com/images/mjlive-header.jpg1" alt="No Image Preview"> </div>
                    <div class="cbp-caption-activeWrap">
                        <div class="cbp-l-caption-alignCenter">
                            <div class="cbp-l-caption-body">
                                <span class="cbp-l-caption-buttonLeft btn green">aaaaaaaaaaaaaaaaa</span>
                                <span class="cbp-l-caption-buttonLeft btn red">Next on <b>{{date('m/d/y')}}</b></span>
                                <a href="" class="cbp-lightbox cbp-l-caption-buttonRight btn yellow" data-title="aaaaaaa<br>aaaaaaa"><i class="icon-size-fullscreen"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cbp-item event_section">
                <div class="cbp-caption">
                    <div class="cbp-caption-defaultWrap">
                        <img src="http://ticketbat.s3-website-us-west-2.amazonaws.com/images/mjlive-header.jpg1" alt="No Image Preview"> </div>
                    <div class="cbp-caption-activeWrap">
                        <div class="cbp-l-caption-alignCenter">
                            <div class="cbp-l-caption-body">
                                <span class="cbp-l-caption-buttonLeft btn green">aaaaaaaaaaaaaaaaa</span>
                                <span class="cbp-l-caption-buttonLeft btn red">Next on <b>{{date('m/d/y')}}</b></span>
                                <a href="" class="cbp-lightbox cbp-l-caption-buttonRight btn yellow" data-title="aaaaaaa<br>aaaaaaa"><i class="icon-size-fullscreen"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>    
    <!-- END BODY GRID-->
</div>
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/fullcalendar.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/js/production/events/index.js" type="text/javascript"></script>
@endsection