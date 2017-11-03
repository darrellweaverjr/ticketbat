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
<link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<!-- BEGIN TOP HEADER -->
<div class="row">
    <center><img style="margin:auto;max-height:422px" src="{{$event->header->url}}" alt="{{$event->header->caption}}"></center>
</div>
<!-- END TOP HEADER -->
<!-- BEGIN NAME BAR-->
<div class="row widget-row">
    <div class="widget-thumb widget-bg-color-white text-uppercase" title="Name of the event">                
        <div class="widget-thumb-wrap text-center uppercase" style="font-size:44px">
            @if(!empty($event->presented_by))<div style="font-size:14px;margin-bottom:-70px">{{$event->presented_by}} PRESENTS</div><br>@endif
            {{$event->name}}
            <p style="margin-top:-25px;max-height:30px">
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
    <!-- BEGIN BANNERS -->
    @if(count($event->banners))
    <div class="row fixed-panel">
        <div class="portlet light about-text"style="margin-left:15px;margin-right:15px">
            <!-- BEGIN BANNER -->
            <div class="portfolio-content color-panel" > 
                <div id="myBanners" class="cbp text-center" data-broken="{{config('app.theme')}}img/no-image.jpg">
                    @foreach($event->banners as $index=>$i)
                    <div class="cbp-item show_section1">
                        <div class="cbp-caption">
                            <div class="cbp-caption-defaultWrap">
                                <a href="{{$i->url}}" target="_blank"><img src="{{$i->file}}" alt="{{$i->url}}"></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- ENDS BANNER -->
        </div>
    </div>
    @endif
    <!-- END BANNERS -->      
    <!-- BEGIN DESCRIPTION AND CALENDAR -->
    <div class="row fixed-panel">
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4 title="Click on the time to view event's details.">
                    <i class="fa fa-check icon-info"></i> Event Details
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
                <p class="margin-top-20" title="Description of the event.">
                    {!! $event->description !!}<br><br>
                </p>
                <!-- END DESCRIPTION -->
                @if(count($event->bands))
                <!-- BEGIN BANDS -->
                <div class="timeline" style="margin:5px;padding-bottom:10px" title="Bands to assist to.">
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
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4 title="Click on the time to view ticket's details.">
                    <i class="fa fa-check icon-calendar"></i> Show Times
                    @if(count($event->showtimes))
                    <div class="actions pull-right">
                        <div class="btn-group">
                            <ul class="nav nav-tabs">
                                <li @if(count($event->showtimes)<8) class="active" @endif>
                                     <a href="#showtimes_list" class="btn-info" data-toggle="tab"><i class="fa fa-list icon-list"></i></a>
                                </li>
                                <li @if(count($event->showtimes)>7) class="active" @endif>
                                    <a href="#showtimes_calendar" class="btn-info" data-toggle="tab"><i class="fa fa-calendar icon-calendar"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif
                </h4> 
                @if($event->restrictions!="None")
                <div class="margin-top-20 alert alert-danger" style="margin:5px" title="Restrictions of the event.">
                    <b>RESTRICTIONS:</b> {{preg_replace('~\D~','',$event->restrictions)}} years of age or older to attend the event.
                </div>
                @endif
                @if(count($event->showtimes))
                <div class="tab-content">
                    <!-- SHOW TIMES AS LIST -->
                    <div class="tab-pane @if(count($event->showtimes)<8) active @endif" id="showtimes_list" style="margin:5px;padding-bottom:10px">   
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
                                    @foreach($event->showtimes as $st)
                                    <tr>
                                        <td>{{$st->show_day}}</td>
                                        <td>{{$st->show_date}}</td>
                                        <td>{{$st->show_hour}}</td>
                                        <td><center><a @if($st->ext_slug) href="{{$st->ext_slug}}" @else href="{{url()->current()}}/{{$st->id}}" @endif style="color:white!important" class="btn bg-blue">
                                            BUY TICKETS <i class="fa fa-arrow-circle-right"></i>
                                        </a></center></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- SHOW TIMES AS CALENDAR -->
                    <div class="tab-pane @if(count($event->showtimes)>7) active @endif" id="showtimes_calendar" style="margin:5px;padding-bottom:10px">
                        <div class="portlet-body light portlet-fit calendar">
                            <div id="cal_model" class="has-toolbar" data-info='{!! $event->showtimes !!}' data-slug="{{url()->current()}}"> </div>
                        </div>
                    </div>
                </div>  
                @else
                 <!-- BEGIN CUTOFF TEXT -->
                <p class="uppercase text-center" style="padding-bottom:20px">
                    {!! $event->cutoff_text !!}
                </p>
                <!-- END CUTOFF TEXT -->
                @endif
                <!-- BEGIN PRESENTED BY -->
                <p class="uppercase text-center" style="margin-top: 30px">
                    <div class="col-md-3">
                        @if(!empty($event->sponsor_logo_id))<img class="timeline-badge-userpic" src="{{$event->sponsor_logo_id}}">@endif
                    </div>
                    <div class="col-md-9">
                        @if(!empty($event->presented_by))<label class="control-label">PRESENTED BY: <b>{{$event->presented_by}}</b></label><br>@endif
                        @if(!empty($event->sponsor))<label class="control-label">SPONSOR: <b>{{$event->sponsor}}</b></label>@endif
                    </div>
                </p>
                <!-- END PRESENTED BY -->
            </div>
        </div>
    </div>
    <!-- END DESCRIPTION AND CALENDAR -->
    <!-- BEGIN VIDEOS -->
    @if(count($event->videos))
    <div class="row fixed-panel" style="padding:15px" title="Videos of the event.">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-youtube-play"></i> Videos
            </h4>  
            @foreach($event->videos as $v)
            <p class="text-center">
                <iframe src="{!!$v->embed_code!!}" width="100%" height="600px" frameborder="0"></iframe>
                {!!$v->description!!}<hr>
            </p>
            @endforeach
        </div>
    </div>
    @endif
    <!-- END VIDEOS -->
    <!-- BEGIN GALLERY -->
    @if(count($event->images))
    <div class="row fixed-panel" style="padding:15px;margin-top:-20px" title="Images of the event.">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-image"></i> Gallery
            </h4>  
            <!-- END DESCRIPTION -->
            <!-- BEGIN GALLERY -->
            <div class="portfolio-content color-panel" style="padding:20px;background-color: white;"> 
                <div id="myGallery" class="cbp text-center" data-broken="{{config('app.theme')}}img/no-image.jpg">
                    @foreach($event->images as $index=>$i)
                    <div class="cbp-item show_section1" style="margin-right:20px">
                        <div class="cbp-caption">
                            <div class="cbp-caption-defaultWrap">
                                <img src="{{$i->url}}" alt="{{$i->url}}"> </div>
                            <div class="cbp-caption-activeWrap">
                                <div class="cbp-l-caption-alignCenter">
                                    <div class="cbp-l-caption-body">
                                        <a href="{{$i->url}}" class="cbp-lightbox btn btn-lg yellow" data-title="Images of {{$event->name}}."><i class="icon-size-fullscreen"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- ENDS GALLERY -->
        </div>
    </div>
    @endif
    <!-- END GALLERY -->    
    <!-- BEGIN MAPS -->
    <div id="event_gmap" class="row gmaps" title="Location of the venue."
                 data-lat="{{$event->lat}}" 
                 data-lng="{{$event->lng}}"
                 data-address="{{$event->address}}<br>{{$event->city}}, {{$event->state}}, {{$event->country}} {{$event->zip}}"
                 data-venue="{{$event->venue}}"
                 ></div>
    <!-- END MAPS -->    
    
</div>
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/fullcalendar.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyC7sODsH3uUz_lBbYH16eOCJU9igquCjzI" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/gmaps.min.js" type="text/javascript"></script>
<script src="/js/production/events/index.js" type="text/javascript"></script>
@endsection