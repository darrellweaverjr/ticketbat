<!-- BEGIN ADD/EDIT ITEMS MODAL-->
<div id="modal_model_restaurant_items" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:500px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Item</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_items" class="form-horizontal">
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
                                <label class="control-label col-md-3">Menu
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <select class="form-control" name="restaurant_menu_id">
                                        @foreach($menu as $index=>$m)
                                            <option value="{{$m->id}}" @if($m->disabled>0) disabled @endif>{{$m->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-md-3">Position
                                </label>
                                <div class="col-md-8 show-error">
                                    <select class="form-control" name="order">
                                    </select>
                                </div>
                                <label class="control-label col-md-3">Name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="name" placeholder="Item name">
                                </div>
                                <label class="control-label col-md-3">Notes
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="notes" placeholder="Notes for this item">
                                </div>
                                <label class="col-md-3 control-label">Price ($)
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="number" value="0.00" name="price" step="0.01" min="0.01" class="form-control" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 || event.charCode == 46">
                                </div>
                                <label class="control-label col-md-3">Enabled
                                </label>
                                <div class="col-md-8">
                                    <input type="hidden" name="enabled" value="0"/>
                                    <input type="checkbox" class="make-switch" name="enabled" data-size="small" value="1" data-on-text="Yes" data-off-text="No" data-on-color="primary" data-off-color="danger">
                                </div>
                                <label class="col-md-3 control-label">Description
                                </label>
                                <div class="col-md-8 show-error">
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <label class="control-label col-md-3">Image
                                </label>
                                <div class="col-md-8 show-error" >
                                    <center>
                                        <input type="hidden" name="image_id"/>
                                        <button type="button" id="btn_restaurant_item_upload_image" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                        <img name="image_id" alt="- No image -" src="" width="200px" height="200px" />
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_items').trigger('reset')">Cancel</button>
                                <button type="button" id="submit_model_restaurant_items" class="btn sbold grey-salsa">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END ADD/EDIT ITEMS MODAL-->