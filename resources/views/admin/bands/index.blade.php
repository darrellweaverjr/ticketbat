@php $page_title='Bands' @endphp
@extends('layouts.admin')
@section('title', 'Bands' )

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
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search"> Search 
                                <i class="fa fa-search"></i>
                            </button>
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
                            @foreach($bands as $index=>$b)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$b->id}}" value="{{$b->name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td class="search-item clearfix" width="88%"> 
                                    <div class="search-content col-md-2"> 
                                        @if(preg_match('/\/uploads\//',$b->image_url)) @php $b->image_url = env('IMAGE_URL_OLDTB_SERVER').$b->image_url @endphp @endif
                                        @if(preg_match('/\/s3\//',$b->image_url)) @php $b->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$b->image_url) @endphp @endif
                                        <center style="color:red;"><i><b><a data-toggle="modal" href="#modal_details_{{$b->id}}"><img alt="- No image -" height="110px" width="110px" src="{{$b->image_url}}"/></a></b></i></center>
                                    </div>
                                    <div class="search-content col-md-10" style="padding-left:35px">
                                        <h3 class="search-title"><a data-toggle="modal" href="#modal_details_{{$b->id}}">{{$b->name}}</a></h3>
                                        <p><small><i>
                                            @if($b->website)Web Site: <a href="{{$b->website}}" target="_blank">{{$b->website}} </a>@endif
                                            @if($b->youtube)YouTube: <a href="{{$b->youtube}}" target="_blank">{{$b->youtube}} </a>@endif 
                                            @if($b->facebook)Facebook: <a href="{{$b->facebook}}" target="_blank">{{$b->facebook}} </a>@endif 
                                            @if($b->twitter)Twitter: <a href="{{$b->twitter}}" target="_blank">{{$b->twitter}} </a>@endif 
                                            @if($b->my_space)MySpace: <a href="{{$b->my_space}}" target="_blank">{{$b->my_space}} </a>@endif 
                                            @if($b->flickr)Flickr: <a href="{{$b->flickr}}" target="_blank">{{$b->flickr}} </a>@endif 
                                            @if($b->instagram)Instagram: <a href="{{$b->instagram}}" target="_blank">{{$b->instagram}} </a>@endif 
                                            @if($b->soundcloud)SoundCloud: <a href="{{$b->soundcloud}}" target="_blank">{{$b->soundcloud}} </a>@endif 
                                        </i></small></p>
                                        <p> @if($b->short_description) {{$b->short_description}} @else <i style="color:red"><b>- No short description -</b></i> @endif </p>
                                    </div>
                                </td>
                                <td width="10%"><center> {{$b->category}} </center></td>
                            </tr>
                            <!-- BEGIN DETAILS MODAL--> 
                            <div id="modal_details_{{$b->id}}" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog">
                                    <div class="modal-content portlet">
                                        <div id="modal_model_update_header" class="modal-header">
                                            <h4 class="modal-title bold uppercase"><center>{{$b->name}}</center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    <center style="color:red;"><i><b><img alt="- No image -" height="200px" width="220px" src="{{$b->image_url}}"/></b></i></center>
                                                </div>
                                                <div class="portlet-body">
                                                    <ul class="chats">
                                                        <li class="in">
                                                            <div class="avatar">Category</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> {{$b->category}} </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Social Media</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    @if($b->website)Web Site: <a href="{{$b->website}}" target="_blank">{{$b->website}} </a><br>@endif
                                                                    @if($b->youtube)YouTube: <a href="{{$b->youtube}}" target="_blank">{{$b->youtube}} </a><br>@endif 
                                                                    @if($b->facebook)Facebook: <a href="{{$b->facebook}}" target="_blank">{{$b->facebook}} </a><br>@endif 
                                                                    @if($b->twitter)Twitter: <a href="{{$b->twitter}}" target="_blank">{{$b->twitter}} </a><br>@endif 
                                                                    @if($b->my_space)MySpace: <a href="{{$b->my_space}}" target="_blank">{{$b->my_space}} </a><br>@endif 
                                                                    @if($b->flickr)Flickr: <a href="{{$b->flickr}}" target="_blank">{{$b->flickr}} </a><br>@endif 
                                                                    @if($b->instagram)Instagram: <a href="{{$b->instagram}}" target="_blank">{{$b->instagram}} </a><br>@endif 
                                                                    @if($b->soundcloud)SoundCloud: <a href="{{$b->soundcloud}}" target="_blank">{{$b->soundcloud}} </a><br>@endif 
                                                                    @if(!$b->website && !$b->youtube && !$b->facebook && !$b->twitter && !$b->my_space && !$b->flickr && !$b->instagram && !$b->soundcloud) <i style="color:red"><b>- No social media links -</b></i> @endif
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Short Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> @if($b->short_description) {{$b->short_description}} @else <i style="color:red"><b>- No short description -</b></i> @endif </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Full Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> @if($b->description) {{$b->description}} @else <i style="color:red"><b>- No description -</b></i> @endif </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END DETAILS MODAL--> 
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
                                                <button type="button" id="btn_bands_upload_image_url" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
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