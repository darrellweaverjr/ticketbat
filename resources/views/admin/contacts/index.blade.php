@php $page_title='Contact Logs' @endphp
@extends('layouts.admin')
@section('title', 'Contact Logs' )

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
                        <div class="btn-group">
                        </div>
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
                                <td width="8%"> {{date('Y-m-d',strtotime($c->created))}} <br> {{date('g:ia',strtotime($c->created))}} </td>
                                <td width="17%"> <b class="uppercase"><a href="mailto:{{$c->email}}" target="_top">{{$c->name}}</a></b>
                                    <p><small><i>
                                        @if($c->email)Email: <a href="mailto:{{$c->email}}" target="_top">{{$c->email}}</a> <br>@endif
                                        @if($c->phone)Phone: <a> {{$c->phone}} </a><br>@endif 
                                        Recived: <a> {{date('F jS, Y \a\t h:i A',strtotime($c->created))}} </a>
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
@endsection

@section('scripts') 
<script src="/js/admin/contacts/index.js" type="text/javascript"></script>
@endsection