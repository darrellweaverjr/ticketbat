@php $page_title='Manifests' @endphp
@extends('layouts.admin')
@section('title', 'Manifests' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List and view manifests.</small>
    </h1>
    <!-- END PAGE TITLE-->    
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} EMAILS </span>
                    </div>
                    <div class="actions">
                        <div id="start_end_date" class="pull-left tooltips btn btn-sm" data-container="body" data-placement="bottom">
                            <i class="icon-calendar"></i>&nbsp;
                            <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                            <i class="fa fa-angle-down"></i>
                        </div>                        
                        <div class="btn-group">
                            <button id="btn_model_csv" class="btn sbold bg-green" disabled="true"> CSV 
                                <i class="fa fa-table"></i>
                            </button>
                            <button id="btn_model_pdf" class="btn sbold bg-yellow" disabled="true"> PDF 
                                <i class="fa fa-archive"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="20%"> Show Time </th>
                                <th width="8%"> Type </th>
                                <th width="60%"> Info </th>
                                <th width="10%"> Sent At </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_show_time_id = '0' @endphp
                            @foreach($manifests as $index=>$m)
                            <tr>
                                <td width="2%">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$m->id}}" value="{{$m->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="20%">
                                    @if($previous_show_time_id != $m->show_time_id)
                                    <center> 
                                        <h4><a>{{$m->name}}</a></h4><small>{{date('m/d/Y g:ia',strtotime($m->show_time))}}</small> 
                                    </center>
                                    @endif
                                </td>
                                <td width="8%">
                                    <span class="label label-sm sbold 
                                        @if($m->manifest_type=='Preliminary') label-success 
                                        @elseif($m->manifest_type=='Primary') label-info 
                                        @else label-warning 
                                        @endif
                                    "> {{$m->manifest_type}} </span>
                                </td>
                                <td width="60%">
                                    <div class="search-content" style="text-align:left">
                                        <small><i>
                                        Purchases: <b>{{$m->num_purchases}}</b>, Tickets Sold: <b>{{$m->num_people}}</b>, Sent at: <b>{{date('l, m/d/Y g:ia',strtotime($m->created))}}</b><br>
                                        @php $emails = explode(',',$m->recipients) @endphp
                                        Receipts: (@foreach($emails as $e) <a href="mailto:{{$e}}" target="_top">{{$e}}</a> . @endforeach) 
                                        </i></small>
                                    </div>
                                </td>
                                <td width="10%"><center> {{date('m/d/Y',strtotime($m->created))}}<br>{{date('g:ia',strtotime($m->created))}} </center></td>
                            </tr>
                            @php $previous_show_time_id = $m->show_time_id @endphp
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
        <form method="post" action="/admin/manifests" id="form_model_update">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <input name="start_date" type="hidden" value="{{$start_date}}"/>
            <input name="end_date" type="hidden" value="{{$end_date}}"/>
        </form>   
    </div>  
    <!-- END UPDATE MODAL--> 
@endsection

@section('scripts') 
<script src="/js/admin/manifests/index.js" type="text/javascript"></script>
@endsection