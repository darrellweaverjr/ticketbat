<!-- BEGIN EMAIL MODAL-->
<div id="modal_model_email" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-green">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Custom Email</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_email">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label col-md-2">Send to
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <label class="col-md-4 mt-radio mt-radio-single mt-radio-outline">Search Result
                                        <input type="radio" name="sendto" checked="true" value="0" />
                                        <span></span>
                                    </label>
                                    <label class="col-md-4 mt-radio mt-radio-single mt-radio-outline">Selected Purchases
                                        <input type="radio" name="sendto" value="1" />
                                        <span></span>
                                    </label>
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <label class="control-label col-md-2">Subject
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <input class="form-control" name="subject" type="text" required="true"/>
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <label class="control-label col-md-2">Message
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea class="form-control" name="body" rows="10" required="true"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2">Template
                                    <span><small>(optional)</small></span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <input class="form-control" name="template" type="text" required="true"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                <button type="button" id="btn_send_custom" class="btn sbold bg-green">Send</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END EMAIL MODAL-->