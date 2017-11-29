<!-- BEGIN LIST MENU MODAL-->
<div id="modal_model_restaurant_menu" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:800px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Restaurant Menu</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_menu" class="form-horizontal">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" name="restaurants_id" value="" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="action" value="1" />
                    <div class="form-body" style="padding: 0 10px">
                        <div class="row">
                            <div class="btn-group">
                                <button type="button" id="btn_model_menu_add" class="btn sbold bg-green"> Add
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="row table-responsive" style="padding:20px;max-height:400px;overflow-y: auto;">
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="40%"> Menu </th>
                                            <th width="40%"> Notes </th>
                                            <th width="10%"> Disabled </th>
                                            <th width="5%"> </th>
                                            <th width="5%"> </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_restaurant_menu">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_items').trigger('reset')">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END LIST MENU MODAL-->
<!-- BEGIN ADD/EDIT MENU MODAL-->
<div id="modal_model_restaurant_menu_add" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:500px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Menu Class</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_menu_add" class="form-horizontal">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="action" value="1" />
                    <div class="form-body" style="padding: 0 10px">
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label col-md-3">Name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="name" placeholder="Menu name">
                                </div>
                                <label class="control-label col-md-3">Notes
                                </label>
                                <div class="col-md-8 show-error">
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <label class="control-label col-md-3">Parent
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <select class="form-control" name="parent_id">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_menu_add').trigger('reset')">Cancel</button>
                                <button type="button" id="submit_model_restaurant_menu_add" class="btn sbold grey-salsa">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END ADD/EDIT MENU MODAL-->