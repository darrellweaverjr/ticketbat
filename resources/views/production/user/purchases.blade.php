@php $page_title='Purchases' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<div class="page-content color-panel" style="min-height:600px">     
@if(empty($purchases) || !count($purchases))
<div>       
    <center><br><h1>There are no purchases to list</h1></center>
</div>
@else
    <!-- BEGIN TABLE-->
    <div class="row fixed-panel">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> Past {{$page_title}} </span>
                    </div>
                </div>
                <div class="portlet-body portlet-body flip-scroll">
                    <table class="table table-striped table-bordered table-hover table-header-fixed table-scrollable table-condensed flip-content" id="tb_purchases">
                        <thead class="flip-content">
                            <tr>
                                <th>#</th>
                                <th width="15%">Show</th>
                                <th width="15%">Type</th>
                                <th width="15%">Venue</th>
                                <th>Show time</th>
                                <th>Purchase time</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>View receipt</th>
                                <th>Print tickets</th>
                                @if($seller>0)
                                <th>BOCA Printer</th>
                                <th>Printer (wrist)</th>
                                @endif
                                <th>Share tickets</th>
                            </tr>
                        </thead>
                        <tbody style="text-align:center">
                            @foreach($purchases as $index=>$p)
                            @if($p->status!='Active')
                            <tr class="danger">
                                <td>{{count($purchases)-$index}}</td>
                                <td>{{$p->show_name}}</td>
                                <td>{{$p->ticket_type}}<br>{{$p->title}}</td>
                                <td>{{$p->venue_name}}</td>
                                <td>{{date('m/d/Y',strtotime($p->show_time))}} - {{date('g:ia',strtotime($p->show_time))}}</td>
                                <td>{{date('m/d/Y',strtotime($p->created))}} - {{date('g:ia',strtotime($p->created))}}</td>
                                <td>{{$p->quantity}}</td>
                                <td style="text-align:right">${{number_format($p->price_paid,2)}}</td>
                                @if($seller>0)
                                <td colspan="5">{{$p->status}}</td>
                                @else
                                <td colspan="3">{{$p->status}}</td>
                                @endif
                            </tr>
                            @else
                            <tr>
                                <td>{{count($purchases)-$index}}</td>
                                <td>{{$p->show_name}}</td>
                                <td>{{$p->ticket_type}}<br>{{$p->title}}</td>
                                <td>{{$p->venue_name}}</td>
                                <td>{{date('m/d/Y',strtotime($p->show_time))}} - {{date('g:ia',strtotime($p->show_time))}}</td>
                                <td>{{date('m/d/Y',strtotime($p->created))}} - {{date('g:ia',strtotime($p->created))}}</td>
                                <td>{{$p->quantity}}</td>
                                <td style="text-align:right">${{number_format($p->price_paid,2)}}</td>
                                <td><a href="/user/purchases/receipts/{{$p->id}}" target="_blank" class="btn btn-lg bg-green btn-outline"><i class="icon-doc"></i></a></td>
                                @if(!$p->passed)
                                    @if($seller>0)
                                    <td>-</td>
                                    <td>-</td>
                                    @endif
                                <td>-</td>
                                <td>-</td>
                                @else
                                <td><a href="/user/purchases/tickets/C/{{$p->id}}" target="_blank" class="btn btn-lg btn-success btn-outline"><i class="icon-printer"></i></a></td>
                                @if($seller>0)
                                <td><a href="/user/purchases/tickets/S/{{$p->id}}" target="_blank" class="btn btn-lg btn-danger btn-outline"><i class="icon-printer"></i></a></td>
                                <td><a href="/user/purchases/tickets/W/{{$p->id}}" target="_blank" class="btn btn-lg btn-info btn-outline"><i class="icon-printer"></i></a></td>
                                @endif
                                <td><button type="button" class="btn btn-lg bg-green btn-outline btn_share_tickets" data-id="{{$p->id}}" data-qty="{{$p->quantity}}"><i class="icon-share"></i></button></td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
@endif
</div>
<!-- END TABLE -->
<!-- BEGIN SHARE TICKETS MODAL -->
@includeIf('production.general.share_tickets')
<!-- END SHARE TICKETS MODAL -->
@endsection

@section('scripts')
<script src="/js/production/general/share_tickets.js" type="text/javascript"></script>
<script src="/js/production/user/purchases.js" type="text/javascript"></script>
@endsection