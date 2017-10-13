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
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')


<!-- END NAME BAR-->
<div class="page-content color-panel">  
    <!-- BEGIN DESCRIPTION AND CALENDAR -->
    <div class="row fixed-panel">
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4>
                    <i class="fa fa-check icon-info"></i> Event details
                    
                </h4>  
                <p class="margin-top-20">sdfsdfsd</p>
                <!-- END DESCRIPTION -->
                <!-- BEGIN BANDS -->
                <div class="timeline" style="margin:5px;padding-bottom:10px">
                    @foreach($event->tickets as $t)
                    <!-- BAND ITEM -->
                    
                    <!-- END BAND ITEM -->
                    @endforeach
                </div>
                <!-- ENDS BANDS -->
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4>
                    <i class="fa fa-check icon-calendar"></i> Show times
                </h4> 
                <div class="timeline" style="margin:5px;padding-bottom:10px">
                    @foreach($event->tickets as $t)
                    <!-- BAND ITEM -->
                    {!! $t->ticket_type !!}
                    <!-- END BAND ITEM -->
                    @endforeach
                </div> 
            </div>
        </div>
    </div>
    <!-- END DESCRIPTION AND CALENDAR -->
</div>
@endsection

@section('scripts')
<script src="/js/production/events/buy.js" type="text/javascript"></script>
@endsection