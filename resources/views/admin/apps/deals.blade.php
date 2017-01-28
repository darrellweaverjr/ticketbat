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
                                <th width="18%"> Image </th>
                                <th width="20%"> Type </th>
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
                                <td class="search-item clearfix" width="18%"> 
                                    <div class="search-content col-md-2">
                                        <center style="color:red;"><i><b><img alt="- No image -" height="110px" width="110px" src="{{$d->image_url}}"/></b></i></center>
                                    </div>
                                </td>
                                <td class="search-item clearfix" width="20%"> 
                                    <div class="search-content col-md-10">
                                        <small><i>
                                            @if($d->url) URL:<br><a href="{{env('IMAGE_URL_OLDTB_SERVER').$d->url}}" target="_blank">{{$d->url}} </a>@endif
                                            @if($d->name && $d->coupon_code) Show/Coupon:<br><a>{{$d->name}} / {{$d->coupon_code}}</a>@endif
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
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Image
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_deals_upload_image_url" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="200px" height="200px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="type">
                                                <option value="url">URL</option>
                                                <option value="show_coupon">Show / Coupon</option>
                                            </select>                                            
                                        </div>    
                                    </div>
                                    <div class="form-group" id="subform_show_coupon">
                                        <label class="control-label col-md-3">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="show_id">
                                                @foreach($shows as $index=>$s)
                                                <option value="{{$s->id}}">{{$s->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>  
                                        <label class="control-label col-md-3">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="discount_id">
                                                @foreach($discounts as $index=>$d)
                                                <option value="{{$d->id}}">{{$d->code}}</option>
                                                @endforeach
                                            </select>
                                        </div>  
                                    </div>
                                    <div class="form-group" id="subform_url">
                                        <label class="control-label col-md-3">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="url" class="form-control" placeholder="/event/event"> </div>
                                        </div>  
                                    </div>
                                </div>
                            </div> 
                            <div class="row" style="padding-right:40px;padding-left:40px;">
                                <label class="control-label">
                                    <span class="required">Display to customers that have purchases on:</span>
                                </label><hr>
                                <div class="col-md-6" style="padding-right:30px">
                                    <div class="form-group">
                                        <label class="control-label">Shows
                                            <span class="required"> * </span>
                                        </label>
                                        <select class="form-control" name="shows[]" multiple="multiple" size="10">
                                            @foreach($shows as $index=>$s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-left:30px;">
                                    <div class="form-group">
                                        <label class="control-label">Venues
                                            <span class="required"> * </span>
                                        </label>
                                        <select class="form-control" name="venues[]" multiple="multiple" size="10">
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