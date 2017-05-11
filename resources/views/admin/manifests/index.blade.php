@php $page_title='Manifests' @endphp
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
                        @if(in_array('Other',Auth::user()->user_type->getACLs()['MANIFESTS']['permission_types']))
                        <div id="start_end_date" class="pull-left tooltips btn btn-sm" data-container="body" data-placement="bottom">
                            <i class="icon-calendar"></i>&nbsp;
                            <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="btn-group">
                            <button id="btn_model_csv" class="btn sbold bg-green" disabled="true"> CSV
                                <i class="fa fa-file-excel-o"></i>
                            </button>
                            <button id="btn_model_pdf" class="btn sbold bg-yellow" disabled="true"> PDF
                                <i class="fa fa-file-pdf-o"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="1%"> </th>
                                <th width="20%"> Show Time </th>
                                <th width="6%"> Type </th>
                                <th width="62%"> Info </th>
                                <th width="9%"> Sent At </th>
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
                                <td width="1%" style="background-color:#{{substr(dechex(crc32($m->show_time_id.date('mdYgi',strtotime($m->show_time)))),0,6)}}"></td>
                                <td width="20%">
                                    @if($previous_show_time_id != $m->show_time_id)
                                    <center>
                                        <b><a>{{$m->name}}</a></b><br><small>{{date('m/d/Y g:ia',strtotime($m->show_time))}}</small>
                                    </center>
                                    @endif
                                </td>
                                <td width="6%">
                                    <center>
                                        <span class="label label-sm sbold
                                        @if($m->manifest_type=='Preliminary') label-success
                                        @elseif($m->manifest_type=='Primary') label-info
                                        @else label-warning
                                        @endif
                                    "> {{$m->manifest_type}} </span>
                                    </center>
                                </td>
                                <td width="62%">
                                    <div class="search-content" style="text-align:left">
                                        <small><i>
                                        Purchases: <b>{{$m->num_purchases}}</b>, Tickets Sold: <b>{{$m->num_people}}</b><br>
                                        @php $emails = explode(',',$m->recipients) @endphp
                                        Receipts: (@foreach($emails as $e) <a href="mailto:{{$e}}" target="_top">{{$e}}</a> . @endforeach)
                                        </i></small>
                                    </div>
                                </td>
                                <td width="9%" data-order="{{strtotime($m->created)}}"><center> {{date('m/d/Y',strtotime($m->created))}}<br>{{date('g:ia',strtotime($m->created))}} </center></td>
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
        <form method="post" action="/admin/manifests" id="form_model_search">
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
