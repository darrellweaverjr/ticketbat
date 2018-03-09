@php $page_title='Shopping cart' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css" />
<link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<style>
    .cash_input div{
        padding:5px;       
    }
    .cash_input button{
        padding:5px;
        font-size:40px!important;
        font-weight:bold!important;
    }
    .cash_total input{
        font-size:20px!important;
        font-weight:bold!important;
        min-width:50px;
    }
</style>
@endsection

@section('content')

<!-- BEGIN TOP HEADER -->
<div class="page-content color-panel">
    <!-- BEGIN ITEMS -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Items in the shopping cart.">
                <i class="fa fa-list icon-list"></i> Shopping Cart
                <div class="actions pull-right">
                    <label id="count_items">You currently have <b>{{count($cart['items'])}}</b> @if(count($cart['items'])>1) items @else item @endif</label>
                </div>
            </h4>
            <p class="margin-top-20">
            <center><b style="color:#578ebe">
                    If you are attending this event with other people and would like to email them their ticket, please click on the
                    <button type="button" disabled="true" class="btn btn-info"><i class="fa fa-share icon-share"></i></button> button below.
            </b></center>
            </p>
            <div class="portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                <table class="table table-hover table-responsive table-condensed table-header-fixed" id="tb_items">
                    <thead>
                        <tr>
                            <th>Item(s)</th>
                            <th>Quantity</th>
                            <th style="text-align:right">Price each</th>
                            <th style="text-align:right">Subtotal</th>
                            <th style="text-align:right">Fees</th>
                            @if(!empty($cart['items']) && empty($cart['items'][0]->consignment))
                            <th><center>Share</center></th>
                            @endif
                            <th><center>Remove</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart['items'] as $i)
                        <tr id="{{$i->id}}" data-qty="{{$i->number_of_items}}">
                            <td>
                                <b class="label label-sm sbold label-success">{{$i->product_type}}</b> for <a href="/buy/{{$i->slug}}/{{$i->show_time_id}}">{{$i->name}}</a>
                                @if(!empty($i->package)) <br><small><i>{{$i->package}}</i></small> @endif <br>On {{date('l, F j, Y @ g:i A', strtotime($i->show_time))}}
                            </td>
                            <td>
                                <input type="number" @if(!empty($i->consignment)) disabled="true" @endif value="{{$i->number_of_items}}" min="1" @if($i->available_qty<0) max="1000" @else max="{{$i->available_qty}}" @endif style="width:60px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0">
                            </td>
                            <td style="text-align:right">${{number_format($i->cost_per_product,2)}}</td>
                            <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}</td>
                            <td style="text-align:right">${{number_format($i->processing_fee,2)}}</td>
                            @if(empty($i->consignment))
                            <td><center><button type="button" class="btn btn-info"><i class="fa fa-share icon-share share"></i></button></center></td>
                            @endif
                            <td><center><button type="button" class="btn btn-danger"><i class="fa fa-remove icon-ban"></i></button></center></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table><hr>
            </div>
            <div class="row portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                <div class="col-md-5">
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
                <div class="col-md-2"></div>
                <div class="col-md-5 text-right" style="padding-right:20px;">
                    <h4><label class="label label-default bold">Subtotal: </label><label id="cost_subtotal" class="font-blue-madison sbold">$ {{number_format($cart['retail_price'],2)}}</label></h4>
                    <h4><label class="label label-info bold">Processing fee: </label><label id="cost_fees" class="font-blue-madison sbold">$ {{number_format($cart['processing_fee'],2)}}</label></h4>
                    <h4 @if(empty($cart['savings'])) class="hidden" @endif><label class="label label-success bold">Savings: </label><label id="cost_savings" class="font-blue-madison sbold">$ {{number_format($cart['savings'],2)}}</label></h4>
                    <h4 @if(empty($cart['printed'])) class="hidden" @endif><label class="label label-warning bold">Printed tickets: </label><label id="cost_printed" class="font-blue-madison sbold">$ {{number_format($cart['printed'],2)}}</label></h4>
                    <h4><label class="label label-primary bold">Grand total: </label><label id="cost_total" data-total="{{$cart['total']}}" class="font-blue-madison bold">$ {{number_format($cart['total'],2)}}</label></h4>
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
                    <option value="0" @if(empty($cart['printed_tickets']['select'])) selected @endif>&diams; eTickets - (No charge. Print your tickets at home or show your tickets from your mobile phone.)</option>
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
                <b style="color:#32c5d2">{{$show}}</b> requires attendees to be {{$age}} years of age or older.<br>
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
    <div class="row fixed-panel" >
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
                    <ul class="nav nav-pills nav-justified">
                        <li class="active">
                            <a href="#tab_card" data-toggle="tab">ENTER CARD</a>
                        </li>
                        <li>
                            <a href="#tab_swipe" data-toggle="tab">SWIPE CARD</a>
                        </li>
                        <li>
                            <a href="#tab_cash" data-toggle="tab">ENTER CASH</a>
                        </li>
                    </ul><hr>
                    @endif
                    <div class="tab-content" id="tabs_payment">
                        <div class="tab-pane fade active in @if($cart['total']>0) hidden @endif" id="tab_skip">
                            <div class="row">
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_skip" class="form-horizontal">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button> You have some form errors. Please check below.
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="method" value="skip">
                                    <input type="hidden" name="newsletter" value="1">
                                    <div class="share_tickets_subform hidden"></div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Customer:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="Write your full name" name="customer" value="{{old('customer')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Phone:</label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="### ### ####" name="phone" value="{{old('phone')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Email (for receipt):
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}" autocomplete="on">
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        <div class="tab-pane fade active in @if(!($cart['total']>0)) hidden @endif" id="tab_card">
                            <div class="row">
                                <div class="form-group text-center">
                                    <img id="icon-mc" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-mastercard.png">
                                    <img id="icon-vs" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-visa.png">
                                    <img id="icon-dc" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-discover.png">
                                    <img id="icon-ax" src="{{config('app.theme')}}img/card/cc-icon-american-express.png">
                                </div>
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_card" class="form-horizontal">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button> You have some form errors. Please check below.
                                    </div>
                                    <div class="alert alert-warning display-hide"></div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="method" value="card">
                                    <input type="hidden" name="newsletter" value="1">
                                    <div class="share_tickets_subform hidden"></div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Customer:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="Write your full name" name="customer" value="{{old('customer')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Card number:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <input type="number" class="form-control" placeholder="#### #### #### ####" name="card" data-amex="{{$cart['amex_only']}}" style="min-width:170px" autocomplete="on">
                                        </div>
                                        <label class="control-label col-sm-2 text-right">CVV:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="####" name="cvv" style="min-width:75px" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <a class="btn btn-info" data-toggle="modal" href="#modal_cvv"><i class="fa fa-question icon-question"></i> What is it?</a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Exp month:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <select class="form-control" name="month" placeholder="M" style="min-width:145px" value="{{old('month')}}" autocomplete="on">
                                                <option value="" disabled="true" selected="true">- Select month -</option>
                                                <option value="01">1 (January)</option>
                                                <option value="02">2 (February)</option>
                                                <option value="03">3 (March)</option>
                                                <option value="04">4 (April)</option>
                                                <option value="05">5 (May)</option>
                                                <option value="06">6 (June)</option>
                                                <option value="07">7 (July)</option>
                                                <option value="08">8 (August)</option>
                                                <option value="09">9 (September)</option>
                                                <option value="10">10 (October)</option>
                                                <option value="11">11 (November)</option>
                                                <option value="12">12 (December)</option>
                                            </select>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Exp year:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <select class="form-control" name="year" placeholder="YYYY" style="min-width:135px" value="{{old('year')}}" autocomplete="on">
                                                <option value="" disabled="true" selected="true">- Select year -</option>
                                                @for ($y = date('Y'); $y <= date('Y')+20; $y++)
                                                    <option value="{{$y}}">{{$y}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Address:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="" name="address" value="{{old('address')}}" autocomplete="on">
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Country:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <select class="form-control" name="country" placeholder="United States" style="min-width:135px" value="{{old('country')}}" autocomplete="on">
                                                @foreach( $cart['countries'] as $c)
                                                    <option @if($c->code=='US') selected @endif value="{{$c->code}}">{{$c->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">State/region:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <select class="form-control" name="state" placeholder="Nevada" style="min-width:135px" value="{{old('state')}}" autocomplete="on">
									                              <option value="" disabled="true" selected="true">- Select state/region -</option>
                                                @foreach( $cart['regions'] as $r)
                                                    <option value="{{$r->code}}">{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">City:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <input type="text" class="form-control" placeholder="" name="city" value="{{old('city')}}" autocomplete="on">
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Zip:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-3 show-error">
                                            <input type="text" class="form-control" placeholder="#####" name="zip" style="min-width:75px" value="{{old('zip')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Phone:</label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="### ### ####" name="phone" value="{{old('phone')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Email (for receipt):
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}" autocomplete="on">
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) hidden @endif" id="tab_swipe">
                            <div class="row">
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_swipe" class="form-horizontal">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button> You have some form errors. Please check below.
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="method" value="swipe">
                                    <input type="hidden" name="newsletter" value="1">
                                    <div class="share_tickets_subform hidden"></div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Customer:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="Write your full name" name="customer" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Phone:</label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="### ### ####" name="phone" value="{{old('phone')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Email (for receipt):
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="hidden">
                                        <input type="hidden" name="card" value="">
                                        <input type="hidden" name="month" value="0">
                                        <input type="hidden" name="year" value="0">
                                        <input type="hidden" name="UMcardpresent" value=true>
                                        <input type="hidden" name="UMmagstripe" value="">
                                        <input type="hidden" name="UMdukpt" value="">
                                        <input type="hidden" name="UMtermtype" value="POS">
                                        <input type="hidden" name="UMmagsupport" value="yes">
                                        <input type="hidden" name="UMcontactless" value="no">
                                        <input type="hidden" name="UMsignature" value="">
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        <div class="tab-pane fade @if(!($cart['total']>0) || $cart['seller']<1) hidden @endif" id="tab_cash">
                            <div class="row">
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_cash" class="form-horizontal">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button> You have some errors. Please check below.
                                    </div>
                                    <div class="form-group cash_breakdown" style="text-align:center;padding-right:15px">
                                        <div class="col-md-2"></div>
                                        <div class="cash_input col-md-4">
                                            <div class="col-md-4"><button name="cash_1" value="1" type="button" class="btn btn-info btn-lg btn-block">1</button></div>
                                            <div class="col-md-4"><button name="cash_2" value="2" type="button" class="btn btn-info btn-lg btn-block">2</button></div>
                                            <div class="col-md-4"><button name="cash_3" value="3" type="button" class="btn btn-info btn-lg btn-block">3</button></div>
                                            
                                            <div class="col-md-4"><button name="cash_4" value="4" type="button" class="btn btn-info btn-lg btn-block">4</button></div>
                                            <div class="col-md-4"><button name="cash_5" value="5" type="button" class="btn btn-info btn-lg btn-block">5</button></div>
                                            <div class="col-md-4"><button name="cash_6" value="6" type="button" class="btn btn-info btn-lg btn-block">6</button></div>
                                            
                                            <div class="col-md-4"><button name="cash_7" value="7" type="button" class="btn btn-info btn-lg btn-block">7</button></div>
                                            <div class="col-md-4"><button name="cash_8" value="8" type="button" class="btn btn-info btn-lg btn-block">8</button></div>
                                            <div class="col-md-4"><button name="cash_9" value="9" type="button" class="btn btn-info btn-lg btn-block">9</button></div>
                                            
                                            <div class="col-md-8"><button name="cash_0" value="0" type="button" class="btn btn-info btn-lg btn-block">0</button></div>
                                            <div class="col-md-4"><button name="cash_x" value="" type="button" class="btn btn-danger btn-lg btn-block">X</button></div>
                                        </div>
                                        <div class="cash_total col-md-4">
                                            <label class="control-label col-sm-4 text-right">Total ($):</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control input-lg text-right" style="color:blue;" value="{{sprintf("%.2f",$cart['total'])}}" name="pending" readOnly="true" value="{{old('pending')}}">
                                            </div>
                                            
                                            <label class="control-label col-sm-4 text-right">Cash ($):</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control input-lg text-right" value="0.00" name="cashed" value="{{old('change')}}">
                                            </div>
                                            
                                            <label class="control-label col-sm-4 text-right" id="label_total">Due ($):</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control input-lg text-right" style="color:red;" value="-{{sprintf("%.2f",$cart['total'])}}" name="subtotal" readOnly="true" value="{{old('subtotal')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div><hr>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="method" value="cash">
                                    <input type="hidden" name="newsletter" value="1">
                                    <div class="share_tickets_subform hidden"></div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Customer:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="Write your full name" name="customer" 
                                                   @if(Auth::check() && Auth::user()->user_type_id==7) value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}" @endif
                                                   value="{{old('customer')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Phone:</label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="### ### ####" name="phone" value="{{old('phone')}}" autocomplete="on">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Email (for receipt):
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}" autocomplete="on">
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        <div class="row" style="padding:20px">
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
<!-- BEGIN SWIPE CARD MODAL -->
@includeIf('production.shoppingcart.swipe')
<!-- END SWIPE CARD MODAL -->
<!-- BEGIN SHARE TICKETS MODAL -->
@includeIf('production.general.share_tickets')
<!-- END SHARE TICKETS MODAL -->

@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/update.js" type="text/javascript"></script>
<script src="/js/production/general/share_tickets.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/share_tickets.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/cash.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/swipe.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/validations.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/index.js" type="text/javascript"></script>
@endsection
