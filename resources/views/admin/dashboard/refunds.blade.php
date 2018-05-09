@php $page_title='Refunds' @endphp
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
        <small>statistics and reports (by default the last 30 days).</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row" id="totals">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 purple">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['amount'],2)}}"></span></div>
                    <div class="desc"> Amount </div>
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
                    <table class="table table-striped table-bordered table-hover" id="tb_model">
                        <thead>
                            <tr>
                                <th>Cardholder</th>
                                <th>Venue</th>
                                <th>Show</th>
                                <th>Show<br>Date</th>
                                <th>Sold<br>Date</th>
                                <th>Qty<br>Sold</th>
                                <th>Amount</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td>{{$d->card_holder}}</td>
                                <td>{{$d->venue_name}}</td>
                                <td>{{$d->show_name}}</td>
                                <td data-order="{{strtotime($d->show_time)}}">{{date('m/d/Y g:ia',strtotime($d->show_time))}}</td>
                                <td data-order="{{strtotime($d->created)}}">{{date('m/d/Y g:ia',strtotime($d->created))}}</td>
                                <td style="text-align:center">{{number_format($d->tickets)}}</td>
                                <td style="text-align:right">$ {{number_format($d->amount,2)}}<br><b>{{$d->payment_type}}</b></td>
                                <td>@php echo $d->note @endphp</b></td>
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
    @includeIf('admin.purchases.filter', ['search'=>$search,'action'=>'/admin/dashboard/refunds'])
    <!-- END INCLUDE FILTER SEARCH-->
@endsection

@section('scripts')
<script src="{{config('app.theme')}}js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/refunds.js" type="text/javascript"></script>
@endsection
