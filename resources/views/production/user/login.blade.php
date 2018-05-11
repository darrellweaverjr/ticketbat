<!-- BEGIN LOGIN MODAL -->
<div id="modal_login" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Login</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_login" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your username (email).">
                            <label class="col-md-3 control-label">Email
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" name="username" class="form-control" placeholder="mail@server.com" required="true" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your password.">
                            <label class="col-md-3 control-label">Password
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" required="true" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <label class="control-label">
                                <span class="required">
                                    <a id="switch_recover_password">Forgot password?</a>
                                </span>
                            </label><br> 
                            <label class="control-label">
                                <span class="required">
                                    <a id="switch_register">Create a new account</a>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline" title="Close form.">Close</button>
                                <button type="button" id="btn_login" class="btn bg-green btn-outline" title="Log in to see your options in our website.">Log in</button>
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