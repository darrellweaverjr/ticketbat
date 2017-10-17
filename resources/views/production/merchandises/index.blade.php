@php $page_title='Merchandise' @endphp
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
        <h1>Merchandise</h1>
        <div class="portfolio-content body_grid color-panel text-center" style="padding:50px;margin-bottom:50px" >        
            <div class="cbp text-center myMerchs">
                
                <div class="cbp-item show_section" data-href="https://shopviperroom.com">
                    <div class="cbp-caption">
                        <a class="cbp-caption-defaultWrap">
                            <img src="{{config('app.theme')}}img/merch-viper-room.jpg" alt="Viper Room Merch"> </a>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="cbp-l-caption-buttonLeft btn red">View merchandise</span>                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="cbp-item show_section" data-href="https://shop.beautybarlv.com">
                    <div class="cbp-caption">
                        <a class="cbp-caption-defaultWrap">
                            <img src="{{config('app.theme')}}img/merch-beauty-bar.jpg" alt="Beauty Bar Merch"> </a>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="cbp-l-caption-buttonLeft btn red">View merchandise</span>                               
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="cbp-item show_section" data-href="http://mjliveshow.com/shop">
                    <div class="cbp-caption">
                        <a class="cbp-caption-defaultWrap">
                            <img src="{{config('app.theme')}}img/merch-mj.jpg" alt="MJ Live Merch"> </a>
                        <div class="cbp-caption-activeWrap">
                            <div class="cbp-l-caption-alignCenter">
                                <div class="cbp-l-caption-body">
                                    <span class="cbp-l-caption-buttonLeft btn red">View merchandise</span>                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>    
        <!-- END SHOWS GRID-->
    </div>
</div>
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/production/merchandises/index.js" type="text/javascript"></script>
@endsection