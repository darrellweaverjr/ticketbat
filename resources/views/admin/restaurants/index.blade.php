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
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_model_update_general" data-toggle="tab" aria-expanded="true"> General </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_items" data-toggle="tab" aria-expanded="false"> Items </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_awards" data-toggle="tab" aria-expanded="false"> Awards </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_reviews" data-toggle="tab" aria-expanded="false"> Reviews </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_comments" data-toggle="tab" aria-expanded="false"> Comments </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_albums" data-toggle="tab" aria-expanded="false"> Albums </a>
                                    </li>
                                </ul>
                                <div class="tab-content" style="padding:20px">
                                    <div class="tab-pane active" id="tab_model_update_general">
                                        <div class="row">
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
                                        <div class="row">
                                            <label class="control-label">Description:</label>
                                            <div class="show-error">
                                                <textarea name="description" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_items">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_items_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Menu</th>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Price</th>
                                                        <th>Disabled</th>
                                                        <th>Image</th>
                                                        <th> </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_restaurant_items">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_awards">
                                        <div class="row">
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_reviews">
                                        <div class="row">
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_comments">
                                        <div class="row">
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_albums">
                                        <div class="row">
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
    <!-- BEGIN ADD/EDIT ITEMS MODAL-->
    <div id="modal_model_restaurant_items" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Item</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_restaurant_items" class="form-horizontal">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="restaurant_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="action" value="1" />
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Menu
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="restaurant_menu_id">
                                            @foreach($menu as $index=>$m)
                                                @if($m->parent_id == 0)
                                                    <option value="{{$m->id}}" @if($m->disabled>0) disabled @endif>{{$m->name}}</option>
                                                    @foreach ($m->children()->get() as $children)
                                                        <option value="{{$children->id}}" @if($children->disabled>0) disabled @endif>&nbsp;&nbsp;-&nbsp;&nbsp;{{$children->name}}</option>
                                                        @foreach ($children->children()->get() as $niece)
                                                            <option value="{{$niece->id}}" @if($niece->disabled>0) disabled @endif>&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;{{$niece->name}}</option>
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-3">Position
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="order">
                                            <option value="">Last</option>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-3">Name
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <input type="text" class="form-control" name="name" placeholder="Item name">
                                    </div>
                                    <label class="control-label col-md-3">Notes
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <input type="text" class="form-control" name="notes" placeholder="Notes for this item">
                                    </div>
                                    <label class="col-md-3 control-label">Price ($)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <input type="number" value="0.00" name="price" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                    </div>
                                    <label class="control-label col-md-3">Disabled
                                    </label>
                                    <div class="col-md-8">
                                        <input type="hidden" name="enabled" value="0"/>
                                        <input type="checkbox" class="make-switch" name="enabled" data-size="small" value="1" data-on-text="Yes" data-off-text="No" data-on-color="primary" data-off-color="danger">
                                    </div>
                                    <label class="col-md-3 control-label">Description
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <label class="control-label col-md-3">Image
                                    </label>
                                    <div class="col-md-8 show-error" >
                                        <center>
                                            <input type="hidden" name="url"/>
                                            <button type="button" id="btn_restaurant_item_upload_image" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="url" alt="- No image -" src="" width="200px" height="200px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_items').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_restaurant_items" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT ITEMS MODAL-->
@endsection

@section('scripts')
<script src="/js/admin/restaurants/index.js" type="text/javascript"></script>
@endsection
