@php $page_title='Referrals' @endphp
@extends('layouts.admin')
@section('title', 'Referrals' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 

    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small>statistics, charts, recent events and reports</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="/admin/home">Dashboard</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{$page_title}}</span>
            </li>
        </ul>
        <div class="page-toolbar">
            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="thin uppercase hidden-xs"></span>&nbsp;
                <i class="fa fa-angle-down"></i>
            </div>
        </div>
    </div>
    <!-- END PAGE BAR -->                        
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" data-value="{{number_format($total['qty_tickets'])}}">0</span>
                    </div>
                    <div class="desc"> Sales </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" data-value="{{number_format($total['qty_purchases'])}}">0</span>
                    </div>
                    <div class="desc"> Purchases </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 red" href="#">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['retail_price'],2)}}"></span></div>
                    <div class="desc"> Retail Price </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 red" href="#">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees'],2)}}"></span></div>
                    <div class="desc"> Fees </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 red" href="#">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['revenue'],2)}}"></span></div>
                    <div class="desc"> Revenue </div>
                </div>
            </a>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    
    @php //print_r($data) @endphp
    <div class="row">
        <div class="col-md-6">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-green-haze"></i>
                        <span class="caption-subject bold uppercase font-green-haze"> Tickets sold</span>
                        <span class="caption-helper">bar and line chart mix</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="chart_6" class="chart" style="height: 200px;"> </div>
                    <div class="well margin-top-20" style="display:none">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="text-left">Top Radius:</label>
                                <input class="chart_7_chart_input" data-property="topRadius" type="range" min="0" max="1.5" value="1" step="0.01" /> </div>
                            <div class="col-sm-3">
                                <label class="text-left">Angle:</label>
                                <input lass="chart_7_chart_input" data-property="angle" type="range" min="0" max="89" value="30" step="1" /> </div>
                            <div class="col-sm-3">
                                <label class="text-left">Depth:</label>
                                <input class="chart_7_chart_input" data-property="depth3D" type="range" min="1" max="120" value="40" step="1" /> </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
        </div>
        <div class="col-md-6">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-green-haze"></i>
                        <span class="caption-subject bold uppercase font-green-haze"> Purchases (Qty)</span>
                        <span class="caption-helper">bar and line chart mix</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="chart_7" class="chart" style="height: 200px;"> </div>
                    <div class="well margin-top-20" style="display:none">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="text-left">Top Radius:</label>
                                <input class="chart_7_chart_input" data-property="topRadius" type="range" min="0" max="1.5" value="1" step="0.01" /> </div>
                            <div class="col-sm-3">
                                <label class="text-left">Angle:</label>
                                <input lass="chart_7_chart_input" data-property="angle" type="range" min="0" max="89" value="30" step="1" /> </div>
                            <div class="col-sm-3">
                                <label class="text-left">Depth:</label>
                                <input class="chart_7_chart_input" data-property="depth3D" type="range" min="1" max="120" value="40" step="1" /> </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
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
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Referral </th>
                                <th> Show Name </th>
                                <th> Tickets Sold </th>
                                <th> Qty Purchases </th>
                                <th> Retail Price($) </th>
                                <th> Fees($) </th>
                                <th> Commissions($) </th>
                                <th> Revenue($) </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td> {{$d->referral_url}} </td>
                                <td> {{$d->show_name}} </td>
                                <td> {{$d->qty_tickets}} </td>
                                <td> {{$d->qty_purchases}} </td>
                                <td> $ {{number_format($d->retail_price,2)}} </td>
                                <td> $ {{number_format($d->fees,2)}} </td>
                                <td> $ {{number_format($d->commission,2)}} </td>
                                <td> $ {{number_format($d->revenue,2)}} </b></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
                    
@endsection

@section('scripts') 
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/pages/scripts/table-datatables-buttons.min.js" type="text/javascript"></script>

<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
<script src="/themes/admin/assets/pages/scripts/charts-amcharts.min.js" type="text/javascript"></script>

<script src="/js/admin/dashboard/referrals.js" type="text/javascript"></script>
@endsection