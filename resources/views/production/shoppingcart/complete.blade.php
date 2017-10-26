@php $page_title='Completed' @endphp
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
<div class="page-content color-panel">  
<!--     BEGIN RECEIPTS -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Items in the shopping cart.">
                <i class="fa fa-thumbs-up"></i> Thank you for purchasing tickets on TicketBat.com! 
            </h4>  
            <p class="margin-top-20">
                <center><a class="btn btn-danger btn-lg uppercase"><i class="fa fa-print icon-printer"></i> Print all tickets now!</a></center>
                <div class="alert alert-danger" style="margin:20px">
                    You have some errors. Please check below. 
                </div>
                <div class="alert alert-success" style="margin:20px">
                    A receipt has been sent to ivan@ticketbat.com.
                </div>
                <div class="alert alert-info" style="margin:20px">
                    Please check your spam / junk folder for our emails.
                </div><hr>
                <center>
                    <h4><span style="color:#32c5d2"><b>Get Tickets Fast.</b></span> Download the <span style="color:#32c5d2"><b>Ticketbat App</b></span> and keep track of all your tickets on your phone.</h4><br>
                    <a href="https://itunes.apple.com/nz/app/ticketbat/id1176307768?mt=8" target="_blank" class="btn btn-outline sbold dark btn-lg"><i class="fa fa-apple"></i> Apple Store</a>
                    <a href="https://play.google.com/store/apps/details?id=com.ionicframework.ticketbatapp892952&hl=en" target="_blank" class="btn btn-outline sbold dark btn-lg"><i class="fa fa-google"></i> Google Play</a>
                </center><br>
            </p>
        </div>
    </div>
    <!-- END RECEIPTS -->
    <!--     BEGIN PURCHASED -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Items in the shopping cart.">
                <i class="fa fa-ticket"></i> You purchased
            </h4>  
            <p class="margin-top-20">
            </p>
            <div class="portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">

            </div>
        </div>
    </div>
    <!-- END PURCHASED -->
</div>

@endsection

@section('scripts')
<script src="/js/production/shoppingcart/complete.js" type="text/javascript"></script>
@endsection