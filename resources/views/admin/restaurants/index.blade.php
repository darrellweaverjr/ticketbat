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
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search">Search
                                <i class="fa fa-search"></i>
                            </button>
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
                                <th width="8%">Status</th>
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
@endsection

@section('scripts')
<script src="/js/admin/restaurants/index.js" type="text/javascript"></script>
@endsection
