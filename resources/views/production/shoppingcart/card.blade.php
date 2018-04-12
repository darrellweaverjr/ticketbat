<div class="row">
    <div class="form-group text-center">
        <img id="icon-mc" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-mastercard.png">
        <img id="icon-vs" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-visa.png">
        <img id="icon-dc" class="@if($cart['amex_only']>0) hidden @endif" src="{{config('app.theme')}}img/card/cc-icon-discover.png">
        <img id="icon-ax" src="{{config('app.theme')}}img/card/cc-icon-american-express.png">
    </div>
    <!-- BEGIN FORM-->
    <form method="post" id="form_card" class="form-horizontal">
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            You have some form errors. Please check below.
        </div>
        <div class="alert alert-warning display-hide"></div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="method" value="card">
        <input type="hidden" name="newsletter" value="1">
        <div class="share_tickets_subform hidden"></div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Customer:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-8 show-error">
                <input type="text" class="form-control" placeholder="Write your full name" name="customer" value="{{old('customer')}}" autocomplete="on">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Card number:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error mb-15-mobile">
                <input type="number" class="form-control" placeholder="#### #### #### ####" name="card" data-amex="{{$cart['amex_only']}}" style="min-width:170px" autocomplete="on">
            </div>
            <label class="control-label col-sm-2 text-right">CVV:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error">
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="####" name="cvv" style="min-width:75px" autocomplete="off">
                    <span class="input-group-btn">
                    <a class="btn btn-info" data-toggle="modal" href="#modal_cvv"><i class="fa fa-question icon-question"></i> What is it?</a>
                </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Exp month:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error mb-15-mobile">
                <select class="form-control" name="month" placeholder="M" style="min-width:145px" value="{{old('month')}}" autocomplete="on">
                    <option value="" disabled="true" selected="true">- Select month -</option>
                    <option value="01">1 (January)</option>
                    <option value="02">2 (February)</option>
                    <option value="03">3 (March)</option>
                    <option value="04">4 (April)</option>
                    <option value="05">5 (May)</option>
                    <option value="06">6 (June)</option>
                    <option value="07">7 (July)</option>
                    <option value="08">8 (August)</option>
                    <option value="09">9 (September)</option>
                    <option value="10">10 (October)</option>
                    <option value="11">11 (November)</option>
                    <option value="12">12 (December)</option>
                </select>
            </div>
            <label class="control-label col-sm-2 text-right">Exp year:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error">
                <select class="form-control" name="year" placeholder="YYYY" style="min-width:135px" value="{{old('year')}}" autocomplete="on">
                    <option value="" disabled="true" selected="true">- Select year -</option>
                    @for ($y = date('Y'); $y <= date('Y')+20; $y++)
                        <option value="{{$y}}">{{$y}}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Address:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-8 show-error">
                <input type="text" class="form-control" placeholder="" name="address" value="{{old('address')}}" autocomplete="on">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Country:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error mb-15-mobile">
                <select class="form-control" name="country" placeholder="United States" style="min-width:135px" value="{{old('country')}}" autocomplete="on">
                    @foreach( $cart['countries'] as $c)
                        <option @if($c->code=='US') selected @endif value="{{$c->code}}">{{$c->name}}</option>
                    @endforeach
                </select>
            </div>
            <label class="control-label col-sm-2 text-right">State/region:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error">
                <select class="form-control" name="state" placeholder="Nevada" style="min-width:135px" value="{{old('state')}}" autocomplete="on">
                    <option value="" disabled="true" selected="true">- Select state/region -</option>
                    @foreach( $cart['regions'] as $r)
                        <option value="{{$r->code}}">{{$r->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">City:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error mb-15-mobile">
                <input type="text" class="form-control" placeholder="" name="city" value="{{old('city')}}" autocomplete="on">
            </div>
            <label class="control-label col-sm-2 text-right ">Zip:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-3 show-error">
                <input type="text" class="form-control" placeholder="#####" name="zip" style="min-width:75px" value="{{old('zip')}}" autocomplete="on">
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
    </form>
    <!-- END FORM-->
</div>
