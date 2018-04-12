@php $page_title='Shopping cart' @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

    <!-- BEGIN TOP HEADER -->
    <div class="page-content color-panel">
        <!-- BEGIN ITEMS -->
        <div class="row fixed-panel">
            <div class="portlet light about-text mb-15">
                <!-- BEGIN DESCRIPTION -->
                <h4 title="Items in the shopping cart.">
                    <i class="fa fa-shopping-cart"></i> Shopping Cart
                    <div class="cart-item-count actions">
                        <label id="count_items">You currently have <strong>{{count($cart['items'])}}</strong> @if(count($cart['items'])>1) items @else item @endif</label>
                    </div>
                </h4>
                <p class="mt-20 mb-25 text-center">
                    <strong style="color:#578ebe">
                        If gifting or attending this event with other people, click on the <button type="button" disabled="true" class="btn btn-info"><i class="fa fa-gift"></i></button> button below.
                    </strong>
                </p>

                <div class="portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                    <table class="table table-hover table-responsive table-condensed table-header-fixed" id="tb_items">
                        <thead>
                        <tr>
                            <th class="hidden-xs">Item(s)</th>
                            <th>Quantity</th>
                            <th class="text-right">Price each</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Fees</th>
                            @if(!empty($cart['items']) && empty($cart['items'][0]->consignment))
                                <th class="text-center">
                                    Share
                                </th>
                            @endif
                            <th class="text-center">
                                Remove
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cart['items'] as $i)
                            <tr id="{{$i->id}}" data-qty="{{$i->number_of_items}}">
                                <td class="hidden-xs">
                                    <strong class="label label-sm sbold label-success">{{$i->product_type}}</strong> for <a href="/buy/{{$i->slug}}/{{$i->show_time_id}}">{{$i->name}}</a>
                                    @if(!empty($i->package)) <br>
                                    <small><i>{{$i->package}}</i></small> @endif <br>On {{date('l, F j, Y @ g:i A', strtotime($i->show_time))}}
                                </td>
                                <td>
                                    <input type="number" @if(!empty($i->consignment)) disabled="true" @endif value="{{$i->number_of_items}}" min="1" @if($i->available_qty<0) max="1000" @else max="{{$i->available_qty}}"
                                           @endif style="width:60px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0">
                                </td>
                                <td style="text-align:right">${{number_format($i->cost_per_product,2)}}</td>
                                <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}</td>
                                <td style="text-align:right">@if($i->inclusive_fee>0) $0.00 @else ${{number_format($i->processing_fee,2)}} @endif</td>
                                @if(empty($i->consignment))
                                    <td class="gift-tix text-center">
                                        <button type="button" class="btn btn-info"><i class="fa fa-gift"></i></button>
                                    </td>
                                @endif
                                <td class="rm-tix text-center">
                                    <button type="button" class="btn btn-danger"><i class="fa fa-remove icon-ban"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="hidden-sm hidden-md hidden-lg hidden-xl">
                                    <strong class="label label-sm sbold label-success">{{$i->product_type}}</strong> for <a href="/buy/{{$i->slug}}/{{$i->show_time_id}}">{{$i->name}}</a>
                                    @if(!empty($i->package)) <br>
                                    <small><i>{{$i->package}}</i></small> @endif <br>On {{date('l, F j, Y @ g:i A', strtotime($i->show_time))}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <hr>
                </div>

                <div class="row portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                    <div class="col-xs-12 col-sm-7">
                        <!-- BEGIN FORM-->
                        <form method="post" id="form_coupon" class="form-horizontal">
                            <div class="alert alert-danger display-hide">Incorrect/Invalid Coupon: That coupon is not valid for you items.</div>
                            <div class="alert alert-success display-hide"></div>
                            <div class="input-group show-error">
                                <input type="text" class="form-control" placeholder="Write here your code" name="coupon">
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="add_coupon_code">Add coupon</button>
                            </span>
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-7 col-xs-offset-5 col-sm-5 col-sm-offset-0">
                        <div class="row">
                            <div class="col-xs-7 text-right">
                                <h6>subtotal: </h6>
                                <h6>processing fee:</h6>
                                @if(!empty($cart['savings'])) <h6>Savings: </h6> @endif
                                @if(!empty($cart['printed'])) <h6>Printed tickets: </h6> @endif
                                <h5><strong>Grand Total: </strong></h5>
                            </div>
                            <div class="col-xs-5 text-right pl-0">
                                <h6>$ {{number_format($cart['retail_price'],2)}}</h6>
                                <h6>$ {{number_format($cart['processing_fee'],2)}}</h6>
                                @if(!empty($cart['savings'])) <h6>$ {{number_format($cart['savings'],2)}}</h6> @endif
                                @if(!empty($cart['printed'])) <h6>$ {{number_format($cart['printed'],2)}}</h6> @endif
                                <h5><strong>$ {{number_format($cart['total'],2)}}</strong></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END ITEMS -->
        <!--     BEGIN PRINTED TICKETS -->
        @if(count($cart['printed_tickets']['shows']) && (!Auth::check() || Auth::user()->user_type_id!=7))
            <div class="row fixed-panel">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4 title="Printed options for selected tickets.">
                        <i class="fa fa-print icon-printer"></i> Ticket options
                    </h4>
                    <p id="printed_details" class="margin-top-20 @if( $cart['printed_tickets']['details'] < 1 ) hidden @endif">
                        All tickets for @if(count($cart['printed_tickets']['shows'])>1) these shows @else this show @endif will be mailed if you pick a printed ticket option:<br>
                        @foreach($cart['printed_tickets']['shows'] as $s)
                            <b style="color:#32c5d2">{{$s}}</b><br>
                        @endforeach
                        Other shows are only available as eTickets and will not be shipped if you choose a printed option.
                    </p>
                    <div class="portlet-body light portlet-fit" style="padding:10px">
                        <select class="form-control" name="printed_tickets">
                            <option value="0" @if(empty($cart['printed_tickets']['select'])) selected @endif>&diams; eTickets - (No charge. Print your tickets at home or show your tickets from your mobile phone.)
                            </option>
                            <option value="NULL" disabled>&diams; Printed Tickets - ($7.50 to $30.00 charge.) Select from below:</option>
                            <option value="20" @if($cart['printed_tickets']['select']==20) selected @endif>&emsp; &#8594; 2 Business Day (Evening) $20.00</option>
                            <option value="21" @if($cart['printed_tickets']['select']==21) selected @endif>&emsp; &#8594; 2 Business Day (Morning) $21.00</option>
                            <option value="17" @if($cart['printed_tickets']['select']==17) selected @endif>&emsp; &#8594; 3 Business Day (Evening) $17.00</option>
                            <option value="30" @if($cart['printed_tickets']['select']==30) selected @endif>&emsp; &#8594; Saturday Delivery $30.00 (By noon, order must be placed before Wednesday)</option>
                            <option value="7.5" @if($cart['printed_tickets']['select']==7.5) selected @endif>&emsp; &#8594; Standard Mail - $7.50</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
    <!-- END PRINTED TICKETS -->
        <!-- BEGIN RESTRICTIONS -->
        @if(count($cart['restrictions']))
            <div class="row fixed-panel">
                <div class="portlet light about-text">
                    <!-- BEGIN DESCRIPTION -->
                    <h4 title="Restrictions for the event(s).">
                        <i class="fa fa-ban icon-ban"></i> Restrictions
                    </h4>
                    <p class="margin-top-20" id="restrictions_panel">
                        @foreach($cart['restrictions'] as $show=>$age)
                            <strong style="color:#32c5d2">{{$show}}</strong> requires attendees to be {{$age}} years of age or older.<br>
                        @endforeach
                    </p>
                    <div class="portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                    </div>
                </div>
            </div>
        @endif
    <!-- END RESTRICTIONS -->
        <!-- BEGIN BANNERS -->
        @if(count($cart['banners']) && (!Auth::check() || Auth::user()->user_type_id!=7))
            <div class="row fixed-panel">
                <div class="portlet light about-text">
                    <!-- BEGIN BANNER -->
                    <div class="portfolio-content color-panel">
                        <div id="myBanners" class="cbp text-center">
                            @foreach($cart['banners'] as $index=>$i)
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
        <!-- BEGIN PAYMENT -->
        <div class="row fixed-panel">
            <div class="portlet light about-text">
                <!-- BEGIN DESCRIPTION -->
                <h4 title="Payment methods.">
                    <i class="fa fa-credit-card icon-credit-card"></i> Payment
                </h4>
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        @if($cart['seller'] && $cart['total']>0)
                            <ul class="pos-nav-pills nav nav-pills nav-justified">
                                <li class="active">
                                    <a href="#tab_card" data-toggle="tab">ENTER CARD</a>
                                </li>
                                <li>
                                    <a href="#tab_swipe" data-toggle="tab">SWIPE CARD</a>
                                </li>
                                <li >
                                    <a href="#tab_cash" data-toggle="tab">ENTER CASH</a>
                                </li>
                            </ul>
                            <hr>
                        @endif
                        <!-- Begin payment tabs -->
                        <div class="tab-content" id="tabs_payment">
                            <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) active @endif in @if($cart['total']>0) hidden @endif" id="tab_skip">
                                <!-- BEGIN SWIPE FORM -->
                                @includeIf('production.shoppingcart.skip')
                                <!-- END SWIPE FORM -->
                            </div>

                            <div class="tab-pane fade active in @if(!($cart['total']>0)) hidden @endif" id="tab_card">
                                <!-- BEGIN SWIPE FORM -->
                                @includeIf('production.shoppingcart.card')
                                <!-- END SWIPE FORM -->
                            </div>

                            <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) hidden @endif" id="tab_swipe">
                                <!-- BEGIN SWIPE FORM -->
                                @includeIf('production.shoppingcart.swipe')
                                <!-- END SWIPE FORM -->
                            </div>

                            <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) hidden @endif" id="tab_cash">
                                <!-- BEGIN CASH FORM -->
                                @includeIf('production.shoppingcart.cash')
                                <!-- END CASH FORM -->
                            </div>

                            <div class="row @if(Auth::check() && Auth::user()->user_type_id==7) hidden @endif" style="padding:20px">
                                <hr>
                                <label class="control-label text-center">
                                    <i class="required"> You must accept the terms and conditions in order to checkout.</i>
                                </label><br>
                                <label class="mt-checkbox"><input type="checkbox" id="accept_terms" value="1"/>
                                    I ACCEPT THE TERMS AND CONDITIONS. <a data-toggle="modal" href="#modal_terms_conditions">CLICK HERE TO VIEW TERMS AND CONDITIONS.</a>
                                    <span></span></label><br>
                                <label class="mt-checkbox"><input type="checkbox" checked="true" id="accept_newsletter" value="1"/>
                                    SIGN UP FOR OUR NEWSLETTER
                                    <span></span></label><br>
                                <center>
                                    <button type="button" id="btn_process" disabled="true" class="btn btn-primary btn-lg uppercase">Process payment <i class="fa fa-arrow-circle-right"></i></button>
                                    <button type="button" id="btn_loading" disabled="true" class="btn btn-primary btn-lg uppercase hidden">Please wait. Processing your items <i class="fa fa-refresh"></i></button>
                                </center>
                            </div>
                            @if(Auth::check() && Auth::user()->user_type_id==7)
                                <div class="row text-center" style="padding:20px">
                                    <hr>
                                    <button type="button" id="btn_check_pay" class="btn btn-primary btn-lg uppercase">Check form and process payment <i class="fa fa-money"></i></button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAYMENT -->
        <!-- BEGIN FORM COMPLETE-->
        <form method="post" id="form_complete" action="/purchase/complete" class="hidden">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="purchases" value="">
            <input type="hidden" name="send_welcome_email" value="">
        </form>
        <!-- END FORM COMPLETE-->
    </div>

    <!-- BEGIN TERMS AND CONDITIONS MODAL -->
    @includeIf('production.shoppingcart.terms')
    <!-- END TERMS AND CONDITIONS MODAL -->
    <!-- BEGIN CVV MODAL -->
    @includeIf('production.shoppingcart.cvv')
    <!-- END CVV MODAL -->
    <!-- BEGIN SHARE TICKETS MODAL -->
    @includeIf('production.general.share_tickets')
    <!-- END SHARE TICKETS MODAL -->

@endsection

@section('scripts')
    <script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/skip.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/card.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/swipe.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/cash.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/coupon.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/update.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/general/share_tickets.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/share_tickets.js?v=1522349999" type="text/javascript"></script>
    <script src="/js/production/shoppingcart/index.js?v=1522349999" type="text/javascript"></script>
@endsection
