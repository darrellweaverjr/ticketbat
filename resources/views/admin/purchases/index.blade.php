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
                            <button id="btn_model_note" class="btn sbold bg-red" disabled="true"> Add Note 
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="1%"> </th>
                                <th width="71%"> Purchase Info </th>
                                <th width="10%"> Purchase Time </th>
                                <th width="5%"> Amount </th>
                                <th width="11%"> Status </th>
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
                                
                                <td class="search-item clearfix" width="71%"> 
                                    <div class="search-content" >
                                        <b class="search-title"><a>@if($p->card_holder) {{$p->card_holder}} @else {{$p->first_name}} {{$p->last_name}} @endif</a></b>
                                        <p><small><i>Customer email: <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a> Referrer Url: <a href="{{$p->referrer_url}}" target="_blank">{{$p->referrer_url}}</a><br>
                                        Purchase: <a>{{$p->id}}</a> Tickets: <a>( {{$p->quantity}} ) {{$p->ticket_type}}</a> Venue: <a>{{$p->venue}}</a> Date/Time: <a>{{date('Y-m-d g:ia',strtotime($p->show_time))}}</a> Coupon: <a>{{$p->code}}</a><br>
                                        Transaction: <a>{{$p->transaction_id}}</a> AuthCode: <a>{{$p->authcode}}</a> RefNum: <a>{{$p->refnum}}</a> Payment: <a>{{$p->payment_type}}</a> Card: <a>...{{$p->last_4}}</a> Retail Price: <a>$ {{number_format($p->retail_price,2)}}</a> Fees: <a>$ {{number_format($p->processing_fee,2)}}</a> Commission: <a>{{number_format($p->commission_percent,2)}}%</a> Savings: <a>$ {{number_format($p->savings,2)}}</a>
                                        @if($p->note)<span id="note_{{$p->id}}"><br>Note: <b>{{$p->note}}</b><span> @endif</i></small></p>
                                    </div>
                                </td>
                                <td width="10%"><center> {{date('Y-m-d g:ia',strtotime($p->created))}} </center></td>
                                <td width="5%" style="text-align:right"> 
                                    @if($previous_session_id != $p->session_id)
                                    @if($p->transaction_id) $ {{number_format($p->price_paid,2)}}
                                        @else (Comp) @endif
                                    @endif
                                </td>
                                <td width="11%"> 
                                    <select ref="{{$p->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $p->status) selected @endif value="{{$indexS}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </td> 
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