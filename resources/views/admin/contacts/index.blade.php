@php $page_title='Contact Logs' @endphp
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
        <small> - List and view contact logs.</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} </span>
                    </div>
                    <div class="actions">
                        @if(in_array('Other',Auth::user()->user_type->getACLs()['CONTACTS']['permission_types']))
                        <div id="start_end_date" class="pull-left tooltips btn btn-sm" data-container="body" data-placement="bottom">
                            <i class="icon-calendar"></i>&nbsp;
                            <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="btn-group">
                        </div>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="8%"> Date </th>
                                <th width="17%"> Client </th>
                                <th width="75%"> Message </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $index=>$c)
                            <tr>
                                <td width="8%" data-order="{{strtotime($c->created)}}"> {{date('m/d/Y',strtotime($c->created))}} <br> {{date('g:ia',strtotime($c->created))}} </td>
                                <td width="17%"> <b class="uppercase"><a href="mailto:{{$c->email}}" target="_top">{{$c->name}}</a></b>
                                    <p><small><i>
                                        @if($c->email)Email: <a href="mailto:{{$c->email}}" target="_top">{{$c->email}}</a> <br>@endif
                                        @if($c->phone)Phone: <a> {{$c->phone}} </a><br>@endif
                                    </i></small></p>
                                <td width="75%">
                                    <p><small><i>
                                        @if($c->show_name)Show: <a>{{$c->show_name}}</a> @if($c->show_time) - Date/Time: <a> {{date('l, F jS, Y h:i A',strtotime($c->show_time))}} </a> @endif <br>@endif
                                        @if($c->system_info)System: <a> {{$c->system_info}} </a>@endif
                                    </i></small></p>
                                    <i>{{$c->message}}</i> </td>
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
    <div style="display: none;">
        <form method="post" action="/admin/contacts" id="form_model_search">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <input name="start_date" type="hidden" value="{{$start_date}}"/>
            <input name="end_date" type="hidden" value="{{$end_date}}"/>
        </form>
    </div>
    <!-- END UPDATE MODAL-->
@endsection

@section('scripts')
<script src="/js/admin/contacts/index.js" type="text/javascript"></script>
@endsection
