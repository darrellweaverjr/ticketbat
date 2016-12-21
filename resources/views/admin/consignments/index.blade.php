@php $page_title='Consignment Tickets' @endphp
@extends('layouts.admin')
@section('title', 'Consignment Tickets' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
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
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_tickets" class="btn sbold bg-red" disabled="true"> View Tickets 
                                <i class="fa fa-ticket"></i>
                            </button>
                            <button id="btn_model_seats" class="btn sbold bg-purple"> Seats at Stages
                                <i class="fa fa-cubes"></i>
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
                                <th width="20%"> Show </th>
                                <th width="15%"> Show Time </th>
                                <th width="15%"> Created </th>
                                <th width="15%"> Seller </th>
                                <th width="10%"> Due Date </th>
                                <th width="6%"> Qty </th>
                                <th width="7%"> Total </th>
                                <th width="11%"> Status </th>
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
                                <td width="20%"> {{$c->show_name}} </td>
                                <td width="15%"><center> {{date('m/d/Y g:ia',strtotime($c->show_time))}} </center></td>
                                <td width="15%"><center> {{date('m/d/Y g:ia',strtotime($c->created))}} </center></td>
                                <td width="15%"> {{$c->first_name}} {{$c->last_name}} </td>
                                <td width="10%"><center> {{date('m/d/Y',strtotime($c->due_date))}} </center></td>
                                <td width="6%"><center> {{$c->qty}} </center></td>
                                <td width="7%" style="text-align:right"> $ {{$c->total}} </td>
                                <td width="11%"> 
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
        <div class="modal-dialog" style="width:60% !important;">
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
                                <div class="col-md-6">
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
                                </div>
                                <div class="col-md-6">
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
                                            <div id="due_date" class="input-group date date-picker">
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
                                                <input type="file" name="agreement_file" accept="application/pdf"  onchange="$('#form_model_update [name=agreement]').val(this.value);"> 
                                            </span>
                                        </div> 
                                        <div class="col-md-8 show-error">
                                            <input type="text" name="agreement" class="form-control" readonly="true"/>
                                        </div> 
                                    </div>
                                </div>
                            </div>   
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span class="required"> Section </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-5">Section/Row
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <select class="form-control" name="ticket_id">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">    
                                        <label class="control-label col-md-5">Sale Price
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" name="retail_price" class="form-control" readonly="true" />
                                        </div> 
                                    </div>
                                    <div class="form-group">    
                                        <label class="control-label col-md-5">Processing Fee
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" name="processing_fee" class="form-control" readonly="true"/>
                                        </div> 
                                    </div>
                                    <div class="form-group">    
                                        <label class="control-label col-md-5">Net to Show
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" name="percent_commission" class="form-control" readonly="true"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span class="required"> Seats </span>
                                    </label><hr>
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Select Seats
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-10 show-error">
                                            <select multiple="multiple" size="24" class="multi-select" id="seats_to_add" name="seats[]">
                                            </select>
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
    <!-- END ADD MODAL--> 
    <!-- BEGIN EDIT MODAL--> 
    <div id="modal_model_update2" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:65% !important;">
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
                                        <label class="control-label col-md-4">Seller
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <select class="form-control" name="seller_id" disabled="true">
                                                @foreach($sellers as $index=>$s)
                                                <option value="{{$s->id}}"> {{$s->email}} </option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-4"> Agreement
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <input type="text" name="agreement" class="form-control" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4"> 
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <span class="btn btn-block green fileinput-button">Add/Change Agreement file <i class="fa fa-edit"></i>
                                                <input type="file" name="agreement_file" accept="application/pdf" onchange="$('#form_model_update2 [name=agreement]').val(this.value);"> 
                                            </span>
                                        </div> 
                                    </div>
                                    <div class="form-group">  
                                        <label class="control-label col-md-4">Due Date
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-8 show-error">
                                            <div id="due_date2" class="input-group date date-picker">
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
                                        <label class="control-label col-md-4">Qty
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-2 show-error">
                                            <input readonly class="form-control" type="text" name="qty">
                                        </div> 
                                        <label class="control-label col-md-3">Total ($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-3 show-error">
                                            <input readonly class="form-control" type="text" name="total">
                                        </div> 
                                    </div>
                                    <label class="control-label">
                                        <span class="required"> Actions with selected seats </span>
                                    </label><hr>
                                    <div class="form-group col-md-6"> 
                                        <div class="mt-radio-list">
                                            <label class="mt-radio"> No action
                                                <input value="no" name="action" type="radio" checked="true">
                                                <span></span>
                                            </label>
                                            <label class="mt-radio"> Change Status
                                                <input value="status" name="action" type="radio">
                                                <span></span>
                                            </label>
                                            <label class="mt-radio"> Move to
                                                <input value="moveto" name="action" type="radio">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">  
                                        <br>
                                        <div class="form-group">  
                                            <select class="form-control" name="status">
                                                <option selected disabled value=""></option>
                                                @foreach($status_seat as $indexS=>$s)
                                                <option value="{{$indexS}}">{{$s}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">  
                                            <select class="form-control" name="moveto">
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label">
                                        <span class="required"> Seats </span>
                                    </label><hr>
                                    <div class="show-error" style="height:450px; overflow: auto;">
                                        <table class="table table-striped table-bordered table-hover table-checkable">
                                            <thead>
                                                <tr>
                                                    <th width="2%"> </th>
                                                    <th width="48%"> Section / Row </th>
                                                    <th width="25%"> Seat </th>
                                                    <th width="25%"> Status </th>
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
    <!-- BEGIN UPDATE MODAL--> 
    <div id="modal_model_seats" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:35% !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Manage Seats at Stages</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_seats" class="form-horizontal">
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="form-group">    
                                    <label class="control-label col-md-3">Stage
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="stage_id" required="true">
                                            <option selected disabled value=""></option>
                                            @foreach($stages as $index=>$s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">        
                                    <label class="control-label col-md-3">Section/Row
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="ticket_id" required="true">

                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">        
                                    <label class="control-label col-md-3">Seat
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" value="1" name="start_seat"  onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0" required="true"> 
                                    </div> 
                                    <label class="control-label col-md-1">to
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" value="1" name="end_seat" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0" required="true"> 
                                    </div> 
                                    <button type="button" id="btn_model_add_seat" class="btn sbold bg-green col-md-1">Add</button>
                                </div> 
                            </div>    
                            <div class="row" style="max-height:500px; overflow: auto;">
                                <table class="table table-striped table-bordered table-hover table-checkable">
                                    <thead>
                                        <tr>
                                            <th width="60%"> Section / Row </th>
                                            <th width="20%"> Seat </th>
                                            <th width="20%"> Delete </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_seats">
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save_seat" class="btn sbold bg-purple">Save</button>
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
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>

<script src="/themes/admin/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        
<script src="/js/admin/consignments/index.js" type="text/javascript"></script>
@endsection