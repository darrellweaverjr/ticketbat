@php $page_title='Purchases' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/summernote.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List, re-send email, view tickets and change status (Only for admin purposes. By default the last 7 days.)</small>
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
                            <button id="btn_model_search" class="btn sbold grey-salsa">Filter
                                <i class="fa fa-filter"></i>
                            </button>
                            <button id="btn_model_email" class="btn sbold bg-green" disabled="true">Email
                                <i class="fa fa-envelope"></i>
                            </button>
                            <button id="btn_model_tickets" class="btn sbold bg-yellow" disabled="true">Tickets
                                <i class="fa fa-ticket"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                            <button id="btn_model_note" class="btn sbold bg-red" disabled="true">Note
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_model_edit" class="btn sbold bg-purple" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_share" class="btn sbold bg-purple" disabled="true">Share
                                <i class="fa fa-share"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model"
                           data-status="{{json_encode($search['status'],true)}}"
                           @if(in_array('Edit',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types'])) data-edit="1" @else data-edit="0" @endif>
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
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$p->id}}" data-qty="{{$p->quantity}}" value="{{$p->email}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td title="Click here to see details" class="modal_details_view"
                                    data-id="{{$p->id}}" style="text-align:center;background-color:#{{$color}};border-top:thick solid @if($previous_color==$color) #{{$color}} @else #ffffff @endif !important;">
                                    <i class="fa fa-search"></i>
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-content" >
                                        @if($previous_color != $color)
                                        <b class="search-title">
                                            <i class="fa fa-ticket"></i> {{$p->first_name}} {{$p->last_name}}, <small><i> <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></i></small> @if($p->shared) &emsp;<small><i><span class="fa fa-share"></span> ticket(s) shared with {{$p->shared}} people</i></small> @endif
                                            @if($p->first_name != $p->u_first_name || $p->last_name != $p->u_last_name || $p->email != $p->email) <br><i class="fa fa-user"></i> {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> @endif
                                            @if($p->card_holder && $p->card_holder != $p->first_name.' '.$p->last_name) <br><i class="fa fa-credit-card"></i> {{$p->card_holder}}@endif
                                        </b>
                                        <br><small><i>Method: <b>{{$p->method}}</b>, @if($p->transaction_id)AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>,@endif</i></small><br>
                                        @endif
                                        <small><i>ID: <b>{{$p->id}}</b>, Qty: <b>{{$p->quantity}}</b>, T.Type: <b>{{$p->ticket_type_type}}</b>, Pkg: <b>{{$p->title}}</b>,
                                        <br> Ret.Price: <b>${{number_format($p->retail_price,2)}}</b>, Fees: <b>${{number_format($p->processing_fee,2)}} @if($p->inclusive_fee>0) (Inclusive) @endif</b>, Commiss.: <b>${{number_format($p->commission_percent,2)}}</b>, Savings: <b>${{number_format($p->savings,2)}}</b>
                                        </i></small>
                                        <div id="note_{{$p->id}}" class="note note-info @if(empty(trim($p->note))) hidden @endif" style="font-style:italic;font-size:smaller">@php echo trim($p->note) @endphp</div>
                                    </div>
                                </td>
                                <td><center>{{$p->show_name}}<br>at<br>{{$p->venue_name}}</center></td>
                                <td data-order="{{strtotime($p->show_time)}}"><center>{{date('m/d/Y',strtotime($p->show_time))}}<br>{{date('g:ia',strtotime($p->show_time))}}</center></td>
                                <td data-order="{{strtotime($p->created)}}"><center>{{date('m/d/Y',strtotime($p->created))}}<br>{{date('g:ia',strtotime($p->created))}}</center></td>
                                <td style="text-align:right">
                                    @if($previous_color != $color) @if($p->amount > 0) $ {{number_format($p->amount,2)}} @elseif($p->method=='Free event') @php echo '(Free event)' @endphp @else @php echo '(Comp)' @endphp @endif @endif
                                </td>
                                <td data-status="{{$p->status}}"><center>{{$p->status}}</center></td>
                            </tr>
                            @php $previous_color = $color @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN DETAILS MODAL -->
    <div id="modal_model_details" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:800px !important;">
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
                                        <span class="body" style="height:50px">
                                            <div class="col-md-6">Customer: <b class="first_name"></b> <b class="last_name"></b> (<b class="customer_id"></b>)<br>Email: <b class="email"></b><br>Phone: <b class="phone"></b></div>
                                            <div class="col-md-6">User: <b class="u_first_name"></b> <b class="u_last_name"></b><br>Email: <b class="u_email"></b><br>Phone: <b class="u_phone"></b></div>
                                        </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Event Info</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body" style="height:50px">
                                            <div class="col-md-6">Show: <b class="show_name"></b><br>Time: <b class="show_time"></b><br>Venue: <b class="venue_name"></b></div>
                                            <div class="col-md-6">Show ID: <b class="show_id"></b><br>ShowTime ID: <b class="show_time_id"></b><br>Venue ID: <b class="venue_id"></b></div>
                                        </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Purchase Info</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body" style="height:50px">
                                            <div class="col-md-6">ID: <b class="id"></b><br>Qty: <b class="quantity"></b><br>Ticket Type: <b class="ticket_type_type"></b></div>
                                            <div class="col-md-6">Status: <b class="status"></b><br>Package: <b class="title"></b><br>Coupon: <b class="code"></b></div>
                                        </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Shared Tickets</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body note tickets" style="margin-left:15px"></span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Transaction Info</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body" style="height:50px">
                                            <div class="col-md-6">ID:<b class="transaction_id"></b><br>AuthCode: <b class="authcode"></b><br>RefNum: <b class="refnum"></b></div>
                                            <div class="col-md-6">Cardholder: <b class="card_holder"></b><br>Payment: <b class="method"></b><br>Card: ...<b class="last_4"></b></div>
                                        </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Accounting Info</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body" style="height:50px">
                                            <div class="col-md-6">Retail Price: <b class="retail_price"></b><br>Fees: <b class="processing_fee"></b><br>Commission: <b class="commission_percent"></b></div>
                                            <div class="col-md-6">Savings: <b class="savings"></b><br>Amount: <b class="amount"></b></div>
                                        </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Referrer Url</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body referrer_url" style="margin-left:15px"></span>
                                    </div>
                                </li>
                                <li class="in">
                                    <div class="avatar">Notes</div>
                                    <div class="message">
                                        <span class="arrow"> </span>
                                        <span class="body note notes" style="margin-left:15px"></span>
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
    <!-- BEGIN INCLUDE FILTER SEARCH-->
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/purchases'])
    <!-- END INCLUDE FILTER SEARCH-->
    <!-- BEGIN EMAIL MODAL -->
    @includeIf('admin.purchases.email')
    <!-- END EMAIL MODAL -->
    <!-- BEGIN EDIT MODAL -->
    @includeIf('admin.purchases.edit')
    <!-- END EDIT MODAL -->
    <!-- BEGIN EDIT MODAL -->
    @includeIf('production.general.share_tickets')
    <!-- END SHARE TICKETS MODAL -->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/summernote.min.js" type="text/javascript"></script>
<script src="/js/production/general/share_tickets.js" type="text/javascript"></script>
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/purchases/index.js" type="text/javascript"></script>
@endsection
