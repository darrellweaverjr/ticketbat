@php $page_title='Purchases pendings to refund' @endphp
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
        <small> - List all purchases pending to refunds in the system.</small>
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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['REFUNDS']['permission_types']))
                            <button id="btn_model_refund" class="btn sbold bg-purple">Chargeback
                                <i class="fa fa-credit-card"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"></th>
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
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$p->id}}" value="{{$p->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td style="text-align:center;background-color:#{{$color}};border-top:thick solid @if($previous_color==$color) #{{$color}} @else #ffffff @endif !important;">
                                    
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-content" >
                                        @if($previous_color != $color)
                                        <b class="search-title">
                                            <i class="fa fa-ticket"></i> {{$p->first_name}} {{$p->last_name}}, <small><i> <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></i></small> 
                                            @if($p->first_name != $p->u_first_name || $p->last_name != $p->u_last_name || $p->email != $p->email) <br><i class="fa fa-user"></i> {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> @endif
                                            @if($p->card_holder && $p->card_holder != $p->first_name.' '.$p->last_name) <br><i class="fa fa-credit-card"></i> {{$p->card_holder}}@endif
                                        </b>
                                        <br><small><i>Method: <b>{{$p->method}}</b>, AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>, </i></small><br>
                                        @endif
                                        <small><i>ID: <b>{{$p->id}}</b>, Qty: <b>{{$p->quantity}}</b>, T.Type: <b>{{$p->ticket_type_type}}</b>, Pkg: <b>{{$p->title}}</b>,
                                        <br> Ret.Price: <b>${{number_format($p->retail_price,2)}}</b>, Fees: <b>${{number_format($p->processing_fee,2)}}</b>, Commiss.: <b>${{number_format($p->commission_percent,2)}}</b>, Savings: <b>${{number_format($p->savings,2)}}</b>
                                        </i></small>
                                        <div id="note_{{$p->id}}" class="note note-info @if(empty(trim($p->note))) hidden @endif" style="font-style:italic;font-size:smaller">@php echo trim($p->note) @endphp</div>
                                    </div>
                                </td>
                                <td><center>{{$p->show_name}}<br>at<br>{{$p->venue_name}}</center></td>
                                <td data-order="{{strtotime($p->show_time)}}"><center>{{date('m/d/Y',strtotime($p->show_time))}}<br>{{date('g:ia',strtotime($p->show_time))}}</center></td>
                                <td data-order="{{strtotime($p->created)}}"><center>{{date('m/d/Y',strtotime($p->created))}}<br>{{date('g:ia',strtotime($p->created))}}</center></td>
                                <td style="text-align:right">${{number_format($p->price_paid,2)}} / ${{number_format($p->amount,2)}}</td>
                                <td><center>{{$p->status}}</center></td>
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
    <!-- BEGIN REFUND MODAL-->
    <div id="modal_model_refund" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Refund Purchase/Transaction</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_refund" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row" style="padding-left:15px">
                                <div class="mt-radio-list">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="type" value="current_purchase" checked="true" />Selected purchase only.
                                        <span></span>
                                    </label><hr>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" class="form-control" name="type" value="full_transaction" />All purchases that matches this transaction.
                                        <span></span>
                                    </label><hr>
                                    <label class="mt-radio mt-radio-single mt-radio-outline col-md-9">
                                        <input type="radio" name="type" value="custom_amount" />Specific amount from this selected purchase:
                                        <span></span>
                                    </label>
                                    <div class="col-md-3">
                                    <input type="number" class="form-control" name="amount" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding:15px">
                                <label class="control-label">Description:</label>
                                <div class="show-error">
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="row" style="padding-left:15px">
                                <div class="mt-radio-list">
                                    <label class="mt-radio mt-radio-single mt-radio-outline" style="color:red;font-size:small;">
                                        <input type="radio" name="type" value="update_purchase" />Select this option only if you want to update the status in the system and already did a manual refund.
                                        <span></span>
                                    </label>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-purple">Process</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END REFUND MODAL-->
@endsection

@section('scripts')
<script src="/js/admin/refunds/pendings.js" type="text/javascript"></script>
@endsection
