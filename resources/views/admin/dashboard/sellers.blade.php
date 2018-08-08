@php $page_title='Sellers' @endphp
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
    <div class="row col-md-12" id="totals1">
        <div class="portlet-body">
            <table width="100%" class="table table-hover table-borderless table-condensed table-header-fixed table-responsive">
                <tr>
                    <th class="success" colspan="3" style='text-align:center'>CASH</th>
                    <th class="warning" colspan="3" style='text-align:center'>CREDIT</th>
                    <th class="danger" colspan="3" style='text-align:center'>REFUNDS</th>
                    <th class="info" colspan="3" style='text-align:center'>TOTAL</th>
                <tr>
                    <th class="success" style='text-align:center'>TRANS.</th>
                    <th class="success" style='text-align:center'>TICKS</th>
                    <th class="success" style='text-align:right'>AMOUNT</th>
                    
                    <th class="warning" style='text-align:center'>TRANS.</th>
                    <th class="warning" style='text-align:center'>TICKS</th>
                    <th class="warning" style='text-align:right'>AMOUNT</th>
                    
                    <th class="danger" style='text-align:center'>TRANS.</th>
                    <th class="danger" style='text-align:center'>TICKS</th>
                    <th class="danger" style='text-align:right'>AMOUNT</th>
                    
                    <th class="info" style='text-align:center'>TRANS.</th>
                    <th class="info" style='text-align:center'>TICKS</th>
                    <th class="info" style='text-align:right'>AMOUNT</th>
                </tr>
                <tr>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['s_trans'])}}">0</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['s_tick'])}}">0</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['s_tot'],2)}}">0.00</span></th>
                    
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['c_trans'])}}">0</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['c_tick'])}}">0</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['c_tot'],2)}}">0.00</span></th>
                    
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['r_trans'])}}">0</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['r_tick'])}}">0</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['r_tot'],2)}}">0.00</span></th>
                    
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['t_trans'])}}">0</span></th>
                    <th style='text-align:center'><span data-counter="counterup" data-value="{{number_format($total['t_tick'])}}">0</span></th>
                    <th style='text-align:right'>$ <span data-counter="counterup" data-value="{{number_format($total['t_tot'],2)}}">0.00</span></th>
                </tr>
            </table>
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
                    <table class="table table-striped table-bordered table-hover" id="tb_model">
                        <thead>
                            <tr>
                                <th style="text-align:center">Seller</th>
                                <th style="text-align:center">Time<br>In</th>
                                <th style="text-align:center">Time<br>Out</th>
                                <th style="text-align:right">Cash<br>In</th>
                                <th style="text-align:right">Cash<br>Out</th>
                                <th style="text-align:right">Cash<br>Trans.</th>
                                <th style="text-align:right">Cash<br>Tickt.</th>
                                <th style="text-align:right">Cash<br>Paid.</th>
                                <th style="text-align:right">Credit<br>Trans.</th>
                                <th style="text-align:right">Credit<br>Tickt.</th>
                                <th style="text-align:right">Credit<br>Paid.</th>
                                <th style="text-align:right">Ref.Cb<br>Trans.</th>
                                <th style="text-align:right">Ref.Cb<br>Tickt.</th>
                                <th style="text-align:right">Ref.Cb<br>Paid.</th>
                                <th style="text-align:right">Total<br>Trans.</th>
                                <th style="text-align:right">Total<br>Tickt.</th>
                                <th style="text-align:right">Total<br>Paid.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr @if(empty($d->time_out)) class="warning" @endif>
                                <td style="text-align:center">{{$d->email}}</td>
                                <td style="text-align:center">{{$d->time_in}}</td>
                                <td style="text-align:center">{{$d->time_out}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->cash_in,2)}}</b></td>
                                <td style="text-align:right"><b>$ {{number_format($d->cash_out,2)}}</b></td>
                                <td style="text-align:right">$ {{number_format($d->s_trans,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->s_tick,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->s_tot,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->c_trans,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->c_tick,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->c_tot,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->r_trans,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->r_tick,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->r_tot,2)}}</td>
                                <td style="text-align:right"><b>$ {{number_format($d->t_trans,2)}}</b></td>
                                <td style="text-align:right"><b>$ {{number_format($d->t_tick,2)}}</b></td>
                                <td style="text-align:right"><b>$ {{number_format($d->t_tot,2)}}</b></td>
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
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/sellers'])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="/js/admin/purchases/filter.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/sellers.js" type="text/javascript"></script>
@endsection
