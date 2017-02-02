@php $page_title='Trend & Pace' @endphp
@extends('layouts.admin')
@section('title', 'Trend & Pace' )

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
            <a class="dashboard-stat dashboard-stat-v2 green" href="#">
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
                        $ <span data-counter="counterup" data-value="{{number_format($total['commissions'],2)}}"></span></div>
                    <div class="desc"> Commissions </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 purple" href="#">
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
<!--     @php print_r($graph) @endphp
 -->    
    <div class="row">
       <div style="display:none" id="trend_pace_json">{{$graph}}</div>
       <div class="col-md-6">
           <div class="portlet light portlet-fit bordered">
               <div class="portlet-body">
                   <div id="trend_pace_chart_qty" style="height:250px;"></div>
               </div>
           </div>
       </div>
       <div class="col-md-6">
           <div class="portlet light portlet-fit bordered">
               <div class="portlet-body">
                   <div id="trend_pace_chart_money" style="height:250px;"></div>
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
                                <th>Show Name</th>
                                <th>Sales<br>2 Days Ago</th>
                                <th>Sales<br>Yesterday</th>
                                <th>Tickets<br>Sold</th>
                                <th>Qty<br>Purchases</th>
                                <th>Retail<br>Price($)</th>
                                <th>Fees($)</th>
                                <th>Commis.($)</th>
                                <th>Revenue($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td>{{$d->show_name}}</td>
                                <td style="text-align:center">{{$d->qty_tickets_two}}</td>
                                <td style="text-align:center">{{$d->qty_tickets_one}}</td>
                                <td style="text-align:center">{{$d->qty_tickets}}</td>
                                <td style="text-align:center">{{$d->qty_purchases}}</td>
                                <td style="text-align:right">$ {{number_format($d->retail_price,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->fees,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->commission,2)}}</td>
                                <td style="text-align:right">$ {{number_format($d->revenue,2)}}</b></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN SEARCH MODAL--> 
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:470px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Search Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/dashboard/trend_pace" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Venue:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="venue" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($venues as $index=>$v)
                                                <option @if($v->id==$venue) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>   
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="show" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($shows as $index=>$s)
                                                <option @if($s->id==$show) selected @endif @if(!empty($show) && $venue==$s->venue_id) style="display:block" @else style="display:none" @endif value="{{$s->id}}" rel="{{$s->venue_id}}">{{$s->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show Time:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="show_times_date">
                                            <input type="text" class="form-control" name="showtime_start_date" value="{{$showtime_start_date}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="showtime_end_date" value="{{$showtime_end_date}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_show_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-3">Sold Date:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="sold_times_date">
                                            <input type="text" class="form-control" name="soldtime_start_date" value="{{$soldtime_start_date}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="soldtime_end_date" value="{{$soldtime_end_date}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_sold_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_search').trigger('reset')">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa" onclick="$('#modal_model_search').modal('hide'); swal({
                                                                                                    title: 'Searching information',
                                                                                                    text: 'Please, wait.',
                                                                                                    type: 'info',
                                                                                                    showConfirmButton: false
                                                                                                });" >Search</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END SEARCH MODAL-->                 
@endsection

@section('scripts') 
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/highcharts/js/highcharts.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/highcharts/js/highcharts-3d.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/highcharts/js/highcharts-more.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/trend_pace.js" type="text/javascript"></script>
@endsection