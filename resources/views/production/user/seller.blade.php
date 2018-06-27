<!-- BEGIN SELLER OPEN MODAL -->
<div id="modal_seller_open" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Open Drawer</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_seller_open" class="form-horizontal">
                    <input name="user_id" type="hidden" @if(Auth::check()) value="{{Auth::user()->id}}" @endif/>
                    <input name="open_drawer" type="hidden" value="1"/>
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Enter the amount of cash to start.">
                            <label class="col-md-3 control-label">Start cash
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <input type="number" name="cash_in" class="form-control" placeholder="0.00" value="0.00" step="0.01" min="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                <button type="button" id="btn_seller_open" class="btn bg-green btn-outline" title="Open selling process for the shift.">Open</button>
            </div>
        </div>
    </div>
</div>
<!-- END SELLER OPEN MODAL -->
<!-- BEGIN CONTINUE MODAL -->
<div id="modal_seller_continue" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Continue/Close Drawer</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_seller_open" class="form-horizontal">
                    <input name="user_id" type="hidden" @if(Auth::check()) value="{{Auth::user()->id}}" @endif/>
                    <input name="open_drawer" type="hidden" value="2"/>
                    <div class="form-body text-center">
                        <h3>Your drawer is currently opened.<br><br>Do you want to continue with it or do you want to close it?</h3>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn bg-green btn-outline">Continue with current one</button>
                <button type="button" id="btn_seller_continue" class="btn bg-red btn-outline" title="Close selling process for the shift.">Close the drawer.</button>
            </div>
        </div>
    </div>
</div>
<!-- END SELLER CONTINUE MODAL -->
<!-- BEGIN CLOSE MODAL -->
<div id="modal_seller_close" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Close Drawer</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_seller_close" class="form-horizontal">
                    <input name="user_id" type="hidden" @if(Auth::check()) value="{{Auth::user()->id}}" @endif/>
                    <input name="open_drawer" type="hidden" value="0"/>
                    <div class="form-body text-center">
                        <h3>Are you sure to close your drawer for this shift?</h3>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                <button type="button" id="btn_seller_close" class="btn bg-red btn-outline" title="Close selling process for the shift.">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- END SELLER CLOSE MODAL -->