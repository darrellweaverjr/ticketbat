<?php  $page_title='Shows'  ?>

<?php $__env->startSection('title', 'Shows' ); ?>

<?php $__env->startSection('styles'); ?> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> <?php echo e($page_title); ?> 
        <small> - List, add, edit and remove shows.</small>
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
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search"> Search 
                                <i class="fa fa-search"></i>
                            </button>
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
                                <th width="15%">Show</th>
                                <th width="67%">Description</th>
                                <th width="8%">Category</th>
                                <th width="3%">Featured</th>
                                <th width="5%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $shows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$s): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="<?php echo e($s->id); ?>" value="<?php echo e($s->name); ?>" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="15%" data-order="<?php echo e($s->name); ?>"> 
                                    <?php if(preg_match('/\/uploads\//',$s->image_url)): ?> <?php  $s->image_url = env('IMAGE_URL_OLDTB_SERVER').$s->image_url  ?> <?php endif; ?>
                                    <?php if(preg_match('/\/s3\//',$s->image_url)): ?> <?php  $s->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$s->image_url)  ?> <?php endif; ?>
                                    <center style="color:red;"><i><b><a data-toggle="modal" href="#modal_details_<?php echo e($s->id); ?>"><img alt="- No image -" height="100px" width="200px" src="<?php echo e($s->image_url); ?>"/></a></b></i></center>
                                </td>
                                <td class="search-item clearfix" width="67%"> 
                                    <div class="search-content">
                                        <h4 class="search-title"><b><a data-toggle="modal" href="#modal_details_<?php echo e($s->id); ?>"><?php echo e($s->name); ?></a></b> [<a href="https://www.ticketbat.com/event/<?php echo e($s->slug); ?>" target="_blank"><?php echo e($s->slug); ?></a>]</h4>
                                        <small><i>
                                            <?php if($s->url): ?>Web Site: <a href="<?php echo e($s->url); ?>" target="_blank"><?php echo e($s->url); ?> </a><?php endif; ?>
                                            <?php if($s->googleplus): ?>Google+: <a href="<?php echo e($s->googleplus); ?>" target="_blank"><?php echo e($s->googleplus); ?> </a><?php endif; ?>
                                            <?php if($s->youtube): ?>YouTube: <a href="<?php echo e($s->youtube); ?>" target="_blank"><?php echo e($s->youtube); ?> </a><?php endif; ?> 
                                            <?php if($s->facebook): ?>Facebook: <a href="<?php echo e($s->facebook); ?>" target="_blank"><?php echo e($s->facebook); ?> </a><?php endif; ?> 
                                            <?php if($s->twitter): ?>Twitter: <a href="<?php echo e($s->twitter); ?>" target="_blank"><?php echo e($s->twitter); ?> </a><?php endif; ?> 
                                            <?php if($s->yelpbadge): ?>YelpBadge: <a href="<?php echo e($s->yelpbadge); ?>" target="_blank"><?php echo e($s->yelpbadge); ?> </a><?php endif; ?> 
                                            <?php if($s->instagram): ?>Instagram: <a href="<?php echo e($s->instagram); ?>" target="_blank"><?php echo e($s->instagram); ?> </a><?php endif; ?> 
                                        </i></small><br>
                                        <?php if($s->short_description): ?> <?php echo e($s->short_description); ?> <?php else: ?> <i style="color:red"><b>- No short description -</b></i> <?php endif; ?> 
                                    </div>
                                </td>
                                <td width="8%"><center> <?php echo e($s->category); ?> </center></td>
                                <td width="3%"><center> <span class="label label-sm sbold
                                    <?php if($s->is_featured): ?> label-success"> Yes 
                                    <?php else: ?> label-danger"> No 
                                    <?php endif; ?>
                                    </center></span> 
                                </td>
                                <td width="5%"><center> <span class="label label-sm sbold
                                    <?php if($s->is_active): ?> label-success"> Active 
                                    <?php else: ?> label-danger"> Inactive 
                                    <?php endif; ?>
                                    </center></span> 
                                </td>
                            </tr>
                            <!-- BEGIN DETAILS MODAL--> 
                            <!---->
                            <!-- END DETAILS MODAL--> 
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
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_model_update_general" data-toggle="tab" aria-expanded="true"> General </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_sponsor" data-toggle="tab" aria-expanded="true"> Sponsorship </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_reports" data-toggle="tab" aria-expanded="false"> Reports </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_checking" data-toggle="tab" aria-expanded="false"> Checking </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_passwords" data-toggle="tab" aria-expanded="true"> Passwords </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_showtimes" data-toggle="tab" aria-expanded="true"> Showtimes </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_tickets" data-toggle="tab" aria-expanded="true"> Tickets </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_bands" data-toggle="tab" aria-expanded="true"> Bands </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_contracts" data-toggle="tab" aria-expanded="true"> Contracts </a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#tab_model_update_multimedia" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> Multimedia
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <li class="">
                                                <a href="#tab_model_update_images" data-toggle="tab"> Images </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_banners" data-toggle="tab"> Banners </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_videos" data-toggle="tab"> Videos </a>
                                            </li><!--
                                            <li class="">
                                                <a href="#tab_model_update_reviews" data-toggle="tab"> Reviews </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_awards" data-toggle="tab"> Awards </a>
                                            </li>-->
                                        </ul>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_model_update_general">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> General </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Name
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="name" class="form-control" placeholder="My Show" /> 
                                                    </div>
                                                    <label class="control-label col-md-3">Slug
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6 show-error">
                                                        <input type="text" name="slug" class="form-control" readonly="true" /> 
                                                    </div>
                                                    <div class="col-md-3 show-error">
                                                        <button class="btn btn-block" id="go_to_slug" type="button">Go to
                                                            <i class="fa fa-link"></i>
                                                        </button>
                                                    </div>
                                                    <label class="control-label col-md-3">Category
                                                    <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="category_id">
                                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$c): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                                <?php if($c->id_parent == 0): ?>
                                                                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                                                    <?php $__currentLoopData = $c->children()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $children): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                                        <option value="<?php echo e($children->id); ?>">&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo e($children->name); ?></option>
                                                                        <?php $__currentLoopData = $children->children()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $niece): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                                            <option value="<?php echo e($niece->id); ?>">&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo e($niece->name); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-3">Venue
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="venue_id">
                                                            <?php $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                            <option class="<?php echo e($v->restrictions); ?>" value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                        </select>
                                                    </div> 
                                                    <label class="control-label col-md-3">Stage
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="stage_id">
                                                            <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$t): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                                <option style = "display:<?php if(isset($venues[0]) && $venues[0]->id == $t->venue_id): ?> block <?php else: ?> none <?php endif; ?> ;"                         class="venue_<?php echo e($t->venue_id); ?>" value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                        </select>
                                                    </div> 
                                                    <label class="control-label col-md-3">Restriction
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="restrictions">
                                                            <?php $__currentLoopData = $restrictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$r): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                            <?php if(isset($venues[0]) && $venues[0]->restrictions == $r): ?>
                                                                <option selected value="<?php echo e($r); ?>"><?php echo e($r); ?> - Venue default</option>
                                                            <?php else: ?>
                                                                <option value="<?php echo e($r); ?>"><?php echo e($r); ?> - WARNING: Not venue default</option>
                                                            <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-3">On sale</label>
                                                    <div class="col-md-9">
                                                        <div id="on_sale_date" class="input-group date form_datetime dtpicker">
                                                            <input size="16" readonly="" class="form-control" type="text" name="on_sale" value="<?php echo e(date('Y-m-d H:i')); ?>">
                                                            <span class="input-group-btn">
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                                <button class="btn default" type="button" id="clear_onsale_date">
                                                                    <i class="fa fa-remove"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <label class="control-label col-md-3">Cutoff Hours</label>
                                                    <div class="col-md-9">
                                                        <input type="text" value="1" name="cutoff_hours" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Status</label>
                                                    <div class="col-md-9">
                                                        <input type="hidden" name="is_active" value="0"/>
                                                        <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> Social Media & Others </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Web Site
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="url" class="form-control" placeholder="https://www.myshow.com" /> 
                                                    </div> 
                                                    <label class="col-md-3 control-label">
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Get Media From Web Site</button>
                                                    </div> 
                                                    <label class="col-md-3 control-label">Youtube
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Facebook
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Twitter
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Google+
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="googleplus" class="form-control" placeholder="https://googleplus.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">YelpBadge
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="yelpbadge" class="form-control" placeholder="https://yelpbadge.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Instagram
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myshow" /> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Featured</label>
                                                    <div class="col-md-2">
                                                        <input type="hidden" name="is_featured" value="0"/>
                                                        <input type="checkbox" class="make-switch" name="is_featured" data-size="small" value="100" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                    <label class="control-label col-md-4">Able Print Ticket</label>
                                                    <div class="col-md-3">
                                                        <input type="hidden" name="printed_tickets" value="0"/>
                                                        <input type="checkbox" class="make-switch input-large" name="printed_tickets" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="control-label">
                                            <span class="required"> Descriptions </span>
                                        </label><hr>
                                        <div class="row">
                                            <label class="col-md-2 control-label">Short Descript.
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-10 show-error">
                                                <textarea name="short_description" class="form-control" rows="2"></textarea>
                                            </div> 
                                        </div>
                                        <div class="row">
                                            <label class="col-md-2 control-label">Description</label>
                                            <div class="col-md-10 show-error">
                                                <textarea name="description" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_sponsor">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Image
                                                    </label>
                                                    <div class="col-md-9 show-error" >
                                                        <center>
                                                            <input type="hidden" name="sponsor_logo_id"/>
                                                            <button type="button" id="btn_shows_upload_sponsor_logo_id" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                            <img name="sponsor_logo_id" alt="- No image -" src="" width="323px" height="270px" />
                                                        </center>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Sponsor
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="sponsor" class="form-control"/> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Presented by
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="presented_by" class="form-control"  /> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_reports">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label" style="padding-left:30px">Email for Individual Sales and Manifests Reports:
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="emails" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" /> 
                                                    </div>
                                                    <label class="control-label" style="padding-left:30px">Email for Daily Accounting: 
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="accounting_email" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" /> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="control-label col-md-9">Send individual order emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="individual_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="individual_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send manifest emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="manifest_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="manifest_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send daily sales emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="daily_sales_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="daily_sales_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send financial report emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="financial_report_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="financial_report_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_checking">
                                        <label class="control-label">
                                            <span class="required"> American Express Card Checking </span>
                                        </label><hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Date range:
                                                    </label>
                                                    <div class="input-group col-md-9" id="amex_only_date">
                                                        <input type="text" class="form-control" name="amex_only_start_date" readonly="true">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control" name="amex_only_end_date" readonly="true">
                                                        <span class="input-group-btn">
                                                            <button class="btn default date-range-toggle" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                            <button class="btn default" type="button" id="clear_amex_only_date">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-md-3 control-label">Ticket types:
                                                </label>
                                                <div class="col-md-9 mt-checkbox-inline ticket_types_lists">
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_passwords">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_password_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th> Password </th>
                                                        <th> Date Start </th>
                                                        <th> Date End </th>
                                                        <th> Ticket Types </th>
                                                        <th> </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_show_passwords">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_showtimes">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_show_time_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" id="btn_model_show_time_edit" class="btn sbold bg-yellow"> Toggle 
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" id="btn_model_show_time_delete" class="btn sbold bg-red"> Remove 
                                                <i class="fa fa-remove"></i>
                                            </button>
                                            <?php if(Auth::user()->user_type_id == 1): ?>
                                            <button type="button" id="btn_model_show_time_change" class="btn sbold bg-purple"> Change/Cancel 
                                                <i class="fa fa-recycle"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row portlet light portlet-fit calendar" style="padding:20px;">
                                            <div id="show_show_times" class="has-toolbar"> </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_tickets">
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_ticket_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th> Ticket Type </th>
                                                        <th> Package </th>
                                                        <th> Retail Price </th>
                                                        <th> P.Fee($) </th>
                                                        <th> P.Fee(%) </th>
                                                        <th> Com($) </th>
                                                        <th> Com(%) </th>
                                                        <th> Default? </th>
                                                        <th> Max </th>
                                                        <th> Status </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_show_tickets">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_bands" >
                                        <div class="btn-group">
                                            <button type="button" id="btn_model_band_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered" id="tb_sub_bands">
                                                <thead>
                                                    <tr>
                                                        <th> Order </th>
                                                        <th> Band </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_contracts">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_contract_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th> Date Uploaded </th>
                                                        <th> Effective Date </th>
                                                        <th> Status </th>
                                                        <th>  </th>
                                                        <th>  </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_show_contracts">
                                                </tbody>
                                            </table>
                                        </div>  
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_images">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_image_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_show_images" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>   
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_banners">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_banner_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_show_banners" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>   
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_videos">
                                        <div class="btn-group" style="padding-bottom:20px;">
                                            <button type="button" id="btn_model_video_add" class="btn sbold bg-green"> Add 
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row" style="max-height:600px !important;overflow-y: auto;">
                                            <div id="grid_show_videos" class="cbp" style="min-height: 2000px; width:950px !important;"></div>
                                        </div>   
                                    </div><!--
                                    <div class="tab-pane" id="tab_model_update_reviews">
                                        <h1>Not Implemented!</h1>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_awards">
                                        <h1>Not Implemented!</h1>
                                    </div>-->
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
    <!-- BEGIN SEARCH MODAL--> 
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:400px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Search Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/shows" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="venue" class="col-md-5"> <span>Venue:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="venue" style="width:200px !important">
                                        <option selected value="">All</option>
                                        <?php $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option <?php if($v->id==$venue): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="showtime" class="col-md-5"> <span>Show Time:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="showtime" style="width:100px !important">
                                        <option <?php if($showtime=='A'): ?> selected <?php endif; ?> value="A">All</option>
                                        <option <?php if($showtime=='P'): ?> selected <?php endif; ?> value="P">Passed</option>
                                        <option <?php if($showtime=='U'): ?> selected <?php endif; ?> value="U">Upcoming</option>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="status" class="col-md-5"> <span>Status:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="status" style="width:90px !important">
                                        <option <?php if($status==1): ?> selected <?php endif; ?> value="1">Active</option>
                                        <option <?php if($status==0): ?> selected <?php endif; ?> value="0">Inactive</option>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="onlyerrors" class="col-md-5"> <span>Only With Error:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="onlyerrors" style="width:65px !important">
                                        <option <?php if($onlyerrors==0): ?> selected <?php endif; ?> value="0">No</option>
                                        <option <?php if($onlyerrors==1): ?> selected <?php endif; ?> value="1">Yes</option>
                                    </select>
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
    <!-- BEGIN ADD/EDIT PASSWORD MODAL--> 
    <div id="modal_model_show_passwords" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Password</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_passwords">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Password
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" name="password" class="form-control" required="true" /> 
                                    </div>
                                    <label class="control-label col-md-3">Date range:
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="show_passwords_date">
                                            <input type="text" class="form-control" name="start_date" readonly="true" required="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="end_date" readonly="true" required="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <label class="col-md-3 control-label">Ticket types
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 ticket_types_lists">
                                    </div> 
                                </div>  
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_passwords').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_passwords" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT PASSWORD MODAL--> 
    <!-- BEGIN ADD/EDIT TICKET MODAL--> 
    <div id="modal_model_show_tickets" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Ticket</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_tickets">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-5">Ticket Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <select class="form-control" name="ticket_type">
                                            <?php $__currentLoopData = $ticket_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$tt): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($index); ?>"><?php echo e($tt); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-5">Package
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <select class="form-control" name="package_id">
                                            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$p): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($p->id); ?>"><?php echo e($p->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                    <label class="col-md-5 control-label">Retail Price
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="retail_price" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Proccessing Fee ($)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="processing_fee" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Proccessing Fee (%)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="percent_pf" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Commission ($)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="fixed_commission" width="100px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Commission (%)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="percent_commission" width="100px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="control-label col-md-5">Make default ticket
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7">
                                        <input type="hidden" name="is_default" value="0"/>
                                        <input type="checkbox" class="make-switch" name="is_default" data-size="small" value="1" data-on-text="Default" data-off-text="Not Default" data-on-color="primary" data-off-color="danger">
                                    </div>
                                    <label class="control-label col-md-5">Status
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7">
                                        <input type="hidden" name="is_active" value="0"/>
                                        <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                    </div>
                                    <label class="col-md-5 control-label">Limit (0 unlimited)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="max_tickets" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                </div>  
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_tickets').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_tickets" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT TICKET MODAL--> 
    <!-- BEGIN ADD/EDIT BAND MODAL--> 
    <div id="modal_model_show_bands" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Band</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_bands">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="show_id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-5">Band
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <select class="form-control" name="band_id">
                                            <?php $__currentLoopData = $bands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$b): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($b->id); ?>"><?php echo e($b->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_tickets').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_bands" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT BAND MODAL--> 
    <!-- BEGIN TOGGLE SHOWTIMES MODAL--> 
    <div id="modal_model_show_times_toggle" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Show Time</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_times_toggle">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">                                     
                                    <center><div class="show-error link_model_show_times_toggle"></div></center><hr>
                                </div>                                 
                                <div class="form-group">                                     
                                    <label class="control-label col-md-3">Status</label>                                     
                                    <div class="col-md-9 show-error">                                         
                                        <input type="hidden" name="is_active" value="0"/>                                         
                                        <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">                                     
                                    </div>                                 
                                </div>                                 
                                <div class="form-group">                                     
                                    <label class="col-md-3 control-label">Tickets to inactive for this event</label>                                     
                                    <div class="col-md-9 ticket_types_lists">                                     
                                    </div>                                 
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_times_toggle').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_times_toggle" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END TOGGLE SHOWTIMES MODAL--> 
    <!-- BEGIN ADD/REMOVE SHOWTIMES MODAL--> 
    <div id="modal_model_show_times" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Show Time</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_times">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span class="required"> Search Showtimes </span>
                                    </label><hr>
                                    <div class="form-group">    
                                        <label class="control-label col-md-3">Week Days
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="1" />Mon<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="2" />Tue<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="3" />Wed<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="4" />Thu<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="5" />Fri<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="6" />Sat<span></span></label>
                                            <label class="mt-checkbox"><input type="checkbox" checked="true" name="days[]" value="0" />Sun<span></span></label>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Date range
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <div class="input-group" id="show_times_date">
                                                <input type="text" class="form-control" name="start_date" value="<?php echo e(date('Y-m-d')); ?>" readonly="true">
                                                <span class="input-group-addon"> to </span>
                                                <input type="text" class="form-control" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" readonly="true">
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
                                        <label class="control-label col-md-3">Time</label>
                                        <div class="col-md-5 show-error">
                                            <div class="input-group">
                                                <input type="text" id="show_times_time" name="time" value="00:00" class="form-control" readonly="true" />
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button" id="show_times_time_toggle">
                                                        <i class="fa fa-clock-o"></i>
                                                    </button>
                                                    <button class="btn default" type="button" id="clear_show_times_time">
                                                    <i class="fa fa-remove"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 show-error">
                                            <input type="text" name="time_alternative" value="" class="form-control" placeholder="Alternative" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-block" type="button" id="available_show_times"> Search
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="subform_show_times"><br>
                                        <label class="control-label">
                                            <span class="required"> Update Available Showtimes </span>
                                        </label><hr>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Status
                                            </label>
                                            <div class="col-md-9 show-error">
                                                <input type="hidden" name="is_active" value="0"/>
                                                <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Tickets to inactive for this event
                                            </label>
                                            <div class="col-md-9 ticket_types_lists">
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-md-6 table-responsive" style="padding:20px;max-height:600px;overflow-y: auto;">
                                    <table class="table table-striped table-hover table-bordered" >
                                        <thead>
                                            <tr>
                                                <th> Week Day </th>
                                                <th> Date </th>
                                                <th> Time </th>
                                                <th> Avail. </th>
                                                <th> </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_show_times">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_times').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_times" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE SHOWTIMES MODAL--> 
    <!-- BEGIN CANCEL SHOWTIMES MODAL--> 
    <div id="modal_model_show_times_change" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Change/Cancel Show Time</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_times_change">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show Time
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="show_time_id">
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Action
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="action">
                                            <option value="change">Change Show Time Date</option>
                                            <option value="cancel">Move dependences and cancel event</option>
                                        </select>
                                    </div> 
                                </div><br><br><br><hr>
                                <div class="form-group" id="subform_show_time_change">
                                    <label class="control-label col-md-3">New Date
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9">
                                        <div id="show_time_to" class="input-group date form_datetime dtpicker">
                                            <input size="16" readonly="" class="form-control" type="text" name="show_time_to" value="<?php echo e(date('Y-m-d H:i')); ?>">
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_show_time_cancel">
                                    <label class="control-label col-md-3">Move to
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="show_time_id_to">
                                        </select>
                                    </div>
                                    <label class="control-label col-md-3">Email</label>
                                    <div class="col-md-9 show-error">
                                        <input type="hidden" name="send_email" value="0"/>
                                        <input type="checkbox" class="make-switch block" name="send_email" data-size="small" value="1" data-on-text="Email clients" data-off-text="Don't email clients" data-on-color="primary" data-off-color="danger">
                                    </div>
                                    <label class="control-label col-md-3">Next status</label>
                                    <div class="col-md-9 show-error">
                                        <input type="hidden" name="status" value="0"/>
                                        <input type="checkbox" class="make-switch block" name="status" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                    </div><br><br><br><hr>
                                    <label class="control-label col-md-3">Dependences</label>
                                    <div class="col-md-9 table-responsive" style="max-height:300px;overflow-y: auto;">
                                        <table class="table table-striped table-hover table-bordered" >
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>ID</th>
                                                    <th>Created</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb_show_times_dependences">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_times_change').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_times_change" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END CANCEL SHOWTIMES MODAL--> 
    <!-- BEGIN ADD/EDIT CONTRACTS MODAL--> 
    <div id="modal_model_show_contracts" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add Contracts</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_contracts">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" class="not_included"/>
                        <input type="hidden" name="show_id" value="" class="not_included"/>
                        <input type="hidden" name="id" value="" class="not_included"/>
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group col-md-5">
                                    <label class="control-label col-md-5">Effective Date
                                            <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <div id="show_contracts_effective_date" class="input-group date date-picker">
                                            <input readonly class="form-control not_included" type="text" name="effective_date" value="<?php echo e(date('Y-m-d')); ?>">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>                          
                                    </div>
                                    <label class="control-label col-md-5">Contract file
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <span class="btn btn-block green fileinput-button">Add <i class="fa fa-plus"></i>
                                            <input type="file" name="file" accept="application/pdf" class="not_included"> 
                                        </span>
                                    </div> <hr>
                                    <label class="control-label">
                                        <span class="required"> Select tickets to modify them automaticly </span>
                                    </label>
                                    <div class="form-group" id="subform_show_contracts">
                                        <select class="form-control" name="ticket_id">
                                        </select><hr>
                                        <label class="control-label col-md-5">Ticket Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <select class="form-control" name="ticket_type">
                                                <?php $__currentLoopData = $ticket_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$tt): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                    <option value="<?php echo e($index); ?>"><?php echo e($tt); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                            </select>
                                        </div>
                                        <label class="control-label col-md-5">Package
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <select class="form-control" name="package_id">
                                                <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$p): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->title); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                            </select>
                                        </div>
                                        <label class="col-md-5 control-label">Retail Price
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="0" name="retail_price" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div> 
                                        <label class="col-md-5 control-label">Proc.Fee ($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="0" name="processing_fee" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div> 
                                        <label class="col-md-5 control-label">Proc.Fee (%)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="0" name="percent_pf" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div> 
                                        <label class="col-md-5 control-label">Commission ($)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="" name="fixed_commission" width="100px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div>
                                        <label class="col-md-5 control-label">Commission (%)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="0" name="percent_commission" width="100px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div> 
                                        <label class="control-label col-md-5">Make default 
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7">
                                            <input type="hidden" name="is_default" value="0"/>
                                            <input type="checkbox" class="make-switch" name="is_default" data-size="small" value="1" data-on-text="Default" data-off-text="Not Default" data-on-color="primary" data-off-color="danger">
                                        </div>
                                        <label class="control-label col-md-5">Status
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7">
                                            <input type="hidden" name="is_active" value="0"/>
                                            <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                        </div>
                                        <label class="col-md-5 control-label">Limit (0 = &#8734;)
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7 show-error">
                                            <input type="text" value="0" name="max_tickets" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                        </div> 
                                    </div>  
                                    <div class="form-group show-error">
                                        <button type="button" id="btn_show_contracts_ticket_add" disabled="true" class="btn btn-block sbold grey-salsa">Add cron job for this ticket <i class="fa fa-plus"></i></button> 
                                    </div> 
                                </div>
                                <div class="form-group col-md-7" style="max-height:500px;overflow-y: auto;">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th> JSON Info </th>
                                                <th> </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_show_contracts_tickets">
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_contracts').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_contracts" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT CONTRACTS MODAL--> 
    <!-- BEGIN ADD/REMOVE SHOWIMAGES MODAL--> 
    <div id="modal_model_show_images" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Image</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_images">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="image_type">
                                            <?php $__currentLoopData = $image_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$it): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($index); ?>"><?php echo e($it); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Caption
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" class="form-control" name="caption" value=""/>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_show_images">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error" >
                                        <center>
                                            <input type="hidden" name="url"/>
                                            <button type="button" id="btn_shows_upload_images" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="url" alt="- No image -" src="" width="323px" height="270px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_show_images" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE SHOWIMAGES MODAL--> 
    <!-- BEGIN ADD/REMOVE SHOWBANNERS MODAL--> 
    <div id="modal_model_show_banners" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Banner</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_banners">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="parent_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Showed on
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                    <?php $__currentLoopData = $banner_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$bt): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <label class="mt-checkbox"><input type="checkbox" name="type[]" value="<?php echo e($index); ?>"/><?php echo e($bt); ?><span></span></label><br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Link to
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" class="form-control" name="url" value=""/>
                                    </div>
                                </div>
                                <div class="form-group" id="subform_show_banners">
                                    <label class="control-label col-md-3">Image
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error" >
                                        <center>
                                            <input type="hidden" name="file"/>
                                            <button type="button" id="btn_shows_upload_banner" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                            <img name="file" alt="- No image -" src="" width="323px" height="270px" />
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_show_banners" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE SHOWBANNERS MODAL--> 
    <!-- BEGIN ADD/REMOVE SHOWVIDEOS MODAL--> 
    <div id="modal_model_show_videos" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Video</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_videos">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="action" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <select class="form-control" name="video_type">
                                            <?php $__currentLoopData = $video_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$vt): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($index); ?>"><?php echo e($vt); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Embed
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <textarea name="embed_code" class="form-control" rows="4"></textarea>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Description</label>
                                    <div class="col-md-9 show-error">
                                        <textarea name="description" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="submit_model_show_videos" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/REMOVE SHOWBANNERS MODAL--> 
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?> 
<script src="/themes/admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
<script src="/js/admin/shows/index.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>