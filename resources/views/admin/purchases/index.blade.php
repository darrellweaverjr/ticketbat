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
                                <th width="41%"> Purchase Info </th>
                                <th width="20%"> Show/Venue </th>
                                <th width="10%"> Show Time </th>
                                <th width="10%"> Purchase Time </th>
                                <th width="5%"> Amount </th>
                                <th width="11%"> Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_color = '0' @endphp
                            @foreach($purchases as $index=>$p)
                                @if($p->transaction_id && is_numeric($p->transaction_id)) 
                                    @php $color = substr(dechex(crc32($p->transaction_id)),0,6) @endphp
                                    @php $transaction = true @endphp
                                @else 
                                    @php $color = substr(dechex(crc32($p->user_id.floor($p->price_paid*100))),0,6) @endphp
                                    @php $transaction = false @endphp
                                @endif
                            <tr>
                                <td width="2%">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$p->id}}" value="{{$p->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="1%" style="background-color:#{{$color}}"></td>  
                                <td class="search-item clearfix" width="41%"> 
                                    <div class="search-content" >       
                                        <b class="search-title"><a data-toggle="modal" href="#modal_details_{{$p->id}}">@if($p->card_holder) {{$p->card_holder}} @else {{$p->first_name}} {{$p->last_name}} @endif</a></b>
                                        <br><small><i>Email: <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a> ID: <a>{{$p->id}}</a> Tickets: <a>{{$p->quantity}}</a> Ticket Type: <a>{{$p->ticket_type_type}}</a> Package: <a>{{$p->title}}</a> 
                                        @if($previous_color != $color) <br> Retail Price: <a>$ {{number_format($p->retail_price,2)}}</a> Fees: <a>$ {{number_format($p->processing_fee,2)}}</a> Commission: <a>{{number_format($p->commission_percent,2)}}%</a> Savings: <a>$ {{number_format($p->savings,2)}}</a> @endif
                                        <br><b>NOTE: </b><span id="note_{{$p->id}}">@php echo $p->note @endphp<span></i></small>
                                    </div>
                                </td>
                                <td width="20%"><center> {{$p->show_name}}<br>at<br>{{$p->venue_name}} </center></td>
                                <td width="10%"><center> {{date('m/d/Y',strtotime($p->show_time))}}<br>{{date('g:ia',strtotime($p->show_time))}} </center></td>
                                <td width="10%"><center> {{date('m/d/Y',strtotime($p->created))}}<br>{{date('g:ia',strtotime($p->created))}} </center></td>
                                <td width="5%" style="text-align:right"> 
                                    @if($previous_color != $color) @if($transaction) $ {{number_format($p->price_paid,2)}} @else @php echo '(Comp)' @endphp @endif @endif
                                </td>
                                <td width="11%"> 
                                    <select ref="{{$p->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $p->status) selected @endif value="{{$indexS}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </td> 
                            </tr>
                            <!-- BEGIN DETAILS MODAL--> 
                            <div id="modal_details_{{$p->id}}" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog">
                                    <div class="modal-content portlet">
                                        <div id="modal_model_update_header" class="modal-header">
                                            <h4 class="modal-title bold uppercase"><center>Purchase Details</center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    <ul class="chats">
                                                        <li class="in">
                                                            <div class="avatar">Client Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="margin-left:15px"> 
                                                                    Name: <b>@if($p->card_holder) {{$p->card_holder}} @else {{$p->first_name}} {{$p->last_name}} @endif</b> 
                                                                    <br> Email: <b><a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></b>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Event Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="margin-left:15px"> Show: <b>{{$p->show_name}}</b><br> Time: <b>{{date('m/d/Y g:ia',strtotime($p->show_time))}}</b><br> Venue: <b>{{$p->venue_name}}</b>  </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Purchase Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="height:50px"> 
                                                                    <div class="col-md-6"> ID: <b>{{$p->id}}</b><br> Qty: <b>{{$p->quantity}}</b><br> Ticket Type: <b>{{$p->ticket_type_type}}</b></div>
                                                                    <div class="col-md-6"> Status: <b>{{$p->status}}</b><br> Package: <b>{{$p->title}}</b><br> Coupon: <b>{{$p->code}}</b></div> 
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Transaction Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="height:50px"> 
                                                                    <div class="col-md-6"> ID:<b>{{$p->transaction_id}}</b><br> AuthCode: <b>{{$p->authcode}}</b><br> RefNum: <b>{{$p->refnum}}</b></div>
                                                                    <div class="col-md-6"> Payment: <b>{{$p->payment_type}}</b><br> Card: <b>...{{$p->last_4}}</b></div> 
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Accounting Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="height:50px"> 
                                                                    <div class="col-md-6"> Retail Price: <b>${{number_format($p->retail_price,2)}}</b><br> Fees: <b>${{number_format($p->processing_fee,2)}}</b><br> Commission: <b>{{number_format($p->commission_percent,2)}}%</b></div>
                                                                    <div class="col-md-6"> Savings: <b>${{number_format($p->savings,2)}}</b><br> Amount: <b>${{number_format($p->price_paid,2)}}</b></div>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Referrer Url</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="margin-left:15px"> <a href="{{$p->referrer_url}}" target="_blank">{{$p->referrer_url}}</a> </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Notes</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="margin-left:15px"> @php echo $p->note @endphp </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END DETAILS MODAL--> 
                            @php $previous_color = $color @endphp
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
        <form method="post" action="/admin/purchases" id="form_model_search">
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