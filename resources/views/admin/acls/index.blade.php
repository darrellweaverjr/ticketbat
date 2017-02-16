@php $page_title='ACLs' @endphp
@extends('layouts.admin')
@section('title', 'ACLs' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove ACLs.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true">Remove 
                                <i class="fa fa-remove"></i>
                            </button>
                            @endif
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                            <button id="btn_model_user_type" class="btn sbold bg-purple"> User Types 
                                <i class="fa fa-users"></i>
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
                                <th width="23%"> Permission </th>
                                <th width="25%"> Code </th>
                                <th width="50%"> Description </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $index=>$p)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$p->id}}" value="{{$p->code}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="23%"> {{$p->permission}} </td>
                                <td width="25%"> {{$p->code}} </td>
                                <td width="50%"> {{$p->description}} </td> 
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
                                <label class="control-label col-md-2">Permission
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4 show-error">
                                    <input type="text" name="permission" class="form-control" placeholder="My permission" /> </div>
                                <label class="control-label col-md-2">Code
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4 show-error">
                                    <input type="text" name="code" class="form-control" placeholder="My CODE" /> </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Description:
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea name="description" class="form-control" rows="2"></textarea>
                                </div> 
                            </div>
                            <hr>
                            <div class="form-group">
                                <table class="table table-striped table-bordered table-hover table-checkable" id="tb_acls">
                                    <thead>
                                        <tr>
                                            <th width="25%"> Group </th>
                                            <th width="15%"> Scope </th>
                                            <th width="60%"> Permissions </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user_types as $index=>$t)
                                        <tr>
                                            <td width="20%"> {{$t->user_type}} </td>
                                            <td width="10%"> 
                                                <select class="form-control" name="user_type_permissions[{{$t->id}}][permission_scope]">
                                                    @foreach($permission_scopes as $indexS=>$s)
                                                        <option value="{{$indexS}}"> {{$s}} </option>
                                                    @endforeach 
                                                </select>
                                            </td>
                                            <td width="70%" class="mt-checkbox-inline"> 
                                                @foreach($permission_types as $indexP=>$p)
                                                <label class="mt-checkbox">
                                                    <input type="checkbox" name="user_type_permissions[{{$t->id}}][permission_type][]" value="{{$indexP}}" />
                                                    {{$p}}<span></span>                                   
                                                </label>
                                                @endforeach 
                                            </td> 
                                        </tr>
                                        @endforeach 
                                    </tbody>
                                </table>
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
<script src="/js/admin/acls/index.js" type="text/javascript"></script>
@endsection