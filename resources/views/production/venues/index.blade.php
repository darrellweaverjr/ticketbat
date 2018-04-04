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
<style>
.cbp-item {
  /*display: inline-block;*/
  margin: 0px 1%!important;
  float: left;
  width: 23%!important;
}
</style>
<div class="page-content">
        <!-- BEGIN SHOWS GRID-->
        @foreach($venues as $index=>$vv)
        <div class="container">
            <h1>{{$vv['city']}}</h1>

            <div class="cbp text-center myVenues">
                @foreach($vv['venues'] as $v)
                <div class="cbp-item  filtered" data-id="{{$v->venue_id}}" data-search="{{$v->name}}" data-href="/venue/{{$v->slug}}">
                    <div class="cbp-caption">
                        <div class="venue-pic">
                            <a class="cbp-caption-defaultWrap"><img src="{{$v->logo_url}}" alt="{{$v->name}}"></a>
                        </div>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="btn green">View Events</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="show-txt"><small>{!! str_limit($v->description, $limit = 200, $end = '...') !!}</small></div>
                </div>

                @endforeach
            </div>
        </div>
        @endforeach
        <!-- END SHOWS GRID-->
</div>
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/production/venues/index.js" type="text/javascript"></script>
@endsection
