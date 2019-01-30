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
            <div class="carousel-inner">
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
                    <div class="hidden-xs hidden-sm col-md-1"></div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by show's name">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-wrap"><i class="icon-magnifier"></i> Search</span>
                                    <span class="widget-thumb-body-stat">
                                        <input type="text" class="form-control" name="filter_name" placeholder="Find an event" aria-label="Seach event by name">
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by location">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-wrap"><i class="icon-directions"></i> City</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_city" aria-label="Filter by city">
                                        <option selected value="">All</option>
                                        @foreach($cities as $c)
                                            <option data-country="{{$c['country']}}" data-state="{{$c['state']}}" value="{{$c['city']}}">{{$c['city']}}</option>
                                        @endforeach
                                     </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by venue">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-wrap"><i class="icon-pointer"></i> Venue</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_venue" data-content='@php echo str_replace("'"," ",json_encode($venues));@endphp' aria-label="Filter by venue">
                                        <option selected value="">All</option>
                                        @foreach($venues as $v)
                                            <option value="{{$v['id']}}">{{$v['name']}}</option>
                                        @endforeach
                                    </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by genre">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-wrap"><i class="icon-tag"></i> Genre</span>
                                    <span class="widget-thumb-body-stat">
                                    <select class="form-control" name="filter_category" aria-label="Filter by category">
                                        <option selected value="">All</option>
                                        @foreach($categories as $c)
                                            <option value="{{$c->id}}" data-parent="{{$c->id_parent}}">{{html_entity_decode($c->name)}} {{$c->subs}}</option>
                                        @endforeach
                                    </select>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                        <div class="widget-thumb widget-bg-color-white text-uppercase" title="Filter by date range">
                            <div class="widget-thumb-wrap">

                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-wrap"><i class="icon-calendar"></i> Date</span>
                                    <span class="widget-thumb-body-stat">
                                    <div id="filter_date" class="pull-left tooltips btn btn-fit-height" style="margin-top:-7px!important;font-size:13px">
                                        <span style="margin-left:-13px;font-weight:bold!important;" class="thin"></span>
                                        <input type="hidden" name="filter_start_date" aria-label="Filter by start date">
                                        <input type="hidden" name="filter_end_date" aria-label="Filter by end date">
                                    </div>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hidden-xs hidden-sm col-md-1"></div>
                </div>
            </div>
        </div>
        <!-- END SEARCH BAR-->
        <!-- BEGIN SHOWS GRID-->
        <div class="row" style="min-height:200px">

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
                                                @if(!($s->price=='0.00'))
                                                <span style="color:whitesmoke!important">@if($s->time_alternative) {{$s->time_alternative}} @else Starting at <b>${{$s->price}}</b> @endif</span>
                                                @else
                                                <span style="color:whitesmoke!important">GET TICKETS <i class="fa fa-arrow-circle-right"></i></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($s->price=='0.00')
                                    <div class="btn btn-warning uppercase show_section_btnbuy"><span style="color:#000;margin-top:5px;"><b>Free</b></span></div>
                                @endif
                                <div class="cbp-l-grid-projects-title uppercase text-center show_section_caption ">{{$s->name}}</div>
                                <a class="cbp-l-grid-projects-desc1 uppercase1 text-center show_section_caption1">
                                    <small>
                                    Next on {{date('M j, Y @ g:i A', strtotime($s->show_time))}}<br>
                                    in {{$s->venue}}
                                    </small>
                                </a>
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
