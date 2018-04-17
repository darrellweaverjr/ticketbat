@php $page_title='POS sales' @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    @if($ticket_types_css)<style>{{$ticket_types_css}}

    #tb_items {
        //width: 100px;
        //height: 100px;
        //background: red;
        -webkit-transition: width 2s; /* Safari */
        transition: width 2s;
    }

    </style>@endif
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')


    <!-- BEGIN SHOWTIMES -->
    <div class="row portlet light">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 dark">
                <img src="{{$event->logo_url}}" alt="-No logo image-" style="width:200px;height:115px">
            </a>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
            <span class="caption-subject font-red sbold uppercase"><h3><b>{{$event->name}}</b></h3><samll>{{$event->venue}}</small></span>
            <div class="text-center" id="pos_showtimes">
                <select class="form-control input-lg" name="show_time_id" data-show="{{$event->show_id}}">
                    @foreach($event->showtimes as $st)
                    <option value="{{$st->id}}" @if($show_time_id == $st->id) selected @endif>{{$st->show_day}} @ {{$st->show_hour}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center" >
            <a data-toggle="modal" href="#modal_tally" class="dashboard-stat dashboard-stat-v2 dark">
                <div class="visual">
                    <i class="fa fa-money"></i>
                </div>
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
    <div class="row portlet light" style="margin-top:-44px;margin-bottom:-30px">
        <div class="col-xs-12 col-sm-5 col-md-5">
            <div class="portlet light about-text">
                <h4><i class="fa fa-ticket"></i> Tickets</h4>
                <div class="portlet-body" id="pos_tickets">
                    @if(empty($event->tickets))
                        <div class="bg-red bg-font-red">
                            <hr>
                            <h1 class="text-center">Tickets are currently not for sale.</h1>
                            <br>
                        </div>
                    @else
                        <div class="panel-group" >
                            <!-- BEGIN TICKETS -->
                            @foreach($event->tickets as $index=>$t)
                                <div class="panel">
                                    <div class="panel-heading p-3">
                                        <h4 class="panel-title {{$t['class']}} event-ticket-type"><strong class="lh-25">{{$t['type']}}</strong></h4>
                                    </div>
                                    <div class="panel-body" style="margin-bottom: -20px">
                                        @foreach($t['tickets'] as $tt)
                                        <div class="row form-section">
                                            <center>
                                                <span class="col-sm-5 col-md-5">
                                                    <h4><b>@if($tt->retail_price>0) ${{$tt->retail_price}} @else FREE @endif</b></h4>
                                                </span>
                                                <div class="col-sm-7 col-md-7 input-group input-group-lg">
                                                    <input type="number" value="{{$tt->cart}}" name="{{$tt->ticket_id}}" class="form-control input-lg">
                                                </div>
                                                @if($tt->title!='None')<small>{{$tt->title}}</small>@endif
                                            </center>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                        @endforeach
                        <!-- END TICKETS -->
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-7 col-md-7">
            <div class="portlet light event-seating">
                <h4><i class="fa fa-shopping-cart"></i> Shopping Cart</h4>
                <div class="portlet-body" id="pos_cart">
                    <!-- BEGIN CART -->
                    <table class="table table-hover table-responsive table-condensed table-header-fixed" id="tb_items">
                        <thead>
                        <tr>
                            <th>Item(s)</th>
                            <th class="text-center">Date<br>Time</th>
                            <th class="text-right">Subt<br>Fees</th>
                            <th class="text-center">Remove</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cart['items'] as $i)
                            <tr data-id="{{$i->id}}">
                                <td>
                                    <h4 class="bold">({{$i->number_of_items}}) {{$i->product_type}}
                                        @if(!empty($i->package)) <br><i>{{$i->package}}</i> @endif</h4>
                                    @if($i->show_id != $event->show_id) <br><i class="label-warning">{{$i->name}}</i> @endif
                                </td>
                                <td @if($i->show_time_id != $show_time_id) class="label-warning" @endif style="text-align:center">{{date('M d, Y', strtotime($i->show_time))}}<br>{{date('g:i A', strtotime($i->show_time))}}</td>
                                <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}<br>@if($i->inclusive_fee>0) $0.00 @else ${{number_format($i->processing_fee,2)}} @endif</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-lg btn-danger"><i class="fa fa-remove icon-ban"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                <!-- END CART -->
                </div>
            </div>
        </div>
    </div>
    <!-- END TICKETS AND SHOPPINGCART -->

    <!-- BEGIN PAYMENT -->
    <div class="row light fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Payment methods.">
                <i class="fa fa-credit-card icon-credit-card"></i> Payment
                <ul class="pull-right pos-nav-pills nav nav-pills ">
                    <li class="active">
                        <a href="#tab_cash" data-toggle="tab"><i class="fa fa-money icon-money"></i> ENTER CASH</a>
                    </li>
                    <li>
                        <a href="#tab_swipe" data-toggle="tab"><i class="fa fa-credit-card icon-credit-card"></i> SWIPE CARD</a>
                    </li>
                    <li>
                        <a href="#tab_card" data-toggle="tab"><i class="fa fa-credit-card icon-credit-card"></i> ENTER CARD</a>
                    </li>
                </ul>
            </h4>
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <!-- Begin payment tabs -->
                    <div class="tab-content" id="tabs_payment">
                        <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) active @endif in @if($cart['total']>0) hidden @endif" id="tab_skip">
                            <!-- BEGIN SWIPE FORM -->
                            @includeIf('production.shoppingcart.skip', ['cart' => $cart])
                            <!-- END SWIPE FORM -->
                        </div>

                        <div class="tab-pane fade in active" id="tab_cash">
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
                            <button type="button" id="btn_check_pay" class="btn btn-primary btn-lg uppercase">Check form and process payment <i class="fa fa-money"></i></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAYMENT -->

    <!-- BEGIN TERMS MODAL -->
    <div id="modal_tally" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Tally</h3>
                </div>
                <div class="modal-body">
                    <div data-always-visible="1" data-rail-visible1="1">
                        <h3 class="text-center"><b>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</b><br>{{Auth::user()->email}}</h3>
                        <div class="row" style="padding:20px">
                            <h3 class="sbold required">Pending: </h3><hr>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-primary">Transactions: </h3>
                                <h3 class="text-center" id="t_p_transactions">1</h3>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-info">Tickets: </h3>
                                <h3 class="text-center" id="t_p_tickets">{{$cart['quantity']}}</h3>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-success">Cash: </h3>
                                <h3 class="text-right" id="t_p_total">${{number_format($cart['total'],2)}}</h3>
                            </div>
                        </div>
                        <div class="row" style="padding:20px">
                            <h3 class="sbold required">Totals: </h3><hr>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-primary">Transactions: </h3>
                                <h3 class="text-center" id="t_t_transactions">{{$cart['tally']['transactions']}}</h3>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-info">Tickets: </h3>
                                <h3 class="text-center" id="t_t_tickets">{{$cart['tally']['tickets']}}</h3>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 text-stat1">
                                <h3 class="sbold label-success">Cash: </h3>
                                <h3 class="text-right" id="t_t_total">${{number_format($cart['tally']['total'],2)}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END TERMS MODAL -->

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
    <script src="/js/production/pos/buy.js" type="text/javascript"></script>
@endsection