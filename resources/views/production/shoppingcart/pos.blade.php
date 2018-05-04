@php $page_title='POS sales' @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    @if($ticket_types_css)<style>{{$ticket_types_css}}</style>@endif
    <style>
        .marked {
            border-style:solid;
            border-color:red;
            border-width:10px;
        }
    </style>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')


    <!-- BEGIN SHOWTIMES -->
    <div id="pos_search" class="row portlet mb-0" >
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center">
            <a data-toggle="modal" href="#modal_venue" class="dashboard-stat dashboard-stat-v2 dark" style="height:130px;color:red;font-size:22px">
                @if(!empty($venue_logo))
                <img src="{{$venue_logo}}" alt="VENUE # {{$venue_id}}" width="100%" height="100%">
                @else
                <h2>Select a venue</h2>
                @endif
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center">
            <a data-toggle="modal" href="#modal_show" class="dashboard-stat dashboard-stat-v2 dark" style="height:130px;color:red;font-size:22px">
                @if(!empty($show_logo))
                <img src="{{$show_logo}}" alt="SHOW # {{$show_id}}" width="100%" height="100%">
                @else
                <h2>Select a show</h2>
                @endif
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center">
            <a data-toggle="modal" href="#modal_showtime" class="dashboard-stat dashboard-stat-v2 dark" style="height:130px;color:red;font-size:22px">
                @if(!empty($show_time))
                <h2 style="color:white!important">{{date('l',strtotime($show_time))}}<br>{{date('M j, Y',strtotime($show_time))}}<br>{{date('g:i A',strtotime($show_time))}}</h2>
                @else
                <h2>Select a show/time</h2>
                @endif
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center" >
            <a data-toggle="modal" href="#modal_tally" class="dashboard-stat dashboard-stat-v2 dark" style="height:130px;color:red;font-size:22px">
                <div class="details sbold">
                    <div class="number sbold">
                        $ <span data-counter="counterup" id="cost_total" data-value="{{number_format($cart['total'],2)}}"></span>
                    </div>
                    <div class="desc"><h2 class="sbold"><i class="fa fa-ticket icon-ticket"></i> <span id="qty_total">{{$cart['quantity']}}</span></h2></div>
                </div>
            </a>
        </div>
    </div>
    <!-- END SHOWTIMES -->

    <!-- BEGIN TICKETS AND SHOPPINGCART -->
    <div id="pos-checkout-top" class="row portlet light" style="margin-bottom:-30px">
        <div id="pos-ticket-wrapper" class="portlet about-text col-xs-12 col-sm-5 col-md-5">
            @if(!empty($tickets))
            <h4><i class="fa fa-ticket"></i> Tickets</h4>
            <div id="pos_tickets" class="portlet-body pr-15 mt-3">
                <div class="panel-group">
                    <!-- BEGIN TICKETS -->
                    @foreach($tickets as $index=>$t)
                    <div class="panel">
                        <div class="panel-heading p-3">
                            <h4 class="panel-title {{$t['class']}} event-ticket-type"><strong class="lh-25">{{$t['type']}}</strong></h4>
                        </div>
                        <div class="panel-body" style="margin-bottom: -20px;">
                            @foreach($t['tickets'] as $tt)
                            <div class="row form-section" style="padding-right:15px">
                                <center>
                                    <span class="col-sm-5 col-md-5">
                                        <h4><b>@if($tt->retail_price>0) ${{$tt->retail_price}} @else FREE @endif</b>
                                            @if(!empty($tt->max_available))<label class="label label-danger bold">{{$tt->max_available}} left</label>@endif</h4>
                                    </span>
                                    @if(isset($tt->max_available) && $tt->max_available<1)
                                    <div class="col-sm-7 col-md-7" style="background-color:red!important;font-size:22px;color:white"><b>SOLD OUT</b></div>
                                    @else
                                    <div class="col-sm-7 col-md-7 input-group input-group-lg">
                                        <input type="number" value="{{$tt->cart}}" name="{{$tt->ticket_id}}" @if(!empty($tt->max_available)) max="{{$tt->max_available}}" @endif class="form-control input-lg">
                                    </div>
                                    @endif
                                    @if($tt->title!='None')<small>{{$tt->title}}</small>@endif
                                </center>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    <!-- END TICKETS -->
                </div>
            </div>
            @else
            <h4><i class="fa fa-info-circle"></i> Information</h4>
            <div id="pos_tickets" class="portlet-body pr-15 mt-3">
                <h2 class="text-center" style="color:red"><b>
                @if(empty($venues)) There are not venues availables to sell tickets.
                @elseif(empty($venue_id)) You must select a venue.
                @elseif(empty($shows)) There are not shows availables in that venue.
                @elseif(empty($show_id)) You must select a show.
                @elseif(empty($showtimes)) There are not show times availables for that event.
                @elseif(empty($show_time_id)) You must select a date/time.
                @else There are not tickets availables.
                @endif
                </b></h2>
            </div>
            @endif
        </div>
        <div id="pos-cart-wrapper" class="portlet light about-text col-xs-12 col-sm-7 col-md-7" >
            <h4><i class="fa fa-shopping-cart"></i> Shopping Cart</h4>
            <div class="portlet-body" id="pos_cart" style="padding-left:5px">
                <!-- BEGIN CART -->
                <table class="table table-hover table-responsive table-condensed table-header-fixed" id="tb_items">
                    <thead>
                    <tr>
                        <th>Item(s)</th>
                        <th class="text-right">Fees</th>
                        <th class="text-center">Remove</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($cart['items']))
                    @foreach($cart['items'] as $i)
                        <tr data-id="{{$i->id}}">
                            <td>
                                <h4 class="bold">{{$i->number_of_items}} :: {{$i->product_type}}
                                    @if(!empty($i->package)) - <small>{{$i->package}}</small> @endif
                                    @if($i->show_time_id != $show_time_id) <br><b class="label-warning">{{date('n/d/Y @ g:i A', strtotime($i->show_time))}}</b> @endif
                                    @if($i->show_id != $show_id) <br><b class="label-warning uppercase">{{$i->name}}</b> @endif
                                </h4>
                            </td>
                            <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}<br>@if($i->inclusive_fee>0) $0.00 @else ${{number_format($i->processing_fee,2)}} @endif</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-lg btn-danger"><i class="fa fa-remove icon-ban"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            <!-- END CART -->
            </div>
        </div>
    </div>
    <!-- END TICKETS AND SHOPPINGCART -->

    <!-- BEGIN PAYMENT -->
    <div id="pos-checkout-bottom" class="row portlet light">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Payment methods.">
                <i class="fa fa-credit-card icon-credit-card"></i> Payment
                <ul class="pull-right pos-nav-pills nav nav-pills ">
                    <li class="active">
                        <a href="#tab_cash" data-toggle="tab"><i class="fa fa-money icon-money"></i> CASH </a>
                    </li>
                    <li>
                        <a href="#tab_swipe" data-toggle="tab"><i class="fa fa-credit-card icon-credit-card"></i> SWIPE </a>
                    </li>
                    <li>
                        <a href="#tab_card" data-toggle="tab"><i class="fa fa-credit-card icon-credit-card"></i> ENTER </a>
                    </li>
                </ul>
            </h4>
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <!-- Begin payment tabs -->
                    <div class="tab-content" id="tabs_payment">
                        
                        <div class="tab-pane fade @if($cart['total']<0.01 && $cart['quantity']>0) active in @else hidden @endif" id="tab_skip">
                            <!-- BEGIN SWIPE FORM -->
                            @includeIf('production.shoppingcart.skip', ['cart' => $cart])
                            <!-- END SWIPE FORM -->
                        </div>
                        
                        <div class="tab-pane fade @if(!($cart['total']<0.01 && $cart['quantity']>0)) active in @endif" id="tab_cash">
                            <!-- BEGIN CASH FORM -->
                            @includeIf('production.shoppingcart.cash', ['cart' => $cart])
                            <!-- END CASH FORM -->
                        </div>

                        <div class="tab-pane fade" id="tab_swipe">
                            <!-- BEGIN SWIPE FORM -->
                            @includeIf('production.shoppingcart.swipe', ['cart' => $cart])
                            <!-- END SWIPE FORM -->
                        </div>

                        <div class="tab-pane fade" id="tab_card">
                            <!-- BEGIN SWIPE FORM -->
                            @includeIf('production.shoppingcart.card', ['cart' => $cart])
                            <!-- END SWIPE FORM -->
                        </div>
                       
                        <center class="hidden">
                            <input type="checkbox" id="accept_terms" value="1"/>
                            <button type="button" id="btn_process" disabled="true" class="btn btn-primary btn-lg uppercase">Process payment <i class="fa fa-arrow-circle-right"></i></button>
                            <button type="button" id="btn_loading" disabled="true" class="btn btn-primary btn-lg uppercase hidden">Please wait. Processing your items <i class="fa fa-refresh"></i></button>
                        </center>

                        <div class="row text-center" style="padding:20px">
                            <button type="button" id="btn_check_pay" class="btn btn-primary btn-lg btn-block uppercase">Check form and process payment <i class="fa fa-money"></i></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAYMENT -->

    <!-- BEGIN VENUES MODAL -->
    <div id="modal_venue" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                    <h3 class="modal-title">Venues</h3>
                </div>
                <div class="modal-body">                    
                    <!-- BEGIN FORM-->
                    <form method="post" action="{{url()->current()}}">
                        <input type="hidden" name="_token" value="{{ Session::token() }}" />
                        <div class="row mt-radio-list">
                            @foreach($venues as $id=>$v)
                            <label class="mt-radio mt-radio-outline text-center bold col-lg-6 col-md-6 col-sm-6 col-xs-12">      
                                <input type="radio" name="venue_id" @if($venue_id==$v['id']) checked="true" @endif value="{{$v['id']}}">
                                <h1><img src="{{$v['logo']}}" style="height:160px;" @if($venue_id==$v['id']) class="marked" @endif alt="{{$v['name']}}" width="100%"></h1>
                                <span style="display:none"></span>
                            </label>
                            @endforeach
                        </div>
                    </form>                    
                    <!-- END FORM-->
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <!-- END VENUES MODAL -->
    
    <!-- BEGIN SHOWS MODAL -->
    <div id="modal_show" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                    <h3 class="modal-title">Shows</h3>
                </div>
                <div class="modal-body">                    
                    <!-- BEGIN FORM-->
                    <form method="post" action="{{url()->current()}}">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="row mt-radio-list">
                            @foreach($shows as $id=>$s)
                            <label class="mt-radio mt-radio-outline text-center bold col-lg-6 col-md-6 col-sm-6 col-xs-12">      
                                <input type="radio" name="show_id" @if($show_id==$s['id']) checked="true" @endif value="{{$s['id']}}">
                                <h1><img src="{{$s['logo']}}" style="height:160px;" @if($show_id==$s['id']) class="marked" @endif alt="{{$s['name']}}" width="100%"></h1>
                                <span style="display:none"></span>
                            </label>
                            @endforeach
                        </div>
                    </form>                    
                    <!-- END FORM-->
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <!-- END SHOWS MODAL -->
    
    <!-- BEGIN SHOWTIMES MODAL -->
    <div id="modal_showtime" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                    <h3 class="modal-title">Show times</h3>
                </div>
                <div class="modal-body">                    
                    <!-- BEGIN FORM-->
                    <form method="post" action="{{url()->current()}}">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="row mt-radio-list" style="padding-right:30px">
                            @foreach($showtimes as $st)
                            <label class="mt-radio mt-radio-outline text-center">      
                                <input type="radio" name="show_time_id" @if($show_time_id==$st->id) checked="true" @endif value="{{$st->id}}">
                                <i class="btn btn-default btn-lg btn-block sbold @if($show_time_id==$st->id) marked @endif "><h2><b>{{$st->show_time}}</b></h2></i>
                                <span style="display:none"></span>
                            </label>
                            @endforeach
                        </div>
                    </form>                    
                    <!-- END FORM-->
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <!-- END SHOWTIMES MODAL -->
    
    <!-- BEGIN TALLY MODAL -->
    <div id="modal_tally" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                    <h3 class="modal-title">Tally</h3>
                </div>
                <div class="modal-body">
                    <div data-always-visible="1" data-rail-visible1="1">
                        <h3 class="text-center"><b>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</b><br>{{Auth::user()->email}}</h3>
                        <div class="row" style="padding:20px">
                            <h3 class="sbold required">Pending: </h3><hr>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-info">Tickets: </h3>
                                <h3 class="text-center" id="t_p_tickets">{{$cart['quantity']}}</h3>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-success">Collect: </h3>
                                <h3 class="text-right" id="t_p_total">${{number_format($cart['total'],2)}}</h3>
                            </div>
                        </div>
                        <div class="row" style="padding:20px">
                            <h3 class="sbold required">Totals: </h3><hr>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-primary">Transactions: </h3>
                                <h3 class="text-center" id="t_t_transactions">@if(!empty($cart['tally'])){{$cart['tally']['transactions']}} @endif</h3>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-info">Tickets: </h3>
                                <h3 class="text-center" id="t_t_tickets">@if(!empty($cart['tally'])){{$cart['tally']['tickets']}} @endif</h3>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-success">Cash: </h3>
                                <h3 class="text-right" id="t_t_cash">@if(!empty($cart['tally']))${{number_format($cart['tally']['cash'],2)}} @endif</h3>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-stat1">
                                <h3 class="sbold label-success">Total: </h3>
                                <h3 class="text-right" id="t_t_total">@if(!empty($cart['tally']))${{number_format($cart['tally']['total'],2)}} @endif</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <!-- END TALLY MODAL -->
    
    <!-- BEGIN COMPLETE MODAL -->
    <div id="modal_complete" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <img src="{{config('app.theme')}}img/checked.png" alt="">
                    <h3 class="modal-title" style="font-weight:bold;color:green">Purchase completed successfully!</h3>
                </div>
                <div class="modal-body" style="padding:30px">
                    <div class="row">
                        <button class="btn btn-danger btn-lg btn-block uppercase" data-href="/purchase/printer/" id="btn_receipt_print"><i class="fa fa-print icon-printer"></i> Print Receipt</button>
                    </div><hr>
                    <div class="row">
                        <h4>Print tickets:</h4>
                        <a class="btn btn-outline sbold dark btn-lg uppercase ticket_regular" href="/user/purchases/tickets/C/" target="_blank"><i class="fa fa-newspaper-o"></i> Regular Paper</a>
                        <a class="btn btn-outline sbold dark btn-lg uppercase ticket_boca" href="/user/purchases/tickets/S/" target="_blank"><i class="fa fa-ticket"></i> BOCA Ticket</a>
                        <a class="btn btn-outline sbold dark btn-lg uppercase ticket_wrist" href="/user/purchases/tickets/W/" target="_blank"><i class="fa fa-hand-paper-o"></i> Wristband</a>
                    </div><hr>
                    <div class="row">
                        <form method="post" id="form_receipt_email" class="form-horizontal">
                            <input type="hidden" name="purchases" value="">
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="email" value="" name="email" class="form-control input-lg" placeholder="abc@gmail.com">
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <button type="button" class="btn btn-info btn-lg btn-block uppercase" id="btn_receipt_email"><i class="fa fa-send"></i> Email receipt</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="btn_continue" class="btn btn-success btn-lg btn-block uppercase"><i class="fa fa-shopping-cart icon-basket"></i> Continue shopping</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END COMPLETE MODAL -->

@endsection

@section('scripts')
    <script src="{{config('app.theme')}}js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/jquery.waypoints.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/jquery.counterup.min.js" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/skip.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/card.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/swipe.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/cash.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/validations.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/pos.js" type="text/javascript"></script>
@endsection
