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
        <div class="dashboard-stat2 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="display col-md-3">
                <div class="number text-left">
                    <small>-</small>
                    <h4 class="font-green-sharp bold">PAID</h4>
                    <h4 class="font-red-haze bold">(REFUND)</h4>
                    <h4 class="bold">TOTAL</h4>
                </div>
            </div>
            <div class="display col-md-5">
                <div class="number text-right">
                    <small>TOTAL CHARGED</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['price_paid'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['price_paid_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['price_paid']+$total['price_paid_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-2">
                <div class="number text-center">
                    <small>TRANS.</small>
                    <h4 class="font-green-sharp">
                        <span data-counter="counterup" data-value="{{number_format($total['purchases'])}}">0</span> 
                    </h4>
                    <h4 class="font-red-haze">
                        <span data-counter="counterup" data-value="{{number_format($total['purchases_'])}}">0</span>
                    </h4>
                    <h4 class="bold">
                        <span data-counter="counterup" data-value="{{number_format($total['purchases']+$total['purchases_'])}}">0</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-2">
                <div class="number text-center">
                    <small>TICKS.</small>
                    <h4 class="font-green-sharp">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets'])}}">0</span>
                    </h4>
                    <h4 class="font-red-haze">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets_'])}}">0</span>
                    </h4>
                    <h4 class="bold">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets']+$total['tickets_'])}}">0</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="display col-md-6">
                <div class="number text-right">
                    <small>DISCOUNTS</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['savings'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['savings_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['savings']+$total['savings_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-6">
                <div class="number text-right">
                    <small>TAXES</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes']+$total['sales_taxes_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="display col-md-4">
                <div class="number text-right">
                    <small >C.C. FEE</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['cc_fees'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['cc_fees_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['cc_fees']+$total['cc_fees_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-4">
                <div class="number text-right">
                    <small>VEN.REV.</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['to_show'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['to_show_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['to_show']+$total['to_show_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-4">
                <div class="number text-right">
                    <small>PRNT FEE</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['printed_fee'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['printed_fee_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['printed_fee']+$total['printed_fee_'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>COMMIS.</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions']+$total['commissions_'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>FEE INC.</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl']+$total['fees_incl_'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>FEE OVER</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_over'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_over_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_over']+$total['fees_over_'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>TB.REV.</small>
                    <h4 class="font-green-sharp">
                        $ <span data-counter="counterup" data-value="{{number_format($total['profit'],2)}}">0.00</span>
                    </h4>
                    <h4 class="font-red-haze">
                        $ <span data-counter="counterup" data-value="{{number_format($total['profit_'],2)}}">0.00</span>
                    </h4>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['profit']+$total['profit_'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    <div class="row" id="ticket_sales_charts" style="height:200px;">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="ticket_sales_chart_sales" data-info="{{$graph_credit}}" style="height:200px;"></div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="ticket_sales_chart_refunds" data-info="{{$graph_debit}}" style="height:200px;"></div>
    </div>    
    <!-- BEGIN TOTAL TABLE FOR PRINT-->
    <div id="tb_summary" class="row portlet-body" style="display:none;" >
        @foreach($summary as $summ)
        <h5 class="bold">@php echo $summ['title'] @endphp</h5>
        <table width="100%" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>CHANNEL - TYPE</th>
                    <th style='text-align:center'>TRANS.</th>
                    <th style='text-align:center'>TICKS</th>
                    <th style='text-align:right'>PAID</th>
                    <th style='text-align:right'>DISC.</th>
                    <th style='text-align:right'>TAXES</th>
                    <th style='text-align:right'>CC<br>FEE</th>
                    <th style='text-align:right'>TO<br>SHOW</th>
                    <th style='text-align:right'>PRINT<br>FEE</th>
                    <th style='text-align:right'>COMMIS.</th>
                    <th style='text-align:right'>FEES<br>INCL</th>
                    <th style='text-align:right'>FEES<br>OVER</th>
                    <th style='text-align:right'>GROSS<br>PROFIT</th>
                </tr>
            </thead>
            <tbody>
               @foreach($summ['table'] as $k=>$d)
                <tr @if(strpos($k, 'otal') !== false) style="font-weight:bold" @endif>   
                    <td>{{$k}}</td>
                    <td style="text-align:center">{{number_format($d['purchases'])}}</td>
                    <td style="text-align:center">{{number_format($d['tickets'])}}</td>
                    <td style="text-align:right">$ {{number_format($d['price_paid'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['savings'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['sales_taxes'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['cc_fees'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['to_show'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($d['printed_fee'],2)}}</td>
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
    <div id="tb_coupon" class="row portlet-body" style="display:none;" >
        @if(!empty($coupons))
        <h5 class="bold">Coupons Report</h5>
        @if(!empty($coupons['descriptions']))
        <table width="100%" class="table table-striped table-bordered table-hover">
            <tbody>
                @foreach($coupons['descriptions'] as $k=>$d)
                <tr> <td><b>{{$k}}*</b></td> <td> => {{$d}}</td> </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <table width="100%" class="table table-striped table-bordered table-hover">
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
                    <th style="text-align:center">Sales<br>Taxes</th>
                    <th style="text-align:center">Total<br>Revenue</th>
                    <th style="text-align:center">CC<br>Fees</th>
                    <th style="text-align:center">To<br>Show</th>
                    <th style="text-align:center">Print Fee</th>
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
                    <td style="text-align:right">$ {{number_format($d->sales_taxes,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->cc_fees,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->printed_fee,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->commissions,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->fees_incl,2)}}</td>
                    <td style="text-align:right">$ {{number_format($d->fees_over,2)}}</td>
                    <td style="text-align:right"><b>$ {{number_format($d->commissions+$d->fees_incl+$d->fees_over+$d->printed_fee,2)}}</b></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;border-top:1px solid #000;">
                    <td colspan="6">TOTALS</td>
                    <td style="text-align:center">{{number_format($coupons['total']['tickets'])}} ({{number_format($coupons['total']['purchases'])}})</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['discounts'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['sales_taxes'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['revenue'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['cc_fees'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['to_show'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['printed_fee'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['commissions'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['fees_incl'],2)}}</td>
                    <td style="text-align:right">$ {{number_format($coupons['total']['fees_over'],2)}}</td>
                    <td style="text-align:right"><b>$ {{number_format($coupons['total']['commissions']+$coupons['total']['fees_incl']+$coupons['total']['fees_over']+$coupons['total']['printed_fee'],2)}}</b></td>
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
                        <i></i>{{strtoupper($page_title)}} BREAKDOWN</div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" id="tb_model">
                        <thead>
                            <tr>
                                <th class="all" style="text-align:center"><i class="fa fa-search"></i></th>
                                <th class="all" style="text-align:center">Order</th>
                                <th class="none">Status</th>
                                <th class="all" style="text-align:center">Customer</th>
                                <th class="none">Email</th>
                                <th class="none">Purchased</th>
                                <th class="all" style="text-align:center">Venue</th>
                                <th class="all" style="text-align:center">Show</th>
                                <th class="all" style="text-align:center">Show<br>Date</th>
                                <th class="all" style="text-align:center">Trans.<br>Date</th>
                                <th class="all" style="text-align:center">Qty<br>Tcks</th>                                
                                <th class="all" style="text-align:center">Subt</th>
                                <th class="all" style="text-align:center">Fees</th>
                                <th class="all" style="text-align:center">Disc</th>
                                <th class="all" style="text-align:center">Print<br>Fee</th>
                                <th class="all" style="text-align:center">Taxes</th>     
                                <th class="all" style="text-align:center">Diff</th>
                                <th class="all" style="text-align:center">Total<br>Charged</th>                                
                                <th class="all" style="text-align:center">CC<br>Fee</th>
                                <th class="all" style="text-align:center">Venue<br>Rev.</th>   
                                <th class="all" style="text-align:center">Print<br>Fee(F)</th>
                                <th class="all" style="text-align:center">Com.(C)</th>
                                <th class="all" style="text-align:center">P.Fee<br>Incl.(F)</th>
                                <th class="all" style="text-align:center">P.Fee<br>Over(F)</th>
                                <th class="all" style="text-align:center">TB<br>Rev.<br>(C+F)</th>
                                <th class="none">Coupon</th>
                                <th class="none">Ticket</th>
                                <th class="none">Channel</th>
                                <th class="none">Method</th>
                                <th class="none">Cardholder</th>
                                <th class="none">Authcode</th>
                                <th class="none">Refnum</th>
                                <th class="none">Invoice</th>
                                <th class="none">CC Trans.</th>
                                <th class="none">CC Last 4</th>
                                <th class="none">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $d)
                            <tr @if($d->display<0) class="danger" @elseif($d->status!='Active') class="warning" @endif>
                                <td>{{substr($d->status,0,1)}}</td>
                                <td>{{$d->id}}</td>
                                <td style="text-align:center">{{$d->status}}</td>
                                <td style="text-align:center">{{$d->name}}</td>
                                <td style="text-align:center">{{$d->email}}</td>
                                <td style="text-align:center">{{date('n/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{$d->venue_name}}</td>
                                <td style="text-align:center">{{$d->show_name}}</td>
                                <td style="text-align:center">{{date('n/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td style="text-align:center">{{date('n/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:right">$ {{number_format($d->retail_price,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->savings,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->printed_fee,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->sales_taxes,2)}}</td>
                                <td style="text-align:right" @if($d->other!=0) class="bold font-red" @endif>$ {{number_format($d->other,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->price_paid,2)}}</b></td>
                                <td style="text-align:right">$ {{number_format($d->cc_fees,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->to_show,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->printed_fee,2)}}</td>
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
                                <td style="text-align:center">{{$d->invoice_num}}</td>
                                <td style="text-align:center">${{number_format($d->price_paid,2)}}/ ${{number_format($d->amount,2)}}</td>
                                <td style="text-align:center">...{{$d->last_4}}</td>
                                <td style="text-align:center">@php echo trim($d->note) @endphp}</td>
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
