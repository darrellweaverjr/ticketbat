@php $page_title=$event->name.' at '.$event->venue @endphp
@extends('layouts.production')
@section('title')
    {!! $page_title !!}
@stop
@section('styles')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    @if($ticket_types_css)<style>{{$ticket_types_css}}</style>@endif
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

    <!-- BEGIN NAME BAR-->
    <div class="row widget-row event-top-row">
        <div class="col-lg-12 ml-15 mr-15">
            <div class="row fixed-panel event-main-title">
                <div class="col-xs-12">
                    <p class="event-name">{{$event->name}}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- END NAME BAR-->
    <div class="page-content color-panel " style="padding-top: 30px">
        <div class="row fixed-panel">
            <div class="col-xs-12 col-sm-5 col-md-6">
                <div class="portlet light event-seating">
                    <!-- BEGIN STAGE -->
                    <h4>
                        <i class="fa fa-street-view"></i> Seating Chart
                    </h4>
                    <div id="stage_images" class="p-15 text-center">
                        <img class="img-responsive" src="{{$event->image_url}}" data-type="default"/>
                        @foreach($event->stage_images as $i)
                            <br>
                            <img class="img-responsive" style="display:none;" src="{{$i->url}}" data-type="{{$i->ticket_type}}"/>
                        @endforeach
                    </div>
                    <!-- END STAGE -->
                </div>
            </div>
            <div class="col-xs-12 col-sm-7 col-md-6">
                <form method="post" id="form_model_update" class="form-horizontal">
                    <input name="show_time_id" type="hidden" value="{{$event->show_time_id}}"/>
                    <input name="password" type="hidden" value=""/>
                    <div class="portlet light about-text">
                        <h4>
                            <i class="fa fa-ticket"></i> Tickets
                        </h4>
                        <div class="portlet-body p-15">
                            <h4 center="text-center">{{$event->show_time}}</h4>
                            @if(!empty($event->ticket_limit))
                                <div class="alert alert-danger display-block text-center" style="margin-bottom:-0px">
                                    You can buy only {{$event->ticket_limit}} ticket(s) for this event.
                                </div>
                            @endif
                            @if(!empty($event->ticket_limit) && empty($event->ticket_left))
                                <div class="bg-yellow bg-font-red text-center">
                                    <hr>
                                    <h1>You have reach the limit of tickets for this event. Please, proceed to checkout.</h1>
                                    <br>
                                </div>
                            @elseif(empty($event->for_sale))
                                <div class="bg-red bg-font-red">
                                    <hr>
                                    <h1 class="text-center">Tickets are currently not for sale.</h1>
                                    <br>
                                </div>
                            @else
                                <div class="panel-group accordion" id="tickets_accordion">
                                    <!-- BEGIN TICKETS -->
                                    @php $selected = true @endphp
                                    @foreach($event->tickets as $index=>$t)
                                        <div class="panel panel-default">
                                            <div class="panel-heading p-3">
                                                <h4 class="panel-title {{$t['class']}} event-ticket-type">
                                                    <a class="accordion data-type="{{$t['type']}}" data-toggle="collapse" data-parent="#tickets_accordion"
                                                    href="#collapse_{{$index}}"> <strong class="lh-25">{{$t['type']}}</strong> </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_{{$index}}" class="panel">
                                                <div class="panel-body" style="margin-bottom: -20px">
                                                    @if(!empty($t['amex_only']))
                                                        <div class="alert alert-danger display-block text-center">
                                                            These tickets can only purchased with an American Express Card:
                                                        </div>
                                                    @endif
                                                    <div class="mt-radio-list">
                                                        @foreach($t['tickets'] as $tt)
                                                            @if($tt->max_available>0)   
                                                                <label class="mt-radio mt-radio-outline">      
                                                                    <input type="radio" name="ticket_id" @if($selected) class="default_radio" @endif data-pass="{{$t['password']}}" data-price="{{$tt->retail_price}}"
                                                                           data-max="{{$tt->max_available}}" value="{{$tt->ticket_id}}">
                                                                    @if($tt->retail_price>0)
                                                                        ${{$tt->retail_price}}
                                                                    @else
                                                                        <b class="label label-sm sbold label-success">FREE</b>
                                                                    @endif
                                                                    @if($tt->title!='None')- {{$tt->title}} @endif  
                                                                    @if(!empty($tt->in_stock))
                                                                        <b class="label label-sm sbold label-danger">Only {{$tt->in_stock}} ticket(s) left!</b>
                                                                    @endif
                                                                    @if($tt->coupon>0)
                                                                        <b class="label label-sm sbold label-warning" style="color:black"><i class="icon-trophy theme-font"></i> Applies discount</b>
                                                                    @endif
                                                                    <span></span>
                                                                </label>
                                                                @php $selected = false @endphp
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                                <!-- END TICKETS -->
                                </div>
                            @endif
                        </div>
                    @if(!empty($event->for_sale) && !empty($event->tickets))
                        <!-- BEGIN TOTALS -->
                            <div class="row ml-15 mr-15 mt-5">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">QTY</label>
                                        <select class="form-control col-md-3" name="qty" data-price="" style="width:65px"></select>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 text-right">TOTAL</label>
                                        <label id="totals" class="control-label col-md-6 text-center" style="font-size:22px">$ 0.00</label>
                                    </div>
                                </div>
                            </div>
                            <!-- END TOTALS -->
                        @endif
                    </div>
                @if(!empty($event->for_sale) && !empty($event->tickets))
                    <!-- BEGIN ADD TO -->
                        <div class="portlet">
                            <!-- BEGIN DESCRIPTION -->
                            <div class="event-add-to-cart">
                                @if($has_coupon>0)
                                    <div class="mt-element-ribbon" style="max-height:100px">
                                        <div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-warning uppercase">
                                            <div class="ribbon-sub ribbon-clip ribbon-right"></div>
                                            <i class="icon-trophy theme-font"></i> Congratulations!
                                        </div>
                                        <p class="ribbon-content text-center">
                                            You have a coupon!<br>Add a ticket to your cart and go to the check out page to see your total price.
                                        </p>
                                    </div>
                                @endif
                                <a id="btn_add_shoppingcart" class="btn btn-green-white btn-block btn-lg uppercase"><i class="fa fa-plus-square"></i> Add to cart</a>
                            </div>
                        </div>
                        <!-- END ADD TO -->
                @endif
                <!-- BEGIN SHORTCUT FORM -->
                    <div class="portlet" id="continue_buy_checkout_msg" style="display:none">
                        <div class="row">
                            <div class="col-xs-7 col-sm-7 col-md-6">
                                <a href="/home" class="btn btn-info btn-block btn-lg uppercase event-continue"><i class="fa fa-suitcase"></i> Continue shopping </a>
                            </div>
                            <div class="col-xs-5 col-sm-5 col-md-6">
                                <a href="/shoppingcart/viewcart" class="btn btn-success btn-block btn-lg uppercase event-checkout"><i class="fa fa-shopping-cart"></i> Checkout </a>
                            </div>
                        </div>
                    </div>
                    <!-- END SHORTCUT FORM -->
                </form>
            </div>
        </div>
        <!-- END DESCRIPTION AND CALENDAR -->
    </div>
@endsection

@section('scripts')
    <script src="/js/production/events/buy.js" type="text/javascript"></script>
@endsection
