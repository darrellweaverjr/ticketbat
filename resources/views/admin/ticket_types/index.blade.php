@php $page_title='Ticket Types' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<style>@php echo $ticket_types_css; @endphp</style>
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List, add, edit and remove ticket types.</small>
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
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['TYPES']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['TYPES']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            <!--<button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove
                                <i class="fa fa-remove"></i>
                            </button>-->
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['TYPES']['permission_types']))
                            <button id="btn_model_styles" class="btn sbold bg-purple">Styles
                                <i class="fa fa-deviantart"></i>
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
                                <th width="30%">Ticket Type</th>
                                <th width="28%">Ticket Class</th>
                                <th width="30%">Preview</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $index=>$t)
                            <tr>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$index}}" value="{{$index}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>{{$t['ticket_type']}}</td>
                                <td>{{$t['ticket_type_class']}}</td>
                                <td><button class="btn {{$t['ticket_type_class']}}">{{$t['ticket_type']}}</button></td>
                                <td><input type="checkbox" class="make-switch" name="active" value="{{$index}}" {{$t['active']}} data-size="mini" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger"></td>
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
        <div class="modal-dialog" style="width:350px !important;">
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
                                <label class="control-label col-md-3">Type
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9 show-error">
                                    <input type="text" name="ticket_type" class="form-control" placeholder="My type" /> </div>
                            </div>
                            <div id="div_model_update_advanced">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Style
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="ticket_type_class">
                                            @foreach($ticket_styles as $index=>$s)
                                            <option value="{{$index}}">{{$s}}</option>
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
    <!-- BEGIN STYLE MODAL-->
    <div id="modal_model_style" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Styles</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <div class="form-body">
                        <div class="col-md-6">
                            <form method="post" id="form_model_style" class="form-horizontal">
                                <label class="control-label">
                                    <span class="required">In the System</span>
                                </label><hr>
                                <div class="form-group">
                                    <div class="col-md-6 show-error">
                                        <button type="button" id="btn-add-style" class="btn sbold btn-block green">Add
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-6 show-error">
                                        <button type="button" id="btn-remove-style" class="btn sbold btn-block red">Remove
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 show-error">
                                        <select class="form-control" name="ticket_type_class" multiple="" size="16">
                                            @foreach($ticket_styles as $index=>$s)
                                            <option value="{{$index}}">{{$s}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 show-error">
                                        <button type="button" id="btn-preview" class="btn btn-block">Preview Selected Style</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="post" id="form_model_file" class="form-horizontal">
                                <label class="control-label">
                                    <span class="required">In the Cloud</span>
                                </label><hr>
                                <div class="form-group">
                                    <div class="col-md-12 show-error">
                                        <button type="button" id="btn-upload-style" class="btn sbold btn-block blue">Upload
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 show-error">
                                        <textarea name="ticket_type_file" class="form-control input-block-level" rows="17">
                                            @php echo $ticket_types_css; @endphp
                                        </textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="location.reload();">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END STYLES MODAL-->
@endsection

@section('scripts')
<script src="/js/admin/ticket_types/index.js" type="text/javascript"></script>
@endsection
