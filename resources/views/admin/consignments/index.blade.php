@php $page_title='Consignment Tickets' @endphp
@extends('layouts.admin')
@section('title', 'Consignment Tickets' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove consignment tickets.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_tickets" class="btn sbold bg-red" disabled="true"> View Tickets 
                                <i class="fa fa-ticket"></i>
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
                                <th width="20%">Show</th>
                                <th width="13%">Show Time</th>
                                <th width="13%">Created</th>
                                <th width="15%">Seller</th>
                                <th width="10%">Due Date</th>
                                <th width="6%">Qty</th>
                                <th width="7%">Total</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consignments as $index=>$c)
                            <tr>
                                <td width="2%">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$c->id}}" value="{{$c->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="1%" class="@if($c->purchase || $c->qty==0) success @else danger @endif"></td>  
                                <td width="20%">{{$c->show_name}} </td>
                                <td width="12%" data-order="{{strtotime($c->show_time)}}"><center>{{date('m/d/Y g:ia',strtotime($c->show_time))}}</center></td>
                                <td width="12%" data-order="{{strtotime($c->created)}}"><center>{{date('m/d/Y g:ia',strtotime($c->created))}}</center></td>
                                <td width="15%">{{$c->first_name}} {{$c->last_name}}</td>
                                <td width="9%" data-order="{{strtotime($c->due_date)}}"><center>{{date('m/d/Y',strtotime($c->due_date))}}</center></td>
                                <td width="6%"><center>{{number_format($c->qty,0)}}</center></td>
                                <td width="10%" style="text-align:right"> $ {{number_format($c->total,2)}}</td>
                                <td width="15%"> 
                                    <select ref="{{$c->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $c->status) selected @endif value="{{$indexS}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </td> 
                            </tr>
                            @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->   
    <!-- BEGIN ADD MODAL--> 
    <div id="modal_model_update" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add Consignment</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="alert alert-warning">
                                <center>
                                    <input type="hidden" name="purchase" value="0" />
                                    <input type="checkbox" class="make-switch" name="purchase" checked="true" value="1"  data-size="mini" data-on-text="Make Purchase" data-off-text="Don't Purchase" data-on-color="primary" data-off-color="danger">
                                    <span style="color:red">You must make a purchase only if this show is one of ours. Otherwise you won't be able to print tickets.</span>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="control-label">
                                        <span class="required"> Event </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-4">Venue
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <select class="form-control" name="venue_id">
                                                <option selected disabled value=""></option>
                                                @foreach($venues as $index=>$v)
                                                <option value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-4">Show
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <select class="form-control" name="show_id">

                                            </select>
                                        </div>  
                                        <label class="control-label col-md-4">Time
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <select class="form-control" name="show_time_id">

                                            </select>
                                        </div>  
                                    </div>
                                    <label class="control-label">
                                        <span class="required"> Seller </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-4">Seller
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <select class="form-control" name="seller_id">
                                                @foreach($sellers as $index=>$s)
                                                <option value="{{$s->id}}"> {{$s->email}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-4">Due Date
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <div class="input-group date date-picker due_date">
                                                <input readonly class="form-control" type="text" name="due_date" value="{{date('Y-m-d')}}">
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>                          
                                        </div> 
                                        <div class="col-md-4 show-error">
                                            <span class="btn btn-block green fileinput-button">Agreement <i class="fa fa-plus"></i>
                                                <input type="file" name="agreement_file" accept="application/pdf" @if(Auth::user()->user_type_id != 1) disabled="true" @endif onchange="$('#form_model_update [name=agreement]').val(this.value);"> 
                                            </span>
                                        </div> 
                                        <div class="col-md-8 show-error">
                                            <input type="text" name="agreement" class="form-control" readonly="true"/>
                                        </div> 
                                    </div>
                                    
                                    <label class="control-label">
                                        <span class="required"> Section </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-4">Ticket Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error"> 
                                            <select class="form-control" name="section">
                                                <option selected disabled value=""></option>
                                                @foreach($sections as $index=>$s)
                                                <option value="{{md5($index)}}"> {{$s}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-4">Seat
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-4">
                                            <input type="text" value="1" name="start_seat"  onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0" required="true"> 
                                        </div> 
                                        <div class="col-md-4">
                                            <input type="text" value="1" name="end_seat" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0" required="true"> 
                                        </div> 
                                        <label class="control-label col-md-4">Seat Number
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error"> 
                                            <input type="hidden" name="show_seat" value="0" />
                                            <input type="checkbox" class="make-switch" name="show_seat" value="1"  data-size="medium" data-on-text="Show on ticket" data-off-text="Hide on ticket" data-on-color="primary" data-off-color="warning">
                                        </div>
                                    </div>    
                                    <div class="col-md-9 form-group">  
                                        <label class="control-label col-md-6">S.Price ($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 show-error">
                                            <input type="number" name="retail_price" value="0.00" step="0.01" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0  || event.charCode == 46"/>
                                        </div>    
                                        <label class="control-label col-md-6">Proc.Fee ($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 show-error">
                                            <input type="number" name="processing_fee" value="0.00" step="0.01" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46"/>
                                        </div>   
                                        <label class="control-label col-md-6">Commiss.(%)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 show-error">
                                            <input type="number" name="percent_commission" value="0.00" step="0.01" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46"/>
                                        </div>
                                        <label class="control-label col-md-6">Commiss.($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 show-error">
                                            <input type="number" name="fixed_commission" value="0.00" step="0.01" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" style="height:137px" id="btn_model_add_seat" class="btn sbold dark btn-outline btn-block">
                                            <i class="fa fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label">
                                        <span class="required"> Seats </span>
                                    </label><hr>
                                    <div style="max-height:670px; overflow: auto;">
                                        <table class="table table-striped table-bordered table-hover table-checkable">
                                            <thead>
                                                <tr>
                                                    <th width="35%">Ticket Type</th>
                                                    <th width="10%">Seat</th>
                                                    <th width="13%">Price</th>
                                                    <th width="13%">Fee</th>
                                                    <th width="13%">Comm.</th>
                                                    <th width="8%">Show</th>
                                                    <th width="8%">Del</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb_seats">
                                            </tbody>
                                        </table>
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
    <!-- END ADD MODAL--> 
    <!-- BEGIN EDIT MODAL--> 
    <div id="modal_model_update2" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-yellow">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Edit Consignment</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update2" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="control-label">
                                        <span class="required"> Consignment Info </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-3">Seller
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="seller_id">
                                                @foreach($sellers as $index=>$s)
                                                <option value="{{$s->id}}"> {{$s->email}} </option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-3"> Agreement
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="agreement" class="form-control col-md-6" readonly="true" style="width:140px !important"/>
                                            <span class="btn yellow fileinput-button col-md-3"><i class="fa fa-edit"></i>
                                                <input type="file" name="agreement_file" accept="application/pdf" @if(Auth::user()->user_type_id != 1) disabled="true" @endif onchange="$('#form_model_update2 [name=agreement]').val(this.value);"> 
                                            </span>
                                            <span class="btn green col-md-3" id="btn_model_file" ><i class="fa fa-search"></i>
                                            </span>
                                        </div>
                                        <label class="control-label col-md-4"> 
                                        </label>
                                        <div class="col-md-4 show-error">
                                            
                                        </div>
                                        <div class="col-md-4 show-error">
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-3">Due Date
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <div class="input-group date date-picker due_date">
                                                <input readonly class="form-control" type="text" name="due_date" value="{{date('Y-m-d')}}">
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-3">Qty</label>
                                        <div class="col-md-3 show-error">
                                            <input readonly class="form-control" type="number" name="qty" style="width:80px !important">
                                        </div> 
                                        <label class="control-label col-md-2">Total($)</label>
                                        <div class="col-md-4 show-error">
                                            <input readonly class="form-control" type="number" name="total" style="width:110px !important">
                                        </div> 
                                    </div>
                                    <label class="control-label">
                                        <span class="required"> Actions with selected seats </span>
                                    </label><hr>
                                    <div class="form-group" style="padding:0 20px"> 
                                        <div class="mt-radio-list col-md-12">
                                            <label class="mt-radio">No action
                                                <input value="no" name="action" type="radio" checked="true">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="mt-radio-list col-md-5">
                                            <label class="mt-radio">Change Status
                                                <input value="status" name="action" type="radio">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-7">  
                                            <select class="form-control" name="status">
                                                <option selected disabled value=""></option>
                                                @foreach($status_seat as $indexS=>$s)
                                                <option value="{{$indexS}}">{{$s}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mt-radio-list col-md-5">
                                            <label class="mt-radio">Move to
                                                <input value="moveto" name="action" type="radio">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-7">  
                                            <select class="form-control" name="moveto">                                                
                                            </select>
                                        </div>
                                        <div class="mt-radio-list col-md-5">
                                            <label class="mt-radio">Toggle Seats #
                                                <input value="showseats" name="action" type="radio">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-7">
                                            <input type="hidden" name="showseats" value="0" />
                                            <input type="checkbox" class="make-switch" name="showseats" checked="true" value="1"  data-size="small" data-on-text="Show Seats #" data-off-text="Hide Seats #" data-on-color="primary" data-off-color="danger">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label">
                                        <span class="required"> Seats </span>
                                    </label><hr>
                                    <div class="show-error" style="height:460px; overflow: auto;">
                                        <table class="table table-striped table-bordered table-hover table-checkable">
                                            <thead>
                                                <tr>
                                                    <th width="2%">
                                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                            <input type="checkbox" id="group-checkable"/>
                                                            <span></span>
                                                        </label>
                                                    </th>
                                                    <th width="29%">Ticket Type</th>
                                                    <th width="10%">Seat</th>
                                                    <th width="13%">Price</th>
                                                    <th width="13%">Fee</th>
                                                    <th width="13%">Comm.</th>
                                                    <th width="8%">Show</th>
                                                    <th width="12%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb_seats_consignment_edit">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save2" class="btn sbold bg-yellow">Save</button>
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
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="/js/admin/consignments/index.js" type="text/javascript"></script>
@endsection