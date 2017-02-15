@php $page_title='Users' @endphp
@extends('layouts.admin')
@section('title', 'Users' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove users (Only for admin purposes)</small>
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
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>
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
                                <th width="20%"> Email </th>
                                <th width="100"> First Name </th>
                                <th width="100"> Last Name </th>
                                <th width="20%"> Phone </th>
                                <th width="15%"> Role </th>
                                <th width="10%"> Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index=>$u)
                            <tr @if(!$u->is_active) class="danger" @endif>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$u->id}}" value="{{$u->email}} * {{$u->first_name}} {{$u->last_name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="20%"> <a href="mailto:{{$u->email}}" target="_top">{{$u->email}}</a> </td>
                                <td width="100"> {{$u->first_name}} </td>
                                <td width="100"> {{$u->last_name}} </td>
                                <td width="20%"> {{$u->phone}} </td>
                                <td width="15%"> <span class="label label-sm sbold 
                                    @if($u->user_type_id == 1) label-success 
                                    @elseif($u->user_type_id == 1) label-success 
                                    @elseif($u->user_type_id == 2) label-danger 
                                    @elseif($u->user_type_id == 3) label-warning 
                                    @elseif($u->user_type_id == 4) label-info 
                                    @elseif($u->user_type_id == 5) label-primary
                                    @else label-default
                                    @endif
                                    "> {{$u->user_type}} </span> 
                                </td> 
                                <td width="10%"> <span class="label label-sm sbold
                                    @if($u->is_active) label-success"> Active 
                                    @else label-danger"> Inactive 
                                    @endif
                                    </span> 
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
                                        <a href="#tab_model_update_discounts" data-toggle="tab" aria-expanded="false">Discounts</a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_permissions" data-toggle="tab" aria-expanded="false">Permissions</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_model_update_general">    
                                        <div class="form-group">
                                            <label class="control-label col-md-2">First Name
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-4 show-error">
                                                <input type="text" name="first_name" class="form-control" placeholder="John" /> </div>
                                            <label class="control-label col-md-2">Last Name
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-4 show-error">
                                                <input type="text" name="last_name" class="form-control" placeholder="Doe"/> </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Email
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-4 show-error">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                                    <input type="email" name="email" class="form-control" placeholder="user@server.com"> 
                                                </div>
                                            </div>
                                            <label class="control-label col-md-2">Set Password</label>
                                            <div class="col-md-2 show-error">
                                                <div class="input-group">
                                                    <input name="password" type="password" class="form-control" /> </div>
                                            </div>        
                                            <div class="col-md-2">
                                                <label>                                        
                                                    <input type="hidden"   name="force_password_reset" value="0" />
                                                    <input type="checkbox" name="force_password_reset" value="1" checked="true" /> Reset?
                                                </label>
                                            </div>    
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Address
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-5 show-error">
                                                <input name="address" type="text" class="form-control" placeholder="000 Main St. Apt 1" /> </div>
                                            <label class="control-label col-md-1">City
                                                    <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-2 show-error">
                                                <input name="city" type="text" class="form-control" placeholder="Las Vegas"/> </div>
                                            <label class="control-label col-md-1">State
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-1 show-error">
                                                <input name="state" type="text" class="form-control" placeholder="NV"/> </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Zip
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-2 show-error">
                                                <input name="zip" type="number" class="form-control" placeholder="#####" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
               event.charCode == 0 " /> </div>
                                            <label class="control-label col-md-2">Country
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-3 show-error">
                                                <select class="form-control" name="country">
                                                    @foreach($countries as $index=>$c)
                                                    <option @if($c->code=='US') selected @endif value="{{$c->code}}">{{$c->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>  
                                            <label class="control-label col-md-1">Phone</label>
                                            <div class="col-md-2 show-error">
                                                <input name="phone" class="form-control" placeholder="### ### ####" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
               event.charCode == 45 || event.charCode == 40 || event.charCode == 41 || event.charCode == 32 || event.charCode == 0 " /> </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Role
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6 show-error">
                                                <select class="form-control" name="user_type_id">
                                                    @foreach($user_types as $index=>$t)
                                                    <option value="{{$t->id}}"> {{$t->user_type}} : {{$t->description}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 show-error">
                                                <label>
                                                    <input type="hidden"   name="is_active" value="0" />
                                                    <input type="checkbox" name="is_active" value="1" checked="true"/> Active?                                    
                                                </label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_discounts">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required">Discounts for this user:</span>
                                                </label>
                                                <select class="form-control" name="discounts[]" multiple="multiple" size="12">
                                                    @foreach($discounts as $index=>$d)
                                                    <option value="{{$d->id}}">{{$d->code}} - {{$d->description}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_permissions">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required">Permitted to check in guests at venue(s):</span>
                                                </label>
                                                <select class="form-control" name="venues_check_ticket[]" multiple="multiple" size="12">
                                                    @foreach($venues as $index=>$v)
                                                    <option value="{{$v->id}}">{{$v->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required">Permitted to edit elements at venue(s):</span>
                                                </label>
                                                <select class="form-control" name="venues_edit[]" multiple="multiple" size="12">
                                                    @foreach($venues as $index=>$v)
                                                    <option value="{{$v->id}}">{{$v->name}}</option>
                                                    @endforeach
                                                </select>
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
@endsection

@section('scripts') 
<script src="/js/admin/users/index.js" type="text/javascript"></script>
@endsection