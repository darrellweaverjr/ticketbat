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
<div class="page-content color-panel" style="min-height:600px;margin-top:50px">
    <div class="row fixed-panel">
        <div class="portlet light about-text">
            <!-- BEGIN DESCRIPTION -->
            <h4>
                <i class="fa fa-close icon-close"></i> Something went wrong!
            </h4>  
            <p class="margin-top-20" style="padding-bottom:20px">
                The system could not recover your session.<br>Your link has been expired!
            </p>
            <p class="margin-top-20" style="padding-bottom:20px">
                <a class="btn btn-danger" href="/production/home">Home</a> <a class="btn btn-danger" data-toggle="modal" href="#modal_contact_us">Contact Us</a>
            </p>
            <!-- END DESCRIPTION -->
        </div>
    </div>
</div>
<!-- END CONTENT -->

@section('scripts')
@endsection