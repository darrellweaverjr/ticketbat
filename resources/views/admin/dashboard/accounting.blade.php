@php $page_title='Accounting' @endphp
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
        <small>statistics and reports (by default the last 7 days).</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row" id="totals">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 dark">
                <div class="visual">
                    <i class="fa fa-ticket"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets'])}}">0</span>
                    </div>
                    <div class="desc">Tickets Sold
                        <br>Transactions: <span data-counter="counterup" data-value="{{number_format($total['purchases'])}}">0</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" >
            <a class="dashboard-stat dashboard-stat-v2 green-seagreen">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['price_paid'],2)}}"></span></div>
                    <div class="desc">Total Charged
                        <br>Sales Tax: $ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes'],2)}}"></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 red">
                <div class="visual">
                    <i class="fa fa-money"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['to_show'],2)}}"></span></div>
                    <div class="desc">Venue Revenue
                        <br>CC Fees: $ <span data-counter="counterup" data-value="{{number_format($total['cc_fees'],2)}}"></span> 
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue">
                <div class="visual">
                    <i class="fa fa-usd"></i>
                </div>
                <div class="details">
                    <div class="number"></div>                        
                    <div class="desc">
                        Fees Incl.: $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl'],2)}}"></span>
                        <br>Fees Over.: $ <span data-counter="counterup" data-value="{{number_format($total['fees_over'],2)}}"></span>
                        <br>Commis.: $ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}"></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 purple">
                <div class="visual">
                    <i class="fa fa-bank"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['profit'],2)}}"></span>
                    </div>
                    <div class="desc">
                        TB Revenue
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue-steel">
                <div class="visual">
                    <i class="fa fa-backward"></i>
                </div>
                <div class="details">
                    <div class="number">
                        ( $ <span data-counter="counterup" data-value="{{number_format($total['refunds'],2)}}"></span> )</div>
                    <div class="desc">Refunds</div>
                </div>
            </a>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i></i>{{strtoupper($page_title)}}</div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="tb_model">
                        <thead>
                            <tr>
                                <th class="all" style="text-align:center">Orders</th>
                                <th class="none">Status</th>
                                <th class="all" style="text-align:center">Customer</th>
                                <th class="none">Email</th>
                                <th class="all" style="text-align:center">Venue</th>
                                <th class="all" style="text-align:center">Show</th>
                                <th class="all" style="text-align:center">Show<br>Date</th>
                                <th class="all" style="text-align:center">Trans.<br>Date</th>
                                <th class="all" style="text-align:center">Qty<br>Tcks</th>                                
                                <th class="all" style="text-align:center">Subt</th>
                                <th class="all" style="text-align:center">Fees</th>
                                <th class="all" style="text-align:center">Disc</th>
                                <th class="all" style="text-align:center">Other</th>
                                <th class="all" style="text-align:center">Taxes</th>                                
                                <th class="all" style="text-align:center">Total<br>Charged</th>                                
                                <th class="all" style="text-align:center">CC<br>Fee</th>
                                <th class="all" style="text-align:center">Venue<br>Rev.</th>                                
                                <th class="all" style="text-align:center">Com.(C)</th>
                                <th class="all" style="text-align:center">Fee<br>Incl.(F)</th>
                                <th class="all" style="text-align:center">Fee<br>Over(F)</th>
                                <th class="all" style="text-align:center">TB<br>Rev.<br>(C+F)</th>
                                <th class="none">Coupon</th>
                                <th class="none">Ticket</th>
                                <th class="none">Channel</th>
                                <th class="none">Method</th>
                                <th class="none">Cardholder</th>
                                <th class="none">Authcode</th>
                                <th class="none">Refnum</th>
                                <th class="none">CC Last 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr @if(strpos($d->status,'Pending') === 0) class="warning" @endif >
                                <td>{{$d->id}}</td>
                                <td style="text-align:center">{{$d->status}}</td>
                                <td style="text-align:center">{{$d->name}}</td>
                                <td style="text-align:center">{{$d->email}}</td>
                                <td style="text-align:center">{{$d->venue_name}}</td>
                                <td style="text-align:center">{{$d->show_name}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->show_time)}}">{{date('n/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->created)}}">{{date('n/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:right">$ {{number_format($d->retail_price,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees,2)}}</td>
                                <td style="text-align:right">($ {{number_format($d->savings,2)}})</td>
                                <td style="text-align:right">$ {{number_format($d->other,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->sales_taxes,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->price_paid,2)}}</b></td>
                                <td style="text-align:right">$ {{number_format($d->cc_fees,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->commissions,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_incl,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_over,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->profit,2)}}</b></td>
                                <td style="text-align:center">{{$d->code}}</td>
                                <td style="text-align:center">{{$d->ticket_type}} - {{$d->title}}</td>
                                <td style="text-align:center">{{$d->channel}}</td>
                                <td style="text-align:center">{{$d->method}}</td>
                                <td style="text-align:center">{{$d->card_holder}}</td>
                                <td style="text-align:center">{{$d->authcode}}</td>
                                <td style="text-align:center">{{$d->refnum}}</td>
                                <td style="text-align:center">...{{$d->last_4}}</td>
                            </tr>
                            @if($d->status=='Refunded')
                            <tr class="danger">
                                <td>{{$d->id}}</td>
                                <td style="text-align:center">{{$d->name}}</td>
                                <td style="text-align:center">{{$d->venue_name}}</td>
                                <td style="text-align:center">{{$d->show_name}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->show_time)}}">{{date('n/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->refunded)}}">{{date('n/d/Y g:ia',strtotime($d->refunded))}}</td>
                                <td style="text-align:center">({{number_format($d->tickets)}})</td>
                                <td style="text-align:right">($ {{number_format($d->retail_price,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->fees,2)}})</td>
                                <td style="text-align:right">$ {{number_format($d->savings,2)}}</td>
                                <td style="text-align:right">($ {{number_format($d->other,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->sales_taxes,2)}})</td>
                                <td style="text-align:right"><b>($ {{number_format($d->price_paid,2)}})</b></td>
                                <td style="text-align:right">($ {{number_format($d->cc_fees,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->to_show,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->commissions,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->fees_incl,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->fees_over,2)}})</td>
                                <td style="text-align:right"><b>($ {{number_format($d->profit,2)}})</b></td>
                                <td style="text-align:center">{{$d->code}}</td>
                                <td style="text-align:center">{{$d->ticket_type}} - {{$d->title}}</td>
                                <td style="text-align:center">{{$d->channel}}</td>
                                <td style="text-align:center">{{$d->method}}</td>
                                <td style="text-align:center">{{$d->card_holder}}</td>
                                <td style="text-align:center">{{$d->authcode}}</td>
                                <td style="text-align:center">{{$d->refnum}}</td>
                                <td style="text-align:center">...{{$d->last_4}}</td>
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
    <!-- BEGIN INCLUDE FILTER SEARCH-->
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/accounting'])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/accounting.js" type="text/javascript"></script>
@endsection
