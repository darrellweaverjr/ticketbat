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

<div class="page-content" style="margin:40px!important;">     
@if(empty($purchases) || !count($purchases))
<div>       
    <h1>There are no purchases to list</h1>
</div>
@else
    <!-- BEGIN TABLE-->
    <div class="row">
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
                                <td colspan="3">{{$p->status}}</td>
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
                                <td><a href="/production/user/purchases/receipts/{{$p->id}}" target="_blank" class="btn btn-lg bg-green btn-outline"><i class="icon-doc"></i></a></td>
                                @if(!$p->passed)
                                <td>-</td>
                                <td>-</td>
                                @else
                                <td><a href="/production/user/purchases/tickets/{{$p->id}}" target="_blank" class="btn btn-lg bg-green btn-outline"><i class="icon-printer"></i></a></td>
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
<div id="modal_share_tickets" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:60% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Share tickets</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    To send tickets to someone, choose the number of tickets you would like to assign them and then click "New Person".<br>
                    In the new fields that appear, please enter the first name, last name and email address of the person to whom you would like to send the tickets. 
                </div>
                <div class="form-group" title="Select the quantity of tickets and then add a person.">
                    <label class="col-md-4 control-label text-right">Assign</label>
                    <div class="col-md-3">
                        <select class="form-control" id="share_tickets_availables">
                            <option>1 ticket(s) available(s)</option>
                        </select>
                    </div>
                    <button type="button" id="new_person_share" class="btn btn-primary">to a new person</button>
                </div>
                <!-- BEGIN FORM-->
                <form method="post" id="form_share_tickets" class="form-horizontal">
                    <input type="hidden" name="purchases_id" value="">
                    <div class="form-body">
                        <table class="table table-striped table-bordered table-hover table-header-fixed">
                            <thead>
                                <tr class="uppercase">
                                    <th width="20%">First Name</th>
                                    <th width="20%">Last Name</th>
                                    <th width="20%">Email</th>
                                    <th width="8%">Qty</th>
                                    <th width="27%">Comment</th>
                                    <th width="3%">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="tb_shared_tickets_body">
                            </tbody>
                        </table>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                <button type="button" id="btn_share_tickets" class="btn bg-green btn-outline" title="Share your tickets now.">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- END SHARE TICKETS MODAL -->
@endsection

@section('scripts')
<script src="/js/production/user/purchases.js" type="text/javascript"></script>
@endsection