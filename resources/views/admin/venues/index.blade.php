@php $page_title='Venues' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List, add, edit and remove venues.</small>
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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search">Filter
                                <i class="fa fa-filter"></i>
                            </button>
                            @endif
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true">Remove
                                <i class="fa fa-remove"></i>
                            </button>
                            @endif
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                            <button id="btn_model_preview" class="btn sbold purple" disabled="true">Preview
                                <i class="fa fa-tablet"></i>
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
                                <th width="10%">Logo</th>
                                <th width="73%">Description</th>
                                <th width="5%">Featured</th>
                                <th width="10%">Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venues as $index=>$v)
                            <tr @if(!empty($v->errors)) class="danger" @endif>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$v->id}}" value="{{$v->name}}" data-preview="{{env('IMAGE_URL_OLDTB_SERVER','https://www.ticketbat.com')}}/venue/{{$v->slug}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td data-order="{{$v->name}}" style="text-align:center;color:red" title="Click on the image to go to the website.">
                                    <a><img alt="- No image -" height="110px" width="110px" src="{{$v->image_url}}"/></a>
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-title">
                                        <h4><b>{{$v->name}}&emsp;
                                            <a style="font-style:italic;font-weight:bold;font-size:small!important" href='http://maps.google.com/?q={{$v->address}} {{$v->city}} {{$v->state}} {{$v->country}} {{$v->zip}}' target="_blank"><span class="fa fa-map-marker"></span> {{$v->address}}, {{$v->city}}, {{$v->state}}, {{$v->country}} {{$v->zip}}</a>
                                        </b></h4>
                                    </div>
                                    <div class="search-content note note-info" style="font-style:italic;font-size:smaller">{{$v->description}}</div>
                                </td>
                                <td><center>{{$v->is_featured}}</center></span>
                                </td>
                                <td class="search-content" style="font-style:italic;font-size:smaller;color:red">@php echo $v->errors @endphp</td>
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
                                        <a href="#tab_model_update_tickets" data-toggle="tab" aria-expanded="false"> Tickets </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_location" data-toggle="tab" aria-expanded="false"> Location </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_reports" data-toggle="tab" aria-expanded="false"> Reports </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_stages" data-toggle="tab" aria-expanded="true"> Stages </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_stage_images" data-toggle="tab" aria-expanded="true"> Stages Images </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_images" data-toggle="tab" aria-expanded="true"> Images </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_banners" data-toggle="tab" aria-expanded="true"> Banners </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_videos" data-toggle="tab" aria-expanded="true"> Videos </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_ads" data-toggle="tab" aria-expanded="true"> Ads </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_model_update_general">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> General </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Name
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="name" class="form-control" placeholder="My Venue" />
                                                    </div>
                                                    <label class="control-label col-md-3">Slug
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6 show-error">
                                                        <input type="text" name="slug" class="form-control" readonly="true" />
                                                    </div>
                                                    <div class="col-md-3 show-error">
                                                        <button class="btn btn-block" id="go_to_slug" type="button">Go to
                                                            <i class="fa fa-link"></i>
                                                        </button>
                                                    </div>
                                                    <label class="control-label col-md-3">Restriction
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="restrictions">
                                                            @foreach($restrictions as $index=>$r)
                                                            <option selected value="{{$r}}">{{$r}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Featured</label>
                                                    <div class="col-md-2">
                                                        <input type="hidden" name="is_featured" value="0"/>
                                                        <input type="checkbox" class="make-switch" name="is_featured" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> Social Media & Others </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <div class="col-md-1"><a data-original-title="youtube" class="social-icon social-icon-color youtube"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="youtube" class="form-control" placeholder="venue-youtube" />
                                                    </div>
                                                    <div class="col-md-1"><a data-original-title="facebook" class="social-icon social-icon-color facebook"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="facebook" class="form-control" placeholder="venue-url" />
                                                    </div>
                                                    <div class="col-md-1"><a data-original-title="twitter" class="social-icon social-icon-color twitter"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="twitter" class="form-control" placeholder="venue-username" />
                                                    </div>
                                                    <div class="col-md-1"><a data-original-title="googleplus" class="social-icon social-icon-color googleplus"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="googleplus" class="form-control" placeholder="venue-id" />
                                                    </div>
                                                    <div class="col-md-1"><a data-original-title="yahoo" class="social-icon social-icon-color yahoo"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="yelpbadge" class="form-control" placeholder="venue-link" />
                                                    </div>
                                                    <div class="col-md-1"><a data-original-title="instagram" class="social-icon social-icon-color instagram"></a>
                                                    </div>
                                                    <div class="col-md-11 show-error">
                                                        <input type="text" name="instagram" class="form-control" placeholder="venue-username" />
                                                    </div>
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
                                    <div class="tab-pane" id="tab_model_update_tickets" style="padding:0 20px">
                                        <div class="row">
                                            <label class="control-label">
                                                <span class="required">Default ticket's values for this venue</span>
                                            </label><br><br>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Default Fixed Proccessing Fee</label>
                                                        <div class="col-md-6">
                                                            <input type="number" value="0.00" name="default_processing_fee" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Default % Proccessing Fee</label>
                                                        <div class="col-md-6">
                                                            <input type="number" value="0.00" name="default_percent_pfee" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Default Fixed Commission</label>
                                                        <div class="col-md-6">
                                                            <input type="number" value="0.00" name="default_fixed_commission" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Default Commission %</label>
                                                        <div class="col-md-6">
                                                            <input type="number" value="0.00" name="default_percent_commission" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label">
                                                <span class="required">Options to admin/seller</span>
                                            </label><br><br>
                                            <div class="form-group">
                                                <label class="control-label col-md-7">Disable cash breakdown on "shopping cart" page (hide it and fill it out by default)</label>
                                                <div class="col-md-5">
                                                    <input type="hidden" name="disable_cash_breakdown" value="0"/>
                                                    <input type="checkbox" class="make-switch input-large" name="disable_cash_breakdown" value="1" data-on-text="Yes, do it" data-off-text="No, keep it" data-on-color="danger" data-off-color="primary">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-7">After purchase ("complete" page) enable link to return to "buy" page</label>
                                                <div class="col-md-5">
                                                    <input type="hidden" name="after_purchase_link" value="0"/>
                                                    <input type="checkbox" class="make-switch input-large" name="after_purchase_link" value="1" data-on-text="Enable link" data-off-text="Disable link" data-on-color="primary" data-off-color="danger">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label">
                                                <span class="required">Ticket Info *</span>
                                            </label>
                                            <div class="form-group" >
                                                <div class="show-error" style="padding:20px">
                                                    <textarea name="ticket_info" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label">
                                                <span class="required">Cuttoff text</span>
                                            </label>
                                            <div class="form-group">
                                                <div class="show-error" style="padding:20px">
                                                    <textarea name="cutoff_text" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_location">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Address
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-8 show-error">
                                                    <input type="text" name="address" class="form-control" placeholder="000 Main St" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="control-label col-md-2">City
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-3 show-error">
                                                    <input type="text" name="city" class="form-control" placeholder="Las Vegas" />
                                                </div>
                                                <label class="control-label col-md-1">State
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-1 show-error">
                                                    <input type="text" name="state" class="form-control" placeholder="NV" />
                                                </div>
                                                <label class="control-label col-md-1">Zip
                                                    <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-2 show-error">
                                                    <input type="number" name="zip" class="form-control" placeholder="00000" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_reports">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label" style="padding-left:30px">Email for Weekly Sales Reports:
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="weekly_email" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" />
                                                    </div>
                                                    <label class="control-label" style="padding-left:30px">Email for Accounting:
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="accounting_email" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="control-label col-md-9">Send weekly sales emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="weekly_sales_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="weekly_sales_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send daily sales emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="daily_sales_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="daily_sales_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send financial report emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="financial_report_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="financial_report_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send weekly promotions</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="enable_weekly_promos" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="enable_weekly_promos" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_stages">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_stage_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_venue_stages" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_stage_images">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_stage_images_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_venue_stage_images" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_images">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_image_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_venue_images" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_banners">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_banner_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_venue_banners" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_videos">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_video_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_venue_videos" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_ads">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_ads_add" class="btn sbold bg-green"> Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Type</th>
                                                        <th>URL</th>
                                                        <th>Order</th>
                                                        <th>Price</th>
                                                        <th>Clicks</th>
                                                        <th>Start</th>
                                                        <th>End</th>
                                                        <th> </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_venue_ads">
                                                </tbody>
                                            </table>
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
    <!-- BEGIN SEARCH MODAL-->
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:400px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Filter Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/venues" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="onlyerrors" class="col-md-9">Only With Error:<br>
                                        <span class="required">(Empty logo image or no stages)</span>
                                    </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-3" name="onlyerrors" style="width:65px !important">
                                        <option @if($onlyerrors==0) selected @endif value="0">No</option>
                                        <option @if($onlyerrors==1) selected @endif value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_search').trigger('reset')">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa" onclick="$('#modal_model_search').modal('hide'); swal({
                                                                                                    title: 'Searching information',
                                                                                                    text: 'Please, wait.',
                                                                                                    type: 'info',
                                                                                                    showConfirmButton: false
                                                                                                });" >Search</button>
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
    <!-- BEGIN ADD/REMOVE VENUESTAGESS MODAL-->
    <div id="modal_model_venue_stages" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:900px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Stage</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_stages">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="venue_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Name
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" class="form-control" name="name" value=""/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Description
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <textarea name="description" class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" id="subform_venue_stages">
                                        <label class="control-label col-md-3">Image
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_venue_upload_stages" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="323px" height="270px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="control-label">
                                        <span class="required"> Ticket types order at the stage:</span>
                                    </label><hr>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">1st</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[1]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">2nd</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[2]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">3rd</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[3]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">4th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[4]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">5th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[5]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">6th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[6]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">7th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[7]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">8th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[8]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">9th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[9]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">10th</label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="ticket_type[10]">
                                                <option value="">- Select -</option>
                                                @foreach($ticket_types as $index=>$tt)
                                                    <option value="{{$index}}">{{$tt}}</option>
                                                @endforeach
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
                                    <button type="button" id="submit_model_venue_stages" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE VENUESTAGES MODAL-->
    <!-- BEGIN ADD/REMOVE STAGEIMAGES MODAL-->
    <div id="modal_model_venue_stage_images" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Stage Images</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_stage_images">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="stage_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Stage
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="stage_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Ticket Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="ticket_type">
                                            @foreach($ticket_types as $index=>$tt)
                                                <option value="{{$index}}">{{$tt}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Image Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="image_type">
                                            @foreach($image_types as $index=>$it)
                                                <option value="{{$index}}">{{$it}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Caption
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" class="form-control" name="caption" value=""/>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_venue_stage_images">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error" >
                                        <center>
                                            <input type="hidden" name="url"/>
                                            <button type="button" id="btn_venue_upload_stage_images" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="url" alt="- No image -" src="" width="323px" height="270px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_venue_stage_images" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE STAGEIMAGES MODAL-->
    <!-- BEGIN ADD/REMOVE VENUEIMAGES MODAL-->
    <div id="modal_model_venue_images" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Image</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_images">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="venue_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="image_type">
                                            @foreach($image_types as $index=>$it)
                                                <option value="{{$index}}">{{$it}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Caption
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" class="form-control" name="caption" value=""/>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_venue_images">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error" >
                                        <center>
                                            <input type="hidden" name="url"/>
                                            <button type="button" id="btn_venue_upload_images" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="url" alt="- No image -" src="" width="323px" height="270px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_venue_images" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE VENUEIMAGES MODAL-->
    <!-- BEGIN ADD/REMOVE VENUEBANNERS MODAL-->
    <div id="modal_model_venue_banners" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Banner</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_banners">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="parent_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Showed on
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                    @foreach($banner_types as $index=>$bt)
                                        <label class="mt-checkbox"><input type="checkbox" name="type[]" value="{{$index}}"/>{{$bt}}<span></span></label><br>
                                    @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Link to
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" class="form-control" name="url" value=""/>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_venue_banners">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error" >
                                        <center>
                                            <input type="hidden" name="file"/>
                                            <button type="button" id="btn_venue_upload_banners" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="file" alt="- No image -" src="" width="323px" height="270px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_venue_banners" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE VENUEBANNERS MODAL-->
    <!-- BEGIN ADD/REMOVE VENUEVIDEOS MODAL-->
    <div id="modal_model_venue_videos" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Video</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_videos">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="venue_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="video_type">
                                            @foreach($video_types as $index=>$vt)
                                                <option value="{{$index}}">{{$vt}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Embed
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <textarea name="embed_code" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Description</label>
                                    <div class="col-md-9 show-error">
                                        <textarea name="description" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_venue_videos" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE VENUEBANNERS MODAL-->
    <!-- BEGIN ADD/EDIT VENUE ADS MODAL-->
    <div id="modal_model_venue_ads" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:900px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Ads</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_venue_ads">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="venue_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="action" value="1" />
                        <div class="form-body">
                            <div class="row" style="padding:10px;">
                                <div class="form-group col-md-6" id="subform_venue_ads">
                                    <label class="control-label">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="show-error" >
                                        <center>
                                            <input type="hidden" name="image"/>
                                            <button type="button" id="btn_venue_upload_ads" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="image" alt="- No image -" src="" width="200px" height="200px" />
                                        </center>
                                    </div>
                                </div> 
                                <div class="form-group col-md-6" style="margin-bottom:60px;">
                                    <label class="control-label col-md-3">Type
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="type" required="true">
                                            @foreach($ads_types as $index=>$at)
                                                <option value="{{$index}}">{{$at}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                    <label class="col-md-3 control-label">Order
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input class="form-control" type="number" value="1" name="order" required="true" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 ">
                                    </div><br>
                                    <label class="control-label col-md-3">URL
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input class="form-control" type="url" name="url" required="true">
                                       <i><small>If the URL link is in Ticketbat set this like: "/event/my-slug". Otherwise put the whole URL: "http://venue.com/mylink".</small></i>
                                    </div>                               
                                    <label class="col-md-3 control-label">Price
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input class="form-control" type="number" value="0.00" name="price" required="true" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                    </div>
                                    <label class="col-md-3 control-label">Clicks
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input class="form-control" type="number" value="0" name="clicks" required="true" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 ">
                                    </div>
                                    <label class="control-label col-md-3">Date range:
                                    </label>
                                    <div class="input-group col-md-9" id="ads_date">
                                        <input type="text" class="form-control" name="start_date" readonly="true" value="{{date('Y-m-d H:i')}}" required="true" style="font-size:11px!important;">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control" name="end_date" readonly="true" value="{{date('Y-m-d H:i',strtotime('+29 day'))}}" required="true" style="font-size:11px!important;">
                                        <span class="input-group-btn">
                                            <button class="btn default date-range-toggle" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_venue_ads').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_venue_ads" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT VENUE ADS MODAL-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/admin/venues/index.js" type="text/javascript"></script>
@endsection
