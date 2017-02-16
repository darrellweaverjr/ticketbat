@php $page_title='Deals' @endphp
@extends('layouts.admin')
@section('title', 'Deals' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
<!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> for the customer App.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['APPS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['APPS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['APPS']['permission_types']))
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
                                <th width="10%"> Image </th>
                                <th width="28%"> Type </th>
                                <th width="30%"> For purchases on shows </th>
                                <th width="30%"> For purchases on venues </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deals as $index=>$d)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$d->id}}" value="{{$d->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td class="search-item clearfix" width="10%"> 
                                    <div class="search-content col-md-2">
                                        <center style="color:red;"><i><b><img alt="- No image -" height="110px" width="110px" src="{{$d->image_url}}"/></b></i></center>
                                    </div>
                                </td>
                                <td class="search-item clearfix" width="28%"> 
                                    <div class="search-content col-md-10">
                                        <small><i>
                                            @if($d->url) URL: <a href="{{env('IMAGE_URL_OLDTB_SERVER').$d->url}}" target="_blank">{{$d->url}} </a>@endif
                                            @if($d->name) Show: <a>{{$d->name}}</a><br>@endif
                                            @if($d->code) Coupon: <a>{{$d->code}}</a>@endif
                                        </i></small>
                                    </div>
                                </td>
                                <td class="search-item clearfix" width="30%"> 
                                    <div class="search-content col-md-10">
                                        <small><i>
                                                @php $displayToShows = explode(',',$d->displayToShows) @endphp
                                                @if(count ($displayToShows))
                                                    @foreach($shows as $index=>$s)
                                                        @if(in_array($s->id,$displayToShows))
                                                        <a>{{$s->name}} </a>,@endif
                                                    @endforeach
                                                @endif
                                        </i></small>
                                    </div>
                                </td>
                                <td class="search-item clearfix" width="30%"> 
                                    <div class="search-content col-md-10">
                                        <small><i>
                                            @php $displayToVenues = explode(',',$d->displayToVenues) @endphp
                                            @if(count ($displayToVenues))
                                                @foreach($venues as $index=>$v)
                                                    @if(in_array($v->id,$displayToVenues))
                                                    <a>{{$v->name}} </a>,@endif
                                                @endforeach
                                            @endif
                                        </i></small>
                                    </div>
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
        <div class="modal-dialog" style="width:700px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title"></center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" >
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input name="id" type="hidden" value=""/>
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Image
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_deals_upload_image_url" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="315px" height="315px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="show-error">
                                            <select class="form-control" name="type">
                                                <option value="url">URL</option>
                                                <option value="show_coupon">Show / Coupon</option>
                                            </select> <hr>
                                            <div class="form-group" id="subform_show_coupon">
                                                <select class="form-control" name="show_id">
                                                    @foreach($shows as $index=>$s)
                                                    <option value="{{$s->id}}">{{$s->name}}</option>
                                                    @endforeach
                                                </select>
                                                <select class="form-control" name="discount_id">
                                                    @foreach($discounts as $index=>$d)
                                                    <option value="{{$d->id}}">{{$d->code}} = {{$d->description}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="subform_url">
                                                <input type="text" name="url" class="form-control" placeholder="/event/event"> 
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" style="padding-right:15px">
                                    <div class="form-group">
                                        <label class="control-label">Shows
                                            <span class="required"> * </span>
                                        </label>
                                        <select class="form-control" name="displayToShows[]" multiple="multiple" size="11">
                                            @foreach($shows as $index=>$s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-left:15px;">
                                    <div class="form-group">
                                        <label class="control-label">Venues
                                            <span class="required"> * </span>
                                        </label>
                                        <select class="form-control" name="displayToVenues[]" multiple="multiple" size="11">
                                            @foreach($venues as $index=>$v)
                                            <option value="{{$v->id}}">{{$v->name}}</option>
                                            @endforeach
                                        </select>
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
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/pages/scripts/table-datatables-buttons.min.js" type="text/javascript"></script>
<script src="/js/admin/apps/deals.js" type="text/javascript"></script>
@endsection