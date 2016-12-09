@php $page_title='Shows' @endphp
@extends('layouts.admin')
@section('title', 'Shows' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove shows.</small>
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
                        <form method="post" action="/admin/shows" id="form_model_search" class=" pull-left">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <div style="width:1000px !important">
                                <label for="venue"> <span>Venue:</span> </label>
                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="venue" style="width:200px !important">
                                    <option selected value="">All</option>
                                    @foreach($venues as $index=>$v)
                                    <option @if($v->id==$venue) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <label for="showtime"> <span>Show Time:</span> </label>
                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="showtime" style="width:100px !important">
                                    <option @if($showtime=='A') selected @endif value="A">All</option>
                                    <option @if($showtime=='P') selected @endif value="P">Passed</option>
                                    <option @if($showtime=='U') selected @endif value="U">Upcoming</option>
                                </select>
                                <label for="status"> <span>Status:</span> </label>
                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="status" style="width:90px !important">
                                    <option @if($status==1) selected @endif value="1">Active</option>
                                    <option @if($status==0) selected @endif value="0">Inactive</option>
                                </select>
                                <label for="onlyerrors"> <span>Only With Error:</span> </label>
                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="onlyerrors" style="width:65px !important">
                                    <option @if($onlyerrors==0) selected @endif value="0">No</option>
                                    <option @if($onlyerrors==1) selected @endif value="1">Yes</option>
                                </select>
                                <button class="btn sbold bg-gray btn-small form-control input-inline  input-sm " > Search 
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>  
                        <div class="btn-group">
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_model .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="88%"> Name </th>
                                <th width="10%"> Category </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shows as $index=>$s)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$s->id}}" value="{{$s->name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td class="search-item clearfix" width="88%"> 
                                    {{$s->name}}
                                </td>
                                <td width="10%"><center> {{$s->category}} </center></td>
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
        <div class="modal-dialog" style="width:50% !important;">
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
                                            <input type="text" name="name" class="form-control" placeholder="My Band" /> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Category
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="category_id">
                                                @foreach($categories as $index=>$c)
                                                    @if($c->id_parent == 0)
                                                        <option value="{{$c->id}}">{{$c->name}}</option>
                                                        @foreach ($c->children()->get() as $children)
                                                            <option value="{{$children->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;{{$children->name}}</option>
                                                            @foreach ($children->children()->get() as $niece)
                                                                <option value="{{$niece->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;{{$niece->name}}</option>
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Image
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_bands_upload_image" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="323px" height="270px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Web Site
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="website" class="form-control" placeholder="https://www.myband.com" /> 
                                        </div> 
                                        <label class="col-md-3 control-label">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Get Media From Web Site</button>
                                        </div> 
                                        <label class="col-md-3 control-label">Youtube
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Facebook
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Twitter
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">My Space
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="my_space" class="form-control" placeholder="https://myspace.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Flickr
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="flickr" class="form-control" placeholder="https://flickr.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Instagram
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">SoundCloud
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="soundcloud" class="form-control" placeholder="https://soundcloud.com/myband" /> 
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <label class="col-md-2 control-label">Short Descript.
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea name="short_description" class="form-control" rows="2"></textarea>
                                </div> 
                            </div>
                            <div class="row">
                                <label class="col-md-2 control-label">Description</label>
                                <div class="col-md-10 show-error">
                                    <textarea name="description" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
<!--                            <div class="row">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="80%"> Show </th>
                                            <th width="20%"> Order </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="80%"> sdfdfdfdf </td>
                                            <td width="20%"> 1 </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>  -->
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
@endsection

@section('scripts') 
<script src="/js/admin/shows/index.js" type="text/javascript"></script>
@endsection