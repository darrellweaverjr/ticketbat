@php $page_title='Consignment Tickets' @endphp
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
        <small> - List, add, edit and remove consignment tickets. (By default the last 30 days.)</small>
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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_search" class="btn sbold grey-salsa">Filter
                                <i class="fa fa-filter"></i>
                            </button>
                            @endif
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                            <button id="btn_model_tickets" class="btn sbold bg-red" disabled="true">View Tickets
                                <i class="fa fa-ticket"></i>
                            </button>
                            <button id="btn_model_contract" class="btn sbold bg-purple" disabled="true">Generate Contract
                                <i class="fa fa-copy"></i>
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
                                <th width="3%">ID</th>
                                <th width="18%">Show</th>
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
                                <td>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" value="{{$c->id}}" data-qty="{{$c->qty}}"/>
                                        <span></span>
                                    </label>
                                </td>
                                <td class="@if($c->purchase || $c->qty==0) success @else danger @endif">{{$c->id}}</td>
                                <td>{{$c->show_name}}</td>
                                <td data-order="{{strtotime($c->show_time)}}"><center>{{date('m/d/Y g:ia',strtotime($c->show_time))}}</center></td>
                                <td data-order="{{strtotime($c->created)}}"><center>{{date('m/d/Y g:ia',strtotime($c->created))}}</center></td>
                                <td>{{$c->first_name}} {{$c->last_name}}</td>
                                <td data-order="{{strtotime($c->due_date)}}"><center>{{date('m/d/Y',strtotime($c->due_date))}}</center></td>
                                <td><center>{{number_format($c->qty,0)}}</center></td>
                                <td style="text-align:right"> $ {{number_format($c->total,2)}}</td>
                                <td>
                                    <select ref="{{$c->id}}" class="form-control" name="status">
                                        @foreach($status as $indexS=>$s)
                                        <option @if($indexS == $c->status) selected="true" @endif value="{{$indexS}}">{{$s}}</option>
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
        <div class="modal-dialog modal-lg">
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
                            <div class="alert alert-danger">
                                <center>
                                    <input type="hidden" name="purchase" value="1" />
                                    <h4 style="color:red">This option is ONLY for pre-sales that create tickets now.<br>For other options use the POS system.</h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="control-label">
                                        <span class="required">General</span>
                                    </label><br>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Venue
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="venue_id">
                                                <option selected disabled value=""></option>
                                                @foreach($search['venues'] as $index=>$v)
                                                <option value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-3">Show
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="show_id" data-content='@php echo str_replace("'"," ",json_encode($search['shows']));@endphp'>
                                            </select>
                                        </div>
                                        <label class="control-label col-md-3">Time
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="show_time_id"></select>
                                        </div>
                                        
                                        <label class="control-label col-md-3">Seller
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="seller_id">
                                                @foreach($sellers as $index=>$s)
                                                <option value="{{$s->id}}"> {{$s->email}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-3">Due Date</label>
                                        <div class="col-md-4 show-error">
                                            <div class="input-group date date-picker due_date">
                                                <input readonly class="form-control" type="text" name="due_date" value="{{date('Y-m-d')}}" style="width:100px">
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-5 show-error">
                                            <span class="btn btn-block green fileinput-button">Agreement <i class="fa fa-plus"></i>
                                                <input type="file" name="agreement_file" accept="application/pdf" @if(Auth::user()->user_type_id != 1) disabled="true" @endif onchange="$('#form_model_update2 [name=agreement]').val(this.value);">
                                            </span>
                                        </div>
                                    </div>
                                    <label class="control-label">
                                        <span class="required">Section</span>
                                    </label><br>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">T.Type
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="section">
                                                <option selected disabled value=""></option>
                                                @foreach($sections as $index=>$s)
                                                <option value="{{md5($index)}}"> {{$s}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-3">Seat
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-4">
                                            <input type="number" style="width:65px" value="1" name="start_seat" required="true">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" style="width:65px" value="1" name="end_seat" required="true">
                                        </div>
                                        <label class="control-label col-md-3">Seat #</label>
                                        <div class="col-md-9 show-error">
                                            <input type="hidden" name="show_seat" value="0" />
                                            <input type="checkbox" class="make-switch input-lg" name="show_seat" value="1"  data-size="small" data-on-text="Show on tick" data-off-text="Hide on tick" data-on-color="primary" data-off-color="warning">
                                        </div>
                                        
                                        <label class="control-label col-md-3">Price($)
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="number" name="retail_price" value="0.00" step="0.01" class="form-control" />
                                        </div>
                                        
                                        <label class="control-label col-md-3">Com (%)</label>
                                        <div class="col-md-4 show-error">
                                            <input type="number" name="percent_commission" value="0.00" step="0.01" class="form-control" />
                                        </div>
                                        <label class="control-label col-md-1">($)</label>
                                        <div class="col-md-4 show-error">
                                            <input type="number" name="fixed_commission" value="0.00" step="0.01" class="form-control" />
                                        </div>
                                        
                                        <label class="control-label col-md-3">Fee (%)</label>
                                        <div class="col-md-4 show-error">
                                            <input type="number" name="percent_processing_fee" value="0.00" step="0.01" class="form-control" disabled="true" />
                                        </div>
                                        <label class="control-label col-md-1">($)</label>
                                        <div class="col-md-4 show-error">
                                            <input type="number" name="processing_fee" value="0.00" step="0.01" class="form-control" />
                                        </div>
                                        <label class="control-label col-md-3">Inclusive</label>
                                        <div class="col-md-9 show-error">
                                            <input type="hidden" name="inclusive" value="0" />
                                            <input type="checkbox" class="make-switch input-lg" name="inclusive" value="1"  data-size="small" data-on-text="Incl. fee" data-off-text="Over price" data-on-color="primary" data-off-color="danger">
                                        </div>
                                        
                                        <label class="control-label col-md-3">Collect($)</label>
                                        <div class="col-md-9 show-error">
                                            <input type="number" name="collect_price" value="0.00" step="0.01" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-9 show-error">
                                            <button type="button" id="btn_model_add_seat" class="btn sbold dark btn-outline btn-block">
                                                Add / Modify seats <i class="fa fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label">
                                        <span class="required">Seats</span>
                                    </label><br>
                                    <div style="max-height:560px; overflow: auto;">
                                        <table class="table table-striped table-bordered table-hover table-checkable">
                                            <thead>
                                                <tr>
                                                    <th width="30%">Ticket Type</th>
                                                    <th width="10%">Seat</th>
                                                    <th width="12%">Price</th>
                                                    <th width="12%">Fee</th>
                                                    <th width="12%">Comm.</th>
                                                    <th width="12%">Collect</th>
                                                    <th width="5%">#</th>
                                                    <th width="7%"><center><input type="button" id="deleteAllPreSeats" value="-" class="btn btn-block bg-red" style="height:16px !important"></center></th>
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
        <div class="modal-dialog  modal-lg">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-yellow">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title">Edit Consignment</center></h4>
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
                                        <label class="control-label col-md-3">Due Date</label>
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
                                            <input type="checkbox" class="make-switch" name="showseats" value="1"  data-size="small" data-on-text="Show Seats #" data-off-text="Hide Seats #" data-on-color="primary" data-off-color="warning">
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
                                                    <th width="23%">Ticket Type</th>
                                                    <th width="10%">Seat</th>
                                                    <th width="12%">Price</th>
                                                    <th width="12%">Fee</th>
                                                    <th width="12%">Com.</th>
                                                    <th width="12%">Coll.</th>
                                                    <th width="5%">#</th>
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
    <!-- BEGIN SEARCH MODAL-->
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Filter Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/consignments" id="form_model_search">
                        <input type="hidden" name="_token" value="{{ Session::token() }}" />
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
                                    <label class="control-label col-md-3">Created:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="created_date">
                                            <input type="text" class="form-control" name="created_start_date" value="{{$search['created_start_date']}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="created_end_date" value="{{$search['created_end_date']}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_created_date">
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
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa">Search</button>
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
    <!-- BEGIN TICKETS MODAL-->
    <div id="modal_model_tickets" class="modal fade" tabindex="1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-red">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>View Tickets</center></h4>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group">
                                <p class="container-fluid">This option generates a PDF that allows you to view/print tickets.<br>
                                    You must select the range first.<br>Then, select the way you want to view/print the tickets.</p>
                            </div>
                            <div id="range_options" class="form-group" style="margin-left:40%"></div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <div class="col-md-6 center-block">
                                    <button type="button" id="btn_tickets_standard" class="btn sbold dark btn-outline btn-block">Standart Printer</button>
                                </div>
                                <div class="col-md-6 center-block">
                                    <button type="button" id="btn_tickets_boca" class="btn sbold red btn-outline btn-block">BOCA Ticket Printer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END TICKETS MODAL-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="/js/admin/consignments/index.js" type="text/javascript"></script>
@endsection
