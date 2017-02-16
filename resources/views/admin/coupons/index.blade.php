@php $page_title='Coupons' @endphp
@extends('layouts.admin')
@section('title', 'Coupons' )

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
                                <th width="12%"> Code </th>
                                <th width="12%"> Discount Type </th>
                                <th width="12%"> Discount Scope </th>
                                <th width="12%"> Coupon Type </th>
                                <th width="5%"> Redemptions </th>
                                <th width="45%"> Description </th>
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
                                <td width="12%"> {{$d->code}} </td>
                                <td width="12%"> <span class="label label-sm sbold 
                                    @if($d->discount_type == 'Dollar') label-success 
                                    @elseif($d->discount_type == 'N for N') label-danger 
                                    @elseif($d->discount_type == 'Percent') label-warning
                                    @else label-default
                                    @endif
                                    "> {{$d->discount_type}} </span> 
                                </td> 
                                <td width="12%"> <span class="label label-sm sbold 
                                    @if($d->discount_scope == 'Ticket') label-success 
                                    @elseif($d->discount_scope == 'Total') label-danger 
                                    @elseif($d->discount_scope == 'Merchandise') label-warning
                                    @else label-default
                                    @endif
                                    "> {{$d->discount_scope}} </span> 
                                </td> 
                                <td width="12%"> <span class="label label-sm sbold 
                                    @if($d->coupon_type == 'Normal') label-success 
                                    @elseif($d->coupon_type == 'Broker') label-danger 
                                    @elseif($d->coupon_type == 'Affiliate') label-warning 
                                    @elseif($d->coupon_type == 'Admin') label-info 
                                    @else label-default
                                    @endif
                                    "> {{$d->coupon_type}} </span> 
                                </td> 
                                <td width="5%"><center><span class="label label-sm sbold 
                                    @if($d->purchases) label-success 
                                    @else label-danger 
                                    @endif
                                    "> {{$d->purchases}} </span></center></td> 
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
                            <div class="form-group">
                                <label class="control-label col-md-2">Code
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-3 show-error">
                                    <input type="text" name="code" class="form-control" placeholder="0000" /> </div>
                                <label class="control-label col-md-2">Percent Off
                                </label>
                                <div class="col-md-1 show-error">
                                    <input type="text" name="start_num" class="form-control" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
   event.charCode == 0 "/> </div>    
                                <label class="control-label col-md-3"># of Codes (0 for infinite)
                                </label>
                                <div class="col-md-1 show-error">
                                    <input type="text" name="quantity" class="form-control" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
   event.charCode == 0 "/> </div>  
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">Discount Type
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="discount_type">
                                        @foreach($discount_types as $index=>$t)
                                        <option value="{{$index}}"> {{$t}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-md-2">Discount Scope
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="discount_scope">
                                        @foreach($discount_scopes as $index=>$t)
                                        <option value="{{$index}}"> {{$t}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-md-2">Coupon Type
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="coupon_type">
                                        @foreach($coupon_types as $index=>$t)
                                        <option value="{{$index}}"> {{$t}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group"> 
                                <div class="col-md-6 show-error">
                                    <input type="hidden"   name="start_date" value="" />
                                    <input type="hidden"   name="end_date" value="" />
                                    <label class="control-label">Dates Action:
                                    </label><br>
                                    <div id="start_end_date" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                                        <i class="icon-calendar"></i>&nbsp;
                                        <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                </div>
                                <div class="col-md-6 show-error">
                                    <input type="hidden"   name="effective_start_date" value="" />
                                    <input type="hidden"   name="effective_end_date" value="" />
                                    <label>                                        
                                        <input type="hidden"   name="effective_dates" value="0" />
                                        <input type="checkbox" name="effective_dates" value="1" /> Use Effective Dates?
                                    </label><br>
                                    <div id="effective_start_end_date" class="pull-right tooltips btn btn-sm show-error" data-container="body" data-placement="bottom">
                                        <i class="icon-calendar"></i>&nbsp;
                                        <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                </div>  
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Description:
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div> 
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Coupon Scope [Shows]:
                                </label>
                                <div class="col-md-10 show-error">
                                    <select class="form-control" name="shows[]" multiple="multiple" size="8">
                                        @foreach($shows as $index=>$s)
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                        @endforeach
                                    </select>
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
@endsection

@section('scripts') 
<script src="/js/admin/coupons/index.js" type="text/javascript"></script>
@endsection