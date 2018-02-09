@php $page_title='Refunds' @endphp
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
        <small> - List all refunds.</small>
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
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="1%"></th>
                                <th width="40%">Purchase Info</th>
                                <th width="40%">Refund Info</th>
                                <th width="9%">Amount</th>
                                <th width="10%">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_color = '0' @endphp
                            @foreach($refunds as $index=>$p)
                                @php $color = substr(dechex(crc32($p->order_id)),0,6) @endphp
                            <tr>
                                <td style="text-align:center;background-color:#{{$color}};border-top:thick solid @if($previous_color==$color) #{{$color}} @else #ffffff @endif !important;">
                                    <i class="fa fa-shopping-cart"></i>
                                </td>
                                <td class="search-item clearfix">
                                    @if($previous_color != $color)
                                    <div class="search-content" >
                                        <b class="search-title">
                                            <i class="fa fa-ticket"></i> {{$p->first_name}} {{$p->last_name}}, <small><i> <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></i></small>
                                            @if($p->first_name != $p->u_first_name || $p->last_name != $p->u_last_name || $p->email != $p->email) <br><i class="fa fa-user"></i> {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> @endif
                                            @if($p->card_holder && $p->card_holder != $p->first_name.' '.$p->last_name) <br><i class="fa fa-credit-card"></i> {{$p->card_holder}}@endif
                                        </b>
                                        <br><small><i>AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>, ID: <b>{{$p->order_id}}</b>, Qty: <b>{{$p->quantity}}</b>, T.Type: <b>{{$p->ticket_type_type}}</b>, Pkg: <b>{{$p->title}}</b>,
                                        <br> Ret.Price: <b>${{number_format($p->retail_price,2)}}</b>, Fees: <b>${{number_format($p->processing_fee,2)}}</b>, Commiss.: <b>${{number_format($p->commission_percent,2)}}</b>, Savings: <b>${{number_format($p->savings,2)}}</b>,
                                        <br> Method: <b>{{$p->method}}</b> , Status: <b>{{$p->status}}</b>
                                        </i></small>
                                        @if(!empty(trim($p->note)))<div class="note note-info" style="font-style:italic;font-size:smaller">@php echo trim($p->note) @endphp</div>@endif
                                    </div>
                                    @endif
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-content" >
                                        <b class="search-title">
                                            <i class="fa fa-user"></i> Refunded by: {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> 
                                        </b>
                                        <br><small><i>ID: <b>{{$p->id}}</b>, AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>,  IsDuplicate: <b>{{$p->is_duplicate}}</b>, 
                                        <br> Result: <b>({{$p->result_code}}) {{$p->result}}</b>, Error: <b>({{$p->error_code}}) {{$p->error}}</b>
                                        </i></small>
                                        @if(!empty(trim($p->description)))<div class="note note-info" style="font-style:italic;font-size:smaller">@php echo trim($p->description) @endphp</div>@endif
                                    </div>
                                </td>
                                <td>${{number_format($p->amount,2)}}</td>
                                <td><center>{{date('m/d/Y g:ia',strtotime($p->created))}}</center></td>
                            </tr>
                            @php $previous_color = $color @endphp
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
<script src="/js/admin/refunds/index.js" type="text/javascript"></script>
@endsection
