<!-- BEGIN ADD/EDIT ALBUMS MODAL-->
<div id="modal_model_restaurant_albums" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:500px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Album</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_albums" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <input type="hidden" name="restaurants_id" value="" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="action" value="1" />
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label col-md-3">Title
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="title" placeholder="Album name">
                                </div>
                                <label class="control-label col-md-3">Posted
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <div id="posted_albums" class="input-group date form_datetime dtpicker">
                                        <input size="16" readonly="" class="form-control" type="text" name="posted" value="{{date('Y-m-d H:i'),strtotime('now')}}">
                                        <span class="input-group-btn">
                                            <button class="btn default date-set" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <label class="control-label col-md-3">Enabled
                                </label>
                                <div class="col-md-8">
                                    <input type="hidden" name="enabled" value="0"/>
                                    <input type="checkbox" class="make-switch" name="enabled" data-size="small" value="1" data-on-text="Yes" data-off-text="No" data-on-color="primary" data-off-color="danger">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_albums').trigger('reset')">Cancel</button>
                                <button type="button" id="submit_model_restaurant_albums" class="btn sbold grey-salsa">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END ADD/EDIT ALBUMS MODAL-->
<!-- BEGIN ADD/VIEW GALLERY MODAL-->
<div id="modal_model_restaurant_albums_images" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:1000px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Album Images</center></h4>
            </div>
            <div class="modal-body">
                <div class="btn-group">
                    <button type="button" id="btn_model_album_images_add" class="btn sbold bg-green"> Add
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div class="row" style="max-height:600px !important;overflow-y: auto;">
                    <div class="portfolio-content body_grid color-panel text-center ">
                        <div id="albumImages" class="cbp text-center" style="min-height: 2000px; width:950px !important;"></div>
                    </div>
                </div>
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_albums_images" class="hidden">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <input type="hidden" name="restaurant_albums_id" value="" />
                    <input type="hidden" name="action" value="1" />
                    <input type="hidden" name="url" value=""/>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- END ADD/VIEW GALLERY MODAL-->