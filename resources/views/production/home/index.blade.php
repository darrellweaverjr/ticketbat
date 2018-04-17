@php $page_title='Show Tickets' @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/daterangepicker.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

    <!-- BEGIN SLIDERS -->
    @if(!empty($sliders))
        <div id="mySliders" class="carousel slide top_header" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @foreach($sliders as $index=>$s)
                    <li data-target="#mySliders" data-slide-to="{{$index}}" @if(!$index) class="active" @endif ></li>
                @endforeach
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @foreach($sliders as $index=>$s)
                    <div class="item @if(!$index) active @endif" data-filter="{{$s->filter}}">
                        <a href="{{$s->slug}}"><img style="margin:auto" src="{{$s->image_url}}" alt="{{$s->alt}}"></a>
                    </div>
                @endforeach
            </div>
            <!-- Left and right controls -->
            <a class="left carousel-control" href="#mySliders" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#mySliders" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    @endif
    <!-- END SLIDERS -->

    <div class="page-content">
        <!-- BEGIN SEARCH BAR-->
        <div class="row">
            <div class="home-page-panel">
                <div id="myFilter" class="row widget-row">
                    <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by show's name">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"><i class="icon-magnifier"></i> Search</span>
                                    <span class="widget-thumb-body-stat">
                                        <input type="text" class="form-control" name="filter_name" placeholder="Find an event">

                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by location">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"><i class="icon-directions"></i> City</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_city">
                                        <option selected value="">All</option>
                                        @foreach($cities as $index=>$c)
                                            <option data-country="{{$c->country}}" data-state="{{$c->state}}" value="{{$c->city}}">{{$c->city}}</option>
                                        @endforeach
                                     </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by venue">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"><i class="icon-pointer"></i> Venue</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_venue" data-content='@php echo str_replace("'"," ",json_encode($venues));@endphp'>
                                        <option selected value="">All</option>
                                        @foreach($venues as $index=>$v)
                                            <option value="{{$v->id}}">{{$v->name}}</option>
                                        @endforeach
                                    </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by genre">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"><i class="icon-tag"></i> Genre</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_category">
                                        <option selected value="">All</option>
                                        @foreach($categories as $index=>$c)
                                            <option value="{{$c->id}}" data-parent="{{$c->id_parent}}">{{html_entity_decode($c->name)}} {{$c->subs}}</option>
                                        @endforeach
                                    </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by date range">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"><i class="icon-calendar"></i> Date</span>
                                    <span class="widget-thumb-body-stat">
                                    <div id="filter_date" class="pull-left tooltips btn btn-fit-height" style="margin-top:-7px!important;font-size:13px">
                                        <span style="margin-left:-13px;font-weight:bold!important;" class="thin"></span>
                                        <input type="hidden" name="filter_start_date">
                                        <input type="hidden" name="filter_end_date">
                                    </div>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </div>
        <!-- END SEARCH BAR-->
        <!-- BEGIN SHOWS GRID-->
        <div class="row">

            <div class="portfolio-content body_grid color-panel text-center ">
                <div class="home-page-panel">

                    <div id="myShows" class="cbp text-center">
                        @foreach($shows as $index=>$s)
                            <div class="cbp-item show_section filtered" data-id="{{$s->id}}" data-search="{{$s->name}}" data-category="{{$s->category_id}}" data-href="{{$s->link}}">
                                <div class="cbp-caption">
                                    <a class="cbp-caption-defaultWrap">
                                        <img src="{{$s->logo_url}}" alt="{{$s->name}}"> </a>
                                    <div class="cbp-caption-activeWrap">
                                        <div class="cbp-l-caption-alignCenter">
                                            <div class="cbp-l-caption-body">
                                                @if(!($s->price=='0.00' || ($s->starting_at && $s->starting_at=='0.00')))
                                                    <span class="cbp-l-caption-buttonLeft btn info">@if($s->time_alternative) {{$s->time_alternative}} @else Starting at <b>@if($s->starting_at) ${{$s->starting_at}} @else
                                                                ${{$s->price}} @endif</b> @endif</span>
                                                @endif
                                                <span class="cbp-l-caption-buttonRight btn info">GET TICKETS <i class="fa fa-arrow-circle-right"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($s->price=='0.00' || ($s->starting_at && $s->starting_at=='0.00'))
                                    <div class="btn btn-warning uppercase show_section_btnbuy"><span style="color:#000;margin-top:5px;"><b>Free</b></span></div>
                                @elseif($s->starting_at)

                                    {{-- This feature is temp disabled , pending a redesign --}}

                                    {{--<div class="btn btn-warning uppercase show_section_btnbuy">--}}
                                        {{--<h4 style="color:red;margin-top:5px;margin-bottom:-20px"><b>Clearance</b></h4><br><b style="text-decoration:line-through;color:#d43f3a">${{$s->regular_price}}</b><br><h4--}}
                                                {{--style="margin-top:-2px;color:green;font-weight:bold">${{$s->starting_at}}</h4>--}}
                                    {{--</div>--}}
                                @endif
                                <div class="cbp-l-grid-projects-title uppercase text-center show_section_caption ">{{$s->name}}</div>
                                <a class="cbp-l-grid-projects-desc uppercase text-center show_section_caption"><b class="date_venue_on">Next
                                        on {{date('M j, Y @ g:i A', strtotime($s->show_time))}}</b><br><b>in {{$s->venue}}</b></a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- END SHOWS GRID-->
    </div>
@endsection

@section('scripts')
    <script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/js/production/home/index.js" type="text/javascript"></script>
@endsection
