<?php  $page_title='Trend & Pace'  ?>

<?php $__env->startSection('title', 'Trend & Pace' ); ?>

<?php $__env->startSection('styles'); ?> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 

    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> <?php echo e($page_title); ?>

        <small>statistics, charts, recent events and reports</small>
    </h1>
    <!-- END PAGE TITLE-->                   
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS 1-->
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 dark">
                <div class="visual">
                    <i class="fa fa-ticket"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" data-value="<?php echo e(number_format($total['tickets'])); ?>">0</span>
                    </div>
                    <div class="desc">Tickets Sold 
                        <br>Purchases: <span data-counter="counterup" data-value="<?php echo e(number_format($total['purchases'])); ?>">0</span>
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
                        $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['retail_prices']-$total['discounts']+$total['fees'],2)); ?>"></span></div>
                    <div class="desc">Total Revenue 
                        <br>Discounts: $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['discounts'],2)); ?>"></span>
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
                        $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['to_show'],2)); ?>"></span></div>
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
                        $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['commissions'],2)); ?>"></span></div>
                    <div class="desc">Commission Revenue</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 blue-steel">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number"> 
                        $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['fees'],2)); ?>"></span></div>
                    <div class="desc">Fee Revenue</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <a class="dashboard-stat dashboard-stat-v2 purple">
                <div class="visual">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="number"> 
                        $ <span data-counter="counterup" data-value="<?php echo e(number_format($total['commissions']+$total['fees'],2)); ?>"></span>
                    </div>
                    <div class="desc">Gross Profit</div>
                </div>
            </a>
        </div>
    </div>
    <!-- END DASHBOARD STATS 1-->
    <div class="row">
       <div style="display:none" id="trend_pace_json"><?php echo e($graph); ?></div>
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
                        <i></i><?php echo e(strtoupper($page_title)); ?></div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="tb_model">
                        <thead>
                            <tr>
                                <th>Show Name</th>
                                <th style="text-align:center">Show<br>Time</th>
                                <th style="text-align:center">Sales<br>-2D</th>
                                <th style="text-align:center">Sales<br>-1D</th>
                                <th style="text-align:center">Tickets<br>Sold</th>
                                <th style="text-align:center">Purch.<br>Qty</th>
                                <th style="text-align:center">Total<br>Revenue</th>  
                                <th style="text-align:center">Discounts</th>
                                <th style="text-align:center">To Show</th>
                                <th style="text-align:center">Commiss.</th>
                                <th style="text-align:center">Fees</th>
                                <th style="text-align:center">Gross<br>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td><?php echo e($d->show_name); ?></td>
                                <td style="text-align:center" data-order="<?php echo e(strtotime($d->show_time)); ?>"><?php echo e(date('m/d/Y g:ia',strtotime($d->show_time))); ?></td>
                                <td style="text-align:center"><?php echo e($d->tickets_two); ?></td>
                                <td style="text-align:center"><?php echo e($d->tickets_one); ?></td>
                                <td style="text-align:center"><?php echo e(number_format($d->tickets)); ?></td>
                                <td style="text-align:center"><?php echo e(number_format($d->purchases)); ?></td>
                                <td style="text-align:right">$ <?php echo e(number_format($d->retail_prices-$d->discounts+$d->fees,2)); ?></td>
                                <td style="text-align:right">$ <?php echo e(number_format($d->discounts,2)); ?></td>
                                <td style="text-align:right">$ <?php echo e(number_format($d->to_show,2)); ?></td>
                                <td style="text-align:right">$ <?php echo e(number_format($d->commissions,2)); ?></td>
                                <td style="text-align:right">$ <?php echo e(number_format($d->fees,2)); ?></td>
                                <td style="text-align:right"><b>$ <?php echo e(number_format($d->commissions+$d->fees,2)); ?></b></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
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
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Venue:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="venue" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                <?php $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option <?php if($v->id==$venue): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
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
                                                <?php $__currentLoopData = $shows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$s): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option <?php if($s->id==$show): ?> selected <?php endif; ?> <?php if(!empty($show) && $venue==$s->venue_id): ?> style="display:block" <?php else: ?> style="display:none" <?php endif; ?> value="<?php echo e($s->id); ?>" rel="<?php echo e($s->venue_id); ?>"><?php echo e($s->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show Time:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="show_times_date">
                                            <input type="text" class="form-control" name="showtime_start_date" value="<?php echo e($showtime_start_date); ?>" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="showtime_end_date" value="<?php echo e($showtime_end_date); ?>" readonly="true">
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
                                            <input type="text" class="form-control" name="soldtime_start_date" value="<?php echo e($soldtime_start_date); ?>" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="soldtime_end_date" value="<?php echo e($soldtime_end_date); ?>" readonly="true">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?> 
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/highcharts/js/highcharts.js" type="text/javascript"></script>
<script src="/js/admin/dashboard/trend_pace.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>