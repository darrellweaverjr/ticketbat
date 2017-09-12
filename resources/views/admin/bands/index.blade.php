@php $page_title='Bands' @endphp
@extends('layouts.admin')
@section('title')
  {!! $page_title !!}
@stop
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}}
        <small> - List, add, edit and remove bands.</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} LIST </span>
                    </div>
                    <div class="actions">
                        <div class="btn-group">
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['BANDS']['permission_types']))
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search"> Search
                                <i class="fa fa-search"></i>
                            </button>
                            @endif
                            @if(in_array('Add',Auth::user()->user_type->getACLs()['BANDS']['permission_types']))
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true">Add
                                <i class="fa fa-plus"></i>
                            </button>
                            @endif
                            @if(in_array('Edit',Auth::user()->user_type->getACLs()['BANDS']['permission_types']))
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true">Edit
                                <i class="fa fa-edit"></i>
                            </button>
                            @endif
                            @if(in_array('Delete',Auth::user()->user_type->getACLs()['BANDS']['permission_types']))
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true">Remove
                                <i class="fa fa-remove"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body"><input type="number" id="autopen" style="display:none" value="{{$autopen}}"/>
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_model .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="10%">Logo</th>
                                <th width="78%">Description</th>
                                <th width="10%">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bands as $index=>$b)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$b->id}}" value="{{$b->name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td width="10%" data-order="{{$b->name}}">
                                    @if(preg_match('/\/uploads\//',$b->image_url)) @php $b->image_url = env('IMAGE_URL_OLDTB_SERVER').$b->image_url @endphp @endif
                                    @if(preg_match('/\/s3\//',$b->image_url)) @php $b->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$b->image_url) @endphp @endif
                                    <center style="color:red;"><i><b><a><img alt="- No image -" height="110px" width="110px" src="{{$b->image_url}}"/></a></b></i></center>
                                </td>
                                <td class="search-item clearfix" width="78%">
                                    <div class="search-title">
                                        <h4>
                                            <a>{{$b->name}}</a>&nbsp;&nbsp;&nbsp;
                                            @if($b->website)<a class="social-icon social-icon-color rss" href="{{$b->website}}" target="_blank"></a>@endif
                                            @if($b->youtube)<a class="social-icon social-icon-color youtube" href="{{$b->youtube}}" target="_blank"></a>@endif
                                            @if($b->facebook)<a class="social-icon social-icon-color facebook" href="{{$b->facebook}}" target="_blank"></a>@endif
                                            @if($b->twitter)<a class="social-icon social-icon-color twitter" href="{{$b->twitter}}" target="_blank"></a>@endif
                                            @if($b->my_space)<a class="social-icon social-icon-color myspace" href="{{$b->my_space}}" target="_blank"></a>@endif
                                            @if($b->flickr)<a class="social-icon social-icon-color flickr" href="{{$b->flickr}}" target="_blank"></a>@endif
                                            @if($b->instagram)<a class="social-icon social-icon-color instagram" href="{{$b->instagram}}" target="_blank"></a>@endif
                                            @if($b->soundcloud)<a class="social-icon social-icon-color jolicloud" href="{{$b->soundcloud}}" target="_blank"></a>@endif
                                        </h4>
                                    </div>
                                    <div class="search-content">
                                        <small>@if($b->short_description){{$b->short_description}}@else <i style="color:red"><b>- No short description -</b></i>@endif</small>
                                    </div>
                                </td>
                                <td width="10%"><center> {{$b->category}} </center></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
    <!-- BEGIN UPDATE MODAL-->
    <div id="modal_model_update" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:1000px !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title"></center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Name
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="name" class="form-control" placeholder="My Band" />
                                        </div>
                                        <label class="control-label col-md-3">Category
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="category_id">
                                                @foreach($categories as $index=>$c)
                                                    @if($c->id_parent == 0)
                                                        <option value="{{$c->id}}" @if($c->disabled) disabled @endif>{{$c->name}}</option>
                                                        @foreach ($c->children()->get() as $children)
                                                            <option value="{{$children->id}}" @if($children->disabled) disabled @endif>&nbsp;&nbsp;-&nbsp;&nbsp;{{$children->name}}</option>
                                                            @foreach ($children->children()->get() as $niece)
                                                                <option value="{{$niece->id}}" @if($niece->disabled) disabled @endif>&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;{{$niece->name}}</option>
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="control-label col-md-3">Image
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_bands_upload_image_url" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="200px" height="200px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="col-md-1"><a data-original-title="rss" class="social-icon social-icon-color rss"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="website" class="form-control" placeholder="https://www.myband.com" />
                                        </div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-11 show-error">
                                            <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Get Media From Web Site</button>
                                        </div>
                                        <div class="col-md-1"><a data-original-title="youtube" class="social-icon social-icon-color youtube"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="facebook" class="social-icon social-icon-color facebook"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="twitter" class="social-icon social-icon-color twitter"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="myspace" class="social-icon social-icon-color myspace"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="my_space" class="form-control" placeholder="https://myspace.com/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="flickr" class="social-icon social-icon-color flickr"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="flickr" class="form-control" placeholder="https://flickr.com/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="instagram" class="social-icon social-icon-color instagram"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myband" />
                                        </div>
                                        <div class="col-md-1"><a data-original-title="jolicloud" class="social-icon social-icon-color jolicloud"></a>
                                        </div>
                                        <div class="col-md-11 show-error">
                                            <input type="text" name="soundcloud" class="form-control" placeholder="https://soundcloud.com/myband" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding:0 20px">
                                <label class="control-label">Short Description:
                                    <span class="required"> * </span>
                                </label>
                                <div class="show-error">
                                    <textarea name="short_description" class="form-control" rows="3"></textarea>
                                </div>
                                <label class="control-label">Description:</label>
                                <div class="show-error">
                                    <textarea name="description" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-green">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END UPDATE MODAL-->
    <!-- BEGIN SEARCH MODAL-->
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:400px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Search Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/bands" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="onlyerrors" class="col-md-5"> <span>Only With Error:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="onlyerrors" style="width:65px !important">
                                        <option @if($onlyerrors==0) selected @endif value="0">No</option>
                                        <option @if($onlyerrors==1) selected @endif value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_search').trigger('reset')">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa" onclick="$('#modal_model_search').modal('hide'); swal({
                                                                                                    title: 'Searching information',
                                                                                                    text: 'Please, wait.',
                                                                                                    type: 'info',
                                                                                                    showConfirmButton: false
                                                                                                });" >Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END SEARCH MODAL-->
@endsection

@section('scripts')
<script src="/js/admin/bands/index.js" type="text/javascript"></script>
@endsection
