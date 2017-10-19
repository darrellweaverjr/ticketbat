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
                    <label>You currently have <b>{{$cart['quantity']}}</b> @if($cart['quantity']>1) items @else item @endif</label>
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
                            <td style="max-width:100px">
                                <input type="number" data-qty="{{$i->number_of_items}}" value="{{$i->number_of_items}}" min="1" @if($i->available_qty<0) max="1000" @else max="{{$i->available_qty}}" @endif name="qty[{{$i->id}}]" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0">
                            </td>
                            <td style="text-align:right">${{number_format($i->cost_per_product,2)}}</td>
                            <td style="text-align:right">${{number_format($i->cost_per_product*$i->number_of_items,2)}}</td>
                            <td style="text-align:right">${{number_format($i->processing_fee,2)}}</td>
                            <td><center><a href="button" class="btn btn-info"><i class="fa fa-share icon-share"></i></a></center></td>
                            <td><center><button type="button" class="btn btn-danger"><i class="fa fa-remove icon-ban"></i></button></center></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table><hr>
            </div>
            <div class="row portlet-body light portlet-fit" style="margin-top:-30px;padding:10px">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="coupon_code" placeholder="" name="coupon">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" id="add_coupon_code">Add coupon</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-6 text-right" style="padding-right:20px;">
                    <h4><label class="label label-default bold">Subtotal: </label><label id="cost_subtotal" class="font-blue-madison">$ {{number_format($cart['retail_price'],2)}}</label></h4>
                    <h4><label class="label label-info bold">Processing fee: </label><label id="cost_fees" class="font-blue-madison">$ {{number_format($cart['processing_fee'],2)}}</label></h4>
                    <h4><label class="label label-success bold">Savings: </label><label id="cost_savings" class="font-blue-madison">$ {{number_format($cart['savings'],2)}}</label></h4>
                    <h4><label class="label label-primary bold">Grand total: </label><label id="cost_total" class="font-blue-madison">$ {{number_format($cart['total'],2)}}</label></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- END ITEMS -->
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
                    </ul>
                    @endif
                    <div class="tab-content">
                        @if(!($cart['total']>0))
                        <div class="tab-pane fade active in" id="tab_skip">
                            <p> 
                                skip payment
                            </p>
                        </div>
                        @else
                            <div class="tab-pane fade active in" id="tab_card">
                                <p> 
                                    pay with card
                                    <a data-toggle="modal" href="#modal_cvv">cvv.</a>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Phone:</label>
                                            <div class="col-sm-9 show-error">
                                                <div class="input-group">
                                                    <input type="text" name="first_name" class="form-control input-large" value="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 text-right">Email (for receipt):
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-sm-9 show-error">
                                                <div class="input-group">
                                                    <input type="email" name="email" class="form-control input-large" value="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </p>
                            </div>
                            @if($cart['seller'])
                            <div class="tab-pane fade" id="tab_swipe">
                                <p>
                                    swipe card
                                </p>
                            </div>
                            <div class="tab-pane fade" id="tab_cash">
                                <p>
                                    pay cash
                                </p>
                            </div>
                            @endif
                        @endif
                        <p class="margin-top-20">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-sm-3 text-right">Customer:
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-sm-9 show-error">
                                        <div class="input-group">
                                            <input type="text" name="customer" class="form-control input-large" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 text-right">Phone:</label>
                                    <div class="col-sm-9 show-error">
                                        <div class="input-group">
                                            <input type="text" name="phone" class="form-control input-large" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 text-right">Email (for receipt):
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-sm-9 show-error">
                                        <div class="input-group">
                                            <input type="email" name="email" class="form-control input-large" value="{{$cart['email']}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding:20px">
                                <hr><label class="mt-checkbox"><input type="checkbox" name="terms" value="1" />
                                    I ACCEPT THE TERMS AND CONDITIONS. <a data-toggle="modal" href="#modal_terms_conditions">CLICK HERE TO VIEW TERMS AND CONDITIONS.</a>
                                <span></span></label><br>
                                <label class="mt-checkbox"><input type="checkbox" checked="true" name="NEWSLETTER" value="1" />
                                    SIGN UP FOR OUR NEWSLETTER
                                <span></span></label><br>
                                <center><button type="submit" id="btn_process" disabled="true" class="btn btn-primary btn-lg uppercase">Process payment <i class="fa fa-arrow-circle-right"></i></button></center>
                            </div>
                        </p>
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

@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/datatables.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/datatables.bootstrap.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="/js/production/shoppingcart/index.js" type="text/javascript"></script>
@endsection