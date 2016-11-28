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
                        <div class="btn-group">
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="88%"> Manifests </th>
                                <th width="10%"> Date </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manifests as $index=>$me)
                            <tr>
                                <td class="search-item clearfix" width="88%"> 
                                    <div class="search-content col-md-2">                                        
                                        <h4 class="search-title sbold"><a>{{$show_times[$me[0]->show_time_id]->name}}</a></h4>
                                    </div>
                                    @foreach($me as $index2=>$m)
                                    <div class="search-content col-md-5" style="padding-left:35px;text-align:left">
                                        <div><h5 class="search-title">
                                            <a class="col-md-7 @if($index2==0) bg-green @else green @endif  btn green-sharp sbold uppercase">{{$m->manifest_type}}</a>
                                            <a href="/admin/manifests/view/csv/{{$m->id}}" target="_blank" class="col-md-2 btn blue-sharp btn-outline sbold uppercase">CSV</a>
                                            <a href="/admin/manifests/view/pdf/{{$m->id}}" target="_blank" class="col-md-2 btn green-sharp btn-outline sbold uppercase">PDF</a>
                                        </h5></div><hr>
                                        <div><small><i>
                                        Purchases: <b>{{$m->num_purchases}}</b>, Tickets Sold: <b>{{$m->num_people}}</b>, Sent at: <b>{{date('l, m/d/Y g:ia',strtotime($m->created))}}</b><br>
                                        @php $emails = explode(',',$m->recipients) @endphp
                                        Receipts: (@foreach($emails as $e) <a href="mailto:{{$e}}" target="_top">{{$e}}</a> . @endforeach) 
                                        </i></small></div>
                                    </div>
                                    @endforeach 
                                </td>
                                <td width="10%"><center> {{date('Y-m-d g:ia',strtotime($show_times[$me[0]->show_time_id]->show_time))}} </center></td>
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
<script src="/js/admin/manifests/index.js" type="text/javascript"></script>
@endsection