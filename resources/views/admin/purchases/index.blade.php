@php $page_title='Purchases' @endphp
@extends('layouts.admin')
@section('title', 'Purchases' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, re-send email, view tickets and change status (Only for admin purposes)</small>
    </h1>
    <!-- END PAGE TITLE-->    
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} LIST </span>
                    </div>
                    <div class="actions"> 
                        <div id="start_end_date" class="pull-left tooltips btn btn-sm" data-container="body" data-placement="bottom">
                            <i class="icon-calendar"></i>&nbsp;
                            <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="btn-group">
                            <button id="btn_model_email" class="btn sbold bg-green" disabled="true"> Email Receipt 
                                <i class="fa fa-envelope"></i>
                            </button>
                            <button id="btn_model_tickets" class="btn sbold bg-yellow" disabled="true"> View Tickets 
                                <i class="fa fa-ticket"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable dt-responsive" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="1%"> </th>
                                <th width="20%"> Cardholder </th>
                                <th width="30%"> Show </th>
                                <th width="13%"> Purchase Time </th>
                                <th width="13%"> Show Time </th>
                                <th width="9%"> Amount </th>
                                <th width="12%"> Status </th>
                                <th class="none">Id Purchase: </th>
                                <th class="none">Id Transaction: </th>
                                <th class="none">Ticket type: </th>
                                <th class="none">Retail Price: </th>
                                <th class="none">Processing fee: </th>
                                <th class="none">Commission: </th>
                                <th class="none">Savings: </th>
                                <th class="none">Payment type: </th>
                                <th class="none">Last 4 Digit Credit Card: </th>
                                <th class="none">Authcode: </th>
                                <th class="none">Refnum: </th>
                                <th class="none">Referrer: </th>
                                <th class="none">Venue: </th>
                                <th class="none">Coupon Code: </th>
                                <th class="none">Customer Email: </th>
                                <th class="none">Note: </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_session_id = '0' @endphp
                            @foreach($purchases as $index=>$p)
                            <tr>
                                <td width="2%">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$p->id}}" value="{{$p->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="1%" style="background-color:#{{substr(dechex(crc32($p->session_id)),0,6)}}"></td>
                                <td width="20%"> @if($p->card_holder) {{$p->card_holder}} @else {{$p->first_name}} {{$p->last_name}} @endif </td>
                                <td width="30%"> {{$p->name}} </td>
                                <td width="13%"> {{date('Y-m-d g:ia',strtotime($p->created))}} </td>
                                <td width="13%"> {{date('Y-m-d g:ia',strtotime($p->show_time))}} </td>
                                <td width="9%"> 
                                    @if($previous_session_id != $p->session_id)
                                        @if($p->transaction_id) $ {{number_format($p->price_paid,2)}} 
                                        @else (Comp) @endif
                                    @endif
                                </td>
                                <td width="12%"> 
                                    <select ref="{{$p->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $p->status) selected @endif value="{{$indexS}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </td> 
                                <td> {{$p->id}} </td>
                                <td> {{$p->transaction_id}} </td>
                                <td> {{$p->ticket_type}} </td>
                                <td> $ {{number_format($p->retail_price,2)}} </td>
                                <td> $ {{number_format($p->processing_fee,2)}} </td>
                                <td> {{number_format($p->commission_percent,2)}}% </td>
                                <td> $ {{number_format($p->savings,2)}} </td>
                                <td> {{$p->payment_type}} </td>
                                <td> ...{{$p->last_4}} </td>
                                <td> {{$p->authcode}} </td>
                                <td> {{$p->refnum}} </td>
                                <td> {{$p->referrer_url}} </td>
                                <td> {{$p->venue}} </td>
                                <td> {{$p->code}} </td>
                                <td> {{$p->email}} </td>
                                <td> {{$p->note}} </td>
                            </tr>
                                @php $previous_session_id = $p->session_id @endphp
                            @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->   
    <!-- BEGIN UPDATE MODAL--> 
    <div style="display: none;">
        <form method="post" action="/admin/purchases" id="form_model_update">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <input name="start_date" type="hidden" value="{{$start_date}}"/>
            <input name="end_date" type="hidden" value="{{$end_date}}"/>
        </form>   
    </div>  
    <!-- END UPDATE MODAL--> 
@endsection

@section('scripts') 
<script src="/js/admin/purchases/index.js" type="text/javascript"></script>
@endsection