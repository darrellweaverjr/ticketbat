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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
                            <button id="btn_model_menu" class="btn sbold bg-purple" disabled="true">Menu
                                <i class="fa fa-spoon"></i>
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
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_awards_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Awarded</th>
                                                        <th>Posted</th>
                                                        <th> </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_restaurant_awards">
                                                </tbody>
                                            </table>
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
    <!-- BEGIN MENU -->
    @include('admin.restaurants.menu')
    <!-- END MENU -->
    <!-- BEGIN ITEMS -->
    @include('admin.restaurants.items')
    <!-- END ITEMS -->
    <!-- BEGIN AWARDS -->
    @include('admin.restaurants.awards')
    <!-- END AWARDS -->
@endsection

@section('scripts')
<script src="/js/admin/restaurants/menu.js" type="text/javascript"></script>
<script src="/js/admin/restaurants/items.js" type="text/javascript"></script>
<script src="/js/admin/restaurants/awards.js" type="text/javascript"></script>
<script src="/js/admin/restaurants/index.js" type="text/javascript"></script>
@endsection
