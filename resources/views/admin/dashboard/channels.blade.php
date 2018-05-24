@php $page_title='Channels' @endphp
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
        <small>statistics, charts and reports (by default the last 30 days).</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row" id="totals">
        <div class="dashboard-stat2 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="display col-md-3">
                <div class="number text-left">
                    <small>-</small>
                    <h4 class="bold">TOTAL</h4>
                </div>
            </div>
            <div class="display col-md-5">
                <div class="number text-right">
                    <small>TOTAL CHARGED</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['price_paids'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-2">
                <div class="number text-center">
                    <small>TRANS.</small>
                    <h4 class="bold">
                        <span data-counter="counterup" data-value="{{number_format($total['purchases'])}}">0</span> 
                    </h4>
                </div>
            </div>
            <div class="display col-md-2">
                <div class="number text-center">
                    <small>TICKS.</small>
                    <h4 class="bold">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets'])}}">0</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="display col-md-6">
                <div class="number text-right">
                    <small>DISCOUNTS</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['discounts'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-6">
                <div class="number text-right">
                    <small>TAXES</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['sales_taxes'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="display col-md-4">
                <div class="number text-right">
                    <small >C.C. FEE</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['cc_fees'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-4">
                <div class="number text-right">
                    <small>VEN.REV.</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['to_show'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-4">
                <div class="number text-right">
                    <small>PRNT FEE</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['printed_fee'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
        </div>
        <div class="dashboard-stat2 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>COMMIS.</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}">0.00</span>
                    </h4>
                </div>
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>FEE INC.</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_incl'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>FEE OVER</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees_over'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
            <div class="display col-md-3">
                <div class="number text-right">
                    <small>TB.REV.</small>
                    <h4 class="bold">
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions']+$total['fees_incl']+$total['fees_over']+$total['printed_fee'],2)}}">0.00</span>
                    </h4>
                </div>                    
            </div>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    <div class="row">
       <div class="col-md-6">
           <div class="portlet light portlet-fit bordered">
               <div class="portlet-body responsive">
                   <div id="chart_channel"  data-info="{{$graph['channel']}}" class="chart" style="height:250px;"></div>
               </div>
           </div>
       </div>
       <div class="col-md-6">
           <div class="portlet light portlet-fit bordered">
               <div class="portlet-body responsive">
                   <div id="chart_show"  data-info="{{$graph['show']}}" class="chart" style="height:250px;"></div>
               </div>
           </div>
       </div>
   </div>
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
                                @if($search['order']=='channel')
                                <th style="text-align:center">Venue</th>
                                <th style="text-align:center">Show</th>
                                <th style="text-align:center">Channel</th>
                                @else
                                <th style="text-align:center">Channel</th>
                                <th style="text-align:center">Venue</th>
                                <th style="text-align:center">Show</th>
                                @endif
                                <th style="text-align:center">Qty<br>Sold</th>
                                <th style="text-align:center">Purch.</th>
                                <th style="text-align:center">Total<br>Revenue</th>
                                <th style="text-align:center">Sales<br>Taxes</th>
                                @if(Auth::user()->user_type_id != 5)
                                <th style="text-align:center">Discounts</th>
                                @endif
                                <th style="text-align:center">CC<br>Fees</th>
                                <th style="text-align:center">To<br>Show</th>
                                <th style="text-align:center">Print<br>Fee</th>
                                <th style="text-align:center">@if(Auth::user()->user_type_id != 5) Commiss. @else TB Comm.<br>Expense @endif</th>
                                <th style="text-align:center">Fees<br>Incl</th>
                                <th style="text-align:center">Fees<br>Over</th>
                                <th style="text-align:center">@if(Auth::user()->user_type_id != 5) Gross<br>Profit @else TB Retains @endif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                @if($search['order']=='channel')
                                <td>{{$d->venue_name}}</td>
                                <td>{{$d->show_name}}</td>
                                <td>{{$d->channel}}</td>
                                @else
                                <td>{{$d->channel}}</td>
                                <td>{{$d->venue_name}}</td>
                                <td>{{$d->show_name}}</td>
                                @endif
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:center">{{number_format($d->purchases)}}</td>
                                <td style="text-align:right">$ {{number_format($d->revenue,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->sales_taxes,2)}}</td>
                                @if(Auth::user()->user_type_id != 5)
                                <td style="text-align:right">$ {{number_format($d->discounts,2)}}</td>
                                @endif
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
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/channels','order'=>1])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/amcharts.js" type="text/javascript"></script>
<script src="{{config('app.theme')}}js/pie.js" type="text/javascript"></script>
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/channels.js" type="text/javascript"></script>
@endsection
