<!-- BEGIN RESEND MODAL-->
<div id="modal_model_resend" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-green">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Resend Email to</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_resend">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <input type="hidden" name="purchase_id" value="" />
                    <div class="form-body">
                        <div class="row" style="padding:20px">
                            <input type="email" value="" name="email" class="form-control input-lg" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                <button type="button" id="btn_resend_email" class="btn sbold bg-green">Send</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END RESEND MODAL-->