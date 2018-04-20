<div class="row">
    <!-- BEGIN FORM-->
    <form method="post" id="form_cash" class="form-horizontal">
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            You have some errors. Please check below.
        </div>
        <div class="form-group cash_breakdown" style="text-align:center;padding:0px 15px">
            <div class="cash_total col-sm-4 col-md-3">
                <label class="control-label col-sm-5 text-right bold">Total ($):</label>
                <div class="col-sm-7 show-error">
                    <input type="text" class="form-control input-lg text-right" style="color:blue;" value="{{sprintf("%.2f",$cart['total'])}}" name="pending" readOnly="true"
                           value="{{old('pending')}}">
                </div>

                <label class="control-label col-sm-5 text-right bold">Cash ($):</label>
                <div class="col-sm-7 show-error">
                    <input type="text" class="form-control input-lg text-right" value="0.00" name="cashed" value="{{old('change')}}" data-number="0">
                </div>

                <label class="control-label col-sm-5 text-right bold" id="label_total">Due ($):</label>
                <div class="col-sm-7 show-error">
                    <input type="text" class="form-control input-lg text-right" style="color:red;" value="-{{sprintf("%.2f",$cart['total'])}}" name="subtotal" readOnly="true"
                           value="{{old('subtotal')}}">
                </div>
            </div>
            <div class="cash_total col-sm-2 col-md-4">
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_1" value="1" type="button" class="btn btn-quick-cash btn-lg btn-block">+1.00</button>
                </div>
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_5" value="5" type="button" class="btn btn-quick-cash btn-lg btn-block">+5.00</button>
                </div>
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_10" value="10" type="button" class="btn btn-quick-cash btn-lg btn-block">+10.00</button>
                </div>
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_20" value="20" type="button" class="btn btn-quick-cash btn-lg btn-block">+20.00</button>
                </div>
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_50" value="50" type="button" class="btn btn-quick-cash btn-lg btn-block">+50.00</button>
                </div>
                <div class="col-md-6" style="padding-top:5px">
                    <button name="plus_100" value="100" type="button" class="btn btn-quick-cash btn-lg btn-block">+100.00</button>
                </div>
            </div>
            <div class="cash_input col-sm-6 col-md-5">
                <div class="col-sm-4">
                    <button name="cash_1" value="1" type="button" class="btn btn-info btn-lg btn-block">1</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_2" value="2" type="button" class="btn btn-info btn-lg btn-block">2</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_3" value="3" type="button" class="btn btn-info btn-lg btn-block">3</button>
                </div>

                <div class="col-sm-4">
                    <button name="cash_4" value="4" type="button" class="btn btn-info btn-lg btn-block">4</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_5" value="5" type="button" class="btn btn-info btn-lg btn-block">5</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_6" value="6" type="button" class="btn btn-info btn-lg btn-block">6</button>
                </div>

                <div class="col-sm-4">
                    <button name="cash_7" value="7" type="button" class="btn btn-info btn-lg btn-block">7</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_8" value="8" type="button" class="btn btn-info btn-lg btn-block">8</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_9" value="9" type="button" class="btn btn-info btn-lg btn-block">9</button>
                </div>

                <div class="col-sm-4">
                    <button name="cash_0" value="0" type="button" class="btn btn-info btn-lg btn-block">0</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_d" value="." type="button" class="btn btn-info btn-lg btn-block">.</button>
                </div>
                <div class="col-sm-4">
                    <button name="cash_x" value="" type="button" class="btn btn-danger btn-lg btn-block">X</button>
                </div>
            </div>
        </div>
        <hr>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="method" value="cash">
        <input type="hidden" name="newsletter" value="1">
        <div class="share_tickets_subform hidden"></div>
        @if($cart['seller']==0)
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Customer:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-8 show-error">
                <input type="text" class="form-control" placeholder="Write your full name" name="customer"
                       @if(Auth::check() && Auth::user()->user_type_id==7) value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}" @endif
                       value="{{old('customer')}}" autocomplete="on">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Phone:</label>
            <div class="col-sm-8 show-error">
                <input type="text" class="form-control" placeholder="### ### ####" name="phone" value="{{old('phone')}}" autocomplete="on">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Email (for receipt):
                <i class="required"> required</i>
            </label>
            <div class="col-sm-8 show-error">
                <input type="email" class="form-control" placeholder="mail@server.com" name="email" value="{{$cart['email']}}" autocomplete="on">
            </div>
        </div>
        @endif
    </form>
    <!-- END FORM-->
</div>
