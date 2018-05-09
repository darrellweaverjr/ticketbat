@php $page_title='Refunds' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')

    <div class="row tabbable-line">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_pendings" data-toggle="tab">
                    <h1 class="page-title"> Pending to refund<br>
                        <small> - Refund purchases.</small>
                    </h1>
                </a>
            </li>
            <li>
                <a href="#tab_refunded" data-toggle="tab">
                    <h1 class="page-title"> Refunded<br>
                        <small> - List purchases refunded.</small>
                    </h1>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="tab_pendings">
                @includeIf('admin.refunds.pendings', ['purchases'=>$pendings])
            </div>

            <div class="tab-pane fade" id="tab_refunded">
                @includeIf('admin.refunds.refunded', ['refunds' => $refunds])
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script src="/js/admin/refunds/pendings.js" type="text/javascript"></script>
<script src="/js/admin/refunds/refunded.js" type="text/javascript"></script>
@endsection
