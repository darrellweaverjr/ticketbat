@php $page_title='Ticket Types' @endphp
@extends('layouts.admin')
@section('title', 'Ticket Types' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove Ticket Types.</small>
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
                                <i class="fa fa-edit"></i><!--
                            </button>
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>-->
                            <button id="btn_model_user_type" class="btn sbold bg-purple" disabled="true"> Styles 
                                <i class="fa fa-users"></i>
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
                                <th width="50%"> Ticket Type </th>
                                <th width="48%"> Ticket Class </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $index=>$t)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$index}}" value="{{$index}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="50%"> {{$t['ticket_type']}} </td>
                                <td width="48%"> {{$t['ticket_type_class']}} </td>
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
        <div class="modal-dialog" style="width:30% !important;">
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
                                <label class="control-label col-md-4">Type
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" name="ticket_type" class="form-control" placeholder="My type" /> </div>
                            </div>
                            <div id="div_model_update_advanced">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Style
                                    </label>
                                    <div class="col-md-8 show-error">
                                        <select class="form-control" name="ticket_type_class">
                                            @foreach($ticket_styles as $index=>$s)
                                            <option value="{{$index}}"> {{$s}} </option>
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
<script src="/js/admin/ticket_types/index.js" type="text/javascript"></script>
@endsection