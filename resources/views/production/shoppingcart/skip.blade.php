<div class="row">
    <!-- BEGIN FORM-->
    <form method="post" id="form_skip" class="form-horizontal">
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            You have some form errors. Please check below.
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="method" value="skip">
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
