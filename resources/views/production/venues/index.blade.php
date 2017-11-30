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
        <!-- BEGIN SHOWS GRID-->
        @foreach($venues as $index=>$vv)
        <div class="container">
            <h1>{{$vv['city']}}</h1>

            <div class="cbp text-center myVenues">
                @foreach($vv['venues'] as $v)
                <div class="cbp-item show_section filtered" data-id="{{$v->venue_id}}" data-search="{{$v->name}}" data-href="/production/venue/{{$v->slug}}">
                    <div class="cbp-caption">
                        <div class="venue-pic">
                            <a class="cbp-caption-defaultWrap"><img src="{{$v->url}}" alt="{{$v->name}}"></a>
                        </div>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="btn green">View Events</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p style="min-height:100px" class="text-center"><small>{!! str_limit($v->description, $limit = 200, $end = '...') !!}</small></p>
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