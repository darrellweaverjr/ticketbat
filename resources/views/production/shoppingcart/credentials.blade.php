@php $page_title='Shopping cart' @endphp
@extends('layouts.production')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

<!-- BEGIN CONTENT -->
<div class="page-content color-panel" style="min-height:600px">  
</div>
<!-- END CONTENT -->

<!-- BEGIN LOGIN/GUEST MODAL -->
<div id="modal_login_guest" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" title="Login or continue as guest to proceed to the shopping cart">
        <div class="modal-content col-md-6">
            <div class="modal-header">
                <h3 class="modal-title">Login</h3>
            </div>
            <div class="modal-body">
                <form method="post" id="form_login_guest" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your username (email).">
                            <label class="control-label">Email
                                <span class="required"> * </span>
                            </label>
                            <div class="show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" name="username" class="form-control" placeholder="mail@server.com" required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" title="Write your password.">
                            <label class="control-label">Password
                                <span class="required"> * </span>
                            </label>
                            <div class="show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <label class="control-label">
                                <span class="required">
                                    <a id="switch_recover_password">Forgot password?</a>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" id="btn_login_guest" class="btn bg-green btn-outline btn-block" title="Log in to see your options in our website and the items in the shopping cart.">Log in</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-content col-md-6">
            <div class="modal-header">
                <h3 class="modal-title">Continue as guest</h3>
            </div>
            <div class="modal-body">
                <form method="post" id="form_guest_login" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="form-group" title="Write your username (email).">
                            <label class="control-label">Email
                                <span class="required"> * </span>
                            </label>
                            <div class="show-error">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" name="username" class="form-control" placeholder="mail@server.com" required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
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
                                <button type="button" id="btn_guest_login" class="btn bg-green btn-outline btn-block" title="Continue as guest to see your items in the shopping cart.">Continue as guest</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END LOGIN/GUEST MODAL -->

@section('scripts')
<script src="/js/production/shoppingcart/credentials.js" type="text/javascript"></script>
@endsection