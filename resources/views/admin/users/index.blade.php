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
        <small>statistics, charts, recent events and reports</small>
    </h1>
    <!-- END PAGE TITLE-->
    
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="note note-danger">
                <p> NOTE: The below datatable is not connected to a real database so the filter and sorting is just simulated for demo purposes only. </p>
            </div>
            <!-- Begin: Demo Datatable 1 -->
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject font-dark sbold uppercase">Ajax Datatable</span>
                    </div>
                    <div class="actions">
                        <div class="btn-group btn-group-devided" data-toggle="buttons">
                            <label class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm active">
                                <input type="radio" name="options" class="toggle" id="option1">Actions</label>
                            <label class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm">
                                <input type="radio" name="options" class="toggle" id="option2">Settings</label>
                        </div>
                        <div class="btn-group">
                            <a class="btn red btn-outline btn-circle" href="javascript:;" data-toggle="dropdown">
                                <i class="fa fa-share"></i>
                                <span class="hidden-xs"> Tools </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="javascript:;"> Export to Excel </a>
                                </li>
                                <li>
                                    <a href="javascript:;"> Export to CSV </a>
                                </li>
                                <li>
                                    <a href="javascript:;"> Export to XML </a>
                                </li>
                                <li class="divider"> </li>
                                <li>
                                    <a href="javascript:;"> Print Invoices </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <span> </span>
                            <select class="table-group-action-input form-control input-inline input-small input-sm">
                                <option value="">Select...</option>
                                <option value="Cancel">Cancel</option>
                                <option value="Cancel">Hold</option>
                                <option value="Cancel">On Hold</option>
                                <option value="Close">Close</option>
                            </select>
                            <button class="btn btn-sm green table-group-action-submit">
                                <i class="fa fa-check"></i> Submit</button>
                        </div>
                        <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax1">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="2%">
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" />
                                            <span></span>
                                        </label>
                                    </th>
                                    <th width="5%"> Email </th>
                                    <th width="100"> First Name </th>
                                    <th width="100"> Last Name </th>
                                    <th width="20%"> Phone </th>
                                    <th width="15%"> Role </th>
                                    <th width="10%"> Active </th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td> </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="order_id"> </td>                                    
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="order_first_name"> </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="order_last_name"> </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="order_phone"> </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="order_user_type"> </td>                                    
                                    <td>
                                        <select name="order_status" class="form-control form-filter input-sm">
                                            <option value="">Select...</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-sm green btn-outline filter-submit margin-bottom">
                                                <i class="fa fa-search"></i> Search</button>
                                        </div>
                                        <button class="btn btn-sm red btn-outline filter-cancel">
                                            <i class="fa fa-times"></i> Reset</button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody> 
                                @php print_r($users[0]->user_type->user_type) @endphp
                                @foreach($users as $index=>$u)
                                    <tr>
                                        <td width="2%">
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="group-checkable" value="{{$u->id}}" data-set="#sample_2 .checkboxes" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="15%"> {{$u->email}} </td>
                                        <td width="100"> {{$u->first_name}} </td>
                                        <td width="100"> {{$u->last_name}} </td>
                                        <td width="20%"> {{$u->phone}} </td>
                                        <td width="15%"> @php print_r($users[$index]->user_type->user_type) @endphp </td>
                                        <td width="10%"> {{$u->is_active}} </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End: Demo Datatable 1 -->
        </div>
    </div>
                    
@endsection

@section('scripts') 
<script src="/themes/admin/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/pages/scripts/table-datatables-buttons.min.js" type="text/javascript"></script>

<script src="/themes/admin/assets/pages/scripts/table-datatables-ajax.min.js" type="text/javascript"></script>
@endsection