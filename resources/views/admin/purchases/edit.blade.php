<!-- BEGIN MOVE MODAL-->
<div id="modal_model_edit" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:1000px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-purple">
                <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title">Edit Purchase</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_edit">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <input type="hidden" name="purchase_id" value="" />
                    <div class="form-body">
                        @if(Auth::check() && in_array(Auth::user()->id,explode(',',env('ROOT_USER_ID'))))
                        <div class="row">
                            <label class="mt-checkbox">
                                <input type="checkbox" name="force_edit" value="1" />Force<span></span>
                            </label>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">
                                    <span class="required">Current Info</span>
                                </label><hr>
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span><b>Ticket:</b></span>
                                    </label>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Type:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="ticket_type" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Pkge:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="title" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Rt.Price:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="retail_price" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Fees($):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="processing_fee" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Fees(%):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="percent_pf" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Comm($):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="fixed_commission" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Comm(%):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="percent_commission" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Active:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="is_active" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Inclusive fee:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="inclusive_fee" type="number" readonly="true"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span><b>Purchase:</b></span>
                                    </label>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Qty:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="quantity" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Rt.Price:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="p_retail_price" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">P.Fees:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="p_processing_fee" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Saving:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="savings" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Commis.:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="commission_percent" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">P.Paid:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="price_paid" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">ShowTime:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" style="font-size:10px" name="show_time" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Coupon:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="code" type="text" readonly="true"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">
                                    <span class="required">Target Info</span>
                                </label><hr>
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span><b>Ticket:</b></span>
                                    </label>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Type:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_ticket_type" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Pkge:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_title" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Rt.Price:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_retail_price" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Fees($):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_processing_fee" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Fees(%):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_percent_pf" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Comm($):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_fixed_commission" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Comm(%):</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_percent_commission" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Active:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_is_active" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Inclusive fee:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_inclusive_fee" type="number" readonly="true"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">
                                        <span><b>Purchase:</b></span>
                                    </label>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Qty:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_quantity" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Rt.Price:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_p_retail_price" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">P.Fees:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_p_processing_fee" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Saving:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_savings" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Commis.:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_commission_percent" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">P.Paid:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_price_paid" type="number" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">ShowTime:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" style="font-size:10px" name="t_show_time" type="text" readonly="true"/>
                                        </div>
                                        <label class="control-label col-md-4">Coupon:</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="t_code" type="text" readonly="true"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><hr>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label">Move to other date/time:</label>
                                <select class="form-control" name="to_show_time_id">
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Change ticket type:</label>
                                <select class="form-control" name="to_ticket_id">
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Apply Coupon:</label>
                                <select class="form-control" name="to_discount_id">
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Change quantity of tickets:</label>
                                <input class="form-control" name="to_quantity" type="number" step="1" min="1" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label">Enter the email of the <b>USER</b> to change to:</label>
                                <input type="email" class="form-control" name="to_user_email">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Enter the email of the <b>CUSTOMER</b> to change to:</label>
                                <input type="email" class="form-control" name="to_customer_email">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                <button type="button" id="btn_model_save" class="btn sbold purple">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END MOVE MODAL-->