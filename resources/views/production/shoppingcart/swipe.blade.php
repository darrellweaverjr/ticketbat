<div class="row">
    <!-- BEGIN FORM-->
    <form method="post" id="form_swipe" class="form-horizontal">
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            You have some form errors. Please check below.
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="method" value="swipe">
        <input type="hidden" name="newsletter" value="1">
        <div class="share_tickets_subform hidden"></div>
        <div class="form-group">
            <label class="control-label col-sm-3 text-right">Customer:
                <i class="required"> required</i>
            </label>
            <div class="col-sm-8 show-error">
                <input type="text" class="form-control" placeholder="Write your full name" name="customer" autocomplete="on" readOnly="true">
            </div>
        </div>
        @if($cart['seller']==0)
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
        <div class="hidden">
            <input type="hidden" name="card" value="">
            <input type="hidden" name="month" value="0">
            <input type="hidden" name="year" value="0">
            <input type="hidden" name="UMcardpresent" value=true>
            <input type="hidden" name="UMmagstripe" value="">
            <input type="hidden" name="UMdukpt" value="">
            <input type="hidden" name="UMtermtype" value="POS">
            <input type="hidden" name="UMmagsupport" value="yes">
            <input type="hidden" name="UMcontactless" value="no">
            <input type="hidden" name="UMsignature" value="">
        </div>
    </form>
    <!-- END FORM-->
</div>

<!-- BEGIN SWIPE CARD MODAL -->
<div id="modal_swipe_card" class="modal fade" aria-hidden="true">
    <div class="modal-dialog" style="width:326px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Swipe the card now</h3>
            </div>
            <div class="modal-body text-center">
                <img src="{{config('app.theme')}}img/card/swipe-bg.jpg">
                <input name="stripe_card" class="form-control" type="password" value="" autofocus="true" />
            </div>
        </div>
    </div>
</div>
<!-- END SWIPE CARD MODAL -->
