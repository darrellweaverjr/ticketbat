@php $page_title='Venues' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<div class="page-content">  
    <div class="row fixed-panel">
        <!-- BEGIN SHOWS GRID-->
        @foreach($venues as $index=>$vv)
        <h1>{{$vv['city']}}</h1>
        <div class="portfolio-content body_grid color-panel text-center" style="padding:50px;">        
            <div class="cbp text-center myVenues">
                @foreach($vv['venues'] as $v)
                <div class="cbp-item show_section filtered" data-id="{{$v->venue_id}}" data-search="{{$v->name}}" data-href="/production/venues/{{$v->slug}}">
                    <div class="cbp-caption">
                        <a class="cbp-caption-defaultWrap">
                            <img src="{{$v->url}}" alt="{{$v->name}}"> </a>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="cbp-l-caption-buttonLeft btn red">View events</span>
                                    <a href="{{$v->url}}" class="cbp-lightbox cbp-l-caption-buttonRight btn yellow" data-title="{{$v->name}}<br>"><i class="icon-size-fullscreen"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p style="height:100px" class="text-center"><small>{!! str_limit($v->description, $limit = 200, $end = '...') !!}</small></p>
                </div>

                @endforeach
            </div>
        </div>    
        @endforeach
        <!-- END SHOWS GRID-->
    </div>
</div>
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/production/venues/index.js" type="text/javascript"></script>
@endsection