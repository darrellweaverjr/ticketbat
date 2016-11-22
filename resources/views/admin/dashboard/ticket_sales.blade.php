@php $page_title='Ticket Sales' @endphp
@extends('layouts.admin')
@section('title', 'Ticket Sales' )

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
                    <i class="fa fa-ticket"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" data-value="{{number_format($total['tickets'])}}">0</span>
                    </div>
                    <div class="desc"> Tickets Sold </div>
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
                        $ <span data-counter="counterup" data-value="{{number_format($total['savings'],2)}}"></span></div>
                    <div class="desc"> Discounts Applied </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 green" href="#">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number">
                        $ <span data-counter="counterup" data-value="{{number_format($total['total'],2)}}"></span>
                    </div>
                    <div class="desc"> Total collected </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 purple" href="#">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number"> +
                        $ <span data-counter="counterup" data-value="{{number_format($total['show_earned'],2)}}"></span></div>
                    <div class="desc"> To Show </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 green" href="#">
                <div class="visual">
                    <i class="fa fa-usd"></i>
                </div>
                <div class="details">
                    <div class="number"> +
                        $ <span data-counter="counterup" data-value="{{number_format($total['commission_earned'],2)}}"></span></div>
                    <div class="desc"> Commision Earned </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 red" href="#">
                <div class="visual">
                    <i class="fa fa-money"></i>
                </div>
                <div class="details">
                    <div class="number"> +
                        $ <span data-counter="counterup" data-value="{{number_format($total['fees'],2)}}"></span></div>
                    <div class="desc"> Fee Revenue Earned </div>
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
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Show Name </th>
                                <th> Show <br> Date </th>
                                <th> Sold <br> Date </th>
                                <th> Customer Name </th>
                                <th> Order # </th>
                                <th> Price <br> Code </th>
                                <th> Ticket <br> Type </th>
                                <th> Tickets <br> (Qty) </th>
                                <th> Tickets($) </th>  
                                <th> Savings($) </th>
                                <th> Fees($) </th>
                                <th> Commissions($) </th>
                                <th> To Show($) </th>
                                <th> TOTAL($) </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td> {{$d->show_name}} </td>
                                <td> {{date('m/d/Y g:ia',strtotime($d->show_time))}} </td>
                                <td> {{date('m/d/Y g:ia',strtotime($d->created))}} </td>
                                <td> {{$d->name}} </td>
                                <td> {{$d->id}} </td>
                                <td> {{$d->code}} </td>
                                <td> {{$d->ticket_type}} </td>
                                <td> {{number_format($d->tickets)}} </td>
                                <td> $ {{number_format($d->tickets_price,2)}} </td>
                                <td> $ {{number_format($d->savings,2)}} </td>
                                <td> $ {{number_format($d->fees,2)}} </td>
                                <td> $ {{number_format($d->commission_earned,2)}} </td>
                                <td> $ {{number_format($d->show_earned,2)}} </td>
                                <td><b> $ {{number_format($d->total,2)}} </b></td>
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
<script src="/js/admin/dashboard/ticket_sales.js" type="text/javascript"></script>
@endsection