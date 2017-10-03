<!-- BEGIN LOGIN MODAL -->
<div id="modal_reset_password" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Reset password</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_reset_password" class="form-horizontal">
                    <div class="form-body">
                        @if(Auth::check() && Auth::user()->force_password_reset>0) 
                        <div class="alert alert-warning">
                            You are required to change your password.<br>If you do not change your password you will be logged out. 
                        </div>
                        @endif
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your new password.">
                            <label class="col-md-3 control-label">New password
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" id="reset_password" name="password" class="form-control" required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Type again your new password.">
                            <label class="col-md-3 control-label">New password (confirm)
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" name="password2" class="form-control" required="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" @if(Auth::check() && Auth::user()->force_password_reset>0) id="btn_reset_password_close" @endif data-dismiss="modal" class="btn dark btn-outline" title="Close form.">Close</button>
                                <button type="button" id="btn_reset_password" class="btn bg-green btn-outline" title="Reset password.">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END LOGIN MODAL -->