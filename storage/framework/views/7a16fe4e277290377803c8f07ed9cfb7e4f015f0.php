<?php  $page_title='Bands'  ?>

<?php $__env->startSection('title', 'Bands' ); ?>

<?php $__env->startSection('styles'); ?> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> <?php echo e($page_title); ?> 
        <small> - List, add, edit and remove bands.</small>
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
                                <th width="10%">Band</th>
                                <th width="78%">Description</th>
                                <th width="10%">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $bands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$b): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="<?php echo e($b->id); ?>" value="<?php echo e($b->name); ?>" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="10%" data-order="<?php echo e($b->name); ?>"> 
                                    <?php if(preg_match('/\/uploads\//',$b->image_url)): ?> <?php  $b->image_url = env('IMAGE_URL_OLDTB_SERVER').$b->image_url  ?> <?php endif; ?>
                                    <?php if(preg_match('/\/s3\//',$b->image_url)): ?> <?php  $b->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$b->image_url)  ?> <?php endif; ?>
                                    <center style="color:red;"><i><b><a data-toggle="modal" href="#modal_details_<?php echo e($b->id); ?>"><img alt="- No image -" height="110px" width="110px" src="<?php echo e($b->image_url); ?>"/></a></b></i></center>
                                </td>
                                <td class="search-item clearfix" width="78%"> 
                                    <div class="search-content">
                                        <h3 class="search-title"><a data-toggle="modal" href="#modal_details_<?php echo e($b->id); ?>"><?php echo e($b->name); ?></a></h3>
                                        <p><small><i>
                                            <?php if($b->website): ?>Web Site: <a href="<?php echo e($b->website); ?>" target="_blank"><?php echo e($b->website); ?> </a><?php endif; ?>
                                            <?php if($b->youtube): ?>YouTube: <a href="<?php echo e($b->youtube); ?>" target="_blank"><?php echo e($b->youtube); ?> </a><?php endif; ?> 
                                            <?php if($b->facebook): ?>Facebook: <a href="<?php echo e($b->facebook); ?>" target="_blank"><?php echo e($b->facebook); ?> </a><?php endif; ?> 
                                            <?php if($b->twitter): ?>Twitter: <a href="<?php echo e($b->twitter); ?>" target="_blank"><?php echo e($b->twitter); ?> </a><?php endif; ?> 
                                            <?php if($b->my_space): ?>MySpace: <a href="<?php echo e($b->my_space); ?>" target="_blank"><?php echo e($b->my_space); ?> </a><?php endif; ?> 
                                            <?php if($b->flickr): ?>Flickr: <a href="<?php echo e($b->flickr); ?>" target="_blank"><?php echo e($b->flickr); ?> </a><?php endif; ?> 
                                            <?php if($b->instagram): ?>Instagram: <a href="<?php echo e($b->instagram); ?>" target="_blank"><?php echo e($b->instagram); ?> </a><?php endif; ?> 
                                            <?php if($b->soundcloud): ?>SoundCloud: <a href="<?php echo e($b->soundcloud); ?>" target="_blank"><?php echo e($b->soundcloud); ?> </a><?php endif; ?> 
                                        </i></small></p>
                                        <p> <?php if($b->short_description): ?> <?php echo e($b->short_description); ?> <?php else: ?> <i style="color:red"><b>- No short description -</b></i> <?php endif; ?> </p>
                                    </div>
                                </td>
                                <td width="10%"><center> <?php echo e($b->category); ?> </center></td>
                            </tr>
                            <!-- BEGIN DETAILS MODAL--> 
                            <div id="modal_details_<?php echo e($b->id); ?>" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog">
                                    <div class="modal-content portlet">
                                        <div id="modal_model_update_header" class="modal-header">
                                            <h4 class="modal-title bold uppercase"><center><?php echo e($b->name); ?></center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    <center style="color:red;"><i><b><img alt="- No image -" height="200px" width="220px" src="<?php echo e($b->image_url); ?>"/></b></i></center>
                                                </div>
                                                <div class="portlet-body">
                                                    <ul class="chats">
                                                        <li class="in">
                                                            <div class="avatar">Category</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> <?php echo e($b->category); ?> </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Social Media</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    <?php if($b->website): ?>Web Site: <a href="<?php echo e($b->website); ?>" target="_blank"><?php echo e($b->website); ?> </a><br><?php endif; ?>
                                                                    <?php if($b->youtube): ?>YouTube: <a href="<?php echo e($b->youtube); ?>" target="_blank"><?php echo e($b->youtube); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->facebook): ?>Facebook: <a href="<?php echo e($b->facebook); ?>" target="_blank"><?php echo e($b->facebook); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->twitter): ?>Twitter: <a href="<?php echo e($b->twitter); ?>" target="_blank"><?php echo e($b->twitter); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->my_space): ?>MySpace: <a href="<?php echo e($b->my_space); ?>" target="_blank"><?php echo e($b->my_space); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->flickr): ?>Flickr: <a href="<?php echo e($b->flickr); ?>" target="_blank"><?php echo e($b->flickr); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->instagram): ?>Instagram: <a href="<?php echo e($b->instagram); ?>" target="_blank"><?php echo e($b->instagram); ?> </a><br><?php endif; ?> 
                                                                    <?php if($b->soundcloud): ?>SoundCloud: <a href="<?php echo e($b->soundcloud); ?>" target="_blank"><?php echo e($b->soundcloud); ?> </a><br><?php endif; ?> 
                                                                    <?php if(!$b->website && !$b->youtube && !$b->facebook && !$b->twitter && !$b->my_space && !$b->flickr && !$b->instagram && !$b->soundcloud): ?> <i style="color:red"><b>- No social media links -</b></i> <?php endif; ?>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Short Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> <?php if($b->short_description): ?> <?php echo e($b->short_description); ?> <?php else: ?> <i style="color:red"><b>- No short description -</b></i> <?php endif; ?> </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Full Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> <?php if($b->description): ?> <?php echo e($b->description); ?> <?php else: ?> <i style="color:red"><b>- No description -</b></i> <?php endif; ?> </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Name
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="name" class="form-control" placeholder="My Band" /> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
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
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Image
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_bands_upload_image_url" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="323px" height="270px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Web Site
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="website" class="form-control" placeholder="https://www.myband.com" /> 
                                        </div> 
                                        <label class="col-md-3 control-label">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Get Media From Web Site</button>
                                        </div> 
                                        <label class="col-md-3 control-label">Youtube
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Facebook
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Twitter
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">My Space
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="my_space" class="form-control" placeholder="https://myspace.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Flickr
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="flickr" class="form-control" placeholder="https://flickr.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Instagram
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">SoundCloud
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="soundcloud" class="form-control" placeholder="https://soundcloud.com/myband" /> 
                                        </div>
                                    </div>
                                </div>
                            </div> 
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
<!--                            <div class="row">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="80%"> Show </th>
                                            <th width="20%"> Order </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="80%"> sdfdfdfdf </td>
                                            <td width="20%"> 1 </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>  -->
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
                    <form method="post" action="/admin/bands" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>" />
                        <div class="form-body">
                            <div class="row">  
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?> 
<script src="/js/admin/bands/index.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>