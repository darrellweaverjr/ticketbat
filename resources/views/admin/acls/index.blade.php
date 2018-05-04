@php $page_title='ACLs' @endphp
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
                <a href="#tab_permissions" data-toggle="tab">
                    <h1 class="page-title"> Permissions<br>
                        <small> - List, add, edit and remove Permissions.</small>
                    </h1>
                </a>
            </li>
            <li>
                <a href="#tab_commands" data-toggle="tab">
                    <h1 class="page-title"> Commands<br>
                        <small> - List, add, edit and remove Commands.</small>
                    </h1>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="tab_permissions">
                @includeIf('admin.acls.permissions', ['permissions'=>$permissions,'user_types'=>$user_types,'permission_types'=>$permission_types,'permission_scopes'=>$permission_scopes])
            </div>

            <div class="tab-pane fade" id="tab_commands">
                @includeIf('admin.acls.commands', ['commands' => $commands])
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script src="/js/admin/acls/permissions.js" type="text/javascript"></script>
<script src="/js/admin/acls/commands.js" type="text/javascript"></script>
@endsection
