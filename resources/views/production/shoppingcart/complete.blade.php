@php $page_title='Completed' @endphp
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

<!-- BEGIN TOP HEADER -->
<div class="page-content color-panel">
<!--     BEGIN RECEIPTS -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Items in the shopping cart.">
                <i class="fa fa-thumbs-up"></i> Thank you for purchasing tickets on TicketBat.com!
            </h4>
            <p class="margin-top-20">
                @foreach($purchased as $p)
                <i style="padding-left:10px" class="fa fa-ticket"></i> <strong> {{$p['qty']}}</strong> @if($p['qty']>1) tickets @else ticket @endif <strong>{{$p['ticket_type']}}</strong> on <strong>{{$p['schedule']}}</strong><br>
                    @if($seller>0)
                    <a class="btn btn-info btn-lg uppercase add_pay" style="float:right;margin-top:-35px;margin-left:10px" data-ticket="{{$p['ticket_id']}}" data-show_time="{{$p['show_time_id']}}" ><i class="fa fa-backward icon-arrow-left"></i> Add & pay</a>
                    <a class="btn btn-info btn-lg uppercase" style="float:right;margin-top:-35px;margin-left:10px" href="/buy/{{$p['slug']}}/{{$p['show_time_id']}}" ><i class="fa fa-backward icon-arrow-left"></i> Buy Page</a>
                    <a class="btn btn-info btn-lg uppercase" style="float:right;margin-top:-35px;margin-left:10px" href="/event/{{$p['slug']}}" ><i class="fa fa-backward icon-arrow-left"></i> Show Page</a>
                    @endif
                @endforeach
                <hr>
                @if(!empty($after_purchase_note))
                @foreach($after_purchase_note as $p)
                <div class="alert alert-info" style="margin:20px">{{$p}}</div>
                @endforeach
                <hr>
                @endif
            @if($seller>0)
                <center>
                    <button class="btn btn-danger btn-lg uppercase" data-href="/purchase/printer/{{$purchases}}" id="btn_receipt_print"><i class="fa fa-print icon-printer"></i> Print Receipt</button><hr>
                    <h4>Print tickets:</h4><br>
                    <a class="btn btn-outline sbold dark btn-lg uppercase" href="/user/purchases/tickets/C/{{$purchases}}" target="_blank"><i class="fa fa-newspaper-o"></i> Regular Paper</a>
                    <a class="btn btn-outline sbold dark btn-lg uppercase" href="/user/purchases/tickets/S/{{$purchases}}" target="_blank"><i class="fa fa-ticket"></i> BOCA Ticket</a>
                    <a class="btn btn-outline sbold dark btn-lg uppercase" href="/user/purchases/tickets/W/{{$purchases}}" target="_blank"><i class="fa fa-hand-paper-o"></i> Wristband</a>
                </center>
            @elseif(!empty($print_receipt))
            <center><a class="btn btn-danger btn-lg uppercase" href="/user/purchases/tickets/C/{{$print_receipt}}" target="_blank"><i class="fa fa-print icon-printer"></i> Print all tickets now!</a></center>
            @endif
                @if(!empty($send_welcome_email))
                    @if($send_welcome_email>0)
                    <div class="alert alert-success" style="margin:20px">
                        A welcome email has been sent to @if(empty($sent_to['email'])) the client @else <strong>{{$sent_to['email']}}</strong>@endif. Please check your spam / junk folder for our emails.
                    </div>
                    @else
                    <div class="alert alert-danger" style="margin:20px">
                        A welcome email could not be sent to @if(empty($sent_to['email'])) the client @else <strong>{{$sent_to['email']}}</strong>@endif. <button type="button" class="btn btn-danger" data-id="{{$sent_to['id']}}" id="resend_welcome" >Click here to re-send <i class="fa fa-send"></i></button>
                    </div>
                    @endif
                @endif
                @if($sent_receipts)
                <div class="alert alert-success" style="margin:20px">
                    A receipt has been sent to @if(empty($sent_to['email'])) the client @else <strong>{{$sent_to['email']}}</strong>@endif. Please check your spam / junk folder for our emails.
                </div>
                @else
                <div class="alert alert-danger" style="margin:20px">
                    The receipt could not be sent to @if(empty($sent_to['email'])) the client @else <strong>{{$sent_to['email']}}</strong>@endif. <button type="button" class="btn btn-danger" data-purchases="{{$purchases}}" id="resend_receipts" >Click here to re-send <i class="fa fa-send"></i></button>
                </div>
                @endif
                <hr>
                <center>
                    <h4><span style="color:#32c5d2"><b>Get Tickets Fast.</b></span> Download the <span style="color:#32c5d2"><b>Ticketbat App</b></span> and keep track of all your tickets on your phone.</h4><br>
                    <a href="https://itunes.apple.com/us/app/ticketbat/id1176307768?mt=8" target="_blank" class="btn btn-outline sbold dark btn-lg"><i class="fa fa-apple"></i> Apple Store</a>
                    <a href="https://play.google.com/store/apps/details?id=com.ionicframework.ticketbatapp2017&hl=en" target="_blank" class="btn btn-outline sbold dark btn-lg"><i class="fa fa-play"></i> Google Play</a>
                </center><br>
            </p>
        </div>
    </div>
    <!-- END RECEIPTS -->
    <!-- BEGIN BANNERS -->
    @if(count($banners))
    <div class="row fixed-panel" >
        <div class="portlet light about-text">
            <!-- BEGIN BANNER -->
            <div class="portfolio-content color-panel">
                <div id="myBanners" class="cbp text-center">
                    @foreach($banners as $index=>$i)
                    <div class="cbp-item show_section1" style="margin-right:20px">
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
</div>

@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/complete.js" type="text/javascript"></script>
@endsection
