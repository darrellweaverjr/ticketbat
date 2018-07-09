<!-- BEGIN SELLER OPEN MODAL -->
<div id="modal_seller_open" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_seller_open" class="btn btn-lg bg-green btn-outline pull-right" title="Open selling process for the shift.">Open</button>
                <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Cancel</button>
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
                                    <input type="number" name="cash_in" class="form-control input-lg" placeholder="0.00" value="0.00" step="0.01" min="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- END SELLER OPEN MODAL -->
<!-- BEGIN CONTINUE MODAL -->
<div id="modal_seller_continue" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Continue/Close Drawer</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_seller_continue" class="form-horizontal">
                    <input name="user_id" type="hidden" @if(Auth::check()) value="{{Auth::user()->id}}" @endif/>
                    <div class="form-body text-center">
                        <h3>Your drawer is currently opened.<br><br>Do you want to continue with it or do you want to close it?</h3><br>
                        <button type="button" id="btn_seller_continue" class="btn btn-lg btn-block bg-green btn-outline" title="Continue process for the shift.">Continue with current one</button><br>
                        <button type="button" id="btn_seller_closing" class="btn btn-lg btn-block bg-red btn-outline" title="Close selling process for the shift.">Close the drawer.</button>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- END SELLER CONTINUE MODAL -->
<!-- BEGIN CLOSE MODAL -->
<div id="modal_seller_close" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_seller_close" class="btn btn-lg bg-red btn-outline pull-right" title="Close selling process for the shift.">Close</button>
                <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Cancel</button>
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
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- END SELLER CLOSE MODAL -->
<!-- BEGIN TALLY MODAL -->
<div id="modal_seller_tally" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="width:90% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                <h3 class="modal-title">Tally (last 5)</h3>
            </div>
            <div class="modal-body">
                @if(Auth::check())
                <h3 class="text-center"><b>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</b><br>{{Auth::user()->email}}</h3>
                <div class="row portlet-body" style="padding:0 20px">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Time<br>In</th>
                                <th width="15%">Time<br>Out</th>
                                <th width="15%">Cash<br>In</th>
                                <th width="15%">Cash<br>Out</th>
                                <th width="10%">Trans.</th>
                                <th width="10%">Tickt</th>
                                <th width="15%">Total</th>
                            </tr>
                        </thead>
                        <tbody id="table_tally_body">
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- END TALLY MODAL -->
<!-- BEGIN SEND REPORT MODAL -->
<div id="modal_seller_report" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_seller_report" class="btn btn-lg bg-red btn-outline pull-right" title="Send reports.">Send</button>
                <button type="button" data-dismiss="modal" class="btn btn-lg dark btn-outline pull-right">Close</button>
                <h3 class="modal-title">Send report</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_seller_report" class="form-horizontal">
                    <input name="user_id" type="hidden" @if(Auth::check()) value="{{Auth::user()->id}}" @endif/>
                    <input name="action" type="hidden" value="1"/>
                    <div class="form-body">
                        <div class="row form-group" style="padding:0 20px">
                            <h3>Destination:</h3><br>
                            <div class="mt-radio-list col-md-12">
                                <label class="mt-radio"> To venue's distribution list.
                                    <input value="v" name="email" type="radio" checked="true">
                                    <span></span>
                                </label>
                                <label class="mt-radio"> To show's distribution list.
                                    <input value="s" name="email" type="radio" checked="false">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group" style="padding:0 20px">
                            <h3>Last events to create the report to:</h3><br>
                            <div class="mt-checkbox-inline" style="padding:0 20px" id="events_list">
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- END SEND REPORT MODAL -->