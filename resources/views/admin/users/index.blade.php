@php $page_title='Users' @endphp
@extends('layouts.admin')
@section('title', 'Users' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/themes/admin/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
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
                            <button id="btn_users_add" class="btn sbold blue"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_users_edit" class="btn sbold yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_users_remove" class="btn sbold red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_users">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_users .checkboxes" />
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
                            <tr @if($u->user_type_id == '1') class="success" @endif
                                @if(!$u->is_active) class="danger" @endif>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="{{$u->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="20%"> {{$u->email}} </td>
                                <td width="100"> {{$u->first_name}} </td>
                                <td width="100"> {{$u->last_name}} </td>
                                <td width="20%"> {{$u->phone}} </td>
                                <td width="15%"> 
                                    @if($u->user_type_id == 1) <span class="label label-sm label-success"> Admin </span> 
                                    @else {{$user_types->find($u->user_type_id)->user_type}} 
                                    @endif
                                </td>                                     
                                <td width="10%"> 
                                    @if($u->is_active) Active 
                                    @else <span class="label label-sm label-danger"> Inactive </span>
                                    @endif
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
    <div id="modal_users_update" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form action="#" id="form_users" class="form-horizontal">
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">First Name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <input type="text" name="name" data-required="1" class="form-control" /> </div>
                                <label class="control-label col-md-2">Last Name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <input type="text" name="name" data-required="1" class="form-control" /> </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Email
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control" placeholder="Email Address"> </div>
                                </div>
                                <label class="control-label col-md-2">Password</label>
                                <div class="col-md-4">
                                    <input name="password" type="password" class="form-control" /> </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Address
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-5">
                                    <input name="address" type="text" class="form-control" /> </div>
                                <label class="control-label col-md-1">City
                                        <span class="required"> * </span>
                                </label>
                                <div class="col-md-2">
                                    <input name="city" type="text" class="form-control" /> </div>
                                <label class="control-label col-md-1">State
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-1">
                                    <input name="state" type="text" class="form-control" /> </div>    
                            </div>
                            <div class="form-group">
                                
                                <label class="control-label col-md-2">Zip
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-2">
                                    <input name="zip" type="number" class="form-control" /> </div>
                                <label class="control-label col-md-2">Country
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-3">
                                    <select class="form-control" name="options2">
                                        <option value="">Select...</option>
                                        <option value="Option 1">Option 1</option>
                                        <option value="Option 2">Option 2</option>
                                        <option value="Option 3">Option 3</option>
                                        <option value="Option 4">Option 4</option>
                                    </select>
                                </div>  
                                <label class="control-label col-md-1">Phone</label>
                                <div class="col-md-2">
                                    <input name="phone" type="number" class="form-control" /> </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Rol
                                </label>
                                <div class="col-md-4">
                                    <select class="form-control" name="options2">
                                        <option value="">Select...</option>
                                        <option value="Option 1">Option 1</option>
                                        <option value="Option 2">Option 2</option>
                                        <option value="Option 3">Option 3</option>
                                        <option value="Option 4">Option 4</option>
                                    </select>
                                </div> 
                                <label class="col-md-3 mt-checkbox"> 
                                    <input type="checkbox" value="1" name="service" /> Forced to reset password?
                                    <span></span>
                                </label>
                            </div>
                            <hr>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            <div class="form-group">
                                <label class="control-label col-md-3">Select2 Dropdown
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <select class="form-control select2me" name="options2">
                                        <option value="">Select...</option>
                                        <option value="Option 1">Option 1</option>
                                        <option value="Option 2">Option 2</option>
                                        <option value="Option 3">Option 3</option>
                                        <option value="Option 4">Option 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Datepicker</label>
                                <div class="col-md-4">
                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control" readonly name="datepicker">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <!-- /input-group -->
                                    <span class="help-block"> select a date </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Membership
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <div class="mt-radio-list" data-error-container="#form_2_membership_error">
                                        <label class="mt-radio">
                                            <input type="radio" name="membership" value="1" /> Fee
                                            <span></span>
                                        </label>
                                        <label class="mt-radio">
                                            <input type="radio" name="membership" value="2" /> Professional
                                            <span></span>
                                        </label>
                                    </div>
                                    <div id="form_2_membership_error"> </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Services
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-4">
                                    <div class="mt-checkbox-list" data-error-container="#form_2_services_error">
                                        <label class="mt-checkbox">
                                            <input type="checkbox" value="1" name="service" /> Service 1
                                            <span></span>
                                        </label>
                                        <label class="mt-checkbox">
                                            <input type="checkbox" value="2" name="service" /> Service 2
                                            <span></span>
                                        </label>
                                        <label class="mt-checkbox">
                                            <input type="checkbox" value="3" name="service" /> Service 3
                                            <span></span>
                                        </label>
                                    </div>
                                    <span class="help-block"> (select at least two) </span>
                                    <div id="form_2_services_error"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn green">Submit</button>
                                    <button type="button" class="btn default">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                    <button type="button" data-dismiss="modal" class="btn green">Continue Task</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END UPDATE MODAL--> 
@endsection

@section('scripts') 
<script src="/themes/admin/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

<script src="/themes/admin/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>

<script src="/js/admin/users/index.js" type="text/javascript"></script>
@endsection