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
            <div class="widget-thumb-wrap text-center uppercase" style="font-size:46px">{{$event->event_name}}</div>
        </div>
    </div>
    <!-- END NAME BAR-->
    <!-- BEGIN BODY GRID-->
    <div class="body_grid text-center">        
        <div id="myShows" class="cbp text-center">
            
        </div>
    </div>    
    <!-- END BODY GRID-->
</div>
@endsection

@section('scripts')
<script src="/js/production/events/event.js" type="text/javascript"></script>
@endsection