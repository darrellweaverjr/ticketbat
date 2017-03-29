@php $page_title='Purchases' @endphp
@extends('layouts.admin')
@section('title', 'Purchases' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/summernote.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, re-send email, view tickets and change status (Only for admin purposes. By default the last 30 days.)</small>
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
                        <div class="btn-group">
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                            <button id="btn_model_search" class="btn sbold grey-salsa">Search
                                <i class="fa fa-search"></i>
                            </button>
                            <button id="btn_model_email" class="btn sbold bg-green" disabled="true">Email Customers 
                                <i class="fa fa-envelope"></i>
                            </button>
                            <button id="btn_model_tickets" class="btn sbold bg-yellow" disabled="true">View Tickets 
                                <i class="fa fa-ticket"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                            <button id="btn_model_note" class="btn sbold bg-red" disabled="true">Add Note 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_move" class="btn sbold bg-purple" disabled="true">Move to Event
                                <i class="fa fa-arrow-circle-right"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_model .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="1%"></th>
                                <th width="47%">Purchase Info</th>
                                <th width="18%">Show/Venue</th>
                                <th width="8%">Show Time</th>
                                <th width="8%">Purchase Time</th>
                                <th width="5%">Amount</th>
                                <th width="11%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_color = '0' @endphp
                            @foreach($purchases as $index=>$p)
                                @php $color = substr(dechex(crc32($p->color)),0,6) @endphp
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$p->id}}" value="{{$p->email}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="1%" style="background-color:#{{$color}}"></td>  
                                <td class="search-item clearfix" width="47%"> 
                                    <div class="search-content" >       
                                        <b class="search-title"><a data-toggle="modal" href="#modal_details_{{$p->id}}">@if($p->card_holder) {{$p->card_holder}} @else {{$p->first_name}} {{$p->last_name}} @endif</a></b>
                                        <br><small><i>Email: <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a> ID: <a>{{$p->id}}</a> Tickets: <a>{{$p->quantity}}</a> Ticket Type: <a>{{$p->ticket_type_type}}</a> Package: <a>{{$p->title}}</a> 
                                        @if($previous_color != $color) <br> Ret.Price: <a>${{number_format($p->retail_price,2)}}</a> Fees: <a>${{number_format($p->processing_fee,2)}}</a> Commiss.: <a>${{number_format($p->commission_percent,2)}}</a> 
                                        Savings: <a>${{number_format($p->savings,2)}}</a> Method: <a>@if($p->ticket_type=='Consignment'){{$p->ticket_type}}@else{{$p->payment_type}}@endif</a> @endif
                                        <br><b>NOTE: </b><span id="note_{{$p->id}}">@php echo $p->note @endphp<span></i></small>
                                    </div>
                                </td>
                                <td width="18%"><center>{{$p->show_name}}<br>at<br>{{$p->venue_name}}</center></td>
                                <td width="8%" data-order="{{strtotime($p->show_time)}}"><center>{{date('m/d/Y',strtotime($p->show_time))}}<br>{{date('g:ia',strtotime($p->show_time))}}</center></td>
                                <td width="8%" data-order="{{strtotime($p->created)}}"><center>{{date('m/d/Y',strtotime($p->created))}}<br>{{date('g:ia',strtotime($p->created))}}</center></td>
                                <td width="5%" style="text-align:right"> 
                                    @if($previous_color != $color) @if($p->amount > 0) $ {{number_format($p->amount,2)}} @else @php echo '(Comp)' @endphp @endif @endif
                                </td>
                                <td width="11%"> 
                                    @if(in_array('Edit',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                                    <select ref="{{$p->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $p->status) selected @endif value="{{$indexS}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                    @else <center>{{$p->status}}</center> @endif
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
                                                                    <br> Phone: <b>{{$p->phone}}</b>
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
                                                                    <div class="col-md-6"> Payment: <b>@if($p->ticket_type=='Consignment'){{$p->ticket_type}}@else{{$p->payment_type}}@endif</b><br> Card: <b>...{{$p->last_4}}</b></div> 
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Accounting Info</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body" style="height:50px"> 
                                                                    <div class="col-md-6"> Retail Price: <b>${{number_format($p->retail_price,2)}}</b><br> Fees: <b>${{number_format($p->processing_fee,2)}}</b><br> Commission: <b>${{number_format($p->commission_percent,2)}}</b></div>
                                                                    <div class="col-md-6"> Savings: <b>${{number_format($p->savings,2)}}</b><br> Amount: <b>${{number_format($p->amount,2)}}</b></div>
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
    <!-- BEGIN SEARCH MODAL--> 
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:470px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Search Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/purchases" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Venue:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="venue" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['venues'] as $index=>$v)
                                                <option @if($v->id==$search['venue']) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>   
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="show" style="width: 321px !important" data-content='@php echo str_replace("'"," ",json_encode($search['shows']));@endphp'>
                                                <option selected value="">All</option>
                                                @foreach($search['shows'] as $index=>$s)
                                                    @if($s->venue_id == $search['venue'] || $s->id==$search['show'])
                                                    <option @if($s->id==$search['show']) selected @endif value="{{$s->id}}">{{$s->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show Time:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="show_times_date">
                                            <input type="text" class="form-control" name="showtime_start_date" value="{{$search['showtime_start_date']}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="showtime_end_date" value="{{$search['showtime_end_date']}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_show_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-3">Sold Date:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="sold_times_date">
                                            <input type="text" class="form-control" name="soldtime_start_date" value="{{$search['soldtime_start_date']}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="soldtime_end_date" value="{{$search['soldtime_end_date']}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_sold_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_search').trigger('reset')">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa" onclick="$('#modal_model_search').modal('hide'); swal({
                                                                                                    title: 'Searching information',
                                                                                                    text: 'Please, wait.',
                                                                                                    type: 'info',
                                                                                                    showConfirmButton: false
                                                                                                });" >Search</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END SEARCH MODAL--> 
    <!-- BEGIN EMAIL MODAL--> 
    <div id="modal_model_email" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Custom Email</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_email">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Send to
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-10 show-error">
                                        <label class="col-md-4 mt-radio mt-radio-single mt-radio-outline">Search Result
                                            <input type="radio" name="sendto" checked="true" value="0" />
                                            <span></span>
                                        </label>
                                        <label class="col-md-4 mt-radio mt-radio-single mt-radio-outline">Selected Purchases
                                            <input type="radio" name="sendto" value="1" />
                                            <span></span>
                                        </label>
                                    </div>
                                </div><br><br>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Subject
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-10 show-error">
                                        <input class="form-control" name="subject" type="text" required="true"/>
                                    </div>
                                </div><br><br>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Message
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-10 show-error">
                                        <textarea class="form-control" name="body" rows="10" required="true"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_send_custom" class="btn sbold bg-green">Send</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END EMAIL MODAL--> 
    <!-- BEGIN MOVE MODAL--> 
    <div id="modal_model_move" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Move to Event</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_move">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="purchase_id" value="" />
                        <input type="hidden" name="ticket_id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-7">Select the showtime to move the current purchase:</label>
                                    <div class="col-md-5 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="show_time_id" required="true"> 
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <hr><table class="table table-striped table-hover table-bordered" >
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Ticket Type</th>
                                            <th>Ret.Price</th>
                                            <th>P.Fee($)</th>
                                            <th>P.Fee(%)</th>
                                            <th>Commis.($)</th>
                                            <th>Commis.(%)</th>
                                            <th>Max</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_purchase_tickets">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold purple">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END MOVE MODAL--> 
@endsection

@section('scripts') 
<script src="{{config('app.theme')}}js/summernote.min.js" type="text/javascript"></script>
<script src="/js/admin/purchases/index.js" type="text/javascript"></script>
@endsection