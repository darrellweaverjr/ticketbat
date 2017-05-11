@php $page_title='Coupons' @endphp
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
        <small> - List, add, edit and remove coupons.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['COUPONS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['COUPONS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['COUPONS']['permission_types']))
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true">Remove
                                <i class="fa fa-remove"></i>
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
                                <th width="12%">Code</th>
                                <th width="12%">Coupon Type</th>
                                <th width="12%">Discount Type</th>
                                <th width="12%">Discount Scope</th>
                                <th width="5%">Redemptions</th>
                                <th width="45%">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $index=>$d)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$d->id}}" value="{{$d->code}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="12%">{{$d->code}}</td>
                                <td width="12%"><span class="label label-sm sbold
                                    @if($d->coupon_type == 'Normal') label-success
                                    @elseif($d->coupon_type == 'Broker') label-danger
                                    @elseif($d->coupon_type == 'Affiliate') label-warning
                                    @elseif($d->coupon_type == 'Admin') label-info
                                    @else label-default
                                    @endif
                                    ">{{$d->coupon_type}}</span>
                                </td>
                                <td width="12%"><span class="label label-sm sbold
                                    @if($d->discount_type == 'Dollar') label-success
                                    @elseif($d->discount_type == 'N for N') label-danger
                                    @elseif($d->discount_type == 'Percent') label-warning
                                    @else label-default
                                    @endif
                                    ">{{($d->discount_type)? $d->discount_type : 'Multiple'}}</span>
                                </td>
                                <td width="12%"><span class="label label-sm sbold
                                    @if($d->discount_scope == 'Ticket') label-success
                                    @elseif($d->discount_scope == 'Total') label-danger
                                    @elseif($d->discount_scope == 'Merchandise') label-warning
                                    @else label-default
                                    @endif
                                    ">{{($d->discount_scope)? $d->discount_scope : 'Multiple'}}</span>
                                </td>
                                <td width="5%"><center><span class="label label-sm sbold
                                    @if($d->purchases) label-success
                                    @else label-danger
                                    @endif
                                    ">{{$d->purchases}}</span></center></td>
                                <td width="45%"> {{$d->description}} </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN UPDATE MODAL-->
    <div id="modal_model_update" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title"></center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_model_update_general" data-toggle="tab" aria-expanded="true">General</a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_scope" data-toggle="tab" aria-expanded="false">Scope</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_model_update_general" style="padding:0 20px">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="control-label col-md-1">Code
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <input type="text" name="code" class="form-control" placeholder="0000" />
                                                </div>
                                                <label class="control-label col-md-1">Type
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <select class="form-control" name="coupon_type">
                                                        @foreach($coupon_types as $index=>$t)
                                                        <option value="{{$index}}"> {{$t}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-1">Reg/Mult
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <select class="form-control" name="multiple">
                                                        <option value="0">Regular</option>
                                                        <option value="1">Multiple</option>
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-1">Qty(0=&#8734;)
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <input type="text" name="quantity" class="form-control" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||
                   event.charCode == 0 "/> </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-1">Scope
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <select class="form-control" name="discount_scope">
                                                        @foreach($discount_scopes as $index=>$t)
                                                        <option value="{{$index}}"> {{$t}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-1">Off
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <select class="form-control" name="discount_type">
                                                        @foreach($discount_types as $index=>$t)
                                                        <option value="{{$index}}"> {{$t}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-1 label_num">% Off
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <input type="text" name="start_num" class="form-control start_num" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46 "/>
                                                </div>
                                                <label class="control-label col-md-1 range">Get
                                                </label>
                                                <div class="col-md-2 show-error range">
                                                    <input type="text" name="end_num" class="form-control end_num" value="" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row"><hr>
                                            <div class="form-group col-md-6">
                                                <label class="control-label col-md-4">Dates Action:
                                                </label>
                                                <div class="col-md-8 show-error">
                                                    <div class="input-group" id="action_dates" style="width:275px">
                                                        <input type="text" class="form-control" name="start_date" value="{{date('Y-m-d')}}" readonly="true">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control" name="end_date" value="{{date('Y-m-d')}}" readonly="true">
                                                        <span class="input-group-btn">
                                                            <button class="btn default date-range-toggle" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label col-md-5">Effective Dates:
                                                </label>
                                                <div class="col-md-7 show-error">
                                                    <div class="input-group" id="effective_dates" style="width:313px">
                                                        <input type="text" class="form-control" name="effective_start_date" value="" readonly="true">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control" name="effective_end_date" value="" readonly="true">
                                                        <span class="input-group-btn">
                                                            <button class="btn default date-range-toggle" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                            <button class="btn default" type="button" id="clear_effective_dates">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row"><hr>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Description:
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-10 show-error">
                                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_scope" style="padding:0 40px">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="show-error portlet-body" style="padding:0 15px;max-height:450px;overflow-y: auto;">
                                                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_ticket">
                                                        <thead>
                                                            <tr>
                                                                <th width="2%"></th>
                                                                <th width="27%">Venue</th>
                                                                <th width="27%">Show</th>
                                                                <th width="15%">Ticket Type</th>
                                                                <th width="24%">Package</th>
                                                                <th width="5%"><center><i class="fa fa-edit"></i></center></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($tickets as $index=>$t)
                                                            <tr>
                                                                <td>
                                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                        <input type="checkbox" name="tickets[]" class="tcheckboxes" value="{{$t->id}}" />
                                                                        <span></span>
                                                                    </label>
                                                                </td>
                                                                <td>{{$t->venue_name}}</td>
                                                                <td>{{$t->show_name}}</td>
                                                                <td>{{$t->ticket_type}}</td>
                                                                <td>{{$t->title}}</td>
                                                                <td><center><button type="button" class="btn sbold bg-grey-salsa" data-ticket="{{$t->id}}"><i class="fa fa-edit"></i></button></center></td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-green">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END UPDATE MODAL-->
    <!-- BEGIN EDIT MODAL-->
    <div id="modal_model_edit" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:380px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Edit Discount for Ticket Type</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_edit">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="discount_id" value="" />
                        <input type="hidden" name="ticket_id" value="" />
                        <input type="hidden" name="action" value="1" />
                        <input type="hidden" name="multiple" value="0" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Fixed commission</label>
                                    <div class="col-md-6 show-error">
                                        <input type="text" class="form-control" name="fixed_commission" value="" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46 "/>
                                    </div>
                                    <label class="control-label col-md-6 label_num">% Off
                                    </label>
                                    <div class="col-md-6 show-error">
                                        <input type="text" name="start_num" class="form-control start_num" value="" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46 "/>
                                    </div>
                                    <label class="control-label col-md-6 range">Get
                                    </label>
                                    <div class="col-md-6 show-error range">
                                        <input type="text" name="end_num" class="form-control end_num" value="" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save_ticket" class="btn sbold grey-salsa">Save</button>
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
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="/js/admin/coupons/index.js" type="text/javascript"></script>
@endsection
