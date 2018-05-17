@php $page_title='Users' @endphp
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
                            <button id="btn_model_search" class="btn sbold grey-salsa">Filter
                                <i class="fa fa-filter"></i>
                            </button>
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true">Remove
                                <i class="fa fa-remove"></i>
                            </button>
                            @endif
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                            <button id="btn_model_purchases" class="btn sbold bg-purple" disabled="true">Purchases
                                <i class="fa fa-ticket"></i>
                            </button>
                            @if(Auth::user()->user_type_id == 1)
                            <button id="btn_model_impersonate" class="btn sbold bg-purple" disabled="true">Impersonate
                                <i class="fa fa-rocket"></i>
                            </button>
                            @endif
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
                                <th width="5%">ID</th>
                                <th width="20%">Email</th>
                                <th width="100">First Name</th>
                                <th width="100">Last Name</th>
                                <th width="15%">Phone</th>
                                <th width="10%">Role</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index=>$u)
                            <tr @if($u->is_active=='Inactive') class="danger" @endif>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$u->id}}" value="{{$u->email}} * {{$u->first_name}} {{$u->last_name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>{{$u->id}}</td>
                                <td><a href="mailto:{{$u->email}}" target="_top">{{$u->email}}</a></td>
                                <td>{{$u->first_name}}</td>
                                <td>{{$u->last_name}}</td>
                                <td>{{$u->phone}}</td>
                                <td>{{$u->user_type}}</td>
                                <td>{{$u->is_active}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN SEARCH MODAL-->
    <div id="modal_model_search" class="modal fade" data-modal="{{$modal}}" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:470px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Filter Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/users" id="form_model_search">
                        <input type="hidden" name="_token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">First Name:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" name="first_name" class="form-control input-large" value="{{$search['first_name']}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Last Name:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" name="last_name" class="form-control input-large" value="{{$search['last_name']}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Email:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" name="email" class="form-control input-large" value="{{$search['email']}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Role:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control input-large" name="user_type_id">
                                                <option @if(empty($search['user_type_id'])) selected @endif value="0">All</option>
                                                @foreach($user_types as $index=>$t)
                                                <option @if($t->id==$search['user_type_id']) selected @endif value="{{$t->id}}">{{$t->user_type}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Status:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control  input-large" name="is_active">
                                                <option @if(empty($search['is_active'])) selected @endif value="0">All</option>
                                                <option @if($search['is_active']==-1) selected @endif value="-1">Inactive</option>
                                                <option @if($search['is_active']==1) selected @endif value="1">Active</option>
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
                                                <input name="zip" type="number" class="form-control" placeholder="#####" /> </div>
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
                                                <input name="phone" class="form-control" placeholder="### ### ####" /> </div>
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
                                        <hr>
                                        <div class="form-group">
                                            <label class="control-label col-md-2"></label>
                                            <div class="col-md-8 show-error">
                                                <label>
                                                    <input type="hidden"   name="update_customer" value="0" />
                                                    <input type="checkbox" name="update_customer" value="1" checked="true"/> Update associated customer with this information?
                                                </label><br>
                                                <label>
                                                    <input type="hidden"   name="update_transaction_customer" value="0" />
                                                    <input type="checkbox" disabled="true" name="update_transaction_customer" value="1" /> Update associated cardholder name in transactions using the Customer ID?
                                                </label><br>
                                                <label>
                                                    <input type="hidden"   name="update_transaction_user" value="0" />
                                                    <input type="checkbox" disabled="true" name="update_transaction_user" value="1" /> Update associated cardholder name in transactions using the User ID?
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
                                                    <span class="required">Permitted to <b>CHECK IN</b> guests and to <b>SELL TICKETS</b> at venue(s):</span>
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
