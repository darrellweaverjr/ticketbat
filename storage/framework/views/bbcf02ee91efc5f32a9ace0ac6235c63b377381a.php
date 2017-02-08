<?php  $page_title='Coupons'  ?>

<?php $__env->startSection('title', 'Coupons' ); ?>

<?php $__env->startSection('styles'); ?> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> <?php echo e($page_title); ?> 
        <small> - List, add, edit and remove coupons.</small>
    </h1>
    <!-- END PAGE TITLE-->    
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> <?php echo e(strtoupper($page_title)); ?> LIST </span>
                    </div>
                    <div class="actions">                        
                        <div class="btn-group">
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_model .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="12%"> Code </th>
                                <th width="12%"> Discount Type </th>
                                <th width="12%"> Discount Scope </th>
                                <th width="12%"> Coupon Type </th>
                                <th width="5%"> Redemptions </th>
                                <th width="45%"> Description </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $discounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$d): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="<?php echo e($d->id); ?>" value="<?php echo e($d->code); ?>" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="12%"> <?php echo e($d->code); ?> </td>
                                <td width="12%"> <span class="label label-sm sbold 
                                    <?php if($d->discount_type == 'Dollar'): ?> label-success 
                                    <?php elseif($d->discount_type == 'N for N'): ?> label-danger 
                                    <?php elseif($d->discount_type == 'Percent'): ?> label-warning
                                    <?php else: ?> label-default
                                    <?php endif; ?>
                                    "> <?php echo e($d->discount_type); ?> </span> 
                                </td> 
                                <td width="12%"> <span class="label label-sm sbold 
                                    <?php if($d->discount_scope == 'Ticket'): ?> label-success 
                                    <?php elseif($d->discount_scope == 'Total'): ?> label-danger 
                                    <?php elseif($d->discount_scope == 'Merchandise'): ?> label-warning
                                    <?php else: ?> label-default
                                    <?php endif; ?>
                                    "> <?php echo e($d->discount_scope); ?> </span> 
                                </td> 
                                <td width="12%"> <span class="label label-sm sbold 
                                    <?php if($d->coupon_type == 'Normal'): ?> label-success 
                                    <?php elseif($d->coupon_type == 'Broker'): ?> label-danger 
                                    <?php elseif($d->coupon_type == 'Affiliate'): ?> label-warning 
                                    <?php elseif($d->coupon_type == 'Admin'): ?> label-info 
                                    <?php else: ?> label-default
                                    <?php endif; ?>
                                    "> <?php echo e($d->coupon_type); ?> </span> 
                                </td> 
                                <td width="5%"><center><span class="label label-sm sbold 
                                    <?php if($d->purchases): ?> label-success 
                                    <?php else: ?> label-danger 
                                    <?php endif; ?>
                                    "> <?php echo e($d->purchases); ?> </span></center></td> 
                                <td width="45%"> <?php echo e($d->description); ?> </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?> 
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->   
    <!-- BEGIN UPDATE MODAL--> 
    <div id="modal_model_update" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title"></center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">Code
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-3 show-error">
                                    <input type="text" name="code" class="form-control" placeholder="0000" /> </div>
                                <label class="control-label col-md-2">Percent Off
                                </label>
                                <div class="col-md-1 show-error">
                                    <input type="text" name="start_num" class="form-control" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
   event.charCode == 0 "/> </div>    
                                <label class="control-label col-md-3"># of Codes (0 for infinite)
                                </label>
                                <div class="col-md-1 show-error">
                                    <input type="text" name="quantity" class="form-control" value="0" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) ||  
   event.charCode == 0 "/> </div>  
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">Discount Type
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="discount_type">
                                        <?php $__currentLoopData = $discount_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$t): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($index); ?>"> <?php echo e($t); ?> </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                </div>
                                <label class="control-label col-md-2">Discount Scope
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="discount_scope">
                                        <?php $__currentLoopData = $discount_scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$t): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($index); ?>"> <?php echo e($t); ?> </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                </div>
                                <label class="control-label col-md-2">Coupon Type
                                </label>
                                <div class="col-md-2 show-error">
                                    <select class="form-control" name="coupon_type">
                                        <?php $__currentLoopData = $coupon_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$t): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($index); ?>"> <?php echo e($t); ?> </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group"> 
                                <div class="col-md-6 show-error">
                                    <input type="hidden"   name="start_date" value="" />
                                    <input type="hidden"   name="end_date" value="" />
                                    <label class="control-label">Dates Action:
                                    </label><br>
                                    <div id="start_end_date" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                                        <i class="icon-calendar"></i>&nbsp;
                                        <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                </div>
                                <div class="col-md-6 show-error">
                                    <input type="hidden"   name="effective_start_date" value="" />
                                    <input type="hidden"   name="effective_end_date" value="" />
                                    <label>                                        
                                        <input type="hidden"   name="effective_dates" value="0" />
                                        <input type="checkbox" name="effective_dates" value="1" /> Use Effective Dates?
                                    </label><br>
                                    <div id="effective_start_end_date" class="pull-right tooltips btn btn-sm show-error" data-container="body" data-placement="bottom">
                                        <i class="icon-calendar"></i>&nbsp;
                                        <span class="thin uppercase hidden-xs"> - </span>&nbsp;
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                </div>  
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Description:
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div> 
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-md-2">Coupon Scope [Shows]:
                                </label>
                                <div class="col-md-10 show-error">
                                    <select class="form-control" name="shows[]" multiple="multiple" size="8">
                                        <?php $__currentLoopData = $shows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$s): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-green">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END UPDATE MODAL--> 
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?> 
<script src="/js/admin/coupons/index.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>