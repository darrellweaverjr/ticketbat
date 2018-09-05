@php $page_title='Sliders' @endphp
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
        <small> - List, add, edit and remove sliders.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['SLIDERS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['SLIDERS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['SLIDERS']['permission_types']))
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
                                <th width="10%">Order</th>
                                <th width="33%">Image</th>
                                <th width="20%">Slug</th>
                                <th width="20%">Alt</th>
                                <th width="15%">Filter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sliders as $index=>$s)
                            <tr>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$s->id}}" value="Slider # {{$s->n_order}} - {{$s->alt}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td style="text-align:center;font-size:30px">{{$s->n_order}}</td>
                                <td><center><b><img alt="- No image -" height="100px" width="400px" src="{{$s->image_url}}"/></b></center></td>
                                <td><a href="{{env('IMAGE_URL_OLDTB_SERVER').$s->slug}}" target="_blank">{{$s->slug}}</a></td>
                                <td>{{$s->alt}}</td>
                                <td>{{$s->filter}}</td>
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
        <div class="modal-dialog">
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
                                <div class="form-group">
                                    <label class="control-label col-md-3">Slug
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <input type="text" class="form-control" name="slug" value=""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Alt
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <input type="text" class="form-control" name="alt" value=""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Filter city
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="filter">
                                            <option selected value="">- No filter -</option>
                                            @foreach($cities as $c)
                                            <option value="{{$c->city}}">{{$c->city}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_sliders">
                                    <label class="control-label col-md-3">Order
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="n_order">
                                            @for ($i = 1; $i <= count($sliders); $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-8 show-error" >
                                        <center>
                                            <input type="hidden" name="image_url"/>
                                            <button type="button" id="btn_sliders_upload_images" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="image_url" alt="- No image -" src="" width="323px" height="110px" />
                                        </center>
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
<script src="/js/admin/sliders/index.js" type="text/javascript"></script>
@endsection
