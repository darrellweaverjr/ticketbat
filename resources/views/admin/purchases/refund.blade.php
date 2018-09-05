<!-- BEGIN REFUND MODAL-->
<div id="modal_model_refund" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-purple">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Refund Purchase</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_refund" class="form-horizontal">
                    <input name="id" type="hidden" value=""/>
                    <div class="form-body" style="padding:10px">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="row">
                            <div class="mt-radio-list" >
                                <label class="mt-radio mt-radio-single mt-radio-outline" style="color:blue;">
                                    <input type="radio" name="type" value="current_purchase" checked="true" />Refund through bank account.
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-single mt-radio-outline" style="color:red;">
                                    <input type="radio" name="type" value="update_purchase" />Select this option only if you want to update the status in the system to <b>REFUNDED</b> and already did a manual refund.
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-single mt-radio-outline" style="color:red;">
                                    <input type="radio" name="type" value="charge_purchase" />Select this option only if you want to update the status in the system to <b>CHARGEBACK</b> and bank already confirmed the operation.
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row" id="refund_details">
                            <table class="table table-responsive table-condensed table-both-scroll" style="font-size:8px!important">
                                <thead>
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="10%">Qty</th>
                                        <th width="10%">Ret.$</th>
                                        <th width="10%">Savings</th>
                                        <th width="10%">Fees</th>
                                        <th width="10%">Print.Fee</th>
                                        <th width="10%">Taxes</th>
                                        <th width="10%">Comm</th>
                                        <th width="10%">Amount</th>
                                        <th width="10%">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_body_pendings" class="text-right">
                                </tbody>
                                <tfoot>
                                    <tr id="tb_foot_pendings" class="warning">
                                        <td>
                                            <input type="hidden" name="ticket_price" value="0"/>
                                            <input type="hidden" name="ticket_fee" value="0"/>
                                            <input type="hidden" name="sales_percent" value="0"/>
                                        </td>
                                        <td><input type="number" class="form-control" name="quantity" value="0" min="0" step="1" style="width:60px"/></td>
                                        <td><input type="number" class="form-control" name="retail_price" value="0.00" min="0" step="0.01" style="width:80px"/></td>
                                        <td><input type="number" class="form-control" name="savings" value="0.00" min="0" step="0.01" style="width:75px"/></td>
                                        <td><input type="number" class="form-control" name="processing_fee" value="0.00" min="0" step="0.01" style="width:75px"/></td>
                                        <td><input type="number" class="form-control" name="printed_fee" value="0.00" min="0" step="0.01" style="width:75px"/></td>
                                        <td><input type="number" class="form-control" name="sales_taxes" value="0.00" min="0" step="0.01" style="width:75px"/></td>
                                        <td><input type="number" class="form-control" name="commission_percent" value="0.00" min="0" step="0.01" style="width:75px"/></td>
                                        <td colspan="2"><input type="text" name="amount" class="form-control" value="0.00" min="0" step="0.01" readonly="true" style="max-width:130px"/>Amount to be refunded</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row" id="purchase_details">
                        </div>
                        <div class="row">
                            <label class="control-label">Description:</label>
                            <div class="show-error">
                                <textarea name="description" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                <button type="button" id="btn_model_process" class="btn sbold bg-purple">Process</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END REFUND MODAL-->