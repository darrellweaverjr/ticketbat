@php $page_title='Refunds' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List and edit refunds.</small>
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
                        @if(in_array('Other',Auth::user()->user_type->getACLs()['REFUNDS']['permission_types']))
                        <div id="start_end_date" class="pull-left tooltips btn btn-sm" data-container="body" data-placement="bottom">
                            <i class="icon-calendar"></i>&nbsp;
                            <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="btn-group">
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model_refunded">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="1%"></th>
                                <th width="39%">Purchase Info</th>
                                <th width="39%">Refund Info</th>
                                <th width="9%">Amount</th>
                                <th width="10%">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_color = '0' @endphp
                            @foreach($refunds as $index=>$p)
                                @php $color = substr(dechex(crc32($p->order_id)),0,6) @endphp
                            <tr>
                                <td>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$p->id}}" value="{{$p->id}} ({{$p->order_id}})" />
                                        <span></span>
                                    </label>
                                </td>
                                <td style="text-align:center;background-color:#{{$color}};border-top:thick solid @if($previous_color==$color) #{{$color}} @else #ffffff @endif !important;">
                                    <i class="fa fa-shopping-cart"></i>
                                </td>
                                <td class="search-item clearfix">
                                    @if($previous_color != $color)
                                    <div class="search-content" >
                                        <b class="search-title">
                                            <i class="fa fa-ticket"></i> {{$p->first_name}} {{$p->last_name}}, <small><i> <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></i></small>
                                            @if($p->first_name != $p->u_first_name || $p->last_name != $p->u_last_name || $p->email != $p->email) <br><i class="fa fa-user"></i> {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> @endif
                                            @if($p->card_holder && $p->card_holder != $p->first_name.' '.$p->last_name) <br><i class="fa fa-credit-card"></i> {{$p->card_holder}}@endif
                                        </b>
                                        <br><small><i>AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>, ID: <b>{{$p->order_id}}</b>, Qty: <b>{{$p->quantity}}</b>, T.Type: <b>{{$p->ticket_type_type}}</b>, Pkg: <b>{{$p->title}}</b>,
                                        <br> Ret.Price: <b>${{number_format($p->retail_price,2)}}</b>, Fees: <b>${{number_format($p->processing_fee,2)}}</b>, Commiss.: <b>${{number_format($p->commission_percent,2)}}</b>, Savings: <b>${{number_format($p->savings,2)}}</b>, Print.Fee: <b>${{number_format($p->printed_fee,2)}}</b>,
                                        <br> Method: <b>{{$p->method}}</b> , Status: <b>{{$p->status}}</b>
                                        </i></small>
                                        @if(!empty(trim($p->note)))<div class="note note-info" style="font-style:italic;font-size:smaller">@php echo trim($p->note) @endphp</div>@endif
                                    </div>
                                    @endif
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-content" >
                                        <b class="search-title">
                                            <i class="fa fa-user"></i> Refunded by: {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> 
                                        </b>
                                        <br><small><i>ID: <b>{{$p->id}}</b>, AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>,  IsDuplicate: <b>{{$p->is_duplicate}}</b>, 
                                        <br> <span class="label label-sm sbold @if($p->result=='Approved') label-success @else label-danger @endif"> Result: <b>({{$p->result_code}}) {{$p->result}}</b>, Error: <b>({{$p->error_code}}) {{$p->error}}</b> </span>
                                        </i></small>
                                        @if(!empty(trim($p->description)))<div class="note note-info" style="font-style:italic;font-size:smaller">@php echo trim($p->description) @endphp</div>@endif
                                    </div>
                                </td>
                                <td>${{number_format($p->amount,2)}}</td>
                                <td><center>{{date('m/d/Y g:ia',strtotime($p->created))}}</center></td>
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
    <!-- BEGIN UPDATE MODAL-->
    <div style="display: none;">
        <form method="post" action="/admin/refunds" id="form_model_search">
            <input type="hidden" name="_token" value="{{ Session::token() }}" />
            <input name="start_date" type="hidden" value="{{$start_date}}"/>
            <input name="end_date" type="hidden" value="{{$end_date}}"/>
        </form>
    </div>
    <!-- END UPDATE MODAL-->
    <!-- BEGIN EDIT MODAL-->
    <div id="modal_model_edit" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-yellow">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Edit Refund</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_edit" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row" style="padding-left:15px">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Refund date</label>
                                    <div class="col-md-3 show-error">
                                        <div id="refund_date_input" class="input-group input-large date form_datetime dtpicker">
                                            <input size="16" readonly="" class="form-control" type="text" name="created" value="{{date('n/d/Y g:i A')}}">
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
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
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-yellow">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT MODAL-->
    
@endsection

@section('scripts')
<script src="/js/admin/refunds/index.js" type="text/javascript"></script>
@endsection
