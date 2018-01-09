@php $page_title=$venue->name @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/gmap.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<!-- BEGIN TOP HEADER -->
<div class="row">
    <center><img style="margin:auto; width:100%;" src="{{$venue->header->url}}" alt="{{$venue->header->caption}}"></center>
</div>
<!-- END TOP HEADER -->
<!-- BEGIN NAME BAR-->
<div class="row widget-row">
    <div class="widget-thumb widget-bg-color-white text-uppercase" title="Name of the venue">                
        <div class="widget-thumb-wrap text-center uppercase" style="font-size:44px">{{$venue->name}}
            <p style="margin-top:-25px;max-height:30px">
                @if(!empty($venue->twitter)) <a class="social-icon social-icon-color twitter" href="https://twitter.com/{{$venue->twitter}}" target="_blank"></a> @endif
                @if(!empty($venue->googleplus)) <a class="social-icon social-icon-color googleplus" href="https://plus.google.com/{{$venue->googleplus}}" target="_blank"></a> @endif
                @if(!empty($venue->facebook)) <a class="social-icon social-icon-color facebook" href="http://www.facebook.com/{{$venue->facebook}}" target="_blank"></a> @endif
                @if(!empty($venue->yelpbadge)) <a class="social-icon social-icon-color yahoo " href="http://yelp.com/biz/{{$venue->yelpbadge}}" target="_blank"></a> @endif
                @if(!empty($venue->instagram)) <a class="social-icon social-icon-color instagram" href="http://instagram.com/{{$venue->instagram}}" target="_blank"></a> @endif
                @if(!empty($venue->youtube)) <a class="social-icon social-icon-color youtube" href="http://www.youtube.com/user/{{$venue->youtube}}" target="_blank"></a> @endif
            </p>
        </div>
    </div>
</div>
<!-- END NAME BAR-->
<div class="page-content color-panel">  
    <!-- BEGIN DESCRIPTION AND CALENDAR -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-check icon-info"></i> Venue Details
                <div class="actions pull-right">
                    <div class="btn-group">
                        <a data-toggle="dropdown"><i class="fa fa-share icon-share"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="https://twitter.com/intent/tweet?text={{$venue->name}} {{url()->current()}}" target="_blank">
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
            <p class="margin-top-20" style="padding-bottom:20px">{!! $venue->description !!}</p>
            <!-- END DESCRIPTION -->
        </div>
    </div>
    @if(count($venue->events))
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-globe icon-globe"></i> Events
            </h4> 
            <!-- BEGIN EVENTS -->
            <div class="timeline" style="margin:10px;padding-bottom:10px;">
                @foreach($venue->events as $e)
                <!-- EVENT ITEM -->
                <div class="timeline-item">
                    <div class="timeline-badge">
                        <a href="/event/{{$e->slug}}">
                            <img height="100px" width="150px" src="{{$e->url}}">
                        </a>
                    </div>
                    <div class="timeline-body" style="margin-left:170px">
                        <div class="timeline-body-arrow"> </div>
                        <div class="timeline-body-head">
                            <div class="timeline-body-head-caption">
                                <a href="/event/{{$e->slug}}" class="timeline-body-title font-blue-madison">{{$e->name}}</a>
                                <span class="timeline-body-time font-grey-cascade">({{$e->category}})</span>
                            </div>
                            <div class="timeline-body-head-actions">
                                <div class="btn-group">
                                    @if(!empty($e->website)) <a class="social-icon social-icon-color rss" href="{{$e->website}}" target="_blank"></a> @endif
                                    @if(!empty($e->twitter)) <a class="social-icon social-icon-color twitter" href="{{$e->twitter}}" target="_blank"></a> @endif
                                    @if(!empty($e->my_space)) <a class="social-icon social-icon-color myspace" href="{{$e->my_space}}" target="_blank"></a> @endif
                                    @if(!empty($e->facebook)) <a class="social-icon social-icon-color facebook" href="{{$e->facebook}}" target="_blank"></a> @endif
                                    @if(!empty($e->flickr)) <a class="social-icon social-icon-color flickr" href="{{$e->flickr}}" target="_blank"></a> @endif
                                    @if(!empty($e->instagram)) <a class="social-icon social-icon-color instagram" href="{{$e->instagram}}" target="_blank"></a> @endif
                                    @if(!empty($e->youtube)) <a class="social-icon social-icon-color youtube" href="{{$e->youtube}}" target="_blank"></a> @endif
                                    @if(!empty($e->soundcloud)) <a class="social-icon social-icon-color jolicloud" href="{{$e->soundcloud}}" target="_blank"></a> @endif
                                </div>
                            </div>
                        </div>
                        <div class="timeline-body-content">
                            <span class="font-grey-cascade">{!! $e->description !!}</span><hr>
                            <span class="col-md-4"><i class="fa fa-calendar icon-calendar"></i> {{date('F j, Y @ h:i A', strtotime($e->show_time))}}</span>
                            <span class="col-md-4"><i class="fa fa-ticket icon-tag"></i>@if($e->price>0) Starting at ${{$e->price}} @else <b class="label label-sm sbold label-success">FREE</b> @endif</span>
                            <span class="col-md-4"><a href="/event/{{$e->slug}}" style="color:white!important" class="btn bg-blue">SEE TICKETS <i class="fa fa-arrow-circle-right"></i></a></span>
                        </div>
                    </div>
                </div>
                <!-- END EVENT ITEM -->
                @endforeach
            </div>
            <!-- ENDS EVENTS -->
        </div>
    </div>
    @endif
    <!-- END DESCRIPTION AND CALENDAR -->
    <!-- BEGIN MAPS -->
    <div class="c-content-contact-1 c-opt-1">
        <div class="row" data-auto-height=".c-height">
            <div class="col-lg-9 col-md-6 c-desktop"></div>
            <div class="col-lg-3 col-md-6">
                <div class="c-body">
                    <div class="c-section">
                        <h3>{{$venue->name}}</h3>
                    </div>
                    <div class="c-section">
                        <div class="c-content-label uppercase bg-blue">Address</div>
                        <p>{{$venue->address}}
                            <br/>{{$venue->city}}, {{$venue->state}}
                            <br/>{{$venue->country}} {{$venue->zip}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="event_gmap" class="gmaps c-content-contact-1-gmap" title="Location of the venue." style="height: 450px;"
                 data-lat="{{$venue->lat}}"
                 data-lng="{{$venue->lng}}"
                 data-address="{{$venue->address}}<br>{{$venue->city}}, {{$venue->state}}, {{$venue->country}} {{$venue->zip}}"
                 data-venue="{{$venue->name}}"
                 ></div>
    </div>
    <!-- END MAPS -->
    
</div>
@endsection

@section('scripts')
<script src="https://maps.google.com/maps/api/js?key=AIzaSyC7sODsH3uUz_lBbYH16eOCJU9igquCjzI" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/gmaps.min.js" type="text/javascript"></script>
<script src="/js/production/venues/view.js" type="text/javascript"></script>
@endsection