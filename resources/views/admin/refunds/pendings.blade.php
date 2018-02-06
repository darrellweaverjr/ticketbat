@php $page_title='Purchases pendings to refund' @endphp
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
        <small> - List all purchases pending to refunds in the system.</small>
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
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['REFUNDS']['permission_types']))
                            <button id="btn_model_search" class="btn sbold bg-purple">Refund
                                <i class="fa fa-credit-card"></i>
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
                                <th width="1%"></th>
                                <th width="47%">Purchase Info</th>
                                <th width="18%">Show/Venue</th>
                                <th width="8%">Show Time</th>
                                <th width="8%">Purchase Time</th>
                                <th width="5%">Amount</th>
                                <th width="11%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $previous_color = '0' @endphp
                            @foreach($purchases as $index=>$p)
                                @php $color = substr(dechex(crc32($p->color)),0,6) @endphp
                            <tr>
                                <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$p->id}}" data-qty="{{$p->quantity}}" value="{{$p->email}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td title="Click here to see details" class="modal_details_view" 
                                    data-id="{{$p->id}}" style="text-align:center;background-color:#{{$color}};border-top:thick solid @if($previous_color==$color) #{{$color}} @else #ffffff @endif !important;">
                                    <i class="fa fa-search"></i>
                                </td>
                                <td class="search-item clearfix">
                                    <div class="search-content" >
                                        @if($previous_color != $color)
                                        <b class="search-title">
                                            <i class="fa fa-ticket"></i> {{$p->first_name}} {{$p->last_name}}, <small><i> <a href="mailto:{{$p->email}}" target="_top">{{$p->email}}</a></i></small> 
                                            @if($p->first_name != $p->u_first_name || $p->last_name != $p->u_last_name || $p->email != $p->email) <br><i class="fa fa-user"></i> {{$p->u_first_name}} {{$p->u_last_name}} <small><i> (<a href="mailto:{{$p->u_email}}" target="_top">{{$p->u_email}}</a>)</i></small> @endif
                                            @if($p->card_holder && $p->card_holder != $p->first_name.' '.$p->last_name) <br><i class="fa fa-credit-card"></i> {{$p->card_holder}}@endif
                                        </b>
                                        <br><small><i>Method: <b>{{$p->method}}</b>, AuthCode: <b>{{$p->authcode}}</b>, RefNum: <b>{{$p->refnum}}</b>, </i></small><br>
                                        @endif
                                        <small><i>ID: <b>{{$p->id}}</b>, Qty: <b>{{$p->quantity}}</b>, T.Type: <b>{{$p->ticket_type_type}}</b>, Pkg: <b>{{$p->title}}</b>,
                                        <br> Ret.Price: <b>${{number_format($p->retail_price,2)}}</b>, Fees: <b>${{number_format($p->processing_fee,2)}}</b>, Commiss.: <b>${{number_format($p->commission_percent,2)}}</b>, Savings: <b>${{number_format($p->savings,2)}}</b>
                                        </i></small>
                                        <div id="note_{{$p->id}}" class="note note-info @if(empty(trim($p->note))) hidden @endif" style="font-style:italic;font-size:smaller">@php echo trim($p->note) @endphp</div>
                                    </div>
                                </td>
                                <td><center>{{$p->show_name}}<br>at<br>{{$p->venue_name}}</center></td>
                                <td data-order="{{strtotime($p->show_time)}}"><center>{{date('m/d/Y',strtotime($p->show_time))}}<br>{{date('g:ia',strtotime($p->show_time))}}</center></td>
                                <td data-order="{{strtotime($p->created)}}"><center>{{date('m/d/Y',strtotime($p->created))}}<br>{{date('g:ia',strtotime($p->created))}}</center></td>
                                <td style="text-align:right">${{number_format($p->price_paid,2)}} / ${{number_format($p->amount,2)}}</td>
                                <td><center>{{$p->status}}</center></td>
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
<script src="/js/admin/refunds/pendings.js" type="text/javascript"></script>
@endsection
