<!-- BEGIN REGISTER MODAL -->
<div id="modal_register" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Register</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_register" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your email.">
                            <label class="col-md-3 control-label">Email
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control" placeholder="mail@server.com">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your first name.">
                            <label class="col-md-3 control-label">First name
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user-plus"></i>
                                    </span>
                                    <input type="text" name="first_name" class="form-control" placeholder="John">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your last name.">
                            <label class="col-md-3 control-label">Last name
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user-plus"></i>
                                    </span>
                                    <input type="text" name="last_name" class="form-control" placeholder="Doe">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your 10 digits phone number.">
                            <label class="col-md-3 control-label">Phone
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    <input type="number" name="phone" class="form-control" placeholder="7025558888" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 ">
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
                                    <input type="password" id="register_password" name="password" class="form-control" placeholder="********">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Again your password.">
                            <label class="col-md-3 control-label">Password again
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" name="password2" class="form-control" placeholder="********">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your address.">
                            <label class="col-md-3 control-label">Address
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-location-arrow"></i>
                                    </span>
                                    <input type="text" name="address" class="form-control" placeholder="000 Main St.">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your city.">
                            <label class="col-md-3 control-label">City
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-location-arrow"></i>
                                    </span>
                                    <input type="text" name="city" class="form-control" placeholder="Las Vegas">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Select your country.">
                            <label class="col-md-3 control-label">Country
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-globe"></i>
                                    </span>
                                    <select class="form-control" name="country">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Select your state/region.">
                            <label class="col-md-3 control-label">State / Region
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-globe"></i>
                                    </span>
                                    <select class="form-control" name="state">
                                    </select>
                                </div>
                            </div>
                        </div>                        
                        <div class="form-group" title="Write your zip code.">
                            <label class="col-md-3 control-label">Zip code
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-globe"></i>
                                    </span>
                                    <input type="text" name="zip" class="form-control" placeholder="00000">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                <button type="button" id="btn_register" class="btn bg-green btn-outline" title="Create a new account with us.">Register</button>
            </div>
        </div>
    </div>
</div>
<!-- END REGISTER MODAL -->