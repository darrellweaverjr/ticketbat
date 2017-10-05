@php $page_title='Event' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')


<!-- BEGIN QUICK NAV -->
        <nav class="quick-nav">
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
        <div id="0x" class="quick-nav-overlay"></div>
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
<div class="page-content">       
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
    <!-- BEGIN TEXT -->
    <div class="row margin-bottom-40">
        <div class="col-lg-6">
            <div class="portlet light about-text">
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
                
                @foreach($event->bands as $b)
                <div class="timeline">
                    <!-- BAND ITEM -->
                    <div class="timeline-item">
                        <div class="timeline-badge">
                            <img class="timeline-badge-userpic" src="http://ticketbat.s3-website-us-west-2.amazonaws.com/bands/TASHAKI600X600.jpg"> </div>
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"> </div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    <a href="javascript:;" class="timeline-body-title font-blue-madison">Lisa Strong</a>
                                    <span class="timeline-body-time font-grey-cascade">Replied at 17:45 PM</span>
                                </div>
                                <div class="timeline-body-head-actions">
                                    <div class="btn-group">
                                        <button class="btn btn-circle green btn-outline btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-body-content">
                                <span class="font-grey-cascade"> this is a description </span>
                            </div>
                        </div>
                    </div>
                    <!-- END BAND ITEM -->
                </div>
                @endforeach
                
                
                <div class="timeline">
                    <!-- BAND ITEM -->
                    <div class="timeline-item">
                        <div class="timeline-badge">
                            <img class="timeline-badge-userpic" src="http://ticketbat.s3-website-us-west-2.amazonaws.com/bands/TASHAKI600X600.jpg"> </div>
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"> </div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    <a href="javascript:;" class="timeline-body-title font-blue-madison">Lisa Strong</a>
                                    <span class="timeline-body-time font-grey-cascade">Replied at 17:45 PM</span>
                                </div>
                                <div class="timeline-body-head-actions">
                                    <div class="btn-group">
                                        @if(!empty($event->twitter)) <a class="social-icon social-icon-color twitter" href="https://twitter.com/{{$event->twitter}}" target="_blank"></a> @endif
                                        @if(!empty($event->googleplus)) <a class="social-icon social-icon-color googleplus" href="https://plus.google.com/{{$event->googleplus}}" target="_blank"></a> @endif
                                        @if(!empty($event->facebook)) <a class="social-icon social-icon-color facebook" href="http://www.facebook.com/{{$event->facebook}}" target="_blank"></a> @endif
                                        @if(!empty($event->yelpbadge)) <a class="social-icon social-icon-color yahoo " href="http://yelp.com/biz/{{$event->yelpbadge}}" target="_blank"></a> @endif
                                        @if(!empty($event->instagram)) <a class="social-icon social-icon-color instagram" href="http://instagram.com/{{$event->instagram}}" target="_blank"></a> @endif
                                        @if(!empty($event->youtube)) <a class="social-icon social-icon-color youtube" href="http://www.youtube.com/user/{{$event->youtube}}" target="_blank"></a> @endif
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-body-content">
                                <span class="font-grey-cascade"> this is a description </span>
                            </div>
                        </div>
                    </div>
                    <!-- END BAND ITEM -->
                </div>
                
                
                
                
                                       
                
            </div>
        </div>
        <div class="col-lg-6">
            <iframe src="http://player.vimeo.com/video/22439234" style="width:100%; height:500px;border:0" allowfullscreen> </iframe>
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
<script src="/js/production/events/event.js" type="text/javascript"></script>
@endsection