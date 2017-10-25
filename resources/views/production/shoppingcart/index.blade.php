@php $page_title='Shopping cart' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/datatables.min.css" rel="stylesheet" type="text/css" />
<link href="{{config('app.theme')}}css/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<!-- BEGIN TOP HEADER -->
<div class="page-content color-panel">  
    <!-- BEGIN ITEMS -->
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Items in the shopping cart.">
                <i class="fa fa-list icon-list"></i> Shoppingcart 
                <div class="actions pull-right">
                    <label>You currently have <b>{{count($cart['items'])}}</b> @if(count($cart['items'])>1) items @else item @endif</label>
                </div>
            </h4>  
            <p class="margin-top-20">
                <center><b class="label label-sm sbold label-info">If you are attending this show with other people and would like to email them their ticket, please click on "Share Tickets"</b></center>
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
                            <th><center>Share</center></th>
                            <th><center>Remove</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart['items'] as $i)
                        <tr>
                            <td>
                                <b class="label label-sm sbold label-success">{{$i->product_type}}</b> for <a href="/production/event/{{$i->slug}}/{{$i->show_time_id}}">{{$i->name}}</a><br>
                                On {{date('l, F j, Y @ g:i A', strtotime($i->show_time))}}
                            </td>
                            <td>
                                <input type="number" data-id="{{$i->id}}" data-qty="{{$i->number_of_items}}" value="{{$i->number_of_items}}" min="1" @if($i->available_qty<0) max="1000" @else max="{{$i->available_qty}}" @endif style="width:60px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0">
                            </td>
                            <td style="text-align:right">${{number_format($i->cost_per_product,2)}}</td>
                            <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}</td>
                            <td style="text-align:right">${{number_format($i->processing_fee,2)}}</td>
                            <td><center><button type="button" data-id="{{$i->id}}" data-qty="{{$i->number_of_items}}" class="btn btn-info"><i class="fa fa-share icon-share"></i></button></center></td>
                            <td><center><button type="button" data-id="{{$i->id}}" class="btn btn-danger"><i class="fa fa-remove icon-ban"></i></button></center></td>
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
                    <h4><label class="label label-default bold">Subtotal: </label><label id="cost_subtotal" class="font-blue-madison">$ {{number_format($cart['retail_price'],2)}}</label></h4>
                    <h4><label class="label label-info bold">Processing fee: </label><label id="cost_fees" class="font-blue-madison">$ {{number_format($cart['processing_fee'],2)}}</label></h4>
                    <h4 @if(empty($cart['savings'])) class="hidden" @endif><label class="label label-success bold">Savings: </label><label id="cost_savings" class="font-blue-madison">$ {{number_format($cart['savings'],2)}}</label></h4>
                    <h4 @if(empty($cart['printed'])) class="hidden" @endif><label class="label label-warning bold">Printed tickets: </label><label id="cost_printed" class="font-blue-madison">$ {{number_format($cart['printed'],2)}}</label></h4>
                    <h4><label class="label label-primary bold">Grand total: </label><label id="cost_total" data-total="{{$cart['total']}}" class="font-blue-madison">$ {{number_format($cart['total'],2)}}</label></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- END ITEMS -->
<!--     BEGIN PRINTED TICKETS -->
    @if(count($cart['printed_tickets']['shows']))
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4 title="Printed options for selected tickets.">
                <i class="fa fa-print icon-printer"></i> Ticket options
            </h4>  
            @if( $cart['printed_tickets']['details'] > 0 )
            <p class="margin-top-20">
            All tickets for @if(count($cart['printed_tickets']['shows'])>1) these shows @else this show @endif will be mailed if you pick a printed ticket option:<br>
            @foreach($cart['printed_tickets']['shows'] as $s)
                <b style="color:#32c5d2">{{$s}}</b><br>
            @endforeach
            Other shows are only available as eTickets and will not be shipped if you choose a printed option.
            </p>
            @endif
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
            <p class="margin-top-20">
                @foreach($cart['restrictions'] as $show=>$r)
                <b style="color:#32c5d2">{{$show}}</b> requires to be {{preg_replace("/[^0-9]/","",$r)}} years of age or older to attend the event.<br>
                @endforeach
            </p>
            <div class="portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
            </div>
        </div>
    </div>
    @endif
    <!-- END RESTRICTIONS -->
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
                        @if(!($cart['total']>0))
                        <div class="tab-pane fade active in" id="tab_skip">
                            <div class="row"> 
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_skip" class="form-horizontal" action="/production/shoppingcart/process">
                                    <div class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button> You have some form errors. Please check below. 
                                    </div>
                                    <input type="hidden" name="newsletter" value="1">
                                    <div class="share_tickets_subform hidden"></div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Customer:
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="Write your full name" name="customer">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Phone:</label>
                                        <div class="col-sm-8 show-error">
                                            <input type="text" class="form-control" placeholder="### ### ####" name="phone">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 text-right">Email (for receipt):
                                            <i class="required"> required</i>
                                        </label>
                                        <div class="col-sm-8 show-error">
                                            <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}">
                                        </div>
                                    </div>
                                </form>
                                <!-- END FORM-->
                            </div>
                        </div>
                        @else
                            <div class="tab-pane fade active in" id="tab_card">
                                <div class="row">
                                    <div class="form-group text-center">
                                        <img src="{{config('app.theme')}}img/card/cc-icon-mastercard.png">
                                        <img src="{{config('app.theme')}}img/card/cc-icon-visa.png">
                                        <img src="{{config('app.theme')}}img/card/cc-icon-discover.png">
                                        <img src="{{config('app.theme')}}img/card/cc-icon-american-express.png">
                                    </div>
                                    <!-- BEGIN FORM-->
                                    <form method="post" id="form_card" class="form-horizontal" action="/production/shoppingcart/process">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. 
                                        </div>
                                        <div class="alert alert-warning display-hide" id="div_show_errors"></div>
                                        <input type="hidden" name="newsletter" value="1">
                                        <div class="share_tickets_subform hidden"></div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Customer:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="Write your full name" name="customer">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Card number:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <input type="number" class="form-control" placeholder="#### #### #### ####" name="card" data-amex="{{$cart['amex_only']}}" style="min-width:170px">
                                            </div>
                                            <label class="control-label col-sm-2 text-right">CVV:
                                                <i class="required"> required</i> 
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" placeholder="####" name="cvv" style="min-width:75px">
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
                                                <select class="form-control" name="exp_month" placeholder="M" style="min-width:145px">
                                                    <option value="" disabled="true" selected="true">- Select month -</option>
                                                    <option value="1">1 (January)</option>
                                                    <option value="2">2 (February)</option>
                                                    <option value="3">3 (March)</option>
                                                    <option value="4">4 (April)</option>
                                                    <option value="5">5 (May)</option>
                                                    <option value="6">6 (June)</option>
                                                    <option value="7">7 (July)</option>
                                                    <option value="8">8 (August)</option>
                                                    <option value="9">9 (September)</option>
                                                    <option value="10">10 (October)</option>
                                                    <option value="11">11 (November)</option>
                                                    <option value="12">12 (December)</option>
                                                </select>
                                            </div>
                                            <label class="control-label col-sm-2 text-right">Exp year:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <select class="form-control" name="exp_year" placeholder="YYYY" style="min-width:135px">
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
                                                <input type="text" class="form-control" placeholder="0000 Main St." name="address">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">City:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <input type="text" class="form-control" placeholder="Las Vegas" name="city">
                                            </div>
                                            <label class="control-label col-sm-2 text-right">Zip:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <input type="text" class="form-control" placeholder="#####" name="zip" style="min-width:75px">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Country:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <select class="form-control" name="country" placeholder="United States" style="min-width:135px">
                                                    @foreach( $cart['countries'] as $c)
                                                        <option @if($c->code=='US') selected @endif value="{{$c->code}}">{{$c->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="control-label col-sm-2 text-right">State/region:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-3 show-error">
                                                <select class="form-control" name="state" placeholder="Nevada" style="min-width:135px">
                                                    @foreach( $cart['regions'] as $r)
                                                        <option value="{{$r->code}}">{{$r->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Phone:</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="### ### ####" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Email (for receipt):
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}">
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>
                            @if($cart['seller'])
                            <div class="tab-pane fade" id="tab_swipe">
                                <div class="row">
                                    <!-- BEGIN FORM-->
                                    <form method="post" id="form_swipe" class="form-horizontal" action="/production/shoppingcart/process">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. 
                                        </div>
                                        <input type="hidden" name="newsletter" value="1">
                                        <div class="share_tickets_subform hidden"></div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Customer:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="Write your full name" name="customer">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Phone:</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="### ### ####" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Email (for receipt):
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}">
                                            </div>
                                        </div>
                                        <div class="hidden">
                                            <input type="hidden" name="card" value="">
                                            <input type="hidden" name="exp_month" value="0">
                                            <input type="hidden" name="exp_year" value="0">
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
                            <div class="tab-pane fade" id="tab_cash">
                                <div class="row">
                                    <!-- BEGIN FORM-->
                                    <form method="post" id="form_cash" class="form-horizontal" action="/production/shoppingcart/process">
                                        <div class="alert alert-danger display-hide">
                                            <button class="close" data-close="alert"></button> You have some errors. Please check below. 
                                        </div>
                                        <div class="form-group desglose" style="padding-right:15px">
                                            <label class="control-label col-sm-1 text-right">$100 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="100" value="0" name="x100" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="100" value="0.00" name="r100" disabled="true">
                                            </div>

                                            <label class="control-label col-sm-1 text-right">$50 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="50" value="0" name="x50" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="50" value="0.00" name="r50" disabled="true">
                                            </div>

                                            <label class="control-label col-sm-1 text-right">$20 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="20" value="0" name="x20" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="20" value="0.00" name="r20" disabled="true">
                                            </div>
                                        </div>
                                        <div class="form-group desglose" style="padding-right:15px">
                                            <label class="control-label col-sm-1 text-right">$10 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="10" value="0" name="x10" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="10" value="0.00" name="r10" disabled="true">
                                            </div>

                                            <label class="control-label col-sm-1 text-right">$5 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="5" value="0" name="x5" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="5" value="0.00" name="r5" disabled="true">
                                            </div>

                                            <label class="control-label col-sm-1 text-right">$1 x</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="100" step="1" data-bill="1" value="0" name="x1" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" data-bill="1" value="0.00" name="r1" disabled="true">
                                            </div>
                                        </div>
                                        <div class="form-group desglose" style="padding-right:15px">
                                            <label class="control-label col-sm-1 text-right">Change</label>
                                            <div class="col-sm-1 show-error">
                                                <input type="number" class="form-control" min="0" max="99" step="1" value="00" name="change" style="min-width:70px">
                                            </div>
                                            <div class="col-sm-2 show-error"></div>
                                            <label class="control-label col-sm-2 text-right" id="collect_text">Collect</label>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" style="color:red;font-size:20px;font-weight:bold" data-pending="{{number_format($cart['total'],2)}}" value="-{{number_format($cart['total'],2)}}" name="pending" readOnly="true">
                                            </div>
                                            <label class="control-label col-sm-2 text-right">Total</label>
                                            <div class="col-sm-2 show-error">
                                                <input type="number" class="form-control" style="color:blue;font-size:20px;font-weight:bold" value="0.00" name="subtotal" readOnly="true">
                                            </div>
                                        </div>
                                        <input type="hidden" name="newsletter" value="1">
                                        <div class="share_tickets_subform hidden"></div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Customer:
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="Write your full name" name="customer">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Phone:</label>
                                            <div class="col-sm-8 show-error">
                                                <input type="text" class="form-control" placeholder="### ### ####" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Email (for receipt):
                                                <i class="required"> required</i>
                                            </label>
                                            <div class="col-sm-8 show-error">
                                                <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}">
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>
                            @endif
                        @endif
                            
                            <div class="row" style="padding:20px">
                                <hr><label class="mt-checkbox"><input type="checkbox" id="accept_terms" value="1"/>
                                    I ACCEPT THE TERMS AND CONDITIONS. <a data-toggle="modal" href="#modal_terms_conditions">CLICK HERE TO VIEW TERMS AND CONDITIONS.</a>
                                <span></span></label><br>
                                <label class="mt-checkbox"><input type="checkbox" checked="true" id="accept_newsletter" value="1"/>
                                    SIGN UP FOR OUR NEWSLETTER
                                <span></span></label><br>
                                <center><button type="button" id="btn_process" disabled="true" class="btn btn-primary btn-lg uppercase">Process payment <i class="fa fa-arrow-circle-right"></i></button></center>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAYMENT -->
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
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="/js/production/general/share_tickets.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/index.js" type="text/javascript"></script>
@endsection