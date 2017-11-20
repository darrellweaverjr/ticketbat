@php $page_title='Restaurants' @endphp
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
        <small> - List, add, edit and remove restaurants.</small>
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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
<!--                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search">Search
                                <i class="fa fa-search"></i>
                            </button>-->
                            @endif
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
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
                                <th width="38%">Venue</th>
                                <th width="30%">Name</th>
                                <th width="30%">Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $index=>$r)
                            <tr>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$r->id}}" value="{{$r->name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>{{$r->venue}}</td>
                                <td>{{$r->name}}</td>
                                <td>{{$r->phone}}</td>
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
                            <div class="row" style="padding:0 20px">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Venue
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="venue_id">
                                            @foreach($venues as $index=>$v)
                                                <option value="{{$v->id}}">{{$v->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-3">Name
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" name="name" class="form-control" placeholder="My Restaurant" />
                                    </div>
                                    <label class="control-label col-md-3">Phone
                                        <span class="required"><br>(Only numbers, splited by commas)</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" name="phone" class="form-control" placeholder="7025556666,7027778888" />
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding:0 20px">
                                <label class="control-label">Description:</label>
                                <div class="show-error">
                                    <textarea name="description" class="form-control" rows="5"></textarea>
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
<script src="/js/admin/restaurants/index.js" type="text/javascript"></script>
@endsection
