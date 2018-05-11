@php $page_title='Ticket Sales' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
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
                        <br>Purchases: <span data-counter="counterup" data-value="{{number_format(count($data))}}">0</span>
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
                        $ <span data-counter="counterup" data-value="{{number_format($total['revenue'],2)}}"></span></div>
                    <div class="desc">Total Revenue
                        @if(Auth::user()->user_type_id != 5)<br>Discounts: $ <span data-counter="counterup" data-value="{{number_format($total['discounts'],2)}}"></span>@endif
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
                    <div class="desc">To Show</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue">
                <div class="visual">
                    <i class="fa fa-usd"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}"></span></div>
                    <div class="desc">
                        @if(Auth::user()->user_type_id != 5) Commission<br>Revenue @else TB Commission<br>Expense @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue-steel">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number"></div>
                    <div class="desc">
                        Fees Incl.: $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl'],2)}}"></span>
                        <br>Fees Over.: $ <span data-counter="counterup" data-value="{{number_format($total['fees_over'],2)}}"></span>
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
                        @if(Auth::user()->user_type_id != 5) Gross Profit @else TB Retains @endif
                    </div>
                </div>
            </a>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    <div class="row">
       <div class="col-md-12">
           <div class="portlet light portlet-fit bordered">
               <div class="portlet-body">
                   <div id="ticket_sales_chart_sales" data-info="{{$graph}}" style="height:250px;"></div>
               </div>
           </div>
       </div>
   </div>
    <!-- BEGIN TOTAL TABLE FOR PRINT-->
    <div id="tb_summary" class="portlet-body" style="display:none;" >
        @foreach($summary as $summ)
        <h5>@php echo $summ['title'] @endphp</h5>
        <table width="100%" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>CHANNEL - TYPE</th>
                    <th style='text-align:center'>TRANSACTIONS</th>
                    <th style='text-align:center'>TICKETS</th>
                    <th style='text-align:right'>TOTAL REVENUE</th>
                    <th style='text-align:right'>DISCOUNTS</th>
                    <th style='text-align:right'>TO SHOW</th>
                    <th style='text-align:right'>COMMISSIONS</th>
                    <th style='text-align:right'>FEES INCL</th>
                    <th style='text-align:right'>FEES OVER</th>
                    <th style='text-align:right'>GROSS PROFIT</th>
                </tr>
            </thead>
            <tbody>
               @foreach($summ['table'] as $k=>$d)
                <tr @if($k=='Subtotals' || $k=='Totals') style="font-weight:bold" @endif>
                    <td>{{$k}}</td>
                    <td style="text-align:center">{{number_format($d['purchases'])}}</td>
                    <td style="text-align:center">{{number_format($d['tickets'])}}</td>
                    <td style="text-align:right">$ {{number_format($d['revenue'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['discounts'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['to_show'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['commissions'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['fees_incl'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['fees_over'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['profit'],2)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </div>
    <!-- BEGIN COUPONS TABLE FOR PRINT-->
    <div id="tb_coupon" class="portlet-body" style="display:none;" >
        @if(!empty($coupons))
        <h5>Coupons Report</h5>
        <table width="100% class="table table-striped table-bordered table-hover">
            <tbody>
                @foreach($coupons['descriptions'] as $k=>$d)
                <tr>
                    <td><b>{{$k}}*</b></td>
                    <td> => {{$d}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table width="100% class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Venue</th>
                    <th>Show</th>
                    <th style="text-align:center">Code</th>
                    <th style="text-align:center">Distrib.<br>At</th>
                    <th style="text-align:center">Sales<br>-7D</th>
                    <th style="text-align:center">Sales<br>-1D</th>
                    <th style="text-align:center">Qty Sold<br>(Purch.)</th>
                    <th style="text-align:center">Discount</th>
                    <th style="text-align:center">Total<br>Revenue</th>
                    <th style="text-align:center">To<br>Show</th>
                    <th style="text-align:center">Comm.</th>
                    <th style="text-align:center">Fees Incl</th>
                    <th style="text-align:center">Fees Over</th>
                    <th style="text-align:center">Gross<br>Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons['data'] as $d)
                <tr>
                    <td>{{$d->venue_name}}</td>
                    <td>{{$d->show_name}}</td>
                    <td style="text-align:center">{{$d->code}}</td>
                    <td style="text-align:center">{{$d->distributed_at}}</td>
                    <td style="text-align:center">{{$d->tickets_seven}}</td>
                    <td style="text-align:center">{{$d->tickets_one}}</td>
                    <td style="text-align:center">{{number_format($d->tickets)}} ({{number_format($d->purchases)}})</td>
                    <td style="text-align:right">$ {{number_format($d->discounts,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->commissions,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->fees_incl,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->fees_over,2)}}</td>
                    <td style="text-align:right"><b>$ {{number_format($d->commissions+$d->fees_incl+$d->fees_over,2)}}</b></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;border-top:1px solid #000;">
                    <td colspan="6">TOTALS</td>
                    <td style="text-align:center">{{number_format($coupons['total']['tickets'])}} ({{number_format($coupons['total']['purchases'])}})</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['discounts'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['revenue'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['to_show'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['commissions'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['fees_incl'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['fees_over'],2)}}</td>
                    <td style="text-align:right"><b>$ {{number_format($coupons['total']['commissions']+$coupons['total']['fees_incl']+$coupons['total']['fees_over'],2)}}</b></td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>
    <!-- END TOTAL TABLE FOR PRINT-->
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
                                <th class="all" style="text-align:center">Sold<br>Date</th>
                                <th class="all" style="text-align:center">Qty<br>Sold</th>
                                <th class="all" style="text-align:center">Total<br>Revenue</th>
                                @if(Auth::user()->user_type_id != 5)
                                <th class="all" style="text-align:center">Disc.</th>
                                @endif
                                <th class="all" style="text-align:center">To<br>Show</th>
                                <th class="all" style="text-align:center">@if(Auth::user()->user_type_id != 5) Comm. @else TB Comm.<br>Exp. @endif</th>
                                <th class="all" style="text-align:center">Fee<br>Incl</th>
                                <th class="all" style="text-align:center">Fee<br>Over</th>
                                <th class="all" style="text-align:center">@if(Auth::user()->user_type_id != 5) Gross<br>Profit @else TB Retain @endif</th>
                                <th class="none">Coupon</th>
                                <th class="none">Ticket Type</th>
                                <th class="none">Method</th>
                                <th class="none">Channel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr @if($d->status != "Active") class="warning" @endif >
                                <td>{{$d->id}}</td>
                                <td>{{$d->status}}</td>
                                <td style="text-align:center">{{$d->name}}</td>
                                <td style="text-align:center"><a href="mailto:{{$d->email}}" target="_top">{{$d->email}}</a></td>
                                <td style="text-align:center">{{$d->venue_name}}</td>
                                <td style="text-align:center">{{$d->show_name}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->show_time)}}">{{date('m/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center" data-order="{{strtotime($d->created)}}">{{date('m/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                                @if(Auth::user()->user_type_id != 5)
                                <td style="text-align:right">$ {{number_format($d->discounts,2)}}</td>
                                @endif
                                <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->commissions,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_incl,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees_over,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->profit,2)}}</b></td>
                                <td style="text-align:center">{{$d->code}}</td>
                                <td style="text-align:center">{{$d->ticket_type}} - {{$d->title}}</td>    
                                <td style="text-align:center">{{$d->method}}</td>
                                <td style="text-align:center">{{$d->channel}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN INCLUDE FILTER SEARCH-->
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/ticket_sales','printing'=>1])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/highcharts.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/bootstrap-touchspin.min.js" type="text/javascript"></script>
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/ticket_sales.js" type="text/javascript"></script>
@endsection
