@php $page_title=$event->name.' at '.$event->venue @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{config('app.theme')}}css/fullcalendar.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/gmap.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

    <!-- BEGIN TOP HEADER -->
    @if(!empty($event->mobile_header->url))
        <div class="row">
            <img class="event-main-img text-center" src="{{$event->mobile_header->url}}" srcset="{{$event->mobile_header->url}} 500w, {{$event->header->url}} 1350w" alt="{{$event->header->caption}}">
        </div>
    @else
        <div class="row">
            <img class="event-main-img text-center" src="{{$event->header->url}}" alt="{{$event->header->caption}}">
        </div>
    @endif
    <!-- END TOP HEADER -->
    <!-- BEGIN NAME BAR-->
    <div class="row widget-row event-top-row">
        <div class="col-lg-12 ml-15 mr-15">
            <div class="row fixed-panel event-main-title">
                <div class="col-xs-12 col-sm-6 col-sm-push-6">
                    @if(!empty($event->presented_by))<p class="presented-by">{{$event->presented_by}} PRESENTS</p>@endif
                    <p class="event-name">{{$event->name}}</p>
                </div>
                <div class="col-xs-12 col-sm-6 col-sm-pull-6">
                    @if(!empty($event->starting_at))
                        <div class="event-price-wrap @if(!empty($event->presented_by)) extra-wrap-height @endif">
                            <p class="event-low-price"><span class="asterisk">*</span></span><span class="from">FROM</span>&nbsp;<span class="usd">$</span>{{$event->starting_at}}</p>
                            @if(!empty($event->regular_price))
                                <div class="event-regular-price">
                                    <span>${{$event->regular_price}}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END NAME BAR-->
    <div class="page-content color-panel pt-10">
        <!-- BEGIN DESCRIPTION AND CALENDAR -->
        <div class="row fixed-panel">
            <div class="col-xs-12 col-sm-6 col-sm-push-6">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4 title="Click on the time to view ticket's details.">
                        <i class="fa fa-calendar"></i> Show Times
                        @if(count($event->showtimes))
                            <div class="actions pull-right">
                                <div class="btn-group">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#showtimes_list" class="btn-info" data-toggle="tab"><i class="fa fa-list icon-list"></i></a>
                                        </li>
                                        <li>
                                            <a href="#showtimes_calendar" class="btn-info" data-toggle="tab"><i class="fa fa-calendar"></i></a>
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
                            <div class="tab-pane active " id="showtimes_list" style="margin:5px;padding-bottom:10px">
                                <div class="portlet-body light portlet-fit" style="margin-top:-30px;">
                                    <table class="event-table table table-hover table-responsive" id="tb_model">
                                        <thead>
                                        <tr>
                                            <th>Day</th>
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
                                                <td>@if(!empty($st->time_alternative)) {{$st->time_alternative}} @else {{$st->show_hour}} @endif</td>
                                                <td class="text-center">
                                                    <a @if($st->ext_slug) href="{{$st->ext_slug}}" @else href="/buy/{{$event->slug}}/{{$st->id}}" @endif style="color:white!important" class="btn bg-blue">
                                                        BUY TICKETS <i class="fa fa-arrow-circle-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- SHOW TIMES AS CALENDAR -->
                            <div class="tab-pane" id="showtimes_calendar" style="margin:5px;padding-bottom:10px">
                                <div class="portlet-body light portlet-fit calendar">
                                    <div id="cal_model" class="has-toolbar" data-info='{!! $event->showtimes !!}' data-slug="/buy/{{$event->slug}}"></div>
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
                    <div class="col-md-3 uppercase text-center">
                        @if(!empty($event->sponsor_logo_id))<img class="timeline-badge-userpic mt-30" src="{{$event->sponsor_logo_id}}">@endif
                    </div>
                    <div class="col-md-9 uppercase text-center">
                        {{-- WE SHOULD KILL THIS --}}
                        {{--@if(!empty($event->presented_by))<label class="control-label">PRESENTED BY: <b>{{$event->presented_by}}</b></label><br>@endif--}}
                        {{--@if(!empty($event->sponsor))<label class="control-label">SPONSOR: <b>{{$event->sponsor}}</b></label>@endif--}}
                    </div>
                    <!-- END PRESENTED BY -->
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-sm-pull-6">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4 title="Click on the time to view event's details.">
                        <i class="fa fa-info-circle"></i> Event Details
                    </h4>
                    <div style="text-align:center;padding-top:10px">
                        <input id="show_id" type="hidden" value="{{$event->show_id}}"/>
                    </div>
                    <p class="margin-top-20" title="Description of the event.">
                        {!! $event->description !!}<br><br>
                    </p>
                    <!-- END DESCRIPTION -->


                    @if(count($event->bands))
                    <!-- BEGIN BANDS -->
                        <div class="timeline" style="margin:5px;padding-bottom:10px">
                        @foreach($event->bands as $b)
                            <!-- BAND ITEM -->
                                <div class="timeline-item" title="{!! $b->short_description !!}">
                                    <div class="timeline-badge">
                                        <a @if(!empty($b->website)) href="{{$b->website}}" target="_blank" @endif >
                                            <img height="90px" width="90px" src="{{$b->image_url}}">
                                        </a>
                                    </div>
                                    <div class="timeline-body">
                                        <div class="timeline-body-arrow"></div>
                                        <div class="timeline-body-head">
                                            <div class="timeline-body-head-caption">
                                                <a @if(!empty($b->website)) href="{{$b->website}}" target="_blank" @endif class="timeline-body-title font-blue-madison">{{$b->name}}</a>
                                                <span class="timeline-body-time font-grey-cascade">({{$b->category}})</span>
                                            </div>
                                            <div class="timeline-body-head-actions">
                                                <div class="btn-group">
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

                    @if(!empty($event->starting_at))
                        <div class="events-price-disclaimer ml-20 mr-20">
                            <p class="disclaimer">*Price subject to availability, taxes, and fees</p>
                        </div>
                    @endif

                    <div class="event-rating form-inline has-warning ml-20 mr-20">
                        @php $rating = $event->reviews['rating'] @endphp
                        @for($i=0;$i<5;$i++)
                            <label class="fa fa-star {{($rating>$i && $rating<$i+1)? 'fa-star-half-full' : ( ($rating>$i)? '' : 'fa-star-o' )}}"></label>
                        @endfor
                        <a href="#review_panel"> (<b id="posts_reviews">{{$event->reviews['posts']}}</b> @if($event->reviews['posts']<2) review @else reviews @endif) </a>
                        <a class="btn btn-outline sbold dark btn-sm" data-toggle="modal" title="You must log in to write a review for this event." @if(!Auth::check()) href="#modal_login"
                           @else href="#modal_write_reviewx" @endif>
                            <i class="icon-pencil"></i> Write a review
                        </a>
                    </div>


                    <div class="events-social-icons ml-20 mr-20">
                        <p>
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
        </div>
        <!-- END DESCRIPTION AND CALENDAR -->
        <!-- BEGIN BANNERS -->
        @if(count($event->banners))
            <div class="row fixed-panel" style="padding:15px;margin-top:-15px">
                <div class="portlet light about-text">
                    <!-- BEGIN BANNER -->
                    <div class="portfolio-content color-panel">
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
        <!-- BEGIN VIDEOS -->
        @if(count($event->videos))
            <div class="row fixed-panel" style="padding:15px;margin-top:-20px" title="Videos of the event.">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4>
                        <i class="fa fa-youtube-play"></i> Videos
                    </h4>
                    @foreach($event->videos as $v)
                        <p class="text-center">
                            <iframe src="{!!$v->embed_code!!}" width="100%" height="600px" frameborder="0"></iframe>
                        {!!$v->description!!}
                        <hr>
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
                                            <img src="{{$i->url}}" alt="{{$i->url}}"></div>
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
        <!-- BEGIN REVIEWS -->
        @if(count($event->reviews['comments']))
            <div id="review_panel" class="row fixed-panel" style="padding:15px;margin-top:-20px" title="Reviews of the event.">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4>
                        <i class="fa fa-comments"></i> Reviews
                    </h4>
                    <div class="mt-comments" style="margin-top:15px;background-color:white !important;" title="Reviews.">
                        @foreach($event->reviews['comments'] as $c)
                            <div class="mt-comment col-md-6">
                                <div class="mt-comment-img">
                                    <img src="{{config('app.theme')}}img/avatar.png"></div>
                                <div class="mt-comment-body">
                                    <div class="mt-comment-info">
                            <span class="mt-comment-author">
                                {{$c->name}}<br>
                                @for($i=0;$i<5;$i++)
                                    <label class="fa fa-star @if($c->rating<=$i) fa-star-o @endif"></label>
                                @endfor
                            </span>
                                        <span class="mt-comment-date">{{date('M d,Y@g:iA',strtotime($c->created))}}</span>
                                    </div>
                                    <div class="mt-comment-text">{!! $c->review !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    @endif
    <!-- END REVIEWS -->
        <!-- BEGIN MAPS -->
        <div class="c-content-contact-1 c-opt-1">
            <div class="row" data-auto-height=".c-height">
                <div class="col-lg-9 col-md-6 c-desktop"></div>
                <div class="col-lg-3 col-md-6">
                    <div class="c-body">
                        <div class="c-section">
                            <h3>{{$event->venue}}</h3>
                        </div>
                        <div class="c-section">
                            <div class="c-content-label uppercase bg-blue">Address</div>
                            <p>{{$event->address}}
                                <br/>{{$event->city}}, {{$event->state}}
                                <br/>{{$event->country}} {{$event->zip}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="event_gmap" class="gmaps c-content-contact-1-gmap" title="Location of the venue." style="height: 450px;"
                 data-lat="{{$event->lat}}"
                 data-lng="{{$event->lng}}"
                 data-address="{{$event->address}}<br>{{$event->city}}, {{$event->state}}, {{$event->country}} {{$event->zip}}"
                 data-venue="{{$event->venue}}"
            ></div>
        </div>
        <!-- END MAPS -->

        <!-- BEGIN RESET PASSWORD MODAL -->
    @includeIf('production.events.reviews')
    <!-- END RECOVER RESET MODAL -->

    </div>
@endsection

@section('scripts')
    <script src="{{config('app.theme')}}js/fullcalendar.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyC7sODsH3uUz_lBbYH16eOCJU9igquCjzI" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/gmaps.min.js" type="text/javascript"></script>
    <script src="/js/production/events/index.js?v=1522346199" type="text/javascript"></script>
@endsection
