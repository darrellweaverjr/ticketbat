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
                        <div class="row">
                            <div class="col-md-3">
                                <label class="control-label">
                                    <span><b>Ticket current:</b></span>
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
                                    <label class="control-label col-md-4">Incl.Fee:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="inclusive_fee" type="number" readonly="true"/>
                                    </div>
                                </div><hr>
                                <div class="form-group">
                                    <label class="control-label required">Move to other date/time:</label>
                                    <select class="form-control" name="to_show_time_id">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label required">Change ticket type:</label>
                                    <select class="form-control" name="to_ticket_id">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label required">Apply Coupon:</label>
                                    <select class="form-control" name="to_discount_id">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">
                                    <span><b>Ticket target:</b></span>
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
                                    <label class="control-label col-md-4">Incl.Fee:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="t_inclusive_fee" type="number" readonly="true"/>
                                    </div>
                                </div><hr>
                                <div class="form-group">
                                    <label class="control-label required">Change quantity of tickets:</label>
                                    <input class="form-control" name="to_quantity" type="number" step="1" min="1"/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label required">Email of <b>CUSTOMER</b> to change to:</label>
                                    <input type="email" class="form-control" name="to_customer_email"/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label required">Email of <b>USER</b> to change to:</label>
                                    <input type="email" class="form-control" name="to_user_email"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">
                                    <span><b>Purchase current:</b></span>
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
                                    <label class="control-label col-md-4">Taxes:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="sales_taxes" type="number" readonly="true"/>
                                    </div>
                                    <label class="control-label col-md-4">CC.Fee:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="cc_fee" type="number" readonly="true"/>
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
                                    <label class="control-label col-md-4">Incl.Fee:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="p_inclusive_fee" type="number" readonly="true"/>
                                    </div>
                                    <label class="control-label col-md-4">Pyment:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="payment_type" type="text" readonly="true"/>
                                    </div>
                                    <label class="control-label col-md-4">Channel:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="channel" type="text" readonly="true"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">
                                    <span><b>Purchase target:
                                    @if(Auth::check() && in_array(Auth::user()->id,explode(',',env('ROOT_USER_ID'))))
                                        <input type="checkbox" name="force_edit" value="1" />
                                    @endif
                                    </b></span>
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
                                    <label class="control-label col-md-4">Taxes:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="t_sales_taxes" type="number" readonly="true"/>
                                    </div>
                                    <label class="control-label col-md-4">CC.Fee:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="t_cc_fee" type="number" readonly="true"/>
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
                                    <label class="control-label col-md-4">Incl.Fee:</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="t_inclusive_fee" readonly="true">
                                            <option selected="true" disabled="true" value=""></option>
                                            <option value="1">Inclusive</option>
                                            <option value="0">Over price</option>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-4">Pyment:</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="t_payment_type" readonly="true">
                                            <option selected="true" disabled="true" value=""></option>
                                            @foreach($search['payment_types'] as $index=>$c)
                                            <option value="{{$index}}">{{$c}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-4">Channel:</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="t_channel" readonly="true">
                                            <option selected="true" disabled="true" value=""></option>
                                            @foreach($search['channels'] as $index=>$c)
                                            <option value="{{$index}}">{{$c}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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