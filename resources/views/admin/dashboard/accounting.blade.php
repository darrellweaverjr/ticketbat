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
                        <br>Transactions: <span data-counter="counterup" data-value="{{number_format(count($data))}}">0</span>
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
                    <div class="desc">
                        Total Paid
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
                    <div class="desc">Venue Revenue</div>
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
                                <th class="all" style="text-align:center">Customer</th>
                                <th class="all" style="text-align:center">Venue<br>Show</th>
                                <th class="all" style="text-align:center">Show<br>Date</th>
                                <th class="all" style="text-align:center">Trans.<br>Date</th>
                                <th class="all" style="text-align:center">Qty<br>Tcks</th>
                                <th class="all" style="text-align:center">Base</th>
                                <th class="all" style="text-align:center">Comm.</th>
                                <th class="all" style="text-align:center">Fee<br>Incl.</th>
                                <th class="all" style="text-align:center">Fee<br>Over</th>
                                <th class="all" style="text-align:center">Disc.</th>
                                <th class="all" style="text-align:center">Total<br>Amount</th>
                                <th class="all" style="text-align:center">Venue<br>Rev.</th>
                                <th class="all" style="text-align:center">TB<br>Rev.</th>
                                <th class="none" style="text-align:left">Coupon</th>
                                <th class="none" style="text-align:left">Method</th>
                                <th class="none" style="text-align:left">Cardholder</th>
                                <th class="none" style="text-align:left">Authcode</th>
                                <th class="none" style="text-align:left">Refnum</th>
                                <th class="none" style="text-align:left">CC Last 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr @if(strpos($d->status,'Pending') === 0) class="warning" @endif >
                                <td>{{$d->id}}</td>
                                <td style="text-align:center">{{$d->name}}</td>
                                <td style="text-align:center">{{$d->venue_name}}<br><b><small>"{{$d->show_name}}"</small></b></td>
                                <td style="text-align:center" data-order="{{strtotime($d->show_time)}}">{{date('n/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->created)}}">{{date('n/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->commissions,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_incl,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_over,2)}}</td>
                                <td style="text-align:right">($ {{number_format($d->discounts,2)}})</td>
                                <td style="text-align:right">$ {{number_format($d->price_paid,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->profit,2)}}</b></td>
                                <td style="text-align:center">{{$d->code}}</td>
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
                                <td style="text-align:center">{{$d->venue_name}}<br><b><small>"{{$d->show_name}}"</small></b></td>
                                <td style="text-align:center" data-order="{{strtotime($d->show_time)}}">{{date('n/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->refunded)}}">{{date('n/d/Y g:ia',strtotime($d->refunded))}}</td>
                                <td style="text-align:center">({{number_format($d->tickets)}})</td>
                                <td style="text-align:right">($ {{number_format($d->revenue,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->commissions,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->fees_incl,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->fees_over,2)}})</td>
                                <td style="text-align:right">$ {{number_format($d->discounts,2)}}</td>
                                <td style="text-align:right">($ {{number_format($d->refunds,2)}})</td>
                                <td style="text-align:right">($ {{number_format($d->to_show,2)}})</td>
                                <td style="text-align:right"><b>($ {{number_format($d->profit,2)}})</b></td>
                                <td style="text-align:center">{{$d->code}}</td>
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
