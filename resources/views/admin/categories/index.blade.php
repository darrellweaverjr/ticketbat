@php $page_title='Categories' @endphp
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
        <small> - List, add, edit and remove categories.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['CATEGORIES']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['CATEGORIES']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['CATEGORIES']['permission_types']))
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
                                <th width="88%">Category</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index=>$c)
                                @if($c->id_parent == 0)
                                    <tr>
                                        <td width="2%">
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="checkboxes" id="{{$c->id}}" value="{{$c->name}}" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="88%">{{$c->name}}</td>
                                        <td width="10%"><input type="checkbox" class="make-switch" name="active" value="{{$c->id}}" @if($c->disabled<1) checked="checked" @endif data-size="mini" data-on-text="Enabled" data-off-text="Disabled" data-on-color="primary" data-off-color="danger"></td>
                                    </tr>
                                    @foreach ($c->children()->get() as $children)
                                        <tr>
                                            <td width="2%">
                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                    <input type="checkbox" class="checkboxes" id="{{$children->id}}" value="{{$children->name}}" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td width="88%"><span style="display: inline-block; width: 4ch;">&#9;</span>-<span style="display: inline-block; width: 4ch;">&#9;</span>{{$children->name}}</td>
                                            <td width="10%"><input type="checkbox" class="make-switch" name="active" value="{{$children->id}}" @if($children->disabled<1) checked="checked" @endif data-size="mini" data-on-text="Enabled" data-off-text="Disabled" data-on-color="primary" data-off-color="danger"></td>
                                        </tr>
                                        @foreach ($children->children()->get() as $niece)
                                            <tr>
                                                <td width="2%">
                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                        <input type="checkbox" class="checkboxes" id="{{$niece->id}}" value="{{$niece->name}}" />
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td width="88%"><span style="display: inline-block; width: 4ch;">&#9;</span>-<span style="display: inline-block; width: 4ch;">&#9;</span>-<span style="display: inline-block; width: 4ch;">&#9;</span>{{$niece->name}}</td>
                                                <td width="10%"><input type="checkbox" class="make-switch" name="active" value="{{$niece->id}}" @if($niece->disabled<1) checked="checked" @endif data-size="mini" data-on-text="Enabled" data-off-text="Disabled" data-on-color="primary" data-off-color="danger"></td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
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
        <div class="modal-dialog" style="width:350px !important;">
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
                                <label class="control-label col-md-3">Name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9 show-error">
                                    <input type="text" name="name" class="form-control" placeholder="My category" /> </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Parent
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9 show-error">
                                    <select class="form-control" name="id_parent">
                                        <option value="0">- No parent -</option>
                                        @foreach($categories as $index=>$c)
                                            @if($c->id_parent == 0)
                                                <option value="{{$c->id}}">{{$c->name}}</option>
                                                @foreach ($c->children()->get() as $children)
                                                    <option value="{{$children->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;{{$children->name}}</option>
                                                    @foreach ($children->children()->get() as $niece)
                                                        <option value="{{$niece->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;{{$niece->name}}</option>
                                                    @endforeach
                                                @endforeach
                                            @endif
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
<script src="/js/admin/categories/index.js" type="text/javascript"></script>
@endsection
