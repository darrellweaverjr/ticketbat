@php $page_title='Manifests' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{config('app.theme')}}css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List and view manifests. <i>(Manifests do not sent if there are no purchases of the show)</i></small>
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
                            <button id="btn_model_csv" class="btn sbold bg-green" disabled="true">CSV
                                <i class="fa fa-file-excel-o"></i>
                            </button>
                            <button id="btn_model_pdf" class="btn sbold bg-yellow" disabled="true">PDF
                                <i class="fa fa-file-pdf-o"></i>
                            </button>
                            <button data-toggle="modal" href="#modal_generate_manifest" class="btn sbold bg-purple">Generate
                                <i class="fa fa-settings"></i>
                            </button>
                            <button id="btn_model_resend" class="btn sbold bg-purple" disabled="true">Re-send
                                <i class="fa fa-send"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="1%"></th>
                                <th width="25%">Show Time</th>
                                <th width="10%">Type</th>
                                <th width="6%">Purchases</th>
                                <th width="6%">Tickets Sold</th>
                                <th width="40%">Receipts</th>
                                <th width="10%">Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_show_time_id = '0' @endphp
                            @foreach($manifests as $index=>$m)
                                @php $color = substr(dechex(crc32($m->show_time_id.date('mdYgi',strtotime($m->show_time)))),0,6) @endphp
                            <tr>
                                <td>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" id="{{$m->id}}" value="{{$m->id}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td style="background-color:#{{$color}};border-top:thick solid @if($previous_show_time_id==$m->show_time_id) #{{$color}} @else #ffffff @endif !important;"></td>
                                <td>
                                    @if($previous_show_time_id != $m->show_time_id)
                                    <center>
                                        <b>{{$m->name}}</b><br>{{date('m/d/Y g:ia',strtotime($m->show_time))}}
                                    </center>
                                    @endif
                                </td>
                                <td>
                                    <center>
                                        <span class="label label-sm sbold
                                        @if($m->manifest_type=='Preliminary') label-success
                                        @elseif($m->manifest_type=='Primary') label-info
                                        @else label-warning
                                        @endif
                                    "> {{$m->manifest_type}} </span>
                                    </center>
                                </td>
                                <td><center>{{$m->num_purchases}}</center></td>
                                <td><center>{{$m->num_people}}</center></td>
                                <td style="text-align:left">
                                    @php $emails = explode(',',$m->recipients) @endphp
                                    ({{count($emails)}}): @foreach($emails as $e) <a href="mailto:{{$e}}" target="_top">{{$e}}</a> @endforeach
                                </td>
                                <td class="text-center" data-order="{{strtotime($m->created)}}"> {{date('m/d/Y',strtotime($m->created))}}<br>{{date('g:ia',strtotime($m->created))}}
                                    @if(isset($m->sent)) @if(!empty($m->sent)) <span class="label label-success"><i class="fa fa-send"></i></span> @else <span class="label label-danger"><i class="fa fa-send"></i></span> @endif @endif
                                </td>
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
    <!-- BEGIN RESEND MODAL-->
    <div id="modal_model_resend" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Re-send Manifest</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_resend" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row" style="padding-left:15px">
                                <div class="mt-radio-list">
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="action" value="0" checked="true" />Same email(s)
                                        <span></span>
                                    </label><hr>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" class="form-control" name="action" value="1" />Email(s) related now on the shows' report
                                        <span></span>
                                    </label><hr>
                                    <label class="mt-radio mt-radio-single mt-radio-outline col-md-2">
                                        <input type="radio" name="action" value="2" />To:
                                        <span></span>
                                    </label>
                                    <div class="col-md-10">
                                    <input type="text" class="form-control" name="email" value=""/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-purple">Send</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END RESEND MODAL-->
    <!-- BEGIN GENERATE MODAL-->
    <div id="modal_generate_manifest" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-purple">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Generate Manifest</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_generate_manifest" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row" style="padding-left:15px">
                                <div class="form-group">
                                    <label class="control-label col-md-5">Manifest date</label>
                                    <div class="col-md-6 show-error">
                                        <div id="date_generate" class="input-group date dtpicker">
                                            <input size="16" readonly="" class="form-control" type="text" name="date" value="{{date('m/d/Y')}}">
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_generate" class="btn sbold bg-purple">Generate</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END GENERATE MODAL-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/js/admin/manifests/index.js" type="text/javascript"></script>
@endsection
