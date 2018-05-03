<!-- BEGIN ADD/EDIT RESERVATIONS MODAL-->
<div id="modal_model_restaurant_reservations" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:500px !important;">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Reservations</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" id="form_model_restaurant_reservations" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <input type="hidden" name="restaurants_id" value="" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="action" value="1" />
                    <div class="form-body" style="padding: 0 10px">
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label col-md-3">Schedule
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8">
                                    <div id="schedule" class="input-group date form_datetime dtpicker">
                                        <input size="16" readonly="" class="form-control" type="text" name="schedule" value="{{date('Y-m-d H:i'),strtotime('today +22 hours')}}">
                                        <span class="input-group-btn">
                                            <button class="btn default date-set" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <label class="control-label col-md-3">People
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="number" class="form-control" value="1" min="1" max="10" name="people" placeholder="Qty guests">
                                </div>
                                <label class="control-label col-md-3">First name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="first_name" placeholder="John">
                                </div>
                                <label class="control-label col-md-3">Last name
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="last_name" placeholder="Doe">
                                </div>
                                <label class="control-label col-md-3">Phone
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="number" class="form-control" min="1000000000" max="9999999999" name="phone" placeholder="7024445555">
                                </div>
                                <label class="control-label col-md-3">Email
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="email" class="form-control" name="email" placeholder="mail@server.com">
                                </div>
                                <label class="control-label col-md-3">Occasion
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <select class="form-control" name="occasion">
                                        @foreach($reservation_occasions as $index=>$o)
                                            <option value="{{$index}}">{{$o}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-md-3">Special request
                                </label>
                                <div class="col-md-8 show-error">
                                    <textarea name="special_request" class="form-control" rows="3"></textarea>
                                </div>
                                <label class="control-label col-md-3">Newsletter
                                </label>
                                <div class="col-md-8">
                                    <input type="hidden" name="newsletter" value="0"/>
                                    <input type="checkbox" class="make-switch input-large" name="newsletter" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                </div>
                                <label class="control-label col-md-3">Status
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-8 show-error">
                                    <select class="form-control" name="status">
                                        @foreach($reservation_status as $index=>$s)
                                            <option value="{{$index}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-md-3">Created
                                </label>
                                <div class="col-md-8 show-error">
                                    <input type="text" class="form-control" name="created" disabled="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_restaurant_reservations').trigger('reset')">Cancel</button>
                                <button type="button" id="submit_model_restaurant_reservations" class="btn sbold grey-salsa">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END ADD/EDIT MENU MODAL-->