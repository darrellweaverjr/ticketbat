@php $page_title='Coupons' @endphp
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
        <small>statistics, charts and reports (by default the last 7 days).</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row col-md-12" id="totals">
        <div class="portlet-body">
            <table width="100%" class="table table-hover table-borderless table-condensed table-header-fixed table-responsive">
                <tr>
                    <th>-</th>
                    <th style='text-align:right'>TOTAL CHARGED</th>
                    <th style='text-align:center'>TRANS.</th>
                    <th style='text-align:center'>TICKS</th>
                    <th style='text-align:right'>DISCOUNTS</th>
                    <th style='text-align:right'>TAXES</th>
                    <th style='text-align:right'>C.C.FEE</th>
                    <th style='text-align:right'>VEN.REV.</th>
                    <th style='text-align:right'>PRNT FEE</th>
                    <th style='text-align:right'>COMMIS.</th>
                    <th style='text-align:right'>FEE INCL.</th>
                    <th style='text-align:right'>FEE OVER</th>
                    <th style='text-align:right'>TB.REV.</th>
                </tr>
                <tr>
                    <th>TOTAL</th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['price_paids'],2)}}">0.00</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['purchases'])}}">0</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['tickets'])}}">0</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['discounts'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['cc_fees'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['to_show'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['printed_fee'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['fees_incl'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['fees_over'],2)}}">0.00</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['commissions']+$total['fees_incl']+$total['fees_over']+$total['printed_fee'],2)}}">0.00</span></th>
                </tr>
            </table>
        </div> 
    </div>
    <!-- END DASHBOARD STATS 1-->
    <!-- BEGIN DESCRIPTION TABLE FOR PRINT-->
    <div id="tb_descriptions" class="portlet-body" style="display:none;" >
        <table width="100%" class="table table-striped table-bordered table-hover">
            <tbody>
                @foreach($descriptions as $k=>$d)
                <tr>
                    <td><b>{{$k}}*</b></td>
                    <td> => {{$d}}</td>
                </tr>
                @endforeach
            </tbody>
        </table><hr>
    </div>
    <!-- END DESCRIPTION TABLE FOR PRINT-->
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
                    <table class="table table-striped table-bordered table-hover" id="tb_model">
                        <thead>
                            <tr>
                                <th>Venue</th>
                                <th>Show</th>
                                <th style="text-align:center">Code</th>
                                <th style="text-align:center">Distrib.<br>At</th>
                                <th style="text-align:center">Sales<br>-7D</th>
                                <th style="text-align:center">Sales<br>-1D</th>
                                <th style="text-align:center">Qty<br>Sold</th>
                                <th style="text-align:center">Purch.</th>
                                <th style="text-align:center">Total<br>Revenue</th>
                                <th style="text-align:center">Sales<br>Taxes</th>
                                <th style="text-align:center">CC<br>Fees</th>
                                <th style="text-align:center">To<br>Show</th>
                                <th style="text-align:center">Print<br>Fee</th>
                                <th style="text-align:center">Comm.</th>
                                <th style="text-align:center">Fees<br>Incl</th>
                                <th style="text-align:center">Fees<br>Over</th>
                                <th style="text-align:center">Gross<br>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td>{{$d->venue_name}}</td>
                                <td>{{$d->show_name}}</td>
                                <td style="text-align:center">{{$d->code}}</td>
                                <td style="text-align:center">{{$d->distributed_at}}</td>
                                <td style="text-align:center">{{$d->tickets_seven}}</td>
                                <td style="text-align:center">{{$d->tickets_one}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:center">{{number_format($d->purchases)}}</td>
                                <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->sales_taxes,2)}}</td>
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
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN INCLUDE FILTER SEARCH-->
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/coupons'])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/highcharts.js" type="text/javascript"></script>
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/coupons.js" type="text/javascript"></script>
@endsection
