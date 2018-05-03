<!-- BEGIN CONTACT US MODAL -->
<div id="modal_contact_us" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Contact Us</h3>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_contact_us" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your full name in this field.">
                            <label class="col-md-3 control-label">Name
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" name="name" @if(Auth::check()) value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}" @endif class="form-control" placeholder="John Doe">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your email here to contact you.">
                            <label class="col-md-3 control-label">Email
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" @if(Auth::check()) value="{{Auth::user()->email}}" @endif class="form-control" placeholder="user@server.com">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write here your 10 digits phone number.">
                            <label class="col-md-3 control-label">Phone</label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    <input type="number" name="phone" @if(Auth::check()) value="{{preg_replace('/[^0-9]/s','',Auth::user()->phone)}}" @endif size="10" class="form-control" placeholder="7024445555" >
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Let us know the event name.">
                            <label class="col-md-3 control-label">Event Name</label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    <input type="text" name="event" class="form-control" placeholder="myEvent">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Select the date/time for that event.">
                            <label class="col-md-3 control-label">Event Time</label>
                            <div class="col-md-8 show-error">
                                <div class="input-group date form_datetime">
                                    <span class="input-group-btn">
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                    <input type="text" name="date" size="16" readonly class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write here the message and we will contact you.">
                            <label class="col-md-3 control-label">Message
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-8 show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-text-width"></i>
                                    </span>
                                    <textarea name="message" rows="4" class="form-control" placeholder="Write your message here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline" title="Close form.">Close</button>
                                <button type="button" id="btn_contact_send" class="btn bg-green btn-outline" title="Send us the message.">Send</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END CONTACT US MODAL -->